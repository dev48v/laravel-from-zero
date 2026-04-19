<?php

// STEP 14 — HomeController.
//
// Serves the Blade frontend at GET /. This is the same app as the API;
// Blade and the API both lean on TheMealDBService, which means the
// cache they share gives the frontend free speed.
//
// Two URL patterns render on this one route:
//   /                        → empty state + category chips
//   /?q=chicken              → text search (uses /search.php upstream)
//   /?category=Seafood       → category filter (uses /filter.php upstream)
// The view handles all three.

namespace App\Http\Controllers;

use App\Services\TheMealDBService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly TheMealDBService $meals) {}

    public function index(Request $request): View
    {
        $query      = trim((string) $request->query('q', ''));
        $category   = trim((string) $request->query('category', ''));
        $categories = $this->meals->categories();

        // Precedence: query term beats category filter. Keeps behaviour
        // obvious when someone crafts a URL with both params set.
        $results = match (true) {
            $query !== ''    => $this->meals->search($query),
            $category !== '' => $this->meals->filterByCategory($category),
            default          => [],
        };

        return view('home', compact('query', 'category', 'categories', 'results'));
    }
}
