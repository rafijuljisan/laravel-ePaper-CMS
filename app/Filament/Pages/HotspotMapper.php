<?php

namespace App\Filament\Pages;

use App\Models\Edition;
use App\Models\Page as EpaperPage;
use App\Models\Article;
use App\Models\Hotspot;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class HotspotMapper extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-viewfinder-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Newspaper';
    protected static ?string $navigationLabel = 'Visual Hotspot Mapper';
    protected string $view = 'filament.pages.hotspot-mapper';

    public $editionId = null;
    public $pageId = null;

    // Multiple pending hotspots array
    public array $pendingHotspots = [];

    // Draft being drawn right now
    public $draftX = null;
    public $draftY = null;
    public $draftW = null;
    public $draftH = null;
    public $selectedArticleId = null;

    public function getEditionsProperty()
    {
        return Edition::orderBy('edition_date', 'desc')->get();
    }

    public function getPagesProperty()
    {
        if (!$this->editionId)
            return [];
        return EpaperPage::where('edition_id', $this->editionId)->orderBy('page_number')->get();
    }

    public function getArticlesProperty()
    {
        if (!$this->editionId)
            return [];
        return Article::where('edition_id', $this->editionId)->orderBy('title')->get();
    }

    public function getActivePageProperty()
    {
        if (!$this->pageId)
            return null;
        return EpaperPage::with('hotspots.article')->find($this->pageId);
    }

    // Called from Alpine when user finishes drawing a box
    public function draftHotspot($x, $y, $w, $h, $imageW = null, $imageH = null)
    {
        $this->draftX = $x;
        $this->draftY = $y;
        $this->draftW = $w;
        $this->draftH = $h;
        $this->selectedArticleId = null;
        // Removed auto-heal — real dimensions are set by ProcessEditionPdf from getimagesize()
    }

    // Add current draft to pending list
    public function addToPending()
    {
        $this->validate([
            'selectedArticleId' => 'nullable|exists:articles,id',
            'draftX' => 'required',
        ]);

        $article = $this->selectedArticleId ? Article::find($this->selectedArticleId) : null;

        $this->pendingHotspots[] = [
            'tempId' => uniqid(),
            'article_id' => $this->selectedArticleId,
            'articleTitle' => $article->title ?? '(No Article)',
            'x' => $this->draftX,
            'y' => $this->draftY,
            'width' => $this->draftW,
            'height' => $this->draftH,
        ];

        // Clear draft
        $this->draftX = null;
        $this->draftY = null;
        $this->draftW = null;
        $this->draftH = null;
        $this->selectedArticleId = null;

        Notification::make()->title('Added to queue — draw more or save all.')->info()->send();
    }

    // Remove one item from pending list
    public function removePending($index)
    {
        array_splice($this->pendingHotspots, $index, 1);
        $this->pendingHotspots = array_values($this->pendingHotspots);
    }

    // Save all pending hotspots at once
    public function saveAllHotspots()
    {
        if (empty($this->pendingHotspots)) {
            Notification::make()->title('No hotspots to save.')->warning()->send();
            return;
        }

        foreach ($this->pendingHotspots as $h) {
            Hotspot::create([
                'page_id' => $this->pageId,
                'article_id' => $h['article_id'],
                'x' => $h['x'],
                'y' => $h['y'],
                'width' => $h['width'],
                'height' => $h['height'],
            ]);
        }

        $count = count($this->pendingHotspots);
        $this->pendingHotspots = [];

        Notification::make()
            ->title("{$count} hotspot(s) saved successfully!")
            ->success()
            ->send();
    }

    // Delete already-saved hotspot
    public function deleteHotspot($id)
    {
        Hotspot::find($id)?->delete();
        Notification::make()->title('Hotspot Deleted')->danger()->send();
    }

    // Clear all pending without saving
    public function clearPending()
    {
        $this->pendingHotspots = [];
        $this->draftX = null;
        $this->draftY = null;
        $this->draftW = null;
        $this->draftH = null;
        $this->selectedArticleId = null;
    }
}