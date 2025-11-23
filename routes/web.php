<?php

use \App\Http\Controllers;
use \App\Http\Controllers\NewsController;
use \App\Http\Controllers\ResourcesController;
use App\Http\Controllers\ProfileController;
use \App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

// Home
Route::view('/home', 'home')->name('home');

// Bible
Route::view('/bible', 'bible')->name('bible');

// Program
Route::view('/program', 'program')->name('program');

// News public index
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

// News admin
Route::prefix('news')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/', [NewsController::class, 'store'])->name('news.store');
    Route::get('/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
});

// Resources public index
Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');

// Resources admin
Route::prefix('resources')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/create', [ResourcesController::class, 'create'])->name('resources.create');
    Route::post('/', [ResourcesController::class, 'store'])->name('resources.store');
    Route::get('/{id}/edit', [ResourcesController::class, 'edit'])->name('resources.edit');
    Route::put('/{id}', [ResourcesController::class, 'update'])->name('resources.update');
    Route::delete('/{id}', [ResourcesController::class, 'destroy'])->name('resources.destroy');
});

// FAQ public index
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// FAQ admin
Route::prefix('faq')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/categories', [FaqController::class, 'categories'])->name('faq.categories.index');
    Route::get('/faqs', [FaqController::class, 'faqs'])->name('faq.faqs.index');
    // categories
    Route::get('/categories/create', [FaqController::class, 'createCategory'])->name('faq.categories.create');
    Route::post('/categories', [FaqController::class, 'storeCategory'])->name('faq.categories.store');
    Route::get('/categories/{id}/edit', [FaqController::class, 'editCategory'])->name('faq.categories.edit');
    Route::put('/categories/{id}', [FaqController::class, 'updateCategory'])->name('faq.categories.update');
    Route::delete('/categories/{id}', [FaqController::class, 'destroyCategory'])->name('faq.categories.destroy');
    // faqs
    Route::get('/faqs/create', [FaqController::class, 'createFaq'])->name('faq.faqs.create');
    Route::post('/faqs', [FaqController::class, 'storeFaq'])->name('faq.faqs.store');
    Route::get('/faqs/{id}/edit', [FaqController::class, 'editFaq'])->name('faq.faqs.edit');
    Route::put('/faqs/{id}', [FaqController::class, 'updateFaq'])->name('faq.faqs.update');
    Route::delete('/faqs/{id}', [FaqController::class, 'destroyFaq'])->name('faq.faqs.destroy');
});

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
