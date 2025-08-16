<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\RequestItemController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\RoomRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-info', [AuthController::class, 'user']);

    // Items & Requests
    Route::apiResource('items', ItemController::class);
    Route::apiResource('requests', RequestItemController::class);

    // Rooms
    Route::apiResource('rooms', RoomController::class);

    // Room Requests
    Route::get('room-requests',  [RoomRequestController::class, 'index']);
    Route::post('room-requests', [RoomRequestController::class, 'store']);
    Route::patch('room-requests/{roomRequest}', [RoomRequestController::class, 'update']);
    Route::delete('room-requests/{roomRequest}', [RoomRequestController::class, 'destroy']);
});
