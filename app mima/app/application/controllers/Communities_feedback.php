<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Communities_feedback extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9;
		$this->id_submodulo_cliente = 14;
		
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
		
		$id_feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $this->session->project_context, "deleted" => 0))->id;
		
		if($id_feedback_matrix_config){
			$columnas_campos = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result_array();
			$json_string_campos = '';
			foreach($columnas_campos as $columna){
				
				/*if($columna["id_tipo_campo"] == 11){
					continue;
				}
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
			$view_data["id_feedback_matrix_config"] = $id_feedback_matrix_config;
		}
		
		// Filtro Tipo de Stakeholder
		$array_tipos_organizaciones[] = array("id" => "", "text" => "- ".lang("type_of_stakeholder")." -");	
		$tipos_organizaciones = $this->Types_of_organization_model->get_all_where(array("deleted" => 0))->result_array();
		foreach($tipos_organizaciones as $tipo_organizacion){
			$array_tipos_organizaciones[] = array("id" => $tipo_organizacion['id'], "text" => lang($tipo_organizacion['nombre']));
		}
		$view_data["tipos_organizaciones_dropdown"] = json_encode($array_tipos_organizaciones);
		
		// Filtro Propósito Visita
		$array_propositos_visita[] = array("id" => "", "text" => "- ".lang("visit_purpose")." -");
		$array_propositos_visita[] = array("id" => "scheduled_meeting", "text" => lang("scheduled_meeting"));
		$array_propositos_visita[] = array("id" => "complain", "text" => lang("complain"));
		$array_propositos_visita[] = array("id" => "comment", "text" => lang("comment"));
		$array_propositos_visita[] = array("id" => "query", "text" => lang("query"));
		$array_propositos_visita[] = array("id" => "congratulation", "text" => lang("congratulation"));
		$view_data["propositos_visita_dropdown"] = json_encode($array_propositos_visita);
		
		// Filtro Responsable
		$project_members = $this->Project_members_model->get_all_where(array("project_id" => $proyecto->id, "deleted" => 0))->result_array();
		$array_responsables[] = array("id" => "", "text" => "- ".lang("responsible")." -");
		foreach($project_members as $pm){
			$user = $this->Users_model->get_one($pm['user_id']);
			$array_responsables[] =array("id" => $user->id, "text" => $user->first_name . " " . $user->last_name);
		}
		$view_data["responsables_dropdown"] = json_encode($array_responsables);

		$this->template->rander("communities_feedback/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        //$this->access_only_allowed_members();
        $value_feedback_id = $this->input->post('id');
		$id_feedback_matrix_config = $this->input->post('id_feedback_matrix_config');
		$feedback_matrix_config = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config);
		$project_id = $feedback_matrix_config->id_proyecto;
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
        $view_data["view"] = $this->input->post('view');
		
		$campos_feedback_matrix = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result_array();
		
		$view_data["id_feedback_matrix_config"] = $id_feedback_matrix_config;
		$view_data["campos_feedback_matrix"] = $campos_feedback_matrix;
		$view_data["Communities_feedback_controller"] = $this;
		
		//Buscar usuarios del cliente asignados al proyecto de la matriz de acuerdos
		$project_members = $this->Project_members_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result_array();
		$responsables_dropdown = array();
		foreach($project_members as $pm){
			$user = $this->Users_model->get_one($pm['user_id']);
			$responsables_dropdown[$user->id] = $user->first_name . " " . $user->last_name;
		}
		
		$view_data['responsables_dropdown'] = array("" => "-") + $responsables_dropdown;
			
		$dropdown_tipos_organizaciones = array("" => "-");
		$tipos_organizaciones = $this->Types_of_organization_model->get_all_where(array("deleted" => 0))->result_array();
		foreach($tipos_organizaciones as $tipo_organizacion){
			$dropdown_tipos_organizaciones[$tipo_organizacion['id']] = lang($tipo_organizacion['nombre']);
		}
		
		$view_data["dropdown_tipos_organizaciones"] = $dropdown_tipos_organizaciones;
		
		if($value_feedback_id){ //edit
			
			$view_data['value_feedback_id'] = $value_feedback_id;
			
			$view_data['model_info'] = $this->Values_feedback_model->get_one($value_feedback_id);
			
			//DESHABILITAR CAMPOS RESPONSABLE Y PROPÓSITO VISITA CUANDO HAYA SEGUIMIENTOS DEL FEEDBACK
			$hay_seguimientos = FALSE;
			$seguimientos_feedback = $this->Feedback_monitoring_model->get_all_where(array(
				"id_valor_feedback" => $value_feedback_id, 
				"deleted" => 0
			))->result_array();
			
			if($seguimientos_feedback){
				$hay_seguimientos = TRUE;
			}
			
			$view_data["hay_seguimientos"] = $hay_seguimientos;

			/*
			$archivos_evidencia = $this->Agreements_evidences_model->get_all_where(array("id_valor_acuerdo" => $value_agreement_id, "deleted" => 0))->result_array();
			
			if($archivos_evidencia){
					
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">Archivos de Evidencia</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
											
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("communities_agreements/download_file/".$value_agreement_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					
					$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-value_agreement_id" => $value_agreement_id, "data-id_evidencia" => $evidencia["id"], "data-action-url" => get_uri("communities_agreements/delete_file"), "data-action" => "delete-confirmation"));
					//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-confirmation"));
					//$html_archivos_evidencia .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</thead>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="form-group">';
					$html_archivos_evidencia .= '<label for="archivos" class="col-md-3"></label>';
					$html_archivos_evidencia .= '<div class="col-md-9">';
					
				}
				
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$view_data["html_archivos_evidencia"] = $html_archivos_evidencia;
				
			}
			*/
			
		} 
	
        $this->load->view('communities_feedback/modal_form', $view_data);
    }
	
	
	function save() {

        $value_feedback_id = $this->input->post('id');
		$id_feedback_matrix_config = $this->input->post('id_feedback_matrix_config');
		
		//$file = $this->input->post('archivo_importado');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		$columnas = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result();
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
				
			} else if($columna->id_tipo_campo == 11 || $columna->id_tipo_campo == 12){
				//CAMPO TIPO TEXTO FIJO NO SE GUARDA
				//CAMPO DIVISOR NO SE GUARDA
			} else {

				if($deshabilitado){
					$array_datos[$columna->id_campo] = $default_value;
				}else{
					$array_datos[$columna->id_campo] =  $this->input->post($columna->html_name);
				}
			}
			
			/*
			if(!$this->input->post($columna->html_name) || $this->input->post($columna->html_name) == ""){
				continue;
			} else {
				$array_datos[$columna->id_campo] = $this->input->post($columna->html_name);
			}
			*/
		}
		
		$json_datos_campos = json_encode($array_datos);
		$requires_monitoring = intval($this->input->post('requires_monitoring'));
		$data_value_feedback = array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config,
			"fecha" => $this->input->post('date'),
			"nombre" => $this->input->post('name'),
			"id_tipo_organizacion" => $this->input->post('type_of_stakeholder'),
			"proposito_visita" => $this->input->post('visit_purpose'),
			"responsable" => $this->input->post('responsible'),
			"datos_campos" => $json_datos_campos,
			"requires_monitoring" => $requires_monitoring
		);
		
		if($value_feedback_id){ //edit
		
			$data_value_feedback["modified_by"] = $this->login_user->id;
			$data_value_feedback["modified"] = get_current_utc_time();
			$save_id = $this->Values_feedback_model->save($data_value_feedback, $value_feedback_id);
		
		} else { //insert
			
			$data_value_feedback["created_by"] = $this->login_user->id;
			$data_value_feedback["created"] = get_current_utc_time();
			$save_id = $this->Values_feedback_model->save($data_value_feedback);

		}
		
        if ($save_id) {
			
			/*
			if($file){
				//Si no existe el directorio para los archivos de evidencia, crearlo antes de mover el archivo.
				$crear_carpeta = $this->create_agreement_evidence_folder($save_id);
				$archivo_subido = move_temp_file($file, "files/evidencias_acuerdos/acuerdo_".$save_id."/");
				
				//Guardar el registro en la tabla de evidencias_acuerdos
				$datos_evidencia = array(
					"id_valor_acuerdo" => $save_id,
					"archivo" => $archivo_subido,
					"created_by" => $this->login_user->id
				);
				$save_evidencia_id = $this->Agreements_evidences_model->save($datos_evidencia);
			}
			*/
			
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id, $columnas, $id_feedback_matrix_config), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	

	function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Values_feedback_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Values_feedback_model->delete($id)) {
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
				$row = $this->Values_feedback_model->get_one($id);
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
			if($this->Values_feedback_model->delete($id)) {
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
	
	function list_data($id_feedback_matrix_config = 0) {
		
		$id_tipo_organizacion = $this->input->post("id_tipo_organizacion");
		$proposito_visita = $this->input->post("proposito_visita");
		$responsable = $this->input->post("responsable");
		
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$list_data = $this->Values_feedback_model->get_details(array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config,
			"id_tipo_organizacion" => $id_tipo_organizacion,
			"proposito_visita" => $proposito_visita,
			"responsable" => $responsable
		))->result();
		
		$columnas = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result();
		$result = array();
		 
		foreach($list_data as $data) {
			
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row($data, $columnas, $id_feedback_matrix_config);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row($data, $columnas, $id_feedback_matrix_config);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}

		}
		
		echo json_encode(array("data" => $result));	
		
    }
	
	private function _row_data($id, $columnas, $id_feedback_matrix_config) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Values_feedback_model->get_details($options)->row();
        return $this->_make_row($data, $columnas, $id_feedback_matrix_config);
    }
	
	private function _make_row($data, $columnas, $id_feedback_matrix_config) {
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$id_proyecto = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config)->id_proyecto;
		
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		$row_data[] = get_date_format($data->fecha, $id_proyecto);
		$row_data[] = $data->nombre;
		
		$tipo_stakeholder = $this->Types_of_organization_model->get_one($data->id_tipo_organizacion);
		$row_data[] = lang($tipo_stakeholder->nombre);
		
		$row_data[] = lang($data->proposito_visita);
		
		//RESPONSABLE
		$responsable = $this->Users_model->get_one($data->responsable);
		$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
		$row_data[] = $nombre_responsable;
		/*
		$row_data[] = $data->respuesta;
		$row_data[] = $data->estado_respuesta;
		$row_data[] = "[evidencias]";
		*/

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
					} elseif($columna->id_tipo_campo == 10){ // ARCHIVO
						if($arreglo_fila[$columna->id_campo]){
							$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id_campo]);
							$valor_campo = anchor(get_uri("environmental_records/download_file/".$data->id."/".$columna->id_campo), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
						} else {
							$valor_campo = '-';
						}	
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
		
		$row_data[] = get_date_format($data->created, $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($data->modified, $id_proyecto) : "-";
		
		$view = modal_anchor(get_uri("communities_feedback/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_feedback'), "data-post-id" => $data->id, "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config));
		$edit = modal_anchor(get_uri("communities_feedback/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_feedback'), "data-post-id" => $data->id, "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_feedback'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("communities_feedback/delete"), "data-action" => "delete-confirmation"));
		
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
	
	function view($value_feedback_id = 0) {
		
        if ($value_feedback_id) {
           
		    $options = array("id" => $value_feedback_id);
            $value_feedback_info = $this->Values_feedback_model->get_details($options)->row();
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$id_proyecto = $proyecto->id;
			
			$campos_feedback_matrix = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($value_feedback_info->id_feedback_matrix_config)->result_array();			
			$feedback_matrix = $this->Feedback_matrix_config_model->get_one($value_feedback_info->id_feedback_matrix_config);
			
            if ($value_feedback_info) {
				
				// Change created_by and modified_by from IDs to Names
				$created_by = $this->Users_model->get_one($value_feedback_info->created_by);
				$value_feedback_info->created_by = $value_feedback_info->created_by ? $created_by->first_name . " " . $created_by->last_name : "-";
				$modified_by = $this->Users_model->get_one($value_feedback_info->modified_by);
				$value_feedback_info->modified_by = $value_feedback_info->modified_by ? $modified_by->first_name . " " . $modified_by->last_name : "-";
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";			
				$view_data["id_proyecto"] = $feedback_matrix->id_proyecto;
				$view_data['model_info'] = $value_feedback_info;
				
				//TIPO STAKEHOLDER
				$tipo_stakeholder = $this->Types_of_organization_model->get_one($value_feedback_info->id_tipo_organizacion);
				$view_data["tipo_stakeholder"] = lang($tipo_stakeholder->nombre);

				//GESTOR
				$responsable = $this->Users_model->get_one($value_feedback_info->responsable);
				$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
				$view_data['responsable'] = $nombre_responsable;
				
				/*
				//STAKEHOLDERS
				$stakeholders = json_decode($value_agreement_info->stakeholders);
				$html_stakeholders = (count($stakeholders) > 1) ? "<ul>" : "";
				foreach($stakeholders as $sh){
					$stakeholder = $this->Values_stakeholders_model->get_one($sh);
					$html_stakeholders .= (count($stakeholders) > 1) ? "<li>" . $stakeholder->nombres . " " . $stakeholder->apellidos . "</li>" : $stakeholder->nombres . " " . $stakeholder->apellidos;
				}
				$html_stakeholders .= (count($stakeholders) > 1) ? "</ul>" : "";
				$view_data['stakeholders'] = $html_stakeholders;
				
				*/
				//EVIDENCIAS
				//$evidencias = $this->Agreements_evidences_model->get_all_where(array("id_valor_acuerdo" => $value_agreement_id, "deleted" => 0))->result_array();
				//$modal_evidencias = modal_anchor(get_uri("communities_agreements/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-value_agreement_id" => $value_agreement_id));
				//$html_evidencias = ($evidencias) ? $modal_evidencias : "-";

				$view_data['campos_feedback_matrix'] = $campos_feedback_matrix;
				$view_data["Communities_feedback_controller"] = $this;
				$view_data['id_proyecto'] = $id_proyecto;
				
				$this->load->view('communities_feedback/view', $view_data);
				
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
			
			$row_elemento = $this->Values_feedback_model->get_details(array("id" => $id_elemento))->result();
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
	
	function get_field_value($id_campo, $id_elemento) {
		
		$id_feedback_matrix_config = $this->Values_feedback_model->get_one($id_elemento)->id_feedback_matrix_config;
		$id_proyecto = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config)->id_proyecto;
		
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
		
		$row_elemento = $this->Values_feedback_model->get_details(array("id" => $id_elemento))->result();
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
	
	function upload_file() {
        upload_file_to_temp();
    }

    function validate_file() {
		
		$file_name = $this->input->post("file_name");
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
		echo json_encode(array("success" => true));

    }
	
	function download_file($value_agreement_id, $id_evidencia) {

		$file_info = $this->Agreements_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->archivo;
        $file_data = serialize(array(array("file_name" => $filename)));

        download_app_files("files/evidencias_acuerdos/acuerdo_".$value_agreement_id."/", $file_data);
		
    }
	
	function delete_file() {
				
		$value_agreement_id = $this->input->post('value_agreement_id');
		$id_evidencia = $this->input->post('id_evidencia');
		
        $file_info = $this->Agreements_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$save_id = $this->Agreements_evidences_model->update_where(array("deleted" => 1), array("id" => $id_evidencia));

        if ($save_id) {

            delete_file_from_directory("files/evidencias_acuerdos/acuerdo_".$value_agreement_id."/".$file_info->archivo);
			echo json_encode(array("success" => true, "data" => $this->_row_data($value_agreement_id), 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
            //echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }

    }
	
	function create_agreement_evidence_folder($id_acuerdo) {
		
		if(!file_exists(__DIR__.'/../../files/evidencias_acuerdos/acuerdo_'.$id_acuerdo)) {
			if(mkdir(__DIR__.'/../../files/evidencias_acuerdos/acuerdo_'.$id_acuerdo, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}
	
	function view_evidences() {
		
        //$this->access_only_allowed_members();
		
		$value_agreement_id = $this->input->post("value_agreement_id");

        if ($value_agreement_id) {
			$archivos_evidencia = $this->Agreements_evidences_model->get_all_where(array("id_valor_acuerdo" => $value_agreement_id, "deleted" => 0))->result_array();
            if ($archivos_evidencia) {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">Archivos de Evidencia</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
				
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("communities_agreements/download_file/".$value_agreement_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $compliance_evaluation_info->id, "data-id_evidencia" => $evidencia["id"], "data-action-url" => get_uri("compromises_compliance_evaluation/delete_file"), "data-action" => "delete-confirmation"));
					//$html_archivos_evidencia .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</thead>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="form-group">';
					$html_archivos_evidencia .= '<label for="archivos" class="col-md-3"></label>';
					$html_archivos_evidencia .= '<div class="col-md-9">';
				
				}
				
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$view_data["html_archivos_evidencia"] = $html_archivos_evidencia;
				
				//$view_data["evaluaciones"]
				$this->load->view('communities_agreements/view_evidences', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function get_excel(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$matriz_feedback = $this->Feedback_matrix_config_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		
		$id_feedback_matrix_config = $matriz_feedback->id;

		$list_data = $this->Values_feedback_model->get_details(array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config
		))->result();
		
		$columnas = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result();
		$result = array();
		 
		foreach($list_data as $data) {
			
			if($puede_ver == 1){ //Todos
				$result[] = $this->_make_row_excel($data, $columnas, $id_feedback_matrix_config);
			}
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_excel($data, $columnas, $id_feedback_matrix_config);
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
		$nombre_columnas[] = array("nombre_columna" => lang("date"), "id_tipo_campo" => "date");
		$nombre_columnas[] = array("nombre_columna" => lang("name"), "id_tipo_campo" => "name");
		$nombre_columnas[] = array("nombre_columna" => lang("type_of_stakeholder"), "id_tipo_campo" => "type_of_stakeholder");
		$nombre_columnas[] = array("nombre_columna" => lang("visit_purpose"), "id_tipo_campo" => "visit_purpose");
		$nombre_columnas[] = array("nombre_columna" => lang("responsible"), "id_tipo_campo" => "responsible");
		
		foreach($columnas as $columna){
			if($columna->id_tipo_campo == 11 || $columna->id_tipo_campo == 12){
				continue;			
			}
			$nombre_columnas[] = array("nombre_columna" => $columna->nombre_campo, "id_tipo_campo" => $columna->id_tipo_campo);
		}

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("feedback"))
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
					
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
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
					
					} elseif($columna["id_tipo_campo"] == "date" || $columna["id_tipo_campo"] == "name"
					|| $columna["id_tipo_campo"] == "type_of_stakeholder" || $columna["id_tipo_campo"] == "visit_purpose"
					|| $columna["id_tipo_campo"] == "responsible"){
					
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
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/

		//$nombre_hoja = strlen($record_info->nombre) > 31?substr($record_info->nombre, 0, 28).'...':$record_info->nombre;
		//$nombre_hoja = $client_info->sigla."_".$project_info->sigla."_".lang("feedback").date('Y-m-d');
		$nombre_hoja = lang("feedback");
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("feedback")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;

	}
	
	private function _make_row_excel($data, $columnas, $id_feedback_matrix_config){
		
		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$id_proyecto = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config)->id_proyecto;
		
		$row_data = array();
		$row_data[] = get_date_format($data->fecha, $id_proyecto);
		$row_data[] = $data->nombre;
		
		$tipo_stakeholder = $this->Types_of_organization_model->get_one($data->id_tipo_organizacion);
		$row_data[] = lang($tipo_stakeholder->nombre);
		
		$row_data[] = lang($data->proposito_visita);
		
		//RESPONSABLE
		$responsable = $this->Users_model->get_one($data->responsable);
		$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
		$row_data[] = $nombre_responsable;

		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
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

