<?php

namespace App\Services;

use App\Models\Edition;
use App\Models\Article;
use App\Models\Hotspot;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class XmlParser
{
    public static function process(Edition $edition)
    {
        // 1. Check if an XML file was actually uploaded
        if (!$edition->xml_file || !Storage::disk('public')->exists($edition->xml_file)) {
            return false; 
        }

        // 2. Load the XML file
        $xmlPath = Storage::disk('public')->path($edition->xml_file);
        $xml = simplexml_load_file($xmlPath);

        if (!$xml) {
            Log::error("ePaper XML Parsing Failed for Edition ID: {$edition->id}");
            return false;
        }

        // 3. Loop through every <article> node in the XML
        $articles = $xml->xpath('//article');

        foreach ($articles as $xmlArticle) {
            $title = (string) $xmlArticle->title;
            $pageNum = (int) $xmlArticle->page;
            
            // Generate a unique slug (prevents crashes if two articles have the same title)
            $slug = Str::slug($title) . '-' . uniqid(); 

            // 4. Create the Article in the database
            $article = Article::create([
                'edition_id'  => $edition->id,
                'title'       => $title,
                'slug'        => $slug,
                'content'     => isset($xmlArticle->content) ? (string) $xmlArticle->content : null,
                'page_number' => $pageNum,
            ]);

            // 5. Find the exact Page ID this belongs to
            $page = Page::where('edition_id', $edition->id)
                        ->where('page_number', $pageNum)
                        ->first();

            if ($page) {
                // 6. Draw the Hotspot!
                Hotspot::create([
                    'page_id'    => $page->id,
                    'article_id' => $article->id,
                    'x'          => (float) $xmlArticle->x,
                    'y'          => (float) $xmlArticle->y,
                    'width'      => (float) $xmlArticle->width,
                    'height'     => (float) $xmlArticle->height,
                ]);
            } else {
                Log::warning("Skipped hotspot for '{$title}' - Page {$pageNum} not found.");
            }
        }

        return true;
    }
}