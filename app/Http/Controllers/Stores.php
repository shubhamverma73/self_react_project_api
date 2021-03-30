<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoresModel;
use DB;
use Validator;

class Stores extends Controller
{    

    function index(Request $request) {
        $get = json_decode($request->getContent(), true);

        $rules = [
	        'username' 	=> 'required',
	        'token' 	=> 'required',
	    ];
	    $validator = Validator::make($get, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }

        $username   = $get['username'];
        $token      = $get['token'];
        $start_date = $get['start_date'];
        $end_date   = $get['end_date'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        //DB::enableQueryLog();
        $query = StoresModel::query();
        if (!empty($start_date)) {
            $query = $query->where('create_date', '>=', date('Y-m-d', strtotime($start_date)));
        }
        if (!empty($end_date)) {
          $query = $query->where('create_date', '<=', date('Y-m-d', strtotime($end_date)));
        }
        $data = $query->get();
        //$query = DB::getQueryLog();
        //dd($query);

        if(!empty($data)) {

            $response = array();
            foreach ($data as $row) {
                $response[]     	= array(
                    'id'        	=> $row->id,
                    'retailer_code' => $row->retailer_code,
                    'retailer_name' => $row->retailer_name,
                    'email'    		=> $row->email,
                    'mobile'      	=> $row->mobile,                    
                    'rso_code'     	=> $row->rso_code,
                    'rso_name'		=> $row->rso_name,
                    'asm_code'   	=> $row->asm_code,
                    'asm_name'     	=> $row->asm_name,
                    'zone'   		=> $row->zone,
                    'state'   		=> $row->state,
                    'city'   		=> $row->city,
                    'address'   	=> $row->address,
                    'pincode'   	=> $row->pincode,
                    'lat'   		=> $row->lat,
                    'long'   		=> $row->long,
                    'image'   		=> $row->store_image,
                    'status'   		=> $row->status,
                );
            }

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function view_store(Request $request) {
        $get = json_decode($request->getContent(), true);

        $rules = [
	        'username' 	=> 'required',
	        'id' 		=> 'required',
	        'token' 	=> 'required',
	    ];
	    $validator = Validator::make($get, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }

        $username   = $get['username'];
        $id         = $get['id'];
        $token      = $get['token'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = StoresModel::where('id', $id)->first();

        if(!empty($row->retailer_code)) {

            $response 			= array();
            $response[]     	= array(
                'id'        	=> $row->id,
                'retailer_code' => $row->retailer_code,
                'retailer_name' => $row->retailer_name,
                'email'    		=> $row->email,
                'mobile'      	=> $row->mobile,                    
                'rso_code'     	=> $row->rso_code,
                'rso_name'		=> $row->rso_name,
                'asm_code'   	=> $row->asm_code,
                'asm_name'     	=> $row->asm_name,
                'zone'   		=> $row->zone,
                'state'   		=> $row->state,
                'city'   		=> $row->city,
                'address'   	=> $row->address,
                'pincode'   	=> $row->pincode,
                'lat'   		=> $row->lat,
                'long'   		=> $row->long,
                'image'   		=> $row->store_image,
                'status'   		=> $row->status,
            );

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function edit_store(Request $request) {

    	$get = json_decode($request->input('formData'), true);

    	/* ================================== Input Data validation ============================ */
    	$rules = [
	        'email' 	=> 'required',
	        'username' 	=> 'required',
	    ];
	    $validator = Validator::make($get, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }
	    /* ================================== Input Data validation ============================ */

	    /* ================================== Image validation ============================ */
	    $fileArray = array('image' => $request->file('image'));
	    $rules = array(
	      'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
	    );
	    $validator = Validator::make($fileArray, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }
	    /* ================================== Image validation ============================ */

        $username   	= $get['username'];
        $id         	= $get['id'];
        $retailer_code  = $get['retailer_code'];
        $retailer_name  = $get['retailer_name'];
        $email      	= $get['email'];
        $mobile     	= $get['mobile'];
        $zone       	= $get['zone'];
        $state      	= $get['state'];
        $city       	= $get['city'];
        $address    	= $get['address'];
        $status     	= $get['status'];
        $pincode        = $get['pincode'];
        $lat         	= $get['lat'];
        $long         	= $get['long'];
        $token      	= $get['token'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = StoresModel::where('id', $id)->first();

        if(!empty($row->retailer_code)) {

        	/* ============ File Upload ============ */
        	$fileName = '';
	        if($request->file()) {		    
		        $fileName = $retailer_code.'_'.time().'.'.$request->image->extension();		     
		        $request->image->move(public_path('uploads'), $fileName);
	        }

            $response     		= array(
                'retailer_code' => $retailer_code,
                'retailer_name' => $retailer_name,
                'email'    		=> $email,
                'mobile'      	=> $mobile,
                'zone'   		=> $zone,
                'state'   		=> $state,
                'city'   		=> $city,
                'address'   	=> $address,
                'pincode'   	=> $pincode,
                'lat'   		=> $lat,
                'long'   		=> $long,
                'status'   		=> $row->status
            );

            if(!empty($fileName)) {
            	$response['store_image'] = url('/public/uploads/'.$fileName);
            }

            //DB::enableQueryLog();
            $updateData = StoresModel::where('id', $id)->update($response);
            //$query = DB::getQueryLog();
        	//dd($query);

            if(!empty($updateData)) {
                http_response_code(200);
                return array('status'=>'2', 'data'=>'', 'message'=>'Store updated successfully.');
            } else {
                http_response_code(401);
                return array('status'=>'1', 'data'=>'', 'message'=>'Store not updated, try again.');
            }
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function downloadCSV(Request $request) {
        $get = json_decode($request->getContent(), true);

        $rules = [
	        'username' 	=> 'required',
	        'token' 	=> 'required',
	    ];
	    $validator = Validator::make($get, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }

        $username   = $get['username'];
        $token      = $get['token'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $csvData = StoresModel::select('retailer_code', 'retailer_name', 'email', 'mobile', 'rso_code', 'rso_name', 'asm_code', 'asm_name', 'zone', 'state', 'city', 'address', 'pincode', 'lat', 'long', 'store_image', 'status')
                    ->get();

        if(!empty($csvData)) {
            http_response_code(200);
            return array('status'=>'2', 'data'=>$csvData, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }
}
