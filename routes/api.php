<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JwtAuthController;
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\User\RegisterUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Swagger\ConfigerSwaggerController;

if (app()->environment('local')) {
    Route::get('documentation', [ConfigerSwaggerController::class, 'api']);
}

Route::name('auth.')->controller(RegisterUserController::class)->group(
    function () {
        Route::post('/register', 'store')->name('register');
    }
);

Route::prefix('/auth')->name('auth.')->controller(AuthController::class)->group(
    function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/get-token', 'getToken')->name('get-token');
    }
);

// Route::middleware('auth:api')->group(function () {
//     Route::resource('articles', ArticleController::class)->middleware([
//         'index' => ['can:article.view', 'check.access'],
//         'store' => ['can:article.create'],
//         'show' => ['can:article.view'],
//         'update' => ['can:article.edit'],
//         'destroy' => ['can:article.delete'],
//     ]);
// });
Route::middleware('auth:api')->prefix('/')->group(
    function () {
        Route::prefix('/article')->name('articles.')->controller(ArticleController::class)->group(
            function () {
                Route::get('/', 'index')->name('index')->middleware(['can:article.view', 'check.access']);
                Route::post('/', 'create')->name('create')->middleware(['can:article.create']);
                Route::get('/{article}', 'show')->name('show')->middleware(['can:article.view']);
                Route::put('/{article}', 'update')->name('update')->middleware(['can:article.edit']);
                Route::delete('/{article}', 'delete')->name('delete')->middleware(['can:article.delete']);
            }
        );
    }
);

Route::prefix('/jwt-auth')->name('jwt-auth.')->controller(JwtAuthController::class)->group(
    function () {
        Route::post('/get-jwt-token', 'getJwtToken')->name('get-token');
    }
);

Route::middleware('auth:jwt-api')->prefix('/')->group(
    function () {
        Route::prefix('/jwt-article')->name('jwt-articles.')->controller(ArticleController::class)->group(
            function () {
                Route::get('/', 'index')->name('index')->middleware(['can:article.view']);
            }
        );
    }
);
