<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class HotelService
{
    protected string $jsonPath;

    public function __construct()
    {
        $this->jsonPath = storage_path('app/mocks/hotels.json');
    }

    /**
     * Search hotels from the JSON mock with caching (5 minutes).
     */
    public function search(array $filters = []): array
    {
        $cacheKey = $this->makeCacheKey($filters);

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $hotels = $this->loadAll();

            $hotels = array_filter($hotels, function ($h) use ($filters) {
                // city
                if (!empty($filters['city']) && stripos($h['city'] ?? '', $filters['city']) === false) {
                    return false;
                }

                // max_price compares with price_per_night_in_inr
                if (!empty($filters['max_price']) && isset($h['price_per_night_in_inr']) && $h['price_per_night_in_inr'] > (float) $filters['max_price']) {
                    return false;
                }

                return true;
            });

            $hotels = array_values($hotels);

            // Sort by price_per_night_in_inr
            if (!empty($filters['sort'])) {
                if ($filters['sort'] === 'price_asc') {
                    usort($hotels, fn($a, $b) => $this->comparePrice($a, $b));
                } elseif ($filters['sort'] === 'price_desc') {
                    usort($hotels, fn($a, $b) => $this->comparePrice($b, $a));
                }
            }

            return $hotels;
        });
    }

    protected function loadAll(): array
    {
        if (!file_exists($this->jsonPath)) {
            \Log::warning('Hotel mock not found', ['path' => $this->jsonPath]);
            return [];
        }

        $json = file_get_contents($this->jsonPath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('Invalid hotels.json', ['error' => json_last_error_msg()]);
            return [];
        }

        return is_array($data) ? $data : [];
    }

    public function findById(string $id): ?array
    {
        $hotels = $this->loadAll();
        foreach ($hotels as $h) {
            if (isset($h['id']) && (string) $h['id'] === (string) $id) {
                return $h;
            }
        }
        return null;
    }

    protected function comparePrice(array $a, array $b): int
    {
        $pa = isset($a['price_per_night_in_inr']) ? (float) $a['price_per_night_in_inr'] : 0.0;
        $pb = isset($b['price_per_night_in_inr']) ? (float) $b['price_per_night_in_inr'] : 0.0;

        return $pa <=> $pb;
    }

    protected function makeCacheKey(array $filters): string
    {
        $parts = [];
        ksort($filters);
        foreach ($filters as $k => $v) {
            $parts[] = "{$k}={$v}";
        }
        return 'hotels:' . md5(implode('|', $parts));
    }
}
