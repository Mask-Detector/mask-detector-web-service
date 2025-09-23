<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaskDetectionController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('mask-detection')->group(function () {
    Route::get('/', [MaskDetectionController::class, 'index']); // Get all detections
    Route::post('/', [MaskDetectionController::class, 'store']); // Store new detection
    Route::get('/statistics', [MaskDetectionController::class, 'statistics']); // Get statistics
    Route::get('/{id}', [MaskDetectionController::class, 'show']); // Get specific detection
    Route::delete('/{id}', [MaskDetectionController::class, 'destroy']); // Delete detection
});
