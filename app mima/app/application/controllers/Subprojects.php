<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subprojects extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
		
		//FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		//FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
        $this->template->rander("subprojects/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $subproject_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->Subprojects_model->get_one($subproject_id);
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		//$view_data["unidades_funcionales_disponibles"] = $this->Functional_units_model->get_all()->result_array();
		$view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_cliente));
		
		/* if($subproject_id){
			$view_data["unidades_funcionales_subproyecto"] = $this->Functional_units_model->get_functional_units_of_subproject($subproject_id);			
		} */
		
        $this->load->view('subprojects/modal_form', $view_data);
    }
	
	
	function save() {

        $subproject_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		// VALIDACION NOMBRE REPETIDO A NIVEL CLIENTE/PROYECTO
		$nombre = trim($this->input->post('name'));
		$id_cliente = $this->input->post('client');
		$id_proyecto = $this->input->post('project');
		
		if($this->Subprojects_model->is_subproject_name_exists($nombre, $id_cliente, $id_proyecto, $subproject_id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_subproject_name')));
			exit(); 
		}
		
		$data_subproject = array(
			"nombre" => $nombre,
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"descripcion" => $this->input->post('description'),
			"deleted" => 0
		);
		
		if($subproject_id){
			$data_subproject["modified_by"] = $this->login_user->id;
			$data_subproject["modified"] = get_current_utc_time();
			$save_id = $this->Subprojects_model->save($data_subproject, $subproject_id);
		} else {
			$data_subproject["created_by"] = $this->login_user->id;
			$data_subproject["created"] = get_current_utc_time();
			$save_id = $this->Subprojects_model->save($data_subproject);
		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	//modificar
	function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Subprojects_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Subprojects_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto")
		);
		
        $list_data = $this->Subprojects_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Subprojects_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {

		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$proyecto = $this->Projects_model->get_one_where(array("id"=>$data->id_proyecto, "deleted" =>0));
		/*
        $row_data = array($data->id, modal_anchor(get_uri("subprojects/view/" . $data->id), $data->nombre, array("title" => lang('view_subproject'), "data-post-id" => $data->id)), 
					$cliente->company_name, $proyecto->title, $data->tipo_infraestructura, $data->superficie);
		*/
		if($proyecto->title){
			$project = $proyecto->title;
		}else{
			$project = "-";
		}
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion = ($data->descripcion) ? $tooltip_descripcion : "-";
		
        $row_data = array(
						$data->id, modal_anchor(get_uri("subprojects/view/" . $data->id), $data->nombre, array("title" => lang('view_subproject'), "data-post-id" => $data->id)), 
						$cliente->company_name, 
						$project,
						$descripcion
		);
		
        $row_data[] =  modal_anchor(get_uri("subprojects/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_subproject'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("subprojects/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_subproject'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_subproject'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("subprojects/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	function view($subproject_id = 0) {
        $this->access_only_allowed_members();
		
		//var_dump($subproject_id);
		
        if ($subproject_id) {
            $options = array("id" => $subproject_id);
            $subproject_info = $this->Subprojects_model->get_details($options)->row();
            if ($subproject_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $subproject_info;
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$view_data["cliente"] = $cliente->company_name;
				$proyecto = $this->Projects_model->get_one($view_data['model_info']->id_proyecto);
				$view_data["proyecto"] = $proyecto->title;
				//$view_data["unidades_funcionales_subproyecto"] = $this->Functional_units_model->get_functional_units_of_subproject($subproject_id);
				$this->load->view('subprojects/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function get_infrastructure_type_fields(){
		
		$tipo_infraestructura = $this->input->post('tipo_infraestructura');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		if($tipo_infraestructura == "Generación"){

			$html = '';
			$html = '<div style="text-align: center;"><h5>'.lang("generation").'</h5></div>';
			$html .= '<br />';
			
		/*  $html .= '<div class="form-group">';
			$html .= '<label for="technologies" class="col-md-3">'.lang('technology').'</label>';
			$html .= '<div class="col-md-9">';
			
			$tecnologias = array(
				"Eólica" => "Eólica",
				"Sola fotovoltaica" => "Sola fotovoltaica",
				"Termosolar" => "Termosolar",
				"Mini hibrido pasada" => "Mini hibrido pasada",
				"Mini hibrido embalse" => "Mini hibrido embalse",
				"Geotérmica" => "Geotérmica"
			);
			
			$html .= form_dropdown("technologies", array("" => "-") + $tecnologias, "", "id='technologies' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>'; */
			
			$html .= '<div class="form-group">';
			$html .= '<label for="no_of_generation_equipment" class="col-md-3">'.lang('no_of_generation_equipment').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "no_of_generation_equipment",
						"name" => "no_of_generation_equipment",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('no_of_generation_equipment'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="unit_power_of_equipment" class="col-md-3">'.lang('unit_power_of_equipment_mw').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "unit_power_of_equipment",
						"name" => "unit_power_of_equipment",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('unit_power_of_equipment_mw'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "surface",
						"name" => "surface",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('surface'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($tipo_infraestructura == "Transmisión"){
		
			$html = '';
			$html = '<div style="text-align: center;"><h5>'.lang("transmission").'</h5></div>';
			$html .= '<br />';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="type_of_substation" class="col-md-3">'.lang('type_of_substation').'</label>';
			$html .= '<div class="col-md-9">';
			
			$types_of_substation = array(
				"Elevación" => "Elevación",
				"Interconexión" => "Interconexión"
			);
			
			$html .= form_dropdown("type_of_substation", array("" => "-") + $types_of_substation, "", "id='type_of_substation' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="transformation_capacity" class="col-md-3">'.lang('transformation_capacity_kv').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "transformation_capacity",
						"name" => "transformation_capacity",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('transformation_capacity_kv'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="number_of_high_voltage_towers" class="col-md-3">'.lang('number_of_high_voltage_towers').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "number_of_high_voltage_towers",
						"name" => "number_of_high_voltage_towers",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('number_of_high_voltage_towers'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
						
			$html .= '<div class="form-group">';
			$html .= '<label for="line_length" class="col-md-3">'.lang('line_length').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "line_length",
						"name" => "line_length",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('line_length'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="surface" class="col-md-3">'.lang('surface_km2').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= '';
			
			$html .= form_input(array(
						"id" => "surface",
						"name" => "surface",
						"type" => "text",
						//"value" => $model_info->nombre,
						"class" => "form-control",
						"placeholder" => lang('surface'),
						"autofocus" => true,
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
						"data-msg-regex" => lang("number_or_decimal_required"),
					));
        
			$html .= '</div>';
			$html .= '</div>';
		
		}
		
		echo $html;
		
	}
	
	function get_subprojects_of_projects(){
		
		$id_proyecto = $this->input->post('id_proyecto');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$subproyectos_de_proyecto = $this->Subprojects_model->get_dropdown_list(array("nombre"), "id", array("id_proyecto" => $id_proyecto));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="subproject" class="col-md-3">'.lang('subproject').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("subproject", array("" => "-") + $subproyectos_de_proyecto, "", "id='subproject' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
}

