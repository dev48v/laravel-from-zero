<?php

// STEP 15 — RecipeWebController.
//
// Serves the single-recipe detail page. Kept in its own class (not on
// HomeController) because the responsibilities are distinct: list vs detail.

namespace App\Http\Controllers;

use App\Services\TheMealDBService;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeWebController extends Controller
{
    public function __construct(private readonly TheMealDBService $meals) {}

    // GET /recipes/{id} — full page for one meal, or 404 if TheMealDB has no match.
    public function show(int $id): View
    {
        $meal = $this->meals->getById($id);

        if ($meal === null) {
            // Throwing here instead of returning a 404 view means Laravel's
            // default error page kicks in, with the right HTTP status set.
            throw new NotFoundHttpException("Recipe {$id} not found");
        }

        // TheMealDB stores ingredients as 20 parallel keys:
        //   strIngredient1..20 and strMeasure1..20
        // Collapse them into a clean [[ingredient, measure], ...] list here
        // so the Blade view stays declarative.
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $name   = trim((string) ($meal["strIngredient{$i}"] ?? ''));
            $amount = trim((string) ($meal["strMeasure{$i}"] ?? ''));
            if ($name !== '') {
                $ingredients[] = ['name' => $name, 'amount' => $amount];
            }
        }

        return view('recipe', compact('meal', 'ingredients'));
    }
}
