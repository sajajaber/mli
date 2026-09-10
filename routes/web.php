<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShowController;

Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'ar'])) {
        abort(404);
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');
