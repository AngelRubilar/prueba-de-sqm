<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compromises_compliance_client extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 6;
		$this->id_submodulo_cliente = 3;
		
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
		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$view_data = array();
		
		$cliente = $this->Clients_model->get_one($id_cliente);
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["project_info"] = $proyecto;
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		if($id_compromiso_rca){
		
			$view_data["id_cliente"] = $id_cliente;
			$view_data["id_compromiso_rca"] = $id_compromiso_rca;
			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			$view_data["id_proyecto"] = $id_proyecto;
			$view_data["Compromises_compliance_client_controller"] = $this;
			
			/* SECCIÓN RESUMEN POR EVALUADO */
			
			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(
				array(
					"id_compromiso" => $id_compromiso_rca, 
					"deleted" => 0
				)
			)->result();
			
			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(
				array(
					"id_cliente" => $id_cliente, 
					"tipo_evaluacion" => "rca",
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project(
				$id_proyecto, 
				$end_date
			)->result();
			
			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$array_evaluados_estados = array();
			$array_total_por_evaluado = array();
			$array_total_por_estado = array();
			$array_compromisos_evaluaciones_no_cumple = array();
			$total = 0;
			
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				
				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados_evaluados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluados" => array(),
					"cantidad_categoria" => 0,
				);
				
				$cant_estado = 0;
				foreach($evaluados as $evaluado) {
					
					$id_evaluado = $evaluado->id;
					$cant = 0;
					
					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());
					
					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado && 
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
							$array_evaluados_estados[$id_evaluado][$id_estado][] = 1;
							$cant++;
							$cant_estado++;
							
							if($estado->categoria == "No Cumple"){
								$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
								$ultima_evaluacion->criticidad = $criticidad_info->nombre;
								$array_compromisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
							}
						}
					}
					
					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
					$array_total_por_evaluado[$id_evaluado][] = $cant;
					$array_total_por_estado[$id_estado][] = $cant;
				}
				
				$array_estados_evaluados[$id_estado]["cantidad_categoria"] = $cant_estado;
				$total += $cant_estado;
			}
			
			$view_data["evaluados_rca"] = $evaluados;
			$view_data["total_compromisos_aplicables_rca"] = $total;
			$view_data["total_cantidades_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["total_cantidades_evaluados_estados_rca"] = $array_evaluados_estados;
			$view_data["array_total_por_evaluado_rca"] = $array_total_por_evaluado;

			/* FIN SECCIÓN RESUMEN POR EVALUADO */
			
			
			/* SECCIÓN ESTADOS DE CUMPLIMIENTO */
			
			$json_string_columnas = ',{"title":"' . lang("name") .'", "class": "text-left dt-head-center"}';
			$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

			foreach($traer_columnas as $columnas){		
				$json_string_columnas .= ',{"title":"' .$columnas["nombre_evaluado"] . '", "class": "text-center dt-head-center no_breakline", render: function (data, type, row) {return "<center>"+data+"</center>";}}';
			}
			
			$json_string_columnas .= ',{"title":"' . lang("evidence") .'", "class":"text-center option"}';
			$json_string_columnas .= ',{"title":"' . lang("observations") .'", "class":"text-center option"}';		
			$view_data["columnas"] = $json_string_columnas;
			
			// Filtro Reportabilidad
			$array_reportabilidad[] = array("id" => "", "text" => "- ".lang("reportability")." -");
			$array_reportabilidad[] = array("id" => "si", "text" => lang("yes"));
			$array_reportabilidad[] = array("id" => "no", "text" => lang("no"));
			$view_data['reportabilidad_dropdown'] = json_encode($array_reportabilidad);
			
			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO */
		}
		
		if($id_compromiso_reportables){
			/* SECCIÓN COMPROMISOS REPORTABLES */
			
			// ESTADOS REPORTABLES
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(
				array(
					"id_cliente" => $id_cliente, 
					"tipo_evaluacion" => "reportable",
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_last_evaluations_of_project(
				$id_proyecto, 
				$end_date
			)->result();
			
			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$total_evaluado = 0;
			$array_compromisos_reportables_evaluaciones_no_cumple = array();
			$max_dates = array();
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				if($estado->categoria == "No Aplica"){
					continue;
				}
				
				$array_estados_evaluados[$id_estado] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"planificaciones_evaluaciones" => array(),
					"cant" => 0,
				);
				
				$cant = 0;
				foreach($ultimas_evaluaciones as $ultima_evaluacion) {
					if($ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado){
						
						$array_estados_evaluados[$id_estado]["planificaciones_evaluaciones"] = $ultima_evaluacion;
						$cant++;
						
						if($estado->categoria == "No Cumple"){
							$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
							$ultima_evaluacion->criticidad = $criticidad_info->nombre;
							$id_valor_compromiso = $ultima_evaluacion->id_valor_compromiso;

							if(is_null($max_dates[$id_valor_compromiso])){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}elseif(strtotime($max_dates[$id_valor_compromiso]) < strtotime($ultima_evaluacion->planificacion)){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}
							
						}
					}
				}
					
				$array_estados_evaluados[$id_estado]["cant"] = $cant;
				$total_evaluado += $cant;
				
			}
			
			$view_data["compromisos_reportables"] = $array_estados_evaluados;
			$view_data["total_reportables"] = $total_evaluado;
			
			// GRAFICO RESUMEN DE CUMPLIMIENTO
			$array_grafico_reportables = array();
			foreach($array_estados_evaluados as $id_estado => $array_estado){
				$array_grafico_reportables[] = array(
					'nombre_estado' => $array_estado["nombre_estado"], 
					'porcentaje' => $total_evaluado == 0?0:(($array_estado["cant"] * 100) / ($total_evaluado)),
					'color' => $array_estado["color"]
				);
			}
			
			$view_data["grafico_reportables"] = $array_grafico_reportables;
			
			/*$compromisos_reportables = $this->Compromises_reportables_model->get_reportable_compromises($id_compromiso_reportables)->result_array();

			$array_compromisos_reportables = array();
			foreach($compromisos_reportables as $cr){
				$cr["sub_total"] = 1;
				$array_compromisos_reportables[] = $cr;
			}
			
			$result_acr = array();
			$cantidad_total_reportables = 0;
			foreach($array_compromisos_reportables as $acr){
				$repeat = false;
				for($i = 0; $i < count($result_acr); $i++){
					if($result_acr[$i]['id_estado'] == $acr['id_estado']){
						$result_acr[$i]['sub_total'] += $acr['sub_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_acr[] = array('id_estado' => $acr['id_estado'], 'nombre_estado' => $acr['nombre_estado'], 'sub_total' => $acr['sub_total'], 'porcentaje' => 0, 'color' => $acr['color']);
				}	
				$cantidad_total_reportables++;	
			}
			
			$array_result_acr = array();
			foreach($result_acr as $result){
				
				$array_result_acr[] = array(
					'id_estado' => $result['id_estado'],
					'nombre_estado' => $result['nombre_estado'],
					'sub_total' => $result['sub_total'],
					'porcentaje' => ($result['sub_total'] * 100) / $cantidad_total_reportables,
					'color' => $result['color']
				);

			}

			$view_data["compromisos_reportables"] = $array_result_acr;*/

			/* FIN SECCIÓN COMPROMISOS REPORTABLES */
			
			
			
			
		}
		
		if(!$id_compromiso_rca && !$id_compromiso_reportables){
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			$view_data["nombre_proyecto"] = $proyecto->title;
		}
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		
		// PARA NOMBRE DE ARCHIVOS EXPORTABLES
		$view_data['sigla_cliente'] = $cliente->sigla;
		$view_data['sigla_proyecto'] = $proyecto->sigla;
		
        $this->template->rander("compromises_compliance_client/index", $view_data);
    
	}
	
	/* Para AppTable de sección ESTADOS DE CUMPLIMIENTO */
	function list_data($id_compromiso_rca) {
		
		$reportabilidad = $this->input->post("reportabilidad");
		$options = array(
			"reportabilidad" => $reportabilidad
		);
		
		$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso_rca, $options)->result_array(); //traer consulta 
		
		$new_list_data = array();
		/*
		foreach($list_data as $row){
			$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"], 
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);
		}
		*/
		
		foreach($list_data as $row){
			
			//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();

			if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){
				
				$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"], 
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);
				
			}

		}
		
		//var_dump($new_list_data);
		
		$array_columnas = array();
		$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();

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
		$row_data[] = $this->Values_compromises_rca_model->get_one(key($data))->numero_compromiso;
		$reportabilidad = $this->Values_compromises_rca_model->get_one(key($data))->reportabilidad;
		$row_data[] = ($reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
		
		$row_data[] = $this->Values_compromises_rca_model->get_one(key($data))->nombre_compromiso;
		
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
						
						//var_dump($array_evaluado);
						
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
		
		$hay_evidencia = false;
		$hay_observaciones = false;
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => key($data), "deleted" => 0))->result_array();
		
		foreach($evaluaciones as $evaluacion){
			
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();
			
			$evidencias_evaluacion = $this->Compromises_compliance_evidences_model->get_all_where(
				array(
					"id_evaluacion_cumplimiento_compromiso" => $ultima_evaluacion[0]["id"], 
					"tipo_evaluacion" => "rca", 
					"deleted" => 0
				)
			)->result_array();
			if($evidencias_evaluacion){
				$hay_evidencia = true;
			}
			
			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){
				if($evaluacion["observaciones"] || $evaluacion["observaciones"] != ""){
					//var_dump("evaluacion: " . $evaluacion["id"] . " | observaciones: " . $evaluacion["observaciones"]);
					$hay_observaciones = true;
				}
			}

		}
		
		$modal_evidencias = modal_anchor(get_uri("compromises_compliance_client/view_all_evidences/"), "<i class='fa fa-folder-open-o'></i>", array("class" => "edit", "title" => lang('view_evidences'), "data-post-id_valor_compromiso" => key($data)));
		//$row_data[] = ($evidencia) ? $modal_evidencias : "-"; 
		$row_data[] = ($hay_evidencia) ? $modal_evidencias : "-"; 
		//$row_data[] = $modal_evidencias; 		
		
		/*
		//Observaciones con ToolTip
		
		$evaluacion = array();
		$nombre_compromiso = $this->Values_compromises_model->get_one(key($data))->nombre_compromiso;
		
		$html_observaciones = $nombre_compromiso;
		$html_observaciones .= "<div style='text-align: left;'>";
		
		foreach($array_evaluado as $evaluado){

			$evaluacion = $this->Compromises_compliance_evaluation_model->get_all_where(array("id" => $evaluado["id_evaluacion"], "deleted" => 0))->result_array();

			if($evaluacion){
				foreach($evaluacion as $row){
					
					$nombre_evaluado = $this->Evaluated_compromises_model->get_one($row["id_evaluado"])->nombre_evaluado;
					$observaciones = $row["observaciones"];
					if(!$observaciones || $observaciones == ""){
						$observaciones = "Sin observaciones";
					}
					
					$html_observaciones .= '<br>';
					$html_observaciones .= $nombre_evaluado . ": " . $observaciones;
						
				}
			}
			
		}
		
		$html_observaciones .= '</div>';
		$tooltip_observaciones = '<span class="help" data-container="body" data-html="true" data-toggle="tooltip" title="'.$html_observaciones.'"><i class="fa fa-info tooltips"></i></span>';
		$tooltip_observaciones .= '<script type="text/javascript">';
		$tooltip_observaciones .= '$(document).ready(function(){';
		$tooltip_observaciones .= '$(\'[data-toggle="tooltip"]\').tooltip();';
		$tooltip_observaciones .= '})';
		$tooltip_observaciones .= '</script>';
		$row_data[] = $tooltip_observaciones;
		*/
		
		$modal_observaciones = modal_anchor(get_uri("compromises_compliance_client/view_all_observations/"), "<i class='fas fa-info-circle fa-lg'></i>", array("class" => "edit", "title" => lang('view_observations'), "data-post-id_valor_compromiso" => key($data)));
		$row_data[] = ($hay_observaciones) ? $modal_observaciones : "-";
		
        return $row_data;
		
    }
	
	function view_all_evidences(){
		
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_rca_model->get_one($id_valor_compromiso)->nombre_compromiso;
		
		$html_titulo_archivos_evidencia = '<div class="form-group">';
		$html_titulo_archivos_evidencia .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_archivos_evidencia .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_archivos_evidencia .= '</div>';
		$html_final = "";

		$this->array_sort_by_column($evaluaciones, 'id_evaluado');
		
		foreach($evaluaciones as $evaluacion){
			
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();
			
			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){
				
				$evidencias_evaluacion = $this->Compromises_compliance_evidences_model->get_all_where(
					array(
						"id_evaluacion_cumplimiento_compromiso" => $ultima_evaluacion[0]["id"], 
						"tipo_evaluacion" => "rca", 
						"deleted" => 0
					)
				)->result_array();
				
				$nombre_evaluado = $this->Evaluated_rca_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;
					
				$html_archivos_evidencia = "<hr>";
				$html_archivos_evidencia .= '<div class="form-group">';
				$html_archivos_evidencia .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
				$html_archivos_evidencia .= '<div class="col-md-9">';
				
				if($evidencias_evaluacion){

					foreach($evidencias_evaluacion as $evidencia){
						
						$html_archivos_evidencia .= '<div class="col-md-8">';
						$html_archivos_evidencia .= remove_file_prefix($evidencia["archivo"]);
						$html_archivos_evidencia .= '</div>';
						$html_archivos_evidencia .= '<div class="col-md-4">';
						$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
						$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
						$html_archivos_evidencia .= anchor(get_uri("compromises_rca_evaluation/download_file/".$evaluacion["id"]. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
					
					
				} else {
					
					$html_archivos_evidencia .= '<div class="col-md-8">';
					$html_archivos_evidencia .= lang("no_evidence_files");
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '<div class="col-md-4">';
					$html_archivos_evidencia .= '<table id="table_delete_'.$evidencia["id"].'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html_archivos_evidencia .= '<tbody><tr><td class="option text-center">';
					//$html_archivos_evidencia .= anchor(get_uri("compromises_compliance_evaluation/download_file/".$evaluacion["id"]. "/" . $evidencia["id"]), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
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
					$html_archivos_evidencia .= '</div>';
					$html_archivos_evidencia .= '</div>';
				}
				
				$html_final .= $html_archivos_evidencia;
				
			}
			
		}
	
		$view_data["html_titulo_archivos_evidencia"] = $html_titulo_archivos_evidencia;
		$view_data["html_archivos_evidencia"] = $html_final;
		
		$this->load->view('compromises_compliance_client/view_all_evidences', $view_data);
		
	}
	
	function view_all_observations(){
		
		$id_valor_compromiso = $this->input->post("id_valor_compromiso");
		$evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_all_where(array("id_valor_compromiso" => $id_valor_compromiso, "deleted" => 0))->result_array();
		$nombre_compromiso = $this->Values_compromises_rca_model->get_one($id_valor_compromiso)->nombre_compromiso;
		
		$html_titulo_observaciones = '<div class="form-group">';
		$html_titulo_observaciones .= '<label for="nombre_compromiso" class="col-md-3">'.lang("compromise_name").'</label>';
		$html_titulo_observaciones .= '<div class="col-md-9">'.$nombre_compromiso.'</div>';
		$html_titulo_observaciones .= '</div>';
		$html_final = "";
		
		$this->array_sort_by_column($evaluaciones, 'id_evaluado');

		foreach($evaluaciones as $evaluacion){
			
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();
			
			if($evaluacion["id"] == $ultima_evaluacion[0]["id"]){
				
				$nombre_evaluado = $this->Evaluated_rca_compromises_model->get_one($evaluacion["id_evaluado"])->nombre_evaluado;
			
				$html_observaciones = "<hr>";
				$html_observaciones .= '<div class="form-group">';
				$html_observaciones .= '<label for="archivos" class="col-md-3">'.$nombre_evaluado.'</label>';
				$html_observaciones .= '<div class="col-md-9">';
				$html_observaciones .= ((!$evaluacion["observaciones"]) || $evaluacion["observaciones"] == "") ? "-" : $evaluacion["observaciones"]; 
				$html_observaciones .= '</div>';
				$html_observaciones .= '</div>';
				$html_final .= $html_observaciones;
				
			}

		}
		
		$view_data["html_titulo_observaciones"] = $html_titulo_observaciones;
		$view_data["html_observaciones"] = $html_final;
		
		$this->load->view('compromises_compliance_client/view_all_observations', $view_data);
		
	}
	
	function get_quantity_of_status_evaluated($id_estado, $id_evaluado){		
		
		$cantidad = 0;
		$evaluaciones = $this->Compromises_rca_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->result_array();

		foreach($evaluaciones as $evaluacion){
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $evaluacion["id_evaluado"], "id_valor_compromiso" => $evaluacion["id_valor_compromiso"]))->result_array();
			if($ultima_evaluacion[0]["id"] == $evaluacion["id"]){
				$cantidad++;
			}	
		}
		
		return $cantidad;
		//$cantidad = $this->Compromises_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->row();
		//return $cantidad->cantidad;		
	}
	
	function get_percentage_of_status_evaluated($cantidad_compromisos, $id_estado, $id_evaluado){		
		
		$compromisos_por_evaluado = $this->Compromises_rca_model->get_total_applicable_compromises_by_evaluated($id_evaluado)->result_array();
		$total_compromisos_por_evaluado = 0;
		
		foreach($compromisos_por_evaluado as $cpe){
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $cpe["id_evaluado"], "id_valor_compromiso" => $cpe["id_valor_compromiso"]))->result_array();
			if($ultima_evaluacion[0]["id"] == $cpe["id_evaluacion"]){
				$total_compromisos_por_evaluado++;
			}
		}
		
		if($cantidad_compromisos == 0){
			$porcentaje = 0;
		} else {
			$porcentaje = ($cantidad_compromisos * 100) / $total_compromisos_por_evaluado; 
		}

		return $porcentaje;
		//$porcentaje = $this->Compromises_model->get_percentage_of_status_evaluated($id_estado, $id_evaluado)->row();
		//return $porcentaje->porcentaje;		
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
	
	function get_excel_compliance_status(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$matriz_compromisos = $this->Compromises_rca_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		
		$id_compromiso = $matriz_compromisos->id;
		$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso)->result_array(); //traer consulta
		
		$new_list_data = array();
		foreach($list_data as $row){
			
			//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
			$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();

			if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){
				
				$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
															"id_evaluacion" => $row["id_evaluacion"],
															"id_evaluado" => $row["id_evaluado"], 
															"nombre_evaluado" => $row["nombre_evaluado"],
															"id_estado" => $row["id_estado"],
															"nombre_estado" => $row["nombre_estado"],
															"fecha_evaluacion" => $row["fecha_evaluacion"]);
				
			}

		}

		$array_columnas = array();
		$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso)->result_array();

		foreach($traer_columnas as $columnas){		
			$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
		}
		
		$columnas_campos_matriz = $this->Compromises_rca_model->get_fields_of_compromise($id_compromiso)->result();
	
        $result = array();
        foreach ($new_list_data as $id_valor_compromiso => $data) {
            $result[] = $this->_make_row_excel_compliance_status(array($id_valor_compromiso => $data), $array_columnas, $columnas_campos_matriz, $id_proyecto);
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
		$nombre_columnas[] = array("nombre_columna" => lang("n_compromise"), "id_tipo_campo" => "n_compromise");
		$nombre_columnas[] = array("nombre_columna" => lang("name"), "id_tipo_campo" => "name");
		$nombre_columnas[] = array("nombre_columna" => lang("phases"), "id_tipo_campo" => "phases");
		$nombre_columnas[] = array("nombre_columna" => lang("reportability"), "id_tipo_campo" => "reportability");
		
		foreach($columnas_campos_matriz as $columna_matriz){
			if(($columna_matriz->id_tipo_campo == 11)||($columna_matriz->id_tipo_campo == 12)){
				continue;
			}
			$nombre_columnas[] = array("nombre_columna" => $columna_matriz->nombre_campo, "id_tipo_campo" => $columna_matriz->id_tipo_campo);
		}
		
		foreach($array_columnas as $nombre_evaluado){
			$nombre_columnas[] = array("nombre_columna" => $nombre_evaluado, "id_tipo_campo" => "evaluated_name");
		}
		
		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("compliance_status"))
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
						
					} elseif($columna["id_tipo_campo"] == "n_compromise"){ // NÚMERO COMPROMISO
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "name"){ // NOMBRE
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "phases"){ // FASES
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "reportability"){ // REPORTABILIDAD
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "evaluated_name"){ // EVALUADO
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "created_date" || $columna["id_tipo_campo"] == "modified_date"){
					
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
				
				//if($columna["id_tipo_campo"] != "unity"){
					$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				//}
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
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/
		
		$nombre_hoja = strlen(lang("compromises_compliance_excel")) > 31 ? substr(lang("compromises_compliance_excel"), 0, 28).'...' : lang("compromises_compliance_excel");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("compromises_compliance_excel")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
	
	}
	
	private function _make_row_excel_compliance_status($data, $array_columnas, $columnas_campos_matriz, $id_proyecto) {
		
		$valor_compromiso = $this->Values_compromises_rca_model->get_one(key($data));
		
		$row_data = array();
		$row_data[] = $valor_compromiso->numero_compromiso;
		$row_data[] = $valor_compromiso->nombre_compromiso;
		
		/*$fases_decoded = json_decode($valor_compromiso->fases);
		$html_fases = "";
		foreach($fases_decoded as $fase){
			$html_fases .= $fase.", ";
		}
		$row_data[] = rtrim($html_fases, ", ");*/
		
		$fases_decoded = json_decode($valor_compromiso->fases);
		$html_fases = "";
		$array_fases = array();
		foreach($fases_decoded as $id_fase){
			$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
			$nombre_fase = lang($nombre_lang);
			$array_fases[] = $nombre_fase;
		}
		$row_data[] = implode(', ', $array_fases);
		
		$row_data[] = ($valor_compromiso->reportabilidad == 1) ? lang("yes") : lang("no");
		
		if($valor_compromiso->datos_campos){
			
			$arreglo_fila = json_decode($valor_compromiso->datos_campos, true);
			$cont = 0;
			
			foreach($columnas_campos_matriz as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if($columna->id_tipo_campo == 4){//si es fecha.
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
		
		// Evaluaciones
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
						$row_data[] = $estado->nombre_estado;
					}
	
				}

			} else {
				
				foreach($array_evaluado as $evaluado){
					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$row_data[] = $estado->nombre_estado;
				}
				
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
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $info_proyecto->id, 'deleted' => 0))->id;
		$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		
		$view_data["info_cliente"] = $info_cliente;
		$view_data["info_proyecto"] = $info_proyecto;
		$view_data["Compromises_compliance_client_controller"] = $this;
		
		$imagenes_graficos = $this->input->post("imagenes_graficos");
		
		$view_data["grafico_cumplimientos_totales"] = $imagenes_graficos["image_cumplimientos_totales"];
		$view_data["graficos_resumen_evaluados"] = $imagenes_graficos["graficos_resumen_evaluados"]; // Array con los gráficos de los evaluados
		$view_data["grafico_cumplimientos_reportables"] = $imagenes_graficos["image_cumplimientos_reportables"];
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		if($id_compromiso_rca){
			
			$view_data["id_compromiso_rca"] = $id_compromiso_rca;

			/* SECCIÓN RESUMEN POR EVALUADO */
			
			// COMPROMISOS AMBIENTALES - RCA

			// EVALUADOS
			$evaluados = $this->Evaluated_rca_compromises_model->get_all_where(
				array(
					"id_compromiso" => $id_compromiso_rca, 
					"deleted" => 0
				)
			)->result();
			
			// ESTADOS RCA
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(
				array(
					"id_cliente" => $id_cliente, 
					"tipo_evaluacion" => "rca",
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluations_of_project(
				$id_proyecto, 
				$end_date
			)->result();
			
			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$array_evaluados_estados = array();
			$array_total_por_evaluado = array();
			$array_total_por_estado = array();
			$array_compromisos_evaluaciones_no_cumple = array();
			$total = 0;
			
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				
				if($estado->categoria == "No Aplica"){
					continue;
				}
				$array_estados_evaluados[$estado->id] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"evaluados" => array(),
					"cantidad_categoria" => 0,
				);
				
				$cant_estado = 0;
				foreach($evaluados as $evaluado) {
					
					$id_evaluado = $evaluado->id;
					$cant = 0;
					
					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());
					
					foreach($ultimas_evaluaciones as $ultima_evaluacion) {
						if(
							$ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado && 
							$ultima_evaluacion->id_evaluado == $id_evaluado
						){
							$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
							$array_evaluados_estados[$id_evaluado][$id_estado][] = 1;
							$cant++;
							$cant_estado++;
							
							if($estado->categoria == "No Cumple"){
								$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
								$ultima_evaluacion->criticidad = $criticidad_info->nombre;
								$array_compromisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
							}
						}
					}
					
					$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
					$array_total_por_evaluado[$id_evaluado][] = $cant;
					$array_total_por_estado[$id_estado][] = $cant;
				}
				
				$array_estados_evaluados[$id_estado]["cantidad_categoria"] = $cant_estado;
				$total += $cant_estado;
			}
			
			$view_data["evaluados_rca"] = $evaluados;
			$view_data["total_compromisos_aplicables_rca"] = $total;
			$view_data["total_cantidades_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["total_cantidades_evaluados_estados_rca"] = $array_evaluados_estados;
			$view_data["array_total_por_evaluado_rca"] = $array_total_por_evaluado;

			/* FIN SECCIÓN RESUMEN POR EVALUADO */


			/* SECCIÓN ESTADOS DE CUMPLIMIENTO */
			$list_data = $this->Compromises_rca_model->get_data_of_compliance_status($id_compromiso_rca)->result_array();
			$new_list_data = array();
			
			foreach($list_data as $row){
				
				//consultar por la combinacion de id_valor_compromiso e id_evaluado del row más reciente por fecha_evaluacion y guardar esa en el new list data
				$ultima_evaluacion = $this->Compromises_compliance_evaluation_rca_model->get_last_evaluation(array("id_evaluado" => $row["id_evaluado"], "id_valor_compromiso" => $row["id_valor_compromiso"]))->result_array();
	
				if($row["id_evaluacion"] == $ultima_evaluacion[0]["id"]){
					
					$new_list_data[$row["id_valor_compromiso"]][$row["id_evaluado"]] = array(
																"id_evaluacion" => $row["id_evaluacion"],
																"id_evaluado" => $row["id_evaluado"], 
																"nombre_evaluado" => $row["nombre_evaluado"],
																"id_estado" => $row["id_estado"],
																"nombre_estado" => $row["nombre_estado"],
																"fecha_evaluacion" => $row["fecha_evaluacion"]);
					
				}
				
				$array_columnas = array();
				$traer_columnas = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();
		
				foreach($traer_columnas as $columnas){		
					$array_columnas[$columnas["id"]] = $columnas["nombre_evaluado"];
				}
				
				$result = array();
				foreach ($new_list_data as $id_valor_compromiso => $data) {
					$result[] = $this->_make_row_compliance_status_pdf(array($id_valor_compromiso => $data), $array_columnas);
				}
	
			}
			
			$columnas_evaluados_estados_cumplimiento = $this->Compromises_rca_model->get_fields_of_compliance_status($id_compromiso_rca)->result_array();
			
			$view_data["columnas_evaluados_estados_cumplimiento"] = $columnas_evaluados_estados_cumplimiento;
			$view_data["result"] = $result;
			//var_dump($result);
			//exit();
			/* FIN SECCIÓN ESTADOS DE CUMPLIMIENTO */
			
		}
		
		if($id_compromiso_reportables){
			
			$view_data["id_compromiso_reportables"] = $id_compromiso_reportables;
			
			/* SECCIÓN COMPROMISOS REPORTABLES */


			/*$compromisos_reportables =  $this->Compromises_reportables_model->get_reportable_compromises($id_compromiso_reportables)->result_array();
	
			$array_compromisos_reportables = array();
			foreach($compromisos_reportables as $cr){
				//$ultima_evaluacion = $this->Compromises_compliance_evaluation_model->get_last_evaluation(array("id_evaluado" => $cr["id_evaluado"], "id_valor_compromiso" => $cr["id_valor_compromiso"]))->result_array();
				//if($ultima_evaluacion[0]["id"] == $cr["id_evaluacion"]){
					$cr["sub_total"] = 1;
					$array_compromisos_reportables[] = $cr;
				//}	
			}
			
			$result_acr = array();
			$cantidad_total_reportables = 0;
			foreach($array_compromisos_reportables as $acr){
				$repeat = false;
				for($i = 0; $i < count($result_acr); $i++){
					if($result_acr[$i]['id_estado'] == $acr['id_estado']){
						$result_acr[$i]['sub_total'] += $acr['sub_total'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_acr[] = array('id_estado' => $acr['id_estado'], 'nombre_estado' => $acr['nombre_estado'], 'sub_total' => $acr['sub_total'], 'porcentaje' => 0, 'color' => $acr['color']);
				}	
				$cantidad_total_reportables++;	
			}
			
			$array_result_acr = array();
			foreach($result_acr as $result){
				
				$array_result_acr[] = array(
					'id_estado' => $result['id_estado'],
					'nombre_estado' => $result['nombre_estado'],
					'sub_total' => $result['sub_total'],
					'porcentaje' => ($result['sub_total'] * 100) / $cantidad_total_reportables,
					'color' => $result['color']
				);
	
			}
	
			$view_data["compromisos_reportables"] = $array_result_acr;*/


			// ESTADOS REPORTABLES
			$estados_cliente = $this->Compromises_compliance_status_model->get_details(
				array(
					"id_cliente" => $id_cliente, 
					"tipo_evaluacion" => "reportable",
				)
			)->result();
			
			// ULTIMAS EVALUACIONES
			$ultimas_evaluaciones = $this->Compromises_compliance_evaluation_reportables_model->get_last_evaluations_of_project(
				$id_proyecto, 
				$end_date
			)->result();
			
			// PROCESAR TABLA
			$array_estados_evaluados = array();
			$total_evaluado = 0;
			$array_compromisos_reportables_evaluaciones_no_cumple = array();
			$max_dates = array();
			foreach($estados_cliente as $estado) {
				
				$id_estado = $estado->id;
				if($estado->categoria == "No Aplica"){
					continue;
				}
				
				$array_estados_evaluados[$id_estado] = array(
					"nombre_estado" => $estado->nombre_estado,
					"categoria" => $estado->categoria,
					"color" => $estado->color,
					"planificaciones_evaluaciones" => array(),
					"cant" => 0,
				);
				
				$cant = 0;
				foreach($ultimas_evaluaciones as $ultima_evaluacion) {
					if($ultima_evaluacion->id_estados_cumplimiento_compromiso == $id_estado){
						
						$array_estados_evaluados[$id_estado]["planificaciones_evaluaciones"] = $ultima_evaluacion;
						$cant++;
						
						if($estado->categoria == "No Cumple"){
							$criticidad_info = $this->Critical_levels_model->get_one($ultima_evaluacion->id_criticidad);
							$ultima_evaluacion->criticidad = $criticidad_info->nombre;
							$id_valor_compromiso = $ultima_evaluacion->id_valor_compromiso;

							if(is_null($max_dates[$id_valor_compromiso])){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}elseif(strtotime($max_dates[$id_valor_compromiso]) < strtotime($ultima_evaluacion->planificacion)){
								$max_dates[$id_valor_compromiso] = $ultima_evaluacion->planificacion;
								$array_compromisos_reportables_evaluaciones_no_cumple[$id_valor_compromiso] = $ultima_evaluacion;
							}
							
						}
					}
				}
					
				$array_estados_evaluados[$id_estado]["cant"] = $cant;
				$total_evaluado += $cant;
				
			}
			
			$view_data["compromisos_reportables"] = $array_estados_evaluados;
			$view_data["total_reportables"] = $total_evaluado;
			
			/* FIN SECCIÓN COMPROMISOS REPORTABLES */
		
		}
		
		if(!$id_compromiso_rca && !$id_compromiso_reportables){
			$view_data["nombre_proyecto"] = $info_proyecto->title;
		}

		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d'));
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
		$html = $this->load->view('compromises_compliance_client/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("compromises")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;
		
	}
	
	private function _make_row_compliance_status_pdf($data, $array_columnas){
		//var_dump(key($data));
		$row_data = array();
		$row_data["numero_compromiso"] = $this->Values_compromises_rca_model->get_one(key($data))->numero_compromiso;
		$reportabilidad = $this->Values_compromises_rca_model->get_one(key($data))->reportabilidad;
		$row_data["reportabilidad"] = ($reportabilidad == 1) ? "&#xf00c;" : "&#xf00d;";
		$row_data["nombre_compromiso"] = $this->Values_compromises_rca_model->get_one(key($data))->nombre_compromiso;
		
		
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
						$row_data[$id_evaluado] = "-";
					} else {

						$id_estado_cumplimiento_compromiso = $array_evaluado[$id_evaluado]["id_estado"];
						$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
						$nombre_estado = $estado->nombre_estado;
						$color_estado = $estado->color;

						$html_estado = '<span style="color:'.$color_estado.';">';
						$html_estado .= '&#xf111;'; // círculo (fontawesome)
						$html_estado .= '</span>';
						$html_estado .= "nombre_estado:".$nombre_estado;
					
						$row_data[$id_evaluado] = $html_estado;
					}
	
				}

			} else {
				
				foreach($array_evaluado as $evaluado){
					$id_estado_cumplimiento_compromiso = $evaluado["id_estado"];
					$estado = $this->Compromises_compliance_status_model->get_one($id_estado_cumplimiento_compromiso);
					$nombre_estado = $estado->nombre_estado;
					$color_estado = $estado->color;
					
					$html_estado = '<span style="color:'.$color_estado.';">';
					$html_estado .= '&#xf111;'; // círculo (fontawesome)
					$html_estado .= '</span>';
					$html_estado .= "nombre_estado:".$nombre_estado;

					$row_data[$evaluado["id_evaluado"]] = $html_estado;

				}
				
			}
			
		}
		
		
		
		
        return $row_data;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}
	
}

