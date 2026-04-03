<?php

use App\Http\Controllers\LegalController;
use App\Http\Controllers\TestAttemptController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestTrainingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('tests.index');
})->name('landing');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing');
})->name('logout');

Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/cookies', [LegalController::class, 'cookies'])->name('legal.cookies');

Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
Route::get('/tests/{test:slug}', [TestController::class, 'show'])->name('tests.show');
Route::post('/tests/{test:slug}/start', [TestController::class, 'start'])->name('tests.start');

Route::prefix('/tests/{test:slug}/trainer')->name('tests.training.')->group(function () {
    Route::get('/', [TestTrainingController::class, 'configure'])->name('configure');
    Route::post('/start', [TestTrainingController::class, 'start'])->name('start');
    Route::get('/session/{session}', [TestTrainingController::class, 'attempt'])->name('attempt');
    Route::post('/session/{session}', [TestTrainingController::class, 'submit'])->name('submit');
    Route::post('/session/{session}/next', [TestTrainingController::class, 'next'])->name('next');
});

Route::get('/attempts/{testAttempt}', [TestAttemptController::class, 'show'])->name('attempts.show');
Route::get('/attempts/{testAttempt}/protocol', [TestAttemptController::class, 'downloadProtocol'])->name('attempts.protocol');
Route::post('/attempts/{testAttempt}', [TestAttemptController::class, 'submit'])->name('attempts.submit');
