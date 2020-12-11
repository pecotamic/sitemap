<?php

namespace Pecotamic\Sitemap\Models;

class SitemapEntry
{
    public $path;
    public $loc;
    public $lastmod;
    public $changefreq;
    public $priority;

    public function __construct($data)
    {
        $augmented = $data->newAugmentedInstance();

        $this->loc = $augmented->get('absolute_url');
        $this->path = parse_url($this->loc)['path'] ?? '/';
        $this->lastmod = $augmented->get('updated_at');
        $this->changefreq = $augmented->get('change_frequency');
        $this->priority = $augmented->get('priority');
    }
}
