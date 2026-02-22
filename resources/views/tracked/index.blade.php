@extends('layouts.app')

@section('title', 'Tracked')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-6">Tracked</h1>

    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $labels = [
                'watching' => 'Watching',
                'completed' => 'Completed',
                'plan_to_watch' => 'Plan to Watch',
                'paused' => 'Paused',
                'all' => 'All',
            ];
        @endphp
        @foreach($statusOptions as $status)
            <a href="{{ route('tracked', ['status' => $status]) }}"
               class="px-3 py-2 rounded-lg text-sm {{ $selectedStatus === $status ? 'bg-purple-600 text-white' : 'bg-gray-800 text-gray-200 hover:bg-gray-700' }}">
                {{ $labels[$status] }}
            </a>
        @endforeach
    </div>

    @if($trackings->isEmpty())
        <p class="text-gray-300">No anime found in {{ str_replace('_', ' ', $selectedStatus) }}.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($trackings as $tracking)
                @php $anime = $animeMap[$tracking->anime_id] ?? null; @endphp
                @if($anime)
                <a href="{{ route('anime.show', $tracking->anime_id) }}" class="block bg-gray-800 border border-gray-700 rounded-lg overflow-hidden hover:bg-gray-700">
                    <div class="flex">
                        <img src="{{ $anime['coverImage']['medium'] ?? '' }}" alt="{{ $anime['title']['romaji'] ?? 'Anime cover' }}" class="w-24 h-32 object-cover">
                        <div class="p-3 flex-1">
                            <h3 class="font-semibold">{{ $anime['title']['romaji'] ?? 'Unknown title' }}</h3>
                            <p class="text-sm text-gray-300 mt-1">Status: {{ str_replace('_', ' ', $tracking->status) }}</p>
                            <p class="text-sm text-gray-300">Watched: {{ $tracking->watched_episodes }} / {{ $anime['episodes'] ?? '?' }}</p>
                            <p class="text-sm text-gray-300">Score: {{ $tracking->score ?? '-' }}</p>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
