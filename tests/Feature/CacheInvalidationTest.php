<?php

namespace Pecotamic\Sitemap\Tests\Feature;

use Pecotamic\Sitemap\Tests\TestCase;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class CacheInvalidationTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected function setUp(): void
    {
        parent::setUp();

        Collection::make('pages')->routes('/{slug}')->save();

        // A long lifetime ensures a stale entry would still be served from the
        // cache unless it is actively flushed when content changes.
        config()->set('pecotamic.sitemap.expire', 3600);
    }

    private function makeEntry(string $slug): void
    {
        Entry::make()
            ->collection('pages')
            ->slug($slug)
            ->published(true)
            ->set('title', ucfirst($slug))
            ->save();
    }

    public function test_saving_an_entry_flushes_the_cached_sitemap()
    {
        $this->makeEntry('first');
        $this->get('/sitemap.xml')->assertSee('first', false);

        $this->makeEntry('second');

        $this->get('/sitemap.xml')->assertSee('second', false);
    }

    public function test_deleting_an_entry_flushes_the_cached_sitemap()
    {
        $this->makeEntry('keep');
        $entry = Entry::make()
            ->collection('pages')
            ->slug('remove')
            ->published(true)
            ->set('title', 'Remove');
        $entry->save();

        $this->get('/sitemap.xml')->assertSee('remove', false);

        $entry->delete();

        $this->get('/sitemap.xml')->assertDontSee('remove', false);
    }
}
