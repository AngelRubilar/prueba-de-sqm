<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AYN_Alert_historical extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('alerts_and_notifications_helper');
    }

    function index() {
		
		//$ayn_alert_historical = $this->AYN_Alert_historical_model->get_alerts($this->login_user->id, $offset = 0, $limit = 20, $options = array("actions" => array("others")));
		//var_dump($ayn_alert_historical);
		//exit();
		$view_data = $this->_prepare_alert_list();
		
		// Filtros AppTable
		
		// Filtros Módulos de Cliente - Proyecto
		$array_clients_modules[] = array("id" => "", "text" => "- ".lang("client_project_modules")." -");
		$clients_modules = $this->Clients_modules_model->get_all()->result();
		$modulos_disponibles = array(2,6,7,12);
		foreach($clients_modules as $client_module){
			if(in_array($client_module->id, $modulos_disponibles)){
				$array_clients_modules[] = array("id" => $client_module->id, "text" => $client_module->name);
			}
		}
		$view_data["clients_modules"] = json_encode($array_clients_modules);
				
		$this->template->rander("ayn_alerts/index", $view_data);
    }
	
	function list_data() {
			
		$options = array(
			"id_client_module" => $this->input->post("id_client_module"),
			//"actions" => $this->input->post("actions")
		);
		
		$ayn_alert_historical = $this->AYN_Alert_historical_model->get_alerts($this->login_user->id, $offset = 0, $limit = NULL, $options);
		$list_data = $ayn_alert_historical->result;
		
		//var_dump($list_data);
		
		$result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _make_row($data){
		
		$project_name = ($data->id_project) ? $this->Projects_model->get_one($data->id_project)->title : "-";
		$module_name = ($data->id_client_module) ? $this->Clients_modules_model->get_one($data->id_client_module)->name : "-";

		$info = get_alert_config($data);
		
		if($info["message_type"]){
		
			$message = '<div class="media-left">';
				$message .= '<span class="avatar avatar-xs">';
					
					if($info["message_type"] == "caution" || $info["message_type"] == "reminder_caution"){
						$message .= '<i class="fa fa-exclamation-triangle" style="color: #f0ad4e; font-size:25px;"></i>';
					} elseif($info["message_type"] == "alert" || $info["message_type"] == "reminder_alert"){
						$message .= '<i class="fa fa-exclamation-circle" style="color: #f06c71; font-size:25px;"></i>';
					} else{
						$message .= '';
					}
					
				$message .= '</span>';
			$message .= '</div>';
			
			$message .= '<div class="media-body w100p">';
				$message .= '<div class="media-heading">';
					$message .= '<strong>'.lang($info["message_type"]).'</strong>';
				$message .= '</div>';
				$message .= '<div class="media m0">';
					$message .= $info["message"];
				$message .= '</div>';
			$message .= '</div>';
		
		} else {
			$message = "-";
		}
		
		$alerted_users = $this->AYN_Alert_historical_users_model->get_all_where(array(
			"id_alert_historical" => $data->id,
			"deleted" => 0
		))->result();
		$html_alerted_users = "";
		foreach($alerted_users as $alerted_user){
			$user = $this->Users_model->get_one($alerted_user->id_user);
			$user_name = $user->first_name." ".$user->last_name;
			$image_url = get_avatar($user->image);
			$avatar = anchor(get_uri("project_info/view_user_profile/".$user->id), "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $user_name", array("title" => ""));
			$html_alerted_users .= $avatar;
			if(next($alerted_users)){
				$html_alerted_users .= "<br><br>";
			}
		}

		$row_data = array(
			$data->id,
			$project_name,
			$module_name,
			$message,
			$html_alerted_users,
			($data->id_project) ? time_date_zone_format($data->alert_date, $data->id_project) : format_to_datetime($data->alertealert_dated_date)
		);

		return $row_data;
		
	}

    function count_alerts() {
		$alerts = $this->AYN_Alert_historical_model->count_alerts($this->login_user->id, $this->login_user->alert_checked_at);
		echo json_encode(array("success" => true, 'total_alerts' => $alerts));
    }

    function get_alerts() {
        $view_data = $this->_prepare_alert_list();
        $view_data["result_remaining"] = false; //don't show load more option in alert popop
        echo json_encode(array("success" => true, 'alert_list' => $this->load->view("ayn_alerts/list", $view_data, true)));
    }

    function update_alert_checking_status() {
        $now = get_current_utc_time();
        $data = array("alert_checked_at" => $now);
        $this->Users_model->save($data, $this->login_user->id);
    }

    function set_alert_status_as_read($id_alert = 0) {
        if ($id_alert) {
            $this->AYN_Alert_historical_model->set_alert_status_as_read($id_alert, $this->login_user->id);
        }
    }

    private function _prepare_alert_list($offset = 0) {
		
		/*
		$ayn_notif_historical = $this->AYN_Notif_historical_model->get_notifications($this->login_user->id, $offset = 0, $limit = NULL, array("actions" => array("others")));
		$array_notifications_historical = array();
		foreach($ayn_notif_historical->result as $notification){			
			$ayn_notif_general = $this->AYN_Notif_general_model->get_one($notification->id_notif_general);
			if($ayn_notif_general->web_notification){
				array_push($array_notifications_historical,$notification);
			}
			$ayn_notif_projects_clients = $this->AYN_Notif_projects_clients_model->get_one($notification->id_notif_projects_clients);
			if($ayn_notif_projects_clients->web_notification){
				array_push($array_notifications_historical,$notification);
			}
			$ayn_notif_projects_admin = $this->AYN_Notif_projects_admin_model->get_one($notification->id_notif_projects_admin);			
			if($ayn_notif_projects_admin->web_notification){
				array_push($array_notifications_historical,$notification);
			}
		}
		$view_data['notifications'] = $array_notifications_historical;
		*/
		
		$ayn_alert_historical = $this->AYN_Alert_historical_model->get_alerts($this->login_user->id, $offset = 0, $limit = 10, $options = array(/*"actions" => array("others")*/));
		$view_data['alerts'] = $ayn_alert_historical->result;
        $view_data['found_rows'] = $ayn_alert_historical->found_rows;
        $next_page_offset = $offset + 20;
        $view_data['next_page_offset'] = $next_page_offset;
        $view_data['result_remaining'] = $ayn_alert_historical->found_rows > $next_page_offset;		
		return $view_data;
    }
	
	function view_element_details(){
		
		$deleted_element = TRUE;
		
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		$id_element = $this->input->post("id_element");
		$event = $this->input->post("event");
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		
		$view_data["deleted_element"] = $deleted_element;
		
		$this->load->view('ayn_alerts/view_element_details', $view_data);
	
	}

}