<?php

namespace Pecotamic\Sitemap\Models;

use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;

class Sitemap
{
    /**
     * @return SitemapEntry[]|array
     */
    public static function entries(): array
    {
        $sitemap = new static;

        return collect()
            ->merge(self::toSitemapEntries($sitemap->publishedEntries()))
            ->merge(self::toSitemapEntries($sitemap->publishedTerms()))
            ->merge(self::toSitemapEntries($sitemap->publishedCollectionTerms()))
            ->values()
            ->sortBy(function (SitemapEntry $entry) {
                return substr_count(rtrim($entry->path, '/'), '/');
            })
            ->toArray();
    }

    protected static function toSitemapEntries($items)
    {
        return $items->map(function ($content) {
            return new SitemapEntry($content);
        });
    }

    protected function publishedEntries(): \Illuminate\Support\Collection
    {
        return Collection::all()
            ->flatMap(function ($collection) {
                return $collection->queryEntries()->get();
            })
            ->filter(function ($entry) {
                return $entry->status() === 'published';
            });
    }

    protected function publishedTerms()
    {
        return Taxonomy::all()
            ->flatMap(function ($taxonomy) {
                return $taxonomy->queryTerms()->get();
            })
            ->filter
            ->published()
            ->filter(function ($term) {
                return view()->exists($term->template());
            });
    }

    protected function publishedCollectionTerms()
    {
        return Collection::all()
            ->flatMap(function ($collection) {
                return $collection->taxonomies()->map->collection($collection);
            })
            ->flatMap(function ($taxonomy) {
                return $taxonomy->queryTerms()->get()->map->collection($taxonomy->collection());
            })
            ->filter
            ->published()
            ->filter(function ($term) {
                return view()->exists($term->template());
            });
    }
}
