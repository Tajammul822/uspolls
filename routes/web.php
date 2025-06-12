<?php

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\PollCandidateController;
use App\Http\Controllers\PollApprovalController;
use App\Http\Controllers\ElectionPollController;
use App\Http\Controllers\ElectionPollResultController;


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

Route::get('/profile', [ProfileController::class, 'profile'])->name('admin-profile');
Route::post('/profile/{id}', [ProfileController::class, 'profileUpdate'])->name('profile-update');
// Password Change
Route::get('change-password', [ProfileController::class, 'changePassword'])->name('change.password.form');
Route::post('change-password', [ProfileController::class, 'changPasswordStore'])->name('change.admin.password');

//Google
Route::get('auth/google', [SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialController::class, 'redirectToGoogleCallback']);
//twitter
Route::get('auth/twitter', [SocialController::class, 'redirectToTwitter'])->name('auth.twitter');
Route::get('auth/twitter/callback', [SocialController::class, 'redirectToTwitterCallback']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('admin/polls/{poll}/details', [PollController::class, 'details'])
     ->name('polls.details');



Route::post('admin/polls/approval', [PollController::class, 'storeApproval'])
     ->name('polls.approval.store');

Route::post('admin/polls/election', [PollController::class, 'storeElection'])
     ->name('polls.election.store');

Route::resource('users', UserController::class);
Route::resource('polls', PollController::class);
Route::resource('states', StateController::class);
Route::resource('candidates', CandidateController::class);
Route::resource('poll_candidates', PollCandidateController::class);
Route::resource('poll_approvals', PollApprovalController::class);
Route::resource('election_polls', ElectionPollController::class);
Route::resource('election_polls_results', ElectionPollResultController::class);

