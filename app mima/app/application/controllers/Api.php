<?php
set_time_limit(200);

//ini_set('LimitRequestBody', 100024000);
//ini_set('post_max_size', 100024000);
//ini_set('upload_max_filesize', 100024000);
/*if (!defined('BASEPATH'))
    exit('No direct script access allowed');*/
    //header('Access-Control-Allow-Origin: *');

require(APPPATH.'/libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();

        //check permission to access this module
        //$this->init_permission_checker("client");
    }

    /* load clients list view */

    public function index() {
        /*$this->access_only_allowed_members();
        $this->template->rander("clients/index");*/
	}
	
	// ENDPOINT LOGIN - POST
    public function login_post() {
        $user = $this->post('email');
        $password = $this->post('password');

		$valido = $this->Users_model->authenticate($user, $password);
		$existing_user = $this->Users_model->is_email_exists($user);
		
		if($valido){
			$token = $this->generate_token();
			$respuesta = array("token" => $token, "message" => "Se ha logueado Correctamente", "success" => true, "user_id" => $existing_user->id);
			$users_api_session_row = array("user_id" => $existing_user->id, "token" => $token, "login_date" => get_current_utc_time());
			$save_id = $this->Api_model->save($users_api_session_row);
		}else{
			$respuesta = array("message" => "Fallo de autenticidad!", "success" => false);
		}

        return $this->response($respuesta);
	}
	
	// ENDPOINT CONFIG - GET
	public function config_get() {
		//var_dump(scandir('/tmp/'));
		//$token = $this->input->get_request_header('auth', true);
		$token = $this->get('Authorization');
		
		// VÁLIDO SI VIENE EL TOKEN
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}
		
		// VÁLIDO SI EXISTE UN USUARIO ASOCIADO AL TOKEN
		$existe_token = $this->Api_model->get_one_where(array("token" => $token, "deleted" => 0));
		if(!$existe_token->id){
			return $this->response(array("message" => "Fallo de autenticidad!", "success" => false));
			exit();
		}
		
		$id_usuario_cliente_token = $existe_token->user_id;
		$usuario_cliente = $this->Users_model->get_one_where(array("id" => $id_usuario_cliente_token, "deleted" => 0));
		$id_usuario_cliente = $usuario_cliente->id;
		$id_cliente = $usuario_cliente->client_id;

		$array_final = array();
		
		// TRAIGO INFORMACION DE LA CUENTA PARA EL USUARIO
		$array_final["user_info"] = array(
			"username" => $usuario_cliente->email,
			"avatar" => get_avatar($usuario_cliente->image),
			"first_name" => $usuario_cliente->first_name,
			"last_name" => $usuario_cliente->last_name
		);
		
		// TRAIGO INFORMACION DE LA ENTIDAD CLIENTE PARA LOS ESTILOS
		$info_cliente = $this->Clients_model->get_one_where(array("id" => $id_cliente, "deleted" => 0));
		$color_sitio = $info_cliente->color_sitio;
		$logo_sitio = $info_cliente->logo ? get_file_uri("files/mimasoft_files/client_".$id_cliente."/".$info_cliente->logo.".png"): get_file_uri("files/system/default-site-logo.png");
		
		// OBJETO STYLES
		$array_final["styles"] = array(
			"color" => $color_sitio,
			"logo" => $logo_sitio,
		);
		
		// OBJETO TRANSLATE
		$array_final["translations"] = array(
			"labels" => array(
				"username" => "Email",
				"password" => "Contraseña",
				"login_success" => "Se ha logueado correctamente.",
				"required_fields" => "Faltan rellenar campos obligatorios",
				"projects" => "Proyectos",
				"startdate" => "Inicio",
				"enddate" => "Término",
				"records" => "Registros",
				//"environmental_records" => "Registros Ambientales",
				//"feeders" => "Mantenedoras",
				//"other_records" => "Otros Registros",
				"open_file" => "Ver archivo",
				"no_more_records" => "No hay más registros",
				"delete_record" => "Eliminar dato",
				"delete_record_warning" => lang('delete_confirmation_message'),
				"offline_save" => "No se han podido enviar los datos, pero se han guardado en la memoria del teléfono. Cuando tengas conexión a internet, abre la App para sincronizar los datos guardados.",
				
			),
			"buttons" => array(
				"login" => "Ingresar",
				"logout" => "Cerrar sesión",
				"restore_password" => "Recuperar contraseña",
				"back" => "Volver",
				"add" => "Añadir",
				"edit" => "Editar",
				"delete" => "Borrar",
				"load_more" => "Cargar más",
				"photo" => "Foto",
				"save" => "Guardar",
				"confirm" => "Confirmar",
				"sync" => "Sincronizar",
				"sync_ready" => "Listo",
				"sync_error" => "Error",
			),
		
		);
		
		// OBTENER LOS DATOS DE LOS PROYECTOS EN LOS QUE ES MIEMBRO
		$proyectos_de_usuario = $this->Api_model->get_projects_of_member($id_usuario_cliente)->result();
		
		$array_projects = array();
		foreach($proyectos_de_usuario as $proyecto){

			// OBTENER LOS FORMULARIOS DEL PROYECTO
			$array_forms = array();
			$formularios_de_proyecto = $this->Forms_model->get_details(array('id_proyecto' => $proyecto->id))->result();
			foreach($formularios_de_proyecto as $formulario){
				
				// CONTAR REGISTROS DEL FORMULARIO
				$count_records = $this->Form_values_model->get_forms_values_of_form($formulario->id)->num_rows();
				if($count_records == 1){
					$count_records_label = "1 Registro";
				}else{
					$count_records_label = $count_records." Registros";
				}
				
				$id_tipo_formulario = $formulario->id_tipo_formulario;
				$icon = $formulario->icono ? base_url("assets/images/icons/".$formulario->icono) : base_url("assets/images/icons/empty.png");
				$flow = $formulario->flujo ? $formulario->flujo : NULL;
				
				// OBTENER LOS CAMPOS DEL FORMULARIO
				$array_fields = array();

				// DETECTAR TIPO DE FORMULARIO Y SI ES R.A, DETECTAR FLUJO
				if($id_tipo_formulario == 1){

					// FECHA DE REGISTRO
					$array_fields[] = array(
						"id" => "date_filed",
						"label" => lang('date_filed'),
						"name" => "date_filed",
						"type" => "datepicker",
						"required" => true,
						"value" => "",
						"disabled" => false
					);

					// CATEGORIA
					$categorias = $this->get_categories_of_form($formulario);

					$disabled = false;
					$value = "";
					if(count($categorias) == 2){
						$disabled = true;
						$value = $categorias[1]["value"];
					}

					$array_fields[] = array(
						"id" => "category",
						"label" => lang('category'),
						"name" => "category",
						"type" => "select",
						"required" => true,
						"value" => $value,
						"disabled" => $disabled,
						"options" => $categorias
					);
					
					$campo_unidad = $formulario->unidad;
					$unidad_decoded = json_decode($campo_unidad, true);
					$nombre_unidad = $unidad_decoded["nombre_unidad"];
					$data_unidad = $this->Unity_model->get_one($unidad_decoded["unidad_id"]);
					$tipo_unidad = $this->Unity_type_model->get_one($unidad_decoded['tipo_unidad_id']);
					$unidad = $data_unidad->nombre;
					
					$array_fields[] = array(
						"id" => "waste_unit",
						"label" => $nombre_unidad." (".$tipo_unidad->nombre.")",
						"name" => "waste_unit",
						"type" => "unit",
						"required" => true,
						"value" => "",
						"disabled" => false,
						"min" => 0,
						"step" => "any",
						"append" => ' '.$unidad
					);

					if($flow == "Residuo"){
						$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result_array();
						$array_tipo_tratamiento = array(array("value" => "", "text" => "-"));
						foreach($tipos_tratamientos as $tipo_tratamiento){
							$array_tipo_tratamiento[] = array("value" => $tipo_tratamiento["id"],
							"text" => $tipo_tratamiento["nombre"]);
						}
						$array_fields[] = array(
							"id" => "type_of_treatment",
							"label" => lang('type_of_treatment'),
							"name" => "type_of_treatment",
							"type" => "select",
							"required" => true,
							"value" => "",
							"disabled" => false,
							"options" => $array_tipo_tratamiento
						);
						
						$array_fields[] = array(
							"id" => "retirement_date",
							"label" => lang('retirement_date'),
							"name" => "retirement_date",
							"type" => "datepicker",
							"required" => true,
							"value" => "",
							"disabled" => false,
						);
						
						
					}

				}
				
				// MOSTRAR CAMPOS DINAMICOS ASOCIADOS AL FORMULARIO
				$campos_de_formulario = $this->Fields_model->get_fields_of_form($formulario->id)->result();
				foreach($campos_de_formulario as $campo){
					$array_fields[] = $this->get_field_object($campo);
				}
				
				// MOSTRAR CAMPOS DE ARCHIVO DE R.A DE FLUJO RESIDUO
				if($id_tipo_formulario == 1 && $flow == "Residuo"){
					$array_fields[] = array(
						"id" => "retirement_evidence",
						"label" => lang('retirement_evidence'),
						"name" => "retirement_evidence",
						"type" => "file",
						"required" => false,
						"valid" => $this->files_format_to_html_format(get_setting("accepted_file_formats")),
						"value" => "",
						"disabled" => false,
					);
					
					$array_fields[] = array(
						"id" => "reception_evidence",
						"label" => lang('reception_evidence'),
						"name" => "reception_evidence",
						"type" => "file",
						"required" => false,
						"valid" => $this->files_format_to_html_format(get_setting("accepted_file_formats")),
						"value" => "",
						"disabled" => false,
					);
				}

				// OBTENER LOS ULTIMOS 5 VALORES DEL FORMULARIO
				$array_values = array();
				$valores_de_formulario = $this->Api_model->get_values_of_form(array("id_formulario" => $formulario->id))->result();
				foreach($valores_de_formulario as $valor){
					//var_dump($valor);

					$datos_decoded = json_decode($valor->datos);
					$array_valores = array();
					foreach($datos_decoded as $id => $value){
						if($id == "fecha"){
							$array_valores["date_filed"] = $value;
						}elseif($id == "id_categoria"){
							$array_valores["category"] = $value;
						}elseif($id == "tipo_tratamiento"){
							$array_valores["type_of_treatment"] = $value;
						}elseif($id == "unidad_residuo"){
							$array_valores["waste_unit"] = $value;
						}elseif($id == "fecha_retiro"){
							$array_valores["retirement_date"] = $value;
						}elseif($id == "nombre_archivo_retiro"){
							$array_valores["retirement_evidence"] = get_uri("api/download_file/".$valor->id."/nombre_archivo_retiro");
							//$array_valores["retirement_evidence"] = $value;
						}elseif($id == "nombre_archivo_recepcion"){
							$array_valores["reception_evidence"] = get_uri("api/download_file/".$valor->id."/nombre_archivo_recepcion");
							//$array_valores["reception_evidence"] = $value;
						}elseif($id == "tipo_unidad"){
							
						}elseif($id == "unidad"){
							
						}else{
							
							// COMPROBAR SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO, HTML O DIVIDER
							$campo_info = $this->Fields_model->get_one($id);
							if($campo_info->id){
								
								if($campo_info->id_tipo_campo == 10){
									if($value){
										$array_valores[$id] = get_uri("api/download_file/".$valor->id."/".$id);
									}else{
										$array_valores[$id] = $value;
									}
									
								}else if($campo_info->id_tipo_campo == 11){// html
									$array_valores[$id] = $campo_info->default_value;
								}else if($campo_info->id_tipo_campo == 12){// divider
									$array_valores[$id] = $campo_info->default_value;
								}else{
									$array_valores[$id] = $value;
								}
								
							}else{
								
							}
							
							
						}
						//$array_valores["test_file"] = get_uri("environmental_records/download_file/".$valor->id."/".$id);
						
					}
					//var_dump($valor);
					$array_values[] = array(
						"id" => (int)$valor->id,
						"data" => $array_valores,
					);
				}
				
				// OBJETO FORMS
				$array_forms[] = array(
					"id" => (int) $formulario->id,
					"title" => $formulario->nombre,
					"description" => $formulario->descripcion,
					"icon" => $icon,
					"category_id" => (int) $formulario->id_tipo_formulario,
					"flow" => $flow,
					"count_records_label" => $count_records_label,
					"fields" => $array_fields,
					"values" => $array_values
				);
			}

			// OBJETO PROJECTS
			$icon = $proyecto->icono ? base_url("assets/images/icons/".$proyecto->icono) : base_url("assets/images/icons/empty.png");
			
			//FORMATO DE FECHA A NIVEL DE PROYECTO
			$general_settings = $this->General_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $proyecto->id, "deleted" => 0));
			$formato_fecha = $this->system_date_format_to_angular_format($general_settings->date_format);
			
			$array_projects[] = array(
				"id" => (int) $proyecto->id,
				"title" => $proyecto->title,
				"description" => $proyecto->description,
				"icon" => $icon,
				"start_date" => get_date_format($proyecto->start_date, $proyecto->id),
				"end_date" => get_date_format($proyecto->deadline, $proyecto->id),
				//"start_date" => $proyecto->start_date,
				//"end_date" => $proyecto->deadline,
				"date_format" => $formato_fecha,
				//"date_format" => 'dd/MM/yyyy',
				"forms" => $array_forms,
			);
		}
		
		$array_final["projects"] = $array_projects;
		
		// OBJETO FORM CATEGORIES
		$array_final["form_categories"] = array(
			array("id" => 1, "title" => lang("environmental_records")),
			array("id" => 2, "title" => lang("feeders")),
			array("id" => 3, "title" => lang("other_records"))
		);
		
		return $this->response(array("data" => $array_final, "success" => true));

	}
	
	// ENDPOINTS SAVE/UPDATE - POST
	public function form_post($id_formulario, $tipo, $id_elemento = NULL) {
		//var_dump($_FILES);
		//var_dump($this->post());
		
		$token = $this->input->get_request_header('Authorization', true);
		$array_errores = array();
		
		// VÁLIDO SI VIENE EL TOKEN
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}
		
		// VÁLIDO SI EXISTE UN USUARIO ASOCIADO AL TOKEN
		$existe_token = $this->Api_model->get_one_where(array("token" => $token, "deleted" => 0));
		if(!$existe_token->id){
			return $this->response(array("message" => "Fallo de autenticidad!", "success" => false));
			exit();
		}
		
		$id_usuario_cliente_token = $existe_token->user_id;
		$usuario_cliente = $this->Users_model->get_one_where(array("id" => $id_usuario_cliente_token, "deleted" => 0));
		$id_usuario_cliente = $usuario_cliente->id;
		$id_cliente = $usuario_cliente->client_id;

		// RECIBO EL ID DE FORMULARIO POR PARAMETRO
		$form_info = $this->Forms_model->get_one($id_formulario);

		// VÁLIDO SI EL FORMULARIO NO SE HA BORRADO
		if(!$form_info->id){
			return $this->response(array("message" => "El formulario no existe!", "success" => false));
			exit();
		}
		
		$array_datos = array();
		
		// PREGUNTO SI ES INGRESO O EDICION
		if($tipo == "save"){
			
			// PREGUNTO SI ES REGISTRO AMBIENTAL
			if($form_info->id_tipo_formulario == 1){
				
				// RECIBO FECHA DE REGISTRO Y CATEGORIA
				$date_filed = $this->post('date_filed');
				$category = $this->post('category');
				
				// VALIDO SI NO VIENEN VACIAS
				if(!$date_filed){$array_errores[] = array("date_filed" => lang("field_required"));}
				else{
					if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_filed)){
						$array_errores[] = array("date_filed" => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
					}
				}
				
				if(!$category){
					$array_errores[] = array(
						"category" => lang("field_required"),
					);
				}
				
				$array_datos['fecha'] = $date_filed;
				$array_datos['id_categoria'] = $category;
				
				// PREGUNTO SI FLUJO ES RESIDUO
				if($form_info->flujo == "Residuo"){
					$type_of_treatment = $this->post('type_of_treatment');
					$waste_unit = $this->post('waste_unit');
					$retirement_date = $this->post('retirement_date');
					$retirement_evidence = $this->post('retirement_evidence');
					$reception_evidence = $this->post('reception_evidence');
				}else{
					$waste_unit = $this->post('waste_unit');
					//$tipo_unidad_residuo = $this->input->post('tipo_unidad_residuo');
					//$unidad_residuo = $this->input->post('unidad_residuo');
				}
	
			}
			
			$array_files = array();
			// CONSULTO CAMPOS DINAMICOS DEL FORMULARIO
			$columnas = $this->Forms_model->get_fields_of_form($id_formulario)->result();
			
			foreach($columnas as $columna){
				
				$obligatorio = $columna->obligatorio;
				$deshabilitado = $columna->habilitado;
				
				if($columna->id_tipo_campo == 3){// SI EL CAMPO DEL BUCLE ES DE TIPO NÚMERO
					$array_datos[$columna->id] = $this->post($columna->html_name);
					if($obligatorio && !$this->post($columna->html_name)){
						$array_errores[] = array($columna->html_name => lang("field_required"));
					}
					if(!is_null($this->post($columna->html_name)) && !is_numeric($this->post($columna->html_name))){
						$array_errores[] = array($columna->html_name => "El valor debe ser un número válido.");
					}
					
				}elseif($columna->id_tipo_campo == 4){// SI EL CAMPO DEL BUCLE ES DE TIPO FECHA
					$array_datos[$columna->id] = $this->post($columna->html_name);
					if($obligatorio && !$this->post($columna->html_name)){
						$array_errores[] = array($columna->html_name => lang("field_required"));
					}
					if(!is_null($this->post($columna->html_name)) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($columna->html_name))){
						$array_errores[] = array($columna->html_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
					}
					
					
				}elseif($columna->id_tipo_campo == 5){// SI EL CAMPO DEL BUCLE ES DE TIPO PERIODO
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id] = array(
						"start_date" => $this->post($start_name),
						"end_date" => $this->post($end_name)
					);
					
					if($obligatorio){
						if(!$this->post($start_name)){
							$array_errores[] = array($start_name => lang("field_required"));
						}
						if(!$this->post($end_name)){
							$array_errores[] = array($end_name => lang("field_required"));
						}
					}
					
					if(!is_null($this->post($start_name)) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($start_name))){
						$array_errores[] = array($start_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
					}
					if(!is_null($this->post($end_name)) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($end_name))){
						$array_errores[] = array($end_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
					}
					
					
				}else if($columna->id_tipo_campo == 10){// SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO
					//$array_datos[$columna->id] = $this->post($columna->html_name);
					//$array_files[$columna->id] = $this->post($columna->html_name);

					/*$datos_archivo = $_FILES[$columna->html_name];
					$nombre_archivo = $_FILES[$columna->html_name]["name"];
					
					$array_datos[$columna->id] = $nombre_archivo;
					$array_files[$columna->html_name] = $nombre_archivo;

					//get_setting("accepted_file_formats")
					if($obligatorio && !$datos_archivo){
						
						$array_errores[] = array(
							$columna->html_name => lang("field_required"),
						);
					}*/
					
					if($this->post($columna->html_name)){
						
						$datos_archivo = (array)$this->post($columna->html_name);
						
						if($datos_archivo["from"] == "app-camera"){
							
							//$nombre_archivo = "camera-picture-".$columna->id.'.jpg';
							$nombre_archivo = "camera-picture.jpg";
							$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
							$array_datos[$columna->id] = $nombre_archivo_prefix;
							$array_files[$columna->html_name] = $nombre_archivo_prefix;
							
							$array_base64 = $datos_archivo["base64"];
							//$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($array_base64);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
												
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									$columna->html_name => lang("invalid_file_type").' (name: '.$nombre_archivo.')',
									//"test" => $datos_archivo["base64"]
								);
							}
							
						}else{
							
							$nombre_archivo = $datos_archivo["name"];
							$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
							$array_datos[$columna->id] = $nombre_archivo_prefix;
							$array_files[$columna->html_name] = $nombre_archivo_prefix;
							
							$array_base64 = explode('base64,', $datos_archivo["base64"]);
							$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($base_sin_data);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
							
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									$columna->html_name => lang("invalid_file_type"),
									//"test" => $datos_archivo["from"]
								);
							}
							
						}
						
						
					}

					//get_setting("accepted_file_formats")
					if($obligatorio && !$datos_archivo){
						$array_errores[] = array(
							$columna->html_name => lang("field_required"),
						);
					}
					
				}elseif($columna->id_tipo_campo == 13){// SI EL CAMPO DEL BUCLE ES DE TIPO CORREO
					$array_datos[$columna->id] = $this->post($columna->html_name);
					if($obligatorio && !$this->post($columna->html_name)){
						$array_errores[] = array($columna->html_name => lang("field_required"));
					}
					if(!is_null($this->post($columna->html_name)) && !(bool)filter_var($this->post($columna->html_name), FILTER_VALIDATE_EMAIL)){
						$array_errores[] = array($columna->html_name => "El valor debe ser un correo válido.");
					}
					
				}elseif($columna->id_tipo_campo == 14){// SI EL CAMPO DEL BUCLE ES DE TIPO HORA
					$array_datos[$columna->id] = $this->post($columna->html_name);
					if($obligatorio && !$this->post($columna->html_name)){
						$array_errores[] = array($columna->html_name => lang("field_required"));
					}
					//if($this->post($columna->html_name) != "" && !preg_match('/\d{2}:\d{2} (AM|PM)/', $this->post($columna->html_name))){
					if(!is_null($this->post($columna->html_name)) && !preg_match('/\d{2}:\d{2}/', $this->post($columna->html_name))){
						$array_errores[] = array($columna->html_name => "La hora debe tener formato HH:MM AM|PM.");
					}
				
				}elseif($columna->id_tipo_campo == 15){// SI EL CAMPO DEL BUCLE ES DE TIPO UNIDAD
					$array_datos[$columna->id] = $this->post($columna->html_name);
					if($obligatorio && !$this->post($columna->html_name)){
						$array_errores[] = array($columna->html_name => lang("field_required"));
					}
					if(!is_null($this->post($columna->html_name)) && !is_numeric($this->post($columna->html_name))){
						$array_errores[] = array($columna->html_name => "El valor debe ser un número válido.");
					}
				
				}else{
					$array_datos[$columna->id] = $this->post($columna->html_name);
					//$array_datos[$columna->id] = $categories;
					if($obligatorio && !$this->post($columna->html_name)){
						
						$array_errores[] = array(
							$columna->html_name => lang("field_required"),
						);
					}
					
				}
			}
			
			//if((isset($type_of_treatment))&&(isset($waste_unit))){
			if($form_info->flujo == "Residuo"){
				
				$array_datos['tipo_tratamiento'] = $type_of_treatment;
				$array_datos['unidad_residuo'] = $waste_unit;
				
				if(!$type_of_treatment){
					$array_errores[] = array(
						"type_of_treatment" => lang("field_required"),
					);
				}else{
					if((int)$type_of_treatment == 0){
						$array_errores[] = array("type_of_treatment" => "El valor debe ser un número entero válido.");
					}
				}
				
				if(!$waste_unit){
					$array_errores[] = array(
						"waste_unit" => lang("field_required"),
					);
				}else{
					if(!is_numeric($waste_unit)){
						$array_errores[] = array("waste_unit" => "El valor debe ser un número válido.");
					}
				}
				
				if($retirement_date != ""){
					$array_datos['fecha_retiro'] = $retirement_date;
					if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $retirement_date)){
						$array_errores[] = array("retirement_date" => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
					}
				}else{
					$array_datos['fecha_retiro'] = NULL;
				}
				
				$datos_unidad_fija = json_decode($form_info->unidad);
				$id_tipo_unidad = $datos_unidad_fija->tipo_unidad_id;
				$id_unidad = $datos_unidad_fija->unidad_id;
				$tipo_unidad_info = $this->Unity_type_model->get_one($id_tipo_unidad);
				$unidad_info = $this->Unity_model->get_one($id_unidad);
				
				$array_datos['tipo_unidad'] = $tipo_unidad_info->nombre;
				$array_datos['unidad'] = $unidad_info->nombre;
	
			}else{
				
				$datos_unidad_fija = json_decode($form_info->unidad);
				$id_tipo_unidad = $datos_unidad_fija->tipo_unidad_id;
				$id_unidad = $datos_unidad_fija->unidad_id;
				$tipo_unidad_info = $this->Unity_type_model->get_one($id_tipo_unidad);
				$unidad_info = $this->Unity_model->get_one($id_unidad);
				
				$array_datos['unidad_residuo'] = $waste_unit;
				$array_datos['tipo_unidad'] = $tipo_unidad_info->nombre;
				$array_datos['unidad'] = $unidad_info->nombre;
			}
	
			//if(isset($_FILES['retirement_evidence'])){
			if($this->post('retirement_evidence')){
				
				$datos_archivo = (array)$this->post('retirement_evidence');
				
				if($datos_archivo["from"] == "app-camera"){
							
					//$nombre_archivo = "camera-picture-retirement-evidence.jpg";
					$nombre_archivo = "camera-picture.jpg";
					$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
					$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
					$array_files['retirement_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = $datos_archivo["base64"];
					//$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($array_base64);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_errores[] = array(
							'retirement_evidence' => lang("invalid_file_type")
						);
					}
					
				}else{
					
					$nombre_archivo = $datos_archivo["name"];
					$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
					$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
					$array_files['retirement_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = explode('base64,', $datos_archivo["base64"]);
					$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($base_sin_data);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_errores[] = array(
							'retirement_evidence' => lang("invalid_file_type"),
						);
					}
				}
				
			}
			
			//if(isset($_FILES['reception_evidence'])){
			if($this->post('reception_evidence')){
				
				$datos_archivo = (array)$this->post('reception_evidence');
							
				if($datos_archivo["from"] == "app-camera"){
							
					//$nombre_archivo = "camera-picture-reception-evidence.jpg";
					$nombre_archivo = "camera-picture.jpg";
					$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
					$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
					$array_files['reception_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = $datos_archivo["base64"];
					//$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($array_base64);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
										
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_errores[] = array(
							'reception_evidence' => lang("invalid_file_type"),
						);
					}
					
				}else{
				
					$nombre_archivo = $datos_archivo["name"];
					$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
					$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
					$array_files['reception_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = explode('base64,', $datos_archivo["base64"]);
					$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($base_sin_data);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_errores[] = array(
							'reception_evidence' => lang("invalid_file_type"),
						);
					}
				}
				
			}
			
			$json_datos = json_encode($array_datos);
			
			$options = array("id_formulario" => $id_formulario/*, "id_proyecto" => $id_proyecto*/);
			$record_info = $this->Form_rel_project_model->get_details($options)->row();
			if($record_info){
				$id_formulario_rel_proyecto = $record_info->id;
				$id_proyecto = $record_info->id_proyecto;
			}
			
			$data = array(
				"id_formulario_rel_proyecto" => $id_formulario_rel_proyecto,
				"datos" => $json_datos, 
				"created_by" => $id_usuario_cliente,
				"created" => get_current_utc_time()
			);

			if(count($array_errores) == 0){
				$save_id = $this->Form_values_model->save($data);
			}
			if ($save_id) {
				$respuesta = array("message" => "Se han ingresado los datos correctamente.", "success" => true);
				
				foreach($array_files as $field_name => $nombre_archivo){
					//$nombre_real_archivo = remove_file_prefix($nombre_archivo);
					$this->api_upload_file_to_temp($nombre_archivo);
					move_temp_file($nombre_archivo, "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$save_id."/", "", "", $nombre_archivo);
				}
				

			}else{
				$respuesta = array("message" => "Ocurrió un error al intentar ingresar los datos.", "success" => false, "details" => $array_errores);
				
			}
			
			
		}elseif($tipo == "update"){
			
			if($id_elemento){
				
				// PREGUNTO SI ES REGISTRO AMBIENTAL
				if($form_info->id_tipo_formulario == 1){
					
					// RECIBO FECHA DE REGISTRO Y CATEGORIA
					$date_filed = $this->post('date_filed');
					$category = $this->post('category');
					
					// VALIDO SI NO VIENEN VACIAS
					if(!$date_filed){$array_errores[] = array("date_filed" => lang("field_required"));}
					else{
						if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_filed)){
							$array_errores[] = array("date_filed" => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
						}
					}
					if(!$category){
						$array_errores[] = array(
							"category" => lang("field_required"),
						);
					}
					
					$array_datos['fecha'] = $date_filed;
					$array_datos['id_categoria'] = $category;
					
					// PREGUNTO SI FLUJO ES RESIDUO
					if($form_info->flujo == "Residuo"){
						$type_of_treatment = $this->post('type_of_treatment');
						$waste_unit = $this->post('waste_unit');
						$retirement_date = $this->post('retirement_date');
						$retirement_evidence = $this->post('retirement_evidence');
						//var_dump($this->post());
						//var_dump($_FILES);
						$reception_evidence = $this->post('reception_evidence');
					}else{
						$waste_unit = $this->post('waste_unit');
						$tipo_unidad_residuo = $this->input->post('tipo_unidad_residuo');
						$unidad_residuo = $this->input->post('unidad_residuo');
					}
		
				}
				
				$array_files = array();
				// CONSULTO CAMPOS DINAMICOS DEL FORMULARIO
				$columnas = $this->Forms_model->get_fields_of_form($id_formulario)->result();
				$elemento = $this->Form_values_model->get_one($id_elemento);
				$datos_elemento = json_decode($elemento->datos, true);
				
				foreach($columnas as $columna){
					
					$obligatorio = $columna->obligatorio;
					$deshabilitado = $columna->habilitado;
					
					if($columna->id_tipo_campo == 3){// SI EL CAMPO DEL BUCLE ES DE TIPO NÚMERO
						$array_datos[$columna->id] = $this->post($columna->html_name);
						if($obligatorio && !$this->post($columna->html_name)){
							$array_errores[] = array($columna->html_name => lang("field_required"));
						}
						if($this->post($columna->html_name) != "" && !is_numeric($this->post($columna->html_name))){
							$array_errores[] = array($columna->html_name => "El valor debe ser un número válido.");
						}
						
					}elseif($columna->id_tipo_campo == 4){// SI EL CAMPO DEL BUCLE ES DE TIPO FECHA
					
						$array_datos[$columna->id] = $this->post($columna->html_name);
						if($obligatorio && !$this->post($columna->html_name)){
							$array_errores[] = array($columna->html_name => lang("field_required"));
						}
						if($this->post($columna->html_name) != "" && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($columna->html_name))){
							$array_errores[] = array($columna->html_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
						}
					
					
					}elseif($columna->id_tipo_campo == 5){// SI EL CAMPO DEL BUCLE ES DE TIPO PERIODO
						$json_name = $columna->html_name;
						$array_name = json_decode($json_name, true);
						$start_name = $array_name["start_name"];
						$end_name = $array_name["end_name"];
						
						$array_datos[$columna->id] = array(
							"start_date" => ($this->post($start_name) == "null")?"":$this->post($start_name),
							"end_date" => ($this->post($end_name) == "null")?"":$this->post($end_name),
						);
						
						if($obligatorio){
							//if(!$this->post($start_name)){
							if($this->post($start_name) == ""){
								$array_errores[] = array($start_name => lang("field_required"));
							}
							//if(!$this->post($end_name)){
							if($this->post($end_name) == ""){
								$array_errores[] = array($end_name => lang("field_required"));
							}
						}
						
						if($this->post($start_name) != "" && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($start_name))){
							$array_errores[] = array($start_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
						}
						if($this->post($end_name) != "" && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->post($end_name))){
							$array_errores[] = array($end_name => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
						}
						
					}else if($columna->id_tipo_campo == 10){// SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO
						
						//var_dump((bool)filter_var($this->post($columna->html_name), FILTER_VALIDATE_URL));exit();
						if((bool)filter_var($this->post($columna->html_name), FILTER_VALIDATE_URL)){
							// SI RECIBE LA URL DE DESCARGA ES POR QUE NO SE MODIFICA EL ARCHIVO
							$nombre_archivo_anterior = $datos_elemento[$columna->id];
							$array_datos[$columna->id] = $nombre_archivo_anterior;
						}elseif($this->post($columna->html_name) == 'undefined'){
							// SI RECIBE UNDEFINED ES POR QUE SE BORRA EL ARCHIVO
							$array_datos[$columna->id] = "";
						}elseif(is_array($this->post($columna->html_name))){
							
							// SI RECIBE UN ARREGLO ES POR QUE SE REEMPLAZA EL ARCHIVO CON OTRO
							$datos_archivo = (array)$this->post($columna->html_name);
							
							if($datos_archivo["from"] == "app-camera"){
							
								$nombre_archivo = "camera-picture.jpg";
								$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
								$array_datos[$columna->id] = $nombre_archivo_prefix;
								$array_files[$columna->html_name] = $nombre_archivo_prefix;
								
								$array_base64 = $datos_archivo["base64"];
								//$base_sin_data = $array_base64[1];
								$decoded_file = base64_decode($array_base64);
								$tmp_folder = sys_get_temp_dir();
								file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
													
								if(!is_valid_file_to_upload($nombre_archivo)){
									$array_errores[] = array(
										//$columna->html_name => lang("invalid_file_type").' (name: '.$nombre_archivo.')',
										$columna->html_name => lang("invalid_file_type"),
										//"test" => $datos_archivo["base64"]
									);
								}
								
							}else{
								
								$nombre_archivo = $datos_archivo["name"];
								$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
								$array_datos[$columna->id] = $nombre_archivo_prefix;
								$array_files[$columna->html_name] = $nombre_archivo_prefix;
								
								$array_base64 = explode('base64,', $datos_archivo["base64"]);
								$base_sin_data = $array_base64[1];
								$decoded_file = base64_decode($base_sin_data);
								$tmp_folder = sys_get_temp_dir();
								file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
								
								if(!is_valid_file_to_upload($nombre_archivo)){
									$array_errores[] = array(
										$columna->html_name => lang("invalid_file_type"),
										//"test" => $datos_archivo["from"]
									);
								}
								
							}
							
						}
						
						
					}elseif($columna->id_tipo_campo == 13){// SI EL CAMPO DEL BUCLE ES DE TIPO CORREO
						$array_datos[$columna->id] = $this->post($columna->html_name);
						if($obligatorio && !$this->post($columna->html_name)){
							$array_errores[] = array($columna->html_name => lang("field_required"));
						}
						if($this->post($columna->html_name) != "" && !(bool)filter_var($this->post($columna->html_name), FILTER_VALIDATE_EMAIL)){
							$array_errores[] = array($columna->html_name => "El valor debe ser un correo válido.");
						}
						
					}elseif($columna->id_tipo_campo == 14){// SI EL CAMPO DEL BUCLE ES DE TIPO HORA
						$array_datos[$columna->id] = $this->post($columna->html_name);
						if($obligatorio && !$this->post($columna->html_name)){
							$array_errores[] = array($columna->html_name => lang("field_required"));
						}
						//if($this->post($columna->html_name) != "" && !preg_match('/\d{2}:\d{2} (AM|PM)/', $this->post($columna->html_name))){
						if($this->post($columna->html_name) != "" && !preg_match('/\d{2}:\d{2}/', $this->post($columna->html_name))){
							$array_errores[] = array($columna->html_name => "La hora debe tener formato HH:MM AM|PM.");
						}
					
					}elseif($columna->id_tipo_campo == 15){// SI EL CAMPO DEL BUCLE ES DE TIPO UNIDAD
						//var_dump($this->post($columna->html_name));
						$array_datos[$columna->id] = ($this->post($columna->html_name) == "null")?"":$this->post($columna->html_name);
						if($obligatorio && !$this->post($columna->html_name)){
							$array_errores[] = array($columna->html_name => lang("field_required"));
						}
						if($this->post($columna->html_name) != "null" && !is_numeric($this->post($columna->html_name))){
							$array_errores[] = array($columna->html_name => "El valor debe ser un número válido.");
						}
						
					
					}else{
						$array_datos[$columna->id] = $this->post($columna->html_name);
						//$array_datos[$columna->id] = $categories;
						if($obligatorio && !$this->post($columna->html_name)){
							
							$array_errores[] = array(
								$columna->html_name => lang("field_required"),
							);
						}
						
					}
				}
				
				//if((isset($type_of_treatment))&&(isset($waste_unit))){
				if($form_info->flujo == "Residuo"){
					
					$array_datos['tipo_tratamiento'] = $type_of_treatment;
					$array_datos['unidad_residuo'] = $waste_unit;
					
					if(!$type_of_treatment){
						$array_errores[] = array(
							"type_of_treatment" => lang("field_required"),
						);
					}else{
						if((int)$type_of_treatment == 0){
							$array_errores[] = array("type_of_treatment" => "El valor debe ser un número entero válido.");
						}
					}
					
					if(!$waste_unit){
						$array_errores[] = array(
							"waste_unit" => lang("field_required"),
						);
					}else{
						if(!is_numeric($waste_unit)){
							$array_errores[] = array("waste_unit" => "El valor debe ser un número válido.");
						}
					}
					
					if($retirement_date != ""){
						$array_datos['fecha_retiro'] = $retirement_date;
						if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $retirement_date)){
							$array_errores[] = array("retirement_date" => "La fecha debe ser válida y bajo el formato YYYY-MM-DD");
						}
					}else{
						$array_datos['fecha_retiro'] = NULL;
					}
					
					$datos_unidad_fija = json_decode($form_info->unidad);
					$id_tipo_unidad = $datos_unidad_fija->tipo_unidad_id;
					$id_unidad = $datos_unidad_fija->unidad_id;
					$tipo_unidad_info = $this->Unity_type_model->get_one($id_tipo_unidad);
					$unidad_info = $this->Unity_model->get_one($id_unidad);
					
					$array_datos['tipo_unidad'] = $tipo_unidad_info->nombre;
					$array_datos['unidad'] = $unidad_info->nombre;
		
				}else{
					
					$datos_unidad_fija = json_decode($form_info->unidad);
					$id_tipo_unidad = $datos_unidad_fija->tipo_unidad_id;
					$id_unidad = $datos_unidad_fija->unidad_id;
					$tipo_unidad_info = $this->Unity_type_model->get_one($id_tipo_unidad);
					$unidad_info = $this->Unity_model->get_one($id_unidad);
					
					$array_datos['unidad_residuo'] = $waste_unit;
					$array_datos['tipo_unidad'] = $tipo_unidad_info->nombre;
					$array_datos['unidad'] = $unidad_info->nombre;
				}
				
				
				//if(isset($_FILES['retirement_evidence'])){
				if($this->post('retirement_evidence')){
					
					if((bool)filter_var($this->post('retirement_evidence'), FILTER_VALIDATE_URL)){
						// SI RECIBE LA URL DE DESCARGA ES POR QUE NO SE MODIFICA EL ARCHIVO
						$nombre_archivo_anterior = $datos_elemento['nombre_archivo_retiro'];
						$array_datos['nombre_archivo_retiro'] = $nombre_archivo_anterior;
					}elseif($this->post('retirement_evidence') == 'undefined'){
						// SI RECIBE UNDEFINED ES POR QUE SE BORRA EL ARCHIVO
						
					}elseif(is_array($this->post('retirement_evidence'))){
						// SI RECIBE UN ARREGLO ES POR QUE SE REEMPLAZA EL ARCHIVO CON OTRO
						
						$datos_archivo = (array)$this->post('retirement_evidence');
					
						if($datos_archivo["from"] == "app-camera"){
									
							//$nombre_archivo = "camera-picture-retirement-evidence.jpg";
							$nombre_archivo = "camera-picture.jpg";
							$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
							$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
							$array_files['retirement_evidence'] = $nombre_archivo_prefix;
							
							$array_base64 = $datos_archivo["base64"];
							//$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($array_base64);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
							
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									'retirement_evidence' => lang("invalid_file_type")
								);
							}
							
						}else{
							
							$nombre_archivo = $datos_archivo["name"];
							$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
							$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
							$array_files['retirement_evidence'] = $nombre_archivo_prefix;
							
							$array_base64 = explode('base64,', $datos_archivo["base64"]);
							$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($base_sin_data);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
							
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									'retirement_evidence' => lang("invalid_file_type"),
								);
							}
						}
						
					}
					
				}
				
				//if(isset($_FILES['reception_evidence'])){
				if($this->post('reception_evidence')){
					
					if((bool)filter_var($this->post('reception_evidence'), FILTER_VALIDATE_URL)){
						// SI RECIBE LA URL DE DESCARGA ES POR QUE NO SE MODIFICA EL ARCHIVO
						$nombre_archivo_anterior = $datos_elemento['nombre_archivo_recepcion'];
						$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_anterior;
					}elseif($this->post('reception_evidence') == 'undefined'){
						// SI RECIBE UNDEFINED ES POR QUE SE BORRA EL ARCHIVO
						
					}elseif(is_array($this->post('reception_evidence'))){
						// SI RECIBE UN ARREGLO ES POR QUE SE REEMPLAZA EL ARCHIVO CON OTRO
						
						$datos_archivo = (array)$this->post('reception_evidence');
									
						if($datos_archivo["from"] == "app-camera"){
									
							$nombre_archivo = "camera-picture.jpg";
							$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
							$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
							$array_files['reception_evidence'] = $nombre_archivo_prefix;
							
							$array_base64 = $datos_archivo["base64"];
							//$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($array_base64);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
												
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									'reception_evidence' => lang("invalid_file_type"),
								);
							}
							
						}else{
						
							$nombre_archivo = $datos_archivo["name"];
							$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
							$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
							$array_files['reception_evidence'] = $nombre_archivo_prefix;
							
							$array_base64 = explode('base64,', $datos_archivo["base64"]);
							$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($base_sin_data);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
							
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_errores[] = array(
									'reception_evidence' => lang("invalid_file_type"),
								);
							}
						}
						
					}
					
				}
				
				$json_datos = json_encode($array_datos);
				
				$options = array("id_formulario" => $id_formulario/*, "id_proyecto" => $id_proyecto*/);
				$record_info = $this->Form_rel_project_model->get_details($options)->row();
				if($record_info){
					$id_formulario_rel_proyecto = $record_info->id;
					$id_proyecto = $record_info->id_proyecto;
				}
				
				$data = array(
					"id_formulario_rel_proyecto" => $id_formulario_rel_proyecto,
					"datos" => $json_datos, 
					"modified_by" => $id_usuario_cliente,
					"modified" => get_current_utc_time()
				);
				
				//var_dump($array_errores);
				/*var_dump($array_files);
				var_dump($data);*/
				//var_dump($array_datos);
				
				if(count($array_errores) == 0){
					$save_id = $this->Form_values_model->save($data, $id_elemento);
					
					foreach($array_files as $field_name => $filename){
						$this->api_upload_file_to_temp($filename);
						move_temp_file($filename, "files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$save_id."/", "", "", $filename);
					}
					
				}
				if ($save_id) {
					$respuesta = array("message" => "Se han modificado los datos correctamente.", "success" => true);
					
				}else{
					$respuesta = array("message" => "Ocurrió un error al intentar modificar los datos.", "success" => false, "details" => $array_errores);
					
				}
				
			}else{
				$respuesta = array("message" => "Ocurrió un error al intentar modificar los datos, no se identificó un elemento por parametro.", "success" => false);
				
			}
			
		}else{
			
			$respuesta = array("message" => "Ocurrió un error al intentar modificar los datos. Método no existente.", "success" => false);
		}
		
		return $this->response($respuesta);

	}

	// ENDPOINTS GET/DELETE/LIST - GET
	public function form_get($id_formulario, $tipo, $id_elemento, $pagina = 1){ // tipo = get, delete, list
		
		//$token = $this->get_request_header('Authorization', true);
		$token = $this->get('Authorization');
		$array_errores = array();
		
		// VÁLIDO SI VIENE EL TOKEN
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}
		
		// VÁLIDO SI EXISTE UN USUARIO ASOCIADO AL TOKEN
		$existe_token = $this->Api_model->get_one_where(array("token" => $token, "deleted" => 0));
		if(!$existe_token->id){
			return $this->response(array("message" => "Fallo de autenticidad!", "success" => false));
			exit();
		}
		
		$id_usuario_cliente_token = $existe_token->user_id;
		$usuario_cliente = $this->Users_model->get_one_where(array("id" => $id_usuario_cliente_token, "deleted" => 0));
		$id_usuario_cliente = $usuario_cliente->id;
		$id_cliente = $usuario_cliente->client_id;

		// RECIBO EL ID DE FORMULARIO POR PARAMETRO
		$form_info = $this->Forms_model->get_one($id_formulario);

		// VÁLIDO SI EL FORMULARIO NO SE HA BORRADO
		if(!$form_info->id){
			return $this->response(array("message" => "El formulario no existe!", "success" => false));
			exit();
		}
		
		// PREGUNTO SI ES GET, DELETE O LIST
		if($tipo == "get"){

			if($id_elemento){
				
				$array_datos = array();
				$elemento = $this->Form_values_model->get_one($id_elemento);
				if(!$elemento->id){
					return $this->response(array("message" => "El elemento no existe!", "success" => false));
					exit();
				}
				$datos = json_decode($elemento->datos, true);
				
				if($form_info->id_tipo_formulario == 1){
					$array_datos['date_filed'] = $datos["fecha"];
					$array_datos['category'] = $datos["id_categoria"];
					
					if($form_info->flujo == "Residuo"){
						
						$array_datos['type_of_treatment'] = $datos["tipo_tratamiento"];
						$array_datos['waste_unit'] = $datos["unidad_residuo"];
						$array_datos['retirement_date'] = $datos["fecha_retiro"];
						$array_datos['retirement_evidence'] = $datos["nombre_archivo_retiro"]?get_uri("api/download_file/".$id_elemento."/nombre_archivo_retiro"):$datos["nombre_archivo_retiro"];
						$array_datos['reception_evidence'] = $datos["nombre_archivo_recepcion"]?get_uri("api/download_file/".$id_elemento."/nombre_archivo_recepcion"):$datos["nombre_archivo_recepcion"];
						
					}else{
						$array_datos['waste_unit'] = $datos["unidad_residuo"];
						
						/*$campo_unidad = $formulario->unidad;
						$unidad_decoded = json_decode($campo_unidad, true);
						$nombre_unidad = $unidad_decoded["nombre_unidad"];
						$data_unidad = $this->Unity_model->get_one($unidad_decoded["unidad_id"]);
						$unidad = $data_unidad->nombre;*/
					
					}
		
				}
				
				$columnas = $this->Forms_model->get_fields_of_form($id_formulario)->result();
				
				foreach($columnas as $columna){
					
					// cuando sea periodo
					if($columna->id_tipo_campo == 5){
						
						$fecha_decoded = json_decode($columna->html_name, true);
						$name_inicio = $fecha_decoded["start_name"];
						$name_final = $fecha_decoded["end_name"];
						
						$datos_fecha_decoded = $datos[$columna->id];
						$date_inicio = $datos_fecha_decoded["start_date"];
						$date_final = $datos_fecha_decoded["end_date"];
						
						//var_dump($date_inicio);
						
						$array_datos[$name_inicio] = (is_null($date_inicio))?"":$date_inicio;
						$array_datos[$name_final] = (is_null($date_final))?"":$date_final;
						//$array_datos[$columna->html_name] = $datos[$columna->id];
						
					}else if($columna->id_tipo_campo == 10){// archivo
						
						if($datos[$columna->id]){
							$array_datos[$columna->html_name] = get_uri("api/download_file/".$id_elemento."/".$columna->id);
						}else{
							$array_datos[$columna->html_name] = $datos[$columna->id];
						}
			
					}else if($columna->id_tipo_campo == 11){// html
						//$array_datos[$columna->html_name] = $columna->default_value;
					}else if($columna->id_tipo_campo == 12){// divider
						//$array_datos[$columna->html_name] = $columna->default_value;
					}else{
						$array_datos[$columna->html_name] = (is_null($datos[$columna->id]))?"":$datos[$columna->id];
						
					}
				}
				
				$respuesta = array("id" => $id_elemento, "data" => $array_datos/*, "success" => true*/);
				
			}else{
				$respuesta = array("message" => "Ocurrió un error al intentar obtener los datos.", "success" => false);
				
			}
			
		}elseif($tipo == "delete"){
			
			if($id_elemento){
		
				if ($this->Form_values_model->delete($id_elemento)) {
					$respuesta = array('message' => lang('record_deleted'), "success" => true);
				} else {
					$respuesta = array('message' => lang('record_cannot_be_deleted'), "success" => false);
				}
				
			}
			
		}elseif($tipo == "list"){
			
			if($id_formulario){
				
				$respuesta = array();
				
				// CONTAR REGISTROS DEL FORMULARIO
				$count_records = $this->Form_values_model->get_forms_values_of_form($id_formulario)->num_rows();
				if($count_records == 1){
					$count_records_label = "1 Registro";
				}else{
					$count_records_label = $count_records." Registros";
				}
				
				
				$valores_de_formulario = $this->Api_model->get_values_of_form(array("id_formulario" => $form_info->id), $pagina)->result();
				//var_dump($valores_de_formulario);
				foreach($valores_de_formulario as $valor){
					//var_dump($valor);

					$datos_decoded = json_decode($valor->datos);
					$array_valores = array();
					foreach($datos_decoded as $id => $value){
						
						
						
						$array_datos['reception_evidence'] = $datos["nombre_archivo_recepcion"]?get_uri("api/download_file/".$id_elemento."/nombre_archivo_recepcion"):$datos["nombre_archivo_recepcion"];
						
						
						if($id == "fecha"){
							$array_valores["date_filed"] = $value;
						}elseif($id == "id_categoria"){
							$array_valores["category"] = $value;
						}elseif($id == "tipo_tratamiento"){
							$array_valores["type_of_treatment"] = $value;
						}elseif($id == "unidad_residuo"){
							$array_valores["waste_unit"] = $value;
						}elseif($id == "fecha_retiro"){
							$array_valores["retirement_date"] = $value;
						}elseif($id == "nombre_archivo_retiro"){
							$array_valores['retirement_evidence'] = $value?get_uri("api/download_file/".$valor->id."/nombre_archivo_retiro"):$value;
						}elseif($id == "nombre_archivo_recepcion"){
							$array_valores["reception_evidence"] = $value?get_uri("api/download_file/".$valor->id."/nombre_archivo_recepcion"):$value;
						}elseif($id == "tipo_unidad"){
							
						}elseif($id == "unidad"){
							
						}else{
							
							// COMPROBAR SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO, HTML O DIVIDER
							$campo_info = $this->Fields_model->get_one($id);
							if($campo_info->id){
								
								if($campo_info->id_tipo_campo == 5){
						
									$fecha_decoded = json_decode($campo_info->html_name, true);
									$name_inicio = $fecha_decoded["start_name"];
									$name_final = $fecha_decoded["end_name"];
									
									$date_inicio = $value->start_date;
									$date_final = $value->end_date;
									
									$array_valores[$name_inicio] = $date_inicio;
									$array_valores[$name_final] = $date_final;
									
								}elseif($campo_info->id_tipo_campo == 10){
									if($value){
										$array_valores[$campo_info->html_name] = get_uri("api/download_file/".$valor->id."/".$id);
									}else{
										$array_valores[$campo_info->html_name] = $value;
									}
									
								}else if($campo_info->id_tipo_campo == 11){// html
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else if($campo_info->id_tipo_campo == 12){// divider
									//$array_valores[$campo_info->html_name] = $campo_info->default_value;
								}else{
									$array_valores[$campo_info->html_name] = $value;
								}
								
								
							}else{
								
							}
							
							
						}
						//$array_valores["test_file"] = get_uri("environmental_records/download_file/".$valor->id."/".$id);
						
					}
					//var_dump($valor);
					$respuesta[] = array(
						"id" => (int)$valor->id,
						"data" => $array_valores
					);
				}
				
			}
			
		}
		
		return $this->response(array("count_records_label" => $count_records_label, "values" => $respuesta, "success" => true));
		
	}
	
	// ENDPOINT OFFLINEDATA - POST
	public function offlinedata_post(/*$id_formulario*/) {
		
		$token = $this->input->get_request_header('Authorization', true);
		$array_datos = (array)$this->post('forms');
		$array_errores = array();
		//var_dump($array_datos);
		
		// VÁLIDO SI VIENE EL TOKEN
		if(!$token){
			return $this->response(array("message" => "Fallo de autenticidad! No se identifico un token de acceso.", "success" => false));
			exit();
		}
		
		// VÁLIDO SI EXISTE UN USUARIO ASOCIADO AL TOKEN
		$existe_token = $this->Api_model->get_one_where(array("token" => $token, "deleted" => 0));
		if(!$existe_token->id){
			return $this->response(array("message" => "Fallo de autenticidad!", "success" => false));
			exit();
		}
		
		// VÁLIDO SI VIENEN DATOS POR POST
		if(empty($array_datos)){
			return $this->response(array("message" => "No se están enviando datos de formulario.", "success" => false));
			exit();
		}
		
		$id_usuario_cliente_token = $existe_token->user_id;
		$usuario_cliente = $this->Users_model->get_one_where(array("id" => $id_usuario_cliente_token, "deleted" => 0));
		$id_usuario_cliente = $usuario_cliente->id;
		$id_cliente = $usuario_cliente->client_id;
		
		$array_files_forms = array();
		$array_insert = array();
		$array_details = array();
		// POR CADA ELEMENTO DEL ARREGLO = ELEMENTO DE UN FORMULARIO
		foreach($array_datos as $elemento){
			
			$obj_data = $elemento["data"];
			
			$id_formulario = $obj_data["form_id"];
			$array_details_form = array();
			$array_details_form["form_id"] = $id_formulario;
			
			// CONSULTO LOS DATOS DEL FORMULARIO
			$form_info = $this->Forms_model->get_one($id_formulario);
			
			// VÁLIDO SI EL FORMULARIO NO SE HA BORRADO
			if(!$form_info->id){
				$array_details_form["data"] =  "El formulario no existe!";
				$array_details[] = $array_details_form;
				continue;
			}
			
			$array_datos = array();
			$array_details_form_data = array();
			// PREGUNTO SI ES REGISTRO AMBIENTAL
			if($form_info->id_tipo_formulario == 1){
				
				// RECIBO FECHA DE REGISTRO Y CATEGORIA
				$date_filed = $obj_data["date_filed"];
				$category = $obj_data["category"];
				
				// VALIDO SI NO VIENEN VACIAS
				if(!$date_filed){$array_details_form_data["date_filed"] = lang("field_required");}
				else{
					if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_filed)){
						$array_details_form_data["date_filed"] = "La fecha debe ser válida y bajo el formato YYYY-MM-DD";
					}
				}
				if(!$category){
					$array_details_form_data["category"] = lang("field_required");
				}
				
				$array_datos['fecha'] = $date_filed;
				$array_datos['id_categoria'] = $category;
				
				// PREGUNTO SI FLUJO ES RESIDUO
				if($form_info->flujo == "Residuo"){
					$type_of_treatment = $obj_data["type_of_treatment"];
					$waste_unit = $obj_data["waste_unit"];
					$retirement_date = $obj_data["retirement_date"];
					$retirement_evidence = $obj_data["retirement_evidence"];
					$reception_evidence = $obj_data["reception_evidence"];
					
				}
			}
			
			$array_files = array();
			//$array_files["id_formulario"] = $id_formulario;
			// CONSULTO CAMPOS DINAMICOS DEL FORMULARIO
			$columnas = $this->Forms_model->get_fields_of_form($form_info->id)->result();
			
			foreach($columnas as $columna){
				
				$obligatorio = $columna->obligatorio;
				$deshabilitado = $columna->habilitado;
				
				if($columna->id_tipo_campo == 3){// SI EL CAMPO DEL BUCLE ES DE TIPO NÚMERO
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}
					if($obj_data[$columna->html_name] != "" && !is_numeric($obj_data[$columna->html_name])){
						$array_details_form_data[$columna->html_name] = "El valor debe ser un número válido.";
					}
					
				}elseif($columna->id_tipo_campo == 4){// SI EL CAMPO DEL BUCLE ES DE TIPO FECHA
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] =  lang("field_required");
					}
					if($obj_data[$columna->html_name] && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $obj_data[$columna->html_name])){
						$array_details_form_data[$columna->html_name] =  "La fecha debe ser válida y bajo el formato YYYY-MM-DD";
					}
				}elseif($columna->id_tipo_campo == 5){// SI EL CAMPO DEL BUCLE ES DE TIPO PERIODO
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id] = array(
						"start_date" => $obj_data[$start_name],
						"end_date" => $obj_data[$end_name]
					);
					
					if($obligatorio && (!$obj_data[$start_name] || !$obj_data[$end_name])){
						$array_details_form_data[$start_name] = lang("field_required");
						$array_details_form_data[$end_name] = lang("field_required");
					}
					
					
				}else if($columna->id_tipo_campo == 10){// SI EL CAMPO DEL BUCLE ES DE TIPO ARCHIVO
				
					/*$datos_archivo = $_FILES[$columna->html_name];
					$nombre_archivo = $_FILES[$columna->html_name]["name"];
					
					$array_datos[$columna->id] = $nombre_archivo;
					$array_files[$columna->html_name] = $nombre_archivo;

					//get_setting("accepted_file_formats")
					if($obligatorio && !$datos_archivo){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}*/
					
					if($obj_data[$columna->html_name]){
							
						$datos_archivo = (array)$obj_data[$columna->html_name];
						/*
						$nombre_archivo = $datos_archivo["name"];
						
						$array_datos[$columna->id] = $nombre_archivo;
						$array_files[$columna->html_name] = $nombre_archivo;
						
						$array_base64 = explode('base64,', $datos_archivo["base64"]);
						$base_sin_data = $array_base64[1];
						$decoded_file = base64_decode($base_sin_data);
						$tmp_folder = sys_get_temp_dir();
						file_put_contents($tmp_folder.'/'.$nombre_archivo, $decoded_file);
						
						if(!is_valid_file_to_upload($nombre_archivo)){
							$array_details_form_data[$columna->html_name] = lang("invalid_file_type");
						}*/
						
						if($datos_archivo["from"] == "app-camera"){
							
							//$nombre_archivo = "camera-picture-".$columna->id.'.jpg';
							$nombre_archivo = "camera-picture.jpg";
							$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
							$array_datos[$columna->id] = $nombre_archivo_prefix;
							$array_files[$columna->html_name] = $nombre_archivo_prefix;
							
							$array_base64 = $datos_archivo["base64"];
							//$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($array_base64);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
												
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_details_form_data[$columna->html_name] = lang("invalid_file_type");
							}

							
						}else{
							
							$nombre_archivo = $datos_archivo["name"];
							$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
							$array_datos[$columna->id] = $nombre_archivo_prefix;
							$array_files[$columna->html_name] = $nombre_archivo_prefix;
							
							$array_base64 = explode('base64,', $datos_archivo["base64"]);
							$base_sin_data = $array_base64[1];
							$decoded_file = base64_decode($base_sin_data);
							$tmp_folder = sys_get_temp_dir();
							file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
							
							if(!is_valid_file_to_upload($nombre_archivo)){
								$array_details_form_data[$columna->html_name] = lang("invalid_file_type");
							}
							
						}
						
					}
					
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}
				
				}elseif($columna->id_tipo_campo == 13){// SI EL CAMPO DEL BUCLE ES DE TIPO CORREO
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] =  lang("field_required");
					}
					if($obj_data[$columna->html_name] != "" && !(bool)filter_var($obj_data[$columna->html_name], FILTER_VALIDATE_EMAIL)){
						$array_details_form_data[$columna->html_name] = "El valor debe ser un correo válido.";
					}
					
				}elseif($columna->id_tipo_campo == 14){// SI EL CAMPO DEL BUCLE ES DE TIPO HORA
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}
					if($obj_data[$columna->html_name] != "" && !preg_match('/\d{2}:\d{2}/', $obj_data[$columna->html_name])){
						$array_details_form_data[$columna->html_name] = "La hora debe tener formato HH:MM AM|PM.";
					}
				
				}elseif($columna->id_tipo_campo == 15){// SI EL CAMPO DEL BUCLE ES DE TIPO UNIDAD
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}
					if($obj_data[$columna->html_name] != "" && !is_numeric($obj_data[$columna->html_name])){
						$array_details_form_data[$columna->html_name] = "El valor debe ser un número válido.";
					}
				
				}else{
					$array_datos[$columna->id] = $obj_data[$columna->html_name];
					//$array_datos[$columna->id] = $categories;
					if($obligatorio && !$obj_data[$columna->html_name]){
						$array_details_form_data[$columna->html_name] = lang("field_required");
					}
					
				}
			}
			
			if($form_info->flujo == "Residuo"){
				
				$array_datos['tipo_tratamiento'] = $type_of_treatment;
				$array_datos['unidad_residuo'] = $waste_unit;
				
				if(!$type_of_treatment){
					$array_details_form_data["type_of_treatment"] = lang("field_required");
				}else{
					if((int)$type_of_treatment == 0){
						$array_details_form_data["type_of_treatment"] = "El valor debe ser un número entero válido.";
					}
				}
				
				if(!$waste_unit){
					$array_details_form_data["waste_unit"] = lang("field_required");
				}else{
					if(!is_numeric($waste_unit)){
						$array_details_form_data["waste_unit"] = "El valor debe ser un número válido.";
					}
				}
				
				if($retirement_date != ""){
					$array_datos['fecha_retiro'] = $retirement_date;
					if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $retirement_date)){
						$array_details_form_data["fecha_retiro"] = "La fecha debe ser válida y bajo el formato YYYY-MM-DD";
					}
				}else{
					$array_datos['fecha_retiro'] = NULL;
				}
				
				$datos_unidad_fija = json_decode($form_info->unidad);
				$id_tipo_unidad = $datos_unidad_fija->tipo_unidad_id;
				$id_unidad = $datos_unidad_fija->unidad_id;
				$tipo_unidad_info = $this->Unity_type_model->get_one($id_tipo_unidad);
				$unidad_info = $this->Unity_model->get_one($id_unidad);
				
				$array_datos['tipo_unidad'] = $tipo_unidad_info->nombre;
				$array_datos['unidad'] = $unidad_info->nombre;
	
			}
			
			//if(isset($_FILES['retirement_evidence'])){
			if($obj_data['retirement_evidence']){
							
				$datos_archivo = (array)$obj_data['retirement_evidence'];
				/*
				$nombre_archivo = $datos_archivo["name"];
				
				$array_datos['nombre_archivo_retiro'] = $nombre_archivo;
				$array_files['retirement_evidence'] = $nombre_archivo;
				
				$array_base64 = explode('base64,', $datos_archivo["base64"]);
				$base_sin_data = $array_base64[1];
				$decoded_file = base64_decode($base_sin_data);
				$tmp_folder = sys_get_temp_dir();
				file_put_contents($tmp_folder.'/'.$nombre_archivo, $decoded_file);
				
				if(!is_valid_file_to_upload($nombre_archivo)){
					$array_details_form_data['retirement_evidence'] = lang("invalid_file_type");
				}
				*/
				
				if($datos_archivo["from"] == "app-camera"){
					
					//$nombre_archivo = "camera-picture-retirement-evidence.jpg";
					$nombre_archivo = "camera-picture.jpg";
					$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
					$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
					$array_files['retirement_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = $datos_archivo["base64"];
					//$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($array_base64);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_details_form_data['retirement_evidence'] = lang("invalid_file_type");
					}
					
				}else{
					
					$nombre_archivo = $datos_archivo["name"];
					$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
					$array_datos['nombre_archivo_retiro'] = $nombre_archivo_prefix;
					$array_files['retirement_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = explode('base64,', $datos_archivo["base64"]);
					$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($base_sin_data);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_details_form_data['retirement_evidence'] = lang("invalid_file_type");
					}
				}
				
				
			}
			
			//if(isset($_FILES['reception_evidence'])){
			if($obj_data['reception_evidence']){
							
				$datos_archivo = (array)$obj_data['reception_evidence'];
				
				/*$nombre_archivo = $datos_archivo["name"];
				
				$array_datos['nombre_archivo_recepcion'] = $nombre_archivo;
				$array_files['reception_evidence'] = $nombre_archivo;
				
				$array_base64 = explode('base64,', $datos_archivo["base64"]);
				$base_sin_data = $array_base64[1];
				$decoded_file = base64_decode($base_sin_data);
				$tmp_folder = sys_get_temp_dir();
				file_put_contents($tmp_folder.'/'.$nombre_archivo, $decoded_file);
				
				if(!is_valid_file_to_upload($nombre_archivo)){
					$array_details_form_data['reception_evidence'] = lang("invalid_file_type");
				}*/
				
				if($datos_archivo["from"] == "app-camera"){
							
					//$nombre_archivo = "camera-picture-reception-evidence.jpg";
					$nombre_archivo = "camera-picture.jpg";
					$nombre_archivo_prefix = uniqid("file")."-camera-picture.jpg";
					$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
					$array_files['reception_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = $datos_archivo["base64"];
					//$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($array_base64);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
										
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_details_form_data['reception_evidence'] = lang("invalid_file_type");
					}
					
				}else{
				
					$nombre_archivo = $datos_archivo["name"];
					$nombre_archivo_prefix = uniqid("file")."-".$datos_archivo["name"];
					$array_datos['nombre_archivo_recepcion'] = $nombre_archivo_prefix;
					$array_files['reception_evidence'] = $nombre_archivo_prefix;
					
					$array_base64 = explode('base64,', $datos_archivo["base64"]);
					$base_sin_data = $array_base64[1];
					$decoded_file = base64_decode($base_sin_data);
					$tmp_folder = sys_get_temp_dir();
					file_put_contents($tmp_folder.'/'.$nombre_archivo_prefix, $decoded_file);
					
					if(!is_valid_file_to_upload($nombre_archivo)){
						$array_details_form_data['reception_evidence'] = lang("invalid_file_type");
					}
				}
				
				
			}
			
			$json_datos = json_encode($array_datos);
			
			$options = array("id_formulario" => $id_formulario/*, "id_proyecto" => $id_proyecto*/);
			$record_info = $this->Form_rel_project_model->get_details($options)->row();
			if($record_info){
				$id_formulario_rel_proyecto = $record_info->id;
				$id_proyecto = $record_info->id_proyecto;
			}
			
			$data = array(
				"id_formulario_rel_proyecto" => $id_formulario_rel_proyecto,
				"datos" => $json_datos, 
				"created_by" => $id_usuario_cliente,
				"created" => get_current_utc_time()
			);
			$array_details_form["data"] =  $array_details_form_data;
			$array_details[] = $array_details_form;

			$array_insert[] = $data;
			$array_files_forms[] = array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_formulario" => $id_formulario, "files" => $array_files);
			//var_dump($array_insert);
			
				
		}// FIN FOREACH ELEMENTO
		
		//var_dump($array_files_forms);exit();
		
		// ERRORES
		$errors = 0;
		foreach($array_details as $errores){
			if(count($errores["data"]) > 0){
				$errors++;
			}
		}

		if($errors == 0){
			$bulk_save = $this->Form_values_model->bulk_load($array_insert);
			
			if($bulk_save){
				$first_id_inserted = $this->db->insert_id();
				$valores_formularios_table = $this->db->dbprefix('valores_formularios');
    			$registros_ingresados = $this->db->get_where($valores_formularios_table, "id >= '$first_id_inserted' AND created_by = '$id_usuario_cliente' AND deleted = '0'")->result_array();
				
				foreach($registros_ingresados as $key => $row){
					$id_elemento_ingresado = $row["id"];
					$archivos_de_elemento = $array_files_forms[$key];
					$file_id_cliente = $archivos_de_elemento["id_cliente"];
					$file_id_proyecto = $archivos_de_elemento["id_proyecto"];
					$file_id_formulario = $archivos_de_elemento["id_formulario"];
					$files = $archivos_de_elemento["files"];
					
					foreach($files as $field_name => $filename){
						if($filename){
							$this->api_upload_file_to_temp($filename);
							move_temp_file($filename, "files/mimasoft_files/client_".$file_id_cliente."/project_".$file_id_proyecto."/form_".$file_id_formulario."/elemento_".$id_elemento_ingresado."/", "", "", $filename);
						}
						
					}
					
					
				}
			}
			
			/*foreach($array_insert as $row){
				$save_id = $this->Form_values_model->save($row);
			}*/
			
		}
		if ($bulk_save) {
			$respuesta = array("message" => "Se han ingresado los datos correctamente.", "success" => true);
			
		}else{
			$respuesta = array(
				"message" => "Ocurrió un error al intentar ingresar los datos.", 
				"success" => false, 
				"details" => $array_details
			);
			
		}		
		
		return $this->response($respuesta);

	}
	
	
	public function password_post() {// NO NECESITA TOKEN
		$email = $this->input->post("email");
        $existing_user = $this->Users_model->is_email_exists($email);

        //send reset password email if found account with this email
        if ($existing_user) {
            $email_template = $this->Email_templates_model->get_final_template("reset_password");

            $parser_data["ACCOUNT_HOLDER_NAME"] = $existing_user->first_name . " " . $existing_user->last_name;
            $parser_data["SIGNATURE"] = $email_template->signature;
            $parser_data["SITE_URL"] = get_uri();
            $key = encode_id($this->encrypt->encode($existing_user->email . '|' . (time() + (24 * 60 * 60))), "reset_password");
            $parser_data['RESET_PASSWORD_URL'] = get_uri("signin/new_password/" . $key);

            $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
            if (send_app_mail($email, $email_template->subject, $message)) {
                echo json_encode(array('success' => true, 'message' => lang("api_reset_info_send")));
            } else {
                echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => lang("no_acount_found_with_this_email")));
            return false;
        }
    }




	// ------------------------------------- METODOS SECUNDARIOS ---------------------------------------------



	protected static function generate_token($length = 24) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}

	public function get_field_object($campo){

		$array_fields = array();
		$array_fields["id"] = $campo->id;
		$array_fields["label"] = $campo->nombre;
		$array_fields["name"] = $campo->html_name;

		if($campo->id_tipo_campo == 1){
			$array_fields["type"] = "text";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
		}
		if($campo->id_tipo_campo == 2){
			$array_fields["type"] = "textarea";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			$array_fields["maxlength"] = 1000;
		}
		if($campo->id_tipo_campo == 3){
			$array_fields["type"] = "number";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			$array_fields["step"] = "any";
		}
		if($campo->id_tipo_campo == 4){
			$array_fields["type"] = "datepicker";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
		}
		if($campo->id_tipo_campo == 5){
			//{"start_name":"42_periodo_test_start","end_name":"42_periodo_test_end"}
			$array_fields["type"] = "daterange";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["name"] = "";

			$start_date_name = json_decode($campo->html_name)->start_name;
			$start_date_value = json_decode($campo->default_value)->start_date;
			
			$array_fields["options"]["startdate"] = array(
				"type" => "datepicker",
				"name" => $start_date_name,
				"value" => $start_date_value
			);

			$end_date_name = json_decode($campo->html_name)->end_name;
			$end_date_value = json_decode($campo->default_value)->end_date;

			$array_fields["options"]["enddate"] = array(
				"type" => "datepicker",
				"name" => $end_date_name,
				"value" => $end_date_value
			);
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			//$array_fields["value"] = $campo->default_value;
		}
		if($campo->id_tipo_campo == 6){
			$array_fields["type"] = "select";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			
			$array_opciones = array();
			if($campo->opciones){
				$opciones = json_decode($campo->opciones);
				foreach($opciones as $index => $opcion){
					if($index == 0){
						$array_opciones[] = array("value" => "", "text" => $opcion->text);
					}else{
						$array_opciones[] = array("value" => $opcion->value, "text" => $opcion->text);
					}
					
				}
			}

			$array_fields["options"] = $array_opciones;
		}

		if($campo->id_tipo_campo == 7){
			$array_fields["type"] = "multiple";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["values"] = ($campo->default_value)?json_decode($campo->default_value):array();
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			
			$array_opciones = array();
			if($campo->opciones){
				$opciones = json_decode($campo->opciones);
				foreach($opciones as $index => $opcion){
					if($index == 0){
						$array_opciones[] = array("value" => "", "text" => $opcion->text);
					}else{
						$array_opciones[] = array("value" => $opcion->value, "text" => $opcion->text);
					}
					
				}
			}

			$array_fields["options"] = $array_opciones;
		}

		if($campo->id_tipo_campo == 8){
			$array_fields["type"] = "rut";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			$array_fields["minlength"] = 10;
			$array_fields["maxlength"] = 13;
		}

		if($campo->id_tipo_campo == 9){
			$array_fields["type"] = "radiosimple";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;

			$array_opciones = array();
			if($campo->opciones){
				$opciones = json_decode($campo->opciones);
				foreach($opciones as $index => $opcion){
					$array_opciones[] = array("value" => $opcion->value, "text" => $opcion->text);
					
				}
			}

			$array_fields["options"] = $array_opciones;
		}

		if($campo->id_tipo_campo == 10){
			$array_fields["type"] = "file";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["valid"] = $this->files_format_to_html_format(get_setting("accepted_file_formats"));
			$array_fields["disabled"] = (boolean)$campo->habilitado;
		}

		if($campo->id_tipo_campo == 11){
			$array_fields["type"] = "html";
			$array_fields["value"] = $campo->default_value;
			
			unset($array_fields["label"]);
			unset($array_fields["name"]);
		}

		if($campo->id_tipo_campo == 12){
			$array_fields["type"] = "divider";
			//$array_fields["value"] = "<hr>";
			
			unset($array_fields["label"]);
			unset($array_fields["name"]);
		}

		if($campo->id_tipo_campo == 13){
			$array_fields["type"] = "email";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
		}

		if($campo->id_tipo_campo == 14){
			$array_fields["type"] = "timepicker";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
		}

		if($campo->id_tipo_campo == 15){
			$array_fields["type"] = "unit";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = $campo->default_value;
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			$array_fields["min"] = 0;
			// Este max es solo para pruebas de growthy, nosotros no ocupamos max, borrar despues:
			//$array_fields["max"] = 1000000;
			$array_fields["step"] = "any";

			$symbol_seleccionado = "";
			if($campo->opciones){
				$opciones = json_decode($campo->opciones);
				$seleccionada = $opciones[0]->id_tipo_unidad;
				$symbol_seleccionado = $opciones[0]->id_unidad;
			}
			
			$unidad = $this->Unity_model->get_one_where(array("id" => $symbol_seleccionado, "deleted" => 0));

			$array_fields["append"] = ' '.$unidad->nombre;
		}

		if($campo->id_tipo_campo == 16){
			$array_fields["type"] = "feeder";
			$array_fields["required"] = ($campo->obligatorio)?true:false;
			$array_fields["value"] = "";
			$array_fields["disabled"] = (boolean)$campo->habilitado;
			
			$valores = json_decode($campo->default_value);
			$id_mantenedora = $valores->mantenedora;
			$id_campo_label = $valores->field_label;
			$id_campo_value = $valores->field_value;
			
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			//var_dump($datos);
			
			$array_opciones = array();
			$array_opciones[] = array("value" => "", "text" => "-");
			foreach($datos as $index => $row){
				$fila = json_decode($row->datos);
				$text = $fila->$id_campo_label;
				$value = $fila->$id_campo_value;

				$array_opciones[] = array("value" => $value, "text" => $text);
			}

			$array_fields["options"] = $array_opciones;


		}


		return $array_fields;
	}

	public function get_categories_of_form($ra){
		$id_ra = $ra->id;
		$id_cliente = $ra->id_cliente;

		$categorias = $this->Categories_model->get_categories_of_material_of_form($id_ra)->result();

		//preparando la vista para el dropdown
		$array_categorias = array();
		$array_categorias[] = array("value" => "", "text" => "-");

		foreach($categorias as $index => $categoria){
			
			$row_alias = $this->Categories_alias_model->get_one_where(
				array(
					'id_categoria' => $categoria->id,
					'id_cliente' => $id_cliente,
					"deleted" => 0
				)
			);

			if($row_alias->alias){
				$nombre = $row_alias->alias;
			}else{
				$nombre = $categoria->nombre;
			}
            
			$array_categorias[] = array("value" => $categoria->id, "text" => $nombre);
		}

		return $array_categorias;

	}
	
	public function download_file_get($id, $id_campo) {
		
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
	
	public function files_format_to_html_format($extensions) {
		
		if(!$extensions){
			redirect("forbidden");
		}
		
		$array_final = array();
		$format_exploded = explode(',', $extensions);
		foreach($format_exploded as $formato){
			$array_final[] = '.'.$formato;
		}
		
		return implode(',', $array_final);
		
		
    }
	
	public function system_date_format_to_angular_format($format) {
		
		if(!$format){
			redirect("forbidden");
		}
		
		$final_format = "";
		
		if($format == "d-m-Y"){
			$final_format = "dd-MM-yyyy";
		}elseif($format == "m-d-Y"){
			$final_format = "MM-dd-yyyy";
		}elseif($format == "Y-m-d"){
			$final_format = "yyyy-MM-dd";
		}elseif($format == "d/m/Y"){
			$final_format = "dd/MM/yyyy";
		}elseif($format == "m/d/Y"){
			$final_format = "MM/dd/yyyy";
		}elseif($format == "Y/m/d"){
			$final_format = "yyyy/MM/dd";
		}elseif($format == "d.m.Y"){
			$final_format = "dd.MM.yyyy";
		}elseif($format == "m.d.Y"){
			$final_format = "MM.dd.yyyy";
		}elseif($format == "Y.m.d"){
			$final_format = "yyyy.MM.dd";
		}else{
			
		}
		
		return $final_format;
		
    }

    public function api_upload_file_to_temp($file_name) {
		
        if (!empty($file_name)) {
			
            if (!is_valid_file_to_upload($file_name)){
				return false;
			} 
				
			$server_temp_file = sys_get_temp_dir().'/'.$file_name;
            $temp_file_path = get_setting("temp_file_path");
            $target_path = getcwd() . '/' . $temp_file_path;
            if (!is_dir($target_path)) {
                if (!mkdir($target_path, 0777, true)) {
                    die('Failed to create file folders.');
                }
            }
            $target_file = $target_path . $file_name;
            copy($server_temp_file, $target_file);
        }
    }

	public function login_options() {
	}

	public function form_options() {
	}

	public function offlinedata_options() {
	}
	
	public function password_options() {
	}
	


}
