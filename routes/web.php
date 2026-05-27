<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Welcome";
});

Route::get('/reports/mobile-calls', [ReportController::class, 'mobileCalls'])
    ->name('reports.mobile-calls');