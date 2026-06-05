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
        $items = $feed->get_items();

        // If it is a GitHub Release Atom feed, filter to get the latest stable release and any newer RC/Beta versions
        if (str_contains($source->url, 'github.com') && (str_contains($source->url, 'releases.atom') || $source->type === 'atom')) {
            $filteredItems = [];
            $foundStable = false;
            foreach ($items as $item) {
                $title = $item->get_title();
                
                // Skip raw development or test tags
                if (preg_match('/alpha|dev|pre|test/i', $title)) {
                    continue;
                }
                
                // Detect pre-release candidates (RC or Beta)
                $isPreRelease = preg_match('/rc\d+|beta/i', $title);
                
                if ($isPreRelease) {
                    // Only include if we haven't found the latest stable yet (meaning they are newer)
                    if (!$foundStable) {
                        $filteredItems[] = $item;
                    }
                } else {
                    // Include the stable release and stop looking at older entries
                    $filteredItems[] = $item;
                    $foundStable = true;
                    break;
                }
            }
            $items = $filteredItems;
        }
        
        foreach ($items as $item) {
            // Check publication date dynamically using max_age_days from the source model
            $timestamp = $item->get_date('U');
            $currentTime = time();
            $maxAgeDays = (int) ($source->max_age_days ?? 1);
            $maxAgeSeconds = $maxAgeDays * 24 * 3600;
            $thresholdTime = $currentTime - $maxAgeSeconds;

            if ($timestamp && (int)$timestamp < $thresholdTime) {
                continue;
            }

            $publishedAt = $timestamp ? Carbon::createFromTimestamp((int)$timestamp) : now();

            $url = $item->get_permalink();
            $title = $item->get_title();
            
            // Basic de-duplication
            $hash = hash('sha256', $title . $url);
            
            if (RawArticle::where('hash', $hash)->exists()) {
                continue;
            }

            $content = $item->get_content();
            
            // Jina Reader Fallback for truncated content
            if (strlen(strip_tags($content)) < 300) {
                /** @var \App\Services\ScraperService $scraper */
                $scraper = app(\App\Services\ScraperService::class);
                $scrapedContent = $scraper->scrape($url);
                if ($scrapedContent) {
                    $content = $scrapedContent;
                }
            }

            $description = $item->get_description();
            $authorItem = $item->get_author();
            $author = $authorItem ? $authorItem->get_name() : null;

            // Find image
            $imageUrl = $this->extractImage($item);

            RawArticle::create([
                'source_id' => $source->id,
                'title' => $title,
                'url' => $url,
                'content' => $content,
                'summary' => strip_tags($description),
                'author' => $author,
                'published_at' => $publishedAt,
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
