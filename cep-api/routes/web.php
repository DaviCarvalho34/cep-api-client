<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/v1/addresses/search/{cep}', [AddressController::class, 'searchByCep'])->name('addresses.search');
Route::post('api/v1/addresses/search/', [AddressController::class, 'store'])->name('addresses.store');
