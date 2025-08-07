<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Communities_agreements_summary extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 9;
		$this->id_submodulo_cliente = 10;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    function index() {

		//$access_info = $this->get_access_info("invoice");
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$agreement_matrix_config = $this->Agreements_matrix_config_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0));
		$stakeholder_matrix_config = $this->Stakeholders_matrix_config_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0));
		
		$view_data = array();
		
		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		if($agreement_matrix_config->id){
		
			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_agreement_matrix_config"] = $agreement_matrix_config->id;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["Communities_agreements_summary_controller"] = $this;
			
			/* SECCIÓN STAKEHOLDERS */
			//Tabla y gráfico de los stakeholders ingresados en la matriz de stakeholders y separados por tipo de organización.
			$view_data["stakeholders_categories"] = $this->Values_stakeholders_model->get_number_of_stakeholders_by_type_organization($id_proyecto)->result();
			
			/* SECCIÓN ACUERDOS */
			//Gráfico y tabla de acuerdos totales del proyecto (seguimiento de acuerdos) agrupados o separados por tipo de cumplimiento 
			//(tipos de estado; tramitacion, actividades, financiero).
			$view_data["total_agreements_by_estado_tramitacion_graphic"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_graphic(array("categoria_estado" => "estado_tramitacion", "id_proyecto" => $id_proyecto))->result();
			$view_data["total_agreements_by_estado_actividades_graphic"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_graphic(array("categoria_estado" => "estado_actividades", "id_proyecto" => $id_proyecto))->result();
			$view_data["total_agreements_by_estado_financiero_graphic"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_graphic(array("categoria_estado" => "estado_financiero", "id_proyecto" => $id_proyecto))->result();
			
			$view_data["estados_tramitacion"] = $this->Communities_evaluation_status_model->get_client_agreements_status_by_type($id_cliente, "estado_tramitacion", $agreement_matrix_config->id)->result_array();
			$view_data["estados_cumplimiento_actividades"] = $this->Communities_evaluation_status_model->get_client_agreements_status_by_type($id_cliente, "estado_actividades", $agreement_matrix_config->id)->result_array();
			$view_data["estados_cumplimiento_financiero"] = $this->Communities_evaluation_status_model->get_client_agreements_status_by_type($id_cliente, "estado_financiero", $agreement_matrix_config->id)->result_array();
			
			//$view_data["total_agreements_by_estado_tramitacion_table"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_tramitacion"))->result();
			//$view_data["total_agreements_by_estado_actividades_table"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_actividades"))->result();
			//$view_data["total_agreements_by_estado_financiero_table"] = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_financiero", "id_proyecto" => $id_proyecto))->result();
			
			/* SECCIÓN FEEDBACK */
			$view_data["number_of_visits_by_type_of_stakeholder"] = $this->Values_feedback_model->get_number_of_visits_by_type_of_stakeholder($id_proyecto)->result();
			$view_data["number_of_visits_by_visit_purpose"] = $this->Values_feedback_model->get_number_of_visits_by_visit_purpose($id_proyecto)->result();
			
		} else {
			
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			$view_data["nombre_proyecto"] = $proyecto->title;
			
		}
		
		// Filtros de estados (Tramitación, Actividades, Financiero)
		$estados_evaluacion_comunidades = $this->Communities_evaluation_status_model->get_all_where(array(
			"id_cliente" => $id_cliente,
			"deleted" => 0
		))->result_array();
		
		$array_estados_tramitacion[] = array("id" => "", "text" => "- ".lang("processing_status")." -");
		$array_estados_actividades[] = array("id" => "", "text" => "- ".lang("activities_status")." -");
		$array_estados_financieros[] = array("id" => "", "text" => "- ".lang("financial_status")." -");
		
		foreach($estados_evaluacion_comunidades as $estado_evaluacion){
			if($estado_evaluacion["categoria"] == "Tramitación"){
				$array_estados_tramitacion[] = array("id" => $estado_evaluacion["id"], "text" => $estado_evaluacion["nombre_estado"]);
			}
			if($estado_evaluacion["categoria"] == "Cumplimiento de Actividades"){
				$array_estados_actividades[] = array("id" => $estado_evaluacion["id"], "text" => $estado_evaluacion["nombre_estado"]);
			}
			if($estado_evaluacion["categoria"] == "Cumplimiento Financiero"){
				$array_estados_financieros[] = array("id" => $estado_evaluacion["id"], "text" => $estado_evaluacion["nombre_estado"]);
			}
		}
		
		$view_data["estados_tramitacion_dropdown"] = json_encode($array_estados_tramitacion);
		$view_data["estados_actividades_dropdown"] = json_encode($array_estados_actividades);
		$view_data["estados_financieros_dropdown"] = json_encode($array_estados_financieros);
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		
		// PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;

        $this->template->rander("communities_agreements_summary/index", $view_data);
    
	}
	
	function list_data_tabla_estado_tramitacion(){
		$id_proyecto = $this->session->project_context;		
		$list_data = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_tramitacion", "id_proyecto" => $id_proyecto))->result();
	    $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_tabla_estado_tramitacion($data);
        }	
        echo json_encode(array("data" => $result));		
	}
	
	private function _make_row_tabla_estado_tramitacion($data) {
		$id_proyecto = $this->session->project_context;		
        $row_data = array(
			$data->nombre_acuerdo,
			$data->nombre_estado,
			$data->cantidad_estados
		);
        return $row_data;
    }
	
	function list_data_tabla_estado_actividades(){
		$id_proyecto = $this->session->project_context;
		$list_data = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_actividades", "id_proyecto" => $id_proyecto))->result();
	    $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_tabla_estado_actividades($data);
        }
        echo json_encode(array("data" => $result));
	}
	
	private function _make_row_tabla_estado_actividades($data) {
        $row_data = array(
			$data->nombre_acuerdo,

			$data->nombre_estado,
			$data->cantidad_estados
		);
        return $row_data;
    }
	
	function list_data_tabla_estado_financiero(){
		$id_proyecto = $this->session->project_context;
		$list_data = $this->Agreements_monitoring_model->get_total_agreements_by_evaluation_status_for_table(array("categoria_estado" => "estado_financiero", "id_proyecto" => $id_proyecto))->result();
	    $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_tabla_estado_financiero($data);
        }		
        echo json_encode(array("data" => $result));
	}
	
	private function _make_row_tabla_estado_financiero($data) {
        $row_data = array(
			$data->nombre_acuerdo,
			$data->nombre_estado,
			$data->cantidad_estados
		);
        return $row_data;
    }
	
	function list_data_consolidated_agreements_evaluations($id_agreement_matrix_config){
		
		// Filtros AppTable
		$estado_tramitacion = $this->input->post("estado_tramitacion");
		$estado_actividades = $this->input->post("estado_actividades");
		$estado_financiero = $this->input->post("estado_financiero");
		
		$options = array(
			"id_agreement_matrix_config" => $id_agreement_matrix_config,
			"estado_tramitacion" => $estado_tramitacion,
			"estado_actividades" => $estado_actividades,
			"estado_financiero" => $estado_financiero
		);
		
		//$list_data = $this->Agreements_monitoring_model->get_consolidated_agreements_evaluations($id_agreement_matrix_config)->result();
		$list_data = $this->Agreements_monitoring_model->get_consolidated_agreements_evaluations($options)->result();
		
		$result = array();
		foreach($list_data as $data){
			$result[] = $this->_make_row_consolidated_agreements_evaluations($data);
		}
		
        echo json_encode(array("data" => $result));
	
	}
	
	private function _make_row_consolidated_agreements_evaluations($data){

		$row_data = array();
		
		$row_data[] = $data->nombre_acuerdo;
		$row_data[] = $data->nombre_stakeholder;
		
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
		
		$row_data[] = $html_estado_tramitacion;
		$row_data[] = $html_estado_actividades;
		$row_data[] = $html_estado_financiero;
		
		$tooltip_observaciones = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->observaciones.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		
		$row_data[] = $tooltip_observaciones;
		
		return $row_data;
		
	}
	
	
	/* Para AppTable de sección ESTADOS DE CUMPLIMIENTO */
	function list_data($id_compromiso) {
		
		$list_data = $this->Compromises_model->get_data_of_compliance_status($id_compromiso)->result_array(); //traer consulta 
		
		$new_list_data = array();
		foreach($list_data as $row){
			$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"], 
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"]);
		}

		$array_columnas = array();
		$traer_columnas = $this->Compromises_model->get_fields_of_compliance_status($id_compromiso)->result_array();

		foreach($traer_columnas as $columnas){		
			$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
		}
		
        $result = array();
        foreach ($new_list_data as $id_valor_compromiso => $data) {
            $result[] = $this->_make_row(array($id_valor_compromiso => $data), $array_columnas);
        }
		
        echo json_encode(array("data" => $result));
		
    }
	
	/* Para AppTable de sección ESTADOS DE CUMPLIMIENTO */
	private function _make_row($data, $array_columnas) {
	
		$row_data = array();
		//$row_data[] = key($data);
		$row_data[] = $this->Values_compromises_model->get_one(key($data))->numero_compromiso;
		$row_data[] = $this->Values_compromises_model->get_one(key($data))->nombre_compromiso;
		
		foreach($data as $key_evaluado => $array_evaluado){
			ksort($array_evaluado);
			if(count($array_columnas) != count($array_evaluado)){ //Si la cantidad de columnas es distinta a la cantidad de evaluados
				
				foreach($array_columnas as $id_evaluado => $columna){ //Loop sobre las columnas (Evaluado 1, Evaluado N)
					
					if(in_array($id_evaluado, $array_evaluado[$id_evaluado])){
						$distintos = false;
					} else {
						$distintos = true;
					}

					if($distintos){
						$row_data[] = "-";
					} else {
						
						$id_estado_cumplimiento_compromiso = $array_evaluado[$id_evaluado]["id_estado"];
						$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;
						
						$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
						$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
						$html_estado .= $nombre_estado;
						$html_estado .= '</div>';

						$row_data[] = $html_estado;
					}
	
				}

			} else {
				
				foreach($array_evaluado as $evaluado){
					
					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$nombre_estado = $estado->nombre_estado;
					$color_estado = $estado->color;
					
					$html_estado = '<div class="text-center" style="text-align: -webkit-center;">';
					$html_estado .= '<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>';
					$html_estado .= $nombre_estado;
					$html_estado .= '</div>';

					$row_data[] = $html_estado;

				}
				
			}
			
		}
		//traer todas las evidencias de las evaluaciones
		$array_evidencias_evaluacion = array();
		$evidencia = array();
		
		foreach($array_evaluado as $evaluado){
			$evidencia = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $evaluado["id_evaluacion"], "deleted" => 0))->result_array();
			if($evidencia){
				foreach($evidencia as $row){
					$array_evidencias_evaluacion[] = $row["id"];
				}
			}
		}

		$modal_evidencias = modal_anchor(get_uri("compromises_compliance_client/view_all_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_valor_compromiso" => key($data)));
		$row_data[] = ($evidencia) ? $modal_evidencias : "-";
		
		$modal_observaciones = modal_anchor(get_uri("compromises_compliance_client/view_all_observations/"), "<i class='fa fa-info tooltips'></i>", array("class" => "edit", "title" => lang('view_observations'), "data-post-id_valor_compromiso" => key($data)));
		$row_data[] = $modal_observaciones;
		
        return $row_data;
		
    }
	
	function view_all_evidences(){
		
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_model->get_one($id_valor_compromiso)->nombre_compromiso;
		
		$html_titulo_archivos_evidencia = '<div class="form-group">';
		$html_titulo_archivos_evidencia .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_archivos_evidencia .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_archivos_evidencia .= '</div>';
		$html_final = "";
		
		foreach($evaluaciones as $evaluacion){
			
			$evidencias_evaluacion = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" => $evaluacion["id"], "deleted" => 0))->result_array();
			
			if($evidencias_evaluacion){

				$nombre_evaluado = $this->Evaluated_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;
				
				$html_archivos_evidencia = "<hr>";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				foreach($evidencias_evaluacion as $evidencia){
					
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					$html_archivos_evidencia .= anchor(get_uri("compromises_compliance_evaluation/download_file/".$evaluacion["id"]. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
				$html_final .= $html_archivos_evidencia;
				
			} 

		}
	
		$view_data["html_titulo_archivos_evidencia"] = $html_titulo_archivos_evidencia;
		$view_data["html_archivos_evidencia"] = $html_final;
		
		$this->load->view('compromises_compliance_client/view_all_evidences', $view_data);
		
	}
	
	function view_all_observations(){
		
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_model->get_one($id_valor_compromiso)->nombre_compromiso;
		
		$html_titulo_observaciones = '<div class="form-group">';
		$html_titulo_observaciones .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_observaciones .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_observaciones .= '</div>';
		$html_final = "";
		
		$this->array_sort_by_column($evaluaciones, 'id_evaluado');

		foreach($evaluaciones as $evaluacion){

			$nombre_evaluado = $this->Evaluated_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;
			
			$html_observaciones = "<hr>";
			$html_observaciones .= '<div class="form-group">';
			$html_observaciones .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
			$html_observaciones .= '<div class="col-md-9">';
			$html_observaciones .= ((!$evaluacion["observaciones"]) || $evaluacion["observaciones"] == "") ? "-" : $evaluacion["observaciones"]; 
			$html_observaciones .= '</div>';
			$html_observaciones .= '</div>';
			$html_final .= $html_observaciones;
		}
		
		$view_data["html_titulo_observaciones"] = $html_titulo_observaciones;
		$view_data["html_observaciones"] = $html_final;
		
		$this->load->view('compromises_compliance_client/view_all_observations', $view_data);
		
	}
	
	function get_quantity_of_status_evaluated($id_estado, $id_evaluado){		
		$cantidad = $this->Compromises_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->row();
		return $cantidad->cantidad;		
	}
	
	function get_percentage_of_status_evaluated($id_estado, $id_evaluado){		
		$porcentaje = $this->Compromises_model->get_percentage_of_status_evaluated($id_estado, $id_evaluado)->row();
		return $porcentaje->porcentaje;		
	}
	
	function get_color_of_status($id_estado){
		$estado = $this->Compromises_compliance_status_model->get_one($id_estado);
		return $estado->color;
	}
	
	/* Función para ordenar un array multidimensional especificando el index ($col) */
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}	
		array_multisort($sort_col, $dir, $arr);
	}
	
	function get_excel_agreements(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$matriz_acuerdos = $this->Agreements_matrix_config_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		
		$id_agreement_matrix_config = $matriz_acuerdos->id;
		
		$options = array(
			"id_agreement_matrix_config" => $id_agreement_matrix_config
		);
		
		$list_data = $this->Agreements_monitoring_model->get_consolidated_agreements_evaluations($options)->result();
		$columnas = $this->Agreements_matrix_config_model->get_fields_of_agreements_matrix($id_agreement_matrix_config)->result();
		$result = array();
		
		foreach($list_data as $data) {
			$result[] = $this->_make_row_excel_agreements($data, $columnas, $id_agreement_matrix_config);
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
		$nombre_columnas[] = array("nombre_columna" => lang("code"), "id_tipo_campo" => "code");
		$nombre_columnas[] = array("nombre_columna" => lang("name"), "id_tipo_campo" => "name");
		$nombre_columnas[] = array("nombre_columna" => lang("description"), "id_tipo_campo" => "description");
		$nombre_columnas[] = array("nombre_columna" => lang("period"), "id_tipo_campo" => "period");
		$nombre_columnas[] = array("nombre_columna" => lang("managing"), "id_tipo_campo" => "managing");
		
		foreach($columnas as $columna_cabecera){
			if(($columna_cabecera->id_tipo_campo == 11)||($columna_cabecera->id_tipo_campo == 12)){
				continue;
			}
			$nombre_columnas[] = array("nombre_columna" => $columna_cabecera->nombre_campo, "id_tipo_campo" => $columna_cabecera->id_tipo_campo);
		}
		
		$nombre_columnas[] = array("nombre_columna" => lang("processing_status"), "id_tipo_campo" => "processing_status");
		$nombre_columnas[] = array("nombre_columna" => lang("activities_status"), "id_tipo_campo" => "activities_status");
		$nombre_columnas[] = array("nombre_columna" => lang("financial_status"), "id_tipo_campo" => "financial_status");
		$nombre_columnas[] = array("nombre_columna" => lang("observations"), "id_tipo_campo" => "observations");

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("agreements_summary_excel"))
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
					
					} elseif($columna["id_tipo_campo"] == "code" || $columna["id_tipo_campo"] == "name"
					|| $columna["id_tipo_campo"] == "period" || $columna["id_tipo_campo"] == "managing"){ 
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

					} elseif($columna["id_tipo_campo"] == "description" || $columna["id_tipo_campo"] == "observations"){ 
						
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
					
					} elseif($columna["id_tipo_campo"] == "processing_status" || $columna["id_tipo_campo"] == "activities_status" 
					|| $columna["id_tipo_campo"] == "financial_status"){ 
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
		
		$row = 6;
		foreach($result as $res){
			$doc->getActiveSheet()->setCellValueExplicit('A'.$row, $res[0], PHPExcel_Cell_DataType::TYPE_STRING);
			$row++;
		}

		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');
		
		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/

		$nombre_hoja = lang("agreements_summary_excel");
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("agreements_summary_excel")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
		
	}
	
	private function _make_row_excel_agreements($data, $columnas, $id_agreement_matrix_config){
		
		$id_proyecto = $this->session->project_context;
		$acuerdo = $this->Values_agreements_model->get_one($data->id_acuerdo);

		$row_data = array();
		$row_data[] = $acuerdo->codigo;
		$row_data[] = $acuerdo->nombre_acuerdo;
		$row_data[] = $acuerdo->descripcion;
		
		// Periodo
		$array_periodo = json_decode($acuerdo->periodo, true);
		$start_date = $array_periodo['start_date'];
		$start_date = get_date_format($start_date, $id_proyecto);
		$end_date = $array_periodo['end_date'];
		$end_date = get_date_format($end_date, $id_proyecto);
		$periodo = $start_date.' - '.$end_date;		
		$row_data[] = $periodo;

		// Gestor
		$gestor = $this->Users_model->get_one($acuerdo->gestor);
		$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
		$row_data[] = $nombre_gestor;

		// DINAMICOS ($row_data[] = $data->datos_campos)
		
		if($acuerdo->datos_campos){

			$arreglo_fila = json_decode($acuerdo->datos_campos, true);
			$cont = 0;
		
			foreach($columnas as $columna) {
				$cont++;
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if($columna->id_tipo_campo == 3){ // si es numero
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto): "-";
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
					}else{
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
		
		// Estados evaluaciones
		$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($data->estado_tramitacion);
		$estado_actividades = $this->Communities_evaluation_status_model->get_one($data->estado_actividades);
		$estado_financiero = $this->Communities_evaluation_status_model->get_one($data->estado_financiero);
		$row_data[] = $estado_tramitacion->nombre_estado;
		$row_data[] = $estado_actividades->nombre_estado;
		$row_data[] = $estado_financiero->nombre_estado;		
		$row_data[] = $data->observaciones;
	
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
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["info_cliente"] = $info_cliente;
		$view_data["info_proyecto"] = $info_proyecto;
		
		$agreement_matrix_config = $this->Agreements_matrix_config_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0));
		$stakeholder_matrix_config = $this->Stakeholders_matrix_config_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0));
		
		$imagenes_graficos = $this->input->post("imagenes_graficos");
		
		$view_data["grafico_categorias_totales_sh"] = $imagenes_graficos["image_categorias_totales_sh"];
		$view_data["grafico_estado_tramitacion"] = $imagenes_graficos["image_estado_tramitacion"];
		$view_data["grafico_estado_actividades"] = $imagenes_graficos["image_estado_actividades"];
		$view_data["grafico_estado_financiero"] = $imagenes_graficos["image_estado_financiero"];
		$view_data["grafico_feedback_visit_purpose"] = $imagenes_graficos["image_feedback_visit_purpose"];
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		if($agreement_matrix_config->id){
		
			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_agreement_matrix_config"] = $agreement_matrix_config->id;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["Communities_agreements_summary_controller"] = $this;
			
			// Sección Stakeholders
			// Tabla y gráfico de los stakeholders ingresados en la matriz de stakeholders y separados por tipo de organización.
			$view_data["stakeholders_categories"] = $this->Values_stakeholders_model->get_number_of_stakeholders_by_type_organization($id_proyecto)->result();
			
			// Sección Acuerdos
			$options = array(
				"id_agreement_matrix_config" => $agreement_matrix_config->id,
			);
			$list_data = $this->Agreements_monitoring_model->get_consolidated_agreements_evaluations($options)->result();
			
			$result = array();
			foreach($list_data as $data){
				$result[] = $this->_make_row_acuerdos_consolidado_pdf($data);
			}
			
			$view_data["data_acuerdos_consolidado"] = $result;
				
			// Sección Feedback
			$view_data["number_of_visits_by_type_of_stakeholder"] = $this->Values_feedback_model->get_number_of_visits_by_type_of_stakeholder($id_proyecto)->result();
			
		} else {
			
			$view_data["nombre_proyecto"] = $info_proyecto->title;
			
		}
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("communities")."_".lang("summary")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("communities")."_".lang("summary")."_".date('Y-m-d'));
        $this->pdf->SetKeywords('TCPDF, PDF');
		
		//$this->pdf->SetPrintHeader(false);
		//$this->pdf->SetPrintFooter(false);
		// set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
        $this->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		// set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$this->pdf->SetAutoPageBreak(TRUE,PDF_MARGIN_BOTTOM);	
		//relación utilizada para ajustar la conversión de los píxeles
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// ---------------------------------------------------------
		// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$this->SetFont('freemono', '', 14, '', true);        
		$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96); 
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$view_data["fontawesome"] = $fontawesome;
		$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('communities_agreements_summary/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("communities")."_".lang("summary")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;
	
	}
	
	private function _make_row_acuerdos_consolidado_pdf($data){
		
		$row_data = array();
		
		$row_data["nombre_acuerdo"] = $data->nombre_acuerdo;
		$row_data["nombre_stakeholder"] = $data->nombre_stakeholder;
		
		$estado_tramitacion = $this->Communities_evaluation_status_model->get_one($data->estado_tramitacion);
		$estado_actividades = $this->Communities_evaluation_status_model->get_one($data->estado_actividades);
		$estado_financiero = $this->Communities_evaluation_status_model->get_one($data->estado_financiero);
		
		$html_estado_tramitacion = '<span style="color:'.$estado_tramitacion->color.';">';
		$html_estado_tramitacion .= '&#xf111;'; // círculo (fontawesome)
		$html_estado_tramitacion .= '</span>';
		$html_estado_tramitacion .= "nombre_estado:".$estado_tramitacion->nombre_estado;
		
		$html_estado_actividades = '<span style="color:'.$estado_actividades->color.';">';
		$html_estado_actividades .= '&#xf111;'; // círculo (fontawesome)
		$html_estado_actividades .= '</span>';
		$html_estado_actividades .= "nombre_estado:".$estado_actividades->nombre_estado;
		
		$html_estado_financiero = '<span style="color:'.$estado_financiero->color.';">';
		$html_estado_financiero .= '&#xf111;'; // círculo (fontawesome)
		$html_estado_financiero .= '</span>';
		$html_estado_financiero .= "nombre_estado:".$estado_financiero->nombre_estado;
		
		$row_data["estado_tramitacion"] = $html_estado_tramitacion;
		$row_data["estado_actividades"] = $html_estado_actividades;
		$row_data["estado_financiero"] = $html_estado_financiero;
		$row_data["observaciones"] = $data->observaciones;
		
		return $row_data;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}
	
}

