<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KPI_Charts_by_project extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");	
		
		$this->id_client_context_module = 2;
		$this->id_client_context_submodule = 6;
		$id_cliente = $this->login_user->client_id;
		//$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
		// KPI
		$kpi_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_modulo" => 2,
			"deleted" => 0
		));
		if(!$kpi_disponibilidad_modulo->disponible){
			$this->access_only_allowed_members();
		}
		
    }

    function index() {
		
		//$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_kpi_active', TRUE);
		$this->session->set_userdata('menu_project_active', NULL);
		$this->session->set_userdata('client_area', NULL);
		$this->session->set_userdata('project_context', NULL);
		$this->session->set_userdata('menu_agreements_active', NULL);	
		$this->session->set_userdata('menu_help_and_support_active', NULL);
		$this->session->set_userdata('menu_recordbook_active', NULL);
		$this->session->set_userdata('menu_ec_active', NULL);
		
		$id_usuario = $this->session->user_id;
		$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
				
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$paises = array("" => "-") + $this->Countries_model->get_dropdown_list(array("nombre"), "id");
		
		$array_fases = array("" => "-");
		$fases = $this->Phases_model->get_all()->result();
		foreach($fases as $fase){
			if($fase->id == 2 || $fase->id == 3){
				$array_fases[$fase->id] = lang($fase->nombre_lang);
			}
		}
		
		$tecnologias = array("" => "-") + $this->Technologies_model->get_dropdown_list(array("nombre"), "id");
		
		$view_data["paises"] = $paises;
		$view_data["fases"] = $array_fases;
		$view_data["tecnologias"] = $tecnologias;
		$view_data["proyectos"] = array("" => "-");
		
		$this->template->rander("kpi_charts_by_project/index", $view_data);
		
    }
	
	function get_project_filter(){
		
		$opciones_proyectos = array(
			"id_pais" => $this->input->post('id_pais'),
			"id_fase" => $this->input->post('id_fase'),
			"id_tech" => $this->input->post('id_tecnologia'),
			"id_cliente" => $this->login_user->client_id
		);
		
		$array_proyectos = array("" => "-");
		$proyectos = $this->Projects_model->get_project_for_kpi_report_filter($opciones_proyectos)->result();
		foreach($proyectos as $proyecto){
			$array_proyectos[$proyecto->id] = $proyecto->title;
		}
		
		$html = '<div class="form-group col-md-4">';
		$html .= '<label for="proyecto" class="col-md-3">'.lang('project').'</label>';
		$html .= 	'<div class="col-md-9">';
		$html .= 		form_dropdown("proyecto", $array_proyectos, "", "id='proyecto' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
	
	}
	
	function get_kpi_report(){
		
		$id_proyecto = $this->input->post('id_proyecto');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		$view_data = $this->generate_report($id_proyecto, $fecha_desde, $fecha_hasta);		
		$html_kpi_report = $this->load->view('kpi_charts_by_project/kpi_charts_by_project', $view_data, TRUE);

		echo $html_kpi_report;
		
	}
	
	function generate_report($id_proyecto, $fecha_desde, $fecha_hasta){
		
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$cliente = $this->Clients_model->get_one($proyecto->client_id);
		$view_data["sigla_cliente"] = $cliente->sigla;
		$view_data["sigla_proyecto"] = $proyecto->sigla;
		$view_data["fecha_actual"] = get_current_utc_time("Y-m-d");
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0));
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["id_cliente"] = $cliente->id;
		$view_data["fecha_desde"] = $fecha_desde;
		$view_data["fecha_hasta"] = $fecha_hasta;
		
		$masa_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $cliente->id, "id_tipo_unidad" => 1)); // MASA
		$unidad_masa_client_config = $this->Unity_model->get_one($masa_client_config->id_unidad);
		$view_data["unidad_masa_config"] = $unidad_masa_client_config->nombre;
		
		$energia_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $cliente->id, "id_tipo_unidad" => 4)); // ENERGÍA
		$unidad_energia_client_config = $this->Unity_model->get_one($energia_client_config->id_unidad);
		$view_data["unidad_energia_config"] = $unidad_energia_client_config->nombre;
		
		$volumen_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $cliente->id, "id_tipo_unidad" => 2)); // VOLUMEN
		$unidad_volumen_client_config = $this->Unity_model->get_one($volumen_client_config->id_unidad);
		$view_data["unidad_volumen_config"] = $unidad_volumen_client_config->nombre;
		
		// Rango de Fechas
		$array_meses = array(
			"01" => "Ene",
			"02" => "Feb",
			"03" => "Mar",
			"04" => "Abr",
			"05" => "May",
			"06" => "Jun",
			"07" => "Jul",
			"08" => "Ago",
			"09" => "Sep",
			"10" => "Oct",
			"11" => "Nov",
			"12" => "Dic",
		);
		
		$array_rango_fechas = array();
		
		$start    = new DateTime($fecha_desde);
		$start->modify('first day of this month');
		$end      = new DateTime($fecha_hasta);
		$end->modify('first day of next month');
		$interval = DateInterval::createFromDateString('1 month');
		$period   = new DatePeriod($start, $interval, $end);
		
		foreach ($period as $dt) {
			$array_rango_fechas[$dt->format("Y")][$dt->format("m")] = $array_meses[$dt->format("m")];
		}
		
		$view_data["rango_fechas"] = $array_rango_fechas;
		// Fin Rango de Fechas

		$estructuras_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
			"id_proyecto" => $id_proyecto,
			"submodulo_grafico" => "charts_by_project",
			"deleted" => 0
		))->result_array();
		
		$array_estructuras_graficos = array();
		foreach($estructuras_graficos as $seccion){
			
			$array_series = json_decode($seccion["series"], TRUE);
				
			foreach($array_series as $nombre_serie => $valor_serie) {
				
				$valor = $this->KPI_Values_model->get_one($valor_serie);
				$valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
					"id_kpi_valores" => $valor->id,
					"deleted" => 0
				))->result_array();
				$formulario_valor = $this->Forms_model->get_one($valor->id_formulario);
				
				if(!$formulario_valor->fijo){			
					$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
						"id_formulario" => $formulario_valor->id,
						"deleted" => 0
					));
					$elementos_formulario = $this->Form_values_model->get_all_where(array(
						"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
						"deleted" => 0
					))->result_array();
				} else {
					$elementos_formulario = $this->Fixed_form_values_model->get_all_where(array(
						"id_formulario" => $formulario_valor->id,
						"deleted" => 0
					))->result_array();
				}
				
				$total_valor = 0;
				
				if($valor->id && $valor->tipo_valor == "simple") {

					if(count($valores_condicion)){
									
						$array_datos_valores_condicion = array();
						
						foreach($valores_condicion as $valor_condicion){
							
							$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
							$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
							$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
							$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
							
							if($valor_condicion_categoria){
								$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
							}
							if($valor_condicion_tipo_tratamiento){
								$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
							}
							if($valor_condicion_id_campo){
								$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
							}
							if($valor_condicion_id_campo_fijo){
								$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
							}
	
						}
	
					}
					
					if(count($elementos_formulario)){
	
						foreach($elementos_formulario as $elemento){
					
							$datos = json_decode($elemento["datos"], TRUE);
							$fecha_elemento = $datos["fecha"];
	

							$elemento_campos_dinamicos = array();
							foreach($datos as $key => $value){
								if(array_key_exists($key, $array_datos_valores_condicion)){
									$elemento_campos_dinamicos[$key] = $datos[$key];
								}
							}
							
							if($array_datos_valores_condicion == $elemento_campos_dinamicos){
								
								if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){

									$campo_indicador = $valor->id_campo_unidad;

									if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
										$valor_unidad_fija = $datos["unidad_residuo"];
										//$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
										$total_valor = $total_valor + $valor_unidad_fija;
									} else {
										$valor_unidad_dinamica = $datos[$campo_indicador]; 
										//$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
										$total_valor = $total_valor + $valor_unidad_dinamica;
									}
									
								}	
	
							}
	
						}
	
					}
					
					if($valor->operador){
					
						$operador = $valor->operador;
						$valor_operador = $valor->valor_operador;

						if($operador == "+"){
							$total_valor = $total_valor + $valor_operador;
						}
						if($operador == "-"){
							$total_valor = $total_valor - $valor_operador;
						}
						if($operador == "*"){
							$total_valor = $total_valor * $valor_operador;
						}
						if($operador == "/"){
							$total_valor = $total_valor / $valor_operador;
						}
					
					}
				
				}
				
				if($valor->id && $valor->tipo_valor == "compound") {
					
					// Cálculo valor inicial
					$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
					$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
						"id_kpi_valores" => $valor_inicial->id,
						"deleted" => 0
					))->result_array();
					$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
					
					if(!$formulario_valor_inicial->fijo){
						$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
							"id_formulario" => $formulario_valor_inicial->id,
							"deleted" => 0
						));
						$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
							"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
							"deleted" => 0
						))->result_array();
					} else {
						$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
							"id_formulario" => $formulario_valor_inicial->id,
							"deleted" => 0
						))->result_array();
					}
					
					$total_valor_inicial = 0;

					if($valor_inicial->id && count($valores_condicion_inicial)) {
						
						$array_datos_valores_condicion_inicial = array();
						
						foreach($valores_condicion_inicial as $valor_condicion_inicial){
							
							$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
							$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
							$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
							$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
							
							if($valor_condicion_categoria){
								$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
							}
							if($valor_condicion_tipo_tratamiento){
								$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
							}
							if($valor_condicion_id_campo){
								$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
							}
							if($valor_condicion_id_campo_fijo){
								$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
							}
	
						}
						
					}
					
					if(count($elementos_formulario_inicial)){
	
						foreach($elementos_formulario_inicial as $elemento){
					
							$datos = json_decode($elemento["datos"], TRUE);
							$fecha_elemento = $datos["fecha"];
	
							$elemento_campos_dinamicos = array();
							foreach($datos as $key => $value){
								if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
									$elemento_campos_dinamicos[$key] = $datos[$key];
								}
							}
							
							if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
								
								if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
									$campo_indicador = $valor_inicial->id_campo_unidad;
									
									// TRANSFORMACIÓN
									$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
									$id_unidad_origen = $valor_inicial->id_unidad;
									/*
									if($seccion["item"] == "materials_and_waste" || $seccion["item"] == "emissions"){
										$id_unidad_destino = 1; // t (Masa)
									} elseif($seccion["item"] == "energy"){
										$id_unidad_destino = 21; // MWH (Energía)
									} elseif($seccion["item"] == "water"){
										$id_unidad_destino = 3; // m3 (Volumen)
									} elseif($seccion["item"] == "social"){
										$id_unidad_destino = 18; // Unidad (Unidad)
									}
									*/
									$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad; 
									
									$fila_conversion = $this->Conversion_model->get_one_where(
										array(
											"id_tipo_unidad" => $id_tipo_unidad_origen,
											"id_unidad_origen" => $id_unidad_origen,
											"id_unidad_destino" => $id_unidad_destino
										)
									);
									$valor_transformacion = $fila_conversion->transformacion;
									
									if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
										$valor_unidad_fija = $datos["unidad_residuo"];
										$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
										$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
									} else {
										$valor_unidad_dinamica = $datos[$campo_indicador]; 
										$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
										$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
									}
									
								}	
	
							}

						}
	
					}
										
					if($valor_inicial->operador){
					
						$operador = $valor_inicial->operador;
						$valor_operador = $valor_inicial->valor_operador;
						
						if($operador == "+"){
							$total_valor_inicial = $total_valor_inicial + $valor_operador;
						}
						if($operador == "-"){
							$total_valor_inicial = $total_valor_inicial - $valor_operador;
						}
						if($operador == "*"){
							$total_valor_inicial = $total_valor_inicial * $valor_operador;
						}
						if($operador == "/"){
							$total_valor_inicial = $total_valor_inicial / $valor_operador;
						}
						
					}
					// Fin Cálculo valor inicial
					
					// Cálculo valores operación
					$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
					
					$total_valor_calculo_final = 0;
					$array_valores_operacion_compuesta = array();
					
					foreach($array_operacion_compuesta as $index => $operacion_compuesta){
						
						$operador = key($operacion_compuesta);
						$id_valor = $operacion_compuesta[key($operacion_compuesta)];

						$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
						$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
							"id_kpi_valores" => $valor_calculo->id,
							"deleted" => 0
						))->result_array();
						$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
						
						if(!$formulario_valor_calculo->fijo){
							$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
								"id_formulario" => $formulario_valor_calculo->id,
								"deleted" => 0
							));
							$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
								"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
								"deleted" => 0
							))->result_array();
						} else {
							$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
								"id_formulario" => $formulario_valor_calculo->id,
								"deleted" => 0
							))->result_array();
						}
						
						$total_valor_calculo = 0;
						
						if($valor_calculo->id && count($valores_condicion_calculo)) {
							
							$array_datos_valores_condicion_calculo = array();
							
							foreach($valores_condicion_calculo as $valor_condicion_calculo){
								
								$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
								$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
								$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
								$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
								
								if($valor_condicion_categoria){
									$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
								}
								if($valor_condicion_tipo_tratamiento){
									$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
								}
								if($valor_condicion_id_campo){
									$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
								}
								if($valor_condicion_id_campo_fijo){
									$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
								}
		
							}
							
						}
						
						if(count($elementos_formulario_calculo)){
		
							foreach($elementos_formulario_calculo as $elemento){
						
								$datos = json_decode($elemento["datos"], TRUE);
								$fecha_elemento = $datos["fecha"];
		
								$elemento_campos_dinamicos = array();
								foreach($datos as $key => $value){
									if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
										$elemento_campos_dinamicos[$key] = $datos[$key];
									}
								}
								
								if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
									
									if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
										
										$campo_indicador = $valor_calculo->id_campo_unidad;
										
										// TRANSFORMACIÓN
										$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
										$id_unidad_origen = $valor_calculo->id_unidad;
										
										/*
										if($seccion["item"] == "materials_and_waste" || $seccion["item"] == "emissions"){
											$id_unidad_destino = 1; // t (Masa)
										} elseif($seccion["item"] == "energy"){
											$id_unidad_destino = 21; // MWH (Energía)
										} elseif($seccion["item"] == "water"){
											$id_unidad_destino = 3; // m3 (Volumen)
										} elseif($seccion["item"] == "social"){
											$id_unidad_destino = 18; // Unidad (Unidad)
										}
										*/
										
										$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad; 
										
										$fila_conversion = $this->Conversion_model->get_one_where(
											array(
												"id_tipo_unidad" => $id_tipo_unidad_origen,
												"id_unidad_origen" => $id_unidad_origen,
												"id_unidad_destino" => $id_unidad_destino
											)
										);
										$valor_transformacion = $fila_conversion->transformacion;
										
										if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
											$valor_unidad_fija = $datos["unidad_residuo"];
											$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
											$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
										} else {
											$valor_unidad_dinamica = $datos[$campo_indicador]; 
											$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
											$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
										}
										
									}	
		
								}
	
							}
		
						}
						
						if($valor_calculo->operador){
						
							$valor_operador = $valor_calculo->valor_operador;
							
							if($valor_calculo->operador == "+"){
								$total_valor_calculo = $total_valor_calculo + $valor_operador;
							}
							if($valor_calculo->operador == "-"){
								$total_valor_calculo = $total_valor_calculo - $valor_operador;
							}
							if($valor_calculo->operador == "*"){
								$total_valor_calculo = $total_valor_calculo * $valor_operador;
							}
							if($valor_calculo->operador == "/"){
								$total_valor_calculo = $total_valor_calculo / $valor_operador;
							}
							
						}
						
						if($operador){
							
							$array_valores_operacion_compuesta[] = array(
								$operador => $total_valor_calculo
							);

						}
						
					}
					// Fin Cálculo valores operación (cada valor se almacena en $array_valores_operacion_compuesta
					
					// Cálculo total final
					$total_valor_calculo_final = $total_valor_inicial;
					
					foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
						
						$operador = key($valor_operacion_compuesta);
						$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
						
						if($operador == "+"){
							$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
						}
						if($operador == "-"){
							$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
						}
						if($operador == "*"){
							$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
						}
						if($operador == "/"){
							$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
						}

					}
					
					$total_valor = $total_valor_calculo_final;
					// Fin Cálculo total final

				}
				
				// TRANSFORMACIÓN
				$id_tipo_unidad_origen = $valor->id_tipo_unidad;
				$id_unidad_origen = $valor->id_unidad;
				$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
				
				$total_valor = get_transformed_value($total_valor, $id_tipo_unidad_origen, $id_unidad_origen, $id_unidad_destino);
				//
								
				if($seccion["item"] == "water" && $seccion["subitem"] == "water_consumption_by_origin"){
					$array_estructuras_graficos[$seccion["item"]][$seccion["subitem"]][$seccion["tipo_grafico"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
				} elseif($seccion["item"] == "water" && $seccion["subitem"] == "water_reused_by_type") {
					$array_estructuras_graficos[$seccion["item"]][$seccion["subitem"]][$seccion["tipo_grafico"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
				} else {
					$array_estructuras_graficos[$seccion["item"]][$seccion["subitem"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
				}

				unset($array_datos_valores_condicion);
	
			}
			
		}
				
		$view_data["array_estructuras_graficos"] = $array_estructuras_graficos;

		$array_valores_waste_recycling_monthly = array();
		$array_valores_energy_consumption = array();
		$array_water_consumption_by_origin = array();
		$array_water_reused_by_type = array();
		$array_expenditure_local_suppliers = array();
		$array_donated_solutions_beneficiaries = array();
		
		foreach($estructuras_graficos as $seccion){
			
			$array_series = json_decode($seccion["series"], TRUE);
				
			foreach($array_series as $nombre_serie => $valor_serie) {

				foreach($array_rango_fechas as $anio => $meses){
					
					foreach($meses as $numero_mes => $nombre_mes){

						$valor = $this->KPI_Values_model->get_one($valor_serie);
						$valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
							"id_kpi_valores" => $valor->id,
							"deleted" => 0
						))->result_array();
						$formulario_valor = $this->Forms_model->get_one($valor->id_formulario);
						
						if(!$formulario_valor->fijo){
							$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
								"id_formulario" => $formulario_valor->id,
								"deleted" => 0
							));
							$elementos_formulario = $this->Form_values_model->get_all_where(array(
								"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
								"deleted" => 0
							))->result_array();
						} else {
							$elementos_formulario = $this->Fixed_form_values_model->get_all_where(array(
								"id_formulario" => $formulario_valor->id,
								"deleted" => 0
							))->result_array();
						}

						if($seccion["item"] == "materials_and_waste" && $seccion["subitem"] == "waste_recycling_monthly"){

							$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {

								if($valor->id && count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}

								if(count($elementos_formulario)){
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 1; // t (Masa)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;

												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = get_transformed_value($valor_unidad_fija, $id_tipo_unidad_origen, $id_unidad_origen, $id_unidad_destino);
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador];
													$valor_unidad_dinamica = get_transformed_value($valor_unidad_dinamica, $id_tipo_unidad_origen, $id_unidad_origen, $id_unidad_destino);
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
				
											} else {
												$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
				
									} // Fin foreach $elementos_formulario
	
								}

								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
						
							}
							
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);	
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else	{
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
								
								if($valor_inicial->id && count($valores_condicion_inicial)) {
					
									$array_datos_valores_condicion_inicial = array();
									
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
									
								}
								
								if(count($elementos_formulario_inicial)){
				
									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
				
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){

											if($fecha_elemento_my == $fecha_my){
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
												$id_unidad_origen = $valor_inicial->id_unidad;
												//$id_unidad_destino = 1; // t (Masa)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14];
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
												}
												
											} else {
												
												$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
			
									}
				
								}
								
								if($valor_inicial->operador){
				
									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial
								
								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
				
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
					
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 1; // t (Masa)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
					
												} else {
													
													$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
					
											}
				
										}
					
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
			
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_valores_waste_recycling_monthly[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;

							} // FIN IF VALOR IS COMPOUND

						}

						if($seccion["item"] == "energy" && $seccion["subitem"] == "energy_consumption"){

							$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {
								
								if(count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}

								if(count($elementos_formulario)){
									
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 21; // MWH (Energía)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
												
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
				
											} else {
												$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
				
									} // Fin foreach $elementos_formulario
	
								}

								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
								
							}
								
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else	{
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
								
								if($valor_inicial->id && count($valores_condicion_inicial)) {
								
									$array_datos_valores_condicion_inicial = array();
			
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
									
								}
								
								if(count($elementos_formulario_inicial)){

									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
						
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
						
											if($fecha_elemento_my == $fecha_my){
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
												$id_unidad_origen = $valor_inicial->id_unidad;
												//$id_unidad_destino = 21; // MWH (Energía)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
												
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador];
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
												}
						
											} else {
												
												$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
						
									}
						
								}
								
								if($valor_inicial->operador){

									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial
								
								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
						
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
						
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 21; // MWH (Energía)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion; 
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
						
												} else {
													
													$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
						
											}
						
										}
						
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
						
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_valores_energy_consumption[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;

							}
						
						}
						
						if($seccion["item"] == "water" && $seccion["subitem"] == "water_consumption_by_origin" && $seccion["tipo_grafico"] == "chart_bars_stacked_percentage"){
						
							$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {
								
								if($valor->id && count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}
								
								if(count($elementos_formulario)){
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 3; // m3 (Volumen)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
													
												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador];
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
							
											} else {
												$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
							
									} // Fin foreach $elementos_formulario
							
								}
								
								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
								
							}
							
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else {
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
								
								if($valor_inicial->id && count($valores_condicion_inicial)) {
								
									$array_datos_valores_condicion_inicial = array();
						
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								
								}

								if(count($elementos_formulario_inicial)){
						
									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
						
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
						
											if($fecha_elemento_my == $fecha_my){
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
												$id_unidad_origen = $valor_inicial->id_unidad;
												//$id_unidad_destino = 3; // m3 (Volumen)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
												}
												
											} else {
												
												$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
						
									}
						
								}
								
								if($valor_inicial->operador){
						
									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial

								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
						
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
						
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 3; // m3 (Volumen)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
						
												} else {
													
													$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
						
											}
						
										}
						
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
						
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_water_consumption_by_origin[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;	

							}	
		
						}
						
						if($seccion["item"] == "water" && $seccion["subitem"] == "water_reused_by_type" && $seccion["tipo_grafico"] == "chart_columns_percentage"){
						
							$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {
							
								if(count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}
						
								if(count($elementos_formulario)){
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 3; // m3 (Volumen)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
							
											} else {
												$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
							
									} // Fin foreach $elementos_formulario
							
								}
						
								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
						
							}
							
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else	{
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
									
								if($valor_inicial->id && count($valores_condicion_inicial)) {
								
									$array_datos_valores_condicion_inicial = array();
						
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								
								}
								
								if(count($elementos_formulario_inicial)){
						
									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
						
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
						
											if($fecha_elemento_my == $fecha_my){
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
												$id_unidad_origen = $valor_inicial->id_unidad;
												//$id_unidad_destino = 3; // m3 (Volumen)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador];
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
												}
						
											} else {
												
												$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
						
									}
						
								}
								
								if($valor_inicial->operador){
						
									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial

								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
						
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
						
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 3; // m3 (Volumen)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador];
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
						
												} else {
													
													$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
						
											}
						
										}
						
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
						
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_water_reused_by_type[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;

							}
							
						}
						
						if($seccion["item"] == "social" && $seccion["subitem"] == "expenditure_local_suppliers"){
						
							$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {
							
								if(count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}
						
								if(count($elementos_formulario)){
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 18; // Unidad (Unidad)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
							
											} else {
												$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
							
									} // Fin foreach $elementos_formulario
							
								}
						
								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
								
							}
							
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);	
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else {
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
								
								if($valor_inicial->id && count($valores_condicion_inicial)) {
								
									$array_datos_valores_condicion_inicial = array();
						
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								
								}
								
								if(count($elementos_formulario_inicial)){
						
									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
						
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
						
											if($fecha_elemento_my == $fecha_my){
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
												$id_unidad_origen = $valor_inicial->id_unidad;
												//$id_unidad_destino = 18; // Unidad (Unidad)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
					
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													
												}
											} else {
												
												$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
						
									}
						
								}
								
								if($valor_inicial->operador){
						
									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial

								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
						
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
						
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 18; // Unidad (Unidad)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;						
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
						
												} else {
													
													$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
						
											}
						
										}
						
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
						
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_expenditure_local_suppliers[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;

							}
							
						}
						
						if($seccion["item"] == "social" && $seccion["subitem"] == "donated_solutions_beneficiaries"){
						
							$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = 0;
							
							if($valor->id && $valor->tipo_valor == "simple") {
							
								if(count($valores_condicion)){		
									$array_datos_valores_condicion = array();
									foreach($valores_condicion as $valor_condicion){
										
										$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								}
						
								if(count($elementos_formulario)){
									$array_total_valor = array();
									foreach($elementos_formulario as $elemento){
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion == $elemento_campos_dinamicos){
											if($fecha_elemento_my == $fecha_my){
												
												$total_valor_serie = 0;
												$campo_indicador = $valor->id_campo_unidad;
												
												// TRANSFORMACIÓN
												$id_tipo_unidad_origen = $valor->id_tipo_unidad;
												$id_unidad_origen = $valor->id_unidad;
												//$id_unidad_destino = 18; // Unidad (Unidad)
												$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
												
												$fila_conversion = $this->Conversion_model->get_one_where(
													array(
														"id_tipo_unidad" => $id_tipo_unidad_origen,
														"id_unidad_origen" => $id_unidad_origen,
														"id_unidad_destino" => $id_unidad_destino
													)
												);
												$valor_transformacion = $fila_conversion->transformacion;
												
												if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
													$valor_unidad_fija = $datos["unidad_residuo"];
													$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
													$total_valor_serie = $total_valor_serie + $valor_unidad_fija;
												} else {
													$valor_unidad_dinamica = $datos[$campo_indicador];
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion; 
													$total_valor_serie = $total_valor_serie + $valor_unidad_dinamica;
												}
												$operador = $valor->operador;
												$valor_operador = $valor->valor_operador;
												$array_total_valor[] = $total_valor_serie;
							
											} else {
												$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = 0;
											}
										}
							
									} // Fin foreach $elementos_formulario
							
								}
							
								$total_valor_serie = array_sum($array_total_valor);
								$operador = $valor->operador;
								$valor_operador = $valor->valor_operador;
								
								if($operador == "+"){
									$total_valor_serie = $total_valor_serie + $valor_operador;
								}
								if($operador == "-"){
									$total_valor_serie = $total_valor_serie - $valor_operador;
								}
								if($operador == "*"){
									$total_valor_serie = $total_valor_serie * $valor_operador;
								}
								if($operador == "/"){
									$total_valor_serie = $total_valor_serie / $valor_operador;
								}	
								$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;
								
							}
							
							if($valor->id && $valor->tipo_valor == "compound") {
								
								// Cálculo valor inicial
								$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
								$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
									"id_kpi_valores" => $valor_inicial->id,
									"deleted" => 0
								))->result_array();
								$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
								
								if(!$formulario_valor_inicial->fijo){
									$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									));
									$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
										"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
										"deleted" => 0
									))->result_array();
								} else	{
									$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
										"id_formulario" => $formulario_valor_inicial->id,
										"deleted" => 0
									))->result_array();
								}
								
								$total_valor_inicial = 0;
								
								if($valor_inicial->id && count($valores_condicion_inicial)) {
								
									$array_datos_valores_condicion_inicial = array();
						
									foreach($valores_condicion_inicial as $valor_condicion_inicial){
										
										$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
										$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
										
										if($valor_condicion_categoria){
											$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
										}
										if($valor_condicion_tipo_tratamiento){
											$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
										}
										if($valor_condicion_id_campo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
										}
										if($valor_condicion_id_campo_fijo){
											$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
										}
									}
								
								}
								
								if(count($elementos_formulario_inicial)){
						
									foreach($elementos_formulario_inicial as $elemento){
								
										$datos = json_decode($elemento["datos"], TRUE);
										$fecha_elemento = $datos["fecha"];
						
										$elemento_campos_dinamicos = array();
										foreach($datos as $key => $value){
											if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
												$elemento_campos_dinamicos[$key] = $datos[$key];
											}
										}
										
										$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
										$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
										
										if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
						
											if($fecha_elemento_my == $fecha_my){
												
												if(!$formulario_valor_inicial->fijo){
													$campo_indicador = $valor_inicial->id_campo_unidad;
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_inicial->id_tipo_unidad;
													$id_unidad_origen = $valor_inicial->id_unidad;
													//$id_unidad_destino = 18; // Unidad (Unidad)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
													
													if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
														$valor_unidad_fija = $datos["unidad_residuo"];
														$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
													} else {
														$valor_unidad_dinamica = $datos[$campo_indicador]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
													}
												} else {
													//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
													$valor_unidad_dinamica = $datos[14]; 
													$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
													$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
												}
						
											} else {
												
												$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = 0;
											
											}
											
										}
						
									}
						
								}
								
								if($valor_inicial->operador){
						
									$operador = $valor_inicial->operador;
									$valor_operador = $valor_inicial->valor_operador;
									
									if($operador == "+"){
										$total_valor_inicial = $total_valor_inicial + $valor_operador;
									}
									if($operador == "-"){
										$total_valor_inicial = $total_valor_inicial - $valor_operador;
									}
									if($operador == "*"){
										$total_valor_inicial = $total_valor_inicial * $valor_operador;
									}
									if($operador == "/"){
										$total_valor_inicial = $total_valor_inicial / $valor_operador;
									}
									
								}
								// Fin Cálculo valor inicial
								
								// Cálculo valores operación
								$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
								
								$total_valor_calculo_final = 0;
								$array_valores_operacion_compuesta = array();
								
								foreach($array_operacion_compuesta as $index => $operacion_compuesta){
									
									$operador = key($operacion_compuesta);
									$id_valor = $operacion_compuesta[key($operacion_compuesta)];
									
									$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
									$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
										"id_kpi_valores" => $valor_calculo->id,
										"deleted" => 0
									))->result_array();
									$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
									
									if(!$formulario_valor_calculo->fijo){
										$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										));
										$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
											"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
											"deleted" => 0
										))->result_array();
									} else {
										$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
											"id_formulario" => $formulario_valor_calculo->id,
											"deleted" => 0
										))->result_array();
									}
									
									$total_valor_calculo = 0;
									
									if($valor_calculo->id && count($valores_condicion_calculo)) {
										
										$array_datos_valores_condicion_calculo = array();
										
										foreach($valores_condicion_calculo as $valor_condicion_calculo){
											
											$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
											$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
											
											if($valor_condicion_categoria){
												$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
											}
											if($valor_condicion_tipo_tratamiento){
												$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
											}
											if($valor_condicion_id_campo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
											}
											if($valor_condicion_id_campo_fijo){
												$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
											}
										}
										
									}
									
									if(count($elementos_formulario_calculo)){
						
										foreach($elementos_formulario_calculo as $elemento){
									
											$datos = json_decode($elemento["datos"], TRUE);
											$fecha_elemento = $datos["fecha"];
						
											$elemento_campos_dinamicos = array();
											foreach($datos as $key => $value){
												if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
													$elemento_campos_dinamicos[$key] = $datos[$key];
												}
											}
											
											$fecha_elemento_my = date("Y-m", strtotime($fecha_elemento));
											$fecha_my = date("Y-m", strtotime($anio . "-" . $numero_mes));
											
											if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
												
												if($fecha_elemento_my == $fecha_my){
													
													// TRANSFORMACIÓN
													$id_tipo_unidad_origen = $valor_calculo->id_tipo_unidad;
													$id_unidad_origen = $valor_calculo->id_unidad;
													//$id_unidad_destino = 18; // Unidad (Unidad)
													$id_unidad_destino = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $proyecto->client_id, "id_tipo_unidad" => $id_tipo_unidad_origen))->id_unidad;
						
													$fila_conversion = $this->Conversion_model->get_one_where(
														array(
															"id_tipo_unidad" => $id_tipo_unidad_origen,
															"id_unidad_origen" => $id_unidad_origen,
															"id_unidad_destino" => $id_unidad_destino
														)
													);
													$valor_transformacion = $fila_conversion->transformacion;
														
													if(!$formulario_valor_calculo->fijo){	
														$campo_indicador = $valor_calculo->id_campo_unidad;
														if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
															$valor_unidad_fija = $datos["unidad_residuo"];
															$valor_unidad_fija = $valor_unidad_fija * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
														} else {
															$valor_unidad_dinamica = $datos[$campo_indicador]; 
															$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
															$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
														}
													} else {
														//valor_unidad_dinamica = campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
														$valor_unidad_dinamica = $datos[14]; 
														$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
														$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
													}
						
												} else {
													
													$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = 0;
												
												}
						
											}
						
										}
						
									}
									
									if($valor_calculo->operador){
									
										//$operador = $valor_calculo->operador;
										$valor_operador = $valor_calculo->valor_operador;
										
										if($valor_calculo->operador == "+"){
											$total_valor_calculo = $total_valor_calculo + $valor_operador;
										}
										if($valor_calculo->operador == "-"){
											$total_valor_calculo = $total_valor_calculo - $valor_operador;
										}
										if($valor_calculo->operador == "*"){
											$total_valor_calculo = $total_valor_calculo * $valor_operador;
										}
										if($valor_calculo->operador == "/"){
											$total_valor_calculo = $total_valor_calculo / $valor_operador;
										}
										
									}
									
									if($operador){
						
										$array_valores_operacion_compuesta[] = array(
											$operador => $total_valor_calculo
										);
							
									}
									
								}
								
								// Cálculo total final
								$total_valor_calculo_final = $total_valor_inicial;
								
								foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
									
									$operador = key($valor_operacion_compuesta);
									$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
																		
									if($operador == "+"){
										$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
									}
									if($operador == "-"){
										$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
									}
									if($operador == "*"){
										$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
									}
									if($operador == "/"){
										$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
									}
							
								}
								
								$total_valor_serie = $total_valor_calculo_final;
								$array_donated_solutions_beneficiaries[$nombre_serie][$anio][$numero_mes] = $valor->id ? $total_valor_serie : 0;

							}
							
						}
						
					} // Fin foreach meses
					
				} // Fin foreach años

			} // Fin foreach series gráficos
		
		} // Fin foreach estructuras gráficos

		$view_data["array_valores_waste_recycling_monthly"] = $array_valores_waste_recycling_monthly;
		$view_data["array_valores_energy_consumption"] = $array_valores_energy_consumption;
		$view_data["array_water_consumption_by_origin"] = $array_water_consumption_by_origin;
		$view_data["array_water_reused_by_type"] = $array_water_reused_by_type;
		$view_data["array_expenditure_local_suppliers"] = $array_expenditure_local_suppliers;
		$view_data["array_donated_solutions_beneficiaries"] = $array_donated_solutions_beneficiaries;		
		
		return $view_data;
		
	}
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->input->post('id_proyecto');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		
		// Imágenes de gráficos
		$graficos["image_grafico_total_waste_produced"] = $this->input->post("image_grafico_total_waste_produced");
		$graficos["image_grafico_waste_recycling_totals"] = $this->input->post("image_grafico_waste_recycling_totals");
		$graficos["image_grafico_waste_recycling_monthly"] = $this->input->post("image_grafico_waste_recycling_monthly");
		$graficos["image_grafico_total_emissions_by_source"] = $this->input->post("image_grafico_total_emissions_by_source");
		$graficos["image_grafico_energy_consumption_source_type"] = $this->input->post("image_grafico_energy_consumption_source_type");
		$graficos["image_grafico_energy_consumption"] = $this->input->post("image_grafico_energy_consumption");
		$graficos["image_grafico_water_consumption_by_origin"] = $this->input->post("image_grafico_water_consumption_by_origin");
		$graficos["image_grafico_water_consumption_by_origin_2"] = $this->input->post("image_grafico_water_consumption_by_origin_2");
		$graficos["image_grafico_water_reused_by_type"] = $this->input->post("image_grafico_water_reused_by_type");
		$graficos["image_grafico_water_reused_by_type_2"] = $this->input->post("image_grafico_water_reused_by_type_2");
		$graficos["image_grafico_proportion_expenses_dedicated_local_suppliers"] = $this->input->post("image_grafico_proportion_expenses_dedicated_local_suppliers");
		$graficos["image_grafico_expenditure_local_suppliers"] = $this->input->post("image_grafico_expenditure_local_suppliers");
		$graficos["image_grafico_solutions_actions_facilities"] = $this->input->post("image_grafico_solutions_actions_facilities");
		$graficos["image_grafico_donated_solutions_beneficiaries"] = $this->input->post("image_grafico_donated_solutions_beneficiaries");
		
		$view_data = $this->generate_report($id_proyecto, $fecha_desde, $fecha_hasta);
		$view_data["graficos"] = $graficos;
		$view_data["info_cliente"] = $info_cliente;
		$view_data["id_proyecto"] = $id_proyecto;
		
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["info_proyecto"] = $info_proyecto;
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".lang("kpi")."_".lang("charts_by_project")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".lang("kpi")."_".lang("charts_by_project")."_".date('Y-m-d'));
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
		$html = $this->load->view('kpi_charts_by_project/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".lang("kpi")."_".lang("charts_by_project")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;

		
	}
	

}