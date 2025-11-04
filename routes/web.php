<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Navbar Links - Accessible to everyone
Route::view('/home', 'home')->name('home');
Route::view('/bible', 'bible')->name('bible');
Route::view('/sermons', 'sermons')->name('sermons');
Route::view('/resources', 'resources')->name('resources');
Route::view('/contact', 'contact')->name('contact');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
