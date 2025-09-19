<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MessageController;

use App\Http\Controllers\DiaryController;

use App\Http\Controllers\ResourceController;


use App\Http\Controllers\UserController;

Route::middleware('auth')->group(function () {
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
});



Route::middleware('auth')->group(function () {
    // Resources
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourceController::class, 'store'])
        ->middleware('can:isTherapist')
        ->name('resources.store');
    Route::delete('/resources/{id}', [ResourceController::class, 'destroy'])
        ->middleware('can:isTherapist')
        ->name('resources.destroy');
});

// Blade view (requires login)
Route::middleware('auth')->group(function () {
    Route::get('/diary', function() {
        return view('diary');
    })->name('diary.index');

    // API routes for AJAX requests
    Route::get('/api/diary', [DiaryController::class, 'index']);
    Route::post('/api/diary', [DiaryController::class, 'store']);
    Route::put('/api/diary/{id}', [DiaryController::class, 'update']);
    Route::delete('/api/diary/{id}', [DiaryController::class, 'destroy']);
});


Route::middleware('auth')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});


Route::middleware('auth')->group(function () {
    // Chat view (with optional receiver)
    Route::get('/chat/{receiverId?}', [MessageController::class, 'chat'])->name('chat');

    // Send a message
    Route::post('/messages/send', [MessageController::class, 'send']);

    // Fetch full conversation with a user
    Route::get('/messages/{receiverId}', [MessageController::class, 'fetch']);

    // 🔥 Fetch recent chats (last message per conversation)
    Route::get('/messages/recent', [MessageController::class, 'recentChats']);
});



/*Web Routes
*/

// 1. Redirect root ('/'):
//    ‑ If visitor is not logged in, show the login form.
//    ‑ If logged in, redirect to dashboard.
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// 2. Dashboard view for authenticated & verified users
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Auth‑protected group: appointments + profile
Route::middleware('auth')->group(function () {
    // Appointments CRUD
    Route::resource('appointments', AppointmentController::class);

    // Profile management (Breeze scaffolding)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\Auth\LoginController;

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


// 4. Include Laravel Breeze auth routes (login, register, password, etc.)
require __DIR__ . '/auth.php';

