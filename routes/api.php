<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\OTPController;
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

// Logging in a user
Route::post('/users/login', [UserController::class, 'authenticate']);

// Route::middleware("auth:sanctum")->group(function () {
// Retrieve user Data
// Route::get("/users/{id}/data", [UserController::class, 'getUserInfo']);


// Retrieve a user by ID
Route::get('/users/{id}', [UserController::class, 'show']);

Route::get('/user/find/{phone_number}', [OTPController::class, 'findUser']);

// Route::post('/user/request/otp', [OTPController::class, 'index']);
Route::post('/user/request/otp', [OTPController::class, 'requestOtp']);

Route::post('/user/verify/otp', [OTPController::class, 'verifyOtp']);

// Update a user by ID
// Route::put('/users/{id}', [UserController::class, 'update']);
Route::put('/users/change/pin/{phone_number}', [UserController::class, 'changePin']);

Route::post('/send-otp', [OTPController::class, 'sendOTP']);

// Delete a user by ID
Route::delete('/users/{id}', [UserController::class, 'destroy']);
// });

// Route::middleware('auth')->group(function () {
    // get all users transactions
    Route::get('/user/{id}/transactions', [TransactionController::class, 'getUserTransactions']);

    // get user balance
    Route::get('/account/{id}/balance', [UserController::class, 'getUserBalance']);
// });





// Route::middleware("auth:sanctum")->group(function () {
// Create a new transaction
Route::post('/transactions/new', [TransactionController::class, 'create']);

// Retrieve all transactions
Route::get('/transactions/all', [TransactionController::class, 'index']);

// Retrieve a transaction by ID
Route::get('/transactions/{id}', [TransactionController::class, 'show']);

// transfer between accounts
Route::post('/transactions/{originAccountId}/transfer/{destinationAccountId}', [TransactionController::class, 'transfer']);

// expenses
Route::post('/transactions/expenses/{account_number}', [TransactionController::class, 'expenses']);

// retrieve expenses for user
Route::get('/transactions/expenses/{account_number}/{transaction_type}', [TransactionController::class, 'getExpenses']);



// Update a transaction by ID
// Route::put('/transactions/{id}', [TransactionController::class, 'update']);

// Delete a transaction by ID
// Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

// create a new cheque
Route::post('/cheques/issue', [ChequeController::class, 'create']);

// Update a cheque
Route::put('/cheques/{id}', [ChequeController::class, 'update']);
// });
