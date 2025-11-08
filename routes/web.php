<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test:slug}', [TestController::class, 'show'])->name('tests.show');
    Route::post('/tests/{test:slug}/start', [TestController::class, 'start'])->name('tests.start');

    Route::get('/attempts/{testAttempt}', [TestAttemptController::class, 'show'])->name('attempts.show');
    Route::post('/attempts/{testAttempt}', [TestAttemptController::class, 'submit'])->name('attempts.submit');
});

require __DIR__.'/auth.php';
