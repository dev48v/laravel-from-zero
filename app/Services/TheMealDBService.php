<?php

// STEP 11 — cache every upstream call.
//
// Why here (inside the service) instead of the controllers:
//   * Every endpoint benefits without duplicating cache boilerplate.
//   * Tests of controllers never need to know about caching.
//   * Swapping the backing store (file, redis) is a .env change.
//
// Cache keys are stable, human-readable, and namespaced under `tmdb:` so
// they're easy to flush with `php artisan cache:forget tmdb:search:chicken`.
// `Cache::remember` returns the cached value if present or calls the
// closure, stores its result, and returns it — one-line memoisation.
//
// We also expose a `wasLastCallCached()` probe so step 13's /api/health
// and the `X-Cache` header can report the last lookup's hit/miss status
// without the caller re-walking the cache store.

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TheMealDBService
{
    private string $baseUrl;
    private int    $ttl;
    private bool   $lastWasCached = false;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.themealdb.base_url'), '/');
        $this->ttl     = (int) config('services.themealdb.cache_ttl');
    }

    public function search(string $query): array
    {
        return $this->cached("search:" . strtolower($query), function () use ($query) {
            return $this->client()->get('/search.php', ['s' => $query])->json('meals') ?? [];
        });
    }

    public function getById(int|string $id): ?array
    {
        return $this->cached("lookup:{$id}", function () use ($id) {
            $meals = $this->client()->get('/lookup.php', ['i' => $id])->json('meals');
            return $meals[0] ?? null;
        });
    }

    // Random is intentionally NOT cached — caching a random endpoint would
    // pin a single meal for the whole TTL, which defeats the point.
    public function random(): ?array
    {
        $this->lastWasCached = false;
        $meals = $this->client()->get('/random.php')->json('meals');

        return $meals[0] ?? null;
    }

    public function categories(): array
    {
        return $this->cached('categories', function () {
            return $this->client()->get('/categories.php')->json('categories') ?? [];
        });
    }

    public function filterByCategory(string $category): array
    {
        return $this->cached("cat:" . strtolower($category), function () use ($category) {
            return $this->client()->get('/filter.php', ['c' => $category])->json('meals') ?? [];
        });
    }

    public function filterByIngredient(string $ingredient): array
    {
        return $this->cached("ing:" . strtolower($ingredient), function () use ($ingredient) {
            return $this->client()->get('/filter.php', ['i' => $ingredient])->json('meals') ?? [];
        });
    }

    // True when the most recent method call was served from cache.
    // Used by controllers / health checks to set response headers.
    public function wasLastCallCached(): bool
    {
        return $this->lastWasCached;
    }

    // Thin wrapper around Cache::remember that records hit/miss for the
    // last lookup. We check Cache::has() first so we can distinguish hit
    // from miss — remember() alone hides that information.
    private function cached(string $key, \Closure $miss): mixed
    {
        $fullKey = "tmdb:{$key}";
        $this->lastWasCached = Cache::has($fullKey);

        return Cache::remember($fullKey, $this->ttl, $miss);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(10);
    }
}
