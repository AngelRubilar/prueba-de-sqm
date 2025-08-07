<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KPI_Charts_between_projects extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_client_context_module = 2;
		$this->id_client_context_submodule = 7;
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
		
		$this->template->rander("kpi_charts_between_projects/index", $view_data);
		
    }
	
	function get_kpi_report(){
		
		$id_pais = $this->input->post('id_pais');
		$id_fase = $this->input->post('id_fase');
		$id_tech = $this->input->post('id_tech');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		
		$view_data = $this->generate_report($id_pais, $id_fase, $id_tech, $fecha_desde, $fecha_hasta);
		$html_kpi_report = $this->load->view('kpi_charts_between_projects/kpi_charts_between_projects', $view_data, TRUE);

		echo $html_kpi_report;
		
	}
	
	function generate_report($id_pais, $id_fase, $id_tech, $fecha_desde, $fecha_hasta){
		
		$id_cliente = $this->login_user->client_id;
		
		$masa_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 1)); // MASA
		$unidad_masa_client_config = $this->Unity_model->get_one($masa_client_config->id_unidad);
		$view_data["unidad_masa_config"] = $unidad_masa_client_config->nombre;
		
		$energia_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 4)); // ENERGÍA
		$unidad_energia_client_config = $this->Unity_model->get_one($energia_client_config->id_unidad);
		$view_data["unidad_energia_config"] = $unidad_energia_client_config->nombre;
		
		$volumen_client_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => 2)); // VOLUMEN
		$unidad_volumen_client_config = $this->Unity_model->get_one($volumen_client_config->id_unidad);
		$view_data["unidad_volumen_config"] = $unidad_volumen_client_config->nombre;

		$view_data["sigla_cliente"] = $this->Clients_model->get_one($id_cliente)->sigla;
		$view_data["fecha_actual"] = get_current_utc_time("Y-m-d");
		
		$array_proyectos = array();
		$proyectos_rel_fase = $this->Project_rel_phases_model->get_all_where(array(
			"id_fase" => $id_fase,
			"deleted" => 0
		))->result();
		
		foreach($proyectos_rel_fase as $rel){
			$proyecto = $this->Projects_model->get_one($rel->id_proyecto);
			if($id_tech){
				if($proyecto->client_id == $id_cliente && $proyecto->id_pais == $id_pais && $proyecto->id_tech == $id_tech){
					$array_proyectos[] = $proyecto->id;
				}
			} else {
				if($proyecto->client_id == $id_cliente && $proyecto->id_pais == $id_pais){
					$array_proyectos[] = $proyecto->id;
				}
			}
		}
		sort($array_proyectos);
		
		$array_nombres_proyectos = array();
		foreach($array_proyectos as $id_proyecto){
			$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
			$array_nombres_proyectos[$id_proyecto] = $nombre_proyecto;
		}
		$view_data["array_nombres_proyectos"] = $array_nombres_proyectos;
		
		$array_datos_proyectos = array();
		foreach($array_proyectos as $id_proyecto){
			$estructuras_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
				"id_proyecto" => $id_proyecto,
				"submodulo_grafico" => "charts_by_project",
				"deleted" => 0
			))->result_array();
			$array_datos_proyectos[$id_proyecto] = $estructuras_graficos;
		}
		
		$array_estructuras_graficos = array();
		foreach($array_datos_proyectos as $id_proyecto => $estructura_graficos){
			
			$proyecto = $this->Projects_model->get_one($id_proyecto);
			
			foreach($estructura_graficos as $seccion){

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
										
										// TRANSFORMACIÓN
										$id_tipo_unidad_origen = $valor->id_tipo_unidad;
										$id_unidad_origen = $valor->id_unidad;
										
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
											$total_valor = $total_valor + $valor_unidad_fija;
										} else {
											$valor_unidad_dinamica = $datos[$campo_indicador]; 
											$valor_unidad_dinamica = $valor_unidad_dinamica * $valor_transformacion;
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
						
						$total_valor = $total_valor_calculo_final;
						// Fin Cálculo total final

					}
	
					if($seccion["item"] == "water" && $seccion["subitem"] == "water_consumption_by_origin"){
						$array_estructuras_graficos[$id_proyecto][$seccion["item"]][$seccion["subitem"]][$seccion["tipo_grafico"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
					} elseif($seccion["item"] == "water" && $seccion["subitem"] == "water_reused_by_type") {
						$array_estructuras_graficos[$id_proyecto][$seccion["item"]][$seccion["subitem"]][$seccion["tipo_grafico"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
					} else {
						$array_estructuras_graficos[$id_proyecto][$seccion["item"]][$seccion["subitem"]][$nombre_serie] = ($valor->id) ? $total_valor : NULL;
					}
	
					unset($array_datos_valores_condicion);

				}
				
			}
			
		}
		
		$array_total_residuos_producidos = array();
		$array_reciclaje_residuos = array();
		$array_total_emisiones_fuente = array();
		$array_total_consumo_energia = array();
		$array_consumo_energia_tipo_fuente = array();
		$array_total_consumo_agua = array();
		$array_consumo_agua_procedencia = array();
		$array_agua_reutilizada_tipo = array();
		$array_social_gastos = array();
		
		foreach($array_estructuras_graficos as $id_proyecto => $secciones){
			$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
			foreach($secciones as $item => $subitems){
				if($item == "materials_and_waste"){
					$array_total_residuos_producidos[$id_proyecto] = array($nombre_proyecto => array_sum($subitems["total_waste_produced"]));
					$array_reciclaje_residuos[$id_proyecto] = array($nombre_proyecto => $subitems["waste_recycling_totals"]);
				}
				if($item == "emissions"){
					$array_total_emisiones_fuente[$id_proyecto] = array($nombre_proyecto => $subitems["total_emissions_by_source"]);
				}
				if($item == "energy"){
					$array_total_consumo_energia[$id_proyecto] = array($nombre_proyecto => array_sum($subitems["energy_consumption_source_type"]));
					$array_consumo_energia_tipo_fuente[$id_proyecto] = array($nombre_proyecto => $subitems["energy_consumption_source_type"]);
				}
				if($item == "water"){
					$array_total_consumo_agua[$id_proyecto] = array($nombre_proyecto => array_sum($subitems["water_consumption_by_origin"]["chart_bars"]));
					$array_consumo_agua_procedencia[$id_proyecto] = array($nombre_proyecto => $subitems["water_consumption_by_origin"]["chart_bars"]);
					$array_agua_reutilizada_tipo[$id_proyecto] = array($nombre_proyecto => $subitems["water_reused_by_type"]["chart_bars_percentage"]);
				}
				if($item == "social"){
					$array_social_gastos[$id_proyecto] = array($nombre_proyecto => $subitems["proportion_expenses_dedicated_local_suppliers"]);
				}
			}
		}
		
		$array_reciclaje_residuos_final = array();
		foreach($array_reciclaje_residuos as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_reciclaje_residuos_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}
		
		$array_total_emisiones_fuente_final = array();
		foreach($array_total_emisiones_fuente as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_total_emisiones_fuente_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}
		
		$array_consumo_energia_tipo_fuente_final = array();
		foreach($array_consumo_energia_tipo_fuente as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_consumo_energia_tipo_fuente_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}
		
		$array_consumo_agua_procedencia_final = array();
		foreach($array_consumo_agua_procedencia as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_consumo_agua_procedencia_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}
		
		$array_agua_reutilizada_tipo_final = array();
		foreach($array_agua_reutilizada_tipo as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_agua_reutilizada_tipo_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}
		
		$array_social_gastos_final = array();
		foreach($array_social_gastos as $id_proyecto => $valor_proyecto){
			foreach($valor_proyecto as $nombre_proyecto => $valores){
				foreach($valores as $nombre_serie => $valor) {
					$array_social_gastos_final[$nombre_serie][$nombre_proyecto] = $valor;
				}
			}
		}

		$view_data["array_total_residuos_producidos"] = $array_total_residuos_producidos;
		$view_data["array_reciclaje_residuos"] = $array_reciclaje_residuos_final;
		$view_data["array_total_emisiones_fuente"] = $array_total_emisiones_fuente_final;
		$view_data["array_total_consumo_energia"] = $array_total_consumo_energia;
		$view_data["array_consumo_energia_tipo_fuente"] = $array_consumo_energia_tipo_fuente_final;
		$view_data["array_total_consumo_agua"] = $array_total_consumo_agua;
		$view_data["array_consumo_agua_procedencia"] = $array_consumo_agua_procedencia_final;
		$view_data["array_agua_reutilizada_tipo"] = $array_agua_reutilizada_tipo_final;
		$view_data["array_social_gastos"] = $array_social_gastos_final;

		$view_data["fecha_desde"] = $fecha_desde;
		$view_data["fecha_hasta"] = $fecha_hasta;
		
		return $view_data;
		
	}
	
	function get_pdf(){
		
		$id_cliente = $this->login_user->client_id;
		$id_pais = $this->input->post('id_pais');
		$id_fase = $this->input->post('id_fase');
		$id_tech = $this->input->post('id_tech');
		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		
		// Imágenes de gráficos
		$graficos["image_grafico_total_waste_produced"] = $this->input->post("image_grafico_total_waste_produced");
		$graficos["image_grafico_waste_recycling_totals"] = $this->input->post("image_grafico_waste_recycling_totals");
		$graficos["image_grafico_total_emissions_by_source"] = $this->input->post("image_grafico_total_emissions_by_source");
		$graficos["image_grafico_total_energy_consumption"] = $this->input->post("image_grafico_total_energy_consumption");
		$graficos["image_grafico_energy_consumption_source_type"] = $this->input->post("image_grafico_energy_consumption_source_type");
		$graficos["image_grafico_total_water_consumption"] = $this->input->post("image_grafico_total_water_consumption");
		$graficos["image_grafico_water_consumption_by_origin"] = $this->input->post("image_grafico_water_consumption_by_origin");
		$graficos["image_grafico_water_reused_by_type"] = $this->input->post("image_grafico_water_reused_by_type");
		$graficos["image_grafico_social_expenses"] = $this->input->post("image_grafico_social_expenses");
		
		$view_data = $this->generate_report($id_pais, $id_fase, $id_tech, $fecha_desde, $fecha_hasta);
		$view_data["graficos"] = $graficos;
		$view_data["info_cliente"] = $info_cliente;
		
		
		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".lang("kpi")."_".lang("charts_between_projects")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".lang("kpi")."_".lang("charts_between_projects")."_".date('Y-m-d'));
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
		$html = $this->load->view('kpi_charts_between_projects/pdf_view', $view_data, true);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf_file_name = $info_cliente->sigla."_".lang("kpi")."_".lang("charts_between_projects")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;		
		
	}

}