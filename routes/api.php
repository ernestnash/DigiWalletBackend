<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Create a new user
Route::post('/users', [UserController::class, 'register']);

// Logging in a new user
Route::post('/users/login', [UserController::class, 'authenticate']);

Route::middleware("auth:sanctum")->group(function () {
    // Retrieve user Data
    Route::get("/users/{id}/data", [UserController::class, 'getUserInfo']);

    // Retrieve a user by ID
    Route::get('/users/{id}', [UserController::class, 'show']);

    // Update a user by ID
    Route::put('/users/{id}', [UserController::class, 'update']);

    // Delete a user by ID
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


// Route::middleware("auth:sanctum")->group(function () {
    // Create a new transaction
    Route::post('/transactions', [TransactionController::class, 'create']);

    // Retrieve all transactions
    // Route::get('/transactions', [TransactionController::class, 'index']);

    // Retrieve a transaction by ID
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);

    // Update a transaction by ID
    // Route::put('/transactions/{id}', [TransactionController::class, 'update']);

    // Delete a transaction by ID
    // Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
// });
