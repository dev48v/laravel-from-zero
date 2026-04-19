<?php

// STEP 4 — RecipeController scaffold.
//
// This controller is still empty. Every method returns a "not yet wired"
// 501 response so that `php artisan route:list` works and `curl`-ing any
// endpoint gives a clear message instead of a mysterious 500. The next
// commits will replace each method body one at a time.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TheMealDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // Laravel's service container auto-resolves this constructor — we get
    // a TheMealDBService instance on every request without any `new` call.
    public function __construct(private readonly TheMealDBService $meals) {}

    // STEP 6 — wired. GET /api/recipes/search?q=chicken.
    //
    // Validation is inline for now; step 12 replaces it with a dedicated
    // Form Request class so we can show how Laravel splits validation
    // off the controller when the rule set grows.
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json([
                'error'   => 'missing_query',
                'message' => "Pass a search term via ?q=, e.g. /api/recipes/search?q=chicken",
            ], 422);
        }

        $results = $this->meals->search($query);

        return response()->json([
            'query' => $query,
            'count' => count($results),
            'data'  => $results,
        ]);
    }

    // GET /api/recipes/{id} — return the full detail of one meal.
    public function show(int $id): JsonResponse
    {
        return $this->todo('show');
    }

    // GET /api/recipes/random — a single random meal from TheMealDB.
    public function random(): JsonResponse
    {
        return $this->todo('random');
    }

    // GET /api/ingredients/{ingredient}/recipes — meals containing an ingredient.
    public function byIngredient(string $ingredient): JsonResponse
    {
        return $this->todo('byIngredient');
    }

    // Single helper so every unfinished endpoint speaks the same shape.
    // Returning 501 (Not Implemented) is deliberately honest — 200 with
    // an empty array would look like a working endpoint with no data.
    private function todo(string $method): JsonResponse
    {
        return response()->json([
            'error'   => 'not_implemented',
            'method'  => $method,
            'message' => "RecipeController::{$method} is not wired yet.",
        ], 501);
    }
}
