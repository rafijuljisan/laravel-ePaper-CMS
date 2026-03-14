<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EpaperController;

// Home — latest edition
Route::get('/', [EpaperController::class, 'index'])->name('epaper.index');

// Archive by edition ID
Route::get('/edition/{edition}', [EpaperController::class, 'show'])->name('epaper.show');

// Article JSON API — used by JS fetch
Route::get('/article/{article}', [EpaperController::class, 'article'])->name('epaper.article');