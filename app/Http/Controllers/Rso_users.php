<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rso_user;

class Rso_users extends Controller
{
    public function login_in(Request $request) {
    	$get = json_decode($request->getContent());

    	$username = blank_check($get->username, 'Username');
    	$password = blank_check($get->password, 'Password');

    	$data = Rso_user::where('username', $username)->where('password', $password)->first();

    	if(!empty($data->id)) {

    		if($data->status != 'Approved') {
    			http_response_code(401);
    			return array('status'=>'0', 'data'=>'', 'message'=>'Your acount is inactive, contact with your manager.');
    		}

    		// ===================== Update token in user table ==============================
    		if(empty($data->token)) {
	    		$token 		= bin2hex(openssl_random_pseudo_bytes(16));
	    		$SecretKey 	= date('Y')."t3Qo7xfdH1";
	    		$newToken	= $SecretKey.$token;
	    		$up_data 	= array("token" => $newToken);
	    		Rso_user::where('username', $username)->update($up_data);
	    	}
    		// ===================== Update token in user table ==============================

			$response[]      = array(
				'status'     => true,
				'name'       => $data->name,
				'username'   => $data->username,
				'email'      => $data->email,
				'phone'      => $data->phone,
				'city'       => $data->city,
				'token'      => !empty($data->token) ? $data->token : $newToken
			);

			http_response_code(200);
			return array('status'=>'2', 'data'=>$response, 'message'=>'');
    	} else {

    		http_response_code(401);
    		return array('status'=>'1', 'data'=>'', 'message'=>'Invalid User');
    	}
    }

    function logout(Request $request) {
    	$get = json_decode($request->getContent());

    	$username = blank_check($get->username, 'Username');
    	$token = blank_check($get->token, 'Token');

    	// ======== Authenticate =============
    	authenticate($username, $token);
    	// ======== Authenticate =============

    	$data 	= array("token" => '');
    	$data 	= Rso_user::where('username', $username)->update($data);
    	if($data) {
    		http_response_code(200);
			return array('status'=>'2', 'data'=>'Logout Successfully', 'message'=>'');
    	} else {
    		http_response_code(401);
    		return array('status'=>'1', 'data'=>'', 'message'=>'Logout Failed');
    	}
    }

    function get_rso_list(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $token      = blank_check($get->token, 'Token');
        $start_date = $get->start_date;
        $end_date   = $get->end_date;

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $query = Rso_user::query();
        $query = $query->where('status', 'Approved');
        if (!empty($start_date)) {
            $query = $query->where('date', '>=', $start_date);
        }
        if (!empty($end_date)) {
          $query = $query->where('date', '<=', $end_date);
        }
        $data = $query->get();

        if(!empty($data)) {

            $response = array();
            foreach ($data as $row) {
                $response[]     = array(
                    'username'  => $row->username,
                    'name'      => $row->name,
                    'role'      => $row->role,                    
                    'email'     => $row->email,
                    'zone'      => $row->zone,
                    'address'   => $row->address,
                    'city'      => $row->city,
                    'state'     => $row->state,
                    'mobile'    => $row->mobile,
                    'status'    => $row->status,
                );
            }

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }
}
