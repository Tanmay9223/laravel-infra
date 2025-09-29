<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\API\V1\User\{
    AuthController, ProfileController
};

Route::prefix('user')->as('user.')->middleware([CorsMiddleware::class])->group(function () {

    Route::get('initialize-data', [AuthController::class, 'initializeData'])->name('initialize_data');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('reset-password', [AuthController::class,'resetPassword'])->name('reset_password');
    
     
    Route::group(['middleware' => ['auth:api-user', 'scopes:user', 'throttle:35,1']], function(){
        Route::post('change-password', action: [ProfileController::class, 'changePassword'])->name('user.change_password');
        Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('verify-otp', [ProfileController::class, 'verifyOTP'])->name('user.kyc.verify_otp');
        Route::post('resend-otp', [ProfileController::class, 'resendOTP'])->name('user.kyc.resend_otp');
        Route::post('logout', [ProfileController::class, 'logout'])->name('logout');

    });

    Route::group(['middleware' => ['auth:api-user', 'scopes:user', 'throttle:35,1','password.expiration']], function(){
            Route::post('update-profile-image', [ProfileController::class, 'updateProfileImage'])->name('update_profile_image');
            Route::post('update-profile-details', [ProfileController::class, 'updateProfileDetails'])->name('update_profile_details');
            
    });
});