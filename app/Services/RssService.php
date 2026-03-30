<?php

namespace App\Services;

use App\Models\RawArticle;
use App\Models\Source;
use Carbon\Carbon;
use SimplePie\SimplePie;

class RssService
{
    /**
     * Fetch and parse an RSS feed for a given source.
     *
     * @param Source $source
     * @return int Number of new articles saved.
     */
    public function fetchSource(Source $source): int
    {
        $feed = new SimplePie();
        $feed->set_feed_url($source->url);
        $feed->set_cache_location(storage_path('framework/cache'));
        $feed->init();
        $feed->handle_content_type();

        if ($feed->error()) {
            $source->increment('score', -5); // Penalty for error
            return 0;
        }

        $newArticlesCount = 0;
        
        foreach ($feed->get_items() as $item) {
            $url = $item->get_permalink();
            $title = $item->get_title();
            
            // Basic de-duplication
            $hash = hash('sha256', $title . $url);
            
            if (RawArticle::where('hash', $hash)->exists()) {
                continue;
            }

            $content = $item->get_content();
            $description = $item->get_description();
            $authorItem = $item->get_author();
            $author = $authorItem ? $authorItem->get_name() : null;
            $publishedAt = $item->get_date('Y-m-d H:i:s');

            // Find image
            $imageUrl = $this->extractImage($item);

            RawArticle::create([
                'source_id' => $source->id,
                'title' => $title,
                'url' => $url,
                'content' => $content,
                'summary' => strip_tags($description),
                'author' => $author,
                'published_at' => $publishedAt ? Carbon::parse($publishedAt) : now(),
                'hash' => $hash,
                'image_url' => $imageUrl,
                'status' => 'pending',
                'metadata' => [
                    'feed_id' => $item->get_id(),
                    'categories' => array_map(fn($cat) => $cat->get_label(), $item->get_categories() ?? []),
                ],
            ]);

            $newArticlesCount++;
        }

        // Update source stats
        $source->update([
            'last_fetched_at' => now(),
        ]);
        
        if ($newArticlesCount > 0) {
            $source->increment('score', 2); // Small boost for freshness
        }

        return $newArticlesCount;
    }

    /**
     * Extract an image URL from a feed item.
     */
    protected function extractImage($item): ?string
    {
        // 1. Try enclosure
        if ($enclosure = $item->get_enclosure()) {
            if ($enclosure->get_link() && str_contains($enclosure->get_type() ?? '', 'image')) {
                return $enclosure->get_link();
            }
        }

        return null;
    }
}
