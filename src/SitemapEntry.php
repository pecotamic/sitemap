<?php

namespace Pecotamic\Sitemap;

class SitemapEntry
{
    /** @var string */
    public $path;
    /** @var string */
    public $loc;
    /** @var \DateTime|null */
    public $lastmod;
    /** @var string|null */
    public $changefreq;
    /** @var float */
    public $priority;

    /**
     * @param float|string|null $priority defaults to 1.0 for the root path, 0.8 otherwise
     */
    public function __construct(string $loc, ?\DateTime $lastmod = null, ?string $changefreq = null, $priority = null)
    {
        $this->loc = $loc;
        $this->path = parse_url($loc)['path'] ?? '/';
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = is_numeric($priority) ? (float) $priority : ($this->path === '/' ? 1.0 : 0.8);
    }
}
