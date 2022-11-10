<?php

use App\Http\Controllers\DriverController;
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

Route::get('/', function () {
    return view('welcome');
});

// --------------------------------- Drivers ---------------------------------------

Route::get('/drivers/register' , [DriverController::class, 'register'])->name('drivers.register');
Route::get('/drivers/login' , [DriverController::class, 'login'])->name('drivers.login');
Route::get('/drivers/getAllDrivers' , [DriverController::class, 'getAllDrivers'])->name('drivers.allDrivers');


