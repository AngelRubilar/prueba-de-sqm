<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permitting_procedure_status extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		//$access_info = $this->get_access_info("invoice");
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
				
		// FILTRO CATEGORIA
		$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
		$array_categorias[] = array("id" => "Cumple", "text" => lang("fulfill"));
		$array_categorias[] = array("id" => "No Cumple", "text" => lang("does_not_fulfill"));
		$array_categorias[] = array("id" => "Pendiente", "text" => lang("pending"));
		$array_categorias[] = array("id" => "No Aplica", "text" => lang("does_not_apply"));
		$view_data['categorias_dropdown'] = json_encode($array_categorias);
		
        $this->template->rander("permitting_procedure_status/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $procedure_status_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');       
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
		if($procedure_status_id){ //edit
			$view_data['model_info'] = $this->Permitting_procedure_status_model->get_one($procedure_status_id);		
		} 
		
        $this->load->view('permitting_procedure_status/modal_form', $view_data);
    }
	
	
	function save() {

        $procedure_status_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		$data_procedure_status = array(
			"id_cliente" => $this->input->post('client'),
			"nombre_estado" => $this->input->post('name'),
			"categoria" => $this->input->post('categoria'),
			"color" => $this->input->post('color')
			
		);
		
		
		if(!$procedure_status_id){
			$procedure_status_same_name = $this->Permitting_procedure_status_model->get_all_where(array("id_cliente" =>$this->input->post('client'), "nombre_estado" => $this->input->post('name'), "deleted" => 0))->result();
			if($procedure_status_same_name){
				echo json_encode(array("success" => false, 'message' => lang('permitting_warning')));
				exit();
			}			
		}else{
			$procedure_status_same_name = $this->Permitting_procedure_status_model->get_all_where(array("id_cliente" =>$this->input->post('client'), "nombre_estado" => $this->input->post('name'), "deleted" => 0));
			if($procedure_status_same_name->num_rows() && $procedure_status_same_name->row()->id != $procedure_status_id){
				echo json_encode(array("success" => false, 'message' => lang('permitting_warning')));
				exit();
			}
		}
		
		
		if($procedure_status_id){ //edit

			$data_procedure_status["modified_by"] = $this->login_user->id;
			$data_procedure_status["modified"] = get_current_utc_time();
			$save_id = $this->Permitting_procedure_status_model->save($data_procedure_status, $procedure_status_id);
			
		} else { //insert
			
			$data_procedure_status["created_by"] = $this->login_user->id;
			$data_procedure_status["created"] = get_current_utc_time();
			$save_id = $this->Permitting_procedure_status_model->save($data_procedure_status);

		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	

	function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

		//SI EL ESTADO ESTÁ SIENDO UTILIZADO EN UNA TRAMITACIÓN NO SE DEBE ELIMINAR
		
		//busco si el estado está siendo utilizado en alguna tramitación de permisos
		$hay_tramitaciones = FALSE;
		$tramitaciones = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_estados_tramitacion_permisos" => $id, "deleted" => 0))->result_array();
		if($tramitaciones){
			$hay_tramitaciones = TRUE;
		}
		
		if($hay_tramitaciones){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_permitting_procedure_status')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->Permitting_procedure_status_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Permitting_procedure_status_model->delete($id)) {
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
			"categoria" => $this->input->post("categoria")
		);
		
        $list_data = $this->Permitting_procedure_status_model->get_details($options)->result();
		
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
		
        $data = $this->Permitting_procedure_status_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$color = '<div id="coloricon1" style="border: 1px solid black; height:15px; width:15px; background-color:'.$data->color.'; border-radius: 50%;"></div>';
		
        $row_data = array($data->id, $cliente->company_name, $data->nombre_estado, $data->categoria, $color);

        $row_data[] =  modal_anchor(get_uri("permitting_procedure_status/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_procedure_status'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("permitting_procedure_status/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_procedure_status'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_procedure_status'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("permitting_procedure_status/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	function view($procedure_status_id = 0) {
        $this->access_only_allowed_members();
		
        if ($procedure_status_id) {
           
		    $options = array("id" => $procedure_status_id);
            $procedure_status_info = $this->Permitting_procedure_status_model->get_details($options)->row();
			
            if ($procedure_status_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				
				$view_data['model_info'] = $procedure_status_info;
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$view_data["cliente"] = $cliente->company_name;

				$this->load->view('permitting_procedure_status/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
		
}

