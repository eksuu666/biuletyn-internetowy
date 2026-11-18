<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\App\Http\Controllers\HomeController;
use Modules\Home\App\Http\Controllers\BulletinController;
use Modules\Home\App\Http\Controllers\BulletinAdminController;

Route::get('/biuletyn', [BulletinController::class, 'index'])->name('bulletins.index');
Route::get('/biuletyn/{issue}/pdf', [BulletinController::class, 'pdf'])->name('bulletins.pdf');
Route::prefix(config('app.panel_prefix', 'panel'))->name(config('app.panel_prefix', 'panel').'.')
    ->group(function () {
        Route::resource('bulletins', BulletinAdminController::class)->parameters(['bulletins' => 'issue'])->except(['show', 'destroy']);
    });

Route::name('home.')->group(function () {
    Route::get('/', HomeController::class)->name('index');
    Route::get('/najnowsze', [HomeController::class, 'latest'])->name('latest');
    Route::get('/najczesciej-czytane', [HomeController::class, 'popular'])->name('popular');
});
