<?php

use App\Http\Controllers\V1\CatalogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1/catalog')->group(function(){
    // GET TIRE BY BRANDS
    Route::get('tires/brands', [CatalogController::class,'getTiresByBrand']);
    // GET WHEEL BY BRANDS
    Route::get('wheels/brands', [CatalogController::class,'getWheelsByBrand']);
    // GET WHEEL BY SIZE
    Route::get('wheels',[CatalogController::class, 'getWheels']);
    // GET WHEEL BY SKU
    Route::get('wheels/sku',[CatalogController::class, 'getWheelsBySku']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
