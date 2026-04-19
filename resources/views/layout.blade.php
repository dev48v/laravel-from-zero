{{-- STEP 14 — shared layout used by every Blade page. --}}
{{-- Tailwind via the Play CDN so there is zero build step. Students
     can clone, run `make serve`, and everything renders. Production
     sites would compile Tailwind locally, but for a "from zero"
     project the CDN keeps the mental model small. --}}
<!doctype html>
<html lang="en" class="bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Recipe Finder')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* A thin hand-written layer for things Tailwind can't do inline. */
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
        .chip { transition: transform .15s ease, background .15s ease; }
        .chip:hover { transform: translateY(-1px); }
    </style>
</head>
<body class="text-slate-100">
    <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="text-2xl">🍳</span>
                <span class="font-semibold tracking-tight text-lg">Recipe Finder</span>
                <span class="hidden sm:inline text-xs text-slate-500">· laravel-from-zero</span>
            </a>
            <nav class="text-sm text-slate-400 flex gap-4">
                <a href="/api/categories" class="hover:text-slate-200">API</a>
                <a href="https://github.com/dev48v/laravel-from-zero" target="_blank" class="hover:text-slate-200">GitHub</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-10">
        @yield('content')
    </main>

    <footer class="border-t border-slate-800 mt-20 text-center text-xs text-slate-500 py-6">
        Powered by <a class="underline hover:text-slate-300" href="https://www.themealdb.com" target="_blank">TheMealDB</a>
        · Day 18 of <a class="underline hover:text-slate-300" href="https://dev48v.infy.uk/techfromzero.php">TechFromZero</a>
    </footer>
</body>
</html>
