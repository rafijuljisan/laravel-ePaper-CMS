<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Article;

class EpaperController extends Controller
{
    public function index()
    {
        $latestEdition = Edition::with(['pages.hotspots.article'])->latest('edition_date')->first();
        $editions = Edition::latest('edition_date')->take(10)->get();

        return view('epaper.index', compact('latestEdition', 'editions'));  // ← changed from 'welcome'
    }

    public function show(Edition $edition)
    {
        $edition->load(['pages.hotspots.article']);
        $editions = Edition::latest('edition_date')->take(10)->get();
        $latestEdition = $edition;

        return view('epaper.index', compact('latestEdition', 'editions'));  // ← changed from 'welcome'
    }

    public function article(Article $article)
    {
        $article->load(['edition', 'category']);

        return response()->json([
            'id'           => $article->id,
            'title'        => $article->title,
            'author'       => $article->author,
            'summary'      => $article->summary,
            'content'      => $article->content,
            'category'     => $article->category->name ?? null,
            'page_number'  => $article->page_number,
            'edition'      => $article->edition->title ?? null,
            'edition_date' => $article->edition->edition_date?->format('d F Y') ?? null,
        ]);
    }
}