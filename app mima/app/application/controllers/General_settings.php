<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class General_settings extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    function index() {
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");		
        $this->template->rander("general_settings/index", $view_data);
    }
	
	/* devolver dropdown con los proyectos de un cliente */	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');
 
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="col-md-5">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	/* Devolver vista de configuracion general */
	function get_all_settings(){
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		
		
		$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $data['timezone_dropdown'][$zone] = $zone;
        }
		
		$data['language_dropdown'] = array();
        $dir = "./application/language/";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file && $file != "." && $file != ".." && $file != "index.html") {
						if($file == "spanish" || $file == "english"){
							//$data['language_dropdown'][$file] = ucfirst($file);
							$data['language_dropdown'][$file] = ucfirst(lang($file));
						}
                        
                    }
                }
                closedir($dh);
            }
        }
		
		//Traer materiales según el proyecto seleccionado
		$data['materiales'] = $this->Materials_model->get_materials_of_projects($id_proyecto)->result(); 
		
		//Traer las configuraciones del proyecto
		$data["general_settings"] = $this->General_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$data["reports_config_settings"] = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		//$data['module_availability_settings'] = $this->Clients_modules_model->get_details()->result();


		$options_clients_modules = array("modules" => array(1,2,3,4,5,6,7,8,9,10,11));
		$options_air_modules = array("modules" => array(12,13,14,15,16,17,18,20));

		$data['module_availability_settings'] = $this->Module_availability_model->get_project_setting($id_cliente, $id_proyecto, $options_clients_modules)->result();
		$data['module_availability_settings_air'] = $this->Module_availability_model->get_project_setting($id_cliente, $id_proyecto, $options_air_modules)->result();

		
		//Trae la data de los tipos de unidades de las huellas utilizadas por proyecto
		//$data["tipos_de_unidad"] = $this->Unity_type_model->get_all_where(array("deleted" => 0))->result();
		$options_unity_types = array("unity_types" => array(1,2,3,4,5,6,7,8,9));
		$options_air_unity_types = array("unity_types" => array(10,11,12,13,14,15,16));
		$data["tipos_de_unidad"] = $this->Unity_type_model->get_details($options_unity_types)->result();
		$data["tipos_de_unidad_air"] = $this->Unity_type_model->get_details($options_air_unity_types)->result();
		
		//$data["huella"] = $this->Unity_model->get_dropdown_list(array("nombre"), "id");
		
		$data["module_footprints_units"] =  $this->Module_footprint_units_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result_array();
		$data["reports_units_settings"] =  $this->Reports_units_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result_array();
		
		$data["id_cliente"] = $id_cliente;
		$data["id_proyecto"] = $id_proyecto;
		
		if(!$data['module_availability_settings']){
			$data['module_availability_settings'] = $this->Clients_modules_model->get_details($options_clients_modules)->result();
			$data['module_availability_settings_air'] = $this->Clients_modules_model->get_details($options_air_modules)->result();
		}
		
		// Tab Configuración de Notificaciones de Usuarios
		$options = array(
			"clients_modules" => array(2, 3, 4, 6, 7, 12, 11),
			"clients_submodules" => array(4, 22, 6, 23, 24, 20, 21)
		);
		$project_modules = $this->Clients_modules_model->get_project_modules_and_submodules($options)->result_array();
		$array_registros_proyecto = array();
		$array_compromisos = array();
		$array_permisos = array();
		$array_administracion_cliente = array();
		
		foreach($project_modules as $module){
			
			$id_submodulo = ($module["id_submodulo"]) ? $module["id_submodulo"] : 0;
			
			$notif_config_add = $this->AYN_Notif_projects_clients_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => $module["id_modulo"],
				"id_client_submodule" => $id_submodulo,
				"event" => "add",
				"deleted" => 0
			));
			$config_icon_add = ($notif_config_add->email_notification || $notif_config_add->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_edit = $this->AYN_Notif_projects_clients_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => $module["id_modulo"],
				"id_client_submodule" => $id_submodulo,
				"event" => "edit",
				"deleted" => 0
			));
			$config_icon_edit = ($notif_config_edit->email_notification || $notif_config_edit->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_delete = $this->AYN_Notif_projects_clients_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => $module["id_modulo"],
				"id_client_submodule" => $id_submodulo,
				"event" => "delete",
				"deleted" => 0
			));
			$config_icon_delete = ($notif_config_delete->email_notification || $notif_config_delete->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			if($module["id_modulo"] == "2"){ // Registros Ambientales
				
				$array_registros_proyecto[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "environmental_records"
				);
				
				$data["events_environmental_records_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "environmental_records", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_add->id, "data-post-id_notif_config_edit" => $notif_config_edit->id, "data-post-id_notif_config_delete" => $notif_config_delete->id));
				
				$data["events_environmental_records_icons"] = array(
					"add" 	 => $config_icon_add, 
					"edit" 	 => $config_icon_edit, 
					"delete" => $config_icon_delete,
				);
								
			}
			
			if($module["id_modulo"] == "3"){ // Mantenedoras
				
				$array_registros_proyecto[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "feeders"
				);
				
				$data["events_feeders_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "feeders", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_add->id, "data-post-id_notif_config_edit" => $notif_config_edit->id, "data-post-id_notif_config_delete" => $notif_config_delete->id));
				
				$data["events_feeders_icons"] = array(
					"add" 	 => $config_icon_add, 
					"edit" 	 => $config_icon_edit, 
					"delete" => $config_icon_delete,
				);
				
			}
			
			if($module["id_modulo"] == "4"){ // Otros Registros
				
				$array_registros_proyecto[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "other_records"
				);
				
				$data["events_other_records_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "other_records", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_add->id, "data-post-id_notif_config_edit" => $notif_config_edit->id, "data-post-id_notif_config_delete" => $notif_config_delete->id));
				
				$data["events_other_records_icons"] = array(
					"add" 	 => $config_icon_add, 
					"edit" 	 => $config_icon_edit, 
					"delete" => $config_icon_delete,
				);
				
			}
			
			
			if($module["id_modulo"] == "6"){ // Compromisos
				
				if($id_submodulo == "4"){ // Evaluación de Compromisos RCA

					$array_compromisos[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"item" => "compromises_rca"
					);
					
					$data["events_compromises_rca_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "compromises_rca", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_add->id, "data-post-id_notif_config_edit" => $notif_config_edit->id));
					
					$data["events_compromises_rca_icons"] = array(
						"add" 	 => $config_icon_add, 
						"edit" 	 => $config_icon_edit, 
					);
					
				}
				
				if($id_submodulo == "22"){ // Evaluación de Compromisos Reportables
					
					$array_compromisos[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"item" => "compromises_rep"
					);
					
					$data["events_compromises_rep_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "compromises_rep", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_edit" => $notif_config_edit->id));
					
					$data["events_compromises_rep_icons"] = array(
						"edit" 	 => $config_icon_edit, 
					);
					
				}
				
			}
			
			if($module["id_modulo"] == "7"){ // Permisos

				$array_permisos[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "permittings"
				);
				
				$data["events_permittings_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "permittings", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_add->id, "data-post-id_notif_config_edit" => $notif_config_edit->id));
				
				$data["events_permittings_icons"] = array(
					"add" 	 => $config_icon_add,
					"edit" 	 => $config_icon_edit, 
				);
				
			}
			
			if($module["id_modulo"] == "11"){ // Administración Cliente
				
				if($id_submodulo == "20"){ // Configuración Panel Principal

					$array_administracion_cliente[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"item" => "setting_dashboard"
					);
					
					$data["events_setting_dashboard_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "setting_dashboard", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_edit" => $notif_config_edit->id));
					
					$data["events_setting_dashboard_icons"] = array(
						"edit" => $config_icon_edit, 
					);
					
				}
				
				if($id_submodulo == "21"){ // Carga Masiva
					
					$notif_config_bulk_load = $this->AYN_Notif_projects_clients_model->get_one_where(array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_client_module" => $module["id_modulo"],
						"id_client_submodule" => $id_submodulo,
						"event" => "bulk_load",
						"deleted" => 0
					));
					$config_icon_bulk_load = ($notif_config_bulk_load->email_notification || $notif_config_bulk_load->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';					
					
					$array_administracion_cliente[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"item" => "bulk_load"
					);
					
					$data["events_bulk_load_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "bulk_load", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_bulk_load" => $notif_config_bulk_load->id));
					
					$data["events_bulk_load_icons"] = array(
						"bulk_load" => $config_icon_bulk_load, 
					);
					
				}
				
			}
			
		}
		
		$data["array_registros_proyecto"] = $array_registros_proyecto;
		$data["array_compromisos"] = $array_compromisos;
		$data["array_permisos"] = $array_permisos;
		$data["array_administracion_cliente"] = $array_administracion_cliente;		
		
		// Tab Configuración de Notificaciones de Administración
		$admin_modules = $this->AYN_Admin_modules_model->get_admin_modules_for_notification_config()->result_array();
		$array_proyectos = array();
		$array_registros = array();
		$array_indicadores = array();
		$array_compromisos_admin = array();
		$array_permisos_admin = array();
		
		
		foreach($admin_modules as $module){
			
			$id_submodulo = ($module["id_submodulo"]) ? $module["id_submodulo"] : 0;
			
			$notif_config_project_edit_name = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_name",
				"deleted" => 0
			));
			$config_icon_project_edit_name = ($notif_config_project_edit_name->email_notification || $notif_config_project_edit_name->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_auth_amb = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_auth_amb",
				"deleted" => 0
			));
			$config_icon_project_edit_auth_amb = ($notif_config_project_edit_auth_amb->email_notification || $notif_config_project_edit_auth_amb->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_start_date = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_start_date",
				"deleted" => 0
			));
			$config_icon_project_edit_start_date = ($notif_config_project_edit_start_date->email_notification || $notif_config_project_edit_start_date->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_end_date = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_end_date",
				"deleted" => 0
			));
			$config_icon_project_edit_end_date = ($notif_config_project_edit_end_date->email_notification || $notif_config_project_edit_end_date->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_members = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_members",
				"deleted" => 0
			));
			$config_icon_project_edit_members = ($notif_config_project_edit_members->email_notification || $notif_config_project_edit_members->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_desc = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_desc",
				"deleted" => 0
			));
			$config_icon_project_edit_desc = ($notif_config_project_edit_desc->email_notification || $notif_config_project_edit_desc->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_status = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_status",
				"deleted" => 0
			));
			$config_icon_project_edit_status = ($notif_config_project_edit_status->email_notification || $notif_config_project_edit_status->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_pu = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_pu",
				"deleted" => 0
			));
			$config_icon_project_edit_pu = ($notif_config_project_edit_pu->email_notification || $notif_config_project_edit_pu->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_project_edit_cat_impact = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "project_edit_cat_impact",
				"deleted" => 0
			));
			$config_icon_project_edit_cat_impact = ($notif_config_project_edit_cat_impact->email_notification || $notif_config_project_edit_cat_impact->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			
			if($module["id_modulo"] == "4"){ // Proyectos
				
				$array_proyectos[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"events" => array(
									"project_edit_name" => array($config_icon_project_edit_name, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_name", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_name->id))), 
									"project_edit_auth_amb" => array($config_icon_project_edit_auth_amb, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_auth_amb", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_auth_amb->id))), 
									"project_edit_start_date" => array($config_icon_project_edit_start_date, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_start_date", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_start_date->id))),
									"project_edit_end_date" => array($config_icon_project_edit_end_date, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_end_date", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_end_date->id))),
									"project_edit_members" => array($config_icon_project_edit_members, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_members", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_members->id))),
									"project_edit_desc" => array($config_icon_project_edit_desc, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_desc", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_desc->id))),
									"project_edit_status" => array($config_icon_project_edit_status, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_status", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_status->id))),
									"project_edit_pu" => array($config_icon_project_edit_pu, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_pu", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_pu->id))),
									"project_edit_cat_impact" => array($config_icon_project_edit_cat_impact, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "project_edit_cat_impact", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_project_edit_cat_impact->id)))
								)
				);				
					
			}
			
			$notif_config_form_add = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "form_add",
				"deleted" => 0
			));
			$config_icon_form_add = ($notif_config_form_add->email_notification || $notif_config_form_add->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_form_edit_name = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "form_edit_name",
				"deleted" => 0
			));
			$config_icon_form_edit_name = ($notif_config_form_edit_name->email_notification || $notif_config_form_edit_name->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_form_edit_cat = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "form_edit_cat",
				"deleted" => 0
			));
			$config_icon_form_edit_cat = ($notif_config_form_edit_cat->email_notification || $notif_config_form_edit_cat->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_form_delete = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "form_delete",
				"deleted" => 0
			));
			$config_icon_form_delete = ($notif_config_form_delete->email_notification || $notif_config_form_delete->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			if($module["id_modulo"] == "5"){ // Registros
				
				$array_registros[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "records"
				);
				
				$data["events_records_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "records", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_form_add" => $notif_config_form_add->id, "data-post-id_notif_config_form_edit_name" => $notif_config_form_edit_name->id, "data-post-id_notif_config_form_edit_cat" => $notif_config_form_edit_cat->id, "data-post-id_notif_config_form_delete" => $notif_config_form_delete->id));
				
				$data["events_records_icons"] = array(
					"form_add"			=> $config_icon_form_add, 
					"form_edit_name"	=> $config_icon_form_edit_name, 
					"form_edit_cat"		=> $config_icon_form_edit_cat,
					"form_delete"		=> $config_icon_form_delete,
				);

			}
			
			$notif_config_uf_add_element = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "uf_add_element",
				"deleted" => 0
			));
			$config_icon_uf_add_element = ($notif_config_uf_add_element->email_notification || $notif_config_uf_add_element->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_uf_edit_element = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "uf_edit_element",
				"deleted" => 0
			));
			$config_icon_uf_edit_element = ($notif_config_uf_edit_element->email_notification || $notif_config_uf_edit_element->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_uf_delete_element = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "uf_delete_element",
				"deleted" => 0
			));
			$config_icon_uf_delete_element = ($notif_config_uf_delete_element->email_notification || $notif_config_uf_delete_element->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			if($module["id_modulo"] == "7"){ // Indicadores
							
				$array_indicadores[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"item" => "indicators"
				);
				
				$data["events_indicators_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-item" => "indicators", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config_add" => $notif_config_uf_add_element->id, "data-post-id_notif_config_edit" => $notif_config_uf_edit_element->id, "data-post-id_notif_config_delete" => $notif_config_uf_delete_element->id));
				
				$data["events_indicators_icons"] = array(
					"uf_add_element" 	 => $config_icon_uf_add_element, 
					"uf_edit_element" 	 => $config_icon_uf_edit_element, 
					"uf_delete_element" => $config_icon_uf_delete_element,
				);
				
			}
			
			$notif_config_comp_rca_add = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "comp_rca_add",
				"deleted" => 0
			));
			$config_icon_comp_rca_add = ($notif_config_comp_rca_add->email_notification || $notif_config_comp_rca_add->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$notif_config_comp_rep_add = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "comp_rep_add",
				"deleted" => 0
			));
			$config_icon_comp_rep_add = ($notif_config_comp_rep_add->email_notification || $notif_config_comp_rep_add->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			if($module["id_modulo"] == "8"){ // Compromisos
				$array_compromisos_admin[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"events" => array(
									"comp_rca_add" => array($config_icon_comp_rca_add, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "comp_rca_add", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_comp_rca_add->id))), 
									"comp_rep_add" => array($config_icon_comp_rep_add, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "comp_rep_add", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_comp_rep_add->id))), 
								)
				);
			}
			
			$notif_config_permitting_add = $this->AYN_Notif_projects_admin_model->get_one_where(array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_admin_module" => $module["id_modulo"],
				"id_admin_submodule" => $id_submodulo,
				"event" => "permitting_add",
				"deleted" => 0
			));
			$config_icon_permitting_add = ($notif_config_permitting_add->email_notification || $notif_config_permitting_add->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			if($module["id_modulo"] == "9"){ // Permisos
				$array_permisos_admin[] = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"events" => array(
									"permitting_add" => array($config_icon_permitting_add, modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => "permitting_add", "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_notif_config" => $notif_config_permitting_add->id)))								)
				);
			}
			
		}
		
		$data["array_proyectos"] = $array_proyectos;
		$data["array_registros"] = $array_registros;
		$data["array_indicadores"] = $array_indicadores;
		$data["array_compromisos_admin"] = $array_compromisos_admin;
		$data["array_permisos_admin"] = $array_permisos_admin;


		// Tab Configuración de Alertas
		$options = array(
			"clients_modules" => array(2, 6, 7, 12),
			"clients_submodules" => array(4, 22, 6, 23)
		);
		$alert_project_modules = $this->Clients_modules_model->get_project_modules_and_submodules($options)->result_array();
		
		foreach($alert_project_modules as $module){
			
			$id_submodulo = ($module["id_submodulo"]) ? $module["id_submodulo"] : 0;
			
			if($module["id_modulo"] == "2"){ // Registros Ambientales
				
				$items = $this->AYN_Alert_projects_model->get_categories_and_units_of_forms_projects($id_proyecto)->result();
				$array_ra = array();
				foreach($items as $item){
					
					// Llamar a la configuración de cada una de las categorías de los formularios del proyecto seleccionado
					$config_options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_client_module" => $module["id_modulo"],
						"id_client_submodule" => $id_submodulo,
						"alert_config" => array(
							"id_categoria" => $item->id_categoria,
							"id_tipo_unidad" => $item->id_tipo_unidad,
							"id_unidad" => $item->id_unidad
						),
					);
					
					$alert_projects_config_ra = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();					
					
					if($alert_projects_config_ra->risk_email_alert || $alert_projects_config_ra->risk_web_alert || $alert_projects_config_ra->threshold_email_alert || $alert_projects_config_ra->threshold_web_alert){
						$config_icon_ra = '<i class="fa fa-check"></i>';
					} else {
						$config_icon_ra = '<i class="fa fa-times"></i>';
					}
					
					$nombre_item = $item->nombre_categoria." (".$item->nombre_tipo_unidad.")";
					$alert_config = json_decode($alert_projects_config_ra->alert_config, TRUE);
					
					$array_ra[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"id_categoria" => $item->id_categoria, 
						"id_tipo_unidad" => $item->id_tipo_unidad,
						"nombre_categoria" => $item->nombre_categoria, 
						"nombre_tipo_unidad" => $item->nombre_tipo_unidad,
						"nombre_item" => $nombre_item,
						"risk_value" => ($alert_config["risk_value"]) ? $alert_config["risk_value"] : "-",
						"threshold_value" => ($alert_config["threshold_value"]) ? $alert_config["threshold_value"] : "-",
						"setting_icon" => $config_icon_ra,
						"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-id_categoria" => $item->id_categoria, "data-post-id_tipo_unidad" => $item->id_tipo_unidad, "data-post-id_unidad" => $item->id_unidad, "data-post-nombre_item" => $nombre_item, "data-post-nombre_unidad" => $item->nombre_unidad, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_alert_config" => $alert_projects_config_ra->id))
					);
				}

			}
			
			if($module["id_modulo"] == "6"){ // Compromisos
				
				if($id_submodulo == "4"){ // Evaluación de Compromisos RCA

					// Llamar a la configuración de cada uno de los valores compromisos rca del cliente - proyecto
					$config_options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_client_module" => $module["id_modulo"],
						"id_client_submodule" => $id_submodulo,
						"alert_config" => array(
							"tipo_evaluacion" => "rca",
						),
					);
					
					$alert_projects_config_comp_rca = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();

					if($alert_projects_config_comp_rca->risk_email_alert || $alert_projects_config_comp_rca->risk_web_alert || $alert_projects_config_comp_rca->threshold_email_alert || $alert_projects_config_comp_rca->threshold_web_alert){
						$config_icon_comp_rca = '<i class="fa fa-check"></i>';
					} else {
						$config_icon_comp_rca = '<i class="fa fa-times"></i>';
					}
					
					$alert_config = json_decode($alert_projects_config_comp_rca->alert_config, TRUE);
					
					//$risk_value = ($alert_config["risk_value"]) ? $this->Compromises_compliance_status_model->get_one($alert_config["risk_value"])->nombre_estado : "-";
					$estado_risk_value = $this->Compromises_compliance_status_model->get_one($alert_config["risk_value"]);
					$nombre_estado_risk_value = $estado_risk_value->nombre_estado;
					$color_estado_risk_value = $estado_risk_value->color;
					
					if($estado_risk_value->id){
						$html_estado_risk_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_risk_value .= '<div style="background-color:'.$color_estado_risk_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_risk_value .= $nombre_estado_risk_value;
						$html_estado_risk_value .= '</div>';
					} else {
						$html_estado_risk_value = "-";
					}
					
					//$threshold_value = ($alert_config["threshold_value"]) ? $this->Compromises_compliance_status_model->get_one($alert_config["threshold_value"])->nombre_estado : "-";
					$estado_threshold_value = $this->Compromises_compliance_status_model->get_one($alert_config["threshold_value"]);
					$nombre_estado_threshold_value = $estado_threshold_value->nombre_estado;
					$color_estado_threshold_value = $estado_threshold_value->color;
					
					if($estado_threshold_value->id){
						$html_estado_threshold_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_threshold_value .= '<div style="background-color:'.$color_estado_threshold_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_threshold_value .= $nombre_estado_threshold_value;
						$html_estado_threshold_value .= '</div>';
					} else {
						$html_estado_threshold_value = "-";
					}
					
					$array_comp_rca = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"tipo_evaluacion" => "rca",
						"nombre_item" => lang("compromises_rca"),
						"risk_value" => $html_estado_risk_value,
						"threshold_value" => $html_estado_threshold_value,
						"setting_icon" => $config_icon_comp_rca,
						"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-nombre_item" => lang("compromises_rca"), "data-post-tipo_evaluacion" => "rca", "data-post-id_alert_config" => $alert_projects_config_comp_rca->id))
					);

				}
				
				if($id_submodulo == "22"){ // Evaluación de Compromisos Reportables
					
					// Llamar a la configuración de cada uno de los valores compromisos reportables del cliente - proyecto
					$config_options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_client_module" => $module["id_modulo"],
						"id_client_submodule" => $id_submodulo,
						"alert_config" => array(
							"tipo_evaluacion" => "reportable",
						),
					);
					
					$alert_projects_config_comp_rep = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					
					if($alert_projects_config_comp_rep->risk_email_alert || $alert_projects_config_comp_rep->risk_web_alert || $alert_projects_config_comp_rep->threshold_email_alert || $alert_projects_config_comp_rep->threshold_web_alert){
						$config_icon_comp_rep = '<i class="fa fa-check"></i>';
					} else {
						$config_icon_comp_rep = '<i class="fa fa-times"></i>';
					}
					
					$alert_config = json_decode($alert_projects_config_comp_rep->alert_config, TRUE);
					
					//$risk_value = ($alert_config["risk_value"]) ? $this->Compromises_compliance_status_model->get_one($alert_config["risk_value"])->nombre_estado : "-";
					$estado_risk_value = $this->Compromises_compliance_status_model->get_one($alert_config["risk_value"]);
					$nombre_estado_risk_value = $estado_risk_value->nombre_estado;
					$color_estado_risk_value = $estado_risk_value->color;
					
					if($estado_risk_value->id){
						$html_estado_risk_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_risk_value .= '<div style="background-color:'.$color_estado_risk_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_risk_value .= $nombre_estado_risk_value;
						$html_estado_risk_value .= '</div>';
					} else {
						$html_estado_risk_value = "-";
					}
					
					//$threshold_value = ($alert_config["threshold_value"]) ? $this->Compromises_compliance_status_model->get_one($alert_config["threshold_value"])->nombre_estado : "-";
					$estado_threshold_value = $this->Compromises_compliance_status_model->get_one($alert_config["threshold_value"]);
					$nombre_estado_threshold_value = $estado_threshold_value->nombre_estado;
					$color_estado_threshold_value = $estado_threshold_value->color;
					
					if($estado_threshold_value->id){
						$html_estado_threshold_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_threshold_value .= '<div style="background-color:'.$color_estado_threshold_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_threshold_value .= $nombre_estado_threshold_value;
						$html_estado_threshold_value .= '</div>';
					} else {
						$html_estado_threshold_value = "-";
					}
					
					$array_comp_rep = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"tipo_evaluacion" => "reportable",
						"nombre_item" => lang("compromises_rep"),
						"risk_value" => $html_estado_risk_value,
						"threshold_value" => $html_estado_threshold_value,
						"setting_icon" => $config_icon_comp_rep,
						"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-nombre_item" => lang("compromises_rep"), "data-post-tipo_evaluacion" => "reportable", "data-post-id_alert_config" => $alert_projects_config_comp_rep->id))
					);		
					
					// items: Planificaciones de Compromisos reportables del cliente y proyecto seleccionado
					$compromiso_rep = $this->Compromises_reportables_model->get_one_where(array(
						"id_proyecto" => $id_proyecto,
						"deleted" => 0
					));
					$valores_compromisos_rep = $this->Values_compromises_reportables_model->get_all_where(array(
						"id_compromiso" => $compromiso_rep->id,
						"deleted" => 0
					))->result();
					
					$array_planificaciones_comp_rep = array();
					foreach($valores_compromisos_rep as $item){
						
						$planificaciones_rep = $this->Plans_reportables_compromises_model->get_all_where(array(
							"id_compromiso" => $item->id,
							"deleted" => 0
						))->result();
						
						foreach($planificaciones_rep as $planificacion_rep){
							
							// Llamar a la configuración de cada planificacion de los valores compromisos reportables del cliente - proyecto
							$config_options = array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_client_module" => $module["id_modulo"],
								"id_client_submodule" => $id_submodulo,
								"alert_config" => array("id_planificacion" => $planificacion_rep->id)
							);
							
							$alert_projects_config_planification_rep = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
							
							if($alert_projects_config_planification_rep->id){
								
								if($alert_projects_config_planification_rep->risk_email_alert || $alert_projects_config_planification_rep->risk_web_alert || $alert_projects_config_planification_rep->threshold_email_alert || $alert_projects_config_planification_rep->threshold_web_alert){
									$config_icon_planification_rep = '<i class="fa fa-check"></i>';
								} else {
									$config_icon_planification_rep = '<i class="fa fa-times"></i>';
								}
								
								$nombre_item = $item->nombre_compromiso." | ".$planificacion_rep->descripcion." (".format_to_date($planificacion_rep->planificacion, false).")";
								$alert_config = json_decode($alert_projects_config_planification_rep->alert_config, TRUE);
								
								$array_planificaciones_comp_rep[] = array(
									"id_module" => $module["id_modulo"],
									"id_submodule" => $id_submodulo,
									"module" => $module["nombre_modulo"],
									"submodule" => $module["nombre_submodulo"],
									"id_planificacion" => $planificacion_rep->id,
									"id_valor_compromiso" => $planificacion_rep->id_compromiso,
									"nombre_item" => $nombre_item,
									"descripcion" => $planificacion_rep->descripcion,
									"planificacion" => $planificacion_rep->planificacion,
									"risk_value" => ($alert_config["risk_value"]) ? $alert_config["risk_value"] : "-",
									"threshold_value" => ($alert_config["threshold_value"]) ? $alert_config["threshold_value"] : "-",
									"setting_icon" => $config_icon_planification_rep,
									"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-nombre_item" => $nombre_item, "data-post-id_planificacion" => $planificacion_rep->id, "data-post-id_alert_config" => $alert_projects_config_planification_rep->id))
								);
								
							}
	
						}
						
					}
					
				}		
				
			}
			
			if($module["id_modulo"] == "7"){ // Permisos
				
				// Llamar a la configuración de cada uno de los valores permisos del cliente - proyecto
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => $module["id_modulo"],
					"id_client_submodule" => $id_submodulo,
					"alert_config" => array()
				);
				
				$alert_projects_config_permisos = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();					
				
				if($alert_projects_config_permisos->risk_email_alert || $alert_projects_config_permisos->risk_web_alert || $alert_projects_config_permisos->threshold_email_alert || $alert_projects_config_permisos->threshold_web_alert){
					$config_icon_permisos = '<i class="fa fa-check"></i>';
				} else {
					$config_icon_permisos = '<i class="fa fa-times"></i>';
				}
				
				$nombre_item = lang("permittings");
				$alert_config = json_decode($alert_projects_config_permisos->alert_config, TRUE);
				
				//$risk_value = ($alert_config["risk_value"]) ? $this->Permitting_procedure_status_model->get_one($alert_config["risk_value"])->nombre_estado : "-";
				$estado_risk_value = $this->Permitting_procedure_status_model->get_one($alert_config["risk_value"]);
				$nombre_estado_risk_value = $estado_risk_value->nombre_estado;
				$color_estado_risk_value = $estado_risk_value->color;
				
				if($estado_risk_value->id){
					$html_estado_risk_value = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_risk_value .= '<div style="background-color:'.$color_estado_risk_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_risk_value .= $nombre_estado_risk_value;
					$html_estado_risk_value .= '</div>';
				} else {
					$html_estado_risk_value = "-";
				}
					
				//$threshold_value = ($alert_config["threshold_value"]) ? $this->Permitting_procedure_status_model->get_one($alert_config["threshold_value"])->nombre_estado : "-";
				$estado_threshold_value = $this->Permitting_procedure_status_model->get_one($alert_config["threshold_value"]);
				$nombre_estado_threshold_value = $estado_threshold_value->nombre_estado;
				$color_estado_threshold_value = $estado_threshold_value->color;
				
				if($estado_threshold_value->id){
					$html_estado_threshold_value = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_threshold_value .= '<div style="background-color:'.$color_estado_threshold_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_threshold_value .= $nombre_estado_threshold_value;
					$html_estado_threshold_value .= '</div>';
				} else {
					$html_estado_threshold_value = "-";
				}
				
				$array_valores_permisos = array(
					"id_module" => $module["id_modulo"],
					"id_submodule" => $id_submodulo,
					"module" => $module["nombre_modulo"],
					"submodule" => $module["nombre_submodulo"],
					"nombre_item" => $nombre_item,
					"risk_value" => $html_estado_risk_value,
					"threshold_value" => $html_estado_threshold_value,
					"setting_icon" => $config_icon_permisos,
					"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-nombre_item" => $nombre_item, "data-post-id_alert_config" => $alert_projects_config_permisos->id))
				);	

			}
			
		}


		// Tab Configuración de Alertas para Calidad del Aire - Pronósticos
		$array_air_forecast_alerts = array();

		// Listar las Variables de las Estaciones
		$options_sector_variables = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto
		);
		$air_sector_variables = $this->Air_stations_model->get_variables_by_station($options_sector_variables)->result();
		$forecast_module = $this->Clients_modules_model->get_one(14);
		
		foreach($air_sector_variables AS $air_sector_variable){

			// Acordeón Alertas de Pronóstico
			// Llamar a la configuración de Alertas de Pronóstico
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => $forecast_module->id, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
					"id_air_station" => $air_sector_variable->id_air_station,
					"id_air_sector" => $air_sector_variable->id_air_sector,
					"id_air_variable" => $air_sector_variable->id_air_variable
				),
			);

			$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
			
			$configured = FALSE;
			$min_value = "-";
			$max_value = "-";

			if(count($alert_config_forecast)){

				$alert_config = $alert_config_forecast->alert_config;
				foreach($alert_config as $config){
					if($config->nc_active){
						$configured = TRUE;
						break;
					}
				}

				$array_min_values = array();
				foreach($alert_config as $config){
					if($config->nc_active){
						$array_min_values[] = $config->min_value;
					}
				}
				
				$min_value = (count($array_min_values)) ? min($array_min_values) : "-";
				$max_value = (count($array_min_values)) ? max($array_min_values) : "-";
			}

			$config_icon_air_forecast_alerts = ($configured) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';

			$array_air_forecast_alerts[] = array(
				"id_module" => $forecast_module->id, // Módulo de Pronóstico
				"id_submodule" => 0,
				"module" => $forecast_module->name,
				"submodule" => "",
				"id_air_station" => $air_sector_variable->id_air_station,
				"id_air_sector" => $air_sector_variable->id_air_sector,
				"id_air_variable" => $air_sector_variable->id_air_variable,
				"name_station" =>$air_sector_variable->name_station,
				"name_sector" => $air_sector_variable->name_sector,
				"name_variable" => $air_sector_variable->name_variable,
				"min_value" => $min_value,
				"max_value" => $max_value,
				"config_icon" => $config_icon_air_forecast_alerts,
				"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_air/forecast_alerts"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $forecast_module->id, "data-post-id_submodulo" => 0, "data-post-id_air_station" => $air_sector_variable->id_air_station, "data-post-id_air_sector" => $air_sector_variable->id_air_sector, "data-post-id_air_variable" => $air_sector_variable->id_air_variable, "data-post-name_sector" => $air_sector_variable->name_sector, "data-post-name_variable" => $air_sector_variable->name_variable, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_alert_config" => $alert_config_air_forecast_alerts->id))
			);


			// Acordeón Plan de Acción
			// Llamar a la configuración de Plan de Acción
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => $forecast_module->id, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "action_plan", // Acordeón Plan de Acción
					"id_air_station" => $air_sector_variable->id_air_station,
					"id_air_sector" => $air_sector_variable->id_air_sector,
					"id_air_variable" => $air_sector_variable->id_air_variable
				),
			);

			$alert_config_air_action_plan = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			$alert_config_action_plan = json_decode($alert_config_air_action_plan->alert_config);
			
			$configured = FALSE;
			//$n_alerts = "-";
			//$n_action_plans = "-";
			$n_alerts = 0;
			$n_action_plans = 0;

			if(count($alert_config_forecast)){
				$n_alerts = count($alert_config_forecast->alert_config);
			}

			if(count($alert_config_action_plan)){
				$alert_config = $alert_config_action_plan->alert_config;
				$n_action_plans = 0;
				foreach($alert_config as $config){
					if($config->ap_active == "1"){
						$n_action_plans++;
					}
				}
				foreach($alert_config as $config){
					if($config->ap_active){
						$configured = TRUE;
						break;
					}
				}
			}

			$config_icon_air_action_plan = ($configured) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';

			$array_air_action_plan[] = array(
				"id_module" => $forecast_module->id, // Módulo de Pronóstico
				"id_submodule" => 0,
				"module" => $forecast_module->name,
				"submodule" => "",
				"id_air_station" => $air_sector_variable->id_air_station,
				"id_air_sector" => $air_sector_variable->id_air_sector,
				"id_air_variable" => $air_sector_variable->id_air_variable,
				"name_station" =>$air_sector_variable->name_station,
				"name_sector" => $air_sector_variable->name_sector,
				"name_variable" => $air_sector_variable->name_variable,
				"n_alerts" => $n_alerts,
				"n_action_plans" => $n_action_plans,
				"config_icon" => $config_icon_air_action_plan,
				"action" => modal_anchor(get_uri("general_settings/modal_form_alert_config_air/action_plan"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $forecast_module->id, "data-post-id_submodulo" => 0, "data-post-id_air_station" => $air_sector_variable->id_air_station, "data-post-id_air_sector" => $air_sector_variable->id_air_sector, "data-post-id_air_variable" => $air_sector_variable->id_air_variable, "data-post-name_sector" => $air_sector_variable->name_sector, "data-post-name_variable" => $air_sector_variable->name_variable, "data-post-id_client" => $id_cliente, "data-post-id_project" => $id_proyecto, "data-post-id_alert_config" => $alert_config_air_action_plan->id))
			);

		}

		$data["array_ra"] = $array_ra;
		$data["array_comp_rca"] = $array_comp_rca;
		$data["array_comp_rep"] = $array_comp_rep;
		$data["array_planificaciones_comp_rep"] = $array_planificaciones_comp_rep;
		$data["array_valores_permisos"] = $array_valores_permisos;
		$data["array_air_forecast_alerts"] = $array_air_forecast_alerts;
		$data["array_air_action_plan"] = $array_air_action_plan;
						
		$this->load->view("general_settings/all_settings", $data);
	}
	
	/* Guardar configuración general*/
	function save_general_settings(){
		
		$id = $this->input->post("id_general_setting");

		$data_general_settings = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"id_proyecto" => $this->input->post('id_proyecto'),
			"thousands_separator" => $this->input->post('thousands_separator'),
			"decimals_separator" => $this->input->post('decimals_separator'),
			"decimal_numbers" => $this->input->post('decimal_numbers_config'),
			"date_format" => $this->input->post('date_format'),
			"timezone" => $this->input->post('timezone'),
			"time_format" => $this->input->post("time_format"),
			//"language" => $this->input->post("language"),
			//"general_color" => $this->input->post("general_color"),
		);
		
		
		if($id){
			$save_id = $this->General_settings_model->save($data_general_settings, $id);
		} else {
			
			$save_id = $this->General_settings_model->save($data_general_settings);
		}	

		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	/* Guardar configuración de reportes */
	function save_reports_config_settings(){
		
		$id = $this->input->post("id_report_config");
		$materials = $this->input->post("materials");

		$project_data = ($this->input->post('project_data')) ? 1 : 0;
		$rca_compromises = ($this->input->post('rca_compromises')) ? 1 : 0;
		$reportable_compromises = ($this->input->post('reportable_compromises')) ? 1 : 0;
		$ambiental_events = ($this->input->post('ambiental_events')) ? 1 : 0;
		$consumptions = ($this->input->post('consumptions')) ? 1 : 0;
		$waste = ($this->input->post('waste')) ? 1 : 0;
		$ambiental_education = ($this->input->post('ambiental_education')) ? 1 : 0;
		$project_modifications = ($this->input->post('project_modifications')) ? 1 : 0;
		$permittings = ($this->input->post('permittings')) ? 1 : 0;
		$relevant_topics = ($this->input->post('relevant_topics')) ? 1 : 0;
		$compromises = ($this->input->post('compromises')) ? 1 : 0;

		$data_reports_config = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"id_proyecto" => $this->input->post('id_proyecto'),
			"project_data" => $project_data,
			"rca_compromises" => $rca_compromises,
			"reportable_compromises" => $reportable_compromises,
			"ambiental_events" => $ambiental_events,
			"consumptions" => $consumptions,
			"waste" => $waste,
			"ambiental_education" => $ambiental_education,
			"project_modifications" => $project_modifications,
			"permittings" => $permittings,
			"relevant_topics" => $relevant_topics,
			"compromises" => $compromises,
		);

		$json_materials = array();
		
		$report_config = $this->Reports_configuration_model->get_one($id);
		$array_report_config_materials = json_decode($report_config->materials, true);

		foreach($array_report_config_materials as $report_config_material){
			if(in_array($report_config_material["id"], $materials)){
				$json_materials[] = array("id" => $report_config_material["id"], "estado" => 1);
			} else {
				$json_materials[] = array("id" => $report_config_material["id"], "estado" => 0);
			}
		}
		
		$data_reports_config["materials"] = json_encode($json_materials);
		
		if($id){
			$save_id = $this->Reports_configuration_model->save($data_reports_config, $id);	
		} else {
			$save_id = $this->Reports_configuration_model->save($data_reports_config);	
		}
		
		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	/* Guardar disponibilidad de módulos */
	function save_module_availability_settings(){
			
		$clients_modules_availability = $this->input->post("clients_modules_availability"); //checkbox chequeados (1 ó 0)
		$clients_modules_availability_thresholds = $this->input->post("clients_modules_availability_thresholds"); //checkbox chequeados (1 ó 0)
		
		foreach($clients_modules_availability as $index => $cma){ // $index = id del módulo, $cma = disp. (1), no disp. (0)

			$id_cliente = $this->input->post('id_cliente');
			$id_proyecto = $this->input->post('id_proyecto');
			
			$data_module_availability = array(
				"id_modulo_cliente" => $index,
				"available" => $cma
			);

			$registro = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => $index, "deleted" => 0));

			if($registro->id){
				$save_id = $this->Module_availability_model->save($data_module_availability, $registro->id);
			} else {
				$data_module_availability["id_cliente"] = $id_cliente;
				$data_module_availability["id_proyecto"] = $id_proyecto;
				$save_id = $this->Module_availability_model->save($data_module_availability);
			}
			
		}
		
		
		foreach($clients_modules_availability_thresholds as $index => $cma_thresholds){ // $index = id del módulo, $cma_thresholds = disp. (1), no disp. (0)

			$id_cliente = $this->input->post('id_cliente');
			$id_proyecto = $this->input->post('id_proyecto');
			
			$data_module_thresholds = array(
				"id_modulo_cliente" => $index,
				"thresholds" => $cma_thresholds
			);

			$registro = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => $index, "deleted" => 0));

			if($registro->id){
				$save_id_thresholds = $this->Module_availability_model->save($data_module_thresholds, $registro->id);
			} else {
				$data_module_thresholds["id_cliente"] = $id_cliente;
				$data_module_thresholds["id_proyecto"] = $id_proyecto;
				$save_id = $this->Module_availability_model->save($data_module_thresholds);
			}
			
		}
		
		if($save_id){
			echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}


	/* Guardar unidades de huellas*/
	function save_unity_settings(){
		
		//$id = $this->input->post("id_module_footprints_units");
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		$unidades = $this->input->post("unidad");
		
		$data_module_footprints_units = array();

		$delete_materials_rel_project = $this->Module_footprint_units_model->delete_footprints($id_proyecto,$id_cliente);
		
			foreach($unidades as $key => $id_unidad){
				
				$id_unidad = (int)$id_unidad;
				$data_module_footprints_units["id_proyecto"] = $id_proyecto;
				$data_module_footprints_units["id_cliente"] = $id_cliente;
				$data_module_footprints_units["id_tipo_unidad"] = $key;
				$data_module_footprints_units["id_unidad"] = $id_unidad;
				$save_id = $this->Module_footprint_units_model->save($data_module_footprints_units);
			}

		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	function save_report_units_settings(){
		
		$id_cliente = $this->input->post("id_cliente_report_units_settings");
		$id_proyecto = $this->input->post("id_proyecto_report_units_settings");
		$unidades = $this->input->post("unidad_report_units_settings");
		
		$data_report_units = array();
		$delete_settings = $this->Reports_units_settings_model->delete_reports_units_settings($id_proyecto, $id_cliente);
		
		foreach($unidades as $key => $id_unidad){
			$id_unidad = (int)$id_unidad;
			$data_report_units["id_proyecto"] = $id_proyecto;
			$data_report_units["id_cliente"] = $id_cliente;
			$data_report_units["id_tipo_unidad"] = $key;
			$data_report_units["id_unidad"] = $id_unidad;
			$save_id = $this->Reports_units_settings_model->save($data_report_units);
		}
		
		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	function get_client_settings(){
		
		$id_cliente = $this->input->post("id_client");
		
		// Pestaña General
		$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $view_data['timezone_dropdown'] = array();
        foreach ($tzlist as $zone) {
            $view_data['timezone_dropdown'][$zone] = $zone;
        }
		
		$view_data["general_settings"] = $this->General_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "deleted" => 0));

		
		// Pestaña Disponibilidad de Módulos
		$view_data["client_module_availability_settings"] = $this->Client_module_availability_model->get_client_setting($id_cliente)->result();
		
		// Pestaña Configuración de Notificación
		$client_modules = $this->Client_context_modules_model->get_client_context_modules_for_notification_config()->result_array();
		
		$array_acuerdos_territorio = array();
		$array_acuerdos_distribucion = array();
		$array_ayuda_y_soporte = array();
		
		foreach($client_modules as $module){
			
			if($module["contexto"] == "help_and_support"){
				
				$id_submodulo = ($module["id_submodulo"]) ? $module["id_submodulo"] : 0;
				
				$notif_config_send_email = $this->AYN_Notif_general_model->get_one_where(array(
					"id_client" => $id_cliente,
					"id_client_context_module" => $module["id_modulo"],
					"id_client_context_submodule" => $id_submodulo,
					"event" => "send_email",
					"deleted" => 0
				));
				$config_icon_send_email = ($notif_config_send_email->email_notification || $notif_config_send_email->web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
				
				if($module["id_modulo"] == "1"){ // Ayuda y Soporte

					$array_ayuda_y_soporte[] = array(
						"id_module" => $module["id_modulo"],
						"id_submodule" => $id_submodulo,
						"module" => $module["nombre_modulo"],
						"submodule" => $module["nombre_submodulo"],
						"item" => "help_and_support_contact"
					);
										
					$view_data["events_help_and_support_contact_btn"] = modal_anchor(get_uri("general_settings/modal_form_notification_config_client"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $module["id_modulo"], "data-post-id_submodulo" => $id_submodulo ,"data-post-item" => "help_and_support_contact", "data-post-id_client" => $id_cliente, "data-post-id_notif_config_send_email" => $notif_config_send_email->id));
					
					$view_data["events_help_and_support_contact_icons"] = array(
						"send_email" => $config_icon_send_email,
					);
					
				}
				
			}
			
		}
		
		//$view_data["array_acuerdos_territorio"] = $array_acuerdos_territorio;
		//$view_data["array_acuerdos_distribucion"] = $array_acuerdos_distribucion;
		$view_data["array_help_and_support"] = $array_ayuda_y_soporte;
		
		
		// Pestaña Factores de transformación
		$categorias_proyectos = $this->Categories_model->get_categories_of_materials_client_projects($id_cliente)->result();
		
		$array_categorias_proyectos = array();
		
		foreach($categorias_proyectos as $categoria){
			
			$config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $categoria->id_categoria,
				"id_tipo_unidad" => $categoria->id_tipo_unidad,
				"deleted" => 0
			));
			
			$tipo_unidad = $this->Unity_type_model->get_one($categoria->id_tipo_unidad);
			
			$array_categorias_proyectos[] = array(
				"nombre_categoria" => $categoria->nombre_categoria,
				"id_categoria" => $categoria->id_categoria,
				"id_tipo_unidad" => ($config->id_tipo_unidad) ? $config->id_tipo_unidad : $categoria->id_tipo_unidad,
				"nombre_tipo_unidad" => $tipo_unidad->nombre,
				"valor_factor_transformacion" => ($config->valor_factor_transformacion) ? $config->valor_factor_transformacion : "",
				"ren" => ($config->ren) ? $config->ren : "",
				"eficiencia" => ($config->eficiencia) ? $config->eficiencia : ""
			);
	
		}

		$view_data["categorias_proyectos"] = $array_categorias_proyectos;
		
		
		// Pestaña Unidades de Reporte
		//Trae la data de los tipos de unidades de las huellas utilizadas por proyecto
		//$view_data["tipos_de_unidad"] = $this->Unity_type_model->get_all_where(array("deleted" => 0))->result();
		$options_unity_types = array("unity_types" => array(1,2,3,4,5,6,7,8,9));
		$options_air_unity_types = array("unity_types" => array(10,11,12,13,14,15,16));
		$view_data["tipos_de_unidad"] = $this->Unity_type_model->get_details($options_unity_types)->result();
		$view_data["tipos_de_unidad_air"] = $this->Unity_type_model->get_details($options_air_unity_types)->result();


		$view_data["reports_units_settings_clients"] =  $this->Reports_units_settings_clients_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result_array();
		$view_data["id_cliente"] = $id_cliente;
		
		//echo $this->load->view("general_settings/client_settings", $view_data);
		$this->load->view("general_settings/client_settings", $view_data);

	}
	
	function save_client_module_availability_settings(){
		
		$id_cliente = $this->input->post("id_cliente");
		$clients_modules_availability = $this->input->post("clients_modules_availability");
		
		foreach($clients_modules_availability as $id_modulo => $disponibilidad){

			$data_module_availability = array(
				"id_modulo" => $id_modulo,
				"disponible" => $disponibilidad
			);
			
			$registro = $this->Client_module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_modulo" => $id_modulo, "deleted" => 0));
			
			if($registro->id){
				$save_id = $this->Client_module_availability_model->save($data_module_availability, $registro->id);
			} else {
				$data_module_availability["id_cliente"] = $id_cliente;
				$save_id = $this->Client_module_availability_model->save($data_module_availability);
			}
			
		}
		
		if($save_id){
			echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	function save_transformation_factors(){
				
		$id_cliente = $this->input->post("id_cliente");
		$valores_factor_transformacion = (array)$this->input->post("valor_factor_transformacion");
		$rems = (array)$this->input->post("ren");
		$eficiencias = (array)$this->input->post("eficiencia");
		
		$categorias_proyectos = $this->Categories_model->get_categories_of_materials_client_projects($id_cliente)->result();
		
		foreach($categorias_proyectos as $categoria){
			
			$data_transformation_factors = array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $categoria->id_categoria,
				"id_tipo_unidad" => $categoria->id_tipo_unidad
			);
			
			if($valores_factor_transformacion[$categoria->id_categoria][$categoria->id_tipo_unidad]){
				$data_transformation_factors["valor_factor_transformacion"] = $valores_factor_transformacion[$categoria->id_categoria][$categoria->id_tipo_unidad];
			} else {
				$data_transformation_factors["valor_factor_transformacion"] = NULL;
			}
			
			if($rems[$categoria->id_categoria][$categoria->id_tipo_unidad]){
				$data_transformation_factors["ren"] = $rems[$categoria->id_categoria][$categoria->id_tipo_unidad];
			} else {
				$data_transformation_factors["ren"] = NULL;
			}
			
			if($eficiencias[$categoria->id_categoria][$categoria->id_tipo_unidad]){
				$data_transformation_factors["eficiencia"] = $eficiencias[$categoria->id_categoria][$categoria->id_tipo_unidad];
			} else {
				$data_transformation_factors["eficiencia"] = NULL;
			}
			
			$config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
				"id_cliente" => $id_cliente,
				"id_categoria" => $categoria->id_categoria,
				"id_tipo_unidad" => $categoria->id_tipo_unidad,
				"deleted" => 0
			));
						
			if($config->id){ 
				$data_transformation_factors["modified"] = get_current_utc_time();
				$data_transformation_factors["modified_by"] = $this->login_user->id;
				$save_id = $this->EC_Client_transformation_factors_config_model->save($data_transformation_factors, $config->id);
			} else {
				$data_transformation_factors["created"] = get_current_utc_time();
				$data_transformation_factors["created_by"] = $this->login_user->id;
				$save_id = $this->EC_Client_transformation_factors_config_model->save($data_transformation_factors);
			}

		}
		
		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
		
	function modal_form_notification_config_client(){
		
        $id_client = $this->input->post('id_client');
		$id_modulo = $this->input->post('id_modulo');
		$id_submodulo = $this->input->post('id_submodulo');
		$item = $this->input->post('item');
		
		// ids de configuraciones
		$id_notif_config_add = $this->input->post('id_notif_config_add');
		$id_notif_config_edit = $this->input->post('id_notif_config_edit');
		$id_notif_config_delete = $this->input->post('id_notif_config_delete');
		$id_notif_config_audit = $this->input->post('id_notif_config_audit');
		$id_notif_config_close = $this->input->post('id_notif_config_close');
		$id_notif_config_send_email = $this->input->post('id_notif_config_send_email');
				
		$view_data["id_notif_config_add"] = $id_notif_config_add;
		$view_data["id_notif_config_edit"] = $id_notif_config_edit;
		$view_data["id_notif_config_delete"] = $id_notif_config_delete;
		$view_data["id_notif_config_audit"] = $id_notif_config_audit;
		$view_data["id_notif_config_close"] = $id_notif_config_close;
		$view_data["id_notif_config_send_email"] = $id_notif_config_send_email;
		
		$view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["id_client"] = $id_client;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;
		$view_data["item"] = $item;
		
		// Grupos de Cliente
		$array_client_groups = array();
		$client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
			"id_client" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_groups as $client_group){
			$array_client_groups[$client_group->id] = $client_group->group_name;
		}
		
		// Usuarios de Cliente
		$array_client_users = array();
		$client_users = $this->Users_model->get_all_where(array(
			"client_id" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_users as $client_user){
			$array_client_users[$client_user->id] = $client_user->first_name." ".$client_user->last_name;
		}
		
		$view_data["array_client_groups"] = $array_client_groups;
		$view_data["array_client_users"] = $array_client_users;
		
		$modulo = "-";
		$submodulo = "-";
		
		if($id_notif_config_add){
			
			$notif_config_add = $this->AYN_Notif_general_model->get_one($id_notif_config_add);
			
			$notif_config_groups_add = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_groups_add = array();
			foreach($notif_config_groups_add as $notif_config_group_add){
				$selected_client_groups_add[] = $notif_config_group_add->id_client_group;
			}
			
			$notif_config_users_add = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_users_add = array();
			foreach($notif_config_users_add as $notif_config_user_add){
				$selected_client_users_add[] = $notif_config_user_add->id_user;
			}
			
		}
		
		if($id_notif_config_edit){
			
			$notif_config_edit = $this->AYN_Notif_general_model->get_one($id_notif_config_edit);
			
			$notif_config_groups_edit = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_groups_edit = array();
			foreach($notif_config_groups_edit as $notif_config_group_edit){
				$selected_client_groups_edit[] = $notif_config_group_edit->id_client_group;
			}
			
			$notif_config_users_edit = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_users_edit = array();
			foreach($notif_config_users_edit as $notif_config_user_edit){
				$selected_client_users_edit[] = $notif_config_user_edit->id_user;
			}
			
		} 
		
		if($id_notif_config_delete){
			
			$notif_config_delete = $this->AYN_Notif_general_model->get_one($id_notif_config_delete);
			
			$notif_config_groups_delete = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_groups_delete = array();
			foreach($notif_config_groups_delete as $notif_config_group_delete){
				$selected_client_groups_delete[] = $notif_config_group_delete->id_client_group;
			}
			
			$notif_config_users_delete = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_users_delete = array();
			foreach($notif_config_users_delete as $notif_config_user_delete){
				$selected_client_users_delete[] = $notif_config_user_delete->id_user;
			}
			
		}
		
		if($id_notif_config_audit){
			
			$notif_config_audit = $this->AYN_Notif_general_model->get_one($id_notif_config_audit);
			
			$notif_config_groups_audit = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_audit,
				"deleted" => 0
			))->result();
			$selected_client_groups_audit = array();
			foreach($notif_config_groups_audit as $notif_config_group_audit){
				$selected_client_groups_audit[] = $notif_config_group_audit->id_client_group;
			}
			
			$notif_config_users_audit = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_audit,
				"deleted" => 0
			))->result();
			$selected_client_users_audit = array();
			foreach($notif_config_users_audit as $notif_config_user_audit){
				$selected_client_users_audit[] = $notif_config_user_audit->id_user;
			}
			
		} 
		
		if($id_notif_config_close){
			
			$notif_config_close = $this->AYN_Notif_general_model->get_one($id_notif_config_close);
			
			$notif_config_groups_close = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_close,
				"deleted" => 0
			))->result();
			$selected_client_groups_close = array();
			foreach($notif_config_groups_close as $notif_config_group_close){
				$selected_client_groups_close[] = $notif_config_group_close->id_client_group;
			}
			
			$notif_config_users_close = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_close,
				"deleted" => 0
			))->result();
			$selected_client_users_close = array();
			foreach($notif_config_users_close as $notif_config_user_close){
				$selected_client_users_close[] = $notif_config_user_close->id_user;
			}
			
		} 
		
		if($id_notif_config_send_email){
			
			$notif_config_send_email = $this->AYN_Notif_general_model->get_one($id_notif_config_send_email);
			
			$notif_config_groups_send_email = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_send_email,
				"deleted" => 0
			))->result();
			$selected_client_groups_send_email = array();
			foreach($notif_config_groups_send_email as $notif_config_group_send_email){
				$selected_client_groups_send_email[] = $notif_config_group_send_email->id_client_group;
			}
			
			$notif_config_users_send_email = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config_send_email,
				"deleted" => 0
			))->result();
			$selected_client_users_send_email = array();
			foreach($notif_config_users_send_email as $notif_config_user_send_email){
				$selected_client_users_send_email[] = $notif_config_user_send_email->id_user;
			}
			
		} 
		
		$eventos = array(
			"add" => array(
				"selected_client_groups" => $selected_client_groups_add,
				"selected_client_users" => $selected_client_users_add,
				"is_email_notification" => $notif_config_add->email_notification,
				"is_web_notification" => $notif_config_add->web_notification
			),
			"edit" => array(
				"selected_client_groups" => $selected_client_groups_edit,
				"selected_client_users" => $selected_client_users_edit,
				"is_email_notification" => $notif_config_edit->email_notification,
				"is_web_notification" => $notif_config_edit->web_notification
			),
			"delete" => array(
				"selected_client_groups" => $selected_client_groups_delete,
				"selected_client_users" => $selected_client_users_delete,
				"is_email_notification" => $notif_config_delete->email_notification,
				"is_web_notification" => $notif_config_delete->web_notification
			),
			"to_audit" => array(
				"selected_client_groups" => $selected_client_groups_audit,
				"selected_client_users" => $selected_client_users_audit,
				"is_email_notification" => $notif_config_audit->email_notification,
				"is_web_notification" => $notif_config_audit->web_notification
			),
			"close" => array(
				"selected_client_groups" => $selected_client_groups_close,
				"selected_client_users" => $selected_client_users_close,
				"is_email_notification" => $notif_config_close->email_notification,
				"is_web_notification" => $notif_config_close->web_notification
			),
			"send_email" => array(
				"selected_client_groups" => $selected_client_groups_send_email,
				"selected_client_users" => $selected_client_users_send_email,
				"is_email_notification" => $notif_config_send_email->email_notification,
				"is_web_notification" => $notif_config_send_email->web_notification
			)
		);

		/*if($item == "ac_t_beneficiarios" || $item == "ac_t_activities" || $item == "ac_d_beneficiarios" || $item == "ac_d_activities"){
			$modulo = ($item == "ac_t_beneficiarios" || $item == "ac_t_activities") ? lang("agreements")." ".lang("territory") : lang("agreements")." ".lang("distribution");
			$submodulo = $this->Client_context_modules_model->get_one($id_modulo)->name;
			unset($eventos["to_audit"]);
			unset($eventos["close"]);
			unset($eventos["send_email"]);
		}
		
		if($item == "ac_t_information" || $item == "ac_d_information"){
			$modulo = ($item == "ac_t_information") ? lang("agreements")." ".lang("territory") : lang("agreements")." ".lang("distribution");
			$submodulo = $this->Client_context_modules_model->get_one($id_modulo)->name." | ".lang("agreements_record")." | ".lang("information");
			unset($eventos["close"]);
			unset($eventos["send_email"]);
		}
		
		if($item == "ac_t_configuration" || $item == "ac_d_configuration"){
			$modulo = ($item == "ac_t_configuration") ? lang("agreements")." ".lang("territory") : lang("agreements")." ".lang("distribution");
			$submodulo = $this->Client_context_modules_model->get_one($id_modulo)->name." | ".lang("agreements_record")." | ".lang("configuration");
			unset($eventos["to_audit"]);
			unset($eventos["send_email"]);
		}
		
		if($item == "ac_t_execution_record" || $item == "ac_d_execution_record"){
			$modulo = ($item == "ac_t_execution_record") ? lang("agreements")." ".lang("territory") : lang("agreements")." ".lang("distribution");
			$submodulo = $this->Client_context_modules_model->get_one($id_modulo)->name." | ".lang("agreements_record")." | ".lang("execution_record");
			unset($eventos["to_audit"]);
			unset($eventos["close"]);
			unset($eventos["send_email"]);
		}
		
		if($item == "ac_t_payment_record" || $item == "ac_d_payment_record"){
			$modulo = ($item == "ac_t_payment_record") ? lang("agreements")." ".lang("territory") : lang("agreements")." ".lang("distribution");
			$submodulo = $this->Client_context_modules_model->get_one($id_modulo)->name." | ".lang("agreements_record")." | ".lang("payment_record");
			unset($eventos["add"]);
			unset($eventos["delete"]);
			unset($eventos["to_audit"]);
			unset($eventos["close"]);
			unset($eventos["send_email"]);
		}
		
		if($item == "ac_t_feeder_society" || $item == "ac_t_feeder_central" || $item == "ac_t_feeder_type_of_agreement"
			|| $item == "ac_d_feeder_society" || $item == "ac_d_feeder_central" || $item == "ac_d_feeder_type_of_agreement"){

			if($item == "ac_t_feeder_society"){
				$modulo = lang("agreements")." ".lang("territory");
				$submodulo = lang("feeders")." | ".lang("societies");
			}
			if($item == "ac_t_feeder_central"){
				$modulo = lang("agreements")." ".lang("territory");
				$submodulo = lang("feeders")." | ".lang("centrals");
			}
			if($item == "ac_t_feeder_type_of_agreement"){
				$modulo = lang("agreements")." ".lang("territory");
				$submodulo = lang("feeders")." | ".lang("types_of_agreement");
			}
			if($item == "ac_d_feeder_society"){
				$modulo = lang("agreements")." ".lang("distribution");
				$submodulo = lang("feeders")." | ".lang("societies");
			}
			if($item == "ac_d_feeder_central"){
				$modulo = lang("agreements")." ".lang("distribution");
				$submodulo = lang("feeders")." | ".lang("centrals");
			}
			if($item == "ac_d_feeder_type_of_agreement"){
				$modulo = lang("agreements")." ".lang("distribution");
				$submodulo = lang("feeders")." | ".lang("types_of_agreement");
			}
			
			unset($eventos["to_audit"]);
			unset($eventos["close"]);
			unset($eventos["send_email"]);
		}*/
		
		if($item == "help_and_support_contact"){
			
			$modulo = $this->Client_context_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Client_context_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["add"]);
			unset($eventos["edit"]);
			unset($eventos["delete"]);
			unset($eventos["to_audit"]);
			unset($eventos["close"]);
			
		}
		
		$view_data["modulo"] = $modulo;
		$view_data["submodulo"] = $submodulo;
		$view_data["eventos"] = $eventos;
		
        $this->load->view('general_settings/client/notification_settings/modal_form', $view_data);
		
	}
	
	function get_users_of_groups(){
		
		$id_client = $this->input->post("id_client");
		$groups = $this->input->post("groups");
		$evento = $this->input->post("evento");
		
		// Usuarios de Cliente
		$array_client_users = array();
		$client_users = $this->Users_model->get_all_where(array(
			"client_id" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_users as $client_user){
			$array_client_users[$client_user->id] = $client_user->first_name." ".$client_user->last_name;
		}
		
		// Usuarios de los grupos seleccionados
		$array_selected_users = array();
		foreach($groups as $id_group){
			
			$users_of_group = $this->Users_model->get_all_where(array(
				"id_client_group" => $id_group,
				"deleted" => 0
			))->result();
			
			foreach($users_of_group as $user){
				$array_selected_users[] = $user->id;
			}
			
		}
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= 	'<label for="users" class="col-md-3">'.lang('users').'</label>';
		$html .= 	'<div class="col-md-9">';
		$html .= 		form_multiselect("users_".$evento."[]", $array_client_users, $array_selected_users, "id='users_".$evento."' class='select2 multiple' multiple='multiple'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
	}
		
	function save_notification_config_client(){
		
		$id_notif_config_add = $this->input->post("id_notif_config_add");
		$id_notif_config_edit = $this->input->post("id_notif_config_edit");
		$id_notif_config_delete = $this->input->post("id_notif_config_delete");
		$id_notif_config_audit = $this->input->post("id_notif_config_audit");
		$id_notif_config_close = $this->input->post("id_notif_config_close");
		$id_notif_config_send_email = $this->input->post("id_notif_config_send_email");
		
		$id_client = $this->input->post("id_client");
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		$item = $this->input->post("item");
		
		$groups_add = $this->input->post("groups_add");
		$users_add = $this->input->post("users_add");
		$notification_email_add = ($this->input->post("notification_email_add")) ? 1 : 0;
		$notification_web_add = ($this->input->post("notification_web_add")) ? 1 : 0;
		
		$groups_edit = $this->input->post("groups_edit");
		$users_edit = $this->input->post("users_edit");
		$notification_email_edit = ($this->input->post("notification_email_edit")) ? 1 : 0;
		$notification_web_edit = ($this->input->post("notification_web_edit")) ? 1 : 0;
		
		$groups_delete = $this->input->post("groups_delete");
		$users_delete = $this->input->post("users_delete");
		$notification_email_delete = ($this->input->post("notification_email_delete")) ? 1 : 0;
		$notification_web_delete = ($this->input->post("notification_web_delete")) ? 1 : 0;
		
		$groups_to_audit = $this->input->post("groups_to_audit");
		$users_to_audit = $this->input->post("users_to_audit");
		$notification_email_to_audit = ($this->input->post("notification_email_to_audit")) ? 1 : 0;
		$notification_web_to_audit = ($this->input->post("notification_web_to_audit")) ? 1 : 0;
		
		$groups_close = $this->input->post("groups_close");
		$users_close = $this->input->post("users_close");
		$notification_email_close = ($this->input->post("notification_email_close")) ? 1 : 0;
		$notification_web_close = ($this->input->post("notification_web_close")) ? 1 : 0;
		
		$groups_send_email = $this->input->post("groups_send_email");
		$users_send_email = $this->input->post("users_send_email");
		$notification_email_send_email = ($this->input->post("notification_email_send_email")) ? 1 : 0;
		$notification_web_send_email = ($this->input->post("notification_web_send_email")) ? 1 : 0;
			
		$data_notif_config_client = array(
			"id_client" => $id_client,
			"id_client_context_module" => $id_modulo,
			"id_client_context_submodule" => $id_submodulo,
		);
		
		/*// ADD
		$event_add = "";
		if($item == "ac_t_beneficiarios" || $item == "ac_t_activities" || $item == "ac_d_beneficiarios" || $item == "ac_d_activities"){
			$event_add = "add";
		} elseif($item == "ac_t_information" || $item == "ac_d_information"){
			$event_add = "information_add";
		} elseif($item == "ac_t_configuration" || $item == "ac_d_configuration"){
			$event_add = "configuration_add";
		} elseif($item == "ac_t_execution_record" || $item == "ac_d_execution_record"){
			$event_add = "execution_record_add";
		} elseif($item == "ac_t_feeder_society" || $item == "ac_d_feeder_society"){
			$event_add = "society_add";
		} elseif($item == "ac_t_feeder_central" || $item == "ac_d_feeder_central"){
			$event_add = "central_add";
		} elseif($item == "ac_t_feeder_type_of_agreement" || $item == "ac_d_feeder_type_of_agreement"){
			$event_add = "type_of_agreement_add";
		}
		
		if($event_add){
			
			$data_notif_config_client["event"] = $event_add;
			$data_notif_config_client["email_notification"] = $notification_email_add;
			$data_notif_config_client["web_notification"] = $notification_web_add;
	
			if($id_notif_config_add){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_add);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_add = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_add, $groups_add, $users_add);
			
		}
		
		// EDIT
		$event_edit = "";
		if($item == "ac_t_beneficiarios" || $item == "ac_t_activities" || $item == "ac_d_beneficiarios" || $item == "ac_d_activities"){
			$event_edit = "edit";
		} elseif($item == "ac_t_information" || $item == "ac_d_information"){
			$event_edit = "information_edit";
		} elseif($item == "ac_t_configuration" || $item == "ac_d_configuration"){
			$event_edit = "configuration_edit";
		} elseif($item == "ac_t_execution_record" || $item == "ac_d_execution_record"){
			$event_edit = "execution_record_edit";
		} elseif($item == "ac_t_payment_record" || $item == "ac_d_payment_record"){
			$event_edit = "payment_record_edit";
		} elseif($item == "ac_t_feeder_society" || $item == "ac_d_feeder_society"){
			$event_edit = "society_edit";
		} elseif($item == "ac_t_feeder_central" || $item == "ac_d_feeder_central"){
			$event_edit = "central_edit";
		} elseif($item == "ac_t_feeder_type_of_agreement" || $item == "ac_d_feeder_type_of_agreement"){
			$event_edit = "type_of_agreement_edit";
		}
		
		if($event_edit){
			
			$data_notif_config_client["event"] = $event_edit;
			$data_notif_config_client["email_notification"] = $notification_email_edit;
			$data_notif_config_client["web_notification"] = $notification_web_edit;
			
			if($id_notif_config_edit){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_edit);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_edit = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_edit, $groups_edit, $users_edit);
			
		}
		
		// DELETE
		$event_delete = "";
		if($item == "ac_t_beneficiarios" || $item == "ac_t_activities" || $item == "ac_d_beneficiarios" || $item == "ac_d_activities"){
			$event_delete = "delete";
		} elseif($item == "ac_t_information" || $item == "ac_d_information"){
			$event_delete = "information_delete";
		} elseif($item == "ac_t_configuration" || $item == "ac_d_configuration"){
			$event_delete = "configuration_delete";
		} elseif($item == "ac_t_execution_record" || $item == "ac_d_execution_record"){
			$event_delete = "execution_record_delete";
		} elseif($item == "ac_t_feeder_society" || $item == "ac_d_feeder_society"){
			$event_delete = "society_delete";
		} elseif($item == "ac_t_feeder_central" || $item == "ac_d_feeder_central"){
			$event_delete = "central_delete";
		} elseif($item == "ac_t_feeder_type_of_agreement" || $item == "ac_d_feeder_type_of_agreement"){
			$event_delete = "type_of_agreement_delete";
		}
		
		if($event_delete){
			
			$data_notif_config_client["event"] = $event_delete;
			$data_notif_config_client["email_notification"] = $notification_email_delete;
			$data_notif_config_client["web_notification"] = $notification_web_delete;
	
			if($id_notif_config_delete){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_delete);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_delete = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_delete, $groups_delete, $users_delete);
			
		}
		
		// AUDIT
		$event_audit = "";
		if($item == "ac_t_information" || $item == "ac_d_information"){
			$event_audit = "information_audit";
		}
		
		if($event_audit){
			
			$data_notif_config_client["event"] = $event_audit;
			$data_notif_config_client["email_notification"] = $notification_email_to_audit;
			$data_notif_config_client["web_notification"] = $notification_web_to_audit;
	
			if($id_notif_config_audit){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_audit);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_audit = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_audit, $groups_to_audit, $users_to_audit);
		
		}
		
		// CLOSE
		$event_close = "";
		if($item == "ac_t_configuration" || $item == "ac_d_configuration"){
			$event_close = "configuration_close";
		}
		
		if($event_close){
			
			$data_notif_config_client["event"] = $event_close;
			$data_notif_config_client["email_notification"] = $notification_email_close;
			$data_notif_config_client["web_notification"] = $notification_web_close;
	
			if($id_notif_config_close){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_close);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_close = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_close, $groups_close, $users_close);
		
		}*/
		
		// SEND EMAIL
		$event_send_email = "";
		if($item == "help_and_support_contact"){
			$event_send_email = "send_email";
		}
		
		if($event_send_email){
			
			$data_notif_config_client["event"] = $event_send_email;
			$data_notif_config_client["email_notification"] = $notification_email_send_email;
			$data_notif_config_client["web_notification"] = $notification_web_send_email;
	
			if($id_notif_config_send_email){
				$data_notif_config_client["modified_by"] = $this->login_user->id;
				$data_notif_config_client["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client, $id_notif_config_send_email);
			} else {
				$data_notif_config_client["created_by"] = $this->login_user->id;
				$data_notif_config_client["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_general_model->save($data_notif_config_client);
			}
			
			$save_id_notif_config_send_email = $save_id;
			$this->_save_notification_config_client_groups_and_users($save_id, $id_notif_config_send_email, $groups_send_email, $users_send_email);
		
		}
		
		$config_icon_add = ($notification_email_add || $notification_web_add) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_edit = ($notification_email_edit || $notification_web_edit) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_delete = ($notification_email_delete || $notification_web_delete) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_audit = ($notification_email_to_audit || $notification_web_to_audit) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_close = ($notification_email_close || $notification_web_close) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_send_email = ($notification_email_send_email || $notification_web_send_email) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		
		$btn_action_attributes = array(
			"class" => "edit", 
			"title" => lang('notification_settings'), 
			"data-post-id_modulo" => $id_modulo, 
			"data-post-id_submodulo" => $id_submodulo, 
			"data-post-item" => $item, 
			"data-post-id_client" => $id_client, 
			"data-post-id_notif_config_add" => $save_id_notif_config_add, 
			"data-post-id_notif_config_edit" => $save_id_notif_config_edit, 
			"data-post-id_notif_config_delete" => $save_id_notif_config_delete, 
			"data-post-id_notif_config_audit" => $save_id_notif_config_audit,
			"data-post-id_notif_config_close" => $save_id_notif_config_close,
			"data-post-id_notif_config_send_email" => $save_id_notif_config_send_email,
		);
		$btn_action = modal_anchor(get_uri("general_settings/modal_form_notification_config_client"), "<i class='fa fa-pencil'></i>", $btn_action_attributes);
	
        if ($save_id) {
			echo json_encode(array(
				"success" => true, 
				//'id_item_config' => $id_item_config, 
				'item' => $item,  // ac_t_beneficiarios por ejemplo
				'config_icon_add' => $config_icon_add,
				'config_icon_edit' => $config_icon_edit, 
				'config_icon_delete' => $config_icon_delete,
				'config_icon_audit' => $config_icon_audit,
				'config_icon_close' => $config_icon_close,
				'config_icon_send_email' => $config_icon_send_email,
				'btn_action' => $btn_action, 
				'message' => lang('record_saved')
			));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	/*
		@$save_id: 			Viene siempre, es el id que retorna el save de la configuración.
		@$id_notif_config: 	Viene siempre y cuando exista configuración antes de guardarla.
		@$groups: 			Grupos seleccionados en la configuración.
		@$users: 			Usuarios seleccionados en la configuración.
	*/
	private function _save_notification_config_client_groups_and_users($save_id, $id_notif_config = NULL, $groups = NULL, $users = NULL){
		
		if($id_notif_config){ // Si existe configuración
			
			// Grupos
			$notif_config_groups = $this->AYN_Notif_general_groups_model->get_all_where(array(
				"id_notif_general" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_groups_ids = array();
			foreach($notif_config_groups as $notif_config_group){
				$array_notif_config_groups_ids[$notif_config_group["id"]] = $notif_config_group["id_client_group"];
			}
			
			if(count($array_notif_config_groups_ids)){ // Si hay grupos seteados
				if(count($groups)){ // Si vienen grupos
					foreach($groups as $id_group){
						if(!in_array($id_group, $array_notif_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
							$data_notif_config_client_groups = array(
								"id_notif_general" => $id_notif_config,
								"id_client_group" => $id_group
							);
							$data_notif_config_client_groups["created_by"] = $this->login_user->id;
							$data_notif_config_client_groups["created"] = get_current_utc_time();
							$save_group_id = $this->AYN_Notif_general_groups_model->save($data_notif_config_client_groups);
						}
					}
					foreach($array_notif_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
						if(!in_array($id_group, $groups)){
							$this->AYN_Notif_general_groups_model->delete($id);
						}
					}
				} else { // Si no vienen grupos, eliminar los seteados
					foreach($notif_config_groups as $notif_config_group){
						$this->AYN_Notif_general_groups_model->delete($notif_config_group["id"]);
					}
				}
			} else { // Si no hay grupos seteados
				if(count($groups)){ // Si vienen grupos, agregarlos
					foreach($groups as $id_group){
						$data_notif_config_client_groups = array(
							"id_notif_general" => $id_notif_config,
							"id_client_group" => $id_group
						);
						$data_notif_config_client_groups["created_by"] = $this->login_user->id;
						$data_notif_config_client_groups["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_general_groups_model->save($data_notif_config_client_groups);
					}
				}
			}
			
			
			// Usuarios
			$notif_config_users = $this->AYN_Notif_general_users_model->get_all_where(array(
				"id_notif_general" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_users_ids = array();
			foreach($notif_config_users as $notif_config_user){
				$array_notif_config_users_ids[$notif_config_user["id"]] = $notif_config_user["id_user"];
			}
			
			if(count($array_notif_config_users_ids)){ // Si hay usuarios seteados
				if(count($users)){ // Si vienen usuarios
					foreach($users as $id_user){
						if(!in_array($id_user, $array_notif_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
							$data_notif_config_client_users = array(
								"id_notif_general" => $id_notif_config,
								"id_user" => $id_user
							);
							$data_notif_config_client_users["created_by"] = $this->login_user->id;
							$data_notif_config_client_users["created"] = get_current_utc_time();
							$save_user_id = $this->AYN_Notif_general_users_model->save($data_notif_config_client_users);
						}
					}
					foreach($array_notif_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
						if(!in_array($id_user, $users)){
							$this->AYN_Notif_general_users_model->delete($id);
						}
					}
				} else { // Si no vienen usuarios, eliminar los seteados
					foreach($notif_config_users as $notif_config_user){
						$this->AYN_Notif_general_users_model->delete($notif_config_user["id"]);
					}
				}
			} else { // Si no hay usuarios seteados
				if(count($users)){ // Si vienen grupos, agregarlos
					foreach($users as $id_user){
						$data_notif_config_client_users = array(
							"id_notif_general" => $id_notif_config,
							"id_user" => $id_user
						);
						$data_notif_config_client_users["created_by"] = $this->login_user->id;
						$data_notif_config_client_users["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_general_users_model->save($data_notif_config_client_users);
					}
				}
			}
		
		} else { // Si no existe configuración
			
			if(count($groups)){ // Si vienen grupos, agregarlos
				foreach($groups as $id_group){
					$data_notif_config_client_groups = array(
						"id_notif_general" => $save_id,
						"id_client_group" => $id_group
					);
					$data_notif_config_client_groups["created_by"] = $this->login_user->id;
					$data_notif_config_client_groups["created"] = get_current_utc_time();
					$save_group_id = $this->AYN_Notif_general_groups_model->save($data_notif_config_client_groups);
				}
			}
			
			if(count($users)){ // Si vienen grupos, agregarlos
				foreach($users as $id_user){
					$data_notif_config_client_userss = array(
						"id_notif_general" => $save_id,
						"id_user" => $id_user
					);
					$data_notif_config_client_userss["created_by"] = $this->login_user->id;
					$data_notif_config_client_userss["created"] = get_current_utc_time();
					$save_user_id = $this->AYN_Notif_general_users_model->save($data_notif_config_client_userss);
				}
			}
			
		}
		
	}
	
	
	/*
		@$save_id: 			Viene siempre, es el id que retorna el save de la configuración.
		@$id_notif_config: 	Viene siempre y cuando exista configuración antes de guardarla.
		@$groups: 			Grupos seleccionados en la configuración.
		@$users: 			Usuarios seleccionados en la configuración.
	*/
	private function _save_notification_config_projects_groups_and_users($save_id, $id_notif_config = NULL, $groups = NULL, $users = NULL){
		
		if($id_notif_config){ // Si existe configuración
			
			// Grupos
			$notif_config_groups = $this->AYN_Notif_projects_clients_groups_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_groups_ids = array();
			foreach($notif_config_groups as $notif_config_group){
				$array_notif_config_groups_ids[$notif_config_group["id"]] = $notif_config_group["id_client_group"];
			}
			
			if(count($array_notif_config_groups_ids)){ // Si hay grupos seteados
				if(count($groups)){ // Si vienen grupos
					foreach($groups as $id_group){
						if(!in_array($id_group, $array_notif_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
							$data_notif_config_client_groups = array(
								"id_notif_projects_clients" => $id_notif_config,
								"id_client_group" => $id_group
							);
							$data_notif_config_client_groups["created_by"] = $this->login_user->id;
							$data_notif_config_client_groups["created"] = get_current_utc_time();
							$save_group_id = $this->AYN_Notif_projects_clients_groups_model->save($data_notif_config_client_groups);
						}
					}
					foreach($array_notif_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
						if(!in_array($id_group, $groups)){
							$this->AYN_Notif_projects_clients_groups_model->delete($id);
						}
					}
				} else { // Si no vienen grupos, eliminar los seteados
					foreach($notif_config_groups as $notif_config_group){
						$this->AYN_Notif_projects_clients_groups_model->delete($notif_config_group["id"]);
					}
				}
			} else { // Si no hay grupos seteados
				if(count($groups)){ // Si vienen grupos, agregarlos
					foreach($groups as $id_group){
						$data_notif_config_client_groups = array(
							"id_notif_projects_clients" => $id_notif_config,
							"id_client_group" => $id_group
						);
						$data_notif_config_client_groups["created_by"] = $this->login_user->id;
						$data_notif_config_client_groups["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_projects_clients_groups_model->save($data_notif_config_client_groups);
					}
				}
			}
			
			
			// Usuarios
			$notif_config_users = $this->AYN_Notif_projects_clients_users_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_users_ids = array();
			foreach($notif_config_users as $notif_config_user){
				$array_notif_config_users_ids[$notif_config_user["id"]] = $notif_config_user["id_user"];
			}
			
			if(count($array_notif_config_users_ids)){ // Si hay usuarios seteados
				if(count($users)){ // Si vienen usuarios
					foreach($users as $id_user){
						if(!in_array($id_user, $array_notif_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
							$data_notif_config_client_users = array(
								"id_notif_projects_clients" => $id_notif_config,
								"id_user" => $id_user
							);
							$data_notif_config_client_users["created_by"] = $this->login_user->id;
							$data_notif_config_client_users["created"] = get_current_utc_time();
							$save_user_id = $this->AYN_Notif_projects_clients_users_model->save($data_notif_config_client_users);
						}
					}
					foreach($array_notif_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
						if(!in_array($id_user, $users)){
							$this->AYN_Notif_projects_clients_users_model->delete($id);
						}
					}
				} else { // Si no vienen usuarios, eliminar los seteados
					foreach($notif_config_users as $notif_config_user){
						$this->AYN_Notif_projects_clients_users_model->delete($notif_config_user["id"]);
					}
				}
			} else { // Si no hay usuarios seteados
				if(count($users)){ // Si vienen grupos, agregarlos
					foreach($users as $id_user){
						$data_notif_config_client_users = array(
							"id_notif_projects_clients" => $id_notif_config,
							"id_user" => $id_user
						);
						$data_notif_config_client_users["created_by"] = $this->login_user->id;
						$data_notif_config_client_users["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_projects_clients_users_model->save($data_notif_config_client_users);
					}
				}
			}
		
		} else { // Si no existe configuración
			
			if(count($groups)){ // Si vienen grupos, agregarlos
				foreach($groups as $id_group){
					$data_notif_config_client_groups = array(
						"id_notif_projects_clients" => $save_id,
						"id_client_group" => $id_group
					);
					$data_notif_config_client_groups["created_by"] = $this->login_user->id;
					$data_notif_config_client_groups["created"] = get_current_utc_time();
					$save_group_id = $this->AYN_Notif_projects_clients_groups_model->save($data_notif_config_client_groups);
				}
			}
			
			if(count($users)){ // Si vienen grupos, agregarlos
				foreach($users as $id_user){
					$data_notif_config_client_userss = array(
						"id_notif_projects_clients" => $save_id,
						"id_user" => $id_user
					);
					$data_notif_config_client_userss["created_by"] = $this->login_user->id;
					$data_notif_config_client_userss["created"] = get_current_utc_time();
					$save_user_id = $this->AYN_Notif_projects_clients_users_model->save($data_notif_config_client_userss);
				}
			}
			
		}
		
	}
	
	/*
		@$save_id: 			Viene siempre, es el id que retorna el save de la configuración.
		@$id_notif_config: 	Viene siempre y cuando exista configuración antes de guardarla.
		@$groups: 			Grupos seleccionados en la configuración.
		@$users: 			Usuarios seleccionados en la configuración.
	*/
	private function _save_notification_config_admin_groups_and_users($save_id, $id_notif_config = NULL, $groups = NULL, $users = NULL){
		
		if($id_notif_config){ // Si existe configuración
			
			// Grupos
			$notif_config_groups = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_groups_ids = array();
			foreach($notif_config_groups as $notif_config_group){
				$array_notif_config_groups_ids[$notif_config_group["id"]] = $notif_config_group["id_client_group"];
			}
			
			if(count($array_notif_config_groups_ids)){ // Si hay grupos seteados
				if(count($groups)){ // Si vienen grupos
					foreach($groups as $id_group){
						if(!in_array($id_group, $array_notif_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
							$data_notif_config_client_groups = array(
								"id_notif_projects_admin" => $id_notif_config,
								"id_client_group" => $id_group
							);
							$data_notif_config_client_groups["created_by"] = $this->login_user->id;
							$data_notif_config_client_groups["created"] = get_current_utc_time();
							$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
						}
					}
					foreach($array_notif_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
						if(!in_array($id_group, $groups)){
							$this->AYN_Notif_projects_admin_groups_model->delete($id);
						}
					}
				} else { // Si no vienen grupos, eliminar los seteados
					foreach($notif_config_groups as $notif_config_group){
						$this->AYN_Notif_projects_admin_groups_model->delete($notif_config_group["id"]);
					}
				}
			} else { // Si no hay grupos seteados
				if(count($groups)){ // Si vienen grupos, agregarlos
					foreach($groups as $id_group){
						$data_notif_config_client_groups = array(
							"id_notif_projects_admin" => $id_notif_config,
							"id_client_group" => $id_group
						);
						$data_notif_config_client_groups["created_by"] = $this->login_user->id;
						$data_notif_config_client_groups["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
					}
				}
			}
			
			
			// Usuarios
			$notif_config_users = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config,
				"deleted" => 0
			))->result_array();
			
			$array_notif_config_users_ids = array();
			foreach($notif_config_users as $notif_config_user){
				$array_notif_config_users_ids[$notif_config_user["id"]] = $notif_config_user["id_user"];
			}
			
			if(count($array_notif_config_users_ids)){ // Si hay usuarios seteados
				if(count($users)){ // Si vienen usuarios
					foreach($users as $id_user){
						if(!in_array($id_user, $array_notif_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
							$data_notif_config_client_users = array(
								"id_notif_projects_admin" => $id_notif_config,
								"id_user" => $id_user
							);
							$data_notif_config_client_users["created_by"] = $this->login_user->id;
							$data_notif_config_client_users["created"] = get_current_utc_time();
							$save_user_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_users);
						}
					}
					foreach($array_notif_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
						if(!in_array($id_user, $users)){
							$this->AYN_Notif_projects_admin_users_model->delete($id);
						}
					}
				} else { // Si no vienen usuarios, eliminar los seteados
					foreach($notif_config_users as $notif_config_user){
						$this->AYN_Notif_projects_admin_users_model->delete($notif_config_user["id"]);
					}
				}
			} else { // Si no hay usuarios seteados
				if(count($users)){ // Si vienen grupos, agregarlos
					foreach($users as $id_user){
						$data_notif_config_client_users = array(
							"id_notif_projects_admin" => $id_notif_config,
							"id_user" => $id_user
						);
						$data_notif_config_client_users["created_by"] = $this->login_user->id;
						$data_notif_config_client_users["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_users);
					}
				}
			}
		
		} else { // Si no existe configuración
			
			if(count($groups)){ // Si vienen grupos, agregarlos
				foreach($groups as $id_group){
					$data_notif_config_client_groups = array(
						"id_notif_projects_admin" => $save_id,
						"id_client_group" => $id_group
					);
					$data_notif_config_client_groups["created_by"] = $this->login_user->id;
					$data_notif_config_client_groups["created"] = get_current_utc_time();
					$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
				}
			}
			
			if(count($users)){ // Si vienen grupos, agregarlos
				foreach($users as $id_user){
					$data_notif_config_client_userss = array(
						"id_notif_projects_admin" => $save_id,
						"id_user" => $id_user
					);
					$data_notif_config_client_userss["created_by"] = $this->login_user->id;
					$data_notif_config_client_userss["created"] = get_current_utc_time();
					$save_user_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_userss);
				}
			}
			
		}
		
	}
	
	
	function modal_form_notification_config_users(){
		
		$id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$id_modulo = $this->input->post('id_modulo');
		$id_submodulo = $this->input->post('id_submodulo');
		$item = $this->input->post('item');
		
		// ids de configuraciones
		$id_notif_config_add = $this->input->post('id_notif_config_add');
		$id_notif_config_edit = $this->input->post('id_notif_config_edit');
		$id_notif_config_delete = $this->input->post('id_notif_config_delete');
		$id_notif_config_bulk_load = $this->input->post('id_notif_config_bulk_load');
		
		$view_data["id_notif_config_add"] = $id_notif_config_add;
		$view_data["id_notif_config_edit"] = $id_notif_config_edit;
		$view_data["id_notif_config_delete"] = $id_notif_config_delete;
		$view_data["id_notif_config_bulk_load"] = $id_notif_config_bulk_load;
		
		$view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$view_data["id_client"] = $id_client;
		$view_data["id_project"] = $id_project;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;
		$view_data["item"] = $item;
		
		// Grupos de Cliente
		$array_client_groups = array();
		$client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
			"id_client" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_groups as $client_group){
			$array_client_groups[$client_group->id] = $client_group->group_name;
		}
		
		// Usuarios de Cliente miembros del Proyecto seleccionado
		$array_client_users = array();
		$project_members = $this->Project_members_model->get_all_where(array(
			"project_id" => $id_project,
			"deleted" => 0
		))->result();
		
		foreach($project_members as $project_member){
			$user = $this->Users_model->get_one($project_member->user_id);
			$array_client_users[$user->id] = $user->first_name." ".$user->last_name;
		}
		
		$view_data["array_client_groups"] = $array_client_groups;
		$view_data["array_client_users"] = $array_client_users;
		
		$modulo = "-";
		$submodulo = "-";
		
		if($id_notif_config_add){
			
			$notif_config_add = $this->AYN_Notif_projects_clients_model->get_one($id_notif_config_add);
			
			$notif_config_groups_add = $this->AYN_Notif_projects_clients_groups_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_groups_add = array();
			foreach($notif_config_groups_add as $notif_config_group_add){
				$selected_client_groups_add[] = $notif_config_group_add->id_client_group;
			}
			
			$notif_config_users_add = $this->AYN_Notif_projects_clients_users_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_users_add = array();
			foreach($notif_config_users_add as $notif_config_user_add){
				$selected_client_users_add[] = $notif_config_user_add->id_user;
			}
			
		}
		
		if($id_notif_config_edit){
			
			$notif_config_edit = $this->AYN_Notif_projects_clients_model->get_one($id_notif_config_edit);
			
			$notif_config_groups_edit = $this->AYN_Notif_projects_clients_groups_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_groups_edit = array();
			foreach($notif_config_groups_edit as $notif_config_group_edit){
				$selected_client_groups_edit[] = $notif_config_group_edit->id_client_group;
			}
			
			$notif_config_users_edit = $this->AYN_Notif_projects_clients_users_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_users_edit = array();
			foreach($notif_config_users_edit as $notif_config_user_edit){
				$selected_client_users_edit[] = $notif_config_user_edit->id_user;
			}
			
		}
		
		if($id_notif_config_delete){
			
			$notif_config_delete = $this->AYN_Notif_projects_clients_model->get_one($id_notif_config_delete);
			
			$notif_config_groups_delete = $this->AYN_Notif_projects_clients_groups_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_groups_delete = array();
			foreach($notif_config_groups_delete as $notif_config_group_delete){
				$selected_client_groups_delete[] = $notif_config_group_delete->id_client_group;
			}
			
			$notif_config_users_delete = $this->AYN_Notif_projects_clients_users_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_users_delete = array();
			foreach($notif_config_users_delete as $notif_config_user_delete){
				$selected_client_users_delete[] = $notif_config_user_delete->id_user;
			}
			
		}
		
		if($id_notif_config_bulk_load){
			
			$notif_config_bulk_load = $this->AYN_Notif_projects_clients_model->get_one($id_notif_config_bulk_load);
			
			$notif_config_groups_bulk_load = $this->AYN_Notif_projects_clients_groups_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_bulk_load,
				"deleted" => 0
			))->result();
			$selected_client_groups_bulk_load = array();
			foreach($notif_config_groups_bulk_load as $notif_config_group_bulk_load){
				$selected_client_groups_bulk_load[] = $notif_config_group_bulk_load->id_client_group;
			}
			
			$notif_config_users_bulk_load = $this->AYN_Notif_projects_clients_users_model->get_all_where(array(
				"id_notif_projects_clients" => $id_notif_config_bulk_load,
				"deleted" => 0
			))->result();
			$selected_client_users_bulk_load = array();
			foreach($notif_config_users_bulk_load as $notif_config_user_bulk_load){
				$selected_client_users_bulk_load[] = $notif_config_user_bulk_load->id_user;
			}
			
		}
		
		$eventos = array(
			"add" => array(
				"selected_client_groups" => $selected_client_groups_add,
				"selected_client_users" => $selected_client_users_add,
				"is_email_notification" => $notif_config_add->email_notification,
				"is_web_notification" => $notif_config_add->web_notification
			),
			"edit" => array(
				"selected_client_groups" => $selected_client_groups_edit,
				"selected_client_users" => $selected_client_users_edit,
				"is_email_notification" => $notif_config_edit->email_notification,
				"is_web_notification" => $notif_config_edit->web_notification
			),
			"delete" => array(
				"selected_client_groups" => $selected_client_groups_delete,
				"selected_client_users" => $selected_client_users_delete,
				"is_email_notification" => $notif_config_delete->email_notification,
				"is_web_notification" => $notif_config_delete->web_notification
			),
			"bulk_load" => array(
				"selected_client_groups" => $selected_client_groups_bulk_load,
				"selected_client_users" => $selected_client_users_bulk_load,
				"is_email_notification" => $notif_config_bulk_load->email_notification,
				"is_web_notification" => $notif_config_bulk_load->web_notification
			)
		);
		
		if($item == "environmental_records" || $item == "feeders" || $item == "other_records"){
			$modulo = lang("records");
			$submodulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			unset($eventos["bulk_load"]);
		}
		
		
		if($item == "compromises_rca"){
			$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["delete"]);
			unset($eventos["bulk_load"]);
		}
		
		if($item == "compromises_rep"){
			$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["add"]);
			unset($eventos["delete"]);
			unset($eventos["bulk_load"]);
		}
		
		if($item == "permittings"){
			$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["delete"]);
			unset($eventos["bulk_load"]);
		}
		
		if($item == "setting_dashboard"){
			$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["add"]);
			unset($eventos["delete"]);
			unset($eventos["bulk_load"]);
		}
		
		if($item == "bulk_load"){
			$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
			$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
			unset($eventos["add"]);
			unset($eventos["edit"]);
			unset($eventos["delete"]);			
		}

		$view_data["modulo"] = $modulo;
		$view_data["submodulo"] = $submodulo;
		$view_data["eventos"] = $eventos;
		
       $this->load->view('general_settings/notification_settings_users/modal_form', $view_data);
		
	}
	
	
	function save_notification_config_users(){
		
		$id_notif_config_add = $this->input->post("id_notif_config_add");
		$id_notif_config_edit = $this->input->post("id_notif_config_edit");
		$id_notif_config_delete = $this->input->post("id_notif_config_delete");
		$id_notif_config_bulk_load = $this->input->post("id_notif_config_bulk_load");
		
		$id_client = $this->input->post("id_client");
		$id_project = $this->input->post("id_project");
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		$item = $this->input->post("item");
		
		$groups_add = $this->input->post("groups_add");
		$users_add = $this->input->post("users_add");
		$notification_email_add = ($this->input->post("notification_email_add")) ? 1 : 0;
		$notification_web_add = ($this->input->post("notification_web_add")) ? 1 : 0;
		
		$groups_edit = $this->input->post("groups_edit");
		$users_edit = $this->input->post("users_edit");
		$notification_email_edit = ($this->input->post("notification_email_edit")) ? 1 : 0;
		$notification_web_edit = ($this->input->post("notification_web_edit")) ? 1 : 0;
		
		$groups_delete = $this->input->post("groups_delete");
		$users_delete = $this->input->post("users_delete");
		$notification_email_delete = ($this->input->post("notification_email_delete")) ? 1 : 0;
		$notification_web_delete = ($this->input->post("notification_web_delete")) ? 1 : 0;
		
		$groups_bulk_load = $this->input->post("groups_bulk_load");
		$users_bulk_load = $this->input->post("users_bulk_load");
		$notification_email_bulk_load = ($this->input->post("notification_email_bulk_load")) ? 1 : 0;
		$notification_web_bulk_load = ($this->input->post("notification_web_bulk_load")) ? 1 : 0;
		
		$data_notif_config_users = array(
			"id_client" => $id_client,
			"id_project" => $id_project,
			"id_client_module" => $id_modulo,
			"id_client_submodule" => $id_submodulo,
		);
		
		// ADD
		$event_add = "";
		if($item == "environmental_records" || $item == "feeders" || $item == "other_records"
			|| $item == "compromises_rca" || $item == "permittings"){
			$event_add = "add";
		}
		
		if($event_add){
			
			$data_notif_config_users["event"] = $event_add;
			$data_notif_config_users["email_notification"] = $notification_email_add;
			$data_notif_config_users["web_notification"] = $notification_web_add;
	
			if($id_notif_config_add){
				$data_notif_config_users["modified_by"] = $this->login_user->id;
				$data_notif_config_users["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users, $id_notif_config_add);
			} else {
				$data_notif_config_users["created_by"] = $this->login_user->id;
				$data_notif_config_users["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users);
			}
			
			$save_id_notif_config_add = $save_id;
			$this->_save_notification_config_projects_groups_and_users($save_id, $id_notif_config_add, $groups_add, $users_add);
			
		}
		
		
		// EDIT
		$event_edit = "";
		if($item == "environmental_records" || $item == "feeders" || $item == "other_records"
			|| $item == "compromises_rca" || $item == "compromises_rep" || $item == "permittings" 
			|| $item == "setting_dashboard"){
			$event_edit = "edit";
		}
		
		if($event_edit){
			
			$data_notif_config_users["event"] = $event_edit;
			$data_notif_config_users["email_notification"] = $notification_email_edit;
			$data_notif_config_users["web_notification"] = $notification_web_edit;
	
			if($id_notif_config_edit){
				$data_notif_config_users["modified_by"] = $this->login_user->id;
				$data_notif_config_users["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users, $id_notif_config_edit);
			} else {
				$data_notif_config_users["created_by"] = $this->login_user->id;
				$data_notif_config_users["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users);
			}
			
			$save_id_notif_config_edit = $save_id;
			$this->_save_notification_config_projects_groups_and_users($save_id, $id_notif_config_edit, $groups_edit, $users_edit);
			
		}
		
		// DELETE
		$event_delete = "";
		if($item == "environmental_records" || $item == "feeders" || $item == "other_records"){
			$event_delete = "delete";
		}
		
		if($event_delete){
			
			$data_notif_config_users["event"] = $event_delete;
			$data_notif_config_users["email_notification"] = $notification_email_delete;
			$data_notif_config_users["web_notification"] = $notification_web_delete;
	
			if($id_notif_config_delete){
				$data_notif_config_users["modified_by"] = $this->login_user->id;
				$data_notif_config_users["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users, $id_notif_config_delete);
			} else {
				$data_notif_config_users["created_by"] = $this->login_user->id;
				$data_notif_config_users["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users);
			}
			
			$save_id_notif_config_delete = $save_id;
			$this->_save_notification_config_projects_groups_and_users($save_id, $id_notif_config_delete, $groups_delete, $users_delete);
			
		}
		
		
		// BULK LOAD
		$event_bulk_load = "";
		if($item == "bulk_load"){
			$event_bulk_load = "bulk_load";
		}
		
		if($event_bulk_load){
			
			$data_notif_config_users["event"] = $event_bulk_load;
			$data_notif_config_users["email_notification"] = $notification_email_bulk_load;
			$data_notif_config_users["web_notification"] = $notification_web_bulk_load;
	
			if($id_notif_config_bulk_load){
				$data_notif_config_users["modified_by"] = $this->login_user->id;
				$data_notif_config_users["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users, $id_notif_config_bulk_load);
			} else {
				$data_notif_config_users["created_by"] = $this->login_user->id;
				$data_notif_config_users["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_clients_model->save($data_notif_config_users);
			}
			
			$save_id_notif_config_bulk_load = $save_id;
			$this->_save_notification_config_projects_groups_and_users($save_id, $id_notif_config_bulk_load, $groups_bulk_load, $users_bulk_load);
			
		}
		
		$config_icon_add = ($notification_email_add || $notification_web_add) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_edit = ($notification_email_edit || $notification_web_edit) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_delete = ($notification_email_delete || $notification_web_delete) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		$config_icon_bulk_load = ($notification_email_bulk_load || $notification_web_bulk_load) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
		
		$btn_action_attributes = array(
			"class" => "edit", 
			"title" => lang('notification_settings'), 
			"data-post-id_modulo" => $id_modulo, 
			"data-post-id_submodulo" => $id_submodulo, 
			"data-post-item" => $item, 
			"data-post-id_client" => $id_client,
			"data-post-id_project" => $id_project,
			"data-post-id_notif_config_add" => $save_id_notif_config_add, 
			"data-post-id_notif_config_edit" => $save_id_notif_config_edit, 
			"data-post-id_notif_config_delete" => $save_id_notif_config_delete, 
			"data-post-id_notif_config_bulk_load" => $save_id_notif_config_bulk_load,
		);
		$btn_action = modal_anchor(get_uri("general_settings/modal_form_notification_config_users"), "<i class='fa fa-pencil'></i>", $btn_action_attributes);
		
		if ($save_id) {
			echo json_encode(array(
				"success" => true, 
				'item' => $item,  
				'config_icon_add' => $config_icon_add,
				'config_icon_edit' => $config_icon_edit, 
				'config_icon_delete' => $config_icon_delete,
				'config_icon_bulk_load' => $config_icon_bulk_load,
				'btn_action' => $btn_action, 
				'message' => lang('record_saved')
			));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	function get_user_members_of_groups(){
		
		$id_client = $this->input->post("id_client");
		$id_project = $this->input->post("id_project");
		$groups = $this->input->post("groups");
		$evento = $this->input->post("evento");
		
		// Usuarios de Cliente miembros del Proyecto seleccionado
		$array_client_users = array();
		$project_members = $this->Project_members_model->get_all_where(array(
			"project_id" => $id_project,
			"deleted" => 0
		))->result();
		
		foreach($project_members as $project_member){
			$user = $this->Users_model->get_one($project_member->user_id);
			$array_client_users[$user->id] = $user->first_name." ".$user->last_name;
		}
		
		// Usuarios de los grupos seleccionados
		$array_selected_users = array();
		foreach($groups as $id_group){
			
			$users_of_group = $this->Users_model->get_all_where(array(
				"id_client_group" => $id_group,
				"deleted" => 0
			))->result();
			
			foreach($users_of_group as $user){
				if(array_key_exists($user->id, $array_client_users)){
					$array_selected_users[] = $user->id;
				}
			}
			
		}
		
		$multiselect_name = ($evento == "admin_config") ? "users[]" : "users_".$evento."[]";
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= 	'<label for="users" class="col-md-3">'.lang('users').'</label>';
		$html .= 	'<div class="col-md-9">';
		$html .= 		form_multiselect($multiselect_name, $array_client_users, $array_selected_users, "id='users_".$evento."' class='select2 multiple' multiple='multiple'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	function modal_form_notification_config_admin(){
	
		$id_notif_config = $this->input->post('id_notif_config');
        $id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$notification_type = $this->input->post('notification_type');
		$id_modulo = $this->input->post('id_modulo');
		$id_submodulo = $this->input->post('id_submodulo');
		
		$view_data["id_notif_config"] = $id_notif_config;
		$view_data["id_client"] = $id_client;
		$view_data["id_project"] = $id_project;
		$view_data["notification_type"] = $notification_type;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;	
		
		if($id_notif_config){
			
			$notif_config = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config);
			$view_data["is_email_notification"] = $notif_config->email_notification;
			$view_data["is_web_notification"] = $notif_config->web_notification;
			
			$notif_config_groups = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config,
				"deleted" => 0
			))->result();
			
			$selected_admin_groups = array();
			foreach($notif_config_groups as $notif_config_group){
				$selected_admin_groups[] = $notif_config_group->id_client_group;
			}
			$view_data["selected_admin_groups"] = $selected_admin_groups;
			
			$notif_config_users = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config,
				"deleted" => 0
			))->result();
			
			$selected_admin_users = array();
			foreach($notif_config_users as $notif_config_user){
				$selected_admin_users[] = $notif_config_user->id_user;
			}
			$view_data["selected_admin_users"] = $selected_admin_users;
			
		} 

		$modulo = "-";
		$submodulo = "-";
		$modulo = $this->AYN_Admin_modules_model->get_one($id_modulo)->name;
		$submodulo = $this->AYN_Admin_submodules_model->get_one($id_submodulo)->name;
		
		// Grupos de Cliente
		$array_client_groups = array();
		$client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
			"id_client" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_groups as $client_group){
			$array_client_groups[$client_group->id] = $client_group->group_name;
		}
		
		// Usuarios de Cliente miembros del Proyecto seleccionado
		$array_client_users = array();
		$project_members = $this->Project_members_model->get_all_where(array(
			"project_id" => $id_project,
			"deleted" => 0
		))->result();
		
		foreach($project_members as $project_member){
			$user = $this->Users_model->get_one($project_member->user_id);
			$array_client_users[$user->id] = $user->first_name." ".$user->last_name;
		}
		
		$item = $this->input->post('item');
		$view_data["item"] = $item;
		
		$id_notif_config_form_add = $this->input->post('id_notif_config_form_add');
		$id_notif_config_form_edit_name = $this->input->post('id_notif_config_form_edit_name');
		$id_notif_config_form_edit_cat = $this->input->post('id_notif_config_form_edit_cat');
		$id_notif_config_form_delete = $this->input->post('id_notif_config_form_delete');
		
		$id_notif_config_add = $this->input->post("id_notif_config_add");
		$id_notif_config_edit = $this->input->post("id_notif_config_edit");
		$id_notif_config_delete = $this->input->post("id_notif_config_delete");
		
		$view_data["id_notif_config_form_add"] = $id_notif_config_form_add;
		$view_data["id_notif_config_form_edit_name"] = $id_notif_config_form_edit_name;
		$view_data["id_notif_config_form_edit_cat"] = $id_notif_config_form_edit_cat;
		$view_data["id_notif_config_form_delete"] = $id_notif_config_form_delete;
		
		$view_data["id_notif_config_add"] = $id_notif_config_add;
		$view_data["id_notif_config_edit"] = $id_notif_config_edit;
		$view_data["id_notif_config_delete"] = $id_notif_config_delete;
		
		if($id_notif_config_form_add){
			
			$notif_config_form_add = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_form_add);
			
			$notif_config_groups_form_add = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_add,
				"deleted" => 0
			))->result();
			$selected_client_groups_form_add = array();
			foreach($notif_config_groups_form_add as $notif_config_group_form_add){
				$selected_client_groups_form_add[] = $notif_config_group_form_add->id_client_group;
			}
			
			$notif_config_users_form_add = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_add,
				"deleted" => 0
			))->result();
			$selected_client_users_form_add = array();
			foreach($notif_config_users_form_add as $notif_config_user_form_add){
				$selected_client_users_form_add[] = $notif_config_user_form_add->id_user;
			}
			
		}
		
		if($id_notif_config_form_edit_name){
			
			$notif_config_form_edit_name = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_form_edit_name);
			
			$notif_config_groups_form_edit_name = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_edit_name,
				"deleted" => 0
			))->result();
			$selected_client_groups_form_edit_name = array();
			foreach($notif_config_groups_form_edit_name as $notif_config_group_form_edit_name){
				$selected_client_groups_form_edit_name[] = $notif_config_group_form_edit_name->id_client_group;
			}
			
			$notif_config_users_form_edit_name = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_edit_name,
				"deleted" => 0
			))->result();
			$selected_client_users_form_edit_name = array();
			foreach($notif_config_users_form_edit_name as $notif_config_user_form_edit_name){
				$selected_client_users_form_edit_name[] = $notif_config_user_form_edit_name->id_user;
			}
			
		}
		
		if($id_notif_config_form_edit_cat){
			
			$notif_config_form_edit_cat = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_form_edit_cat);
			
			$notif_config_groups_form_edit_cat = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_edit_cat,
				"deleted" => 0
			))->result();
			$selected_client_groups_form_edit_cat = array();
			foreach($notif_config_groups_form_edit_cat as $notif_config_group_form_edit_cat){
				$selected_client_groups_form_edit_cat[] = $notif_config_group_form_edit_cat->id_client_group;
			}
			
			$notif_config_users_form_edit_cat = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_edit_cat,
				"deleted" => 0
			))->result();
			$selected_client_users_form_edit_cat = array();
			foreach($notif_config_users_form_edit_cat as $notif_config_user_form_edit_cat){
				$selected_client_users_form_edit_cat[] = $notif_config_user_form_edit_cat->id_user;
			}
			
		}
		
		if($id_notif_config_form_delete){
			
			$notif_config_form_delete = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_form_delete);
			
			$notif_config_groups_form_delete = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_delete,
				"deleted" => 0
			))->result();
			$selected_client_groups_form_delete = array();
			foreach($notif_config_groups_form_delete as $notif_config_group_form_delete){
				$selected_client_groups_form_delete[] = $notif_config_group_form_delete->id_client_group;
			}
			
			$notif_config_users_form_delete = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_form_delete,
				"deleted" => 0
			))->result();
			$selected_client_users_form_delete = array();
			foreach($notif_config_users_form_delete as $notif_config_user_form_delete){
				$selected_client_users_form_delete[] = $notif_config_user_form_delete->id_user;
			}
			
		}
		
		if($id_notif_config_add){
			
			$notif_config_add = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_add);
			
			$notif_config_groups_add = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_groups_add = array();
			foreach($notif_config_groups_add as $notif_config_group_add){
				$selected_client_groups_add[] = $notif_config_group_add->id_client_group;
			}
			
			$notif_config_users_add = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_add,
				"deleted" => 0
			))->result();
			$selected_client_users_add = array();
			foreach($notif_config_users_add as $notif_config_user_add){
				$selected_client_users_add[] = $notif_config_user_add->id_user;
			}
			
		}
		
		if($id_notif_config_edit){
			
			$notif_config_edit = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_edit);
			
			$notif_config_groups_edit = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_groups_edit = array();
			foreach($notif_config_groups_edit as $notif_config_group_edit){
				$selected_client_groups_edit[] = $notif_config_group_edit->id_client_group;
			}
			
			$notif_config_users_edit = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_edit,
				"deleted" => 0
			))->result();
			$selected_client_users_edit = array();
			foreach($notif_config_users_edit as $notif_config_user_edit){
				$selected_client_users_edit[] = $notif_config_user_edit->id_user;
			}
			
		}
		
		if($id_notif_config_delete){
			
			$notif_config_delete = $this->AYN_Notif_projects_admin_model->get_one($id_notif_config_delete);
			
			$notif_config_groups_delete = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_groups_delete = array();
			foreach($notif_config_groups_delete as $notif_config_group_delete){
				$selected_client_groups_delete[] = $notif_config_group_delete->id_client_group;
			}
			
			$notif_config_users_delete = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
				"id_notif_projects_admin" => $id_notif_config_delete,
				"deleted" => 0
			))->result();
			$selected_client_users_delete = array();
			foreach($notif_config_users_delete as $notif_config_user_delete){
				$selected_client_users_delete[] = $notif_config_user_delete->id_user;
			}
			
		}
		
		$eventos = array(
			"form_add" => array(
				"selected_client_groups" => $selected_client_groups_form_add,
				"selected_client_users" => $selected_client_users_form_add,
				"is_email_notification" => $notif_config_form_add->email_notification,
				"is_web_notification" => $notif_config_form_add->web_notification
			),
			"form_edit_name" => array(
				"selected_client_groups" => $selected_client_groups_form_edit_name,
				"selected_client_users" => $selected_client_users_form_edit_name,
				"is_email_notification" => $notif_config_form_edit_name->email_notification,
				"is_web_notification" => $notif_config_form_edit_name->web_notification
			),
			"form_edit_cat" => array(
				"selected_client_groups" => $selected_client_groups_form_edit_cat,
				"selected_client_users" => $selected_client_users_form_edit_cat,
				"is_email_notification" => $notif_config_form_edit_cat->email_notification,
				"is_web_notification" => $notif_config_form_edit_cat->web_notification
			),
			"form_delete" => array(
				"selected_client_groups" => $selected_client_groups_form_delete,
				"selected_client_users" => $selected_client_users_form_delete,
				"is_email_notification" => $notif_config_form_delete->email_notification,
				"is_web_notification" => $notif_config_form_delete->web_notification
			),
			"uf_add_element" => array(
				"selected_client_groups" => $selected_client_groups_add,
				"selected_client_users" => $selected_client_users_add,
				"is_email_notification" => $notif_config_add->email_notification,
				"is_web_notification" => $notif_config_add->web_notification
			),
			"uf_edit_element" => array(
				"selected_client_groups" => $selected_client_groups_edit,
				"selected_client_users" => $selected_client_users_edit,
				"is_email_notification" => $notif_config_edit->email_notification,
				"is_web_notification" => $notif_config_edit->web_notification
			),
			"uf_delete_element" => array(
				"selected_client_groups" => $selected_client_groups_delete,
				"selected_client_users" => $selected_client_users_delete,
				"is_email_notification" => $notif_config_delete->email_notification,
				"is_web_notification" => $notif_config_delete->web_notification
			)
		);
		
		if($item == "records"){
			$modulo = lang("records");
			$submodulo = lang("forms");
			unset($eventos["uf_add_element"]);
			unset($eventos["uf_edit_element"]);
			unset($eventos["uf_delete_element"]);
		}
		if($item == "indicators"){
			$modulo = lang("indicators");
			$submodulo = lang("functional_units");
			unset($eventos["form_add"]);
			unset($eventos["form_edit_name"]);
			unset($eventos["form_edit_cat"]);
			unset($eventos["form_delete"]);
		}
		
		$view_data["eventos"] = $eventos;
			
		$view_data["modulo"] = $modulo;
		$view_data["submodulo"] = $submodulo;
		$view_data["array_client_groups"] = $array_client_groups;
		$view_data["array_client_users"] = $array_client_users;
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $this->load->view('general_settings/notification_settings_admin/modal_form', $view_data);
		
	}
	
	function save_notification_config_admin(){
		
		$id_client = $this->input->post("id_client");
		$id_project = $this->input->post("id_project");
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		
		$item = $this->input->post("item");
		
		$data_notif_config_admin = array(
			"id_client" => $id_client,
			"id_project" => $id_project,
			"id_admin_module" => $id_modulo,
			"id_admin_submodule" => $id_submodulo,
		);
		
		if($item == "records"){
			
			$id_notif_config_form_add = $this->input->post('id_notif_config_form_add');
			$id_notif_config_form_edit_name = $this->input->post('id_notif_config_form_edit_name');
			$id_notif_config_form_edit_cat = $this->input->post('id_notif_config_form_edit_cat');
			$id_notif_config_form_delete = $this->input->post('id_notif_config_form_delete');
			
			$groups_form_add = $this->input->post('groups_form_add');
			$users_form_add = $this->input->post('users_form_add');
			$notification_email_form_add = ($this->input->post('notification_email_form_add')) ? 1 : 0;
			$notification_web_form_add = ($this->input->post('notification_web_form_add')) ? 1 : 0;
			
			$groups_form_edit_name = $this->input->post('groups_form_edit_name');
			$users_form_edit_name = $this->input->post('users_form_edit_name');
			$notification_email_form_edit_name = ($this->input->post('notification_email_form_edit_name')) ? 1 : 0;
			$notification_web_form_edit_name = ($this->input->post('notification_web_form_edit_name')) ? 1 : 0;
			
			$groups_form_edit_cat = $this->input->post('groups_form_edit_cat');
			$users_form_edit_cat = $this->input->post('users_form_edit_cat');
			$notification_email_form_edit_cat = ($this->input->post('notification_email_form_edit_cat')) ? 1 : 0;
			$notification_web_form_edit_cat = ($this->input->post('notification_web_form_edit_cat')) ? 1 : 0;
			
			$groups_form_delete = $this->input->post('groups_form_delete');
			$users_form_delete = $this->input->post('users_form_delete');
			$notification_email_form_delete = ($this->input->post('notification_email_form_delete')) ? 1 : 0;
			$notification_web_form_delete = ($this->input->post('notification_web_form_delete')) ? 1 : 0;
			
			
			// FORM ADD
			$data_notif_config_admin["event"] = "form_add";
			$data_notif_config_admin["email_notification"] = $notification_email_form_add;
			$data_notif_config_admin["web_notification"] = $notification_web_form_add;
	
			if($id_notif_config_form_add){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_form_add);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_form_add = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_form_add, $groups_form_add, $users_form_add);
			
			// FORM EDIT NAME
			$data_notif_config_admin["event"] = "form_edit_name";
			$data_notif_config_admin["email_notification"] = $notification_email_form_edit_name;
			$data_notif_config_admin["web_notification"] = $notification_web_form_edit_name;
	
			if($id_notif_config_form_edit_name){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_form_edit_name);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_form_edit_name = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_form_edit_name, $groups_form_edit_name, $users_form_edit_name);
			
			// FORM EDIT CAT
			$data_notif_config_admin["event"] = "form_edit_cat";
			$data_notif_config_admin["email_notification"] = $notification_email_form_edit_cat;
			$data_notif_config_admin["web_notification"] = $notification_web_form_edit_cat;
	
			if($id_notif_config_form_edit_cat){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_form_edit_cat);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_form_edit_cat = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_form_edit_cat, $groups_form_edit_cat, $users_form_edit_cat);
			
			// FORM DELETE
			$data_notif_config_admin["event"] = "form_delete";
			$data_notif_config_admin["email_notification"] = $notification_email_form_delete;
			$data_notif_config_admin["web_notification"] = $notification_web_form_delete;
	
			if($id_notif_config_form_delete){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_form_delete);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_form_delete = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_form_delete, $groups_form_delete, $users_form_delete);
			
			$config_icon_form_add = ($notification_email_form_add || $notification_web_form_add) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			$config_icon_form_edit_name = ($notification_email_form_edit_name || $notification_web_form_edit_name) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			$config_icon_form_edit_cat = ($notification_email_form_edit_cat || $notification_web_form_edit_cat) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			$config_icon_form_delete = ($notification_email_form_delete || $notification_web_form_delete) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$btn_action_attributes = array(
				"class" => "edit", 
				"title" => lang('notification_settings'), 
				"data-post-id_modulo" => $id_modulo, 
				"data-post-id_submodulo" => $id_submodulo, 
				"data-post-item" => $item, 
				"data-post-id_client" => $id_client,
				"data-post-id_project" => $id_project,
				"data-post-id_notif_config_form_add" => $save_id_notif_config_form_add, 
				"data-post-id_notif_config_form_edit_name" => $save_id_notif_config_form_edit_name, 
				"data-post-id_notif_config_form_edit_cat" => $save_id_notif_config_form_edit_cat, 
				"data-post-id_notif_config_form_delete" => $save_id_notif_config_form_delete,
			);
			
			$btn_action = modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", $btn_action_attributes);
						
			if ($save_id) {
				echo json_encode(array(
					"success" => true, 
					'item' => $item,  
					'config_icon_form_add' => $config_icon_form_add,
					'config_icon_form_edit_name' => $config_icon_form_edit_name, 
					'config_icon_form_edit_cat' => $config_icon_form_edit_cat,
					'config_icon_form_delete' => $config_icon_form_delete,
					'btn_action' => $btn_action, 
					'message' => lang('record_saved')
				));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}
			
		} elseif($item == "indicators"){
		
			$id_notif_config_add = $this->input->post('id_notif_config_add');
			$id_notif_config_edit = $this->input->post('id_notif_config_edit');
			$id_notif_config_delete = $this->input->post('id_notif_config_delete');
			
			$groups_add = $this->input->post('groups_uf_add_element');
			$users_add = $this->input->post('users_uf_add_element');
			$notification_email_add = ($this->input->post('notification_email_uf_add_element')) ? 1 : 0;
			$notification_web_add = ($this->input->post('notification_web_uf_add_element')) ? 1 : 0;
			
			$groups_edit = $this->input->post('groups_uf_edit_element');
			$users_edit = $this->input->post('users_uf_edit_element');
			$notification_email_edit = ($this->input->post('notification_email_uf_edit_element')) ? 1 : 0;
			$notification_web_edit = ($this->input->post('notification_web_uf_edit_element')) ? 1 : 0;
			
			$groups_delete = $this->input->post('groups_uf_delete_element');
			$users_delete = $this->input->post('users_uf_delete_element');
			$notification_email_delete = ($this->input->post('notification_email_uf_delete_element')) ? 1 : 0;
			$notification_web_delete = ($this->input->post('notification_web_uf_delete_element')) ? 1 : 0;
			
			// ADD
			$data_notif_config_admin["event"] = "uf_add_element";
			$data_notif_config_admin["email_notification"] = $notification_email_add;
			$data_notif_config_admin["web_notification"] = $notification_web_add;
	
			if($id_notif_config_add){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_add);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_add = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_add, $groups_add, $users_add);
			
			// EDIT
			$data_notif_config_admin["event"] = "uf_edit_element";
			$data_notif_config_admin["email_notification"] = $notification_email_edit;
			$data_notif_config_admin["web_notification"] = $notification_web_edit;
	
			if($id_notif_config_edit){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_edit);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_edit = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_edit, $groups_edit, $users_edit);
			
			// DELETE
			$data_notif_config_admin["event"] = "uf_delete_element";
			$data_notif_config_admin["email_notification"] = $notification_email_delete;
			$data_notif_config_admin["web_notification"] = $notification_web_delete;
	
			if($id_notif_config_delete){
				$data_notif_config_admin["modified_by"] = $this->login_user->id;
				$data_notif_config_admin["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin, $id_notif_config_delete);
			} else {
				$data_notif_config_admin["created_by"] = $this->login_user->id;
				$data_notif_config_admin["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config_admin);
			}
			
			$save_id_notif_config_delete = $save_id;
			$this->_save_notification_config_admin_groups_and_users($save_id, $id_notif_config_delete, $groups_delete, $users_delete);
			
			$config_icon_add = ($notification_email_add || $notification_web_add) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			$config_icon_edit = ($notification_email_edit || $notification_web_edit) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			$config_icon_delete = ($notification_email_delete || $notification_web_delete) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
			
			$btn_action_attributes = array(
				"class" => "edit", 
				"title" => lang('notification_settings'), 
				"data-post-id_modulo" => $id_modulo, 
				"data-post-id_submodulo" => $id_submodulo, 
				"data-post-item" => $item, 
				"data-post-id_client" => $id_client,
				"data-post-id_project" => $id_project,
				"data-post-id_notif_config_add" => $save_id_notif_config_add, 
				"data-post-id_notif_config_edit" => $save_id_notif_config_edit, 
				"data-post-id_notif_config_delete" => $save_id_notif_config_delete, 
			);
			
			$btn_action = modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", $btn_action_attributes);
			
			if ($save_id) {
				echo json_encode(array(
					"success" => true, 
					'item' => $item,  
					'config_icon_add' => $config_icon_add,
					'config_icon_edit' => $config_icon_edit, 
					'config_icon_delete' => $config_icon_delete,
					'btn_action' => $btn_action, 
					'message' => lang('record_saved')
				));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}
			
		} else {
		
			$id_notif_config = $this->input->post("id_notif_config");
			$notification_type = $this->input->post("notification_type");
			$groups = (array)$this->input->post("groups");
			$users = (array)$this->input->post("users");
			$email_notification = ($this->input->post("notification_email")) ? 1 : 0;
			$web_notification = ($this->input->post("notification_web")) ? 1 : 0;
			
			$data_notif_config = array(
				"id_client" => $id_client,
				"id_project" => $id_project,
				"id_admin_module" => $id_modulo,
				"id_admin_submodule" => $id_submodulo,
				"event" => $notification_type,
				"email_notification" => $email_notification,
				"web_notification" => $web_notification
			);
			
			if($id_notif_config){
				$data_notif_config["modified_by"] = $this->login_user->id;
				$data_notif_config["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config, $id_notif_config);
			} else {
				$data_notif_config["created_by"] = $this->login_user->id;
				$data_notif_config["created"] = get_current_utc_time();
				$save_id = $this->AYN_Notif_projects_admin_model->save($data_notif_config);
			}
			
	
			if($id_notif_config){ // Si existe configuración
				
				// Grupos
				$notif_config_groups = $this->AYN_Notif_projects_admin_groups_model->get_all_where(array(
					"id_notif_projects_admin" => $id_notif_config,
					"deleted" => 0
				))->result_array();
				
				$array_notif_config_groups_ids = array();
				foreach($notif_config_groups as $notif_config_group){
					$array_notif_config_groups_ids[$notif_config_group["id"]] = $notif_config_group["id_client_group"];
				}
				
				if(count($array_notif_config_groups_ids)){ // Si hay grupos seteados
					if(count($groups)){ // Si vienen grupos
						foreach($groups as $id_group){
							if(!in_array($id_group, $array_notif_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
								$data_notif_config_client_groups = array(
									"id_notif_projects_admin" => $save_id,
									"id_client_group" => $id_group
								);
								$data_notif_config_client_groups["created_by"] = $this->login_user->id;
								$data_notif_config_client_groups["created"] = get_current_utc_time();
								$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
							}
						}
						foreach($array_notif_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
							if(!in_array($id_group, $groups)){
								$this->AYN_Notif_projects_admin_groups_model->delete($id);
							}
						}
					} else { // Si no vienen grupos, eliminar los seteados
						foreach($notif_config_groups as $notif_config_group){
							$this->AYN_Notif_projects_admin_groups_model->delete($notif_config_group["id"]);
						}
					}
				} else { // Si no hay grupos seteados
					if(count($groups)){ // Si vienen grupos, agregarlos
						foreach($groups as $id_group){
							$data_notif_config_client_groups = array(
								"id_notif_projects_admin" => $save_id,
								"id_client_group" => $id_group
							);
							$data_notif_config_client_groups["created_by"] = $this->login_user->id;
							$data_notif_config_client_groups["created"] = get_current_utc_time();
							$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
						}
					}
				}
				
				
				// Usuarios
				$notif_config_users = $this->AYN_Notif_projects_admin_users_model->get_all_where(array(
					"id_notif_projects_admin" => $id_notif_config,
					"deleted" => 0
				))->result_array();
				
				$array_notif_config_users_ids = array();
				foreach($notif_config_users as $notif_config_user){
					$array_notif_config_users_ids[$notif_config_user["id"]] = $notif_config_user["id_user"];
				}
				
				if(count($array_notif_config_users_ids)){ // Si hay usuarios seteados
					if(count($users)){ // Si vienen usuarios
						foreach($users as $id_user){
							if(!in_array($id_user, $array_notif_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
								$data_notif_config_client_users = array(
									"id_notif_projects_admin" => $save_id,
									"id_user" => $id_user
								);
								$data_notif_config_client_users["created_by"] = $this->login_user->id;
								$data_notif_config_client_users["created"] = get_current_utc_time();
								$save_user_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_users);
							}
						}
						foreach($array_notif_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
							if(!in_array($id_user, $users)){
								$this->AYN_Notif_projects_admin_users_model->delete($id);
							}
						}
					} else { // Si no vienen usuarios, eliminar los seteados
						foreach($notif_config_users as $notif_config_user){
							$this->AYN_Notif_projects_admin_users_model->delete($notif_config_user["id"]);
						}
					}
				} else { // Si no hay usuarios seteados
					if(count($users)){ // Si vienen grupos, agregarlos
						foreach($users as $id_user){
							$data_notif_config_client_users = array(
								"id_notif_projects_admin" => $save_id,
								"id_user" => $id_user
							);
							$data_notif_config_client_users["created_by"] = $this->login_user->id;
							$data_notif_config_client_users["created"] = get_current_utc_time();
							$save_group_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_users);
						}
					}
				}
				
			} else { // Si no existe configuración
				
				if(count($groups)){ // Si vienen grupos, agregarlos
					foreach($groups as $id_group){
						$data_notif_config_client_groups = array(
							"id_notif_projects_admin" => $save_id,
							"id_client_group" => $id_group
						);
						$data_notif_config_client_groups["created_by"] = $this->login_user->id;
						$data_notif_config_client_groups["created"] = get_current_utc_time();
						$save_group_id = $this->AYN_Notif_projects_admin_groups_model->save($data_notif_config_client_groups);
					}
				}
				
				if(count($users)){ // Si vienen grupos, agregarlos
					foreach($users as $id_user){
						$data_notif_config_client_userss = array(
							"id_notif_projects_admin" => $save_id,
							"id_user" => $id_user
						);
						$data_notif_config_client_userss["created_by"] = $this->login_user->id;
						$data_notif_config_client_userss["created"] = get_current_utc_time();
						$save_user_id = $this->AYN_Notif_projects_admin_users_model->save($data_notif_config_client_userss);
					}
				}
				
			}
			
			if ($save_id) {
							
				$config_icon = ($email_notification || $web_notification) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
				$id_item_config = $id_modulo."-".$id_submodulo."-".$notification_type;
				$btn_action = modal_anchor(get_uri("general_settings/modal_form_notification_config_admin"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('notification_settings'), "data-post-id_modulo" => $id_modulo, "data-post-id_submodulo" => $id_submodulo, "data-post-notification_type" => $notification_type, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-id_notif_config" => $save_id));
				
				echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'config_icon' => $config_icon, 'btn_action' => $btn_action, 'message' => lang('record_saved')));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}
		
		}
		
	}
	
	function modal_form_alert_config_users(){
				
		$id_alert_project_config = $this->input->post('id_alert_config');
        $id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$nombre_item = $this->input->post('nombre_item');
		$id_modulo = $this->input->post('id_modulo');
		$id_submodulo = ($this->input->post('id_submodulo')) ? $this->input->post('id_submodulo') : 0;
		
		$view_data["id_alert_config"] = $id_alert_project_config;
		$view_data["id_client"] = $id_client;
		$view_data["id_project"] = $id_project;
		$view_data["nombre_item"] = $nombre_item;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;	
		
		if($id_modulo == "2"){ // Registros Ambientales
			
			$id_categoria = $this->input->post('id_categoria');
			$id_unidad = $this->input->post('id_unidad');
			$id_tipo_unidad = $this->input->post('id_tipo_unidad');
			$view_data["id_categoria"] = $id_categoria;
			$view_data["id_unidad"] = $id_unidad;
			$view_data["id_tipo_unidad"] = $id_tipo_unidad;
			$view_data["nombre_unidad"] = $this->input->post('nombre_unidad');
			
		} elseif($id_modulo == "6"){ // Compromisos
			
			$view_data["id_planificacion"] = $this->input->post('id_planificacion');
					
			$tipo_evaluacion = $this->input->post('tipo_evaluacion');
			$view_data["tipo_evaluacion"] = $tipo_evaluacion;
			
			if($id_submodulo == "4"){ // Evaluación de Compromisos RCA
				$view_data["estados"] = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(
					array("nombre_estado", "color"), 
					"id", 
					array("id_cliente" => $id_client, "tipo_evaluacion" => "rca")
				);	
			} elseif($id_submodulo == "22"){ // Evaluación de Compromisos Reportables
				$view_data["estados"] = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(
					array("nombre_estado", "color"), 
					"id", 
					array("id_cliente" => $id_client, "tipo_evaluacion" => "reportable")
				);
			}
			
		} elseif($id_modulo == "7"){ // Permisos
			
			$id_valor_permiso = $this->input->post('id_valor_permiso');
			$view_data["id_valor_permiso"] = $id_valor_permiso;
			$view_data["estados"] = array("" => "-") + $this->Permitting_procedure_status_model->get_dropdown_list(
				array("nombre_estado", "color"), 
				"id", 
				array("id_cliente" => $id_client)
			);
			
		}
		
		if($id_alert_project_config){
			
			$alert_project_config = $this->AYN_Alert_projects_model->get_one($id_alert_project_config);
			$alert_config = json_decode($alert_project_config->alert_config, TRUE);
			
			$view_data["risk_value"] = $alert_config["risk_value"];
			$view_data["threshold_value"] = $alert_config["threshold_value"];
			
			$view_data["is_risk_email_alert"] = $alert_project_config->risk_email_alert;
			$view_data["is_risk_web_alert"] = $alert_project_config->risk_web_alert;
			$view_data["is_threshold_email_alert"] = $alert_project_config->threshold_email_alert;
			$view_data["is_threshold_web_alert"] = $alert_project_config->threshold_web_alert;
			
			$alert_config_groups = $this->AYN_Alert_projects_groups_model->get_all_where(array(
				"id_alert_project" => $id_alert_project_config,
				"deleted" => 0
			))->result();
			
			$selected_client_groups = array();
			foreach($alert_config_groups as $alert_config_group){
				$selected_client_groups[] = $alert_config_group->id_client_group;
			}
			$view_data["selected_client_groups"] = $selected_client_groups;
			
			$alert_config_users = $this->AYN_Alert_projects_users_model->get_all_where(array(
				"id_alert_project" => $id_alert_project_config,
				"deleted" => 0
			))->result();
			
			$selected_client_users = array();
			foreach($alert_config_users as $alert_config_user){
				$selected_client_users[] = $alert_config_user->id_user;
			}
			$view_data["selected_client_users"] = $selected_client_users;
			
		}
		
		$modulo = "-";
		$submodulo = "-";
		$modulo = $this->Clients_modules_model->get_one($id_modulo)->name;
		$submodulo = $this->Clients_submodules_model->get_one($id_submodulo)->name;
		
		// Grupos de Cliente
		$array_client_groups = array();
		$client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
			"id_client" => $id_client,
			"deleted" => 0
		))->result();
		
		foreach($client_groups as $client_group){
			$array_client_groups[$client_group->id] = $client_group->group_name;
		}
		
		// Usuarios de Cliente miembros del Proyecto seleccionado
		$array_client_users = array();
		$project_members = $this->Project_members_model->get_all_where(array(
			"project_id" => $id_project,
			"deleted" => 0
		))->result();
		
		foreach($project_members as $project_member){
			$user = $this->Users_model->get_one($project_member->user_id);
			$array_client_users[$user->id] = $user->first_name." ".$user->last_name;
		}
		
		$view_data["modulo"] = $modulo;
		$view_data["submodulo"] = $submodulo;
		$view_data["array_client_groups"] = $array_client_groups;
		$view_data["array_client_users"] = $array_client_users;
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		
		$this->load->view('general_settings/alert_settings_users/modal_form', $view_data);
		
	}
	
	function save_alert_config_users(){
						
		$id_alert_config = $this->input->post("id_alert_config");
		$id_client = $this->input->post("id_client");
		$id_project = $this->input->post("id_project");
		$id_modulo = $this->input->post("id_modulo");
		$id_submodulo = $this->input->post("id_submodulo");
		
		$nombre_item = $this->input->post("nombre_item");

		$risk_email_alert = ($this->input->post("risk_email_alert")) ? 1 : 0;
		$risk_web_alert = ($this->input->post("risk_web_alert")) ? 1 : 0;
		
		$threshold_email_alert = ($this->input->post("threshold_email_alert")) ? 1 : 0;
		$threshold_web_alert = ($this->input->post("threshold_web_alert")) ? 1 : 0;
		
		$groups = (array)$this->input->post("groups");
		$users = (array)$this->input->post("users");
		
		$risk_value = $this->input->post("risk_value");
		$threshold_value = $this->input->post("threshold_value");
		
		if($id_modulo == "2"){ // Registros Ambientales
			
			$id_categoria = $this->input->post("id_categoria");
			$id_tipo_unidad = $this->input->post("id_tipo_unidad");
			$id_unidad = $this->input->post("id_unidad");
			$alert_config = array(
				"id_categoria" => $id_categoria,
				"id_tipo_unidad" => $id_tipo_unidad,
				"id_unidad" => $id_unidad,
				"risk_value" => $risk_value,
				"threshold_value" => $threshold_value
			);
			
		} elseif($id_modulo == "6"){ // Compromisos
			
			$id_planificacion = $this->input->post("id_planificacion");
			$tipo_evaluacion = $this->input->post("tipo_evaluacion");
			
			if($id_planificacion){
				$alert_config = array(
					"id_planificacion" => $id_planificacion,
					"risk_value" => $risk_value,
					"threshold_value" => $threshold_value
				);
			} else {
				$alert_config = array(
					"tipo_evaluacion" => $tipo_evaluacion,
					"risk_value" => $risk_value,
					"threshold_value" => $threshold_value
				);
			}

		} elseif($id_modulo == "7"){ // Permisos
			
			$alert_config = array(
				"risk_value" => $risk_value,
				"threshold_value" => $threshold_value
			);
			
		}
		
		$data_alert_config = array(
			"id_client" => $id_client,
			"id_project" => $id_project,
			"id_client_module" => $id_modulo,
			"id_client_submodule" => $id_submodulo,
			"alert_config" => json_encode($alert_config, TRUE),
			"risk_email_alert" => $risk_email_alert,
			"risk_web_alert" => $risk_web_alert,
			"threshold_email_alert" => $threshold_email_alert,
			"threshold_web_alert" => $threshold_web_alert,
		);
		
		// Validaciones
		if($id_modulo == "2"){ // Registros Ambientales
			
			if(($risk_value != "" || $risk_value != NULL) && $threshold_value != "" || $risk_value != NULL){
				if($risk_value >= $threshold_value){
					echo json_encode(array("success" => false, 'message' => lang("umbral_higher_than_threshold_msj")));
					exit();
				}
			}
			
		} elseif($id_modulo == "6" || $id_modulo == "7"){ // Compromisos || Permisos
			if(!$id_planificacion){
				if(($risk_value != "" || $risk_value != NULL) && $threshold_value != "" || $risk_value != NULL){
					if($risk_value == $threshold_value){
						echo json_encode(array("success" => false, 'message' => lang("umbral_threshold_distinct_msj")));
						exit();
					}
				}
			} else { // Compromisos - Planificación Reportables
				if(($risk_value != "" || $risk_value != NULL) && $threshold_value != "" || $risk_value != NULL){
					if($risk_value <= $threshold_value){
						echo json_encode(array("success" => false, 'message' => lang("threshold_higher_than_umbral_msj")));
						exit();
					}
				}
			}
		}
		
		if($id_alert_config){
			$data_alert_config["modified_by"] = $this->login_user->id;
			$data_alert_config["modified"] = get_current_utc_time();
			$save_id = $this->AYN_Alert_projects_model->save($data_alert_config, $id_alert_config);
		} else {
			$data_alert_config["created_by"] = $this->login_user->id;
			$data_alert_config["created"] = get_current_utc_time();
			$save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
		}
		
		if($id_alert_config){ // Update
			
			// Grupos
			$alert_config_groups = $this->AYN_Alert_projects_groups_model->get_all_where(array(
				"id_alert_project" => $id_alert_config,
				"deleted" => 0
			))->result_array();
			
			$array_alert_config_groups_ids = array();
			foreach($alert_config_groups as $alert_config_group){
				$array_alert_config_groups_ids[$alert_config_group["id"]] = $alert_config_group["id_client_group"];
			}
			
			if(count($array_alert_config_groups_ids)){ // Si hay grupos seteados
				if(count($groups)){ // Si vienen grupos
					foreach($groups as $id_group){
						if(!in_array($id_group, $array_alert_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
							$data_alert_config_groups = array(
								"id_alert_project" => $save_id,
								"id_client_group" => $id_group,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
						}
					}
					foreach($array_alert_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
						if(!in_array($id_group, $groups)){
							$this->AYN_Alert_projects_groups_model->delete($id);
						}
					}
				} else { // Si no vienen grupos, eliminar los seteados
					foreach($alert_config_groups as $alert_config_group){
						$this->AYN_Alert_projects_groups_model->delete($alert_config_group["id"]);
					}
				}
			} else { // Si no hay grupos seteados
				if(count($groups)){ // Si vienen grupos, agregarlos
				
					foreach($groups as $id_group){
						$data_alert_config_groups = array(
							"id_alert_project" => $save_id,
							"id_client_group" => $id_group,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
					}
				}
			}
			
			
			// Usuarios
			$alert_config_users = $this->AYN_Alert_projects_users_model->get_all_where(array(
				"id_alert_project" => $id_alert_config,
				"deleted" => 0
			))->result_array();
			
			$array_alert_config_users_ids = array();
			foreach($alert_config_users as $alert_config_user){
				$array_alert_config_users_ids[$alert_config_user["id"]] = $alert_config_user["id_user"];
			}
			
			if(count($array_alert_config_users_ids)){ // Si hay usuarios seteados
				if(count($users)){ // Si vienen usuarios
					foreach($users as $id_user){
						if(!in_array($id_user, $array_alert_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
							$data_alert_config_users = array(
								"id_alert_project" => $save_id,
								"id_user" => $id_user,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
						}
					}
					foreach($array_alert_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
						if(!in_array($id_user, $users)){
							$this->AYN_Alert_projects_users_model->delete($id);
						}
					}
				} else {  // Si no vienen usuarios, eliminar los seteados
					foreach($alert_config_users as $alert_config_user){
						$this->AYN_Alert_projects_users_model->delete($alert_config_user["id"]);
					}
				}
			} else {  // Si no hay usuarios seteados
				if(count($users)){ // Si vienen usuarios, agregarlos
					foreach($users as $id_user){
						$data_alert_config_users = array(
							"id_alert_project" => $save_id,
							"id_user" => $id_user,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
					}
				}
			}
			
			// Caso especial en Alertas para Planificación de Compromisos Reportables
			// Si se edita una configuración perteneciente a una planificación de Compromiso Reportable, se deben actualizar los usuarios
			// del histórico con los usuarios asociados a esa configuración.
			if($id_modulo == "6" && $id_submodulo == "22" && $id_planificacion){
				
				// Buscar histórico asociado a la configuración
				$alert_historical = $this->AYN_Alert_historical_model->get_one_where(array(
					"id_alert_projects" => $id_alert_config,
					"deleted" => 0
				));
				
				// Buscar usuarios asociados al histórico
				$alert_historical_users = $this->AYN_Alert_historical_users_model->get_all_where(array(
					"id_alert_historical" => $alert_historical->id,
					"deleted" => 0
				))->result_array();
				
				$array_alert_historical_users_ids = array();
				foreach($alert_historical_users as $historical_user){
					$array_alert_historical_users_ids[$historical_user["id"]] = $historical_user["id_user"];
				}
				
				// Buscar usuarios asociados a la configuración
				// Los usuarios asociados a la configuración están en el array $users ya que estos vienen por Post desde
				// la vista de configuración y son los usuarios que pertenecen a la configuración.
				
				// Si el histórico tiene usuarios asociados, actualizarlos o igualarlos con los usuarios de la configuración.
				if(count($array_alert_historical_users_ids)){
					
					if(count($users)){ // usuarios de la configuración.
						foreach($users as $id_user){
							if(!in_array($id_user, $array_alert_historical_users_ids)){ // Si un usuario de la configuración no está en el histórico, agregarlo
								$data_alert_historical_users = array(
									"id_alert_historical" => $alert_historical->id,
									"id_user" => $id_user
								);
								$save_user_id = $this->AYN_Alert_historical_users_model->save($data_alert_historical_users);
							}
						}
						foreach($array_alert_historical_users_ids as $id => $id_user){ // Si un usuario que está en el histórico no está en la configuración, borrarlo
							if(!in_array($id_user, $users)){
								$this->AYN_Alert_historical_users_model->delete($id);
							}
						}
					} else {  // Si no vienen usuarios, eliminar los del histórico
						foreach($alert_historical_users as $alert_historical_user){
							$this->AYN_Alert_historical_users_model->delete($alert_historical_user["id"]);
						}
					}
				} else { // Si no hay usuarios en el histórico, agregarle los usuarios que tiene la configuración.
				
					if(count($users)){ // Si vienen usuarios, agregarlos
						
						foreach($users as $id_user){
							$data_alert_historical_users = array(
								"id_alert_historical" => $alert_historical->id,
								"id_user" => $id_user
							);							
							$save_user_id = $this->AYN_Alert_historical_users_model->save($data_alert_historical_users);
						}
						
					}
				
				}
				
			}

		} else { // Si no existe configuración
			if(count($groups)){ // Si vienen grupos, agregarlos
				foreach($groups as $id_group){
					$data_alert_config_groups = array(
						"id_alert_project" => $save_id,
						"id_client_group" => $id_group,
						"created_by" => $this->login_user->id,
						"created" => get_current_utc_time()
					);
					$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
				}
			}
			if(count($users)){ // Si vienen grupos, agregarlos
				foreach($users as $id_user){
					$data_alert_config_users = array(
						"id_alert_project" => $save_id,
						"id_user" => $id_user,
						"created_by" => $this->login_user->id,
						"created" => get_current_utc_time()
					);
					$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
				}
			}
		}
		
		if ($save_id) {
						
			$config_icon = ($risk_email_alert || $risk_web_alert || $threshold_email_alert || $threshold_web_alert) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
						
			if($id_modulo == "2"){ // Registros Ambientales
				
				$risk_value = ($risk_value || $risk_value != "") ? $risk_value : "-";
				$threshold_value = ($threshold_value || $threshold_value != "") ? $threshold_value : "-";
				
				$nombre_unidad = $this->input->post("nombre_unidad");
				$id_item_config = $id_modulo."-".$id_submodulo."-".$id_categoria."-".$id_tipo_unidad;
				$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_modulo, "data-post-id_submodulo" => $id_submodulo, "data-post-id_categoria" => $id_categoria, "data-post-id_tipo_unidad" => $id_tipo_unidad, "data-post-id_unidad" => $id_unidad, "data-post-nombre_item" => $nombre_item, "data-post-nombre_unidad" => $nombre_unidad, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-id_alert_config" => $save_id));
				echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'risk_value' => $risk_value, 'threshold_value' => $threshold_value, 'config_icon' => $config_icon, 'btn_action' => $btn_action, 'message' => lang('record_saved')));

			} elseif($id_modulo == "6"){ // Compromisos
				
				if($id_planificacion){
					
					$risk_value = ($risk_value) ? $risk_value : "-";
					$threshold_value = ($threshold_value) ? $threshold_value : "-";
					
					$id_item_config = $id_modulo."-".$id_submodulo."-".$id_planificacion."-planification";
					$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_modulo, "data-post-id_submodulo" => $id_submodulo, "data-post-nombre_item" => $nombre_item, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project,  "data-post-id_planificacion" => $id_planificacion, "data-post-id_alert_config" => $save_id));
					echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'risk_value' => $risk_value, 'threshold_value' => $threshold_value, 'config_icon' => $config_icon, 'btn_action' => $btn_action, 'message' => lang('record_saved')));
					
				} else {
					
					$estado_risk_value = $this->Compromises_compliance_status_model->get_one($risk_value);
					$nombre_estado_risk_value = $estado_risk_value->nombre_estado;
					$color_estado_risk_value = $estado_risk_value->color;
					if($estado_risk_value->id){
						$html_estado_risk_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_risk_value .= '<div style="background-color:'.$color_estado_risk_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_risk_value .= $nombre_estado_risk_value;
						$html_estado_risk_value .= '</div>';
					} else {
						$html_estado_risk_value = "-";
					}
					
					$estado_threshold_value = $this->Compromises_compliance_status_model->get_one($threshold_value);
					$nombre_estado_threshold_value = $estado_threshold_value->nombre_estado;
					$color_estado_threshold_value = $estado_threshold_value->color;
					if($estado_threshold_value->id){
						$html_estado_threshold_value = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado_threshold_value .= '<div style="background-color:'.$color_estado_threshold_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado_threshold_value .= $nombre_estado_threshold_value;
						$html_estado_threshold_value .= '</div>';
					} else {
						$html_estado_threshold_value = "-";
					}
					
					$id_item_config = $id_modulo."-".$id_submodulo."-".$tipo_evaluacion;
					$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_modulo, "data-post-id_submodulo" => $id_submodulo, "data-post-nombre_item" => $nombre_item, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-tipo_evaluacion" => $tipo_evaluacion, "data-post-id_alert_config" => $save_id));
					echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'risk_value' => $html_estado_risk_value, 'threshold_value' => $html_estado_threshold_value, 'config_icon' => $config_icon, 'btn_action' => $btn_action, 'message' => lang('record_saved')));
					
				}
				
			} elseif($id_modulo == "7"){ // Permisos
				
				$estado_risk_value = $this->Permitting_procedure_status_model->get_one($risk_value);
				$nombre_estado_risk_value = $estado_risk_value->nombre_estado;
				$color_estado_risk_value = $estado_risk_value->color;
				if($estado_risk_value->id){
					$html_estado_risk_value = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_risk_value .= '<div style="background-color:'.$color_estado_risk_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_risk_value .= $nombre_estado_risk_value;
					$html_estado_risk_value .= '</div>';
				} else {
					$html_estado_risk_value = "-";
				}
				
				$estado_threshold_value = $this->Permitting_procedure_status_model->get_one($threshold_value);
				$nombre_estado_threshold_value = $estado_threshold_value->nombre_estado;
				$color_estado_threshold_value = $estado_threshold_value->color;
				if($estado_threshold_value->id){
					$html_estado_threshold_value = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado_threshold_value .= '<div style="background-color:'.$color_estado_threshold_value.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado_threshold_value .= $nombre_estado_threshold_value;
					$html_estado_threshold_value .= '</div>';
				} else {
					$html_estado_threshold_value = "-";
				}
				
				$id_item_config = $id_modulo."-".$id_submodulo."-permitting";
				$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_users"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_modulo, "data-post-id_submodulo" => $id_submodulo, "data-post-nombre_item" => $nombre_item, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-id_alert_config" => $save_id));				
				echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'risk_value' => $html_estado_risk_value, 'threshold_value' => $html_estado_threshold_value, 'config_icon' => $config_icon, 'btn_action' => $btn_action, 'message' => lang('record_saved')));
	
			}
			
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	/* Guardar configuración general a nivel de cliente */
	function save_general_settings_client(){
		
		$id = $this->input->post("id_general_setting");

		$data_general_settings = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"thousands_separator" => $this->input->post('thousands_separator'),
			"decimals_separator" => $this->input->post('decimals_separator'),
			"decimal_numbers" => $this->input->post('decimal_numbers_config'),
			"date_format" => $this->input->post('date_format'),
			"timezone" => $this->input->post('timezone'),
			"time_format" => $this->input->post("time_format"),
		);
		
		$save_id = $this->General_settings_clients_model->save($data_general_settings, $id);

		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	function get_global_settings(){
		
		$tab_view['active_tab'] = "general";
				
		$html = "";
		$html .= '<div id="page-content" class="p20 pt0 row">';
		$html .= 	'<div class="col-sm-3 col-lg-2">';
    	$html .= 		$this->load->view("general_settings/tabs_global", $tab_view, TRUE);
		$html .= 	'</div>';
		$html .= 	'<div role="tabpanel" class="tab-pane fade active in">';
		$html .= 		'<div class="tab-content">';
		$html .= 			'<div id="general" class="tab-pane fade in active">';
		$html .= 				$this->load->view('general_settings/global/general', "", TRUE);
		$html .= 			'</div>';
		$html .= 			'<div id="email" class="tab-pane fade">';
		$html .= 				$this->load->view('general_settings/global/email', "", TRUE);
		$html .= 			'</div>';
		$html .= 			'<div id="email_templates" class="tab-pane fade">';
        $html .=             	$this->load->view('general_settings/global/email_templates', "", TRUE);
        $html .=       	 	'</div>';
		$html .= 		'</div>';
		$html .= 	'</div>';
		$html .= '</div>';
				
		echo $html;		
		
	}
	
	function save_global_email_settings(){
		
		$settings = array("email_sent_from_address", "email_sent_from_name", "email_protocol", "email_smtp_host", "email_smtp_port", "email_smtp_user", "email_smtp_pass", "email_smtp_security_type");
		
		foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (!$value) {
                $value = "";
            }
            $this->Settings_model->save_setting($setting, $value);
        }

        $test_email_to = $this->input->post("send_test_mail_to");
		
        if ($test_email_to) {
            $email_config = Array(
                'charset' => 'utf-8',
                'mailtype' => 'html'
            );
            if ($this->input->post("email_protocol") === "smtp") {
                $email_config["protocol"] = "smtp";
                $email_config["smtp_host"] = $this->input->post("email_smtp_host");
                $email_config["smtp_port"] = $this->input->post("email_smtp_port");
                $email_config["smtp_user"] = $this->input->post("email_smtp_user");
                $email_config["smtp_pass"] = $this->input->post("email_smtp_pass");
                $email_config["smtp_crypto"] = $this->input->post("email_smtp_security_type");
            }

            $this->load->library('email', $email_config);
            $this->email->set_newline("\r\n");
            $this->email->from($this->input->post("email_sent_from_address"), $this->input->post("email_sent_from_name"));

            $this->email->to($test_email_to);
            $this->email->subject(lang("test_message"));
            $this->email->message(lang("test_message_text"));

            if ($this->email->send()) {
                echo json_encode(array("success" => true, 'message' => lang('test_mail_sent')));
                return false;
            } else {
                echo json_encode(array("success" => false, 'message' => lang('test_mail_send_failed')));
                show_error($this->email->print_debugger());
                return false;
            }
        }
        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
		
	}

	function save_report_units_settings_clients(){
		
		$id_cliente = $this->input->post("id_cliente_report_units_settings");
		$unidades = $this->input->post("unidad_report_units_settings");
		
		$data_report_units = array();
		$delete_settings = $this->Reports_units_settings_clients_model->delete_reports_units_settings($id_cliente);
		
		foreach($unidades as $key => $id_unidad){
			$id_unidad = (int)$id_unidad;
			$data_report_units["id_cliente"] = $id_cliente;
			$data_report_units["id_tipo_unidad"] = $key;
			$data_report_units["id_unidad"] = $id_unidad;
			$save_id = $this->Reports_units_settings_clients_model->save($data_report_units);
		}
		
		if($save_id){
			echo json_encode(array("success" => true, "save_id" => $save_id, 'message' => lang('settings_updated')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	/*
	function get_unit_type_label(){
		
		$id_tipo_unidad = $this->input->post("id_tipo_unidad");
		$id_categoria = $this->input->post("id_categoria");

		if($id_tipo_unidad == "2"){ // Volumen
			$html = "t/m3";
		} elseif ($id_tipo_unidad == "3"){ // Transporte
			$html = "t/tkm";
		} elseif ($id_tipo_unidad == "4"){ // Energía
			
			$html = "<div class='col-md-4' style='padding: 0px;'>";
			$html .= "t/Mwh";
			$html .= "</div>";
			$html .= "<div class='col-md-4' style='padding: 0px;'>";
			$html .= form_input(array(
						"id" => "ren-".$id_categoria,
						"name" => "ren[$id_categoria]",
						"value" => "",
						"class" => "form-control",
						"placeholder" => lang('ren'),
						"autofocus" => true,
						//"data-rule-required" => true,
						//"data-msg-required" => lang("field_required"),
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
						"autocomplete" => "off",
						//"maxlength" => "255"
					));
			$html .= "</div>";
			$html .= "<div class='col-md-4 text-left'>";
			$html .= "%";
			$html .= "</div>";
			
		} elseif ($id_tipo_unidad == "9"){ // Unidad
			$html = "t";
		} else {
			$html = "";
		}
		
		echo $html;

	}
	*/
	
	function save_global_general_settings(){
		
		$settings = array("max_file_size"); // Agregar nuevas configuraciones
		
		foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (!$value) {
                $value = "";
            }
            $this->Settings_model->save_setting($setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
		
	}


	function modal_form_alert_config_air($accordeon = ""){
				
		$id_alert_config = $this->input->post('id_alert_config');
        $id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$id_modulo = $this->input->post('id_modulo');
		$id_submodulo = ($this->input->post('id_submodulo')) ? $this->input->post('id_submodulo') : 0;
		$id_air_station = $this->input->post('id_air_station');
		$id_air_sector = $this->input->post('id_air_sector');
		$id_air_variable = $this->input->post('id_air_variable');
		$name_sector = $this->input->post('name_sector');
		$name_variable = $this->input->post('name_variable');

		validate_submitted_data(array(
			"id" => "numeric"
		));
		
		$view_data["id_alert_config"] = $id_alert_config;
		$view_data["id_client"] = $id_client;
		$view_data["id_project"] = $id_project;
		$view_data["id_modulo"] = $id_modulo;
		$view_data["id_submodulo"] = $id_submodulo;
		$view_data["id_air_station"] = $id_air_station;
		$view_data["id_air_sector"] = $id_air_sector;
		$view_data["id_air_variable"] = $id_air_variable;
		$view_data["name_sector"] = $name_sector;
		$view_data["name_variable"] = $name_variable;	
		
		$view_data['label_column'] = "col-md-3";
		$view_data['field_column'] = "col-md-9";

		$view_data['accordeon'] = $accordeon;

		$modulo = $this->Clients_modules_model->get_one($id_modulo);
		$submodulo = $this->Clients_submodules_model->get_one($id_submodulo);
		$view_data["modulo"] = ($modulo->id) ? $modulo->name : "-";
		$view_data["submodulo"] = ($submodulo->id) ? $submodulo->name : "-";

		if($accordeon == "forecast_alerts"){
			if($id_alert_config){
				$alert_projects_config_air = $this->AYN_Alert_projects_model->get_one($id_alert_config);
				$alert_config = json_decode($alert_projects_config_air->alert_config, TRUE);
				$alert_config = $alert_config["alert_config"];
				$view_data["array_data_alert_config"] = $alert_config;
			}
		}

		if($accordeon == "action_plan"){

			// Grupos de Cliente
			$array_client_groups = array();
			$client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
				"id_client" => $id_client,
				"deleted" => 0
			))->result();
			
			foreach($client_groups as $client_group){
				$array_client_groups[$client_group->id] = $client_group->group_name;
			}
			
			// Usuarios de Cliente miembros del Proyecto seleccionado
			$array_client_users = array();
			$project_members = $this->Project_members_model->get_all_where(array(
				"project_id" => $id_project,
				"deleted" => 0
			))->result();

			foreach($project_members as $project_member){
				$user = $this->Users_model->get_one($project_member->user_id);
				$array_client_users[$user->id] = $user->first_name." ".$user->last_name;
			}
			
			$view_data["array_client_groups"] = $array_client_groups;
			$view_data["array_client_users"] = $array_client_users;
			

			// Llamar a la configuración de Alertas de Pronóstico
			$config_options = array(
				"id_client" => $id_client,
				"id_project" => $id_project,
				"id_client_module" => $id_modulo, // Módulo de Pronóstico
				"id_client_submodule" => $id_submodulo, // Sin submódulo
				"alert_config" => array(
					"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
					"id_air_station" => $id_air_station,
					"id_air_sector" => $id_air_sector,
					"id_air_variable" => $id_air_variable
				),
			);

			$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			
			$alert_config = json_decode($alert_config_air_forecast_alerts->alert_config, TRUE);
			$alert_config_forecast_alerts = $alert_config["alert_config"];
			$view_data["alert_config_forecast_alerts"] = $alert_config_forecast_alerts;

			if($id_alert_config){
				
				// Grupos
				$alert_config_groups = $this->AYN_Alert_projects_groups_model->get_all_where(array(
					"id_alert_project" => $id_alert_config,
					"deleted" => 0
				))->result();
				
				$selected_client_groups = array();
				foreach($alert_config_groups as $alert_config_group){
					$selected_client_groups[] = $alert_config_group->id_client_group;
				}
				$view_data["selected_client_groups"] = $selected_client_groups;
				
				// Usuarios
				$alert_config_users = $this->AYN_Alert_projects_users_model->get_all_where(array(
					"id_alert_project" => $id_alert_config,
					"deleted" => 0
				))->result();
				
				$selected_client_users = array();
				foreach($alert_config_users as $alert_config_user){
					$selected_client_users[] = $alert_config_user->id_user;
				}
				$view_data["selected_client_users"] = $selected_client_users;

				// Configuraciones
				$alert_config_air_action_plan = $this->AYN_Alert_projects_model->get_alert_projects_config(array("id" => $id_alert_config))->row();
				$alert_config = json_decode($alert_config_air_action_plan->alert_config, TRUE);
				$alert_config = $alert_config["alert_config"];

				// Llamar a la configuración de Alertas de Pronóstico
				$config_options = array(
					"id_client" => $id_client,
					"id_project" => $id_project,
					"id_client_module" => $id_modulo, // Módulo de Pronóstico
					"id_client_submodule" => $id_submodulo, // Sin submódulo
					"alert_config" => array(
						"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
						"id_air_station" => $id_air_station,
						"id_air_sector" => $id_air_sector,
						"id_air_variable" => $id_air_variable
					),
				);

				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config, TRUE);
				$alert_config_forecast = $alert_config_forecast["alert_config"];
				$alert_config = array_intersect_key($alert_config, $alert_config_forecast);

				foreach($alert_config_forecast as $index => $config_forecast){
					$alert_config[$index]["nc_name"] = $config_forecast["nc_name"];
					$alert_config[$index]["min_value"] = $config_forecast["min_value"]; 
				}

				$view_data["alert_config_forecast_alerts"] = $alert_config;
	
			}

		}

		$this->load->view('general_settings/alert_settings_air/modal_form', $view_data);

	}

	function save_alert_config_air(){
		
		$id_alert_config = $this->input->post('id_alert_config');
		$id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$id_module = $this->input->post('id_module');
		$id_submodule = $this->input->post('id_submodule');
		$id_air_station = $this->input->post('id_air_station');
		$id_air_sector = $this->input->post('id_air_sector');
		$id_air_variable = $this->input->post('id_air_variable');

		$accordeon = $this->input->post('accordeon');
		
		if($accordeon == "forecast_alerts"){

			$nc_active = $this->input->post('nc_active');
			array_shift($nc_active);
			$nc_name = $this->input->post('nc_name');
			array_shift($nc_name);
			$nc_color = $this->input->post('nc_color');
			array_shift($nc_color);
			$min_value = $this->input->post('min_value');
			array_shift($min_value);
			//$max_value = $this->input->post('max_value');
			//array_shift($max_value);

			// Validar que los rangos (min_value y max_value) no se superpongan.
			/*
			$previous_max_val = NULL;
			foreach($min_value as $index => $row){
				if($previous_max_val != NULL){
					if($previous_max_val >= $min_value[$index]){
						echo json_encode(array("success" => false, 'message' => lang("overlapping_ranges_val_min_max_msj")));
						exit();
					}
				}
				$previous_max_val = $max_value[$index];
			}
			*/

			// Validar que el maximo sea mayor al minimo en cada rango
			/*
			foreach($min_value as $index => $row){
				$min_val = $min_value[$index];
				$max_val = $max_value[$index];
				if($min_val > $max_val){
					echo json_encode(array("success" => false, 'message' => lang("ranges_val_min_max_msj")));
					exit();
				}
			}
			*/

			// Validar que un valor mínimo no sea menor o igual al anterior
			$previous_min_val = NULL;
			foreach($min_value as $value){
				if($previous_min_val != NULL){
					if($previous_min_val >= $value){
						echo json_encode(array("success" => false, 'message' => lang("val_min_validation_msj")));
						exit();
					}
				}
				$previous_min_val = $value;
			}

			// Si hay al menos 1 fila creada y activada, se debe validar que existan mínimo 2 filas activadas: 
			// Ya que la fila 1 necesita tener un limite superior que se lo da el valor mínimo de la fila 2
			$n_activated_rows = 0;
			foreach($nc_active as $index => $row){
				if($row == "1"){
					$n_activated_rows++;
				}
			}
			if($n_activated_rows == 1){
				echo json_encode(array("success" => false, 'message' => lang("two_rows_activated_msj")));
				exit();
			} 
			
			$data_alert_config = array();
			$data_alert_config["air_config"] = $accordeon;
			$data_alert_config["id_air_station"] = $id_air_station;
			$data_alert_config["id_air_sector"] = $id_air_sector;
			$data_alert_config["id_air_variable"] = $id_air_variable;

			foreach($nc_active as $index => $row){
				$data_alert_config["alert_config"][] = array(
					"nc_active" => $nc_active[$index],
					"nc_name" => $nc_name[$index],
					"nc_color" => $nc_color[$index],
					"min_value" => $min_value[$index],
					//"max_value" => $max_value[$index],
				);
			}

			$data = array(
				"id_client" => $id_client,
				"id_project" => $id_project,
				"id_client_module" => $id_module,
				"id_client_submodule" => $id_submodule,
				"alert_config" => json_encode($data_alert_config)
			);

			if($id_alert_config){
				$data["modified_by"] = $this->login_user->id;
				$data["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Alert_projects_model->save($data, $id_alert_config);
			} else {
				$data["created_by"] = $this->login_user->id;
				$data["created"] = get_current_utc_time();
				$save_id = $this->AYN_Alert_projects_model->save($data);
			}

			if($save_id){

				$id_item_config = $id_module."-".$id_submodule."-".$id_air_station."-".$id_air_sector."-".$id_air_variable;

				$configured = FALSE;
				if(count($nc_active)){
					foreach($nc_active as $index => $row){
						if($nc_active[$index]){
							$configured = TRUE;
							break;
						}
					}
				}

				$array_min_values = array();
				foreach($data_alert_config["alert_config"] as $config){
					if($config["nc_active"] == "1"){
						$array_min_values[] = $config["min_value"];
					}
				}

				$min_value = (count($array_min_values)) ? min($array_min_values) : 0;
				$max_value = (count($array_min_values)) ? max($array_min_values) : 0;

				$config_icon_air = ($configured) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
				$air_sector = $this->Air_sectors_model->get_one($id_air_sector);
				$air_variable = $this->Air_variables_model->get_one($id_air_variable);

				$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_air/".$accordeon), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_module, "data-post-id_submodulo" => $id_submodule, "data-post-id_air_station" => $id_air_station, "data-post-id_air_sector" => $id_air_sector, "data-post-id_air_variable" => $id_air_variable, "data-post-name_sector" => $air_sector->name, "data-post-name_variable" => $air_variable->name, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-id_alert_config" => $save_id));				
				echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'min_value' => $min_value, 'max_value' => $max_value, 'config_icon' => $config_icon_air, 'btn_action' => $btn_action, 'message' => lang('record_saved')));

			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}


		}

		if($accordeon == "action_plan"){
			
			$ap_active = $this->input->post('ap_active');
			$ap_action_plan = $this->input->post('ap_action_plan');
			$ap_email = $this->input->post('ap_email');
			$ap_web = $this->input->post('ap_web');

			$groups = (array)$this->input->post("groups");
			$users = (array)$this->input->post("users");

			$data_alert_config = array();
			$data_alert_config["air_config"] = $accordeon;
			$data_alert_config["id_air_station"] = $id_air_station;
			$data_alert_config["id_air_sector"] = $id_air_sector;
			$data_alert_config["id_air_variable"] = $id_air_variable;

			foreach($ap_active as $index => $row){
				$data_alert_config["alert_config"][] = array(
					"ap_active" => $ap_active[$index],
					"ap_action_plan" => $ap_action_plan[$index],
					"ap_email" => $ap_email[$index],
					"ap_web" => $ap_web[$index],
				);
			}

			$data = array(
				"id_client" => $id_client,
				"id_project" => $id_project,
				"id_client_module" => $id_module,
				"id_client_submodule" => $id_submodule,
				"alert_config" => json_encode($data_alert_config)
			);

			if($id_alert_config){

				$data["modified_by"] = $this->login_user->id;
				$data["modified"] = get_current_utc_time();
				$save_id = $this->AYN_Alert_projects_model->save($data, $id_alert_config);

				// Grupos
				$alert_config_groups = $this->AYN_Alert_projects_groups_model->get_all_where(array(
					"id_alert_project" => $id_alert_config,
					"deleted" => 0
				))->result_array();
				
				$array_alert_config_groups_ids = array();
				foreach($alert_config_groups as $alert_config_group){
					$array_alert_config_groups_ids[$alert_config_group["id"]] = $alert_config_group["id_client_group"];
				}
				
				if(count($array_alert_config_groups_ids)){ // Si hay grupos seteados
					if(count($groups)){ // Si vienen grupos
						foreach($groups as $id_group){
							if(!in_array($id_group, $array_alert_config_groups_ids)){ // Si un grupo que viene no está en la config, agregarlo
								$data_alert_config_groups = array(
									"id_alert_project" => $save_id,
									"id_client_group" => $id_group,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time()
								);
								$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
							}
						}
						foreach($array_alert_config_groups_ids as $id => $id_group){ // Si un grupo que está en la config no viene, borrarlo
							if(!in_array($id_group, $groups)){
								$this->AYN_Alert_projects_groups_model->delete($id);
							}
						}
					} else { // Si no vienen grupos, eliminar los seteados
						foreach($alert_config_groups as $alert_config_group){
							$this->AYN_Alert_projects_groups_model->delete($alert_config_group["id"]);
						}
					}
				} else { // Si no hay grupos seteados
					if(count($groups)){ // Si vienen grupos, agregarlos
					
						foreach($groups as $id_group){
							$data_alert_config_groups = array(
								"id_alert_project" => $save_id,
								"id_client_group" => $id_group,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
						}
					}
				}
				
				
				// Usuarios
				$alert_config_users = $this->AYN_Alert_projects_users_model->get_all_where(array(
					"id_alert_project" => $id_alert_config,
					"deleted" => 0
				))->result_array();
				
				$array_alert_config_users_ids = array();
				foreach($alert_config_users as $alert_config_user){
					$array_alert_config_users_ids[$alert_config_user["id"]] = $alert_config_user["id_user"];
				}
				
				if(count($array_alert_config_users_ids)){ // Si hay usuarios seteados
					if(count($users)){ // Si vienen usuarios
						foreach($users as $id_user){
							if(!in_array($id_user, $array_alert_config_users_ids)){ // Si un usuario que viene no está en la config, agregarlo
								$data_alert_config_users = array(
									"id_alert_project" => $save_id,
									"id_user" => $id_user,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time()
								);
								$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
							}
						}
						foreach($array_alert_config_users_ids as $id => $id_user){ // Si un usuario que está en la config no viene, borrarlo
							if(!in_array($id_user, $users)){
								$this->AYN_Alert_projects_users_model->delete($id);
							}
						}
					} else {  // Si no vienen usuarios, eliminar los seteados
						foreach($alert_config_users as $alert_config_user){
							$this->AYN_Alert_projects_users_model->delete($alert_config_user["id"]);
						}
					}
				} else {  // Si no hay usuarios seteados
					if(count($users)){ // Si vienen usuarios, agregarlos
						foreach($users as $id_user){
							$data_alert_config_users = array(
								"id_alert_project" => $save_id,
								"id_user" => $id_user,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
						}
					}
				}

			
			} else {

				$data["created_by"] = $this->login_user->id;
				$data["created"] = get_current_utc_time();
				$save_id = $this->AYN_Alert_projects_model->save($data);

				if(count($groups)){ // Si vienen grupos, agregarlos
					foreach($groups as $id_group){
						$data_alert_config_groups = array(
							"id_alert_project" => $save_id,
							"id_client_group" => $id_group,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_group_id = $this->AYN_Alert_projects_groups_model->save($data_alert_config_groups);
					}
				}
				if(count($users)){ // Si vienen grupos, agregarlos
					foreach($users as $id_user){
						$data_alert_config_users = array(
							"id_alert_project" => $save_id,
							"id_user" => $id_user,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_user_id = $this->AYN_Alert_projects_users_model->save($data_alert_config_users);
					}
				}

			}
			
			if($save_id){

				$id_item_config = $id_module."-".$id_submodule."-".$id_air_station."-".$id_air_sector."-".$id_air_variable;
				$alert_config = $data_alert_config["alert_config"];

				// Llamar a la configuración de Alertas de Pronóstico
				$config_options = array(
					"id_client" => $id_client,
					"id_project" => $id_project,
					"id_client_module" => $id_module, 
					"id_client_submodule" => $id_submodule,
					"alert_config" => array(
						"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
						"id_air_station" => $id_air_station,
						"id_air_sector" => $id_air_sector,
						"id_air_variable" => $id_air_variable
					),
				);

				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
			
				$configured = FALSE;
				$n_alerts = 0;
				$n_action_plans = 0;

				if(count($alert_config_forecast)){
					$n_alerts = count($alert_config_forecast->alert_config);
				}
				foreach($alert_config as $config){
					if($config["ap_active"] == "1"){
						$n_action_plans++;
						$configured = TRUE;
					}
				}

				$config_icon_air = ($configured) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>';
				$air_sector = $this->Air_sectors_model->get_one($id_air_sector);
				$air_variable = $this->Air_variables_model->get_one($id_air_variable);

				$btn_action = modal_anchor(get_uri("general_settings/modal_form_alert_config_air/".$accordeon), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('alert_settings'), "data-post-id_modulo" => $id_module, "data-post-id_submodulo" => $id_submodule, "data-post-id_air_station" => $id_air_station, "data-post-id_air_sector" => $id_air_sector, "data-post-id_air_variable" => $id_air_variable, "data-post-name_sector" => $air_sector->name, "data-post-name_variable" => $air_variable->name, "data-post-id_client" => $id_client, "data-post-id_project" => $id_project, "data-post-id_alert_config" => $save_id));				
				echo json_encode(array("success" => true, 'id_item_config' => $id_item_config, 'n_alerts' => $n_alerts, 'n_action_plans' => $n_action_plans, 'config_icon' => $config_icon_air, 'btn_action' => $btn_action, 'message' => lang('record_saved')));

			} else {
				echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			}

		}



	}

}