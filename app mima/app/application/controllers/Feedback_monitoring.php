<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_monitoring extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9;
		$this->id_submodulo_cliente = 15;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
    }

    function index() {
		//$this->access_only_allowed_members();
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$access_info = $this->get_access_info("invoice");
		
		$id_proyecto = $this->session->project_context;	
		$proyect_info = $this->Projects_model->get_one($id_proyecto);
		
		$id_feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		
		//FILTRO PROPÓSITO VISITA. Buscar solo los propósitos que estén en los feedback
		/*
		$propositos_visita_dropdown = array(
			"" => "-",
			"Reunión Programada" => "Reunión Programada",
			"Queja" => "Queja",
			"Comentario" => "Comentario",
			"Consulta" => "Consulta",
			"Felicitación" => "Felicitación",
		);
		*/
		
		$valores_feedback = $this->Values_feedback_model->get_all_where(array("id_feedback_matrix_config" => $id_feedback_matrix_config, "deleted" => 0))->result_array();
		$propositos_visita_dropdown = array("" => "-");
		foreach($valores_feedback as $vf){
			$propositos_visita_dropdown[$vf["proposito_visita"]] = lang($vf["proposito_visita"]);
		}
		
		//FILTRO RESPONSABLE. Buscar usuarios del cliente asignados al proyecto de la matriz de acuerdos
		//$project_members = $this->Project_members_model->get_all_where(array("project_id" => $proyect_info->id, "deleted" => 0))->result_array();
		$responsable_dropdown = array("" => "-");
		/*
		foreach($project_members as $pm){
			$user = $this->Users_model->get_one($pm['user_id']);
			$responsable_dropdown[$user->id] = $user->first_name . " " . $user->last_name;
		}
		*/
		foreach($valores_feedback as $vf){
			$user = $this->Users_model->get_one($vf['responsable']);
			$responsable_dropdown[$user->id] = $user->first_name . " " . $user->last_name;
		}
	
		$view_data["project_info"] = $proyect_info;
		$view_data["id_feedback_matrix_config"] = $id_feedback_matrix_config;
		$view_data["propositos_visita_dropdown"] = $propositos_visita_dropdown;
		$view_data["responsable_dropdown"] = $responsable_dropdown;
		
        $this->template->rander("feedback_monitoring/index", $view_data);
		
    }
	
	//modificar
	function modal_form() {
		
        $id_evaluacion = $this->input->post("id_evaluacion");
		$id_responsable = $this->input->post("id_responsable");
		$proposito_visita = $this->input->post("proposito_visita");
		
		$id_valor_feedback = $this->input->post("id_valor_feedback");
		$select_proposito_visita = $this->input->post("select_proposito_visita");
		$select_responsable = $this->input->post("select_responsable");
		
		$view_data["view"] = $this->input->post('view');
		$view_data["select_proposito_visita"] = $select_proposito_visita;
		$view_data["select_responsable"] = $select_responsable;
		
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$view_data["id_evaluacion"] = $id_evaluacion;
		$view_data["id_responsable"] = $id_responsable;
		$view_data["proposito_visita"] = $proposito_visita;
		
		$view_data["id_proyecto"] = $this->session->project_context;

        if ($id_evaluacion) {

			$feedback_evaluation_info = $this->Feedback_monitoring_model->get_one($id_evaluacion);

            if ($feedback_evaluation_info) {
				
				//$id_valor_feedback = $feedback_evaluation_info->id_valor_feedback;
				$valor_feedback = $this->Values_feedback_model->get_one($feedback_evaluation_info->id_valor_feedback);
				
				
				$archivos_evidencia = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $feedback_evaluation_info->id, "deleted" => 0))->result_array();
				
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
						$html_archivos_evidencia .= anchor(get_uri("feedback_monitoring/download_file/".$feedback_evaluation_info->id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						
						$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id_evaluacion" => $feedback_evaluation_info->id, "data-proposito_visita" => $valor_feedback->proposito_visita, "data-id_evidencia" => $evidencia["id"], "data-id_responsable" => $valor_feedback->responsable, "data-select_proposito_visita" => $select_proposito_visita, "data-select_responsable" => $select_responsable, "data-action-url" => get_uri("feedback_monitoring/delete_file"), "data-action" => "delete-fileConfirmation"));
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
				
				
				$view_data["valor_feedback"] = $valor_feedback;
				$view_data["feedback_evaluation_info"] = $feedback_evaluation_info;
				
				$responsable = $this->Users_model->get_one($valor_feedback->responsable);
				$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
				$view_data["responsable"] = $nombre_responsable;
				
				$view_data["id_estados_cumplimiento_compromiso"] = $id_estados_cumplimiento_compromiso;

				$this->load->view('feedback_monitoring/feedback_evaluation/modal_form', $view_data);
				
            } else {
                show_404();
            }
        } else if($id_responsable && $proposito_visita){
			
			$valor_feedback = $this->Values_feedback_model->get_one($id_valor_feedback);
			$view_data["valor_feedback"] = $valor_feedback;
			
			$this->load->view('feedback_monitoring/feedback_evaluation/modal_form', $view_data);
			
		} else {
            show_404();
        }

    }
	
	
	function save() {
		
		$id_evaluacion = $this->input->post("id_evaluacion");
		$id_responsable = $this->input->post("id_responsable");
		$proposito_visita = $this->input->post("proposito_visita");
		$id_valor_feedback = $this->input->post("id_valor_feedback");
		
		$file = $this->input->post('archivo_importado');

		$select_proposito_visita = $this->input->post("select_proposito_visita");
		$select_responsable = $this->input->post("select_responsable");
		
		$id_evidencia_eliminar = $this->input->post("id_evidencia_eliminar");
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));		
		
		//SI EL USUARIO HA ELIMINADO ARCHIVOS, BORRARLOS DE LA BASE DE DATOS Y FÍSICAMENTE
		
		if($id_evaluacion){
			
			if($id_evidencia_eliminar){
				
				foreach($id_evidencia_eliminar as $id_evidencia){
					
					$file_info = $this->Feedback_monitoring_evidences_model->get_one($id_evidencia);
					$delete_evidence_id = $this->Feedback_monitoring_evidences_model->delete($id_evidencia);
					
					if($delete_evidence_id){
						delete_file_from_directory("files/evaluaciones_feedback/evaluacion_".$id_evaluacion."/".$file_info->archivo);
					}
				
				}
				
			}
			
		}
		
		
		$data_evaluation = array(
			"id_valor_feedback" => $id_valor_feedback,
			"respuesta" => $this->input->post("answer"),
			"estado_respuesta" => $this->input->post("answer_status"),
		);
		
		if($id_evaluacion){ //edit

			$data_evaluation["modified_by"] = $this->login_user->id;
			$data_evaluation["modified"] = get_current_utc_time();
			$save_id = $this->Feedback_monitoring_model->save($data_evaluation, $id_evaluacion);
			
		} else { //insert
			
			$data_evaluation["created_by"] = $this->login_user->id;
			$data_evaluation["created"] = get_current_utc_time();
			$save_id = $this->Feedback_monitoring_model->save($data_evaluation);

		}
		
        if ($save_id) {
			
			if($file){
				//Si no existe el directorio para los archivos de evidencia, crearlo antes de mover el archivo.
				$crear_carpeta = $this->create_feedback_evidence_folder($save_id);			
				//$archivo_subido = move_temp_file($file, "files/evaluaciones_compromisos/evaluacion_".$save_id."/", "", "", $file);
				$archivo_subido = move_temp_file($file, "files/evaluaciones_feedback/evaluacion_".$save_id."/");
				
				//Guardar el registro en la tabla de evidencias_cumplimiento_compromisos
				$datos_evidencia = array(
					"id_evaluacion_feedback" => $save_id,
					"archivo" => $archivo_subido,
					"created_by" => $this->login_user->id
				);
				$save_evidencia_id = $this->Feedback_monitoring_evidences_model->save($datos_evidencia);
				
			}
			
			$evaluacion_feedback = $this->Feedback_monitoring_model->get_one($save_id);
			$values_feedback = $this->Values_feedback_model->get_one($evaluacion_feedback->id_valor_feedback);

			if($select_proposito_visita && !$select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'proposito_visita' => $values_feedback->proposito_visita, 'id_responsable' => "", 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
			} else if(!$select_proposito_visita && $select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'proposito_visita' => "", 'id_responsable' => $values_feedback->responsable, 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));		
			} else if($select_proposito_visita && $select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'proposito_visita' => $values_feedback->proposito_visita, 'id_responsable' => $values_feedback->responsable, 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
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
	
	function list_data($id_feedback_matrix_config, $proposito_visita, $id_responsable) {

		$puede_editar = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
				
		$options = array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config,
			"proposito_visita" => urldecode($proposito_visita),
			"id_responsable" => $id_responsable,
			"id_proyecto" => $this->session->project_context,
			"puede_editar" => $puede_editar
		);
		
		//$this->access_only_allowed_members();
		
		//$list_data = $this->Evaluations_compliances_compromises_model->get_details($options);
		$list_data = $this->Feedback_monitoring_model->get_details($options);

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
		
        //$data = $this->Compromises_compliance_evaluation_model->get_one($id);
		$data = $this->Feedback_monitoring_model->get_one($id);
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_proyecto = $this->session->project_context;	
		$valor_feedback = $this->Values_feedback_model->get_one($data->id_valor_feedback);
		
		$row_data = array();
		$row_data[] = $valor_feedback->id;
		$row_data[] = get_date_format($valor_feedback->fecha, $id_proyecto);
		$row_data[] = $valor_feedback->proposito_visita;
		
		$responsable = $this->Users_model->get_one($valor_feedback->responsable);
		$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
		
		$row_data[] = $nombre_responsable;
		$row_data[] = $data->respuesta;
		$row_data[] = $data->estado_respuesta;
		$row_data[] = "-"; //Evidencias
		$row_data[] = ($data->modified) ? get_date_format($data->modified, $id_proyecto) : get_date_format($data->created, $id_proyecto);
		
		$row_data[] = modal_anchor(get_uri("feedback_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_compliance_evaluation'), "data-post-id_evaluacion" => $data->id))
				    . modal_anchor(get_uri("feedback_monitoring/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_compliance_evaluation'), "data-post-id_evaluacion" => $data->id))
				    . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_compliance_evaluation'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("feedback_monitoring/delete"), "data-action" => "delete-confirmation"));
					 
        return $row_data;
		
    }
	
	function view() {
		
		$id_proyecto = $this->session->project_context;
		
		$id_evaluacion = $this->input->post("id_evaluacion");
		$id_responsable = $this->input->post("id_responsable");
		$proposito_visita = $this->input->post("proposito_visita");
		
		$id_valor_feedback = $this->input->post("id_valor_feedback");
		//$select_proposito_visita = $this->input->post("select_proposito_visita");
		//$select_responsable = $this->input->post("select_responsable");
		
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
        if ($id_evaluacion) {

			$evaluation_info = $this->Feedback_monitoring_model->get_one($id_evaluacion);
			$values_feedback = $this->Values_feedback_model->get_one($evaluation_info->id_valor_feedback);
			
            if ($evaluation_info) {
				
				$view_data = array();
				$view_data["fecha"] = get_date_format($values_feedback->fecha, $id_proyecto);
				$view_data["nombre"] = $values_feedback->nombre;
				$view_data["proposito_visita"] = lang($values_feedback->proposito_visita);
				
				$responsable = $this->Users_model->get_one($values_feedback->responsable);
				$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
				$view_data["responsable"] = $nombre_responsable;
				
				$view_data["respuesta"] = $evaluation_info->respuesta;
				$view_data["estado_respuesta"] = $evaluation_info->estado_respuesta;
				$view_data["ultima_modificacion"] = ($evaluation_info->modified) ? time_date_zone_format($evaluation_info->modified, $id_proyecto) : time_date_zone_format($evaluation_info->created, $id_proyecto);

				$this->load->view('feedback_monitoring/feedback_evaluation/view', $view_data);
				
            } else {
                show_404();
            }
        } else if($proposito_visita && $id_responsable){
			
			$values_feedback = $this->Values_feedback_model->get_one($id_valor_feedback);	
			
			$view_data = array();
			$view_data["fecha"] = get_date_format($values_feedback->fecha, $id_proyecto);
			$view_data["nombre"] = $values_feedback->nombre;
			$view_data["proposito_visita"] = lang($values_feedback->proposito_visita);
			
			$responsable = $this->Users_model->get_one($values_feedback->responsable);
			$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
			$view_data["responsable"] = $nombre_responsable;
			
			$view_data["respuesta"] = "-";
			$view_data["estado_respuesta"] = "-";
			$view_data["ultima_modificacion"] = "-";

			$this->load->view('feedback_monitoring/feedback_evaluation/view', $view_data);
			
		} else {
            show_404();
        }
    }
	

	function view_evidences() {
		
        //$this->access_only_allowed_members();
		
		$evaluation_id = $this->input->post("id_evaluacion");

        if ($evaluation_id) {
			$archivos_evidencia = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $evaluation_id, "deleted" => 0))->result_array();
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
					$html_archivos_evidencia .= anchor(get_uri("feedback_monitoring/download_file/".$evaluation_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
				$this->load->view('feedback_monitoring/feedback_evaluation/view_evidences', $view_data);
				
            } else {
                show_404();
            }
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
	function get_evaluation_table_of_feedback(){
		
		$view_data["proposito_visita"] = $this->input->post('proposito_visita');
		$view_data["id_responsable"] = $this->input->post('id_responsable');
		$view_data["id_feedback_matrix_config"] = $this->input->post('id_feedback_matrix_config');
		
		$this->load->view("feedback_monitoring/feedback_evaluation/index", $view_data);

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
	
	function create_feedback_evidence_folder($id_evaluacion) {
		
		if(!file_exists(__DIR__.'/../../files/evaluaciones_feedback/evaluacion_'.$id_evaluacion)) {
			if(mkdir(__DIR__.'/../../files/evaluaciones_feedback/evaluacion_'.$id_evaluacion, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}	
	
	function download_file($id_evaluacion, $id_evidencia) {

		$file_info = $this->Feedback_monitoring_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->archivo;
        $file_data = serialize(array(array("file_name" => $filename)));

        download_app_files("files/evaluaciones_feedback/evaluacion_".$id_evaluacion."/", $file_data);
		
    }
	
	function delete_file() {
				
		$id_evaluacion = $this->input->post('id_evaluacion');
		$id_evidencia = $this->input->post('id_evidencia');
		/*
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$id_evaluado = $this->input->post("id_evaluado");
		
		$select_evaluado = $this->input->post("select_evaluado");
		$select_valor_compromiso = $this->input->post("select_valor_compromiso");
		*/
		
		$proposito_visita = $this->input->post('proposito_visita');
		$id_responsable = $this->input->post('id_responsable');
		
		$select_proposito_visita = $this->input->post('select_proposito_visita');
		$select_responsable = $this->input->post('select_responsable');
		
        $file_info = $this->Feedback_monitoring_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$campo_nuevo = "";
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, "id_evidencia" => $id_evidencia));
		
		
		/*
		$save_id = $this->Feedback_monitoring_evidences_model->update_where(array("deleted" => 1), array("id" => $id_evidencia));
		
		$evaluacion_feedback = $this->Feedback_monitoring_model->get_one($file_info->id_evaluacion);
		$values_feedback = $this->Values_feedback_model->get_one($evaluacion_feedback->id_valor_feedback);
		
        if ($save_id) {

            delete_file_from_directory("files/evaluaciones_feedback/evaluacion_".$id_evaluacion."/".$file_info->archivo);
			
			//if($select_evaluado && !$select_valor_compromiso){
			if($select_proposito_visita && !$select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'proposito_visita' => $values_feedback->proposito_visita, 'responsable' => "", 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
			} else if(!$select_proposito_visita && $select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'proposito_visita' => "", 'responsable' => $values_feedback->id_responsable, 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));		
			} else if($select_proposito_visita && $select_responsable){
				echo json_encode(array("success" => true, "data" => $this->_row_data($id_evaluacion), 'id_evidencia' => $id_evidencia, 'proposito_visita' => $values_feedback->proposito_visita, 'responsable' => $values_feedback->id_responsable, 'id_feedback_matrix_config' => $values_feedback->id_feedback_matrix_config, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
			}
		
            //echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
		*/

    }
		
}

