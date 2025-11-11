<?php

use App\Http\Controllers\Api\V1\CollectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1')
    ->group(function (): void {
        Route::apiResource('collections', CollectionController::class);
    });
