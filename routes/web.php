<?php

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ProfileController;


use App\Http\Controllers\UserController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\RaceCandidateController;
use App\Http\Controllers\RaceApprovalController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollsterController;

use App\Http\Controllers\HomeController;


use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


Route::get('dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard')->middleware('auth');


Route::get('auth/login', [LoginController::class, 'loginForm'])->name('login');
Route::post('auth/login', [LoginController::class, 'loginUser'])->name('auth.login');

Route::get('auth/register', [RegisterController::class, 'registerForm'])->name('register');
Route::post('auth/register', [RegisterController::class, 'registerUser'])->name('auth.register');

Route::get('/profile', [ProfileController::class, 'profile'])->name('admin-profile');
Route::post('/profile/{id}', [ProfileController::class, 'profileUpdate'])->name('profile-update');
// Password Change
Route::get('change-password', [ProfileController::class, 'changePassword'])->name('change.password.form');
Route::post('change-password', [ProfileController::class, 'changPasswordStore'])->name('change.admin.password');

// Forgot Password
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Reset Password
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

//Google
Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'redirectToGoogleCallback']);
//twitter
Route::get('auth/twitter', [SocialController::class, 'redirectToTwitter'])->name('auth.twitter');
Route::get('auth/twitter/callback', [SocialController::class, 'redirectToTwitterCallback']);


Route::resource('users', UserController::class);
Route::resource('races', RaceController::class);
Route::resource('states', StateController::class);
Route::resource('candidates', CandidateController::class)->except(['show']);
Route::resource('race_candidates', RaceCandidateController::class);
Route::resource('race_approvals', RaceApprovalController::class);
Route::resource('polls', PollController::class)->except(['show']);
Route::resource('pollsters', PollsterController::class);


// Front_End 
Route::get('/home', function () {
    return view('frontend.home');
})->name('homeboard');
Route::view('/presidential', 'frontend.presidential')->name('presidential');
Route::view('/senate', 'frontend.senate')->name('senate');
Route::view('/house', 'frontend.house')->name('house');
Route::view('/governor', 'frontend.governor')->name('governor');

// Route::get('/polls/search', [HomeController::class, 'searchPolls'])
//      ->name('polls.search');

// Route::get('/polls/{poll}/results', [HomeController::class, 'getResults'])
//      ->name('polls.results');

Route::get('/candidates/search', [HomeController::class, 'searchPolls'])->name('candidates.search');
Route::get('/polls/results/{key}', [HomeController::class, 'getResults']);
