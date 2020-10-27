<?php

namespace Pecotamic\Sitemap;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'pecotamic/sitemap';

    protected $routes = [
        'web' => __DIR__.'/../routes/web.php',
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
}
