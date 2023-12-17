<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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



Route::get('/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/users', [UserController::class, 'register'])->name('register-user');
Route::get('/', function () {
    return view('signin');
})->name('signin');
Route::post('/users/login', [UserController::class, 'authenticate'])->name('signin-user');