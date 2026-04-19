{{-- STEP 15 — recipe detail page.
     Ingredients were pre-flattened in the controller so this template
     stays focused on layout. Video/source links are optional; guard
     with @if so missing ones don't render empty <a> tags. --}}
@extends('layout')

@section('title', $meal['strMeal'] . ' — Recipe Finder')

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-slate-200">
        &larr; Back to search
    </a>

    <article class="mt-4 grid md:grid-cols-2 gap-8">
        <img src="{{ $meal['strMealThumb'] }}" alt="{{ $meal['strMeal'] }}"
             class="rounded-xl w-full object-cover shadow-xl">

        <div>
            <h1 class="text-3xl font-bold">{{ $meal['strMeal'] }}</h1>
            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                @if (!empty($meal['strCategory']))
                    <span class="bg-blue-900/60 border border-blue-700 rounded-full px-3 py-1">
                        {{ $meal['strCategory'] }}
                    </span>
                @endif
                @if (!empty($meal['strArea']))
                    <span class="bg-slate-800 border border-slate-700 rounded-full px-3 py-1">
                        {{ $meal['strArea'] }}
                    </span>
                @endif
            </div>

            <h2 class="mt-6 text-lg font-semibold">Ingredients</h2>
            <ul class="mt-2 divide-y divide-slate-800 border-y border-slate-800">
                @foreach ($ingredients as $row)
                    <li class="flex justify-between py-2 text-sm">
                        <span>{{ $row['name'] }}</span>
                        <span class="text-slate-400">{{ $row['amount'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 flex gap-3 text-sm">
                @if (!empty($meal['strYoutube']))
                    <a href="{{ $meal['strYoutube'] }}" target="_blank"
                       class="bg-red-600 hover:bg-red-500 rounded-lg px-4 py-2">▶ Watch on YouTube</a>
                @endif
                @if (!empty($meal['strSource']))
                    <a href="{{ $meal['strSource'] }}" target="_blank"
                       class="bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg px-4 py-2">Source</a>
                @endif
            </div>
        </div>
    </article>

    <section class="mt-10">
        <h2 class="text-lg font-semibold mb-3">Instructions</h2>
        <div class="prose prose-invert max-w-none whitespace-pre-line leading-relaxed text-slate-300">
            {{ $meal['strInstructions'] }}
        </div>
    </section>
@endsection
