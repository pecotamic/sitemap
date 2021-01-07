<?php

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
    'exclude_urls' => []
];
