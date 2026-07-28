<?php

namespace Pecotamic\Sitemap;

use Pecotamic\Sitemap\Listeners\ClearSitemapCache;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;
use Statamic\Events\TermDeleted;
use Statamic\Events\TermSaved;
use Statamic\Facades\Site;
use Statamic\Facades\URL;
use Statamic\Providers\AddonServiceProvider;
use Statamic\StaticSite\SSG;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'pecotamic/sitemap';

    protected $routes = [
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $listen = [
        EntrySaved::class => [ClearSitemapCache::class],
        EntryDeleted::class => [ClearSitemapCache::class],
        TermSaved::class => [ClearSitemapCache::class],
        TermDeleted::class => [ClearSitemapCache::class],
    ];

    protected function bootViews(): self
    {
        parent::bootViews();

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/'.$this->viewNamespace),
        ], 'view');

        return $this;
    }

    protected function bootConfig(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sitemap.php', 'pecotamic.sitemap');

        $this->publishes([
            __DIR__.'/../config/sitemap.php' => config_path('pecotamic/sitemap.php'),
        ], 'config');

        return $this;
    }

    protected function bootRoutes(): self
    {
        parent::bootRoutes();

        $this->addRoutesToSSG();

        return $this;
    }

    protected function addRoutesToSSG(): void
    {
        if (!class_exists(SSG::class)) {
            return;
        }

        SSG::addUrls(static function () {
            return self::sitePrefixes()->map(static function ($sitePrefix) {
                return $sitePrefix . '/' . config('pecotamic.sitemap.url');
            });
        });
    }

    /**
     * Relative URL prefix for each configured site (deduplicated).
     */
    public static function sitePrefixes(): \Illuminate\Support\Collection
    {
        return Site::all()
            ->map(static function ($site) {
                return URL::makeRelative($site->url());
            })
            ->unique()
            ->values();
    }
}
