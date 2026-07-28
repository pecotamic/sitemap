<?php

namespace Pecotamic\Sitemap\Listeners;

use Pecotamic\Sitemap\SitemapCache;

class ClearSitemapCache
{
    public function handle(): void
    {
        SitemapCache::flush();
    }
}
