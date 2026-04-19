# laravel-from-zero

Recipe Finder — a real Laravel 11 REST API + Blade frontend that proxies [TheMealDB](https://www.themealdb.com/api.php).

Built step by step from an empty folder. Every commit is one concept — clone the repo and read the history if you want to learn Laravel by following real code.

---

## Quick Start (no PHP install needed)

You only need **Docker**. Everything else — PHP 8.2, Composer, the app itself — runs inside short-lived containers.

```bash
# 1. Clone
git clone https://github.com/dev48v/laravel-from-zero.git
cd laravel-from-zero

# 2. Install PHP dependencies
make composer CMD="install"
# Windows PowerShell users:
#   .\run.ps1 composer install

# 3. Environment + app key
cp .env.example .env
make artisan CMD="key:generate"

# 4. Migrations (SQLite file — no DB server needed)
make artisan CMD="migrate"

# 5. Boot on http://localhost:8000
make serve
```

Open `http://localhost:8000` for the search UI, or hit the API:

```bash
curl 'http://localhost:8000/api/recipes/search?q=chicken' | jq
curl 'http://localhost:8000/api/categories' | jq
curl 'http://localhost:8000/api/recipes/random' | jq
```

---

## What's inside

- **REST API** — 7 endpoints covering search, detail, random, categories, filters, health.
- **Blade frontend** — dark-theme Tailwind UI at `/` with search box, category chips, and a detail page at `/recipes/{id}`.
- **One service for upstream calls** — `TheMealDBService` is the only class that talks to TheMealDB; everything else goes through it.
- **10-minute cache** — every upstream response is cached; responses carry `X-Cache: HIT|MISS` so you can watch it working.
- **Form-request validation**, **global JSON error handler**, **`/api/health`** probe.

---

## API reference

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/recipes/search?q={term}` | Search meals by name (2–50 chars). |
| GET | `/api/recipes/{id}` | Full meal detail by TheMealDB id. |
| GET | `/api/recipes/random` | One random meal (never cached). |
| GET | `/api/categories` | All meal categories. |
| GET | `/api/categories/{category}/recipes` | Meals inside a category (id/name/thumb). |
| GET | `/api/ingredients/{ingredient}/recipes` | Meals containing an ingredient. |
| GET | `/api/health` | Liveness probe — reports cache status. |

Error responses are always JSON:
```json
{ "error": "ValidationException", "status": 422, "message": "..." }
```

---

## Step-by-step commits

Each commit is one teaching moment. Clone the repo and run `git log --oneline --reverse` to walk through.

| Step | Concept |
|---|---|
| 1 | Scaffold Laravel 11 via Docker composer, wrappers (`Makefile`, `run.ps1`) |
| 2 | Add TheMealDB config + `.env` keys |
| 3 | Define API routes (`routes/api.php`) pointing at stub controllers |
| 4 | Scaffold `RecipeController` + `CategoryController` with 501 stubs |
| 5 | `TheMealDBService` — single place for upstream HTTP calls |
| 6 | Wire `/api/recipes/search` to the service |
| 7 | Add `/api/recipes/{id}` with 404 on miss |
| 8 | Add `/api/recipes/random` with 502 on empty upstream |
| 9 | Add `/api/categories` + `/api/categories/{cat}/recipes` |
| 10 | Add `/api/ingredients/{ingredient}/recipes` |
| 11 | 10-minute cache layer + `X-Cache: HIT/MISS` response header |
| 12 | `SearchRecipeRequest` — move validation into a Form Request |
| 13 | Global JSON exception handler + `/api/health` endpoint |
| 14 | Blade home page — search, category chips, Tailwind UI |
| 15 | Detail page at `/recipes/{id}` + full README |

---

## Laravel concepts covered

- **Routing** — grouped routes, controller resolution, typed route params.
- **Controllers** — constructor injection, single-responsibility methods.
- **Services** — separation of concerns, the service container.
- **HTTP client** — `Http::baseUrl()->acceptJson()->timeout()`.
- **Cache** — `Cache::remember`, `Cache::has`, TTL.
- **Form Requests** — validation rules, `prepareForValidation`, custom messages.
- **Exception handling** — Laravel 11's `bootstrap/app.php` `withExceptions`.
- **Blade** — layouts, `@extends`/`@section`/`@yield`, control structures.
- **Config** — `config/services.php`, `env()` vs `config()`.

---

## Tech stack

| | |
|---|---|
| **Framework** | Laravel 11 |
| **Language** | PHP 8.4 |
| **Runtime** | Docker (`php:8.4-cli`, `composer:2`) |
| **Frontend** | Blade + Tailwind (Play CDN) |
| **Storage** | SQLite (cache + sessions) |
| **Upstream API** | [TheMealDB](https://www.themealdb.com) |

---

## Explore the code

- [`routes/api.php`](routes/api.php) — every API endpoint in one file
- [`app/Services/TheMealDBService.php`](app/Services/TheMealDBService.php) — upstream HTTP calls + caching
- [`app/Http/Controllers/Api/RecipeController.php`](app/Http/Controllers/Api/RecipeController.php) — API surface
- [`app/Http/Requests/SearchRecipeRequest.php`](app/Http/Requests/SearchRecipeRequest.php) — validation
- [`bootstrap/app.php`](bootstrap/app.php) — exception handler wiring
- [`resources/views/home.blade.php`](resources/views/home.blade.php) — Blade frontend
