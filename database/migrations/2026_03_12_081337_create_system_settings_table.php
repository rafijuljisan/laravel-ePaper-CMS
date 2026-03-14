<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('system_settings', 'site_name')) {
                $table->string('site_name')->default('দৈনিক সংবাদ');
            }
            if (!Schema::hasColumn('system_settings', 'site_tagline')) {
                $table->string('site_tagline')->default('E-Paper — Online Edition');
            }
            if (!Schema::hasColumn('system_settings', 'site_logo')) {
                $table->string('site_logo')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'site_favicon')) {
                $table->string('site_favicon')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'site_email')) {
                $table->string('site_email')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'site_phone')) {
                $table->string('site_phone')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'site_address')) {
                $table->string('site_address')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'editor_name')) {
                $table->string('editor_name')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'facebook_url')) {
                $table->string('facebook_url')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'twitter_url')) {
                $table->string('twitter_url')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'youtube_url')) {
                $table->string('youtube_url')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'header_ad_image')) {
                $table->string('header_ad_image')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'header_ad_url')) {
                $table->string('header_ad_url')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'sidebar_ad1_image')) {
                $table->string('sidebar_ad1_image')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'sidebar_ad1_url')) {
                $table->string('sidebar_ad1_url')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'sidebar_ad2_image')) {
                $table->string('sidebar_ad2_image')->nullable();
            }
            if (!Schema::hasColumn('system_settings', 'sidebar_ad2_url')) {
                $table->string('sidebar_ad2_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};