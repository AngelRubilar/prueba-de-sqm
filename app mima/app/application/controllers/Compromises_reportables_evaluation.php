<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compromises_reportables_evaluation extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 6;
		$this->id_submodulo_cliente = 22;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}		
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    function index() {
		//$this->access_only_allowed_members();
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$access_info = $this->get_access_info("invoice");
		
		$id_proyecto = $this->session->project_context;	
		$proyect_info = $this->Projects_model->get_one($id_proyecto);

		$view_data["project_info"] = $proyect_info;
		
        $this->template->rander("compromises_reportables_evaluation/index", $view_data);
		
    }
	
	//modificar
	function modal_form() {
		$id_compromiso = $this->input->post("id_compromiso");
		$id_evaluacion = $this->input->post("id_evaluacion");
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		$id_cliente = $proyecto->client_id;
		
		$puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
		
		$view_data["puede_editar"] = $puede_editar;

		if ($id_evaluacion) {

			$evaluation_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion);
			$id_valor_compromiso = $evaluation_info->id_valor_compromiso;
			$id_planificacion = $evaluation_info->id_planificacion;
			$id_estado = $evaluation_info->id_estados_cumplimiento_compromiso;
			
			$compromiso_info = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso);
			
			$view_data["id_compromiso"] = $compromiso_info->id;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["label_column"] = "col-md-3";
			$view_data["field_column"] = "col-md-9";
			$view_data['model_info'] = $evaluation_info;
			
			$evaluations_dropdown = array();
			$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $id_valor_compromiso,
					"deleted" => 0
				)
			)->result();
			foreach($planificaciones as $plan){
				$evaluations_dropdown[$plan->id] = lang("plan_with_date") . " " . get_date_format($plan->planificacion, $id_proyecto);
			}
			$view_data["evaluations_dropdown"] = $evaluations_dropdown;
			$view_data["id_planificacion"] = $id_planificacion;
			$view_data["fecha_evaluacion"] = $evaluation_info->fecha_evaluacion;
			$view_data["ejecucion"] = $evaluation_info->ejecucion;
			$view_data["nombre_compromiso"] = $compromiso_info->nombre_compromiso;
			$view_data["estados"] = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "tipo_evaluacion" => "reportable"));
			$view_data["estado_evaluacion"] = $evaluation_info->id_estados_cumplimiento_compromiso;
			$view_data["observaciones"] = $evaluation_info->observaciones;
			
			// TRAER CAMPOS ADICIONALES EN CASO DE QUE EL ESTADO SEA NO CUMPLE
			$status_info = $this->Compromises_compliance_status_model->get_one($evaluation_info->id_estados_cumplimiento_compromiso);
			if($status_info->categoria == "No Cumple"){
				$view_data["no_cumple"] = $status_info->categoria;
				$view_data["id_criticidad"] = $evaluation_info->id_criticidad;
				$view_data["dropdown_criticidad"] = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");
				$view_data["responsable_reporte"] = $evaluation_info->responsable_reporte;
				$view_data["plazo_cierre"] = $evaluation_info->plazo_cierre;
				
			}

			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluation_info->id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result_array();
				
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
					$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$evaluation_info->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					
					$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $evaluation_info->id, "data-id_valor_compromiso" => $id_valor_compromiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_compromiso" => $select_valor_compromiso, "data-action-url" => get_uri("compromises_reportables_evaluation/delete_file"), "data-action" => "delete-confirmation"));				
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
				
			} else {
				
			}

			$this->load->view('compromises_reportables_evaluation/compliance_evaluation/modal_form', $view_data);

		} else {
            show_404();
		}

    }
	
	
	function save() {

        $plan_id = $this->input->post("evaluation");
		$id_compromiso = $this->input->post("id_compromiso");
		$file = $this->input->post('archivo_importado');
		$id_evidencia_eliminar = $this->input->post("id_evidencia_eliminar");
		
		validate_submitted_data(array(
            "plan_id" => "numeric",
        ));
		
		if($plan_id){
			
			// SI EL USUARIO HA ELIMINADO ARCHIVOS, BORRARLOS DE LA BASE DE DATOS Y FÍSICAMENTE
			if($id_evidencia_eliminar){
				
				foreach($id_evidencia_eliminar as $id_evidencia){

					$file_info = $this->Compromises_compliance_evidences_model->get_one($id_evidencia);
					$delete_evidence_id = $this->Compromises_compliance_evidences_model->delete($id_evidencia);
					$id_valor_compromiso = $this->Compromises_compliance_evaluation_reportables_model->get_one($compliance_evaluation_id)->id_valor_compromiso;
					$id_evaluado = $this->Compromises_compliance_evaluation_reportables_model->get_one($compliance_evaluation_id)->id_evaluado;
					$id_compromiso_proyecto = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso)->id_compromiso;
					
					$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso_proyecto)->id_proyecto;
					$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;

					if($delete_evidence_id){
						delete_file_from_directory("files/evaluaciones_compromisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$compliance_evaluation_id."/".$file_info->archivo);
					}

				}
				
			}
			
			// INFORMACION DE LA EVALUACION
			$evaluation_info = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
				array(
					"id_valor_compromiso" => $id_compromiso,
					"id_planificacion" => $plan_id,
					"deleted" => 0,
				)
			);
		
			// VERIFICAR SI SE MARCO UN ESTADO CON CAT. NO CUMPLE Y TRAER LOS DATOS DE SUS CAMPOS
			$id_estado = $this->input->post("estado");
			$estado = $this->Compromises_compliance_status_model->get_one($id_estado);
			$categoria = $estado->categoria;
			
			if($categoria == "No Cumple"){
				$id_criticidad = $this->input->post("criticidad");
				$report_responsible = $this->input->post("report_responsible");
				$plazo_cierre = $this->input->post("plazo_cierre");
			}
	
			$data_compliance_evaluation = array(
				"id_estados_cumplimiento_compromiso" => $this->input->post("estado"),
				"ejecucion" => $this->input->post("execution"),
				"id_criticidad" => ($id_criticidad)?$id_criticidad:NULL,
				"responsable_reporte" => $report_responsible?$report_responsible:NULL,
				"plazo_cierre" => $plazo_cierre?$plazo_cierre:NULL,
				"observaciones" => $this->input->post("observaciones"),
				"responsable" => $this->login_user->id,
				"fecha_evaluacion" => $this->input->post("fecha_evaluacion"),
				"modified_by" => $this->login_user->id,
				"modified" => get_current_utc_time(),
			);
			
			$save_id = $this->Compromises_compliance_evaluation_reportables_model->save($data_compliance_evaluation, $evaluation_info->id);
			
		}
		
        if ($save_id) {
			
			$id_valor_compromiso = $this->Compromises_compliance_evaluation_reportables_model->get_one($save_id)->id_valor_compromiso;
			//$id_evaluado = $this->Compromises_compliance_evaluation_reportables_model->get_one($save_id)->id_evaluado;
			$id_compromiso_proyecto = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso)->id_compromiso;
			
			if($file){
				//Si no existe el directorio para los archivos de evidencia, crearlo antes de mover el archivo.
				$crear_carpeta = $this->create_compliance_evidence_folder($save_id);
				
				$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one($save_id);
				$valor_compromiso = $this->Values_compromises_reportables_model->get_one($evaluacion->id_valor_compromiso);
				$matriz_compromiso = $this->Compromises_reportables_model->get_one($valor_compromiso->id_compromiso);
				$id_proyecto = $matriz_compromiso->id_proyecto;
				$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
				
				$archivo_subido = move_temp_file($file, "files/evaluaciones_compromisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$evaluacion->id."/");
				
				//Guardar el registro en la tabla de evidencias_cumplimiento_compromisos
				$datos_evidencia = array(
					"id_evaluacion_cumplimiento_compromiso" => $save_id,
					"tipo_evaluacion" => "reportable", 
					"archivo" => $archivo_subido,
					"created_by" => $this->login_user->id
				);
				$save_evidencia_id = $this->Compromises_compliance_evidences_model->save($datos_evidencia);
				
			}
			
			// Guardar histórico notificaciones
			$id_cliente = $this->login_user->client_id;
			$id_proyecto = $this->session->project_context;	
			$id_user = $this->session->user_id;
			
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"module_level" => "project",
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"event" => "edit",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
			// Guardar histórico alertas (Evaluación)
			$options_alerts = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"alert_config" => array(
					"id_valor_compromiso" => $id_valor_compromiso,
					"tipo_evaluacion" => "reportable"
				),				
				"id_element" => $save_id
			);
			ayn_save_historical_alert($options_alerts);
			
			echo json_encode(array("success" => true, "data" => $this->_row_data($id_compromiso), 'id' => $id_compromiso, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
				
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
		
		//$this->access_only_allowed_members();
		
		$id_proyecto = $this->session->project_context;
		$matriz = $this->Compromises_reportables_model->get_one_where(
			array(
				"id_proyecto" => $id_proyecto, 
				"deleted" => 0
			)
		);
		$id_matriz = $matriz->id;
		
		if($id_matriz){
			$options = array(
				"id_compromiso" => $id_matriz
			);
			
			$list_data = $this->Values_compromises_reportables_model->get_details($options)->result();
		}else{
			$list_data = array();
		}

		$result = array();
		foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
		
	}
	
	private function _row_data($id) {
        $data = $this->Values_compromises_reportables_model->get_one($id);
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_proyecto = $this->session->project_context;
		$nombre_compromiso = $data->nombre_compromiso;
		
		$puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
		
		// TRAER INFORMACION DE LA ULTIMA PLANIFICACION, ES DECIR, EL DE LA FECHA DE PLANIFICACION MAS TARDÍA
		// COMO SE PUEDEN REPETIR LAS FECHAS, PUEDE HABER MAS DE 1 
		$evaluaciones = $this->Plans_reportables_compromises_model->get_evaluations_of_compromise($data->id)->result();
		$ultima_fecha_modificacion = NULL;
		$id_evaluacion_fecha_modificacion = NULL;
		
		foreach($evaluaciones as $evaluacion){
			$id_planificacion = $evaluacion->id_planificacion;
			$fecha_modificacion = $evaluacion->modified;
			
			if(($fecha_modificacion) > $ultima_fecha_modificacion){
				$ultima_fecha_modificacion = $fecha_modificacion;
				$id_evaluacion_fecha_modificacion = $evaluacion->id;
			}
		}
		
		$ultima_fecha_plan = NULL;
		$id_ultimo_plan = NULL;
		if(!$id_evaluacion_fecha_modificacion){
			
			$planes = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $data->id,
					"deleted" => 0
				)
			)->result();
			
			foreach($planes as $plan){
				$planificacion = $plan->planificacion;
				
				if(($planificacion) > $ultima_fecha_plan){
					$ultima_fecha_plan = $planificacion;
					$id_ultimo_plan = $plan->id;
				}
			}
			
			$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
				array(
					"id_valor_compromiso" => $data->id,
					"id_planificacion" => $id_ultimo_plan,
				)
			);
			
			$id_evaluacion_fecha_modificacion = $evaluacion->id;
			
		}
		
		$evaluacion_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion_fecha_modificacion);
		
		//var_dump($evaluacion_info);
		
		// TRAER ESTADO
		$estado = $this->Compromises_compliance_status_model->get_one($evaluacion_info->id_estados_cumplimiento_compromiso);
		$nombre_estado = $estado->nombre_estado;
		$color_estado = $estado->color;
		$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado .= $nombre_estado;
		$html_estado .= '</div>';
		
		// BOTON EVIDENCIAS
		$existen_evaluaciones_con_archivos = FALSE;
		$evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_all_where(
			array(
				"id_valor_compromiso" => $data->id,
				"deleted" => 0,
			)
		)->result();
		
		foreach($evaluaciones as $evaluacion){
			$evidencias = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluacion->id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result_array();
			
			if($evidencias){
				$existen_evaluaciones_con_archivos = TRUE;
			} 
		}
		if($existen_evaluaciones_con_archivos){
			$modal_evidencias = modal_anchor(get_uri("compromises_reportables_evaluation/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion" => $evaluacion_info->id, "data-post-id_compromiso" => $data->id));					
		} else {
			$modal_evidencias = "-";
		}
		
		// OBSERVACION
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$evaluacion_info->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$observaciones = ((!$evaluacion_info->observaciones) || $evaluacion_info->observaciones == "") ? "-" : $tooltip_observaciones; 
		
		// FECHA DE EVALUACION
		$fecha_evaluacion = ($evaluacion_info->fecha_evaluacion) ? get_date_format($evaluacion_info->fecha_evaluacion, $id_proyecto) : "-";
		
		// RESPONSABLE
		$responsable = $this->Users_model->get_one($evaluacion_info->responsable);
		$nombre_responsable = $responsable->first_name." ".$responsable->last_name;
		
		$row_data = array(
			$data->numero_compromiso, 
			$nombre_compromiso,
			$html_estado,
			$modal_evidencias,
			$data->condicion_o_compromiso ? $data->condicion_o_compromiso : "-",
			$observaciones,
			$fecha_evaluacion,
			$nombre_responsable,	
		);
					
		if($puede_editar == 3){
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(), "<i class='fa fa-user-edit'></i>", array("class" => "edit", "title" => lang('evaluation_edit'), "data-post-id" => $data->id, "data-post-id_evaluacion" => $evaluacion_info->id,  "data-post-id_compromiso" => $data->id, "data-post-select_evaluado" => "1", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
		} else {
			$boton_editar = modal_anchor(get_uri("compromises_reportables_evaluation/modal_form"), "<i class='fa fa-user-edit'></i>", array("class" => "edit", "title" => lang('evaluation_edit'), "data-post-id" => $data->id, "data-post-id_evaluacion" => $evaluacion_info->id, "data-post-id_compromiso" => $data->id));
		}
		 
		$row_data[] = modal_anchor(get_uri("compromises_reportables_evaluation/view/"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluacion" => $evaluacion_info->id, "data-post-id_compromiso" => $data->id))
					. $boton_editar;
					 
        return $row_data;
		
    }
	
	function view() {
		
        //$this->access_only_allowed_members();
		
		$id_compromiso = $this->input->post("id_compromiso");
		$id_evaluacion = $this->input->post("id_evaluacion");
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		$id_cliente = $proyecto->client_id;

		if ($id_evaluacion) {
			
			$evaluation_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion);
			$id_valor_compromiso = $evaluation_info->id_valor_compromiso;
			$id_planificacion = $evaluation_info->id_planificacion;
			$id_estado = $evaluation_info->id_estados_cumplimiento_compromiso;
			
			$compromiso_info = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso);
			
			$view_data["id_compromiso"] = $compromiso_info->id;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["label_column"] = "col-md-3";
			$view_data["field_column"] = "col-md-9";
			$view_data['model_info'] = $evaluation_info;
			
			$evaluations_dropdown = array();
			$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
				array(
					"id_compromiso" => $id_valor_compromiso,
					"deleted" => 0
				)
			)->result();
			foreach($planificaciones as $plan){
				$evaluations_dropdown[$plan->id] = lang("plan_with_date") . " " . get_date_format($plan->planificacion, $id_proyecto);
			}
			$view_data["evaluations_dropdown"] = $evaluations_dropdown;
			$view_data["id_planificacion"] = $id_planificacion;
			$view_data["fecha_evaluacion"] = get_date_format($evaluation_info->fecha_evaluacion, $id_proyecto);
			$view_data["ejecucion"] = $evaluation_info->ejecucion;
			$view_data["nombre_compromiso"] = $compromiso_info->nombre_compromiso;
			//$view_data["estados"] = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "tipo_evaluacion" => "reportable"));
			//$view_data["estado_evaluacion"] = $evaluation_info->id_estados_cumplimiento_compromiso;
			$view_data["observaciones"] = $evaluation_info->observaciones;
			
			$status_info = $this->Compromises_compliance_status_model->get_one($evaluation_info->id_estados_cumplimiento_compromiso);
			
			
			$nombre_estado = $status_info->nombre_estado;
			$color_estado = $status_info->color;
			$html_estado = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
			$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$nombre_estado;
			$html_estado .= '</div>';
			$view_data["html_estado"] = $html_estado;
			
			// TRAER CAMPOS ADICIONALES EN CASO DE QUE EL ESTADO SEA NO CUMPLE
			if($status_info->categoria == "No Cumple"){
				$view_data["no_cumple"] = $status_info->categoria;
				$view_data["id_criticidad"] = $evaluation_info->id_criticidad;
				$view_data["dropdown_criticidad"] = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");
				$view_data["responsable_reporte"] = $evaluation_info->responsable_reporte;
				$view_data["plazo_cierre"] = get_date_format($evaluation_info->plazo_cierre, $id_proyecto);
				
			}
			
			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluation_info->id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result();
			
			if($archivos_evidencia){
				foreach($archivos_evidencia as $evidencia){
					$html_archivos_evidencia .= '<div class="col-md-8" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia->archivo);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia->id.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody>';
					$html_archivos_evidencia .= '<tr>';
					$html_archivos_evidencia .= '<td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$id_evaluacion. "/" . $evidencia->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</tbody>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
					$view_data["evidencia"] = $html_archivos_evidencia;
				}
			} else {
				$view_data["evidencia"] = lang("no_evidence_files");
			}
			
			$observaciones = $evaluation_info->observaciones;
			$responsable = $this->Users_model->get_one($evaluation_info->responsable);
			$responsable = $responsable->first_name." ".$responsable->last_name;
			$ult_mod = ($evaluation_info->modified) ? time_date_zone_format($evaluation_info->modified, $id_proyecto) : time_date_zone_format($evaluation_info->created, $id_proyecto);
			
			$view_data["observaciones"] = ($observaciones) ? $observaciones : "-";
			$view_data["responsable"] = $responsable;
			$view_data["ult_mod"] = $ult_mod;

			$this->load->view('compromises_reportables_evaluation/compliance_evaluation/view', $view_data);

        } else {
            show_404();
        }
    }
	
	function view_evidences() {
		
        //$this->access_only_allowed_members();
		$id_compromiso = $this->input->post("id_compromiso");
		$compliance_evaluation_id = $this->input->post("id_evaluacion");
		$id_proyecto = $this->session->project_context;
		$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one($compliance_evaluation_id);
		$id_planificacion = $evaluacion->id_planificacion;
		
		/*$evaluaciones_evaluado_compromiso = $this->Compromises_compliance_evaluation_reportables_model->get_all_where_order_by_date_desc(array("id_evaluado" => $evaluacion->id_evaluado, "id_valor_compromiso" => $evaluacion->id_valor_compromiso))->result();
		$evaluations_dropdown = array();
		foreach($evaluaciones_evaluado_compromiso as $eec){
			$evaluations_dropdown[$eec->id] = lang("evaluation_with_date") . " " . get_date_format($eec->fecha_evaluacion, $this->session->project_context);
		}
		
		$view_data["evaluations_dropdown"] = $evaluations_dropdown;
		*/
		
		$evaluations_dropdown = array();
		$planificaciones = $this->Plans_reportables_compromises_model->get_all_where(
			array(
				"id_compromiso" => $id_compromiso,
				"deleted" => 0
			)
		)->result();
		foreach($planificaciones as $plan){
			$evaluations_dropdown[$plan->id] = lang("plan_with_date") . " " . get_date_format($plan->planificacion, $id_proyecto);
		}
		$view_data["evaluations_dropdown"] = $evaluations_dropdown;
		$view_data["id_planificacion"] = $id_planificacion;
		
        if ($compliance_evaluation_id) {
			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $compliance_evaluation_id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result_array();
            if ($archivos_evidencia) {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
				
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$compliance_evaluation_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				
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
				$this->load->view('compromises_reportables_evaluation/compliance_evaluation/view_evidences', $view_data);
				
            } else {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				$html_archivos_evidencia .= lang("no_evidence_files");
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				
				$view_data["html_archivos_evidencia"] = $html_archivos_evidencia;
				$this->load->view('compromises_reportables_evaluation/compliance_evaluation/view_evidences', $view_data);
                //show_404();
            }
        } else {
            show_404();
        }
    }
	
	function view_historical_evaluations(){
		
		$id_compromiso = $this->input->post("id_compromiso");
		$id_plan = $this->input->post("id_plan");
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		
		$evaluation_info = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
			array(
				"id_valor_compromiso" => $id_compromiso,
				"id_planificacion" => $id_plan,
				"deleted" => 0
				
			)
		);
		$compromiso = $this->Values_compromises_reportables_model->get_one($id_compromiso);
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		if($evaluation_info->id){
			
			$id_valor_compromiso = $evaluation_info->id_valor_compromiso;
			$id_planificacion = $evaluation_info->id_planificacion;
			$id_estado = $evaluation_info->id_estados_cumplimiento_compromiso;
			
			$compromiso_info = $this->Values_compromises_reportables_model->get_one($id_valor_compromiso);
			
			$fecha_evaluacion = get_date_format($evaluation_info->fecha_evaluacion, $id_proyecto);
			$ejecucion = $evaluation_info->ejecucion;
			$nombre_compromiso = $compromiso_info->nombre_compromiso;
			$status_info = $this->Compromises_compliance_status_model->get_one($evaluation_info->id_estados_cumplimiento_compromiso);
			
			$nombre_estado = $status_info->nombre_estado;
			$color_estado = $status_info->color;
			$html_estado = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
			$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$nombre_estado;
			$html_estado .= '</div>';
			$view_data["html_estado"] = $html_estado;
			
			// TRAER CAMPOS ADICIONALES EN CASO DE QUE EL ESTADO SEA NO CUMPLE
			if($status_info->categoria == "No Cumple"){
				$view_data["no_cumple"] = $status_info->categoria;
				$id_criticidad = $evaluation_info->id_criticidad;
				$criticidad = $this->Critical_levels_model->get_one($id_criticidad)->nombre;
				$responsable_reporte = $evaluation_info->responsable_reporte;
				$plazo_cierre = get_date_format($evaluation_info->plazo_cierre, $id_proyecto);
			}
			
			$observaciones = ($evaluation_info->observaciones) ? $evaluation_info->observaciones : "-";
			$responsable = $this->Users_model->get_one($evaluation_info->responsable);
			$responsable = $responsable->first_name." ".$responsable->last_name;
			$ult_mod = ($evaluation_info->modified) ? time_date_zone_format($evaluation_info->modified, $id_proyecto) : time_date_zone_format($evaluation_info->created, $id_proyecto);
			
			//
			
			$html_view .= '';
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="execution" class="col-md-3">'.lang('execution').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$nombre_compromiso;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="execution_date" class="col-md-3">'.lang('execution_date').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$fecha_evaluacion;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('status').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$html_estado;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$categoria = $status_info->categoria;
			if($categoria == "No Cumple"){
				$criticidad = $this->Critical_levels_model->get_one($evaluation_info->id_criticidad)->nombre;
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
				$html_view .= 	'<div class="col-md-9">';
				$html_view .= 		$criticidad;
				$html_view .= 	'</div>';
				$html_view .= '</div>';
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
				$html_view .= 	'<div class=" col-md-9">';
				$html_view .= 		$responsable_reporte;
				$html_view .= 	'</div>';
				$html_view .= '</div>';
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
				$html_view .= 	'<div class=" col-md-9">';
				$html_view .= 		$plazo_cierre;
				$html_view .= 	'</div>';
				$html_view .= '</div>';
			}
			
			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluation_info->id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result_array();
            
			if ($archivos_evidencia) {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
				
					$html_archivos_evidencia .= '<div class="col-md-8" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$evaluation_info->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</thead>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
				
				}

				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				
            } else {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				$html_archivos_evidencia .= lang("no_evidence_files");
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				
            }
			
			$html_view .= $html_archivos_evidencia;
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('observations').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		($observaciones) ? $observaciones : "-";
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('evaluator').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$responsable;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('last_evaluation').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$ult_mod;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			echo $html_view;
			
		} else {
            show_404();
        }
		
		/*
		$compliance_evaluation_id = $this->input->post("id_evaluacion");
		
		if ($compliance_evaluation_id) {
			
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$id_proyecto = $proyecto->id;
			$compliance_evaluation_info = $this->Compromises_compliance_evaluation_reportables_model->get_one($compliance_evaluation_id);
			$nombre_compromiso = $this->Values_compromises_reportables_model->get_one($compliance_evaluation_info->id_valor_compromiso)->nombre_compromiso;
			$nombre_evaluado = $this->Evaluated_rca_compromises_model->get_one($compliance_evaluation_info->id_evaluado)->nombre_evaluado;
			
			$estado = $this->Compromises_compliance_status_model->get_one($compliance_evaluation_info->id_estados_cumplimiento_compromiso);
			$nombre_estado = $estado->nombre_estado;
			$color_estado = $estado->color;
			
			$html_estado = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
			$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$nombre_estado;
			$html_estado .= '</div>';
			
			$observaciones = $compliance_evaluation_info->observaciones;
			$responsable = $this->Users_model->get_one($compliance_evaluation_info->responsable);
			$responsable = $responsable->first_name." ".$responsable->last_name;
			$ult_mod = ($compliance_evaluation_info->modified) ? time_date_zone_format($compliance_evaluation_info->modified, $id_proyecto) : time_date_zone_format($compliance_evaluation_info->created, $id_proyecto);
			
			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $compliance_evaluation_id, "deleted" => 0))->result_array();
            
			if ($archivos_evidencia) {
				
				$html_view = "";
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
				
					$html_archivos_evidencia .= '<div class="col-md-8" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4" style="padding-left: 0px; margin-bottom: 5px;">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_rca_evaluation/download_file/".$compliance_evaluation_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				
					$html_archivos_evidencia .= '</td>';
					$html_archivos_evidencia .= '</tr>';
					$html_archivos_evidencia .= '</thead>';
					$html_archivos_evidencia .= '</table>';
					$html_archivos_evidencia .= '</div>';
				
				}

				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				
            } else {
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				$html_archivos_evidencia .= lang("no_evidence_files");
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '</div>';
				
            }
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('compromise').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$nombre_compromiso;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('evaluated').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$nombre_evaluado;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('status').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$html_estado;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$categoria = $estado->categoria;
			if($categoria == "No Cumple"){
				$criticidad = $this->Critical_levels_model->get_one($compliance_evaluation_info->id_criticidad)->nombre;
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
				$html_view .= 	'<div class="col-md-9">';
				$html_view .= 		$criticidad;
				$html_view .= 	'</div>';
				$html_view .= '</div>';
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
				$html_view .= 	'<div class=" col-md-9">';
				$html_view .= 		$compliance_evaluation_info->responsable_reporte;
				$html_view .= 	'</div>';
				$html_view .= '</div>';
				
				$html_view .= '<div class="form-group">';
				$html_view .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
				$html_view .= 	'<div class=" col-md-9">';
				$html_view .= 		get_date_format($compliance_evaluation_info->plazo_cierre, $id_proyecto);
				$html_view .= 	'</div>';
				$html_view .= '</div>';
			}
			
			$html_view .= $html_archivos_evidencia;
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('observations').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		($observaciones) ? $observaciones : "-";
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('evaluator').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$responsable;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('last_evaluation').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$ult_mod;
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			echo $html_view;
			
        } else {
            show_404();
        }*/
		
	}
	
	//Esta función devuelve un dropdown que contiene los compromisos (tabla valores_compromisos) que pertenecen a un evaluado.
	function get_compromises_of_evaluated(){
		
		$id_evaluado = $this->input->post('id_evaluado');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$id_matriz_compromiso_evaluado = $this->Evaluated_rca_compromises_model->get_one($id_evaluado)->id_compromiso;
		$valores_compromisos_evaluado = $this->Values_compromises_reportables_model->get_all_where(array("id_compromiso" => $id_matriz_compromiso_evaluado, "deleted" => 0))->result_array();

		
		$dropdown_vce = array();
		foreach($valores_compromisos_evaluado as $vce){
			$dropdown_vce[$vce["id"]] = $vce["nombre_compromiso"];;
		}
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="compromise" class="col-md-2 p0">'.lang('compromise').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("compromise", array("" => "-") + $dropdown_vce, "", "id='compromise' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	//Funcion que retorna la tabla con la evaluacion del evaluado del compromiso de los filtros.
	function get_evaluation_table_of_compromise(){
		
		$view_data["id_evaluado"] = $this->input->post('id_evaluado');
		$view_data["id_valor_compromiso"] = $this->input->post('id_valor_compromiso');
		$view_data["id_compromiso_proyecto"] = $this->input->post('id_compromiso_proyecto');
		$this->load->view("compromises_reportables_evaluation/compliance_evaluation/index", $view_data);

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
		
		echo json_encode(array("success" => true));
		
		/*
		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'xlsx') {
			echo json_encode(array("success" => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		*/
		
    }
	
	function create_compliance_evidence_folder($id_evaluacion) {
		
		$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion);
		$valor_compromiso = $this->Values_compromises_reportables_model->get_one($evaluacion->id_valor_compromiso);
		$matriz_compromiso = $this->Compromises_reportables_model->get_one($valor_compromiso->id_compromiso);
		$id_proyecto = $matriz_compromiso->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		if(!file_exists(__DIR__.'/../../files/evaluaciones_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/evaluation_'.$id_evaluacion)) {
			if(mkdir(__DIR__.'/../../files/evaluaciones_compromisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/evaluation_'.$id_evaluacion, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}	
	
	function download_file($id_evaluacion, $id_evidencia) {

		$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one($id_evaluacion);
		$valor_compromiso = $this->Values_compromises_reportables_model->get_one($evaluacion->id_valor_compromiso);
		$matriz_compromiso = $this->Compromises_reportables_model->get_one($valor_compromiso->id_compromiso);
		$id_proyecto = $matriz_compromiso->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$file_info = $this->Compromises_compliance_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->archivo;
        $file_data = serialize(array(array("file_name" => $filename)));
		
		//download_app_files("files/evaluaciones_compromisos/evaluacion_".$id_evaluacion."/", $file_data, true);
        download_app_files("files/evaluaciones_compromisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$id_evaluacion."/", $file_data, true);
		
    }
	
	function delete_file() {
				
		$id_evaluacion = $this->input->post('id_evaluacion');
		$id_evidencia = $this->input->post('id_evidencia');
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$id_evaluado = $this->input->post("id_evaluado");
		
		$select_evaluado = $this->input->post("select_evaluado");

		$select_valor_compromiso = $this->input->post("select_valor_compromiso");
		
        $file_info = $this->Compromises_compliance_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}

		$campo_nuevo = "";
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, "id_evidencia" => $id_evidencia));

    }
	
	function get_form_fields_of_evaluation(){
		
		$id_compromiso = $this->input->post("id_compromiso");
		$id_plan = $this->input->post("id_plan");
		$id_proyecto = $this->session->project_context;
		
		$evaluacion = $this->Compromises_compliance_evaluation_reportables_model->get_one_where(
			array(
				"id_valor_compromiso" => $id_compromiso,
				"id_planificacion" => $id_plan,
				"deleted" => 0
				
			)
		);
		$compromiso = $this->Values_compromises_reportables_model->get_one($id_compromiso);
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$estados = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "tipo_evaluacion" => "reportable"));
		
		$html = '';
		
		if($evaluacion->id){
			
			$html .= '<div class="form-group">';
            $html .= '  <label for="execution" class="col-md-3">'.lang('execution').'</label>';
            $html .= '    <div class=" col-md-9">';
            $html .=         form_input(array(
								"id" => "execution",
								"name" => "execution",
								"value" => $evaluacion->ejecucion,
								"class" => "form-control",
								"placeholder" => lang('execution'),
								"data-rule-required" => true,
								"data-msg-required" => lang("field_required"),
								"autocomplete" => "off"
							));
            $html .= '    </div>';
            $html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="evaluation_date" class="col-md-3">' . lang('execution_date'). '</label>';
			$html .= 	'<div class=" col-md-9">';
			$html .= 		form_input(array(
								"id" => "fecha_evaluacion",
								"name" => "fecha_evaluacion",
								"value" => $evaluacion->fecha_evaluacion,
								"class" => "form-control datepicker",
								"placeholder" => lang('execution_date'),
								"data-rule-required" => true,
								"data-msg-required" => lang("field_required"),
								"autocomplete" => "off",
							));
			$html .= 	'</div>';
			$html .= '</div>';
						
			$html .= '<div class="form-group">';
			$html .= '<label for="nombre_compromiso" class="col-md-3">'. lang('reportable') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		$compromiso->nombre_compromiso;
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="status" class="col-md-3">'. lang('status') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		form_dropdown("estado", $estados, $evaluacion->id_estados_cumplimiento_compromiso, "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
			
			//
			$html .= '<div id="grupo_no_cumple">';
			$estado = $this->Compromises_compliance_status_model->get_one($evaluacion->id_estados_cumplimiento_compromiso);
			$dropdown_criticidad = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");
			
			if($estado){
				
				$categoria = $estado->categoria;
				if($categoria == "No Cumple"){
					
					$html .= '<div class="form-group">';
					$html .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
					$html .= 	'<div class="col-md-9">';
					$html .= 		form_dropdown("criticidad", $dropdown_criticidad, $evaluacion->id_criticidad, "id='criticidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					$html .= 	'</div>';
					$html .= '</div>';
					
					$html .= '<div class="form-group">';
					$html .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
					$html .= 	'<div class=" col-md-9">';
					$html .= 		form_input(array(
										"id" => "report_responsible",
										"name" => "report_responsible",
										"value" => $evaluacion->responsable_reporte,
										"class" => "form-control",
										"placeholder" => lang('responsible'),
										"data-rule-required" => true,
										"data-msg-required" => lang("field_required"),
										"autocomplete" => "off",
									));
					$html .= 	'</div>';
					$html .= '</div>';
					
					$html .= '<div class="form-group">';
					$html .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
					$html .= 	'<div class=" col-md-9">';
					$html .= 		form_input(array(
										"id" => "plazo_cierre",
										"name" => "plazo_cierre",
										"value" => $evaluacion->plazo_cierre,
										"class" => "form-control datepicker",
										"placeholder" => lang('closing_term'),
										"data-rule-required" => true,
										"data-msg-required" => lang("field_required"),
										"autocomplete" => "off",
									));
					$html .= 	'</div>';
					$html .= '</div>';
				}
			}
			$html .= '</div>';
			//
			
			$html .= '<div class="form-group">';
			$html .= '<label for="observations" class="col-md-3">' . lang('observations');
			$html .= ' <span class="help" data-container="body" data-toggle="tooltip" title="'.lang('add_evaluations_details').'"><i class="fa fa-question-circle"></i></span>';
			$html .= '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .=		form_textarea(array(
								"id" => "observaciones",
								"name" => "observaciones",
								"value" => $evaluacion->observaciones,
								"class" => "form-control",
								"placeholder" => lang('observations'),
								//"autofocus" => false,
								"data-msg-required" => lang("field_required"),
								"autocomplete" => "off",
							));
			$html .= 	'</div>';
			$html .= '</div>';
					
			$html .= '<div class="form-group">';
			
			$html .= '<label for="file" class="col-md-3">' . lang('upload_evidence_file') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		'<div id="dropzone_bulk" class="">';
			
			$html .= 			$this->load->view("includes/compliance_evaluation_file_uploader", array(
									"upload_url" => get_uri("compromises_reportables_evaluation/upload_file"),
									"validation_url" =>get_uri("compromises_reportables_evaluation/validate_file"),
									//"html_name" => 'test',
									//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
								), true);
			
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $evaluacion->id, 
					"tipo_evaluacion" => "reportable", 
					"deleted" => 0
				)
			)->result_array();
	
			if($archivos_evidencia){
				
				$html_archivos_evidencia = "";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($archivos_evidencia as $evidencia){
											
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$evaluacion->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					
					$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $evaluacion->id, "data-id_valor_compromiso" => $evaluacion->id_valor_compromiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_compromiso" => $select_valor_compromiso, "data-action-url" => get_uri("compromises_reportables_evaluation/delete_file"), "data-action" => "delete-fileConfirmation"));
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
				
				$html .= $html_archivos_evidencia;
				
			}		
		
		}

		echo $html;
		
	}
	
	function get_fields_of_evaluation_status(){
		
		$id_estado = $this->input->post("id_estado");
		$estado = $this->Compromises_compliance_status_model->get_one($id_estado);
		$dropdown_criticidad = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");

		$html  = '';
		
		if($estado){
			
			$categoria = $estado->categoria;
			if($categoria == "No Cumple"){
				
				$html .= '<div class="form-group">';
				$html .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
				$html .= 	'<div class="col-md-9">';
				$html .= 		form_dropdown("criticidad", $dropdown_criticidad, "", "id='criticidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= 	'</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
				$html .= 	'<div class=" col-md-9">';
				$html .= 		form_input(array(
									"id" => "report_responsible",
									"name" => "report_responsible",
									"value" => "",
									"class" => "form-control",
									"placeholder" => lang('responsible'),
									"data-rule-required" => true,
									"data-msg-required" => lang("field_required"),
									"autocomplete" => "off",
								));
				$html .= 	'</div>';
				$html .= '</div>';
				
				$html .= '<div class="form-group">';
				$html .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
				$html .= 	'<div class=" col-md-9">';
				$html .= 		form_input(array(
									"id" => "plazo_cierre",
									"name" => "plazo_cierre",
									"value" => "",
									"class" => "form-control datepicker",
									"placeholder" => lang('closing_term'),
									"data-rule-required" => true,
									"data-msg-required" => lang("field_required"),
									"autocomplete" => "off",
								));
				$html .= 	'</div>';
				$html .= '</div>';
				
				
			}
		}
		
		
		echo $html;
		
	}
	
	function get_files_of_evaluation(){
		
		$id_evaluacion = $this->input->post("id_evaluacion");
		$archivos_evidencia = $this->Compromises_compliance_evidences_model->get_all_where(
			array(
				"id_evaluacion_cumplimiento_compromiso" => $id_evaluacion, 
				"tipo_evaluacion" => "reportable", 
				"deleted" => 0
			)
		)->result_array();
		
		if($archivos_evidencia){
				
			$html_archivos_evidencia = "";
			$html_archivos_evidencia .= '<div class="form-group">';
			$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
			$html_archivos_evidencia .= '<div class="col-md-9">';
			
			foreach($archivos_evidencia as $evidencia){
										
				$html_archivos_evidencia .= '<div class="col-md-8">';
				$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
				$html_archivos_evidencia .= '</div>';
				$html_archivos_evidencia .= '<div class="col-md-4">';
				$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
				$html_archivos_evidencia .= anchor(get_uri("compromises_reportables_evaluation/download_file/".$id_evaluacion. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				
				//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $id_evaluacion, "data-id_valor_compromiso" => $evaluacion->id_valor_compromiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_compromiso" => $select_valor_compromiso, "data-action-url" => get_uri("compromises_rca_evaluation/delete_file"), "data-action" => "delete-confirmation"));
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
			
			$html .= $html_archivos_evidencia;
			
		} else {
			
			$html_archivos_evidencia = "";
			$html_archivos_evidencia .= '<div class="form-group">';
			$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
			$html_archivos_evidencia .= '<div class="col-md-9">';
			$html_archivos_evidencia .= lang("no_evidence_files");
			$html_archivos_evidencia .= '</div>';
			$html_archivos_evidencia .= '</div>';
			$html_archivos_evidencia .= '</div>';
			
			$html .= $html_archivos_evidencia;
		}
		
		echo $html;
		
	}
		
}

