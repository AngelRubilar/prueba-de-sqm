<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends MY_Controller {

    public function index() {

        if ($this->login_user->user_type === "staff") {

			// if($this->login_user->id == 1){
			// 	$phpVersion = phpversion();
			// 	echo "Versión de PHP: " . $phpVersion."<br><br>";
	
			// 	$mysqli = new mysqli("localhost", "promimairemlp_mima", "Mulearn159", "promimairemlp_sistema");
	
			// 	if ($mysqli->connect_errno) {
			// 		echo "Error al conectar a MySQL: " . $mysqli->connect_error;
			// 		exit();
			// 	}
	
			// 	$mysqlVersion = $mysqli->server_info;
			// 	echo "Versión de MySQL: " . $mysqlVersion;
	
			// 	$mysqli->close();
			// }
			

            redirect('dashboard');
        } else {
            //client's dashboard    
			
			$this->session->set_userdata('menu_project_active', NULL);
			$this->session->set_userdata('client_area', NULL);
			$this->session->set_userdata('project_context', NULL);
			$this->session->set_userdata('menu_agreements_active', NULL);
			$this->session->set_userdata('menu_kpi_active', NULL);
			$this->session->set_userdata('menu_help_and_support_active', NULL);
			$this->session->set_userdata('menu_recordbook_active', NULL);
			$this->session->set_userdata('menu_ec_active', NULL);
           
		    $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			
			$this->session->set_userdata('logo', $client_info->logo);
			$this->session->set_userdata('bar_color', $client_info->color_sitio);

            $view_data['client_info'] = $client_info;
            $view_data['client_id'] = $client_info->id;
			
            $view_data['page_type'] = "dashboard";
			
			$view_data["home_modules_info"] = $this->Home_modules_info_model->get_all_ordered()->result();
			
			// Disponibilidad de módulos
			
			// Proyectos
			$proyectos_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id,
				"id_modulo" => 3,
				"deleted" => 0
			));
			$view_data["proyectos_modulo_disponible"] = $proyectos_disponibilidad_modulo->disponible;
			
			// KPI
			$kpi_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id,
				"id_modulo" => 2,
				"deleted" => 0
			));
			$view_data["kpi_modulo_disponible"] = $kpi_disponibilidad_modulo->disponible;
			
			// Economía Circular
			$economia_circular_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id,
				"id_modulo" => 4,
				"deleted" => 0
			));
			$view_data["economia_circular_modulo_disponible"] = $economia_circular_disponibilidad_modulo->disponible;
			
			// Ayuda y Soporte
			$ayuda_soporte_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id,
				"id_modulo" => 1,
				"deleted" => 0
			));
			$view_data["ayuda_soporte_modulo_disponible"] = $ayuda_soporte_disponibilidad_modulo->disponible;

			// Fin Disponibilidad de módulos
			
			
			// Perfiles General
			$id_usuario = $this->session->user_id;
			
			$view_data["puede_ver_reporte_kpi"] = $this->general_profile_access($id_usuario, 2, 5, "ver");
			$view_data["puede_ver_graf_por_proyecto"] = $this->general_profile_access($id_usuario, 2, 6, "ver");
			$view_data["puede_ver_graf_entre_proyectos"] = $this->general_profile_access($id_usuario, 2, 7, "ver");
			
			$view_data["puede_ver_ayuda_soporte_faq"] = $this->general_profile_access($id_usuario, 1, 1, "ver");
			$view_data["puede_ver_ayuda_soporte_glossary"] = $this->general_profile_access($id_usuario, 1, 2, "ver");
			$view_data["puede_ver_ayuda_soporte_what_is_mimasoft"] = $this->general_profile_access($id_usuario, 1, 3, "ver");
			$view_data["puede_ver_ayuda_soporte_contact"] = $this->general_profile_access($id_usuario, 1, 4, "ver");
			
			$view_data["puede_ver_ec_ind_por_proyecto"] = $this->general_profile_access($id_usuario, 4, 8, "ver");
			$view_data["puede_ver_ec_ind_entre_proyectos"] = $this->general_profile_access($id_usuario, 4, 9, "ver");
			
			// Fin Perfiles General

			if($client_info->habilitado){
				$this->template->rander("home/index", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }
    }
	
	function save(){
		
		
		$id_user= $this->login_user->id;
		
    	$id = $this->input->post('id');
    	$nombre= $this->input->post('nombre');
    	$correo= $this->input->post('correo');
    	$asunto= $this->input->post('asunto');
    	$contenido=$this->input->post('contenido');
		//$contacto=$this->input->post('contact');
		//$destino= $destinatario['contacto'];
		//$contacto = "natalia@ulearn.cl";
		$contacto = "soporte@mimasoft.cl";
		
		
		
		validate_submitted_data(array(
            "nombre" => "required",
			"correo" => "required",
			"asunto" => "required"
			
        ));

    	$data_contact = array( 
            "nombre" => $nombre,
            "correo" => $correo,
            "asunto" => $asunto,
            "contenido" => $contenido,
        );

        $data_contact["created"] = get_current_utc_time();
        $data_contact["created_by"] = $this->login_user->id;


         $save_id = $this->Contact_model->save($data_contact);
		 if ($save_id) {
			 send_app_mail($contacto,$asunto,$contenido);
            echo json_encode(array("success" => true, 'view' => $this->input->post('view'), 'message' => lang('message_sent')));
            
        } else {
            
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
            
        }

    }
	
}