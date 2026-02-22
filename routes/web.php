<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TrackedController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnimeController::class, 'index'])->name('home');

Route::get('/search', [AnimeController::class, 'search'])->name('anime.search');

Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');
Route::post('/anime/{id}/progress', [AnimeController::class, 'updateProgress'])
    ->middleware('auth')
    ->name('anime.progress');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::get('/tracked', [TrackedController::class, 'index'])->name('tracked');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
