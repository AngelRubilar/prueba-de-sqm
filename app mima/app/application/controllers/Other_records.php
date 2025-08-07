<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Other_records extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
		
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 4;
		$this->id_submodulo_cliente = 0;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;	
		
		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}	
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();
		//$this->check_module_availability("module_ticket");
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        $this->template->rander("other_records/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form($id_record = 0) {
        //$this->access_only_allowed_members();
		
        $data_row_id = $this->input->post('id');
        /*validate_submitted_data(array(
            "id" => "numeric"
        ));*/
		
		
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		
		$view_data['id_other_record'] = $id_record;
		if($data_row_id){
			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
		}
		$view_data['Other_records_controller'] = $this;
		
        $this->load->view('other_records/records/modal_form', $view_data);
    }
	
	function modal_form_fixed_form($id_record = 0){
		
		$record_info = $this->Forms_model->get_one($id_record);
		$data_row_id = $this->input->post('id');
		
		$view_data['campos'] = $this->Fixed_fields_model->get_all_where(array(
			"codigo_formulario_fijo" => $record_info->codigo_formulario_fijo,
			"deleted" => 0
		))->result();
		
		$view_data['id_other_record'] = $id_record;
		
		if($data_row_id){
			$view_data['model_info'] = $this->Fixed_form_values_model->get_one($data_row_id);
		}
		
		/*$campo_tipos_educacion = $this->Fixed_fields_model->get_fixed_fields(
			array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"codigo_formulario_fijo" => "or_educacion_ambiental",
				"nombre_campo" => "Tipo Educación Ambiental"
			)
		)->row();
		
		$view_data['id_campo_tipo'] = $campo_tipos_educacion->id;
		$view_data['html_name_campo_tipo'] = $campo_tipos_educacion->html_name;*/
		
		$view_data['Other_records_controller'] = $this;
		$view_data['record_info'] = $record_info;
		
		$this->load->view('other_records/records/fixed_forms/modal_form', $view_data);

	}

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a client */

    function save($id_other_record) {
		
        $elemento_id = $this->input->post('id');
		$id_proyecto = $this->session->project_context;
		
        validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$id_campo_archivo_eliminar = $this->input->post('id_campo_archivo_eliminar');

		// SI EL USUARIO HA ELIMINADO ARCHIVOS DEL ELEMENTO, ELIMINAR ESTOS ARCHIVOS DEL ELEMENTO (BD) Y FÍSICAMENTE
		if($elemento_id){
			
			if($id_campo_archivo_eliminar){
				
				$elemento = $this->Form_values_model->get_one($elemento_id);
				$datos_elemento = json_decode($elemento->datos, true);
				$datos_formulario = $this->Form_rel_project_model->get_details(array("id" => $elemento->id_formulario_rel_proyecto))->result();
				$id_cliente = $datos_formulario[0]->id_cliente;
				$id_proyecto = $datos_formulario[0]->id_proyecto;
				$id_formulario = $datos_formulario[0]->id_formulario;
				
				foreach($id_campo_archivo_eliminar as $id_archivo){
				
					$filename = $datos_elemento[$id_archivo];
					$file_path = "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$elemento_id."/".$filename;
					
					$campo_archivo_obligatorio = $this->Fields_model->get_one($id_archivo)->obligatorio;
						
					if(!$campo_archivo_obligatorio){

						$datos_elemento[$id_archivo] = "";
						$datos_final = json_encode($datos_elemento);
						$save_id = $this->Form_values_model->update_where(array("datos" => $datos_final), array("id" => $elemento_id));
						
						delete_file_from_directory($file_path);
						
					} 

				}
				
			}
			
		}
		
		$array_files = array();
		$array_datos = array();
		$columnas = $this->Forms_model->get_fields_of_form($id_other_record)->result();
		$options = array("id_formulario" => $id_other_record, "id_proyecto" => $id_proyecto);
        $record_info = $this->Form_rel_project_model->get_details($options)->row();
        if($record_info){
			$id_formulario_rel_proyecto = $record_info->id;
		}
		
		$array_datos['fecha'] = $this->input->post("date");
		
		foreach($columnas as $columna){
			
			// VERIFICO SI EL CAMPO EN LOOP VIENE DESHABILITADO
			$deshabilitado = $columna->habilitado;
			$default_value = $columna->default_value;

			// cuando sea periodo
			if($columna->id_tipo_campo == 5){
				if($deshabilitado){
					$array_datos[$columna->id] = json_decode($default_value, true);
				}else{
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id] = array(
						"start_date" => $this->input->post($start_name),
						"end_date" => $this->input->post($end_name)
					);
				}
			}else if($columna->id_tipo_campo == 10){
				if($elemento_id){
					
					if(array_key_exists($columna->html_name.'_unchange', $this->input->post())){
						$array_datos[$columna->id] = $this->input->post($columna->html_name);
						//$array_files[$columna->id] = $this->input->post($columna->html_name);
					}else{
						if($this->input->post($columna->html_name)){
							$filename = uniqid("file")."-".$this->input->post($columna->html_name);
							$array_datos[$columna->id] = $filename;
							$array_files[$columna->id] = $filename;
						}else{
							$array_datos[$columna->id] = "";
						}
					}
					
				}else{
					if($this->input->post($columna->html_name)){
						$filename = uniqid("file")."-".$this->input->post($columna->html_name);
						$array_datos[$columna->id] = $filename;
						$array_files[$columna->id] = $filename;
					}else{
						$array_datos[$columna->id] = "";
					}
				}
				
			}else{
				
				if($deshabilitado){
					$array_datos[$columna->id] = $default_value;
				}else{
					$array_datos[$columna->id] =  $this->input->post($columna->html_name);
				}
			}
			
		}
		
		$json_datos = json_encode($array_datos);

        $data = array(
			"id_formulario_rel_proyecto" => $id_formulario_rel_proyecto,
            "datos" => $json_datos, 
        );
		
		if($elemento_id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
        $save_id = $this->Form_values_model->save($data, $elemento_id);
		
        if ($save_id) {
			
			// traslado los archivos subidos
			$client_id = $this->login_user->client_id;
			foreach($array_files as $id_columna => $nombre_archivo){
				
				$nombre_real_archivo = remove_file_prefix($nombre_archivo);
				$value = move_temp_file($id_columna.'_'.$nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_other_record."/elemento_".$save_id."/", "", "", $nombre_archivo);
			}
			
			$registros = $this->Other_records_model->get_values_of_record($id_other_record)->result();
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? time_date_zone_format($record_info->modified, $id_proyecto) : time_date_zone_format(max($arrayFechas), $id_proyecto);
			$num_registros = count($registros);
			
			$columnas = $this->Forms_model->get_fields_of_form($id_other_record)->result();
            
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $client_id,
				"id_project" => $id_proyecto,
				"id_user" => $this->session->user_id,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => ($elemento_id) ? "edit" : "add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id, $columnas, $id_other_record), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete($id_record) {
        //$this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$id_user = $this->session->user_id;
		
		$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
				
        if ($this->input->post('undo')) {
            if ($this->Form_values_model->delete($id, true)) {
				
				$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Form_values_model->delete($id)) {
				
				$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
				$num_registros = count($registros);
				
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "delete",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple($id_record) {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$id_user = $this->session->user_id;
		$data_ids = json_decode($this->input->post('data_ids'));
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->profile_access($id_user, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->Form_values_model->get_one($id);
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
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->Form_values_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			// Guardar histórico notificaciones
			foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "delete",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			}
			
			$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
			$num_registros = count($registros);

			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	

    }
	
	function delete_element_fixed_form($id_record) {
        //$this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$id_user = $this->session->user_id;
        $id = $this->input->post('id');
		
		$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
		
        if ($this->input->post('undo')) {
            if ($this->Fixed_form_values_model->delete($id, true)) {
				
				$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data_fixed_form($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Fixed_form_values_model->delete($id)) {
				
				// Guardar histórico notificaciones
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "delete_fixed_or",
					"id_element" => $id
				);
				ayn_save_historical_notification($options);
				
				
				$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple_fixed_form($id_record) {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$id_user = $this->session->user_id;		
		$data_ids = json_decode($this->input->post('data_ids'));
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->profile_access($id_user, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->Fixed_form_values_model->get_one($id);
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
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->Fixed_form_values_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			// Guardar histórico notificaciones
			foreach($data_ids as $index => $id){
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "delete_fixed_or",
					"id_element" => $id,
					"massive" => (count($data_ids) > 1) ? 1 : 0,
					"is_email_sended" => ($index !== count($data_ids) -1) ? 1 : 0
				);
				ayn_save_historical_notification($options);
			}
			
			$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
			$num_registros = count($registros);

			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}  
		
    }
	

    /* list of clients, prepared for datatable  */

    function list_data($id_record = 0) {

        //$this->access_only_allowed_members();
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
        $list_data = $this->Other_records_model->get_values_of_record($id_record)->result();
		$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();
        $result = array();
        foreach ($list_data as $data) {			
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row($data, $columnas, $id_record);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row($data, $columnas, $id_record);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$numero_columnas = count($columnas) + 4;
				if(is_int($numero_columnas)){
					$result[$numero_columnas] = array();
				} else {
					$result[] = $this->_make_row($data, $columnas, $id_record);
				}
			}
  
        }
		
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */

    private function _row_data($id, $columnas, $id_formulario) {
        $options = array(
            "id" => $id
        );
        $data = $this->Form_values_model->get_details($options)->row();
        return $this->_make_row($data, $columnas, $id_formulario);
    }

    /* prepare a row of client list table */

    private function _make_row($data, $columnas, $id_record) {
		
		$form = $this->Forms_model->get_one($id_record);
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$datos = json_decode($data->datos, true);
		
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}

		$row_data[] = $datos["fecha"] ? get_date_format($datos["fecha"], $id_proyecto) : "-";
		
		if($data->datos){
			$arreglo_fila = json_decode($data->datos, true);   
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;

				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id])){
					if($columna->id_tipo_campo == 2){ // TEXT AREA
						// $tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						// $valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
						$valor_campo = ($arreglo_fila[$columna->id]) ? $arreglo_fila[$columna->id] : "-";
					} elseif($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id]) ? get_date_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id]['start_date'];
						$end_date = $arreglo_fila[$columna->id]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 10){ // ARCHIVO
						if($arreglo_fila[$columna->id]){
							$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
							$valor_campo = anchor(get_uri("other_records/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
						} else {
							$valor_campo = '-';
						}
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id] == "") ? '-' : $arreglo_fila[$columna->id];
					}
				}else{
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					}
					$valor_campo = '-';
				}
				
				
				if($cont == 1){
					$view_modal = modal_anchor(get_uri("other_records/view_file/" . $data->id ."/".$columna->id), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id, "data-modal-lg" => true));	
					$view_modal .= modal_anchor(get_uri("other_records/preview/" . $id_record), "<i class='fa fa-cloud-download'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));	
					$row_data[] = $view_modal;
				}else{
					$row_data[] = $valor_campo;
				}
				
			}
			
		}
		/*
		$row_data[] = $data->created;
		$row_data[] = $data->modified ? $data->modified : "-";
		*/
		/*
		$row_data[] = time_date_zone_format($data->created,$id_proyecto);
		$row_data[] = $data->modified ? time_date_zone_format($data->modified,$id_proyecto) : "-";
		*/
		
		$fecha_created = explode(' ',$data->created); 
		$fecha_modified = explode(' ',$data->modified);
		
		$row_data[] = get_date_format($fecha_created["0"],$id_proyecto);
		$row_data[] = $data->modified ? get_date_format($fecha_modified["0"],$id_proyecto) : "-";
		
		$view = modal_anchor(get_uri("other_records/preview/" .$id_record), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("other_records/modal_form/".$id_record), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang("edit").' '.$form->nombre, "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_other_records'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("other_records/delete/".$id_record), "data-action" => "delete-confirmation", "data-custom" => true));
		
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
	
	function preview($id_record = 0){
		//$this->access_only_allowed_members();
		
		$id_proyecto = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
        $data_row_id = $this->input->post('id');
        /*validate_submitted_data(array(
            "id" => "numeric"
        ));*/
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		$view_data['id_other_record'] = $id_record;
		$view_data['id_proyecto'] = $id_proyecto;
		
		if($data_row_id){
			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
			$json = json_decode($view_data['model_info']->datos, true);
			$view_data['date'] = $json['fecha'];
		}
		$view_data['Other_records_controller'] = $this;

		$created_by = $this->Users_model->get_one($view_data['model_info']->created_by);
		$creador = $created_by->first_name." ".$created_by->last_name;
		if($view_data['model_info']->modified_by){
			$modified_by = $this->Users_model->get_one($view_data['model_info']->modified_by);
			$modificador = ($modified_by->id)?$modified_by->first_name." ".$modified_by->last_name:"-";
		}else{
			$modificador = "-";
		}
		
		$view_data['created_by'] = $creador;
		$view_data['modified_by'] = $modificador;
		
        $this->load->view('other_records/records/view', $view_data);
	}
	
	function preview_fixed_form($id_record = 0){
		
		$id_proyecto = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
        $data_row_id = $this->input->post('id');
        $record_info = $this->Forms_model->get_one($id_record);
		
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		$view_data['campos'] = $this->Fixed_fields_model->get_all_where(array(
			"codigo_formulario_fijo" => $record_info->codigo_formulario_fijo,
			"deleted" => 0
		))->result();
		
		$view_data['id_other_record'] = $id_record;
		$view_data['id_proyecto'] = $id_proyecto;
		
		if($data_row_id){
			$view_data['model_info'] = $this->Fixed_form_values_model->get_one($data_row_id);
			$json = json_decode($view_data['model_info']->datos, true);
			$view_data['date'] = $json['fecha'];
		}
		$view_data['Other_records_controller'] = $this;

		$created_by = $this->Users_model->get_one($view_data['model_info']->created_by);
		$creador = $created_by->first_name." ".$created_by->last_name;
		if($view_data['model_info']->modified_by){
			$modified_by = $this->Users_model->get_one($view_data['model_info']->modified_by);
			$modificador = ($modified_by->id)?$modified_by->first_name." ".$modified_by->last_name:"-";
		}else{
			$modificador = "-";
		}
		
		$view_data['created_by'] = $creador;
		$view_data['modified_by'] = $modificador;
		
        $this->load->view('other_records/records/fixed_forms/view', $view_data);
	}
	
    /* load client details view */

    function view($id_record) {
        //$this->access_only_allowed_members();

        if ($id_record) {
			
			//VALIDAR QUE EL FORMULARIO QUE SE ESTA VIENDO PERTENECE AL MISMO CLIENTE DEL USUARIO EN SESIÓN			
			$formulario = $this->Forms_model->get_one($id_record);
			
			if($formulario->id_cliente == $this->login_user->client_id){

				//VALIDAR QUE EL USUARIO SEA MIEMBRO DEL PROYECTO DEL FORMULARIO
				if(!$formulario->fijo){
					
					$id_proyecto_formulario = $this->Form_rel_project_model->get_one_where(array(
						"id_formulario" => $id_record,
						"deleted" => 0
					))->id_proyecto;
	
					$miembro_proyecto = $this->Project_members_model->get_one_where(array(
						"user_id" => $this->login_user->id,
						"project_id" => $id_proyecto_formulario, 
						"deleted" => 0
					));
					
					if(!$miembro_proyecto->id){
						redirect("forbidden");
					}
				
				} else { // Si el formulario es fijo
					
					$id_proyecto_formulario = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array(
						"id_formulario" => $id_record,
						"deleted" => 0
					))->id_proyecto;
					
					$miembro_proyecto = $this->Project_members_model->get_one_where(array(
						"user_id" => $this->login_user->id,
						"project_id" => $id_proyecto_formulario, 
						"deleted" => 0
					));
					
					if(!$miembro_proyecto->id){
						redirect("forbidden");
					}

				}

			} else {
				redirect("forbidden");
			}
			
			// FIN VALIDACIÓN DE CLIENTE - USUARIO DEL PROYECTO
			
			$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
			
			if(!$formulario->fijo){
			
				$options = array("id" => $id_record);
				$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
				$num_registros = count($registros);
				$record_info = $this->Forms_model->get_details($options)->row();
				$proyecto = $this->Projects_model->get_one($this->session->project_context);
				$view_data["project_info"] = $proyecto;
				
				$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
				$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");			
				
				if ($record_info){
					$view_data['num_registros'] = $num_registros;
					$view_data['record_info'] = $record_info;
					
					$columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					
					$json_string = "";
					foreach($columns as $column){
						
						if($column->id_tipo_campo == 1){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 2){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 3){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 4){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
						}elseif($column->id_tipo_campo == 5){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
						}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 10){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option no_breakline"}';
						}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 15){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 16){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else{
							$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
						}
						
					}
					
					$string_columnas = "";
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$view_data["columnas"] = $string_columnas;
					
					$amount_columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$cantidad_columnas = array();
					foreach($amount_columns as $columns){
						if(($columns->id_tipo_campo == 11) || ($columns->id_tipo_campo == 12)){
							continue;
						}else{
							$cantidad_columnas[] = $columns;
						}
					}
					$view_data["cantidad_columnas"] = count($cantidad_columnas);
					//$view_data["cantidad_columnas"] = count($cantidad_columnas);
					
					$arrayFechas = array();
					foreach($registros as $index => $reg){
						if(!$reg->modified){
							$arrayFechas[$index] = $reg->created;
						} else {
							$arrayFechas[$index] = $reg->modified;
						}
					}
					
					$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
					$view_data["fecha_modificacion"] = $fecha_modificacion;
					
					$this->template->rander("other_records/records/index", $view_data);
					//$this->load->view('clients/view', $view_data);
				} else {
					show_404();
				}
				
			} else { // Si el formulario es fijo
				
				$options = array("id" => $id_record);
				$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
				$num_registros = count($registros);
				$record_info = $this->Forms_model->get_details_formularios_fijos($options)->row();
				
				$proyecto = $this->Projects_model->get_one($this->session->project_context);
				$view_data["project_info"] = $proyecto;
				
				$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
				$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");	
				
				if ($record_info){
					
					$view_data['num_registros'] = $num_registros;
					$view_data['record_info'] = $record_info;

					$columns = $this->Fixed_fields_model->get_all_where(array(
						"codigo_formulario_fijo" => $record_info->codigo_formulario_fijo,
						"deleted" => 0
					))->result();
					
					$json_string = "";
					foreach($columns as $column){
						
						if($column->id_tipo_campo == 1){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 2){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center"}';
						}else if($column->id_tipo_campo == 3){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 4){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
						}elseif($column->id_tipo_campo == 5){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
						}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 10){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option"}';
						}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 15){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 16){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else{
							$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
						}
						
					}
					
					$string_columnas = "";
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$view_data["columnas"] = $string_columnas;
					
					$cantidad_columnas = array();
					foreach($columns as $column){
						if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else{
							$cantidad_columnas[] = $column;
						}
					}
					$view_data["cantidad_columnas"] = count($cantidad_columnas);
					
					$arrayFechas = array();
					foreach($registros as $index => $reg){
						if(!$reg->modified){
							$arrayFechas[$index] = $reg->created;
						} else {
							$arrayFechas[$index] = $reg->modified;
						}
					}
					
					$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
					$view_data["fecha_modificacion"] = $fecha_modificacion;

					$this->template->rander("other_records/records/index", $view_data);
					
				} else {
					show_404();
				}
			
			}
	
        } else {
            show_404();
        }
    }
	
	
	function get_field($id_campo, $id_elemento, $preview = NULL) {

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
			$row_elemento = $this->Values_model->get_details(array("id" => $id_elemento))->result();
			$decoded_default = json_decode($row_elemento[0]->datos, true);			
			$default_value = $decoded_default[$id_campo];
			//echo($decoded_default[$id_campo]);
			
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
				"autocomplete"=> "off",
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
					$html .= remove_file_prefix($default_value);
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("other_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
					
				} else {
					
					$html = '<div class="col-md-8">';
					$html .= remove_file_prefix($default_value);
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("other_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("other_records/delete_file"), "data-action" => "delete-fileConfirmation"));
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
			//$html = $default_value;
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
				"maxlength" => "255",
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
	
	function get_field_value($id_campo, $id_elemento, $id_proyecto) {

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $id_proyecto;
		
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
		
		$row_elemento = $this->Values_model->get_details(array("id" => $id_elemento))->result();
		$decoded_default = json_decode($row_elemento[0]->datos, true);
		
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		
		$default_value = $decoded_default[$id_campo];
		
		if($id_tipo_campo == 3){
			$default_value = ($default_value != "")?to_number_project_format($default_value, $id_proyecto):"";
		}
		if($id_tipo_campo == 4){
			$default_value = ($default_value != "")?get_date_format($default_value, $id_proyecto):"";
		}
		if($id_tipo_campo == 5){
			$default_value1 = $default_value["start_date"]?get_date_format($default_value["start_date"], $id_proyecto):"";
			$default_value2 = $default_value["end_date"]?get_date_format($default_value["end_date"], $id_proyecto):"";
			$default_value = $default_value1.' - '.$default_value2;
		}
		if($id_tipo_campo == 11){
			$default_value = $datos_campo->default_value;
		}
		if($id_tipo_campo == 15){
			$default_value = ($default_value != "")?to_number_project_format($default_value, $id_proyecto):"";
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
			$html = $default_value;
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
			$html = implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
		}
		
		//Rut
		if($id_tipo_campo == 8){
			$html = $default_value;
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			$html = $default_value;// es el value, no la etiqueta
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value ){
				
				$html = '<div class="col-md-8">';
				$html .= remove_file_prefix($default_value);
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("other_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
			//$html = $default_value;
		}
		
		//Correo
		if($id_tipo_campo == 13){
			$html = $default_value;
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$html = convert_to_general_settings_time_format($id_proyecto, $default_value);
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
		
		if($html == ""){$html = "-";}
		return $html;

    }
	
	function get_field_value_fixed_forms($id_campo, $id_elemento, $id_proyecto) {

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $id_proyecto; 
		
		$datos_campo = $this->Fixed_fields_model->get_one($id_campo);
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

		$row_elemento = $this->Fixed_form_values_model->get_one($id_elemento);
		$decoded_default = json_decode($row_elemento->datos, true);
		
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		
		$default_value = $decoded_default[$id_campo];
		
		if($id_tipo_campo == 3){
			$default_value = ($default_value != "")?to_number_project_format($default_value, $id_proyecto):"";
		}
		if($id_tipo_campo == 4){
			$default_value = ($default_value != "")?get_date_format($default_value, $id_proyecto):"";
		}
		if($id_tipo_campo == 5){
			$default_value1 = $default_value["start_date"]?get_date_format($default_value["start_date"], $id_proyecto):"";
			$default_value2 = $default_value["end_date"]?get_date_format($default_value["end_date"], $id_proyecto):"";
			$default_value = $default_value1.' - '.$default_value2;
		}
		if($id_tipo_campo == 11){
			$default_value = $datos_campo->default_value;
		}
		if($id_tipo_campo == 15){
			$default_value = ($default_value != "")?to_number_project_format($default_value, $id_proyecto):"";
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
			$html = $default_value;
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			 $html = $default_value;
		}
		
		//Selección
		if($id_tipo_campo == 6){
			
			// CARGA DE UNIDADES FUNCIONALES DE PROYECTO EN CAMPO SELECT UNIDAD FUNCIONAL DE OR FIJO UNIDADES FUNCIONALES.
			$formulario = $this->Forms_model->get_one($row_elemento->id_formulario);
			if($formulario->fijo && $formulario->codigo_formulario_fijo == "or_unidades_funcionales"){
				
				$id_uf = $default_value;
				$unidad_funcional = $this->Functional_units_model->get_one($id_uf);
				$html = $unidad_funcional->nombre;
				
			} else {
				
				$html = $default_value;// es el value, no el text
				
			}
			
			
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			$html = implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
		}
		
		//Rut
		if($id_tipo_campo == 8){
			$html = $default_value;
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			$html = $default_value;// es el value, no la etiqueta
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value ){
				
				$html = '<div class="col-md-8">';
				$html .= remove_file_prefix($default_value);
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("other_records/download_file_fixed_forms/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
			//$html = $default_value;
		}
		
		//Correo
		if($id_tipo_campo == 13){
			$html = $default_value;
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$html = convert_to_general_settings_time_format($id_proyecto, $default_value);
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
		
		if($html == ""){$html = "-";}
		return $html;

    }		

	function list_data_fixed_form($id_record = 0){
		
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$formulario = $this->Forms_model->get_one($id_record);
		
		$list_data = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
		$columnas = $this->Fixed_fields_model->get_all_where(array(
			"codigo_formulario_fijo" => $formulario->codigo_formulario_fijo,
			"deleted" => 0
		))->result();
		
		$result = array();
		
		foreach ($list_data as $data) {			
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_fixed_form($data, $columnas, $id_record);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_fixed_form($data, $columnas, $id_record);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$numero_columnas = count($columnas) + 4;
				if(is_int($numero_columnas)){
					$result[$numero_columnas] = array();
				} else {
					$result[] = $this->_make_row_fixed_form($data, $columnas, $id_record);
				}
			}
	
		}
		
		echo json_encode(array("data" => $result));
		
	}
	
	private function _row_data_fixed_form($id, $columnas, $id_record) {
		
		$data = $this->Fixed_form_values_model->get_one($id);
        return $this->_make_row_fixed_form($data, $columnas, $id_record);
		
    }
	
	private function _make_row_fixed_form($data, $columnas, $id_record){
				
		$form = $this->Forms_model->get_one($id_record);
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$formulario = $this->Forms_model->get_one($id_record);
		
		$datos = json_decode($data->datos, true);
		
		$row_data = array();
		$row_data[] = $data->id;
		
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		if(!$formulario->fijo){			
			$row_data[] = $datos["fecha"] ? get_date_format($datos["fecha"], $id_proyecto) : "-";
		}
		
		if($data->datos){
			
			$arreglo_fila = json_decode($data->datos, true);   
			$cont = 0;
			foreach($columnas as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id])){
					if($columna->id_tipo_campo == 2){ // TEXT AREA
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
					} elseif($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id]) ? get_date_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id]['start_date'];
						$end_date = $arreglo_fila[$columna->id]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 6){ // SELECCIÓN
						if($columna->codigo_formulario_fijo == "or_unidades_funcionales"){
							$id_unidad_funcional = $arreglo_fila[$columna->id];
							$unidad_funcional = $this->Functional_units_model->get_one($id_unidad_funcional);
							$valor_campo = ($id_unidad_funcional) ? $unidad_funcional->nombre : '-';
						} else {
							$valor_campo = ($arreglo_fila[$columna->id] == "") ? '-' : $arreglo_fila[$columna->id];
						}
					} elseif($columna->id_tipo_campo == 10){ // ARCHIVO
						if($arreglo_fila[$columna->id]){
							$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
							$valor_campo = anchor(get_uri("other_records/download_file_fixed_forms/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
						} else {
							$valor_campo = '-';
						}
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id] == "") ? '-' : $arreglo_fila[$columna->id];
					}
				} else {
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					}
					$valor_campo = '-';
				}

				if($cont == 1){
					$row_data[] = modal_anchor(get_uri("other_records/preview_fixed_form/" . $id_record), $valor_campo, array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
				}else{
					$row_data[] = $valor_campo;
				}
				
			}
			
		}
		
		$fecha_created = explode(' ',$data->created); 
		$fecha_modified = explode(' ',$data->modified);
		
		$row_data[] = get_date_format($fecha_created["0"],$id_proyecto);
		$row_data[] = $data->modified ? get_date_format($fecha_modified["0"],$id_proyecto) : "-";
		
		$view = modal_anchor(get_uri("other_records/preview_fixed_form/" .$id_record), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("other_records/modal_form_fixed_form/".$id_record), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang("edit").' '.$form->nombre, "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_other_records'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("other_records/delete_element_fixed_form/" .$id_record), "data-action" => "delete-confirmation", "data-custom" => true));
		
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
	
	function get_field_fixed_form($id_campo, $id_elemento, $preview = NULL, $id_formulario) {
		
		if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fixed_fields_model->get_one($id_campo);
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
			
			$row_elemento = $this->Fixed_form_values_model->get_one($id_elemento);
			$decoded_default = json_decode($row_elemento->datos, true);			
			$default_value = $decoded_default[$id_campo];
			
			if($id_tipo_campo == 5){
				$default_value1 = $default_value["start_date"] ? $default_value["start_date"] : "";
				$default_value2 = $default_value["end_date"] ? $default_value["end_date"] : "";
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
				
				$datos = $this->Fixed_form_values_model->get_all_where(array("id_formulario" => $id_mantenedora))->result();
				
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
					$default_value1 = json_decode($default_value)->start_date ? json_decode($default_value)->start_date : "";
					$default_value2 = json_decode($default_value)->end_date ? json_decode($default_value)->end_date : "";
				}else{
					$default_value1 = "";
					$default_value2 = "";
				}
			}else if($id_tipo_campo == 7){
				$default_value_multiple = array();
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
				
				$datos = $this->Fixed_form_values_model->get_all_where(array("id_formulario" => $id_mantenedora))->result();
				
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
				"autocomplete"=> "off",
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
			
			if($datos_campo->id == 25){ // SI EL CAMPO FIJO ES VALOR (id 23) (or_unidades_funcionales)
				$datos_campo = array(
					"id" => $name,
					"name" => $name,
					"value" => $default_value,
					"class" => "form-control",
					"placeholder" => $etiqueta,
					"autocomplete" => "off",
					"data-rule-regex" => "^[1-9][0-9]*$",
					"data-msg-regex" => lang("enter_integer_greater_than_zero"),
					"data-rule-number" => true,
					"data-msg-number" => lang("enter_integer_greater_than_zero"),
				);
			} else {
				$datos_campo = array(
					"id" => $name,
					"name" => $name,
					"value" => $default_value,
					"class" => "form-control",
					"placeholder" => $etiqueta,
					"autocomplete" => "off",
					"data-rule-number" => true,
					"data-msg-number" => lang("number_or_decimal_required")
				);
			}
			
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
						
			// CARGA DE UNIDADES FUNCIONALES DE PROYECTO EN CAMPO SELECT UNIDAD FUNCIONAL DE OR FIJO UNIDADES FUNCIONALES.
			$formulario = $this->Forms_model->get_one($id_formulario);
			if($formulario->fijo && $formulario->codigo_formulario_fijo == "or_unidades_funcionales"){
				
				$id_cliente = $formulario->id_cliente;
				$id_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array(
					"id_formulario" => $id_formulario, 
					"deleted" => 0
				))->id_proyecto;
				$opciones_select_uf = array("" => "-");
				$unidades_funcionales_proyecto = $this->Functional_units_model->get_all_where(array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"deleted" => 0
				))->result();
				foreach($unidades_funcionales_proyecto as $uf){
					$opciones_select_uf[$uf->id] = $uf->nombre;
				}
				$html = form_dropdown($name, $opciones_select_uf, $default_value, "id='$name' class='select2 validate-hidden' $extra");
				
			} else {
				
				$html = form_dropdown($name, $options, $default_value, "id='$name' class='select2 validate-hidden' $extra");
				
			}
			
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
					$html .= remove_file_prefix($default_value);
					$html .= '</div>';
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("other_records/download_file_fixed_forms/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';	
				} else {
					$html = '<div class="col-md-8">';
					$html .= remove_file_prefix($default_value);
					$html .= '</div>';
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("other_records/download_file_fixed_forms/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("other_records/delete_file_fixed_forms"), "data-action" => "delete-fileConfirmation"));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				}
			} else {
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
			//$html = $default_value;
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
				"maxlength" => "255",
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
				"data-msg-number" => lang("number_or_decimal_required"),
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
	
	function save_element_fixed_form($id_other_record){
		
		$elemento_id = $this->input->post('id');
		$id_proyecto = $this->session->project_context;
		
		$datos_formulario = $this->Forms_model->get_one($id_other_record);
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$id_campo_archivo_eliminar = $this->input->post('id_campo_archivo_eliminar');

		// SI EL USUARIO HA ELIMINADO ARCHIVOS DEL ELEMENTO, ELIMINAR ESTOS ARCHIVOS DEL ELEMENTO (BD) Y FÍSICAMENTE
		if($elemento_id){
			
			if($id_campo_archivo_eliminar){
				
				$elemento = $this->Fixed_form_values_model->get_one($elemento_id);
				$datos_elemento = json_decode($elemento->datos, true);

				$id_cliente = $datos_formulario->id_cliente;
				$id_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array(
					"id_formulario" => $datos_formulario->id,
					"deleted" => 0
				))->id_proyecto;
				$id_formulario = $datos_formulario->id;
				
				foreach($id_campo_archivo_eliminar as $id_archivo){
				
					$filename = $datos_elemento[$id_archivo];
					$file_path = "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$elemento_id."/".$filename;
					$campo_archivo_obligatorio = $this->Fixed_fields_model->get_one($id_archivo)->obligatorio;
						
					if(!$campo_archivo_obligatorio){

						$datos_elemento[$id_archivo] = "";
						$datos_final = json_encode($datos_elemento);
						$save_id = $this->Fixed_form_values_model->update_where(array("datos" => $datos_final), array("id" => $elemento_id));
						
						delete_file_from_directory($file_path);
						
					} 

				}
				
			}
			
		}

		$array_files = array();
		$array_datos = array();
		$columnas = $this->Fixed_fields_model->get_all_where(array(
			"codigo_formulario_fijo" => $datos_formulario->codigo_formulario_fijo,
			"deleted" => 0
		))->result();
		
		foreach($columnas as $columna){
			
			// VERIFICO SI EL CAMPO EN LOOP VIENE DESHABILITADO
			$deshabilitado = $columna->habilitado;
			$default_value = $columna->default_value;

			// cuando sea periodo
			if($columna->id_tipo_campo == 5){
				if($deshabilitado){
					$array_datos[$columna->id] = json_decode($default_value, true);
				}else{
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id] = array(
						"start_date" => $this->input->post($start_name),
						"end_date" => $this->input->post($end_name)
					);
				}
			}else if($columna->id_tipo_campo == 10){
				if($elemento_id){
					
					if(array_key_exists($columna->html_name.'_unchange', $this->input->post())){
						$array_datos[$columna->id] = $this->input->post($columna->html_name);
						//$array_files[$columna->id] = $this->input->post($columna->html_name);
					}else{
						if($this->input->post($columna->html_name)){
							$filename = uniqid("file")."-".$this->input->post($columna->html_name);
							$array_datos[$columna->id] = $filename;
							$array_files[$columna->id] = $filename;
						}else{
							$array_datos[$columna->id] = "";
						}
					}
					
				}else{
					if($this->input->post($columna->html_name)){
						$filename = uniqid("file")."-".$this->input->post($columna->html_name);
						$array_datos[$columna->id] = $filename;
						$array_files[$columna->id] = $filename;
					}else{
						$array_datos[$columna->id] = "";
					}
				}
				
			}else{
				
				if($deshabilitado){
					$array_datos[$columna->id] = $default_value;
				}else{
					$array_datos[$columna->id] =  $this->input->post($columna->html_name);
				}
			}
			
		}
		
		$json_datos = json_encode($array_datos);
		
		$data = array(
			"id_formulario" => $datos_formulario->id,
            "datos" => $json_datos, 
        );
		
		if($elemento_id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
        $save_id = $this->Fixed_form_values_model->save($data, $elemento_id);
		
		if ($save_id) {
			
			$client_id = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;
			
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $client_id,
				"id_project" => $id_proyecto,
				"id_user" => $this->session->user_id,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => ($elemento_id) ? "edit_fixed_or" : "add_fixed_or",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
			// traslado los archivos subidos			
			foreach($array_files as $id_columna => $nombre_archivo){
				$nombre_real_archivo = $id_columna . "_" . remove_file_prefix($nombre_archivo);
				$value = move_temp_file($nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_other_record."/elemento_".$save_id."/", "", "", $nombre_archivo);
			}

			$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_other_record)->result();
			$arrayFechas = array();
				foreach($registros as $index => $reg){
					if(!$reg->modified){
						$arrayFechas[$index] = $reg->created;
					} else {
						$arrayFechas[$index] = $reg->modified;
					}
				}
			$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? time_date_zone_format($record_info->modified, $id_proyecto) : time_date_zone_format(max($arrayFechas), $id_proyecto);

			$num_registros = count($registros);

			$columnas = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $datos_formulario->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
			echo json_encode(array("success" => true, "data" => $this->_row_data_fixed_form($save_id, $columnas, $id_other_record), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        
		} else {
			
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			
        }
		
	}

    function view_file($element_id = 0, $columna_id) {
        /* $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

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
        } */

        $row_data = $this->Form_values_model->get_one($element_id);
		if ($row_data->id) {

			$arreglo_fila = json_decode($row_data->datos, true);
			$filename = $arreglo_fila[$columna_id];

			$proyecto_rel_formulario = $this->Form_rel_project_model->get_one($row_data->id_formulario_rel_proyecto);
			$datos_formulario = $this->Forms_model->get_one($proyecto_rel_formulario->id_formulario);
			
			$id_cliente = $datos_formulario->id_cliente;
			$id_proyecto = $proyecto_rel_formulario->id_proyecto;
			
			$base_url = getcwd()."/";
			$file_url = $base_url."files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$datos_formulario->id."/elemento_".$element_id."/".$filename;

            $new_url = explode($base_url,$file_url);
            $view_data["file_url"] = get_file_uri($new_url[1]);
            $view_data["is_image_file"] = is_image_file($filename);
            $view_data["is_google_preview_available"] = is_google_preview_available($filename);
			$view_data['filename'] = $filename;

			//$uploaded_by_user = $this->Users_model->get_one($remuneracion->created_by);
			//$view_data['uploaded_by'] = $uploaded_by_user->first_name . " " . $uploaded_by_user->last_name;
			
			//$colaborador = $this->Users_model->get_one($remuneracion->id_user);
			//$view_data["colaborador"] = $colaborador->first_name." ".$colaborador->last_name;
			
			//$archivo = $filename;
			//$target_path = getcwd() . "/" . "files/remunerations/".$remuneracion->id."/";
			//$file_size = filesize($target_path.$archivo);
			//$view_data["file_size"] = $this->formatSizeUnits($file_size);

            $this->load->view("other_records/records/view_file", $view_data);
        } else {
            show_404();
        }

    }

    /* download a file */

    function download_file($id, $id_campo) {

        //$file_info = $this->General_files_model->get_one($id);
		$file_info = $this->Form_values_model->get_one($id);
        /*if (!$file_info->client_id) {
            redirect("forbidden");
        }*/
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos,true);
		$filename = $datos[$id_campo];
		
		$datos_formulario = $this->Form_rel_project_model->get_details(array("id" => $file_info->id_formulario_rel_proyecto))->result();
		$id_cliente = $datos_formulario[0]->id_cliente;
		$id_proyecto = $datos_formulario[0]->id_proyecto;
		$id_formulario = $datos_formulario[0]->id_formulario;
		
        //serilize the path
        $file_data = serialize(array(array("file_name" => $filename)));
        download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/", $file_data, true);
		
    }
	
	function download_file_fixed_forms($id, $id_campo) {

		$file_info = $this->Fixed_form_values_model->get_one($id);

		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos,true);
		$filename = $datos[$id_campo];
		
		$datos_formulario = $this->Forms_model->get_one($file_info->id_formulario);
		
		$id_cliente = $datos_formulario->id_cliente;
		$id_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array(
			"id_formulario" => $datos_formulario->id,
			"deleted" => 0
		))->id_proyecto;
		$id_formulario = $datos_formulario->id;

        //serilize the path
        $file_data = serialize(array(array("file_name" => $filename)));
        download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/", $file_data, true);
		
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
		$id_campo = $this->input->post('campo');
		$archivo_obligatorio = $this->input->post('obligatorio');		
		$file_info = $this->Form_values_model->get_one($id);
        
		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos,true);
		$filename = $datos[$id_campo];
		//$filename = $id_campo."-".$datos[$id_campo];
		
		$datos_formulario = $this->Form_rel_project_model->get_details(array("id" => $file_info->id_formulario_rel_proyecto))->result();
		$id_cliente = $datos_formulario[0]->id_cliente;
		$id_proyecto = $datos_formulario[0]->id_proyecto;
		$id_formulario = $datos_formulario[0]->id_formulario;
		$file_path = "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/".$filename;
		
		if(!$archivo_obligatorio){
			$datos[$id_campo] = "";
		}
		
		//$datos_final = json_encode($datos);
		//$save_id = $this->Form_values_model->update_where(array("datos" => $datos_final), array("id" => $id));
		
		$field_info = $this->Fields_model->get_one($id_campo);
		$obligatorio = $field_info->obligatorio;
		
		$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("fields/upload_file"),
					"validation_url" =>get_uri("fields/validate_file"),
					"html_name" => $field_info->html_name,
					"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"id_campo" => $id_campo
				),
				true);
				
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
    }
	
	function delete_file_fixed_forms() {

        $id = $this->input->post('id');
		$id_campo = $this->input->post('campo');
		$archivo_obligatorio = $this->input->post('obligatorio');
        $file_info = $this->Fixed_form_values_model->get_one($id);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos,true);
		$filename = $datos[$id_campo];
		
		$datos_formulario = $this->Forms_model->get_one($file_info->id_formulario);

		$id_cliente = $datos_formulario->id_cliente;
		$id_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array(
			"id_formulario" => $datos_formulario->id,
			"deleted" => 0
		))->id_proyecto;
		$id_formulario = $datos_formulario->id;
		
		$file_path = "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/".$filename;
		
		if(!$archivo_obligatorio){
			$datos[$id_campo] = "";
		}

		$field_info = $this->Fixed_fields_model->get_one($id_campo);
		$obligatorio = $field_info->obligatorio;
		
		$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("fields/upload_file"),
					"validation_url" =>get_uri("fields/validate_file"),
					"html_name" => $field_info->html_name,
					"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"id_campo" => $id_campo
				),
				true);
				
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
    }

	function download_last_bulletin(){

		$id_form = 2; // Formulario dinámico creado para Boletines
		$id_field = 1; // Campo Archivo dinámico para Boletín
		$last_bulletin = $this->Form_values_model->get_last_value_of_form(array("id_form" => $id_form))->row();
        $id_element = $last_bulletin->id;
        $data = json_decode($last_bulletin->datos, true);
        $filename = $data[$id_field];

        //serilize the path
        $file_data = serialize(array(array("file_name" => $filename)));
        download_app_files("files/mimasoft_files/client_1/project_1/form_".$id_form."/elemento_".$id_element."/", $file_data, true);

	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */