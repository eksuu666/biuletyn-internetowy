<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/up', static function () {
    try {
        DB::select('select 1');

        return response()->json([
            'status' => 'ok',
            'service' => config('app.name'),
        ]);
    } catch (Throwable $exception) {
        report($exception);

        return response()->json([
            'status' => 'unavailable',
            'service' => config('app.name'),
        ], 503);
    }
})->name('health');
