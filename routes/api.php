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
// url/api/v1/catalog/tires/brands
Route::prefix('v1/catalog')->group(function(){
    // GET TIRE BRANDS
    Route::get('tires/brands', [CatalogController::class,'getTiresByBrand']);
    // GET WHEEL BRANDS
    Route::get('wheels/brands', [CatalogController::class,'getWheelsByBrand']);
    // GET CATALOGS BASE ON PARAMETER
    Route::get('',[CatalogController::class, 'getCatalog']);


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
