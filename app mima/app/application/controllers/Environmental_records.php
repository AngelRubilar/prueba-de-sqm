<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Environmental_records extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
		//$this->load->helper("date_time");
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 2;
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
        $this->template->rander("environmental_records/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form($id_record = 0) {
        //$this->access_only_allowed_members();

        $data_row_id = $this->input->post('id');
        $flujo = $this->input->post('flujo');
        /*validate_submitted_data(array(
            "id" => "numeric"
        ));*/

		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();
		//Consulta desde la bd categorías por materiales de un formulario
		$categories = $this->Categories_model->get_categories_of_material_of_form($id_record)->result();
		//preparando la vista para el dropdown
		$array_cat = array();
		foreach($categories as $index => $key){
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
			if($row_alias->alias){
				$nombre = $row_alias->alias;
			}else{
				$nombre = $key->nombre;
			}
			$array_cat[$key->id] = $nombre;
		}
		
		$view_data["flujo"] = $flujo;
		$view_data['categorias'] = $array_cat;
		$view_data['count_cat'] = count($array_cat);
		$view_data['id_registro_ambiental'] = $id_record;
		
		if((isset($flujo))&&(($flujo == "Residuo") || ($flujo == "Consumo") || ($flujo == "No Aplica"))){
			$form = $this->Forms_model->get_one($id_record);
			$form_data = $form->unidad;
			$data_unidad_residuo = json_decode($form_data);
			$data_tipo_unidad = $this->Unity_type_model->get_one($data_unidad_residuo->tipo_unidad_id);
			$data_unidad = $this->Unity_model->get_one($data_unidad_residuo->unidad_id);
			$nombre_unidad = $data_unidad_residuo->nombre_unidad;
			$tipo_unidad = $data_tipo_unidad->nombre;
			$unidad = $data_unidad->nombre;
			
			$view_data['nombre_unidad_residuo'] = $nombre_unidad;
			$view_data['tipo_unidad_residuo'] = $tipo_unidad;
			$view_data['unidad_residuo'] = $unidad;

		}
		
		if($flujo == 'Residuo'){
			$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result_array();
			$array_tipo_tratamiento = array();
			foreach($tipos_tratamientos as $tipo_tratamiento){
				$array_tipo_tratamiento[$tipo_tratamiento["id"]] = $tipo_tratamiento["nombre"];
			}
			
			$view_data["tipo_tratamiento"] = $array_tipo_tratamiento;
			
			// TIPO DE TRATAMIENTO POR DEFECTO
			if($form->tipo_tratamiento){
				$data_tipo_tratamiento = json_decode($form->tipo_tratamiento);
				$view_data["tipo_tratamiento_default"] = $data_tipo_tratamiento->tipo_tratamiento;
				$view_data["disabled_field"] = (boolean)$data_tipo_tratamiento->disabled_field;
			}
		}
		
		if($flujo == "Consumo"){
			
			$formulario = $this->Forms_model->get_one($id_record);
			
			if($formulario->tipo_origen){
				
				$data_tipo_origen = json_decode($formulario->tipo_origen);
				
				if($data_tipo_origen->type_of_origin == "1"){ // id 1: matter
										
					$data_row = $this->Form_values_model->get_one($data_row_id);
					$datos_data_row = json_decode($data_row->datos, true);
					
					if($datos_data_row["type_of_origin"]){
						$view_data["type_of_origin"] = $datos_data_row["type_of_origin"];
						$view_data["default_matter"] = $datos_data_row["type_of_origin_matter"];
					} else {
						$view_data["type_of_origin"] = $data_tipo_origen->type_of_origin;
						$view_data["default_matter"] = ($data_tipo_origen->default_matter) ? $data_tipo_origen->default_matter : "";
					}
					
					$view_data["disabled_field"] = $data_tipo_origen->disabled_field;
					
					$array_tipos_origen_materia = array("" => "-");
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $data_tipo_origen->type_of_origin,
						"deleted" => 0
					))->result();
					
					foreach($tipos_origen_materia as $tipo_origen_materia){
						$array_tipos_origen_materia[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
					}
					$view_data["array_tipos_origen_materia"] = $array_tipos_origen_materia;
				}
				
				if($data_tipo_origen->type_of_origin == "2"){ // id 2: energy
					
					$array_tipos_origen = array("" => "-");
					$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
					foreach($tipos_origen as $tipo_origen){
						$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
					}
					$view_data["array_tipos_origen"] = $array_tipos_origen;
					$view_data["type_of_origin"] = $data_tipo_origen->type_of_origin;
					$view_data["disabled_field"] = $data_tipo_origen->disabled_field;
				}

			}
			
		}
		
		if($flujo == "No Aplica"){
			
			// DEFINICION TIPO POR DEFECTO
			$array_tipos_por_defecto = array("" => "-");
			$tipos_por_defecto = $this->EC_Types_no_apply_model->get_all()->result();
			foreach($tipos_por_defecto as $tipo_por_defecto){
				$array_tipos_por_defecto[$tipo_por_defecto->id] = lang($tipo_por_defecto->nombre);
			}
			$view_data["array_tipos_por_defecto"] = $array_tipos_por_defecto;
			
			// TIPO POR DEFECTO
			if($form->tipo_por_defecto){
				$data_tipo_por_defecto = json_decode($form->tipo_por_defecto);
				$view_data["tipo_por_defecto_default"] = $data_tipo_por_defecto->default_type;
				$view_data["disabled_default_type"] = (boolean)$data_tipo_por_defecto->disabled_field;
			}
			
		}
		
		if($data_row_id){

			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
			$datos = json_decode($view_data['model_info']->datos, true);
			if((isset($flujo))&&($flujo == "Residuo")){
				
				/* Archivos Fijos */
				if(isset($datos["nombre_archivo_retiro"])){
					//var_dump($datos["nombre_archivo_retiro"]);
					
					$view_data['archivo_retiro'] = $datos["nombre_archivo_retiro"];
					
					$html_archivo_retiro = '<div class="col-md-8">';
					$html_archivo_retiro .= remove_file_prefix($datos["nombre_archivo_retiro"]);
					$html_archivo_retiro .= '</div>';
					$html_archivo_retiro .= '<div class="col-md-4">';
					$html_archivo_retiro .= '<table id="table_delete_nombre_archivo_retiro" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivo_retiro .= '<tbody><tr><td class="option text-center">';
					$html_archivo_retiro .= anchor(get_uri("environmental_records/download_file/".$data_row_id."/nombre_archivo_retiro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html_archivo_retiro .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $data_row_id, "data-campo" => "nombre_archivo_retiro", "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-fileConfirmation"));
					$html_archivo_retiro .= '<input type="hidden" name="retirement_unchange" value="1" />';
					$html_archivo_retiro .= '</td>';
					$html_archivo_retiro .= '</tr>';
					$html_archivo_retiro .= '</thead>';
					$html_archivo_retiro .= '</table>';
					$html_archivo_retiro .= '</div>';
					
					$view_data['html_archivo_retiro'] = $html_archivo_retiro;
					
				} else {
					$html_archivo_retiro = '<div class="col-md-8">';
					$html_archivo_retiro .= '-';
					$html_archivo_retiro .= '</div>';
					$view_data['html_archivo_retiro'] = $html_archivo_retiro;
				}
				
				if(isset($datos["nombre_archivo_recepcion"])){
					
					$view_data['archivo_recepcion'] = $datos["nombre_archivo_recepcion"];
					$html_archivo_recepcion = '<div class="col-md-8">';
					$html_archivo_recepcion .= remove_file_prefix($datos["nombre_archivo_recepcion"]);
					$html_archivo_recepcion .= '</div>';
					$html_archivo_recepcion .= '<div class="col-md-4">';
					$html_archivo_recepcion .= '<table id="table_delete_nombre_archivo_recepcion" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivo_recepcion .= '<tbody><tr><td class="option text-center">';
					$html_archivo_recepcion .= anchor(get_uri("environmental_records/download_file/".$data_row_id."/nombre_archivo_recepcion"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html_archivo_recepcion .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $data_row_id, "data-campo" => "nombre_archivo_recepcion", "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-fileConfirmation"));
					$html_archivo_recepcion .= '<input type="hidden" name="reception_unchange" value="1" />';
					//$html_archivo_recepcion .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html_archivo_recepcion .= '</td>';
					$html_archivo_recepcion .= '</tr>';
					$html_archivo_recepcion .= '</thead>';
					$html_archivo_recepcion .= '</table>';
					$html_archivo_recepcion .= '</div>';
					
					$view_data['html_archivo_recepcion'] = $html_archivo_recepcion;
					
				} else {
					$html_archivo_recepcion = '<div class="col-md-8">';
					$html_archivo_recepcion .= '-';
					$html_archivo_recepcion .= '</div>';
					$view_data['html_archivo_recepcion'] = $html_archivo_recepcion;
				}
			}
		}
		
		$view_data['Environmental_records_controller'] = $this;
        $this->load->view('environmental_records/records/modal_form', $view_data);
		
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a client */

    function save($id_registro_ambiental) {
		
        $elemento_id = $this->input->post('id');
		$id_proyecto = $this->session->project_context;
		$form_info = $this->Forms_model->get_one($id_registro_ambiental);
		$category = $this->input->post('category');
		
		// Campo unidad (no solo residuo)
		$waste_unit = $this->input->post('waste_unit');
		$tipo_unidad_residuo = $this->input->post('tipo_unidad_residuo');
		$unidad_residuo = $this->input->post('unidad_residuo');
		
		// cuando es flujo "Consumo"
		$type_of_origin = $this->input->post("type_of_origin");
		$type_of_origin_matter = $this->input->post("type_of_origin_matter");
		
		// cuando es flujo "Residuo"
		$retirement_date = $this->input->post('retirement_date');
		$type_of_treatment = $this->input->post('type_of_treatment');
		
		// cuando es flujo "No Aplica"
		$tipo_por_defecto = $this->input->post("default_type");
		
		$id_campo_archivo_eliminar = $this->input->post('id_campo_archivo_eliminar'); //ID DE ARCHIVOS A ELIMINAR

        validate_submitted_data(array("id" => "numeric"));
		
		// SI EL USUARIO HA ELIMINADO ARCHIVOS DEL ELEMENTO (FIJOS O DINÁMICOS), ELIMINAR ESTOS ARCHIVOS DEL ELEMENTO (BD) Y FÍSICAMENTE
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
					
					if( ($id_archivo != "nombre_archivo_retiro") && ($id_archivo != "nombre_archivo_recepcion") ){
						
						$campo_archivo_obligatorio = $this->Fields_model->get_one($id_archivo)->obligatorio;
						
						if(!$campo_archivo_obligatorio){

							$datos_elemento[$id_archivo] = "";
							$datos_final = json_encode($datos_elemento);
							$save_id = $this->Form_values_model->update_where(array("datos" => $datos_final), array("id" => $elemento_id));
							
							delete_file_from_directory($file_path);
							
						} 

					} else {

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
		$columnas = $this->Forms_model->get_fields_of_form($id_registro_ambiental)->result();
		
		$options = array("id_formulario" => $id_registro_ambiental, "id_proyecto" => $id_proyecto);
        $record_info = $this->Form_rel_project_model->get_details($options)->row();
		$form_rel_proyect_info = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $id_registro_ambiental, "id_proyecto" => $id_proyecto));
	
        if($form_rel_proyect_info){
			$id_formulario_rel_proyecto = $form_rel_proyect_info->id;
		}

		// LLENAR EL JSON ----
		$array_datos['fecha'] = $this->input->post('date_filed');
		$array_datos['id_categoria'] = (int)$category;
		
		// UNIDAD FIJA
		validate_submitted_data(array("waste_unit" => "numeric"));
		$array_datos['unidad_residuo'] = (float)$waste_unit;
		$array_datos['tipo_unidad'] = $tipo_unidad_residuo;
		$array_datos['unidad'] = $unidad_residuo;
		
		// DATOS CONSUMO - RESIDUO - NO APLICA
		if($form_info->flujo == "Consumo"){
			$array_datos['type_of_origin'] = $type_of_origin;
			if($type_of_origin_matter){
				$array_datos['type_of_origin_matter'] = $type_of_origin_matter;
			}
		}elseif($form_info->flujo == "Residuo"){
			
			// SI SE DEFINE EL TIPO DE TRATAMIENTO DESHABILITADO, DEBO INGRESAR EL VALOR POR DEFECTO
			$tipo_tratamiento_json = json_decode($form_info->tipo_tratamiento);
			if($tipo_tratamiento_json->disabled_field){
				$type_of_treatment = $tipo_tratamiento_json->tipo_tratamiento;
			}
			$array_datos['tipo_tratamiento'] = $type_of_treatment;
			
			if($retirement_date !== ""){
				$array_datos['fecha_retiro'] = $retirement_date;
			}else{
				$array_datos['fecha_retiro'] = NULL;
			}
			
			// ARCHIVOS FIJOS DE RESIDUO
			$file_name_retirement = $this->input->post('file_name_retirement');
			$file_name_retirement_prefix = uniqid("file")."-".$file_name_retirement;
			$file_name_reception = $this->input->post('file_name_reception');
			$file_name_reception_prefix = uniqid("file")."-".$file_name_reception;
			
			if($elemento_id){
				$elemento = $this->Form_values_model->get_one($elemento_id);
				$datos_elemento = json_decode($elemento->datos, true);
				
				// SI NO SE MODIFICA EL ARCHIVO
				if($this->input->post('retirement_unchange')){
					$nombre_archivo_anterior_retiro = $datos_elemento['nombre_archivo_retiro'];
					$array_datos['nombre_archivo_retiro'] = $nombre_archivo_anterior_retiro;
				// SI SE SUBE CON LA CARGA DE ARCHIVO DESDE EL INICIO DEL FORMULARIO
				}elseif(!array_key_exists('retirement_unchange', $this->input->post()) && $file_name_retirement){
					$array_datos['nombre_archivo_retiro'] = $file_name_retirement_prefix;
					$array_files['nombre_archivo_retiro'] = $file_name_retirement_prefix;
				}
				
				// SI NO SE MODIFICA EL ARCHIVO
				if($this->input->post('reception_unchange')){
					$nombre_archivo_anterior_recepcion = $datos_elemento['nombre_archivo_recepcion'];
					$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_anterior_recepcion;
				// SI SE SUBE CON LA CARGA DE ARCHIVO DESDE EL INICIO DEL FORMULARIO
				}elseif(!array_key_exists('reception_unchange', $this->input->post()) && $file_name_reception){
					$array_datos['nombre_archivo_recepcion'] = $file_name_reception_prefix;
					$array_files['nombre_archivo_recepcion'] = $file_name_reception_prefix;
				}
				
			}else{
				if($file_name_retirement){
					$array_datos['nombre_archivo_retiro'] = $file_name_retirement_prefix;
					$array_files['nombre_archivo_retiro'] = $file_name_retirement_prefix;
				}else{
					$array_datos['nombre_archivo_retiro'] = NULL;
				}
				
				if($file_name_reception){
					$array_datos['nombre_archivo_recepcion'] = $file_name_reception_prefix;
					$array_files['nombre_archivo_recepcion'] = $file_name_reception_prefix;
				}else{
					$array_datos['nombre_archivo_recepcion'] = NULL;
				}
				
			}
			
		}elseif($form_info->flujo == "No Aplica"){
			
			$array_datos['default_type'] = $tipo_por_defecto;
			
		}else{
			
		}
		
		// CAMPOS DINAMICOS
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
        $data = array("id_formulario_rel_proyecto" => $id_formulario_rel_proyecto, "datos" => $json_datos);
		
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
				$value = move_temp_file($id_columna.'_'.$nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_registro_ambiental."/elemento_".$save_id."/", "","", $nombre_archivo);
			}
			
			if($file_name_retirement){
				//$nombre_real_archivo = remove_file_prefix($array_datos['nombre_archivo_retiro']);
				$nombre_real_archivo = "retirement_file-".$file_name_retirement;
				$file_retirement = move_temp_file($nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_registro_ambiental."/elemento_".$save_id."/", "", "", $array_datos['nombre_archivo_retiro']);
			}
			
			if($file_name_reception){
				//$nombre_real_archivo = remove_file_prefix($array_datos['nombre_archivo_recepcion']);
				$nombre_real_archivo = "reception_file-".$file_name_reception;
				$file_reception = move_temp_file($nombre_real_archivo, "files/mimasoft_files/client_".$client_id."/project_".$id_proyecto."/form_".$id_registro_ambiental."/elemento_".$save_id."/", "", "", $array_datos['nombre_archivo_recepcion']);
			}
			
			$registros = $this->Environmental_records_model->get_values_of_record($id_registro_ambiental)->result();
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
			
			$columnas = $this->Forms_model->get_fields_of_form($id_registro_ambiental)->result();
			
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
			
			// Guardar histórico alertas
			$unidad = json_decode($form_info->unidad, TRUE);
			$options = array(
				"id_client" => $client_id,
				"id_project" => $id_proyecto,
				"id_user" => $this->session->user_id,
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"alert_config" => array(
					"id_categoria" => $category,
					"id_tipo_unidad" => (string)$unidad["tipo_unidad_id"],
					"id_unidad" => (string)$unidad["unidad_id"]
				),
				"id_element" => $save_id
			);
			ayn_save_historical_alert($options);
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id, $columnas, $id_registro_ambiental), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'view' => $this->input->post('view'), 'message' => lang('record_saved'))); // se usará en este caso el view?
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete($id_record) {
        //$this->access_only_allowed_members();
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_user = $this->session->user_id;
		$id = $this->input->post('id');
		
		$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));
        
        if ($this->input->post('undo')) {
            if ($this->Form_values_model->delete($id, true)) {
				
				$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
				$num_registros = count($registros);
				
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Form_values_model->delete($id)) {
				
				$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
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
			
			$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
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
		
		// Filtro AppTable
		$options = array(
			"id_categoria" => $this->input->post('id_categoria')
		);
		
        //$this->access_only_allowed_members();
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
        $list_data = $this->Environmental_records_model->get_values_of_record($id_record, $options)->result();
		$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();
		//get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $this->login_user->client_id));
		
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
				$numero_columnas = count($columnas) + 5;
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
		$flujo = $form->flujo;
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;

		$options = array("id" => $id_record);
		$record_info = $this->Forms_model->get_details($options)->row();

		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$datos = json_decode($data->datos, true);
		
		$id_categoria = $datos["id_categoria"];
		$categoria_original = $this->Categories_model->get_one_where(array('id' => $id_categoria, "deleted" => 0));
		$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
		
		if($categoria_alias->alias){
			$nombre_categoria = $categoria_alias->alias;
		}else{
			$nombre_categoria = $categoria_original->nombre;
		}
		
		$row_data[] = get_date_format($datos["fecha"], $id_proyecto);
		$row_data[] =  modal_anchor(get_uri("environmental_records/preview/" . $id_record), $nombre_categoria, array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
		
		if(($record_info->flujo == "Residuo")||($record_info->flujo == "Consumo")||($record_info->flujo == "No Aplica")){
			
			if(isset($datos["unidad_residuo"])){
				$row_data[] = to_number_project_format($datos["unidad_residuo"], $id_proyecto);
			}else{
				$row_data[] = "-";
			}
		}

		if($record_info->flujo == "Residuo"){
			$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $datos["tipo_tratamiento"], "deleted" => 0));
			if(isset($datos["tipo_tratamiento"])){
				
				if($datos["tipo_tratamiento"] == $tipo_tratamiento->id){
					$row_data[] = $tipo_tratamiento->nombre;
				}
				
			}else{
				$row_data[] = "-";
			}
		}
		
		if($record_info->flujo == "Consumo"){
			
			$elemento = $this->Form_values_model->get_one($data->id);
			$datos_elemento = json_decode($elemento->datos, true);
			
			if($datos_elemento["type_of_origin"] == "1"){
				if(isset($datos_elemento["type_of_origin_matter"])){
					$type_of_origin_matter = $this->EC_Types_of_origin_matter_model->get_one($datos_elemento["type_of_origin_matter"]);
					$row_data[] = lang($type_of_origin_matter->nombre);
				} else {
					$row_data[] = "-";
				}
			} else {
				$type_of_origin = $this->EC_Types_of_origin_model->get_one($datos_elemento["type_of_origin"]);
				$row_data[] = lang($type_of_origin->nombre);
			}
			
		}
		
		if($record_info->flujo == "No Aplica"){
			
			$elemento = $this->Form_values_model->get_one($data->id);
			$datos_elemento = json_decode($elemento->datos, true);
			
			if(isset($datos_elemento["default_type"])){
				$default_type = $this->EC_Types_no_apply_model->get_one($datos_elemento["default_type"]);
				$row_data[] = lang($default_type->nombre);
			} else {
				$row_data[] = "-";
			}
			
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
							$valor_campo = anchor(get_uri("environmental_records/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
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
				
				$row_data[] = $valor_campo;
			}
		}
		$fecha_created = explode(' ',$data->created); 
		$fecha_modified = explode(' ',$data->modified);

		
		if($record_info->flujo == "Residuo"){
			
			if(isset($datos["fecha_retiro"])){
				$row_data[] = get_date_format($datos["fecha_retiro"],$id_proyecto);
			}else{
				$row_data[] = "-";
			}
			
			$elemento = $this->Form_values_model->get_one($data->id);
			$datos_elemento = json_decode($elemento->datos, true);
			
			if(isset($datos["nombre_archivo_retiro"])){
				//$row_data[] = $datos["nombre_archivo_retiro"];	
				if($datos_elemento["nombre_archivo_retiro"]){
					$row_data[] = anchor(get_uri("environmental_records/download_file/".$data->id."/nombre_archivo_retiro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => remove_file_prefix($datos_elemento["nombre_archivo_retiro"])));
				} else {
					$row_data[] = "-";
				}
			}else{
				$row_data[] = "-";
			}

			if(isset($datos["nombre_archivo_recepcion"])){
				//$row_data[] = $datos["nombre_archivo_recepcion"];	
				if($datos_elemento["nombre_archivo_recepcion"]){
					$row_data[] = anchor(get_uri("environmental_records/download_file/".$data->id."/nombre_archivo_recepcion"), "<i class='fa fa fa-cloud-download'></i>", array("title" => remove_file_prefix($datos_elemento["nombre_archivo_recepcion"])));
				} else {
					$row_data[] = "-";
				}
			}else{
				$row_data[] = "-";
			}
		}

		$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
		
		$view = modal_anchor(get_uri("environmental_records/preview/" .$id_record), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang("view").' '.$form->nombre, "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("environmental_records/modal_form/".$id_record), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang("edit").' '.$form->nombre, "data-post-id" => $data->id,"data-post-flujo" => $flujo));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_environmental_record'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("environmental_records/delete/".$id_record), "data-action" => "delete-confirmation", "data-custom" => true));
		
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
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
        $data_row_id = $this->input->post('id');
		
		$view_data['campos'] = $this->Forms_model->get_fields_of_form($id_record)->result();	
		$view_data['id_registro_ambiental'] = $id_record;
		$view_data['form_info'] = $this->Forms_model->get_one($id_record);
		
		if($data_row_id){
			
			$view_data['model_info'] = $this->Form_values_model->get_one($data_row_id);
			$datos = json_decode($view_data['model_info']->datos, true);
			
			if($view_data['form_info']->flujo == "Residuo"){
				
				$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id"=>$datos["tipo_tratamiento"], "deleted" => 0));
			
				if(isset($datos['tipo_tratamiento'])){
					if($datos['tipo_tratamiento'] == $tipo_tratamiento->id){
						$view_data['tipo_tratamiento'] = $tipo_tratamiento->nombre;
					}
				}else{
					$view_data['tipo_tratamiento'] = "-";
				}
				
				$formulario_unidad = json_decode($view_data['form_info']->unidad, true);
				$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
				$campo_unidad = $formulario_unidad["nombre_unidad"];
				
				$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
				$nombre_tipo_unidad = $tipo_unidad->nombre;
				
				$nombre_unidad = $unidad->nombre;
				$label_unidad = $campo_unidad . " (" . $nombre_tipo_unidad. ")";
				
				$view_data['label_unidad'] = $label_unidad;
				$view_data['nombre_unidad'] = $nombre_unidad;
				$view_data['unidad_residuo'] = $datos["unidad_residuo"];
	
				if(isset($datos["fecha_retiro"])){
					$view_data['fecha_retiro'] = get_date_format($datos["fecha_retiro"], $id_proyecto);
				} else {
					$view_data['fecha_retiro'] = "-";
				}
				
				/* Archivos Fijos */
				if(isset($datos["nombre_archivo_retiro"])){
					if($datos["nombre_archivo_retiro"]){
						
						$html_archivo_retiro = '<div class="col-md-8">';
						$html_archivo_retiro .= remove_file_prefix($datos["nombre_archivo_retiro"]);
						$html_archivo_retiro .= '</div>';
						
						$html_archivo_retiro .= '<div class="col-md-4">';
						$html_archivo_retiro .= '<table id="table_delete_nombre_archivo_retiro" class="table_delete"><thead><tr><th></th></tr></thead>';
						$html_archivo_retiro .= '<tbody><tr><td class="option text-center">';
						$html_archivo_retiro .= anchor(get_uri("environmental_records/download_file/".$data_row_id."/nombre_archivo_retiro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));				
						$html_archivo_retiro .= '</td>';
						$html_archivo_retiro .= '</tr>';
						$html_archivo_retiro .= '</thead>';
						$html_archivo_retiro .= '</table>';
						$html_archivo_retiro .= '</div>';
		
						$view_data['html_archivo_retiro'] = $html_archivo_retiro;
						
					} else {
						$html_archivo_retiro .= '-';
						$view_data['html_archivo_retiro'] = $html_archivo_retiro;
					}
					
				} else {
					$html_archivo_retiro .= '-';
					$view_data['html_archivo_retiro'] = $html_archivo_retiro;
				}
				
				if(isset($datos["nombre_archivo_recepcion"])){
					if($datos["nombre_archivo_recepcion"]){
						$html_archivo_recepcion = '<div class="col-md-8">';
						$html_archivo_recepcion .= remove_file_prefix($datos["nombre_archivo_recepcion"]);
						$html_archivo_recepcion .= '</div>';
						
						$html_archivo_recepcion .= '<div class="col-md-4">';
						$html_archivo_recepcion .= '<table id="table_delete_nombre_archivo_recepcion" class="table_delete"><thead><tr><th></th></tr></thead>';
						$html_archivo_recepcion .= '<tbody><tr><td class="option text-center">';
						$html_archivo_recepcion .= anchor(get_uri("environmental_records/download_file/".$data_row_id."/nombre_archivo_recepcion"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));			
						$html_archivo_recepcion .= '</td>';
						$html_archivo_recepcion .= '</tr>';
						$html_archivo_recepcion .= '</thead>';
						$html_archivo_recepcion .= '</table>';
						$html_archivo_recepcion .= '</div>';
						
						$view_data['html_archivo_recepcion'] = $html_archivo_recepcion;
						
					} else {
						$html_archivo_recepcion .= '-';
						$view_data['html_archivo_recepcion'] = $html_archivo_recepcion;
					}
					
				} else {
					$html_archivo_recepcion .= '-';
					$view_data['html_archivo_recepcion'] = $html_archivo_recepcion;
				}
			}
			
			if($view_data['form_info']->flujo == "Consumo"){

				$formulario_unidad = json_decode($view_data['form_info']->unidad, true);
				$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
				$campo_unidad = $formulario_unidad["nombre_unidad"];
				
				$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
				$nombre_tipo_unidad = $tipo_unidad->nombre;
				
				$nombre_unidad = $unidad->nombre;
				$label_unidad = $campo_unidad . " (" . $nombre_tipo_unidad. ")";
				
				$view_data['label_unidad'] = $label_unidad;
				$view_data['nombre_unidad'] = $nombre_unidad;
				$view_data['unidad_residuo'] = $datos["unidad_residuo"];
				
				if($datos["type_of_origin"] == "1"){
				
					$type_of_origin = $this->EC_Types_of_origin_matter_model->get_one_where(array("id"=>$datos["type_of_origin_matter"], "deleted" => 0));
					if(isset($datos['type_of_origin'])){
						if($datos['type_of_origin_matter'] == $type_of_origin->id){
							$view_data['type_of_origin'] = lang($type_of_origin->nombre);
						}
					}else{
						$view_data['type_of_origin'] = "-";
					}
				
				} else {
					
					$type_of_origin = $this->EC_Types_of_origin_model->get_one_where(array("id"=>$datos["type_of_origin"], "deleted" => 0));
					if(isset($datos['type_of_origin'])){
						if($datos['type_of_origin'] == $type_of_origin->id){
							$view_data['type_of_origin'] = lang($type_of_origin->nombre);
						}
					}else{
						$view_data['type_of_origin'] = "-";
					}
					
				}

			}
			
			if($view_data['form_info']->flujo == "No Aplica"){
				
				$formulario_unidad = json_decode($view_data['form_info']->unidad, true);
				$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
				$campo_unidad = $formulario_unidad["nombre_unidad"];
				
				$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
				$nombre_tipo_unidad = $tipo_unidad->nombre;
				
				$nombre_unidad = $unidad->nombre;
				$label_unidad = $campo_unidad . " (" . $nombre_tipo_unidad. ")";
				
				$view_data['label_unidad'] = $label_unidad;
				$view_data['nombre_unidad'] = $nombre_unidad;
				$view_data['unidad_residuo'] = $datos["unidad_residuo"];
				
				$default_type = $this->EC_Types_no_apply_model->get_one_where(array("id"=>$datos["default_type"], "deleted" => 0));
				if(isset($datos['default_type'])){
					if($datos['default_type'] == $default_type->id){
						$view_data['default_type'] = lang($default_type->nombre);
					}
				}else{
					$view_data['default_type'] = "-";
				}
				
			}
			
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
		}
			
		$view_data['Environmental_records_controller'] = $this;
		$view_data['id_proyecto'] = $id_proyecto;
        $this->load->view('environmental_records/records/view', $view_data);
	}
	
    /* load client details view */

    function view($id_record) {
        
		//$this->access_only_allowed_members();

        if ($id_record) {
			
			// Filtro Categorias
			$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
			$categorias = $this->Categories_model->get_categories_of_material_of_form($id_record)->result();
			foreach($categorias as $categoria){
				$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $categoria->id, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
				$nombre_categoria = ($categoria_alias->alias) ? $categoria_alias->alias : $categoria->nombre;	
				$array_categorias[] = array("id" => $categoria->id, "text" => $nombre_categoria);
			}
			$view_data['categorias_dropdown'] = json_encode($array_categorias);
			
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
			$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
			$num_registros = count($registros);
            $record_info = $this->Forms_model->get_details($options)->row();
			$unidad_data_json_encode = $record_info->unidad;
			$unidad_data_json_decode = json_decode($unidad_data_json_encode, true);
			$nombre_unidad = $unidad_data_json_decode["nombre_unidad"];
			$unidad = $this->Unity_model->get_one($unidad_data_json_decode["unidad_id"]);
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
						$column_options = json_decode($column->opciones, true);
						$id_unidad = $column_options[0]["id_unidad"];
						$unidad = $this->Unity_model->get_one($id_unidad);
						$json_string .= ',' . '{"title":"' . $column->nombre . ' ('.$unidad->nombre.')' .  '", "class": "text-right dt-head-center"}';
					}else if($column->id_tipo_campo == 16){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
					}else{
						$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
					}
				}
				
				$string_columnas = "";
				if($record_info->flujo == "Residuo"){
					
					$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
					$string_columnas .= ',{"title":"'.$nombre_unidad.' ('.$unidad->nombre.')", "class": "text-right dt-head-center"}';
					$string_columnas .= ',{"title":"'.lang("type_of_treatment").'", "class": "text-left dt-head-center"}';
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("retirement_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("retirement_evidence").'", "class": "text-center w100 no_breakline option"}';
					$string_columnas .= ',{"title":"'.lang("reception_evidence").'", "class": "text-center w100 no_breakline option"}';
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					
				}else if(($record_info->flujo == "Consumo")||($record_info->flujo == "No Aplica")){
					
					$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
					$string_columnas .= ',{"title":"'.$nombre_unidad.' ('.$unidad->nombre.')", "class": "text-right dt-head-center"}';
					$string_columnas .= ',{"title":"'.lang("type").'", "class": "text-left dt-head-center"}';
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
				}else{
					$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					
				}
				
				$view_data["columnas"] = $string_columnas;
				
				//$view_data["columnas"] =  ',{"title":"' . lang("date_filed") .'", "class": " w100 no_breakline"},{"title":"' . lang("category") .'"}'. $this->Forms_model->get_fields_of_form_json($record_info->id) . ',{"title":"' . lang("created_date") . '", "class": " w100 no_breakline"},{"title":"' . lang("modified_date") . '", "class": " w100 no_breakline"}';
				
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
				
				if($record_info->flujo == "Residuo"){
					if($view_data["puede_eliminar"] != 3){ // 3 = Perfil Eliminar Ninguno 
						$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 7;
					} else {
						$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 6;
					}
				}else if(($record_info->flujo == "Consumo")||($record_info->flujo == "No Aplica")){
					if($view_data["puede_eliminar"] != 3){ // 3 = Perfil Eliminar Ninguno 
						$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 4;
					} else {
						$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 3;
					}
				}else{
					if($view_data["puede_eliminar"] != 3){ // 3 = Perfil Eliminar Ninguno 
					$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 3;
					} else {
					$view_data["cantidad_columnas_restantes"] = count($cantidad_columnas) + 2;
					}
				}
				
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

                $this->template->rander("environmental_records/records/index", $view_data);
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
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-fileConfirmation"));
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
				$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
			} else {
				
				//$html = '<div class="col-md-9">';
				$html .= '-';
				//$html .= '</div>';
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

		//$filename_prefix = $related_to . "_" . uniqid("file") . "-";
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

            $view_data["file_url"] = get_file_uri(get_general_file_path("client", $file_info->client_id) . $file_info->file_name);
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
     	download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/", $file_data);
		
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
		
		if($id_campo == "nombre_archivo_retiro"){
			$campo_nuevo = $this->load->view("includes/retirement_evidence_uploader", array(
				"upload_url" => get_uri("fields/upload_file"),
				"validation_url" =>get_uri("fields/validate_file")
			), true);
		}elseif($id_campo == "nombre_archivo_recepcion"){
			$campo_nuevo = $this->load->view("includes/reception_evidence_uploader", array(
				"upload_url" => get_uri("fields/upload_file"),
				"validation_url" =>get_uri("fields/validate_file")
			), true);
		}else{
			$campo_nuevo = $this->load->view("includes/form_file_uploader", array(
				"upload_url" =>get_uri("fields/upload_file"),
				"validation_url" =>get_uri("fields/validate_file"),
				"html_name" => $field_info->html_name,
				"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
				"id_campo" => $id_campo
			),
			true);
		}
		
		/*
		if(file_exists($file_path)) {
			
			if(!$archivo_obligatorio){
				delete_file_from_directory($file_path);
			}			
			if($save_id){
				echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
			} else {
				echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
			}
        }else{
			if($save_id){
				echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'id_campo' => $id_campo));
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