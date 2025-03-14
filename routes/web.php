<?php

use App\Http\Controllers\UrlCheckController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::view('/', 'welcome')->name('welcome');

Route::get('/cv', function () {
    return view('resume/resume');
});

Route::view('laravel', 'laravel ')->name('laravel');

Route::prefix('laravel')->group(function () {
    Route::resource('urls', UrlController::class)->only('index', 'store', 'show');
    Route::post('urls/{url}/checks', [UrlCheckController::class, 'store'])
        ->name('urls.checks.store');});

Route::post('/set-language', function (Request $request) {
    session(['lang' => $request->input('lang')]);
    return redirect()->back();
});
