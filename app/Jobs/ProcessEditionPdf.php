<?php

namespace App\Jobs;

use App\Models\Edition;
use App\Models\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf;
use App\Services\XmlParser;

class ProcessEditionPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Edition $edition;

    // Set a high timeout (20 minutes) for cPanel to prevent the job from dying mid-process
    public $timeout = 1200;

    public function __construct(Edition $edition)
    {
        $this->edition = $edition;
    }

    public function handle(): void
    {
        try {
            // 1. Get the PDF file from Spatie Media Library
            $pdfMedia = $this->edition->getFirstMedia('editions');

            if (!$pdfMedia) {
                throw new \Exception("No PDF file found for Edition ID: {$this->edition->id}");
            }

            $pdfPath = $pdfMedia->getPath();

            // 2. Initialize Spatie PDF to Image
            $pdf = new Pdf($pdfPath);
            $totalPages = $pdf->getNumberOfPages();

            // Create a specific folder for this edition in public storage
            $storageFolder = "epaper/editions/{$this->edition->id}";
            Storage::disk('public')->makeDirectory($storageFolder);

            // Ensure temp directory exists for intermediate processing
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // 3. Loop through every page in the PDF
            for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++) {

                $fileName = "page-{$pageNumber}.jpg";
                $tempImagePath = "{$tempDir}/" . uniqid() . "-{$fileName}";
                $publicImagePath = "{$storageFolder}/{$fileName}";

                // Extract the specific page as a high-quality JPG
                $pdf->setPage($pageNumber)
                    ->setOutputFormat('jpg')
                    ->saveImage($tempImagePath);

                // Move the generated image to Laravel's public storage disk
                Storage::disk('public')->put($publicImagePath, file_get_contents($tempImagePath));

                // 4. Save the Page record to the database
                // Get actual image dimensions after saving
                $savedImagePath = public_path('storage/' . $publicImagePath);
                $imageSize = @getimagesize($savedImagePath);

                Page::updateOrCreate(
                    [
                        'edition_id' => $this->edition->id,
                        'page_number' => $pageNumber,
                    ],
                    [
                        'image_path' => $publicImagePath,
                        'thumbnail_path' => $publicImagePath,
                        'width' => $imageSize ? $imageSize[0] : null,  // ← real width
                        'height' => $imageSize ? $imageSize[1] : null,  // ← real height
                    ]
                );

                // Free up cPanel disk space instantly by deleting the temp file
                @unlink($tempImagePath);
            }

            // 🚀 THE NEW AUTOMATION TRIGGER
            // After all pages are saved to the database, run the XML parser
            XmlParser::process($this->edition);
            // 5. Mark the edition as published once all pages are extracted successfully
            $this->edition->update(['status' => 'published']);

        } catch (\Exception $e) {
            $this->edition->update(['status' => 'failed']);
            Log::error('ePaper Processing Failed: ' . $e->getMessage());
        }
    }
}