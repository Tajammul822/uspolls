<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\SocialController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');
Route::get('dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard')->middleware('auth');


Route::get('auth/login', [LoginController::class, 'loginForm'])->name('login');
Route::post('auth/login', [LoginController::class, 'loginUser'])->name('auth.login'); 

Route::get('auth/register', [RegisterController::class, 'registerForm'])->name('register');
Route::post('auth/register', [RegisterController::class, 'registerUser'])->name('auth.register');

//Google
Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'redirectToGoogleCallback']);
//twitter
Route::get('auth/twitter', [SocialController::class, 'redirectToTwitter'])->name('auth.twitter');
Route::get('auth/twitter/callback', [SocialController::class, 'redirectToTwitterCallback']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

