<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AnimeTracker') - Track Your Anime</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-gray-950 to-gray-900 text-white">
    <nav class="fixed top-0 inset-x-0 z-50 bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 grid grid-cols-[auto_1fr_auto] items-center gap-6">
                <div class="flex items-center min-w-fit">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-purple-400">AnimeTracker</a>
                </div>

                <div class="hidden md:flex justify-center">
                    <form action="{{ route('anime.search') }}" method="GET" class="flex w-full max-w-xl">
                        @if(request('genre'))
                            <input type="hidden" name="genre" value="{{ request('genre') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('season'))
                            <input type="hidden" name="season" value="{{ request('season') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        <input
                            type="text"
                            name="q"
                            placeholder="Search anime..."
                            class="px-4 py-1 bg-gray-700 rounded-l-lg focus:outline-none focus:ring-1 focus:ring-purple-400"
                            value="{{ request('q') }}"
                        >
                        <button type="submit" class="px-4 py-1 bg-purple-600 rounded-r-lg hover:bg-purple-700">Search</button>
                    </form>
                </div>

                <div class="flex items-center gap-5 justify-end">
                    @auth
                        <a href="{{ route('home') }}" class="text-gray-300 hover:text-white whitespace-nowrap">Home</a>
                        <a href="{{ route('tracked') }}" class="text-gray-300 hover:text-white whitespace-nowrap">Tracked</a>
                        <a href="{{ route('account') }}" class="text-gray-300 hover:text-white">Account</a>
                        <div
                            class="h-8 w-8 rounded-full bg-gray-700 border border-gray-500 flex items-center justify-center text-sm font-semibold text-white"
                            title="{{ auth()->user()->name }}"
                        >
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-white">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('home') }}" class="text-gray-300 hover:text-white whitespace-nowrap">Home</a>
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Log in</a>
                        <a href="{{ route('register') }}" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg">Sign up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-20">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-600 text-white px-4 py-2 rounded-lg">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
