<?php

// Web routes — render Blade views for humans.
// API routes live in routes/api.php.

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/recipes/{id}', [RecipeWebController::class, 'show'])
    ->whereNumber('id')
    ->name('recipes.show');
