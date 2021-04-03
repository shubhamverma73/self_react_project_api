<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Mail\WelcomeEmail; //for view template only
use App\Mail\AccountCreated; //for view template only
//php artisan make:mail WelcomeEmail -m email.welcome

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'Rso_users@login_in');
Route::post('logout', 'Rso_users@logout');
Route::post('dashboard', 'Dashboard@index');
Route::post('rso_list', 'Rso_users@get_rso_list');
Route::post('view_rso', 'Rso_users@view_rso');
Route::post('add_rso', 'Rso_users@add_rso');
Route::post('edit_rso', 'Rso_users@edit_rso');
Route::match(['GET', 'POST'], 'zone-list', 'Citylist@getZoneList');
Route::post('state-list', 'Citylist@getStateList');
Route::post('city-list', 'Citylist@getCityList');
Route::post('rso-csv-download', 'Rso_users@downloadCSV');
Route::get('mailable_check', 'Rso_users@mailable_check');


Route::post('tickets', 'Ticket@index');
Route::post('ticket-csv-download', 'Ticket@downloadCSV');
Route::post('view_ticket', 'Ticket@view_ticket');
Route::post('ticket_count', 'Ticket@group_by_ticket_count');
Route::post('edit_ticket', 'Ticket@edit_ticket');


Route::post('stores', 'Stores@index');
Route::post('view_store', 'Stores@view_store');
Route::post('edit_store', 'Stores@edit_store');
Route::post('store-csv-download', 'Stores@downloadCSV');


Route::post('targets', 'Targets@index');
Route::post('view_target', 'Targets@view_target');
Route::post('edit_target', 'Targets@edit_target');
Route::post('target-csv-download', 'Targets@downloadCSV');
Route::post('upload-targets', 'Targets@upload_targets');

Route::get('/email', function() {
	$return_array = ['name'=>'Shubham', 'email'=>'shubham@gmail.com', 'username'=>'shubhamv'];
	//return new WelcomeEmail($return_array);
	return new AccountCreated($return_array);
}); //for view template only