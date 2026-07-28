<?php

namespace Pecotamic\Sitemap;

use Closure;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Site;

class SitemapCache
{
    private const KEY = 'pecotamic-sitemap.sitemap';

    public static function remember(DateTimeInterface $until, Closure $callback): string
    {
        return Cache::remember(self::key(Site::current()->handle()), $until, $callback);
    }

    /**
     * Forget the cached sitemap for every site.
     */
    public static function flush(): void
    {
        Site::all()->each(static function ($site) {
            Cache::forget(self::key($site->handle()));
        });
    }

    private static function key(string $siteHandle): string
    {
        return self::KEY . '.' . $siteHandle;
    }
}
