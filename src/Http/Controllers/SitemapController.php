<?php

namespace Pecotamic\Sitemap\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Pecotamic\Sitemap\Models\Sitemap;

class SitemapController extends Controller
{
    private const CACHE_KEY = 'pecotamic-sitemap.sitemap';

    public function show()
    {
        $cacheUntil = Carbon::now()->addSeconds(config('pecotamic.sitemap.expire'));
        $content = Cache::remember(self::CACHE_KEY, $cacheUntil, function () {
            return view('pecotamic/sitemap::sitemap', [
                'entries' => Sitemap::entries(),
                'xml_header' => '<?xml version="1.0" encoding="UTF-8"?>',
            ])->render();
        });

        return response($content)
            ->header('Content-Type', 'application/xml')
            ->header('Expires', $cacheUntil->format('D, d M Y H:i:s T'));
    }
}
