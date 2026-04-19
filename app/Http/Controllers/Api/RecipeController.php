<?php

// RecipeController — handles every recipe-shaped endpoint.
//
// Each public method is wired to one route from routes/api.php. The actual
// HTTP call to TheMealDB lives in TheMealDBService; this class only takes
// input, asks the service, and shapes a response.
//
// STEP 11 update: every response now carries an `X-Cache: HIT|MISS` header
// so callers can see whether the answer came from Laravel's cache or the
// upstream API. Great for teaching and for dashboards.

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

    // GET /api/recipes/search?q=chicken.
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

        return $this->cached(response()->json([
            'query' => $query,
            'count' => count($results),
            'data'  => $results,
        ]));
    }

    // GET /api/recipes/{id}.
    //
    // Singular endpoint, so `data` is the meal object (not wrapped in a list).
    // 404 when the meal doesn't exist — honest contract over a silent 200.
    public function show(int $id): JsonResponse
    {
        $meal = $this->meals->getById($id);

        if ($meal === null) {
            return response()->json([
                'error'   => 'not_found',
                'id'      => $id,
                'message' => "No recipe with id {$id}.",
            ], 404);
        }

        return $this->cached(response()->json(['data' => $meal]));
    }

    // GET /api/recipes/random.
    //
    // Service intentionally skips caching for this one; a cached "random"
    // is not random. So X-Cache here is always MISS.
    public function random(): JsonResponse
    {
        $meal = $this->meals->random();

        if ($meal === null) {
            return response()->json([
                'error'   => 'upstream_empty',
                'message' => 'TheMealDB returned no meal. Try again in a moment.',
            ], 502);
        }

        return $this->cached(response()->json(['data' => $meal]));
    }

    // GET /api/ingredients/{ingredient}/recipes.
    //
    // TheMealDB expects underscores instead of spaces for this endpoint
    // (chicken_breast, not chicken%20breast). We rewrite here so our API
    // stays friendly and users can just pass "chicken breast".
    public function byIngredient(string $ingredient): JsonResponse
    {
        $upstream = str_replace(' ', '_', trim($ingredient));
        $recipes  = $this->meals->filterByIngredient($upstream);

        return $this->cached(response()->json([
            'ingredient' => $ingredient,
            'count'      => count($recipes),
            'data'       => $recipes,
        ]));
    }

    // Tag the response with X-Cache based on what the service just did.
    // Moved into a helper so each endpoint stays one-line readable.
    private function cached(JsonResponse $response): JsonResponse
    {
        return $response->header(
            'X-Cache',
            $this->meals->wasLastCallCached() ? 'HIT' : 'MISS'
        );
    }
}
