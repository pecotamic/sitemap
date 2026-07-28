<?php

namespace Pecotamic\Sitemap\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Pecotamic\Sitemap\Sitemap;
use Pecotamic\Sitemap\SitemapCache;
use Pecotamic\Sitemap\SitemapEntry;
use Statamic\Support\Str;
use Statamic\View\Antlers\Engine as AntlersEngine;

class SitemapController extends Controller
{
    public function show()
    {
        $cacheUntil = Carbon::now()->addSeconds(config('pecotamic.sitemap.expire'));
        $content = SitemapCache::remember($cacheUntil, function () {
            $view = view('pecotamic/sitemap::sitemap');
            return $view->with([
                'entries' => self::entriesFor($view),
                'xml_header' => '<?xml version="1.0" encoding="UTF-8"?>',
            ])->render();
        });

        return response($content)
            ->header('Content-Type', 'application/xml')
            ->header('Expires', $cacheUntil->format('D, d M Y H:i:s T'));
    }

    /**
     * @param View $view
     * @return SitemapEntry[]|array
     */
    private static function entriesFor(View $view): array
    {
        $entries = Sitemap::entries();
        if (self::isUsingAntlersTemplate($view)) {
            return array_map(static function (SitemapEntry $entry) {
                return (array)$entry;
            }, $entries);
        }

        return $entries;
    }

    /**
     * @see \Statamic\View\View::isUsingAntlersTemplate()
     */
    private static function isUsingAntlersTemplate(View $view): bool
    {
        return Str::endsWith($view->getPath(), collect(AntlersEngine::EXTENSIONS)->map(function ($extension) {
            return '.' . $extension;
        })->all());
    }
}
