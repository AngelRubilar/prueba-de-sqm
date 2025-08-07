<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project_info extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->init_permission_checker("client");
    }

    function index() {
		//$this->access_only_allowed_members();
		
		$id_proyecto = $this->session->project_context;
		$view_data['project_info'] = $this->Projects_model->get_one($id_proyecto);
		$rubro = $this->Industries_model->get_one($view_data['project_info']->id_industria);
		$subrubro = $this->Subindustries_model->get_one($view_data['project_info']->id_tecnologia);
		$view_data['rubro'] = $rubro->nombre;
		$view_data['subrubro'] = $subrubro->nombre;
		$view_data['tecnologia'] = $this->Subindustries_model->get_one($view_data['project_info']->id_tecnologia);
		$view_data["miembros_de_proyecto"] = $this->Users_model->Users_model->get_users_of_project($id_proyecto)->result_array();
		
		$view_data["id_proyecto"] = $id_proyecto;
        $this->template->rander("project_info/index", $view_data);
    }

	function view_user_profile($id_usuario){

        $view_data['user_info'] = $this->Users_model->get_one($id_usuario);
        $view_data['client_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
		$this->template->rander("project_info/view_user_profile", $view_data);

	}
	
	function contact_general_info_tab($id_usuario = 0) {
		
        if ($id_usuario) {
            //$this->access_only_allowed_members_or_contact_personally($id_usuario);

            $view_data['user_info'] = $this->Users_model->get_one($id_usuario);
			$view_data["client_info"] = $this->Clients_model->get_one($view_data['user_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $id_usuario, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('project_info/contact_general_info_tab', $view_data);
        }
		
    }
	
}

