<?php

namespace Pecotamic\Sitemap\Tests\Unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pecotamic\Sitemap\SitemapEntry;

class SitemapEntryTest extends TestCase
{
    public function test_it_derives_the_path_from_the_location()
    {
        $entry = new SitemapEntry('https://example.com/blog/post');

        $this->assertSame('/blog/post', $entry->path);
    }

    public function test_it_falls_back_to_the_root_path_when_the_location_has_none()
    {
        $entry = new SitemapEntry('https://example.com');

        $this->assertSame('/', $entry->path);
    }

    public function test_lastmod_is_optional()
    {
        $entry = new SitemapEntry('https://example.com/');

        $this->assertNull($entry->lastmod);
    }

    public function test_it_keeps_the_given_lastmod()
    {
        $date = new DateTime('2020-02-20');

        $entry = new SitemapEntry('https://example.com/', $date);

        $this->assertSame($date, $entry->lastmod);
    }

    public function test_priority_defaults_to_one_for_the_root_path()
    {
        $entry = new SitemapEntry('https://example.com/');

        $this->assertSame(1.0, $entry->priority);
    }

    public function test_priority_defaults_to_zero_point_eight_for_other_paths()
    {
        $entry = new SitemapEntry('https://example.com/blog/post');

        $this->assertSame(0.8, $entry->priority);
    }

    public function test_it_normalizes_a_numeric_string_priority_to_a_float()
    {
        $entry = new SitemapEntry('https://example.com/', null, null, '0.5');

        $this->assertSame(0.5, $entry->priority);
    }

    public function test_it_keeps_a_float_priority()
    {
        $entry = new SitemapEntry('https://example.com/', null, null, 0.3);

        $this->assertSame(0.3, $entry->priority);
    }

    public function test_it_falls_back_to_the_default_priority_for_a_non_numeric_value()
    {
        $entry = new SitemapEntry('https://example.com/blog/post', null, null, '');

        $this->assertSame(0.8, $entry->priority);
    }
}
