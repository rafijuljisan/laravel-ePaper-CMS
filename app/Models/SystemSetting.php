<?php
// app/Models/SystemSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_tagline',
        'site_logo',
        'site_favicon',
        'site_email',
        'site_phone',
        'site_address',
        'editor_name',
        'facebook_url',
        'twitter_url',
        'youtube_url',
        'header_ad_image',
        'header_ad_url',
        'sidebar_ad1_image',
        'sidebar_ad1_url',
        'sidebar_ad2_image',
        'sidebar_ad2_url',
        'archive_retention_days',
    ];

    /**
     * Always get-or-create the single settings row.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'site_name'               => 'দৈনিক সংবাদ',
            'site_tagline'            => 'E-Paper — Online Edition',
            'archive_retention_days'  => 7,
        ]);
    }
}