<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_compromises extends MY_Controller {
	
	private $id_admin_module;
	private $id_admin_submodule;

    function __construct() {
        parent::__construct();
		
		$this->id_admin_module = 8; // Compromisos
		$this->id_admin_submodule = 29; // Carga de Compromisos
		
		$this->load->helper('email');
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $this->template->rander("upload_compromises/index", $view_data);
    }

	function carga_individual($id_compromiso_proyecto, $tipo_matriz) {
		
		//Obtener las columnas (campos y evaluados) de la matriz de cumplimiento del proyecto
		$json_string_campos = "";
		if($tipo_matriz == "rca"){
			$columnas_campos = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}else{
			$columnas_campos = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}
		
		foreach($columnas_campos as $columna){
			if($columna["id_tipo_campo"] == 1){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 2){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
			}else if($columna["id_tipo_campo"] == 3){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
			}else if($columna["id_tipo_campo"] >= 4 && $columna["id_tipo_campo"] <= 9){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 10){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
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
		$view_data["id_compromiso_proyecto"] = $id_compromiso_proyecto;
		$view_data["tipo_matriz"] = $tipo_matriz;
		
        $this->load->view('upload_compromises/carga_individual/index', $view_data);
    }
	
	function modal_form_carga_individual($id_compromiso_proyecto, $tipo_matriz){
			
		$id_elemento = $this->input->post('id');
		if($tipo_matriz == "rca"){
			$campos_compromiso = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}else{
			$campos_compromiso = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		}
		$view_data["campos_compromiso"] = $campos_compromiso;
		$view_data["id_compromiso_proyecto"] = $id_compromiso_proyecto;
		$view_data["Upload_compromises_controller"] = $this;
		$view_data["tipo_matriz"] = $tipo_matriz;
		
		if($tipo_matriz == "rca"){
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$fases_dropdown = array();
			foreach($fases_disponibles as $fase){
				$fases_dropdown[$fase["id"]] = lang($fase["nombre_lang"]);
			}
			$view_data["fases_disponibles"] = $fases_dropdown;
		}
		
		if($id_elemento){ //edit
			if($tipo_matriz == "rca"){
				$model_info = $this->Values_compromises_rca_model->get_one($id_elemento);
			}else{
				$model_info = $this->Values_compromises_reportables_model->get_one($id_elemento);
				
				$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
					array(
						"id_compromiso" => $id_elemento,
						"deleted" => 0,
					)
				)->result();
				
				$array_planificaciones = array();
				foreach($planificaciones as $planificacion){
					$array_planificaciones[] = array("descripcion" => $planificacion->descripcion, "planificacion" => $planificacion->planificacion);
				}
				
				$view_data['array_planificaciones'] = $array_planificaciones;
			}
			$view_data['model_info'] = $model_info;

			$fases_decoded = json_decode($model_info->fases);
			$view_data['fases_compromiso'] = $fases_decoded;
			
		} 

		$this->load->view('upload_compromises/carga_individual/modal_form', $view_data);
		
	}
	
	function save_carga_individual($id_compromiso_proyecto, $tipo_matriz){
		
		$id_elemento = $this->input->post('id'); //para la edición, este es el id de un elemento (valores_compromisos)
		
		$numero_compromiso = $this->input->post('numero_compromiso');
		$nombre_compromiso = $this->input->post('nombre_compromiso');
		$fases = $this->input->post('phases');
		$json_fases = json_encode($fases);
		$reportabilidad = ($this->input->post('reportability')) ? 1 : 0;
		
		$considering = $this->input->post('considering');
		$condition_or_commitment = $this->input->post('condition_or_commitment');
		$descripcion = $this->input->post('short_description');
		
		if($tipo_matriz == "rca"){
			$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			$matriz_info = $this->Compromises_rca_model->get_one($id_compromiso_proyecto);
			$project_info = $this->Projects_model->get_one($matriz_info->id_proyecto);
		}else{
			$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			$matriz_info = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto);
			$project_info = $this->Projects_model->get_one($matriz_info->id_proyecto);
		}
		
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

		}
		
		$json_datos = json_encode($array_datos);

		$data = array(
			"id_compromiso" => $id_compromiso_proyecto,
			"numero_compromiso" => $numero_compromiso,
			"nombre_compromiso" => $nombre_compromiso,
            "datos_campos" => $json_datos,
        );
		
		if($tipo_matriz == "rca"){
			$data["fases"] = $json_fases;
			$data["reportabilidad"] = $reportabilidad;
			$data["accion_cumplimiento_control"] = $this->input->post('compliance_action_control');
			$data["frecuencia_ejecucion"] = $this->input->post('execution_frequency');
		}else{
			$data["considerando"] = $considering;
			$data["condicion_o_compromiso"] = $condition_or_commitment;
			$data["descripcion"] = $descripcion;
		}
		
		if($id_elemento){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
		if($tipo_matriz == "rca"){
			$save_id = $this->Values_compromises_rca_model->save($data, $id_elemento);
		}else{
			
			// VALIDAR QUE NO VENGAN 2 O MAS VECES LAS MISMAS FECHAS DE PLANIFICACIÓN
			$array_fecha_termino = (array)$this->input->post('term_date');
			$valores_repetidos = (count(array_unique($array_fecha_termino)) != count($array_fecha_termino));
			
			if($valores_repetidos){
				echo json_encode(array("success" => false, 'message' => lang('repeated_planifications_message')));
				exit();
			}
			
			$save_id = $this->Values_compromises_reportables_model->save($data, $id_elemento);
		}
		
		if ($save_id) {
			
			if(!$id_elemento){ // Insert
				
				if($tipo_matriz == "rca"){
					// SI SE INGRESA EL COMPROMISO RCA, AUTOMATICAMENTE SE DEBEN INGRESAR TANTAS EVALUACIONES 
					// COMO EVALUADOS HAYAN RELACIONADOS A ESTE COMPROMISO Y CON ESTADO POR DEFECTO NO APLICA
					// PRIMERO VOY A BUSCAR EL ID DEL ESTADO NO APLICA DEL CLIENTE
					
					$estado_no_aplica = $this->Compromises_compliance_status_model->get_one_where(
						array(
							"id_cliente" => $project_info->client_id, 
							"tipo_evaluacion" => "rca", 
							"categoria" => "No Aplica", 
							"deleted" => 0
						)
					);
					$id_estado = $estado_no_aplica->id;
					
					$evaluados_matriz = $this->Evaluated_rca_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso_proyecto, "deleted" => 0))->result();
					foreach($evaluados_matriz as $evaluado){
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_evaluado"] = $evaluado->id;
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						//$data_compliance_evaluation["fecha_evaluacion"] = "2018-10-24";
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						$evaluation_save_id = $this->Compromises_compliance_evaluation_rca_model->save($data_compliance_evaluation);
						
					}
				}else{// INGRESO DE COMPROMISO REPORTABLE
					
					$array_descripcion = (array)$this->input->post('description');
					$array_fecha_termino = (array)$this->input->post('term_date');
					array_shift($array_descripcion);
					array_shift($array_fecha_termino);
					
					$array_planificaciones = array();
					foreach($array_descripcion as $index => $descripcion){
						$fecha_termino = $array_fecha_termino[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $save_id;
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;

						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);
						
						// Crear configuración por cada planificación de evaluación, con valores vacíos (ok).
						$data_alert_config = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"risk_value" => "",
								"threshold_value" => ""
							)),
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						
						$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
						
						// Guardar histórico alertas por cada planificación de evaluación
						$data_historical_alert = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_user" => $this->login_user->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"id_valor_compromiso" => (string)$save_id,
								"tipo_evaluacion" => "reportable",
							), TRUE),
							"id_alert_projects" => $alert_save_id,
							"id_element" => $save_id,
							"alert_date" => get_current_utc_time()
						);
						
						$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);
												
					}
				}
				
			}else{ // Update
				
				if($tipo_matriz == "rca"){
					
				}else{
					$array_descripcion = (array)$this->input->post('description');
					$array_fecha_termino = (array)$this->input->post('term_date');
					array_shift($array_descripcion);
					array_shift($array_fecha_termino);
					
					$array_planificaciones = array();
					foreach($array_descripcion as $index => $descripcion){
						$fecha_termino = $array_fecha_termino[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $save_id;
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $project_info->client_id, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $save_id;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);
						
						// Crear configuración por cada planificación de evaluación, con valores vacíos (ok).
						$data_alert_config = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"risk_value" => "",
								"threshold_value" => ""
							)),
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						
						$alert_save_id = $this->AYN_Alert_projects_model->save($data_alert_config);
						
						// Guardar histórico alertas por cada planificación de evaluación
						$data_historical_alert = array(
							"id_client" => $project_info->client_id,
							"id_project" => $project_info->id,
							"id_user" => $this->login_user->id,
							"id_client_module" => 6, // Compromisos
							"id_client_submodule" => 22, // Evaluación de Compromisos Reportables
							"alert_config" => json_encode(array(
								"id_planificacion" => (string)$fila_planificacion["id"],
								"id_valor_compromiso" => (string)$save_id,
								"tipo_evaluacion" => "reportable",
							), TRUE),
							"id_alert_projects" => $alert_save_id,
							"id_element" => $save_id,
							"alert_date" => get_current_utc_time()
						);
						
						$historical_alert_save_id = $this->AYN_Alert_historical_model->save($data_historical_alert);

					}
					
				}
				
			}
			
			if($tipo_matriz == "rca"){
				$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
				$elemento_compromiso = $this->Values_compromises_rca_model->get_one($save_id);
				$matriz = $this->Compromises_rca_model->get_one($elemento_compromiso->id_compromiso);
			}else{
				$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
				$elemento_compromiso = $this->Values_compromises_reportables_model->get_one($save_id);
				$matriz = $this->Compromises_reportables_model->get_one($elemento_compromiso->id_compromiso);
			}
			
			$proyecto = $this->Projects_model->get_one($matriz->id_proyecto);
			$id_cliente = $proyecto->client_id;
			// Guardar histórico notificaciones
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $proyecto->id,
				"id_user" => $this->login_user->id,
				"module_level" => "admin",
				"id_admin_module" => $this->id_admin_module,
				"id_admin_submodule" => $this->id_admin_submodule,
				"event" => ($tipo_matriz == "rca") ? "comp_rca_add" : "comp_rep_add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);

            echo json_encode(array("success" => true, "data" => $this->_row_data_carga_individual($save_id, $columnas, $id_compromiso_proyecto, $tipo_matriz), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_carga_individual($id_compromiso_proyecto = 0, $tipo_matriz){
		
		$options = array(
			"id_compromiso" => $id_compromiso_proyecto
		);
		
		if($tipo_matriz == "rca"){
			$list_data = $this->Values_compromises_rca_model->get_details($options)->result();
			$columnas = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		}else{
			$list_data = $this->Values_compromises_reportables_model->get_details($options)->result();
			$columnas = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		}
		
		$result = array();
		foreach($list_data as $data) {
			$result[] = $this->_make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz);
		}
		
		echo json_encode(array("data" => $result));	
	}
	
	function _row_data_carga_individual($id, $columnas, $id_compromiso_proyecto, $tipo_matriz){
		
		$options = array(
            "id" => $id
        );
		
		if($tipo_matriz == "rca"){
			$data = $this->Values_compromises_rca_model->get_details($options)->row();
		}else{
			$data = $this->Values_compromises_reportables_model->get_details($options)->row();
		}

        return $this->_make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz);
		
	}
	
	function _make_row_carga_individual($data, $columnas, $id_compromiso_proyecto, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}else{
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->numero_compromiso;
		$row_data[] = $data->nombre_compromiso;
		
		if($tipo_matriz == "rca"){
			$fases_decoded = json_decode($data->fases);
			$html_fases = "";
			foreach($fases_decoded as $id_fase){
				$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
				$nombre_fase = lang($nombre_lang);
				$html_fases .= "&bull; " . $nombre_fase . "<br>";
			}
			$row_data[] = $html_fases;
			$row_data[] = ($data->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
		}else{
			$tooltip_considerando = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->considerando.'"><i class="fas fa-info-circle fa-lg"></i></span>';
			$row_data[] = ((!$data->considerando) || $data->considerando == "") ? "-" : $tooltip_considerando;
			
			$tooltip_condicion_o_compromiso = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->condicion_o_compromiso.'"><i class="fas fa-info-circle fa-lg"></i></span>';
			$row_data[] = ((!$data->condicion_o_compromiso) || $data->condicion_o_compromiso == "") ? "-" : $tooltip_condicion_o_compromiso;
			
			$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
			$row_data[] = ((!$data->descripcion) || $data->descripcion == "") ? "-" : $tooltip_observaciones;
			/*$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0,
				)
			)->result();
			
			$html_planes = "";
			foreach($planificaciones as $planificacion){
				$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
			}
			$row_data[] = $html_planes;*/
		}
		
		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if($columna->id_tipo_campo == 2){ // Si es text area
						
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					
					}elseif($columna->id_tipo_campo == 4){//si es fecha.
						$valor_campo = get_date_format($arreglo_fila[$columna->id_campo],$id_proyecto);
					}elseif($columna->id_tipo_campo == 5){// si es periodo
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = $start_date.' - '.$end_date;
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}elseif($columna->id_tipo_campo == 14){
						$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]);
					}
					else{
						$valor_campo = $arreglo_fila[$columna->id_campo];
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
		
		if($tipo_matriz == "rca"){
			$row_data[] = $data->accion_cumplimiento_control;
			$row_data[] = $data->frecuencia_ejecucion;
		}else{
			
			$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0,
				)
			)->result();
			
			$html_planes = "";
			foreach($planificaciones as $planificacion){
				$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
			}
			$row_data[] = $html_planes;
			
		}
		
		$view = modal_anchor(get_uri("upload_compromises/preview/" .$id_compromiso_proyecto."/".$tipo_matriz), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang('view_compromise'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("upload_compromises/modal_form_carga_individual/".$id_compromiso_proyecto."/".$tipo_matriz), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_compromise'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compromise'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("upload_compromises/delete/".$tipo_matriz), "data-action" => "delete-confirmation"));
		
		$row_data[] = $view . $edit . $delete;
		
		return $row_data;
		
	}
	
	function carga_masiva($id_compromiso_proyecto, $tipo_matriz) {
		
		$id_compromiso = $id_compromiso_proyecto;
		if($tipo_matriz == "rca"){
			$compromiso = $this->Compromises_rca_model->get_one($id_compromiso);
		}else{
			$compromiso = $this->Compromises_reportables_model->get_one($id_compromiso);
		}
		
		$id_proyecto = $compromiso->id_proyecto;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_cliente = $proyecto->client_id;
		
		$excel_template = $this->get_excel_template_of_compromise($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz);
		
		$view_data["id_cliente"] = $id_cliente;
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["id_compromiso"] = $id_compromiso;
		$view_data["excel_template"] = $excel_template;
		$view_data["tipo_matriz"] = $tipo_matriz;
		
        $this->load->view('upload_compromises/carga_masiva/index', $view_data);
    }
	
	function save_carga_masiva($tipo_matriz){
		
		$id_cliente = $this->input->post('id_cliente');
		$id_proyecto = $this->input->post('id_proyecto');
		$id_compromiso_proyecto = $this->input->post('id_compromiso');
		$file = $this->input->post('archivo_importado');
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
		}

		$archivo_subido = move_temp_file($file, "files/carga_masiva_compromisos/client_".$id_cliente."/project_".$id_proyecto."/", "", "", $file);
		
		if($archivo_subido){
			
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			$campos_compromiso = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
			
			$html = '<table class="table table-responsive table-striped">';
			$html .= '<thead><tr>';
			$html .= '<th></th>';
			
			$valor_columna_a1 = $worksheet->getCell('A1')->getValue();
			$valor_columna_b1 = $worksheet->getCell('B1')->getValue();
			$valor_columna_c1 = $worksheet->getCell('C1')->getValue();
			$valor_columna_d1 = $worksheet->getCell('D1')->getValue();
						
			/*if($valor_columna_a1 != lang('name')){ $num_errores++; var_dump("error a1 :D");}
			if($valor_columna_b1 != lang('phases')){ $num_errores++; var_dump("error b1 :D");}
			if($valor_columna_c1 != lang('reportability')){ $num_errores++; var_dump("error c1 :D");}
			
			exit();*/
			//$cont = 0;
			
			if(lang('compromise_number') == $worksheet->getCell('A1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($tipo_matriz == "rca"){
				if(lang('name') == $worksheet->getCell('B1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('phases') == $worksheet->getCell('C1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('reportability') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
			
				$cont = 4;
				
			}else{
				
				if(lang('reportable_matrix_name') == $worksheet->getCell('B1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('considering') == $worksheet->getCell('C1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('condition_or_commitment') == $worksheet->getCell('D1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if(lang('short_description') == $worksheet->getCell('E1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('E1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('E1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				$cont = 5;
				
			}
			
			foreach($campos_compromiso as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
				//var_dump($campo->nombre_campo);
				//var_dump($valor_columna);
				//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
				if($campo->nombre_campo == $valor_columna){
					$html .= '<th>'.$valor_columna.'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
					$num_errores++;
				}
				$cont++;
			}
			
			if($tipo_matriz == "rca"){
			
				if(lang('compliance_action_control') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				$cont++;
				
				if(lang('execution_frequency') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
			}else{
				
				if(lang('planning_description') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				$cont++;
				
				if(lang('planning_date') == $worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue()){
						$html .= '<th>'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell($this->getNameFromNumber($cont).'1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
			}
			
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			
			if($tipo_matriz == "rca"){
			
				// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ
				$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
				$array_fases_disponibles = array();
				foreach($fases_disponibles as $fase){
					$array_fases_disponibles[] = lang($fase["nombre_lang"]);
				}
			}
			
			// DATOS DEL CUERPO
			for($row = 2; $row <= $lastRow; $row++){
				$html .= '<tr>';
				$html .= '<td>'.$row.'</td>';
				
				//NUMERO COMPROMISO
				$numero_compromiso = $worksheet->getCell('A'.$row)->getValue();
				if(strlen(trim($numero_compromiso)) > 0){

					if(is_numeric($numero_compromiso)){
						$html .= '<td>'.$numero_compromiso.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$numero_compromiso.' '.$msg_formato.'</td>';
						$num_errores++;
					}

				}else{
					$html .= '<td class="error app-alert alert-danger">'.$numero_compromiso.' '.$msg_obligatorio.'</td>';
					$num_errores++;
				}
				
				// CELDA NOMBRE
				$nombre_compromiso = $worksheet->getCell('B'.$row)->getValue();
				if(strlen(trim($nombre_compromiso)) > 0){
					$html .= '<td>'.$nombre_compromiso.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$nombre_compromiso.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				
				if($tipo_matriz == "rca"){
					
					// CELDA FASES
					$fases = $worksheet->getCell('C'.$row)->getValue();
					$array_fases = explode(',', $fases);
					$array_fases_final = array();
					$error_fases = FALSE;
					
					foreach($array_fases as $nombre_fase){
						$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
						if(!in_array($nombre_fase_limpia, $array_fases_disponibles)){
							$error_fases = TRUE;
						}
						$array_fases_final[] = $nombre_fase_limpia;
					}
					
					$html_fases = "";
					foreach($array_fases_final as $nombre_fase){
						$html_fases .= "&bull; " . $nombre_fase . "<br>";
					}
					
					if(!$error_fases){
						$html .= '<td>'.$html_fases.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_fases.' '.$msg_formato.'</td>';
						$num_errores++;
					}
						
					// CELDA REPORTABILIDAD
					$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
					$reportabilidad_mayus = strtoupper($reportabilidad);
					
					if($reportabilidad_mayus == "SI"){
						$html .= '<td><i class="fa fa-check" aria-hidden="true"></i></td>';
					} else if($reportabilidad_mayus == "NO"){
						$html .= '<td><i class="fa fa-times" aria-hidden="true"></i></td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$reportabilidad.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
				}else{
					
					// CELDA CONSIDERANDO
					$considering = $worksheet->getCell('C'.$row)->getValue();
					if(strlen(trim($considering)) > 0){
						$html .= '<td>'.$considering.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$considering.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA CONDICION
					$condition_or_commitment = $worksheet->getCell('D'.$row)->getValue();
					if(strlen(trim($condition_or_commitment)) > 0){
						$html .= '<td>'.$condition_or_commitment.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$condition_or_commitment.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA DESCRIPCION
					$descripcion = $worksheet->getCell('E'.$row)->getValue();
					if(strlen(trim($descripcion)) > 0){
						$html .= '<td>'.$descripcion.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$descripcion.' '.$msg_formato.'</td>';
						$num_errores++;
					}
				}
				
				// OTRAS CELDAS
				if($tipo_matriz == "rca"){
					$cont = 4;
				}else{
					$cont = 5;
				}
				foreach($campos_compromiso as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
					
					if($campo->id_tipo_campo == 1){
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
						
					}
					if($campo->id_tipo_campo == 2){
						if($campo->obligatorio){

							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
					}
					if($campo->id_tipo_campo == 3){
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if(is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
										
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || is_numeric($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
						
					}
					if($campo->id_tipo_campo == 4){
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if($this->validateDate($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || $this->validateDate($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
						
					}
					if($campo->id_tipo_campo == 5){
						
						if($campo->obligatorio){
							if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
							
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
									if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}else{
							
							if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
									if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}elseif(strlen($valor_columna) == 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							
						}
						
					}
					if($campo->id_tipo_campo == 6){
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							if($campo->obligatorio){
								if($op->value == ""){continue;}
							}else{
								if($op->value == ""){
									$opciones[] = "";
									continue;

								}
							}
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						
					}
					if($campo->id_tipo_campo == 7){//select_multiple
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							if($campo->obligatorio){
								if($op->value == ""){continue;}
							}else{
								if($op->value == ""){
									$opciones[] = "";
									continue;

								}
							}
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					}
					if($campo->id_tipo_campo == 8){
						// POR AHORA NO ESTAMOS VALIDANDO CAMPO RUT
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							$html .= '<td>'.$valor_columna.'</td>';
						}
						
					}
					if($campo->id_tipo_campo == 9){
						// CAMPO RADIO, SIEMPRE SERA OBLIGATORIO
						
						$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							$opciones[] = $op->value;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					
					}
					if($campo->id_tipo_campo == 13){
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								if(valid_email($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || valid_email($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
					}
					if($campo->id_tipo_campo == 14){
						// OJO CON ESTE, DEPENDE DEL FORMATO DE HORA
						
						if($campo->obligatorio){
							//if(strlen($valor_columna) == 8){// 12:00 PM
							if(strlen($valor_columna) == 5){// 12:00
								//if(preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
								if(preg_match('/\d{2}:\d{2}/', $valor_columna)){
									$hora = explode(":", $valor_columna);
									if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
										$html .= '<td>'.$valor_columna.'</td>';
									} else {
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}elseif(strlen(trim($valor_columna)) == 0){
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}else{
							//if($valor_columna == "" || preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
							if($valor_columna == "" || preg_match('/\d{2}:\d{2}/', $valor_columna)){
								$hora = explode(":", $valor_columna);
								if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
									$html .= '<td>'.$valor_columna.'</td>';
								} else {
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}

					}
					if($campo->id_tipo_campo == 15){
						
						if($campo->obligatorio){
							if(strlen(trim($valor_columna)) > 0){
								
								if(is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
										
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
								$num_errores++;
							}
						}else{
							if($valor_columna == "" || is_numeric($valor_columna)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						}
						
					}
					
					$cont++;
				}
				
				if($tipo_matriz == "rca"){
					
					// CELDA ACCION
					$accion = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					if(strlen(trim($accion)) > 0){
						$html .= '<td>'.$accion.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$accion.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					$cont++;
					
					// CELDA FRECUENCIA EJECUCION
					$frecuencia = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					if(strlen(trim($frecuencia)) > 0){
						$html .= '<td>'.$accion.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$frecuencia.' '.$msg_formato.'</td>';
						$num_errores++;
					}
				}else{
					
					// CELDA DESCRIPCIONES
					$descripciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					$array_descripciones = explode(';', $descripciones);
					
					$html_descripciones = "";
					foreach($array_descripciones as $desc){
						$html_descripciones .= "&bull; " . $desc . "<br>";
					}
					
					if(count($array_descripciones) > 0){
						$html .= '<td>'.$html_descripciones.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_descripciones.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					$cont++;
					
					// CELDA PLANIFICACIONES
					$planificaciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
					$array_planificaciones = explode(';', $planificaciones);
					$error_planificaciones = FALSE;
					
					$html_planificaciones = "";
					foreach($array_planificaciones as $plan){
						
						$html_planificaciones .= "&bull; " . $plan . "<br>";
						
						if(strlen($plan) == 10){// YYYY-MM-DD
						
							if($this->validateDate($plan)){
								
							}else{
								$error_planificaciones = TRUE;
							}
						}else{
							$error_planificaciones = TRUE;
						}
						
					}
					
					if(count($array_planificaciones) != count($array_descripciones)){
						$error_planificaciones = TRUE;
					}
					
					if(!$error_planificaciones){
						$html .= '<td>'.$html_planificaciones.'</td>';
					} else {
						$html .= '<td class="error app-alert alert-danger">'.$html_planificaciones.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
				}
				

				$html .= '</tr>';

			}
			
			$html .= '</tbody>';
			$html .= '</table>';			

			if($num_errores > 0){
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
			}else{
				$this->bulk_load($id_cliente, $id_proyecto, $id_compromiso_proyecto, $archivo_subido, $tipo_matriz);
				//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
			}
			
			exit();

			
		}
		
	}
	
	function bulk_load($id_cliente, $id_proyecto, $id_compromiso_proyecto, $archivo_subido, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
			$Values_compromises_model = $this->Values_compromises_rca_model;
			$Evaluated_compromises_model = $this->Evaluated_rca_compromises_model;
			$Compromises_compliance_evaluation_model = $this->Compromises_compliance_evaluation_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
			$Values_compromises_model = $this->Values_compromises_reportables_model;
			$Evaluated_compromises_model = $this->Plans_reportables_compromises_model;
			$Compromises_compliance_evaluation_model = $this->Compromises_compliance_evaluation_reportables_model;
		}
		
		$compromiso = $Compromises_model->get_one($id_compromiso_proyecto);

		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();
		$campos_compromiso = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result();
		$array_insert = array();
		$array_planes = array();
		
		if($tipo_matriz == "rca"){
			// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ, CON LOS LANG
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$array_fases_disponibles = array();
			foreach($fases_disponibles as $fase){
				$array_fases_disponibles[lang($fase["nombre_lang"])] = $fase["id"];
			}
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			$numero_compromiso = (int)$worksheet->getCell('A'.$row)->getValue();
			$nombre_compromiso = $worksheet->getCell('B'.$row)->getValue();
			
			if($tipo_matriz == "rca"){
				//
				$fases = $worksheet->getCell('C'.$row)->getValue();
				$array_fases = explode(',', $fases);
				$array_fases_final = array();
				
				foreach($array_fases as $nombre_fase){
					$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
					$id_fase = $array_fases_disponibles[$nombre_fase_limpia];
					$array_fases_final[] = $id_fase;
				}
				$array_fases_json = json_encode($array_fases_final);
				//
			
				$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
				$reportabilidad_mayus = strtoupper($reportabilidad);
				
				if($reportabilidad_mayus == "SI"){
					$reportabilidad = 1;
				} else {
					$reportabilidad = 0;
				}
			}else{
				$considering = $worksheet->getCell('C'.$row)->getValue();
				$condition_or_commitment = $worksheet->getCell('D'.$row)->getValue();
				$descripcion = $worksheet->getCell('E'.$row)->getValue();
			}
			
			if($tipo_matriz == "rca"){
				$cont = 4;
			}else{
				$cont = 5;
			}
			$array_campos_json = array();
			foreach($campos_compromiso as $campo){
				
				if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
					$array_campos_json["$campo->id_campo"] = NULL;
					continue;
				}
				
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
				//echo var_dump($letra_columna.$row.' - '.$campo->id_tipo_campo.': '.$valor_columna);
				
				if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					// CAMPO DESHABILITADO = 1
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 5){
					if($campo->obligatorio){
						$array_periodo = explode("/", $valor_columna);
						$fecha_desde = $array_periodo[0];
						$fecha_hasta = $array_periodo[1];
						$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
					}else{
						/*if(trim($valor_columna) == ""){
							$json_periodo = array("start_date" => "", "end_date" => "");
						}else{
							$array_periodo = explode("/", $valor_columna);
							$fecha_desde = $array_periodo[0];
							$fecha_hasta = $array_periodo[1];
							$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
						}*/
						if($campo->habilitado == 1){
							if(trim($valor_columna) == ""){
								$json_periodo = array("start_date" => "", "end_date" => "");
							}else{
								$periodo = json_decode($campo->default_value);
								$json_periodo = array("start_date" => $periodo->start_date, "end_date" => $periodo->end_date);
							}
							
						}else{
							if(trim($valor_columna) == ""){
								$json_periodo = array("start_date" => "", "end_date" => "");
							}else{
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
							}
						}
						
					}
					
					$array_campos_json["$campo->id_campo"] = $json_periodo;
				}
				if($campo->id_tipo_campo == 6){
					/*$ops = json_decode($campo->opciones);
					$opciones = array();
					foreach($ops as $op){
						if($campo->obligatorio){
							if($op->value == ""){continue;}
						}else{
							if($op->value == ""){
								$opciones[""] = "";
								continue;

							}
						}
						$opciones[$op->text] = $op->value;
					}
					
					$array_campos_json["$campo->id_campo"] = $opciones[$valor_columna];*/
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
					
				}
				if($campo->id_tipo_campo == 8){// RUT
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 9){// RADIO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 13){// CORREO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 14){// HORA
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 15){// UNIDAD
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				
				$cont++;
			}
			
			if($tipo_matriz == "rca"){
				$accion = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
				$frecuencia = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
			}else{
				$descripciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
				$planificaciones = $worksheet->getCell($this->getNameFromNumber($cont).$row)->getValue();
				$cont++;
			}
			
			
			
			$array_row["id_compromiso"] = $id_compromiso_proyecto;
			$array_row["numero_compromiso"] = $numero_compromiso;
			
			if($tipo_matriz == "rca"){
				$array_row["nombre_compromiso"] = $nombre_compromiso;
				$array_row["fases"] = $array_fases_json;
				$array_row["reportabilidad"] = $reportabilidad;
				$array_row["accion_cumplimiento_control"] = $accion;
				$array_row["frecuencia_ejecucion"] = $frecuencia;
			}else{
				$array_row["nombre_compromiso"] = $nombre_compromiso;
				$array_row["considerando"] = $considering;
				$array_row["condicion_o_compromiso"] = $condition_or_commitment;
				$array_row["descripcion"] = $descripcion;
				
				$array_planes[] = array("descripcion" => $descripciones, "fecha" => $planificaciones);
			}
			
			$json_datos_campos = json_encode($array_campos_json);
			$array_row["datos_campos"] = $json_datos_campos;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["modified_by"] = NULL;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$array_insert[] = $array_row;
		}// FIN FOR ROW
	
		$bulk_load = $Values_compromises_model->bulk_load($array_insert);
		if($bulk_load){
			
			if($tipo_matriz == "rca"){
				// SI SE INGRESAN LOS COMPROMISOS, AUTOMATICAMENTE SE DEBEN INGRESAR TANTAS EVALUACIONES 
				// COMO EVALUADOS HAYAN RELACIONADOS A ESTOS COMPROMISOS Y CON ESTADO POR DEFECTO NO APLICA
				// PRIMERO VOY A BUSCAR EL ID DEL ESTADO NO APLICA DEL CLIENTE
				
				$first_id = $this->db->insert_id();
				$last_id = $first_id + (count($array_insert) - 1);
				
				$array_ides = array();
				for($i = $first_id; $i <= $last_id; $i++) {
					$array_ides[] = $i;
				}
				
				foreach($array_insert as $index => $compromiso){
					$id_compromiso = $array_ides[$index];
					
					$estado_no_aplica = $this->Compromises_compliance_status_model->get_one_where(
						array(
							"id_cliente" => $id_cliente, 
							"categoria" => "No Aplica", 
							"deleted" => 0
						)
					);
					$id_estado = $estado_no_aplica->id;
					
					$evaluados_matriz = $Evaluated_compromises_model->get_all_where(array("id_compromiso" => $compromiso["id_compromiso"], "deleted" => 0))->result();
					foreach($evaluados_matriz as $evaluado){
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $id_compromiso;
						$data_compliance_evaluation["id_evaluado"] = $evaluado->id;
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						//$data_compliance_evaluation["fecha_evaluacion"] = "2018-10-24";
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						$evaluation_save_id = $Compromises_compliance_evaluation_model->save($data_compliance_evaluation);
						
					}
				}
			}else{
				// SI SE INGRESAN LOS COMPROMISOS, AUTOMATICAMENTE SE DEBEN INGRESAR LAS PLANIFICACIONES
				// Y TANTAS EVALUACIONES COMO PLANIFICACIONES HAYAN RELACIONADOS A ESTOS COMPROMISOS 
				// Y CON ESTADO POR DEFECTO "NO CUMPLE" O "PENDIENTE" DEPENDIENDO DE LA FECHA DE LA PLANIFICACION
				
				$first_id = $this->db->insert_id();
				$last_id = $first_id + (count($array_insert) - 1);
				
				$array_ides = array();
				for($i = $first_id; $i <= $last_id; $i++) {
					$array_ides[] = $i;
				}
				
				foreach($array_insert as $index => $compromiso){
					$id_compromiso = $array_ides[$index];
					
					$descripciones = $array_planes[$index]["descripcion"];
					$planificaciones = $array_planes[$index]["fecha"];
					
					$array_descripciones = explode(';', $descripciones);
					$array_plans = explode(';', $planificaciones);
					
					$array_planificaciones = array();
					foreach($array_descripciones as $index => $descripcion){
						$fecha_termino = $array_plans[$index];
						
						$data_planificacion = array();
						$data_planificacion["id_compromiso"] = $id_compromiso;
						$data_planificacion["descripcion"] = $descripcion;
						$data_planificacion["planificacion"] = $fecha_termino;
						$data_planificacion["created_by"] = $this->login_user->id;
						$data_planificacion["created"] = get_current_utc_time();
						
						$plan_save_id = $this->Plans_reportables_compromises_model->save($data_planificacion);
						
						if($plan_save_id){
							$data_planificacion["id"] = $plan_save_id;
							$array_planificaciones[] = $data_planificacion;
						}
					}
					
					// CONTINUO CON LAS COMBINACIONES COMPROMISO-PLANIFICACION
					// (CON ESTADO SEGUN CRITERIO DE FECHA)
					
					foreach($array_planificaciones as $fila_planificacion){
						
						$fecha = $fila_planificacion["planificacion"];
						
						if($fecha < date("Y-m-d")){
							// no cumple
							$estado_no_cumple = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $id_cliente, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "No Cumple", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_no_cumple->id;
						}else{
							// pendiente
							$estado_pendiente = $this->Compromises_compliance_status_model->get_one_where(
								array(
									"id_cliente" => $id_cliente, 
									"tipo_evaluacion" => "reportable", 
									"categoria" => "Pendiente", 
									"deleted" => 0
								)
							);
							$id_estado = $estado_pendiente->id;
						}
						
						$data_compliance_evaluation = array();
						$data_compliance_evaluation["id_valor_compromiso"] = $id_compromiso;
						$data_compliance_evaluation["id_planificacion"] = $fila_planificacion["id"];
						$data_compliance_evaluation["id_estados_cumplimiento_compromiso"] = $id_estado;
						$data_compliance_evaluation["observaciones"] = NULL;
						$data_compliance_evaluation["responsable"] = $this->login_user->id;
						$data_compliance_evaluation["fecha_evaluacion"] = get_current_utc_time();
						$data_compliance_evaluation["created_by"] = $this->login_user->id;
						$data_compliance_evaluation["created"] = get_current_utc_time();
						
						$evaluation_save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation);
						
					}
					
					
				}
			}
			
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}
		
	}
	
	
	function preview($id_record = 0, $tipo_matriz){
		
		$id_compromiso_proyecto = $id_record;
		$id_elemento = $this->input->post('id');
		
		if($tipo_matriz == "rca"){
			$campos_compromiso = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}else{
			$campos_compromiso = $this->Compromises_reportables_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
		}
		
		$view_data['campos_compromiso'] = $campos_compromiso;
		$view_data['id_compromiso'] = $id_record;
		
		if($id_elemento){
			if($tipo_matriz == "rca"){
				$model_info = $this->Values_compromises_rca_model->get_one($id_elemento);
				
				$fases_decoded = json_decode($model_info->fases);
				$html_fases = "";
				foreach($fases_decoded as $id_fase){
					$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
					$nombre_fase = lang($nombre_lang);
					$html_fases .= "&bull; " . $nombre_fase . "<br>";
				}
				$view_data['html_fases'] = $html_fases;
				
			}else{
				$model_info = $this->Values_compromises_reportables_model->get_one($id_elemento);
				
				$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
					array(
						"id_compromiso" => $id_elemento,
						"deleted" => 0,
					)
				)->result();
				
				$html_planes = "";
				foreach($planificaciones as $planificacion){
					$html_planes .= "&bull; ".get_date_format($planificacion->planificacion, $id_proyecto)."<br>";
				}
				$view_data['html_planes'] = $html_planes;
				
			}
			
			$view_data['model_info'] = $model_info;
			
		}
		
		$view_data["Upload_compromises_controller"] = $this;
		$view_data["tipo_matriz"] = $tipo_matriz;

        $this->load->view('upload_compromises/carga_individual/view', $view_data);
		
	}
		
	function delete($tipo_matriz) {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		// SI EL COMPROMISO TIENE EVALUACION NO SE PUEDE ELIMINAR
		// POSTERIORMENTE SE CAMBIO, SI SE PUEDEN ELIMINAR Y DE PASO SE ELIMINAN SUS EVALUACIONES
		if($tipo_matriz == "rca"){
			$evaluaciones_compromiso = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array(
				"id_valor_compromiso" => $id,
				"deleted" => 0
			))->result();
			foreach($evaluaciones_compromiso as $evaluacion){
				$this->Compromises_compliance_evaluation_rca_model->delete($evaluacion->id);
			}
			
		}else{
			$evaluaciones_compromiso = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(array(
				"id_valor_compromiso" => $id,
				"deleted" => 0
			))->result();
			foreach($evaluaciones_compromiso as $evaluacion){
				$this->Compromises_compliance_evaluation_reportables_model->delete($evaluacion->id);
			}
		}
		
		/*if($evaluaciones_compromiso){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_compromise')));
			exit();
		}*/
		
		if($tipo_matriz == "rca"){
			$Values_compromises_model = $this->Values_compromises_rca_model;
		}else{
			$Values_compromises_model = $this->Values_compromises_reportables_model;
		}

        if ($this->input->post('undo')) {
            if ($Values_compromises_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($Values_compromises_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	/* devolver dropdown con los proyectos de un cliente */	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente, "deleted" => 0));
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	/* devolver dropdown con los tipos de matrices */	
	function get_matrix_types(){

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="matrix_type" class="col-md-2">'.lang('matrix_type').'</label>';
		$html .= '<div class="col-md-10">';
		$html .=  form_dropdown("matrix_type", array("" => "-", "rca" => lang("rca"), "reportable" => lang("reportable")), "", "id='matrix_type' class='select2 validate-hidden col-md-12' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	function get_upload_compromises_of_project(){
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		$tipo_matriz = $this->input->post("matrix_type");
		
		$view_data["nombre_proyecto"] = $this->Projects_model->get_one($id_proyecto)->title;
		if($tipo_matriz == "rca"){
			$view_data["id_compromiso_proyecto"] = $this->Compromises_rca_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		}else{
			$view_data["id_compromiso_proyecto"] = $this->Compromises_reportables_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		}
		
		$view_data["tipo_matriz"] = $tipo_matriz;
		
		$this->load->view("upload_compromises/upload_compromises_of_project", $view_data);
		
	}
	
	function get_field($id_campo, $id_elemento, $preview, $tipo_matriz){
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		if($opciones){
			$array_opciones = json_decode($opciones, true);
			$options = array();
			foreach($array_opciones as $opcion){
				$options[$opcion['value']] = $opcion['text'];
			}
		}
		
		$obligatorio = $datos_campo->obligatorio;
		$habilitado = $datos_campo->habilitado;

		if($id_elemento){
			if($tipo_matriz == "rca"){
				$row_elemento = $this->Values_compromises_rca_model->get_details(array("id" => $id_elemento))->result();
			}else{
				$row_elemento = $this->Values_compromises_reportables_model->get_details(array("id" => $id_elemento))->result();
			}
			
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
				"maxlength" => "255"
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
				"maxlength" => "2000"
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
	
	function get_field_value($id_campo, $id_elemento, $tipo_matriz) {
		
		if($tipo_matriz == "rca"){
			$id_compromiso = $this->Values_compromises_rca_model->get_one($id_elemento)->id_compromiso;
			$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso)->id_proyecto;
		}else{
			$id_compromiso = $this->Values_compromises_reportables_model->get_one($id_elemento)->id_compromiso;
			$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso)->id_proyecto;
		}
		
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
		
		if($tipo_matriz == "rca"){
			$row_elemento = $this->Values_compromises_rca_model->get_details(array("id" => $id_elemento))->result();
		}else{
			$row_elemento = $this->Values_compromises_reportables_model->get_details(array("id" => $id_elemento))->result();
		}
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
			$html = implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
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

	function get_excel_template_of_compromise($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz){
		
		if($tipo_matriz == "rca"){
			$Compromises_model = $this->Compromises_rca_model;
			$Values_compromises_model = $this->Values_compromises_rca_model;
		}else{
			$Compromises_model = $this->Compromises_reportables_model;
			$Values_compromises_model = $this->Values_compromises_reportables_model;
		}
		
		$columnas_campos = $Compromises_model->get_fields_of_compromise($id_compromiso_proyecto)->result_array();
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
		if($tipo_matriz == "rca"){
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_rca_template_excel').'_'.date("Y-m-d");
		}else{
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_reportable_template_excel').'_'.date("Y-m-d");
		}
		
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle(lang("template_compromise"))
							 ->setSubject(lang("template_compromise"))
							 ->setDescription(lang("template_compromise"))
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$doc->setActiveSheetIndex(0);
		
		// CREAR HOJA PARA OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN
		$doc->createSheet();
		// usar nueva hoja
		$doc->setActiveSheetIndex(1);
		//$doc->getActiveSheet()->setCellValue('A1', 'More data');
		$doc->getActiveSheet()->setTitle('options');
		//volver a usar la primera hoja
		$doc->setActiveSheetIndex(0);
		
		
		$doc->getActiveSheet()->setCellValue('A1', lang('compromise_number'));
		
		if($tipo_matriz == "rca"){
			$columna = 4;
			
			$doc->getActiveSheet()->setCellValue('B1', lang('name'));
			$doc->getActiveSheet()->setCellValue('C1', lang('phases'));
			$doc->getActiveSheet()->setCellValue('D1', lang('reportability'));
		}else{
			$columna = 5;
			
			$doc->getActiveSheet()->setCellValue('B1', lang('reportable_matrix_name'));
			$doc->getActiveSheet()->setCellValue('C1', lang('considering'));
			$doc->getActiveSheet()->setCellValue('D1', lang('condition_or_commitment'));
			$doc->getActiveSheet()->setCellValue('E1', lang('short_description'));
		}
		
		foreach($columnas_campos as $cc){
			
			if($cc["id_tipo_campo"] == 10 || $cc["id_tipo_campo"] == 11 || $cc["id_tipo_campo"] == 12){
				continue;
			}
			
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $cc["nombre_campo"]);
			
			if($cc["default_value"] && $cc["id_tipo_campo"] != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
			
				if($cc["id_tipo_campo"] == 5){	
					$periodo = json_decode($cc["default_value"]);
					$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
				} else {
					$valor_por_defecto = $cc["default_value"];
				}
				
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
				$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
				$comentario->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				}

			} else if(!$cc["default_value"] && $cc["id_tipo_campo"] != 16){
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
				
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					
					}
					
				}
	
			}
			
			$columna++;
		}
		
		if($tipo_matriz == "rca"){
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('compliance_action_control'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('execution_frequency'));
			//$columna++;
		}else{
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('planning_description'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', lang('planning_date'));
		}
		
		if($tipo_matriz == "rca"){
			$columna = 4;
		}else{
			$columna = 5;
		}
		
		// CUERPO DEL EXCEL
		//$doc->getActiveSheet()->setCellValue('A2', lang('excel_number_example'));
		$doc->getActiveSheet()->setCellValueExplicit('A2', lang('excel_number_example'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		if($tipo_matriz == "rca"){
			
			$doc->getActiveSheet()->setCellValue('B2', lang('excel_name_example'));
			
			// LISTA DEMO DE FASES A PARTIR DE LAS FASES REALES DEL SISTEMA
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$array_fases = array();
			foreach($fases_disponibles as $fase){
				$array_fases[] = lang($fase["nombre_lang"]);
			}
			
			$doc->getActiveSheet()->setCellValue('C2', implode(', ', $array_fases));
			
			//
			$array_opciones = array("Si", "No");
			$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
			//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
			$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
	
			if($array_opciones[0]){
				$doc->getActiveSheet()->setCellValue('D2', $array_opciones[0]);
			}
			//
			
			
		}else{
			
			$doc->getActiveSheet()->setCellValue('B2', lang('reportable_excel_name_example'));
			$doc->getActiveSheet()->setCellValue('C2', lang('reportable_excel_short_description_example'));
			$doc->getActiveSheet()->setCellValue('D2', lang('reportable_excel_short_description_example'));
			$doc->getActiveSheet()->setCellValue('E2', lang('reportable_excel_short_description_example'));
		}

		$options = array("id_compromiso" => $columnas_campos["id_compromiso"]);
		$list_data = $Values_compromises_model->get_details($options)->result();
		
		$columna_opciones = 0; //A
		
		foreach($columnas_campos as $campo){
			
			if($campo["id_tipo_campo"] == 10 || $campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
				continue;
			}
			
			if($campo["id_tipo_campo"] == 1){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_text'));
			}
			if($campo["id_tipo_campo"] == 2){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_textarea'));
			}
			if($campo["id_tipo_campo"] == 3){
				//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$numero_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_number');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			if($campo["id_tipo_campo"] == 4){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_date'));
			}
			if($campo["id_tipo_campo"] == 5){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_period'));
			}
			
			if($campo["id_tipo_campo"] == 6){
				$datos_campo = json_decode($campo["opciones"]);
				$array_opciones = array();
				foreach($datos_campo as $row){
					$label = $row->text;
					$value = $row->value;
					$array_opciones[] = $label;
				}
				array_shift($array_opciones);
				
				//GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				
				$fila_opcion = 1;
				foreach($array_opciones as $opcion){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
					$fila_opcion++;
				}

				$doc->setActiveSheetIndex(0);

				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
				//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
				//$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
				
				$cantidad_opciones_seleccion = count($array_opciones);
				if($cantidad_opciones_seleccion > 0){
					$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}

				//if($array_opciones[0]){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
				//}
				
				$columna_opciones++;
			}
			
			if($campo["id_tipo_campo"] == 7){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_select_multiple'));
			}
			if($campo["id_tipo_campo"] == 8){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_rut'));
			}
			if($campo["id_tipo_campo"] == 9){
				//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
				
				$datos_campo = json_decode($campo["opciones"]);
					
				$array_opciones = array();
				foreach($datos_campo as $row){
					$label = $row->text;
					$value = $row->value;
					$array_opciones[] = $label;
				}
				
				$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
				
				if($array_opciones[0]){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}
				
			}
			if($campo["id_tipo_campo"] == 13){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_mail'));
			}
			if($campo["id_tipo_campo"] == 14){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_time'));
			}
			if($campo["id_tipo_campo"] == 15){
				$unidad_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_unity');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			
			$columna++;
		}
		
		if($tipo_matriz == "rca"){
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_compliance_action_control_example'));
			$columna++;
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_execution_frequency_example'));
			$columna++;
		}else{
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('reportable_excel_descriptions_example'));
			$columna++;
			$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('reportable_excel_plans_example'));
			$columna++;
		}

		foreach(range('A', $this->getNameFromNumber($columna)) as $columnID) {
			$doc->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		$nombre_hoja = strlen(lang("compromises").' '.$nombre_proyecto)>31?substr(lang("compromises").' '.$nombre_proyecto, 0, 28).'...':lang("compromises").' '.$nombre_proyecto;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		// OCULTO HOJA OPTIONS
		$doc->getSheetByName('options')->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		
		$objWriter->save('files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx');
		
		if(!file_exists(__DIR__.'/../../files/carga_masiva_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx')) {
			echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
			exit();
		}
		
		$html = '';		
		$html .= '<div class="col-md-12">';
		$html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
		$html .= '<a href="'.get_uri("upload_compromises/download_compromise_template/".$id_compromiso_proyecto."/".$id_cliente."/".$id_proyecto."/".$tipo_matriz).'">'.$filename.'.xlsx</a>';
		$html .= '</div>';
		
		return $html;
		
	}
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	}
	
	function download_compromise_template($id_compromiso_proyecto, $id_cliente, $id_proyecto, $tipo_matriz) {
		
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;	
		$nombre_hoja = strlen(lang("compromises").' '.$nombre_proyecto)>31?substr(lang("compromises").' '.$nombre_proyecto, 0, 28).'...':lang("compromises").' '.$nombre_proyecto;
		
		if(!$id_compromiso_proyecto && !$id_cliente && !$id_proyecto){
			redirect("forbidden");
		}
		
		//$nombre_archivo = $this->clean($nombre_hoja);
		if($tipo_matriz == "rca"){
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_rca_template_excel').'_'.date("Y-m-d");
		}else{
			$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_reportable_template_excel').'_'.date("Y-m-d");
		}
		//$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('compromise_template_excel').'_'.date("Y-m-d");
		
        $file_data = serialize(array(array("file_name" => $filename.".xlsx")));
        download_app_files("files/carga_masiva_compromisos/client_".$id_cliente."/project_".$id_proyecto."/", $file_data, false);
		
    }
	
	function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}
		
	function validateDate($date){
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}
	
	/* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }
	
	/* check valid file for client */

    function validate_file() {
		
		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'xlsx') {
			echo json_encode(array("success" => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
    }
	
}

