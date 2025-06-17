<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestTestController;
use App\Http\Controllers\Blog\PostController as BlogPostController;
use App\Http\Controllers\Blog\Admin\PostController as AdminPostController;
use App\Http\Controllers\Blog\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\DiggingDeeperController;

// Головна сторінка
Route::get('/', function () {
    return view('welcome');
});

// Авторизовані користувачі
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'digging_deeper'], function () {

    Route::get('collections', [DiggingDeeperController::class, 'collections'])

        ->name('digging_deeper.collections');

});
    
});

// REST-тест
Route::resource('rest', RestTestController::class)
    ->names('restTest');

// Гостьова частина блогу
Route::prefix('blog')->group(function () {
    Route::resource('posts', BlogPostController::class)
        ->names('blog.posts');
});

// Адмінка
Route::prefix('admin/blog')->group(function () {
    // Категорії
    Route::resource('categories', AdminCategoryController::class)
        ->only(['index', 'edit', 'store', 'update', 'create'])
        ->names('blog.admin.categories');

    // Пости
    Route::resource('posts', AdminPostController::class)
        ->except(['show']) // не робити маршрут для show
        ->names('blog.admin.posts');
});
