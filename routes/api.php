<?php

use App\Http\Controllers\V1\CatalogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// url/api/v1/catalog/tires/brands
Route::prefix('v1/catalog')->group(function () {
    // GET WHEEL BRANDS
    Route::get('wheels/brands', [CatalogController::class, 'getWheelsByBrand']);
    // GET CATALOGS BASE ON PARAMETER
    Route::get('', [CatalogController::class, 'getCatalog']);
    // GET TIRES BY BRAND || SKU || SIZE
    Route::get('tires', [CatalogController::class, 'getTires']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});