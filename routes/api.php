<?php

use App\Http\Controllers\V1\CatalogController;
use App\Http\Controllers\V1\OrderingController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [UserController::class, 'userLogin']);



Route::middleware('auth:api')->group(function () {

    Route::prefix('catalog')->group(function () {
        // GET CATALOG TIRES AND WHEELS 
        Route::get('tires', [CatalogController::class, 'getTires']);
        Route::get('wheels', [CatalogController::class, 'getWheels']);


        //GET CATALOG BY VEHICLE
        Route::get('vehicles', [CatalogController::class, 'getVehicles']);
        Route::get('vehicle/years', [CatalogController::class, 'getVehicleYear']);
        Route::get('vehicle/makes', [CatalogController::class, 'getVehicleByMakes']);
        Route::get('vehicle/models', [CatalogController::class, 'getVehicleByModels']);
        Route::get('vehicle/options', [CatalogController::class, 'getVehicleConfigurations']);
        Route::get('vehicle/size', [CatalogController::class, 'getVehicleSize']);
        Route::get('vehicle/tires', [CatalogController::class, 'getTiresByVehicle']);
        Route::get('vehicle/wheels', [CatalogController::class, 'getWheelsByVehicle']);


        // GET INVENTORY LOCATION AND PRICE
        Route::get('locations', [CatalogController::class, 'getLocation']);
        Route::get('inventory', [CatalogController::class, 'inventoryPrice']);
        Route::get('order/status', [CatalogController::class, 'getOrderStatus']);


        // POST QUOTE AND ORDERING
        Route::post('quote', [OrderingController::class, 'postQuote']);
    });



});




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
