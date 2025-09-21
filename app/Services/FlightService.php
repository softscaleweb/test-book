<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use DateTime;
use DateTimeZone;

class FlightService
{
    protected string $jsonPath;

    public function __construct()
    {
        $this->jsonPath = storage_path('app/mocks/flights.json');
    }

    /**
     * Search flights from the json mock with caching (5 minutes).
     **/
    public function search(array $filters = []): array
    {
        $cacheKey = $this->makeCacheKey($filters);

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $flights = $this->loadAll();
            $flights = array_filter($flights, function ($f) use ($filters) {
                // origin
                if (!empty($filters['origin']) && stripos($f['origin'] ?? '', $filters['origin']) === false) {
                    return false;
                }

                // destination
                if (!empty($filters['destination']) && stripos($f['destination'] ?? '', $filters['destination']) === false) {
                    return false;
                }

                // date: compare date part of 'departure'
                if (!empty($filters['date'])) {
                    $dep = $f['departure'] ?? null;
                    if (!$dep) {
                        return false;
                    }
                    $dt = new DateTime($dep);
                    $depDate = $dt->format('Y-m-d');
                    if ($depDate !== $filters['date']) {
                        return false;
                    }
                }

                // max_price compares with price_in_inr
                if (!empty($filters['max_price']) && isset($f['price_in_inr']) && $f['price_in_inr'] > (float) $filters['max_price']) {
                    return false;
                }

                return true;
            });

            $flights = array_values($flights);

            // Sort by price_in_inr
            if (!empty($filters['sort'])) {
                if ($filters['sort'] === 'price_asc') {
                    usort($flights, fn($a, $b) => $this->comparePrice($a, $b));
                } elseif ($filters['sort'] === 'price_desc') {
                    usort($flights, fn($a, $b) => $this->comparePrice($b, $a));
                }
            }

            return $flights;
        });
    }

    protected function loadAll(): array
    {
        if (!file_exists($this->jsonPath)) {
            return [];
        }

        $json = file_get_contents($this->jsonPath);
        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }

    public function findById($id): ?array
    {
        $flights = $this->loadAll();
        foreach ($flights as $f) {
            if (isset($f['id']) && (string) $f['id'] === (string) $id) {
                return $f;
            }
        }
        return null;
    }

    protected function comparePrice(array $a, array $b): int
    {
        $pa = isset($a['price_in_inr']) ? (float) $a['price_in_inr'] : 0.0;
        $pb = isset($b['price_in_inr']) ? (float) $b['price_in_inr'] : 0.0;

        return $pa <=> $pb;
    }

    protected function makeCacheKey(array $filters): string
    {
        $parts = [];
        ksort($filters);
        foreach ($filters as $k => $v) {
            $parts[] = "{$k}={$v}";
        }
        return 'flights:' . md5(implode('|', $parts));
    }
}
