@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="xl:grid xl:grid-cols-[1fr_260px] xl:gap-8">
        <section>
            <h1 class="text-3xl font-bold mb-6">
                Popular Anime
                @if(!empty($filters['genre']))
                    <span class="text-base text-gray-300">- {{ $filters['genre'] }}</span>
                @endif
            </h1>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($popularAnime as $anime)
                <a href="{{ route('anime.show', $anime['id']) }}" class="group h-full">
                    <article class="h-full flex flex-col overflow-hidden rounded-xl bg-gray-800 border border-gray-700">
                        @if(isset($anime['coverImage']['large']) || isset($anime['coverImage']['medium']))
                        <img
                            src="{{ $anime['coverImage']['large'] ?? $anime['coverImage']['medium'] }}"
                            alt="{{ $anime['title']['romaji'] }}"
                            class="w-full h-72 object-cover"
                            loading="lazy"
                        >
                        @else
                        <div class="w-full h-72 bg-gray-700 flex items-center justify-center text-sm">No Image</div>
                        @endif

                        <div class="p-3 flex flex-col flex-1">
                            <h3 class="font-semibold text-sm leading-tight min-h-[2.5rem] group-hover:text-purple-300">
                                {{ $anime['title']['romaji'] }}
                            </h3>
                            <div class="flex items-center justify-between text-xs text-gray-300 mt-auto pt-2">
                                <span>{{ $anime['episodes'] ?? '?' }} eps</span>
                                @if(isset($anime['averageScore']))
                                <span class="text-yellow-300">{{ $anime['averageScore'] / 10 }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                </a>
                @endforeach
            </div>

            @php
                $currentPage = $pageInfo['currentPage'] ?? 1;
                $lastPage = $pageInfo['lastPage'] ?? 1;
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
            @endphp
            @if($lastPage > 1)
            <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                @php
                    $pageParams = array_filter([
                        'genre' => $filters['genre'],
                        'status' => $filters['status'],
                        'season' => $filters['season'],
                        'sort' => $filters['sort'],
                        'type' => $filters['type'],
                    ]);
                @endphp
                <a href="{{ route('home', array_merge($pageParams, ['page' => max(1, $currentPage - 1)])) }}"
                   class="px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 {{ $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' }}">
                    Prev
                </a>
                @for($p = $startPage; $p <= $endPage; $p++)
                <a href="{{ route('home', array_merge($pageParams, ['page' => $p])) }}"
                   class="px-3 py-2 rounded-lg {{ $p === $currentPage ? 'bg-purple-600 text-white' : 'bg-gray-800 hover:bg-gray-700' }}">
                    {{ $p }}
                </a>
                @endfor
                <a href="{{ route('home', array_merge($pageParams, ['page' => min($lastPage, $currentPage + 1)])) }}"
                   class="px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 {{ ($pageInfo['hasNextPage'] ?? false) ? '' : 'pointer-events-none opacity-40' }}">
                    Next
                </a>
            </div>
            @endif
        </section>

        <aside class="mt-8 xl:mt-0 xl:sticky xl:top-24 self-start">
            <div class="mb-4">
                <a href="{{ route('home') }}" class="inline-block px-3 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-sm">
                    Reset filters
                </a>
            </div>
            <h2 class="text-sm uppercase tracking-wide text-gray-400 mb-3">Status</h2>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <a href="{{ route('home', array_merge(request()->except(['status', 'page']), ['status' => 'airing'])) }}"
                    class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['status'] ?? null) === 'airing' ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                        Airing
                    </a>
                <a href="{{ route('home', request()->except(['status', 'page'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ empty($filters['status']) ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    Any
                </a>
            </div>

            <h2 class="text-sm uppercase tracking-wide text-gray-400 mb-3">Sort</h2>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <a href="{{ route('home', array_merge(request()->except(['sort', 'page']), ['sort' => 'popular'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['sort'] ?? 'popular') === 'popular' ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    Most popular
                </a>
                <a href="{{ route('home', array_merge(request()->except(['sort', 'page']), ['sort' => 'recent'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['sort'] ?? null) === 'recent' ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    Most recent
                </a>
            </div>

            <h2 class="text-sm uppercase tracking-wide text-gray-400 mb-3">Type</h2>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <a href="{{ route('home', array_merge(request()->except(['type', 'page']), ['type' => 'shows'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['type'] ?? null) === 'shows' ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    Shows
                </a>
                <a href="{{ route('home', array_merge(request()->except(['type', 'page']), ['type' => 'movies'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['type'] ?? null) === 'movies' ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    Movies
                </a>
            </div>

            <h2 class="text-sm uppercase tracking-wide text-gray-400 mb-3">Genre</h2>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('home', request()->except(['genre', 'page'])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ empty($filters['genre']) ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    All
                </a>
                @foreach($genres as $genre)
                <a href="{{ route('home', array_merge(request()->except(['genre', 'page']), ['genre' => $genre])) }}"
                   class="px-3 py-2 rounded-full text-sm text-center {{ ($filters['genre'] ?? null) === $genre ? 'bg-white text-gray-900' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                    {{ $genre }}
                </a>
                @endforeach
            </div>
        </aside>
    </div>
</div>
@endsection
