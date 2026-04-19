# laravel-from-zero

Recipe Finder — a real Laravel 11 REST API + Blade frontend that proxies [TheMealDB](https://www.themealdb.com/api.php).

Built step by step from an empty folder. Every commit is one concept — clone and read the history if you want to learn Laravel by following real code.

> Full step-by-step guide will appear here as the project grows.

---

## Quick Start (no PHP install needed)

You only need **Docker**. Everything else — PHP 8.2, Composer, the app itself — runs inside containers.

```bash
# 1. Clone
git clone https://github.com/dev48v/laravel-from-zero.git
cd laravel-from-zero

# 2. Install PHP dependencies (uses composer:2 image)
make composer CMD="install"
# Windows PowerShell:
#   .\run.ps1 composer install

# 3. Copy env + generate app key
cp .env.example .env
make artisan CMD="key:generate"

# 4. Run migrations (SQLite file — no DB server needed)
make artisan CMD="migrate"

# 5. Boot the app on http://localhost:8000
make serve
```

Open `http://localhost:8000` for the Blade UI (once the frontend ships), or hit `http://localhost:8000/api/recipes/search?q=chicken` for the API.
