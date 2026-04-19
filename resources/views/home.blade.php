{{-- STEP 14 — home page.
     Two states in one template:
       1. No ?q= in the URL → show the full category grid so users can browse.
       2. ?q=chicken → show search results above the category grid.
     Rendering both states in one Blade file keeps the URL design simple
     and means bookmarking /?q=pasta always works. --}}
@extends('layout')

@section('title', 'Recipe Finder — search any dish')

@section('content')
    <section class="text-center mb-10">
        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight">Find a recipe.</h1>
        <p class="text-slate-400 mt-3">Search any dish name, or browse by category.</p>

        <form action="{{ route('home') }}" method="GET"
              class="mt-6 flex max-w-xl mx-auto gap-2">
            <input type="text" name="q" value="{{ $query }}" autofocus
                   placeholder="e.g. pasta, sushi, brownie"
                   class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-3
                          focus:outline-none focus:border-blue-500">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                           rounded-lg px-6 font-medium">Search</button>
        </form>
    </section>

    @if ($query !== '' || $category !== '')
        <section class="mb-12">
            <h2 class="text-xl font-semibold mb-4">
                @if (count($results))
                    @if ($query !== '')
                        Results for "<span class="text-blue-400">{{ $query }}</span>"
                    @else
                        <span class="text-blue-400">{{ $category }}</span> recipes
                    @endif
                    <span class="text-slate-500 text-sm">({{ count($results) }})</span>
                @else
                    No recipes found.
                @endif
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($results as $meal)
                    <a href="/recipes/{{ $meal['idMeal'] }}"
                       class="group rounded-xl overflow-hidden border border-slate-800
                              hover:border-blue-500 bg-slate-900 transition">
                        <img src="{{ $meal['strMealThumb'] }}" alt="{{ $meal['strMeal'] }}"
                             class="w-full h-40 object-cover group-hover:scale-105 transition">
                        <div class="p-3">
                            <div class="font-medium text-sm leading-snug">{{ $meal['strMeal'] }}</div>
                            {{-- strArea/strCategory only appear on the search endpoint, not on
                                 filter.php responses — so guard with null coalescing. --}}
                            @if (!empty($meal['strArea']) || !empty($meal['strCategory']))
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ $meal['strArea'] ?? '' }}{{ !empty($meal['strArea']) && !empty($meal['strCategory']) ? ' · ' : '' }}{{ $meal['strCategory'] ?? '' }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <h2 class="text-xl font-semibold mb-4">Browse by category</h2>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $cat)
                <a href="/?category={{ urlencode($cat['strCategory']) }}"
                   class="chip {{ $category === $cat['strCategory'] ? 'bg-blue-600 border-blue-500' : 'bg-slate-800 hover:bg-blue-600 border-slate-700' }}
                          border rounded-full px-4 py-2 text-sm">
                    {{ $cat['strCategory'] }}
                </a>
            @endforeach
        </div>
    </section>
@endsection
