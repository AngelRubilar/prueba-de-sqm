<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compromises_compliance_status extends MY_Controller {

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
		
		$array_tipos_evaluacion[] = array("id" => "", "text" => "- ".lang("evaluation_type")." -");
		$array_tipos_evaluacion[] = array("id" => "rca", "text" => lang("rca"));
		$array_tipos_evaluacion[] = array("id" => "reportable", "text" => lang("reportable"));
		$view_data['tipo_evaluacion_dropdown'] = json_encode($array_tipos_evaluacion);
		
        $this->template->rander("compromises_compliance_status/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $compliance_status_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');       
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
		if($compliance_status_id){ //edit
			$view_data['model_info'] = $this->Compromises_compliance_status_model->get_one($compliance_status_id);		
		} 
		
        $this->load->view('compromises_compliance_status/modal_form', $view_data);
    }
	
	
	function save() {

        $compliance_status_id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		$data_compliance_status = array(
			"id_cliente" => $this->input->post('client'),
			"nombre_estado" => $this->input->post('name'),
			"tipo_evaluacion" => $this->input->post('evaluation_type'),
			"categoria" => $this->input->post('categoria'),
			"color" => $this->input->post('color')
		);
		
		
		if(!$compliance_status_id){
			$compliance_status_same_name = $this->Compromises_compliance_status_model->get_all_where(
				array(
					"id_cliente" =>$this->input->post('client'), 
					"tipo_evaluacion" => $this->input->post('evaluation_type'),
					"nombre_estado" => $this->input->post('name'), 
					"deleted" => 0
				)
			)->result();
			if($compliance_status_same_name){
				echo json_encode(array("success" => false, 'message' => lang('comprmise_warning')));
				exit();
			}			
		}else{
			$compliance_status_same_name = $this->Compromises_compliance_status_model->get_all_where(
				array(
					"id_cliente" =>$this->input->post('client'), 
					"tipo_evaluacion" => $this->input->post('evaluation_type'),
					"nombre_estado" => $this->input->post('name'), 
					"deleted" => 0
				)
			);
			if($compliance_status_same_name->num_rows() && $compliance_status_same_name->row()->id != $compliance_status_id){
				echo json_encode(array("success" => false, 'message' => lang('comprmise_warning')));
				exit();
			}
		}
		
		if($compliance_status_id){ //edit
			
			if($this->input->post('evaluation_type') == "rca"){
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA NO APLICA
				if($this->input->post('categoria') == "No Aplica"){
					$estado = $this->Compromises_compliance_status_model->get_one($compliance_status_id);
					
					if($estado->id_cliente == $this->input->post('client') && $estado->tipo_evaluacion == "rca" && $estado->categoria != "No Aplica" && $this->input->post('categoria') == "No Aplica"){
						
						$estado_rca_no_aplica = $this->Compromises_compliance_status_model->get_all_where(array(
							"id_cliente" => $this->input->post('client'),
							"tipo_evaluacion" => "rca",
							"categoria" => "No Aplica",
							"deleted" => 0
						));		
					
						if($estado_rca_no_aplica->num_rows() && $estado_rca_no_aplica->row()->id != $compliance_status_id){
							echo json_encode(array("success" => false, 'message' => lang("rca_status_no_apply_message")));
							exit();
						}
					}
				}
			}
			
			if($this->input->post('evaluation_type') == "reportable"){
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA NO CUMPLE
				if($this->input->post('categoria') == "No Cumple"){
					$estado_reportable_no_cumple = $this->Compromises_compliance_status_model->get_all_where(array(
						"id_cliente" => $this->input->post('client'),
						"tipo_evaluacion" => "reportable",
						"categoria" => "No Cumple",
						"deleted" => 0
					));
					
					if($estado_reportable_no_cumple->num_rows() && $estado_reportable_no_cumple->row()->id != $compliance_status_id){
						echo json_encode(array("success" => false, 'message' => lang("reportable_status_not_fulfill_message")));
						exit();
					}
				}
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA PENDIETNE
				if($this->input->post('categoria') == "Pendiente"){
					$estado_reportable_pendiente = $this->Compromises_compliance_status_model->get_all_where(array(
						"id_cliente" => $this->input->post('client'),
						"tipo_evaluacion" => "reportable",
						"categoria" => "Pendiente",
						"deleted" => 0
					));
					
					if($estado_reportable_pendiente->num_rows() && $estado_reportable_pendiente->row()->id != $compliance_status_id){
						echo json_encode(array("success" => false, 'message' => lang("reportable_status_pending_message")));
						exit();
					}
				}
			}
				
			$data_compliance_status["modified_by"] = $this->login_user->id;
			$data_compliance_status["modified"] = get_current_utc_time();
			$save_id = $this->Compromises_compliance_status_model->save($data_compliance_status, $compliance_status_id);
				
		} else { //insert
			
			if($this->input->post('evaluation_type') == "rca"){
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA NO APLICA
				if($this->input->post('categoria') == "No Aplica"){
					$estado_rca_no_aplica = $this->Compromises_compliance_status_model->get_all_where(array(
						"id_cliente" => $this->input->post('client'),
						"tipo_evaluacion" => "rca",
						"categoria" => "No Aplica",
						"deleted" => 0
					));
					
					if($estado_rca_no_aplica->num_rows()){
						echo json_encode(array("success" => false, 'message' => lang("rca_status_no_apply_message")));
						exit();
					}
				}
			}
			
			if($this->input->post('evaluation_type') == "reportable"){
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA NO CUMPLE
				if($this->input->post('categoria') == "No Cumple"){
					$estado_reportable_no_cumple = $this->Compromises_compliance_status_model->get_all_where(array(
						"id_cliente" => $this->input->post('client'),
						"tipo_evaluacion" => "reportable",
						"categoria" => "No Cumple",
						"deleted" => 0
					));
					
					if($estado_reportable_no_cumple->num_rows()){
						echo json_encode(array("success" => false, 'message' => lang("reportable_status_not_fulfill_message")));
						exit();
					}
				}
				
				// VALIDO QUE PARA UN CLIENTE, NO HAYA MAS DE 1 ESTADO CON CATEGORIA PENDIENTE
				if($this->input->post('categoria') == "Pendiente"){
					$estado_reportable_pendiente = $this->Compromises_compliance_status_model->get_all_where(array(
						"id_cliente" => $this->input->post('client'),
						"tipo_evaluacion" => "reportable",
						"categoria" => "Pendiente",
						"deleted" => 0
					));
					
					if($estado_reportable_pendiente->num_rows()){
						echo json_encode(array("success" => false, 'message' => lang("reportable_status_pending_message")));
						exit();
					}
				}
			}
			
			$data_compliance_status["created_by"] = $this->login_user->id;
			$data_compliance_status["created"] = get_current_utc_time();
			$save_id = $this->Compromises_compliance_status_model->save($data_compliance_status);
			
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
		
		//SI EL ESTADO ESTÁ SIENDO UTILIZADO EN UNA EVALUACIÓN NO SE DEBE ELIMINAR
		$hay_evaluaciones = FALSE;
		if($tipo_evaluacion == "rca"){
			$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_estados_cumplimiento_compromiso" => $id, "deleted" => 0))->result_array();
		}else{
			$evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(array("id_estados_cumplimiento_compromiso" => $id, "deleted" => 0))->result_array();
		}
		
		if($evaluaciones){
			$hay_evaluaciones = TRUE;
		}
		if($hay_evaluaciones){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_compromise_compliance_status')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->Compromises_compliance_status_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Compromises_compliance_status_model->delete($id)) {
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
			"categoria" => $this->input->post("categoria"),
			"tipo_evaluacion" => $this->input->post("tipo_evaluacion")
		);
		
        $list_data = $this->Compromises_compliance_status_model->get_details($options)->result();
		
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
		
        $data = $this->Compromises_compliance_status_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$color = '<div id="coloricon1" style="border: 1px solid black; height:15px; width:15px; background-color:'.$data->color.'; border-radius: 50%;"></div>';
		
        $row_data = array($data->id, $cliente->company_name, $data->nombre_estado, lang($data->tipo_evaluacion), $data->categoria, $color);

        $row_data[] =  modal_anchor(get_uri("compromises_compliance_status/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_status'), "data-post-id" => $data->id))
				.  modal_anchor(get_uri("compromises_compliance_status/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_compliance_status'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_status'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("compromises_compliance_status/delete/".$data->tipo_evaluacion), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	function view($compliance_status_id = 0) {
        $this->access_only_allowed_members();
		
        if ($compliance_status_id) {
           
		    $options = array("id" => $compliance_status_id);
            $compliance_status_info = $this->Compromises_compliance_status_model->get_details($options)->row();
			
            if ($compliance_status_info) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				
				$view_data['model_info'] = $compliance_status_info;
				$cliente = $this->Clients_model->get_one($view_data['model_info']->id_cliente);
				$view_data["cliente"] = $cliente->company_name;

				$this->load->view('compromises_compliance_status/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
		
}

