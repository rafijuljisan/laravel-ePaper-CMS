<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $fillable = [
        'edition_id',
        'page_number',
        'image_path',
        'thumbnail_path',
        'width',
        'height',
    ];

    // Add this relationship!
    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}