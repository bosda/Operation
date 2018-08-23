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
use Illuminate\Support\Facades\Auth;

Route::get('/',function(){
	if(Auth::check()){
		$user = Auth::user();
		//session(['user'=>$user]);
		return view('index',['user'=>$user]);
	}else{
		return redirect()->action('UserController@index');
	}
});

Route::resource('user','UserController');

Route::get('logout','UserController@logout');
Route::get('list','UserController@list');
Route::get('getlist','UserController@getlist');
Route::post('add','UserController@add');

Route::resource('project','ProjectController');

Route::get('message','MessageController@index');
Route::get('message/{id}','MessageController@list');
Route::post('message/{id}','MessageController@update');
Route::get('getLog/{log}',function($log){
	return response()->download(storage_path().'/upload/logs/'.$log);
});

Route::get('sdk',function(){
	return view('sdk');
});

Route::get('api',function(){
	return view('api');
});

Route::get('getDocument',function(){
	return response()->download(storage_path().'/document.zip');
});
