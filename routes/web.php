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


// Documentation
Route::get('/', [DocumentationController::class, 'home'])->name('home');


Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('viewlogin');
    Route::post('/login-submit', [UserController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'showSettings']);
    // Route::get('/users', [UserController::class, 'getUsers'])->name('users');
    Route::get('/users', [UserController::class, 'getRole'])->name('users');

    //Search User
    Route::get('/search-user', [UserController::class, 'searchUser'])->name('searchUser');
    // Add Role
    Route::post('/add-role', [UserController::class, 'addRole'])->name('addRole');
    // Delete Role
    Route::post('/delete-role/{id}', [UserController::class, 'deleteRole'])->name('deleteRole');
    //Search Role
    Route::get('/search-role', [UserController::class, 'searchRole'])->name('searchRole');
    // Role Controller
    Route::get('/role-controller/{id}', [UserController::class, 'roleController'])->name('role-controller');
    // View Catalog Modal
    Route::get('/view-catalog/{id}', [UserController::class, 'viewCatalogModal'])->name('viewCatalogModal');

    Route::get('/user-management', [UserController::class, 'userManagementPage'])->name('userManagementPage');
    Route::post('/add-user', [UserController::class, 'addUser'])->name('addUser');
    Route::post('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
    Route::post('/edit-user/{id}', [UserController::class, 'editUser'])->name('editUser');
    Route::get('/users-column-settings/{id}', [UserController::class, 'showUserCatalogSettings'])->name('user.column.settings');
    
    
    Route::post('update-user-column-settings/{id}', [UserController::class, 'updateUserColumnSettings'])->name('update.user.column.settings');
    Route::post('update-user-endpoint-settings/{id}', [UserController::class, 'updateUserEndpointSettings'])->name('update.user.endpoint.settings');
    Route::post('settings/catalog', [SettingsController::class, 'submitCatalog'])->name('submitCatalog');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');


    //View Catalog
    Route::get('/catalog', [ExcelImporterController::class, 'index'])->name('view');
    Route::post('/import', [ExcelImporterController::class, 'import'])->name('import');
    Route::post('/export', [ExcelImporterController::class, 'export'])->name('catalog.export');
    Route::get('/{page}', [ExcelImporterController::class, 'getData'])->name('get.data');
});