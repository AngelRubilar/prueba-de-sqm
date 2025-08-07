<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Communities_evaluation_status extends MY_Controller {

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
		$array_categorias[] = array("id" => "Tramitación", "text" => lang("procedure"));
		$array_categorias[] = array("id" => "Cumplimiento de Actividades", "text" => lang("activities_compliance"));
		$array_categorias[] = array("id" => "Cumplimiento Financiero", "text" => lang("financial_compliance"));
		$view_data['categorias_dropdown'] = json_encode($array_categorias);
		
		$this->template->rander("communities_evaluation_status/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $evaluation_status_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');       
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
		if($evaluation_status_id){ //edit
			$view_data['model_info'] = $this->Communities_evaluation_status_model->get_one($evaluation_status_id);		
		} 
		
        $this->load->view('communities_evaluation_status/modal_form', $view_data);
    }
	
	
	function save() {

        $evaluation_status_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		$data_evaluation_status = array(
			"id_cliente" => $this->input->post('client'),
			"nombre_estado" => $this->input->post('name'),
			"categoria" => $this->input->post('categoria'),
			"color" => $this->input->post('color')
			
		);
		
		if($evaluation_status_id){ //edit
			
			//Validación de un nombre único para cliente/estado
			$evaluation_status = $this->Communities_evaluation_status_model->get_one($evaluation_status_id);
			if($data_evaluation_status["nombre_estado"] !== $evaluation_status->nombre_estado){
				$evaluations_status = $this->Communities_evaluation_status_model->get_all_where(array("id_cliente" => $data_evaluation_status["id_cliente"], "deleted" => 0))->result();
				foreach($evaluations_status as $data){
					if($data_evaluation_status["id_cliente"] == $data->id_cliente){
						if($data_evaluation_status["nombre_estado"] == $data->nombre_estado){
							if($data_evaluation_status["categoria"] == $data->categoria){
								echo json_encode(array("success" => false, 'message' => lang('status_name_warning')));
								exit();
							}							
						}
					}
				}
			}

			$data_evaluation_status["modified_by"] = $this->login_user->id;
			$data_evaluation_status["modified"] = get_current_utc_time();
			$save_id = $this->Communities_evaluation_status_model->save($data_evaluation_status, $evaluation_status_id);
			
		} else { //insert
			
			//Validación de un nombre único para cliente/estado
			//Ahora debe ser cliente/tipo_estado/estado
			$nombre_estado = $data_evaluation_status["nombre_estado"];
			$categoria_estado = $data_evaluation_status["categoria"];
			$client = $data_evaluation_status["id_cliente"];
			$evaluations_status = $this->Communities_evaluation_status_model->get_all_where(array("id_cliente" => $data_evaluation_status["id_cliente"], "deleted" => 0))->result();
			foreach($evaluations_status as $data){
				if($client == $data->id_cliente){
					if($nombre_estado == $data->nombre_estado){
						if($categoria_estado == $data->categoria){
							echo json_encode(array("success" => false, 'message' => lang('status_name_warning')));
							exit();
						}						
					}
				}
			}
			
			$data_evaluation_status["created_by"] = $this->login_user->id;
			$data_evaluation_status["created"] = get_current_utc_time();
			$save_id = $this->Communities_evaluation_status_model->save($data_evaluation_status);

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
		
		//SI EL ESTADO ESTÁ SIENDO UTILIZADO EN UN SEGUIMIENTO DE ACUERDOS NO SE DEBE ELIMINAR
		
		//busco si el estado está siendo utilizado en algun seguimiento de acuerdos
		$hay_seguimiento = FALSE;
		$seguimiento_estado_tramitacion = $this->Agreements_monitoring_model->get_all_where(array("estado_tramitacion" => $id, "deleted" => 0))->result_array();
		$seguimiento_estado_actividades = $this->Agreements_monitoring_model->get_all_where(array("estado_actividades" => $id, "deleted" => 0))->result_array();
		$seguimiento_estado_financiero = $this->Agreements_monitoring_model->get_all_where(array("estado_financiero" => $id, "deleted" => 0))->result_array();
		if($seguimiento_estado_tramitacion || $seguimiento_estado_actividades || $seguimiento_estado_financiero){
			$hay_seguimiento = TRUE;
		}
		
		if($hay_seguimiento){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_communities_evaluation_status')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->Communities_evaluation_status_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Communities_evaluation_status_model->delete($id)) {
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
		
        $list_data = $this->Communities_evaluation_status_model->get_details($options)->result();
		
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
		
        $data = $this->Communities_evaluation_status_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$color = '<div id="coloricon1" style="border: 1px solid black; height:15px; width:15px; background-color:'.$data->color.'; border-radius: 50%;"></div>';
		
        $row_data = array($data->id, $cliente->company_name, $data->nombre_estado, $data->categoria, $color);

        $row_data[] =  modal_anchor(get_uri("communities_evaluation_status/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_status'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("communities_evaluation_status/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_status'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_status'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("communities_evaluation_status/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	function view($evaluation_status_id = 0) {
        $this->access_only_allowed_members();
		
        if ($evaluation_status_id) {
           
		    $options = array("id" => $evaluation_status_id);
            $evaluation_status_info = $this->Communities_evaluation_status_model->get_details($options)->row();
			
            if ($evaluation_status_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				
				$view_data['model_info'] = $evaluation_status_info;
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$view_data["cliente"] = $cliente->company_name;

				$this->load->view('communities_evaluation_status/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
		
}

