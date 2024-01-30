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
use App\Http\Controllers\TelegramController;
/*
Route::get('/', function () {
    return view('welcome');
})->name('home');
*/

Route::any('/telegramsecret', [ TelegramController::class, 'getDataFromTg' ]);
Route::any('/thello/{id}', [ TelegramController::class, 'tHello' ]);
Route::any('/tauth', [ TelegramController::class, 'tAuth' ]);
Route::any('/addnumber', [ TelegramController::class, 'addNumber' ]);
Route::any('/removenumber', [ TelegramController::class, 'removeNumber' ]);
Route::any('/logout', [ TelegramController::class, 'logout' ]);
Route::get('/iflogin', [ TelegramController::class, 'ifLogin' ]);
Route::get('/doparse', [ ParseController::class, 'doParse' ]);
Route::get('/docheck', [ ParseController::class, 'doCheck' ]);
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
 
    return "Кэш очищен.";
});

Route::view('/about', 'about');
Route::get('/ee/data-chart/{border_id?}/{delta?}', [App\Http\Controllers\EeController::class, 'chartData']);
Route::get('/ee/progress/{border_id?}', [App\Http\Controllers\EeController::class, 'progressData']);
Route::get('/ee/cars/{border_id?}', [App\Http\Controllers\EeController::class, 'getCars']);
Route::get('/{border?}', [App\Http\Controllers\EeController::class, 'index']);