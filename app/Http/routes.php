<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //

    Route::any('/admin/login', 'Admin\LoginController@login');
    Route::get('/admin/code', 'Admin\LoginController@code');
    Route::get('/admin/getcode', 'Admin\LoginController@getCode');


});


Route::group(['middleware' => ['web', 'admin.login'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    //

    Route::get('index', 'IndexController@index');
    Route::get('/', 'IndexController@index');
    Route::get('info', 'IndexController@info');
    Route::any('syssetting', 'IndexController@sysSetting');
    Route::get('quit', 'LoginController@quit');
    Route::any('password', 'IndexController@password');

	//ajax
	Route::any('question/ajax', 'QuestionController@ajax');

	//question
    Route::any('question', 'QuestionController@index');
    Route::any('question/create', 'QuestionController@create');
    Route::any('question/exportAnalysis', 'QuestionController@analysis');
    Route::any('question/{question_id}/edit', 'QuestionController@edit');
    Route::any('question/{question_id}/update', 'QuestionController@update');
    Route::any('question/store', 'QuestionController@store');
    Route::delete('question/{question_id}/delete', 'QuestionController@delete');


    Route::any('upload', 'CommonController@upload');

    //users

    Route::any('users', 'UserController@index');
    Route::get('users/create', 'UserController@create');
    Route::post('users/store', 'UserController@store');
    Route::get('users/{user_id}/edit', 'UserController@edit');
    Route::any('users/{user_id}/delete', 'UserController@delete');
    Route::post('users/{user_id}', 'UserController@update');

	//paper
	Route::get('papers/{user_id}', 'PaperController@index');
	Route::get('paper/{paper_id}', 'PaperController@questionList');
	Route::get('paper/{paper_id}/export', 'PaperController@exportExcel');

});



Route::group(['middleware' => ['web'], 'namespace' => 'Home'], function () {


    Route::any('/login', 'LoginController@login');
    Route::get('/register', 'LoginController@register');
    Route::get('/registerdone', 'LoginController@registerdone');
	Route::get('/changepsw', 'LoginController@changepsw');
	Route::get('/changepswdone', 'LoginController@changepswdone');
    Route::any('/sendsms', 'LoginController@sendSms');
    Route::any('/findpsw', 'LoginController@findpsw');
	//wechat
	Route::any('wechat/getBaseInfo', 'WeChatController@getBaseInfo'); //基本信息获取
	Route::any('wechat/getCode', 'WeChatController@getUserOpenId');
	Route::any('wechat/getUserDetail', 'WeChatController@getUserDetail'); //用户相信信息获取
	Route::any('wechat/getUserInfo', 'WeChatController@getUserInfo');
});

Route::group(['middleware' => ['web', 'home.login'], 'namespace' => 'Home'], function () {


	//
    Route::get('quit', 'LoginController@quit');

    Route::get('index', 'IndexController@index');
    Route::get('/', 'IndexController@index');
    Route::any('usercenter', 'IndexController@userCenter');
    Route::any('startexam/{quest_id?}', 'IndexController@startExam');
    Route::any('handin', 'IndexController@handIn');


    Route::any('recentPapers', 'IndexController@recentPapers');
    Route::get('paper/{paper_id}', 'IndexController@paper');
    Route::get('getQuestion/{quest_id}/{paper_id}', 'IndexController@getQuestion');



});

Route::group(['middleware' => ['web', 'home.login', 'home.examming'], 'namespace' => 'Home'], function () {
    //

    Route::any('questionlist', 'IndexController@questionList');


});