<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DashboardModel;
use DB;

class Dashboard extends Controller
{
    public function index(Request $request) {
    	$get = json_decode($request->getContent());

    	$username = blank_check($get->username, 'Username');
    	$token = blank_check($get->token, 'Token');

    	// ======== Authenticate =============
    	authenticate($username, $token);
    	// ======== Authenticate =============

    	$retailers = DashboardModel::where('status', 'Approved')->get();
    	$retailersCount = $retailers->count();

    	$rsoCount = DB::table('rso_user')->where('status', 'Approved')->count();
    	$adsCount = DB::table('ad_list')->where('status', 'Approved')->count();
    	$distributorCount = DB::table('distributor_list_rso')->where('status', 'Approved')->count();

    	if($retailersCount > 0) {

    		$return_data = array(
    								'retailers' => $retailersCount,
    								'rso' => $rsoCount,
    								'ads' => $adsCount,
    								'distributors' => $distributorCount,

    							);

    		http_response_code(200);
			return array('status'=>'2', 'data'=>$return_data, 'message'=>'');
    	} else {
    		http_response_code(401);
    		return array('status'=>'1', 'data'=>'', 'message'=>'Logout Failed');
    	}
    }
}
