<?php

use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\ExcelChecker\ExcelImporterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Node\Block\Document;

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






Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('viewlogin');
    Route::post('/login-submit', [UserController::class, 'login'])->name('login');


    // Documentation
    Route::get('document-introduction', [DocumentationController::class, 'home'])->name('home');

    // Vehicle API
    Route::get('vehicle-getyears', [DocumentationController::class, 'getyears'])->name('getyears');
    Route::get('vehicle-getmakes', [DocumentationController::class, 'getmakes'])->name('getmakes');
    Route::get('vehicle-getmodels', [DocumentationController::class, 'getmodels'])->name('getmodels');
    Route::get('vehicle-getoptions', [DocumentationController::class, 'getoptions'])->name('getoptions');
    Route::get('vehicle-getsize', [DocumentationController::class, 'getsize'])->name('getsize');

    // Wheel API
    Route::get('wheel-getbrand', [DocumentationController::class, 'wheelgetbrand'])->name('wheelgetbrand');
    Route::get('wheel-getmspn', [DocumentationController::class, 'wheelgetmspn'])->name('wheelgetmspn');
    Route::get('wheel-getsize', [DocumentationController::class, 'wheelgetsize'])->name('wheelgetsize');
    Route::get('wheels-by-vehicle', [DocumentationController::class, 'getwheelsbyvehicle'])->name('getwheelsbyvehicle');

    // Tire API
    Route::get('tire-getbrand', [DocumentationController::class, 'tiregetbrand'])->name('tiregetbrand');
    Route::get('tire-getmspn', [DocumentationController::class, 'tiregetmspn'])->name('tiregetmspn');
    Route::get('tire-getsize', [DocumentationController::class, 'tiregetsize'])->name('tiregetsize');
    Route::get('tires-by-vehicle', [DocumentationController::class, 'gettiresbyvehicle'])->name('gettiresbyvehicle');

    // Inventory API
    Route::get('inventory-getlocation', [DocumentationController::class, 'getlocation'])->name('getlocation');
    Route::get('inventory-price-by-location', [DocumentationController::class, 'getinventorybylocation'])->name('getinventorybylocation');
});

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'showSettings']);
    Route::get('/users', [UserController::class, 'getUsers'])->name('users');

    Route::get('/user-management', [UserController::class, 'userManagementPage'])->name('userManagementPage');
    Route::post('/add-user', [UserController::class, 'addUser'])->name('addUser');
    Route::post('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
    Route::get('/users-column-settings/{id}', [UserController::class, 'showUserCatalogSettings'])->name('user.column.settings');
    Route::post('update-user-column-settings/{id}', [UserController::class, 'updateUserColumnSettings'])->name('update.user.column.settings');
    Route::post('settings/catalog', [SettingsController::class, 'submitCatalog'])->name('submitCatalog');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    //View Catalog
    Route::get('/catalog', [ExcelImporterController::class, 'index'])->name('catalog');
    Route::post('/import', [ExcelImporterController::class, 'import'])->name('import');

    // Documentation
    Route::get('document-introduction', [DocumentationController::class, 'home'])->name('home');

    // Vehicle API
    Route::get('vehicle-getyears', [DocumentationController::class, 'getyears'])->name('getyears');
    Route::get('vehicle-getmakes', [DocumentationController::class, 'getmakes'])->name('getmakes');
    Route::get('vehicle-getmodels', [DocumentationController::class, 'getmodels'])->name('getmodels');
    Route::get('vehicle-getoptions', [DocumentationController::class, 'getoptions'])->name('getoptions');
    Route::get('vehicle-getsize', [DocumentationController::class, 'getsize'])->name('getsize');

    // Wheel API
    Route::get('wheel-getbrand', [DocumentationController::class, 'wheelgetbrand'])->name('wheelgetbrand');
    Route::get('wheel-getmspn', [DocumentationController::class, 'wheelgetmspn'])->name('wheelgetmspn');
    Route::get('wheel-getsize', [DocumentationController::class, 'wheelgetsize'])->name('wheelgetsize');
    Route::get('wheels-by-vehicle', [DocumentationController::class, 'getwheelsbyvehicle'])->name('getwheelsbyvehicle');

    // Tire API
    Route::get('tire-getbrand', [DocumentationController::class, 'tiregetbrand'])->name('tiregetbrand');
    Route::get('tire-getmspn', [DocumentationController::class, 'tiregetmspn'])->name('tiregetmspn');
    Route::get('tire-getsize', [DocumentationController::class, 'tiregetsize'])->name('tiregetsize');
    Route::get('tires-by-vehicle', [DocumentationController::class, 'gettiresbyvehicle'])->name('gettiresbyvehicle');

    // Inventory API
    Route::get('inventory-getlocation', [DocumentationController::class, 'getlocation'])->name('getlocation');
    Route::get('inventory-price-by-location', [DocumentationController::class, 'getinventorybylocation'])->name('getinventorybylocation');
});