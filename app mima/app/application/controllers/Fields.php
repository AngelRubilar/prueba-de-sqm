<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fields extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

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
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO TIPOS DE CAMPO
		$array_tipos_campo[] = array("id" => "", "text" => "- ".lang("field_type")." -");
		$tipos_campo = $this->Field_types_model->get_dropdown_list(array("nombre"), 'id');
		foreach($tipos_campo as $id => $nombre){
			$array_tipos_campo[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['tipos_campo_dropdown'] = json_encode($array_tipos_campo);
		
        $this->template->rander("fields/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $field_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-2";
        $view_data['field_column'] = "col-md-10";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        //$view_data['model_info'] = $this->Fields_model->get_details(array('id' => $field_id))->result();
		
		$model_info = $this->Fields_model->get_one($field_id);
		$view_data['model_info'] = $model_info;
		$view_data['tipos_de_unidad'] = array("" => "-") + $this->Unity_type_model->get_dropdown_list(array("id" => "nombre"));
		
		if($field_id){
			
			if($view_data['model_info']->opciones){
				$opciones = json_decode($view_data['model_info']->opciones);
				$seleccionada = $opciones[0]->id_tipo_unidad;
				$symbol_seleccionado = $opciones[0]->id_unidad;
			}
			
			$unidad = $this->Unity_model->get_dropdown_list(array("nombre"),"id", array("id_tipo_unidad"=>$seleccionada));
			$view_data["symbol_select_values"]= $unidad;
			//var_dump($view_data["symbol_select_values"]);
			$view_data['tipo_campo'] = $this->Fields_model->get_details(array('id' => $field_id))->result();
			
			
			// Deshabilitar campos cuando el campo esté siendo usado en algún formulario
			$campo_utilizado_en_formulario = FALSE;
			$campo_en_formulario = $this->Field_rel_form_model->get_all_where(array(
				"id_campo" => $field_id, 
				"deleted" => 0
			))->result_array();
			
			if($campo_en_formulario){
				$campo_utilizado_en_formulario = TRUE;
			}
			
			$view_data["campo_utilizado_en_formulario"] = $campo_utilizado_en_formulario;
			
			
			// Deshabilitar campos cuando el campo esté siendo usado en alguna matriz
			$campo_utilizado_en_matriz_rca = FALSE;
			
			$matriz_compromisos_rca = $this->Compromises_rca_model->get_one_where(array("id_proyecto" => $model_info->id_proyecto, "deleted" => 0));						
			$campo_en_matriz_compromisos_rca = $this->Compromises_rca_rel_fields_model->get_all_where(array(
				"id_compromiso" => $matriz_compromisos_rca->id,
				"id_campo" => $field_id,
				"deleted" => 0
			))->result_array();			
			if($campo_en_matriz_compromisos_rca){
				$campo_utilizado_en_matriz_rca = TRUE;
			}

			$matriz_permisos = $this->Permitting_model->get_one_where(array("id_proyecto" => $model_info->id_proyecto, "deleted" => 0));						
			$campo_en_matriz_permisos = $this->Permitting_rel_fields_model->get_all_where(array(
				"id_permiso" => $matriz_permisos->id,
				"id_campo" => $field_id,
				"deleted" => 0
			))->result_array();			
			if($campo_en_matriz_permisos){
				$campo_utilizado_en_matriz = TRUE;
			}
			
			$matriz_stakeholder = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $model_info->id_proyecto, "deleted" => 0));						
			$campo_en_matriz_stakeholder = $this->Stakeholders_rel_fields_model->get_all_where(array(
				"id_stakeholder_matrix_config" => $matriz_stakeholder->id,
				"id_campo" => $field_id,
				"deleted" => 0
			))->result_array();			
			if($campo_en_matriz_stakeholder){
				$campo_utilizado_en_matriz = TRUE;
			}
			
			$matriz_acuerdos = $this->Agreements_matrix_config_model->get_one_where(array("id_proyecto" => $model_info->id_proyecto, "deleted" => 0));						
			$campo_en_matriz_acuerdos = $this->Agreements_rel_fields_model->get_all_where(array(
				"id_agreement_matrix_config" => $matriz_acuerdos->id,
				"id_campo" => $field_id,
				"deleted" => 0
			))->result_array();			
			if($campo_en_matriz_acuerdos){
				$campo_utilizado_en_matriz = TRUE;
			}
			
			$matriz_feedback = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $model_info->id_proyecto, "deleted" => 0));						
			$campo_en_matriz_feedback = $this->Feedback_rel_fields_model->get_all_where(array(
				"id_feedback_matrix_config" => $matriz_acuerdos->id,
				"id_campo" => $field_id,
				"deleted" => 0
			))->result_array();			
			if($campo_en_matriz_feedback){
				$campo_utilizado_en_matriz = TRUE;
			}
			
			$view_data["campo_utilizado_en_matriz"] = $campo_utilizado_en_matriz;
			

			// Deshabilitar campos cuando el campo esté siendo usado en relacionamiento
			$campo_utilizado_en_relacionamiento = FALSE;
			$is_field_used_in_rule = $this->Rule_model->is_field_used_in_rule($model_info->id);
			if($is_field_used_in_rule){
				$campo_utilizado_en_relacionamiento = TRUE;
			}
			
			$view_data["campo_utilizado_en_relacionamiento"] = $campo_utilizado_en_relacionamiento;
	
		}
		
        //$view_data["field_types_dropdown"] = array("" => "-") + $this->Field_types_model->get_dropdown_list(array("nombre"), "id");
		$view_data["field_types_dropdown"] = array("" => "-") + $this->Field_types_model->get_dropdown_list(array("nombre"), "nombre");
		
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id");
		
        $this->load->view('fields/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a field */

    function save() {
        $field_id = $this->input->post('id');
		
		$default_values = "";
        $opciones = "";
        $obligatorio = $this->input->post('obligatory_field') ? $this->input->post('obligatory_field') : "";
        $habilitado = $this->input->post('disabled_field') ? $this->input->post('disabled_field') : "";
		
		/*$created_by = "";
		$created = "";
		$modified_by = "";
		$modified = "";*/
		
        //$this->access_only_allowed_members_or_client_contact($field_id);

        /*validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required"
        ));*/
		
		$tipo_campo = $this->input->post('field_type');
        $info_tipo_campo = $this->Field_types_model->get_one_where(array("nombre" => $tipo_campo));
		
		if($this->input->post('field_type')){
			if($this->input->post('field_type') == "Input text"){
				$default_values = $this->input->post('default_value_field');
			}
			if($this->input->post('field_type') == "Texto Largo"){	
				$default_values = $this->input->post('default_value_field');			
			}
			if($this->input->post('field_type') == "Número"){
				$default_values = $this->input->post('default_value_field');
			}
			if($this->input->post('field_type') == "Fecha"){
				$default_values = $this->input->post('default_date_field');
			}
			if($this->input->post('field_type') == "Periodo"){
				$default_values1 = $this->input->post('default_date_field1');
				$default_values2 = $this->input->post('default_date_field2');
				$default_values = json_encode(array('start_date' => $default_values1, 'end_date' => $default_values2));
			}
			if($this->input->post('field_type') == "Selección"){
				$default_values = $this->input->post('default_value_field');
				
				$array_opciones = array();
				//$default_label = $this->input->post('label');
				$default_value = $this->input->post('value');
				$array_opciones[] = array('value' => "", 'text' => $default_value);
				//$array_labels = $this->input->post('labels');
				$array_values = $this->input->post('values');
				foreach($array_values as $index => $value){
					//$array_opciones[] = array('value' => $array_values[$index], 'text' => $label);
					$array_opciones[] = array('value' => $value, 'text' => $value);
				}
				array_splice($array_opciones, 1, 1);
				$opciones = json_encode($array_opciones);
				
			}
			if($this->input->post('field_type') == "Selección Múltiple"){
				$default_values = $this->input->post('default_value_field');
				$array_opciones = array();
				//$default_label = $this->input->post('label');
				//$default_value = $this->input->post('value');
				//$array_opciones[] = array('value' => $default_value, 'text' => $default_label);
				$array_labels = $this->input->post('labels');
				$array_values = $this->input->post('values');
				foreach($array_labels as $index => $label){
					$array_opciones[] = array('value' => $array_values[$index], 'text' => $label);
				}
				array_splice($array_opciones, 0, 1);
				$opciones = json_encode($array_opciones);
				
				if($default_values != NULL){
					$default_values = json_encode($default_values);
				} 

			}
			if($this->input->post('field_type') == "Rut"){
				$default_values = $this->input->post('default_value_field');
			}
			if($this->input->post('field_type') == "Radio Buttons"){
				$default_values = $this->input->post('default_value_field');
				
				$array_opciones = array();
				$array_labels_values = $this->input->post('values_radio');
				foreach($array_labels_values as $index => $label){
					$array_opciones[] = array('value' => $label, 'text' => $label);
				}
				array_splice($array_opciones, 0, 1);
				$opciones = json_encode($array_opciones);
				
			}
			if($this->input->post('field_type') == "Archivo"){
				//$default_values = $this->input->post('default_value_field');
			}
			if($this->input->post('field_type') == "Texto Fijo"){
				$default_values = $this->input->post('default_value_field');
			}
			if($this->input->post('field_type') == "Divisor"){
				$default_values = '<hr>';
			}
			if($this->input->post('field_type') == "Correo"){
				$default_values = $this->input->post('default_value_field');
			}
			
			//#########
			if($this->input->post('field_type') == "Hora"){
				$default_values = $this->input->post('default_time_field');
			}
			/* if($this->input->post('field_type') == "Unidad"){
				$default_values = $this->input->post('default_value_field');
				
				$array_unit = array();
				$array_unit_values[] = $this->input->post('unit_field');				
				foreach($array_unit_values as $index => $unit){
					$array_unit[] = array('value' => $unit, 'text' => $unit);
				}
				$opciones = json_encode($array_unit);
			} */
			if($this->input->post('field_type') == "Unidad"){
				$default_values = $this->input->post('default_value_field');
				
				$array_unit = array();
				$unit_field = $this->input->post('unit_field'); 
				$unit_symbol = $this->input->post('unit_symbol');
				$array_unit[] = array('id_tipo_unidad' => $unit_field,'id_unidad' => $unit_symbol);
				$opciones = json_encode($array_unit);
			}
			
			if($this->input->post('field_type') == "Selección desde Mantenedora"){
				$mantenedora = $this->input->post('feeder_table');
				//$field_label = $this->input->post('labels_field');
				$field_value = $this->input->post('values_field');
				
				//$default_values = json_encode(array('mantenedora' => $mantenedora, 'field_label' => $field_label, 'field_value' => $field_value));
				$default_values = json_encode(array('mantenedora' => $mantenedora, 'field_label' => $field_value, 'field_value' => $field_value));
				$opciones = "";
				
			}
			
		}
		
		if($field_id){
			$field_info = $this->Fields_model->get_one($field_id);
		}
		
        $data = array(
			"id_tipo_campo" => ($this->input->post('field_type')) ? $info_tipo_campo->id : $field_info->id_tipo_campo,
			"id_cliente" => ($this->input->post('client')) ? $this->input->post('client') : $field_info->id_cliente,
			"id_proyecto" => ($this->input->post('project')) ? $this->input->post('project'): $field_info->id_proyecto ,
            "nombre" => $this->input->post('field_name'),
			"default_value" => $default_values,
            "opciones" => $opciones,
            "obligatorio" => $obligatorio,
            "habilitado" => $habilitado,
        );

		/* if($default_values != NULL){
			$data["default_value"] = $default_values;
		} */
		
		if($field_id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
		/*
        if ($this->login_user->is_admin) {
            $data["currency_symbol"] = $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "";
            $data["currency"] = $this->input->post('currency') ? $this->input->post('currency') : "";
            $data["disable_online_payment"] = $this->input->post('disable_online_payment') ? $this->input->post('disable_online_payment') : 0;
        }*/
		
		if(!$field_id){
			$field_same_name = $this->Fields_model->get_all_where(array("id_cliente" =>$this->input->post('client'), "id_proyecto" =>$this->input->post('project'), "nombre" => $this->input->post('field_name'), "deleted" => 0))->result();
			if($field_same_name){
				echo json_encode(array("success" => false, 'message' => lang('fields_warning')));
				exit();
			}			
		}else{
			$field_same_name = $this->Fields_model->get_all_where(array("id_cliente" =>$this->input->post('client'), "id_proyecto" =>$this->input->post('project'), "nombre" => $this->input->post('field_name'), "deleted" => 0));
			if($field_same_name->num_rows() && $field_same_name->row()->id != $field_id){
				echo json_encode(array("success" => false, 'message' => lang('fields_warning')));
				exit();
			}
		}

        $save_id = $this->Fields_model->save($data, $field_id);
        if ($save_id) {
			
			if($this->input->post('field_type') == "Periodo"){
				$data["html_name"] = json_encode(array(
					'start_name' => $this->clean($save_id.'_'.$this->input->post('field_name').'_start'), 
					'end_name' => $this->clean($save_id.'_'.$this->input->post('field_name').'_end')
				));
			}else{
				$data["html_name"] = $this->clean($save_id.'_'.$this->input->post('field_name'));
			}
			$save_id = $this->Fields_model->save($data, $save_id);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
    }

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		$field_rel_form = $this->Field_rel_form_model->get_all_where(array("id_campo" => $id, "deleted" => 0))->result_array();
		
		//Si el campo a eliminar está asociado a algún formulario
		if(count($field_rel_form) > 0){
			
			echo json_encode(array("success" => false, 'message' => lang('busy_field_message')));
			exit();
			
		} else {
			
			if ($this->input->post('undo')) {
				if ($this->Fields_model->delete($id, true)) {
					echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
				} else {
					echo json_encode(array("success" => false, lang('error_occurred')));
				}
			} else {
				if ($this->Fields_model->delete($id)) {
					echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
				} else {
					echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
				}
			}
			
		}
        
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_tipo_campo" => $this->input->post("id_tipo_campo")
		);
		
        $list_data = $this->Fields_model->get_details($options)->result();
        $result = array();
		
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of field list  table */

    private function _row_data($id) {
        //$custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
           // "custom_fields" => $custom_fields
        );
        $data = $this->Fields_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		
        $row_data = array($data->id,
            //anchor(get_uri("fields/view/" . $data->id), $data->nombre),
			modal_anchor(get_uri("fields/view/" . $data->id), $data->nombre, array("title" => lang('view_field'))),
            $data->tipo_campo,
			$cliente->company_name,
			$data->proyecto,
			$data->creado_por,
        );

        $row_data[] = modal_anchor(get_uri("fields/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_field'))) 
				. modal_anchor(get_uri("fields/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_field'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("fields/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load field details view */

    function view($field_id = 0) {
        $this->access_only_allowed_members();

        if ($field_id) {
            $options = array("id" => $field_id);
            $field_info = $this->Fields_model->get_details($options)->row();
            if ($field_info) {

                //$access_info = $this->get_access_info("invoice");
                //$view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;

                //$access_info = $this->get_access_info("estimate");
                //$view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                //$access_info = $this->get_access_info("estimate_request");
                //$view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_info->access_type == "all") ? true : false;

                //$access_info = $this->get_access_info("ticket");
                //$view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_info->access_type == "all") ? true : false;

                //$view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                //$view_data["show_event_info"] = (get_setting("module_event")) ? true : false;
				
				$view_data["preview"] = $this->get_preview_field($field_info->id);
				//var_dump($view_data["preview"]);
                $view_data['field_info'] = $field_info;

                //$view_data["is_starred"] = strpos($client_info->starred_by, ":" . $this->login_user->id . ":") ? true : false;

                $view_data["tab"] = $tab;

                //$this->template->rander("fields/view", $view_data);
				$this->load->view('fields/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	/* traigo las opciones de acuerdo al tipo de campo */

    function get_field_type_options() {

        $id = $this->input->post('id_field');
		$client_id = $this->input->post('client_id');
		$project_id = $this->input->post('project_id');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		if($id == "Input text"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => lang('default_value_field'),
					"autofocus" => true,
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Texto Largo"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_textarea(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => lang('default_value_field'),
					"style" => "height:150px;",
					"autocomplete" => "off",
					"autocomplete" => "off",
					"maxlength" => "2000"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Número"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => lang('default_value_field'),
					"autofocus" => true,
					"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
					"data-msg-regex" => lang("number_or_decimal_required"),
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Fecha"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_date_field" class="col-md-2">'.lang('default_date_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_date_field",
					"name" => "default_date_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => "YYYY-MM-DD",
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Periodo"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_date_field" class="col-md-2">'.lang('default_date_field').'</label>';
				$html .= '<div class="col-md-5">';
				$html .= form_input(array(
					"id" => "default_date_field1",
					"name" => "default_date_field1",
					"value" => '',
					"class" => "form-control",
					"placeholder" => "YYYY-MM-DD",
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
				
				$html .= '<div class="col-md-5">';
				$html .= form_input(array(
					"id" => "default_date_field2",
					"name" => "default_date_field2",
					"value" => '',
					"class" => "form-control",
					"placeholder" => "YYYY-MM-DD",
					"data-rule-greaterThanOrEqual" => "#default_date_field1",
                	"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Selección"){
			$html = '';
			
			// FILA POR DEFECTO
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("default_value_field", array("" => "-"), "", "id='default_value_field' class='select2'");
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA AGREGAR - QUITAR
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= '<div class="col-md-9">&nbsp;</div>';
				$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
				$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA OPCIONES
			$html .= '<div class="form-group default">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('select_field_options').'</label>';
			
				// CAMPO LABEL
				/*$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "label",
					"value" => '-',
					"class" => "form-control",
					"placeholder" => "",
					//"autofocus" => true,
					//"disabled" => true,
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
			
				// CAMPO VALUE
				$html .= '<div class="col-md-8">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "value",
					"value" => '',
					"class" => "form-control",
					//"placeholder" => lang('empty'),
					//"autofocus" => true,
					"readonly" => true,
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
				*/
				
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "value",
					"value" => '-',
					"class" => "form-control",
					"autocomplete" => "off",
					"maxlength" => "255",
					"data-rule-required" => "true",
					"data-msg-required" => lang("field_required")
				));
				$html .= '</div>';
				
				
			$html .= '</div>';
		}
		
		if($id == "Selección Múltiple"){
			$html = '';
			
			// FILA POR DEFECTO
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				//$html .= form_multiselect("default_value_field[]", NULL/*array("" => "-")*/, NULL, "id='default_value_field' class='multiple' multiple='multiple'");
				$html .= '<select name="default_value_field[]" id="default_value_field" class="multiple" multiple="multiple" style="position: absolute; left: -9999px;"></select>';
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA AGREGAR - QUITAR
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= '<div class="col-md-9">&nbsp;</div>';
				$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
				$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA OPCIONES
			$html .= '<div class="form-group default">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('select_field_options').'</label>';
			
				// CAMPO LABEL
				/*$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "label",
					"value" => '-',
					"class" => "form-control",
					"placeholder" => "",
					//"autofocus" => true,
					//"disabled" => true,
					"autocomplete" => "off",
				));
				$html .= '</div>';
			
				// CAMPO VALUE
				$html .= '<div class="col-md-4">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "value",
					"value" => '',
					"class" => "form-control",
					//"placeholder" => lang('empty'),
					//"autofocus" => true,
					"readonly" => true,
					"autocomplete" => "off",
				));
				$html .= '</div>';*/
				
			$html .= '</div>';
		}
		
		if($id == "Rut"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => '',
					"class" => "form-control rut",
					"placeholder" => lang('default_value_field'),
					"autofocus" => true,
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"data-rule-minlength" => 6,
					"data-msg-minlength" => lang("enter_minimum_6_characters"),
					"data-rule-maxlength" => 13,
					"data-msg-maxlength" => lang("enter_maximum_13_characters"),
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Radio Buttons"){
			$html = '';
			
			// FILA POR DEFECTO
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("default_value_field", array("Opción 1" => "Opción 1", "Opción 2" => "Opción 2"), "", "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA AGREGAR - QUITAR
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= '<div class="col-md-9">&nbsp;</div>';
				$html .= '<button type="button" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>';
				$html .= '<button type="button" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>';
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA OPCIONES
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('radio_field_options').'</label>';
			
				// CAMPO VALUE
				$html .= '<div class="col-md-8">';
				$html .= form_input(array(
					"id" => "default_value_field1",
					"name" => "values_radio[1]",
					"value" => 'Opción 1',
					"class" => "form-control",
					"placeholder" => lang('option_1'),
					//"autofocus" => true,
					"autocomplete" => "off",
					"maxlength" => "255",
					"data-rule-required" => "true",
					"data-msg-required" => lang("field_required")
				));
				$html .= '</div>';
				
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2"></label>';
			
				// CAMPO VALUE
				$html .= '<div class="col-md-8">';
				$html .= form_input(array(
					"id" => "default_value_field2",
					"name" => "values_radio[2]",
					"value" => 'Opción 2',
					"class" => "form-control",
					"placeholder" => lang('option_2'),
					//"autofocus" => true,
					"autocomplete" => "off",
					"maxlength" => "255",
					"data-rule-required" => "true",
					"data-msg-required" => lang("field_required")
				));
				$html .= '</div>';
				
			$html .= '</div>';
		}
		
		if($id == "Archivo"){
			
		}
		
		if($id == "Texto Fijo"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_textarea(array(
					"id" => "default_value_field_rich",
					"name" => "default_value_field",
					"value" => '',
					"placeholder" => lang('default_value_field'),
					"class" => "form-control",
					"autocomplete" => "off",
					//"maxlength" => "2000"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Divisor"){
			
		}
		
		if($id == "Correo"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => lang('email'),
					"autofocus" => true,
					"data-rule-email" => true,
					"data-msg-email" => lang("enter_valid_email"),
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
					"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Hora"){
			$html = '';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_time_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_time_field",
					"name" => "default_time_field",
					"value" => '',
					"class" => "form-control",
					"placeholder" => lang('default_value_field'),
					"autofocus" => true,
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($id == "Unidad"){
		
			$seleccionada = "";
			if($model_info->opciones){
				$opciones = json_decode($model_info->opciones);
				$seleccionada = $opciones[0]->id_tipo_unidad;
			}
			
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('default_value_field').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => "default_value_field",
					"value" => $model_info->default_value,
					"class" => "form-control",
					"placeholder" => lang('default_value_field'),
					"autofocus" => true,
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
					"data-msg-regex" => lang("number_or_decimal_required"),
					"autocomplete" => "off",
					//"maxlength" => "255"
				));
				$html .= '</div>';
			$html .= '</div>';

		// FILA POR DEFECTO
		/*$html .= '<div class="form-group">';
			$html .= '<label for="unit_field" class="col-md-2">'.lang('unit_type').'</label>';
			$html .= '<div class="col-md-5">';
			$html .= form_dropdown("unit_field", array("" => "-", "Masa" => "Masa", "Volumen" => "Volumen", "Longitud" => "Longitud", "Superficie" => "Superficie", "Potencia" => "Potencia", "Energía" => "Energía"), $seleccionada, "id='unit_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';*/
			
			$tipo_unidad = array("" => "-") + $this->Unity_type_model->get_dropdown_list(array("nombre"), "id");
			
			$html .= '<div class="form-group multi-column">';
			$html .= '<label for="unit_field" class="col-md-2">'.lang('unit_type').'</label>';
			$html .= '<div class="col-md-5">';
			$html .= form_dropdown("unit_field", $tipo_unidad, $seleccionada, "id='unit_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			
			$html .= '<div id="symbol_group">';
			$html .= '<div class="col-md-5">';
			$html .= form_dropdown("unit_symbol", array("" => "-"), "", "id='unit_symbol' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
			$html .= '</div>';
					
		$html .= '</div>';
		
		}
		
		if($id == "Selección desde Mantenedora"){
			
			$html = '';
			$array_mantenedoras = array();
			$array_mantenedoras[""] = "-";
			
			$mantenedoras = $this->Forms_model->get_details(
				array(
					"id_tipo_formulario" => 2, 
					"id_cliente" => $client_id, 
					"id_proyecto" => $project_id
				)
			)->result();
			
			foreach($mantenedoras as $mantenedora){
				$array_mantenedoras[$mantenedora->id] = $mantenedora->nombre;
			}
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('feeder_table').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("feeder_table", $array_mantenedoras, "", "id='feeder_table' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA SELECT LABEL
			/*$html .= '<div id="mantenedora_group">';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('label').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("labels_field", array("" => "-"), "", "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
			// FILA SELECT VALUE
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('value').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("values_field", array("" => "-"), "", "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';*/
			
			$html .= '<div id="mantenedora_group">';
			$html .= '<div class="form-group">';
				$html .= '<label for="default_value_field" class="col-md-2">'.lang('value').'</label>';
				$html .= '<div class="col-md-10">';
				$html .= form_dropdown("values_field", array("" => "-"), "", "id='default_value_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			
			
		}
		
		echo $html;

    }
	
	function get_fields_of_feeder() {

        $id_mantenedora = $this->input->post('id_feeder');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_campos = array();
		if($id_mantenedora){
			$campos = $this->Field_rel_form_model->get_fields_details_related_to_form($id_mantenedora)->result_array();
			foreach ($campos as $row){
				$array_campos[$row['id']] = $row['nombre'];
			}
		}
		
		$html = '';
		
		/*// FILA SELECT LABEL
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('label').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("labels_field", array("" => "-") + $array_campos, "", "id='labels_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		// FILA SELECT VALUE
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('value').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("values_field", array("" => "-") + $array_campos, "", "id='values_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		*/
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-2">'.lang('value').'</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("values_field", array("" => "-") + $array_campos, "", "id='values_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	/* construye el campo y retorna el html */
	function get_preview_field($field_id){
		$html = "";
		
		if($field_id){
            $options = array("id" => $field_id);
            $field_info = $this->Fields_model->get_details($options)->row();
            if($field_info){
				
				
				if($field_info->tipo_campo == "Input text"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;					
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "default_value_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						//"placeholder" => lang('default_value_field'),
						"autofocus" => true,
						//"data-rule-required" => true,
						//"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Texto Largo"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;					
					$html .= '<div class="col-md-9">';
					$html .= form_textarea(array(
						"id" => "default_value_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						//"placeholder" => lang('default_value_field'),
						"style" => "height:150px;",
						"autocomplete" => "off",
						"maxlength" => 1000,
						$disabled => ""
					));
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Número"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "default_value_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						//"placeholder" => lang('default_value_field'),
						"autofocus" => true,
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';
					
				}
				
				if($field_info->tipo_campo == "Fecha"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "default_date_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						//"placeholder" => "YYYY-MM-DD",
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';
					
				}
				
				if($field_info->tipo_campo == "Periodo"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;		
					$date_default = "";
					if($field_info->default_value){
						$date_default = json_decode($field_info->default_value);
					}
					
					$date_name = "";
					if($field_info->html_name){
						$date_name = json_decode($field_info->html_name);
					}
					
					
					$html .= '<div class="col-md-4">';
					$html .= form_input(array(
						"id" => "default_date_field1",
						"name" => $date_name->start_name,
						"value" => $date_default->start_date,
						"class" => "form-control",
						//"placeholder" => "YYYY-MM-DD",
						"autocomplete" => "off",
						$disabled => ""						
					));
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= form_input(array(
						"id" => "default_date_field2",
						"name" => $date_name->end_name,
						"value" => $date_default->end_date,
						"class" => "form-control",
						//"placeholder" => "YYYY-MM-DD",
						"data-rule-greaterThanOrEqual" => "#default_date_field1",
						"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
						"autocomplete" => "off",
						$disabled => ""	
					));
					$html .= '</div>';
					
				}
				
				if($field_info->tipo_campo == "Selección"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
					$array_opciones = array();
					if($field_info->opciones){
						$opciones = json_decode($field_info->opciones);
						foreach($opciones as $index => $opcion){
							if($index == 0){
								$array_opciones[''] = $opcion->text;
							}else{
								$array_opciones[$opcion->value] = $opcion->value;
							}
							//$array_opciones[] = array($opcion->value => $opcion->value);
							
						}
					}
					
					$html .= '<div class="col-md-9">';
					$html .= form_dropdown($field_info->html_name, $array_opciones, $field_info->default_value, "id='default_value_field' class='select2' $disabled");
					$html .= '</div>';
				}
				
				
				if($field_info->tipo_campo == "Selección Múltiple"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
					$array_opciones = array();
					if($field_info->opciones){
						$opciones = json_decode($field_info->opciones);
						foreach($opciones as $index => $opcion){
							if($index == 0){
								$array_opciones[] = $opcion->text;
							}else{
								$array_opciones[$opcion->value] = $opcion->value;
							}
							//$array_opciones[] = array($opcion->value => $opcion->value);
							
						}
					}
					
					$html .= '<div class="col-md-9">';
					$html .= form_multiselect($field_info->html_name, $array_opciones, json_decode($field_info->default_value), "id='default_value_field' class='select2' $disabled");
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Rut"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "default_value_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control rut",
						//"placeholder" => lang('default_value_field'),
						"autofocus" => true,
						//"data-rule-required" => true,
						//"data-msg-required" => lang("field_required"),
						"data-rule-minlength" => 6,
						"data-msg-minlength" => lang("enter_minimum_6_characters"),
						"data-rule-maxlength" => 13,
						"data-msg-maxlength" => lang("enter_maximum_13_characters"),
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';
					
				}
				
				if($field_info->tipo_campo == "Radio Buttons"){
					
					$array_opciones = array();
					if($field_info->opciones){
						
						$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
						$opciones = json_decode($field_info->opciones);
						
						foreach($opciones as $index => $opcion){
							$array_opciones[] = array($opcion->value => $opcion->value);
							if($index == 0){
								$html .= '<div class="col-md-9">';
							}else{
								$html .= '<div class="col-md-9 col-md-offset-3">';
							}
							
							if($field_info->default_value == $opcion->value){
								$checked = "checked";
							} else {
								$checked = null;
							}

							$html .= form_radio(array(
								"id" => $field_info->html_name,
								"name" => $field_info->html_name,
								"value" => $opcion->value,
								"class" => "toggle_specific",
								$disabled => "",
								"checked" => $checked,
							), $opcion->value, ($model_info->share_with === "") ? true : false);
							$html .= '<label for="'.$field_info->html_name.'">'.$opcion->value.'</label>';
							$html .= '</div>';
						}
					}
	
				}
				
				if($field_info->tipo_campo == "Archivo"){
					
					$html .= '<div class="col-md-9">';
					$html .= $this->load->view("includes/multi_file_uploader", array(
						"upload_url" =>get_uri("fields/upload_file"),
						"validation_url" =>get_uri("fields/validate_project_file"),
					),
					true);
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Texto Fijo"){

					$html .= '<div class="col-md-9">';
					
					/*
						$html .= form_textarea(array(
							"id" => "default_value_field_rich",
							"name" => $field_info->html_name,
							"value" => $field_info->default_value,
							//"placeholder" => lang('default_value_field'),
							"class" => "form-control",
							"autocomplete" => "off",
						));
					*/
					$html .= $field_info->default_value;
					
					$html .= '</div>';
					
				}
				
				if($field_info->tipo_campo == "Divisor"){
					$html .= '<div class="col-md-9">';
					$html .= $field_info->default_value;
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Correo"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "default_value_field",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						//"placeholder" => lang('email'),
						"autofocus" => true,
						"autocomplete" => "off",
						"data-rule-email" => true,
						"data-msg-email" => lang("enter_valid_email"),
						//"data-rule-required" => true,
						//"data-msg-required" => lang("field_required"),
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';
				}
				
				if($field_info->tipo_campo == "Hora"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;					
					$html .= '<div class="col-md-9">';
					$html .= form_input(array(
						"id" => "time_preview",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						//"placeholder" => lang('default_value_field'),
						"class" => "form-control",
						"autofocus" => true,
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';				
				}
				
				if($field_info->tipo_campo == "Unidad"){
					
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
					$seleccionada = "";
					$symbol_seleccionado = "";
					if($field_info->opciones){
						$opciones = json_decode($field_info->opciones);
						$seleccionada = $opciones[0]->id_tipo_unidad;
						$symbol_seleccionado = $opciones[0]->id_unidad;
					}
					
					$unidad = $this->Unity_model->get_one_where(array("id" => $symbol_seleccionado));
					//var_dump($unidad);
					
					$html .= '<div class="form-group">';
						//$html .= '<label for="default__field" class="col-md-2">'.lang('default_value_field').'</label>';
						$html .= '<div class="col-md-5">';
						$html .= form_input(array(
							"id" => "default_value",
							"name" => $field_info->html_name,
							"value" => $field_info->default_value,
							"class" => "form-control",
							"placeholder" => lang('default_value_field'),
							"autofocus" => true,
							//"data-rule-required" => true,
							//"data-msg-required" => lang("field_required"),
							"autocomplete" => "off",
							$disabled => ""
						));
						$html .= '</div>';					
						
					// DROPDOWN UNIDAD
						$html .= '<div class="col-md-4">';
						$html .=  $unidad->nombre;
						$html .= '</div>';
									
					// FILA POR DEFECTO
					
					$html .= '</div>';
		
				}
				
				if($field_info->tipo_campo == "Selección desde Mantenedora"){
					$disabled = ($field_info->habilitado == 1) ? "disabled" : null;	
					
					$valores = json_decode($field_info->default_value);
					$id_mantenedora = $valores->mantenedora;
					$id_campo_label = $valores->field_label;
					$id_campo_value = $valores->field_value;
					
					$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
					
					$array_opciones = array();
					foreach($datos as $index => $row){
						$fila = json_decode($row->datos, true);
						$label = $fila[$id_campo_label];
						$value = $fila[$id_campo_value];
						$array_opciones[$value] = $label;
					}
					
					$html .= '<div class="col-md-9">';
					$html .= form_dropdown($field_info->html_name, array("" => "-") + $array_opciones, "", "id='default_value_field' class='select2' $disabled");
					$html .= '</div>';
					
				}
				
				
			}
		}
		return $html;
		
	}
	
	/* Retorna un select HTML con los simbolos de una unidad de medida según la unidad que reciba como parametro */
	function get_symbol_of_unit(){
		
		$unidad = $this->input->post("unidad");
		$select_values;
		$tosting = array("" =>"-")+ $this->Unity_model->get_units_of_unit_type2($unidad)->result();
		$array_tost = array();
        if($tosting){
            foreach($tosting as $index => $key){
                $array_tost[$key->id] = $key->nombre;
            }
        }
		//var_dump($tosting);
		
		
		
		$html = '';
		$html .= '<div class="col-md-5">';
		$html .= form_dropdown("unit_symbol", array("" =>"-")+$array_tost, "", "id='unit_symbol' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		
		echo $html;
		/* var_dump($select_values);
		foreach($select_values as $sv){
			var_dump($sv);
		}
		exit();
 		*/
		
	}
	
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	}

    /* add-remove start mark from client */

    function add_remove_star($client_id, $type = "add") {
        if ($client_id) {
            $view_data["client_id"] = $client_id;

            if ($type === "add") {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "add");
                $this->load->view('clients/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                $this->load->view('clients/star/not_starred', $view_data);
            }
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id)->result();
        $this->load->view('clients/star/clients_list', $view_data);
    }

    /* load projects tab  */

    function projects($client_id) {
        $this->access_only_allowed_members();

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = $client_id;
        $this->load->view("clients/projects/index", $view_data);
    }

    /* load payments tab  */

    function payments($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/payments/index", $view_data);
        }
    }

    /* load tickets tab  */

    function tickets($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tickets", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/tickets/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/invoices/index", $view_data);
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id) {

        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->result();
        $view_data['client_id'] = $client_id;
        $this->load->view("clients/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->input->post('id'));
        $client_id = $this->input->post('client_id') ? $this->input->post('client_id') : $view_data['model_info']->client_id;

        $this->access_only_allowed_members();

        $view_data['client_id'] = $client_id;
        $this->load->view('clients/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        validate_submitted_data(array(
            "id" => "numeric",
            "field_id" => "required|numeric"
        ));

        $field_id = $this->input->post('field_id');
        $this->access_only_allowed_members();


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $field_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "client_id" => $field_id,
                        "file_name" => $new_file_name,
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("clients/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("clients/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

        $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->client_id) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = false;

            $view_data["file_url"] = get_file_uri(get_general_file_path("client", $file_info->client_id) . $file_info->file_name);;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            $this->load->view("clients/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }
        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->file_name)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file($file_type = "") {
		
		$id_campo = $this->input->post('cid');
		
		if($file_type == "retirement_file"){
			upload_file_to_temp('file', $options = array("retirement_file" => TRUE));
		}elseif($file_type == "reception_file") {
			upload_file_to_temp('file', $options = array("reception_file" => TRUE));
		}elseif($id_campo){
			upload_file_to_temp("file", array("id_campo" => $id_campo));
		}else{
			upload_file_to_temp();
		}
        
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            delete_file_from_directory(get_general_file_path("client", $info->client_id) . $info->file_name);

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['client_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = $tab;
        if ($view_data['user_info']->user_type === "client") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            $this->template->rander("clients/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->load->view("users/account_settings", $view_data);
    }

    /* load contacts tab  */

    function contacts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->input->post('client_id');

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();
        $this->load->view('clients/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members_or_client_contact($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $this->load->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->input->post('contact_id');
        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $user_data = array(
            "first_name" => $this->input->post('first_name'),
            "last_name" => $this->input->post('last_name'),
            "phone" => $this->input->post('phone'),
            "skype" => $this->input->post('skype'),
            "job_title" => $this->input->post('job_title'),
            "gender" => $this->input->post('gender'),
            "note" => $this->input->post('note')
        );

        validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric"
        ));


        if (!$contact_id) {
            //inserting new contact. client_id is required

            validate_submitted_data(array(
                "email" => "required|valid_email",
                "login_password" => "required",
            ));

            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["email"] = trim($this->input->post('email'));
            $user_data["password"] = md5($this->input->post('login_password'));
            $user_data["created_at"] = get_current_utc_time();

            //validate duplicate email address
            if ($this->Users_model->is_email_exists($user_data["email"])) {
                echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
                exit();
            }
        }

        //by default, the first contact of a client is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->input->post('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }


        $save_id = $this->Users_model->save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->save($user_data, $primary_contact);
            }

            //send login details to user only for first time. when creating  a new contact
            if (!$contact_id && $this->input->post('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info");

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->input->post('login_password');
                $parser_data["DASHBOARD_URL"] = base_url();

                $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
                send_app_mail($this->input->post('email'), $email_template->subject, $message);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->input->post('facebook'),
            "twitter" => $this->input->post('twitter'),
            "linkedin" => $this->input->post('linkedin'),
            "googleplus" => $this->input->post('googleplus'),
            "digg" => $this->input->post('digg'),
            "youtube" => $this->input->post('youtube'),
            "pinterest" => $this->input->post('pinterest'),
            "instagram" => $this->input->post('instagram'),
            "github" => $this->input->post('github'),
            "tumblr" => $this->input->post('tumblr'),
            "vine" => $this->input->post('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $this->Social_links_model->save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => lang('record_updated')));
    }

    //save account settings of a client contact (user)
    function save_account_settings($user_id) {
        $this->access_only_allowed_members_or_contact_personally($user_id);

        validate_submitted_data(array(
            "email" => "required|valid_email"
        ));

        if ($this->Users_model->is_email_exists($this->input->post('email'), $user_id)) {
            echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $this->input->post('email')
        );

        //don't reset password if user doesn't entered any password
        if ($this->input->post('password')) {
            $account_data['password'] = md5($this->input->post('password'));
        }

        //only admin can disable other users login permission
        if ($this->login_user->is_admin) {
            $account_data['disable_login'] = $this->input->post('disable_login');
        }


        if ($this->Users_model->save($account_data, $user_id)) {
            echo json_encode(array("success" => true, 'message' => lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($user_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->input->post("profile_image"));

        if ($profile_image) {
            $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image);
            $image_data = array("image" => $profile_image);
            $this->Users_model->save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name);
                $image_data = array("image" => $profile_image);
                $this->Users_model->save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->input->post('id');

        if ($this->input->post('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        $this->access_only_allowed_members_or_client_contact($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "client", "client_id" => $client_id, "custom_fields" => $custom_fields);
        $list_data = $this->Users_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->row();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='label-info label'>" . lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("clients/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "client") {
            $contact_link = $full_name; //don't show clickable link to client
        }


        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("clients/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* open invitation modal */

    function invitation_modal() {


        validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members_or_client_contact($client_id);

        $view_data["client_info"] = $this->Clients_model->get_one($client_id);
        $this->load->view('clients/contacts/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation() {

        $client_id = $this->input->post('client_id');
        $email = trim($this->input->post('email'));

        validate_submitted_data(array(
            "client_id" => "required|numeric",
            "email" => "required|valid_email|trim"
        ));

        $this->access_only_allowed_members_or_client_contact($client_id);

        $email_template = $this->Email_templates_model->get_final_template("client_contact_invitation");

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();

        //make the invitation url with 24hrs validity
        $key = encode_id($this->encrypt->encode('client|' . $email . '|' . (time() + (24 * 60 * 60)) . '|' . $client_id), "signup");
        $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $key);

        //send invitation email
        $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
        if (send_app_mail($email, $email_template->subject, $message)) {
            echo json_encode(array('success' => true, 'message' => lang("invitation_sent")));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
        }
    }

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            $this->template->rander("clients/contacts/users", $view_data);
        }
    }

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */