<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feeders extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        
		parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 3;
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
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
        $this->template->rander("feeders/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form($id_record = 0) {
        //$this->access_only_allowed_members();

        $data_row_id = $this->input->post('id');
        /* validate_submitted_data(array(
            "id" => "numeric"
        )); */
		
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		$view_data['id_mantenedora'] = $id_record;
		
		if($data_row_id){
			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
		}
		
		$view_data['Feeders_controller'] = $this;
		
        $this->load->view('feeders/records/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a client */

    function save($id_mantenedora) {

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
		$columnas = $this->Forms_model->get_fields_of_form($id_mantenedora)->result();
		$options = array("id_formulario" => $id_mantenedora, "id_proyecto" => $id_proyecto);
        $record_info = $this->Form_rel_project_model->get_details($options)->row();
        if($record_info){
			$id_formulario_rel_proyecto = $record_info->id;
		}
		
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
				$value = move_temp_file($id_columna.'_'.$nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_mantenedora."/elemento_".$save_id."/", "", "", $nombre_archivo);
			}
			
			$registros = $this->Feeders_model->get_values_of_record($id_mantenedora)->result();
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
			
			$columnas = $this->Forms_model->get_fields_of_form($id_mantenedora)->result();
			
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
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id, $columnas, $id_mantenedora), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
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
		
		/*
		$valor_formulario = $this->Form_values_model->get_one($id);
		$datos = json_decode($valor_formulario->datos, true);
		$cant_asignaciones = 0;
		$cant_calculos = 0;
		*/

		// VALIDACION SI EL ELEMENTO QUE SE INTENTA BORRAR EXISTE EN RELACIONAMIENTO
		
		// CRITERIOS: Consultar criterios del cliente y proyecto en los que en sus criterios tienen al menos 1 campo de tipo seleccion desde mantenedora, en el cual esta es la actual desde la que se esta borrando el elemento
		// Los criterios son campos de tipo 6 (Selección) y 16 (Selección desde Mantenedora)
		
		// ASIGNACIONES: Consultar asignaciones en los que el cliente y proyecto sean igual al del elemento que se intenta borrar, y el criterio pertenezca al cliente y proyecto y ademas el formulario de registro ambiental del criterio tenga asociado campos de tipo seleccion desde mantenedora donde la mantenedora fuente sea la del elemento que intento borrar
		
		$valor_formulario = $this->Form_values_model->get_one($id);
		$datos_formulario = json_decode($valor_formulario->datos, true);
		$formulario_rel_proyecto = $this->Form_rel_project_model->get_one($valor_formulario->id_formulario_rel_proyecto);
		$mantenedora = $this->Forms_model->get_one($formulario_rel_proyecto->id_formulario);
		
		$array_criterios_seleccion_mantenedora = array();
		$criterios = $this->Rule_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto, 
			"deleted" => 0))->result();
		
		foreach($criterios as $criterio){

			if($criterio->id_campo_sp){
				$campo = $this->Fields_model->get_one($criterio->id_campo_sp);
				if($campo->id_tipo_campo == 16){ //SI EL TIPO DE CAMPO ES SELECCIÓN DESDE MANTENEDORA
					
					if($campo->default_value || $campo->default_value != ""){
						$default_value_decoded = json_decode($campo->default_value, true);
						if($default_value_decoded["mantenedora"] == $mantenedora->id){
							$array_criterios_seleccion_mantenedora[] = $criterio->id;
						}
					}
					
				}
			} 
			
			if($criterio->id_campo_pu){
				$campo = $this->Fields_model->get_one($criterio->id_campo_pu);
				if($campo->id_tipo_campo == 16){ //SI EL TIPO DE CAMPO ES SELECCIÓN DESDE MANTENEDORA
				
					if($campo->default_value || $campo->default_value != ""){
						$default_value_decoded = json_decode($campo->default_value, true);
						if($default_value_decoded["mantenedora"] == $mantenedora->id){
							$array_criterios_seleccion_mantenedora[] = $criterio->id;
						}
					}

				}
			}
			
			/*
			if($criterio->id_campo_fc){
				$campo = $this->Fields_model->get_one($criterio->id_campo_fc);
				if($campo->id_tipo_campo == 16){ //SI EL TIPO DE CAMPO ES SELECCIÓN DESDE MANTENEDORA
				
					if($campo->default_value || $campo->default_value != ""){
						$default_value_decoded = json_decode($campo->default_value, true);
						if($default_value_decoded["mantenedora"] == $mantenedora->id){
							$array_criterios_seleccion_mantenedora[] = $criterio->id;
						}
						
					}
	
				}	
			}
			*/

		}
		
		$array_criterios_seleccion_mantenedora = array_unique($array_criterios_seleccion_mantenedora);
		$borrar_elemento = TRUE;
		
		if($array_criterios_seleccion_mantenedora){
			
			foreach($array_criterios_seleccion_mantenedora as $id_criterio_sel_mant){
				
				$asignaciones = $this->Assignment_model->get_all_where(array("id_criterio" => $id_criterio_sel_mant, "deleted" => 0))->result_array();
				
				foreach($asignaciones as $asignacion){
					$combinaciones_asignacion = $this->Assignment_combinations_model->get_all_where(array("id_asignacion" => $asignacion["id"], "deleted" => 0))->result_array();
					foreach($combinaciones_asignacion as $combinacion){
						if(in_array($combinacion["criterio_sp"], $datos_formulario)){
							$borrar_elemento = FALSE;
						}
						
						if(in_array($combinacion["criterio_pu"], $datos_formulario)){
							$borrar_elemento = FALSE;
						}
					}
				}	

				$calculos = $this->Calculation_model->get_all_where(array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"deleted" => 0
				))->result_array();
				
				foreach($calculos as $calculo){
					if(in_array($calculo["criterio_fc"], $datos_formulario)){
						$borrar_elemento = FALSE;
					}
				}
				
			}
			
		}

		if(!$borrar_elemento){
			echo json_encode(array("success" => false, 'message' => lang('busy_element_message')));
			exit();
		}
		
		$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		
		$fecha_modificacion = format_to_datetime(max($arrayFechas));
		$view_data["fecha_modificacion"] = $fecha_modificacion;
		
		validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Form_values_model->delete($id, true)) {
				$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
				$num_registros = count($registros);
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Form_values_model->delete($id)) {
				
				$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
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
	
	function delete_multiple($id_record){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_user = $this->session->user_id;
		$data_ids = json_decode($this->input->post('data_ids'));
		
		// VALIDACION SI EL ELEMENTO QUE SE INTENTA BORRAR EXISTE EN RELACIONAMIENTO
		
		// CRITERIOS: Consultar criterios del cliente y proyecto en los que en sus criterios tienen al menos 1 campo de tipo seleccion desde mantenedora, en el cual esta es la actual desde la que se esta borrando el elemento
		// Los criterios son campos de tipo 6 (Selección) y 16 (Selección desde Mantenedora)
		
		// ASIGNACIONES: Consultar asignaciones en los que el cliente y proyecto sean igual al del elemento que se intenta borrar, y el criterio pertenezca al cliente y proyecto y ademas el formulario de registro ambiental del criterio tenga asociado campos de tipo seleccion desde mantenedora donde la mantenedora fuente sea la del elemento que intento borrar
		
		foreach($data_ids as $id){
			
			$valor_formulario = $this->Form_values_model->get_one($id);
			$datos_formulario = json_decode($valor_formulario->datos, true);
			$formulario_rel_proyecto = $this->Form_rel_project_model->get_one($valor_formulario->id_formulario_rel_proyecto);
			$mantenedora = $this->Forms_model->get_one($formulario_rel_proyecto->id_formulario);
			
			$array_criterios_seleccion_mantenedora = array();
			$criterios = $this->Rule_model->get_all_where(array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto, 
				"deleted" => 0))->result();
				
			foreach($criterios as $criterio){
	
				if($criterio->id_campo_sp){
					$campo = $this->Fields_model->get_one($criterio->id_campo_sp);
					if($campo->id_tipo_campo == 16){ //SI EL TIPO DE CAMPO ES SELECCIÓN DESDE MANTENEDORA
						
						if($campo->default_value || $campo->default_value != ""){
							$default_value_decoded = json_decode($campo->default_value, true);
							if($default_value_decoded["mantenedora"] == $mantenedora->id){
								$array_criterios_seleccion_mantenedora[] = $criterio->id;
							}
						}
						
					}
				} 
				
				if($criterio->id_campo_pu){
					$campo = $this->Fields_model->get_one($criterio->id_campo_pu);
					if($campo->id_tipo_campo == 16){ //SI EL TIPO DE CAMPO ES SELECCIÓN DESDE MANTENEDORA
					
						if($campo->default_value || $campo->default_value != ""){
							$default_value_decoded = json_decode($campo->default_value, true);
							if($default_value_decoded["mantenedora"] == $mantenedora->id){
								$array_criterios_seleccion_mantenedora[] = $criterio->id;
							}
						}
	
					}
				}
	
			}
			
			$array_criterios_seleccion_mantenedora = array_unique($array_criterios_seleccion_mantenedora);
			$borrar_elemento = TRUE;
			
			if($array_criterios_seleccion_mantenedora){
			
				foreach($array_criterios_seleccion_mantenedora as $id_criterio_sel_mant){
					
					$asignaciones = $this->Assignment_model->get_all_where(array("id_criterio" => $id_criterio_sel_mant, "deleted" => 0))->result_array();
					
					foreach($asignaciones as $asignacion){
						$combinaciones_asignacion = $this->Assignment_combinations_model->get_all_where(array("id_asignacion" => $asignacion["id"], "deleted" => 0))->result_array();
						foreach($combinaciones_asignacion as $combinacion){
							if(in_array($combinacion["criterio_sp"], $datos_formulario)){
								$borrar_elemento = FALSE;
							}
							
							if(in_array($combinacion["criterio_pu"], $datos_formulario)){
								$borrar_elemento = FALSE;
							}
						}
					}	
	
					$calculos = $this->Calculation_model->get_all_where(array(
						"id_cliente" => $id_cliente,
						"id_proyecto" => $id_proyecto,
						"deleted" => 0
					))->result_array();
					
					foreach($calculos as $calculo){
						if(in_array($calculo["criterio_fc"], $datos_formulario)){
							$borrar_elemento = FALSE;
						}
					}
					
				}
				
			}
			
			if(!$borrar_elemento){
				echo json_encode(array("success" => false, 'message' => lang('busy_element_message_2')));
				exit();
			}

		}	

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
		
		if ($deleted_values) {
			
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
			
			$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
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
		
		$list_data = $this->Feeders_model->get_values_of_record($id_record)->result();
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
		
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
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
					} elseif($columna->id_tipo_campo == 10){ // ARCHIVO
						if($arreglo_fila[$columna->id]){
							$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
							$valor_campo = anchor(get_uri("feeders/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
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
					$row_data[] = modal_anchor(get_uri("feeders/preview/" . $id_record), $valor_campo, array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
				}else{
					$row_data[] = $valor_campo;
				}
				
			}
			
		}
		
		$fecha_created = explode(' ',$data->created); 
		$fecha_modified = explode(' ',$data->modified);
		
		$row_data[] = get_date_format($fecha_created["0"],$id_proyecto);
		$row_data[] = $data->modified ? get_date_format($fecha_modified["0"],$id_proyecto) : "-";
				
		$view = modal_anchor(get_uri("feeders/preview/" .$id_record), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("feeders/modal_form/".$id_record), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang("edit").' '.$form->nombre, "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_feeder'), "class" => "delete", "data-id" => $data->id, "data-id_record" => $id_record, "data-action-url" => get_uri("feeders/delete/".$id_record), "data-action" => "delete-confirmation", "data-custom" => true));
        
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
		
        $data_row_id = $this->input->post('id');
        /*validate_submitted_data(array(
            "id" => "numeric"
        ));*/
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		
		$view_data['id_mantenedora'] = $id_record;
		if($data_row_id){
			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
		}
		$view_data['Feeders_controller'] = $this;
		$view_data['id_proyecto'] = $id_proyecto;

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
		
        $this->load->view('feeders/records/view', $view_data);
	}
	
    /* load client details view */

    function view($id_record) {
        //$this->access_only_allowed_members();

        if ($id_record) {
			
			//VALIDAR QUE EL FORMULARIO QUE SE ESTA VIENDO PERTENECE AL MISMO CLIENTE DEL USUARIO EN SESIÓN			
			$formulario = $this->Forms_model->get_one($id_record);
			if($formulario->id_cliente == $this->login_user->client_id){

				//VALIDAR QUE EL USUARIO SEA MIEMBRO DEL PROYECTO DEL FORMULARIO
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

			} else {
				redirect("forbidden");
			}
			
            $options = array("id" => $id_record);
			$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
			$num_registros = count($registros);
            $record_info = $this->Forms_model->get_details($options)->row();
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$view_data["project_info"] = $proyecto;
			
			$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
			$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
			
            if ($record_info){
				$view_data['num_registros'] = $num_registros;				
                $view_data['record_info'] = $record_info;
				
				$columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
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
				/*
				$cantidad_columnas = $this->Forms_model->get_fields_of_form($record_info->id)->result();
				$view_data["cantidad_columnas"] = count($cantidad_columnas);
				*/
				
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
				
                $this->template->rander("feeders/records/index", $view_data);
				//$this->load->view('clients/view', $view_data);
            } else {
                show_404();
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
		
		} else {
			
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
					$html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("feeders/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
					$html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("feeders/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("feeders/delete_file"), "data-action" => "delete-fileConfirmation"));
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
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, array("" => "-") + $array_opciones, "", "id='$name' class='select2 validate-hidden' $extra");
			
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
			
			/*if($default_value){
				
				$html = '<div class="col-md-8">';
				$html .= remove_file_prefix($default_value);
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("feeders/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
			}*/
			
			if($default_value){
				
				if($preview){
					$html = '<div class="col-md-8">';
					$html .= remove_file_prefix($default_value);
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
					$html .= remove_file_prefix($default_value);
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-confirmation"));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';	
					$html .= '<input type="hidden" name="'.$name.'_unchange" value="1" />';			
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
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');
        $this->access_only_allowed_members();


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "client_id" => $client_id,
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

    function download_file($id, $id_campo) {

        //$file_info = $this->General_files_model->get_one($id);
		$file_info = $this->Form_values_model->get_one($id);
        /*if (!$file_info->client_id) {
            redirect("forbidden");
        }*/
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos, true);
		$filename = $datos[$id_campo];
		
		$datos_formulario = $this->Form_rel_project_model->get_details(array("id" => $file_info->id_formulario_rel_proyecto))->result();
		$id_cliente = $datos_formulario[0]->id_cliente;
		$id_proyecto = $datos_formulario[0]->id_proyecto;
		$id_formulario = $datos_formulario[0]->id_formulario;
				
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
		
		/*
		if(file_exists($file_path)) {
			
			if(!$archivo_obligatorio){
				delete_file_from_directory($file_path);
			}			
			if($save_id){
				echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			}
        }else{
			if($save_id){
				echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			}
		}
		*/
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
		
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