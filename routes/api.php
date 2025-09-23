<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaskDetectionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test CORS endpoint
Route::get('/test-cors', function () {
    return response()->json([
        'message' => 'CORS is working!',
        'timestamp' => now(),
        'headers' => request()->headers->all()
    ]);
});

Route::post('/test-cors', function () {
    return response()->json([
        'message' => 'POST request successful!',
        'data' => request()->all(),
        'timestamp' => now()
    ]);
});

// Public API routes dengan CORS middleware
Route::middleware(['cors'])->group(function () {
    Route::prefix('mask-detection')->group(function () {
        Route::get('/', [MaskDetectionController::class, 'index']); // Get all detections
        Route::post('/', [MaskDetectionController::class, 'store']); // Store new detection
        Route::get('/statistics', [MaskDetectionController::class, 'statistics']); // Get statistics
        Route::get('/{id}', [MaskDetectionController::class, 'show']); // Get specific detection
        Route::delete('/{id}', [MaskDetectionController::class, 'destroy']); // Delete detection
    });
});

// Protected routes (jika diperlukan authentication)
Route::middleware(['auth:sanctum', 'cors'])->group(function () {
    // Route::delete('/mask-detection/{id}', [MaskDetectionController::class, 'destroy']);
});
