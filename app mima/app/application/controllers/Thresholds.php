<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Thresholds extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->init_permission_checker("client");
    }

    function index() {
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['client_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), "id");
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['project_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO MÓDULO
		$array_modulos[] = array("id" => "", "text" => "- ".lang("module")." -");
		$modulos = $this->Clients_modules_model->get_dropdown_list(array("name"), "id");
		foreach($modulos as $id => $name){
			$array_modulos[] = array("id" => $id, "text" => $name);
		}
		$view_data['modules_dropdown'] = json_encode($array_modulos);
		
		// FILTRO FORMULARIO
		$array_formularios[] = array("id" => "", "text" => "- ".lang("form")." -");
		$formularios = $this->Forms_model->get_dropdown_list(array("nombre"), "id");
		foreach($formularios as $id => $nombre){
			$array_formularios[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['forms_dropdown'] = json_encode($array_formularios);
		
		// FILTRO MATERIAL
		$array_materiales[] = array("id" => "", "text" => "- ".lang("material")." -");
		$materiales = $this->Materials_model->get_dropdown_list(array("nombre"), "id");
		foreach($materiales as $id => $nombre){
			$array_materiales[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['materials_dropdown'] = json_encode($array_materiales);
		
        $this->template->rander("thresholds/index", $view_data);
		
    }
	
	
	function modal_form(){

		$id = $this->input->post('id');
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
		if($id){
			
			$data = $this->Thresholds_model->get_one($id);

			$view_data['id_thresholds'] = $id;
			$view_data['client'] = $data->id_client;
			$view_data['id_project'] = $data->id_project;
			$client_project = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $data->id_client));
			$view_data["available_project"]= $client_project;
			
			$view_data['id_module'] = $data->id_module;
			$modules_array = array("" => "-");
			$modules = $this->Module_availability_model->get_project_setting( $data->id_client, $data->id_project)->result();
			foreach($modules as $module){
				if(($module->thresholds == 1)){
					$modules_array[$module->id]=$module->name;
				}else{
					continue;
				}	
			}
			$view_data["available_module"]= $modules_array;
			$view_data["id_form"] = $data->id_form;

			// Mostrar en el dropdown de formulario solo los que pertenecen al cliente/proyecto del umbral
			
			/*$forms_rel_project = $this->Form_rel_project_model->get_all_where(array(
				"id_proyecto" => $data->id_project,
				"deleted" => 0 
			))->result_array();
			
			$array_forms = array("" => "-");
			foreach($forms_rel_project as $rel){
				$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
				if($formulario->id_tipo_formulario == 1 && $formulario->flujo == "Residuo"){
					$array_forms[$formulario->id] = $formulario->nombre;
				}
			}
			
			$view_data['available_forms'] = $array_forms;*/
			
			// FORMULARIOS
			$array_forms  = array("" => "-");
			
			if($data->id_module == 8){ // Residuos
				$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
					"id_proyecto" => $data->id_project,
					"deleted" => 0
				))->result_array();
				
				foreach($formularios_rel_proyecto as $rel){
					$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
					if($formulario->id_tipo_formulario == 1 && $formulario->flujo == "Residuo"){
						//$array_formularios[] = array("id" => $formulario->id, "text" => $formulario->nombre);
						$array_forms[$formulario->id] = $formulario->nombre;
					}
				}
			}
			
			if($data->id_module == 5){ // Reportes
				$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
					"id_proyecto" => $data->id_project,
					"deleted" => 0
				))->result_array();
				
				foreach($formularios_rel_proyecto as $rel){
					$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
					if($formulario->id_tipo_formulario == 1 && ($formulario->flujo == "Consumo" || $formulario->flujo == "Residuo")){
						//$array_formularios[] = array("id" => $formulario->id, "text" => $formulario->nombre);
						$array_forms[$formulario->id] = $formulario->nombre;
					}
				}
			}
			
			$view_data['available_forms'] = $array_forms;
			
			/*
			$forms_data = $this->Forms_model->get_all_where(array("flujo"=>"Residuo" , "deleted"=>0))->result();
			$array_forms  = array("" => "-");
			foreach($forms_data as $form_data){
				$array_forms[$form_data->id] = $form_data->nombre;
			}
			$view_data['available_forms'] = $array_forms;
			*/
	
			
	
			$view_data['label'] = $data->label;
			$view_data['id_material'] = $data->id_material;
			
			/*
			$materiales = $this->Thresholds_model->get_material_flow_project($data->id_project,"Residuo")->result();
			$array_materiales = array("" => "-");
			foreach($materiales as $material){
				$array_materiales[$material->id_material]=$material->nombre_material;
			}
			$view_data["available_material"]= $array_materiales;
			*/
			
			// MATERIALES
			
			$array_materials = array("" => "-");
			$form_material_data = $this->Form_rel_material_model->get_materials_related_to_form($data->id_form)->result();
			foreach($form_material_data as $material){
				$material_data = $this->Materials_model->get_one($material->id_material);
				$array_materials[$material_data->id] = $material_data->nombre;
			}
			$view_data["available_material"]= $array_materials;
			
			/*
			$category_data = $this->Materials_rel_category_model->get_categories_related_to_material($view_data['id_material'])->result();
			$array_category = array("" => "-");
			foreach($category_data as $cat_data){
				$data_category = $this->Categories_model->get_one($cat_data->id_categoria);
				$array_category[$cat_data->id_categoria] = $data_category->nombre;
			}
			$view_data["available_category"]= $array_category;
			*/
			
			// CATEGORIAS
			$view_data['id_category'] = $data->id_category;
			$array_category = array("" => "-");
			$category_data = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form_and_material($data->id_form, $data->id_material)->result();
			foreach($category_data as $value){
				$categorie = $this->Categories_model->get_one($value->id_categoria);
				$array_category[$categorie->id] = $categorie->nombre;
			}
			$view_data["available_category"]= $array_category;
			
			$view_data['id_unit_type'] = $data->id_unit_type;
			$view_data['unit_type'] = $this->Unity_type_model->get_one($data->id_unit_type)->nombre;
			//$view_data['unit_value'] = $data->unit_value;
			$view_data['id_unit'] = $data->id_unit;
			$view_data['unit_name'] = $this->Unity_model->get_one($data->id_unit)->nombre;
			$view_data['risk_value'] = $data->risk_value;
			$view_data['threshold_value'] = $data->threshold_value;
		
		} else {
			
			$view_data["available_project"] = array("" => "-");
			$view_data["available_module"] = array("" => "-");
			$view_data["available_forms"] = array("" => "-");
			$view_data["available_material"] = array("" => "-");
			$view_data["available_category"] = array("" => "-");
			
		}

		$view_data['label_column'] = "col-md-3";
		$view_data['field_column'] = "col-md-9";
		
		$this->load->view('thresholds/modal_form', $view_data);
		
	}
	
	
    function view($thresholds_id = 0) {

        if ($thresholds_id) {
            $thresholds_info = $this->Thresholds_model->get_one($thresholds_id);
            if ($thresholds_info){
				
				$client_data = $this->Clients_model->get_one($thresholds_info->id_client);
				$project_data = $this->Projects_model->get_one($thresholds_info->id_project);
				$array_options_module = array("id"=>$thresholds_info->id_module);
				$module_data = $this->Clients_modules_model->get_details($array_options_module)->result();
				$material = $this->Materials_model->get_one($thresholds_info->id_material);
				$category = $this->Categories_model->get_one($thresholds_info->id_category);
				$form = $this->Forms_model->get_one($thresholds_info->id_form)->nombre;
				$unit_type = $this->Unity_type_model->get_one($thresholds_info->id_unit_type)->nombre;
				$unit = $this->Unity_model->get_one($thresholds_info->id_unit)->nombre;
					
                $view_data['client'] = $client_data->company_name;
                $view_data['project'] = $project_data->title;
                $view_data['module_name'] = $module_data[0]->name;
				$view_data['form'] = $form;
                $view_data['label'] = $thresholds_info->label;
                $view_data['material_name'] = $material->nombre;
                $view_data['category_name'] = $category->nombre;
				$view_data['unit_type'] = $unit_type;
				//$view_data['unit_value'] = $thresholds_info->unit_value;
				$view_data['unit'] = $unit;
                $view_data['risk_value'] = $thresholds_info->risk_value;
                $view_data['threshold_value'] = $thresholds_info->threshold_value;
				$view_data['thresholds_info'] = $thresholds_info;

				$this->load->view('thresholds/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function list_data(){
		
		$options = array(
			"id_client" => $this->input->post("id_client"),
			"id_project" => $this->input->post("id_project"),
			"id_module" => $this->input->post("id_module"),
			"id_form" => $this->input->post("id_form"),
			"id_material" => $this->input->post("id_material"),
		);
		
        $list_data = $this->Thresholds_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
	}
	
	
    private function _row_data($id) {
        $options = array(
            "id" => $id
        );
		
        $data = $this->Thresholds_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	
	function _make_row($data){
		
		$client_data = $this->Clients_model->get_one($data->id_client);
		$project_data = $this->Projects_model->get_one($data->id_project);
		$array_options_module = array("id"=>$data->id_module);
		$module_data = $this->Clients_modules_model->get_details($array_options_module)->result();
		$id_material = $this->Materials_model->get_one($data->id_material);
		$id_category = $this->Categories_model->get_one($data->id_category);
		$form = $this->Forms_model->get_one($data->id_form)->nombre;
		$unit_type = $this->Unity_type_model->get_one($data->id_unit_type)->nombre;
		$unit = $this->Unity_model->get_one($data->id_unit)->nombre;
		
		$row_data = array(
			$data->id,
			$client_data->company_name,
			$project_data->title,
			$module_data[0]->name,
			$form,
			$data->label,
			$id_material->nombre,
			$id_category->nombre,
			$unit_type,
			$unit,
			//$data->unit_value,
			$data->risk_value,
			$data->threshold_value,
		);

		$row_data[] = modal_anchor(get_uri("thresholds/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "", "title" => lang('view_threshold'), "data-post-id" => $data->id)).
				modal_anchor(get_uri("thresholds/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_threshold'), "data-post-id" => $data->id))
				. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_threshold'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("thresholds/delete"), "data-action" => "delete-confirmation"));

		return $row_data;
	}
	
    function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->Thresholds_model->delete($id, true)) {
                echo json_encode(array("success" =>true, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Thresholds_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

	
	function save(){
		
		$id_thresholds = $this->input->post('id_thresholds');

		$id_client = $this->input->post('clients');
		$id_project = $this->input->post('project');
		$id_module = $this->input->post('module');
		$id_form = $this->input->post('forms');
		$label = $this->input->post('label');
		$id_material = $this->input->post('material');
		$id_category = $this->input->post('category');
		$id_unit_type = $this->input->post('id_unit_type');
		//$unit_value = $this->input->post('unit_value');
		$id_unit = $this->input->post('id_unit');
		$risk_value = $this->input->post('risk_value');
		$threshold_value = $this->input->post('threshold_value');
		
		
		if(!$id_thresholds){ //INSERT
			$thresholds_same_name = $this->Thresholds_model->get_all_where(array("id_client" => $id_client, "id_project" => $id_project, "id_module" => $id_module, "label" =>$label, "deleted" => 0))->result();
			if($thresholds_same_name){
				echo json_encode(array("success" => false, 'message' => lang('threshold_label_save_validator')));
				exit();
			}
		}else{ //EDIT
			$thresholds_same_name = $this->Thresholds_model->get_all_where(array("id_client" => $id_client, "id_project" => $id_project, "id_module" => $id_module, "label" =>$label, "deleted" => 0));
			if($thresholds_same_name->num_rows() && $thresholds_same_name->row()->id != $id_thresholds){
				echo json_encode(array("success" => false, 'message' => lang('threshold_label_save_validator')));
				exit();
			}
		}
		
		/*
		$thresholds_data = $this->Thresholds_model->get_all_where(array("deleted" => 0))->result();
		if(!$id_thresholds){
			foreach($thresholds_data as $data){
				if( ($data->id_client == $id_client) && ($data->id_project == $id_project ) && ($data->id_module == $id_module) && ($data->id_form == $id_form) && ($data->id_material == $id_material) && ($data->id_category == $id_category) ){
					echo json_encode(array("success" => false, 'message' => lang('threshold_save_validator')));
					exit();
				}
			}
		}
		*/
		
		//NO SE DEBEN CREAR DOS UMBRALES PARA LA MISMA CATEGORÍA DE UN CLIENTE/PROYECTO
		$thresholds_data = $this->Thresholds_model->get_all_where(array("id_client" => $id_client, "id_project" => $id_project, "deleted" => 0))->result();
		if(!$id_thresholds){
			foreach($thresholds_data as $data){
				if($data->id_category == $id_category){
					echo json_encode(array("success" => false, 'message' => lang('threshold_save_validator')));
					exit();
				}
			}
		}
		
		$save_options = array(
			"id_client" => $id_client,
			"id_project"=> $id_project ,
			"id_module"=> $id_module,
			"id_form"=> $id_form,
			"label"=> $label,
			"id_material"=> $id_material,
			"id_category"=> $id_category,
			"id_unit_type"=> $id_unit_type,
			//"unit_value"=> $unit_value,
			"id_unit"=> $id_unit,
			"risk_value"=> $risk_value,
			"threshold_value"=> $threshold_value,
		);

		if($id_thresholds){
			$save_options["modified_by"] = $this->login_user->id;
			$save_options["modified"] = get_current_utc_time();
			$save = $this->Thresholds_model->save($save_options,$id_thresholds);
		}else{
			$save_options["created_by"] = $this->login_user->id;
			$save_options["created"] = get_current_utc_time();
			$save = $this->Thresholds_model->save($save_options);
		}
		
		if ($save) {
			echo json_encode(array("success" => true,"data" => $this->_row_data($save), 'id' => $save, 'message' => lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
	function get_projects_by_client(){
		
		$id_cliente = $this->input->post('id_client');
		
        if(!$this->login_user->id) {
			redirect("forbidden");
		}
		
		$client_project = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		$result = json_encode($client_project);
		echo $result;
		
	}
	
	function get_modules(){
		
		$id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		
		$modules_array = array();
		$modules_array[] = array("id" => "", "text" => "-");
		
		if($id_project){
			$modules = $this->Module_availability_model->get_project_setting($id_client, $id_project)->result();
			foreach($modules as $module){
				if($module->thresholds == 1){
					$modules_array[] = array("id" => $module->id, "text" => $module->name);
				}
			}
		}
		
		echo json_encode($modules_array);

	}
	
	function get_material(){
		
		$id_project = $this->input->post('id_project');
		$id_module = $this->input->post('id_module');

		$module = $this->Clients_modules_model->get_module_name($id_module)->result();
		$module_name = $module[0]->name;
		$array_materiales = array();
		if($module_name == "Residuos"){

			$materiales = $this->Thresholds_model->get_material_flow_project($id_project,"Residuo")->result();
			
			foreach($materiales as $material){
				$array_materiales[$material->id_material]=$material->nombre_material;
			}

			$result = json_encode($array_materiales);
			echo $result;
		}
	}
	
	function get_materials_by_form(){
		
		$id_form = $this->input->post('id_form');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_materials = array();
		$array_materials[] = array("id" => "", "text" => "-");
		if($id_form){
			
			$form_material_data = $this->Form_rel_material_model->get_materials_related_to_form($id_form)->result();
			foreach($form_material_data as $material){
				$material_data = $this->Materials_model->get_one($material->id_material);
				$array_materials[] = array("id" => $material_data->id, "text" => $material_data->nombre);
			}
		}
		
		echo json_encode($array_materials);
		
	}

	function get_categorias(){
		
		$id_project = $this->input->post('id_project');
		$id_form = $this->input->post('forms');
		$id_material = $this->input->post('id_material');
		
		if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_category = array();
		$array_category[] = array("id" => "", "text" => "-");
		if($id_material && $id_form){
			$category_data = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form_and_material($id_form, $id_material)->result();
			foreach($category_data as $value){
				$categorie = $this->Categories_model->get_one($value->id_categoria);
				$array_category[] = array("id" => $categorie->id, "text" => $categorie->nombre);
			}
		}
		
		echo json_encode($array_category);
		
	}
	
	function get_forms(){
		
		$id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$id_module = $this->input->post('id_module');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_formularios = array();
		$array_formularios[] = array("id" => "", "text" => "-");
		
		if($id_module == 8){ // Residuos
			$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
				"id_proyecto" => $id_project,
				"deleted" => 0
			))->result_array();
			
			foreach($formularios_rel_proyecto as $rel){
				$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
				if($formulario->id_tipo_formulario == 1 && $formulario->flujo == "Residuo"){
					$array_formularios[] = array("id" => $formulario->id, "text" => $formulario->nombre);
				}
			}
		}
		
		if($id_module == 5){ // Reportes
			$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
				"id_proyecto" => $id_project,
				"deleted" => 0
			))->result_array();
			
			foreach($formularios_rel_proyecto as $rel){
				$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
				if($formulario->id_tipo_formulario == 1 && ($formulario->flujo == "Consumo" || $formulario->flujo == "Residuo")){
					$array_formularios[] = array("id" => $formulario->id, "text" => $formulario->nombre);
				}
			}
		}
		
		echo json_encode($array_formularios);
		
	}
	
	function get_unit_type(){
		
		$id_form = $this->input->post('id_form');

		$form = $this->Forms_model->get_one($id_form);
		$unit_data = json_decode($form->unidad);
		$type_unit_id = $unit_data->tipo_unidad_id;
		
		$unit_name_type = $this->Unity_type_model->get_one($type_unit_id)->nombre;
		$data = array("unit_type_name"=>$unit_name_type, "unit_type_id"=>$type_unit_id);
		$result = json_encode($data);
		echo $result;
	}
	
	
	function get_system_unit(){
		
		$id_client = $this->input->post('id_client');
		$id_project = $this->input->post('id_project');
		$unit_type = $this->input->post('unit_type');
		
		if($unit_type == "Masa"){
			$unit_id_on_config = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_client, "id_proyecto" => $id_project, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
			$unit_id = $this->Unity_model->get_one($unit_id_on_config)->id;
			$unit_name = $this->Unity_model->get_one($unit_id)->nombre;
		}elseif($unit_type == "Volumen"){
			$unit_id_on_config = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_client, "id_proyecto" => $id_project, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
			$unit_id = $this->Unity_model->get_one($unit_id_on_config)->id;
			$unit_name = $this->Unity_model->get_one($unit_id)->nombre;
		}
		$data = array("unit_name"=>$unit_name,"unit_id"=>$unit_id);
		$result = json_encode($data);
		echo $result;

	}
	
	
}