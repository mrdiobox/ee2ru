<?php

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
use App\Http\Controllers\ParseController;
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/doparse', [ ParseController::class, 'doParse' ]);
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
 
    return "Кэш очищен.";
});

Route::get('/', [App\Http\Controllers\EeController::class, 'index']);
Route::get('/ee/data-chart/{delta?}', [App\Http\Controllers\EeController::class, 'chartData']);
Route::get('/ee/progress', [App\Http\Controllers\EeController::class, 'progressData']);
Route::get('/ee/cars', [App\Http\Controllers\EeController::class, 'getCars']);
Route::view('/about', 'about');