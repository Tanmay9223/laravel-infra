<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    ListController
};

Route::fallback(function (Request $request) {
    return response()->json([
        'status' => false,
        'message' => __('custom_messages.unknown_route'),
    ], 404);
});

Route::post('login')->name('login');

Route::prefix('v1')->as('v1:')->group(base_path('routes/V1/admin.php'));
Route::prefix('v1')->as('v1:')->group(base_path('routes/V1/user.php'));
Route::prefix('v1')->as('v1:')->group(function () {

    Route::get('country-list', [ListController::class, 'countryList'])->name('country_list');
    Route::get('state-list/{countryID}', [ListController::class, 'stateList'])->name('state_list');
    Route::get('city-list/{countryID}/{stateID}', [ListController::class, 'cityList'])->name('city_list');

});
