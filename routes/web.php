<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\BibleController;
use App\Http\Controllers\FavoriteVerseController;
use App\Http\Controllers\ConnectController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

// Home
Route::view('/home', 'home')->name('home');

// Bible
Route::view('/bible', 'bible.index')->name('bible.index');

// Bible API endpoints (serve JSON from storage/app/bible/{TRANSLATION})
Route::get('/bible/api/{translation}/index', [BibleController::class, 'index'])->name('bible.api.index');
Route::get('/bible/api/{translation}/{book}/{chapter}', [BibleController::class, 'chapter'])->name('bible.api.chapter');

// Favorites (user)
Route::middleware('auth')->group(function () {
    Route::get('/favorites', [FavoriteVerseController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [FavoriteVerseController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{id}', [FavoriteVerseController::class, 'destroy'])->name('favorites.destroy');
});

// Program - public index
Route::get('/program', [ProgramController::class, 'index'])->name('program.index');

// Program - admin
Route::prefix('program')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/manage', [ProgramController::class, 'manage'])->name('program.manage');
    Route::get('/create', [ProgramController::class, 'create'])->name('program.create');
    Route::post('/', [ProgramController::class, 'store'])->name('program.store');
    Route::get('/{id}/edit', [ProgramController::class, 'edit'])->name('program.edit');
    Route::put('/{id}', [ProgramController::class, 'update'])->name('program.update');
    Route::delete('/{id}', [ProgramController::class, 'destroy'])->name('program.destroy');
});

// Connect - public index
Route::get('/connect', [ConnectController::class, 'index'])->name('connect.index');

// Connect - actions (require auth)
Route::middleware('auth')->prefix('connect')->group(function () {
    Route::post('/{receiver}/request', [ConnectController::class, 'sendRequest'])->name('connect.request.send');
    Route::post('/requests/{id}/accept', [ConnectController::class, 'accept'])->name('connect.request.accept');
    Route::post('/requests/{id}/decline', [ConnectController::class, 'decline'])->name('connect.request.decline');
    Route::delete('/requests/{id}', [ConnectController::class, 'cancel'])->name('connect.request.cancel');
});

// News - public index
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

// News - admin
Route::prefix('news')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/', [NewsController::class, 'store'])->name('news.store');
    Route::get('/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
});

// Resources - public index
Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');

// Resources - admin
Route::prefix('resources')->middleware(['auth', EnsureAdmin::class])->group(function () {
    Route::get('/create', [ResourcesController::class, 'create'])->name('resources.create');
    Route::post('/', [ResourcesController::class, 'store'])->name('resources.store');
    Route::get('/{id}/edit', [ResourcesController::class, 'edit'])->name('resources.edit');
    Route::put('/{id}', [ResourcesController::class, 'update'])->name('resources.update');
    Route::delete('/{id}', [ResourcesController::class, 'destroy'])->name('resources.destroy');
});

// FAQ - public index
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// FAQ - admin
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
