<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Mail\WelcomeEmail; //for view template only
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
Route::post('edit_rso', 'Rso_users@edit_rso');


Route::get('mailable_check', 'Rso_users@mailable_check');
Route::get('/email', function() {
	$return_array = ['name'=>'Shubham', 'email'=>'shubham@gmail.com'];
	return new WelcomeEmail($return_array);
}); //for view template only