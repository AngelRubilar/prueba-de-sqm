<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
		
        //check permission to access this module
        $this->init_permission_checker("client");
		$this->load->helper('pdf_helper');
		
		$this->id_modulo_cliente = 5;
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();
		
		$id_proyecto = $this->session->project_context;	
		
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		$proyect_info = $this->Projects_model->get_one($this->session->project_context);
		$client_info = $this->Clients_model->get_one($proyect_info->client_id);
		$technology = $this->Subindustries_model->get_one($proyect_info->id_tecnologia);
		$view_data["project_info"] = $proyect_info;
		$view_data['client_info'] = $client_info;
		$view_data['technology'] = $technology;
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		//$pdf = $this->pdf();

        $this->template->rander("reports/index",$view_data);
    }
	
	
	function generate(){
		
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->input->post('id_cliente');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		$technology = $this->Subindustries_model->get_one($project_info->id_tecnologia);
		//$data_by_date = $this->data_by_date($id_proyecto,$id_cliente,$start_date,$end_date);
		
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		$id_unidad_energia = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		
		$unidad_volumen = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
		$unidad_volumen_nombre_real = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
		$unidad_masa = $this->Unity_model->get_one($id_unidad_masa)->nombre;
		$unidad_masa_nombre_real = $this->Unity_model->get_one($id_unidad_masa)->nombre_real;
		$unidad_energia = $this->Unity_model->get_one($id_unidad_energia)->nombre;
		$unidad_energia_nombre_real = $this->Unity_model->get_one($id_unidad_energia)->nombre_real;
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		// Datos del usuario
		$id_usuario = $this->session->user_id;
		$usuario_info = $this->Users_model->get_one($id_usuario);

		// Fila de configuración de 'reportes' del proyecto (reports_configuration_settings)
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'deleted' => 0
		);
		$configuracion_reporte = $this->Reports_configuration_model->get_one_where($filtro);
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		
		$html = '';
		$html .='<div id="contenido">';
		
		if($report_config->project_data){
			
			// CONSIGO TODA LA INFORMACION PRIMERO
			$color_sitio = $client_info->color_sitio;
			$nombre_proyecto = $project_info->title;
			$pais = $this->Countries_model->get_one($project_info->id_pais);
			$ubicacion_proyecto = (($project_info->state)?$project_info->state.', ':'').(($project_info->city)?$project_info->city.', ':'').(($pais->nombre)?$pais->nombre:'');
			$rut = (($project_info->client_label_rut)?$project_info->client_label_rut:'-');
			$environmental_authorization = ($project_info->environmental_authorization) ? $project_info->environmental_authorization : "-";

			$proyecto_fase = $this->Project_rel_phases_model->get_one_where(
				array(
					"id_proyecto" => $id_proyecto, 
					"deleted" => 0
				)
			);
			
			if($proyecto_fase->id){
				$proyecto_fase = $this->Phases_model->get_one($proyecto_fase->id_fase);
				$etapa_proyecto = lang($proyecto_fase->nombre_lang);
			}else{
				$etapa_proyecto = '-';
			}
			
			$n_informe = $client_info->rut;
			$fecha_reporte = date('Y-m-d');
			$inicio_consulta = $start_date;
			$termino_consulta = $end_date;
			
			// Reviso si puede visualizar ANTECEDENTES DEL PROYECTO
			$puede_ver_antecedentes_proyecto = $configuracion_reporte->project_data;
			
			if ($puede_ver_antecedentes_proyecto) {
				$html .='<!--ANTECEDENTES DEL PROYECTO-->';	
				
				$html .=	'<div class="panel panel-default">';
		
				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("project_background").'</h1>';
				$html .=	'</div>';
	
				$html .='<div class="panel-body" style="padding-bottom:0px;">';
				$html .=	'<div class="form-group">';
				$html .=		'<div class="col-md-6" style="padding-left:0px">';	
				$html .=			'<table class="table table-bordered">';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("client").'</th>';
				$html .=					'<td>'.$client_info->company_name.'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("project").'</th>';
				$html .=					'<td>'.$nombre_proyecto.'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("location").'</th>';
				$html .=					'<td>'.$ubicacion_proyecto.'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("rut").'</th>';
				$html .=					'<td>'.$rut.'</td>';
				$html .=				'</tr>';
				$html .=			'</table>';
				$html .=		'</div>';
				
				$html .=		'<div class="col-md-6" style="padding-right:0px;">';
				$html .=			'<table class="table table-bordered">';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("environmental_authorization").'</th>';
				$html .=					'<td id="environmental_authorization" data-value="'.$environmental_authorization.'" >'.$environmental_authorization.'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("report_project_stage").'</th>';
				$html .=					'<td>'.$etapa_proyecto.'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("report_start_date").'</th>';
				$html .=					'<td>'.get_date_format($inicio_consulta, $id_proyecto).'</td>';
				$html .=				'</tr>';
				$html .=				'<tr>';
				$html .=					'<th style="background-color:'.$color_sitio.';">'.lang("report_end_date").'</th>';
				$html .=					'<td>'.get_date_format($termino_consulta, $id_proyecto).'</td>';
				$html .=				'</tr>';
				$html .=			'</table>';
				$html .=		'</div>';
				$html .=	'</div>';
				$html .='</div>';
				
				$html .='</div>';
				$html .='<!-- FIN ANTECEDENTES DEL PROYECTO -->';
			}
		}
		
		// COMPROMISOS AMBIENTALES - RCA
		// Reviso si puede tiene acceso a compromisos ambientales rca, por módulo y perfil
		$id_submodulo_dashboard_compromisos = 3;
		$id_modulo_compromisos = 6;
		
		$tiene_configuracion_disponible = $configuracion_reporte->rca_compromises;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_compromisos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_compromisos, $id_submodulo_dashboard_compromisos, "ver");
		$puede_ver_compromisos_rca = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_compromisos_rca) {

			$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
			if($id_compromiso_rca){
				
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
				$array_total_por_evaluado = array();
				$array_total_por_estado = array();
				$array_compromisos_evaluaciones_no_cumple = array();
				foreach($estados_cliente as $estado) {
					
					$id_estado = $estado->id;
					
					if($estado->categoria == "No Aplica"){
						continue;
					}
					$array_estados_evaluados[$estado->id] = array(
						"nombre_estado" => $estado->nombre_estado,
						"categoria" => $estado->categoria,
						"color" => $estado->color,
						"evaluados" => array()
					);
					
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
								$cant++;
								
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
				}
				
				//var_dump($array_compromisos_evaluaciones_no_cumple);
				
				$html .= '<div class="panel panel-default">';

				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("environmental_commitments").' - '.$environmental_authorization.'</h1>';
				$html .=	'</div>';

				$html .= '<div class="panel-body">';

				$html .= '<div class="table-responsive">';
				$html .= '<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

				$html .= '<table id="tabla_resumen_por_evaluado" class="table table-striped">';
				$html .= '<thead>';

				$html .= '<tr>';
				$html .= '<th rowspan="2" class="text-center" style="vertical-align:middle;">'.lang("compliance_status").'</th>';
				foreach($evaluados as $evaluado) {
					$html .= '<th colspan="2" class="text-center">'.$evaluado->nombre_evaluado.'</th>';
				}
				$html .= '</tr>';

				$html .= '<tr>';
					foreach($evaluados as $evaluado) {
						$html .= '<th class="text-center">N°</th>';
						$html .= '<th class="text-center">%</th>';
					}
				$html .= '</tr>';

				$html .= '</thead>';
				$html .= '<tbody>';

				$html .= '<tr>';
				$html .= '<th class="text-left">'.lang("total_applicable_compromises").'</th>';
					foreach($evaluados as $evaluado) {
						$html .= '<td class=" text-right">'.to_number_project_format(array_sum($array_total_por_evaluado[$evaluado->id]), $id_proyecto).'</td>';
						$html .= '<td class=" text-right">'.to_number_project_format(100, $id_proyecto).'%</td>';
					}
				$html .= '</tr>';

				foreach($array_estados_evaluados as $estado_evaluado) {

					$html .= '<tr>';
					$html .= '<td class="text-left">'.$estado_evaluado["nombre_estado"].'</td>';
					foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) {
						
						$total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
						if($total_evaluado == 0){
							$porcentaje = 0;
						} else {
							$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
						}
						$html .= '<td class="text-right">'.to_number_project_format($evaluado["cant"], $id_proyecto).'</td>';
						$html .= '<td class="text-right">'.to_number_project_format($porcentaje, $id_proyecto).'%</td>';
					}
					$html .= '</tr>';
				}

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				
				// GRAFICO RESUMEN DE CUMPLIMIENTO
				$total_compromisos_aplicables = 0;
				$total_cantidades_estados_evaluados = array();
				foreach($array_total_por_estado as $id_estado => $array_cant){
					$total_compromisos_aplicables += array_sum($array_cant);
					$status_info = $this->Compromises_compliance_status_model->get_one($id_estado);
					$total_cantidades_estados_evaluados[] = array(
						'nombre_estado' => $status_info->nombre_estado, 
						'cantidad_categoria' => array_sum($array_cant), 
						'color' => $status_info->color
					);
				}
				
				// LISTA DE COMPROMISOS CON EVALUACIONES NO CUMPLE
				$html .='<div class="panel panel-default">';

					$html .='<div class="panel-body">';

						$html .='<div class="col-md-6">';
							$html .='<div class="grafico_torta" id="grafico_cumplimientos_totales" style="height: 240px;"></div>';
						$html .='</div>';

						$html .='<div class="col-md-6">';
							$html .='<div class="table-responsive">';
							$html .='<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

							$html .='<table id="tabla_evaluaciones_rca_no_cumple" class="table table-striped">';
							$html .='<thead>';
								$html .='<tr>';
								$html .='<th class="text-center">'.lang("compromise").'</th>';
								$html .='<th class="text-center">'.lang("critical_level").'</th>';
								$html .='<th class="text-center">'.lang("responsible").'</th>';
								$html .='<th class="text-center">'.lang("closing_term").'</th>';
								$html .='</tr>';
							$html .='</thead>';
							$html .='<tbody>';

							foreach($array_compromisos_evaluaciones_no_cumple as $row) {
								$html .='<tr>';
								$html .='<td class="text-left">'.$row->nombre_compromiso.'</td>';
								$html .='<td class="text-left">'.$row->criticidad.'</td>';
								$html .='<td class="text-left">'.$row->responsable_reporte.'</td>';
								$html .='<td class="text-left">'.get_date_format($row->plazo_cierre, $id_proyecto).'</td>';
								$html .='</tr>';
							}

							$html .='</tbody>';
							$html .='</table>';
							$html .='</div>';
						$html .='</div>';
					$html .='</div>';

				$html .='</div>';
				$html .='</div>';
				
			}
		}
		
		// Reviso si puede visualizar la sección de compromisos reportables
		$id_submodulo_dashboard_compromisos = 3;
		$id_modulo_compromisos = 6;
		
		$tiene_configuracion_disponible = $configuracion_reporte->reportable_compromises;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_compromisos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_compromisos, $id_submodulo_dashboard_compromisos, "ver");
		$puede_ver_compromisos_reportables = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_compromisos_reportables) {
			$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
			if($id_compromiso_reportables){

				// COMPROMISOS AMBIENTALES - REPORTABLES

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
				
				$html .= '<div class="panel panel-default">';

				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("environmental_reportable_commitments").' - '.$environmental_authorization.'</h1>';
				$html .=	'</div>';

				$html .= '<div class="panel-body">';

				$html .= '<div class="table-responsive">';
				$html .= '<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

				$html .= '<table id="tabla_resumen_por_estado" class="table table-striped">';
				$html .= '<thead>';

				$html .= '<tr>';
				$html .= '<th rowspan="2" class="text-center" style="vertical-align:middle;">'.lang("general_compliance_status").'</th>';
				$html .= '<th colspan="2" class="text-center">'.lang("sub_total").'</th>';
				$html .= '</tr>';

				$html .= '<tr>';
				$html .= '<th class="text-center">N°</th>';
				$html .= '<th class="text-center">%</th>';
				$html .= '</tr>';

				$html .= '</thead>';
				$html .= '<tbody>';

				foreach($array_estados_evaluados as $estado_evaluado){
					
					if($total_evaluado == 0){
						$porcentaje = 0;
					} else {
						$porcentaje = ($estado_evaluado["cant"] * 100) / ($total_evaluado);
					}

					$html .= '<tr>';
					$html .= '<td class="text-left">'.$estado_evaluado["nombre_estado"].'</td>';
					$html .= '<td class="text-right">'.to_number_project_format($estado_evaluado["cant"], $id_proyecto).'</td>';
					$html .= '<td class="text-right">'.to_number_project_format($porcentaje, $id_proyecto).'%</td>';
					$html .= '</tr>';
				}

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				
				
				// GRAFICO RESUMEN DE CUMPLIMIENTO
				$array_grafico_reportables = array();
				foreach($array_estados_evaluados as $id_estado => $array_estado){
					$array_grafico_reportables[] = array(
						'nombre_estado' => $array_estado["nombre_estado"], 
						'porcentaje' => $total_evaluado == 0?0:(($array_estado["cant"] * 100) / ($total_evaluado)),
						'color' => $array_estado["color"]
					);
				}
				
				// LISTA DE COMPROMISOS CON EVALUACIONES NO CUMPLE
				$html .='<div class="panel panel-default">';

					$html .='<div class="panel-body">';

						$html .='<div class="col-md-6">';
							$html .='<div class="grafico_torta" id="grafico_cumplimientos_reportables" style="height: 240px;"></div>';
						$html .='</div>';

						$html .='<div class="col-md-6">';
							$html .='<div class="table-responsive">';
							$html .='<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

							$html .='<table id="tabla_evaluaciones_reportables_no_cumple" class="table table-striped">';
							$html .='<thead>';
								$html .='<tr>';
								$html .='<th class="text-center">'.lang("compromise").'</th>';
								$html .='<th class="text-center">'.lang("critical_level").'</th>';
								$html .='<th class="text-center">'.lang("responsible").'</th>';
								$html .='<th class="text-center">'.lang("closing_term").'</th>';
								$html .='</tr>';
							$html .='</thead>';
							$html .='<tbody>';

							foreach($array_compromisos_reportables_evaluaciones_no_cumple as $row) {
								$html .='<tr>';
								$html .='<td class="text-left">'.$row->nombre_compromiso.'</td>';
								$html .='<td class="text-left">'.$row->criticidad.'</td>';
								$html .='<td class="text-left">'.$row->responsable_reporte.'</td>';
								$html .='<td class="text-left">'.get_date_format($row->plazo_cierre, $id_proyecto).'</td>';
								$html .='</tr>';
							}
							
							$html .='</tbody>';
							$html .='</table>';
							$html .='</div>';
						$html .='</div>';
					$html .='</div>';

				$html .='</div>';
				$html .='</div>';

			}
		}
		
		// CONSUMOS
		// Reviso si puede visualizar la sección de CONSUMOS
		$id_submodulo_consumos = 0;
		$id_modulo_consumos = 2;
		
		$tiene_configuracion_disponible = $configuracion_reporte->consumptions;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_consumos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_consumos, $id_submodulo_consumos, "ver");
		$puede_ver_consumos = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_consumos) {
			if($report_config->consumptions){

				$html .='<!-- CONSUMOS -->';
				$html .='<div class="panel panel-default">';

				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("consumptions").'</h1>';
				$html .=	'</div>';

				$html .='<div class="panel-body">';
				$html .='<table class="table table-bordered" id="tabla_consumo">';
				$html .=	'<tr>';
				$html .=		'<th colspan="4" class="label-info" style="text-align:center; background-color:'.$client_info->color_sitio.';">'.lang("consumptions").'</th>';
				$html .=	'</tr>';
				$html .=	'<tr>';
				$html .=		'<th class="text-center">'.lang("categories").'</th>';
				$html .=		'<th class="text-center">'.lang("Reported_in_period").'</th>';
				$html .=		'<th class="text-center">'.lang("accumulated").'</th>';
				$html .=		'<th class="text-center">'.lang("declared").'</th>';
				$html .=	'</tr>';

				$tabla_consumo_volumen = $this->get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_volumen_reportados = $tabla_consumo_volumen["reportados"];
				$tabla_consumo_volumen_acumulados = $tabla_consumo_volumen["acumulados"];

				$tabla_consumo_masa = $this->get_datos_tabla_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_masa_reportados = $tabla_consumo_masa["reportados"];
				$tabla_consumo_masa_acumulados = $tabla_consumo_masa["acumulados"];

				$tabla_consumo_energia = $this->get_datos_tabla_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_energia_reportados = $tabla_consumo_energia["reportados"];
				$tabla_consumo_energia_acumulados = $tabla_consumo_energia["acumulados"];

				foreach ($tabla_consumo_volumen_reportados as $id_categoria => $arreglo_valores){
					$arreglo_valores_acumulados = $tabla_consumo_volumen_acumulados[$id_categoria];

					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}

					// ACA VALIDAR SI CLIENTE/PROYECTO/ID_CATEGORIA ESTA HABILITADO PARA MOSTRARSE EN TABLA Y GRAFICO
					// UNA CATEGORIA ES UNICA A NIVEL DE FLUJO/TIPO-UNIDAD/UNIDAD
					// SI UNA CATEGORIA SE REPITE EN OTRO FORMULARIO (DEL MISMO FLUJO), SUMARLO SI TIENE LA MISMA UNIDAD
					// SI TIENE EL MISMO TIPO DE UNIDAD Y OTRA UNIDAD, CONVERTIRLA Y SUMARLA
					// NO PUEDE EXISTIR LA MISMA CATEGORIA EN UN FORMULARIO CON FLUJO CONSUMO Y RESIDUO
					// NO PUEDEN EXISTIR 1 CAMPO TIPO UNIDAD VOLUMEN Y OTRO MASA EN EL MISMO FORMULARIO

					// en el mismo form: no debiera poder tener 2 campos detipo unidad masa y volumen, son excuyentes
					// EXISTE UNA EXCEPCION, ENEL, LISTA 5

					/* $array_grafico_residuos_volumen_categories[] = $nombre_categoria;
					$array_grafico_residuos_volumen_data[] = array_sum($arreglo_valores); */

					// CONSULTA CONFIGURACIÓN DE ALERTAS 					
					// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
					$options = array(
						'id_client' => $id_cliente, 
						'id_project' => $id_proyecto,
						'id_client_module' => $id_modulo_consumos,
						'alert_config' => array(
							'id_categoria' => $id_categoria,
							'id_tipo_unidad' => 2,
						)
					);
					
					$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
					$alert_config = json_decode($declarado->alert_config, TRUE);
					$threshold_value = (int)$alert_config["threshold_value"];
					$id_unidad_destino = (int)$alert_config["id_unidad"];
					
					if($threshold_value){

						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => 2,// 2 (VOLUMEN)
								"id_unidad_origen" => $id_unidad_volumen, // Unidad según configuración de reportes
								"id_unidad_destino" => $id_unidad_destino
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;

						$cant_declarado = $threshold_value * $valor_transformacion;
					}else{
						$cant_declarado = 0;
					}
					// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
					
					$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"warning":"";
					$reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
					$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
					$declarado = to_number_project_format($cant_declarado, $id_proyecto);

					$html .=	'<tr>';
					$html .= 		'<td>'.$nombre_categoria.' ('.$unidad_volumen.')</td>';
					$html .= 		'<td class="text-right">'.$reportado.'</td>';
					$html .= 		'<td class="text-right '.$alerta.'">'.$acumulado.'</td>';
					$html .= 		'<td class="text-right">'.$declarado.'</td>';
					$html .= 	'</tr>';
				}

				foreach ($tabla_consumo_masa_reportados as $id_categoria => $arreglo_valores){
					$arreglo_valores_acumulados = $tabla_consumo_masa_acumulados[$id_categoria];

					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}

					// CONSULTA CONFIGURACIÓN DE ALERTAS
					// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
					$options = array(
						'id_client' => $id_cliente, 
						'id_project' => $id_proyecto,
						'id_client_module' => $id_modulo_consumos,
						'alert_config' => array(
							'id_categoria' => $id_categoria,
							'id_tipo_unidad' => 1,
						)
					);
					
					$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
					$alert_config = json_decode($declarado->alert_config, TRUE);
					$threshold_value = (int)$alert_config["threshold_value"];
					$id_unidad_destino = (int)$alert_config["id_unidad"];
					
					if($threshold_value){

						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => 1,// 1 (MASA)
								"id_unidad_origen" => $id_unidad_masa, // Unidad según configuración de reportes
								"id_unidad_destino" => $id_unidad_destino
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;

						$cant_declarado = $threshold_value * $valor_transformacion;
					}else{
						$cant_declarado = 0;
					}	
					// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
					

					$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"warning":"";
					$reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
					$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
					$declarado = to_number_project_format($cant_declarado, $id_proyecto);

					$html .=	'<tr>';
					$html .=		'<td>'.$nombre_categoria.' ('.$unidad_masa.')</td>';
					$html .=		'<td class="text-right">'.$reportado.'</td>';
					$html .= 		'<td class="text-right '.$alerta.'">'.$acumulado.'</td>';
					$html .= 		'<td class="text-right">'.$declarado.'</td>';
					$html .=	'</tr>';
				}

				foreach ($tabla_consumo_energia_reportados as $id_categoria => $arreglo_valores){
					$arreglo_valores_acumulados = $tabla_consumo_energia_acumulados[$id_categoria];
	
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}

					// CONSULTA CONFIGURACIÓN DE ALERTAS
					// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
					$options = array(
						'id_client' => $id_cliente, 
						'id_project' => $id_proyecto,
						'id_client_module' => $id_modulo_consumos,
						'alert_config' => array(
							'id_categoria' => $id_categoria,
							'id_tipo_unidad' => 4,
						)
					);
					
					$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
					$alert_config = json_decode($declarado->alert_config, TRUE);
					$threshold_value = (int)$alert_config["threshold_value"];
					$id_unidad_destino = (int)$alert_config["id_unidad"];
										
					if($threshold_value){

						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => 4,// 4 (ENERGÍA)
								"id_unidad_origen" => $id_unidad_energia, // Unidad según configuración de reportes
								"id_unidad_destino" => $id_unidad_destino
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;

						$cant_declarado = $threshold_value * $valor_transformacion;
					}else{
						$cant_declarado = 0;
					}	
					// FIN CONSULTA CONFIGURACIÓN DE ALERTAS


					$alerta = (array_sum($arreglo_valores_acumulados) > $cant_declarado)?"warning":"";
					$reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
					$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
					$declarado = to_number_project_format($cant_declarado, $id_proyecto);

					$html .=	'<tr>';
					$html .=		'<td>'.$nombre_categoria.' ('.$unidad_energia.')</td>';
					$html .=		'<td class="text-right">'.$reportado.'</td>';
					$html .= 		'<td class="text-right '.$alerta.'">'.$acumulado.'</td>';
					$html .= 		'<td class="text-right">'.$declarado.'</td>';
					$html .=	'</tr>';
				}
				
				//$html .= 	'<tr>';
				//$html .= 		'<td colspan="4" class="text-left"><strong>'.lang("no_information_available_in_period").'</strong></td>';
				//$html .= 	'</tr>';

				$html .='</table>';
				$html .='</div>';
				$html .='<div class="panel panel-default">';
				$html .='<div class="panel-body">';
				$html .='<div class="row">';

				$html .='<div class="col-md-12">';
				$html .='<div class="grafico_consumo" id="consumo_volumen"></div>'; //m3
				$html .='</div>';
				$html .='<div class="col-md-12">';
				$html .='<div class="grafico_consumo" id="consumo_masa"></div>'; //ton
				$html .='</div>';
				$html .='<div class="col-md-12">';
				$html .='<div class="grafico_consumo" id="consumo_energia"></div>'; //ton
				$html .='</div>';

				$html .='</div>';
				$html .='</div>';
				$html .='</div>';
				$html .='</div>';  
				$html .='<!-- FIN CONSUMO -->';

			}
		}
		
		// RESIDUOS
		// Reviso si puede visualizar la sección de RESIDUOS
		$id_submodulo_residuos = 0;
		$id_modulo_residuos = 2;
		
		$tiene_configuracion_disponible = $configuracion_reporte->waste;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_residuos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_residuos, $id_submodulo_residuos, "ver");
		$puede_ver_residuos = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if ($puede_ver_residuos) {
			if($report_config->waste){
				
				// CONFIGURACIÓN DE REPORTE
				$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
				$array_report_config_materials = json_decode($report_config->materials, true);
				$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
				$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
				$array_id_categoria_estado = array_column($array_report_config_materials, 'estado', 'id');
				
				$html .='<!--RESIDUOS -->';
				$html .='<div class="panel panel-default">';
				
				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("waste").'</h1>';
				$html .=	'</div>';
				
				$html .='<div class="panel-body">';
		
				$html .='<table class="table table-bordered" id="tabla_residuo">';
				$html .=	'<tr>';
				$html .=		'<th colspan="4" class="label-info" style="text-align:center; background-color:'.$client_info->color_sitio.';">'.lang("waste").'</th>';
				$html .=	'</tr>';
				$html .=	'<tr>';
				$html .=		'<th class="text-center">'.lang("categories").'</th>';
				$html .=		'<th class="text-center">'.lang("Reported_in_period").'</th>';
				$html .=		'<th class="text-center">'.lang("accumulated").'</th>';
				$html .=		'<th class="text-center">'.lang("declared").'</th>';
				$html .=	'</tr>';
				
				$tabla_residuo_volumen = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_residuo_volumen_reportados = $tabla_residuo_volumen["reportados"];
				$tabla_residuo_volumen_acumulados = $tabla_residuo_volumen["acumulados"];
				$tabla_residuo_volumen_declarados = $tabla_residuo_volumen["declarados"];
				
				$tabla_residuo_masa = $this->get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_residuo_masa_reportados = $tabla_residuo_masa["reportados"];
				$tabla_residuo_masa_acumulados = $tabla_residuo_masa["acumulados"];
				$tabla_residuo_masa_declarados = $tabla_residuo_masa["declarados"];
				
				foreach ($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){
					$arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
					$arreglo_valores_declarados = $tabla_residuo_volumen_declarados[$id_categoria];
					
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					
					$alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
					$reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
					$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
					$declarado = to_number_project_format(array_sum($arreglo_valores_declarados), $id_proyecto);

					$html .=	'<tr>';
					$html .=		'<td class="text-left">'.$nombre_categoria.' ('.$unidad_volumen.')</td>';
					$html .=		'<td class="text-right">'.$reportado.'</td>';
					$html .=		'<td class="text-right '.$alerta.'">'.$acumulado.'</td>';
					$html .=		'<td class="text-right">'.$declarado.'</td>';
					$html .=	'</tr>';
				}
		
				foreach ($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){
					$arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
					$arreglo_valores_declarados = $tabla_residuo_masa_declarados[$id_categoria];
					
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
					if($row_alias->alias){
						$nombre_categoria = $row_alias->alias;
					}else{
						$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
						$nombre_categoria = $row_categoria->nombre;
					}
					
					$alerta = (array_sum($arreglo_valores_acumulados) > array_sum($arreglo_valores))?"warning":"";
					$reportado = to_number_project_format(array_sum($arreglo_valores), $id_proyecto);
					$acumulado = to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto);
					$declarado = to_number_project_format(array_sum($arreglo_valores_declarados), $id_proyecto);

					$html .=	'<tr>';
					$html .=		'<td class="text-left">'.$nombre_categoria.' ('.$unidad_masa.')</td>';
					$html .=		'<td class="text-right">'.$reportado.'</td>';
					$html .=		'<td class="text-right '.$alerta.'">'.$acumulado.'</td>';
					$html .=		'<td class="text-right">'.$declarado.'</td>';
					$html .=	'</tr>';
				}
				
		
				$html .='</table>';
		
				
				$html .='</div>';
				$html .='</div>';
				$html .='<div class="panel panel-default">';
				$html .='<div class="panel-body">';
				$html .='<div class="row">';
				$html .='<div class="col-md-6">';
				$html .='<div class="grafico_residuo" id="residuo_volumen"></div>';
				$html .='</div>';
				$html .='<div class="col-md-6">';
				$html .='<div class="grafico_residuo" id="residuo_masa"></div>';
				$html .='</div>';
				$html .='</div>';
				$html .='</div>';
				$html .='</div>';
				$html .='</div> '; 
				$html .='<!--FIN RESIDUOS-->';
				
			}
		}
		
		// PERMISOS
		// Reviso si puede visualizar la sección de PERMISOS
		$id_submodulo_permittings = 5;
		$id_modulo_permittings = 7;
		
		$tiene_configuracion_disponible = $configuracion_reporte->permittings;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_permittings,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_permittings, $id_submodulo_permittings, "ver");
		
		$puede_ver_permittings = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		$id_permiso = $this->Permitting_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		
		if ($puede_ver_permittings) {
			if($report_config->permittings){

				// EVALUADOS
				$evaluados = $this->Evaluated_permitting_model->get_all_where(
					array(
						"id_permiso" => $id_permiso, 
						"deleted" => 0
					)
				)->result();
				
				// ESTADOS
				$estados_cliente = $this->Permitting_procedure_status_model->get_details(
					array(
						"id_cliente" => $id_cliente,
					)
				)->result();
				
				// ULTIMAS EVALUACIONES
				$ultimas_evaluaciones = $this->Permitting_procedure_evaluation_model->get_last_evaluations_of_project(
					$id_proyecto, 
					$end_date
				)->result();
				
				// PROCESAR TABLA
				$array_estados_evaluados = array();
				$array_total_por_evaluado = array();
				$array_total_por_estado = array();
				$array_permisos_evaluaciones_no_cumple = array();
				foreach($estados_cliente as $estado) {
					
					$id_estado = $estado->id;
					
					if($estado->categoria == "No Aplica"){
						continue;
					}
					$array_estados_evaluados[$estado->id] = array(
						"nombre_estado" => $estado->nombre_estado,
						"categoria" => $estado->categoria,
						"color" => $estado->color,
						"evaluados" => array()
					);
					
					foreach($evaluados as $evaluado) {
						
						$id_evaluado = $evaluado->id;
						$cant = 0;
						
						$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());
						
						foreach($ultimas_evaluaciones as $ultima_evaluacion) {
							if(
								$ultima_evaluacion->id_estados_tramitacion_permisos == $id_estado && 
								$ultima_evaluacion->id_evaluado == $id_evaluado
							){
								$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
								$cant++;
								
								if($estado->categoria == "No Cumple"){
									$array_permisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
								}
							}
						}
						
						$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
						$array_total_por_evaluado[$id_evaluado][] = $cant;
						$array_total_por_estado[$id_estado][] = $cant;
					}
				}
				
				
				$html .=	'<div class="page-title clearfix">';
				$html .=		'<h1>'.lang("environmental_permittings").' - '.$environmental_authorization.'</h1>';
				$html .=	'</div>';

				$html .= '<div class="panel-body">';

				$html .= '<div class="table-responsive">';
				$html .= '<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

				$html .= '<table id="tabla_resumen_por_evaluado" class="table table-striped">';
				$html .= '<thead>';

				$html .= '<tr>';
				$html .= '<th rowspan="2" class="text-center" style="vertical-align:middle;">'.lang("general_procedure_status").'</th>';
				foreach($evaluados as $evaluado) {
					$html .= '<th colspan="2" class="text-center">'.$evaluado->nombre_evaluado.'</th>';
				}
				$html .= '</tr>';

				$html .= '<tr>';
					foreach($evaluados as $evaluado) {
						$html .= '<th class="text-center">N°</th>';
						$html .= '<th class="text-center">%</th>';
					}
				$html .= '</tr>';

				$html .= '</thead>';
				$html .= '<tbody>';

				$html .= '<tr>';
				$html .= '<th class="text-left">'.lang("total_applicable_permittings").'</th>';
					foreach($evaluados as $evaluado) {
						$html .= '<td class=" text-right">'.to_number_project_format(array_sum($array_total_por_evaluado[$evaluado->id]), $id_proyecto).'</td>';
						$html .= '<td class=" text-right">'.to_number_project_format(100, $id_proyecto).'%</td>';
					}
				$html .= '</tr>';

				foreach($array_estados_evaluados as $estado_evaluado) {

					$html .= '<tr>';
					$html .= '<td class="text-left">'.$estado_evaluado["nombre_estado"].'</td>';
					foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) {
						
						$total_evaluado = array_sum($array_total_por_evaluado[$id_evaluado]);
						if($total_evaluado == 0){
							$porcentaje = 0;
						} else {
							$porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
						}
						$html .= '<td class="text-right">'.to_number_project_format($evaluado["cant"], $id_proyecto).'</td>';
						$html .= '<td class="text-right">'.to_number_project_format($porcentaje, $id_proyecto).'%</td>';
					}
					$html .= '</tr>';
				}

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				
				
				// GRAFICO RESUMEN DE CUMPLIMIENTO
				
				$total_permisos_aplicables = 0;
				$total_cantidades_estados_evaluados_permisos = array();
				foreach($array_total_por_estado as $id_estado => $array_cant){
					$total_permisos_aplicables += array_sum($array_cant);
					$status_info = $this->Permitting_procedure_status_model->get_one($id_estado);
					$total_cantidades_estados_evaluados_permisos[] = array(
						'nombre_estado' => $status_info->nombre_estado, 
						'cantidad_categoria' => array_sum($array_cant), 
						'color' => $status_info->color
					);
				}
				
				// SECCIÓN RESUMEN POR EVALUADO
				$html .='<div class="panel panel-default">';

					$html .='<div class="panel-body">';

						$html .='<div class="col-md-6">';
							$html .='<div class="grafico_torta" id="grafico_cumplimientos_totales_permisos" style="height: 240px;"></div>';
						$html .='</div>';

						$html .='<div class="col-md-6">';
							$html .='<div class="table-responsive">';
							$html .='<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';

							$html .='<table id="tabla_permisos_no_cumple" class="table table-striped">';
							$html .='<thead>';
								$html .='<tr>';
								$html .='<th class="text-center">'.lang("permission").'</th>';
								$html .='<th class="text-center">'.lang("critical_level").'</th>';
								$html .='<th class="text-center">'.lang("report_responsible").'</th>';
								$html .='<th class="text-center">'.lang("closing_term").'</th>';
								$html .='</tr>';
							$html .='</thead>';
							$html .='<tbody>';

							foreach($array_permisos_evaluaciones_no_cumple as $row) {
								$html .='<tr>';
								$html .='<td class="text-left">'.$row->nombre_permiso.'</td>';
								$html .='<td class="text-left">'.$row->criticidad.'</td>';
								$html .='<td class="text-left">'.$row->responsable_reporte.'</td>';
								$html .='<td class="text-left">'.get_date_format($row->plazo_cierre, $id_proyecto).'</td>';
								$html .='</tr>';
							}

							$html .='</tbody>';
							$html .='</table>';
							$html .='</div>';
						$html .='</div>';
					$html .='</div>';

				$html .='</div>';
				$html .='</div>';
				
			}
		}
		

		$html .='</div><!--Fin div contenido -->';
		$html .='<script type="text/javascript">';
		
		$html .='var decimals_separator = AppHelper.settings.decimalSeparator;';
		$html .='var thousands_separator = AppHelper.settings.thousandSeparator;';
		$html .='var decimal_numbers = AppHelper.settings.decimalNumbers;';
		
		// JS Compromisos RCA
		if ($puede_ver_compromisos_rca) {
			if(!empty(array_filter($total_cantidades_estados_evaluados))){

				$html .='$("#grafico_cumplimientos_totales").highcharts({';
					$html .='chart: {';
						$html .='plotBackgroundColor: null,';
						$html .='plotBorderWidth: null,';
						$html .='plotShadow: false,';
						$html .='type: "pie",';
						$html .='events: {';
						   $html .= 'load: function() {';
							   $html .= 'if (this.options.chart.forExport) {';
								   $html .= 'Highcharts.each(this.series, function (series) {';
									   $html .= 'series.update({';
										   $html .= 'dataLabels: {';
											   $html .= 'enabled: true,';
											$html .= '}';
										$html .= '}, false);';
									$html .= '});';
									$html .= 'this.redraw();';
								$html .= '}';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'title: {';
						$html .= 'text: "",';
					$html .= '},';
					$html .= 'credits: {';
						$html .= 'enabled: false';
					$html .= '},';
					$html .= 'tooltip: {';
						$html .= 'formatter: function() {';
							$html .= 'return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";';
						$html .= '},';
					$html .= '},';
					$html .= 'plotOptions: {';
						$html .= 'pie: {';
						$html .= 'allowPointSelect: true,';
						$html .= 'cursor: "pointer",';
						$html .= 'dataLabels: {';
							$html .= 'enabled: false,';
							$html .= 'format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",';
							$html .= 'style: {';
								$html .= 'color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
								$html .= 'fontSize: "9px",';
								$html .= 'distance: -30';
							$html .= '},';
							$html .= 'crop: false';
						$html .= '},';
						$html .= 'showInLegend: true';
						$html .= '}';
					$html .= '},';
					$html .= 'legend: {';
						$html .= 'enabled: true,';
						$html .= 'itemStyle:{';
							$html .= 'fontSize: "9px"';
						$html .= '}';
					$html .= '},';
					$html .= 'exporting: {';
					
					$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("compromises").'_'.clean(lang("total_compliances")).'_'.date("Y-m-d");
						$html .= 'filename: "'.$nombre_exportacion.'",';
						$html .= 'buttons: {';
							$html .= 'contextButton: {';
								$html .= 'menuItems: [{';
									$html .= 'text: "'.lang('export_to_png').'",';
									$html .= 'onclick: function() {';
										$html .= 'this.exportChart();';
									$html .= '},';
									$html .= 'separator: false';
								$html .= '}]';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'colors: [';
					foreach($total_cantidades_estados_evaluados as $estado) { 
						$html .= '"'.$estado["color"].'", ';
					}
					$html .= '],';
					$html .= 'series: [{';
						$html .= 'name: "Porcentaje",';
						$html .= 'colorByPoint: true,';
						$html .= 'data: [';
						foreach($total_cantidades_estados_evaluados as $estado) {
							$html .= '{';
								$html .= 'name: "'.$estado["nombre_estado"].'",';
								$y = (($estado["cantidad_categoria"] * 100) / $total_compromisos_aplicables);
								$html .= 'y: '.$y;
							$html .= '},';
						}
						$html .= ']';
					$html .= '}]';
				$html .= '});';

			}else{
				$html .= '$("#grafico_cumplimientos_totales").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});'; 
			}
		}
		
		// JS Compromisos reportables
		if ($puede_ver_compromisos_reportables) {
			if(!empty(array_filter($array_grafico_reportables))){

				$html .='$("#grafico_cumplimientos_reportables").highcharts({';
					$html .='chart: {';
						$html .='plotBackgroundColor: null,';
						$html .='plotBorderWidth: null,';
						$html .='plotShadow: false,';
						$html .='type: "pie",';
						$html .='events: {';
						   $html .= 'load: function() {';
							   $html .= 'if (this.options.chart.forExport) {';
								   $html .= 'Highcharts.each(this.series, function (series) {';
									   $html .= 'series.update({';
										   $html .= 'dataLabels: {';
											   $html .= 'enabled: true,';
											$html .= '}';
										$html .= '}, false);';
									$html .= '});';
									$html .= 'this.redraw();';
								$html .= '}';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'title: {';
						$html .= 'text: "",';
					$html .= '},';
					$html .= 'credits: {';
						$html .= 'enabled: false';
					$html .= '},';
					$html .= 'tooltip: {';
						$html .= 'formatter: function() {';
							$html .= 'return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";';
						$html .= '},';
					$html .= '},';
					$html .= 'plotOptions: {';
						$html .= 'pie: {';
						$html .= 'allowPointSelect: true,';
						$html .= 'cursor: "pointer",';
						$html .= 'dataLabels: {';
							$html .= 'enabled: false,';
							$html .= 'format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",';
							$html .= 'style: {';
								$html .= 'color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
								$html .= 'fontSize: "9px",';
								$html .= 'distance: -30';
							$html .= '},';
							$html .= 'crop: false';
						$html .= '},';
						$html .= 'showInLegend: true';
						$html .= '}';
					$html .= '},';
					$html .= 'legend: {';
						$html .= 'enabled: true,';
						$html .= 'itemStyle:{';
							$html .= 'fontSize: "9px"';
						$html .= '}';
					$html .= '},';
					$html .= 'exporting: {';
					$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("compromises").'_'.clean(lang("reportable_compliances")).'_'.date("Y-m-d");
						$html .= 'filename: "'.$nombre_exportacion.'",';
						$html .= 'buttons: {';
							$html .= 'contextButton: {';
								$html .= 'menuItems: [{';
									$html .= 'text: "'.lang('export_to_png').'",';
									$html .= 'onclick: function() {';
										$html .= 'this.exportChart();';
									$html .= '},';
									$html .= 'separator: false';
								$html .= '}]';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'colors: [';
					foreach($array_grafico_reportables as $estado) { 
						$html .= '"'.$estado["color"].'", ';
					}
					$html .= '],';
					$html .= 'series: [{';
						$html .= 'name: "Porcentaje",';
						$html .= 'colorByPoint: true,';
						$html .= 'data: [';
						foreach($array_grafico_reportables as $estado) {
							$html .= '{';
								$html .= 'name: "'.$estado["nombre_estado"].'",';
								$y = $estado["porcentaje"];
								$html .= 'y: '.$y;
							$html .= '},';
						}
						$html .= ']';
					$html .= '}]';
				$html .= '});';

			}else{
				$html .= '$("#grafico_cumplimientos_reportables").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});'; 
			}
		}
		
		// JS Consumos
		if ($puede_ver_consumos) {
			$html .='$("#consumo_volumen").highcharts({';
			$html .='chart:{';
			$html .='	zoomType: "x", reflow: true, vresetZoomButton: {position: {align: "left",x: 0}},';
			$html .='	type: "column",';
			$html .='	events: {load: function(event){}}';
			$html .='},';
			$html .='title: {text: "'.lang('consumptions').' ('.$unidad_volumen.')"},';
			$html .='subtitle: {text: ""},';
			//$html .='exporting:{ enabled: false},';

			$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_volumen.'_'.date("Y-m-d");

			$html .= 	'exporting: {';
			$html .= 		'filename: "'.$nombre_exportacion.'",';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';

			$html .='xAxis: {';
			$html .='		min: 0,';
			$html .=' 		categories: [';
							foreach ($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_categories as $index => $value){
								$html .='"'.$value.'",';
							}
			$html .='		],';							
			$html .='		crosshair: true';
			$html .='},';
			$html .='	yAxis: {';
			$html .='		min: 0,';
			$html .='		title: {text: "'.$unidad_volumen_nombre_real.' ('.$unidad_volumen.')"},';
			//$html .='		labels:{ formatter: function(){return (this.value);} },';
			$html .='		labels:{ formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
			//$html .='		labels:{ formatter: function(){numberFormat(this.value, 0, ",", ".");} },';
			$html .='	},';
			$html .='	credits: {';
			$html .='		enabled: false';
			$html .='	},';
			$html .='	tooltip: {';
			$html .='		headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><table>",';
			$html .='		pointFormatter: function(){';
			$html .='			return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_volumen.'</b></td></tr>"';
			$html .='		},';
			$html .='		footerFormat:"</table>",';
			$html .='		shared: true,';
			$html .='		useHTML: true';
			$html .='	},';
			$html .='	plotOptions: {';
			$html .='		column: {';
			$html .='			pointPadding: 0.2,';
			$html .='			borderWidth: 0,';
			$html .='			dataLabels: {';
			$html .='				enabled: true,color: "#000000",align: "center",formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},';
			$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"},';
			$html .='				format: "{y:,." + decimal_numbers + "f}",';
			$html .='			}';
			$html .='		}';
			$html .='	},';
			$html .='	colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],';
			$html .='	series: [';
			$html .='	{';
			$html .='		name: "'.lang('reported').'",';
			$html .='		data: [';
								foreach($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data as $categoria_valor){
									$html .= $categoria_valor.',';	
								} 
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('accumulated').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data_a as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('declared').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data_d as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	}';
			$html .='	]';
			$html .='});';
			//var_dump($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data);

			$html .='$("#consumo_masa").highcharts({';
			$html .='chart:{';
			$html .='	zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},';
			$html .='	type: "column",';
			$html .='	events: {load: function(event){}}';
			$html .='},';
			$html .='title: {text: "'.lang('consumptions').' ('.$unidad_masa.')"},';
			$html .='subtitle: {text: ""},';
			//$html .='exporting:{ enabled: false},';

			$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_masa.'_'.date("Y-m-d");

			$html .= 	'exporting: {';
			$html .= 		'filename: "'.$nombre_exportacion.'",';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';

			$html .='xAxis: {';
			$html .='		min: 0,';
			$html .=' 		categories: [';
							foreach ($this->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_categories as $index => $value){
								$html .='"'.$value.'",';
							}
			$html .='		],';				
			$html .='		crosshair: true';
			$html .='},';
			$html .='	yAxis: {';
			$html .='		min: 0,';
			$html .='		title: {text: "'.$unidad_masa_nombre_real.' ('.$unidad_masa.')"},';
			//$html .='		labels:{ formatter: function(){return (this.value);} },';
			$html .='		labels:{ formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
			$html .='	},';
			$html .='	credits: {';
			$html .='		enabled: false';
			$html .='	},';
			$html .='	tooltip: {';
			$html .='		headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><table>",';
			$html .='		pointFormatter: function(){';
			$html .='			return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"';
			$html .='		},';
			$html .='		footerFormat:"</table>",';
			$html .='		shared: true,';
			$html .='		useHTML: true';
			$html .='	},';
			$html .='	plotOptions: {';
			$html .='		column: {';
			$html .='			pointPadding: 0.2,';
			$html .='			borderWidth: 0,';
			$html .='			dataLabels: {';
			$html .='				enabled: true,color: "#000000",align: "center",formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},';
			$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"},';
			$html .='				format: "{y:,." + decimal_numbers + "f}",';
			$html .='			}';
			$html .='		}';
			$html .='	},';
			$html .='	colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],';
			$html .='	series: [';
			$html .='	{';
			$html .='		name: "'.lang('reported').'",';
			$html .='		data: [';
								foreach($this->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_data as $categoria_valor){
									$html .= $categoria_valor.',';	
								} 
			$html .='		]'; 
			$html .='	},';
			//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
			$html .='	{';
			$html .='		name: "'.lang('accumulated').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_data_a as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('declared').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_masa_data_d as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	}';
			$html .='	]';
			$html .='});';


			$html .='$("#consumo_energia").highcharts({';
			$html .='chart:{';
			$html .='	zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},';
			$html .='	type: "column",';
			$html .='	events: {load: function(event){}}';
			$html .='},';
			$html .='title: {text: "'.lang('consumptions').' ('.$unidad_energia.')"},';
			$html .='subtitle: {text: ""},';
			//$html .='exporting:{ enabled: false},';

			$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("consumption").'_'.$unidad_energia.'_'.date("Y-m-d");

			$html .= 	'exporting: {';
			$html .= 		'filename: "'.$nombre_exportacion.'",';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';

			$html .='xAxis: {';
			$html .='		min: 0,';
			$html .=' 		categories: [';
							foreach ($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_categories as $index => $value){
								$html .='"'.$value.'",';
							}
			$html .='		],';				
			$html .='		crosshair: true';
			$html .='},';
			$html .='	yAxis: {';
			$html .='		min: 0,';
			$html .='		title: {text: "'.$unidad_energia_nombre_real.' ('.$unidad_energia.')"},';
			//$html .='		labels:{ formatter: function(){return (this.value);} },';
			$html .='		labels:{ formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
			$html .='	},';
			$html .='	credits: {';
			$html .='		enabled: false';
			$html .='	},';
			$html .='	tooltip: {';
			$html .='		headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><table>",';
			$html .='		pointFormatter: function(){';
			$html .='			return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_energia.'</b></td></tr>"';
			$html .='		},';
			$html .='		footerFormat:"</table>",';
			$html .='		shared: true,';
			$html .='		useHTML: true';
			$html .='	},';
			$html .='	plotOptions: {';
			$html .='		column: {';
			$html .='			pointPadding: 0.2,';
			$html .='			borderWidth: 0,';
			$html .='			dataLabels: {';
			$html .='				enabled: true,color: "#000000",align: "center",formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},';
			$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"},';
			$html .='				format: "{y:,." + decimal_numbers + "f}",';
			$html .='			}';
			$html .='		}';
			$html .='	},';
			$html .='	colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],';
			$html .='	series: [';
			$html .='	{';
			$html .='		name: "'.lang('reported').'",';
			$html .='		data: [';
								foreach($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_data as $categoria_valor){
									$html .= $categoria_valor.',';	
								} 
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('accumulated').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_data_a as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('declared').'",';
			$html .='		data: [';

								foreach($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_data_d as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	}';
			$html .='	]';
			$html .='});';
		}
		
		// JS Residuos
		if ($puede_ver_residuos) {
			// GRAFICOS DE RESIDUOS
		
			$html .='$("#residuo_volumen").highcharts({';
			$html .='chart:{';
			$html .='	zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},';
			$html .='	type: "column",';
			$html .='	events: {load: function(event){}}';
			$html .='},';
			$html .='title: {text: "'.lang('waste').' ('.$unidad_volumen.')"},';
			$html .='subtitle: {text: ""},';
			//$html .='exporting:{enabled: false},';
			
			$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("waste").'_'.$unidad_volumen.'_'.date("Y-m-d");
			
			$html .= 	'exporting: {';
			$html .= 		'filename: "'.$nombre_exportacion.'",';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';
	
			$html .='xAxis: {';
			$html .='		min: 0,';			
			$html .=' 		categories: [';
							foreach ($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_categories as $index => $value){
								$html .='"'.$value.'",';
							}
			$html .='		],';							
			$html .='		crosshair: true';
			$html .='},';
			$html .='	yAxis: {';
			$html .='		min: 0,';
			$html .='		title: {text: "'.$unidad_volumen_nombre_real.' ('.$unidad_volumen.')"},';
			//$html .='		labels:{ formatter: function(){return (this.value);} },';
			$html .='		labels:{ formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
			$html .='	},';
			$html .='	credits: {';
			$html .='		enabled: false';
			$html .='	},';
			$html .='	tooltip: {';
			$html .='		headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><table>",';
			$html .='		pointFormatter: function(){';
			$html .='			return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_volumen.'</b></td></tr>"';
			$html .='		},';
			$html .='		footerFormat:"</table>",';
			$html .='		shared: true,';
			$html .='		useHTML: true';
			$html .='	},';
			$html .='	plotOptions: {';
			$html .='		column: {';
			$html .='			pointPadding: 0.2,';
			$html .='			borderWidth: 0,';
			$html .='			dataLabels: {';
			$html .='				enabled: true,color: "#000000",align: "center",formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},';
			$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"},';
			$html .='				format: "{y:,." + decimal_numbers + "f}",';
			$html .='			}';
			$html .='		}';
			$html .='	},';
			$html .='	colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],';
			$html .='	series: [';
			$html .='	{';
			$html .='		name: "'.lang('reported').'",';
			$html .='		data: [';
								foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data as $categoria_valor){
									$html .= $categoria_valor.',';	
								} 
			$html .='		]';
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('accumulated').'",';
			$html .='		data: [';
			
								foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data_a as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]';
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('declared').'",';
			$html .='		data: [';
			
								foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data_d as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]';
			$html .='	}';
			$html .='	]';
			$html .='});';
			//var_dump($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data);
					
			$html .='$("#residuo_masa").highcharts({';
			$html .='chart:{';
			$html .='	zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},';
			$html .='	type: "column",';
			$html .='	events: {load: function(event){}}';
			$html .='},';
			$html .='title: {text: "'.lang('waste').' ('.$unidad_masa.')"},';
			$html .='subtitle: {text: ""},';
			//$html .='exporting:{enabled: false},';
			
			$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("waste").'_'.$unidad_masa.'_'.date("Y-m-d");
			
			$html .= 	'exporting: {';
			$html .= 		'filename: "'.$nombre_exportacion.'",';
			$html .= 		'buttons: {';
			$html .= 			'contextButton: {';
			$html .= 				'menuItems: [{';
			$html .= 					'text: "'.lang('export_to_png').'",';
			$html .= 					'onclick: function() {';
			$html .= 						'this.exportChart();';
			$html .= 					'},';
			$html .= 					'separator: false';
			$html .= 				'}]';
			$html .= 			'}';
			$html .= 		'}';
			$html .= 	'},';
			
			$html .='xAxis: {';
			$html .='		min: 0,';
			$html .=' 		categories: [';
							foreach ($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_categories as $index => $value){
								$html .='"'.$value.'",';
							}
			$html .='		],';				
			$html .='		crosshair: true';
			$html .='},';
			$html .='';
			$html .='	yAxis: {';
			$html .='		min: 0,';
			$html .='		title: {text: "'.$unidad_masa_nombre_real.' ('.$unidad_masa.')"},';
			//$html .='		labels:{ formatter: function(){return (this.value);} },';
			$html .='		labels:{ formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
			//$html .='		labels:{ format: numberFormat("{value:.0f}", 0, ",", ".") },';
	
			$html .='	},';
			$html .='	credits: {';
			$html .='		enabled: false';
			$html .='	},';
			$html .='	tooltip: {';
			$html .='		headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><table>",';
			$html .='		pointFormatter: function(){';
			$html .='			return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"';
			$html .='		},';
			$html .='		footerFormat:"</table>",';
			$html .='		shared: true,';
			$html .='		useHTML: true';
			$html .='	},';
			$html .='	plotOptions: {';
			$html .='		column: {';
			$html .='			pointPadding: 0.2,';
			$html .='			borderWidth: 0,';
			$html .='			dataLabels: {';
			$html .='				enabled: true,color: "#000000",align: "center",formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},';
			$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"},';
			$html .='				format: "{y:,." + decimal_numbers + "f}",';
			$html .='			}';
			$html .='		}';
			$html .='	},';
			$html .='	colors: ["#4CD2B1","#5C6BC0","#B3B3B3"],';
			$html .='	series: [';
			$html .='	{';
			$html .='		name: "'.lang('reported').'",';
			$html .='		data: [';
								foreach($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data as $categoria_valor){
									$html .= $categoria_valor.',';	
								} 
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('accumulated').'",';
			$html .='		data: [';
			
								foreach($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data_a as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	},';
			$html .='	{';
			$html .='		name: "'.lang('declared').'",';
			$html .='		data: [';
			
								foreach($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data_d as $categoria_valor){
									$html .= $categoria_valor.',';
								}
			$html .='		]'; 
			$html .='	}';
			$html .='	]';
			$html .='});';
		}
		
		
		// JS Permisos
		if ($puede_ver_permittings) {
			// GRAFICO PERMISOS
			if(!empty(array_filter($total_cantidades_estados_evaluados_permisos))){
			
			$html .='$("#grafico_cumplimientos_totales_permisos").highcharts({';
					$html .='chart: {';
						$html .='plotBackgroundColor: null,';
						$html .='plotBorderWidth: null,';
						$html .='plotShadow: false,';
						$html .='type: "pie",';
						$html .='events: {';
						   $html .= 'load: function() {';
							   $html .= 'if (this.options.chart.forExport) {';
								   $html .= 'Highcharts.each(this.series, function (series) {';
									   $html .= 'series.update({';
										   $html .= 'dataLabels: {';
											   $html .= 'enabled: true,';
											$html .= '}';
										$html .= '}, false);';
									$html .= '});';
									$html .= 'this.redraw();';
								$html .= '}';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'title: {';
						$html .= 'text: "",';
					$html .= '},';
					$html .= 'credits: {';
						$html .= 'enabled: false';
					$html .= '},';
					$html .= 'tooltip: {';
						$html .= 'formatter: function() {';
							$html .= 'return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +" %";';
						$html .= '},';
					$html .= '},';
					$html .= 'plotOptions: {';
						$html .= 'pie: {';
						$html .= 'allowPointSelect: true,';
						$html .= 'cursor: "pointer",';
						$html .= 'dataLabels: {';
							$html .= 'enabled: false,';
							$html .= 'format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",';
							$html .= 'style: {';
								$html .= 'color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
								$html .= 'fontSize: "9px",';
								$html .= 'distance: -30';
							$html .= '},';
							$html .= 'crop: false';
						$html .= '},';
						$html .= 'showInLegend: true';
						$html .= '}';
					$html .= '},';
					$html .= 'legend: {';
						$html .= 'enabled: true,';
						$html .= 'itemStyle:{';
							$html .= 'fontSize: "9px"';
						$html .= '}';
					$html .= '},';
					$html .= 'exporting: {';
					$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("permittings").'_'.clean(lang("total_procedures")).'_'.date("Y-m-d");
						$html .= 'filename: "'.$nombre_exportacion.'",';
						$html .= 'buttons: {';
							$html .= 'contextButton: {';
								$html .= 'menuItems: [{';
									$html .= 'text: "'.lang('export_to_png').'",';
									$html .= 'onclick: function() {';
										$html .= 'this.exportChart();';
									$html .= '},';
									$html .= 'separator: false';
								$html .= '}]';
							$html .= '}';
						$html .= '}';
					$html .= '},';
					$html .= 'colors: [';
					foreach($total_cantidades_estados_evaluados_permisos as $estado) { 
						$html .= '"'.$estado["color"].'", ';
					}
					$html .= '],';
					$html .= 'series: [{';
						$html .= 'name: "Porcentaje",';
						$html .= 'colorByPoint: true,';
						$html .= 'data: [';
						foreach($total_cantidades_estados_evaluados_permisos as $estado) {
							$html .= '{';
								$html .= 'name: "'.$estado["nombre_estado"].'",';
								$y = (($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables);
								$html .= 'y: '.$y;
							$html .= '},';
						}
						$html .= ']';
					$html .= '}]';
				$html .= '});';

			}else{
				$html .= '$("#grafico_cumplimientos_totales_permisos").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});'; 
			}
		}
		
		$html .='</script>';
		echo $html;
	}
	
	function get_color_of_status_for_permitting($id_estado){
		$estado = $this->Permitting_procedure_status_model->get_one($id_estado);
		return $estado->color;
	}
	
	function get_percentage_of_status_evaluated($cantidad_permisos, $id_estado, $id_evaluado){		
		
		$permisos_por_evaluado = $this->Permitting_model->get_total_applicable_procedures_by_evaluated($id_evaluado)->result_array();
		$total_permisos_por_evaluado = 0;
		
		foreach($permisos_por_evaluado as $ppe){
			
			$ultima_evaluacion = $this->Permitting_procedure_evaluation_model->get_last_evaluation(array("id_evaluado" => $ppe["id_evaluado"], "id_valor_permiso" => $ppe["id_valor_permiso"]))->result_array();
			if($ultima_evaluacion[0]["id"] == $ppe["id_evaluacion"]){
				$total_permisos_por_evaluado++;
			}
			
		}
		
		if($cantidad_permisos == 0){
			$porcentaje = 0;
		} else {
			$porcentaje = ($cantidad_permisos * 100) / $total_permisos_por_evaluado; 
		}

		return $porcentaje;	
	}
	
	function get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Consumo"))->result();
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		
		$array_id_categorias_valores_volumen = array();
		$array_id_categorias_valores_volumen_a = array();
		
		foreach($campos_unidad_consumo as $formulario_campo){
			$id_campo = $formulario_campo->id_campo;
			
			$datos_campo = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
			$id_unidad = $datos_campo["unidad_id"];
			
			// SI ES VOLUMEN // Y UNIDAD DE LA CONFIGURACION
			if($id_tipo_unidad == 2/* && $id_unidad == $id_unidad_volumen*/){
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($categorias as $cat){
					
					//MOSTRAR CATEGORÍAS DE MATERIALES SEGÚN CONFIGURACIÓN DE REPORTE
					$mostrar_categorias = true;
					foreach($array_report_config_materials as $report_config_material){
						if($cat->id_material == $report_config_material["id"]){
							if(!$report_config_material["estado"]){
								$mostrar_categorias = false;
								break;
							}
							
						}
					}
					
					if(!$mostrar_categorias){continue;}

					// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
					$array_id_categorias_valores_volumen[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = 0;
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $index => $ef){
						
						$datos_decoded = json_decode($ef->datos, true);
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date)) {
							
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_volumen){
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $this->Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_volumen
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = ($valor * $valor_transformacion);
								$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = ($valor * $valor_transformacion);
							}
						}else{// ACUMULADOS
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
								
								if($id_unidad == $id_unidad_volumen){
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor;
									
								}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
											"id_unidad_origen" => $id_unidad,
											"id_unidad_destino" => $id_unidad_volumen
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									$datos_decoded = json_decode($ef->datos, true);
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = ($valor * $valor_transformacion);
								}
							}
							
							
						}//end if date	
					}
					//exit();
				}
				
			}

			
		}
		
		return array(
			"reportados" => $array_id_categorias_valores_volumen, 
			"acumulados" => $array_id_categorias_valores_volumen_a
		);
		
	}
	
	function get_datos_tabla_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_consumo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Consumo")->result();	
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Consumo"))->result();
		$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		
		$array_id_categorias_valores_masa = array();
		$array_id_categorias_valores_masa_a = array();
		
		foreach($campos_unidad_consumo as $formulario_campo){
			
			$id_campo = $formulario_campo->id_campo;
			
			/*
			$datos_campo = json_decode($formulario_campo->opciones, true);
			$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
			$id_unidad = $datos_campo[0]["id_unidad"];
			*/
			$datos_campo = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
			$id_unidad = $datos_campo["unidad_id"];
			
			
			if($id_tipo_unidad == 1/* && $id_unidad == $id_unidad_volumen*/){
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($categorias as $cat){
					
					//MOSTRAR CATEGORÍAS DE MATERIALES SEGÚN CONFIGURACIÓN DE REPORTE
					$mostrar_categorias = true;
					foreach($array_report_config_materials as $report_config_material){
						if($cat->id_material == $report_config_material["id"]){
							if(!$report_config_material["estado"]){
								$mostrar_categorias = false;
								break;
							}
							
						}
					}
					
					if(!$mostrar_categorias){continue;}

					// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
					$array_id_categorias_valores_masa[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_masa_a[$cat->id_categoria][] = 0;
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						
						$datos_decoded = json_decode($ef->datos, true);
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date))  {
						
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_masa){
								//$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $this->Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_masa
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor * $valor_transformacion;
								$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
							
						}else{// ACUMULADOS
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
								// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
								if($id_unidad == $id_unidad_masa){
									//$datos_decoded = json_decode($ef->datos, true);
									//$valor = $datos_decoded[$id_campo];
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor;
									
								}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad,
											"id_unidad_origen" => $id_unidad,
											"id_unidad_destino" => $id_unidad_masa
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									$datos_decoded = json_decode($ef->datos, true);
									//$valor = $datos_decoded[$id_campo];
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor * $valor_transformacion;
									
								}
							}
							
						}//end if date	
						
					}
					
				}
				
			}
			
		}
		
		return array("reportados" => $array_id_categorias_valores_masa, "acumulados" => $array_id_categorias_valores_masa_a);
		//return $array_id_categorias_valores_masa;	
	}
	
	function get_datos_tabla_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_consumo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Consumo")->result();	
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Consumo"))->result();
		$id_unidad_energia = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		
		$array_id_categorias_valores_energia = array();
		$array_id_categorias_valores_energia_a = array();
		foreach($campos_unidad_consumo as $formulario_campo){
			$id_campo = $formulario_campo->id_campo;
			
			$datos_campo = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_campo["tipo_unidad_id"];
			$id_unidad = $datos_campo["unidad_id"];
			
			if($id_tipo_unidad == 4){
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($categorias as $cat){
					//MOSTRAR CATEGORÍAS DE MATERIALES SEGÚN CONFIGURACIÓN DE REPORTE
					$mostrar_categorias = true;
					foreach($array_report_config_materials as $report_config_material){
						if($cat->id_material == $report_config_material["id"]){
							if(!$report_config_material["estado"]){
								$mostrar_categorias = false;
								break;
							}
							
						}
					}
					
					if(!$mostrar_categorias){continue;}

					// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
					$array_id_categorias_valores_energia[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_energia_a[$cat->id_categoria][] = 0;
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Consumo")->result();
					
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						$datos_decoded = json_decode($ef->datos, true);
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date))  {
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_energia){
								//$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_energia[$cat->id_categoria][] = $valor;
								$array_id_categorias_valores_energia_a[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $this->Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_energia
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_energia[$cat->id_categoria][] = $valor * $valor_transformacion;
								$array_id_categorias_valores_energia_a[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
							
						}else{// ACUMULADOS
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
								// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
								if($id_unidad == $id_unidad_energia){
									//$datos_decoded = json_decode($ef->datos, true);
									//$valor = $datos_decoded[$id_campo];
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_energia_a[$cat->id_categoria][] = $valor;
									
								}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad,
											"id_unidad_origen" => $id_unidad,
											"id_unidad_destino" => $id_unidad_energia
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									$datos_decoded = json_decode($ef->datos, true);
									//$valor = $datos_decoded[$id_campo];
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_energia_a[$cat->id_categoria][] = $valor * $valor_transformacion;
									
								}
							}
							
						}//end if date	
						
					}
					
				}
				
			}
			
		}
		//exit();
		return array("reportados" => $array_id_categorias_valores_energia, "acumulados" => $array_id_categorias_valores_energia_a);
		
	}
	
	function get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$object = new StdClass;
		
		$array_id_categorias_valores_volumen_total = $this->get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_volumen = $array_id_categorias_valores_volumen_total["reportados"];
		$array_id_categorias_valores_volumen_a = $array_id_categorias_valores_volumen_total["acumulados"];
		
		$array_grafico_consumos_volumen_categories = array();
		$array_grafico_consumos_volumen_data = array();
		$array_grafico_consumos_volumen_data_a = array();
		$array_grafico_consumos_volumen_data_d = array();
		
		foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
			$arreglo_valores_a = $array_id_categorias_valores_volumen_a[$id_categoria];
			
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			$array_grafico_consumos_volumen_categories[] = $nombre_categoria;
			$array_grafico_consumos_volumen_data[] = array_sum($arreglo_valores);
		}
		
		foreach ($array_id_categorias_valores_volumen_a as $id_categoria => $arreglo_valores_a){
			$array_grafico_consumos_volumen_data_a[] = array_sum($arreglo_valores_a);
			
			// CONSULTA CONFIGURACIÓN DE ALERTAS 
			// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
			$options = array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto,
				'id_client_module' => $id_modulo_consumos,
				'alert_config' => array(
					'id_categoria' => $id_categoria,
					'id_tipo_unidad' => 2,
				)
			);
			
			$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
			$alert_config = json_decode($declarado->alert_config, TRUE);
			$threshold_value = (int)$alert_config["threshold_value"];
			$id_unidad_destino = (int)$alert_config["id_unidad"];
			
			if($threshold_value){

				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => 2,// 2 (VOLUMEN)
						"id_unidad_origen" => $id_unidad_volumen, // Unidad según configuración de reportes
						"id_unidad_destino" => $id_unidad_destino
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}
			
			$array_grafico_consumos_volumen_data_d[] = $cant_declarado;
			// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
		}
		
		$object->array_grafico_consumos_volumen_categories = $array_grafico_consumos_volumen_categories;
		$object->array_grafico_consumos_volumen_data = $array_grafico_consumos_volumen_data;
		$object->array_grafico_consumos_volumen_data_a = $array_grafico_consumos_volumen_data_a;
		$object->array_grafico_consumos_volumen_data_d = $array_grafico_consumos_volumen_data_d;
		
		return $object;
		
	}
	
	function get_datos_grafico_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$object = new StdClass;
		
		$array_id_categorias_valores_masa_total = $this->get_datos_tabla_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_masa = $array_id_categorias_valores_masa_total["reportados"];
		$array_id_categorias_valores_masa_a = $array_id_categorias_valores_masa_total["acumulados"];
		
		$array_grafico_consumos_masa_categories = array();
		$array_grafico_consumos_masa_data = array();
		$array_grafico_consumos_masa_data_a = array();
		$array_grafico_consumos_masa_data_d = array();
		
		foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			$array_grafico_consumos_masa_categories[] = $nombre_categoria;
			$array_grafico_consumos_masa_data[] = array_sum($arreglo_valores);
		}
		
		foreach ($array_id_categorias_valores_masa_a as $id_categoria => $arreglo_valores_a){
			$array_grafico_consumos_masa_data_a[] = array_sum($arreglo_valores_a);
			
			// CONSULTA CONFIGURACIÓN DE ALERTAS
			// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
			$options = array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto,
				'id_client_module' => $id_modulo_consumos,
				'alert_config' => array(
					'id_categoria' => $id_categoria,
					'id_tipo_unidad' => 1,
				)
			);
			
			$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
			$alert_config = json_decode($declarado->alert_config, TRUE);
			$threshold_value = (int)$alert_config["threshold_value"];
			$id_unidad_destino = (int)$alert_config["id_unidad"];
			
			if($threshold_value){
				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => 1,// 1 (MASA)
						"id_unidad_origen" => $id_unidad_masa, // Unidad según configuración de reportes
						"id_unidad_destino" => $id_unidad_destino
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}
			
			$array_grafico_consumos_masa_data_d[] = $cant_declarado;
			// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
		}
		
		$object->array_grafico_consumos_masa_categories = $array_grafico_consumos_masa_categories;
		$object->array_grafico_consumos_masa_data = $array_grafico_consumos_masa_data;
		$object->array_grafico_consumos_masa_data_a = $array_grafico_consumos_masa_data_a;
		$object->array_grafico_consumos_masa_data_d = $array_grafico_consumos_masa_data_d;
		
		return $object;
		
	}
	
	function get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$object = new StdClass;
		
		$array_id_categorias_valores_energia_total = $this->get_datos_tabla_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_energia = $array_id_categorias_valores_energia_total["reportados"];
		$array_id_categorias_valores_energia_a = $array_id_categorias_valores_energia_total["acumulados"];
		
		$array_grafico_consumos_energia_categories = array();
		$array_grafico_consumos_energia_data = array();
		$array_grafico_consumos_energia_data_a = array();
		$array_grafico_consumos_energia_data_d = array();
		
		$id_modulo_consumos = 2; // Registros ambientales
		$id_unidad_energia = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		
		foreach ($array_id_categorias_valores_energia as $id_categoria => $arreglo_valores){
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			$array_grafico_consumos_energia_categories[] = $nombre_categoria;
			$array_grafico_consumos_energia_data[] = array_sum($arreglo_valores);
		}
		
		foreach ($array_id_categorias_valores_energia_a as $id_categoria => $arreglo_valores_a){
			$array_grafico_consumos_energia_data_a[] = array_sum($arreglo_valores_a);
			
			// CONSULTA CONFIGURACIÓN DE ALERTAS
			// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
			$options = array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto,
				'id_client_module' => $id_modulo_consumos,
				'alert_config' => array(
					'id_categoria' => $id_categoria,
					'id_tipo_unidad' => 4,
				)
			);
			
			$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
			$alert_config = json_decode($declarado->alert_config, TRUE);
			$threshold_value = (int)$alert_config["threshold_value"];
			$id_unidad_destino = (int)$alert_config["id_unidad"];
			
			if($threshold_value){
				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => 4,// 4 (ENERGÍA)
						"id_unidad_origen" => $id_unidad_energia, // Unidad según configuración de reportes
						"id_unidad_destino" => $id_unidad_destino
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}	
			
			$array_grafico_consumos_energia_data_d[] = $cant_declarado;
			// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
		}
		
		$object->array_grafico_consumos_energia_categories = $array_grafico_consumos_energia_categories;
		$object->array_grafico_consumos_energia_data = $array_grafico_consumos_energia_data;
		$object->array_grafico_consumos_energia_data_a = $array_grafico_consumos_energia_data_a;
		$object->array_grafico_consumos_energia_data_d = $array_grafico_consumos_energia_data_d;
		
		return $object;
		
	}
	
	function get_tabla_categorias_de_material($id_cliente, $id_proyecto, $id_tipo_unidad_tabla, $id_material, $id_unidad_configurada, $start_date, $end_date){
		
		$id_modulo_consumos = 2; // Registros Ambientales
		
		// TIPOS DE TRATAMIENTOS
		$array_tipos_tratamiento = array();
		$tipos_tratamiento = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result();
		foreach($tipos_tratamiento as $tipo_tratamiento){
			$array_tipos_tratamiento[$tipo_tratamiento->id] = $tipo_tratamiento->nombre;
		}
		
		// INICIALIZAR ARREGLOS
		$array_id_categorias_valores_rsd = array();
		$array_id_categorias_valores_rsd_acumulado = array();
		$array_id_categorias_valores_rsd_declarado = array();
		$array_id_categorias_valores_rsd_tipos_tratamiento = array();
		$array_id_categorias_nombres_rsd = array();
		
		$formularios_categorias_rsd = $this->Reports_model->get_categories_of_project($id_cliente, $id_proyecto, $id_tipo_unidad_tabla, $id_material)->result();
		// POR CADA FORMULARIO - CATEGORIA
		foreach($formularios_categorias_rsd as $formulario_categoria){
			
			$id_formulario = $formulario_categoria->id;
			
			// CATEGORIA
			$id_categoria = $formulario_categoria->id_categoria;
			$row_alias = $this->Categories_alias_model->get_one_where(
				array(
					'id_categoria' => $id_categoria, 
					'id_cliente' => $id_cliente, 
					'deleted' => 0
				)
			);
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			// UNIDAD
			$unidad_decoded = json_decode($formulario_categoria->unidad);
			$id_tipo_unidad = $unidad_decoded->tipo_unidad_id;
			$id_unidad = $unidad_decoded->unidad_id;
			//$row_unidad = $this->Unity_model->get_one($unidad_decoded->unidad_id);// UNIDAD DEL FORMULARIO
			$row_unidad = $this->Unity_model->get_one($id_unidad_configurada);// UNIDAD DE CONFIGURACION DE REPORTE
			
			$array_id_categorias_nombres_rsd[$id_categoria] = $nombre_categoria.' ('.$row_unidad->nombre.')';
			
			// SUMA DE VALORES
			$array_id_categorias_valores_rsd[$id_categoria][] = 0;
			$array_id_categorias_valores_rsd_acumulado[$id_categoria][] = 0;
			$array_id_categorias_valores_rsd_tipos_tratamiento[$id_categoria][$array_tipos_tratamiento[1]][] = 0; // Disposición
			$array_id_categorias_valores_rsd_tipos_tratamiento[$id_categoria][$array_tipos_tratamiento[2]][] = 0; // Reutilización
			$array_id_categorias_valores_rsd_tipos_tratamiento[$id_categoria][$array_tipos_tratamiento[3]][] = 0; // Reciclaje
			$elementos_form = $this->Calculation_model->get_records_of_category_of_form($id_categoria, $id_formulario, "Residuo")->result();
			foreach($elementos_form as $ef){
				$datos_decoded = json_decode($ef->datos, true);
				
				// REPORTADOS EN EL PERIODO
				if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date)) {
					
					if($id_unidad == $id_unidad_configurada){
						$valor = $datos_decoded["unidad_residuo"];
						$array_id_categorias_valores_rsd[$id_categoria][] = $valor;
						$array_id_categorias_valores_rsd_acumulado[$id_categoria][] = $valor;
						if($datos_decoded["tipo_tratamiento"]){
							$id_tipo_tratamiento = (int)$datos_decoded["tipo_tratamiento"];
							$array_id_categorias_valores_rsd_tipos_tratamiento[$id_categoria][$array_tipos_tratamiento[$id_tipo_tratamiento]][] = $valor;
						}
					}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 1 (MASA)
								"id_unidad_origen" => $id_unidad,
								"id_unidad_destino" => $id_unidad_configurada
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;
						
						//$datos_decoded = json_decode($ef->datos, true);
						$valor = $datos_decoded["unidad_residuo"];
						//$valor = $datos_decoded[$id_campo];
						$array_id_categorias_valores_rsd[$id_categoria][] = $valor * $valor_transformacion;
						$array_id_categorias_valores_rsd_acumulado[$id_categoria][] = $valor * $valor_transformacion;
						if($datos_decoded["tipo_tratamiento"]){
							$id_tipo_tratamiento = (int)$datos_decoded["tipo_tratamiento"];
							$array_id_categorias_valores_rsd_tipos_tratamiento[$id_categoria][$array_tipos_tratamiento[$id_tipo_tratamiento]][] = $valor * $valor_transformacion;
						}
					}
				}else{// ACUMULADOS
				
					// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
					if($datos_decoded["fecha"] <= $end_date){
						
						// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
						if($id_unidad == $id_unidad_configurada){
							$valor = $datos_decoded["unidad_residuo"];
							$array_id_categorias_valores_rsd_acumulado[$id_categoria][] = $valor;
							
						}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
							$fila_conversion = $this->Conversion_model->get_one_where(
								array(
									"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
									"id_unidad_origen" => $id_unidad,
									"id_unidad_destino" => $id_unidad_configurada
								)
							);
							$valor_transformacion = $fila_conversion->transformacion;
							
							$datos_decoded = json_decode($ef->datos, true);
							$valor = $datos_decoded["unidad_residuo"];
							//$valor = $datos_decoded[$id_campo];
							$array_id_categorias_valores_rsd_acumulado[$id_categoria][] = $valor * $valor_transformacion;
							
						}
						
					}
					
				}// FIN IF ELSE
				
			}// FIN FOREACH ELEMENTO
			
			// CONSULTA CONFIGURACIÓN DE ALERTAS
			// Traigo la unidad según configuración de reportes
			$unit_id_on_config = $this->Reports_units_settings_model->get_one_where(array(
				"id_cliente" => $id_cliente, 
				"id_proyecto" => $id_proyecto, 
				"id_tipo_unidad" => $id_tipo_unidad, 
				"deleted" => 0
			))->id_unidad;
			
			// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
			$options = array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto,
				'id_client_module' => $id_modulo_consumos,
				'alert_config' => array(
					'id_categoria' => $id_categoria,
					'id_tipo_unidad' => $id_tipo_unidad,
					'id_unidad' => $unit_id_on_config
				)
			);
			
			$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
			$alert_config = json_decode($declarado->alert_config, TRUE);
			$threshold_value = (int)$alert_config["threshold_value"];

			if($threshold_value){

				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => $id_tipo_unidad,
						"id_unidad_origen" => $unit_id_on_config,
						"id_unidad_destino" => $id_unidad_configurada
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}
			// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
			
			$array_id_categorias_valores_rsd_declarado[$id_categoria][] = $cant_declarado;
		}
		
		return array(
			"nombre_categorias" => $array_id_categorias_nombres_rsd, 
			"reportados" => $array_id_categorias_valores_rsd, 
			"acumulados" => $array_id_categorias_valores_rsd_acumulado, 
			"declarados" => $array_id_categorias_valores_rsd_declarado, 
			"tipos_tratamiento" => $array_id_categorias_valores_rsd_tipos_tratamiento
		);
		
	}
	
	function get_permitting_summary_by_evaluated($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_permiso = $this->Permitting_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";

		$html = '<div class="panel panel-default">';
		$html .= 	'<div class="page-title clearfix">';
		$html .= 		'<h1>'.lang('permittings') . " - " . lang('summary_by_evaluated').'</h1>';
		$html .= '</div>';
		
		$html .= '<!-- UN GRÁFICO POR CADA EVALUADO -->';
		$html .= '<div class="panel-body">';
		
		if($id_permiso){
			
			$evaluados_matriz_permisos = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $id_permiso, "deleted" => 0))->result_array();
			
			$array_total_permisos_aplicables_por_evaluado = array();
			
			foreach($evaluados_matriz_permisos as $evaluado){
		
				$permisos_por_evaluado = $this->Permitting_model->get_total_applicable_procedures_by_evaluated($evaluado["id"])->result_array();
				$total_permisos_por_evaluado = 0;
				
				foreach($permisos_por_evaluado as $ppe){
					if( ($ppe["fecha_evaluacion"] >= $start_date) && ($ppe["fecha_evaluacion"] <= $end_date) ){					
						$total_permisos_por_evaluado++;
					} 	
				}
		
				$array_total_permisos_aplicables_por_evaluado[$evaluado["id"]] = $total_permisos_por_evaluado;
			}
			
			//listado de estados de categoría Aplica que están siendo utilizados en alguna evaluación
			$estados = $this->Permitting_model->get_status_in_evaluations($id_cliente, $id_proyecto)->result_array();
			$array_estados_en_evaluaciones = array();
			
			foreach($estados as $estado){
				if( ($estado["fecha_evaluacion"] >= $start_date) && ($estado["fecha_evaluacion"] <= $end_date) ){					
					$array_estados_en_evaluaciones[] = $estado;
				} 
			}
			
			//SE AGRUPA $array_estados_en_evaluaciones POR id_estado
			$result_estados = array();
			foreach($array_estados_en_evaluaciones as $atcee){
				$repeat = false;
				for($i = 0; $i < count($result_estados); $i++){
					if($result_estados[$i]['id_estado'] == $atcee['id_estado']){
						//$result_estado[$i]['cantidad_categoria'] += $atcee['cantidad_categoria'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_estados[] = array('nombre_estado' =>  $atcee['nombre_estado'], 'id_estado' => $atcee['id_estado']);
				}		
			}
			//FIN AGRUPAR $array_estados_en_evaluaciones

			foreach($evaluados_matriz_permisos as $evaluado) {
				
				$html .= '<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">';
				$html .= 	'<div class="panel panel-default">';
				$html .= 		'<div class="page-title clearfix panel-success">';
				$html .= 			'<div class="pt10 pb10 text-center">'.$evaluado["nombre_evaluado"].'</div>';
				$html .= 		'</div>';
				$html .= 		'<div class="panel-body">';
				
				$array_nombre_porcentaje = array();
				foreach($result_estados as $estado) {
					$cantidad = $this->get_quantity_of_status_evaluated_for_permitting($estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$array_nombre_porcentaje[$estado["nombre_estado"]] = $this->get_percentage_of_status_evaluated_for_permitting($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
				}
				
				if(!empty(array_filter($array_nombre_porcentaje))){
					$html .= '<div id="grafico_resumen_evaluado_permisos_'.$evaluado["id"].'" style="height: 240px;" class="grafico_resumen_evaluado_permisos" data-nombre_evaluado="'.$evaluado["nombre_evaluado"].'" data-tiene_evaluacion="1"></div>';
				} else {
					$html .= '<div id="grafico_resumen_evaluado_permisos_'.$evaluado["id"].'" style="height: 240px;" class="grafico_resumen_evaluado_permisos" data-nombre_evaluado="'.$evaluado["nombre_evaluado"].'" data-tiene_evaluacion="0"></div>';
				}

				$html .= 		'</div>';
				$html .= 	'</div>';
				$html .= '</div>';
				
			}

			//JAVASCRIPT PARA LOS GRÁFICOS
			$html .= '<script type="text/javascript">';
			
			$array_nombre_porcentaje = array();
			$array_colores = array();
			
			foreach($evaluados_matriz_permisos as $evaluado) { 
			
				foreach($result_estados as $estado) {
					$array_colores[$estado["id_estado"]] = $this->get_color_of_status_for_permitting($estado["id_estado"]);
					$cantidad = $this->get_quantity_of_status_evaluated_for_permitting($estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$array_nombre_porcentaje[$estado["nombre_estado"]] = $this->get_percentage_of_status_evaluated_for_permitting($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
				}
				
				if(!empty(array_filter($array_nombre_porcentaje))){
				
					$html .= '$("#grafico_resumen_evaluado_permisos_'.$evaluado["id"].'").highcharts({';
					$html .= 	'chart: {';
					$html .= 		'plotBackgroundColor: null,';
					$html .= 		'plotBorderWidth: null,';
					$html .= 		'plotShadow: false,';
					$html .= 		'type: "pie",';
					$html .= 		'events: {';
					$html .= 			'load: function() {';
					$html .= 				'if (this.options.chart.forExport) {';
					$html .= 					'Highcharts.each(this.series, function (series) {';
					$html .= 						'series.update({';
					$html .= 							'dataLabels: {';
					$html .= 								'enabled: true,';
					$html .= 							'}';
					$html .= 							'}, false);';
					$html .= 						'});';
					$html .= 						'this.redraw();';
					$html .= 					'}';
					$html .= 				'}';
					$html .= 			'}';
					$html .= 		'},';
					
					$html .= 	'title: {';
					$html .= 		'text: "",';
					$html .= 	'},';
					$html .= 	'credits: {';
					$html .= 		'enabled: false';
					$html .= 	'},';
					$html .= 	'tooltip: {';
					$html .= 		'formatter: function() {';
					//$html .= 			'return "<b>"+ this.point.name +"</b>: "+ Math.round(this.percentage) +" %";';
					$html .= 			'return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'") +" %";';
					$html .= 		'},';
					$html .= 		'pointFormat: "{series.name}: <b>{point.y}%</b>"';
					$html .= 	'},';
					$html .= 	'plotOptions: {';
					$html .= 		'pie: {';
					//$html .= 		'//size: 80,';
					$html .= 		'allowPointSelect: true,';
					$html .= 		'cursor: "pointer",';
					$html .= 		'dataLabels: {';
					$html .= 			'enabled: false,';
					$html .= 			'format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",';
					$html .= 			'style: {';
					$html .= 				'color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
					$html .= 				'fontSize: "9px",';
					$html .= 				'distance: -30';
					$html .= 			'},';
					$html .= 			'crop: false';
					$html .= 		'},';
					$html .= 		'showInLegend: true';
					$html .= 		'}';
					$html .= 	'},';
					$html .= 	'legend: {';
					$html .= 		'enabled: true,';
					$html .= 		'itemStyle:{';
					$html .= 			'fontSize: "9px"';
					$html .= 		'}';
					$html .= 	'},';
					
					$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("permittings").'_'.date("Y-m-d");

					$html .= 	'exporting: {';
					//$html .= 		'filename: "'. lang("summary_evaluated"). '" - "' .$evaluado["nombre_evaluado"].'",';
					$html .= 		'chartOptions:{';
					$html .= 			'title: {';
					$html .= 				'text:"'.$evaluado["nombre_evaluado"].'"';
					$html .= 			'}';
					$html .= 		'},';
					$html .= 		'filename: "'.$nombre_exportacion.'",';
					$html .= 		'buttons: {';
					$html .= 			'contextButton: {';
					$html .= 				'menuItems: [{';
					$html .= 					'text: "'.lang('export_to_png').'",';
					$html .= 					'onclick: function() {';
					$html .= 						'this.exportChart();';
					$html .= 					'},';
					$html .= 					'separator: false';
					$html .= 				'}]';
					$html .= 			'}';
					$html .= 		'}';
					$html .= 	'},';
					$html .= 	'colors: [';
					foreach($array_colores as $color) {
						$html .= 	'"'. $color . '",';
					}
					$html .= 	'],';
					$html .= 	'series: [{';
					$html .= 		'name: "Porcentaje",';
					$html .= 		'colorByPoint: true,';
					$html .= 		'data: [';
					foreach($array_nombre_porcentaje as $nombre => $porcentaje){
						$html .= 		'{';
						$html .= 			'name: "'. $nombre.'",';
						$html .= 			'y: '.$porcentaje.'';
						$html .= 		'},';
					}	
					$html .= 		']';
					$html .= 	'}]';
					
					$html .= '});';
				
				}else{
	
					$html .= '$("#grafico_resumen_evaluado_permisos_'.$evaluado["id"].'").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"table-cell"});';
	
				}
				
			}
	
			$html .= '</script>';			

			$html .= '<!-- TABLA -->';
			$html .= '<div class="panel-body">';
			$html .= 	'<div class="table-responsive">';
			$html .= 		'<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';
			$html .= 			'<table id="tabla_resumen_por_evaluado" class="table table-striped">';
			
			$html .= 				'<thead>';		
			$html .= 					'<tr>';
			$html .= 						'<th rowspan="2" class="text-center" style="vertical-align:middle;">'.lang("compliance_status").'</th>';
			foreach($evaluados_matriz_permisos as $evaluado) {	
				$html .= 						'<th colspan="2" class="text-center">'.$evaluado["nombre_evaluado"].'</th>';
			}
			$html .= 					'</tr>';		
			$html .= 					'<tr>';
			foreach($evaluados_matriz_permisos as $evaluado) {
				$html .= 				'<th class="text-center">N°</th>';
				$html .= 				'<th class="text-center">%</th>';	
			}
			$html .= 					'</tr>';		
			$html .= 				'</thead>';
			
			$html .= 				'<tbody>';
			$html .= 					'<tr>';
			$html .= 						'<th class="text-left">'.lang("total_applicable_permittings").'</th>';
			foreach($array_total_permisos_aplicables_por_evaluado as $total) {
				$html .= 						'<td class="text-right">'.to_number_project_format($total, $id_proyecto).'</td>';
				$html .= 						'<td class="text-right">'.to_number_project_format(100, $id_proyecto).' %</td>';
			}
			$html .= 					'</tr>';
			foreach($result_estados as $estado){
				$html .= 				'<tr>';
				$html .= 					'<td class="text-left">'.$estado["nombre_estado"].'</td>';
				foreach($evaluados_matriz_permisos as $evaluado) {
					$cantidad = $this->get_quantity_of_status_evaluated_for_permitting($estado["id_estado"], $evaluado["id"], $start_date, $end_date); 
					$porcentaje = $this->get_percentage_of_status_evaluated_for_permitting($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$html .= 				'<td class="text-right">'.to_number_project_format($cantidad, $id_proyecto).'</td>';  
					$html .= 				'<td class="text-right">'.to_number_project_format($porcentaje, $id_proyecto).' %</td>';
				}
				$html .= 				'</tr>';	
			}
			$html .= 				'</tbody>';
			
			$html .= 			'</table>';
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			$html .= '<!-- FIN TABLA -->';
						
		} else {
			
			$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
			
			$html .= '<div class="panel panel-default mb15">';
			$html .= 	'<div class="panel-body">';
			$html .= 		'<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
			$html .= 			lang('the_project').' "'.$nombre_proyecto.'" '.lang('permitting_matrix_not_enabled');
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
		}
		
		return $html;
		
	}
	
	function get_quantity_of_status_evaluated_for_permitting($id_estado, $id_evaluado, $start_date, $end_date){		
		
		$cantidad = 0;
		$evaluaciones = $this->Permitting_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->result_array();

		foreach($evaluaciones as $evaluacion){
			if( ($evaluacion["fecha_evaluacion"] >= $start_date) && ($evaluacion["fecha_evaluacion"] <= $end_date) ){					
				$cantidad++;
			} 	
		}
		
		return $cantidad;
			
	}
	
	function get_percentage_of_status_evaluated_for_permitting($cantidad_permisos, $id_estado, $id_evaluado, $start_date, $end_date){		
		
		$permisos_por_evaluado = $this->Permitting_model->get_total_applicable_procedures_by_evaluated($id_evaluado)->result_array();
		$total_permisos_por_evaluado = 0;
		
		foreach($permisos_por_evaluado as $ppe){
			if( ($ppe["fecha_evaluacion"] >= $start_date) && ($ppe["fecha_evaluacion"] <= $end_date) ){					
				$total_permisos_por_evaluado++;
			} 
		}
		
		if($cantidad_permisos == 0){
			$porcentaje = 0;
		} else {
			$porcentaje = ($cantidad_permisos * 100) / $total_permisos_por_evaluado; 
		}

		return $porcentaje;
	
	}
	
	function get_compromises_summary_by_evaluated($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_compromiso = $this->Compromises_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		$html = '<div class="panel panel-default">';
		$html .= 	'<div class="page-title clearfix">';
		$html .= 		'<h1>'.lang("compromises") . " - " . lang('summary_by_evaluated').'</h1>';
		$html .= '</div>';
		
		$html .= '<!-- UN GRÁFICO POR CADA EVALUADO -->';
		$html .= '<div class="panel-body">';
		
		if($id_compromiso){
			
			$evaluados_matriz_compromiso = $this->Evaluated_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso, "deleted" => 0))->result_array();
			//$view_data["evaluados_matriz_compromiso"] = $evaluados_matriz_compromiso;
			$array_total_compromisos_aplicables_por_evaluado = array();
			
			foreach($evaluados_matriz_compromiso as $evaluado){
		
				$compromisos_por_evaluado = $this->Compromises_model->get_total_applicable_compromises_by_evaluated($evaluado["id"])->result_array();
				$total_compromisos_por_evaluado = 0;
				
				foreach($compromisos_por_evaluado as $cpe){
					if( ($cpe["fecha_evaluacion"] >= $start_date) && ($cpe["fecha_evaluacion"] <= $end_date) ){					
						$total_compromisos_por_evaluado++;
					} 	
				}
		
				$array_total_compromisos_aplicables_por_evaluado[$evaluado["id"]] = $total_compromisos_por_evaluado;
			}
			
			//$view_data["total_compromisos_aplicables_por_evaluado"] = $array_total_compromisos_aplicables_por_evaluado;
			
			//listado de estados de categoría Cumple y No Cumple que están siendo utilizados en alguna evaluación
			$estados = $this->Compromises_model->get_status_in_evaluations($id_cliente)->result_array();
			$array_estados_en_evaluaciones = array();
			
			foreach($estados as $estado){
				if( ($estado["fecha_evaluacion"] >= $start_date) && ($estado["fecha_evaluacion"] <= $end_date) ){					
					$array_estados_en_evaluaciones[] = $estado;
				} 
			}
			
			//SE AGRUPA $array_estados_en_evaluaciones POR id_estado
			$result_estados = array();
			foreach($array_estados_en_evaluaciones as $atcee){
				$repeat = false;
				for($i = 0; $i < count($result_estados); $i++){
					if($result_estados[$i]['id_estado'] == $atcee['id_estado']){
						//$result_estado[$i]['cantidad_categoria'] += $atcee['cantidad_categoria'];
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_estados[] = array('nombre_estado' =>  $atcee['nombre_estado'], 'id_estado' => $atcee['id_estado']);
				}		
			}
			//FIN AGRUPAR $array_estados_en_evaluaciones
			
			//$view_data["estados"] = $result_estados;
			
			foreach($evaluados_matriz_compromiso as $evaluado) {
				
				$html .= '<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 col-xl-2">';
				$html .= 	'<div class="panel panel-default">';
				//$html .= 		'<div class="page-title clearfix" style="background-color:'.$client_info->color_sitio.'">';
				$html .= 		'<div class="page-title clearfix panel-success">';
				$html .= 			'<div class="pt10 pb10 text-center">'.$evaluado["nombre_evaluado"].'</div>';
				$html .= 		'</div>';
				$html .= 		'<div class="panel-body">';
				
				$array_nombre_porcentaje = array();
				foreach($result_estados as $estado) {
					$cantidad = $this->get_quantity_of_status_evaluated_for_compromises($estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$array_nombre_porcentaje[$estado["nombre_estado"]] = $this->get_percentage_of_status_evaluated_for_compromises($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
				}
				
				if(!empty(array_filter($array_nombre_porcentaje))){
					$html .='<div id="grafico_resumen_evaluado_'.$evaluado["id"].'" style="height: 240px;" class="grafico_resumen_evaluado_compromisos" data-nombre_evaluado="'.$evaluado["nombre_evaluado"].'" data-tiene_evaluacion="1"></div>';
				} else {
					$html .='<div id="grafico_resumen_evaluado_'.$evaluado["id"].'" style="height: 240px;" class="grafico_resumen_evaluado_compromisos" data-nombre_evaluado="'.$evaluado["nombre_evaluado"].'" data-tiene_evaluacion="0"></div>';
				}
				
				$html .= 		'</div>';
				$html .= 	'</div>';
				$html .= '</div>';
				
			}
		
			//JAVASCRIPT PARA LOS GRÁFICOS
			$html .= '<script type="text/javascript">';
			
			$array_nombre_porcentaje = array();
			$array_colores = array();
			
			foreach($evaluados_matriz_compromiso as $evaluado) { 
			
				foreach($result_estados as $estado) {
					$array_colores[$estado["id_estado"]] = $this->get_color_of_status_for_compromises($estado["id_estado"]);
					$cantidad = $this->get_quantity_of_status_evaluated_for_compromises($estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$array_nombre_porcentaje[$estado["nombre_estado"]] = $this->get_percentage_of_status_evaluated_for_compromises($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
				}
				
				if(!empty(array_filter($array_nombre_porcentaje))){
				
					$html .= '$("#grafico_resumen_evaluado_'.$evaluado["id"].'").highcharts({';
					$html .= 	'chart: {';
					$html .= 		'plotBackgroundColor: null,';
					$html .= 		'plotBorderWidth: null,';
					$html .= 		'plotShadow: false,';
					$html .= 		'type: "pie",';
					$html .= 		'events: {';
					$html .= 			'load: function() {';
					$html .= 				'if (this.options.chart.forExport) {';
					$html .= 					'Highcharts.each(this.series, function (series) {';
					$html .= 						'series.update({';
					$html .= 							'dataLabels: {';
					$html .= 								'enabled: true,';
					$html .= 							'}';
					$html .= 							'}, false);';
					$html .= 						'});';
					$html .= 						'this.redraw();';
					$html .= 					'}';
					$html .= 				'}';
					$html .= 			'}';
					$html .= 		'},';
					
					$html .= 	'title: {';
					$html .= 		'text: "",';
					$html .= 	'},';
					$html .= 	'credits: {';
					$html .= 		'enabled: false';
					$html .= 	'},';
					$html .= 	'tooltip: {';
					$html .= 		'formatter: function() {';
					//$html .= 			'return "<b>"+ this.point.name +"</b>: "+ Math.round(this.percentage) +" %";';
					$html .= 			'return "<b>"+ this.point.name +"</b>: "+ numberFormat(this.percentage, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'") +" %";';
					$html .= 		'},';
					$html .= 		'pointFormat: "{series.name}: <b>{point.y}%</b>"';
					$html .= 	'},';
					$html .= 	'plotOptions: {';
					$html .= 		'pie: {';
					//$html .= 		'//size: 80,';
					$html .= 		'allowPointSelect: true,';
					$html .= 		'cursor: "pointer",';
					$html .= 		'dataLabels: {';
					$html .= 			'enabled: false,';
					$html .= 			'format: "<b>{point.name}</b>: {point.percentage:." + decimal_numbers + "f} %",';
					$html .= 			'style: {';
					$html .= 				'color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black",';
					$html .= 				'fontSize: "9px",';
					$html .= 				'distance: -30';
					$html .= 			'},';
					$html .= 			'crop: false';
					$html .= 		'},';
					$html .= 		'showInLegend: true';
					$html .= 		'}';
					$html .= 	'},';
					$html .= 	'legend: {';
					$html .= 		'enabled: true,';
					$html .= 		'itemStyle:{';
					$html .= 			'fontSize: "9px"';
					$html .= 		'}';
					$html .= 	'},';
					
					$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("report").'_'.lang("compromises").'_'.date("Y-m-d");
					
					$html .= 	'exporting: {';
					//$html .= 		'filename: "'. lang("summary_evaluated"). '" - "' .$evaluado["nombre_evaluado"].'",';
					$html .= 		'chartOptions:{';
					$html .= 			'title: {';
					$html .= 				'text:"'.$evaluado["nombre_evaluado"].'"';
					$html .= 			'}';
					$html .= 		'},';
					$html .= 		'filename: "'.$nombre_exportacion.'",';
					$html .= 		'buttons: {';
					$html .= 			'contextButton: {';
					$html .= 				'menuItems: [{';
					$html .= 					'text: "'.lang('export_to_png').'",';
					$html .= 					'onclick: function() {';
					$html .= 						'this.exportChart();';
					$html .= 					'},';
					$html .= 					'separator: false';
					$html .= 				'}]';
					$html .= 			'}';
					$html .= 		'}';
					$html .= 	'},';
					
					
					$html .= 	'colors: [';
					foreach($array_colores as $color) {
						$html .= 	'"'. $color . '",';
					}
					$html .= 	'],';
					$html .= 	'series: [{';
					$html .= 		'name: "Porcentaje",';
					$html .= 		'colorByPoint: true,';
					$html .= 		'data: [';
					foreach($array_nombre_porcentaje as $nombre => $porcentaje){
						$html .= 		'{';
						$html .= 			'name: "'. $nombre.'",';
						$html .= 			'y: '.$porcentaje.'';
						$html .= 		'},';
					}	
					$html .= 		']';
					$html .= 	'}]';
					
					$html .= '});';
				
				}else{
	
					$html .= '$("#grafico_resumen_evaluado_'.$evaluado["id"].'").html("<strong>'.lang("no_information_available").'</strong>").css({"text-align":"center", "vertical-align":"middle", "display":"table-cell"});';
	
				}
				
			}
	
			$html .= '</script>';
			
			
			
			$html .= '<!-- TABLA -->';
			$html .= '<div class="panel-body">';
			$html .= 	'<div class="table-responsive">';
			$html .= 		'<div id="milestone-table_wrapper" class="dataTables_wrapper no-footer">';
			$html .= 			'<table id="tabla_resumen_por_evaluado" class="table table-striped">';
			
			$html .= 				'<thead>';		
			$html .= 					'<tr>';
			$html .= 						'<th rowspan="2" class="text-center" style="vertical-align:middle;">'.lang("compliance_status").'</th>';
			foreach($evaluados_matriz_compromiso as $evaluado) {	
				$html .= 						'<th colspan="2" class="text-center">'.$evaluado["nombre_evaluado"].'</th>';
			}
			$html .= 					'</tr>';		
			$html .= 					'<tr>';
			foreach($evaluados_matriz_compromiso as $evaluado) {
				$html .= 				'<th class="text-center">N°</th>';
				$html .= 				'<th class="text-center">%</th>';	
			}
			$html .= 					'</tr>';		
			$html .= 				'</thead>';
			
			$html .= 				'<tbody>';
			$html .= 					'<tr>';
			$html .= 						'<th class="text-left">'.lang("total_applicable_compromises").'</th>';
			foreach($array_total_compromisos_aplicables_por_evaluado as $total) {
				$html .= 						'<td class="text-right">'.to_number_project_format($total, $id_proyecto).'</td>';
				$html .= 						'<td class="text-right">'.to_number_project_format(100, $id_proyecto).' %</td>';
			}
			$html .= 					'</tr>';
			foreach($result_estados as $estado){
				$html .= 				'<tr>';
				$html .= 					'<td class="text-left">'.$estado["nombre_estado"].'</td>';
				foreach($evaluados_matriz_compromiso as $evaluado) {
					$cantidad = $this->get_quantity_of_status_evaluated_for_compromises($estado["id_estado"], $evaluado["id"], $start_date, $end_date); 
					$porcentaje = $this->get_percentage_of_status_evaluated_for_compromises($cantidad, $estado["id_estado"], $evaluado["id"], $start_date, $end_date);
					$html .= 				'<td class="text-right">'.to_number_project_format($cantidad, $id_proyecto).'</td>';  
					$html .= 				'<td class="text-right">'.to_number_project_format($porcentaje, $id_proyecto).' %</td>';
				}
				$html .= 				'</tr>';	
			}
			$html .= 				'</tbody>';
			
			$html .= 			'</table>';
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			$html .= '<!-- FIN TABLA -->';
			
			
			
			
			
			
		} else {
			
			/*			
			<div class="panel panel-default mb15">
				<div class="panel-body">              
					<div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
						<?php echo lang('the_project').' '.$nombre_proyecto.' '.lang('compromise_matrix_not_enabled'); ?>
					</div>
				</div>	  
			</div>
			*/
			
			$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
			
			$html .= '<div class="panel panel-default mb15">';
			$html .= 	'<div class="panel-body">';
			$html .= 		'<div class="app-alert alert alert-warning alert-dismissible mb0 col-md-12" style="float: left;">';
			$html .= 			lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_matrix_not_enabled');
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			
		}
		
		$html .= "</div>";
		$html .= '<!-- FIN UN GRÁFICO POR CADA EVALUADO -->';
		
		return $html;
		
	}
	
	function get_quantity_of_status_evaluated_for_compromises($id_estado, $id_evaluado, $start_date, $end_date){		
		
		$cantidad = 0;
		$evaluaciones = $this->Compromises_rca_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->result_array();

		foreach($evaluaciones as $evaluacion){
			if( ($evaluacion["fecha_evaluacion"] >= $start_date) && ($evaluacion["fecha_evaluacion"] <= $end_date) ){					
				$cantidad++;
			} 	
		}
		
		return $cantidad;
		
		//$cantidad = $this->Compromises_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->row();
		//return $cantidad->cantidad;		
	}
	
	function get_percentage_of_status_evaluated_for_compromises($cantidad_compromisos, $id_estado, $id_evaluado, $start_date, $end_date){		
		
		$compromisos_por_evaluado = $this->Compromises_rca_model->get_total_applicable_compromises_by_evaluated($id_evaluado)->result_array();
		$total_compromisos_por_evaluado = 0;
		
		foreach($compromisos_por_evaluado as $cpe){
			if( ($cpe["fecha_evaluacion"] >= $start_date) && ($cpe["fecha_evaluacion"] <= $end_date) ){					
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
	
	function get_color_of_status_for_compromises($id_estado){
		$estado = $this->Compromises_compliance_status_model->get_one($id_estado);
		return $estado->color;
	}
	
	function get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_modulo_consumos = 2; // Registros Ambientales
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$campos_unidad_residuo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Residuo"))->result();
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
	
		$array_id_categorias_valores_volumen = array();
		$array_id_categorias_valores_volumen_a = array();
		$array_id_categorias_valores_volumen_d = array();
		
		foreach($campos_unidad_residuo as $formulario_campo){
			$id_campo = $formulario_campo->id_campo;
			
			$datos_unidad_formulario = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_unidad_formulario["tipo_unidad_id"];
			$id_unidad = $datos_unidad_formulario["unidad_id"];
			
			// SI ES VOLUMEN // Y UNIDAD DE LA CONFIGURACION
			//if($id_tipo_unidad == 2/* && $id_unidad == $id_unidad_volumen*/ || $tipo_unidad_id == 2){
			if($id_tipo_unidad == 2){
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($categorias as $cat){
					
					//MOSTRAR CATEGORÍAS DE MATERIALES SEGÚN CONFIGURACIÓN DE REPORTE
					$mostrar_categorias = true;
					foreach($array_report_config_materials as $report_config_material){
						if($cat->id_material == $report_config_material["id"]){
							if(!$report_config_material["estado"]){
								$mostrar_categorias = false;
								break;
							}
							
						}
					}
					
					if(!$mostrar_categorias){continue;}
					
					// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
					$array_id_categorias_valores_volumen[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_volumen_d[$cat->id_categoria][] = 0;
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						$datos_decoded = json_decode($ef->datos, true);
						
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date)) {
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							if($id_unidad == $id_unidad_volumen){

								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor;

							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $this->Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_volumen
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor * $valor_transformacion;
								$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor * $valor_transformacion;
								
							}
						}else{// ACUMULADOS
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
						
								// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
								if($id_unidad == $id_unidad_volumen){
	
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor;
	
								}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
											"id_unidad_origen" => $id_unidad,
											"id_unidad_destino" => $id_unidad_volumen
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									$datos_decoded = json_decode($ef->datos, true);
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = $valor * $valor_transformacion;
									
								}
							}
							
							
						}//end if date	
						
						
					}
					
					// CONSULTA CONFIGURACIÓN DE ALERTAS
					// Traigo la unidad según configuración de reportes
					$unit_id_on_config = $this->Reports_units_settings_model->get_one_where(array(
						"id_cliente" => $id_cliente, 
						"id_proyecto" => $id_proyecto, 
						"id_tipo_unidad" => $id_tipo_unidad, 
						"deleted" => 0
					))->id_unidad;
					
					// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
					$options = array(
						'id_client' => $id_cliente, 
						'id_project' => $id_proyecto,
						'id_client_module' => $id_modulo_consumos,
						'alert_config' => array(
							'id_categoria' => $id_categoria,
							'id_tipo_unidad' => $id_tipo_unidad,
							'id_unidad' => $unit_id_on_config
						)
					);
					
					$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
					$alert_config = json_decode($declarado->alert_config, TRUE);
					$threshold_value = (int)$alert_config["threshold_value"];
		
					if($threshold_value){
		
						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => $id_tipo_unidad,
								"id_unidad_origen" => $unit_id_on_config,
								"id_unidad_destino" => $id_unidad_volumen
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;
		
						$cant_declarado = $threshold_value * $valor_transformacion;
					}else{
						$cant_declarado = 0;
					}
					// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
					
					$array_id_categorias_valores_volumen_d[$id_categoria][] = $cant_declarado;
					
				}
			}
		}
		
		return array(
			"reportados" => $array_id_categorias_valores_volumen, 
			"acumulados" => $array_id_categorias_valores_volumen_a, 
			"declarados" => $array_id_categorias_valores_volumen_d, 
		);
	}
	
	function get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_modulo_consumos = 2; // Registros Ambientales
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_consumo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Residuo")->result();	
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Residuo"))->result();
		$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		
		$array_id_categorias_valores_masa = array();
		$array_id_categorias_valores_masa_a = array();
		$array_id_categorias_valores_masa_d = array();
		
		foreach($campos_unidad_consumo as $formulario_campo){
			
			$id_campo = $formulario_campo->id_campo;
			/*
			$datos_campo = json_decode($formulario_campo->opciones, true);
			$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
			$id_unidad = $datos_campo[0]["id_unidad"];
			*/
			$datos_unidad_formulario = json_decode($formulario_campo->unidad, true);
			$id_tipo_unidad = $datos_unidad_formulario["tipo_unidad_id"];
			$id_unidad = $datos_unidad_formulario["unidad_id"];
			
			//if($id_tipo_unidad == 1/* && $id_unidad == $id_unidad_volumen*/ || $tipo_unidad_id == 1){
			if($id_tipo_unidad == 1){
				$id_formulario = $formulario_campo->id;
				$categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $id_formulario, "deleted" => 0))->result();
				// POR CADA CATEGORIA DEL FORMULARIO
				foreach($categorias as $cat){
					
					//MOSTRAR CATEGORÍAS DE MATERIALES SEGÚN CONFIGURACIÓN DE REPORTE
					$mostrar_categorias = true;
					foreach($array_report_config_materials as $report_config_material){
						if($cat->id_material == $report_config_material["id"]){
							if(!$report_config_material["estado"]){
								$mostrar_categorias = false;
								break;
							}
							
						}
					}
					
					if(!$mostrar_categorias){continue;}
					
					// FORZO A QUE APAREZCA LA CATEGORIA SI O SI
					$array_id_categorias_valores_masa[$cat->id_categoria][] = 0;
					$array_id_categorias_valores_masa_a[$cat->id_categoria][] = 0;
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
					
						$datos_decoded = json_decode($ef->datos, true);
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date)) {
						
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							/*
							if($id_unidad == $id_unidad_masa){
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								
							}
							*/
							if($id_unidad == $id_unidad_masa){
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor;
								$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor;
								
							}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
								$fila_conversion = $this->Conversion_model->get_one_where(
									array(
										"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
										"id_unidad_origen" => $id_unidad,
										"id_unidad_destino" => $id_unidad_masa
									)
								);
								$valor_transformacion = $fila_conversion->transformacion;
								
								$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								//$valor = $datos_decoded[$id_campo];
								$array_id_categorias_valores_masa[$cat->id_categoria][] = $valor * $valor_transformacion;
								$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor * $valor_transformacion;
							}
						}else{// ACUMULADOS
						
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
								
								// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
								if($id_unidad == $id_unidad_masa){
									$valor = $datos_decoded["unidad_residuo"];
									$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor;
									
								}else{// SI LA UNIDAD DEL ELEMENTO NO ES LA MISMA, LA CONVIERTO A LA DE LA CONFIGURACION Y LA INCORPORO
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad,// VA A SER IGUAL A 2 (VOLUMEN)
											"id_unidad_origen" => $id_unidad,
											"id_unidad_destino" => $id_unidad_masa
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									$datos_decoded = json_decode($ef->datos, true);
									$valor = $datos_decoded["unidad_residuo"];
									//$valor = $datos_decoded[$id_campo];
									$array_id_categorias_valores_masa_a[$cat->id_categoria][] = $valor * $valor_transformacion;
									
								}
								
							}
							
							
						}//end if date	
					}
					
					// CONSULTA CONFIGURACIÓN DE ALERTAS
					// Traigo la unidad según configuración de reportes
					$unit_id_on_config = $this->Reports_units_settings_model->get_one_where(array(
						"id_cliente" => $id_cliente, 
						"id_proyecto" => $id_proyecto, 
						"id_tipo_unidad" => $id_tipo_unidad, 
						"deleted" => 0
					))->id_unidad;
					
					// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
					$options = array(
						'id_client' => $id_cliente, 
						'id_project' => $id_proyecto,
						'id_client_module' => $id_modulo_consumos,
						'alert_config' => array(
							'id_categoria' => $id_categoria,
							'id_tipo_unidad' => $id_tipo_unidad,
							'id_unidad' => $unit_id_on_config
						)
					);
					
					$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
					$alert_config = json_decode($declarado->alert_config, TRUE);
					$threshold_value = (int)$alert_config["threshold_value"];
		
					if($threshold_value){
		
						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => $id_tipo_unidad,
								"id_unidad_origen" => $unit_id_on_config,
								"id_unidad_destino" => $id_unidad_masa
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;
		
						$cant_declarado = $threshold_value * $valor_transformacion;
					}else{
						$cant_declarado = 0;
					}
					// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
					
					$array_id_categorias_valores_masa_d[$id_categoria][] = $cant_declarado;
					
				}
			}
		}
		
		return array(
			"reportados" => $array_id_categorias_valores_masa, 
			"acumulados" => $array_id_categorias_valores_masa_a, 
			"declarados" => $array_id_categorias_valores_masa_d, 
		);
	}
	
	function get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$object = new StdClass;
		
		$array_id_categorias_valores_volumen_total = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_volumen = $array_id_categorias_valores_volumen_total["reportados"];
		$array_id_categorias_valores_volumen_a = $array_id_categorias_valores_volumen_total["acumulados"];
		
		$array_grafico_residuos_volumen_categories = array();
		$array_grafico_residuos_volumen_data = array();
		$array_grafico_residuos_volumen_data_a = array();
		$array_grafico_residuos_volumen_data_d = array();
		
		$id_modulo_consumos = 2; // Registros ambientales
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		
		foreach ($array_id_categorias_valores_volumen as $id_categoria => $arreglo_valores){
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			$array_grafico_residuos_volumen_categories[] = $nombre_categoria;
			$array_grafico_residuos_volumen_data[] = array_sum($arreglo_valores);
		}
		
		foreach ($array_id_categorias_valores_volumen_a as $id_categoria => $arreglo_valores){
			$array_grafico_residuos_volumen_data_a[] = array_sum($arreglo_valores);
			
			// CONSULTA CONFIGURACIÓN DE ALERTAS 
			// Traigo la configuración de alertas del $id_cliente, $id_proyecto, $id_modulo_consumos (2 - Registros Ambientales)
			/*$options = array(
				'id_client' => $id_cliente, 
				'id_project' => $id_proyecto,
				'id_client_module' => $id_modulo_consumos,
				'alert_config' => array(
					'id_categoria' => $id_categoria,
					'id_tipo_unidad' => 2,
				)
			);
			
			$declarado = $this->AYN_Alert_projects_model->get_alert_projects_config($options)->row();
			$alert_config = json_decode($declarado->alert_config, TRUE);
			$threshold_value = (int)$alert_config["threshold_value"];
			$id_unidad_destino = (int)$alert_config["id_unidad"];
			
			if($threshold_value){

				$fila_conversion = $this->Conversion_model->get_one_where(
					array(
						"id_tipo_unidad" => 2,// 2 (VOLUMEN)
						"id_unidad_origen" => $id_unidad_volumen, // Unidad según configuración de reportes
						"id_unidad_destino" => $id_unidad_destino
					)
				);
				$valor_transformacion = $fila_conversion->transformacion;

				$cant_declarado = $threshold_value * $valor_transformacion;
			}else{
				$cant_declarado = 0;
			}
			
			$array_grafico_residuos_volumen_data_d[] = $cant_declarado;*/
			// FIN CONSULTA CONFIGURACIÓN DE ALERTAS
		}
		
		$object->array_grafico_residuos_volumen_categories = $array_grafico_residuos_volumen_categories;
		$object->array_grafico_residuos_volumen_data = $array_grafico_residuos_volumen_data;
		$object->array_grafico_residuos_volumen_data_a = $array_grafico_residuos_volumen_data_a;
		$object->array_grafico_residuos_volumen_data_d = $array_grafico_residuos_volumen_data_d;
		
		return $object;
		
	}
	
	function get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date){
	
		$object = new StdClass;
		
		$array_id_categorias_valores_masa_total = $this->get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_masa = $array_id_categorias_valores_masa_total["reportados"];
		$array_id_categorias_valores_masa_a = $array_id_categorias_valores_masa_total["acumulados"];
		
		$array_grafico_residuos_masa_categories = array();
		$array_grafico_residuos_masa_data = array();
		$array_grafico_residuos_masa_data_a = array();
		
		foreach ($array_id_categorias_valores_masa as $id_categoria => $arreglo_valores){
			$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
			if($row_alias->alias){
				$nombre_categoria = $row_alias->alias;
			}else{
				$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
				$nombre_categoria = $row_categoria->nombre;
			}
			
			$array_grafico_residuos_masa_categories[] = $nombre_categoria;
			$array_grafico_residuos_masa_data[] = array_sum($arreglo_valores);
		}
		
		foreach ($array_id_categorias_valores_masa_a as $id_categoria => $arreglo_valores){
			$array_grafico_residuos_masa_data_a[] = array_sum($arreglo_valores);
			
		}
		
		$object->array_grafico_residuos_masa_categories = $array_grafico_residuos_masa_categories;
		$object->array_grafico_residuos_masa_data = $array_grafico_residuos_masa_data;
		$object->array_grafico_residuos_masa_data_a = $array_grafico_residuos_masa_data_a;
		
		return $object;
	
	}
