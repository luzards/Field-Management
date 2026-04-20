<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    private const NEWS_API_URL = 'https://newsapi.org/v2';

    private function getApiKey(): string
    {
        return env('NEWS_API_KEY', '9f41b59aee8f41a59b69aa0f9cfd6321');
    }

    /**
     * Fetch trending food/fastfood news from NewsAPI.org.
     * Results are cached for 30 minutes.
     */
    public function index()
    {
        $news = Cache::remember('food_news_indonesia', 1800, function () {
            return $this->fetchNewsFromApi();
        });

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    /**
     * Force refresh the news cache.
     */
    public function refresh()
    {
        Cache::forget('food_news_indonesia');
        $news = Cache::remember('food_news_indonesia', 1800, function () {
            return $this->fetchNewsFromApi();
        });

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    /**
     * Fetch news from NewsAPI.org using multiple queries for broader coverage.
     */
    private function fetchNewsFromApi(): array
    {
        $allArticles = [];

        // Search queries for trending fastfood in Indonesia
        $queries = [
            'fastfood Indonesia trending',
            'KFC McDonalds Burger King Indonesia',
            'ayam goreng cepat saji Indonesia',
            'restoran cepat saji promo Indonesia',
            'Pizza Hut Domino tren makanan Indonesia',
            'bisnis kuliner franchise F&B Indonesia',
            'HokBen Richeese Factory promo Indonesia',
            'kopi kekinian tren minuman Indonesia',
            'Gofood Grabfood diskon restoran Indonesia',
            'industri makanan minuman kuliner Indonesia',
        ];

        // Economics queries for Indonesia
        $economicsQueries = [
            'harga BBM naik Indonesia',
            'kurs Rupiah dolar USD Indonesia',
            'ekonomi Indonesia inflasi',
        ];

        // Fetch trending fastfood news
        foreach ($queries as $query) {
            try {
                $articles = $this->searchNews($query);
                $allArticles = array_merge($allArticles, $articles);
            } catch (\Exception $e) {
                Log::warning("NewsAPI query failed: {$query} — {$e->getMessage()}");
            }
        }

        // Fetch economics news
        foreach ($economicsQueries as $query) {
            try {
                $articles = $this->searchNews($query, 5);
                $allArticles = array_merge($allArticles, $articles);
            } catch (\Exception $e) {
                Log::warning("NewsAPI economics query failed: {$query} — {$e->getMessage()}");
            }
        }

        // Also fetch top headlines from Indonesia
        try {
            $headlines = $this->getTopHeadlines();
            $allArticles = array_merge($allArticles, $headlines);
        } catch (\Exception $e) {
            Log::warning("NewsAPI top headlines failed: {$e->getMessage()}");
        }

        // Remove duplicates by URL
        $seen = [];
        $unique = [];
        foreach ($allArticles as $article) {
            if (!isset($seen[$article['url']])) {
                $seen[$article['url']] = true;
                $unique[] = $article;
            }
        }

        // Sort by published date descending
        usort($unique, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        // Limit to 30 articles
        return array_slice($unique, 0, 30);
    }

    /**
     * Search news using the /everything endpoint.
     */
    private function searchNews(string $query, int $pageSize = 15): array
    {
        $response = Http::timeout(15)->get(self::NEWS_API_URL . '/everything', [
            'q' => $query,
            'language' => 'id',
            'sortBy' => 'publishedAt',
            'pageSize' => $pageSize,
            'apiKey' => $this->getApiKey(),
        ]);

        if (!$response->successful()) {
            throw new \Exception("NewsAPI returned status {$response->status()}: {$response->body()}");
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'ok') {
            throw new \Exception("NewsAPI error: " . ($data['message'] ?? 'Unknown error'));
        }

        return $this->mapArticles($data['articles'] ?? []);
    }

    /**
     * Get top headlines from Indonesia.
     */
    private function getTopHeadlines(): array
    {
        $response = Http::timeout(15)->get(self::NEWS_API_URL . '/top-headlines', [
            'country' => 'id',
            'category' => 'general',
            'pageSize' => 15,
            'apiKey' => $this->getApiKey(),
        ]);

        if (!$response->successful()) {
            throw new \Exception("NewsAPI headlines returned status {$response->status()}");
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'ok') {
            throw new \Exception("NewsAPI headlines error: " . ($data['message'] ?? 'Unknown error'));
        }

        return $this->mapArticles($data['articles'] ?? []);
    }

    /**
     * Map NewsAPI article format to our standard format.
     */
    private function mapArticles(array $articles): array
    {
        $mapped = [];

        foreach ($articles as $article) {
            $title = $article['title'] ?? '';
            $url = $article['url'] ?? '';

            // Skip removed articles
            if (empty($title) || empty($url) || $title === '[Removed]') {
                continue;
            }

            $description = $article['description'] ?? '';
            if (strlen($description) > 200) {
                $description = substr($description, 0, 200) . '...';
            }

            $sourceName = $article['source']['name'] ?? 'Unknown';
            $imageUrl = $article['urlToImage'] ?? '';
            $publishedAt = $article['publishedAt'] ?? now()->toISOString();

            // Generate a source logo URL using Google's favicon service
            $sourceDomain = parse_url($url, PHP_URL_HOST) ?? '';
            $sourceLogo = $sourceDomain ? "https://www.google.com/s2/favicons?domain={$sourceDomain}&sz=64" : '';

            $mapped[] = [
                'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
                'description' => html_entity_decode($description, ENT_QUOTES, 'UTF-8'),
                'url' => $url,
                'image_url' => $imageUrl,
                'source' => $sourceName,
                'source_logo' => $sourceLogo,
                'published_at' => date('c', strtotime($publishedAt)),
            ];
        }

        return $mapped;
    }

}
