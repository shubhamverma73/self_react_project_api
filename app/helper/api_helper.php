<?php
//============== Set json header ===============
function set_json_header(){
	@header("content-type: application/json");
}

//========= Empty check ==========
function blank_check($var, $name='') {
	if( !empty($name) ){
		if( empty($var) && $var!='0' ) {
			set_json_header();
			echo json_encode(array('status'=>'0', 'data'=>'', 'message'=>"".$name." Can not be null"));
			exit;
		}  else {
			return $var;
		}
	} else {
		if( empty($var) ) {
			set_json_header();
			echo json_encode(array('status'=>'0', 'data'=>'', 'message'=>"Check Your Input."));
			exit;
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
?>