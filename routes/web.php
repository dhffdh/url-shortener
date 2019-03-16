<?php

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/r/{code}', 'RedirectController@redirect');


Route::prefix('/urls')->group(function (){
    Route::get('/', 'UrlsController@all');
    Route::get('/{id}', 'UrlsController@show');
    Route::post('/','UrlsController@store');
    Route::put('/{id}','UrlsController@update');
    Route::delete('/{id}', 'UrlsController@delete');
});