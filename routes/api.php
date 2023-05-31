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
    // GET TIRE BRANDS
    Route::get('tires/brands', [CatalogController::class,'tireBrand']);
    // GET WHEEL BRANDS
    Route::get('wheels/brands', [CatalogController::class,'wheelBrand']);
    // GET CATALOGS BASE ON PARAMETER
    Route::get('',[CatalogController::class, 'getCatalog']);

    // GET INVENTORY/Price by location
    Route::get('inventory', [CatalogController::class, 'tireInventoryPrice']);


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
