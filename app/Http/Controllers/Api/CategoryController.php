<?php

// STEP 4 — CategoryController scaffold.
//
// Mirrors RecipeController: empty methods that return 501 so the routing
// layer is demonstrably live even though no TheMealDB calls exist yet.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // GET /api/categories — every category TheMealDB knows about.
    public function index(): JsonResponse
    {
        return $this->todo('index');
    }

    // GET /api/categories/{category}/recipes — list meals inside one category.
    public function recipes(string $category): JsonResponse
    {
        return $this->todo('recipes');
    }

    private function todo(string $method): JsonResponse
    {
        return response()->json([
            'error'   => 'not_implemented',
            'method'  => $method,
            'message' => "CategoryController::{$method} is not wired yet.",
        ], 501);
    }
}
