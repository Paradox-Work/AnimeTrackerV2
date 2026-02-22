@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <p class="text-gray-300 mb-6">You are logged in.</p>
    <a href="{{ route('home') }}" class="inline-block bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg">
        Go to Home
    </a>
</div>
@endsection
