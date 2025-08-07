<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact extends MY_Controller {
	
	//private $id_modulo_cliente;
	//private $id_submodulo_cliente;
	private $id_modulo_contexto_cliente;
	private $id_submodulo_contexto_cliente;
	
	function __construct() {
		
        parent::__construct();

		//$this->id_modulo_cliente = 10;
		//$this->id_submodulo_cliente = 19;
		$this->id_modulo_contexto_cliente = 1;
		$this->id_submodulo_contexto_cliente = 4;
		
    }
	
    public function index() {

        if ($this->login_user->user_type === "staff") {
			show_404();
            
			$view_data['page_type'] = "dashboard";
			$this->template->rander("contact/form", $view_data);
			
        } else {
			//client's dashboard
			
			$this->session->set_userdata('menu_help_and_support_active', TRUE);
			$this->session->set_userdata('menu_kpi_active', NULL);
			$this->session->set_userdata('menu_project_active', NULL);
			$this->session->set_userdata('client_area', NULL);
			$this->session->set_userdata('project_context', NULL);
			$this->session->set_userdata('menu_ec_active', NULL);
			
			//Si el módulo no está disponible para el usuario, bloquea la url.
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;
			if($id_proyecto){
				$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
			} else {
				$this->block_url_client_context($id_cliente, $this->id_modulo_contexto_cliente);
			}

			//$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			$view_data["puede_ver"] = $this->general_profile_access($this->session->user_id, $this->id_modulo_contexto_cliente, $this->id_submodulo_contexto_cliente, "ver");
			
            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			
			$view_data['client_contact'] = $client_info->contacto;
			$view_data['page_type'] = "dashboard";
			
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$view_data["project_info"] = $proyecto;

			if($client_info->habilitado){
				$this->template->rander("contact/form", $view_data);
				
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }
    }


    function save(){
		
		
		$id_user= $this->login_user->id;
		
    	$id = $this->input->post('id');
    	$nombre = $this->input->post('nombre');
    	$correo = $this->input->post('correo');
		$from = array("nombre" => $nombre, "correo" => $correo);
    	$asunto = $this->input->post('asunto');
    	$contenido = $this->input->post('contenido');
		$to = "soporte@mimasoft.cl";
		
		
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
		if ($save_id){
			 send_app_mail($contacto,$asunto,$contenido);
			 echo json_encode(array("success" => true, 'view' => $this->input->post('view'), 'message' => lang('message_sent')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }

    }

}