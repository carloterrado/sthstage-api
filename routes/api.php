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
Route::prefix('v1/catalog')->group(function () {

    // GET TIRES BY BRAND || SKU || SIZE
    Route::get('tires', [CatalogController::class, 'getTires']);
    // GET WHEELS BY BRAND || SKU || SIZE
    Route::get('wheels', [CatalogController::class, 'getWheels']);
    //GET location
    Route::get('locations', [CatalogController::class, 'getLocation']);
    //GET vehicle years
    Route::get('vehicle/years', [CatalogController::class, 'getVehicleYear']);
    
    Route::get('vehicle/makes', [CatalogController::class, 'getVehicleByMakes']);
    Route::get('vehicle/models', [CatalogController::class, 'getVehicleByModels']);
    Route::get('vehicle/option', [CatalogController::class, 'getVehicleOption']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// request
// api/v1/catalog/tires/location

// response