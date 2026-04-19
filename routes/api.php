<?php

// STEP 3 — API route skeleton.
//
// Laravel 11 automatically prefixes every route defined here with `/api`,
// and applies the `api` route group middleware stack (throttle, bindings).
// We point each URL at a controller method now, even though those methods
// are still empty — wiring the routes first makes `php artisan route:list`
// show the full API surface from day one.

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Support\Facades\Route;

// Recipe endpoints — anything about a single dish or a search over dishes.
Route::prefix('recipes')->controller(RecipeController::class)->group(function () {
    Route::get('/search', 'search');          // GET /api/recipes/search?q=chicken
    Route::get('/random', 'random');          // GET /api/recipes/random
    Route::get('/{id}',  'show')->whereNumber('id'); // GET /api/recipes/52772
});

// Category endpoints — list categories or list recipes inside a category.
Route::prefix('categories')->controller(CategoryController::class)->group(function () {
    Route::get('/',                        'index');          // GET /api/categories
    Route::get('/{category}/recipes',      'recipes');        // GET /api/categories/Seafood/recipes
});

// Ingredient filter — "show me every recipe that uses chicken_breast".
Route::get('/ingredients/{ingredient}/recipes', [RecipeController::class, 'byIngredient']);
