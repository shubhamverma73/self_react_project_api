<?php

use App\Models\LogTransactionModel;
//============== Set json header ===============
function set_json_header(){
	@header("content-type: application/json");
}

//========= Empty check ==========
function blank_check($var, $name='') {
	if( !empty($name) ){
		if( empty($var) && $var!='0' ) {
			http_response_code(401);
    		exit(json_encode(array('status'=>'1', 'data'=>'', 'message'=>"".$name." Can not be null")));
		}  else {
			return $var;
		}
	} else {
		if( empty($var) ) {
			http_response_code(401);
    		exit(json_encode(array('status'=>'1', 'data'=>'', 'message'=>"Check you input value")));
		} else {
			return $var;
		}
	}
}

//======== Real escape string ============
function escape($text) {
	$db = get_instance()->db->conn_id;
    $text = mysqli_real_escape_string($db, $text);
    return $text;
}

//======== Distance calculator ============
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}

//======== change rupee format ============
function rupee_format($amount) {	
	setlocale(LC_MONETARY, 'en_IN');
	if (ctype_digit($amount) ) {
		$amount = money_format('%!.0n', $amount);
	}
	else {
		$amount = money_format('%!i', $amount);
	}
	return $amount;
}

function authenticate($username, $token) {
	if( empty($username) ) {
		return array('status'=>'0', 'data'=>'', 'message'=>'Userename can not be null');
	}
	$query = DB::table('rso_user')
                ->select('token')
                ->where('username', $username)
                ->where('token', $token)
    			->first();

    if(!empty($query->token)) {
    	return true;
    } else {
    	exit(json_encode(array('status'=>'1', 'data'=>'', 'message'=>'Authentication failed')));
    }
}

function isUserExists($username, $email) {
	if( empty($username) ) {
		return array('status'=>'0', 'data'=>'', 'message'=>'Userename can not be null');
	}
	if( empty($email) ) {
		return array('status'=>'0', 'data'=>'', 'message'=>'Email can not be null');
	}
	$query = DB::table('rso_user')
                ->select('id')
                ->where('username', $username)
                ->orWhere('email', $email)
    			->first();

    if(!empty($query->id)) {
    	http_response_code(401);
    	exit(json_encode(array('status'=>'1', 'data'=>'', 'message'=>'User already exists.')));
    } else {
    	http_response_code(200);
    	return true;
    }
}

function get_row($table, $id, $where='id') {
	$query = DB::table($table)
                ->where($where, $id)
    			->first();
   	if(!empty($query->id)) {
    	return $query;
    } else {
    	return $type=""; 
    }
}

function txn_type($code) {
	$query = DB::table('m_transaction_code')
                ->select('type')
                ->where('code', $code)
    			->first();
   	if(!empty($query->type)) {
    	return $query->type;
    } else {
    	return $type=""; 
    }
}

function txn_id($code) {
	$type = txn_type($code);
	$query = DB::table('log_transaction')
                ->select('txn_id')
                ->where('txn_id', 'like', '%' . $code . '%')
                ->orderBy('id', 'DESC')
    			->first();

    if(!empty($query->txn_id)) {
		$arr = array(
			"txn_id" => $code.date("dmy",time()).(substr($query->txn_id, -7)+1),
			"type" => $type,
		);
		return $arr; 
    } else {
    	$arr = array(
			"txn_id" => $code.date("dmy",time()).'1000001',
			"type" => $type,
		); 
		return $arr;
    }
}

//============= Update master txn table ========
function update_txn($txn_id, $type, $uniquecode, $role, $device_info, $status='',$username='',$platform='App') {

	$user           	= new LogTransactionModel;
    $user->date_created = date("Y-m-d");
    $user->time_created = date("H:i:s");
    $user->txn_id 		= $txn_id;
    $user->txn_type 	= $type;
    $user->retailer_code= $uniquecode;
    $user->role     	= $role;
    $user->platform   	= $platform;
    $user->device_info 	= $device_info;
    $user->status     	= $status;
    $user->ip_add    	= $_SERVER['REMOTE_ADDR'];
    $user->username     = $username;
    $user->execution_time= '';
    $user->save();

    $user_id        	= $user->id;
    if(!empty($user_id)) {
    	return true;
    } return false;
}

function csvToArray($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
        {
            if (!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }

    return $data;
}
?>