<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Public\RegisterController;
use App\Http\Controllers\Public\LoginController as PublicLoginController;
use App\Http\Controllers\Public\PostController as PublicPostController;
use App\Http\Controllers\Public\UserController as PublicUserController;
use App\Http\Controllers\Public\SearchController as PublicSearchController;
use App\Http\Controllers\Public\CommentController as PublicCommentController;
use App\Http\Controllers\Public\VerifyEmailController as PublicVerifyEmailController;
use App\Http\Controllers\Public\ResetPasswordController as PublicResetPasswordController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DisableCacheMiddleware;
use App\Models\Post;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => DisableCacheMiddleware::class], function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('categories', CategoryController::class);
        Route::resource('tags', TagController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('posts', PostController::class);
        Route::resource('users', UserController::class);
        Route::resource('comments', CommentController::class);
        Route::resource('settings', SettingController::class)
            ->only(['index', 'edit', 'update'])->scoped(['setting' => 'slug']);
    });
});

Route::name('public.')->group(function () {
    Route::get('/', [PublicPostController::class, 'index'])->name('posts.index');
    Route::get('/search', PublicSearchController::class)->name('search');

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::controller(PublicPostController::class)->group(function () {
            Route::get('/create', 'create')->name('create')->can('create', Post::class);
            Route::post('/store', 'store')->name('store')->can('store', Post::class);
            Route::delete('/{post}', 'destroy')->name('destroy')->can('delete', 'post');
            Route::get('/edit/{post:slug}', 'edit')->name('edit')->can('edit', 'post');
            Route::put('/{post}', 'update')->name('update')->can('update', 'post');
            Route::get('/{post:slug}', 'show')->name('show');
        });
    });

    Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => 'verified'], function () {
        Route::controller(PublicUserController::class)->group(function () {
            Route::get('/{user}', 'show')->name('show')->can('view', 'user');
            Route::get('/{user}/edit', 'edit')->name('edit')->can('edit', 'user');
            Route::put('/{user}', 'update')->name('update')->can('update', 'user');
        });
    });

    Route::group(['prefix' => 'comments', 'as' => 'comments.'], function () {
        Route::controller(PublicCommentController::class)->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

    Route::group(['prefix' => 'register', 'as' => 'register.', 'middleware' => 'guest'], function () {
        Route::controller(RegisterController::class)->group(function () {
            Route::get('/', 'showForm')->name('showForm');
            Route::post('/', 'register')->name('register');
        });
    });

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::controller(PublicLoginController::class)->group(function () {
            Route::get('/login', 'showForm')->name('showForm')->middleware('guest');
            Route::post('/login', 'authenticate')->name('authenticate')->middleware('guest');
            Route::post('/logout', 'logout')->name('logout')->middleware('auth');
        });
    });
});

Route::group(['prefix' => 'email', 'as' => 'verification.', 'middleware' => 'auth'], function () {
    Route::controller(PublicVerifyEmailController::class)->group(function () {
        Route::get('/verify', 'showVerificationNotice')->name('notice');
        Route::get('/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verify');
        Route::post('/verification-notification', 'resendVerificationEmail')->middleware('throttle:2,1')->name('send');
    });
});

Route::group(['prefix' => 'password', 'as' => 'password.', 'middleware' => 'guest'], function () {
    Route::controller(PublicResetPasswordController::class)->group(function () {
        Route::get('/forgot-password', 'showEmailForm')->name('request');
        Route::post('/forgot-password', 'emailStore')->name('email');
        Route::get('/reset-password/{token}', 'showResetForm')->name('reset');
        Route::post('/reset-password', 'resetStore')->name('update');
    });
});
