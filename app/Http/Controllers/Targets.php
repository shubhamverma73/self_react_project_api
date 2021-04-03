<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetsModel;
use DB;
use Validator;

class Targets extends Controller
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

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        //DB::enableQueryLog();
        $data = TargetsModel::all();
        //$query = DB::getQueryLog();
        //dd($query);

        if(!empty($data)) {

            $response = array();
            foreach ($data as $row) {
                $response[]     	= array(
                    'id'        	=> $row->id,                   
                    'rso_code'     	=> $row->rso_code,
                    'rso_name'		=> $row->rso_name,
                    'asm_code'   	=> $row->asm_code,
                    'asm_name'     	=> $row->asm_name,
                    'zone'   		=> $row->zone,
                    'state'   		=> $row->state,
                    'city'   		=> $row->city,
                    'year'   	    => $row->year,
                    'month'   	    => $row->month,
                    'qt'   		    => $row->qt,
                    'value'   		=> $row->value,
                );
            }

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function view_target(Request $request) {
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

        $row = TargetsModel::where('id', $id)->first();

        if(!empty($row->qt)) {

            $response 			= array();
            $response[]     	= array(
                'id'            => $row->id,                   
                'rso_code'      => $row->rso_code,
                'rso_name'      => $row->rso_name,
                'asm_code'      => $row->asm_code,
                'asm_name'      => $row->asm_name,
                'zone'          => $row->zone,
                'state'         => $row->state,
                'city'          => $row->city,
                'year'          => $row->year,
                'month'         => $row->month,
                'qt'            => $row->qt,
                'value'         => $row->value,
            );

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function edit_target(Request $request) {

    	$get = json_decode($request->getContent(), true);

    	/* ================================== Input Data validation ============================ */
    	$rules = [
            'username'   => 'required',
	        'id' 	     => 'required',
            'rso_code'   => 'required',
            'year'       => 'required',
            'month'      => 'required',
            'qt'         => 'required',
            'value'      => 'required',
	        'token' 	 => 'required',
	    ];
	    $validator = Validator::make($get, $rules);
	    if (!$validator->passes()) {
	        http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
	    }
	    /* ================================== Input Data validation ============================ */

        $id             = $get['id'];
        $username       = $get['username'];
        $rso_code       = $get['rso_code'];
        $year    	    = $get['year'];
        $month     	    = $get['month'];
        $qt             = $get['qt'];
        $value         	= $get['value'];
        $token      	= $get['token'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = TargetsModel::where('id', $id)->first();

        if(!empty($row->qt)) {

            $response     		= array(
                'rso_code'      => $rso_code,
                'year'   	    => $year,
                'month'   	    => $month,
                'qt'   		    => $qt,
                'value'   		=> $value,
            );

            $updateData = TargetsModel::where('id', $id)->update($response);

            if(!empty($updateData)) {
                http_response_code(200);
                return array('status'=>'2', 'data'=>'', 'message'=>'Target updated successfully.');
            } else {
                http_response_code(401);
                return array('status'=>'1', 'data'=>'', 'message'=>'Target not updated, try again.');
            }
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function upload_targets(Request $request) {

        $get = json_decode($request->input('formData'), true);

        /* ================================== Input Data validation ============================ */
        $rules = [
            'username'  => 'required',
            'token'     => 'required',
        ];
        $validator = Validator::make($get, $rules);
        if (!$validator->passes()) {
            http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
        }
        /* ================================== Input Data validation ============================ */

        $username       = $get['username'];
        $token          = $get['token'];

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        /* ================================== CSV validation ============================ */
        $fileArray = array('csv_file' => $request->file('csv_file'));
        $rules = array(
          'csv_file' => 'required|mimes:csv,txt|max:10000' // max 10000kb
        );
        $validator = Validator::make($fileArray, $rules);
        if (!$validator->passes()) {
            http_response_code(404);
            return array('status'=>'0', 'data'=>'', 'message'=>$validator->errors()->all());
        }
        /* ================================== CSV validation ============================ */

        /* ============ File Upload ============ */
        $fileName = '';
        $csvFile = $request->file('csv_file');
        $csvData = array();
        if($request->file()) {

            $customerArr = csvToArray($csvFile);
            for ($i = 0; $i < count($customerArr); $i ++) {
                
                $getUserData = (array) get_row('rso_user', $customerArr[$i]['RSO Code'], 'rso_code');
                $txn    = txn_id("UTG");
                $txn_id = $txn['txn_id'];
                $type   = $txn['type'];

                $csvData[$i]['role'] = '';
                $csvData[$i]['unique_tran_id'] = $txn_id;
                $csvData[$i]['rso_code'] = $customerArr[$i]['RSO Code'];
                $csvData[$i]['rso_name'] = $getUserData['name'];
                $csvData[$i]['asm_name'] = $getUserData['asm_name'];
                $csvData[$i]['asm_code'] = $getUserData['asm_code'];
                $csvData[$i]['zone'] = $getUserData['zone'];
                $csvData[$i]['state'] = $getUserData['state'];
                $csvData[$i]['city'] = $getUserData['city'];
                $csvData[$i]['year'] = $customerArr[$i]['Year'];
                $csvData[$i]['month'] = $customerArr[$i]['Month'];
                $csvData[$i]['qt'] = $customerArr[$i]['QTR'];
                $csvData[$i]['value'] = $customerArr[$i]['Value'];
                $csvData[$i]['date_time'] = date('Y-m-d H:i:s');

                update_txn($txn_id, $type, $customerArr[$i]['RSO Code'], 'rso', 'Web', 'Done', $username);
            }

            $fileName = $username.'_'.time().'.csv';          
            $request->csv_file->move(public_path('csv/targets'), $fileName);
        }
        $insertData = TargetsModel::insert($csvData); // Not confirm insertData about this val it is getting data or not

        /*if(!empty($fileName)) {
            $response['store_image'] = url('/public/csv/targets/'.$fileName);
        }*/

        if(!empty($insertData)) {
            http_response_code(200);
            return array('status'=>'2', 'data'=>'', 'message'=>'Targets uploaded successfully.');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Targets not uploaded, try again.');
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

        $csvData = TargetsModel::select('rso_code', 'rso_name', 'asm_code', 'asm_name', 'zone', 'state', 'city', 'year', 'month', 'qt', 'value')->get();

        if(!empty($csvData)) {
            http_response_code(200);
            return array('status'=>'2', 'data'=>$csvData, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }
}
