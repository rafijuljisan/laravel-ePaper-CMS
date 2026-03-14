<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Edition extends Model implements HasMedia
{
    // HasUuids automatically generates a secure UUID for new editions
    // InteractsWithMedia enables Spatie PDF uploads
    use HasFactory, HasUuids, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'edition_date',
        'status',
    ];

    protected $casts = [
        'edition_date' => 'date',
    ];

    // An Edition has many pages (We will create the Page model next)
    public function pages()
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    // An Edition has many articles (We will create the Article model next)
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}