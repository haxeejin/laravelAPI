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
use App\Http\Controllers\FlyersController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/flyers.json', [FlyersController::class, 'getAll'])->middleware('verifyFields','verifyFilters');

Route::get('/api/flyers/{id}.json', [FlyersController::class, 'getOne'])->middleware('verifyFields');
