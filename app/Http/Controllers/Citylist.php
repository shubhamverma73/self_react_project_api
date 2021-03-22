<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citylists;

class Citylist extends Controller
{
    function getZoneList() {
    	$data = Citylists::orderBy('region', 'asc')->groupBy('region')->pluck('region')->toArray();

    	$response = array();
        foreach ($data as $row) {
            $response[]     = array(
                'region'    => $row
            );
        }

    	if(!empty($data)) {
    		http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
    	} else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function getStateList(Request $request) {
    	$get = json_decode($request->getContent());
        $zone= blank_check($get->zone, 'Zone');

    	$data = Citylists::where('region', $zone)->orderBy('statename', 'asc')->groupBy('statename')->pluck('statename')->toArray();

    	$response = array();
        foreach ($data as $row) {
            $response[]     = array(
                'statename' => $row
            );
        }

    	if(!empty($data)) {
    		http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
    	} else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function getCityList(Request $request) {
    	$get 	= json_decode($request->getContent());
        $state 	= blank_check($get->state, 'State');

    	$data 	= Citylists::where('statename', $state)->orderBy('cityname', 'asc')->pluck('cityname')->toArray();

    	$response = array();
        foreach ($data as $row) {
            $response[]     = array(
                'cityname' => $row
            );
        }

    	if(!empty($data)) {
    		http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
    	} else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }
}
