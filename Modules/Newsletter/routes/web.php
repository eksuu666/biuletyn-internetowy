<?php

use Illuminate\Support\Facades\Route;
use Modules\Newsletter\App\Http\Controllers\CampaignController;
use Modules\Newsletter\App\Http\Controllers\Front\NewsletterController as FrontNewsletterController;
use Modules\Newsletter\App\Http\Controllers\NewsletterController;

Route::prefix(config('app.panel_prefix').'/newsletter-campaigns')->name(config('app.panel_prefix').'.campaigns.')->middleware(['web', 'auth', 'verified', 'can:newsletter::send'])->group(function () {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::post('/', [CampaignController::class, 'store'])->name('store');
    Route::post('/{campaign}/send', [CampaignController::class, 'send'])->name('send');
});

Route::prefix(config('app.panel_prefix', 'panel').'/newsletters')
    ->name(config('app.panel_prefix', 'panel').'.newsletters.')
    ->controller(NewsletterController::class)
    ->middleware(['web', 'auth', 'verified'])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/{newsletter}', 'destroy')->name('destroy');
    });

Route::post('newsletters/subscribe', [FrontNewsletterController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletters.subscribe');
Route::get('newsletters/confirm/{newsletter}/{token}', [FrontNewsletterController::class, 'confirm'])->middleware('signed')->name('newsletters.confirm');
Route::get('newsletters/unsubscribe/{newsletter}', [FrontNewsletterController::class, 'unsubscribeForm'])->middleware('signed')->name('newsletters.unsubscribe');
Route::post('newsletters/unsubscribe/{newsletter}', [FrontNewsletterController::class, 'unsubscribe'])->middleware('signed');
