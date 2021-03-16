<?php

use Statamic\Entries\Entry;
use Statamic\Taxonomies\LocalizedTerm;

return [
    'url' => 'sitemap.xml',
    'expire' => 60,
    'include_entries' => true,
    'include_terms' => true,
    'include_collection_terms' => true,

    /**
     * - Leave as `null` for all types
     * - Provide array of types/handles for filtering
     */
    'entry_types' => null,

    /**
     * - Use valid regex patterns (uses `preg_match`)
     * - Relative URLs (relative to site url)
     */
    'exclude_urls' => [],

    /**
     * Filter entries by callback
     *
     * @param  LocalizedTerm|Entry  $entry
     * @return bool
     */
    'filter' => static function ($entry): bool {
        /* example:

        $augmented = $entry->newAugmentedInstance();

        if (!($metaRobots = $augmented->get('meta_robots')) || !is_array($metaRobotsArray = $metaRobots->raw())) {
            return true;
        }

        return !in_array('noindex', $metaRobotsArray, true);
        */

        return true;
    },

    /**
     * Provide properties for sitemap entries to override default values.
     *
     * Possible array fields are:
     * - loc: string: absolute url
     * - lastmod: DateTime: date of last modification
     * - changefreq: string|null: see https://www.sitemaps.org/de/protocol.html#changefreqdef
     * - priority: float|null: value in range 0 to 1
     *
     * @param  LocalizedTerm|Entry  $entry
     * @return null|array   array of overrides or null to use default values
     */
    'properties' => static function ($entry): ?array {
        /* example:

        if ($entry instanceof LocalizedTerm) {
            return [
                'changefreq' => 'weekly',
                'priority' => 0.3,
            ];
        }

        if ($entry->collectionHandle() === 'blog') {
            return [
                'changefreq' => 'monthly',
                'priority' => 0.5,
            ];
        }

        return null;
        */

        return null;
    },
];
