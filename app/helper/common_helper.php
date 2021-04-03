<?php

function sendmail( $data ) {
	require_once(FCPATH.'assets/phpmailer/class-phpmailer.php');
	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Username = SMTP_USER;
	$mail->Password = SMTP_PASS;
	$mail->SMTPSecure = 'STARTTLS';
	$mail->SMTPAutoTLS = true;
	$mail->Host = SMTP_HOST;
	$mail->Port = SMTP_PORT;

	$from_email = isset($data['from'])?$data['from']:SMTP_EMAIL;
	$from_name = isset($data['from_name'])?$data['from_name']:SMTP_NAME;

	$mail->SetFrom( $from_email, $from_name );
	$mail->isHTML( true );
	$mail->Subject = $data['subject'];
	$mail->MsgHTML( $data['message'] );
	$mail->AddAddress( $data['to'] );
	$mail->SMTPDebug = 0;

	/* Send mail and return result */
	if ( ! $mail->Send() ) {
		$errors = $mail->ErrorInfo;
		return false;
	} else {
		$mail->ClearAddresses();
		$mail->ClearAllRecipients();
		return true;
	}
}

function get_title($title, $trailing = true) {
	if( $trailing ) $title .= ' - '.SITE_NAME;
	return $title;
}

function user_logged_in( $redirect = '' ) {
	if ( get_session('logged_in') == 1 ) {
		if( !empty($redirect) ) redirect($redirect);
		return true;
	} else {
		return false;
	}
}

function logged_in_user( $user_type ) {
	if( get_session('user_type') == $user_type ) {
		return true;
	}
	return false;
}

function can_access( $admin='', $marketing='', $sales='', $sevices='', $appgift='') {
	$CI =& get_instance();
	$users = array( $admin, $marketing, $sales, $sevices, $appgift);
	
	if( user_logged_in() ) {
		if( in_array(get_session('role'), $users) ) {
			// go ahead
		} else {
			redirect('404');
		}
	} else {
		redirect('login?redirect='.current_url());
	}
}

function only_for( $admin='', $marketing='', $sales='', $sevices='', $appgift='') {
	$users = array( $admin, $marketing, $sales, $sevices, $appgift);
	$users = array_filter($users);
	
	if( user_logged_in() ) {
		if( in_array(get_session('role'), $users) ) {
			return true;
		}
		return false;
	} else {
		redirect('login');
	}
}

function get_value($field, $table, $value, $where='id') {
	$CI =& get_instance();
	$output = false;
	
	$CI->db->select($field);
	$CI->db->from($table);
	$CI->db->where($where, $value);
	$query = $CI->db->get();
	if( $query->num_rows() > 0 ) {
		$result = $query->result_array();
		$output = $result[0][$field];
	}
	return $output;
}

function set_value($field, $value, $table, $where_value, $where_cond = 'id') {
	$CI =& get_instance();
	
	$CI->db->set($field, $value);
	$CI->db->where($where_cond, $where_value);
	$result = $CI->db->update($table);
	return $result;
}

/*function get_row($table, $id, $where='id') {
	$CI =& get_instance();
	$CI->db->from($table);
	$CI->db->where($where, $id);
	$query = $CI->db->get();
	if( $query->num_rows() > 0 ) {
		$result = $query->row_array();
		return $result;
	}
	return false;
}*/


function get_table($table, $where_value ='', $where ='', $where_value1 ='', $where1 ='') {
	$CI =& get_instance();
	if( !empty($where) ) {
		$CI->db->where($where, $where_value);
	}
	if( !empty($where1) ) {
		$CI->db->where($where1, $where_value1);
	}
	$query = $CI->db->get($table);
	if( $query->num_rows() > 0 ) {
		$result = $query->result_array();
		return $result;
	}
	return false;
}

function get_extention($file) {
	return pathinfo($file['name'], PATHINFO_EXTENSION);
}

function custom_encode($string) {
	$key = "ArmSvmX";
	$string = base64_encode($string);
	$string = str_replace('=', '', $string);
	$main_arr = str_split($string);
	$output = array();
	$count = 0;
	for( $i=0; $i<strlen($string); $i++) {
		$output[] = $main_arr[$i];
		if($i%2==1) {
			$output[] = substr($key, $count, 1);
			$count++;
		}
	}
	$string = implode('', $output);
	return $string;
}

function custom_decode($string) {
	$key = "ArmSvmX";
	$arr = str_split($string);
	$count = 0;
	for( $i=0; $i<strlen($string); $i++) {
		if( $count < strlen($key) ) {
			if($i%3==2) {
				unset($arr[$i]);
				$count++;
			}
		}
	}
	$string = implode('', $arr);
	$string = base64_decode($string);
	return $string;
}

function get_array_key($value, $array) {
	while ($single = current($array)) {
		if ($single == $value) {
			return key($array);
		}
		next($array);
	}
}


// function set_session($name, $value) {
// 	$CI =& get_instance();
// 	$CI->session->set_userdata($name, $value);
// }

function set_sessions($values) {
	$CI =& get_instance();
	$CI->session->set_userdata($values);
}

function get_session($name='') {
	$CI =& get_instance();
	if( !empty($name) ) {
		return $CI->session->userdata($name);
	}
	return $CI->session->userdata();
}

function unset_session($name) {
	$CI =& get_instance();
	$CI->session->unset_userdata($name);
}


