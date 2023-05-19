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
    // GET TIRES
    Route::get('tires', [CatalogController::class,'tires']);
    Route::get('tires/by-size', [CatalogController::class,'tiresBySize']);
    Route::get('tires/by-brand', [CatalogController::class,'tiresByBrand']);

    // GET WHEELS
    Route::get('wheels', [CatalogController::class,'wheels']);
    Route::get('wheels/by-size', [CatalogController::class,'wheelsBySize']);
    Route::get('wheels/by-brand', [CatalogController::class,'wheelsByBrand']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
