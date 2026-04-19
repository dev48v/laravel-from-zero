<?php

// STEP 4 — CategoryController scaffold.
//
// Mirrors RecipeController: empty methods that return 501 so the routing
// layer is demonstrably live even though no TheMealDB calls exist yet.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TheMealDBService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // Same pattern as RecipeController: service injected by the container.
    public function __construct(private readonly TheMealDBService $meals) {}

    // GET /api/categories.
    public function index(): JsonResponse
    {
        $categories = $this->meals->categories();

        return $this->cached(response()->json([
            'count' => count($categories),
            'data'  => $categories,
        ]));
    }

    // GET /api/categories/{category}/recipes.
    //
    // URL decoding note: Laravel already url-decodes route params, so a
    // request for `/api/categories/Side%20Dish/recipes` arrives here with
    // $category = "Side Dish" (space, not %20) — we pass that straight
    // through to TheMealDB, which re-encodes it inside the Http client.
    public function recipes(string $category): JsonResponse
    {
        $recipes = $this->meals->filterByCategory($category);

        return $this->cached(response()->json([
            'category' => $category,
            'count'    => count($recipes),
            'data'     => $recipes,
        ]));
    }

    // STEP 11 — tag every response with X-Cache: HIT|MISS.
    private function cached(JsonResponse $response): JsonResponse
    {
        return $response->header(
            'X-Cache',
            $this->meals->wasLastCallCached() ? 'HIT' : 'MISS'
        );
    }
}
