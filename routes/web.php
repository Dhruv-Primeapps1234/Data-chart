<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\PieController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
route::get('/',[ChartController::class,'index']);
Route::get('/chart', [ChartController::class, 'getChartdata']);
Route::get('/pie', [PieController::class, 'pieChartdata']);

