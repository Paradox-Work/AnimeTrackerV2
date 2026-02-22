<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center p-6 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-violet-300">AnimeTracker</a>
        </div>

        <div class="w-full sm:max-w-md px-6 py-6 bg-slate-900/90 border border-slate-700 shadow-xl rounded-2xl">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