/*	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_usuario = $this->login_user->id;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
				
		$id_fase_proyecto = $this->Project_rel_phases_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		))->id_fase;							
		
		$nombre_fase = $this->Phases_model->get_one($id_fase_proyecto)->nombre;
		
		// Configuración de reporte del proyecto
		$report_config = $this->Reports_configuration_model->get_one_where(array(
			"id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0
		));
		
		// Disponibilidad de módulos y perfilamiento de compromisos y permisos para mostrar o no mostrar secciones
		$disponibilidad_modulo_compromisos = $this->Module_availability_model->get_one_where(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"id_modulo_cliente" => 6,
			"deleted" => 0
		))->available;
		
		$disponibilidad_modulo_permisos = $this->Module_availability_model->get_one_where(array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"id_modulo_cliente" => 7,
			"deleted" => 0
		))->available;	
		
		$perfil_puede_ver_compromisos = $this->profile_access($this->session->user_id, 6, 3, "ver");
		$perfil_puede_ver_permisos = $this->profile_access($this->session->user_id, 7, 5, "ver");

		$usuario = $this->Users_model->get_one($id_usuario);
		
		if($info_cliente->logo){
			$url_logo_cliente = "files/mimasoft_files/client_".$id_cliente."/".$info_cliente->logo.".png";
		} else {
			$url_logo_cliente = "files/system/default-site-logo.png";
		}
		
		$pais = $this->Countries_model->get_one($info_proyecto->id_pais);
		$ubicacion = (($info_proyecto->state)?$info_proyecto->state.', ':'').(($info_proyecto->city)?$info_proyecto->city.', ':'').(($pais->nombre)?$pais->nombre:'');
		$fecha_desde = $this->input->post("fecha_desde");
		$fecha_hasta = $this->input->post("fecha_hasta");
		$graficos_consumo = $this->input->post("graficos_consumo");
		$graficos_residuo = $this->input->post("graficos_residuo");
		$graficos_resumen_evaluado_compromisos = $this->input->post("graficos_resumen_evaluado_compromisos");
		//var_dump($graficos_resumen_evaluado_compromisos);
		//exit();
		$graficos_resumen_evaluado_permisos = $this->input->post("graficos_resumen_evaluado_permisos");
		
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0
		))->id_unidad;
		
		$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0
		))->id_unidad;
		
		$id_unidad_energia = $this->Reports_units_settings_model->get_one_where(array(
			"id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0
		))->id_unidad;

		//var_dump($fecha_desde);
		//var_dump($fecha_hasta);
		//var_dump($graficos_consumo);
		//var_dump($graficos_residuo);
		//var_dump($graficos_resumen_evaluado_compromisos);
		//var_dump($graficos_resumen_evaluado_permisos);
		//exit();
		
		$view_data["Reports_controller"] = $this;
		$view_data["fecha_desde"] = $fecha_desde;
		$view_data["fecha_hasta"] = $fecha_hasta;
		$view_data["info_cliente"] = $info_cliente;
		$view_data["info_proyecto"] = $info_proyecto;
		$view_data["nombre_fase"] = $nombre_fase;
		$view_data["ubicacion"] = $ubicacion;
		$view_data["usuario"] = $usuario;
		$view_data["logo_cliente"] = $url_logo_cliente;
		$view_data["report_config"] = $report_config;
		
		$view_data["unidad_volumen"] = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
		$view_data["unidad_volumen_nombre_real"] = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
		$view_data["unidad_masa"] = $this->Unity_model->get_one($id_unidad_masa)->nombre;
		$view_data["unidad_masa_nombre_real"] = $this->Unity_model->get_one($id_unidad_masa)->nombre_real;
		$view_data["unidad_energia"] = $this->Unity_model->get_one($id_unidad_energia)->nombre;
		$view_data["unidad_energia_nombre_real"] = $this->Unity_model->get_one($id_unidad_energia)->nombre_real;
		
		// Datos Consumos
		$tabla_consumo_volumen = $this->get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["tabla_consumo_volumen_reportados"] = $tabla_consumo_volumen["reportados"];
		$view_data["tabla_consumo_volumen_acumulados"] = $tabla_consumo_volumen["acumulados"];
		
		$tabla_consumo_masa = $this->get_datos_tabla_consumo_masa($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["tabla_consumo_masa_reportados"] = $tabla_consumo_masa["reportados"];
		$view_data["tabla_consumo_masa_acumulados"] = $tabla_consumo_masa["acumulados"];
		
		$tabla_consumo_energia = $this->get_datos_tabla_consumo_energia($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["tabla_consumo_energia_reportados"] = $tabla_consumo_energia["reportados"];
		$view_data["tabla_consumo_energia_acumulados"] = $tabla_consumo_energia["acumulados"];
		
		$view_data["graficos_consumo"] = $graficos_consumo;
		
		// Datos Residuo
		$tabla_residuo_volumen = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["tabla_residuo_volumen_reportados"] = $tabla_residuo_volumen["reportados"];
		$view_data["tabla_residuo_volumen_acumulados"] = $tabla_residuo_volumen["acumulados"];
		
		$tabla_residuo_masa = $this->get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["tabla_residuo_masa_reportados"] = $tabla_residuo_masa["reportados"];
		$view_data["tabla_residuo_masa_acumulados"] = $tabla_residuo_masa["acumulados"];
		
		$view_data["graficos_residuo"] = $graficos_residuo;
		
		// Datos Compromisos
		$view_data["disponibilidad_modulo_compromisos"] = $disponibilidad_modulo_compromisos;
		$view_data["perfil_puede_ver_compromisos"] = $perfil_puede_ver_compromisos;
		
		$view_data["graficos_resumen_evaluado_compromisos"] = $graficos_resumen_evaluado_compromisos;
		if($report_config->compromises){
			if($disponibilidad_modulo_compromisos == 1){
				if($perfil_puede_ver_compromisos == 1){
					$tabla_compromisos = $this->get_compromises_summary_by_evaluated_for_pdf($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
					$view_data["contenido_tabla_compromisos"] = $tabla_compromisos;
				}			
			}
		}
		
		// Datos Permisos
		$view_data["disponibilidad_modulo_permisos"] = $disponibilidad_modulo_permisos;
		$view_data["perfil_puede_ver_permisos"] = $perfil_puede_ver_permisos;
		
		$view_data["graficos_resumen_evaluado_permisos"] = $graficos_resumen_evaluado_permisos;
		if($report_config->permittings){
			if($disponibilidad_modulo_permisos == 1){
				if($perfil_puede_ver_permisos == 1){
					$tabla_permisos = $this->get_permitting_summary_by_evaluated_for_pdf($id_cliente, $id_proyecto, $fecha_desde, $fecha_hasta);
					$view_data["contenido_tabla_permisos"] = $tabla_permisos;
				}			
			}
		}
		
		//var_dump($disponibilidad_modulo_permisos);
		//var_dump($perfil_puede_ver_permisos);
		//var_dump($graficos_resumen_evaluado_permisos);
		//exit();
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("report")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("report")."_".date('Y-m-d'));
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
		      
		//$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96); 
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//$view_data["fontawesome"] = $fontawesome;
		//$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('reports/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("report")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;
		
	}
	*/	
	
	function get_pdf(){
		
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$view_data["start_date"] = $start_date;
		$view_data["end_date"] = $end_date;
		
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		$view_data["project_info"] = $project_info;
		$view_data["client_info"] = $client_info;
		$view_data["autorizacion_ambiental"] = (($project_info->environmental_authorization)?$project_info->environmental_authorization:'-');
		
		$technology = $this->Subindustries_model->get_one($project_info->id_tecnologia);
		
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		$id_unidad_masa = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		$id_unidad_energia = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $client_info->id, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 4, "deleted" => 0))->id_unidad;
		$view_data["id_unidad_volumen"] = $id_unidad_volumen;
		$view_data["id_unidad_masa"] = $id_unidad_masa;
		$view_data["id_unidad_energia"] = $id_unidad_energia;
		
		$view_data["unidad_volumen"] = $this->Unity_model->get_one($id_unidad_volumen)->nombre;
		$view_data["unidad_volumen_nombre_real"] = $this->Unity_model->get_one($id_unidad_volumen)->nombre_real;
		$view_data["unidad_masa"] = $this->Unity_model->get_one($id_unidad_masa)->nombre;
		$view_data["unidad_masa_nombre_real"] = $this->Unity_model->get_one($id_unidad_masa)->nombre_real;
		$view_data["unidad_energia"] = $this->Unity_model->get_one($id_unidad_energia)->nombre;
		$view_data["unidad_energia_nombre_real"] = $this->Unity_model->get_one($id_unidad_energia)->nombre_real;
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		// Datos del usuario
		$id_usuario = $this->session->user_id;
		$usuario_info = $this->Users_model->get_one($id_usuario);
		$view_data["usuario_info"] = $usuario_info;
		
		// Fila de configuración de 'reportes' del proyecto (reports_configuration_settings)
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'deleted' => 0
		);
		
		// Configuración de reporte del proyecto
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		
		if($report_config->project_data){
			
			// CONSIGO TODA LA INFORMACION PRIMERO
			$color_sitio = $client_info->color_sitio;
			$nombre_proyecto = $project_info->title;
			$pais = $this->Countries_model->get_one($project_info->id_pais);
			$ubicacion_proyecto = (($project_info->state)?$project_info->state.', ':'').(($project_info->city)?$project_info->city.', ':'').(($pais->nombre)?$pais->nombre:'');
			$view_data["ubicacion_proyecto"] = $ubicacion_proyecto;
			$rut = (($project_info->client_label_rut)?$project_info->client_label_rut:'-');

			$proyecto_fase = $this->Project_rel_phases_model->get_one_where(
				array(
					"id_proyecto" => $id_proyecto, 
					"deleted" => 0
				)
			);
			
			if($proyecto_fase->id){
				$proyecto_fase = $this->Phases_model->get_one($proyecto_fase->id_fase);
				$etapa_proyecto = lang($proyecto_fase->nombre_lang);
			}else{
				$etapa_proyecto = '-';
			}
			$view_data["etapa_proyecto"] = $etapa_proyecto;
			
			$n_informe = $client_info->rut;
			$fecha_reporte = date('Y-m-d');
			$inicio_consulta = $start_date;
			$termino_consulta = $end_date;
			
			// Reviso si puede visualizar ANTECEDENTES DEL PROYECTO
			$view_data["puede_ver_antecedentes_proyecto"] = $report_config->project_data;

		}
		
		// COMPROMISOS AMBIENTALES - RCA
		// Reviso si puede tiene acceso a compromisos ambientales rca, por módulo y perfil
		$id_submodulo_dashboard_compromisos = 3;
		$id_modulo_compromisos = 6;
		
		$tiene_configuracion_disponible = $report_config->rca_compromises;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_compromisos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_compromisos, $id_submodulo_dashboard_compromisos, "ver");
		$puede_ver_compromisos_rca = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_compromisos_rca){
			
			$id_compromiso_rca = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
			if($id_compromiso_rca){
				
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
				$array_total_por_evaluado = array();
				$array_total_por_estado = array();
				$array_compromisos_evaluaciones_no_cumple = array();
				foreach($estados_cliente as $estado) {
					
					$id_estado = $estado->id;
					
					if($estado->categoria == "No Aplica"){
						continue;
					}
					$array_estados_evaluados[$estado->id] = array(
						"nombre_estado" => $estado->nombre_estado,
						"categoria" => $estado->categoria,
						"color" => $estado->color,
						"evaluados" => array()
					);
					
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
								$cant++;
								
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
				}
				
			}
			
			$view_data["puede_ver_compromisos_rca"] = $puede_ver_compromisos_rca;
			$view_data["evaluados_matriz_compromiso"] = $evaluados;
			$view_data["array_total_por_evaluado"] = $array_total_por_evaluado;
			$view_data["array_estados_evaluados_rca"] = $array_estados_evaluados;
			$view_data["estados"] = $estados;
			$view_data["array_compromisos_evaluaciones_no_cumple"] = $array_compromisos_evaluaciones_no_cumple;
			
			$grafico_cumplimientos_totales = $this->input->post("grafico_cumplimientos_totales");
			$view_data["grafico_cumplimientos_totales"] = $grafico_cumplimientos_totales;
			
			//$view_data["totales_rca"] = $totales_rca;
			
		}
		
		// Reviso si puede visualizar la sección de compromisos reportables
		$id_submodulo_dashboard_compromisos = 3;
		$id_modulo_compromisos = 6;
		
		$tiene_configuracion_disponible = $report_config->reportable_compromises;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_compromisos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_compromisos, $id_submodulo_dashboard_compromisos, "ver");
		$puede_ver_compromisos_reportables = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_compromisos_reportables) {
			
			$id_compromiso_reportables = $this->Compromises_reportables_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
			if($id_compromiso_reportables){
				
				
				// COMPROMISOS AMBIENTALES - REPORTABLES

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
				
				$view_data["puede_ver_compromisos_reportables"] = $puede_ver_compromisos_reportables;
				$view_data["array_estados_evaluados_reportables"] = $array_estados_evaluados;
				$view_data["total_evaluado"] = $total_evaluado;
				$view_data["array_compromisos_reportables_evaluaciones_no_cumple"] = $array_compromisos_reportables_evaluaciones_no_cumple;
				
				$grafico_cumplimientos_reportables = $this->input->post("grafico_cumplimientos_reportables");
				$view_data["grafico_cumplimientos_reportables"] = $grafico_cumplimientos_reportables;
				
			}
			
		}
		
		
		// CONSUMOS
		// Reviso si puede visualizar la sección de CONSUMOS
		$id_submodulo_consumos = 0;
		$id_modulo_consumos = 2;
		
		$tiene_configuracion_disponible = $report_config->consumptions;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_consumos,
			'deleted' => 0
		);
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_consumos, $id_submodulo_consumos, "ver");
		$puede_ver_consumos = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if($puede_ver_consumos) {
			if($report_config->consumptions){
				
				$tabla_consumo_volumen = $this->get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_volumen_reportados = $tabla_consumo_volumen["reportados"];
				$tabla_consumo_volumen_acumulados = $tabla_consumo_volumen["acumulados"];

				$tabla_consumo_masa = $this->get_datos_tabla_consumo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_masa_reportados = $tabla_consumo_masa["reportados"];
				$tabla_consumo_masa_acumulados = $tabla_consumo_masa["acumulados"];

				$tabla_consumo_energia = $this->get_datos_tabla_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_consumo_energia_reportados = $tabla_consumo_energia["reportados"];
				$tabla_consumo_energia_acumulados = $tabla_consumo_energia["acumulados"];
				
				$view_data["puede_ver_consumos"] = $puede_ver_consumos;
				$view_data["tabla_consumo_volumen_reportados"] = $tabla_consumo_volumen_reportados;
				$view_data["tabla_consumo_volumen_acumulados"] = $tabla_consumo_volumen_acumulados;
				$view_data["tabla_consumo_masa_reportados"] = $tabla_consumo_masa_reportados;
				$view_data["tabla_consumo_masa_acumulados"] = $tabla_consumo_masa_acumulados;
				$view_data["tabla_consumo_energia_reportados"] = $tabla_consumo_energia_reportados;
				$view_data["tabla_consumo_energia_acumulados"] = $tabla_consumo_energia_acumulados;
				
				$graficos_consumo = $this->input->post("graficos_consumo");
				$grafico_consumo_volumen = $graficos_consumo["consumo_volumen"];
				$view_data["grafico_consumo_volumen"] = $grafico_consumo_volumen;
				$grafico_consumo_masa = $graficos_consumo["consumo_masa"];
				$view_data["grafico_consumo_masa"] = $grafico_consumo_masa;
				$grafico_consumo_energia = $graficos_consumo["consumo_energia"];
				$view_data["grafico_consumo_energia"] = $grafico_consumo_energia;

			}
		}
		
		
		// RESIDUOS
		// Reviso si puede visualizar la sección de RESIDUOS
		$id_submodulo_residuos = 0;
		$id_modulo_residuos = 2;
		
		$tiene_configuracion_disponible = $report_config->waste;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_residuos,
			'deleted' => 0
		);
		
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_residuos, $id_submodulo_residuos, "ver");
		$puede_ver_residuos = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		
		if ($puede_ver_residuos) {
			if($report_config->waste){
			
				$tabla_residuo_volumen = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_residuo_volumen_reportados = $tabla_residuo_volumen["reportados"];
				$tabla_residuo_volumen_acumulados = $tabla_residuo_volumen["acumulados"];
				
				$tabla_residuo_masa = $this->get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
				$tabla_residuo_masa_reportados = $tabla_residuo_masa["reportados"];
				$tabla_residuo_masa_acumulados = $tabla_residuo_masa["acumulados"];
				
				$view_data["puede_ver_residuos"] = $puede_ver_residuos;
				$view_data["tabla_residuo_volumen_reportados"] = $tabla_residuo_volumen_reportados;
				$view_data["tabla_residuo_volumen_acumulados"] = $tabla_residuo_volumen_acumulados;
				$view_data["tabla_residuo_masa_reportados"] = $tabla_residuo_masa_reportados;
				$view_data["tabla_residuo_masa_acumulados"] = $tabla_residuo_masa_acumulados;
				
				$graficos_residuo = $this->input->post("graficos_residuo");
				$grafico_residuo_volumen = $graficos_residuo["residuo_volumen"];
				$view_data["grafico_residuo_volumen"] = $grafico_residuo_volumen;
				$grafico_residuo_masa = $graficos_residuo["residuo_masa"];
				$view_data["grafico_residuo_masa"] = $grafico_residuo_masa;

			}
		}
		
		
		// PERMISOS
		// Reviso si puede visualizar la sección de PERMISOS
		$id_submodulo_permittings = 5;
		$id_modulo_permittings = 7;
		
		$tiene_configuracion_disponible = $report_config->permittings;
		
		$filtro = array(
			'id_cliente' => $id_cliente,
			'id_proyecto' => $id_proyecto,
			'id_modulo_cliente' => $id_modulo_permittings,
			'deleted' => 0
		);
		
		$tiene_modulo_disponible = $this->Module_availability_model->get_one_where($filtro)->available;
		$tiene_acceso = $this->profile_access($id_usuario, $id_modulo_permittings, $id_submodulo_permittings, "ver");
		$puede_ver_permittings = $tiene_configuracion_disponible && $tiene_modulo_disponible && $tiene_acceso == 1;
		$id_permiso = $this->Permitting_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		
		if ($puede_ver_permittings) {
			if($report_config->permittings){
				
				// EVALUADOS
				$evaluados = $this->Evaluated_permitting_model->get_all_where(
					array(
						"id_permiso" => $id_permiso, 
						"deleted" => 0
					)
				)->result();
				
				// ESTADOS
				$estados_cliente = $this->Permitting_procedure_status_model->get_details(
					array(
						"id_cliente" => $id_cliente,
					)
				)->result();
				
				// ULTIMAS EVALUACIONES
				$ultimas_evaluaciones = $this->Permitting_procedure_evaluation_model->get_last_evaluations_of_project(
					$id_proyecto, 
					$end_date
				)->result();
				
				// PROCESAR TABLA
				$array_estados_evaluados = array();
				$array_total_por_evaluado = array();
				$array_total_por_estado = array();
				$array_permisos_evaluaciones_no_cumple = array();
				foreach($estados_cliente as $estado) {
					
					$id_estado = $estado->id;
					
					if($estado->categoria == "No Aplica"){
						continue;
					}
					$array_estados_evaluados[$estado->id] = array(
						"nombre_estado" => $estado->nombre_estado,
						"categoria" => $estado->categoria,
						"color" => $estado->color,
						"evaluados" => array()
					);
					
					foreach($evaluados as $evaluado) {
						
						$id_evaluado = $evaluado->id;
						$cant = 0;
						
						$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado] = array("cant" => 0, "evaluaciones" => array());
						
						foreach($ultimas_evaluaciones as $ultima_evaluacion) {
							if(
								$ultima_evaluacion->id_estados_tramitacion_permisos == $id_estado && 
								$ultima_evaluacion->id_evaluado == $id_evaluado
							){
								$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["evaluaciones"][] = $ultima_evaluacion;
								$cant++;
								
								if($estado->categoria == "No Cumple"){
									$array_permisos_evaluaciones_no_cumple[] = $ultima_evaluacion;
								}
							}
						}
						
						$array_estados_evaluados[$id_estado]["evaluados"][$id_evaluado]["cant"] = $cant;
						$array_total_por_evaluado[$id_evaluado][] = $cant;
						$array_total_por_estado[$id_estado][] = $cant;
					}
				}
				
				$view_data["puede_ver_permittings"] = $puede_ver_permittings;
				$view_data["evaluados_matriz_permiso"] = $evaluados;
				$view_data["array_estados_evaluados_permiso"] = $array_estados_evaluados;
				$view_data["totales_permisos"] = $totales_permisos;
				$view_data["array_total_por_evaluado_permiso"] = $array_total_por_evaluado;
				$view_data["array_permisos_evaluaciones_no_cumple"] = $array_permisos_evaluaciones_no_cumple;
				
				$grafico_cumplimientos_totales_permisos = $this->input->post("grafico_cumplimientos_totales_permisos");
				$view_data["grafico_cumplimientos_totales_permisos"] = $grafico_cumplimientos_totales_permisos;
				
					
			}
		}
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($client_info->sigla."_".$project_info->sigla."_".lang("report")."_".date('Y-m-d'));
        $this->pdf->SetSubject($client_info->sigla."_".$project_info->sigla."_".lang("report")."_".date('Y-m-d'));
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
		      
		//$fontawesome = TCPDF_FONTS::addTTFfont('assets/js/font-awesome/fonts/fontawesome-webfont.ttf', 'TrueTypeUnicode', '', 96); 
		
		$this->pdf->AddPage();

		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//$view_data["fontawesome"] = $fontawesome;
		//$view_data["pdf"] = $this->pdf;
		$html = $this->load->view('reports/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $client_info->sigla."_".$project_info->sigla."_".lang("report")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;
		
	}
	
	private function get_compromises_summary_by_evaluated_for_pdf($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_compromiso = $this->Compromises_rca_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		$result = array();
		
		if($id_compromiso){
			
			$evaluados_matriz_compromiso = $this->Evaluated_rca_compromises_model->get_all_where(array(
				"id_compromiso" => $id_compromiso, "deleted" => 0
			))->result_array();
			$array_total_compromisos_aplicables_por_evaluado = array();
			
			foreach($evaluados_matriz_compromiso as $evaluado){
		
				$compromisos_por_evaluado = $this->Compromises_rca_model->get_total_applicable_compromises_by_evaluated($evaluado["id"])->result_array();
				$total_compromisos_por_evaluado = 0;
				
				foreach($compromisos_por_evaluado as $cpe){
					if( ($cpe["fecha_evaluacion"] >= $start_date) && ($cpe["fecha_evaluacion"] <= $end_date) ){					
						$total_compromisos_por_evaluado++;
					} 	
				}
		
				$array_total_compromisos_aplicables_por_evaluado[$evaluado["id"]] = $total_compromisos_por_evaluado;
			}
			
			// Listado de estados de categoría Cumple y No Cumple que están siendo utilizados en alguna evaluación
			$estados = $this->Compromises_rca_model->get_status_in_evaluations($id_cliente, $id_proyecto)->result_array();
			$array_estados_en_evaluaciones = array();
			
			foreach($estados as $estado){
				if( ($estado["fecha_evaluacion"] >= $start_date) && ($estado["fecha_evaluacion"] <= $end_date) ){					
					$array_estados_en_evaluaciones[] = $estado;
				} 
			}
			
			// Se agrupa $array_estados_en_evaluaciones por id_estado
			$result_estados = array();
			foreach($array_estados_en_evaluaciones as $atcee){
				$repeat = false;
				for($i = 0; $i < count($result_estados); $i++){
					if($result_estados[$i]['id_estado'] == $atcee['id_estado']){
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_estados[] = array('nombre_estado' =>  $atcee['nombre_estado'], 'id_estado' => $atcee['id_estado']);
				}		
			}

			$result["evaluados_matriz_compromiso"] = $evaluados_matriz_compromiso;
			$result["array_total_compromisos_aplicables_por_evaluado"] = $array_total_compromisos_aplicables_por_evaluado;
			$result["result_estados"] = $result_estados;
			
		} else {
			$result["matriz_no_disponible"] = lang('the_project').' "'.$project_info->title.'" '.lang('compromise_matrix_not_enabled');
		}
		
		return $result;
		
	}
	
	private function get_permitting_summary_by_evaluated_for_pdf($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_permiso = $this->Permitting_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		if($id_permiso){
			
			$evaluados_matriz_permisos = $this->Evaluated_permitting_model->get_all_where(array(
				"id_permiso" => $id_permiso, "deleted" => 0
			))->result_array();
			$array_total_permisos_aplicables_por_evaluado = array();
			
			foreach($evaluados_matriz_permisos as $evaluado){
		
				$permisos_por_evaluado = $this->Permitting_model->get_total_applicable_procedures_by_evaluated($evaluado["id"])->result_array();
				$total_permisos_por_evaluado = 0;
				
				foreach($permisos_por_evaluado as $ppe){
					if( ($ppe["fecha_evaluacion"] >= $start_date) && ($ppe["fecha_evaluacion"] <= $end_date) ){					
						$total_permisos_por_evaluado++;
					} 	
				}
		
				$array_total_permisos_aplicables_por_evaluado[$evaluado["id"]] = $total_permisos_por_evaluado;
			}
			
			// Listado de estados de categoría Aplica que están siendo utilizados en alguna evaluación
			$estados = $this->Permitting_model->get_status_in_evaluations($id_cliente, $id_proyecto)->result_array();
			$array_estados_en_evaluaciones = array();
			
			foreach($estados as $estado){
				if( ($estado["fecha_evaluacion"] >= $start_date) && ($estado["fecha_evaluacion"] <= $end_date) ){					
					$array_estados_en_evaluaciones[] = $estado;
				} 
			}

			// Se agrupa $array_estados_en_evaluaciones POR id_estado
			$result_estados = array();
			foreach($array_estados_en_evaluaciones as $atcee){
				$repeat = false;
				for($i = 0; $i < count($result_estados); $i++){
					if($result_estados[$i]['id_estado'] == $atcee['id_estado']){
						$repeat = true;
						break;
					}
				}
				if($repeat == false){
					$result_estados[] = array('nombre_estado' =>  $atcee['nombre_estado'], 'id_estado' => $atcee['id_estado']);
				}		
			}
			
			$result["evaluados_matriz_permisos"] = $evaluados_matriz_permisos;
			$result["array_total_permisos_aplicables_por_evaluado"] = $array_total_permisos_aplicables_por_evaluado;
			$result["result_estados"] = $result_estados;
			
		} else {
			$result["matriz_no_disponible"] = lang('the_project').' "'.$project_info->title.'" '.lang('permitting_matrix_not_enabled');
		}
		
		return $result;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}

}

/* End of file Reports.php */
/* Location: ./application/controllers/clients.php */