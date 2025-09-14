<?php
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function() {
    Route::view('/', 'dashboard')->name('dashboard');
    Route::resource('appointments', AppointmentController::class);
});
?>