// include scripts and css
function inclusions( $values = array() ) {
	$options = array(
		'validate' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/js/validator'
							),
						),
		'datepicker' => array(
							array(
								'type' => 'css',
								'value' => 'assets/datepicker/datetimepicker.min'
							),
							array(
								'type' => 'js',
								'value' => 'assets/datepicker/moment.min'
							),
							array(
								'type' => 'js',
								'value' => 'assets/datepicker/datetimepicker.min'
							)
						),
		'chart' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/chart/charts'
							),
							array(
								'type' => 'header_js',
								'value' => 'assets/chart/light'
							),
							array(
								'type' => 'header_js',
								'value' => 'assets/chart/serial'
							)
						),
		'jquery-ui' => array(
							array(
								'type' => 'js',
								'value' => 'assets/js/jquery-ui.min'
							),
						),
		'jquery-browser' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/js/jquery-browser'
							)
						),
		'fancybox' => array(
							array(
								'type' => 'js',
								'value' => 'assets/fancybox/jquery.fancybox'
							),
							array(
								'type' => 'js',
								'value' => 'assets/js/jquery-browser'
							),
							array(
								'type' => 'css',
								'value' => 'assets/fancybox/jquery.fancybox'
							),
						),
		'datatable' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/datatables/jquery.dataTables.min'
							),
							array(
								'type' => 'header_js',
								'value' => 'assets/datatables/dataTables.bootstrap.min'
							),
							array(
								'type' => 'css',
								'value' => 'assets/datatables/dataTables.bootstrap'
							),
						),
		'chosen' => array(
							array(
								'type' => 'css',
								'value' => 'assets/chosen/chosen'
							),
							array(
								'type' => 'js',
								'value' => 'assets/chosen/chosen'
							),
						),
		'fusion' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/chart/fusion/fusioncharts'
							),
							array(
								'type' => 'header_js',
								'value' => 'assets/chart/fusion/fusioncharts.theme.fusion'
							)
						),
		'circle_progress' => array(
							array(
								'type' => 'header_js',
								'value' => 'assets/circle_progress/circlos'
							),
							array(
								'type' => 'css',
								'value' => 'assets/circle_progress/circleos'
							),
						),
	);
	
	$output['header_js'] = array(
		'assets/js/jquery-2.2.3.min'
	);

	foreach( $values as $value ) {
		$inputs = $options[$value];
		foreach( $inputs as $input ) {
			$output[$input['type']][] = $input['value'];
		}
	}

	return $output;
}

function delete_file($file_path) {
	if( is_file($file_path) ) {
		unlink($file_path);
	}
}

function format_datetime($datetime) {
	return date('j M, Y - h:ia', strtotime($datetime));
}

function format_date($date) {
	return date('j M, Y', strtotime($date));
}

function format_time($time) {
	return date('h:i A', strtotime($time));
}

function timezone_datetime($datetime = '') {
	$timezone_datetime = new DateTime($datetime, new DateTimeZone('Asia/Kolkata'));
	return $timezone_datetime;
}

function posted_ago($datetime, $full = false) {
	$now = timezone_datetime();
    $ago = timezone_datetime($datetime);

    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


function debugs($item = array(), $die = true, $display = true) {
	if( is_array($item) || is_object($item) ) {
		echo "<pre ".($display?'':'style="display:none"').">"; print_r($item); echo "</pre>";
	} else {
		echo $item;
	}
	
	if( $die ) {
		die();
	}
}

function get_count($table, $where='', $value='', $where1='', $value1='') {
	$CI =& get_instance();
	$output = false;
	
	$CI->db->select('count(*) as total');
	if( !empty($where) ) {
		$CI->db->where($where, $value);
	}
	if( !empty($where1) ) {
		$CI->db->where($where1, $value1);
	}
	$query = $CI->db->get($table);
	if( $query->num_rows() > 0 ) {
		$result = $query->row_array();
		$output = $result['total'];
	}
	return $output;
}

function safe($data) {
	$CI =& get_instance();
	$data = $CI->security->xss_clean($data);
	return $data;
}

function send_web_notification() {
	
	$registrationIds = 'dFFLSWPP2gx74To2U2yQhk:APA91bF3LKM--fkJW41w1Sn0pAEKuJ9W2PmTfkj9qOxbQk4AsChkjNBJctkfdLg5taN8s9aj7FNjqBFyko7VMBbz2MgK-Fd_jphBlotKnwWRRbOVCK4ceo2PQN6kw38yJmVVkJc1g3If';
	$API_ACCESS_KEY = 'AAAA8Iwy7Hs:APA91bF29MGH1p6yvSWwRZBLC2OZ39H4c4y_DjD-uVOTed_cgXj5yy4RgDyhhBKx1IVoR6At8EwG9bXZGetjgRCR0JTT4R1WgQzWwIeN0OksQ3sy6pkhcagpTXp5JOJsnzICFLmNaPNx';

	$headers = array(
		'Authorization: key=' . $API_ACCESS_KEY,
		'Content-Type: application/json'
	);

	//======================== Web =====================
	if( count($registrationIds) > 0 ){
		$fields = array(
			'registration_ids'  => $registrationIds,
			'data' => array(
				'title' => 'Testing Title', 
				'body' => 'Testing Message',
				'icon' => 'https://shubhamverma.tech/laravel/images/shopping-cart-icon.png',
				'click_action' => "laravel"
			),
			'priority' => 'high'
		);
			
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch);
		curl_close( $ch );
	}
}