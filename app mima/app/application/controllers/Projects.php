<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projects extends MY_Controller {

    private $is_user_a_project_member = false;
    private $is_clients_project = false; //check if loged in user's client's project
	
	private $id_admin_module;
	private $id_admin_submodule;
	
    public function __construct() {
        parent::__construct();
		
        $this->id_admin_module = 4; // Proyectos
		$this->id_admin_submodule = 9; // Proyectos
		
        $this->load->model("Project_settings_model");
        $this->load->helper('directory');
    }

    private function can_manage_all_projects() {
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_projects") == "1") {
            return true;
        }
    }

    //When checking project permissions, to reduce db query we'll use this init function, where team members has to be access on the project
    private function init_project_permission_checker($project_id = 0) {
        if ($this->login_user->user_type == "client") {
            $project_info = $this->Projects_model->get_one($project_id);
            if ($project_info->client_id == $this->login_user->client_id) {
                $this->is_clients_project = true;
            }
        } else {
            $this->is_user_a_project_member = $this->Project_members_model->is_user_a_project_member($project_id, $this->login_user->id);
        }
    }

    private function can_edit_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_projects") == "1") {
                return true;
            }
        }
    }

    private function can_delete_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_projects") == "1") {
                return true;
            }
        }
    }

    private function can_add_remove_project_members() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_add_remove_project_members") == "1") {
                return true;
            }
        }
    }

    private function can_view_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if ($this->is_user_a_project_member) {
                //all team members who has access to project can view tasks
                return true;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_create_tasks() {
        if ($this->login_user->user_type == "staff") {

            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_create_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_edit_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_edit_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_delete_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_delete_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_comment_on_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_milestones")) {
                //even the settings allow to view milestones, the client can only create their own project's milestones
                return $this->is_clients_project;
            }
        }
    }

    private function can_create_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_edit_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_files") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_add_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_files")) {
                //even the settings allow to create/edit task, the client can only comment on their own project's files
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_gantt() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_gantt")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    /* load the project settings into ci settings */

    private function init_project_settings($project_id) {
        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->result();
        foreach ($settings as $setting) {
            $this->config->set_item($setting->setting_name, $setting->setting_value);
        }
    }

    private function can_view_timesheet($project_id = 0) {
        if (!get_setting("module_project_timesheet")) {
            return false;
        }

        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {


                if ($project_id) {
                    //check is user a project member
                    return $this->is_user_a_project_member;
                } else {
                    $access_info = $this->get_access_info("timesheet_manage_permission");

                    if ($access_info->access_type == "all") {
                        return true;
                    } else if (count($access_info->allowed_members)) {
                        return true;
                    }
                }
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_timesheet")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    /* load project view */

    function index() {
        redirect("projects/all_projects");
    }

    function all_projects() {
        $label_suggestions = array(array("id" => "", "text" => "- " . lang("label") . " -"));
        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $temp_labels = array();

        foreach ($labels as $label) {
            if ($label && !in_array($label, $temp_labels)) {
                $temp_labels[] = $label;
                $label_suggestions[] = array("id" => $label, "text" => $label);
            }
        }

        $view_data['project_labels_dropdown'] = json_encode($label_suggestions);

        $view_data["can_create_projects"] = $this->can_create_projects();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        if ($this->login_user->user_type === "staff") {
            
			$view_data["can_edit_projects"] = $this->can_edit_projects();
            $view_data["can_delete_projects"] = $this->can_delete_projects();
			
			//FILTRO CLIENTE		
			$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
			$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
			foreach($clientes as $id => $company_name){
				$array_clientes[] = array("id" => $id, "text" => $company_name);
			}
			$view_data['clientes_dropdown'] = json_encode($array_clientes);
			
			//FILTRO ESTADO
			$array_estados[] = array("id" => "", "text" => "- ".lang("status")." -");
			$array_estados[] = array("id" => "open", "text" => lang("open"));
			$array_estados[] = array("id" => "completed", "text" => lang("completed"));
			$array_estados[] = array("id" => "canceled", "text" => lang("canceled"));
			$view_data["estados_dropdown"] = json_encode($array_estados);
			
            $this->template->rander("projects/index", $view_data);
			
        } else {
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            $this->template->rander("clients/projects/index", $view_data);
        }
    }

    /* load project  add/edit modal */

    function modal_form() {
		
        $project_id = $this->input->post('id');
        $client_id = $this->input->post('client_id');
       
	    $view_data["client_id"] = $client_id;
        $view_data['clients_dropdown'] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"));
		
		$array_fases_dropdown = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
		$fases_dropdown = array("" => "-");
		foreach($array_fases_dropdown as $fase){
			$fases_dropdown[$fase["id"]] = lang($fase["nombre_lang"]);
		}
		
        $view_data['fases_dropdown'] = $fases_dropdown;
        $view_data['footprint_format_dropdown'] = array("" => "-") + $this->Footprint_format_model->get_dropdown_list(array("nombre"));
        $view_data['methodology_dropdown'] = array("" => "-");
        $view_data["huellas_disponibles"] = $this->Footprints_model->get_all()->result_array();
		$view_data["industrias_dropdown"] = array("" => "-") + $this->Industries_model->get_dropdown_list(array("nombre"));
		$view_data['subindustry_dropdown'] = array("" => "-");
        $view_data["materiales_disponibles"] = $this->Materials_model->get_all_where(array("deleted" => 0))->result_array();
        //$view_data["materiales_disponibles"] = $this->Materials_model->get_all()->result_array();
		$view_data["tecnologias_dropdown"] = array("" => "-") + $this->Technologies_model->get_dropdown_list(array("nombre"));
		$view_data["paises_dropdown"] = array("" => "-") + $this->Countries_model->get_dropdown_list(array("nombre"));

        // ICONS
        $iconos = directory_map('./assets/images/icons/');
        if (($key = array_search('empty.png', $iconos)) !== false) {
            unset($iconos[$key]);
        }
		sort($iconos);
        $view_data["iconos"] = $iconos; 
     
		// EDIT
        if($project_id){
			
            $view_data['model_info'] = $this->Projects_model->get_one($project_id);
            
			$fase_rel_project = $this->Project_rel_phases_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
            $id_fase = $fase_rel_project->id_fase;

            $id_footprint_format = $view_data['model_info']->id_formato_huella;
            $methodologies = $this->Methodology_model->get_methodologies_of_fh($id_footprint_format)->result_array();
		
			$methodologies_dropdown = array("" => "-");
			foreach($methodologies as $methodology){
				$methodologies_dropdown[$methodology['id']] = $methodology['nombre']; 
            }
            $view_data['methodology_dropdown'] = $methodologies_dropdown;
            
			
			if ($client_id) {
				$view_data['model_info']->client_id = $client_id;
				$view_data["miembros_disponibles"] = $this->Users_model->get_all_where(array("deleted" => 0, "client_id" => $view_data['model_info']->client_id))->result_array();		
            }
            
			$view_data["project_rel_fases"] = $this->Project_rel_phases_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
			$view_data["pu_disponibles"] = $this->Unit_processes_model->get_unit_processes_of_phase($id_fase)->result_array();
            $view_data["miembros_de_proyecto"] = $this->Users_model->get_users_of_project($project_id)->result_array();
            $view_data["unidades_funcionales_de_proyecto"] = $this->Functional_units_model->get_functional_units_of_projects($project_id);
            $view_data["pu_de_proyectos"] = $this->Unit_processes_model->get_pu_of_projects($project_id)->result_array();
            $view_data["huellas_de_proyecto"] = $this->Footprints_model->get_footprints_of_project($project_id);
            $view_data["materiales_de_proyecto"] = $this->Materials_model->get_materials_of_project($project_id)->result();
			$view_data["materiales_deshabilitados"] = $this->Materials_model->get_materials_used_in_project($project_id)->result();
  			
			// TRAIGO LAS TECNOLOGIAS ASOCIADAS A LA INDUSTRIA DEL PROYECTO
			$id_industria_proyecto = $this->Projects_model->get_one($project_id)->id_industria;
			$technologies = $this->Industries_model->get_subindustries_of_industry($id_industria_proyecto)->result_array();
			$technology_dropdown = array();
			foreach($technologies as $technology){
				$technology_dropdown[$technology['id']] = $technology['nombre']; 
			}
			$view_data['subindustry_dropdown'] = array("" => "-") + $technology_dropdown;
			
			$kpi_estructura_reporte = $this->KPI_Report_structure_model->get_one_where(array(
				"id_cliente" => $client_id,
				"id_proyecto" => $project_id,
				"deleted" => 0
			));
			$view_data["is_valor_asignado"] = $kpi_estructura_reporte->is_valor_asignado;
			
		}

        $this->load->view('projects/modal_form', $view_data);/**/
    }

    /* insert or update a project */
     
    function get_unity_processes(){
        $id_fase = $this->input->post('id_fase');
        /*
        $pu = $this->Unit_processes_model->get_dropdown_list(array("nombre"), "id");*/
        $pu = $this->Unit_processes_model->get_unit_processes_of_phase($id_fase)->result_array();
        $html .= '<div class="form-group">';
            $html .= '<label for="default_value_field" class="col-md-2">'.lang('materials').'</label>';
            $html .= '<div class="col-md-10">';
            $html .= form_multiselect("pu[]", $pu, "", "id='pu' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }

    function get_unity_processes_phases(){
    
        $id_fase = $this->input->post('id_fase');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        $pu = $this->Unit_processes_model->get_unit_processes_of_phase($id_fase)->result_array();


        $array_pu = array();
        if($pu){
            foreach($pu as $index => $pu){
                $array_pu[$pu->id] = $pu->nombre;
            }
        }
        
        $html = '';
            
        // FILA POR DEFECTO
        $html .= '<div class="form-group">';
            $html .= '<label for="pu" class="col-md-2">'.lang('fields').'</label>';
            $html .= '<div class="col-md-10">';
            $html .= form_multiselect("pu[]", $array_pu, "", "id='pu' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';

        if($id_fase){
            echo $html;
        } else {
            echo "";
        }

    }

    function save() {
		
		$id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric"
        ));	
		
        $id_fase = $this->input->post('fases');
        $multiselect_miembros = $this->input->post('miembros');
        $multiselect_procesos_unitarios = $this->input->post('pu');
        $multiselect_huellas = $this->input->post('footprints');
        $multiselect_materiales = $this->input->post('materiales');

        /*Project table data*/
        $data_project = array(
            "id_industria" => $this->input->post('industry'),
            "id_tecnologia" => $this->input->post('subindustry'),
			"id_tech" => $this->input->post('technology'),
			"id_formato_huella" => $this->input->post('footprint_format'),
			"id_metodologia" => $this->input->post('id_methodology'),
			"client_label" => trim($this->input->post('client_label')),
			"client_label_rut" => $this->input->post('client_label_rut'),
			"legal_representative" => $this->input->post('legal_representative'),
            "title" => trim($this->input->post('title')),
            "sigla" => trim($this->input->post('initial')),
            "description" => $this->input->post('description'),
            "icono" => $this->input->post('icono'),
            "contenido" => $this->input->post('contenido'),
            "city" => $this->input->post('city'),
            "state" => $this->input->post('state'),
            //"country" => $this->input->post('country'),
			"id_pais" => $this->input->post('country'),
			"environmental_authorization" => $this->input->post('environmental_authorization'),
			"status" => $this->input->post('status'),
            "start_date" => $this->input->post('start_date') ? $this->input->post('start_date') : NULL,
            "deadline" => $this->input->post('deadline') ? $this->input->post('deadline') : NULL,
            "client_id" => $this->input->post('client_id'),
            "starred_by" => '',
        );
		
        if (!$id) {
            $data_project["created_date"] = get_current_utc_time();
            $data_project["created_by"] = $this->login_user->id;
        } else {
			$data_project["modified"] = get_current_utc_time();
            $data_project["modified_by"] = $this->login_user->id;
		}

		//VALIDACION PARA MATERIALES
		if(!$multiselect_materiales){
			echo json_encode(array("success" => false, 'message' => lang('empty_material')));
			exit();
		}
		
        $data_project_member = array();
        $data_project_rel_phases = array();

        if($id){ // EDIT PROJECT
			
			$project = $this->Projects_model->get_one($id);
			if($data_project["sigla"] !== $project->sigla){
				$projects = $this->Projects_model->get_all_where(array("client_id" => $data_project["client_id"], "deleted" => 0))->result();
				foreach($projects as $data){
					if($data_project["client_id"] == $data->client_id){
						if($data_project["sigla"] == $data->sigla){
							echo json_encode(array("success" => false, 'message' => lang('initials_warning')));
							exit();
						}
					}
				}
			}
			
			// VALIDACION DE NOMBRE REPETIDO DENTRO DE CLIENTE
			$client = $data_project["client_id"];
			$titulo_proyecto = $data_project["title"];
			$project_same_name = $this->Projects_model->get_all_where(array("client_id" => $client, "title" => $titulo_proyecto, "deleted" => 0));
			if($project_same_name->num_rows() && $project_same_name->row()->id != $id){
				echo json_encode(array("success" => false, 'message' => lang('project_title_warning')));
				exit();
			}
			
			//Edit member of project
			if($multiselect_miembros){
				
				$array_current_project_members = array();
				$current_project_members = $this->Project_members_model->get_all_where(array(
					"project_id" => $id,
					"deleted" => 0
				))->result_array();
				foreach($current_project_members as $project_member){
					$array_current_project_members[] = $project_member["user_id"];
				}
				
				$delete_project_member = $this->Project_members_model->delete_members($id);
				//if($delete_project_member){
				foreach($multiselect_miembros as $user_id){
					$user_id = (int)$user_id;
					$data_project_member["project_id"] = $id;
					$data_project_member["user_id"] = $user_id;
					$save_project_member = $this->Project_members_model->save($data_project_member);
				}
			}
			
			//Edit phase relation member
			if($id_fase){
				$delete_phase_rel_project = $this->Project_rel_phases_model->delete_phases_rel_project($id);
				//if($delete_phase_rel_project){
					$data_phase_rel_project["id_proyecto"] = $id;
                    $data_phase_rel_project["id_fase"] = $id_fase;
					$data_phase_rel_project["created_by"] = $this->login_user->id;
					$data_phase_rel_project["created"] = get_current_utc_time();
                    $data_phase_rel_project["modified_by"] = $this->login_user->id;
                    $data_phase_rel_project["modified"] = get_current_utc_time();
                    $save_phase_rel_project = $this->Project_rel_phases_model->save($data_phase_rel_project);
				//}	
			}
			
			//Edit unit processes relation project
			if($multiselect_procesos_unitarios){
				
				/*$array_current_pu = array();
				$current_pu = $this->Project_rel_pu_model->get_all_where(array(
					"id_proyecto" => $id,
					"deleted" => 0
				))->result_array();
				foreach($current_pu as $pu){
					$array_current_pu[] = $pu["id_proceso_unitario"];
				}*/
				
				$delete_pu_rel_project = $this->Project_rel_pu_model->delete_pu_rel_project($id);
				foreach($multiselect_procesos_unitarios as $id_proceso_unitario){
					$id_proceso_unitario = (int)$id_proceso_unitario;
					$data_pu_rel_project["id_proyecto"] = $id;
					$data_pu_rel_project["id_proceso_unitario"] = $id_proceso_unitario;
					$data_pu_rel_project["created_by"] = $project->created_by;
					$data_pu_rel_project["created"] = $project->created_date;
					$data_pu_rel_project["modified_by"] = $this->login_user->id;
					$data_pu_rel_project["modified"] = get_current_utc_time();
					$save_pu_rel_project = $this->Project_rel_pu_model->save($data_pu_rel_project);
				}
			}
			
			//Edit footprints relation project
			if($multiselect_huellas){
				
				/*$array_current_huellas = array();
				$current_huellas = $this->Project_rel_footprints_model->get_all_where(array(
					"id_proyecto" => $id,
					"deleted" => 0
				))->result_array();
				foreach($current_huellas as $huella){
					$array_current_huellas[] = $huella["id_huella"];
				}*/
				
				$delete_footprints_rel_project = $this->Project_rel_footprints_model->delete_footprints_rel_project($id);
				foreach($multiselect_huellas as $id_huella){
					$id_huella = (int)$id_huella;
					$data_footprints_rel_project["id_proyecto"] = $id;
					$data_footprints_rel_project["id_huella"] = $id_huella;
					$data_footprints_rel_project["created_by"] = $project->created_by;
					$data_footprints_rel_project["created"] = $project->created_date;
					$data_footprints_rel_project["modified"] = get_current_utc_time();
					$data_footprints_rel_project["modified_by"] = $this->login_user->id;
					$save_footprints_rel_project = $this->Project_rel_footprints_model->save($data_footprints_rel_project);
				}
			}
			
			//Edit materials relation project
			if($multiselect_materiales){
				$delete_materials_rel_project = $this->Project_rel_material_model->delete_materials_rel_project($id);
				foreach($multiselect_materiales as $id_material){
					$id_material = (int)$id_material;
					$data_materials_rel_project["id_proyecto"] = $id;
					$data_materials_rel_project["id_material"] = $id_material;
					$data_materials_rel_project["created_by"] = $project->created_by;
					$data_materials_rel_project["created"] = $project->created_date;
					$data_materials_rel_project["modified"] = get_current_utc_time();
					$data_materials_rel_project["modified_by"] = $this->login_user->id;
					$save_materials_rel_project = $this->Project_rel_material_model->save($data_materials_rel_project);
				}
				
				// Actualizar configuración de factores de transformación cuando admin elimine un material del proyecto
				// si el admin elemina un material del proyecto, se debe eliminar las filas de config que tengan la categoria del material
				// eliminado si es que este material no se repite en otros proyectos.
				
				// Traigo la configuración actual de factores de transformación del cliente
				$config_factores_transformacion = $this->EC_Client_transformation_factors_config_model->get_all_where(array(
					"id_cliente" => $data_project["client_id"],
					"deleted" => 0
				))->result();
				$array_config_factores_transformacion = array();
				foreach($config_factores_transformacion as $config){
					$array_config_factores_transformacion[] = $config->id_categoria;
				}
				
				// Traigo las categorias de los materiales (ya actualizados) de todos los proyectos del cliente.
				$categorias_proyectos = $this->Categories_model->get_categories_of_materials_client_projects($data_project["client_id"])->result();
				$array_categorias_proyectos = array();
				foreach($categorias_proyectos as $categoria){
					$array_categorias_proyectos[] = $categoria->id_categoria;
				}
				
				foreach($array_config_factores_transformacion as $id_categoria_config){
					if(!in_array($id_categoria_config, $array_categorias_proyectos)){
						$config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
							"id_cliente" => $data_project["client_id"],
							"id_categoria" => $id_categoria_config,
							"deleted" => 0
						));
						$delete_config = $this->EC_Client_transformation_factors_config_model->delete($config->id);						
					} 
				}
				
			}
			
            $save_id = $this->Projects_model->save($data_project, $id);
			
            // ACTUALIZA LOS CODIGOS DE LOS FORMULARIOS DEL PROYECTO, EN BASE A SU NUEVA SIGLA
			$forms_rel_project = $this->Form_rel_project_model->get_all_where(array("id_proyecto" => $id, "deleted" => 0))->result();
			foreach($forms_rel_project as $data){
				$form = $this->Forms_model->get_one($data->id_formulario);
				$client = $this->Clients_model->get_one($form->id_cliente);
				
				$nombre_form = $name = str_replace(' ', '', $form->nombre);
				$sigla_cliente = $client->sigla;
				$sigla_project = $data_project["sigla"];
				$numero_form = $form->numero;
				
				$codigo = $sigla_project.$numero_form.$nombre_form;
				
				$data_form = array("codigo" => $codigo);
				$save_form = $this->Forms_model->save($data_form, $form->id);
			}
			
			$crea_carpeta = $this->create_project_folder($client, $save_id);
			
			// Se guarda la estructura de reporte KPI del proyecto para el Reporte KPI
			$kpi_estructura_reporte = $this->KPI_Report_structure_model->get_one_where(array(
				"id_cliente" => $project->client_id,
				"id_proyecto" => $project->id,
				"deleted" => 0
			));
			
			if(!$kpi_estructura_reporte->id){
				if($id_fase == "2" || $id_fase == "3"){
					$opciones_reporte_kpi = array(
						"id_cliente" => $project->client_id,
						"id_fase" => $id_fase,
						"id_proyecto" => $project->id,
					);
					$save_registro_kpi = $this->guardar_estructura_reporte_kpi($opciones_reporte_kpi);
				}
			}
			
			// Se guarda la estructura de gráficos KPI del proyecto para el Gráfico KPI
			$kpi_estructura_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
				"id_cliente" => $project->client_id,
				"id_proyecto" => $project->id,
				"deleted" => 0
			))->result_array();
			
			if(!count($kpi_estructura_graficos)){
								
				if($id_fase == "2" || $id_fase == "3"){
					$opciones_grafico_kpi = array(
						"id_cliente" => $project->client_id,
						"id_fase" => $id_fase,
						"id_proyecto" => $project->id,
					);
					$save_grafico_kpi = $this->guardar_estructura_graficos_kpi($opciones_grafico_kpi);
				}
			}
			
			// Actualiza configuración de reporte
			$configuracion_reporte = $this->Reports_configuration_model->get_one_where(array(
				"id_cliente" => $project->client_id,
				"id_proyecto" => $project->id,
				"deleted" => 0
			));
			$this->Reports_configuration_model->save_default_settings($this->input->post('client_id'), $project->id, $configuracion_reporte->id);

        } else { //INSERT PROJECT
            
			$sigla = $data_project["sigla"];
			$client = $data_project["client_id"];
			$projects = $this->Projects_model->get_all_where(array("client_id" => $client, "deleted" => 0))->result();
			foreach($projects as $data){
				if($client == $data->client_id){
					if($sigla == $data->sigla){
						echo json_encode(array("success" => false, 'message' => lang('initials_warning')));
						exit();
					}
				}
			}
			
			// VALIDACION DE NOMBRE REPETIDO DENTRO DE CLIENTE
			$client = $data_project["client_id"];
			$titulo_proyecto = $data_project["title"];
			$project_same_name = $this->Projects_model->get_all_where(array("client_id" => $client, "title" => $titulo_proyecto, "deleted" => 0))->result();
			if($project_same_name){
				echo json_encode(array("success" => false, 'message' => lang('project_title_warning')));
				exit();
			}

            $save_id = $this->Projects_model->save($data_project);

            //Insert members
            if($multiselect_miembros){
                foreach($multiselect_miembros as $user_id){
                    $user_id = (int)$user_id;
                    $data_project_members["project_id"] = $save_id;
                    $data_project_members["user_id"] = $user_id;
                    $this->Project_members_model->save($data_project_members);
                }
            }
            
			//Insert phase
            if($id_fase){
				$data_project_rel_phases["id_proyecto"] = $save_id;
				$data_project_rel_phases["id_fase"] = $id_fase;
				$data_project_rel_phases["created_by"] = $this->login_user->id;
				$data_project_rel_phases["created"] = get_current_utc_time();
				$this->Project_rel_phases_model->save($data_project_rel_phases);
            }

            //Insert PU
            if($multiselect_procesos_unitarios){
                foreach($multiselect_procesos_unitarios as $id_proceso_unitario){
                    $id_proceso_unitario = (int)$id_proceso_unitario;
                    $data_pu_project["id_proyecto"] = $save_id;
                    $data_pu_project["id_proceso_unitario"] = $id_proceso_unitario;
                    $data_pu_project["created"] = get_current_utc_time();
                    $data_pu_project["created_by"] = $this->login_user->id;  
                    $this->Project_rel_pu_model->save($data_pu_project);
                }
            }
			
            //Insert footprints
            if($multiselect_huellas){
                foreach($multiselect_huellas as $id_huella){
                    $id_huella = (int)$id_huella;
                    $data_project_footprints["id_proyecto"] = $save_id;
                    $data_project_footprints["id_huella"] = $id_huella;
                    $data_project_footprints["created"] = get_current_utc_time();
                    $data_project_footprints["created_by"] = $this->login_user->id;
                    $this->Project_rel_footprints_model->save($data_project_footprints);
                }
            }

            //Insert materials
            if($multiselect_materiales){
                foreach($multiselect_materiales as $id_material){
                    $id_material = (int)$id_material;
                    $data_project_materials["id_proyecto"] = $save_id;
                    $data_project_materials["id_material"] = $id_material;
                    $data_project_materials["created"] = get_current_utc_time();
                    $data_project_materials["created_by"] = $this->login_user->id;
                    $this->Project_rel_material_model->save($data_project_materials);
                }
            }
			
			if($id_fase == "2" || $id_fase == "3"){
				
				// Se guarda la estructura de reporte KPI del proyecto para el Reporte KPI
				$opciones_reporte_kpi = array(
					"id_cliente" => $this->input->post('client_id'),
					"id_fase" => $id_fase,
					"id_proyecto" => $save_id,
				);
				$save_registro_kpi = $this->guardar_estructura_reporte_kpi($opciones_reporte_kpi);
				
				
				// Se guarda la estructura de gráficos KPI del proyecto para el Gráfico KPI
				$opciones_grafico_kpi = array(
					"id_cliente" => $this->input->post('client_id'),
					"id_fase" => $id_fase,
					"id_proyecto" => $save_id,
				);
				$save_grafico_kpi = $this->guardar_estructura_graficos_kpi($opciones_grafico_kpi);
				
				
			}
            
            // Se setea la configuración por defecto del proyecto
            $this->General_settings_model->save_default_settings($this->input->post('client_id'), $save_id);
            $this->Reports_configuration_model->save_default_settings($this->input->post('client_id'), $save_id);
            $this->Module_availability_model->save_default_settings($this->input->post('client_id'), $save_id);
			$this->Module_footprint_units_model->save_default_settings($this->input->post('client_id'), $save_id);
			$this->Reports_units_settings_model->save_default_settings($this->input->post('client_id'), $save_id);
			
			// Se guarda la configuración por defecto del proyecto para el cliente
			//Administración Cliente / Configuración Panel Principal / Tab Huellas Ambientales
			$this->Client_environmental_footprints_settings_model->save_default_settings($this->input->post('client_id'), $save_id);	
			//Administración Cliente / Configuración Panel Principal / Tab Compromisos
			$this->Client_compromises_settings_model->save_default_settings($this->input->post('client_id'), $save_id);
			//Administración Cliente / Configuración Panel Principal / Tab Permisos
			$this->Client_permitting_settings_model->save_default_settings($this->input->post('client_id'), $save_id);
			
			// crear carpeta proyecto
            $crea_carpeta = $this->create_project_folder($this->input->post('client_id'), $save_id);
			
        }
        
        if ($save_id) {
			
			// GUARDAR IMÁGENES DEL CAMPO "CONTENIDO" (TEXTO ENRIQUECIDO) DESDE TEMPORALES A LA CARPETA DE CONTENIDOS DEL PROYECTO
			$contenido = $this->input->post("contenido");
			$project_content_files = $this->input->post("project_content_files");
			
			$array_project_content_files = array();
			
			if(count($project_content_files)){
				foreach($project_content_files as $file_name){
					
					$array_project_content_files[] = $file_name;
					
					$file_path_temp = get_setting("temp_file_path") . "project_content_files/";
					$file_path_project = "files/project_files/project_".$save_id."/project_content/";
					$contenido = str_replace($file_path_temp.$file_name, $file_path_project.$file_name, $contenido);
					
					if (strpos($contenido, $file_name) !== false) {
						
						$archivos_contenido = json_encode(array_values($array_project_content_files));
						$options = array("contenido" => $contenido, "archivos_contenido" => $archivos_contenido);
						$this->Projects_model->save($options, $save_id);
						$this->save_project_content_file($file_path_temp, $file_path_project, $file_name);
						
					} else {
						
						unset($array_project_content_files[array_search($file_name, $array_project_content_files)]);
						$archivos_contenido = json_encode(array_values($array_project_content_files));
						
						$this->delete_project_content_file_from_temp($file_name);
						$options = array("contenido" => $contenido, "archivos_contenido" => $archivos_contenido);
						$this->Projects_model->save($options, $save_id);
					}
				}
			} else {
				
				$options = array("archivos_contenido" => "");
				$this->Projects_model->save($options, $save_id);
				
			}

			// TRAER TODAS LAS IMAGENES DE LA CARPETA project_content DEL PROYECTO
			// SI UNA DE ESTAS IMAGENES NO ESTA DENTRO DE LAS IMAGENES QUE ESTÁN GUARDADAS EN EL CONTENIDO DEL PROYECTO, ELIMINARLA DEL FTP
			
			$file_path_project = "files/project_files/project_".$save_id."/project_content/";
			$files = scandir($file_path_project);
			foreach($files as $file){
				if(!in_array($file, $array_project_content_files)){
					unlink($file_path_project.$file);
				}
			}
			
			// FORMULARIOS OTROS REGISTROS FIJOS. TANTO EN INGRESO COMO EN EDICIÓN, SI NO ESTÁ CREADO ALGUNO DE ELLOS, CREARLO.
			$this->_save_other_records_fixed_forms($save_id);
		
			if($id) { // Edición de Proyecto
				
				// Guardar histórico notificaciones
				$id_client = $project->client_id;
				$id_project = $project->id;
				$id_user = $this->login_user->id;
				
				// Edición de proyecto - Nombre de proyecto
				if($project->title != $this->input->post('title')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_name",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Autorización ambiental
				if($project->environmental_authorization != $this->input->post('environmental_authorization')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_auth_amb",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Fecha de Inicio
				if($project->start_date != $this->input->post('start_date')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_start_date",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Fecha de término
				if($project->deadline != $this->input->post('deadline')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_end_date",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Miembros del proyecto
				if($multiselect_miembros){
					sort($multiselect_miembros);
					sort($array_current_project_members);
					// Miembros del proyecto editados
					
					//var_dump($multiselect_miembros);
					//var_dump($array_current_project_members);
					
					if($multiselect_miembros != $array_current_project_members){
						$options = array(
							"id_client" => $id_client,
							"id_project" => $id_project,
							"id_user" => $id_user,
							"module_level" => "admin",
							"id_admin_module" => $this->id_admin_module,
							"id_admin_submodule" => $this->id_admin_submodule,
							"event" => "project_edit_members",
							"id_element" => $id_project
						);
						ayn_save_historical_notification($options);
					}
				}
				
				// Edición de proyecto - Descripción de proyecto
				if($project->description != $this->input->post('description')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_desc",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Estado de proyecto
				if($project->status != $this->input->post('status')){
					$options = array(
						"id_client" => $id_client,
						"id_project" => $id_project,
						"id_user" => $id_user,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "project_edit_status",
						"id_element" => $id_project
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de proyecto - Procesos unitarios
				if($multiselect_procesos_unitarios){
					sort($multiselect_procesos_unitarios);
					sort($array_current_pu);
					
					if($multiselect_procesos_unitarios != $array_current_pu){
						$options = array(
							"id_client" => $id_client,
							"id_project" => $id_project,
							"id_user" => $id_user,
							"module_level" => "admin",
							"id_admin_module" => $this->id_admin_module,
							"id_admin_submodule" => $this->id_admin_submodule,
							"event" => "project_edit_pu",
							"id_element" => $id_project
						);
						ayn_save_historical_notification($options);
					}
				}
				
				// 	Edición de proyecto - Categorías de impacto
				if($multiselect_huellas){
					sort($multiselect_huellas);
					sort($array_current_huellas);
					// Procesos Unitarios editados
					if($multiselect_huellas != $array_current_huellas){
						$options = array(
							"id_client" => $id_client,
							"id_project" => $id_project,
							"id_user" => $id_user,
							"module_level" => "admin",
							"id_admin_module" => $this->id_admin_module,
							"id_admin_submodule" => $this->id_admin_submodule,
							"event" => "project_edit_cat_impact",
							"id_element" => $id_project
						);
						ayn_save_historical_notification($options);
					}
				}
				
			}
				
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {          
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));   
        }
    }
	
	private function _save_other_records_fixed_forms($id_proyecto){
		
		$existe_or_unidades_funcionales = FALSE;
		
		$campo_fijo_rel_formulario_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_all_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		))->result();
		
		$array_id_formularios_fijos = array();
		foreach($campo_fijo_rel_formulario_rel_proyecto as $rel){
			$array_id_formularios_fijos[] = $rel->id_formulario;
		}
		$array_id_formularios_fijos = array_unique($array_id_formularios_fijos);
		
		foreach($array_id_formularios_fijos as $id_formulario){
			
			$formulario_fijo = $this->Forms_model->get_one($id_formulario);
			
			if($formulario_fijo->codigo_formulario_fijo == "or_unidades_funcionales"){
				$existe_or_unidades_funcionales = TRUE;
			}
			
		}
		
		// Insert de formularios fijos Otros Registros
		$campos_fijos = $this->Fixed_fields_model->get_all()->result();
		
		// Otros Registros - Unidades Funcionales
		if(!$existe_or_unidades_funcionales){
			
			$sigla = explode(" ", $this->input->post('initial'));
			$sigla = implode("", $sigla);
			$numero = explode(" ", "01");
			$numero = implode("", $numero);
			$nombre = explode(" ", lang("functional_units"));
			$nombre = implode("", $nombre);
			$codigo = $sigla.$numero.$nombre;
			
			$data_or_unidades_funcionales = array(
				"id_tipo_formulario" => 3,
				"id_cliente" => $this->input->post('client_id'),
				"numero" => "01",
				"nombre" => lang("functional_units"),
				"codigo" => $codigo,
				"icono" => "cogwheel.png",
				"fijo" => 1,
				"codigo_formulario_fijo" => "or_unidades_funcionales",
				"created_by" => $this->login_user->id,
				"created" => get_current_utc_time()
			);
			
			$save_id_or_unidades_funcionales = $this->Forms_model->save($data_or_unidades_funcionales);
			$crea_carpeta_or_unidades_funcionales = $this->create_form_folder($this->input->post('client_id'), $id_proyecto, $save_id_or_unidades_funcionales);
			
			foreach($campos_fijos as $campo_fijo){
				if($campo_fijo->codigo_formulario_fijo == "or_unidades_funcionales"){
					$data_rel = array(
						"id_campo_fijo" => $campo_fijo->id,
						"id_formulario" => $save_id_or_unidades_funcionales,
						"id_proyecto" => $id_proyecto,
						"created_by" => $this->login_user->id,
						"created" => get_current_utc_time()
					);
					$save_rel = $this->Fixed_field_rel_form_rel_project_model->save($data_rel);
				}
			}	
			
		}

	}
	
	function get_subindustries_of_industry(){
		
		$id_industria = $this->input->post("id_industria");
		
		if($id_industria){
			
			$subindustrias = $this->Industries_model->get_subindustries_of_industry($id_industria)->result_array();
		
			$subindustria_dropdown = array();
			foreach($subindustrias as $subindustria){
				$subindustria_dropdown[$subindustria['id']] = $subindustria['nombre']; 
			}
	
			$html = '<div class="form-group">';
				$html .= '<label for="subindustry" class="col-md-3">'.lang('subindustry').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_dropdown("subindustry", array("" => "-") + $subindustria_dropdown, "", "id='id_subindustry', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
		} else {
			
			$html = '<div class="form-group">';
				$html .= '<label for="subindustry" class="col-md-3">'.lang('subindustry').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_dropdown("subindustry", array("" => "-"), "", "id='id_subindustry', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
		}

		echo $html;
		
    }
    
    function get_methodologies_of_fh(){
		
		$id_footprint_format = $this->input->post("id_footprint_format");
		
		if($id_footprint_format){
			
			$methodologies = $this->Methodology_model->get_methodologies_of_fh($id_footprint_format)->result_array();
		
			$methodologies_dropdown = array();
			foreach($methodologies as $methodology){
				$methodologies_dropdown[$methodology['id']] = $methodology['nombre']; 
			}
	
			$html = '<div class="form-group">';
				$html .= '<label for="id_methodology" class="col-md-3">'.lang('calculation_methodology').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_dropdown("id_methodology", array("" => "-") + $methodologies_dropdown, "", "id='metodologiaCH', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
		} else {
			
			$html = '<div class="form-group">';
				$html .= '<label for="id_methodology" class="col-md-3">'.lang('calculation_methodology').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_dropdown("id_methodology", array("" => "-"), "", "id='metodologiaCH', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
		}

		echo $html;
		
	}

	function create_project_folder($client_id_obj, $project_id_obj){

        $client_id = is_object($client_id_obj) ? $client_id_obj->id : $client_id_obj;
        $project_id = is_object($project_id_obj) ? $project_id_obj->id : $project_id_obj;

        if (!file_exists(__DIR__ . '/../../files/mimasoft_files/client_' . $client_id . '/project_' . $project_id)) {
            if (mkdir(__DIR__ . '/../../files/mimasoft_files/client_' . $client_id . '/project_' . $project_id, 0777, TRUE)) {
                return true;
            } else {
                return false;
            }
        }
    }

    function get_infrastructure_type_options() {

        $id_industria = $this->input->post('id_industria');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }

        $html = '';

        if($id_industria == 1){

            $html .= '<div class="form-group">';
                $html .= '<label for="tipo_infraestructura" class="col-md-3">'.lang('infrastructure_type').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_dropdown("infrastructure_type", array("" => "-", "Generación" => "Generación", "Elevación" => "Elevación", "Transmisión" => "Transmisión"), "", "id='tipo_infraestructura', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                $html .= '</div>';
            $html .= '</div>';
        }

        echo $html;
    }

    function get_infrastructure_type_fields() {

        $tipo_infraestructura = $this->input->post('tipo_infraestructura');

        if (!$this->login_user->id) {

            redirect("forbidden");
        }

        $html = '';

        if($tipo_infraestructura == "Generación"){

            $html .= '<div class="form-group">';
                $html .= '<label for="tecnologia" class="col-md-3">'.lang('technology').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_dropdown("technology", array("" => "-", "Eólica" => "Eólica", "Solar" => "Solar", "Minihidro" => "Minihidro", "Geotermica" => "Geotermica"), "", "id='tecnologia', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
                $html .= '<label for="num_equipos_generacion" class="col-md-3">'.lang('number_of_generation_equipment').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_input(array(
                    "id" => "num_equipos_generacion",
                    "name" => "number_of_generation_equipment",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('number_of_generation_equipment'),
                    "autofocus" => true,
                    "data-rule-required" => true,

                    "data-msg-required" => lang("field_required"),
                    "data-rule-number" => true,
                    "data-msg-number" => lang("enter_a_integer"),
                    "autocomplete" => "off",
                ));
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
                $html .= '<label for="potencia_unitaria_equipos" class="col-md-3">'.lang('unit_power_of_equipment').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_input(array(
                    "id" => "potencia_unitaria_equipos",
                    "name" => "unit_power_of_equipment",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('unit_power_of_equipment'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "data-rule-number" => true,
                    "data-msg-number" => lang("enter_a_integer"),
                    "autocomplete" => "off",
                ));
                $html .= '</div>';
            $html .= '</div>';
        }

        if($tipo_infraestructura == "Elevación"){

            $html .= '<div class="form-group">';
                $html .= '<label for="tipo_subestacion_electrica" class="col-md-3">'.lang('electrical_substation_type').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_dropdown("electrical_substation_type", array("" => "-", "Elevación" => "Elevación", "Interconexión" => "Interconexión"), "", "id='tipo_subestacion_electrica', class='select2 validate-hidden', data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
                $html .= '<label for="capacidad_transformacion" class="col-md-3">'.lang('transformation_capacity').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_input(array(
                    "id" => "capacidad_transformacion",
                    "name" => "transformation_capacity",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('transformation_capacity'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "data-rule-number" => true,
                    "data-msg-number" => lang("enter_a_integer"),
                    "autocomplete" => "off",
                ));
                $html .= '</div>';
            $html .= '</div>';

        }

        if($tipo_infraestructura == "Transmisión"){

            $html .= '<div class="form-group">';
                $html .= '<label for="num_torres_alta_tension" class="col-md-3">'.lang('number_of_high_voltage_towers').'</label>';


                $html .= '<div class="col-md-9">';
                $html .= form_input(array(
                    "id" => "num_torres_alta_tension",
                    "name" => "number_of_high_voltage_towers",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('number_of_high_voltage_towers'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "data-rule-number" => true,
                    "data-msg-number" => lang("enter_a_integer"),
                    "autocomplete" => "off",
                ));
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="form-group">';
                $html .= '<label for="longitud_linea" class="col-md-3">'.lang('line_length').'</label>';
                $html .= '<div class="col-md-9">';
                $html .= form_input(array(
                    "id" => "longitud_linea",
                    "name" => "line_length",
                    "value" => '',
                    "class" => "form-control",
                    "placeholder" => lang('line_length'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "data-rule-number" => true,
                    "data-msg-number" => lang("enter_a_integer"),
                    "autocomplete" => "off",
                ));
                $html .= '</div>';
            $html .= '</div>';

        }

        echo $html;
    }

    /* Show a modal to clone a project */

    function clone_project_modal_form() {

        $project_id = $this->input->post('id');

        if (!$this->can_create_projects()) {
            redirect("forbidden");
        }


        $view_data['model_info'] = $this->Projects_model->get_one($project_id);

        $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"));

        $labels = explode(",", $this->Projects_model->get_label_suggestions());
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;


        $this->load->view('projects/clone_project_modal_form', $view_data);
    }

    /* create a new project from another project */

    function save_cloned_project() {

        ini_set('max_execution_time', 300); //300 seconds

        $project_id = $this->input->post('project_id');

        if (!$this->can_create_projects()) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "title" => "required"
        ));


        $copy_same_assignee_and_collaborators = $this->input->post("copy_same_assignee_and_collaborators");

        $copy_milestones = $this->input->post("copy_milestones");
        $copy_tasks_start_date_and_deadline = $this->input->post("copy_tasks_start_date_and_deadline");


        //prepare new project data
        $now = get_current_utc_time();
        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "client_id" => $this->input->post('client_id'),
            "start_date" => $this->input->post('start_date') * 1 ? $this->input->post('start_date') : "0000-00-00",
            "deadline" => $this->input->post('deadline') * 1 ? $this->input->post('deadline') : "0000-00-00",
            "price" => unformat_currency($this->input->post('price')),
            "created_date" => $now,
            "created_by" => $this->login_user->id,
            "labels" => $this->input->post('labels'),
            "status" => "open",
        );

        //add new project
        $new_project_id = $this->Projects_model->save($data);



        //add milestones
        //when the new milestones will be created the ids will be different. so, we have to convert the milestone ids.
        $milestones_array = array();

        if ($copy_milestones) {
            $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
            foreach ($milestones as $milestone) {
                $old_milestone_id = $milestone->id;

                //prepare new milestone data. remove id from existing data
                $milestone->project_id = $new_project_id;
                $milestone_data = (array) $milestone;
                unset($milestone_data["id"]);

                //add new milestone and keep a relation with new id and old id
                $milestones_array[$old_milestone_id] = $this->Milestones_model->save($milestone_data);
            }
        }



        //add tasks
        $tasks = $this->Tasks_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        foreach ($tasks as $task) {

            //prepare new task data.
            $task->project_id = $new_project_id;
            $milestone_id = get_array_value($milestones_array, $task->milestone_id);
            $task->milestone_id = $milestone_id ? $milestone_id : "";
            $task->status = "to_do";

            if (!$copy_same_assignee_and_collaborators) {
                $task->assigned_to = "";
                $task->collaborators = "";
            }

            if (!$copy_tasks_start_date_and_deadline) {
                $task->start_date = "";
                $task->deadline = "";
            }

            $task_data = (array) $task;
            unset($task_data["id"]); //remove id from existing data
            //add new task
            $this->Tasks_model->save($task_data);
        }

        //add project members
        $project_members = $this->Project_members_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();

        foreach ($project_members as $project_member) {
            //prepare new project member data. remove id from existing data
            $project_member->project_id = $new_project_id;
            $project_member_data = (array) $project_member;
            unset($project_member_data["id"]);

            $this->Project_members_model->save_member($project_member_data);
        }


        if ($new_project_id) {
            log_notification("project_created", array("project_id" => $new_project_id));

            echo json_encode(array("success" => true, 'id' => $new_project_id, 'message' => lang('project_cloned_successfully')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete a project */

    function delete() {

        if (!$this->can_delete_projects()) {
            redirect("forbidden");
        }

        $id = $this->input->post('id');
		$this->delete_cascade_project($id);

        //if ($this->Projects_model->delete_project_and_sub_items($id)) {
		if($this->Projects_model->delete($id)){

			$this->Project_members_model->delete_members($id);
			$this->Project_rel_phases_model->delete_phases_rel_project($id);
			$this->Project_rel_pu_model->delete_pu_rel_project($id);
			$this->Project_rel_footprints_model->delete_footprints_rel_project($id);
			$this->Project_rel_material_model->delete_materials_rel_project($id);

            log_notification("project_deleted", array("project_id" => $id));

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    /* list of projcts, prepared for datatable  */

    function list_data() {
        $this->access_only_team_members();

        //$custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "client_id" => $this->input->post("client_id"),
			"status" => $this->input->post("status")
        );

        //only admin/ the user has permission to manage all projects, can see all projects, other team mebers can see only their own projects.
        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $list_data = $this->Projects_model->get_details($options)->result();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of projcts, prepared for datatable  */

    function projects_list_data_of_team_member($team_member_id = 0) {
        $this->access_only_team_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "status" => $this->input->post("status"),
            "custom_fields" => $custom_fields
        );

        //add can see all members projects but team members can see only ther own projects
        if (!$this->can_manage_all_projects() && $team_member_id != $this->login_user->id) {
            redirect("forbidden");
        }

        $options["user_id"] = $team_member_id;


        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    function projects_list_data_of_client($client_id) {

        $this->access_only_team_members_or_client_contact($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "client_id" => $client_id,
            "status" => $this->input->post("status"),
            "project_label" => $this->input->post("project_label"),
            "custom_fields" => $custom_fields
        );

        $list_data = $this->Projects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );

        $data = $this->Projects_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of project list table */

    private function _make_row($data, $custom_fields) {

        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;

        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";
        $start_date = $data->start_date * 1 ? format_to_date($data->start_date) : "-";
        $dateline = $data->deadline * 1 ? format_to_date($data->deadline) : "-";
        $price = $data->price ? to_currency($data->price, $data->currency_symbol) : "-";

        //has deadline? change the color of date based on status
        if ($data->deadline * 1) {
            if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") > $data->deadline) {
                $dateline = "<span class='text-danger mr5'>" . $dateline . "</span> ";
            } else if ($progress !== 100 && $data->status === "open" && get_my_local_time("Y-m-d") == $data->deadline) {
                $dateline = "<span class='text-warning mr5'>" . $dateline . "</span> ";
            }
        }

        $title = $data->title;
        $project_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $project_labels .= "<span class='label label-info clickable'  title='Label'>" . $label . "</span> ";
            }
            $title .= "<br />" . $project_labels;
        }

        $optoins = "";
        /*Only visual, not code*/
        if ($this->can_edit_projects()) {
            $optoins .= modal_anchor(get_uri("projects/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_project'), "data-post-id" => $data->id,"data-post-client_id" => $data->client_id));
        }

        if ($this->can_edit_projects()) {
            $optoins .= modal_anchor(get_uri("projects/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_project'), "data-post-id" => $data->id,"data-post-client_id" => $data->client_id));
        }

        if ($this->can_delete_projects()) {
            $optoins .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_project'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete"), "data-action" => "delete-confirmation"));
        }

        //show the project price to them who has permission to create projects
        if ($this->login_user->user_type == "staff" && !$this->can_create_projects()) {
            $price = "-";
        }


        $row_data = array(
            $data->id,
            modal_anchor(get_uri("projects/view/" . $data->id), $data->title, array("title" => lang('view_project'), "data-post-id" => $data->id)),
            $data->company_name,
            $price,
            ($data->start_date != "0000-00-00") ? $data->start_date : "-",
            $start_date,
			($data->deadline != "0000-00-00") ? $data->deadline : "-",
            lang($data->status)
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = $optoins;

        return $row_data;
    }

    /* load project details view */

    function view($project_id = 0) {

        //$project_id = $this->input->post('id');

        if ($project_id) {
            $options = array("id" => $project_id);
            $project_info = $this->Projects_model->get_details($options)->row();

            if ($project_info) {
                $view_data['project_info'] = $project_info;
                $view_data['project'] = $this->Projects_model->get_one($project_id);
                $view_data['cliente'] = $this->Clients_model->get_one($view_data['project_info']->client_id);
                $view_data["miembros_de_proyecto"] = $this->Users_model->Users_model->get_users_of_project($project_id)->result_array();
                // Vista fase
                $view_data["project_rel_fases"] = $this->Project_rel_phases_model->get_one_where(array("id_proyecto" => $project_id));
                $view_data['fase'] = $this->Phases_model->get_one_where(array('id' => $view_data["project_rel_fases"]->id_fase));
                // vista PU
                $view_data["procesos_unitarios"] = $this->Unit_processes_model->get_pu_of_projects($project_id)->result_array();
                // metodología
                $view_data['metodologia'] = $this->Methodology_model->get_one($view_data['project_info']->id_metodologia);
                // industria
				$view_data['industria'] = $this->Industries_model->get_one($view_data['project_info']->id_industria);
				// subrubro
                $view_data['subrubro'] = $this->Subindustries_model->get_one($view_data['project_info']->id_tecnologia);
				// tecnologia
                $view_data['tecnologia'] = $this->Technologies_model->get_one($view_data['project_info']->id_tech);
                // vista Huellas
                $view_data["huellas"] = $this->Footprints_model->get_footprints_of_project($project_id);
                // vista Materiales
                $view_data["materiales"] = $this->Materials_model->get_materials_of_project($project_id)->result();

				$view_data["pais"] = $this->Countries_model->get_one($view_data['project_info']->id_pais);

                $this->load->view('projects/view', $view_data);

            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* function view($project_id = 0, $tab = "") {

        $this->init_project_permission_checker($project_id);

        $view_data = $this->_get_project_info_data($project_id);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;

        $expense_access_info = $this->get_access_info("expense");
        $view_data["show_expense_info"] = (get_setting("module_expense") && $expense_access_info->access_type == "all") ? true : false;

        $view_data["show_actions_dropdown"] = $this->can_create_projects();

        $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;

        $view_data["show_timmer"] = get_setting("module_project_timesheet") ? true : false;

        $this->init_project_settings($project_id);
        $view_data["show_timesheet_info"] = $this->can_view_timesheet($project_id);

        $view_data["show_tasks"] = true;

        $view_data["show_gantt_info"] = $this->can_view_gantt();
        $view_data["show_milestone_info"] = $this->can_view_milestones();


        if ($this->login_user->user_type === "client") {

            $view_data["show_timmer"] = false;
            $view_data["show_tasks"] = $this->can_view_tasks();
            $view_data["show_actions_dropdown"] = false;
        }

        $view_data["show_files"] = $this->can_view_files();

        $view_data["tab"] = $tab;

        $view_data["is_starred"] = strpos($view_data['project_info']->starred_by, ":" . $this->login_user->id . ":") ? true : false;

        $this->template->rander("projects/details_view", $view_data);

    } */

    function get_footprints_of_meth(){

        $id_metodologia = $this->input->post('id_metodologia');
        $footprint_methodology = $this->Footprints_model->get_footprints_of_methodology($id_metodologia)->result();

        $array_huellas = array();
        if($footprint_methodology){
            foreach($footprint_methodology as $index => $huella){
                $array_huellas[$huella->id] = $huella->nombre;
            }
        }

        $html = '';

        // FILA POR DEFECTO
        $html .= '<div class="form-group">';
            $html .= '<label for="footprints" class="col-md-3">'.lang('footprints').'</label>';
            $html .= '<div class="col-md-9">';
			$html .= form_multiselect("footprints[]", $array_huellas, "", "id='footprints' class='multiple' multiple='multiple'");
            $html .= '</div>';
        $html .= '</div>';

        if($id_metodologia){
            echo $html;
        } else {
            echo "";
        }

    }

    /* devolver la sigla de un proyecto (para formar codigo de formulario) */
    function get_sigla_of_project(){

        $id_proyecto = $this->input->post('id_project');
        $proyecto = $this->Projects_model->get_one($id_proyecto);
        $sigla = $proyecto->sigla;
        echo $sigla;

    }

    /* devolver multiselect con los campos de un proyecto */
    function get_fields_of_project(){

        $id_project = $this->input->post('id_project');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        $campos = $this->Fields_model->get_all_where(array("id_proyecto" => $id_project, "deleted" => 0))->result();

        $array_campos = array();
        if($campos){
            foreach($campos as $index => $campo){
                $array_campos[$campo->id] = $campo->nombre;
            }
        }

        $html = '';

        // FILA POR DEFECTO
        $html .= '<div class="form-group">';

            $html .= '<label for="campos" class="col-md-2">'.lang('fields').'</label>';
            $html .= '<div class="col-md-10">';
            $html .= form_multiselect("campos[]", $array_campos, "", "id='campos' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';

        if($id_project){
            echo $html;
        } else {
            echo "";
        }

    }

	/* devolver multiselect con los campos de un proyecto usado en formulario */
    function get_fields_of_project2(){

        $id_project = $this->input->post('id_project');
		$tipo_formulario = $this->input->post('tipo_formulario');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		if($tipo_formulario == 2){
			//cargar todos los campos menos mantenedora.
			$campos = $this->Fields_model->get_fields_of_projects_where_not($id_project, array("id_tipo_campo" => 16))->result();
		} else {
			$campos = $this->Fields_model->get_all_where(array("id_proyecto" => $id_project, "deleted" => 0))->result();

		}

		//asort($campos);
        $array_campos = array();
        if($campos){
            foreach($campos as $index => $campo){
                $array_campos[$campo->id] = $campo->nombre;
            }
        }
        //array_multisort($array_campos, SORT_ASC, $campos);
		//sort($array_campos, SORT_STRING);
		natcasesort($array_campos);
        $html = '';

        // FILA POR DEFECTO
        $html .= '<div class="form-group">';

            $html .= '<label for="campos" class="col-md-3">'.lang('fields').'</label>';
            $html .= '<div class="col-md-9">';
            $html .= form_multiselect("campos[]", $array_campos, "", "id='campos' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';

        if($id_project){
            echo $html;
        } else {
            echo "";
        }

    }




     /* muestra los Pu relacionados a la fase al agregar */
    function get_pu_phase(){

        $fases = $this->input->post('fases');
        $pu_fase= $this->Unit_processes_model->get_unit_processes_of_phase($fases)->result();

        $array_pu_fase = array();
        if($pu_fase){
            foreach($pu_fase as $index => $pu2){
                $array_pu_fase[$pu2->id] = $pu2->nombre;
            }
        }


        $html = '';

        // FILA POR DEFECTO
        $html .= '<div class="form-group">';
            $html .= '<label for="pu" class="col-md-3">'.lang('unit_processes').'</label>';
            $html .= '<div class="col-md-9">';
            //$html .= form_multiselect("pu[]", $array_pu_fase, "", "id='pu' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= form_multiselect("pu[]", $array_pu_fase, "", "id='pu' class='multiple' multiple='multiple'");
            $html .= '</div>';
        $html .= '</div>';

        if($fases){
            echo $html;
        } else {
            echo "";
        }

    }


    /* prepare project info data for reuse */

    private function _get_project_info_data($project_id) {

        $options = array(
            "id" => $project_id,

            "client_id" => $this->login_user->client_id,
        );

        if (!$this->can_manage_all_projects()) {
            $options["user_id"] = $this->login_user->id;
        }

        $project_info = $this->Projects_model->get_details($options)->row();
        $view_data['project_info'] = $project_info;

        if ($project_info) {
            $view_data['project_info'] = $project_info;
            $timer = $this->Timesheets_model->get_timer_info($project_id, $this->login_user->id)->row();

            if ($timer) {
                $view_data['timer_status'] = "open";
            } else {
                $view_data['timer_status'] = "";
            }

            $view_data['project_progress'] = $project_info->total_points ? round(($project_info->completed_points / $project_info->total_points) * 100) : 0;

            return $view_data;
        } else {
            show_404();
        }
    }

    function show_my_starred_projects() {
        $view_data["projects"] = $this->Projects_model->get_starred_projects($this->login_user->id)->result();
        $this->load->view('projects/star/projects_list', $view_data);
    }

    /* load project overview section */

    function overview($project_id) {
        $this->access_only_team_members();
        $this->init_project_permission_checker($project_id);

        $view_data = $this->_get_project_info_data($project_id);
        $task_statuses = $this->Tasks_model->get_task_statistics(array("project_id" => $project_id));

        $view_data["task_to_do"] = 0;
        $view_data["task_in_progress"] = 0;
        $view_data["task_done"] = 0;
        foreach ($task_statuses as $status) {
            $view_data["task_" . $status->status] = $status->total;
        }

        $view_data['project_id'] = $project_id;
        $offset = 0;
        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => "project", "log_for_id" => $project_id, "limit" => 20, "offset" => $offset);

        $view_data["can_add_remove_project_members"] = $this->can_add_remove_project_members();

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("projects", $project_id, $this->login_user->is_admin, $this->login_user->user_type)->result();


        $this->load->view('projects/overview', $view_data);
    }

    /* add-remove start mark from project */

    function add_remove_star($project_id, $type = "add") {
        if ($project_id) {
            $view_data["project_id"] = $project_id;

            if ($type === "add") {
                $this->Projects_model->add_remove_star($project_id, $this->login_user->id, $type = "add");
                $this->load->view('projects/star/starred', $view_data);
            } else {
                $this->Projects_model->add_remove_star($project_id, $this->login_user->id, $type = "remove");
                $this->load->view('projects/star/not_starred', $view_data);
            }
        }
    }

    /* load project overview section */

    function overview_for_client($project_id) {
        if ($this->login_user->user_type === "client") {
            $view_data = $this->_get_project_info_data($project_id);

            $view_data['project_id'] = $project_id;

            $view_data['show_overview'] = false;
            if (get_setting("client_can_view_overview")) {
                $view_data['show_overview'] = true;

                $task_statuses = $this->Tasks_model->get_task_statistics(array("project_id" => $project_id));

                $view_data["task_to_do"] = 0;
                $view_data["task_in_progress"] = 0;
                $view_data["task_done"] = 0;
                foreach ($task_statuses as $status) {
                    $view_data["task_" . $status->status] = $status->total;
                }
            }

            $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("projects", $project_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $this->load->view('projects/overview_for_client', $view_data);
        }
    }

    /* load project members add/edit modal */

    function project_member_modal_form() {

        $view_data['model_info'] = $this->Project_members_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $view_data['users_dropdown'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("user_type" => "staff"));
        $this->load->view('projects/project_members/modal_form', $view_data);
    }

    /* add a project members  */

    function save_project_member() {
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }

        $project_member_id = $this->input->post('user_id');

        $data = array(
            "project_id" => $project_id,
            "user_id" => $project_member_id
        );
        $save_id = $this->Project_members_model->save_member($data);
        if ($save_id && $save_id == "exists") {
            //this member already exists.
            echo json_encode(array("success" => true, 'id' => $save_id));
        } else if ($save_id) {
            log_notification("project_member_added", array("project_id" => $project_id, "to_user_id" => $project_member_id));
            echo json_encode(array("success" => true, "data" => $this->_project_member_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a project members  */

    function delete_project_member() {
        $id = $this->input->post('id');
        $project_member_info = $this->Project_members_model->get_one($id);

        $this->init_project_permission_checker($project_member_info->project_id);

        if (!$this->can_add_remove_project_members()) {
            redirect("forbidden");
        }


        if ($this->input->post('undo')) {
            if ($this->Project_members_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_project_member_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Project_members_model->delete($id)) {

                $project_member_info = $this->Project_members_model->get_one($id);

                log_notification("project_member_deleted", array("project_id" => $project_member_info->project_id, "to_user_id" => $project_member_info->user_id));
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of project members, prepared for datatable  */

    function project_member_list_data($project_id = 0) {
        $this->access_only_team_members();
        $this->init_project_permission_checker($project_id);

        $options = array("project_id" => $project_id);
        $list_data = $this->Project_members_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_project_member_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of project member list */

    private function _project_member_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_members_model->get_details($options)->row();
        return $this->_make_project_member_row($data);
    }

    /* prepare a row of project member list */

    private function _make_project_member_row($data) {
        $image_url = get_avatar($data->member_image);
        $member = get_team_member_profile_link($data->user_id, "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->member_name");
        $link = "";

        //check message module availability and show message button
        if (get_setting("module_message") && ($this->login_user->id != $data->user_id)) {
            $link = modal_anchor(get_uri("messages/modal_form/" . $data->user_id), "<i class='fa fa-envelope-o'></i>", array("class" => "edit", "title" => lang('send_message')));
        }

        $can_add_remove_project_members = $this->can_add_remove_project_members();
        if ($can_add_remove_project_members && !$data->is_leader && $data->user_id != $this->login_user->id) {
            $link .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_member'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_project_member"), "data-action" => "delete"));
        }
        $member = '<div class="pull-left">' . $member . '</div><div class="pull-right"><label class="label label-light ml10">' . $data->job_title . '</label></div>';
        return array($member,
            $link
        );
    }

    //stop timer note modal
    function stop_timer_modal_form($project_id) {
        $this->access_only_team_members();

        if ($project_id) {
            $view_data["project_id"] = $project_id;
            $view_data["tasks_dropdown"] = $this->_get_timesheet_tasks_dropdown($project_id);

            $this->load->view('projects/timesheets/stop_timer_modal_form', $view_data);
        }
    }



    //show timer note modal
    function timer_note_modal_form() {

        $id = $this->input->post("id");
        if ($id) {
            $model_info = $this->Timesheets_model->get_one($id);

            $this->init_project_permission_checker($model_info->project_id);
            $this->init_project_settings($model_info->project_id); //since we'll check this permission project wise


            if (!$this->can_view_timesheet($model_info->project_id)) {
                redirect("forbidden");
            }

            $view_data["model_info"] = $model_info;
            $this->load->view('projects/timesheets/note_modal_form', $view_data);
        }
    }

    private function _get_timesheet_tasks_dropdown($project_id, $return_json = false) {
        $tasks_dropdown = array("" => "-");
        $tasks_dropdown_json = array(array("id" => "", "text" => "- " . lang("task") . " -"));

        $tasks = $this->Tasks_model->get_details(array("project_id" => $project_id))->result();

        foreach ($tasks as $task) {
            $tasks_dropdown_json[] = array("id" => $task->id, "text" => $task->id . "-" . $task->title);
            $tasks_dropdown[$task->id] = $task->id . " - " . $task->title;
        }

        if ($return_json) {
            return json_encode($tasks_dropdown_json);
        } else {
            return $tasks_dropdown;
        }
    }

    /* start/stop project timer */

    function timer($project_id, $timer_status = "start") {
        $this->access_only_team_members();
        $note = $this->input->post("note");
        $task_id = $this->input->post("task_id");

        $data = array(
            "project_id" => $project_id,
            "user_id" => $this->login_user->id,
            "status" => $timer_status,
            "note" => $note ? $note : "",
            "task_id" => $task_id ? $task_id : 0,
        );

        $this->Timesheets_model->process_timer($data);
        if ($timer_status === "start") {
            $view_data = $this->_get_project_info_data($project_id);
            $this->load->view('projects/project_timer', $view_data);
        } else {
            echo json_encode(array("success" => true));
        }
    }

    /* load timesheets view for a project */

    function timesheets($project_id) {

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise


        if (!$this->can_view_timesheet($project_id)) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;

        //client can't add log or update settings
        $view_data['can_add_log'] = false;
        $view_data['can_update_settings'] = false;

        if ($this->login_user->user_type === "staff") {
            $view_data['can_add_log'] = true;
            $view_data['can_update_settings'] = $this->can_create_projects(); //settings can update only the allowed members
        }

        $view_data['project_members_dropdown'] = json_encode($this->_get_project_members_dropdown_list_for_filter($project_id));
        $view_data['tasks_dropdown'] = $this->_get_timesheet_tasks_dropdown($project_id, true);

        $this->load->view("projects/timesheets/index", $view_data);
    }

    /* prepare project members dropdown */

    private function _get_project_members_dropdown_list_for_filter($project_id) {

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result();
        $project_members_dropdown = array(array("id" => "", "text" => "- " . lang("member") . " -"));
        foreach ($project_members as $member) {
            $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
        }
        return $project_members_dropdown;
    }

    /* load timelog add/edit modal */

    function timelog_modal_form() {
        $this->access_only_team_members();
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;
        $view_data['model_info'] = $this->Timesheets_model->get_one($this->input->post('id'));
        $view_data['project_id'] = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;
        $view_data["tasks_dropdown"] = $this->_get_timesheet_tasks_dropdown($view_data['project_id']);
        $this->load->view('projects/timesheets/modal_form', $view_data);
    }

    /* insert/update a timelog */

    function save_timelog() {
        $this->access_only_team_members();
        $id = $this->input->post('id');

        //convert to 24hrs time format
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');
        $note = $this->input->post("note");
        $task_id = $this->input->post("task_id");


        if (get_setting("time_format") != "24_hours") {
            $start_time = convert_time_to_24hours_format($start_time);
            $end_time = convert_time_to_24hours_format($end_time);
        }

        //join date with time
        $start_date_time = $this->input->post('start_date') . " " . $start_time;
        $end_date_time = $this->input->post('end_date') . " " . $end_time;

        //add time offset
        $start_date_time = convert_date_local_to_utc($start_date_time);
        $end_date_time = convert_date_local_to_utc($end_date_time);

        $data = array(
            "project_id" => $this->input->post('project_id'),
            "start_time" => $start_date_time,
            "end_time" => $end_date_time,
            "note" => $note ? $note : "",
            "task_id" => $task_id ? $task_id : 0,
        );

        //save user_id only on insert and it will not be editable
        if (!$id) {
            //insert mode
            $data["user_id"] = $this->input->post('user_id') ? $this->input->post('user_id') : $this->login_user->id;
        } else {
            //edit mode
            //check edit permission
            $this->check_timelog_updte_permission($id);
        }


        $save_id = $this->Timesheets_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* insert/update a timelog */

    function save_timelog_note() {
        $this->access_only_team_members();

        validate_submitted_data(array(
            "id" => "required"
        ));

        $id = $this->input->post('id');
        $data = array(
            "note" => $this->input->post("note")
        );

        if ($id) {
            //check edit permission
            $this->check_timelog_updte_permission($id);
        }


        $save_id = $this->Timesheets_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a timelog */

    function delete_timelog() {
        $this->access_only_team_members();



        $id = $this->input->post('id');

        $this->check_timelog_updte_permission($id);

        if ($this->input->post('undo')) {
            if ($this->Timesheets_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Timesheets_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    private function check_timelog_updte_permission($log_id) {
        //check delete permission
        $members = $this->_get_members_to_manage_timesheet();


        $info = $this->Timesheets_model->get_one($log_id);

        if ($members != "all" && !in_array($info->user_id, $members)) {
            redirect("forbidden");
        }
    }

    /* list of timesheets, prepared for datatable  */

    function timesheet_list_data() {

        $project_id = $this->input->post("project_id");

        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise


        if (!$this->can_view_timesheet($project_id)) {
            redirect("forbidden");
        }

        $options = array(
            "project_id" => $project_id,
            "status" => "none_open",
            "user_id" => $this->input->post("user_id"),
            "start_date" => $this->input->post("start_date"),
            "end_date" => $this->input->post("end_date"),
            "task_id" => $this->input->post("task_id")
        );

        //get allowed member ids
        $members = $this->_get_members_to_manage_timesheet();
        if ($members != "all" && $this->login_user->user_type == "staff") {
            //if user has permission to access all members, query param is not required
            //client can view all timesheet
            $options["allowed_members"] = $members;
        }


        $list_data = $this->Timesheets_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_timesheet_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of timesheet list  table */

    private function _timesheet_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Timesheets_model->get_details($options)->row();
        return $this->_make_timesheet_row($data);
    }

    /* prepare a row of timesheet list table */

    private function _make_timesheet_row($data) {
        $image_url = get_avatar($data->logged_by_avatar);
        $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->logged_by_user";

        $start_time = $data->start_time;
        $end_time = $data->end_time;
        $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);
        $task_title = modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id));

        $note_link = modal_anchor(get_uri("projects/timer_note_modal_form/"), "<i class='fa fa-comment-o p10'></i>", array("class" => "edit text-muted", "title" => lang("note"), "data-post-id" => $data->id));
        if ($data->note) {
            $note_link = modal_anchor(get_uri("projects/timer_note_modal_form/"), "<i class='fa fa-comment p10'></i>", array("class" => "edit text-muted", "title" => $data->note, "data-modal-title" => lang("note"), "data-post-id" => $data->id));
        }


        return array(
            get_team_member_profile_link($data->user_id, $user),
            $project_title,
            $task_title,
            $data->start_time,
            format_to_datetime($data->start_time),

            $data->end_time,
            format_to_datetime($data->end_time),
            convert_seconds_to_time_format(abs(strtotime($end_time) - strtotime($start_time))),
            $note_link,
            modal_anchor(get_uri("projects/timelog_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_timelog'), "data-post-id" => $data->id))
            . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_timelog'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_timelog"), "data-action" => "delete"))
        );
    }

    /* load timesheets summary view for a project */

    function timesheet_summary($project_id) {


        $this->init_project_permission_checker($project_id);
        $this->init_project_settings($project_id); //since we'll check this permission project wise

        if (!$this->can_view_timesheet($project_id)) {
            redirect("forbidden");
        }



        $view_data['project_id'] = $project_id;

        $view_data['group_by_dropdown'] = json_encode(
                array(
                    array("id" => "", "text" => "- " . lang("group_by") . " -"),
                    array("id" => "member", "text" => lang("member")),
                    array("id" => "task", "text" => lang("task"))
        ));

        $view_data['project_members_dropdown'] = json_encode($this->_get_project_members_dropdown_list_for_filter($project_id));
        $view_data['tasks_dropdown'] = $this->_get_timesheet_tasks_dropdown($project_id, true);

        $this->load->view("projects/timesheets/summary_list", $view_data);
    }

    /* list of timesheets summary, prepared for datatable  */

    function timesheet_summary_list_data() {

        $project_id = $this->input->post("project_id");


        //client can't view all projects timesheet. project id is required.
        if (!$project_id) {
            $this->access_only_team_members();
        }

        if ($project_id) {
            $this->init_project_permission_checker($project_id);
            $this->init_project_settings($project_id); //since we'll check this permission project wise

            if (!$this->can_view_timesheet($project_id)) {
                redirect("forbidden");
            }
        }


        $group_by = $this->input->post("group_by");

        $options = array(
            "project_id" => $project_id,
            "status" => "none_open",
            "user_id" => $this->input->post("user_id"),
            "start_date" => $this->input->post("start_date"),
            "end_date" => $this->input->post("end_date"),
            "task_id" => $this->input->post("task_id"),
            "group_by" => $group_by
        );

        //get allowed member ids
        $members = $this->_get_members_to_manage_timesheet();
        if ($members != "all" && $this->login_user->user_type == "staff") {
            //if user has permission to access all members, query param is not required
            //client can view all timesheet
            $options["allowed_members"] = $members;
        }

        $list_data = $this->Timesheets_model->get_summary_details($options)->result();


        $result = array();
        foreach ($list_data as $data) {


            $member = "-";
            $task_title = "-";

            if ($group_by != "task") {
                $image_url = get_avatar($data->logged_by_avatar);
                $user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->logged_by_user";

                $member = get_team_member_profile_link($data->user_id, $user);
            }

            $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);

            if ($group_by != "member") {
                $task_title = modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id));
                if (!$data->task_title) {
                    $task_title = lang("not_specified");
                }
            }


            $duration = convert_seconds_to_time_format(abs($data->total_duration));


            $result[] = array(
                $project_title,
                $member,
                $task_title,
                $duration,
                to_decimal_format(convert_time_string_to_decimal($duration))
            );
        }
        echo json_encode(array("data" => $result));
    }

    /* get all projects list */

    private function _get_all_projects_dropdown_list() {
        $projects = $this->Projects_model->get_dropdown_list(array("title"));

        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $id => $title) {
            $projects_dropdown[] = array("id" => $id, "text" => $title);
        }
        return $projects_dropdown;
    }

    /*
     * admin can manage all members timesheet
     * allowed member can manage other members timesheet accroding to permission
     */

    private function _get_members_to_manage_timesheet() {

        $access_info = $this->get_access_info("timesheet_manage_permission");

        if ($access_info->access_type == "all") {
            return "all"; //can access all member's timelogs
        } else if (count($access_info->allowed_members)) {
            return $access_info->allowed_members; //can access allowed member's timelogs
        } else {
            return array($this->login_user->id); //can access own timelogs
        }
    }

    /* prepare dropdown list */

    private function _prepare_members_dropdown_for_timesheet_filter($members) {
        $where = array("user_type" => "staff");

        if ($members != "all") {
            $where["where_in"] = array("id" => $members);
        }

        $users = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", $where);

        $members_dropdown = array(array("id" => "", "text" => "- " . lang("member") . " -"));
        foreach ($users as $id => $name) {
            $members_dropdown[] = array("id" => $id, "text" => $name);
        }
        return $members_dropdown;
    }

    /* load all time sheets view  */

    function all_timesheets() {
        $this->access_only_team_members();

        $members = $this->_get_members_to_manage_timesheet();

        $view_data['members_dropdown'] = json_encode($this->_prepare_members_dropdown_for_timesheet_filter($members));

        $view_data['projects_dropdown'] = json_encode($this->_get_all_projects_dropdown_list());
        $this->template->rander("projects/timesheets/all_timesheets", $view_data);
    }

    /* load all timesheets summary view */

    function all_timesheet_summary() {
        $this->access_only_team_members();

        $members = $this->_get_members_to_manage_timesheet();

        $view_data['group_by_dropdown'] = json_encode(
                array(
                    array("id" => "", "text" => "- " . lang("group_by") . " -"),
                    array("id" => "member", "text" => lang("member")),
                    array("id" => "project", "text" => lang("project")),
                    array("id" => "task", "text" => lang("task"))
        ));


        $view_data['members_dropdown'] = json_encode($this->_prepare_members_dropdown_for_timesheet_filter($members));
        $view_data['projects_dropdown'] = json_encode($this->_get_all_projects_dropdown_list());

        $this->load->view("projects/timesheets/all_summary_list", $view_data);
    }


    /* load milestones view */

    function milestones($project_id) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_milestones()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;

        $view_data["can_create_milestones"] = $this->can_create_milestones();
        $view_data["can_edit_milestones"] = $this->can_edit_milestones();
        $view_data["can_delete_milestones"] = $this->can_delete_milestones();

        $this->load->view("projects/milestones/index", $view_data);
    }

    /* load milestone add/edit modal */

    function milestone_modal_form() {
        $id = $this->input->post('id');
        $view_data['model_info'] = $this->Milestones_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_milestones()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_milestones()) {
                redirect("forbidden");
            }
        }

        $view_data['project_id'] = $project_id;

        $this->load->view('projects/milestones/modal_form', $view_data);
    }

    /* insert/update a milestone */

    function save_milestone() {

        $id = $this->input->post('id');
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_milestones()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_milestones()) {
                redirect("forbidden");
            }
        }

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "project_id" => $this->input->post('project_id'),
            "due_date" => $this->input->post('due_date')
        );
        $save_id = $this->Milestones_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete/undo a milestone */

    function delete_milestone() {

        $id = $this->input->post('id');
        $info = $this->Milestones_model->get_one($id);
        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_milestones()) {
            redirect("forbidden");
        }

        if ($this->input->post('undo')) {
            if ($this->Milestones_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_milestone_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Milestones_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of milestones, prepared for datatable  */

    function milestones_list_data($project_id = 0) {
        $this->init_project_permission_checker($project_id);

        $options = array("project_id" => $project_id);
        $list_data = $this->Milestones_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_milestone_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of milestone list  table */

    private function _milestone_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Milestones_model->get_details($options)->row();
        $this->init_project_permission_checker($data->project_id);

        return $this->_make_milestone_row($data);
    }

    /* prepare a row of milestone list table */

    private function _make_milestone_row($data) {

        //calculate milestone progress
        $progress = $data->total_points ? round(($data->completed_points / $data->total_points) * 100) : 0;
        $class = "progress-bar-primary";
        if ($progress == 100) {
            $class = "progress-bar-success";
        }

        $progress_bar = "<div class='progress' title='$progress%'>
            <div  class='progress-bar $class' role='progressbar' aria-valuenow='$progress' aria-valuemin='0' aria-valuemax='100' style='width: $progress%'>
            </div>
        </div>";

        //define milesone color based on due date
        $due_date = date("L", strtotime($data->due_date));
        $label_class = "";
        if ($progress == 100) {
            $label_class = "label-success";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") > $data->due_date) {
            $label_class = "label-danger";
        } else if ($progress !== 100 && get_my_local_time("Y-m-d") == $data->due_date) {
            $label_class = "label-warning";
        } else {
            $label_class = "label-primary";
        }

        $day_name = lang(strtolower(date("l", strtotime($data->due_date)))); //get day name from language
        $month_name = lang(strtolower(date("F", strtotime($data->due_date)))); //get month name from language

        $due_date = "<div class='milestone pull-left' title='" . format_to_date($data->due_date) . "'>
            <span class='label $label_class'>" . $month_name . "</span>
            <h1>" . date("d", strtotime($data->due_date)) . "</h1>
            <span>" . $day_name . "</span>
            </div>
            "
        ;

        $optoins = "";
        if ($this->can_edit_milestones()) {
            $optoins .= modal_anchor(get_uri("projects/milestone_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_milestone'), "data-post-id" => $data->id));
        }

        if ($this->can_delete_milestones()) {
            $optoins .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_milestone'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_milestone"), "data-action" => "delete"));
        }


        $title = "<div><b>" . $data->title . "</b></div>";
        if ($data->description) {
            $title .= "<div>" . nl2br($data->description) . "<div>";
        }

        return array(
            $data->due_date,
            $due_date,
            $title,
            $progress_bar,
            $optoins
        );
    }

    /* load task list view */

    function tasks($project_id) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $view_data['view_type'] = "project_tasks";

        $view_data['can_create_tasks'] = $this->can_create_tasks();
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['can_delete_tasks'] = $this->can_delete_tasks();

        $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
        $view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $this->load->view("projects/tasks/index", $view_data);
    }


    /* get list of milestones for filter */
    function get_milestones_for_filter(){

        $this->access_only_team_members();
        $project_id = $this->input->post("project_id");
        if($project_id){
            echo $this->_get_milestones_dropdown_list($project_id);
        }

    }


    private function _get_milestones_dropdown_list($project_id = 0) {
        $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        $milestone_dropdown = array(array("id" => "", "text" => "- " . lang("milestone") . " -"));

        foreach ($milestones as $milestone) {
            $milestone_dropdown[] = array("id" => $milestone->id, "text" => $milestone->title);
        }
        return json_encode($milestone_dropdown);
    }

    private function _get_project_members_dropdown_list() {
        $assigned_to_dropdown = array(array("id" => "", "text" => "- " . lang("assigned_to") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {
            $assigned_to_dropdown[] = array("id" => $key, "text" => $value);
        }
        return json_encode($assigned_to_dropdown);
    }

    function all_tasks() {
        $this->access_only_team_members();
        $view_data['project_id'] = 0;
        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->result();
        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . lang("team_member") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value, "isSelected" => true);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);

            }
        }


        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);
        $this->template->rander("projects/tasks/my_tasks", $view_data);
    }

    function task_view() {

        $task_id = $this->input->post('id');
        $model_info = $this->Tasks_model->get_details(array("id" => $task_id))->row();
        if (!$model_info->id) {
            show_404();
        }
        $this->init_project_permission_checker($model_info->project_id);

        if (!$this->can_view_tasks($model_info->project_id)) {
            redirect("forbidden");
        }

        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['can_comment_on_tasks'] = $this->can_comment_on_tasks();

        $view_data['model_info'] = $model_info;
        $view_data['collaborators'] = $this->_get_collaborators($model_info->collaborator_list);

        $task_labels = "";
        if ($model_info->labels) {
            $labels = explode(",", $model_info->labels);
            foreach ($labels as $label) {
                $task_labels .= "<span class='label label-info'  title='Label'>" . $label . "</span> ";
            }
        }

        $view_data['labels'] = $task_labels;

        $options = array("task_id" => $task_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['task_id'] = $task_id;

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("tasks", $task_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $this->load->view('projects/tasks/view', $view_data);
    }

    /* task add/edit modal */

    function task_modal_form() {

        $id = $this->input->post('id');
        $view_data['model_info'] = $this->Tasks_model->get_one($id);
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;


        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks()) {
                redirect("forbidden");
            }
        }


        $view_data['milestones_dropdown'] = array(0 => "None") + $this->Milestones_model->get_dropdown_list(array("title"), "id", array("project_id" => $project_id));

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->result();
        $project_members_dropdown = array("" => "-");
        $collaborators_dropdown = array();
        foreach ($project_members as $member) {
            $project_members_dropdown[$member->user_id] = $member->member_name;
            $collaborators_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
        }
        $view_data['assign_to_dropdown'] = $project_members_dropdown;
        $view_data['collaborators_dropdown'] = $collaborators_dropdown;

        $labels = explode(",", $this->Tasks_model->get_label_suggestions($project_id));
        $label_suggestions = array();
        foreach ($labels as $label) {
            if ($label && !in_array($label, $label_suggestions)) {
                $label_suggestions[] = $label;
            }
        }
        if (!count($label_suggestions)) {
            $label_suggestions = array("0" => "");
        }
        $view_data['label_suggestions'] = $label_suggestions;
        $view_data['points_dropdown'] = array(1 => "1 " . lang("point"), 2 => "2 " . lang("points"), 3 => "3 " . lang("points"), 4 => "4 " . lang("points"), 5 => "5 " . lang("points"));

        $view_data['project_id'] = $project_id;

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client") {
            $view_data['show_assign_to_dropdown'] = false;
        } else {
            //set default assigne to for new tasks
            if (!$id && !$view_data['model_info']->assigned_to) {
                $view_data['model_info']->assigned_to = $this->login_user->id;
            }
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $this->load->view('projects/tasks/modal_form', $view_data);
    }

    /* insert/upadate a task */

    function save_task() {

        $project_id = $this->input->post('project_id');
        $id = $this->input->post('id');

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks()) {
                redirect("forbidden");
            }
        }


        $assigned_to = $this->input->post('assigned_to');
        $collaborators = $this->input->post('collaborators');

        $data = array(
            "title" => $this->input->post('title'),
            "description" => $this->input->post('description'),
            "project_id" => $project_id,
            "milestone_id" => $this->input->post('milestone_id'),
            "points" => $this->input->post('points'),
            "status" => $this->input->post('status'),
            "labels" => $this->input->post('labels'),
            "start_date" => $this->input->post('start_date') ? $this->input->post('start_date') : "0000-00-00",
            "deadline" => $this->input->post('deadline') ? $this->input->post('deadline') : "0000-00-00"
        );


        //clint can't save the assign to and collaborators
        if ($this->login_user->user_type == "client") {
            if (!$id) { //it's new data to save
                $data["assigned_to"] = 0;
                $data["collaborators"] = "";
            }
        } else {
            $data["assigned_to"] = $assigned_to;
            $data["collaborators"] = $collaborators;
        }

        $save_id = $this->Tasks_model->save($data, $id);
        if ($save_id) {
            save_custom_fields("tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            if ($id) {
                //updated
                log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            } else {
                //created
                log_notification("project_task_created", array("project_id" => $project_id, "task_id" => $save_id));
            }

            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upadate a task status */

    function save_task_status($id = 0) {
        $this->access_only_team_members();
        $data = array(
            "status" => $this->input->post('value')
        );

        $save_id = $this->Tasks_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, "message" => lang('record_saved')));

            $task_info = $this->Tasks_model->get_one($save_id);

            log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    /* delete or undo a task */

    function delete_task() {

        $id = $this->input->post('id');
        $info = $this->Tasks_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_tasks()) {
            redirect("forbidden");
        }

        if ($this->input->post('undo')) {
            if ($this->Tasks_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_task_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Tasks_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));

                $task_info = $this->Tasks_model->get_one($id);
                log_notification("project_task_deleted", array("project_id" => $task_info->project_id, "task_id" => $id));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of tasks, prepared for datatable  */

    function tasks_list_data($project_id = 0) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_tasks($project_id)) {
            redirect("forbidden");
        }
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $status = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";
        $milestone_id = $this->input->post('milestone_id');
        $options = array(
            "project_id" => $project_id,
            "assigned_to" => $this->input->post('assigned_to'),
            "deadline" => $this->input->post('deadline'),
            "status" => $status,
            "milestone_id" => $milestone_id,
            "custom_fields" => $custom_fields
        );

        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of tasks, prepared for datatable  */

    function my_tasks_list_data() {
        $this->access_only_team_members();

        $status = $this->input->post('status') ? implode(",", $this->input->post('status')) : "";
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        $specific_user_id = $this->input->post('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "specific_user_id" => $specific_user_id,
            "status" => $status,
            "project_id" => $project_id,
            "milestone_id" =>  $this->input->post('milestone_id'),
            "deadline" => $this->input->post('deadline'),
            "custom_fields" => $custom_fields
        );

        if (!$this->login_user->is_admin) {
            $options["project_member_id"] = $this->login_user->id; //don't show all tasks to non-admin users
        }


        $list_data = $this->Tasks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of task list table */

    private function _task_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Tasks_model->get_details($options)->row();

        $this->init_project_permission_checker($data->project_id);

        return $this->_make_task_row($data, $custom_fields);
    }

    /* prepare a row of task list table */

    private function _make_task_row($data, $custom_fields) {
        $title = modal_anchor(get_uri("projects/task_view"), $data->title, array("title" => lang('task_info') . " #$data->id", "data-post-id" => $data->id));
        $task_labels = "";
        if ($data->labels) {
            $labels = explode(",", $data->labels);
            foreach ($labels as $label) {
                $task_labels .= "<span class='label label-info clickable'  title='Label'>" . $label . "</span> ";
            }
        }
        $title .= "<span class='pull-right'>" . $task_labels . "</span>";


        $project_title = anchor(get_uri("projects/view/" . $data->project_id), $data->project_title);

        $assigned_to = "-";

        if ($data->assigned_to) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $assigned_to_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->assigned_to_user";
            $assigned_to = get_team_member_profile_link($data->assigned_to, $assigned_to_user);
        }


        $collaborators = $this->_get_collaborators($data->collaborator_list);

        if (!$collaborators) {
            $collaborators = "-";
        }


        $status_class = "";
        $checkbox_class = "checkbox-blank";
        if ($data->status === "to_do") {
            $status_class = "b-warning";
        } else if ($data->status === "in_progress") {
            $status_class = "b-primary";
        } else {
            $checkbox_class = "checkbox-checked";
            $status_class = "b-success";
        }

        if ($this->login_user->user_type == "staff") {
            //show changeable status checkbox and link to team members
            $check_status = js_anchor("<span class='$checkbox_class'></span>", array('title' => "", "class" => "", "data-id" => $data->id, "data-value" => $data->status === "done" ? "to_do" : "done", "data-act" => "update-task-status-checkbox")) . $data->id;
            $status = js_anchor(lang($data->status), array('title' => "", "class" => "", "data-id" => $data->id, "data-value" => $data->status, "data-act" => "update-task-status"));
        } else {
            //don't show clickable checkboxes/status to client
            if ($checkbox_class == "checkbox-blank") {
                $checkbox_class = "checkbox-un-checked";
            }
            $check_status = "<span class='$checkbox_class'></span> " . $data->id;
            $status = lang($data->status);
        }



        $deadline_text = "-";
        if ($data->deadline) {
            $deadline_text = format_to_date($data->deadline, false);
            if (get_my_local_time("Y-m-d") > $data->deadline && $data->status != "done") {
                $deadline_text = "<span class='text-danger'>" . $deadline_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->deadline && $data->status != "done") {
                $deadline_text = "<span class='text-warning'>" . $deadline_text . "</span> ";
            }
        }


        $start_date = "-";
        if ($data->start_date * 1) {
            $start_date = format_to_date($data->start_date);
        }

        $options = "";
        if ($this->can_edit_tasks()) {
            $options .= modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_task'), "data-post-id" => $data->id));
        }
        if ($this->can_delete_tasks()) {
            $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_task'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_task"), "data-action" => "delete"));
        }

        $row_data = array(
            $status_class,
            $check_status,
            $title,
            $data->start_date,
            $start_date,
            $data->deadline,
            $deadline_text,
            $project_title,
            $assigned_to,
            $collaborators,
            $status
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = $options;

        return $row_data;
    }

    private function _get_collaborators($collaborator_list) {
        $collaborators = "";
        if ($collaborator_list) {

            $collaborators_array = explode(",", $collaborator_list);
            foreach ($collaborators_array as $collaborator) {
                $collaborator_parts = explode("--::--", $collaborator);

                $collaborator_id = get_array_value($collaborator_parts, 0);
                $collaborator_name = get_array_value($collaborator_parts, 1);

                $image_url = get_avatar(get_array_value($collaborator_parts, 2));
                $collaboratr_image = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span>";
                $collaborators .= get_team_member_profile_link($collaborator_id, $collaboratr_image, array("title" => $collaborator_name));
            }
        }
        return $collaborators;
    }

    /* load comments view */

    function comments($project_id) {
        $this->access_only_team_members();

        $options = array("project_id" => $project_id);
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* load comments view */

    function customer_feedback($project_id) {
        $options = array("customer_feedback_id" => $project_id); //customer feedback id and project id is same
        $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
        $view_data['customer_feedback_id'] = $project_id;
        $this->load->view("projects/comments/index", $view_data);
    }

    /* save project comments */

    function save_comment() {
        $id = $this->input->post('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "project_comment");

        $project_id = $this->input->post('project_id');
        $task_id = $this->input->post('task_id');
        $file_id = $this->input->post('file_id');
        $customer_feedback_id = $this->input->post('customer_feedback_id');
        $comment_id = $this->input->post('comment_id');


        $data = array(
            "created_by" => $this->login_user->id,
            "created_at" => get_current_utc_time(),
            "project_id" => $project_id,
            "file_id" => $file_id ? $file_id : 0,
            "task_id" => $task_id ? $task_id : 0,
            "customer_feedback_id" => $customer_feedback_id ? $customer_feedback_id : 0,
            "comment_id" => $comment_id ? $comment_id : 0,
            "description" => $this->input->post('description'),
            "files" => $files_data
        );

        $save_id = $this->Project_comments_model->save_comment($data, $id);
        if ($save_id) {
            $response_data = "";
            $options = array("id" => $save_id);

            if ($this->input->post("reload_list")) {
                $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
                $response_data = $this->load->view("projects/comments/comment_list", $view_data, true);
            }
            echo json_encode(array("success" => true, "data" => $response_data, 'message' => lang('comment_submited')));


            $comment_info = $this->Project_comments_model->get_one($save_id);

            $notification_options = array("project_id" => $comment_info->project_id, "project_comment_id" => $save_id);

            if ($comment_info->file_id) { //file comment
                $notification_options["project_file_id"] = $comment_info->file_id;
                log_notification("project_file_commented", $notification_options);
            } else if ($comment_info->task_id) { //task comment
                $notification_options["task_id"] = $comment_info->task_id;
                log_notification("project_task_commented", $notification_options);
            } else if ($comment_info->customer_feedback_id) {  //customer feedback comment
                $notification_options["project_comment_id"] = $comment_info->comment_id;

                if ($comment_id) {
                    log_notification("project_customer_feedback_replied", $notification_options);
                } else {
                    log_notification("project_customer_feedback_added", $notification_options);
                }
            } else {  //project comment
                if ($comment_id) {
                    log_notification("project_comment_replied", $notification_options);
                } else {
                    log_notification("project_comment_added", $notification_options);
                }
            }
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function delete_comment($id = 0) {

        if (!$id) {
            exit();
        }

        $comment_info = $this->Project_comments_model->get_one($id);

        //only admin and creator can delete the comment
        if (!($this->login_user->is_admin || $comment_info->created_by == $this->login_user->id)) {
            redirect("forbidden");
        }


        //delete the comment and files
        if ($this->Project_comments_model->delete($id) && $comment_info->files) {

            //delete the files
            $file_path = get_setting("timeline_file_path");
            $files = unserialize($comment_info->files);

            foreach ($files as $file) {
                $source_path = $file_path . get_array_value($file, "file_name");
                delete_file_from_directory($source_path);
            }
        }
    }

    /* load all replies of a comment */

    function view_comment_replies($comment_id) {
        $view_data['reply_list'] = $this->Project_comments_model->get_details(array("comment_id" => $comment_id))->result();
        $this->load->view("projects/comments/reply_list", $view_data);
    }

    /* show comment reply form */

    function comment_reply_form($comment_id, $type = "project", $type_id = 0) {
        $view_data['comment_id'] = $comment_id;


        if ($type === "project") {
            $view_data['project_id'] = $type_id;
        } else if ($type === "task") {
            $view_data['task_id'] = $type_id;
        } else if ($type === "file") {
            $view_data['file_id'] = $type_id;
        }
        $this->load->view("projects/comments/reply_form", $view_data);
    }

    /* load files view */

    function files($project_id) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_files()) {
            redirect("forbidden");
        }

        $view_data['can_add_files'] = $this->can_add_files();
        $options = array("project_id" => $project_id);
        $view_data['files'] = $this->Project_files_model->get_details($options)->result();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/files/index", $view_data);
    }

    function view_file($file_id = 0) {
        $file_info = $this->Project_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {

            $this->init_project_permission_checker($file_info->project_id);

            if (!$this->can_view_files()) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = $this->can_comment_on_files();


            $file_url = get_file_uri(get_setting("project_file_path") . $file_info->project_id . "/" . $file_info->file_name);
            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $options = array("file_id" => $file_id);
            $view_data['comments'] = $this->Project_comments_model->get_details($options)->result();
            $view_data['file_id'] = $file_id;
            $this->load->view("projects/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->Project_files_model->get_one($this->input->post('id'));
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_files()) {
            redirect("forbidden");
        }

        $view_data['project_id'] = $project_id;
        $this->load->view('projects/files/modal_form', $view_data);
    }

    /* save project file data and move temp file to parmanent file directory */

    function save_file() {

        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if (!$this->can_add_files()) {
            redirect("forbidden");
        }


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_setting("project_file_path") . $project_id . "/";

        //process the fiiles which has been uploaded by dropzone

        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "project_id" => $project_id,
                        "file_name" => $new_file_name,
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),

                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->Project_files_model->save($data);

                    log_notification("project_file_added", array("project_id" => $project_id, "project_file_id" => $success));
                } else {
                    $success = false;
                }
            }
        }
        //process the files which has been submitted manually
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $description = $this->input->post('description');
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];

                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $data = array(
                            "project_id" => $project_id,
                            "file_name" => $new_file_name,
                            "description" => get_array_value($description, $key),
                            "file_size" => $file_size,
                            "created_at" => $now,
                            "uploaded_by" => $this->login_user->id
                        );
                        $success = $this->Project_files_model->save($data);

                        log_notification("project_file_added", array("project_id" => $project_id, "project_file_id" => $success));
                    }
                }
            }
        }

        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for project */

    function validate_project_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->Project_files_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_files()) {
            redirect("forbidden");
        }

        if ($this->Project_files_model->delete($id)) {

            //delete the files
            $file_path = get_setting("project_file_path");
            delete_file_from_directory($file_path . $info->project_id . "/" . $info->file_name);

            log_notification("project_file_deleted", array("project_id" => $info->project_id, "project_file_id" => $id));
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->Project_files_model->get_one($id);

        $this->init_project_permission_checker($file_info->project_id);
        if (!$this->can_view_files()) {
            redirect("forbidden");
        }
        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->project_id . "/" . $file_info->file_name)));

        download_app_files(get_setting("project_file_path"), $file_data);
    }

    /* download files by zip */

    function download_comment_files($id) {

        $info = $this->Project_comments_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);
        if ($this->login_user->user_type == "client" && !$this->is_clients_project) {
            redirect("forbidden");

        } else if ($this->login_user->user_type == "staff" && !$this->is_user_a_project_member) {
            redirect("forbidden");
        }

        download_app_files(get_setting("timeline_file_path"), $info->files);
    }

    /* list of files, prepared for datatable  */

    function files_list_data($project_id = 0) {

        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_files()) {
            redirect("forbidden");
        }


        $options = array("project_id" => $project_id);
        $list_data = $this->Project_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of file list table */

    private function _file_row_data($id) {
        $options = array("id" => $id);
        $data = $this->Project_files_model->get_details($options)->row();

        $this->init_project_permission_checker($data->project_id);
        return $this->_make_file_row($data);
    }

    /* prepare a row of file list table */

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        if ($data->uploaded_by_user_type == "staff") {
            $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);
        } else {
            $uploaded_by = get_client_contact_profile_link($data->uploaded_by, $uploaded_by);
        }

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "1", "data-url" => get_uri("projects/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("projects/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
        if ($this->can_delete_files()) {
            $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_file"), "data-action" => "delete-confirmation"));
        }

        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    /* load notes view */

    function notes($project_id) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/notes/index", $view_data);
    }

    /* load history view */

    function history($offset = 0, $log_for = "", $log_for_id = "", $log_type = "", $log_type_id = "") {
        $this->access_only_team_members();
        $view_data['offset'] = $offset;
        $view_data['activity_logs_params'] = array("log_for" => $log_for, "log_for_id" => $log_for_id, "log_type" => $log_type, "log_type_id" => $log_type_id, "limit" => 20, "offset" => $offset);
        $this->load->view("projects/history/index", $view_data);
    }


    /* load project members view */

    function members($project_id = 0) {
        $this->access_only_team_members();
        $view_data['project_id'] = $project_id;
        $this->load->view("projects/project_members/index", $view_data);
    }

    /* load payments tab  */

    function payments($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();
            $view_data['project_id'] = $project_id;
            $this->load->view("projects/payments/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_id'] = $project_id;
            $view_data['project_info'] = $this->Projects_model->get_details(array("id" => $project_id))->row();

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("projects/invoices/index", $view_data);
        }
    }

    /* load payments tab  */

    function expenses($project_id) {
        $this->access_only_team_members();
        if ($project_id) {
            $view_data['project_id'] = $project_id;
            $this->load->view("projects/expenses/index", $view_data);
        }
    }

    function change_status($project_id, $status) {
        if ($project_id && $this->can_create_projects() && ($status == "completed" || $status == "canceled" || $status == "open" )) {
            $status_data = array("status" => $status);
            $this->Projects_model->save($status_data, $project_id);
        }
    }

    function gantt($project_id = 0) {


        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            if (!$this->can_view_gantt()) {
                redirect("forbidden");
            }

            $view_data['project_id'] = $project_id;

            //prepare members list
            $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($project_id);
            $view_data['project_members_dropdown'] = $this->_get_project_members_dropdown_list();

            $view_data['show_project_members_dropdown'] = true;
            if ($this->login_user->user_type == "client") {
                $view_data['show_project_members_dropdown'] = false;
            }


            $status_dropdown = array(
                array("id" => "", "text" => "- " . lang("status") . " -"),
                array("id" => "to_do", "text" => lang("to_do")),
                array("id" => "in_progress", "text" => lang("in_progress")),
                array("id" => "done", "text" => lang("done"))
            );


            $view_data['status_dropdown'] = json_encode($status_dropdown);

            $this->load->view("projects/gantt/index", $view_data);
        }
    }

    //prepare gantt data for gantt chart
    function gantt_data($project_id = 0, $group_by = "milestones", $filter_id = 0, $status = "") {
        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            if (!$this->can_view_gantt()) {
                redirect("forbidden");
            }

            $options = array("status" => $status);

            if ($group_by == "milestones") {
                $options["milestone_id"] = $filter_id;
            } else if ($group_by == "members") {
                $options["assigned_to"] = $filter_id;
            }

            $gantt_data = $this->Projects_model->get_gantt_data($project_id, $options);
            $now = get_current_utc_time("Y-m-d");

            $group_array = array();
            $series = array();
            $status_class = array("to_do" => "label-warning", "in_progress" => "label-primary", "done" => "label-success");

            foreach ($gantt_data as $data) {


                $start_date = $data->start_date * 1 ? $data->start_date : $now;
                $end_date = $data->end_date * 1 ? $data->end_date : $data->milestone_due_date;

                if (!$end_date * 1) {
                    $end_date = $start_date;
                }

                $group_id = 0;

                if ($group_by === "milestones") {
                    $group_id = $data->milestone_id;
                    $group_array[$group_id] = array("id" => $group_id, "name" => $data->milestone_title);
                } else {
                    $group_id = $data->assigned_to;
                    $group_array[$data->assigned_to] = array("id" => $group_id, "name" => $data->assigned_to_name);
                }

                $class = get_array_value($status_class, $data->status);

                //has deadline? change the color of date based on status
                if ($data->status === "to_do" && $data->end_date * 1 && get_my_local_time("Y-m-d") > $data->end_date) {
                    $class = "label-danger";
                }

                $series[$group_id][] = array("name" => modal_anchor(get_uri("projects/task_view"), $data->task_title, array("title" => lang('task_info') . " #$data->task_id", "data-post-id" => $data->task_id)), "start" => $start_date, "end" => $end_date, "class" => $class);
            }

            $gantt = array();
            foreach ($group_array as $group_value) {
                $gantt_section = $group_value;

                if (!get_array_value($group_value, "name")) {
                    $gantt_section["name"] = lang("not_specified");
                } else {
                    $gantt_section["name"] = get_array_value($group_value, "name");
                }

                $gantt_section["id"] = get_array_value($group_value, "id");
                $gantt_section["series"] = get_array_value($series, get_array_value($group_value, "id"));
                $gantt[] = $gantt_section;
            }
            echo json_encode($gantt);
        }
    }

    /* load project settings modal */

    function settings_modal_form() {

        $project_id = $this->input->post('project_id');

        //onle team members who can create project, he/she can update settings
        if (!$project_id || !($this->login_user->user_type == "staff" && $this->can_create_projects())) {
            redirect("forbidden");
        }


        $this->init_project_settings($project_id);

        $view_data['project_id'] = $project_id;

        $this->load->view('projects/settings/modal_form', $view_data);
    }

    /* save project settings */

    function save_settings() {

        $project_id = $this->input->post('project_id');

        //onle team members who can create project, he/she can update settings
        if (!$project_id || !($this->login_user->user_type == "staff" && $this->can_create_projects())) {
            redirect("forbidden");
        }

        validate_submitted_data(array(
            "project_id" => "required"
        ));

        $settings = array("client_can_view_timesheet");

        foreach ($settings as $setting) {
            $value = $this->input->post($setting);
            if (!$value) {
                $value = "";
            }

            $this->Project_settings_model->save_setting($project_id, $setting, $value);
        }

        echo json_encode(array("success" => true, 'message' => lang('settings_updated')));
    }

	private function delete_cascade_project($project_id){

		//ELIMINA RELACIONAMIENTOS
		$rules = $this->Rule_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($rules){
			foreach($rules as $rule){
				$this->Rule_model->delete($rule->id);
			}
		}

		$assignments = $this->Assignment_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($assignments){
			foreach($assignments as $assignment){
				$assignments_combinations = $this->Assignment_combinations_model->get_all_where(array("id_asignacion" => $assignment->id, "deleted" => 0))->result();
				if($assignments_combinations){
					foreach($assignments_combinations as $assig_comb){
						$this->Assignment_combinations_model->delete($assig_comb->id);
					}
				}
				$this->Assignment_model->delete($assignment->id);
			}
		}

		$calculations = $this->Calculation_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($calculations){
			foreach($calculations as $calculation){
				$this->Calculation_model->delete($calculation->id);
			}
		}

		//ELIMINA FORMULARIOS, REALCION_PROYECTO_FORMULARIO Y VALORES FORMULARIOS
		$form_rel_project_model = $this->Form_rel_project_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($form_rel_project_model){
			foreach($form_rel_project_model as $frpm){

				$forms_values = $this->Form_values_model->get_all_where(array("id_formulario_rel_proyecto" => $frpm->id, "deleted" => 0))->result();
				if($forms_values){
					foreach($forms_values as $fv){
						$this->Form_values_model->delete($fv->id);
					}
				}

				$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
				if($campos_rel_formulario){
					foreach($campos_rel_formulario as $crf){
						$this->Field_rel_form_model->delete($crf->id);
					}
				}

				$form_rel_materiales = $this->Form_rel_material_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
				if($form_rel_materiales){
					foreach($form_rel_materiales as $form_rel_material){
						$this->Form_rel_material_model->delete($form_rel_material->id);
					}
				}

				$form_rel_materiales_rel_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
				if($form_rel_materiales_rel_categorias){
					foreach($form_rel_materiales_rel_categorias as $form_rel_material_rel_categoria){
						$this->Form_rel_materiales_rel_categorias_model->delete($form_rel_material_rel_categoria->id);
					}
				}

				$this->Forms_model->delete($frpm->id_formulario);
				$this->Form_rel_project_model->delete($frpm->id);

			}
		}

		//ELIMINA UNIDADES FUNCIONALES
		$functional_unit = $this->Functional_units_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($functional_unit){
			foreach($functional_unit as $fn){
				$this->Functional_units_model->delete($fn->id);
			}
		}

		//ELIMINA COMPROMISOS
		$compromise = $this->Compromises_rca_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($compromise->id){

			$evaluated_compromises = $this->Evaluated_rca_compromises_model->get_all_where(array("id_compromiso" => $compromise->id ,"deleted" => 0))->result();
			if($evaluated_compromises){
				foreach($evaluated_compromises as $evaluated_compromise){
					$this->Evaluated_rca_compromises_model->delete($evaluated_compromise->id);
				}
			}

			$compromises_rel_fields = $this->Compromises_rca_rel_fields_model->get_all_where(array("id_compromiso" => $compromise->id ,"deleted" => 0))->result();
			if($compromises_rel_fields){
				foreach($compromises_rel_fields as $compromises_rel_field){
					$this->Compromises_rca_rel_fields_model->delete($compromises_rel_field->id);
				}
			}

			$values_compromises = $this->Values_compromises_rca_model->get_all_where(array("id_compromiso" => $compromise->id , "deleted" => 0))->result();

			if($values_compromises){
				foreach($values_compromises as $values_compromise){

					$compromises_compliance_evaluations = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => $values_compromise->id , "deleted" => 0))->result();
					if($compromises_compliance_evaluations){
						foreach($compromises_compliance_evaluations as $compromises_compliance_evaluation){

							$compromises_compliance_evidences = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" =>$compromises_compliance_evaluation->id , "deleted" => 0))->result();
							if($compromises_compliance_evidences){
								foreach($compromises_compliance_evidences as $compromise_compliance_evidences){
									$this->Compromises_compliance_evidences_model->delete($compromise_compliance_evidences->id);
								}
							}
							$this->Compromises_compliance_evaluation_rca_model->delete($compromises_compliance_evaluation->id);
						}
					}
					$this->Values_compromises_rca_model->delete($values_compromise->id);
				}
			}

			$this->Compromises_rca_model->delete($compromise->id);

		}

		$compromise = $this->Compromises_reportables_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($compromise->id){

			$compromises_rel_fields = $this->Compromises_reportables_rel_fields_model->get_all_where(array("id_compromiso" => $compromise->id ,"deleted" => 0))->result();
			if($compromises_rel_fields){
				foreach($compromises_rel_fields as $compromises_rel_field){
					$this->Compromises_reportables_rel_fields_model->delete($compromises_rel_field->id);
				}
			}

			$values_compromises = $this->Values_compromises_reportables_model->get_all_where(array("id_compromiso" => $compromise->id , "deleted" => 0))->result();

			if($values_compromises){
				foreach($values_compromises as $values_compromise){

					$compromises_compliance_evaluations = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(array("id_valor_compromiso" => $values_compromise->id , "deleted" => 0))->result();
					if($compromises_compliance_evaluations){
						foreach($compromises_compliance_evaluations as $compromises_compliance_evaluation){

							$compromises_compliance_evidences = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" =>$compromises_compliance_evaluation->id , "deleted" => 0))->result();
							if($compromises_compliance_evidences){
								foreach($compromises_compliance_evidences as $compromise_compliance_evidences){
									$this->Compromises_compliance_evidences_model->delete($compromise_compliance_evidences->id);
								}
							}
							$this->Compromises_compliance_evaluation_reportables_model->delete($compromises_compliance_evaluation->id);
						}
					}
					$this->Values_compromises_reportables_model->delete($values_compromise->id);
				}
			}

			$this->Compromises_reportables_model->delete($compromise->id);

		}

		//ELIMINA PERMISOS
		$permitting = $this->Permitting_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($permitting->id){

			$permitting_rel_fields = $this->Permitting_rel_fields_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
			if($permitting_rel_fields){
				foreach($permitting_rel_fields as $permitting_rel_field){
					$this->Permitting_rel_fields_model->delete($permitting_rel_field->id);
				}
			}

			$evaluated_permittings = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
			if($evaluated_permittings){
				foreach($evaluated_permittings as $evaluated_permitting){
					$this->Evaluated_permitting_model->delete($evaluated_permitting->id);
				}
			}

			$values_permitting = $this->Values_permitting_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
			if($values_permitting){
				foreach($values_permitting as $value_permitting){

					$permitting_procedure_evaluations = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_valor_permiso" =>$value_permitting->id , "deleted" => 0))->result();
					if($permitting_procedure_evaluations){
						foreach($permitting_procedure_evaluations as $permitting_procedure_evaluation){

							$permittings_procedure_evidences = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_procedure_evaluation->id , "deleted" => 0))->result();
							if($permittings_procedure_evidences){
								foreach($permittings_procedure_evidences as $permitting_procedure_evidences){
									$this->Permitting_procedure_evidences_model->delete($permitting_procedure_evidences->id);
								}
							}
							$this->Permitting_procedure_evaluation_model->delete($permitting_procedure_evaluation->id);
						}
					}
					$this->Values_permitting_model->delete($value_permitting->id);
				}
			}
			$this->Permitting_model->delete($permitting->id);

		}

		//ELIMINA COMUNIDADES
		//ELIMINA ACUERDOS
		$agreement_matrix_config = $this->Agreements_matrix_config_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($agreement_matrix_config->id){

			$agreements_rel_fields = $this->Agreements_rel_fields_model->get_all_where(array("id_agreement_matrix_config" => $agreement_matrix_config->id, "deleted" => 0))->result();
			if($agreements_rel_fields){
				foreach($agreements_rel_fields as $agreement_rel_field){
					$this->Agreements_rel_fields_model->delete($agreement_rel_field->id);
				}
			}

			$values_agreements = $this->Values_agreements_model->get_all_where(array("id_agreement_matrix_config" =>$agreement_matrix_config->id ,"deleted" => 0))->result();
			if($values_agreements){
				foreach($values_agreements as $value_agreement){

					$agreements_monitoring = $this->Agreements_monitoring_model->get_all_where(array("id_valor_acuerdo" =>$value_agreement->id, "deleted" => 0))->result();
					if($agreements_monitoring){
						foreach($agreements_monitoring as $agreement_monitoring){

							$agreements_evidences = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" =>$agreement_monitoring->id , "deleted" => 0))->result();
							if($agreements_evidences){
								foreach($agreements_evidences as $agreement_evidence){
									$this->Agreements_evidences_model->delete($agreement_evidence->id);
								}
							}
							$this->Agreements_monitoring_model->delete($agreement_monitoring->id);
						}
					}
					$this->Values_agreements_model->delete($value_agreement->id);
				}
			}
			$this->Agreements_matrix_config_model->delete($agreement_matrix_config->id);

		}

		//ELIMINA FEEDBACK
		$feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($feedback_matrix_config->id){

			$feedbacks_rel_fields = $this->Feedback_rel_fields_model->get_all_where(array("id_feedback_matrix_config" => $feedback_matrix_config->id, "deleted" => 0))->result();
			if($feedbacks_rel_fields){
				foreach($feedbacks_rel_fields as $feedback_rel_field){
					$this->Feedback_rel_fields_model->delete($feedback_rel_field->id);
				}
			}

			$values_feedbacks = $this->Values_feedback_model->get_all_where(array("id_feedback_matrix_config" => $feedback_matrix_config->id, "deleted" => 0))->result();
			if($values_feedbacks){
				foreach($values_feedbacks as $value_feedback){

					$feedbacks_monitoring = $this->Feedback_monitoring_model->get_all_where(array("id_valor_feedback" => $value_feedback->id, "deleted" => 0))->result();
					if($feedbacks_monitoring){
						foreach($feedbacks_monitoring as $feedback_monitoring){

							$feedbacks_monitoring_evidences = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $feedback_monitoring->id, "deleted" => 0))->result();
							if($feedbacks_monitoring_evidences){
								foreach($feedbacks_monitoring_evidences as $feedback_monitoring_evidences){
									$this->Feedback_monitoring_evidences_model->delete($feedback_monitoring_evidences->id);
								}
							}
							$this->Feedback_monitoring_model->delete($feedback_monitoring->id);
						}
					}
					$this->Values_feedback_model->delete($value_feedback->id);
				}
			}
			$this->Feedback_matrix_config_model->delete($feedback_matrix_config->id);

		}

		//ELIMINA STAKEHOLDERS
		$stakeholder_matrix_config = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $project_id, "deleted" => 0));
		if($stakeholder_matrix_config->id){

			$stakeholders_rel_fields = $this->Stakeholders_rel_fields_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix_config->id, "deleted" => 0))->result();
			if($stakeholders_rel_fields){
				foreach($stakeholders_rel_fields as $stakeholder_rel_field){
					$this->Stakeholders_rel_fields_model->delete($stakeholder_rel_field->id);
				}
			}

			$values_stakeholders = $this->Values_stakeholders_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix_config->id, "deleted" => 0))->result();
			if($values_stakeholders){
				foreach($values_stakeholders as $value_stakeholder){
					$this->Values_stakeholders_model->delete($value_stakeholder->id);
				}
			}
			$this->Stakeholders_matrix_config_model->delete($stakeholder_matrix_config->id);

		}

		//ELIMINA UMBRALES
		$thresholds = $this->Thresholds_model->get_all_where(array("id_project" => $project_id, "deleted" => 0))->result();
		if($thresholds){
			foreach($thresholds as $threshold){
				$this->Thresholds_model->delete($threshold->id);
			}
		}

		//ELIMINA INDICADORES DE RESIDUOS
		$indicators = $this->Indicators_model->get_all_where(array("id_project" => $project_id, "deleted" => 0))->result();
		if($indicators){
			foreach($indicators as $indicator){

				$client_indicators = $this->Client_indicators_model->get_all_where(array("id_indicador" => $indicator->id, "deleted" => 0))->result();
				if($client_indicators){
					foreach($client_indicators as $client_indicator){
						$this->Client_indicators_model->delete($client_indicator->id);
					}
				}
				$this->Indicators_model->delete($indicator->id);
			}
		}

		//ELIMINA CONFIGURACIONES
		$generals_settings = $this->General_settings_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($generals_settings){
			foreach($generals_settings as $general_setting){
				$this->General_settings_model->delete($general_setting->id);
			}
		}

		$reports_configuration = $this->Reports_configuration_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($reports_configuration){
			foreach($reports_configuration as $report_configuration){
				$this->Reports_configuration_model->delete($report_configuration->id);
			}
		}

		$modules_availabilitys = $this->Module_availability_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($modules_availabilitys){
			foreach($modules_availabilitys as $module_availability){
				$this->Module_availability_model->delete($module_availability->id);
			}
		}

		$modules_footprint_units = $this->Module_footprint_units_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($modules_footprint_units){
			foreach($modules_footprint_units as $module_footprint_unit){
				$this->Module_footprint_units_model->delete($module_footprint_unit->id);
			}
		}

		$reports_units_settings = $this->Reports_units_settings_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($reports_units_settings){
			foreach($reports_units_settings as $report_unit_setting){
				$this->Reports_units_settings_model->delete($report_unit_setting->id);
			}
		}

		//ELIMINA SUBPROYECTOS
		$subprojects = $this->Subprojects_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($subprojects){
			foreach($subprojects as $subproject){
				$this->Subprojects_model->delete($subproject->id);
			}
		}

		//ELIMINA CAMPOS
		$fields = $this->Fields_model->get_all_where(array("id_proyecto" => $project_id, "deleted" => 0))->result();
		if($fields){
			foreach($fields as $field){
				$this->Fields_model->delete($field->id);
			}
		}

		//ELIMINA OTROS
		$client_environmental_footprints_settings = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_proyecto" => $project_id ,"deleted" => 0))->result();
		if($client_environmental_footprints_settings){
			foreach($client_environmental_footprints_settings as $client_environmental_footprint_setting){
				$this->Client_environmental_footprints_settings_model->delete($client_environmental_footprint_setting->id);
			}
		}

		$clients_waste_settings = $this->Client_waste_settings_model->get_all_where(array("id_proyecto" => $project_id ,"deleted" => 0))->result();
		if($clients_waste_settings){
			foreach($clients_waste_settings as $client_waste_setting){
				$this->Client_waste_settings_model->delete($client_waste_setting->id);
			}
		}

		$client_consumptions_settings = $this->Client_consumptions_settings_model->get_all_where(array("id_proyecto" => $project_id ,"deleted" => 0))->result();
		if($client_consumptions_settings){
			foreach($client_consumptions_settings as $client_consumption_setting){
				$this->Client_consumptions_settings_model->delete($client_consumption_setting->id);
			}
		}

		$client_compromises_settings = $this->Client_compromises_settings_model->get_all_where(array("id_proyecto" => $project_id ,"deleted" => 0))->result();
		if($client_compromises_settings){
			foreach($client_compromises_settings as $client_compromise_setting){
				$this->Client_compromises_settings_model->delete($client_compromise_setting->id);
			}
		}

		$client_permitting_settings = $this->Client_permitting_settings_model->get_all_where(array("id_proyecto" => $project_id ,"deleted" => 0))->result();
		if($client_permitting_settings){
			foreach($client_permitting_settings as $client_permitting_setting){
				$this->Client_permitting_settings_model->delete($client_permitting_setting->id);
			}
		}

		//

		// Elimina KPI Valores
		$kpi_valores = $this->KPI_Values_model->get_all_where(array(
			"id_proyecto" => $project_id,
			"deleted" => 0
		))->result();

		foreach($kpi_valores as $valor){
			$kpi_valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
				"id_kpi_valores" => $valor->id,
				"deleted" => 0
			))->result();
			if($kpi_valores_condicion){
				foreach($kpi_valores_condicion as $condicion){
					$this->KPI_Values_condition_model->delete($condicion->id);
				}
			}
			$this->KPI_Values_model->delete($valor->id);
		}

		// Elimina KPI Reporte
		$kpi_reportes = $this->KPI_Report_structure_model->get_all_where(array(
			"id_proyecto" => $project_id,
			"deleted" => 0
		))->result();

		foreach($kpi_reportes as $reporte){
			$this->KPI_Report_structure_model->delete($reporte->id);
		}

		// Elimina KPI Gráficos
		$kpi_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
			"id_proyecto" => $project_id,
			"deleted" => 0
		))->result();

		foreach($kpi_graficos as $grafico){
			$this->KPI_Charts_structure_model->delete($grafico->id);
		}

	}

	function create_form_folder($client_id, $project_id, $form_id) {
		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$client_id.'/project_'.$project_id.'/form_'.$form_id)) {
			if(mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$client_id.'/project_'.$project_id.'/form_'.$form_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
	}

	private function guardar_estructura_reporte_kpi($opciones_reporte_kpi = array()){

		$id_cliente = get_array_value($opciones_reporte_kpi, "id_cliente");
		$id_fase = get_array_value($opciones_reporte_kpi, "id_fase");
		$id_proyecto = get_array_value($opciones_reporte_kpi, "id_proyecto");

		if($id_fase == "2"){

			$datos = array(
				"construction_sites_considered" 	=> array(
														"valor" => "",
														"codigo" => "D164",
														"descripcion" => lang("desc_construction_sites_considered"),
														"unidad" => "18" // Unidad
												   ),
				"network_electricity_consumption" 				=> array(
														"valor" => "",
														"codigo" => "D10",
														"descripcion" => lang("desc_network_electricity_consumption"),
														"unidad" => "21" // MWh
												   ),
				"electricity_consumption_renewable_source" 	=> array(
														"valor" => "",
														"codigo" => "D179",
														"descripcion" => lang("desc_electricity_consumption_renewable_source"),
														"unidad" => "21" // MWh
												   ),
				"electricity_consumption_diesel" 			=> array(
														"valor" => "",
														"codigo" => "D180",
														"descripcion" => lang("desc_electricity_consumption_diesel"),
														"unidad" => "21" // MWh
												   ),
				"petroleum" 						=> array(
														"valor" => "",
														"codigo" => "D13",
														"descripcion" => lang("desc_petroleum"),
														"unidad" => "1" // t
												   ),
				"gasoline" 						=> array(
														"valor" => "",
														"codigo" => "D15",
														"descripcion" => lang("desc_gasoline"),
														"unidad" => "1" // t
												   ),
				"glp" 							=> array(
														"valor" => "",
														"codigo" => "D17",
														"descripcion" => lang("desc_glp"),
														"unidad" => "1" // t
												   ),
				"natural_gas" 					=> array(
														"valor" => "",
														"codigo" => "D19",
														"descripcion" => lang("desc_natural_gas"),
														//"unidad" => "3" // m3
														"unidad" => "25" // m3 x 10^3
												   ),
				"biodiesel" 			=> array(
														"valor" => "",
														"codigo" => "D205",
														"descripcion" => lang("desc_biodiesel"),
														"unidad" => "1" // t
												   ),
				"concrete" 						=> array(
														"valor" => "",
														"codigo" => "D182",
														"descripcion" => lang("desc_concrete"),
														"unidad" => "1" // t
												   ),
				"recycled_aggregates_concrete" => array(
														"valor" => "",
														"codigo" => "D183",
														"descripcion" => lang("desc_recycled_aggregates_concrete"),
														"unidad" => "1" // t
												   ),
				"sand_gravel_construction" 			=> array(
														"valor" => "",
														"codigo" => "D29",
														"descripcion" => lang("desc_sand_gravel_construction"),
														"unidad" => "1" // t
												   ),
				"structures_steel_pipes" 			=> array(
														"valor" => "",
														"codigo" => "D184",
														"descripcion" => lang("desc_structures_steel_pipes"),
														"unidad" => "1" // t
												   ),
				"reinforcement_bars_concrete" 		=> array(
														"valor" => "",
														"codigo" => "D185",
														"descripcion" => lang("desc_reinforcement_bars_concrete"),
														"unidad" => "1" // t
												   ),
				"sustainable_iron" 			=> array(
														"valor" => "",
														"codigo" => "D186",
														"descripcion" => lang("desc_sustainable_iron"),
														"unidad" => "1" // t
												   ),
				"cement_lime" 					=> array(
														"valor" => "",
														"codigo" => "D31",
														"descripcion" => lang("desc_cement_lime"),
														"unidad" => "1" // t
												   ),
				"biodegradable_oil" 				=> array(
														"valor" => "",
														"codigo" => "D187",
														"descripcion" => lang("desc_biodegradable_oil"),
														"unidad" => "1" // t
												   ),
				"no_biodegradable_oil" 				=> array(
														"valor" => "",
														"codigo" => "D34",
														"descripcion" => lang("desc_no_biodegradable_oil"),
														"unidad" => "1" // t
												   ),
				"dielectric_oil" 			=> array(
														"valor" => "",
														"codigo" => "D35",
														"descripcion" => lang("desc_dielectric_oil"),
														"unidad" => "1" // t
												   ),
				"other_oil" 					=> array(
														"valor" => "",
														"codigo" => "D36",
														"descripcion" => lang("desc_other_oil"),
														"unidad" => "1" // t
												   ),
				"excavated_ground" 				=> array(
														"valor" => "",
														"codigo" => "D189",
														"descripcion" => lang("desc_excavated_ground"),
														"unidad" => "3" // m3
												   ),
				"reused_ground" 			=> array(
														"valor" => "",
														"codigo" => "D190",
														"descripcion" => lang("desc_reused_ground"),
														"unidad" => "3" // m3
												   ),
				"of_which_reused_on_site" 				=> array(
														"valor" => "",
														"codigo" => "D191",
														"descripcion" => lang("desc_of_which_reused_on_site"),
														"unidad" => "3" // m3
												   ),
				"of_which_contaminated_ground_rehab" 		=> array(
														"valor" => "",
														"codigo" => "D192",
														"descripcion" => lang("desc_of_which_contaminated_ground_rehab"),
														"unidad" => "3" // m3
												   ),
				"concrete_bricks_mortar" 	=> array(
														"valor" => "",
														"codigo" => "D194",
														"descripcion" => lang("desc_concrete_bricks_mortar"),
														"unidad" => "3" // m3
												   ),
				"aggregates_demolition" 			=> array(
														"valor" => "",
														"codigo" => "D195",
														"descripcion" => lang("desc_aggregates_demolition"),
														"unidad" => "3" // m3
												   ),
				"structures_demolition" 		=> array(
														"valor" => "",
														"codigo" => "D196",
														"descripcion" => lang("desc_structures_demolition"),
														"unidad" => "1" // t
												   ),
				"ui_drinking_water" 					=> array(
														"valor" => "",
														"codigo" => "D41",
														"descripcion" => lang("desc_ui_drinking_water"),
														"unidad" => "3" // m3
												   ),
				"ui_non_potable_water_surface" 		=> array(
														"valor" => "",
														"codigo" => "D39",
														"descripcion" => lang("desc_ui_non_potable_water_surface"),
														"unidad" => "3" // m3
												   ),
				"ui_non_potable_water_well" 			=> array(
														"valor" => "",
														"codigo" => "D40",
														"descripcion" => lang("desc_ui_non_potable_water_well"),
														"unidad" => "3" // m3
												   ),
				"ui_non_potable_water_rain" 		=> array(
														"valor" => "",
														"codigo" => "D201",
														"descripcion" => lang("desc_ui_non_potable_water_rain"),
														"unidad" => "3" // m3
												   ),
				"ui_non_potable_water_plants_ext" 	=> array(
														"valor" => "",
														"codigo" => "D202",
														"descripcion" => lang("desc_ui_non_potable_water_plants_ext"),
														"unidad" => "3" // m3
												   ),
				"ui_non_potable_water_plants_site" 	=> array(
														"valor" => "",
														"codigo" => "D203",
														"descripcion" => lang("desc_ui_non_potable_water_plants_site"),
														"unidad" => "3" // m3
												   ),
				"uc_drinking_water" 					=> array(
														"valor" => "",
														"codigo" => "D200",
														"descripcion" => lang("desc_uc_drinking_water"),
														"unidad" => "3" // m3
												   ),
				"uc_non_potable_water_surface" 		=> array(
														"valor" => "",
														"codigo" => "D198",
														"descripcion" => lang("desc_uc_non_potable_water_surface"),
														"unidad" => "3" // m3
												   ),
				"uc_non_potable_water_well" 			=> array(
														"valor" => "",
														"codigo" => "D199",
														"descripcion" => lang("desc_uc_non_potable_water_well"),
														"unidad" => "3" // m3
												   ),
				"uc_non_potable_water_rain" 		=> array(
														"valor" => "",
														"codigo" => "D201",
														"descripcion" => lang("desc_uc_non_potable_water_rain"),
														"unidad" => "3" // m3
												   ),
				"uc_non_potable_water_plants_ext" 	=> array(
														"valor" => "",
														"codigo" => "D202",
														"descripcion" => lang("desc_uc_non_potable_water_plants_ext"),
														"unidad" => "3" // m3
												   ),
				"uc_non_potable_water_plants_site" 	=> array(
														"valor" => "",
														"codigo" => "D203",
														"descripcion" => lang("desc_uc_non_potable_water_plants_site"),
														"unidad" => "3" // m3
												   ),
				"accidental_spills" 		=> array(
														"valor" => "",
														"codigo" => "D59",
														"descripcion" => lang("desc_accidental_spills"),
														"unidad" => "3" // m3
												   ),
				"significant_events" 		=> array(
														"valor" => "",
														"codigo" => "D175",
														"descripcion" => lang("desc_significant_events"),
														"unidad" => "18" // Unidad
												   ),
				"np_waste_production" 		=> array(
														"valor" => "",
														"codigo" => "D100",
														"descripcion" => lang("desc_np_waste_production"),
														"unidad" => "1" // t
												   ),
				"np_waste_recycling" 		=> array(
														"valor" => "",
														"codigo" => "D101",
														"descripcion" => lang("desc_np_waste_recycling"),
														"unidad" => "1" // t
												   ),
				"np_reused_waste" 		=> array(
														"valor" => "",
														"codigo" => "D102",
														"descripcion" => lang("desc_np_reused_waste"),
														"unidad" => "1" // t
												   ),
				"p_waste_production" 				=> array(
														"valor" => "",
														"codigo" => "D152",
														"descripcion" => lang("desc_p_waste_production"),
														"unidad" => "1" // t
												   ),
				"p_waste_recycling" 		=> array(
														"valor" => "",
														"codigo" => "D153",
														"descripcion" => lang("desc_p_waste_recycling"),
														"unidad" => "1" // t
												   ),
				"p_reused_waste" 		=> array(
														"valor" => "",
														"codigo" => "-",
														"descripcion" => lang("desc_p_reused_waste"),
														"unidad" => "1" // t
												   ),
				"occupied_surface_construction" 	=> array(
														"valor" => "",
														"codigo" => "D159",
														"descripcion" => lang("desc_occupied_surface_construction"),
														"unidad" => "14" // ha
												   ),
				"total_co2_offset" 				=> array(
														"valor" => "",
														"codigo" => "-",
														"descripcion" => lang("desc_total_co2_offset"),
														"unidad" => "1" // t
												   ),
				"n_biodiversity_projects" 	=> array(
														"valor" => "",
														"codigo" => "-",
														"descripcion" => lang("desc_n_biodiversity_projects"),
														"unidad" => "18" // Unidad
												   ),

			);

		}

		if($id_fase == "3"){

			$datos = array(
				"installed_capacity"				=> array(
															"valor" => "",
															"codigo" => "1",
															"descripcion" => lang("desc_installed_capacity"),
															"unidad" => "19" // MW
													   ),
				"n_gen_wind_turbine"				=> array(
															"valor" => "",
															"codigo" => "2",
															"descripcion" => lang("desc_n_gen_wind_turbine"),
															"unidad" => "18" // Unidad
													   ),

				"occupied_surface"				=> array(
															"valor" => "",
															"codigo" => "3",
															"descripcion" => lang("desc_occupied_surface"),
															"unidad" => "14" // ha
													   ),
				"operating_hours"				=> array(
															"valor" => "",
															"codigo" => "4",
															"descripcion" => lang("desc_operating_hours"),
															"unidad" => "17" // hrs
													   ),
				"network_electricity_consumption"					=> array(
															"valor" => "",
															"codigo" => "5",
															"descripcion" => lang("desc_network_electricity_consumption"),
															"unidad" => "21" // MWh
													   ),
				"electricity_autoconsumption" 					=> array(
															"valor" => "",
															"codigo" => "6",
															"descripcion" => lang("desc_electricity_autoconsumption"),
															"unidad" => "21" // MWh
													   ),
				"electricity_consumption_from_diesel" 				=> array(
															"valor" => "",
															"codigo" => "7",
															"descripcion" => lang("desc_electricity_consumption_from_diesel"),
															"unidad" => "21" // MWh
													   ),
				"petroleum_diesel" 					=> array(
															"valor" => "",
															"codigo" => "8",
															"descripcion" => lang("desc_petroleum_diesel"),
															"unidad" => "1" // t
													   ),
				"gasoline" 							=> array(
															"valor" => "",
															"codigo" => "9",
															"descripcion" => lang("desc_gasoline"),
															"unidad" => "1" // t
													   ),
				"glp" 								=> array(
															"valor" => "",
															"codigo" => "10",
															"descripcion" => lang("desc_glp"),
															"unidad" => "1" // t
													   ),
				"natural_gas" 						=> array(
															"valor" => "",
															"codigo" => "11",
															"descripcion" => lang("desc_natural_gas"),
															//"unidad" => "3" // m3
															"unidad" => "25" // m3 x 10^3
													   ),
				"biodiesel_alcohol" 				=> array(
															"valor" => "",
															"codigo" => "12",
															"descripcion" => lang("desc_biodiesel_alcohol"),
															"unidad" => "1" // t
													   ),
				"sf6_present_on_plant" 			=> array(
															"valor" => "",
															"codigo" => "13",
															"descripcion" => lang("desc_sf6_present_on_plant"),
															"unidad" => "4" // l
													   ),
				"biodegradable_oil" 					=> array(
															"valor" => "",
															"codigo" => "14",
															"descripcion" => lang("desc_biodegradable_oil"),
															"unidad" => "1" // t
													   ),
				"no_biodegradable_oil" 					=> array(
															"valor" => "",
															"codigo" => "15",
															"descripcion" => lang("desc_no_biodegradable_oil"),
															"unidad" => "1" // t
													   ),
				"dielectric_oil" 				=> array(
															"valor" => "",
															"codigo" => "16",
															"descripcion" => lang("desc_dielectric_oil"),
															"unidad" => "1" // t
													   ),
				"oil_containing_pcb" 					=> array(
															"valor" => "",
															"codigo" => "17",
															"descripcion" => lang("desc_oil_containing_pcb"),
															"unidad" => "1" // t
													   ),
				"others_no_biodegradable_oils" 			=> array(
															"valor" => "",
															"codigo" => "18",
															"descripcion" => lang("desc_others_no_biodegradable_oils"),
															"unidad" => "1" // t
													   ),
				"decrease_oil_good_practices" 		=> array(
															"valor" => "",
															"codigo" => "19",
															"descripcion" => lang("desc_decrease_oil_good_practices"),
															"unidad" => "4" // l
													   ),
				"waste_production_np" 			=> array(
															"valor" => "",
															"codigo" => "20",
															"descripcion" => lang("desc_waste_production_np"),
															"unidad" => "1" // t
													   ),
				"np_recycled_waste" 			=> array(
															"valor" => "",
															"codigo" => "21",
															"descripcion" => lang("desc_np_recycled_waste"),
															"unidad" => "1" // t
													   ),
				"dangerous_waste_production" 			=> array(
															"valor" => "",
															"codigo" => "22",
															"descripcion" => lang("desc_dangerous_waste_production"),
															"unidad" => "1" // t
													   ),
				"dangerous_waste_recycled" 	=> array(
															"valor" => "",
															"codigo" => "23",
															"descripcion" => lang("desc_dangerous_waste_recycled"),
															"unidad" => "1" // t
													   ),
				"drinking_water_consumption_river" 				=> array(
															"valor" => "",
															"codigo" => "25",
															"descripcion" => lang("desc_drinking_water_consumption_river"),
															"unidad" => "3" // m3
													   ),
				"drinking_water_consumption_well" 				=> array(
															"valor" => "",
															"codigo" => "26",
															"descripcion" => lang("desc_drinking_water_consumption_well"),
															"unidad" => "3" // m3
													   ),
				"drinking_water_consumption_plants" 			=> array(
															"valor" => "",
															"codigo" => "27",
															"descripcion" => lang("desc_drinking_water_consumption_plants"),
															"unidad" => "3" // m3
													   ),
				"drinking_water_consumption_wastewater_treatment_plant" 			=> array(
															"valor" => "",
															"codigo" => "28",
															"descripcion" => lang("desc_drinking_water_consumption_wastewater_treatment_plant"),
															"unidad" => "3" // m3
													   ),
				"drinking_water_consumption_system_harvest" 			=> array(
															"valor" => "",
															"codigo" => "29",
															"descripcion" => lang("desc_drinking_water_consumption_system_harvest"),
															"unidad" => "3" // m3
													   ),
				"non_potable_water_consumption_river" 				=> array(
															"valor" => "",
															"codigo" => "25",
															"descripcion" => lang("desc_non_potable_water_consumption_river"),
															"unidad" => "3" // m3
													   ),
				"non_potable_water_consumption_well" 			=> array(
															"valor" => "",
															"codigo" => "26",
															"descripcion" => lang("desc_non_potable_water_consumption_well"),
															"unidad" => "3" // m3
													   ),
				"non_potable_water_consumption_plants_water" 	=> array(
															"valor" => "",
															"codigo" => "27",
															"descripcion" => lang("desc_non_potable_water_consumption_plants_water"),
															"unidad" => "3" // m3
													   ),
				"non_potable_water_consumption_plants_res_water" => array(
															"valor" => "",
															"codigo" => "28",
															"descripcion" => lang("desc_non_potable_water_consumption_plants_res_water"),
															"unidad" => "3" // m3
													   ),
				"non_potable_water_consumption_system_harvest" 			=> array(
															"valor" => "",
															"codigo" => "29",
															"descripcion" => lang("desc_non_potable_water_consumption_system_harvest"),
															"unidad" => "3" // m3
													   ),
				"n_biodiversity_projects" 		=> array(
															"valor" => "",
															"codigo" => "30",
															"descripcion" => lang("desc_n_biodiversity_projects"),
															"unidad" => "18" // Unidad
													   ),
				"n_dead_birds" 					=> array(
															"valor" => "",
															"codigo" => "31",
															"descripcion" => lang("desc_n_dead_birds"),
															"unidad" => "18" // Unidad
													   ),
				"n_species_uicn" 					=> array(
															"valor" => "",
															"codigo" => "32",
															"descripcion" => lang("desc_n_species_uicn"),
															"unidad" => "18" // Unidad
													   ),
				"accidental_spills_ground_water" 		=> array(
															"valor" => "",
															"codigo" => "33",
															"descripcion" => lang("desc_accidental_spills_ground_water"),
															"unidad" => "4" // l
													   ),
				"n_environmental_events" 			=> array(
															"valor" => "",
															"codigo" => "34",
															"descripcion" => lang("desc_n_environmental_events"),
															"unidad" => "18" // Unidad
													   ),
				"total_stop_days_site" 			=> array(
															"valor" => "",
															"codigo" => "35",
															"descripcion" => lang("desc_total_stop_days_site"),
															"unidad" => "18" // Unidad
													   ),
				"n_local_hired_employees" 				=> array(
															"valor" => "",
															"codigo" => "36",
															"descripcion" => lang("desc_n_local_hired_employees"),
															"unidad" => "18" // Unidad
													   ),
				"n_plant_hired_employees" 			=> array(
															"valor" => "",
															"codigo" => "37",
															"descripcion" => lang("desc_n_plant_hired_employees"),
															"unidad" => "18" // Unidad
													   ),
				"n_turnover_employees" 				=> array(
															"valor" => "",
															"codigo" => "38",
															"descripcion" => lang("desc_n_turnover_employees"),
															"unidad" => "18" // Unidad
													   ),
				"total_trained_local_people" 				=> array(
															"valor" => "",
															"codigo" => "39",
															"descripcion" => lang("desc_total_trained_local_people"),
															"unidad" => "18" // Unidad
													   ),
				"total_hired_trained_local_people" 		=> array(
															"valor" => "",
															"codigo" => "40",
															"descripcion" => lang("desc_total_hired_trained_local_people"),
															"unidad" => "18" // Unidad
													   ),
				"n_training_hours" 					=> array(
															"valor" => "",
															"codigo" => "41",
															"descripcion" => lang("desc_n_training_hours"),
															"unidad" => "18" // Unidad
													   ),
				"n_stakeholders_complaints" 					=> array(
															"valor" => "",
															"codigo" => "42",
															"descripcion" => lang("desc_n_stakeholders_complaints"),
															"unidad" => "18" // Unidad
													   ),
				"noise_levels_near_population" 			=> array(
															"valor" => "",
															"codigo" => "43",
															"descripcion" => lang("desc_noise_levels_near_population"),
															"unidad" => "18" // Unidad
													   ),
				"sustainable_actions_plant" 				=> array(
															"valor" => "",
															"codigo" => "44",
															"descripcion" => lang("desc_sustainable_actions_plant"),
															"unidad" => "18" // Unidad
													   ),
				"n_donated_solutions" 					=> array(
															"valor" => "",
															"codigo" => "45",
															"descripcion" => lang("desc_n_donated_solutions"),
															"unidad" => "18" // Unidad
													   ),
				"n_beneficiaries_donated_solutions" 			=> array(
															"valor" => "",
															"codigo" => "46",
															"descripcion" => lang("desc_n_beneficiaries_donated_solutions"),
															"unidad" => "18" // Unidad
													   ),
				"n_people_from_local_communities" 				=> array(
															"valor" => "",
															"codigo" => "47",
															"descripcion" => lang("desc_n_people_from_local_communities"),
															"unidad" => "18" // Unidad
													   ),
				"expenses_local_suppliers" 				=> array(
															"valor" => "",
															"codigo" => "48",
															"descripcion" => lang("desc_expenses_local_suppliers"),
															"unidad" => "18" // Unidad
													   ),
				"opex_total" 						=> array(
															"valor" => "",
															"codigo" => "49",
															"descripcion" => lang("desc_opex_total"),
															"unidad" => "18" // Unidad
													   ),
				"environmental_expenses" 				=> array(
															"valor" => "",
															"codigo" => "50",
															"descripcion" => lang("desc_environmental_expenses"),
															"unidad" => "18" // Unidad
													   ),
				"enel_hours_worked" 			=> array(
															"valor" => "",
															"codigo" => "51",
															"descripcion" => lang("desc_enel_hours_worked"),
															//"unidad" => "18" // Unidad
															"unidad" => "17" // Horas
													   ),
				"enel_accidents" 					=> array(
															"valor" => "",
															"codigo" => "52",
															"descripcion" => lang("desc_enel_accidents"),
															"unidad" => "18" // Unidad
													   ),
				"enel_first_aid" 				=> array(
															"valor" => "",
															"codigo" => "53",
															"descripcion" => lang("desc_enel_first_aid"),
															"unidad" => "18" // Unidad
													   ),
				"enel_near_miss" 					=> array(
															"valor" => "",
															"codigo" => "54",
															"descripcion" => lang("desc_enel_near_miss"),
															"unidad" => "18" // Unidad
													   ),
				"enel_lost_days" 				=> array(
															"valor" => "",
															"codigo" => "55",
															"descripcion" => lang("desc_enel_lost_days"),
															//"unidad" => "18" // Unidad
															"unidad" => "16" // Días
													   ),
				"contractor_hours_worked" 			=> array(
															"valor" => "",
															"codigo" => "51",
															"descripcion" => lang("desc_contractor_hours_worked"),
															//"unidad" => "18" // Unidad
															"unidad" => "17" // Horas
													   ),
				"contractor_accidents" 				=> array(
															"valor" => "",
															"codigo" => "52",
															"descripcion" => lang("desc_contractor_accidents"),
															"unidad" => "18" // Unidad
													   ),
				"contractor_first_aid" 				=> array(
															"valor" => "",
															"codigo" => "53",
															"descripcion" => lang("desc_contractor_first_aid"),
															"unidad" => "18" // Unidad
													   ),
				"contractor_near_miss" 				=> array(
															"valor" => "",
															"codigo" => "54",
															"descripcion" => lang("desc_contractor_near_miss"),
															"unidad" => "18" // Unidad
													   ),
				"contractor_lost_days" 			=> array(
															"valor" => "",
															"codigo" => "55",
															"descripcion" => lang("desc_contractor_lost_days"),
															//"unidad" => "18" // Unidad
															"unidad" => "16" // Días
													   ),


			);

		}

		$data_registro_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"is_valor_asignado" => 0,
			"datos" => json_encode($datos),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_registro_kpi = $this->KPI_Report_structure_model->save($data_registro_kpi);


	}

	private function guardar_estructura_graficos_kpi($opciones_grafico_kpi = array()){

		$id_cliente = get_array_value($opciones_grafico_kpi, "id_cliente");
		$id_fase = get_array_value($opciones_grafico_kpi, "id_fase");
		$id_proyecto = get_array_value($opciones_grafico_kpi, "id_proyecto");

		// Gráficos por proyecto

		// 1
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "materials_and_waste",
			"subitem" => "total_waste_produced",
			"tipo_grafico" => "chart_pie_basic",
			"series" => json_encode(array(
							"non_hazardous_industrial_waste" => "",
							"hazardous_industrial_waste" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 2
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "materials_and_waste",
			"subitem" => "waste_recycling_totals",
			"tipo_grafico" => "chart_pie_basic",
			"series" => json_encode(array(
							"waste_without_recycling" => "",
							"rises_recycled" => "",
							"respel_recycled" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 3
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "materials_and_waste",
			"subitem" => "waste_recycling_monthly",
			"tipo_grafico" => "chart_bars_stacked_100",
			"series" => json_encode(array(
							"waste_without_recycling" => "",
							"rises_recycled" => "",
							"respel_recycled" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 4
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "emissions",
			"subitem" => "total_emissions_by_source",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"direct_emissions" => "",
							"indirect_emissions_energy" => "",
							"other_indirect_emissions" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 5
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "energy",
			"subitem" => "energy_consumption_source_type",
			"tipo_grafico" => "chart_pie_basic",
			"series" => json_encode(array(
							"renewable" => "",
							"not_renewable" => "",
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 6
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "energy",
			"subitem" => "energy_consumption",
			"tipo_grafico" => "chart_bars_stacked_100",
			"series" => json_encode(array(
							"renewable" => "",
							"not_renewable" => "",
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 7
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "water",
			"subitem" => "water_consumption_by_origin",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"drinking_water" => "",
							"natural_source" => "",
							"reused_water" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 8
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "water",
			"subitem" => "water_consumption_by_origin",
			"tipo_grafico" => "chart_bars_stacked_percentage",
			"series" => json_encode(array(
							"drinking_water" => "",
							"natural_source" => "",
							"reused_water" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 9
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "water",
			"subitem" => "water_reused_by_type",
			"tipo_grafico" => "chart_bars_percentage",
			"series" => json_encode(array(
							"treated_water" => "",
							"rainwater_collector" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 10
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "water",
			"subitem" => "water_reused_by_type",
			"tipo_grafico" => "chart_columns_percentage",
			"series" => json_encode(array(
							"treated_water" => "",
							"rainwater_collector" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 11
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "social",
			"subitem" => "proportion_expenses_dedicated_local_suppliers",
			"tipo_grafico" => "chart_pie_basic",
			"series" => json_encode(array(
							"expenditure_local_suppliers" => "",
							"other_expenses" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 12
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "social",
			"subitem" => "expenditure_local_suppliers",
			"tipo_grafico" => "chart_bars_stacked_percentage",
			"series" => json_encode(array(
							"expenditure_local_suppliers" => "",
							"other_expenses" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 13
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "social",
			"subitem" => "solutions_actions_facilities",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"solutions_donated_to_community" => "",
							"sustainable_actions_on_site" => "",
							"facilities_for_workers" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 14
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"item" => "social",
			"subitem" => "donated_solutions_beneficiaries",
			"tipo_grafico" => "chart_bars_and_line",
			"series" => json_encode(array(
							"solutions_donated_to_community" => "",
							"beneficiaries" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);


		// Gráficos entre proyectos

		// 1
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "materials_and_waste",
			"subitem" => "total_waste_produced",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"total_waste_produced" => "",
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 2
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "materials_and_waste",
			"subitem" => "waste_recycling",
			"tipo_grafico" => "chart_bars_stacked_100",
			"series" => json_encode(array(
							"waste_without_recycling" => "",
							"rises_recycled" => "",
							"respel_recycled" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 3
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "emissions",
			"subitem" => "total_emissions_produced",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"total_produced_emissions" => "",
			)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 4
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "emissions",
			"subitem" => "emissions_by_source",
			"tipo_grafico" => "chart_bars_stacked_100",
			"series" => json_encode(array(
							"direct_emissions" => "",
							"other_indirect_emissions" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 5
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "energy",
			"subitem" => "total_energy_consumption",
			"tipo_grafico" => "chart_pie_basic",
			"series" => json_encode(array(
							"total_energy_consumption" => "",
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 6
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "energy",
			"subitem" => "energy_consumption",
			"tipo_grafico" => "chart_bars_stacked_100",
			"series" => json_encode(array(
							"not_renewable" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 7
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "water",
			"subitem" => "total_water_consumption",
			"tipo_grafico" => "chart_bars",
			"series" => json_encode(array(
							"total_water_consumption" => "",
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 8
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "water",
			"subitem" => "water_consumption_by_origin",
			"tipo_grafico" => "chart_bars_stacked_percentage",
			"series" => json_encode(array(
							"natural_source" => "",
							"reused_water" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

		// 9
		$data_grafico_kpi = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $id_fase,
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_between_projects",
			"item" => "water",
			"subitem" => "water_reused_by_type",
			"tipo_grafico" => "chart_columns_percentage",
			"series" => json_encode(array(
							"treated_water" => "",
							"rainwater_collector" => ""
						)),
			"created_by" => $this->login_user->id,
			"created" => get_current_utc_time()
		);
		$save_data_grafico_kpi = $this->KPI_Charts_structure_model->save($data_grafico_kpi);

	}

	function move_project_content_file_to_temp(){

		if (!empty($_FILES)) {

			$array_files = array();

			foreach($_FILES as $index => $_file){

				$file = $_file['tmp_name'];
				$file_name = uniqid("file")."-".$_file['name'];

				if (!is_valid_file_to_upload($file_name)){
					return false;
				}

				$target_path = getcwd() . "/" . get_setting("temp_file_path") . "project_content_files/";

				if (!is_dir($target_path)) {
					if (!mkdir($target_path, 0777, true)) {
						die('Failed to create file folders.');
					}
				}

				$target_file = $target_path . $file_name;
				copy($file, $target_file);

				$array_files[] = array("file_name" => $file_name);

			}

			echo json_encode($array_files);

		}

	}

	function delete_project_content_file_from_temp($file_name){
		$file_path = $this->input->post("file_path");
		delete_file_from_directory($file_path);
	}

	function save_project_content_file($file_path_temp, $file_path_project, $file_name){

		$target_path = getcwd() . '/' . $file_path_project;
		if (!is_dir($target_path)) {
			if (!mkdir($target_path, 0777, true)) {
				die('Failed to create file folders.');
			}
		}

		$target_file_temp = getcwd() . "/" . $file_path_temp . $file_name;
		$target_file = $target_path . $file_name;
		$save_file = rename($target_file_temp, $target_file);

		return $save_file;

	}

}


/* End of file projects.php */
/* Location: ./application/controllers/projects.php */