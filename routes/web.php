<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

// Route::get('/', function () {
//     return view('login');
// })->name('login');


Route::get('/', [WebController::class, 'index'])->name('login');

Route::post('/login', [WebController::class, 'Login'])->name('login-user');

// Route::middleware("auth:sanctum")->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard')->middleware('auth');
// });

// Route::get('/dashboard', function () {
//     return view('admin.dashboard');
// })->name('dashboard');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [WebController::class, 'Register'])->name('register-user');


