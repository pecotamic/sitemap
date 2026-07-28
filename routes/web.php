<?php

use Illuminate\Support\Facades\Route;
use Pecotamic\Sitemap\Http\Controllers\SitemapController;
use Pecotamic\Sitemap\ServiceProvider;

ServiceProvider::sitePrefixes()->each(function ($sitePrefix) {
    Route::group(['prefix' => $sitePrefix], static function () {
        Route::get(config('pecotamic.sitemap.url'), [SitemapController::class, 'show']);
    });
});
