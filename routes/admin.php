<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;

Route::prefix('admin')->group(function() {
    Route::prefix('/auth')->group(function() {
        Route::post('login', [UserController::class, 'login']);
        Route::post('signup', [UserController::class, 'signup']);
    });

    Route::prefix('/users')->group(function() {
        Route::group([
            'middleware' => ['auth:api','customToken']
        ], function() {
            Route::get('me', [UserController::class, 'me']);
            Route::delete('logout', [UserController::class, 'logout']);
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{id}', [UserController::class, 'getUserInfo']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });
    });

    Route::prefix('/categories')->group(function() {
        Route::group([
            'middleware' => ['auth:api', 'customToken']
        ], function() {
            Route::get('/', [CategoryController::class, 'getAllCategory']);
            Route::get('/parent', [CategoryController::class, 'getParent']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });
    });
});
