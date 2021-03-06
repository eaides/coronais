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

Route::get('/', 'WellcomeController@index')->name('wellcome');

// Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('ajaxStatistic','StatisticController');
Route::resource('country','CountryController');

Route::post('ajaxChart', 'WellcomeController@ajaxData')->name('ajax.chart');
Route::post('ajaxChartAll', 'WellcomeController@ajaxDataAll')->name('ajax.chartall');

Route::post('executeScrapper', 'StatisticController@executeScrapper')->name('execute.scrapper');
Route::post('removeAll', 'StatisticController@removeAll')->name('ajaxStatistic.empty');


// test only
Route::get('test', 'StatisticController@test');
