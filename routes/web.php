<?php

use App\Http\Controllers\UrlCheckController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\SocialController;
use Illuminate\Support\Facades\Auth;


Route::view('/', 'welcome')->name('welcome');

Route::get('/cv', function () {
    return view('resume/resume');
});
Route::view('/dividir', 'dividir')->name('dividir');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
Route::view('laravel', 'laravel ')->name('laravel');
Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [SocialController::class, 'handleGoogleCallback']);
Route::prefix('laravel')->group(function () {
    Route::resource('urls', UrlController::class)->only('index', 'store', 'show');
    Route::post('urls/{url}/checks', [UrlCheckController::class, 'store'])
        ->name('urls.checks.store');
    Route::post('urls/check', [UrlController::class, 'check'])->name('urls.check.guest');
});

Route::post('/set-language', function (Request $request) {
    session(['lang' => $request->input('lang')]);
    return redirect()->back();
});
