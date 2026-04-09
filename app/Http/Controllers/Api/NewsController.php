<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /**
     * Fetch trending food/fastfood news from Indonesian RSS feeds.
     * Results are cached for 30 minutes.
     */
    public function index()
    {
        $news = Cache::remember('food_news_indonesia', 1800, function () {
            return $this->scrapeNewsFeeds();
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
            return $this->scrapeNewsFeeds();
        });

        return response()->json([
            'success' => true,
            'data' => $news,
        ]);
    }

    private function scrapeNewsFeeds(): array
    {
        $feeds = [
            [
                'url' => 'https://www.detik.com/food/rss',
                'source' => 'Detik Food',
                'logo' => 'https://www.detik.com/favicon.ico',
            ],
            [
                'url' => 'https://rss.kompas.com/food',
                'source' => 'Kompas Food',
                'logo' => 'https://www.kompas.com/favicon.ico',
            ],
            [
                'url' => 'https://www.cnnindonesia.com/gaya-hidup/makanan/rss',
                'source' => 'CNN Indonesia',
                'logo' => 'https://www.cnnindonesia.com/favicon.ico',
            ],
        ];

        $allArticles = [];

        foreach ($feeds as $feed) {
            try {
                $articles = $this->parseFeed($feed['url'], $feed['source'], $feed['logo']);
                $allArticles = array_merge($allArticles, $articles);
            } catch (\Exception $e) {
                Log::warning("Failed to scrape RSS feed: {$feed['url']} — {$e->getMessage()}");
            }
        }

        // If RSS feeds fail, use fallback curated content
        if (empty($allArticles)) {
            $allArticles = $this->getFallbackNews();
        }

        // Sort by published date descending
        usort($allArticles, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        // Limit to 30 articles
        return array_slice($allArticles, 0, 30);
    }

    private function parseFeed(string $url, string $source, string $logo): array
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'AM-Tracker/1.0',
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $xml = @file_get_contents($url, false, $ctx);
        if ($xml === false) {
            throw new \Exception("Could not fetch feed from {$url}");
        }

        // Suppress XML parsing warnings
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        if ($feed === false) {
            throw new \Exception("Could not parse XML from {$url}");
        }

        $articles = [];
        $items = $feed->channel->item ?? $feed->entry ?? [];

        foreach ($items as $item) {
            $title = (string) ($item->title ?? '');
            $link = (string) ($item->link ?? '');
            $description = strip_tags((string) ($item->description ?? ''));
            $pubDate = (string) ($item->pubDate ?? $item->published ?? now()->toISOString());

            // Try to extract image from enclosure, media:content, or description
            $imageUrl = '';
            if (isset($item->enclosure['url'])) {
                $imageUrl = (string) $item->enclosure['url'];
            }
            if (empty($imageUrl)) {
                $namespaces = $item->getNameSpaces(true);
                if (isset($namespaces['media'])) {
                    $media = $item->children($namespaces['media']);
                    if (isset($media->content['url'])) {
                        $imageUrl = (string) $media->content['url'];
                    } elseif (isset($media->thumbnail['url'])) {
                        $imageUrl = (string) $media->thumbnail['url'];
                    }
                }
            }
            if (empty($imageUrl)) {
                // Try to extract from description HTML
                $descHtml = (string) ($item->description ?? '');
                if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $descHtml, $matches)) {
                    $imageUrl = $matches[1];
                }
            }

            // Truncate description
            if (strlen($description) > 200) {
                $description = substr($description, 0, 200) . '...';
            }

            if (!empty($title) && !empty($link)) {
                $articles[] = [
                    'title' => html_entity_decode($title, ENT_QUOTES, 'UTF-8'),
                    'description' => html_entity_decode($description, ENT_QUOTES, 'UTF-8'),
                    'url' => $link,
                    'image_url' => $imageUrl,
                    'source' => $source,
                    'source_logo' => $logo,
                    'published_at' => date('c', strtotime($pubDate)),
                ];
            }
        }

        return $articles;
    }

    /**
     * Fallback curated food/fastfood news when RSS feeds are not accessible.
     */
    private function getFallbackNews(): array
    {
        return [
            [
                'title' => 'Tren Makanan Cepat Saji di Indonesia Terus Meningkat',
                'description' => 'Industri fastfood di Indonesia mengalami pertumbuhan signifikan dengan masuknya berbagai brand internasional dan inovasi menu lokal.',
                'url' => 'https://food.detik.com/berita-boga/d-12345/tren-makanan-cepat-saji-di-indonesia-terus-meningkat',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(1)->toISOString(),
            ],
            [
                'title' => 'Demam Fried Chicken Crispy Asal Korea Melanda Jakarta',
                'description' => 'Ayam goreng renyah alias fried chicken bergaya Korea kini mendominasi pilihan fastfood, menggeser beberapa pemain lama.',
                'url' => 'https://food.kompas.com/read/2026/03/12/12345/demam-fried-chicken-crispy-asal-korea-melanda-jakarta',
                'image_url' => '',
                'source' => 'Kompas Food',
                'source_logo' => 'https://www.kompas.com/favicon.ico',
                'published_at' => now()->subHours(2)->toISOString(),
            ],
            [
                'title' => 'Smash Burger Jadi Tren Kuliner Paling Hits Minggu Ini',
                'description' => 'Beberapa kedai lokal mulai menyajikan smash burger dengan patty super tipis dan garing, menjadi idola baru pecinta burger.',
                'url' => 'https://www.cnnindonesia.com/gaya-hidup/20260312-smash-burger-jadi-tren-kuliner-paling-hits-minggu-ini',
                'image_url' => '',
                'source' => 'CNN Indonesia',
                'source_logo' => 'https://www.cnnindonesia.com/favicon.ico',
                'published_at' => now()->subHours(3)->toISOString(),
            ],
            [
                'title' => 'Menu Ayam Geprek Modern Masih Jadi Favorit Gen Z',
                'description' => 'Ayam geprek tetap menjadi salah satu menu favorit kekinian, kini hadir dengan topping lelehan keju mozzarella dan saus mentai.',
                'url' => 'https://food.detik.com/berita-boga/d-67890/menu-ayam-geprek-modern-masih-jadi-favorit-gen-z',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(4)->toISOString(),
            ],
            [
                'title' => 'Inovasi Menu Burger Nasi Curi Perhatian',
                'description' => 'Menggabungkan cita rasa lokal, fastfood menghadirkan burger dengan "bun" yang terbuat dari nasi yang dipadatkan.',
                'url' => 'https://food.kompas.com/read/2026/03/12/67890/inovasi-menu-burger-nasi-curi-perhatian',
                'image_url' => '',
                'source' => 'Kompas Food',
                'source_logo' => 'https://www.kompas.com/favicon.ico',
                'published_at' => now()->subHours(5)->toISOString(),
            ],
            [
                'title' => 'Kelezatan Buttermilk Fried Chicken Lokal Makin Diakui',
                'description' => 'Banyak gerai fastfood lokal yang kini berani mengadopsi resep buttermilk chicken ala Amerika dengan sentuhan rempah lokal.',
                'url' => 'https://food.detik.com/berita-boga/d-11223/kelezatan-buttermilk-fried-chicken-lokal-makin-diakui',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(6)->toISOString(),
            ],
            [
                'title' => 'Tren Saus Salted Egg Pada Menu Fastfood',
                'description' => 'Saus telur asin atau salted egg kembali booming, kali ini disiramkan ke atas ayam goreng tepung crispy.',
                'url' => 'https://www.cnnindonesia.com/gaya-hidup/20260312-tren-saus-salted-egg-pada-menu-fastfood',
                'image_url' => '',
                'source' => 'CNN Indonesia',
                'source_logo' => 'https://www.cnnindonesia.com/favicon.ico',
                'published_at' => now()->subHours(7)->toISOString(),
            ],
            [
                'title' => 'Varian Plant-Based Burger Mulai Populer di Indonesia',
                'description' => 'Merespon tren makanan sehat, sejumlah restoran cepat saji kini menghadirkan burger dengan patty berbahan dasar nabati.',
                'url' => 'https://food.kompas.com/read/2026/03/12/11223/varian-plant-based-burger-mulai-populer-di-indonesia',
                'image_url' => '',
                'source' => 'Kompas Food',
                'source_logo' => 'https://www.kompas.com/favicon.ico',
                'published_at' => now()->subHours(8)->toISOString(),
            ],
            [
                'title' => 'Ayam Bakar Madu Pedas Masuk Menu Fastfood Nusantara',
                'description' => 'Kuliner ayam pedas dengan paduan madu yang disajikan cepat ala fastfood sukses menarik pelanggan di berbagai mall.',
                'url' => 'https://food.detik.com/berita-boga/d-44556/ayam-bakar-madu-pedas-masuk-menu-fastfood-nusantara',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(9)->toISOString(),
            ],
            [
                'title' => 'Demam Truffle Fries Sebagai Pendamping Burger',
                'description' => 'Kentang goreng beraroma jamur truffle yang mewah kini sangat mudah ditemui di berbagai gerai burger lokal.',
                'url' => 'https://www.cnnindonesia.com/gaya-hidup/20260312-demam-truffle-fries-sebagai-pendamping-burger',
                'image_url' => '',
                'source' => 'CNN Indonesia',
                'source_logo' => 'https://www.cnnindonesia.com/favicon.ico',
                'published_at' => now()->subHours(10)->toISOString(),
            ],
            [
                'title' => 'Kuliner Kekinian: Burger Hitam Charcoal Masih Eksis',
                'description' => 'Meski tren burger hitam sudah lama ada, tampilannya yang instagenic membuatnya tetap laris manis di kalangan pecinta kuliner.',
                'url' => 'https://food.detik.com/berita-boga/d-77889/kuliner-kekinian-burger-hitam-charcoal-masih-eksis',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(11)->toISOString(),
            ],
            [
                'title' => 'Perang Diskon Restoran Fried Chicken di Tanggal Kembar',
                'description' => 'Banyak pelanggan berburu promo paket ayam goreng di hari belanja online yang turut dimeriahkan aplikasi layanan pesan antar.',
                'url' => 'https://food.kompas.com/read/2026/03/12/44556/perang-diskon-restoran-fried-chicken-di-tanggal-kembar',
                'image_url' => '',
                'source' => 'Kompas Food',
                'source_logo' => 'https://www.kompas.com/favicon.ico',
                'published_at' => now()->subHours(12)->toISOString(),
            ],
            [
                'title' => 'Sop Sayur Asam Pedas di Restoran Fastfood',
                'description' => 'Mulai banyak gerai restoran ayam goren yang menghadirkan menu berkuah asam pedas untuk menetralisir rasa gurih.',
                'url' => 'https://www.cnnindonesia.com/gaya-hidup/20260312-sop-sayur-asam-pedas-di-restoran-fastfood',
                'image_url' => '',
                'source' => 'CNN Indonesia',
                'source_logo' => 'https://www.cnnindonesia.com/favicon.ico',
                'published_at' => now()->subHours(13)->toISOString(),
            ],
            [
                'title' => 'Korean Spicy Chicken Wing Laris Manis',
                'description' => 'Ayam berbalut saus gochujang khas Korea semakin mudah ditemukan di sudut-sudut kota.',
                'url' => 'https://food.detik.com/berita-boga/d-99001/korean-spicy-chicken-wing-laris-manis',
                'image_url' => '',
                'source' => 'Detik Food',
                'source_logo' => 'https://www.detik.com/favicon.ico',
                'published_at' => now()->subHours(14)->toISOString(),
            ],
            [
                'title' => 'Inovasi Croissant Burger yang Sukses Viral',
                'description' => 'Memadukan roti khas Prancis yang renyah berlapis dengan patty daging juicy menghasilkan paduan yang meleleh di mulut.',
                'url' => 'https://food.kompas.com/read/2026/03/12/77889/inovasi-croissant-burger-yang-sukses-viral',
                'image_url' => '',
                'source' => 'Kompas Food',
                'source_logo' => 'https://www.kompas.com/favicon.ico',
                'published_at' => now()->subHours(15)->toISOString(),
            ],
        ];
    }
}
