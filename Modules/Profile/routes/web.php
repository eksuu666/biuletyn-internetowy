<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\App\Http\Controllers\ChangeEmailController;
use Modules\Profile\App\Http\Controllers\ChangePasswordController;
use Modules\Profile\App\Http\Controllers\ProfileController;
use Modules\Profile\App\Http\Controllers\SocialNetworkController;

Route::prefix(config('app.panel_prefix', 'panel').'/profile')->name(config('app.panel_prefix', 'panel').'.profile.')->group(function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
    });

    Route::prefix('email/change')->controller(ChangeEmailController::class)->group(function () {
        Route::get('/', 'changeEmailView')->name('email.change');
        Route::patch('/', 'sendChangeEmailVerification')->name('email.update');
        Route::get('/verify', 'verifyChangeEmail')->name('email.change.verify')->middleware('signed');
    });

    Route::prefix('password/change')->controller(ChangePasswordController::class)->group(function () {
        Route::get('/', 'changePasswordView')->name('password.change');
        Route::patch('/', 'changePassword')->name('password.update');
    });

    Route::prefix('social-networks')->controller(SocialNetworkController::class)->group(function () {
        Route::get('/', 'edit')->name('social-networks.edit');
        Route::put('/', 'update')->name('social-networks.update');
    });
});
