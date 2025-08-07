<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Agreements_monitoring extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9;
		$this->id_submodulo_cliente = 13;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }

    function index() {
		//$this->access_only_allowed_members();
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$access_info = $this->get_access_info("invoice");
		
		$id_proyecto = $this->session->project_context;	
		$proyect_info = $this->Projects_model->get_one($id_proyecto);
		
		//Traer todos los acuerdos ingresados en la matriz de acuerdos del proyecto.
		//matriz de acuerdos del proyecto:
		$agreement_matrix = $this->Agreements_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$valores_acuerdos = $this->Values_agreements_model->get_all_where(array("id_agreement_matrix_config" => $agreement_matrix->id, "deleted" => 0))->result_array();
		$dropdown_acuerdos = array("" => "-");
		
		foreach($valores_acuerdos as $valor_acuerdo){
			$dropdown_acuerdos[$valor_acuerdo["id"]] = $valor_acuerdo["nombre_acuerdo"];
		}
		/*
		//Traer todos los stakeholders ingresados en la matriz de stakeholders del proyecto
		$stakeholder_matrix = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$valores_stakeholders = $this->Values_stakeholders_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix->id, "deleted" => 0))->result_array();
		
		
		$dropdown_stakeholders = array("" => "-");
		
		foreach($valores_stakeholders as $valor_sh){
			//$dropdown_stakeholders[$valor_sh["id"]] = $valor_sh["nombres"] . " " . $valor_sh["apellidos"];
			$dropdown_stakeholders[$valor_sh["id"]] = $valor_sh["nombre"];
		}
		*/
		$array_stakeholders = array();
		$dropdown_stakeholders = array("" => "-");
		foreach($valores_acuerdos as $valor_acuerdo){
			$valor_acuerdo = json_decode($valor_acuerdo["stakeholders"]);
			foreach($valor_acuerdo as $sh){
				$array_stakeholders[$sh] = $sh;
			}	
		}
		
		ksort($array_stakeholders);
		
		foreach($array_stakeholders as $sh){
			$valor_stakeholder = $this->Values_stakeholders_model->get_one($sh);
			$dropdown_stakeholders[$valor_stakeholder->id] = $valor_stakeholder->nombre; 
		}

		$view_data["project_info"] = $proyect_info;
		$view_data["dropdown_acuerdos"] = $dropdown_acuerdos;
		$view_data["dropdown_stakeholders"] = $dropdown_stakeholders;
		$view_data["id_agreements_matrix"] = $agreement_matrix->id;

        $this->template->rander("agreements_monitoring/index", $view_data);
		
    }
	
	//modificar
	function modal_form() {
		
		$agreement_monitoring_id = $this->input->post("id");
        $value_agreement_id = $this->input->post("value_agreement_id");
		$id_stakeholder = $this->input->post("id_stakeholder");
		
		/*
		//traer el id de cliente para consultar y listar los estados de cumplimiento de ese cliente.
		$id_valor_compromiso = $compliance_evaluation_info->id_valor_compromiso;
		$id_compromiso = $this->Values_compromises_model->get_one($id_valor_compromiso)->id_compromiso;
		$id_proyecto_compromiso = $this->Compromises_model->get_one($id_compromiso)->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto_compromiso)->client_id;
		$id_estados_cumplimiento_compromiso = $compliance_evaluation_info->id_estados_cumplimiento_compromiso;
		$estados = array("" => "-") + $this->Compromises_compliance_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente));
		*/
		
		$id_agreement_matrix_config = $this->Values_agreements_model->get_one($value_agreement_id)->id_agreement_matrix_config;
		$id_proyecto = $this->Agreements_matrix_config_model->get_one($id_agreement_matrix_config)->id_proyecto;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$dropdown_estados_tramitacion = array("" => "-") + $this->Communities_evaluation_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "categoria" => "Tramitación"));
		$dropdown_estados_actividades = array("" => "-") + $this->Communities_evaluation_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "categoria" => "Cumplimiento de Actividades"));
		$dropdown_estados_financieros = array("" => "-") + $this->Communities_evaluation_status_model->get_dropdown_list(array("nombre_estado", "color"), "id", array("id_cliente" => $id_cliente, "categoria" => "Cumplimiento Financiero"));
				
		//Dropdown de los tipos de estados de acuerdo ingresados.
		//$estados_tramitacion = $this->Communities_evaluation_status_model->get_all_where(array("categoria" => "Tramitación", "deleted" => 0))->result_array();
		//$estados_actividades = $this->Communities_evaluation_status_model->get_all_where(array("categoria" => "Cumplimiento de Actividades", "deleted" => 0))->result_array();
		//$estados_financieros = $this->Communities_evaluation_status_model->get_all_where(array("categoria" => "Cumplimiento Financiero", "deleted" => 0))->result_array();
		
		//$dropdown_estados_tramitacion = array("" => "-");
		//foreach($estados_tramitacion as $et){
			//$dropdown_estados_tramitacion[$et['id']] = $et['nombre_estado'];
		//}
		
		//$dropdown_estados_actividades = array("" => "-");
		//foreach($estados_actividades as $et){
		//	$dropdown_estados_actividades[$et['id']] = $et['nombre_estado'];
		//}
		
		//$dropdown_estados_financieros = array("" => "-");
		//foreach($estados_financieros as $et){
		//	$dropdown_estados_financieros[$et['id']] = $et['nombre_estado'];
		//}
		
		$view_data["dropdown_estados_tramitacion"] = $dropdown_estados_tramitacion;
		$view_data["dropdown_estados_actividades"] = $dropdown_estados_actividades;
		$view_data["dropdown_estados_financieros"] = $dropdown_estados_financieros;
		
		if($agreement_monitoring_id){
		
			$agreement_monitoring = $this->Agreements_monitoring_model->get_one($agreement_monitoring_id);
			$view_data["agreement_monitoring_id"] = $agreement_monitoring_id;
			$view_data["model_info"] = $agreement_monitoring;
			
			if($value_agreement_id && $id_stakeholder){
				
				$value_agreement = $this->Values_agreements_model->get_one($value_agreement_id);
				$stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
				
				$view_data["value_agreement_id"] = $value_agreement_id;
				$view_data["value_agreement"] = $value_agreement;
				$view_data["id_stakeholder"] = $id_stakeholder;
				$view_data["stakeholder"] = $stakeholder;
				
				//GESTOR
				$gestor = $this->Users_model->get_one($value_agreement->gestor);
				$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
				$view_data["nombre_gestor"] = $nombre_gestor;
				
				$archivos_evidencia = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $agreement_monitoring_id, "deleted" => 0))->result_array();
				
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
						$html_archivos_evidencia .= anchor(get_uri("agreements_monitoring/download_file/".$agreement_monitoring_id. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
						
						$html_archivos_evidencia .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-agreement_monitoring_id" => $agreement_monitoring_id, "data-id_evidencia" => $evidencia["id"], "data-action-url" => get_uri("agreements_monitoring/delete_file"), "data-action" => "delete-fileConfirmation"));
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
				

			} else {
				show_404();
			}
		
		} else {
			
			if($value_agreement_id && $id_stakeholder){
				
				$value_agreement = $this->Values_agreements_model->get_one($value_agreement_id);
				$stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
				
				$view_data["value_agreement_id"] = $value_agreement_id;
				$view_data["value_agreement"] = $value_agreement;
				$view_data["id_stakeholder"] = $id_stakeholder;
				$view_data["stakeholder"] = $stakeholder;
				
				//GESTOR
				$gestor = $this->Users_model->get_one($value_agreement->gestor);
				$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
				$view_data["nombre_gestor"] = $nombre_gestor;

			} else {
				show_404();
			}
			
		}
		
		$this->load->view('agreements_monitoring/agreements_evaluation/modal_form', $view_data);

    }
	
	
	function save() {
		
		$agreement_monitoring_id = $this->input->post("agreement_monitoring_id"); 
		$file = $this->input->post("archivo_importado");	
		$value_agreement_id = $this->input->post("value_agreement_id");
		
		$id_evidencia_eliminar = $this->input->post("id_evidencia_eliminar");
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		//SI EL USUARIO HA ELIMINADO ARCHIVOS, BORRARLOS DE LA BASE DE DATOS Y FÍSICAMENTE
		if($agreement_monitoring_id){
			
			if($id_evidencia_eliminar){
				
				foreach($id_evidencia_eliminar as $id_evidencia){
					
					$file_info = $this->Agreements_evidences_model->get_one($id_evidencia);
					$delete_evidence_id = $this->Agreements_evidences_model->delete($id_evidencia);
					
					if($delete_evidence_id){
						delete_file_from_directory("files/evaluaciones_acuerdos/evaluacion_".$agreement_monitoring_id."/".$file_info->archivo);
					}
					
				}
			
			}
			
		}			
		
		$data_agreement_evaluation = array(
			"id_valor_acuerdo" => $value_agreement_id,
			"id_stakeholder" => $this->input->post("id_stakeholder"),
			"estado_tramitacion" => $this->input->post("processing_status"),
			"estado_actividades" => $this->input->post("activities_status"),
			"estado_financiero" => $this->input->post("financial_status"),
			"observaciones" => $this->input->post("observaciones"),
			//"id_evidencias" => ""
		);
		
		if($agreement_monitoring_id){ //edit
			
			$data_agreement_evaluation["modified_by"] = $this->login_user->id;
			$data_agreement_evaluation["modified"] = get_current_utc_time();
			$save_id = $this->Agreements_monitoring_model->save($data_agreement_evaluation, $agreement_monitoring_id);
			
		} else { //insert
			
			$data_agreement_evaluation["created_by"] = $this->login_user->id;
			$data_agreement_evaluation["created"] = get_current_utc_time();
			$save_id = $this->Agreements_monitoring_model->save($data_agreement_evaluation);

		}
		
        if ($save_id) {

			if($file){
				//Si no existe el directorio para los archivos de evidencia, crearlo antes de mover el archivo.
				$crear_carpeta = $this->create_agreement_evidence_folder($save_id);
				$archivo_subido = move_temp_file($file, "files/evaluaciones_acuerdos/evaluacion_".$save_id."/");
				
				//Guardar el registro en la tabla de evidencias_acuerdos
				$datos_evidencia = array(
					"id_evaluacion_acuerdo" => $save_id,
					"archivo" => $archivo_subido,
					"created_by" => $this->login_user->id
				);
				$save_evidencia_id = $this->Agreements_evidences_model->save($datos_evidencia);
			}			
			
			echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => (string)$save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
       
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
	
	function list_data($id_agreements_matrix, $value_agreement_id, $id_stakeholder) {

		$puede_editar = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
				
		$options = array(
			"id_agreements_matrix" => $id_agreements_matrix,
			"id_valor_acuerdo" => $value_agreement_id,
			"id_stakeholder" => $id_stakeholder,
			"id_proyecto" => $this->session->project_context,
			"puede_editar" => $puede_editar
		);
		
		//$data = $this->Agreements_monitoring_model->get_details($options)->row();
		$list_data = $this->Agreements_monitoring_model->get_details($options);
		
		/*
		if($data){
			$result[] = $this->_make_row($data);			
			echo json_encode(array("data" => $result));	
		} else {
			
			//array para mostrar una fila y poder evaluar el acuerdo y stakeholder cuando no esté ya evaluado.
			$result = array($this->Agreements_monitoring_model->get_details_no_data($options));
			$result[] = $this->Agreements_monitoring_model->get_details_no_data($options);
			echo json_encode(array("data" => $result));
		}
		*/
		echo json_encode(array("data" => $list_data)); 
		
	}
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->Agreements_monitoring_model->get_one($id);
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$value_agreement = $this->Values_agreements_model->get_one($data->id_valor_acuerdo);
		$stakeholder = $this->Values_stakeholders_model->get_one($data->id_stakeholder);
		$nombre_stakeholder = $stakeholder->nombre;
		$gestor = $this->Users_model->get_one($value_agreement->gestor);
		$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
		
		//ESTADOS
		$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($data->estado_tramitacion);
		$estado_actividades = $this->Communities_evaluation_status_model->get_one($data->estado_actividades);
		$estado_financiero = $this->Communities_evaluation_status_model->get_one($data->estado_financiero);
		
		$html_estado_tramitacion = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado_tramitacion .= '<div style="background-color:'.$estado_tramitacion->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado_tramitacion .= $estado_tramitacion->nombre_estado;
		$html_estado_tramitacion .= '</div>';
		
		$html_estado_actividades = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado_actividades .= '<div style="background-color:'.$estado_actividades->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado_actividades .= $estado_actividades->nombre_estado;
		$html_estado_actividades .= '</div>';
		
		$html_estado_financiero = '<div class="text-center" style="text-align: -webkit-center;">';
		$html_estado_financiero .= '<div style="background-color:'.$estado_financiero->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
		$html_estado_financiero .= $estado_financiero->nombre_estado;
		$html_estado_financiero .= '</div>';
		
		//EVIDENCIAS
		$evidencias = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $data->id, "deleted" => 0))->result_array();
		$modal_evidencias = modal_anchor(get_uri("agreements_monitoring/view_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_evaluacion_acuerdo" => $data->id));
		$evidencias_acuerdo = ($evidencias) ? $modal_evidencias : "-";

		$row_data = array(
			$value_agreement->id,
			$value_agreement->codigo,
			$value_agreement->nombre_acuerdo,
			$nombre_gestor,
			$nombre_stakeholder,
			$html_estado_tramitacion,
			$html_estado_actividades,
			$html_estado_financiero,
			$data->observaciones,
			$evidencias_acuerdo,
			($data->modified) ? get_date_format($data->modified, $id_proyecto) : "-"
		);
		
		$row_data[] = modal_anchor(get_uri("agreements_monitoring/view"), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_evaluation'), "data-post-id" => $data->id, "data-post-value_agreement_id" => $data->id_valor_acuerdo, "data-post-id_stakeholder" => $data->id_stakeholder))
				    . modal_anchor(get_uri("agreements_monitoring/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_evaluation'), "data-post-id" => $data->id, "data-post-value_agreement_id" => $data->id_valor_acuerdo, "data-post-id_stakeholder" => $data->id_stakeholder));
		
        return $row_data;
		
    }
	
	function view() {
		
		$agreement_monitoring_id = $this->input->post("id"); 
		$value_agreement_id = $this->input->post("value_agreement_id"); 
		$id_stakeholder = $this->input->post("id_stakeholder"); 
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
        if ($agreement_monitoring_id) {

		    $agreement_monitoring_info = $this->Agreements_monitoring_model->get_one($agreement_monitoring_id);

            if ($agreement_monitoring_info) {
				
				$value_agreement = $this->Values_agreements_model->get_one($agreement_monitoring_info->id_valor_acuerdo);
				$value_stakeholder = $this->Values_stakeholders_model->get_one($agreement_monitoring_info->id_stakeholder);
				
				$view_data["codigo"] = $value_agreement->codigo;
				$view_data["nombre_acuerdo"] = $value_agreement->nombre_acuerdo;
				
				//GESTOR
				$gestor = $this->Users_model->get_one($value_agreement->gestor);
				$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
				$view_data["nombre_gestor"] = $nombre_gestor;
				
				$view_data["nombre_stakeholder"] = $value_stakeholder->nombre;
				
				//ESTADOS
				$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($agreement_monitoring_info->estado_tramitacion);
				$estado_actividades = $this->Communities_evaluation_status_model->get_one($agreement_monitoring_info->estado_actividades);
				$estado_financiero = $this->Communities_evaluation_status_model->get_one($agreement_monitoring_info->estado_financiero);

				$html_estado_tramitacion = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
				$html_estado_tramitacion .= '<div style="background-color:'.$estado_tramitacion->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$estado_tramitacion->nombre_estado;
				$html_estado_tramitacion .= '</div>';
				
				$html_estado_actividades = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
				$html_estado_actividades .= '<div style="background-color:'.$estado_actividades->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$estado_actividades->nombre_estado;
				$html_estado_actividades .= '</div>';
				
				$html_estado_financiero = '<div class="text-center pull-left" style="text-align: -webkit-center;">';
				$html_estado_financiero .= '<div style="background-color:'.$estado_financiero->color.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>'.$estado_financiero->nombre_estado;
				$html_estado_financiero .= '</div>';
				
				$view_data["estado_tramitacion"] = $html_estado_tramitacion;
				$view_data["estado_actividades"] = $html_estado_actividades;
				$view_data["estado_financiero"] = $html_estado_financiero;
				
				$view_data["observaciones"] = $agreement_monitoring_info->observaciones;
				$view_data["ult_mod"] = ($agreement_monitoring_info->modified) ? time_date_zone_format($agreement_monitoring_info->modified, $id_proyecto) : time_date_zone_format($agreement_monitoring_info->created, $id_proyecto);

				$this->load->view('agreements_monitoring/agreements_evaluation/view', $view_data);
				
            } else {
                show_404();
            }
        } else if($value_agreement_id && $id_stakeholder){
			
			$value_agreement = $this->Values_agreements_model->get_one($value_agreement_id);
			$value_stakeholder = $this->Values_stakeholders_model->get_one($id_stakeholder);
			
			$view_data["codigo"] = $value_agreement->codigo;
			$view_data["nombre_acuerdo"] = $value_agreement->nombre_acuerdo;
			
			//GESTOR
			$gestor = $this->Users_model->get_one($value_agreement->gestor);
			$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
			$view_data["nombre_gestor"] = $nombre_gestor;
			
			$view_data["nombre_stakeholder"] = $value_stakeholder->nombre;				
			
			$view_data["estado_tramitacion"] = "-";
			$view_data["estado_actividades"] = "-";
			$view_data["estado_financiero"] = "-";
			$view_data["observaciones"] = "-";
			$view_data["ult_mod"] = "-";
			
			$this->load->view('agreements_monitoring/agreements_evaluation/view', $view_data);
			
		} else {
            show_404();
        }
    }
	

	function view_evidences() {
		
        //$this->access_only_allowed_members();
		
		$id_evaluacion_acuerdo = $this->input->post("id_evaluacion_acuerdo");

        if ($id_evaluacion_acuerdo) {
			$archivos_evidencia = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" => $id_evaluacion_acuerdo, "deleted" => 0))->result_array();
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
					$html_archivos_evidencia .= anchor(get_uri("agreements_monitoring/download_file/".$id_evaluacion_acuerdo. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
				$this->load->view('agreements_monitoring/agreements_evaluation/view_evidences', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	//Funcion que retorna la tabla con la evaluacion del evaluado del compromiso de los filtros.
	function get_stakeholders_of_agreement(){
		
		/*
		$view_data["id_evaluado"] = $this->input->post('id_evaluado');
		$view_data["id_valor_compromiso"] = $this->input->post('id_valor_compromiso');
		$view_data["id_compromiso_proyecto"] = $this->input->post('id_compromiso_proyecto');
		
		$this->load->view("compromises_compliance_evaluation/compliance_evaluation/index", $view_data);
		*/
		
		$value_agreement_id = $this->input->post('id_acuerdo');
		$value_agreement = $this->Values_agreements_model->get_one($value_agreement_id);
		$stakeholders_of_value_agreement = json_decode($value_agreement->stakeholders);
		
		$dropdown_stakeholders = array("" => "-");
		foreach($stakeholders_of_value_agreement as $stakeholder_id){
			$stakeholder = $this->Values_stakeholders_model->get_one($stakeholder_id);
			//$dropdown_stakeholders[$stakeholder->id] = $stakeholder->nombres . " " . $stakeholder->apellidos;
			$dropdown_stakeholders[$stakeholder->id] = $stakeholder->nombre;
		}
		
		$html = '<div class="col-md-4 p0">';
			$html .= '<label for="stakeholder" class="col-md-2 p0">'. lang('stakeholder'). '</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("stakeholder", $dropdown_stakeholders, "", "id='stakeholder' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
		//$this->load->view("agreements_monitoring/agreements_evaluation/index", $view_data);
		
	}
	
	function get_agreements_of_stakeholder(){
		
		$id_stakeholder = $this->input->post('id_stakeholder');
		$agreements_of_stakeholders = $this->Values_agreements_model->get_agreements_that_have_stakeholder($id_stakeholder)->result_array();
		
		$dropdown_acuerdos = array("" => "-");
		foreach($agreements_of_stakeholders as $agreement){
			$dropdown_acuerdos[$agreement["id"]] = $agreement["nombre_acuerdo"];
		}
		
		$html = '<div class="col-md-4 p0">';
			$html .= '<label for="agreement" class="col-md-2">'. lang('agreement'). '</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("agreement", $dropdown_acuerdos, "", "id='agreement' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	function get_stakeholders_dropdown(){
		
		$id_proyecto = $this->session->project_context;	
		
		//Traer todos los stakeholders ingresados en la matriz de stakeholders del proyecto
		$stakeholder_matrix = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$valores_stakeholders = $this->Values_stakeholders_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix->id, "deleted" => 0))->result_array();
		$dropdown_stakeholders = array("" => "-");
		
		foreach($valores_stakeholders as $valor_sh){
			//$dropdown_stakeholders[$valor_sh["id"]] = $valor_sh["nombres"] . " " . $valor_sh["apellidos"];
			$dropdown_stakeholders[$valor_sh["id"]] = $valor_sh["nombre"];
		}
		
		$html = '<div class="col-md-4 p0">';
			$html .= '<label for="stakeholder" class="col-md-2 p0">'. lang('stakeholder'). '</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("stakeholder", $dropdown_stakeholders, "", "id='stakeholder' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	function get_agreements_dropdown(){
		
		$id_proyecto = $this->session->project_context;	
		
		//Traer todos los acuerdos ingresados en la matriz de acuerdos del proyecto.
		//matriz de acuerdos del proyecto:
		$agreement_matrix = $this->Agreements_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$valores_acuerdos = $this->Values_agreements_model->get_all_where(array("id_agreement_matrix_config" => $agreement_matrix->id, "deleted" => 0))->result_array();
		$dropdown_acuerdos = array("" => "-");
		
		foreach($valores_acuerdos as $valor_acuerdo){
			$dropdown_acuerdos[$valor_acuerdo["id"]] = $valor_acuerdo["nombre_acuerdo"];
		}
		
		$html = '<div class="col-md-4 p0">';
			$html .= '<label for="agreement" class="col-md-2">'. lang('agreement'). '</label>';
			$html .= '<div class="col-md-10">';
			$html .= form_dropdown("agreement", $dropdown_acuerdos, "", "id='agreement' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	function get_evaluation_table_of_agreement(){
		
		$view_data["value_agreement_id"] = $this->input->post('id_acuerdo');
		$view_data["id_stakeholder"] = $this->input->post('id_stakeholder');
		$view_data["id_agreements_matrix"] = $this->input->post('id_agreements_matrix');
		//$this->load->view("compromises_compliance_evaluation/compliance_evaluation/index", $view_data);
		$this->load->view("agreements_monitoring/agreements_evaluation/index", $view_data);
		
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
	
	function download_file($id_evaluacion_acuerdo, $id_evidencia) {

		$file_info = $this->Agreements_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$filename = $file_info->archivo;
        $file_data = serialize(array(array("file_name" => $filename)));

        download_app_files("files/evaluaciones_acuerdos/evaluacion_".$id_evaluacion_acuerdo."/", $file_data);
		
    }
	
	function delete_file() {
				
		$agreement_monitoring_id = $this->input->post('agreement_monitoring_id');
		$id_evidencia = $this->input->post('id_evidencia');
		
        $file_info = $this->Agreements_evidences_model->get_one($id_evidencia);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$campo_nuevo = "";
		
		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, "id_evidencia" => $id_evidencia));
		
		/*
		$save_id = $this->Agreements_evidences_model->update_where(array("deleted" => 1), array("id" => $id_evidencia));

        if ($save_id) {

            delete_file_from_directory("files/evaluaciones_acuerdos/evaluacion_".$agreement_monitoring_id."/".$file_info->archivo);
			echo json_encode(array("success" => true, "data" => $this->_row_data($agreement_monitoring_id), 'id_evidencia' => $id_evidencia, 'view' => $this->input->post('view'), 'message' => lang('record_deleted')));
            //echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
		*/

    }
	
	function create_agreement_evidence_folder($id_evaluacion_acuerdo) {
		
		if(!file_exists(__DIR__.'/../../files/evaluaciones_acuerdos/evaluacion_'.$id_evaluacion_acuerdo)) {
			if(mkdir(__DIR__.'/../../files/evaluaciones_acuerdos/evaluacion_'.$id_evaluacion_acuerdo, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}
		
}

