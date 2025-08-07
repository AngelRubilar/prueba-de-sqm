<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Communities_stakeholders extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9;
		$this->id_submodulo_cliente = 11;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    function index() {
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
		$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$id_stakeholder_matrix_config = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $this->session->project_context, "deleted" => 0))->id;
		
		if($id_stakeholder_matrix_config){
			$columnas_campos = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($id_stakeholder_matrix_config)->result_array();
			$json_string_campos = '';
			foreach($columnas_campos as $columna){
				/*if($columna["id_tipo_campo"] == 11){ continue; }
				if($columna["id_tipo_campo"] == 2){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class" : "text-center"}';
				} else {
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '"}';
				}*/
				
				if($columna["id_tipo_campo"] == 1){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 2){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
				}else if($columna["id_tipo_campo"] == 3){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 4){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center", type: "extract-date"}';
				}else if($columna["id_tipo_campo"] == 5){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center no_breakline"}';
				}else if($columna["id_tipo_campo"] >= 6 && $columna["id_tipo_campo"] <= 9){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 10){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center option"}';
				}else if(($columna["id_tipo_campo"] == 11) || ($columna["id_tipo_campo"] == 12)){
					continue;
				}else if($columna["id_tipo_campo"] == 13 || $columna["id_tipo_campo"] == 14){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 15){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 16){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else{
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '"}';
				}
				
			}
			
			$view_data["columnas_campos"] = $json_string_campos;	
			$view_data["id_stakeholder_matrix_config"] = $id_stakeholder_matrix_config;
		}
		
		// Filtro Tipo de Stakeholder
		$array_tipos_organizaciones[] = array("id" => "", "text" => "- ".lang("type_of_stakeholder")." -");	
		$tipos_organizaciones = $this->Types_of_organization_model->get_all_where(array("deleted" => 0))->result_array();
		foreach($tipos_organizaciones as $tipo_organizacion){
			$array_tipos_organizaciones[] = array("id" => $tipo_organizacion['id'], "text" => lang($tipo_organizacion['nombre']));
		}
		$view_data["tipos_organizaciones_dropdown"] = json_encode($array_tipos_organizaciones);

		$this->template->rander("communities_stakeholders/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        //$this->access_only_allowed_members();
        $value_stakeholder_id = $this->input->post('id');
		$id_stakeholder_matrix_config = $this->input->post('id_stakeholder_matrix_config');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
        $view_data["view"] = $this->input->post('view');       
		
		$dropdown_tipos_organizaciones = array("" => "-");
		$tipos_organizaciones = $this->Types_of_organization_model->get_all_where(array("deleted" => 0))->result_array();
		foreach($tipos_organizaciones as $tipo_organizacion){
			$dropdown_tipos_organizaciones[$tipo_organizacion['id']] = lang($tipo_organizacion['nombre']);
		}
		
		$view_data["dropdown_tipos_organizaciones"] = $dropdown_tipos_organizaciones;
		
		$campos_stakeholder_matrix = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($id_stakeholder_matrix_config)->result_array();
		
		$view_data["id_stakeholder_matrix_config"] = $id_stakeholder_matrix_config;
		$view_data["campos_stakeholder_matrix"] = $campos_stakeholder_matrix;
		$view_data["Communities_stakeholders_controller"] = $this;
		
		if($value_stakeholder_id){ //edit
			$view_data['model_info'] = $this->Values_stakeholders_model->get_one($value_stakeholder_id);		
		} 
		
        $this->load->view('communities_stakeholders/modal_form', $view_data);
    }
	
	
	function save() {

        $value_stakeholder_id = $this->input->post('id');
		$id_stakeholder_matrix_config = $this->input->post('id_stakeholder_matrix_config');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		$columnas = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($id_stakeholder_matrix_config)->result();
		$array_datos = array();
		
		foreach($columnas as $columna){
			
			// VERIFICO SI EL CAMPO EN LOOP VIENE DESHABILITADO
			$deshabilitado = $columna->habilitado;
			$default_value = $columna->default_value;
			
			if($columna->id_tipo_campo == 5){

				if($deshabilitado){
					$array_datos[$columna->id_campo] = json_decode($default_value, true);
				}else{
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id_campo] = array(
						"start_date" => $this->input->post($start_name),
						"end_date" => $this->input->post($end_name)
					);
				}
				
			} else if($columna->id_tipo_campo == 11){
				//CAMPO TIPO TEXTO FIJO NO SE GUARDA
			} else {

				if($deshabilitado){
					$array_datos[$columna->id_campo] = $default_value;
				}else{
					$array_datos[$columna->id_campo] =  $this->input->post($columna->html_name);
				}
			}
			//$array_datos_campos[$columna->id_campo] = $this->input->post($columna->html_name);
		}
		
		$json_datos_campos = json_encode($array_datos);
		
		$data_value_stakeholder = array(
			"id_stakeholder_matrix_config" => $id_stakeholder_matrix_config,
			"nombre" => $this->input->post('name'),
			//"nombres" => $this->input->post('name'),
			//"apellidos" => $this->input->post('last_name'),
			"rut" => $this->input->post('rut'),
			"id_tipo_organizacion" => $this->input->post('type_of_organization'),
			"localidad" => $this->input->post('locality'),
			"nombres_contacto" => $this->input->post('contact_name'),
			"apellidos_contacto" => $this->input->post('contact_last_name'),
			"telefono_contacto" => $this->input->post('contact_phone'),
			"correo_contacto" => $this->input->post('contact_email'),
			"direccion_contacto" => $this->input->post('contact_address'),
			"datos_campos" => $json_datos_campos
		);

		if($value_stakeholder_id){ //edit

			$data_value_stakeholder["modified_by"] = $this->login_user->id;
			$data_value_stakeholder["modified"] = get_current_utc_time();
			$save_id = $this->Values_stakeholders_model->save($data_value_stakeholder, $value_stakeholder_id);
			
		} else { //insert
			
			$data_value_stakeholder["created_by"] = $this->login_user->id;
			$data_value_stakeholder["created"] = get_current_utc_time();
			$save_id = $this->Values_stakeholders_model->save($data_value_stakeholder);

		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id, $columnas, $id_stakeholder_matrix_config), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	

	function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		//NO SE PUEDE ELIMINAR UN STAKEHOLDER SI ESTÁ SIENDO USADO EN UN ACUERDO

		$stakeholder_en_acuerdo = FALSE;
		$acuerdos = $this->Values_agreements_model->get_all()->result_array();
		
		foreach($acuerdos as $acuerdo){
			$array_stakeholders = json_decode($acuerdo["stakeholders"]);
			foreach($array_stakeholders as $id_stakeholder){
				if($id == $id_stakeholder){
					$stakeholder_en_acuerdo = TRUE;
					break;
				}
			}
			if($stakeholder_en_acuerdo){ break; }
		}
		
		if($stakeholder_en_acuerdo){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_stakeholder')));
			exit();
		}		
		
        if ($this->input->post('undo')) {
            if ($this->Values_stakeholders_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Values_stakeholders_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple(){
		
		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->profile_access($id_user, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->Values_stakeholders_model->get_one($id);
				if($id_user != $row->created_by){
					$eliminar = FALSE;
					break;
				}
			}
			if($puede_eliminar == 3){ // Ninguno
				$eliminar = FALSE;
				break;
			}
		}
		
		if(!$eliminar){
			echo json_encode(array("success" => false, 'message' => lang("record_cannot_be_deleted_by_profile")));
			exit();
		}
		
		//NO SE PUEDE ELIMINAR UN STAKEHOLDER SI ESTÁ SIENDO USADO EN UN ACUERDO
		$stakeholder_en_acuerdo = FALSE;
		$acuerdos = $this->Values_agreements_model->get_all()->result_array();
		
		foreach($data_ids as $id){
		
			foreach($acuerdos as $acuerdo){
				$array_stakeholders = json_decode($acuerdo["stakeholders"]);
				foreach($array_stakeholders as $id_stakeholder){
					if($id == $id_stakeholder){
						$stakeholder_en_acuerdo = TRUE;
						break;
					}
				}
				if($stakeholder_en_acuerdo){ break; }
			}
		
		}

		if($stakeholder_en_acuerdo){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_stakeholders')));
			exit();
		}	
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->Values_stakeholders_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			echo json_encode(array("success" => true, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
	}
	
	function list_data($id_stakeholder_matrix_config = 0) {
		
		// Filtro AppTable
		$id_tipo_organizacion = $this->input->post("id_tipo_organizacion");
		
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
        $list_data = $this->Values_stakeholders_model->get_details(array(
			"id_stakeholder_matrix_config" => $id_stakeholder_matrix_config,
			"id_tipo_organizacion" => $id_tipo_organizacion 
		))->result();
		
		$columnas = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($id_stakeholder_matrix_config)->result();
		
        $result = array();
        foreach ($list_data as $data) {
          
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row($data, $columnas, $id_stakeholder_matrix_config);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row($data, $columnas, $id_stakeholder_matrix_config);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();	
			}

        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id, $columnas, $id_stakeholder_matrix_config) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Values_stakeholders_model->get_details($options)->row();
        return $this->_make_row($data, $columnas, $id_stakeholder_matrix_config);
    }
	
	private function _make_row($data, $columnas, $id_stakeholder_matrix_config) {
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
			
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		//$row_data[] = $data->nombres . " " . $data->apellidos;
		$row_data[] = $data->nombre;
		$row_data[] = $data->rut;
		
		$tipo_organizacion = $this->Types_of_organization_model->get_one($data->id_tipo_organizacion);
		
		$row_data[] = lang($tipo_organizacion->nombre);
		$row_data[] = ($data->localidad) ? $data->localidad : "-";

		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;
				// Si existe el campo dentro de los valores del registro

				if(isset($arreglo_fila[$columna->id_campo])){
					if($columna->id_tipo_campo == 2){ // TEXT AREA
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					} elseif($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? get_date_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id_campo] == "") ? '-' : $arreglo_fila[$columna->id_campo];
					}
				} else {
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}
					$valor_campo = '-';
				}
								
				$row_data[] = $valor_campo;
				
			}
			
		}	
		
		$row_data[] = $data->nombres_contacto . " " . $data->apellidos_contacto;
		$row_data[] = ($data->telefono_contacto) ? $data->telefono_contacto : "-";
		$row_data[] = ($data->correo_contacto) ? $data->correo_contacto : "-";
		$row_data[] = ($data->direccion_contacto) ? $data->direccion_contacto : "-";	
		$row_data[] = get_date_format($data->created, $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($data->modified, $id_proyecto) : "-";
		
		
		$view = modal_anchor(get_uri("communities_stakeholders/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_stakeholder'), "data-post-id" => $data->id, "data-post-id_stakeholder_matrix_config" => $id_stakeholder_matrix_config));
		$edit = modal_anchor(get_uri("communities_stakeholders/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_stakeholder'), "data-post-id" => $data->id, "data-post-id_stakeholder_matrix_config" => $id_stakeholder_matrix_config));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_stakeholder'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("communities_stakeholders/delete"), "data-action" => "delete-confirmation"));
		
		//Validaciones de Perfil
		if($puede_editar == 1 && $puede_eliminar ==1){
			$row_data[] = $view.$edit.$delete;		
		} else if($puede_editar == 1 && $puede_eliminar == 2){
			$row_data[] = $view.$edit;
			if($id_usuario == $data->created_by){
				$botones = array_pop($row_data);
				$botones = $botones.$delete;
				$row_data[] = $botones;
			}
		} else if($puede_editar == 1 && $puede_eliminar == 3){
			$row_data[] = $view.$edit;
		} else if($puede_editar == 2 && $puede_eliminar == 1){
			$row_data[] = $view;
			$botones = array_pop($row_data);
			if($id_usuario == $data->created_by){
				$botones = $botones.$edit.$delete;
			} else {
				$botones = $botones.$delete;
			}
			$row_data[] = $botones;
		} else if($puede_editar == 2 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 2 && $puede_eliminar == 3){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 1){
			$row_data[] = $view.$delete;
		} else if($puede_editar == 3 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 3){
			$row_data[] = $view;
		}
		
        return $row_data;
    }
	
	function view($value_stakeholder_id = 0) {
		
        if ($value_stakeholder_id) {
           
		    $options = array("id" => $value_stakeholder_id);
            $value_stakeholder_info = $this->Values_stakeholders_model->get_details($options)->row();
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$id_proyecto = $proyecto->id;
			
			$campos_stakeholder_matrix = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($value_stakeholder_info->id_stakeholder_matrix_config)->result_array();
			
            if ($value_stakeholder_info) {
				
				// Change created_by and modified_by from IDs to Names
				$created_by = $this->Users_model->get_one($value_stakeholder_info->created_by);
				$value_stakeholder_info->created_by = $value_stakeholder_info->created_by ? $created_by->first_name . " " . $created_by->last_name : "-";
				$modified_by = $this->Users_model->get_one($value_stakeholder_info->modified_by);
				$value_stakeholder_info->modified_by = $value_stakeholder_info->modified_by ? $modified_by->first_name . " " . $modified_by->last_name : "-";
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";			
				$view_data['model_info'] = $value_stakeholder_info;
				$view_data['campos_stakeholder_matrix'] = $campos_stakeholder_matrix;
				$view_data["Communities_stakeholders_controller"] = $this;
				$view_data['id_proyecto'] = $id_proyecto;
				
				$tipo_organizacion = $this->Types_of_organization_model->get_one($value_stakeholder_info->id_tipo_organizacion);
				$view_data["tipo_organizacion"] = lang($tipo_organizacion->nombre);
				
				$this->load->view('communities_stakeholders/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	
	function get_field($id_campo, $id_elemento, $preview){
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		$array_opciones = json_decode($opciones, true);
		$options = array();
		foreach($array_opciones as $opcion){
			$options[$opcion['value']] = $opcion['text'];
		}
		
		$obligatorio = $datos_campo->obligatorio;
		$habilitado = $datos_campo->habilitado;

		if($id_elemento){
			$row_elemento = $this->Values_stakeholders_model->get_details(array("id" => $id_elemento))->result();
			$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
			$default_value = $decoded_default[$id_campo];
			
			if($id_tipo_campo == 5){
				$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
				$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			}
			if($id_tipo_campo == 11){
				$default_value = $datos_campo->default_value;
			}
			if($id_tipo_campo == 7){
				$default_value_multiple = (array)$default_value;
			}
			
			if($id_tipo_campo == 16){
					
				$datos_mantenedora = json_decode($datos_campo->default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}
			
			}
	
			
		}else{
			if($id_tipo_campo == 5){
				if($default_value){
					$default_value1 = json_decode($default_value)->start_date?json_decode($default_value)->start_date:"";
					$default_value2 = json_decode($default_value)->end_date?json_decode($default_value)->end_date:"";
				}else{
					$default_value1 = "";
					$default_value2 = "";
				}
			}else if($id_tipo_campo == 7){
				$default_value_multiple = array();
				//var_dump(json_decode($default_value, true));exit();
				foreach(json_decode($default_value, true) as $value){
					$default_value_multiple[] = $value;
				}
				
			}else{
				
			}
			
			if($id_tipo_campo == 16){
				
				$datos_mantenedora = json_decode($default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}

			}
			
		}
		
		//Input text
		if($id_tipo_campo == 1){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "255",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"style" => "height:150px;",
				"autocomplete" => "off",
				"maxlength" => "2000",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_textarea($datos_campo);
		}
		
		//Número
		if($id_tipo_campo == 3){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer")
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			
			$name = json_decode($name, true);
			$name1 = $name['start_name'];
			$name2 = $name['end_name'];
			
			$datos_campo1 = array(
				"id" => $name1,
				"name" => $name1,
				"value" => $default_value1,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			
			$datos_campo2 = array(
				"id" => $name2,
				"name" => $name2,
				"value" => $default_value2,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-greaterThanOrEqual" => "#".$name1,
				"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo1['data-rule-required'] = true;
				$datos_campo1['data-msg-required'] = lang("field_required");
				$datos_campo2['data-rule-required'] = true;
				$datos_campo2['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo1['disabled'] = true;
				$datos_campo2['disabled'] = true;
			}
			
			
			$html = '<div class="col-md-6">';
			$html .= form_input($datos_campo1);
			$html .= '</div>';
			$html .= '<div class="col-md-6">';
			$html .= form_input($datos_campo2);
			$html .= '</div>';
		}
		
		//Selección
		if($id_tipo_campo == 6){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, $options, $default_value, "id='$name' class='select2 validate-hidden' $extra");
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_multiselect($name."[]", $options, $default_value_multiple, "id='$name' class='select2 validate-hidden' $extra multiple");

		}
		
		//Rut
		if($id_tipo_campo == 8){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-minlength" => 6,
				"data-msg-minlength" => lang("enter_minimum_6_characters"),
				"data-rule-maxlength" => 13,
				"data-msg-maxlength" => lang("enter_maximum_13_characters"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			
			$html = '';
			$cont = 0;
			foreach($options as $value => $label){
				$cont++;
				
				$html .= '<div class="col-md-6">';
				$html .= $label;
				$html .= '</div>';
				
				$html .= '<div class="col-md-6">';
				$datos_campo = array(
					"id" => $name.'_'.$cont,
					"name" => $name,
					"value" => $value,
					"class" => "toggle_specific",
					//$disabled => "",
				);
				if($value == $default_value){
					$datos_campo["checked"] = true;
				}
				if($obligatorio){
					$datos_campo['data-rule-required'] = true;
					$datos_campo['data-msg-required'] = lang("field_required");
				}
				if($habilitado){
					$datos_campo['disabled'] = true;
				}
				$html .= form_radio($datos_campo);
				$html .= '</div>';
				
			}
			
			
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value){
				
				if($preview){
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
					
				} else {
					
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-confirmation"));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				}
				
				
			}else{
				
				$html = $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("fields/upload_file"),
					"validation_url" =>get_uri("fields/validate_file"),
					"html_name" => $name,
					"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"id_campo" => $id_campo,
					//"preimagen" => $default_value
				),
				true);
			}
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "2000",
				"data-rule-email" => true,
				"data-msg-email" => lang("enter_valid_email"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control timepicker",
				//"placeholder" => "YYYY-MM-DD",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			
			//$simbolo = $array_opciones[0]["symbol"];
			$id_simbolo = $array_opciones[0]["id_unidad"];
			$simbolo = $this->Unity_model->get_one($id_simbolo);
			
			$html = '';
			$html .= '<div class="col-md-10 p0">';
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			$html .= form_input($datos_campo);
			$html .= '</div>';
			$html .= '<div class="col-md-2">';
			$html .= $simbolo->nombre;
			$html .= '</div>';
		
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			/* $datos_mantenedora = json_decode($default_value, true);
			$id_mantenedora = $datos_mantenedora['mantenedora'];
			$id_field_label = $datos_mantenedora['field_label'];
			$id_field_value = $datos_mantenedora['field_value'];
			
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			$array_opciones = array();
			foreach($datos as $index => $row){
				$fila = json_decode($row->datos, true);
				$label = $fila[$id_field_label];
				$value = $fila[$id_field_value];
				$array_opciones[$value] = $label;
			} */
			
			//var_dump($array_opciones);
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, array("" => "-") + $array_opciones, $default_value, "id='$name' class='select2 validate-hidden' $extra");
			
		}
		
		return $html;

	}	
	
	function get_field_value($id_campo, $id_elemento) {
		
		$id_stakeholder_matrix_config = $this->Values_stakeholders_model->get_one($id_elemento)->id_stakeholder_matrix_config;
		$id_proyecto = $this->Stakeholders_matrix_config_model->get_one($id_stakeholder_matrix_config)->id_proyecto;
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		$array_opciones = json_decode($opciones, true);
		$options = array();
		foreach($array_opciones as $opcion){
			$options[$opcion['value']] = $opcion['text'];
		}
		
		$row_elemento = $this->Values_stakeholders_model->get_details(array("id" => $id_elemento))->result();
		$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
		
		$default_value = $decoded_default[$id_campo];
		if($id_tipo_campo == 5){
			$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
			$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			$default_value = $default_value1.' - '.$default_value2;
		}
		if($id_tipo_campo == 11){
			$default_value = $datos_campo->default_value;
		}
		if($id_tipo_campo == 7){
			$default_value_multiple = (array)$default_value;
		}
		
		
		//Input text
		if($id_tipo_campo == 1){
			$html = $default_value;
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			$html = $default_value;
		}
		
		//Número
		if($id_tipo_campo == 3){
			$html = $default_value;
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$html = get_date_format($default_value,$id_proyecto);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			 $html = $default_value;
		}
		
		//Selección
		if($id_tipo_campo == 6){
			$html = $default_value;// es el value, no el text
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			$html = $default_value_multiple;//siempre es un arreglo, aunque tenga 1
		}
		
		//Rut
		if($id_tipo_campo == 8){
			$html = $default_value;
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			//$html = $value;// es el value, no la etiqueta
			$html = $default_value;
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value ){
				
				$html = '<div class="col-md-8">';
				$html .= $default_value;
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
			} else {
				
				$html = '<div class="col-md-8">';
				$html .= '-';
				$html .= '</div>';
			}
			
			
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			$html = $default_value;
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$html = $default_value;
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			$simbolo = $array_opciones[0]["symbol"];
			$html = $default_value?$default_value:"-".' '.$simbolo;
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			$html = $default_value;
			
		}
		
		return $html;

    }
	
	function get_excel(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$matriz_stakeholders = $this->Stakeholders_matrix_config_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		
		$id_stakeholder_matrix_config = $matriz_stakeholders->id;

		$list_data = $this->Values_stakeholders_model->get_details(array(
			"id_stakeholder_matrix_config" => $id_stakeholder_matrix_config
		))->result();
		
		$columnas = $this->Stakeholders_matrix_config_model->get_fields_of_stakeholder_matrix($id_stakeholder_matrix_config)->result();
		$result = array();
		 
		foreach($list_data as $data) {
			
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_excel($data, $columnas, $id_stakeholder_matrix_config);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_excel($data, $columnas, $id_stakeholder_matrix_config);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}

		}

		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		
		if($client_info->color_sitio){
			$color_sitio = str_replace('#', '', $client_info->color_sitio);
		} else {
			$color_sitio = "00b393";
		}
		
		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => $color_sitio)
			),
		);
		
		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}
		
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);
		
		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("stakeholder"), "id_tipo_campo" => "stakeholder");
		$nombre_columnas[] = array("nombre_columna" => lang("rut"), "id_tipo_campo" => "rut");
		$nombre_columnas[] = array("nombre_columna" => lang("type_of_organization"), "id_tipo_campo" => "type_of_organization");
		$nombre_columnas[] = array("nombre_columna" => lang("locality"), "id_tipo_campo" => "locality");
		
		foreach($columnas as $columna){
			if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
				continue;
			}
			$nombre_columnas[] = array("nombre_columna" => $columna->nombre_campo, "id_tipo_campo" => $columna->id_tipo_campo);
		}

		$nombre_columnas[] = array("nombre_columna" => lang("contact"), "id_tipo_campo" => "contact");
		$nombre_columnas[] = array("nombre_columna" => lang("contact_phone"), "id_tipo_campo" => "contact_phone");
		$nombre_columnas[] = array("nombre_columna" => lang("contact_email"), "id_tipo_campo" => "contact_email");
		$nombre_columnas[] = array("nombre_columna" => lang("contact_address"), "id_tipo_campo" => "contact_address");

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("stakeholders"))
			->setCellValue('C2', $project_info->title)
            ->setCellValue('C3', lang("date").': '.$fecha.' '.lang("at").' '.$hora);
			
		$doc->setActiveSheetIndex(0);

		// SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
		//$doc->getActiveSheet()->fromArray($nombre_columnas, NULL,"A5");
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		foreach($nombre_columnas as $index => $columna){
			$valor = (!is_array($columna)) ? $columna : $columna["nombre_columna"];
			$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row = 5, $valor);
			$col++;
		}
		
		// CARGA DE CONTENIDO A LA HOJA DE EXCEL
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$row = 6; // EMPEZANDO DE LA FILA 6 
		foreach($result as $res){

			foreach($nombre_columnas as $index_columnas => $columna){
				
				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$valor = $res[$index_columnas];
				
				if(!is_array($columna)){
					
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
					
				} else {
					
					if($columna["id_tipo_campo"] == 1){ // INPUT TEXT
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 2){ // TEXTO LARGO
					
						$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(false);
						$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(50);
						$doc->getActiveSheet()->getStyle($name_col.$row)->getAlignment()->setWrapText(true);
						
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 3){ // NÚMERO
						
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == 4){ // FECHA 
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 5){ // PERIODO
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] >= 6 && $columna["id_tipo_campo"] <= 9){ // SELECCIÓN, SELECCIÓN MÚLTIPLE, RUT, RADIO BUTTONS
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 10){ // ARCHIVO
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 11 || $columna["id_tipo_campo"] == 12){ // TEXTP FIJO, DIVISOR
						continue;
					} elseif($columna["id_tipo_campo"] == 13 || $columna["id_tipo_campo"] == 14){ // CORREO, HORA
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 15){ // UNIDAD
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);	
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == 16){ // SELECCIÓN DESDE MANTENEDORA
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "stakeholder" || $columna["id_tipo_campo"] == "rut"
					|| $columna["id_tipo_campo"] == "type_of_organization" || $columna["id_tipo_campo"] == "locality"
					|| $columna["id_tipo_campo"] == "contact" || $columna["id_tipo_campo"] == "contact_phone"
					|| $columna["id_tipo_campo"] == "contact_email" || $columna["id_tipo_campo"] == "contact_address"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} else {	
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						
					}
	
				}
				
				$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				$col++;
			}
			
			$col = 0;
			$row++;

		}
		//$doc->getActiveSheet()->fromArray($result, NULL,"A6");
		
		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');
		
		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/

		$nombre_hoja = lang("stakeholders");
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("stakeholders")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;

	}
	
	private function _make_row_excel($data, $columnas, $id_stakeholder_matrix_config) {
		
		$id_proyecto = $this->session->project_context;
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
			
		$row_data = array();
		$row_data[] = $data->nombre;
		$row_data[] = $data->rut;
		
		$tipo_organizacion = $this->Types_of_organization_model->get_one($data->id_tipo_organizacion);
		
		$row_data[] = lang($tipo_organizacion->nombre);
		$row_data[] = ($data->localidad) ? $data->localidad : "-";

		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			//var_dump($arreglo_fila);
			//exit();
			
			foreach($columnas as $columna) {
				$cont++;
				if(isset($arreglo_fila[$columna->id_campo])){
					if($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? get_date_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 10){
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? remove_file_prefix($arreglo_fila[$columna->id_campo]) : '-';
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id_campo] == "") ? '-' : $arreglo_fila[$columna->id_campo];
					}
				}else{
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}
					$valor_campo = '-';
				}
								
				$row_data[] = $valor_campo;
				
			}
			
		}	
		
		$row_data[] = $data->nombres_contacto . " " . $data->apellidos_contacto;
		$row_data[] = ($data->telefono_contacto) ? $data->telefono_contacto : "-";
		$row_data[] = ($data->correo_contacto) ? $data->correo_contacto : "-";
		$row_data[] = ($data->direccion_contacto) ? $data->direccion_contacto : "-";	

        return $row_data;
    }
	
	private function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}


}

