<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inicio_projects extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
	function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");

		$this->id_client_context_module = 3;
		$this->id_client_context_submodule = 0;
		
		$id_cliente = $this->login_user->client_id;
		$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
    }

    public function index() {

        if ($this->login_user->user_type === "staff") {
            redirect('dashboard');
        } else {
            //client's dashboard    
			
			$this->session->set_userdata('menu_project_active', TRUE);
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
			
            //$view_data['show_invoice_info'] = get_setting("module_invoice") ? true : false;
            $view_data['client_info'] = $client_info;
            $view_data['client_id'] = $client_info->id;
			//$view_data['projects'] = $this->Projects_model->get_details(array("client_id" => $client_info->id))->result();
			$view_data['projects'] = $this->Projects_model->get_projects_of_member($this->login_user->id, $client_info->id)->result();
			
            $view_data['page_type'] = "dashboard";
            
			$view_data['General_settings_model'] = $this->General_settings_model;
			$view_data['Projects_model'] = $this->Projects_model;
			$view_data['Project_rel_footprints_model'] = $this->Project_rel_footprints_model;
			$view_data['Calculation_model'] = $this->Calculation_model;
			$view_data['Fields_model'] = $this->Fields_model;
			$view_data['Unity_model'] = $this->Unity_model;
			$view_data["Forms_model"] = $this->Forms_model;
			$view_data['Characterization_factors_model'] = $this->Characterization_factors_model;
			$view_data['Module_footprint_units_model'] = $this->Module_footprint_units_model;
			$view_data['Conversion_model'] = $this->Conversion_model;
			$view_data['Unit_processes_model'] = $this->Unit_processes_model;
			$view_data['Tipo_tratamiento_model'] = $this->Tipo_tratamiento_model;
			
			//SECCIÓN COMPROMISOS Y PERMISOS
			$view_data['Compromises_rca_model'] = $this->Compromises_rca_model;
			$view_data['Compromises_compliance_evaluation_rca_model'] = $this->Compromises_compliance_evaluation_rca_model;
			$view_data['Permitting_model'] = $this->Permitting_model;
			$view_data['Permitting_procedure_evaluation_model'] = $this->Permitting_procedure_evaluation_model;
			
			$view_data["perfil_puede_ver_compromisos"] = $this->profile_access($this->session->user_id, 6, 3, "ver");
			$view_data["perfil_puede_ver_permisos"] = $this->profile_access($this->session->user_id, 7, 5, "ver");
			
			if($client_info->habilitado){
				$this->template->rander("dashboard/inicio_projects", $view_data);
			}else{
				$this->session->sess_destroy();
				redirect('signin/index/disabled');
			}
            
        }
    }
	
	/* carga el contexto del proyecto en la constante */

    function load_project_context($project_id = 0) {
		$this->session->set_userdata('project_context', $project_id);
		redirect('report_sqm_variables');
	}

}

