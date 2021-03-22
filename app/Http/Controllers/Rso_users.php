<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rso_user;
use Mail;
use App\Mail\WelcomeEmail;
use App\Mail\AccountCreated;
use DB;

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
        //$query = $query->where('status', 'Approved');
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
                    'id'        => $row->id,
                    'ids'       => $row->id,
                    'username'  => $row->username,
                    'name'      => $row->name,
                    'role'      => $row->role,                    
                    'email'     => $row->email,
                    'mobile'    => $row->mobile,
                    'zone'      => $row->zone,
                    'state'     => $row->state,
                    'city'      => $row->city,
                    'address'   => $row->address,
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

    function view_rso(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $id         = blank_check($get->id, 'ID');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        //DB::enableQueryLog();
        $row = Rso_user::where('id', $id)->first();
        //$query = DB::getQueryLog();

        if(!empty($row->username)) {

            $response = array();
            $response[]     = array(
                'id'        => $row->id,
                'username'  => $row->username,
                'name'      => $row->name,
                'role'      => $row->role,                    
                'email'     => $row->email,
                'mobile'    => $row->mobile,
                'zone'      => $row->zone,
                'state'     => $row->state,
                'city'      => $row->city,
                'address'   => $row->address,
                'status'    => $row->status,
            );

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function add_rso(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $name       = blank_check($get->name, 'Name');
        $role       = blank_check($get->role, 'Role');
        $email      = blank_check($get->email, 'Email');
        $password   = blank_check($get->password, 'Password');
        $mobile     = blank_check($get->mobile, 'Mobile');
        $zone       = blank_check($get->zone, 'Zone');
        $state      = blank_check($get->state, 'State');
        $city       = blank_check($get->city, 'City');
        $address    = blank_check($get->address, 'Address');
        $pincode    = blank_check($get->pincode, 'Pincode');

        // ===== Check user Present ==========
        isUserExists($username, $email);
        // ===== Check user Present ==========

        if(!empty($username)) {

            $user           = new Rso_user;
            $user->username = $get->username;
            $user->rsm_name = '';
            $user->rsm_code = '';
            $user->rsm_email= '';
            $user->asm_name = 'Sanjay Mittal';
            $user->asm_code = 'OHS147';
            $user->asm_email= 'sanjay.mittal@omron.com';
            $user->rso_code = mt_rand(1000000, 9999999);
            $user->name     = $get->name;
            $user->email    = $get->email;
            $user->role     = $get->role;
            $user->type     = 'FSO';
            $user->mobile   = $get->mobile;
            $user->password = $get->password;
            $user->city     = $get->city;
            $user->state    = $get->state;
            $user->zone     = $get->zone;
            $user->address  = $get->address;
            $user->pincode  = $get->pincode;
            $user->date     = date('Y-m-d');
            $user->status   = 'Approved';
            $user->device_token= '';
            $user->token   = '';
            $user->create_datetime=date('Y-m-d H:i:s');
            $user->save();

            $user_id        = $user->id;
            if(!empty($user_id)) {

                // ==================== Send confirmation mail to registered user ====================
                $return_array = ['name'=>$get->name, 'email'=>$get->email, 'username'=>$get->username];
                Mail::to($get->email)->send(new AccountCreated($return_array));
                // ==================== Send confirmation mail to registered user ====================

                http_response_code(200);
                return array('status'=>'2', 'data'=>'', 'message'=>'User added successfully. User-id: '.$user_id);
            } else {
                http_response_code(401);
                return array('status'=>'1', 'data'=>'', 'message'=>'User not added, try again.');
            }
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function edit_rso(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $id         = blank_check($get->id, 'ID');
        $name       = blank_check($get->name, 'Name');
        $role       = blank_check($get->role, 'Role');
        $email      = blank_check($get->email, 'Email');
        $mobile     = blank_check($get->mobile, 'Mobile');
        $zone       = blank_check($get->zone, 'Zone');
        $state      = blank_check($get->state, 'State');
        $city       = blank_check($get->city, 'City');
        $address    = blank_check($get->address, 'Address');
        $status     = blank_check($get->status, 'status');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = Rso_user::where('id', $id)->first();

        if(!empty($row->username)) {

            $response     = array(
                'name'    => $name,
                'role'    => $role, 
                'mobile'  => $mobile,                   
                'zone'    => $zone,
                'state'   => $state,
                'city'    => $city,
                'address' => $address,
                'status'  => $status,
            );

            $updateData = Rso_user::where('id', $id)->update($response);

            if(!empty($updateData)) {
                http_response_code(200);
                return array('status'=>'2', 'data'=>'', 'message'=>'User updated successfully.');
            } else {
                http_response_code(401);
                return array('status'=>'1', 'data'=>'', 'message'=>'User not updated, try again.');
            }
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function downloadCSV(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $csvData = Rso_user::select('username', 'asm_name', 'asm_code', 'asm_email', 'rso_code', 'name', 'email', 'mobile', 'role', 'type', 'zone', 'state', 'city', 'address', 'pincode', 'status', 'date')
                           ->get();

        if(!empty($csvData)) {
            http_response_code(200);
            return array('status'=>'2', 'data'=>$csvData, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function mailable_check() {
        $return_array = ['name'=>'Shubham', 'email'=>'shubham@gmail.com'];
        Mail::to('shubham@gmail.com')->send(new WelcomeEmail($return_array)); //->cc($moreUsers)  ->bcc($evenMoreUsers)
        //->queue(new OrderShipped($return_array));
    }
}
