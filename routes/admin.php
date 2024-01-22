<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::prefix('/users')->group(function() {
    Route::group([
        'middleware' => ['auth:api','customToken']
    ], function() {
        Route::get('me', [UserController::class, 'me']);
        Route::delete('logout', [UserController::class, 'logout']);
        Route::get('/', [UserController::class, 'getAllUser']);
        Route::get('/{id}', [UserController::class, 'getInfoUser']);
        Route::post('/', [UserController::class, 'createUser']);
        Route::put('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
    });
});