<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tutorials extends MY_Controller {
	
	//private $id_modulo_contexto_cliente;
	//private $id_submodulo_contexto_cliente;
		
	function __construct() {
		
        parent::__construct();

		//$this->id_modulo_contexto_cliente = 1;
		//$this->id_submodulo_contexto_cliente = 3;
		
    }
	
    public function index() {

        if ($this->login_user->user_type === "staff") {
            
			$view_data['page_type'] = "dashboard";
			$view_data['mimasoft'] = $this->Mimasoft_model->get_all()->result();
			$view_data["puede_ver"] = 1;
			$this->template->rander("tutorials/inicio", $view_data);
			
        } else {
			//client's dashboard
			
			//Si el módulo no está disponible para el usuario, bloquea la url.
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;
			if($id_proyecto){
				//$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
			} else {
				$this->block_url_client_context($id_cliente, $this->id_modulo_contexto_cliente);
			}
			
			//$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			//$view_data["puede_ver"] = $this->general_profile_access($this->session->user_id, $this->id_modulo_contexto_cliente, $this->id_submodulo_contexto_cliente, "ver");
			

            $options = array("id" => $this->login_user->client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
			
            $view_data['page_type'] = "dashboard";
			$view_data['mimasoft'] = $this->Mimasoft_model->get_all()->result();
			
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$view_data["project_info"] = $proyecto;

			### GENERAR REGISTRO EN LOGS_MODEL ###
			$this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_tutorial');


			if($client_info->habilitado){
				$this->template->rander("tutorials/inicio", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }
    }
	
	/* carga el contexto del proyecto en la constante */

    function load_project_context($project_id = 0) {
		$this->session->set_userdata('project_context', $project_id);
		redirect('faq');
	}

}

