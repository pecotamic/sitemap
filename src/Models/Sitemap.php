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

        $entries = collect();

        if (config('pecotamic.sitemap.include_entries')) {
            $entries = $entries->merge(self::toSitemapEntries($sitemap->publishedEntries()));
        }

        if (config('pecotamic.sitemap.include_terms')) {
            $entries = $entries->merge(self::toSitemapEntries($sitemap->publishedTerms()));
        }

        if (config('pecotamic.sitemap.include_collection_terms')) {
            $entries = $entries->merge(self::toSitemapEntries($sitemap->publishedCollectionTerms()));
        }

        return $entries
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
        $exluded_urls = self::getExcludedUrlCollection();
        $entry_types = config('pecotamic.sitemap.entry_types');

        return Collection::all()
            ->flatMap(function ($collection) {
                return $collection->queryEntries()->get();
            })
            ->filter(function ($entry) use ($exluded_urls, $entry_types) {
                if (!preg_match('#^https?://#', $entry->absoluteUrl())) {
                    return false;
                }

                // is excluded by url pattern
                if (self::isExcluded($entry->url(), $exluded_urls)) {
                    return false;
                }

                // is an included entry type
                if ($entry_types !== null && !in_array($entry->collectionHandle(), $entry_types)) {
                    return false;
                }

                return $entry->status() === 'published';
            });
    }

    protected function publishedTerms()
    {
        $exluded_urls = self::getExcludedUrlCollection();

        return Taxonomy::all()
            ->flatMap(function ($taxonomy) {
                return $taxonomy->queryTerms()->get();
            })
            ->filter
            ->published()
            ->filter(function ($term) use ($exluded_urls) {

                if (self::isExcluded($entry->url(), $exluded_urls)) {
                    return false;
                }

                return view()->exists($term->template());
            });
    }

    protected function publishedCollectionTerms()
    {
        $exluded_urls = self::getExcludedUrlCollection();

        return Collection::all()
            ->flatMap(function ($collection) {
                return $collection->taxonomies()->map->collection($collection);
            })
            ->flatMap(function ($taxonomy) {
                return $taxonomy->queryTerms()->get()->map->collection($taxonomy->collection());
            })
            ->filter
            ->published()
            ->filter(function ($term) use ($exluded_urls) {

                if (self::isExcluded($entry->url(), $exluded_urls)) {
                    return false;
                }

                return view()->exists($term->template());
            });
    }

    private static function isExcluded($url, $exluded_urls)
    {
        return $exluded_urls->contains(function ($value) use ($url) {
            return preg_match($value, $url);
        });
    }

    private function getExcludedUrlCollection()
    {
        return collect(config('pecotamic.sitemap.exclude_urls', []));
    }
}
