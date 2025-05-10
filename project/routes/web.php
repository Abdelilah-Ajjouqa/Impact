<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SavedPostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.form');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');



/*
|--------------------------------------------------------------------------
| Protected Routes - Users
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'userStatus'])->prefix('users')->name('users.')->group(function () {
    // Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::post('/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    // User posts for album selection
    Route::get('/{id}/posts', [UserController::class, 'getPosts'])->name('posts');
    // User albums for post selection
    Route::get('/{id}/albums', [UserController::class, 'getAlbums'])->name('albums');
});



/*
|--------------------------------------------------------------------------
| Protected Routes - Posts & Comments
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'userStatus'])->prefix('posts')->group(function () {
    Route::get('/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('', [PostController::class, 'store'])->name('posts.store');
    // Comment routes
    Route::post('/{post_id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/{post_id}/comments/{comment_id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/{post_id}/comments/{comment_id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::middleware('postStatus')->group(function () {
        Route::get('/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('/{id}/update', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    });
});



/*
|--------------------------------------------------------------------------
| Protected Routes - Albums
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'userStatus'])->prefix('albums')->name('albums.')->group(function () {
    Route::get('', [AlbumController::class, 'index'])->name('index');
    Route::get('/create', [AlbumController::class, 'create'])->name('create');
    Route::post('', [AlbumController::class, 'store'])->name('store');
    Route::get('/{id}', [AlbumController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AlbumController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AlbumController::class, 'update'])->name('update');
    Route::delete('/{id}', [AlbumController::class, 'destroy'])->name('destroy');
    // Album post management
    Route::post('/{id}/posts', [AlbumController::class, 'addPost'])->name('posts.add');
    Route::delete('/{id}/posts/{post_id}', [AlbumController::class, 'removePost'])->name('posts.remove');
});



/*
|--------------------------------------------------------------------------
| Protected Routes - Save/Unsave Posts
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'userStatus'])->group(function () {
    Route::post('/save/{post_id}', [SavedPostController::class, 'save'])->name('save');
    Route::post('/unsave/{post_id}', [SavedPostController::class, 'unsave'])->name('unsave');
});



/*
|--------------------------------------------------------------------------
| Protected Routes - Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'userStatus', 'is_admin:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/active', [AdminController::class, 'getAllActiveUsers'])->name('users.active');
    Route::get('/users/inactive', [AdminController::class, 'getAllInactiveUsers'])->name('users.inactive');
    Route::put('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::put('/users/{id}/deactivate', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    // Post management
    Route::get('/posts/archived', [AdminController::class, 'getAllArchivePosts'])->name('posts.archived');
    Route::post('/posts/{id}/archive', [AdminController::class, 'archivePost'])->name('posts.archive');
    Route::post('/posts/{id}/restore', [AdminController::class, 'restorePost'])->name('posts.restore');
    Route::delete('/posts/{id}/force', [AdminController::class, 'deletePost'])->name('posts.delete');
    // Comment management
    Route::delete('/comment/{id}/force', [AdminController::class, 'deleteComment'])->name('comments.force-delete');
});



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PostController::class, 'index'])->name('posts.index');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/posts/{post_id}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');