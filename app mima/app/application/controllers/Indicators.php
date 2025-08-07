<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Indicators extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->init_permission_checker("client");
    }

    function index() {
		//$this->access_only_allowed_members();
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO CATEGORIAS
		$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
		
		$categorias = $this->Categories_model->get_categories_for_indicators_filter()->result_array();
		foreach($categorias as $categoria){
			$array_categorias[] = array("id" => $categoria["id"], "text" => $categoria["nombre"]);
		}
		$view_data['categorias_dropdown'] = json_encode($array_categorias);

        $this->template->rander("waste/admin/indicators/index", $view_data);
		
    }

    function modal_form() {
        //$this->access_only_allowed_members();

        $id = $this->input->post('id');
		
		if(isset($id)){
			
			$options = array("id" => $id);
			$data = $this->Indicators_model->get_details($options)->row();
			$view_data["id_indicator"] = $id;
			$view_data["cliente"] = $data->id_client;
			$view_data["id_project"] = $data->id_project;
			$client_project = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $data->id_client));
			$view_data["available_project"] = $client_project;
			$view_data["indicator_name"] = $data->indicator_name;
			$view_data["unit"] = $data->unit;
			$view_data["color"] = $data->color;
			$view_data["icon_selected"] = $data->id_fontawesome;
			$categories = json_decode($data->categories);
			$array_categories = array();
			foreach($categories as $key => $value){
				$array_categories[$key] = $value;
			}
			$view_data["categories_selected"] = $categories;
			
			
			// Buscar las categorías pertenecientes a los formularios de tipo residuo del proyecto del indicador
			$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
				"id_proyecto" => $data->id_project,
				"deleted" => 0
			))->result_array();
			
			$array_formularios_residuo_proyecto = array();
			foreach($formularios_rel_proyecto as $rel){
				$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
				if($formulario->flujo == "Residuo"){
					$array_formularios_residuo_proyecto[] = $formulario;
				}
			}
	
			$array_categorias = array();
			foreach($array_formularios_residuo_proyecto as $formulario){
	
				$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array(
					"id_formulario" => $formulario->id,
					"deleted" => 0
				))->result_array();
	
				foreach($form_rel_mat_rel_cat as $rel){
					$categoria = $this->Categories_model->get_one($rel["id_categoria"]);
					$array_categorias[$categoria->id] = $categoria->nombre;
				}	
				
			}

			$view_data["categories_available"] = $array_categorias;	
		}
			
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["proyectos"] = array("" => "-");
		
		$iconos_fontawesome = $this->Fontawesome_model->get_dropdown_list(array("clase"), "id");
		$view_data["icons"] = $iconos_fontawesome;
		
		$view_data['label_column'] = "col-md-3";
		$view_data['field_column'] = "col-md-9";
	
        $this->load->view('waste/admin/indicators/modal_form', $view_data);
    }
		
	function list_data(){
		
        //$this->access_only_allowed_members();
		
		$id_client = $this->input->post("id_client");
		$id_project = $this->input->post("id_project");
		$id_categoria = $this->input->post("id_categoria");
		
		$options = array(
			"id_client" => $id_client,
			"id_project" => $id_project
		);
		
        $list_data = $this->Indicators_model->get_details($options)->result();
		
		if($id_categoria){
			foreach($list_data as $index => $data){
				$array_categorias = json_decode($data->categories, true);
				if(!isset($array_categorias[$id_categoria])){
					unset($list_data[$index]);
				}
			}	
		} 

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
		
        $data = $this->Indicators_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	function _make_row($data){

		$client_data = $this->Clients_model->get_one($data->id_client);
		$project_data = $this->Projects_model->get_one($data->id_project); 
		$categories_data = json_decode($data->categories);
		
		$categories ='';
		foreach($categories_data as $key => $category){
			$categories.= $category."<br>";
		}
		
		$row_data = array(
			$data->id,
			$client_data->company_name,
			$project_data->title,
			$data->indicator_name,
			$data->unit,
			'<div style="border: 1px solid black; height:15px; width:15px; border-radius: 50%; background:'.$data->color.';"></div>',
			$categories
		);

		$row_data[] = modal_anchor(get_uri("indicators/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "", "title" => lang('view_indicator'), "data-post-id" => $data->id)).
				modal_anchor(get_uri("indicators/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_indicator'), "data-post-id" => $data->id))
				. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_indicator'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("indicators/delete"), "data-action" => "delete-confirmation"));

		return $row_data;
	}
	
    function view($indicator_id = 0) {
        //$this->access_only_allowed_members();

        if ($indicator_id) {

            $indicator_info = $this->Indicators_model->get_one($indicator_id);
			
            if ($indicator_info){
				
				$client_data = $this->Clients_model->get_one($indicator_info->id_client);
				$project_data = $this->Projects_model->get_one($indicator_info->id_project); 
				$categories_data = json_decode($indicator_info->categories);

				$categories ='';
				foreach($categories_data as $key => $category){
					$categories.= $category."<br>";
				}
				
				$view_data["model_info"] = $indicator_info;
                $view_data['client'] = $client_data->company_name;
                $view_data['project'] = $project_data->title;
                $view_data['indicator_name'] = $indicator_info->indicator_name;
                $view_data['unit'] = $indicator_info->unit;
                $view_data['color'] = $indicator_info->color;
				$icono_fontawesome = $this->Fontawesome_model->get_one($indicator_info->id_fontawesome);
                $view_data['icon'] = $icono_fontawesome->clase;
                $view_data['categories'] = $categories;
				
				$this->load->view('waste/admin/indicators/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function save(){
		$id_indicator = $this->input->post('id_indicator');
		
		$id_client = $this->input->post('clients');
		$project = $this->input->post('project');
		$indicator_name = $this->input->post('indicator_name');
		$unit = $this->input->post('unit');
		$color = $this->input->post('color');
		$id_fontawesome = $this->input->post('icon');
		$categories = $this->input->post('category');
		
		$array_categories = array();
		foreach($categories as $category){
			$data = $this->Categories_model->get_one($category);
			$array_categories[$data->id] = $data->nombre;
		}
		$categories_json = json_encode($array_categories);

		if(!$id_indicator){
			$indicator_same_name = $this->Indicators_model->get_all_where(array("id_client" => $id_client, "id_project" => $project, "indicator_name" =>$indicator_name, "deleted" => 0))->result();
			if($indicator_same_name){
				echo json_encode(array("success" => false, 'message' => lang('indicator_warning')));
				exit();
			}
		}else{
			$indicator_same_name = $this->Indicators_model->get_all_where(array("id_client" => $id_client, "id_project" => $project, "indicator_name" =>$indicator_name, "deleted" => 0));
			if($indicator_same_name->num_rows() && $indicator_same_name->row()->id != $id_indicator){
				echo json_encode(array("success" => false, 'message' => lang('indicator_warning')));
				exit();
			}			
		}

		$save_options = array(
			"id_client" => $id_client,
			"id_project"=> $project ,
			"indicator_name"=> $indicator_name,
			"unit"=> $unit,
			"color"=> $color,
			"id_fontawesome" => $id_fontawesome,
			"categories"=> $categories_json,
		);	
		
		if(!$id_indicator){
			$save_options["created"] = get_current_utc_time();
            $save_options["created_by"] = $this->login_user->id;
		}else{
			$save_options["modified"] = get_current_utc_time();
			$save_options["modified_by"] = $this->login_user->id;
		}
		
		
		$save = $this->Indicators_model->save($save_options,$id_indicator);

		if ($save) {
			echo json_encode(array("success" => true,"data" => $this->_row_data($save), 'id' => $save, 'message' => lang('record_saved')));
		} else {

			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));

		}
		
	}
	
    function delete() {
        //$this->access_only_allowed_members();
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->Indicators_model->delete($id, true)) {
                echo json_encode(array("success" =>true, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Indicators_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function get_projects_by_client(){
		
		$id_cliente = $this->input->post('id_client');
		
        if (!$this->login_user->id) { redirect("forbidden"); }
		
		$client_project = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		$result = json_encode($client_project);
		echo $result;
		
	}
	
	function get_categories($id){
		
		if(isset($id)){
			$id_project = $id;
		}else{
			$id_project = $this->input->post('id_project');
		}

		if (!$this->login_user->id){
			redirect("forbidden");
		}

		
		// Buscar las categorías pertenecientes a los formularios de tipo residuo del proyecto seleccionado
		$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
			"id_proyecto" => $id_project,
			"deleted" => 0
		))->result_array();
		
		$array_formularios_residuo_proyecto = array();
		foreach($formularios_rel_proyecto as $rel){
			$formulario = $this->Forms_model->get_one($rel["id_formulario"]);
			if($formulario->flujo == "Residuo"){
				$array_formularios_residuo_proyecto[] = $formulario;
			}
		}

		$array_categorias = array();
		foreach($array_formularios_residuo_proyecto as $formulario){

			$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array(
				"id_formulario" => $formulario->id,
				"deleted" => 0
			))->result_array();

			foreach($form_rel_mat_rel_cat as $rel){
				$categoria = $this->Categories_model->get_one($rel["id_categoria"]);
				$array_categorias[$categoria->id] = $categoria->nombre;
			}	
			
		}

        $html = '';
        $html .= '<div class="form-group">';
            $html .= '<label for="category" class="col-md-3">'.lang('category').'</label>';
            $html .= '<div class="col-md-9">';
            $html .= form_multiselect("category[]", $array_categorias, "", "id='category' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';
		
        if($id_project){
            echo $html;
        } else {
            echo "";
        }

	}
	
}