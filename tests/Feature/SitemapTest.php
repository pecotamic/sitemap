<?php

namespace Pecotamic\Sitemap\Tests\Feature;

use Pecotamic\Sitemap\Tests\TestCase;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class SitemapTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected function setUp(): void
    {
        parent::setUp();

        Collection::make('pages')->routes('/{slug}')->save();
    }

    private function makeEntry(string $slug, bool $published = true): void
    {
        Entry::make()
            ->collection('pages')
            ->slug($slug)
            ->published($published)
            ->set('title', ucfirst($slug))
            ->save();
    }

    public function test_it_serves_an_xml_sitemap()
    {
        $this->makeEntry('about');

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<urlset', false);
        $response->assertSee('about', false);
    }

    public function test_it_excludes_draft_entries()
    {
        $this->makeEntry('published-page', true);
        $this->makeEntry('draft-page', false);

        $response = $this->get('/sitemap.xml');

        $response->assertSee('published-page', false);
        $response->assertDontSee('draft-page', false);
    }

    public function test_it_respects_the_exclude_urls_config()
    {
        config()->set('pecotamic.sitemap.exclude_urls', ['#/secret#']);

        $this->makeEntry('secret');
        $this->makeEntry('visible');

        $response = $this->get('/sitemap.xml');

        $response->assertSee('visible', false);
        $response->assertDontSee('secret', false);
    }

    public function test_it_renders_priority_and_lastmod_for_each_entry()
    {
        $this->makeEntry('about');

        $response = $this->get('/sitemap.xml');

        $response->assertSee('<priority>0.8</priority>', false);
        $response->assertSee('<lastmod>', false);
    }
}
