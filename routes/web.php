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

// Route for displaying the login form
Route::get('/', [WebController::class, 'index'])->name('login');

// Route for handling the login form submission
Route::post('/login', [WebController::class, 'Login'])->name('login-user');

// Route for displaying the registration form
Route::get('/register', function () {
    return view('register');
})->name('register');

// Route for handling the registration form submission
Route::post('/register', [WebController::class, 'Register'])->name('register-user');

Route::middleware('auth:web')->group(function () {
    // Protected routes

// Route for the dashboard - only accessible for authenticated users
Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');

Route::post('/logout', [WebController::class, 'signOut'])->name('logout');

});



