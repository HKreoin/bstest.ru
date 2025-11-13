<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestTrainingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
Route::get('/tests/{test:slug}', [TestController::class, 'show'])->name('tests.show');
Route::post('/tests/{test:slug}/start', [TestController::class, 'start'])->name('tests.start');

Route::prefix('/tests/{test:slug}/trainer')->name('tests.training.')->group(function () {
    Route::get('/', [TestTrainingController::class, 'configure'])->name('configure');
    Route::post('/start', [TestTrainingController::class, 'start'])->name('start');
    Route::get('/session/{session}', [TestTrainingController::class, 'attempt'])->name('attempt');
    Route::post('/session/{session}', [TestTrainingController::class, 'submit'])->name('submit');
});

Route::get('/attempts/{testAttempt}', [TestAttemptController::class, 'show'])->name('attempts.show');
Route::get('/attempts/{testAttempt}/protocol', [TestAttemptController::class, 'downloadProtocol'])->name('attempts.protocol');
Route::post('/attempts/{testAttempt}', [TestAttemptController::class, 'submit'])->name('attempts.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
