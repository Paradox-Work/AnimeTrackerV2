@extends('layouts.app')

@section('title', 'Account')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-6">Account</h1>

    <div class="space-y-6 mb-10">
        <section class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Profile Information</h2>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-300 mb-3">Profile updated.</p>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="name" class="block text-sm mb-1">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('name')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('email')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg font-semibold">
                    Save Profile
                </button>
            </form>
        </section>

        <section class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Change Password</h2>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-300 mb-3">Password updated.</p>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm mb-1">Current Password</label>
                    <input id="current_password" name="current_password" type="password"
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('current_password', 'updatePassword')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm mb-1">New Password</label>
                    <input id="password" name="password" type="password"
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('password', 'updatePassword')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm mb-1">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('password_confirmation', 'updatePassword')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg font-semibold">
                    Save Password
                </button>
            </form>
        </section>

        <section class="bg-gray-800 border border-gray-700 rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4 text-red-300">Delete Account</h2>
            <p class="text-sm text-gray-300 mb-4">This action is permanent.</p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="delete_password" class="block text-sm mb-1">Confirm Password</label>
                    <input id="delete_password" name="password" type="password" required
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 px-3 py-2">
                    @error('password', 'userDeletion')
                        <p class="text-sm text-red-300 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg font-semibold">
                    Delete Account
                </button>
            </form>
        </section>
    </div>

</div>
@endsection
