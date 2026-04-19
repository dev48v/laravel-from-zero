<?php

// STEP 5 — TheMealDBService.
//
// One class, one responsibility: every call that leaves this app and hits
// TheMealDB goes through here. Controllers never construct URLs or parse
// upstream JSON themselves — they ask this service for PHP arrays.
//
// Why a service and not inline HTTP in the controller:
//   * Testable: can be swapped with Http::fake() in one line.
//   * Caching gets added in step 11 without touching controllers.
//   * If TheMealDB ever changes (new version, new host), only this file moves.

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TheMealDBService
{
    private string $baseUrl;

    public function __construct()
    {
        // Config, not env(), because env() is only reliable before config
        // is cached. Reading config keys keeps `php artisan config:cache`
        // safe in production.
        $this->baseUrl = rtrim(config('services.themealdb.base_url'), '/');
    }

    // GET {base}/search.php?s={query} — returns meals matching name.
    public function search(string $query): array
    {
        $json = $this->client()->get('/search.php', ['s' => $query])->json();

        // TheMealDB returns `{"meals": null}` when nothing matches. Normalising
        // that to an empty array means callers can always `foreach` the result.
        return $json['meals'] ?? [];
    }

    // GET {base}/lookup.php?i={id} — returns a single meal or null.
    public function getById(int|string $id): ?array
    {
        $meals = $this->client()->get('/lookup.php', ['i' => $id])->json('meals');

        return $meals[0] ?? null;
    }

    // GET {base}/random.php — returns one random meal, never null from upstream.
    public function random(): ?array
    {
        $meals = $this->client()->get('/random.php')->json('meals');

        return $meals[0] ?? null;
    }

    // GET {base}/categories.php — list of all top-level categories.
    public function categories(): array
    {
        return $this->client()->get('/categories.php')->json('categories') ?? [];
    }

    // GET {base}/filter.php?c={category} — lightweight meal list for a category.
    // NOTE: TheMealDB's filter endpoint returns *only* id/name/thumb, not full
    // detail. Callers that want full detail must loop via getById(). That's a
    // deliberate upstream API choice — we mirror it instead of hiding it.
    public function filterByCategory(string $category): array
    {
        return $this->client()->get('/filter.php', ['c' => $category])->json('meals') ?? [];
    }

    // GET {base}/filter.php?i={ingredient} — same shape as filterByCategory.
    public function filterByIngredient(string $ingredient): array
    {
        return $this->client()->get('/filter.php', ['i' => $ingredient])->json('meals') ?? [];
    }

    // Single place to configure the HTTP client used for every upstream call.
    // `acceptJson()` sets the Accept header, `timeout(10)` bounds slow upstreams,
    // `baseUrl()` lets each method above use short paths like '/search.php'.
    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(10);
    }
}
