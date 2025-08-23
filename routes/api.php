<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * @OA\Get(
 *     path="/user",
 *     tags={"Authentication"},
 *     summary="Get current user",
 *     description="Get authenticated user information",
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Current user information",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
 *     )
 * )
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public event routes (no auth required)
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// Protected event routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::patch('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
});
