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

        // collect
        if (config('pecotamic.sitemap.include_entries', true)) {
            $entries = $entries->merge($sitemap->publishedEntries(config('pecotamic.sitemap.entry_types')));
        }
        if (config('pecotamic.sitemap.include_terms', true)) {
            $entries = $entries->merge($sitemap->publishedTerms());
        }
        if (config('pecotamic.sitemap.include_collection_terms', true)) {
            $entries = $entries->merge($sitemap->publishedCollectionTerms());
        }

        // filter by config
        if ($excludedUrls = config('pecotamic.sitemap.exclude_urls')) {
            $entries = $entries->filter(self::excludedUrlsFilter($excludedUrls));
        }

        // filter by function
        if ($callback = config('pecotamic.sitemap.filter')) {
            $entries = $entries->filter($callback);
        }

        return $entries
            ->map(function ($entry) {
                $properties = self::sitemapProperties($entry);

                if ($callback = config('pecotamic.sitemap.properties')) {
                    $properties = ($callback($entry) ?? []) + $properties;
                }

                return new SitemapEntry($properties['loc'], $properties['lastmod'], $properties['changefreq'], $properties['priority']);
            })
            ->values()
            ->sortBy(function (SitemapEntry $entry) {
                return substr_count(rtrim($entry->path, '/'), '/');
            })
            ->toArray();
    }

    protected function publishedEntries(?array $entryTypes = null): \Illuminate\Support\Collection
    {
        return Collection::all()
            ->flatMap(function ($collection) {
                return $collection->queryEntries()->get();
            })
            ->filter(function ($entry) use ($entryTypes) {
                if (!self::isAbsoluteUrl($entry->absoluteUrl())) {
                    return false;
                }
                if ($entryTypes !== null && !in_array($entry->collectionHandle(), $entryTypes)) {
                    return false;
                }

                return $entry->published();
            });
    }

    protected static function isAbsoluteUrl(string $url): bool
    {
        return preg_match('#^https?://#', $url);
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

    protected static function excludedUrlsFilter(array $excludedUrls): callable
    {
        return function ($entry) use ($excludedUrls) {
            $url = $entry->url();
            foreach ($excludedUrls as $pattern) {
                if (preg_match($pattern, $url)) {
                    return false;
                }
            }

            return true;
        };
    }

    protected static function sitemapProperties($entry): array
    {
        $augmented = $entry->newAugmentedInstance();

        return [
            'loc' => $augmented->get('absolute_url'),
            'lastmod' => $augmented->get('updated_at'),
            'changefreq' => $augmented->get('change_frequency'),
            'priority' => $augmented->get('priority'),
        ];
    }
}
