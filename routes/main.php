<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

// Auth::routes();
Auth::routes(['register'=>false]);

Route::group(['namespace' => 'ems'], function()
{
    Route::get('view/job/application/link','IntervieweeController@createLink')->name('createJobLink');
    Route::post('generate/link/','IntervieweeController@generateLink')->name('sendJobLink');
    Route::get('generate/link','IntervieweeController@generateLink')->name('generateLinkView');
    Route::get('generate/link/{email}','IntervieweeController@generateLink')->name('generateLink');
    Route::get('job/application','IntervieweeController@view')->name('jobApplicationForm');
    Route::post('insert/job/application','IntervieweeController@insert')->name('insertJobApplication');
    Route::get('interviewee/response/saved',function(){
        return view('error.responseSaved');
    })->name('errorResponse');
});