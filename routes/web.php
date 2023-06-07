<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SthController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

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








Route::get('/settings', [SettingsController::class, 'showSettings']);
Route::get('/users', [UserController::class, 'getUsers'])->name('users');
Route::get('/users-catalog-settings/{user_id}',[UserController::class, 'showUserCatalogSettings'])->name('user.column.settings');
Route::post('update-user-catalog-column-settings/{user_id}', [UserController::class, 'updateUserCatalogColumnSettings'])->name('update.user.catalog.column.settings');
Route::post('settings/catalog', [SettingsController::class, 'submitCatalog'])->name('submitCatalog');
