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

Route::get('/', 'HomeController@print');
Route::get('/new', 'NewTestController@print');
Route::get('/getTags', 'NewTestController@getTags');
Route::get('/tests', 'TestsController@printPage');
Route::get('/{id}/preface', 'TestController@printPrefacePage');
Route::get('/{id}/question', 'TestController@printQuestionPage');
Route::get('/{id}/result', 'TestController@printResultPage');
Route::post('/getQuestionForTest', 'TestController@getQuestionForTest');
Route::post('/addTest', 'NewTestController@addTest');
Route::post('/addQuestion', 'NewTestController@addQuestion');
Route::post('/changeOrderOfQuestionNumbers', 'NewTestController@changeOrderOfQuestionNumbers');
Route::post('/getQuestionForCreate', 'NewTestController@getQuestion');
Route::post('/updateQuestion', 'NewTestController@updateQuestion');
Route::post('/getTestInfoToView', 'NewTestController@getTestInfoToView');
Route::post('/addAnswer', 'TestController@addAnswer');
Route::post('/setUserName', 'TestController@setUserName');