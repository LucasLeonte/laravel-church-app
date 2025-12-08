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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Models\ContactMessage;

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

// Program
Route::get('/program', [ProgramController::class, 'index'])->name('program.index');

// Connect
Route::get('/connect', [ConnectController::class, 'index'])->name('connect.index');

// Connect - actions (users only)
Route::middleware('auth')->prefix('connect')->group(function () {
    Route::post('/{receiver}/request', [ConnectController::class, 'sendRequest'])->name('connect.request.send');
    Route::post('/requests/{id}/accept', [ConnectController::class, 'accept'])->name('connect.request.accept');
    Route::post('/requests/{id}/decline', [ConnectController::class, 'decline'])->name('connect.request.decline');
    Route::delete('/requests/{id}', [ConnectController::class, 'cancel'])->name('connect.request.cancel');
    Route::post('/{other}/remove', [ConnectController::class, 'removeFriend'])->name('connect.friend.remove');
});

// News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

// Resources
Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');

// FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Admin
Route::prefix('admin')->middleware(['auth', EnsureAdmin::class])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.dashboard', compact('messages'));
    })->name('admin.dashboard');

    // Program
    Route::prefix('program')->group(function () {
        Route::get('/manage', [ProgramController::class, 'manage'])->name('program.manage');
        Route::get('/create', [ProgramController::class, 'create'])->name('program.create');
        Route::post('/', [ProgramController::class, 'store'])->name('program.store');
        Route::get('/{id}/edit', [ProgramController::class, 'edit'])->name('program.edit');
        Route::put('/{id}', [ProgramController::class, 'update'])->name('program.update');
        Route::delete('/{id}', [ProgramController::class, 'destroy'])->name('program.destroy');
    });

    // News
    Route::prefix('news')->group(function () {
        Route::get('/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/', [NewsController::class, 'store'])->name('news.store');
        Route::get('/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/{id}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    });

    // Resources
    Route::prefix('resources')->group(function () {
        Route::get('/create', [ResourcesController::class, 'create'])->name('resources.create');
        Route::post('/', [ResourcesController::class, 'store'])->name('resources.store');
        Route::get('/{id}/edit', [ResourcesController::class, 'edit'])->name('resources.edit');
        Route::put('/{id}', [ResourcesController::class, 'update'])->name('resources.update');
        Route::delete('/{id}', [ResourcesController::class, 'destroy'])->name('resources.destroy');
    });

    // FAQ
    Route::prefix('faq')->group(function () {
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

    // Contact messages
    Route::prefix('contact-messages')-> group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('admin.contact.index');
        Route::get('/{message}', [ContactMessageController::class, 'show'])->name('admin.contact.show');
        Route::post('/{message}/reply', [ContactMessageController::class, 'reply'])->name('admin.contact.reply');
        Route::delete('/{message}', [ContactMessageController::class, 'destroy'])->name('admin.contact.destroy');
    });

    // User management
    Route::prefix('users')-> group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('admin.users.toggleAdmin');
    });
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
