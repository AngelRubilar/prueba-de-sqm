<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permitting_procedure_evaluation extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 7; //Evaluación de Permisos
		$this->id_submodulo_cliente = 6; 
		
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

		$evaluados = $this->Evaluated_permitting_model->get_evaluated_related_to_project_permitting($id_proyecto)->result_array();
		$dropdown_evaluados = array("" => "-");
		
		foreach($evaluados as $eval){
			$dropdown_evaluados[$eval["id_evaluados_permisos"]] = $eval["nombre_evaluado"];
		}
		
		$id_permiso_proyecto = $this->Permitting_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		$permisos = $this->Values_permitting_model->get_all_where(array("id_permiso" => $id_permiso_proyecto, "deleted" => 0))->result_array();
		$dropdown_permisos = array("" => "-");
		
		foreach($permisos as $index => $permiso){
			$dropdown_permisos[$permiso["id"]] = $permiso["nombre_permiso"];
		}

		$view_data["project_info"] = $proyect_info;
		$view_data["evaluados"] = $dropdown_evaluados;
		$view_data["permisos"] = $dropdown_permisos;
		$view_data["id_permiso_proyecto"] = $id_permiso_proyecto;
		
        $this->template->rander("permitting_procedure_evaluation/index", $view_data);
		
    }
	
	//modificar
	function modal_form() {
		
        $permitting_evaluation_id = $this->input->post("id_evaluacion");
		$id_valor_permiso = $this->input->post("id_valor_permiso");
		$id_evaluado = $this->input->post("id_evaluado");
		
		$select_evaluado = $this->input->post("select_evaluado");
		$select_valor_permiso = $this->input->post("select_valor_permiso");
		
		$view_data["view"] = $this->input->post('view');
		$view_data["select_evaluado"] = $select_evaluado;
		$view_data["select_valor_permiso"] = $select_valor_permiso;
		
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$view_data["id_evaluacion"] = $permitting_evaluation_id;
		$view_data["id_valor_permiso"] = $id_valor_permiso;
		$view_data["id_evaluado"] = $id_evaluado;
		
		$puede_agregar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"agregar"
		);
		$puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
		
		$view_data["puede_agregar"] = $puede_agregar;
		$view_data["puede_editar"] = $puede_editar;
		
        if ($permitting_evaluation_id) {
			
		    $permitting_evaluation_info = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id);
			$evaluaciones_historicas = $this->Permitting_procedure_evaluation_model->get_all_where_order_by_date_desc(array("id_valor_permiso" => $permitting_evaluation_info->id_valor_permiso, "id_evaluado" => $permitting_evaluation_info->id_evaluado))->result();
			
			
			if($puede_agregar == 1){
				if($puede_editar == 1){
					$evaluations_dropdown = array("" => "- " . lang("new_evaluation") . " -");
					foreach($evaluaciones_historicas as $evaluacion_historica){
						$evaluations_dropdown[$evaluacion_historica->id] = lang("evaluation_with_date") . " " . get_date_format($evaluacion_historica->fecha_evaluacion, $this->session->project_context);
					}
				}
				if($puede_editar == 2){
					$evaluations_dropdown = array("" => "- " . lang("new_evaluation") . " -");
					foreach($evaluaciones_historicas as $evaluacion_historica){
						if($evaluacion_historica->created_by == $this->login_user->id){
							$evaluations_dropdown[$evaluacion_historica->id] = lang("evaluation_with_date") . " " . get_date_format($evaluacion_historica->fecha_evaluacion, $this->session->project_context);
						}
					}
				}
				if($puede_editar == 3){
					$evaluations_dropdown = array("" => "- " . lang("new_evaluation") . " -");
				}
			}
			
			if($puede_agregar == 3){
				if($puede_editar == 1){
					foreach($evaluaciones_historicas as $evaluacion_historica){
						$evaluations_dropdown[$evaluacion_historica->id] = lang("evaluation_with_date") . " " . get_date_format($evaluacion_historica->fecha_evaluacion, $this->session->project_context);
					}
				}
				if($puede_editar == 2){
					foreach($evaluaciones_historicas as $evaluacion_historica){
						if($evaluacion_historica->created_by == $this->login_user->id){
							$evaluations_dropdown[$evaluacion_historica->id] = lang("evaluation_with_date") . " " . get_date_format($evaluacion_historica->fecha_evaluacion, $this->session->project_context);
						}
					}
				}
				if($puede_editar == 3){
					// Se bloquea el botón para crear evaluaciones
					$evaluations_dropdown = array();
				}
			}
					
			$view_data["evaluations_dropdown"] = $evaluations_dropdown;
			
            if ($permitting_evaluation_info) {
				
				/*
				//traer el id de cliente para consultar y listar los estados de tramitacion de ese cliente.
				$id_valor_permiso = $permitting_evaluation_info->id_valor_permiso;
				$id_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;
				$id_proyecto_permiso = $this->Permitting_model->get_one($id_permiso)->id_proyecto;
				$id_cliente = $this->Projects_model->get_one($id_proyecto_permiso)->client_id;
				$id_estados_tramitacion_permisos = $permitting_evaluation_info->id_estados_tramitacion_permisos;
				$estados = array("" => "-") + $this->Permitting_procedure_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente));
				
				//$estados = $this->Compromises_compliance_status_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result_array();
				
				$nombre_permiso = $this->Values_permitting_model->get_one($permitting_evaluation_info->id_valor_permiso)->nombre_permiso;
				$nombre_evaluado = $this->Evaluated_permitting_model->get_one($permitting_evaluation_info->id_evaluado)->nombre_evaluado;

				$observaciones = $permitting_evaluation_info->observaciones;
				$responsable = $this->Users_model->get_one($permitting_evaluation_info->responsable);
				$responsable = $responsable->first_name." ".$responsable->last_name;
				$ult_mod = ($permitting_evaluation_info->modified) ? $permitting_evaluation_info->modified : $permitting_evaluation_info->created;
				
				$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_evaluation_info->id, "deleted" => 0))->result_array();
				
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
						$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$permitting_evaluation_info->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						
						$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $permitting_evaluation_info->id, "data-id_valor_permiso" => $id_valor_permiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_permiso" => $select_valor_permiso, "data-action-url" => get_uri("permitting_procedure_evaluation/delete_file"), "data-action" => "delete-confirmation"));
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
					
				} else {
					
				}
				
				$view_data["nombre_permiso"] = $nombre_permiso;
				$view_data["nombre_evaluado"] = $nombre_evaluado;
				$view_data["estados"] = $estados;
				$view_data["evidencia"] = "-";
				$view_data["observaciones"] = $observaciones;
				$view_data["id_estados_tramitacion_permisos"] = $id_estados_tramitacion_permisos;

				$this->load->view('permitting_procedure_evaluation/permittings_evaluation/modal_form', $view_data);
				
				*/
				
				
				$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(
					array(
						"id_evaluacion_tramitacion_permisos" => $permitting_evaluation_info->id, 
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
						$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$permitting_evaluation_info->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						
						$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $permitting_evaluation_info->id, "data-id_valor_permiso" => $id_valor_permiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_permiso" => $select_valor_permiso, "data-action-url" => get_uri("permitting_procedure_evaluation/delete_file"), "data-action" => "delete-confirmation"));
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
				
				
				$id_valor_permiso = $permitting_evaluation_info->id_valor_permiso;
				$id_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;
				$id_proyecto_permiso = $this->Permitting_model->get_one($id_permiso)->id_proyecto;
				$id_cliente = $this->Projects_model->get_one($id_proyecto_permiso)->client_id;
				
				$estados = array("" => "-") + $this->Permitting_procedure_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente));
				
				$nombre_permiso = $this->Values_permitting_model->get_one($permitting_evaluation_info->id_valor_permiso)->nombre_permiso;
				$nombre_evaluado = $this->Evaluated_permitting_model->get_one($permitting_evaluation_info->id_evaluado)->nombre_evaluado;
				
				$view_data["fecha_evaluacion"] = get_date_format($permitting_evaluation_info->fecha_evaluacion, $this->session->project_context);
				$view_data["nombre_permiso"] = $nombre_permiso;
				$view_data["nombre_evaluado"] = $nombre_evaluado;
				$view_data["estados"] = $estados;
				$view_data["estado_evaluacion"] = $permitting_evaluation_info->id_estados_tramitacion_permisos;
				$view_data["observaciones"] = $permitting_evaluation_info->observaciones;
				
				$view_data["id_valor_permiso"] =  $permitting_evaluation_info->id_valor_permiso;
				$view_data["id_evaluado"] =  $permitting_evaluation_info->id_evaluado;
				
				$view_data["evaluaciones_propias"] = $this->Permitting_procedure_evaluation_model->get_all_where(array(
					"id_valor_permiso" => $permitting_evaluation_info->id_valor_permiso,
					"id_evaluado" => $permitting_evaluation_info->id_evaluado,
					"responsable" => $this->login_user->id,
					"deleted" => 0
				))->result();
				
				$this->load->view('permitting_procedure_evaluation/permittings_evaluation/modal_form', $view_data);
				
            } else {
                show_404();
            }
        } else if($id_valor_permiso && $id_evaluado){
			
			$evaluations_dropdown = array("" => "- " . lang("new_evaluation") . " -");
			$view_data["evaluations_dropdown"] = $evaluations_dropdown;
			
			$nombre_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->nombre_permiso;
			$nombre_evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado)->nombre_evaluado;
			
			//traer el id de cliente para consultar y listar los estados de tramitacion de ese cliente.
			$id_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;
			$id_proyecto_permiso = $this->Permitting_model->get_one($id_permiso)->id_proyecto;
			$id_cliente = $this->Projects_model->get_one($id_proyecto_permiso)->client_id;
			$estados = array("" => "-") + $this->Permitting_procedure_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente));
			
			$view_data["nombre_permiso"] = $nombre_permiso;
			$view_data["nombre_evaluado"] = $nombre_evaluado;
			$view_data["estados"] = $estados;
			$view_data["evidencia"] = "-";
			$view_data["observaciones"] = "";
			
			$this->load->view('permitting_procedure_evaluation/permittings_evaluation/modal_form', $view_data);
			
		} else {
            show_404();
        }

    }
	
	function save() {

        $permitting_evaluation_id = $this->input->post("id_evaluacion");
		$id_valor_permiso = $this->input->post("id_valor_permiso");
		$id_evaluado = $this->input->post("id_evaluado");
		$file = $this->input->post('archivo_importado');
		
		$select_evaluado = $this->input->post("select_evaluado");
		$select_valor_permiso = $this->input->post("select_valor_permiso");
		
		$id_evaluacion_historica = $this->input->post("evaluation");
		$id_evidencia_eliminar = $this->input->post("id_evidencia_eliminar");
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		//SI EL USUARIO HA ELIMINADO ARCHIVOS, BORRARLOS DE LA BASE DE DATOS Y FÍSICAMENTE
		if($permitting_evaluation_id){
			
			if($id_evidencia_eliminar){
				
				foreach($id_evidencia_eliminar as $id_evidencia){
				
					$file_info = $this->Permitting_procedure_evidences_model->get_one($id_evidencia);
					$delete_evidence_id = $this->Permitting_procedure_evidences_model->delete($id_evidencia);
					$id_valor_permiso = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id)->id_valor_permiso;
					$id_evaluado = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id)->id_evaluado;
					$id_permiso_proyecto = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;
					
					$id_proyecto = $this->Permitting_model->get_one($id_permiso_proyecto)->id_proyecto;
					$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
					
					if($delete_evidence_id){					
						 delete_file_from_directory("files/evaluaciones_permisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$permitting_evaluation_id."/".$file_info->archivo);					
					}
				
				}
				
			}
			
		}

		// VERIFICAR SI SE MARCO UN ESTADO CON CAT. NO APLICA Y TRAER LOS DaTOS DE SUS CAMPOS
		$id_estado = $this->input->post("estado");
		$estado = $this->Permitting_procedure_status_model->get_one($id_estado);
		$categoria = $estado->categoria;
		
		if($categoria == "No Cumple"){
			$criticidad = $this->input->post("criticidad");
			$report_responsible = $this->input->post("report_responsible");
			$plazo_cierre = $this->input->post("plazo_cierre");
		}
		
		$data_permitting_evaluation = array(
			"id_estados_tramitacion_permisos" => $this->input->post("estado"),
			"criticidad" => ($criticidad)?$criticidad:NULL,
			"responsable_reporte" => $report_responsible?$report_responsible:NULL,
			"plazo_cierre" => $plazo_cierre?$plazo_cierre:NULL,
			"observaciones" => $this->input->post("observaciones"),
			"responsable" => $this->login_user->id,
			"fecha_evaluacion" => $this->input->post("fecha_evaluacion")
		);
		
		if($permitting_evaluation_id){ //edit

			if($id_evaluacion_historica){
				
				$evaluacion_historica = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion_historica);
				
				$data_permitting_evaluation["fecha_evaluacion"] = $evaluacion_historica->fecha_evaluacion;
				$data_permitting_evaluation["modified_by"] = $this->login_user->id;
				$data_permitting_evaluation["modified"] = get_current_utc_time();
				$save_id = $this->Permitting_procedure_evaluation_model->save($data_permitting_evaluation, $id_evaluacion_historica);
				
			} else {
				
				//ESTA ES UNA NUEVA EVALUACION PARA LA COMBINACIÓN DE EVALUADO Y PERMISO
				$fecha_evaluacion = $data_permitting_evaluation["fecha_evaluacion"];
				$evaluaciones = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
				foreach($evaluaciones as $evaluacion){
					if($fecha_evaluacion == $evaluacion["fecha_evaluacion"]){
						echo json_encode(array("success" => false, 'message' => lang("permitting_evaluation_exists")));
						exit();
					}
				}
				
				$data_permitting_evaluation["id_valor_permiso"] = $id_valor_permiso;
				$data_permitting_evaluation["id_evaluado"] = $id_evaluado;
				$data_permitting_evaluation["created_by"] = $this->login_user->id;
				$data_permitting_evaluation["created"] = get_current_utc_time();
				$save_id = $this->Permitting_procedure_evaluation_model->save($data_permitting_evaluation);
			
			}
			
		} else { //insert
			
			$fecha_evaluacion = $data_permitting_evaluation["fecha_evaluacion"];
			$evaluaciones = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_evaluado" => $id_evaluado, "id_valor_permiso" => $id_valor_permiso, "deleted" => 0))->result_array();
			foreach($evaluaciones as $evaluacion){
				if($fecha_evaluacion == $evaluacion["fecha_evaluacion"]){
					echo json_encode(array("success" => false, 'message' => lang("permitting_evaluation_exists")));
					exit();
				}
			}
			
			$data_permitting_evaluation["id_valor_permiso"] = $id_valor_permiso;
			$data_permitting_evaluation["id_evaluado"] = $id_evaluado;
			$data_permitting_evaluation["created_by"] = $this->login_user->id;
			$data_permitting_evaluation["created"] = get_current_utc_time();
			$save_id = $this->Permitting_procedure_evaluation_model->save($data_permitting_evaluation);
			
		}
		
        if ($save_id) {
			
			$id_valor_permiso = $this->Permitting_procedure_evaluation_model->get_one($save_id)->id_valor_permiso;
			$id_evaluado = $this->Permitting_procedure_evaluation_model->get_one($save_id)->id_evaluado;
			$id_permiso_proyecto = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;
			
			if($file){
				//Si no existe el directorio para los archivos de evidencia, crearlo antes de mover el archivo.
				$crear_carpeta = $this->create_permitting_evidence_folder($save_id);			
				//$archivo_subido = move_temp_file($file, "files/evaluaciones_compromisos/evaluacion_".$save_id."/", "", "", $file);
				//$archivo_subido = move_temp_file($file, "files/evaluaciones_permisos/evaluacion_".$save_id."/");
				
				$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($save_id);
				$valor_permiso = $this->Values_permitting_model->get_one($evaluacion->id_valor_permiso);
				$matriz_permiso = $this->Permitting_model->get_one($valor_permiso->id_permiso);
				$id_proyecto = $matriz_permiso->id_proyecto;
				$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
				
				$archivo_subido = move_temp_file($file, "files/evaluaciones_permisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$evaluacion->id."/");
				
				//Guardar el registro en la tabla de evidencias_cumplimiento_compromisos
				$datos_evidencia = array(
					"id_evaluacion_tramitacion_permisos" => $save_id,
					"archivo" => $archivo_subido,
					"created_by" => $this->login_user->id
				);
				$save_evidencia_id = $this->Permitting_procedure_evidences_model->save($datos_evidencia);
				
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
				"event" => ($id_evaluacion_historica) ? "edit" : "add",
				"id_element" => $save_id
			);
			ayn_save_historical_notification($options);
			
			// Guardar histórico alertas
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_user" => $id_user,
				"id_client_module" => $this->id_modulo_cliente,
				"id_client_submodule" => $this->id_submodulo_cliente,
				"alert_config" => array(
					"id_valor_permiso" => $id_valor_permiso,
				),
				"id_element" => $save_id
			);
			ayn_save_historical_alert($options);
			
			if($select_evaluado && !$select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id_evaluado' => $id_evaluado, 'id_valor_permiso' => "", 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
			} else if(!$select_evaluado && $select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id_evaluado' => "", 'id_valor_permiso' => $id_valor_permiso, 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));		
			} else if($select_evaluado && $select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id_evaluado' => $id_evaluado, 'id_valor_permiso' => $id_valor_permiso, 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
			}
				
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
	
	function list_data($id_permiso_proyecto, $id_evaluado, $id_valor_permiso) {

		$puede_agregar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"agregar"
		);
		$puede_editar = $this->profile_access(
			$this->session->user_id, 
			$this->id_modulo_cliente, 
			$this->id_submodulo_cliente, 
			"editar"
		);
				
		$options = array(
			"id_permiso_proyecto" => $id_permiso_proyecto,
			"id_evaluado" => $id_evaluado,
			"id_valor_permiso" => $id_valor_permiso,
			"id_proyecto" => $this->session->project_context,
			"puede_agregar" => $puede_agregar,
			"puede_editar" => $puede_editar
		);
		
		//$this->access_only_allowed_members();
		
		//$list_data = $this->Evaluations_compliances_compromises_model->get_details($options);
		$list_data = $this->Permitting_procedure_evaluation_model->get_details($options);
		//var_dump($list_data);
		
		/*
		$result = array();
		
		foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
		*/
		 echo json_encode(array("data" => $list_data));
		
	}
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Permitting_procedure_evaluation_model->get_one($id);
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$nombre_permiso = $this->Values_permitting_model->get_one($data->id_valor_permiso)->nombre_permiso;
		$entidad = $this->Values_permitting_model->get_one($data->id_valor_permiso)->entidad;
		$nombre_evaluado = $this->Evaluated_permitting_model->get_one($data->id_evaluado)->nombre_evaluado;
		$estado = $this->Permitting_procedure_status_model->get_one($data->id_estados_tramitacion_permisos);
		$nombre_estado = $estado->nombre_estado;
		$color_estado = $estado->color;
		
		$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado .= $nombre_estado;
		$html_estado .= '</div>';
		
		$responsable = $this->Users_model->get_one($data->responsable);
		$responsable = $responsable->first_name." ".$responsable->last_name;
		
		$row_data = array(
			$data->id_valor_permiso, 
			$nombre_permiso,
			$entidad,
			$nombre_evaluado,
			$html_estado,
			"-",
			$data->observaciones,
			$responsable,
			($data->modified) ? $data->modified : $data->created		
		);
		
		$row_data[] = modal_anchor(get_uri("permitting_procedure_evaluation/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_permitting_evaluation'), "data-post-id_evaluacion" => $data->id))
				    . modal_anchor(get_uri("permitting_procedure_evaluation/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_permitting_evaluation'), "data-post-id_evaluacion" => $data->id))
				    . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_permitting_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("permitting_procedure_evaluation/delete"), "data-action" => "delete-confirmation"));
					 
        return $row_data;
		
    }
	
	function view($id_evaluacion = NULL) {
		
        //$this->access_only_allowed_members();
		
		$permitting_evaluation_id = ($id_evaluacion) ? $id_evaluacion : $this->input->post("id_evaluacion");
		$view_data["permitting_evaluation_id"] = $permitting_evaluation_id;
		
		$id_valor_permiso = $this->input->post("id_valor_permiso");
		$id_evaluado = $this->input->post("id_evaluado");
		
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		
		$proyecto = $this->Projects_model->get_one($project_context);
		$id_proyecto = $proyecto->id;
		
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
        if ($permitting_evaluation_id) {

		    $permitting_evaluation_info = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id);

            if ($permitting_evaluation_info) {
				
				$evaluaciones_evaluado_permiso = $this->Permitting_procedure_evaluation_model->get_all_where_order_by_date_desc(array("id_evaluado" => $permitting_evaluation_info->id_evaluado, "id_valor_permiso" => $permitting_evaluation_info->id_valor_permiso))->result();
				$evaluations_dropdown = array();
				foreach($evaluaciones_evaluado_permiso as $eec){
					$evaluations_dropdown[$eec->id] = lang("evaluation_with_date") . " " . get_date_format($eec->fecha_evaluacion, $project_context);
				}
				
				$view_data["evaluations_dropdown"] = $evaluations_dropdown;
				
				$nombre_permiso = $this->Values_permitting_model->get_one($permitting_evaluation_info->id_valor_permiso)->nombre_permiso;
				$nombre_evaluado = $this->Evaluated_permitting_model->get_one($permitting_evaluation_info->id_evaluado)->nombre_evaluado;
				$estado = $this->Permitting_procedure_status_model->get_one($permitting_evaluation_info->id_estados_tramitacion_permisos);
				
				$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_evaluation_info->id, "deleted" => 0))->result();
				
				$nombre_estado = $estado->nombre_estado;
				$color_estado = $estado->color;
				$html_estado = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
				$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$nombre_estado;
				$html_estado .= '</div>';
				
				$categoria = $estado->categoria;
				$html_no_cumple .= '';
				if($categoria == "No Cumple"){
					
					$html_no_cumple .= '<div class="form-group">';
					$html_no_cumple .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
					$html_no_cumple .= '<div class="col-md-9">';
					$html_no_cumple .= $permitting_evaluation_info->criticidad;
					$html_no_cumple .= '</div>';
					$html_no_cumple .= '</div>';
					
					$html_no_cumple .= '<div class="form-group">';
					$html_no_cumple .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
					$html_no_cumple .= '<div class=" col-md-9">';
					$html_no_cumple .= $permitting_evaluation_info->responsable_reporte;
					$html_no_cumple .= '</div>';
					$html_no_cumple .= '</div>';
					
					$html_no_cumple .= '<div class="form-group">';
					$html_no_cumple .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
					$html_no_cumple .= '<div class=" col-md-9">';
					$html_no_cumple .= get_date_format($permitting_evaluation_info->plazo_cierre, $id_proyecto);
					$html_no_cumple .= '</div>';
					$html_no_cumple .= '</div>';
				}
				
				$observaciones = $permitting_evaluation_info->observaciones;
				$responsable = $this->Users_model->get_one($permitting_evaluation_info->responsable);
				$responsable = $responsable->first_name." ".$responsable->last_name;
				$ult_mod = ($permitting_evaluation_info->modified) ? time_date_zone_format($permitting_evaluation_info->modified, $id_proyecto) : time_date_zone_format($permitting_evaluation_info->created, $id_proyecto);
				
				$view_data["nombre_permiso"] = $nombre_permiso;
				$view_data["nombre_evaluado"] = $nombre_evaluado;
				$view_data["html_estado"] = $html_estado;
				$view_data["html_no_cumple"] = $html_no_cumple;
				
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
						$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$permitting_evaluation_id. "/" . $evidencia->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						$html_archivos_evidencia .= '</td>';
						$html_archivos_evidencia .= '</tr>';
						$html_archivos_evidencia .= '</tbody>';
						$html_archivos_evidencia .= '</table>';
						$html_archivos_evidencia .= '</div>';
						$view_data["evidencia"] = $html_archivos_evidencia;
					}
				} else {
					$view_data["evidencia"] = "-";
				}
				
				
				$view_data["observaciones"] = ($observaciones) ? $observaciones : "-";
				$view_data["responsable"] = $responsable;
				$view_data["ult_mod"] = $ult_mod;

				$this->load->view('permitting_procedure_evaluation/permittings_evaluation/view', $view_data);
				
            } else {
                show_404();
            }
        } else if($id_valor_permiso && $id_evaluado){
			
			$nombre_permiso = $this->Values_permitting_model->get_one($id_valor_permiso)->nombre_permiso;
			$nombre_evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado)->nombre_evaluado;
			
			$view_data["nombre_permiso"] = $nombre_permiso;
			$view_data["nombre_evaluado"] = $nombre_evaluado;
			$view_data["html_estado"] = "-";
			$view_data["evidencia"] = "-";
			$view_data["observaciones"] = "-";
			$view_data["responsable"] = "-";
			$view_data["ult_mod"] = "-";
			
			$this->load->view('permitting_procedure_evaluation/permittings_evaluation/view', $view_data);
			
		} else {
            show_404();
        }
    }
	

	function view_evidences() {
		
        //$this->access_only_allowed_members();
		
		$permitting_evaluation_id = $this->input->post("id_evaluacion");
		
		$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id);
		$evaluaciones_evaluado_permiso = $this->Permitting_procedure_evaluation_model->get_all_where_order_by_date_desc(array("id_evaluado" => $evaluacion->id_evaluado, "id_valor_permiso" => $evaluacion->id_valor_permiso))->result();
		$evaluations_dropdown = array();
		foreach($evaluaciones_evaluado_permiso as $eec){
			$evaluations_dropdown[$eec->id] = lang("evaluation_with_date") . " " . get_date_format($eec->fecha_evaluacion, $this->session->project_context);
		}
		
		$view_data["evaluations_dropdown"] = $evaluations_dropdown;
		
        if ($permitting_evaluation_id) {
			$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_evaluation_id, "deleted" => 0))->result_array();
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
					$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$permitting_evaluation_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
				$this->load->view('permitting_procedure_evaluation/permittings_evaluation/view_evidences', $view_data);
				
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
				$this->load->view('permitting_procedure_evaluation/permittings_evaluation/view_evidences', $view_data);
                //show_404();
				
            }
        } else {
            show_404();
        }
    }
	
	function view_historical_evaluations(){
		
		$permitting_evaluation_id = $this->input->post("id_evaluacion");
		$project_context = ($this->session->project_context) ? $this->session->project_context : $this->input->post("id_proyecto");
		
		if ($permitting_evaluation_id) {
			
			$proyecto = $this->Projects_model->get_one($project_context);
			$id_proyecto = $proyecto->id;
			
			$permitting_evaluation_info = $this->Permitting_procedure_evaluation_model->get_one($permitting_evaluation_id);			
			$nombre_permiso = $this->Values_permitting_model->get_one($permitting_evaluation_info->id_valor_permiso)->nombre_permiso;
			$nombre_evaluado = $this->Evaluated_permitting_model->get_one($permitting_evaluation_info->id_evaluado)->nombre_evaluado;
			$estado = $this->Permitting_procedure_status_model->get_one($permitting_evaluation_info->id_estados_tramitacion_permisos);

			$nombre_estado = $estado->nombre_estado;
			$color_estado = $estado->color;
			$html_estado = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
			$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$nombre_estado;
			$html_estado .= '</div>';
			
			$categoria = $estado->categoria;
			$html_no_cumple .= '';
			if($categoria == "No Cumple"){
				
				$html_no_cumple .= '<div class="form-group">';
				$html_no_cumple .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
				$html_no_cumple .= '<div class="col-md-9">';
				$html_no_cumple .= $permitting_evaluation_info->criticidad;
				$html_no_cumple .= '</div>';
				$html_no_cumple .= '</div>';
				
				$html_no_cumple .= '<div class="form-group">';
				$html_no_cumple .= '<label for="report_responsible" class="col-md-3">' . lang('responsible'). '</label>';
				$html_no_cumple .= '<div class=" col-md-9">';
				$html_no_cumple .= $permitting_evaluation_info->responsable_reporte;
				$html_no_cumple .= '</div>';
				$html_no_cumple .= '</div>';
				
				$html_no_cumple .= '<div class="form-group">';
				$html_no_cumple .= '<label for="plazo_cierre" class="col-md-3">' . lang('closing_term'). '</label>';
				$html_no_cumple .= '<div class=" col-md-9">';
				$html_no_cumple .= get_date_format($permitting_evaluation_info->plazo_cierre, $id_proyecto);
				$html_no_cumple .= '</div>';
				$html_no_cumple .= '</div>';
			}
			
			$observaciones = $permitting_evaluation_info->observaciones;
			$responsable = $this->Users_model->get_one($permitting_evaluation_info->responsable);
			$responsable = $responsable->first_name." ".$responsable->last_name;
			$ult_mod = ($permitting_evaluation_info->modified) ? time_date_zone_format($permitting_evaluation_info->modified, $id_proyecto) : time_date_zone_format($permitting_evaluation_info->created, $id_proyecto);
			
			$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_evaluation_info->id, "deleted" => 0))->result_array();
            
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
					$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$permitting_evaluation_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('permitting').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		$nombre_permiso;
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
			
			$html_view .= $html_no_cumple;
			
			$html_view .= $html_archivos_evidencia;
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('observations').'</label>';
			$html_view .= 	'<div class="col-md-9">';
			$html_view .= 		($observaciones) ? $observaciones : "-";
			$html_view .= 	'</div>';
			$html_view .= '</div>';
			
			$html_view .= '<div class="form-group">';
			$html_view .= 	'<label for="date_filed" class="col-md-3">'.lang('responsible').'</label>';
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
		
	}
	
	//Esta función devuelve un dropdown que contiene los compromisos (tabla valores_compromisos) que pertenecen a un evaluado.
	function get_compromises_of_evaluated(){
		
		$id_evaluado = $this->input->post('id_evaluado');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$id_matriz_compromiso_evaluado = $this->Evaluated_compromises_model->get_one($id_evaluado)->id_compromiso;
		$valores_compromisos_evaluado = $this->Values_compromises_model->get_all_where(array("id_compromiso" => $id_matriz_compromiso_evaluado))->result_array();
		
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
	function get_evaluation_table_of_permitting(){
		
		$view_data["id_evaluado"] = $this->input->post('id_evaluado');
		$view_data["id_valor_permiso"] = $this->input->post('id_valor_permiso');
		$view_data["id_permiso_proyecto"] = $this->input->post('id_permiso_proyecto');
		
		$this->load->view("permitting_procedure_evaluation/permittings_evaluation/index", $view_data);

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
	
	function create_permitting_evidence_folder($id_evaluacion) {
		
		$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion);
		$valor_permiso = $this->Values_permitting_model->get_one($evaluacion->id_valor_permiso);
		$matriz_permiso = $this->Permitting_model->get_one($valor_permiso->id_permiso);
		$id_proyecto = $matriz_permiso->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		//if(!file_exists(__DIR__.'/../../files/evaluaciones_permisos/evaluacion_'.$id_evaluacion)) {
		if(!file_exists(__DIR__.'/../../files/evaluaciones_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/evaluation_'.$id_evaluacion)) {
			//if(mkdir(__DIR__.'/../../files/evaluaciones_permisos/evaluacion_'.$id_evaluacion, 0777, TRUE)){
			if(mkdir(__DIR__.'/../../files/evaluaciones_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/evaluation_'.$id_evaluacion, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}	
	
	function download_file($id_evaluacion, $id_evidencia) {
		
		$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion);
		$valor_permiso = $this->Values_permitting_model->get_one($evaluacion->id_valor_permiso);
		$matriz_permiso = $this->Permitting_model->get_one($valor_permiso->id_permiso);
		$id_proyecto = $matriz_permiso->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$file_info = $this->Permitting_procedure_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->archivo;
        $file_data = serialize(array(array("file_name" => $filename)));

        download_app_files("files/evaluaciones_permisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$evaluacion->id."/", $file_data);
		
    }
	
	function delete_file() {
				
		$id_evaluacion = $this->input->post('id_evaluacion');
		$id_evidencia = $this->input->post('id_evidencia');
		$id_valor_permiso = $this->input->post("id_valor_permiso");
		$id_evaluado = $this->input->post("id_evaluado");
		
		$select_evaluado = $this->input->post("select_evaluado");
		$select_valor_permiso = $this->input->post("select_valor_permiso");
		
        $file_info = $this->Permitting_procedure_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$campo_nuevo = "";
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, "id_evidencia" => $id_evidencia));
		
		/*
		$save_id = $this->Permitting_procedure_evidences_model->delete($id_evidencia);
		
		$id_valor_permiso = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion)->id_valor_permiso;
		$id_evaluado = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion)->id_evaluado;
		$id_permiso_proyecto = $this->Values_permitting_model->get_one($id_valor_permiso)->id_permiso;	
		
        if ($save_id) {
			
			$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion);
			$valor_permiso = $this->Values_permitting_model->get_one($evaluacion->id_valor_permiso);
			$matriz_permiso = $this->Permitting_model->get_one($valor_permiso->id_permiso);
			$id_proyecto = $matriz_permiso->id_proyecto;
			$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
			
            delete_file_from_directory("files/evaluaciones_permisos/client_".$id_cliente."/project_".$id_proyecto."/evaluation_".$evaluacion->id."/".$file_info->archivo);
		
			if($select_evaluado && !$select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'id_evaluado' => $id_evaluado, 'id_valor_permiso' => "", 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
			} else if(!$select_evaluado && $select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'id_evaluado' => "", 'id_valor_permiso' => $id_valor_permiso, 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));		
			} else if($select_evaluado && $select_valor_permiso){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'id_evaluado' => $id_evaluado, 'id_valor_permiso' => $id_valor_permiso, 'id_permiso_proyecto' => $id_permiso_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
			}
		
            //echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
		*/

    }
	
	function get_form_fields_of_evaluation(){
		
		$id_evaluacion = $this->input->post("id_evaluacion");
		$id_proyecto = $this->session->project_context;
		$evaluacion = $this->Permitting_procedure_evaluation_model->get_one($id_evaluacion);
		$compromiso = $this->Values_permitting_model->get_one($evaluacion->id_valor_permiso);
		$evaluado = $this->Evaluated_permitting_model->get_one($evaluacion->id_evaluado);
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$select_evaluado = $this->input->post("select_evaluado");
		$select_valor_permiso = $this->input->post("select_valor_permiso");
		
		$estados = array("" => "-") + $this->Permitting_procedure_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente));
		
		if($id_evaluacion){
			
			$html  = '<div class="form-group">';
			$html .= '<label for="evaluation_date" class="col-md-3">' . lang('evaluation_date'). '</label>';
			$html .= 	'<div class=" col-md-9">';
			$html .= 		get_date_format($evaluacion->fecha_evaluacion, $id_proyecto);
			$html .= 	'</div>';
			$html .= '</div>';
						
			$html .= '<div class="form-group">';
			$html .= '<label for="nombre_permiso" class="col-md-3">'. lang('permission') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		$compromiso->nombre_permiso;
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="nombre_evaluado" class="col-md-3">'. lang('evaluated') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		$evaluado->nombre_evaluado;
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="status" class="col-md-3">'. lang('status') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		form_dropdown("estado", $estados, $evaluacion->id_estados_tramitacion_permisos, "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';

			//
			$html .= '<div id="grupo_no_cumple">';
			$estado = $this->Permitting_procedure_status_model->get_one($evaluacion->id_estados_tramitacion_permisos);
			//$dropdown_criticidad = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");
			$dropdown_criticidad = array("" => "-", "Si" => "Si", "No" => "No");
			
			if($estado){
				
				$categoria = $estado->categoria;
				if($categoria == "No Cumple"){
					
					$html .= '<div class="form-group">';
					$html .= '<label for="criticidad" class="col-md-3">'. lang('critical_level') . '</label>';
					$html .= 	'<div class="col-md-9">';
					$html .= 		form_dropdown("criticidad", $dropdown_criticidad, $evaluacion->criticidad, "id='criticidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
			$html .= '<label for="observations" class="col-md-3">' . lang('observations') . '</label>';
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
			
			$html .= 			$this->load->view("includes/permitting_evaluation_file_uploader", array(
									"upload_url" => get_uri("permitting_procedure_evaluation/upload_file"),
									"validation_url" =>get_uri("permitting_procedure_evaluation/validate_file"),
									//"html_name" => 'test',
									//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
								), true);
			
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $id_evaluacion, "deleted" => 0))->result_array();
			
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
					$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$id_evaluacion. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					
					$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $id_evaluacion, "data-id_valor_permiso" => $evaluacion->id_valor_permiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_permiso" => $select_valor_permiso, "data-action-url" => get_uri("permitting_procedure_evaluation/delete_file"), "data-action" => "delete-fileConfirmation"));
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
			
		} else {
			
			$id_evaluado = $this->input->post("id_evaluado");
			$id_valor_permiso = $this->input->post("id_valor_permiso");
			
			$permiso = $this->Values_permitting_model->get_one($id_valor_permiso);
			$evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado);
			
			$html  = '<div class="form-group">';
			$html .= '<label for="date_filed" class="col-md-3">' . lang('evaluation_date'). '</label>';
			$html .= 	'<div class=" col-md-9">';
			$html .= 		form_input(array(
								"id" => "fecha_evaluacion",
								"name" => "fecha_evaluacion",
								"value" => "",
								"class" => "form-control datepicker",
								"placeholder" => lang('evaluation_date'),
								"data-rule-required" => true,
								"data-msg-required" => lang("field_required"),
								"autocomplete" => "off",
							));
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="nombre_permiso" class="col-md-3">'. lang('permission') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		$permiso->nombre_permiso;
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="nombre_evaluado" class="col-md-3">'. lang('evaluated') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		$evaluado->nombre_evaluado;
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="status" class="col-md-3">'. lang('status') . '</label>';
			$html .= 	'<div class="col-md-9">';
			$html .= 		form_dropdown("estado", $estados, $evaluacion->id_estados_tramitacion_permisos, "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div id="grupo_no_cumple">';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '<label for="observations" class="col-md-3">' . lang('observations') . '</label>';
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
			$html .= 			$this->load->view("includes/permitting_evaluation_file_uploader", array(
									"upload_url" => get_uri("permitting_procedure_evaluation/upload_file"),
									"validation_url" =>get_uri("permitting_procedure_evaluation/validate_file"),
									//"html_name" => 'test',
									//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
								), true);
			
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
		}
		
		echo $html;
		
	}

	function get_fields_of_evaluation_status(){
		
		$id_estado = $this->input->post("id_estado");
		$estado = $this->Permitting_procedure_status_model->get_one($id_estado);
		//$dropdown_criticidad = array("" => "-") + $this->Critical_levels_model->get_dropdown_list(array("nombre"), "id");
		$dropdown_criticidad = array("" => "-", "Si" => "Si", "No" => "No");

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
		$archivos_evidencia = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $id_evaluacion, "deleted" => 0))->result_array();
		
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
				$html_archivos_evidencia .= anchor(get_uri("permitting_procedure_evaluation/download_file/".$id_evaluacion. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				
				//$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $id_evaluacion, "data-id_valor_compromiso" => $evaluacion->id_valor_compromiso, "data-id_evidencia" => $evidencia["id"], "data-id_evaluado" => $id_evaluado, "data-select_evaluado" => $select_evaluado, "data-select_valor_compromiso" => $select_valor_compromiso, "data-action-url" => get_uri("compromises_compliance_evaluation/delete_file"), "data-action" => "delete-confirmation"));
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
		
		} else {
			
			$html_archivos_evidencia = "";
			$html_archivos_evidencia .= '<div class="form-group">';
			$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.lang("evidence_files").'</label>';
			$html_archivos_evidencia .= '<div class="col-md-9">';
			$html_archivos_evidencia .= lang("no_evidence_files");
			$html_archivos_evidencia .= '</div>';
			$html_archivos_evidencia .= '</div>';
			$html_archivos_evidencia .= '</div>';
			
		}
		
		echo $html_archivos_evidencia;
	
	}
	
}

