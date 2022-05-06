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

Route::get('/', function () {
    return view('welcome');
});

Route::get ('/home'     , [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('shortener/store'   , [App\Http\Controllers\ShortenerController::class, 'storeLink'])->name('write.link');
Route::get ('shortener/read'    , [App\Http\Controllers\ShortenerController::class, 'getMoreAccessedLink'])->name('get.links');
Route::get ('shortener/{uid}'   , [App\Http\Controllers\ShortenerController::class, 'getLinkShortened'])->name('get.uid');

Route::get ('{uid}'             , [App\Http\Controllers\ShortenerController::class, 'gotoShortten']);