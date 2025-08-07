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
		
		$project_info = $this->Projects_model->get_one($this->session->project_context);
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
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		
		$html = '';
		$html .='<div id="contenido">';
		
		if($report_config->project_data){
			
			$html .='<!--ANTECEDENTES DEL PROYECTO-->';	
	
			$html .=	'<div class="page-title clearfix">';
			$html .=		'<h1>'.lang("project_background").'</h1>';
			$html .=	'</div>';

			$html .='<div class="panel-body" style="padding-bottom:0px;">';
			$html .=	'<div class="form-group">';
			$html .=		'<div class="col-md-6" style="padding-left:0px">';	
			$html .=			'<table class="table table-bordered">';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("client").'</th>';
			$html .=					'<td id="client_name" data-value="'.$project_info->title.'">'.$client_info->company_name.'</td>';
			$html .=				'</tr>';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("project").'</th>';
			$html .=					'<td id="project_name" data-value="'.$project_info->title.'">'.$project_info->title.'</td>';
			$html .=				'</tr>';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("location").'</th>';
			$html .=					'<td id="location" data-value="'.$project_info->state .', '.$project_info->city.', '.$project_info->country.'">'.$project_info->state .', '.$project_info->city.', '.$project_info->country.'</td>';
			$html .=				'</tr>';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("rut").'</th>';
			$html .=					'<td id="rut" data-value="'.$client_info->rut.'">'.$client_info->rut.'</td>';
			$html .=				'</tr>';
			//$html .=				'<tr>';
			//$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("technology").'</th>';
			//$html .=					'<td id="technology" data-value="'.$technology->nombre.'" >'.$technology->nombre.'</td>';
			//$html .=				'</tr>';
			$html .=			'</table>';
			$html .=		'</div>';
			$html .=		'<div class="col-md-6" style="padding-right:0px">';
			$html .=			'<table class="table table-bordered">';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("environmental_authorization").'</th>';
			
			$environmental_authorization = ($project_info->environmental_authorization) ? $project_info->environmental_authorization : "-";
			
			$html .=					'<td id="environmental_authorization" data-value="'.$environmental_authorization.'" >'.$environmental_authorization.'</td>';
			$html .=				'</tr>';
			
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("phase").'</th>';
										
			$id_fase_proyecto = $this->Project_rel_phases_model->get_one_where(array(
				"id_proyecto" => $project_info->id,
				"deleted" => 0
			))->id_fase;							
			
			$nombre_fase = $this->Phases_model->get_one($id_fase_proyecto)->nombre;							
										
			$html .=					'<td id="phase" data-value="'.$nombre_fase.'" >'.$nombre_fase.'</td>';
			$html .=				'</tr>';
			
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("start_date").'</th>';
			$html .=					'<td id="start_date" data-value="'.get_date_format($project_info->start_date, $id_proyecto).'" >'.get_date_format($project_info->start_date, $id_proyecto).'</td>';
			$html .=				'</tr>';
			$html .=				'<tr>';
			$html .=					'<th style="background-color:'.$client_info->color_sitio.';">'.lang("deadline").'</th>';
			$html .=					'<td id="deadline" data-value="'.get_date_format($project_info->deadline, $id_proyecto).'" >'.get_date_format($project_info->deadline, $id_proyecto).'</td>';
			$html .=				'</tr>';
			$html .=			'</table>';
			$html .=		'</div>';
			$html .=	'</div>';
			$html .='</div>';
			$html .='<!-- FIN ANTECEDENTES DEL PROYECTO -->';
		
		}
		
		if($report_config->consumptions){
	
			$html .='<!-- CONSUMOS -->';
			$html .='<div class="panel panel-default">';
			$html .='<div class="panel-body">';
			$html .='<table class="table table-bordered" id="tabla_consumo">';
			$html .=	'<tr>';
			$html .=		'<th colspan="3" class="label-info" style="text-align:center; background-color:'.$client_info->color_sitio.';">'.lang("consumptions").'</th>';
			$html .=	'</tr>';
			$html .=	'<tr>';
			$html .=		'<th class="text-center">'.lang("categories").'</th>';
			$html .=		'<th class="text-center">'.lang("Reported_in_period").'</th>';
			$html .=		'<th class="text-center">'.lang("accumulated").'</th>';
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
				
				$html .=	'<tr>';
				$html .= 		'<td>'.$nombre_categoria.' ('.$unidad_volumen.')</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto).'</td>';
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
				$html .=	'<tr>';
				$html .=		'<td>'.$nombre_categoria.' ('.$unidad_masa.')</td>';
				$html .=		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
				$html .=		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto).'</td>';
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
				$html .=	'<tr>';
				$html .=		'<td>'.$nombre_categoria.' ('.$unidad_energia.')</td>';
				$html .=		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
				$html .=		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto).'</td>';
				$html .=	'</tr>';
			}
	
			$html .='</table>';
			$html .='</div>';
			$html .='<div class="panel panel-default">';
			$html .='<div class="panel-body">';
			$html .='<div class="row">';
			
			$html .='<div class="col-md-6">';
			$html .='<div class="grafico_consumo" id="consumo_volumen"></div>'; //m3
			$html .='</div>';
			$html .='<div class="col-md-6">';
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
		
		if($report_config->waste){
		
			$html .='<!--RESIDUOS -->';
			$html .='<div class="panel panel-default">';
			/* $html .='<div class="page-title clearfix">';
			$html .='<h1 style="font-size: 15px;">'.lang("waste").'</h1>';
			$html .='</div>'; */
			
			$html .='<div class="panel-body">';
			
	
	
	
			$html .='<table class="table table-bordered" id="tabla_residuo">';
			$html .=	'<tr>';
			$html .=		'<th colspan="3" class="label-info" style="text-align:center; background-color:'.$client_info->color_sitio.';">'.lang("waste").'</th>';
			$html .=	'</tr>';
			$html .=	'<tr>';
			$html .=		'<th class="text-center">'.lang("categories").'</th>';
			$html .=		'<th class="text-center">'.lang("Reported_in_period").'</th>';
			$html .=		'<th class="text-center">'.lang("accumulated").'</th>';
			$html .=	'</tr>';
	
			
			$tabla_residuo_volumen = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
			$tabla_residuo_volumen_reportados = $tabla_residuo_volumen["reportados"];
			$tabla_residuo_volumen_acumulados = $tabla_residuo_volumen["acumulados"];
			
			$tabla_residuo_masa = $this->get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date);
			$tabla_residuo_masa_reportados = $tabla_residuo_masa["reportados"];
			$tabla_residuo_masa_acumulados = $tabla_residuo_masa["acumulados"];
			
			foreach ($tabla_residuo_volumen_reportados as $id_categoria => $arreglo_valores){
				$arreglo_valores_acumulados = $tabla_residuo_volumen_acumulados[$id_categoria];
				
				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
				}
	
				$html .= 	'<tr>';
				$html .= 		'<td>'.$nombre_categoria.' ('.$unidad_volumen.')</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto).'</td>';
				$html .= 	'</tr>';
			}
	
			foreach ($tabla_residuo_masa_reportados as $id_categoria => $arreglo_valores){
				$arreglo_valores_acumulados = $tabla_residuo_masa_acumulados[$id_categoria];
				
				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, 'deleted' => 0));
				if($row_alias->alias){
					$nombre_categoria = $row_alias->alias;
				}else{
					$row_categoria = $this->Categories_model->get_one_where(array('id' => $id_categoria, 'deleted' => 0));
					$nombre_categoria = $row_categoria->nombre;
				}
				$html .= 	'<tr>';
				$html .= 		'<td>'.$nombre_categoria.' ('.$unidad_masa.')</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores), $id_proyecto).'</td>';
				$html .= 		'<td class="text-right">'.to_number_project_format(array_sum($arreglo_valores_acumulados), $id_proyecto).'</td>';
				$html .= 	'</tr>';
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

		$html .='</div><!--Fin div contenido -->';
		
		
		$html .='<script type="text/javascript">';
		
		$html .='$("#consumo_volumen").highcharts({';
		$html .='chart:{';
		$html .='	zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},';
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
		
		/*
		$html .='exporting:{';
		$html .=			'enabled: true,';
		$html .='			yAxis: {';
		$html .='				min: 0,';
		$html .='				title: {text: "'.$unidad_volumen_nombre_real.' ('.$unidad_volumen.')"},';	
		$html .='				labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
		$html .='			},';			
		$html .=		'},'; 
		*/
		
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
		$html .='	series: [';
		$html .='	{';
		$html .='		name: "'.lang('reported').'",';
		//$html .='		data: [1000000, 123.99443345345]';
		$html .='		data: [';
							foreach($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data as $categoria_valor){
								$html .= $categoria_valor.',';	
							} 
		$html .='		]'; 
		$html .='	},';
		//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
		$html .='	{';
		$html .='		name: "'.lang('accumulated').'",';
		$html .='		data: [';
		
							foreach($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data_a as $categoria_valor){
								$html .= $categoria_valor.',';
							}
		$html .='		]'; 
		$html .='	}';
		$html .='	]';
		$html .='});';
		//var_dump($this->get_datos_grafico_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_volumen_data_a);
		
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
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
		$html .='	}';
		$html .='	]';
		$html .='});';
		
		
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
		$html .='	series: [';
		$html .='	{';
		$html .='		name: "'.lang('reported').'",';
		$html .='		data: [';
							foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data as $categoria_valor){
								$html .= $categoria_valor.',';	
							} 
		$html .='		]'; 
		$html .='	},';
		//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
		$html .='	{';
		$html .='		name: "'.lang('accumulated').'",';
		$html .='		data: [';
		
							foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data_a as $categoria_valor){
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
		$html .='	series: [';
		$html .='	{';
		$html .='		name: "'.lang('reported').'",';
		$html .='		data: [';
							foreach($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_data as $categoria_valor){
								$html .= $categoria_valor.',';	
							} 
		$html .='		]'; 
		$html .='	},';
		//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
		$html .='	{';
		$html .='		name: "'.lang('accumulated').'",';
		$html .='		data: [';
		
							foreach($this->get_datos_grafico_consumo_energia($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_consumos_energia_data_a as $categoria_valor){
								$html .= $categoria_valor.',';
							}
		$html .='		]'; 
		$html .='	}';
		$html .='	]';
		$html .='});';
		
		
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
		$html .='	series: [';
		$html .='	{';
		$html .='		name: "'.lang('reported').'",';
		$html .='		data: [';
							foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data as $categoria_valor){
								$html .= $categoria_valor.',';	
							} 
		$html .='		]'; 
		$html .='	},';
		//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
		$html .='	{';
		$html .='		name: "'.lang('accumulated').'",';
		$html .='		data: [';
		
							foreach($this->get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_volumen_data_a as $categoria_valor){
								$html .= $categoria_valor.',';
							}
		$html .='		]'; 
		$html .='	}';
		$html .='	]';
		$html .='});';
		
		
		
				
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
		$html .='		labels:{ formatter: function(){return numberFormat(this.value, 0, "'.$decimals_separator.'", "'.$thousands_separator.'");} },';
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
		$html .='				style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}';
		$html .='			}';
		$html .='		}';
		$html .='	},';
		$html .='	colors: ["#4CD2B1","#5C6BC0"],';
		$html .='	series: [';
		$html .='	{';
		$html .='		name: "'.lang('reported').'",';
		$html .='		data: [';
							foreach($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data as $categoria_valor){
								$html .= $categoria_valor.',';	
							} 
		$html .='		]'; 
		$html .='	},';
		//$html .='		{name: "'.lang('accumulated').'", data: [4000,4000,345,0,]}';
		$html .='	{';
		$html .='		name: "'.lang('accumulated').'",';
		$html .='		data: [';
		
							foreach($this->get_datos_grafico_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date)->array_grafico_residuos_masa_data_a as $categoria_valor){
								$html .= $categoria_valor.',';
							}
		$html .='		]'; 
		$html .='	}';
		$html .='	]';
		$html .='});';
			
		$html .='</script>';
		
		
		// CONSULTAR DISPONIBILIDAD DE MÓDULOS Y PERFILAMIENTO DE MÓDULOS DE COMPROMISOS Y PERMISOS PARA MOSTRAR O NO MOSTRAR SECCIONES
		$disponibilidad_modulo_compromisos = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 6, "deleted" => 0))->available;
		$disponibilidad_modulo_permisos = $this->Module_availability_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_modulo_cliente" => 7, "deleted" => 0))->available;		
		$perfil_puede_ver_compromisos = $this->profile_access($this->session->user_id, 6, 3, "ver");
		$perfil_puede_ver_permisos = $this->profile_access($this->session->user_id, 7, 5, "ver");
		
		if($report_config->compromises){
			
			if($disponibilidad_modulo_compromisos == 1){
				if($perfil_puede_ver_compromisos == 1){
					$html .='<!-- RESUMEN POR EVALUADO (COMPROMISOS) -->';
					$html .= $this->get_compromises_summary_by_evaluated($id_cliente, $id_proyecto, $start_date, $end_date);
					$html .='<!-- FIN RESUMEN POR EVALUADO (COMPROMISOS)-->';
				}
			}
			
		}
		
		if($report_config->permittings){
			
			if($disponibilidad_modulo_permisos == 1){
				if($perfil_puede_ver_permisos == 1){
					$html .='<!-- RESUMEN POR EVALUADO (PERMISOS) -->';
					$html .= $this->get_permitting_summary_by_evaluated($id_cliente, $id_proyecto, $start_date, $end_date);
					$html .='<!-- FIN RESUMEN POR EVALUADO (PERMISOS)-->';
				}
			}

		}

		echo $html;
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
					$html .= 			'format: "<b>{point.name}</b>: {point.percentage:.1f} %",';
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
			$html .= 			lang('the_project').' "'.$nombre_proyecto.'" '.lang('permitting_matrix_not_enabled');
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			//$html .= "El proyecto no tiene matriz de compromisos habilitada";
			
		}
		
		return $html;
		
	}
	
	function get_color_of_status_for_permitting($id_estado){
		$estado = $this->Permitting_procedure_status_model->get_one($id_estado);
		return $estado->color;
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
		
		//var_dump("start_date: " . $start_date);
		//var_dump("end_date: " . $end_date);
		
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
					$html .= 			'format: "<b>{point.name}</b>: {point.percentage:.1f} %",';
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
		$evaluaciones = $this->Compromises_model->get_quantity_of_status_evaluated($id_estado, $id_evaluado)->result_array();

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
		
		$compromisos_por_evaluado = $this->Compromises_model->get_total_applicable_compromises_by_evaluated($id_evaluado)->result_array();
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

	function get_datos_tabla_consumo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		//$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_consumo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Consumo")->result();	
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Consumo"))->result();
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		
		$array_id_categorias_valores_volumen = array();
		$array_id_categorias_valores_volumen_a = array();
		
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
								//$datos_decoded = json_decode($ef->datos, true);
								//$valor = $datos_decoded[$id_campo];
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
								//$valor = $datos_decoded[$id_campo];
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = ($valor * $valor_transformacion);
								$array_id_categorias_valores_volumen_a[$cat->id_categoria][] = ($valor * $valor_transformacion);
							}
						}else{// ACUMULADOS
						
							// CONSIDERAR SOLO HASTA LA FECHA INGRESADA EN EL "HASTA"
							if($datos_decoded["fecha"] <= $end_date){
								
								if($id_unidad == $id_unidad_volumen){
									//$datos_decoded = json_decode($ef->datos, true);
									//$valor = $datos_decoded[$id_campo];
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
									//$valor = $datos_decoded[$id_campo];
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
		
		return array("reportados" => $array_id_categorias_valores_volumen, "acumulados" => $array_id_categorias_valores_volumen_a);
		//return $array_id_categorias_valores_volumen;
		
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
		}
		
		$object->array_grafico_consumos_volumen_categories = $array_grafico_consumos_volumen_categories;
		$object->array_grafico_consumos_volumen_data = $array_grafico_consumos_volumen_data;
		$object->array_grafico_consumos_volumen_data_a = $array_grafico_consumos_volumen_data_a;
		
		//var_dump($array_id_categorias_valores_volumen_a);
		
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
		}
		
		$object->array_grafico_consumos_masa_categories = $array_grafico_consumos_masa_categories;
		$object->array_grafico_consumos_masa_data = $array_grafico_consumos_masa_data;
		$object->array_grafico_consumos_masa_data_a = $array_grafico_consumos_masa_data_a;
		
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
		}
		
		$object->array_grafico_consumos_energia_categories = $array_grafico_consumos_energia_categories;
		$object->array_grafico_consumos_energia_data = $array_grafico_consumos_energia_data;
		$object->array_grafico_consumos_energia_data_a = $array_grafico_consumos_energia_data_a;
		
		return $object;
		
	}
	
	function get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		//$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_residuo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Residuo")->result();	
		$campos_unidad_residuo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Residuo"))->result();
		$id_unidad_volumen = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
	
		$array_id_categorias_valores_volumen = array();
		$array_id_categorias_valores_volumen_a = array();
		
		foreach($campos_unidad_residuo as $formulario_campo){
			$id_campo = $formulario_campo->id_campo;
			/*
			$datos_campo = json_decode($formulario_campo->opciones, true);
			$id_tipo_unidad = $datos_campo[0]["id_tipo_unidad"];
			$id_unidad = $datos_campo[0]["id_unidad"];
			*/
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
					// CONSULTO LOS VALORES DEL FORMULARIOS CORRESPONDIENTES A LA CATEGORIA
					$elementos_form = $this->Calculation_model->get_records_of_category_of_form($cat->id_categoria, $cat->id_formulario, "Residuo")->result();
					// POR CADA ELEMENTO DE LA CATEGORIA DEL FORMULARIO
					foreach($elementos_form as $ef){
						$datos_decoded = json_decode($ef->datos, true);
						
						if(($datos_decoded["fecha"] >= $start_date) && ($datos_decoded["fecha"] <= $end_date)) {
							// SI LA UNIDAD DEL ELEMENTO ES LA MISMA DE LA CONFIGURACION LA INCORPORO A LA CATEGORIA
							/*
							if($id_unidad == $id_unidad_volumen){
								//$datos_decoded = json_decode($ef->datos, true);
								$valor = $datos_decoded["unidad_residuo"];
								$array_id_categorias_valores_volumen[$cat->id_categoria][] = $valor;
								
							}
							*/
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
				}
			}
		}
		
		return array("reportados" => $array_id_categorias_valores_volumen, "acumulados" => $array_id_categorias_valores_volumen_a);
		//return $array_id_categorias_valores_volumen;
	}
	
	function get_datos_tabla_residuo_masa($id_cliente, $id_proyecto, $start_date, $end_date){
		
		//CONFIGURACIÓN DE REPORTE DEL PROYECTO
		$report_config = $this->Reports_configuration_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		$array_report_config_materials = json_decode($report_config->materials, true);
		
		$categorias = $this->Form_rel_materiales_rel_categorias_model->get_categories_of_form_2($id_proyecto)->result();
		//$campos_unidad_consumo = $this->Fields_model->get_unity_fields_of_ra($id_cliente, $id_proyecto, "Residuo")->result();	
		$campos_unidad_consumo = $this->Forms_model->get_details(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "flujo" => "Residuo"))->result();
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
				}
			}
		}
		
		return array("reportados" => $array_id_categorias_valores_masa, "acumulados" => $array_id_categorias_valores_masa_a);
		//return $array_id_categorias_valores_masa;
	}
	
	function get_datos_grafico_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$object = new StdClass;
		
		$array_id_categorias_valores_volumen_total = $this->get_datos_tabla_residuo_volumen($id_cliente, $id_proyecto, $start_date, $end_date);
		$array_id_categorias_valores_volumen = $array_id_categorias_valores_volumen_total["reportados"];
		$array_id_categorias_valores_volumen_a = $array_id_categorias_valores_volumen_total["acumulados"];
		
		$array_grafico_residuos_volumen_categories = array();
		$array_grafico_residuos_volumen_data = array();
		$array_grafico_residuos_volumen_data_a = array();
		
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
		}
		
		$object->array_grafico_residuos_volumen_categories = $array_grafico_residuos_volumen_categories;
		$object->array_grafico_residuos_volumen_data = $array_grafico_residuos_volumen_data;
		$object->array_grafico_residuos_volumen_data_a = $array_grafico_residuos_volumen_data_a;
		
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
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_usuario = $this->login_user->id;
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		
		$environmental_authorization = ($info_proyecto->environmental_authorization) ? $info_proyecto->environmental_authorization : "-";
		
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
		
		$ubicacion = $info_proyecto->state . ", " . $info_proyecto->city . ", " . $info_proyecto->country;
		$fecha_desde = $this->input->post("fecha_desde");
		$fecha_hasta = $this->input->post("fecha_hasta");
		$graficos_consumo = $this->input->post("graficos_consumo");
		$graficos_residuo = $this->input->post("graficos_residuo");
		$graficos_resumen_evaluado_compromisos = $this->input->post("graficos_resumen_evaluado_compromisos");
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
	
	private function get_compromises_summary_by_evaluated_for_pdf($id_cliente, $id_proyecto, $start_date, $end_date){
		
		$id_compromiso = $this->Compromises_model->get_one_where(array('id_proyecto' => $id_proyecto, 'deleted' => 0))->id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		
		$result = array();
		
		if($id_compromiso){
			
			$evaluados_matriz_compromiso = $this->Evaluated_compromises_model->get_all_where(array(
				"id_compromiso" => $id_compromiso, "deleted" => 0
			))->result_array();
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
			
			// Listado de estados de categoría Cumple y No Cumple que están siendo utilizados en alguna evaluación
			$estados = $this->Compromises_model->get_status_in_evaluations($id_cliente)->result_array();
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
			$estados = $this->Permitting_model->get_status_in_evaluations($id_cliente)->result_array();
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