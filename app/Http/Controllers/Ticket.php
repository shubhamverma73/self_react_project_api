<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketModel;
use DB;

class Ticket extends Controller
{    

    function index(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $token      = blank_check($get->token, 'Token');
        $start_date = $get->start_date;
        $end_date   = $get->end_date;

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        //DB::enableQueryLog();
        $query = TicketModel::query();
        $query->where('store_id',  '!=', '');
        if (!empty($start_date)) {
            $query = $query->where('date', '>=', date('Y-m-d 00:00:00', strtotime($start_date)));
        }
        if (!empty($end_date)) {
          $query = $query->where('date', '<=', date('Y-m-d 23:59:00', strtotime($end_date)));
        }
        $data = $query->get();
        //$query = DB::getQueryLog();
        //dd($query);

        if(!empty($data)) {

            $response = array();
            foreach ($data as $row) {
                $response[]     	= array(
                    'id'        	=> $row->id,
                    'ticket_id'     => $row->ticket_id,
                    'store_id'  	=> $row->store_id,
                    'store_name'    => $row->store_name,
                    'rso_code'      => $row->rso_code,                    
                    'user_type'     => $row->user_type,
                    'ticket_related'=> $row->ticket_related,
                    'description'   => $row->description,
                    'status'     	=> $row->status,
                    'date'      	=> $row->date,
                    'remarks'   	=> $row->remarks,
                );
            }

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function view_ticket(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $id         = blank_check($get->id, 'ID');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = TicketModel::where('id', $id)->first();

        if(!empty($row->ticket_id)) {

            $response = array();
            $response[]     = array(
                'id'        	=> $row->id,
                'ticket_id'     => $row->ticket_id,
                'store_id'  	=> $row->store_id,
                'store_name'    => $row->store_name,
                'rso_code'      => $row->rso_code,                    
                'user_type'     => $row->user_type,
                'ticket_related'=> $row->ticket_related,
                'description'   => $row->description,
                'status'     	=> $row->status,
                'date'      	=> $row->date,
                'remarks'   	=> $row->remarks,
            );

            http_response_code(200);
            return array('status'=>'2', 'data'=>$response, 'message'=>'');
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function edit_ticket(Request $request) {
        $get = json_decode($request->getContent());

        $username   = blank_check($get->username, 'Username');
        $id         = blank_check($get->id, 'ID');
        $status     = blank_check($get->status, 'status');
        $remarks    = blank_check($get->remarks, 'Remarks');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

        $row = TicketModel::where('id', $id)->first();

        if(!empty($row->ticket_id)) {

            $response     = array(
                'status'  => $status,
                'remarks' => $remarks,
            );

            $updateData = TicketModel::where('id', $id)->update($response);

            if(!empty($updateData)) {
                http_response_code(200);
                return array('status'=>'2', 'data'=>'', 'message'=>'Ticket updated successfully.');
            } else {
                http_response_code(401);
                return array('status'=>'1', 'data'=>'', 'message'=>'Ticket not updated, try again.');
            }
        } else {
            http_response_code(401);
            return array('status'=>'1', 'data'=>'', 'message'=>'Data not found');
        }
    }

    function group_by_ticket_count(Request $request) {
    	$get = json_decode($request->getContent());

    	$username   = blank_check($get->username, 'Username');
        $token      = blank_check($get->token, 'Token');

        // ======== Authenticate =============
        authenticate($username, $token);
        // ======== Authenticate =============

    	$data = TicketModel::groupBy('status')
		->selectRaw('count(id) as total_ticket, status')
		->get();

		$totalRow = TicketModel::count();
		$data[] = array('total_ticket' => $totalRow, 'status' => 'Total');

    	if(!empty($data)) {
    		http_response_code(200);
            return array('status'=>'2', 'data'=>$data, 'message'=>'');
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

        $csvData = TicketModel::select('ticket_id', 'store_id', 'store_name', 'rso_code', 'user_type', 'ticket_related', 'description', 'status', 'date', 'remarks')
        			->where('store_id',  '!=', '')
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
