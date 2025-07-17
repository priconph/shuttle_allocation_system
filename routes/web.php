<?php

use Illuminate\Support\Facades\Route;

/**
 * Import Controllers
 */
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoutesController;
use App\Http\Controllers\ShuttleProviderController;
use App\Http\Controllers\PickupTimeController;
use App\Http\Controllers\SystemOneController;
use App\Http\Controllers\MasterlistController;
use App\Http\Controllers\ExportReportController;
use App\Http\Controllers\CutoffTimeController;


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

/**
 * Common Routes
 */
Route::get('/', function () {
    return view('dashboard');
})->name('index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/signin_page', function () {
    return view('signin');
})->name('signin_page');

Route::get('/change_password_page', function () {
    return view('change_password');
})->name('change_password_page');

Route::get('/user_management', function () {
    return view('user_management');
})->name('user_management');

Route::get('/routes_management', function () {
    return view('routes_management');
})->name('routes_management');

Route::get('/shuttle_provider_management', function () {
    return view('shuttle_provider_management');
})->name('shuttle_provider_management');

Route::get('/pickup_time_management', function () {
    return view('pickup_time_management');
})->name('pickup_time_management');

Route::get('/masterlist_management', function () {
    return view('masterlist_management');
})->name('masterlist_management');

Route::get('/export_report', function () {
    return view('export_report');
})->name('export_report');

Route::get('/cutoff_time_management', function () {
    return view('cutoff_time_management');
})->name('cutoff_time_management');



/**
 * USER CONTROLLER
 * Note: always use snake case(underscore separator) naming convention to route & route name and camel case to the method for best practice
 */
Route::get('/view_users', [UserController::class, 'viewUsers'])->name('view_users');
Route::post('/add_user', [UserController::class, 'addUser'])->name('add_user');
Route::get('/get_rapidx_users', [UserController::class, 'getRapidxUsers'])->name('get_rapidx_users');
Route::get('/get_user_roles', [UserController::class, 'getUserRoles'])->name('get_user_roles');
Route::get('/get_user_by_id', [UserController::class, 'getUserById'])->name('get_user_by_id');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/check_session', [UserController::class, 'checkSession'])->name('check_session');
Route::get('/get_user_levels', [UserController::class, 'getUserLevels'])->name('get_user_levels');
Route::get('/get_data_for_dashboard', [UserController::class, 'getDataForDashboard'])->name('get_data_for_dashboard');
Route::get('/get_shuttle_allocation_user', [UserController::class, 'getShuttleAllocationUser'])->name('get_shuttle_allocation_user');

/**
 * ROUTES MANAGEMEMENT CONTROLLER
 */
Route::get('/view_routes', [RoutesController::class, 'viewRoutes'])->name('view_routes');
Route::post('/add_routes', [RoutesController::class, 'addRoutes'])->name('add_routes');
Route::get('/get_routes_by_id', [RoutesController::class, 'getRoutesById'])->name('get_routes_by_id');
Route::post('/edit_routes_status', [RoutesController::class, 'editRoutesStatus'])->name('edit_routes_status');
Route::get('/get_routes', [RoutesController::class, 'getRoutes'])->name('get_routes');

/**
 * SHUTTLE PROVIDER MANAGEMEMENT CONTROLLER
 */
Route::get('/view_shuttle_provider', [ShuttleProviderController::class, 'viewShuttleProvider'])->name('view_shuttle_provider');
Route::post('/add_shuttle_provider', [ShuttleProviderController::class, 'addShuttleProvider'])->name('add_shuttle_provider');
Route::get('/get_shuttle_provider_by_id', [ShuttleProviderController::class, 'getShuttleProviderById'])->name('get_shuttle_provider_by_id');
Route::post('/edit_shuttle_provider_status', [ShuttleProviderController::class, 'editShuttleProviderStatus'])->name('edit_shuttle_provider_status');
Route::get('/get_shuttle_provider', [ShuttleProviderController::class, 'getShuttleProvider'])->name('get_shuttle_provider');


/**
 * PICKUP TIME MANAGEMEMENT CONTROLLER
 */
Route::get('/view_pickup_time', [PickupTimeController::class, 'viewPickupTime'])->name('view_pickup_time');
Route::post('/add_pickup_time', [PickupTimeController::class, 'addPickupTime'])->name('add_pickup_time');
Route::get('/get_pickup_time_by_id', [PickupTimeController::class, 'getPickupTimeById'])->name('get_pickup_time_by_id');
Route::post('/edit_pickup_time_status', [PickupTimeController::class, 'editPickupTimeStatus'])->name('edit_pickup_time_status');
Route::get('/get_pickup_time', [PickupTimeController::class, 'getPickupTime'])->name('get_pickup_time');

/**
 * MASTERLIST MANAGEMEMENT CONTROLLER
 */
Route::get('/get_masterlist', [MasterlistController::class, 'getMasterlist'])->name('get_masterlist');
Route::get('/view_masterlist', [MasterlistController::class, 'viewMasterlist'])->name('view_masterlist');
Route::get('/view_masterlist_test', [MasterlistController::class, 'viewMasterlistTest'])->name('view_masterlist_test');
Route::post('/add_masterlist', [MasterlistController::class, 'addMasterlist'])->name('add_masterlist');
Route::get('/get_masterlist_by_id', [MasterlistController::class, 'getMasterlistById'])->name('get_masterlist_by_id');
Route::post('/edit_masterlist_status', [MasterlistController::class, 'editMasterlistStatus'])->name('edit_masterlist_status');
Route::post('/delete_masterlist', [MasterlistController::class, 'deleteMasterlist'])->name('delete_masterlist');
Route::get('/get_masterlist', [MasterlistController::class, 'getMasterlist'])->name('get_masterlist');

/**
 * CUTOFF TIME MANAGEMEMENT CONTROLLER
 */
Route::get('/view_cutoff_time', [CutoffTimeController::class, 'viewCutoffTime'])->name('view_cutoff_time');
Route::post('/add_cutoff_time', [CutoffTimeController::class, 'addCutoffTime'])->name('add_cutoff_time');
Route::get('/get_cutoff_time_by_id', [CutoffTimeController::class, 'getCutoffTimeById'])->name('get_cutoff_time_by_id');
Route::post('/edit_cutoff_time_status', [CutoffTimeController::class, 'editCutoffTimeStatus'])->name('edit_cutoff_time_status');
Route::get('/get_cutoff_time', [CutoffTimeController::class, 'getCutoffTime'])->name('get_masterlist');

/**
 * SYSTEMONE CONTROLLER
 */
Route::get('/get_employees', [SystemOneController::class, 'getEmployees'])->name('get_employees');

/**
 * REPORT CONTROLLER
 */
Route::get('/export_report',[ExportReportController::class, 'export_report'])->name('export_report');
Route::get('/export_report_test',[ExportReportController::class, 'export_report_test'])->name('export_report_test');