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
            $pdfMedia = $this->edition->getFirstMedia('editions');

            if (!$pdfMedia) {
                throw new \Exception("No PDF file found for Edition ID: {$this->edition->id}");
            }

            $pdfPath = $pdfMedia->getPath();

            // Get total pages via Imagick
            $counter = new \Imagick();
            $counter->pingImage($pdfPath);
            $totalPages = $counter->getNumberImages();
            $counter->clear();
            $counter->destroy();

            $storageFolder = "epaper/editions/{$this->edition->id}";
            Storage::disk('public')->makeDirectory($storageFolder);

            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++) {

                $fileName = "page-{$pageNumber}.png";
                $tempImagePath = "{$tempDir}/" . uniqid() . "-{$fileName}";
                $publicImagePath = "{$storageFolder}/{$fileName}";

                // Imagick: resolution MUST be set before readImage
                $imagick = new \Imagick();
                $imagick->setResolution(200, 200);
                $imagick->readImage($pdfPath . '[' . ($pageNumber - 1) . ']');
                $imagick->setImageFormat('png');
                $imagick->setImageColorspace(\Imagick::COLORSPACE_SRGB);
                $imagick->setImageCompression(\Imagick::COMPRESSION_NO);
                $imagick->setImageCompressionQuality(100);
                $imagick->stripImage();
                $imagick->writeImage($tempImagePath);
                $imagick->clear();
                $imagick->destroy();

                Storage::disk('public')->put($publicImagePath, file_get_contents($tempImagePath));

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
                        'width' => $imageSize ? $imageSize[0] : null,
                        'height' => $imageSize ? $imageSize[1] : null,
                    ]
                );

                @unlink($tempImagePath);
            }

            XmlParser::process($this->edition);
            $this->edition->update(['status' => 'published']);

        } catch (\Exception $e) {
            $this->edition->update(['status' => 'failed']);
            Log::error('ePaper Processing Failed: ' . $e->getMessage());
        }
    }
}