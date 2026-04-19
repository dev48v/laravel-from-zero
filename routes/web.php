<?php

// STEP 14 — swap the default welcome page for the real Blade frontend.

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
