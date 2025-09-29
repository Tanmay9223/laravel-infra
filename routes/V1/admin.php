<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\API\V1\
{
    Google2FAController
};
use App\Http\Controllers\API\V1\Admin\{
    AuthController, ProfileController
};

Route::prefix('admin')->as('admin.')->middleware([CorsMiddleware::class])->group(function () {
    Route::get('initialize-data', [AuthController::class, 'initializeData'])->name('initialize_data');

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('reset-password', [AuthController::class,'resetPassword'])->name('reset_password');
    
    Route::group(['middleware' => ['auth:api-admin', 'scopes:admin', 'throttle:20,1']], function(){
        Route::post('change-password', [ProfileController::class, 'changePassword'])->name('admin.change_password');
        Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::post('logout', [ProfileController::class, 'logout'])->name('logout');
    });
    
});