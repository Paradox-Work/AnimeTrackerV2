@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-md mx-auto bg-gray-800 rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Login</h1>

        <x-auth-session-status class="mb-4 text-green-300" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email" class="block text-sm mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-purple-400">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <label for="password" class="block text-sm mb-2">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2 focus:outline-none focus:ring-1 focus:ring-purple-400">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-300 hover:text-white">Forgot password?</a>
                @endif
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg font-semibold">Login</button>
            </div>
        </form>

        <div class="mt-6 text-sm text-gray-300">
            <a href="{{ route('register') }}" class="hover:text-white">Register</a>
        </div>
    </div>
</div>
@endsection
