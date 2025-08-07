<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class AYN_Notif_historical extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('alerts_and_notifications_helper');
    }

    //load notifications view
    function index() {
		
		$view_data = $this->_prepare_notification_list();
		
		// Filtros AppTable
		
		// Filtro Módulos de Cliente
		$array_client_context_modules[] = array("id" => "", "text" => "- ".lang("client_modules")." -");
		$client_context_modules = $this->Client_context_modules_model->get_all()->result();
		$modulos_disponibles = array(1,2,3,4,5,6,7,8,9);
		foreach($client_context_modules as $client_context_module){
			if(in_array($client_context_module->id, $modulos_disponibles)){
				if($client_context_module->contexto == "agreements_territory" || $client_context_module->contexto == "agreements_distribution"){
					//$text = $client_context_module->name." (".lang($client_context_module->contexto).")";
					$text = lang($client_context_module->contexto)." | ".$client_context_module->name;
				} else {
					$text = $client_context_module->name;
				}
				$array_client_context_modules[] = array("id" => $client_context_module->id, "text" => $text);
			}
		}
		$view_data["client_context_modules"] = json_encode($array_client_context_modules);
		
		// Filtros Módulos de Cliente - Proyecto
		$array_clients_modules[] = array("id" => "", "text" => "- ".lang("client_project_modules")." -");
		$clients_modules = $this->Clients_modules_model->get_all()->result();
		$modulos_disponibles = array(2,3,4,6,7,12,11);
		foreach($clients_modules as $client_module){
			if(in_array($client_module->id, $modulos_disponibles)){
				$array_clients_modules[] = array("id" => $client_module->id, "text" => $client_module->name);
			}
		}
		$view_data["clients_modules"] = json_encode($array_clients_modules);
		
		// Filtros Módulos de Administración
		$array_admin_modules[] = array("id" => "", "text" => "- ".lang("admin_modules")." -");
		$admin_modules = $this->AYN_Admin_modules_model->get_all()->result();
		$modulos_disponibles = array(4,5,7,8,9);
		foreach($admin_modules as $admin_module){
			if(in_array($admin_module->id, $modulos_disponibles)){
				$array_admin_modules[] = array("id" => $admin_module->id, "text" => $admin_module->name);
			}
		}
		$view_data["admin_modules"] = json_encode($array_admin_modules);
		
		$this->template->rander("ayn_notifications/index", $view_data);
    }
	
	function list_data() {
			
		$options = array(
			"id_admin_module" => $this->input->post("id_admin_module"),
			"id_client_module" => $this->input->post("id_client_module"),
			"id_client_context_module" => $this->input->post("id_client_context_module"),
			"actions" => $this->input->post("actions")
		);
		
		$ayn_notif_historical = $this->AYN_Notif_historical_model->get_notifications($this->login_user->id, $offset = 0, $limit = NULL, $options);
		$list_data = $ayn_notif_historical->result;
		
		$result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _make_row($data){
		
		$project_name = ($data->id_project) ? $this->Projects_model->get_one($data->id_project)->title : "-";
        
		if($data->id_client_module){
			$module_name = $this->Clients_modules_model->get_one($data->id_client_module)->name;
			//$submodule_name = ($data->id_client_submodule) ? $this->Clients_submodules_model->get_one($data->id_client_submodule)->name : "-";
		}
		
		if($data->id_client_context_module){
			$module = $this->Client_context_modules_model->get_one($data->id_client_context_module);
			if($module->contexto == "agreements_territory" || $module->contexto == "agreements_distribution"){
				//$module_name = $module->name." (".lang($module->contexto).")";
				$module_name = lang($module->contexto)." | ".$module->name;
			} else {
				$module_name = $this->Client_context_modules_model->get_one($data->id_client_context_module)->name;
				//$submodule_name = ($data->id_client_context_submodule) ? $this->Client_context_submodules_model->get_one($data->id_client_context_submodule)->name : "-";
			}
		}
		
		if($data->id_admin_module){
			$module_name = $this->AYN_Admin_modules_model->get_one($data->id_admin_module)->name;
			//$submodule_name = ($data->id_admin_submodule) ? $this->AYN_Admin_submodules_model->get_one($data->id_admin_submodule)->name : "-";
		}
		
		$info = get_notification_config($data);
		$message = $data->id_user ? "<strong>".$data->user_name."</strong>" : "<strong>".get_setting("app_title")."</strong>";
		$message .= "<br>";
        $message .= (is_array($info)) ? get_array_value($info, "message") : "";

		$row_data = array(
			$data->id,
			$project_name,
			$module_name,
			$message,
			($data->id_project) ? time_date_zone_format($data->notified_date, $data->id_project) : format_to_datetime($data->notified_date)
		);
		
		return $row_data;
		
	}

    //function load_more($offset = 0) {
    //    $view_data = $this->_prepare_notification_list($offset);
    //    $this->load->view("notifications/list_data", $view_data);
    //}

    function count_notifications() {
        $notifiations = $this->AYN_Notif_historical_model->count_notifications($this->login_user->id, $this->login_user->notification_checked_at);
        echo json_encode(array("success" => true, 'total_notifications' => $notifiations));
    }

    function get_notifications() {
        $view_data = $this->_prepare_notification_list();
        $view_data["result_remaining"] = false; //don't show load more option in notification popop
        echo json_encode(array("success" => true, 'notification_list' => $this->load->view("ayn_notifications/list", $view_data, true)));
    }

    function update_notification_checking_status() {
        $now = get_current_utc_time();
        $data = array("notification_checked_at" => $now);
        $this->Users_model->save($data, $this->login_user->id);
    }

    function set_notification_status_as_read($id_notification = 0) {
        if ($id_notification) {
            $this->AYN_Notif_historical_model->set_notification_status_as_read($id_notification, $this->login_user->id);
        }
    }

    private function _prepare_notification_list($offset = 0) {
		
		/*
		$ayn_notif_historical = $this->AYN_Notif_historical_model->get_notifications($this->login_user->id, $offset = 0, $limit = 10, array("actions" => array("others")));
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
		
		$ayn_notif_historical = $this->AYN_Notif_historical_model->get_notifications($this->login_user->id, $offset = 0, $limit = 10, array("actions" => array("others")));
		$view_data['notifications'] = $ayn_notif_historical->result;
        $view_data['found_rows'] = $ayn_notif_historical->found_rows;
        $next_page_offset = $offset + 20;
        $view_data['next_page_offset'] = $next_page_offset;
        $view_data['result_remaining'] = $ayn_notif_historical->found_rows > $next_page_offset;		
		return $view_data;
    }
	
	function view_element_details(){
		
		$deleted_element = TRUE;
		
		$module_level = $this->input->post("module_level");
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		$id_element = $this->input->post("id_element");
		$event = $this->input->post("event");
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		
		$view_data["module_level"] = $module_level;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;
		$view_data["id_element"] = $id_element;
		$view_data["event"] = $event;
		$view_data["id_cliente"] = $id_cliente;
		$view_data["id_proyecto"] = $id_proyecto;

		if($module_level == "general"){
			
			if($id_modulo == "1"){ // Ayuda y Soporte
				if($id_submodulo == "4" && $event == "send_email"){ // Contacto
					$element = $this->Contact_model->get_one($id_element);
					$deleted_element = ($element->id) ? FALSE : TRUE;
					$view_data["element"] = $element;
				}
			}
			
		}
		
		if($module_level == "project"){
		
			if($id_modulo == "11"){ // Administración Cliente
				
				if($id_submodulo == "20"){ // Configuración Panel Principal
					
					$view_data["client_environmental_footprints_settings"] = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
					$view_data["categorias_proyecto_form_consumo"] = $this->Client_consumptions_settings_model->get_project_consumptions_settings($id_cliente, $id_proyecto)->result();
					$view_data["categorias_proyecto_form_residuo"] = $this->Client_waste_settings_model->get_project_waste_settings($id_cliente, $id_proyecto)->result();
					$view_data["client_compromises_settings"] = $this->Client_compromises_settings_model->get_one_where(array("id_cliente" => $id_cliente,"id_proyecto" => $id_proyecto ,"deleted" => 0));
					$view_data["client_permitting_settings"] = $this->Client_permitting_settings_model->get_one_where(array("id_cliente" => $id_cliente,"id_proyecto" => $id_proyecto ,"deleted" => 0));
					$deleted_element = FALSE;
					
				}
				
			}
		
		}
		
		if($module_level == "admin"){
			
			if($id_modulo == "4"){ // Proyectos
				$element = $this->Projects_model->get_one($id_element);
				$deleted_element = ($element->id) ? FALSE : TRUE;
				$view_data["element"] = $element;
				$view_data["miembros_de_proyecto"] = $this->Users_model->Users_model->get_users_of_project($id_element)->result_array();
				$view_data["procesos_unitarios"] = $this->Unit_processes_model->get_pu_of_projects($id_element)->result_array();
				$view_data["huellas"] = $this->Footprints_model->get_footprints_of_project($id_element);
			}
			
			if($id_modulo == "7"){ // Unidades Funcionales
				$options = array("id" => $id_element);
				$element = $this->Functional_units_model->get_one($id_element);
				if($element->id) {
					$view_data['element'] = $element;
					$cliente = $this->Clients_model->get_one($element->id_cliente);
					$proyecto = $this->Projects_model->get_one($element->id_proyecto);
					$subproyecto = $this->Subprojects_model->get_one($element->id_subproyecto);
					$view_data["cliente"] = $cliente->company_name;
					$view_data["proyecto"] = $proyecto->title;
					$view_data["subproyecto"] = $subproyecto->nombre;
					$deleted_element = FALSE;
				}
			}
			
			if($id_modulo == "8"){ // Compromisos
				

				if($event == "comp_rca_add"){
					
					$element = $this->Values_compromises_rca_model->get_one($id_element);
					
					if($element->id) {
						
						$view_data['element'] = $element;
						$id_compromiso_proyecto = $element->id_compromiso;
						$campos_compromiso = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
						$view_data['campos_compromiso'] = $campos_compromiso;
						$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso_proyecto)->id_proyecto;
						
						$fases_decoded = json_decode($element->fases);
						$html_fases = "";
						foreach($fases_decoded as $id_fase){
							$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
							$nombre_fase = lang($nombre_lang);
							$html_fases .= "&bull; " . $nombre_fase . "<br>";
						}
						
						$view_data['html_fases'] = $html_fases;
						$view_data['tipo_matriz'] = "rca";
						
						$deleted_element = FALSE;

					}
					
				}
				
				if($event == "comp_rep_add"){
					
					$element = $this->Values_compromises_reportables_model->get_one($id_element);
					
					if($element->id) {
						
						$view_data['element'] = $element;
						$id_compromiso_proyecto = $element->id_compromiso;
						$campos_compromiso = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
						$view_data['campos_compromiso'] = $campos_compromiso;
						$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
						
						$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(array(
							"id_compromiso" => $id_element,
							"deleted" => 0,
						))->result();
						
						$html_planes = "";
						foreach($planificaciones as $planificacion){
							$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
						}
						$view_data['html_planes'] = $html_planes;
						$view_data['tipo_matriz'] = "reportable";
						
						$deleted_element = FALSE;
					}
					
				}
				
			}
			
			if($id_modulo == "9"){ // Permisos
			
				if($event == "permitting_add"){
					
					$element = $this->Values_permitting_model->get_one($id_element);
					$view_data["element"] = $element;
					if($element->id){
						
						$campos_permiso = $this->Permitting_model->get_fields_of_permitting($element->id_permiso)->result_array();
						$view_data["campos_permiso"] = $campos_permiso;
						
						$fases_decoded = json_decode($element->fases);
						$html_fases = "";
						foreach($fases_decoded as $id_fase){
							$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
							$nombre_fase = lang($nombre_lang);
							$html_fases .= "&bull; " . $nombre_fase . "<br>";
						}
						$view_data['html_fases'] = $html_fases;
						
						$deleted_element = FALSE;
					}
					
				}
			
			}
			
		}

		$view_data["deleted_element"] = $deleted_element;
		
		$this->load->view('ayn_notifications/view_element_details', $view_data);
		
	}

}

/* End of file notifications.php */
/* Location: ./application/controllers/Notifications.php */