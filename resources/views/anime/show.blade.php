@extends('layouts.app')

@section('title', $anime['title']['romaji'])

@section('content')
<div class="relative">
    @if(isset($anime['bannerImage']))
    <div class="h-80 bg-cover bg-center" style="background-image: url('{{ $anime['bannerImage'] }}')">
        <div class="h-full bg-black bg-opacity-50"></div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 {{ isset($anime['bannerImage']) ? '-mt-40' : '' }}">
        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-xl">
            <div class="md:flex">
                <div class="md:w-64 flex-shrink-0">
                    @if(isset($anime['coverImage']['large']))
                    <img src="{{ $anime['coverImage']['large'] }}" alt="{{ $anime['title']['romaji'] }}" class="w-full h-auto">
                    @endif
                </div>

                <div class="p-8 flex-1">
                    <h1 class="text-3xl font-bold mb-2">{{ $anime['title']['romaji'] }}</h1>
                    @if(isset($anime['title']['english']) && $anime['title']['english'] != $anime['title']['romaji'])
                    <h2 class="text-xl text-gray-400 mb-4">{{ $anime['title']['english'] }}</h2>
                    @endif

                    <div class="flex flex-wrap gap-4 mb-6">
                        @if(isset($anime['averageScore']))
                        <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">Score {{ $anime['averageScore'] / 10 }}</span>
                        @endif
                        <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $anime['episodes'] ?? '?' }} Episodes</span>
                        <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $anime['status'] ?? 'Unknown' }}</span>
                    </div>

                    @if(isset($anime['genres']))
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($anime['genres'] as $genre)
                        <span class="bg-purple-600 px-3 py-1 rounded-full text-xs">{{ $genre }}</span>
                        @endforeach
                    </div>
                    @endif

                    <div class="prose prose-invert max-w-none mb-8">
                        {!! $anime['description'] ?? 'No description available.' !!}
                    </div>

                    @if($canTrack)
                    <div class="bg-gray-700 rounded-lg p-6">
                        <h3 class="text-xl font-semibold mb-4">Your Progress</h3>

                        <form action="{{ route('anime.progress', $anime['id']) }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Episodes Watched</label>
                                    <div class="flex items-center">
                                        <input
                                            type="number"
                                            name="watched_episodes"
                                            value="{{ $tracking['watched_episodes'] }}"
                                            min="0"
                                            max="{{ $anime['episodes'] ?? 999 }}"
                                            class="w-24 bg-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-purple-400"
                                        >
                                        <span class="ml-2">/ {{ $anime['episodes'] ?? '?' }}</span>
                                    </div>

                                    @if(isset($anime['episodes']) && $anime['episodes'] > 0)
                                    <div class="mt-2 h-2 bg-gray-600 rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-purple-600"
                                            style="width: {{ min(100, ($tracking['watched_episodes'] / $anime['episodes']) * 100) }}%"
                                        ></div>
                                    </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Status</label>
                                    <select name="status" class="w-full bg-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-purple-400">
                                        <option value="watching" {{ $tracking['status'] == 'watching' ? 'selected' : '' }}>Watching</option>
                                        <option value="completed" {{ $tracking['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="plan_to_watch" {{ $tracking['status'] == 'plan_to_watch' ? 'selected' : '' }}>Plan to Watch</option>
                                        <option value="paused" {{ $tracking['status'] == 'paused' ? 'selected' : '' }}>Paused</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Your Score (0-10)</label>
                                    <input
                                        type="number"
                                        name="score"
                                        value="{{ $tracking['score'] }}"
                                        min="0"
                                        max="10"
                                        step="0.5"
                                        class="w-full bg-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-purple-400"
                                    >
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-6 py-2 rounded-lg font-semibold">
                                    Update Progress
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-gray-700 rounded-lg p-6">
                        <h3 class="text-xl font-semibold mb-2">Track This Anime</h3>
                        <p class="text-gray-300 mb-4">Log in to save watched episodes, status, and score to your account.</p>
                        <div class="flex gap-3">
                            <a href="{{ route('login') }}" class="bg-white text-purple-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">Log in</a>
                            <a href="{{ route('register') }}" class="bg-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-700">Create account</a>
                        </div>
                    </div>
                    @endif

                    @if(isset($anime['nextAiringEpisode']))
                    <div class="mt-4 bg-blue-600 bg-opacity-20 border border-blue-600 rounded-lg p-4">
                        <p class="text-blue-400">
                            Episode {{ $anime['nextAiringEpisode']['episode'] }} airs
                            {{ \Carbon\Carbon::createFromTimestamp($anime['nextAiringEpisode']['airingAt'])->diffForHumans() }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
