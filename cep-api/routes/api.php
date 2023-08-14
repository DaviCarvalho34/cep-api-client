<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AddressController;

Route::group(['prefix' => 'v1'], function() {
    Route::apiResource('addresses', AddressController::class);
});
