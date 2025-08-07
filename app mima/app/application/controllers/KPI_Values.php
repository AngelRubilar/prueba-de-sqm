<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KPI_Values extends MY_Controller {
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");		
    }

    function index() {
		
		// Filtro Cliente
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// Filtro Fase
		
		$array_fases[] = array("id" => "", "text" => "- ".lang("phase")." -");
		$fases = $this->Phases_model->get_dropdown_list(array("nombre"), 'id');
		foreach($fases as $id => $nombre_fase){
			if($id == 2 || $id == 3){
				$array_fases[] = array("id" => $id, "text" => $nombre_fase);
			}
		}
		$view_data['fases_dropdown'] = json_encode($array_fases);
		
		// Filtro Proyecto
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
        $this->template->rander("kpi_values/index", $view_data);
    }
	
	function modal_form() {

        $id_kpi_value = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));
		
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		
		$fases = $this->Phases_model->get_dropdown_list(array("nombre"), 'id');
		$array_fases = array("" => "-");
		foreach($fases as $id => $nombre_fase){
			if($id == 2 || $id == 3){
				$array_fases[$id] = $nombre_fase;
			}
		}
		$view_data["fases"] = $array_fases;
		
		$view_data["proyectos"] = array("" => "-");
		$view_data["tipo_unidad"] = array("" => "-") + $this->Unity_type_model->get_dropdown_list(array("nombre"), "id");
		
		$array_operadores = array(
			"" => "-",
			"+" => lang("addition") . " ( + )" ,
			"-" => lang("subtraction") . " ( - )",
			"*" => lang("multiplication") . " ( * )",
			"/" => lang("division") . " ( / )",
		);
		
		$view_data["array_operadores"] = $array_operadores;
		$view_data["array_unidades"] = array("" => "-");
		
		if($id_kpi_value){
			
			$model_info = $this->KPI_Values_model->get_one($id_kpi_value);
			$view_data['model_info'] = $model_info;
			$id_tipo_formulario = $model_info->id_tipo_formulario;
			
			$formulario = $this->Forms_model->get_one($model_info->id_formulario);
			$view_data["formulario"] = $formulario;

			$proyecto_rel_fase = $this->Project_rel_phases_model->get_all_where(array(
				"id_fase" => $model_info->id_fase,
				"deleted" => 0
			))->result();
			
			$proyectos = array();
			foreach($proyecto_rel_fase as $rel){
				$proyecto = $this->Projects_model->get_one($rel->id_proyecto);
				if($proyecto->client_id == $model_info->id_cliente){
					$proyectos[$proyecto->id] = $proyecto->title;
				}
			}
			
			$view_data["proyectos"] = $proyectos;
			
			$array_unidades = array("" => "-");
			$unidades = $this->Unity_model->get_all_where(array(
				"id_tipo_unidad" => $model_info->id_tipo_unidad,
				"deleted" => 0
			))->result_array();
			
			if(count($unidades)){
				foreach($unidades as $unidad){
					$array_unidades[$unidad["id"]] = $unidad["nombre"];
				}
			}
			
			$view_data["array_unidades"] = $array_unidades;
			
			if($model_info->tipo_valor == "simple"){
				
				// Tipos Formulario
				$array_tipos_formulario = array();
				$tipos_formulario = array("" => "-") + $this->Form_types_model->get_dropdown_list(array("nombre"), "id");
				foreach($tipos_formulario as $id => $nombre){
					if($id != 2){ // Si el tipo de formulario es distinto de Mantenedora
						$array_tipos_formulario[$id] = $nombre;
					}
				}
				
				$view_data["tipos_formulario"] = $array_tipos_formulario;
				
				$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
					"id_proyecto" => $model_info->id_proyecto,
					"deleted" => 0
				))->result();
				
				// Formularios
				$array_formularios = array();
				foreach($formularios_rel_proyecto as $rel){
					$formulario = $this->Forms_model->get_one($rel->id_formulario);
					
					// Los formularios que se despliegan para el valor deben tener asociados al menos 1 campo dinámico de tipo unidad o numero.
					$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array(
						"id_formulario" => $formulario->id,
						"deleted" => 0
					))->result();
					
					/*foreach($campos_rel_formulario as $rel){
						$campo = $this->Fields_model->get_one($rel->id_campo);
						if($campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 15){
							if($formulario->id_tipo_formulario == $model_info->id_tipo_formulario){
								$array_formularios[$formulario->id] = $formulario->nombre;
							}
						}
					
					}*/
					
					foreach($campos_rel_formulario as $rel){
						$campo = $this->Fields_model->get_one($rel->id_campo);
						if($formulario->id_tipo_formulario == $id_tipo_formulario && $id_tipo_formulario == 3){
							if($campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 15){
								$array_formularios[$formulario->id] = $formulario->nombre;
							}
						} elseif($formulario->id_tipo_formulario == $id_tipo_formulario) {
							$array_formularios[$formulario->id] = $formulario->nombre;
						}
					}



				}
				
				if($model_info->id_tipo_formulario == "3"){
				
					// Agrego formulario fijo a lista de formularios
					$campo_fijo_rel_form_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_all_where(array(
						"id_proyecto" => $model_info->id_proyecto,
						"deleted" => 0
					))->result();
						
					foreach($campo_fijo_rel_form_rel_proyecto as $rel){
	
						$formulario_fijo = $this->Forms_model->get_one($rel->id_formulario);
						if($formulario_fijo->codigo_formulario_fijo == "or_educacion_ambiental"){
							$array_formularios[$formulario_fijo->id] = $formulario_fijo->nombre;
							break;
						}
					}
				
				}
				
				$view_data["array_formularios"] = $array_formularios;
		
				$formulario = $this->Forms_model->get_one($model_info->id_formulario);
				
				// Campo Indicador
				$array_campos_unidad = array();
				
				if(!$formulario->fijo){
				
					if($formulario->id_tipo_formulario == "1"){
						
						// Unidad Fija
						$campo_unidad_fija = json_decode($formulario->unidad, TRUE);
						$nombre_unidad = $campo_unidad_fija["nombre_unidad"];
						$unidad_id = 0; // Se asume que el id = 0 es el campo de tipo unidad fijo
						$array_campos_unidad[$unidad_id] = $nombre_unidad;
			
					}
					
					$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array(
						"id_formulario" => $formulario->id,
						"deleted" => 0
					))->result();
					
					// Campos de tipo unidad dinámicos del formulario
					foreach($campos_rel_formulario as $rel){
						$campo = $this->Fields_model->get_one($rel->id_campo);
						if($campo->id_tipo_campo == 15){
							$array_campos_unidad[$campo->id] = $campo->nombre;
						}
					}

				} else {
					
					if($formulario->id_tipo_formulario == "3"){
						// Unidad Fija
						// Campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
						$campo_fijo_unidad = $this->Fixed_fields_model->get_one(14); 
						$array_campos_unidad[$campo_fijo_unidad->id] = $campo_fijo_unidad->nombre;				
					}
					
				}
				
				$view_data["array_campos_unidad"] = $array_campos_unidad;
				// Fin Campo Indicador
				
				// Condición
				if($formulario->id_tipo_formulario == "1"){
					
					$categories = $this->Categories_model->get_categories_of_material_of_form($formulario->id)->result();
					$array_categorias = array();
					foreach($categories as $index => $key){
						$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
						if($row_alias->alias){
							$nombre = $row_alias->alias;
						}else{
							$nombre = $key->nombre;
						}
						$array_categorias[$key->id] = $nombre;
					}
					$view_data["array_categorias"] = $array_categorias;
					
					// Valor categoría
					$kpi_valores_condicion_categoria = $this->KPI_Values_condition_model->get_one_where(array(
						"id_kpi_valores" => $model_info->id,
						"is_category" => 1,
						"deleted" => 0
					));
	
					$view_data["kpi_valor_condicion_categoria"] = $kpi_valores_condicion_categoria->valor;
					
					// Valor tipo tratamiento
					$kpi_valores_condicion_tipo_tratamiento = $this->KPI_Values_condition_model->get_one_where(array(
						"id_kpi_valores" => $model_info->id,
						"is_tipo_tratamiento" => 1,
						"deleted" => 0
					));
					
					$view_data["kpi_valor_condicion_tipo_tratamiento"] = $kpi_valores_condicion_tipo_tratamiento->valor;
					
				}
				
				if(!$formulario->fijo){
					
					$array_campos_dinamicos = array();
					foreach($campos_rel_formulario as $rel){
						$campo = $this->Fields_model->get_one($rel->id_campo);
						if($campo->id_tipo_campo == 6 || $campo->id_tipo_campo == 16 || $campo->id_tipo_campo == 9){
							$array_campos_dinamicos[$campo->id] = $campo->nombre;
						}
					}
					
					$view_data["array_campos_dinamicos"] = $array_campos_dinamicos;	
	
					
					// Valores campos dinámicos
					$kpi_valores_condicion_campos_dinamicos = $this->KPI_Values_condition_model->get_all_where(array(
						"id_kpi_valores" => $model_info->id,
						"is_category" => 0,
						"is_tipo_tratamiento" => 0,
						"deleted" => 0
					))->result_array();
					
					$array_kpi_valores_condicion_campos_dinamicos = array();
					foreach($kpi_valores_condicion_campos_dinamicos as $campo_dinamico){
						$array_kpi_valores_condicion_campos_dinamicos[$campo_dinamico["id_campo"]] = $campo_dinamico["valor"];
					}
					$view_data["kpi_valores_condicion_campos_dinamicos"] = $array_kpi_valores_condicion_campos_dinamicos;
				
				} else {
					
					// Campo fijo "Tipo Educación Ambiental" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 12
					$campo_tipo_edu_amb = $this->Fixed_fields_model->get_one(12);
					$opciones_campo_tipo_edu_amb = json_decode($campo_tipo_edu_amb->opciones, TRUE);
					$array_opciones_campo_tipo_edu_amb = array();
					foreach($opciones_campo_tipo_edu_amb as $opcion){
						$array_opciones_campo_tipo_edu_amb[$opcion["value"]] = $opcion["text"];
					}
					
					// Campo fijo "Tipo de Inducción" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 13
					$campo_tipo_induccion = $this->Fixed_fields_model->get_one(13);
					$opciones_campo_tipo_induccion = json_decode($campo_tipo_induccion->opciones, TRUE);
					$array_opciones_campo_tipo_induccion = array();
					foreach($opciones_campo_tipo_induccion as $opcion){
						$array_opciones_campo_tipo_induccion[$opcion["value"]] = $opcion["text"];
					}
					
					$view_data["campo_tipo_edu_amb"] = $campo_tipo_edu_amb;
					$view_data["campo_tipo_induccion"] = $campo_tipo_induccion;
					$view_data["array_valores_tipo_edu_amb"] = $array_opciones_campo_tipo_edu_amb;
					$view_data["array_valores_tipo_induccion"] = $array_opciones_campo_tipo_induccion;
					
					// Valores campos fijos
					$kpi_valores_condicion_campos_fijos = $this->KPI_Values_condition_model->get_all_where(array(
						"id_kpi_valores" => $model_info->id,
						"id_campo" => NULL,
						"is_category" => 0,
						"is_tipo_tratamiento" => 0,
						"deleted" => 0
					))->result_array();
					
					$array_kpi_valores_condicion_campos_fijos = array();
					foreach($kpi_valores_condicion_campos_fijos as $campo_fijo){
						$array_kpi_valores_condicion_campos_fijos[$campo_fijo["id_campo_fijo"]] = $campo_fijo["valor"];
					}
					$view_data["array_kpi_valores_condicion_campos_fijos"] = $array_kpi_valores_condicion_campos_fijos;

				}
				
			}
			
			if($model_info->tipo_valor == "compound"){
				
				//$view_data["array_valores"] = $this->KPI_Values_model->get_dropdown_list(array("nombre_valor"), 'id', array("tipo_valor" => "simple"));
				$valores = $this->KPI_Values_model->get_all_where(array(
					"id_cliente" => $model_info->id_cliente,
					"id_proyecto" => $model_info->id_proyecto,
					"tipo_valor" => "simple",
					"deleted" => 0
				))->result();
				
				$array_valores = array();
				foreach($valores as $valor){
					$array_valores[$valor->id] = $valor->nombre_valor;
				}
				
				$view_data["array_valores"] = $array_valores;
				
				$view_data["array_operacion_compuesta"] = json_decode($model_info->operacion_compuesta, TRUE);
				//var_dump($view_data["array_operacion_compuesta"]);
				
			}
			
			// Se debe validar que si un valor está siendo utilizado en Reporte KPI, no se puede editar su tipo de unidad y unidad
			$kpi_reporte = $this->KPI_Report_structure_model->get_one_where(array(
				"id_cliente" => $model_info->id_cliente,
				"id_proyecto" => $model_info->id_proyecto,
				"deleted" => 0
			));
			
			$valor_ocupado_reporte = FALSE;
			if($kpi_reporte->id){
				$array_datos = json_decode($kpi_reporte->datos, TRUE);
				foreach($array_datos as $index => $dato){
					if($model_info->id == $dato["valor"]){
						$valor_ocupado_reporte = TRUE;
					}
					
				}
			}
			
			$view_data["valor_ocupado_reporte"] = $valor_ocupado_reporte;
			
			// Se debe validar que si un valor está siendo utilizado en Gráficos KPI, no se puede editar su tipo de unidad y unidad
			$kpi_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
				"id_cliente" => $model_info->id_cliente,
				"id_proyecto" => $model_info->id_proyecto,
				"deleted" => 0
			))->result();
			
			$valor_ocupado_graficos = FALSE;
			if(count($kpi_graficos)){
				foreach($kpi_graficos as $kpi_grafico){
					$array_datos = json_decode($kpi_grafico->series, TRUE);
					foreach($array_datos as $index => $value){
						if($model_info->id == $value){
							$valor_ocupado_graficos = TRUE;
							break;
						}
					}
				}
			}

			$view_data["valor_ocupado_graficos"] = $valor_ocupado_graficos;
		}
		
        $this->load->view('kpi_values/modal_form', $view_data);
    }
	
	
	function save() {
		
		$id_kpi_value = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$id_tipo_formulario = $this->input->post('id_tipo_formulario');
		$id_formulario = $this->input->post('id_formulario');
		$id_cliente = $this->input->post('id_cliente');
		$tipo_valor = $this->input->post('tipo_valor');
		
		$id_tipo_unidad = $this->input->post('tipo_unidad');
		$id_unidad = $this->input->post('unidad');
		
		$data_kpi_value = array(
			"id_cliente" => $id_cliente,
			"id_fase" => $this->input->post('id_fase'),
			"id_proyecto" => $this->input->post('id_proyecto'),
			"tipo_valor" => $tipo_valor,
			"nombre_valor" => trim($this->input->post('nombre_valor')),
			"id_tipo_formulario" => $id_tipo_formulario,
			"id_formulario" => $id_formulario,
			"id_campo_unidad" => $this->input->post('id_campo_unidad'),
			"operador" => ($tipo_valor == "simple") ? $this->input->post('operador') : NULL,
			"valor_operador" => $this->input->post('valor_operador') ? $this->input->post('valor_operador') : 0,
			"valor_inicial" => ($tipo_valor == "simple") ? NULL : $this->input->post('valor_inicial'),
			"id_tipo_unidad" => $id_tipo_unidad,
			"id_unidad" => $id_unidad
			//"valor_calculo" => $this->input->post('valor_calculo'),
		);
		
		$categoria = $this->input->post("categoria") ? $this->input->post("categoria") : 0;
		$tipo_tratamiento = $this->input->post("tipo_tratamiento") ? $this->input->post("tipo_tratamiento") : 0;
		$campos_dinamicos = $this->input->post("campos_dinamicos");
		$campos_or_fijos = $this->input->post("campos_or_fijos");

		// Mantener tipo de unidad y unidad en caso de que estos campos esten deshabilitados
		if($id_kpi_value){
			$valor = $this->KPI_Values_model->get_one($id_kpi_value);
			if(!$this->input->post('tipo_unidad')){
				$data_kpi_value["id_tipo_unidad"] = $valor->id_tipo_unidad;
			}
			if(!$this->input->post('unidad')){
				$data_kpi_value["id_unidad"] = $valor->id_unidad;
			}
		}
		
		// Validación de clones (Edit)
		if($id_kpi_value){
			
			if($tipo_valor == "simple"){
				
				$formulario = $this->Forms_model->get_one($id_formulario);
				
				$filter_clone = $data_kpi_value;
				unset($filter_clone["valor_inicial"]);
				$filter_clone["deleted"] = "0";
				$similar_values = $this->KPI_Values_model->get_all_where($filter_clone)->result_array();
				
				if(count($similar_values)){
					
					foreach($similar_values as $similar_value){
						
						if($similar_value["id"] != $valor->id){
							
							// Verificar si las condiciones del valor similar ($values_condition)
							// son iguales a las condiciones del valor que se está editando
							
							$array_values_condition = array();
							$array_values_condition_edit = array();
							
							$values_conditions = $this->KPI_Values_condition_model->get_all_where(array(
								"id_kpi_valores" => $similar_value["id"],
								"deleted" => 0
							))->result_array();
							
							foreach($values_conditions as $condition){
								
								unset($condition["id"]);
								unset($condition["id_kpi_valores"]);
								unset($condition["created_by"]);
								unset($condition["created"]);
								unset($condition["modified_by"]);
								unset($condition["modified"]);
								$array_values_condition[] = $condition;
								
							}
							
							$array_values_condition_to_json = json_encode($array_values_condition);

							if($formulario->id_tipo_formulario == "1"){
								
								$categories = $this->Categories_model->get_categories_of_material_of_form($formulario->id)->result();
								$array_categorias = array();
								foreach($categories as $index => $key){
									$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
									if($row_alias->alias){
										$nombre = $row_alias->alias;
									}else{
										$nombre = $key->nombre;
									}
									$array_categorias[$key->id] = $nombre;
								}
								
								if(count($array_categorias)){
				
									$data_kpi_valores_condicion_categoria = array(
										//"id_kpi_valores" => $save_id,
										"id_campo" => NULL,
										"id_campo_fijo" => NULL,
										"is_category" => "1",
										"is_tipo_tratamiento" => "0",
										"valor" => (string)$categoria,
										"deleted" => "0"
									);
									$array_values_condition_edit[] = $data_kpi_valores_condicion_categoria;
								}
								
							}
							
							// Tipo Tratamiento
							if($formulario->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
									
								$data_kpi_valores_condicion_tipo_tratamiento = array(
									"id_campo" => NULL,
									"id_campo_fijo" => NULL,
									"is_category" => "0",
									"is_tipo_tratamiento" => "1",
									"valor" => (string)$tipo_tratamiento,
									"deleted" => "0"
								);
								$array_values_condition_edit[] = $data_kpi_valores_condicion_tipo_tratamiento;
							}
							
							// Campos Dinámicos
							if(!$formulario->fijo){
								
								foreach($campos_dinamicos as $id_campo => $valor_campo){
									$data_kpi_valores_condicion = array(
										"id_campo" => (string)$id_campo,
										"id_campo_fijo" => NULL,
										"is_category" => "0",
										"is_tipo_tratamiento" => "0",
										"valor" => $valor_campo ? (string)$valor_campo : "0",
										"deleted" => "0"
									);
									$array_values_condition_edit[] = $data_kpi_valores_condicion;
								}
								
							} else {
								
								foreach($campos_or_fijos as $id_campo => $valor_campo){
									$data_kpi_valores_condicion = array(
										"id_campo" => NULL,
										"id_campo_fijo" => (string)$id_campo,
										"is_category" => "0",
										"is_tipo_tratamiento" => "0",
										"valor" => $valor_campo ? (string)$valor_campo : "0",
										"deleted" => "0"
									);
									$array_values_condition_edit[] = $data_kpi_valores_condicion;
								}
								
							}
							
							$array_values_condition_edit_to_json = json_encode($array_values_condition_edit);
							
							if($array_values_condition_to_json == $array_values_condition_edit_to_json){
								echo json_encode(array("success" => false, 'message' => lang('error_occurred_kpi_value_duplicated')));
								exit();
							}
							
						} else {
							//var_dump("no hay valores similares");
						}
						
						
					}
										
				} else {
					//var_dump("no hay valores similares");
				}
				
			}
			
			if($tipo_valor == "compound"){
				
				$operadores = $this->input->post("operador");
				$valores_calculo = $this->input->post("valor_calculo");
				
				array_shift($operadores);
				array_shift($valores_calculo);
				
				$array_opciones = array_map(function($index, $value) {
					return array($index => $value);
				}, $operadores, $valores_calculo);
		
				$array_opciones_json = json_encode($array_opciones, TRUE);
				
				$filter_clone = $data_kpi_value;
				unset($filter_clone["id_tipo_formulario"]);
				unset($filter_clone["id_formulario"]);
				unset($filter_clone["id_campo_unidad"]);
				unset($filter_clone["operador"]);
				unset($filter_clone["valor_operador"]);
				$filter_clone["operacion_compuesta"] = $array_opciones_json;
				
				
				$filter_clone["deleted"] = "0";
				$similar_values = $this->KPI_Values_model->get_all_where($filter_clone)->result_array();
				
				if(count($similar_values)){
					
					foreach($similar_values as $similar_value){
						
						if($similar_value["id"] != $valor->id){
							echo json_encode(array("success" => false, 'message' => lang('error_occurred_kpi_value_duplicated')));
							exit();
						}
						
					}
					
				}
				
			}
					
		} else { // Validación de clones (Insert)
			
			// Validando que no sea un registro clon de otro (valores simples)
			if($tipo_valor == "simple"){
				
				$formulario = $this->Forms_model->get_one($id_formulario);
				
				$filter_clone = $data_kpi_value;
				unset($filter_clone["valor_inicial"]);
				$filter_clone["deleted"] = "0";
				$similar_values = $this->KPI_Values_model->get_all_where($filter_clone)->result_array();
		
				if(count($similar_values)){
					
					foreach($similar_values as $similar_value){
						
						$values_conditions = $this->KPI_Values_condition_model->get_all_where(array(
							"id_kpi_valores" => $similar_value["id"],
							"deleted" => 0
						))->result_array();
						
						$array_values_condition = array();
						$array_values_condition_insert = array();
						
						foreach($values_conditions as $condition){
							
							unset($condition["id"]);
							unset($condition["id_kpi_valores"]);
							unset($condition["created_by"]);
							unset($condition["created"]);
							unset($condition["modified_by"]);
							unset($condition["modified"]);
							$array_values_condition[] = $condition;
							
						}

						$array_values_condition_to_json = json_encode($array_values_condition);
						
						if($formulario->id_tipo_formulario == "1"){
							
							$categories = $this->Categories_model->get_categories_of_material_of_form($formulario->id)->result();
							$array_categorias = array();
							foreach($categories as $index => $key){
								$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
								if($row_alias->alias){
									$nombre = $row_alias->alias;
								}else{
									$nombre = $key->nombre;
								}
								$array_categorias[$key->id] = $nombre;
							}
							
							if(count($array_categorias)){
			
								$data_kpi_valores_condicion_categoria = array(
									//"id_kpi_valores" => $save_id,
									"id_campo" => NULL,
									"id_campo_fijo" => NULL,
									"is_category" => "1",
									"is_tipo_tratamiento" => "0",
									"valor" => (string)$categoria,
									"deleted" => "0"
								);
								$array_values_condition_insert[] = $data_kpi_valores_condicion_categoria;
							}
							
						}
						
						// Tipo Tratamiento
						if($formulario->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
								
							$data_kpi_valores_condicion_tipo_tratamiento = array(
								"id_campo" => NULL,
								"id_campo_fijo" => NULL,
								"is_category" => "0",
								"is_tipo_tratamiento" => "1",
								"valor" => (string)$tipo_tratamiento,
								"deleted" => "0"
							);
							$array_values_condition_insert[] = $data_kpi_valores_condicion_tipo_tratamiento;
						}
					
						// Campos Dinámicos
						if(!$formulario->fijo){
							
							foreach($campos_dinamicos as $id_campo => $valor_campo){
								$data_kpi_valores_condicion = array(
									"id_campo" => (string)$id_campo,
									"id_campo_fijo" => NULL,
									"is_category" => "0",
									"is_tipo_tratamiento" => "0",
									"valor" => $valor_campo ? (string)$valor_campo : "0",
									"deleted" => "0"
								);
								$array_values_condition_insert[] = $data_kpi_valores_condicion;
							}
							
						} else {
							
							foreach($campos_or_fijos as $id_campo => $valor_campo){
								$data_kpi_valores_condicion = array(
									"id_campo" => NULL,
									"id_campo_fijo" => (string)$id_campo,
									"is_category" => "0",
									"is_tipo_tratamiento" => "0",
									"valor" => $valor_campo ? (string)$valor_campo : "0",
									"deleted" => "0"
								);
								$array_values_condition_insert[] = $data_kpi_valores_condicion;
							}
							
						}
						
						$array_values_condition_insert_to_json = json_encode($array_values_condition_insert);
						
					}
					
					if($array_values_condition_to_json == $array_values_condition_insert_to_json){
						echo json_encode(array("success" => false, 'message' => lang('error_occurred_kpi_value_duplicated')));
						exit();
					} 
					
				}
				
			}
			
			// Validando que no sea un registro clon de otro (valores compuestos)
			if($tipo_valor == "compound"){
				
				$operadores = $this->input->post("operador");
				$valores_calculo = $this->input->post("valor_calculo");
				
				array_shift($operadores);
				array_shift($valores_calculo);
				
				$array_opciones = array_map(function($index, $value) {
					return array($index => $value);
				}, $operadores, $valores_calculo);

				$array_opciones_json = json_encode($array_opciones, TRUE);
				
				$filter_clone = $data_kpi_value;
				unset($filter_clone["id_tipo_formulario"]);
				unset($filter_clone["id_formulario"]);
				unset($filter_clone["id_campo_unidad"]);
				unset($filter_clone["operador"]);
				unset($filter_clone["valor_operador"]);
				$filter_clone["operacion_compuesta"] = $array_opciones_json;
				
				
				$filter_clone["deleted"] = "0";
				$similar_values = $this->KPI_Values_model->get_all_where($filter_clone)->result_array();
				
				if(count($similar_values)){
					echo json_encode(array("success" => false, 'message' => lang('error_occurred_kpi_value_duplicated')));
					exit();
				}
				
			}
			
		}

		// De aquí hacia abajo, es donde ya pasó las validaciones de posibles clones, y hace el ingreso o edición según sea el caso.
		if($id_kpi_value){
			// Antes de guardar la edición, consulto el registro.
			$kpi_value = $this->KPI_Values_model->get_one($id_kpi_value);
			$data_kpi_value["modified_by"] = $this->login_user->id;
			$data_kpi_value["modified"] = get_current_utc_time();
			$save_id = $this->KPI_Values_model->save($data_kpi_value, $id_kpi_value);
		} else {
			$data_kpi_value["created_by"] = $this->login_user->id;
			$data_kpi_value["created"] = get_current_utc_time();
			$save_id = $this->KPI_Values_model->save($data_kpi_value);
		}
		
		if($id_kpi_value){ // Edit
		
			if($kpi_value->tipo_valor == "simple"){
			
				if($kpi_value->id_tipo_formulario == $id_tipo_formulario){
					
					if($kpi_value->id_formulario == $id_formulario){
						
						$formulario = $this->Forms_model->get_one($kpi_value->id_formulario);
						
						if(!$formulario->fijo){
							
							// Categoria
							$categories = $this->Categories_model->get_categories_of_material_of_form($kpi_value->id_formulario)->result();
	
							$array_categorias = array();
							foreach($categories as $index => $key){
								$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $kpi_value->id_cliente, "deleted" => 0));
								if($row_alias->alias){
									$nombre = $row_alias->alias;
								}else{
									$nombre = $key->nombre;
								}
								$array_categorias[$key->id] = $nombre;
							}
							
							if(count($array_categorias)){
								$kpi_valores_condicion = $this->KPI_Values_condition_model->get_one_where(array(
									"id_kpi_valores" => $id_kpi_value,
									"is_category" => 1,
									"deleted" => 0
								));
								$data_kpi_valores_condicion_categoria = array(
									"valor" => $categoria,
									"modified_by" => $this->login_user->id,
									"modified" => get_current_utc_time(),
								);
								$save_condition_categoria = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_categoria, $kpi_valores_condicion->id);
							}
							
							// Tipo Tratamiento
			
							if($kpi_value->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
								$kpi_valores_condicion = $this->KPI_Values_condition_model->get_one_where(array(
									"id_kpi_valores" => $id_kpi_value,
									"is_tipo_tratamiento" => 1,
									"deleted" => 0
								));
								$data_kpi_valores_condicion_tipo_tratamiento = array(
									"valor" => $tipo_tratamiento,
									"modified_by" => $this->login_user->id,
									"modified" => get_current_utc_time(),
								);
								$save_condition_tipo_tratamiento = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_tipo_tratamiento, $kpi_valores_condicion->id);
							}
							
							// Campos Dinámicos
							$kpi_valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
								"id_kpi_valores" => $id_kpi_value,
								"deleted" => 0
							))->result_array();
				
							foreach($kpi_valores_condicion as $condicion){
								if($condicion["id_campo"] != NULL){
									$data_kpi_valores_condicion = array(
										"valor" => $campos_dinamicos[$condicion["id_campo"]] ? $campos_dinamicos[$condicion["id_campo"]] : 0,
										"modified_by" => $this->login_user->id,
										"modified" => get_current_utc_time(),
									);
									$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion, $condicion["id"]);
								}	
							}
						
						} else {
							
							// Campos Fijos
							$kpi_valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
								"id_kpi_valores" => $id_kpi_value,
								"deleted" => 0
							))->result_array();
								
							foreach($kpi_valores_condicion as $condicion){
								if($condicion["id_campo_fijo"] != NULL){
									$data_kpi_valores_condicion = array(
										"valor" => $campos_or_fijos[$condicion["id_campo_fijo"]] ? $campos_or_fijos[$condicion["id_campo_fijo"]] : 0,
										"modified_by" => $this->login_user->id,
										"modified" => get_current_utc_time(),
									);
									$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion, $condicion["id"]);
								}	
							}
							
						}
						
					} else { // $kpi_value->id_formulario != $id_formulario
					
						$formulario_nuevo = $this->Forms_model->get_one($id_formulario);
						
						if(!$formulario_nuevo->fijo){
						
							// Borrar relaciones ya que cambió el formulario, y luego agregar nuevas filas de relaciones.
							$kpi_values_condition = $this->KPI_Values_condition_model->get_all_where(array("id_kpi_valores" => $id_kpi_value))->result();
							foreach($kpi_values_condition as $condition){
								$this->KPI_Values_condition_model->delete($condition->id);
							}
							
							$categories = $this->Categories_model->get_categories_of_material_of_form($formulario_nuevo->id)->result();
							$array_categorias = array();
							foreach($categories as $index => $key){
								$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario_nuevo->id_cliente, "deleted" => 0));
								if($row_alias->alias){
									$nombre = $row_alias->alias;
								}else{
									$nombre = $key->nombre;
								}
								$array_categorias[$key->id] = $nombre;
							}
							
							if(count($array_categorias)){
								$data_kpi_valores_condicion_categoria = array(
									"id_kpi_valores" => $id_kpi_value,
									"id_campo" => NULL,
									"is_category" => 1,
									"is_tipo_tratamiento" => 0,
									"valor" => $categoria,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time(),
								);
								$save_condition_categoria = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_categoria);
							}
		
							if($formulario_nuevo->flujo == "Residuo"){
								$data_kpi_valores_condicion_tipo_tratamiento = array(
									"id_kpi_valores" => $id_kpi_value,
									"id_campo" => NULL,
									"is_category" => 0,
									"is_tipo_tratamiento" => 1,
									"valor" => $tipo_tratamiento,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time(),
								);
								$save_condition_tipo_tratamiento = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_tipo_tratamiento);
							}
							
							// Campos Dinámicos
							foreach($campos_dinamicos as $id_campo => $valor_campo){
								$data_kpi_valores_condicion = array(
									"id_kpi_valores" => $id_kpi_value,
									"id_campo" => $id_campo,
									"is_category" => 0,
									"is_tipo_tratamiento" => 0,
									"valor" => (!$valor_campo || $valor_campo == "") ? NULL : $valor_campo,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time()
								);
								$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
							}	
						
						} else { // Si el formulario es fijo
							
							// Borrar relaciones ya que cambió el formulario, y luego agregar nuevas filas de relaciones.
							$kpi_values_condition = $this->KPI_Values_condition_model->get_all_where(array("id_kpi_valores" => $id_kpi_value))->result();
							foreach($kpi_values_condition as $condition){
								$this->KPI_Values_condition_model->delete($condition->id);
							}
							
							foreach($campos_or_fijos as $id_campo => $valor_campo){
								$data_kpi_valores_condicion = array(
									"id_kpi_valores" => $save_id,
									"id_campo_fijo" => $id_campo,
									"is_category" => 0,
									"is_tipo_tratamiento" => 0,
									"valor" => (!$valor_campo || $valor_campo == "") ? NULL : $valor_campo,
									"created_by" => $this->login_user->id,
									"created" => get_current_utc_time()
								);
								$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
								
							}
	
						}
		
					}
					
				} else { // $kpi_value->id_tipo_formulario != $id_tipo_formulario
					
					// Borrar relaciones ya que cambió el formulario, y luego agregar nuevas filas de relaciones.
					$kpi_values_condition = $this->KPI_Values_condition_model->get_all_where(array("id_kpi_valores" => $id_kpi_value))->result();
					foreach($kpi_values_condition as $condition){
						$this->KPI_Values_condition_model->delete($condition->id);
					}
					
					$formulario_nuevo = $this->Forms_model->get_one($id_formulario);
					
					if(!$formulario_nuevo->fijo){
					
						$categories = $this->Categories_model->get_categories_of_material_of_form($formulario_nuevo->id)->result();
						$array_categorias = array();
						foreach($categories as $index => $key){
							$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario_nuevo->id_cliente, "deleted" => 0));
							if($row_alias->alias){
								$nombre = $row_alias->alias;
							}else{
								$nombre = $key->nombre;
							}
							$array_categorias[$key->id] = $nombre;
						}
						
						if(count($array_categorias)){
							$data_kpi_valores_condicion_categoria = array(
								"id_kpi_valores" => $id_kpi_value,
								"id_campo" => NULL,
								"is_category" => 1,
								"is_tipo_tratamiento" => 0,
								"valor" => $categoria,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time(),
							);
							$save_condition_categoria = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_categoria);
						}
						
						if($formulario->flujo == "Residuo"){
							$data_kpi_valores_condicion_tipo_tratamiento = array(
								"id_kpi_valores" => $id_kpi_value,
								"id_campo" => NULL,
								"is_category" => 0,
								"is_tipo_tratamiento" => 1,
								"valor" => $tipo_tratamiento,
								"modified_by" => $this->login_user->id,
								"modified" => get_current_utc_time(),
							);
							
							$save_condition_tipo_tratamiento = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_tipo_tratamiento);
						}
						
						// Campos Dinámicos
						foreach($campos_dinamicos as $id_campo => $valor_campo){
							$data_kpi_valores_condicion = array(
								"id_kpi_valores" => $id_kpi_value,
								"id_campo" => $id_campo,
								"is_category" => 0,
								"is_tipo_tratamiento" => 0,
								"valor" => (!$valor_campo || $valor_campo == "") ? NULL : $valor_campo,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
						}
						
					} else { // Si el formulario es fijo
						
						// Borrar relaciones ya que cambió el formulario, y luego agregar nuevas filas de relaciones.
						$kpi_values_condition = $this->KPI_Values_condition_model->get_all_where(array("id_kpi_valores" => $id_kpi_value))->result();
						foreach($kpi_values_condition as $condition){
							$this->KPI_Values_condition_model->delete($condition->id);
						}
											
						foreach($campos_or_fijos as $id_campo => $valor_campo){
							$data_kpi_valores_condicion = array(
								"id_kpi_valores" => $save_id,
								"id_campo_fijo" => $id_campo,
								"is_category" => 0,
								"is_tipo_tratamiento" => 0,
								"valor" => (!$valor_campo || $valor_campo == "") ? NULL : $valor_campo,
								"created_by" => $this->login_user->id,
								"created" => get_current_utc_time()
							);
							$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
						}
						
					}

				}
			
			}
			
			if($tipo_valor == "compound"){

				$operadores = $this->input->post("operador");
				$valores_calculo = $this->input->post("valor_calculo");
				
				array_shift($operadores);
				array_shift($valores_calculo);

				$array_opciones = array_map(function($index, $value) {
					return array($index => $value);
				}, $operadores, $valores_calculo);
				$array_opciones_json = json_encode($array_opciones, TRUE);
				$data_kpi_value = array(
					"operacion_compuesta" => $array_opciones_json
				);

				$save_id = $this->KPI_Values_model->save($data_kpi_value, $id_kpi_value);
				
			}

		} else { // Insert
			
			$formulario = $this->Forms_model->get_one($id_formulario);
			
			if($tipo_valor == "simple"){
			
				// Categoría
				if($formulario->id_tipo_formulario == "1"){
					
					$categories = $this->Categories_model->get_categories_of_material_of_form($formulario->id)->result();
					$array_categorias = array();
					foreach($categories as $index => $key){
						$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
						if($row_alias->alias){
							$nombre = $row_alias->alias;
						}else{
							$nombre = $key->nombre;
						}
						$array_categorias[$key->id] = $nombre;
					}
					
					if(count($array_categorias)){
						$data_kpi_valores_condicion_categoria = array(
							"id_kpi_valores" => $save_id,
							"id_campo" => NULL,
							"is_category" => 1,
							"is_tipo_tratamiento" => 0,
							"valor" => $categoria,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_condition_categoria = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_categoria);
					}
					
				}
				
				// Tipo Tratamiento
				if($formulario->id_tipo_formulario == "1" && $formulario->flujo == "Residuo"){
					$data_kpi_valores_condicion_tipo_tratamiento = array(
						"id_kpi_valores" => $save_id,
						"id_campo" => NULL,
						"is_category" => 0,
						"is_tipo_tratamiento" => 1,
						"valor" => $tipo_tratamiento
					);
					$data_kpi_valores_condicion_tipo_tratamiento["created_by"] = $this->login_user->id;
					$data_kpi_valores_condicion_tipo_tratamiento["created"] = get_current_utc_time();
					$save_condition_tipo_tratamiento = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion_tipo_tratamiento);
				}
			
				// Campos Dinámicos
				
				if(!$formulario->fijo){
					
					foreach($campos_dinamicos as $id_campo => $valor_campo){
						$data_kpi_valores_condicion = array(
							"id_kpi_valores" => $save_id,
							"id_campo" => $id_campo,
							"is_category" => 0,
							"is_tipo_tratamiento" => 0,
							"valor" => $valor_campo ? $valor_campo : 0,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
					}
					
				} else {
					
					foreach($campos_or_fijos as $id_campo => $valor_campo){
						$data_kpi_valores_condicion = array(
							"id_kpi_valores" => $save_id,
							"id_campo_fijo" => $id_campo,
							"is_category" => 0,
							"is_tipo_tratamiento" => 0,
							"valor" => $valor_campo ? $valor_campo : 0,
							"created_by" => $this->login_user->id,
							"created" => get_current_utc_time()
						);
						$save_condition = $this->KPI_Values_condition_model->save($data_kpi_valores_condicion);
					}
					
				}
				
			}
			
			if($tipo_valor == "compound"){
				
				$operadores = $this->input->post("operador");
				$valores_calculo = $this->input->post("valor_calculo");
				
				array_shift($operadores);
				array_shift($valores_calculo);

				$array_opciones = array_map(function($index, $value) {
					return array($index => $value);
				}, $operadores, $valores_calculo);
				
				$array_opciones_json = json_encode($array_opciones, TRUE);

				$data_kpi_value = array(
					"operacion_compuesta" => $array_opciones_json
				);

				$save_id = $this->KPI_Values_model->save($data_kpi_value, $save_id);
			}

		}

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		// Si el valor está siendo utilizado en KPI Reportes o Gráficos.
		$valor = $this->KPI_Values_model->get_one($id);
		$kpi_reporte = $this->KPI_Report_structure_model->get_one_where(array(
			"id_cliente" => $valor->id_cliente,
			"id_proyecto" => $valor->id_proyecto,
			"deleted" => 0
		));
		
		$kpi_graficos = $this->KPI_Charts_structure_model->get_all_where(array(
			"id_cliente" => $valor->id_cliente,
			"id_proyecto" => $valor->id_proyecto,
			"deleted" => 0
		))->result_array();

		$valor_ocupado_reporte = FALSE;
		if($kpi_reporte->id){
			$array_datos = json_decode($kpi_reporte->datos, TRUE);
			foreach($array_datos as $index => $dato){
				if($valor->id == $dato["valor"]){
					$valor_ocupado_reporte = TRUE;
				}
				
			}
		}
		$valor_ocupado_graficos = FALSE;
		if(count($kpi_graficos)){
			foreach($kpi_graficos as $kpi_grafico){
				$series = json_decode($kpi_grafico["series"], TRUE);
				foreach($series as $nombre_serie => $id_valor){
					if($valor->id == $id_valor){
						$valor_ocupado_graficos = TRUE;
					}
				}
			}
		}
		if($valor_ocupado_reporte){
			 echo json_encode(array("success" => false, 'message' => lang('value_occupied_in_report')));
			 exit();
		}
		if($valor_ocupado_graficos){
			 echo json_encode(array("success" => false, 'message' => lang('value_occupied_in_charts')));
			 exit();
		}
		
		$kpi_values_condition = $this->KPI_Values_condition_model->get_all_where(array("id_kpi_valores" => $id))->result();
		foreach($kpi_values_condition as $condition){
			$this->KPI_Values_condition_model->delete($condition->id);
		}
		
        if ($this->input->post('undo')) {
            if ($this->KPI_Values_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->KPI_Values_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function list_data() {
		
		$options = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"id_fase" => $this->input->post('id_fase'),
			"id_proyecto" => $this->input->post('id_proyecto')
		);
		
        $list_data = $this->KPI_Values_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
			$result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->KPI_Values_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$row_data[] = $data->id;
		
		$span_nombre_valor = "<span>" . $data->nombre_valor . "</span>";
		$nombre_valor = modal_anchor(get_uri("KPI_Values/view/" . $data->id), $span_nombre_valor, array(
			"class" => "edit",
			"title" => lang("view_value"),
			"data-post-id" => $data->id
		));
		//$row_data[] = $data->nombre_valor;
		$row_data[] = $nombre_valor;
		$unidad = $this->Unity_model->get_one($data->id_unidad)->nombre;
		$row_data[] = $unidad ? $unidad : "-";
		$row_data[] = $data->nombre_cliente;
		$row_data[] = $data->nombre_fase;
		$row_data[] = $data->nombre_proyecto;
		
		$creado_por = $this->Users_model->get_one($data->created_by);
		$row_data[] = $creado_por->first_name . " " . $creado_por->last_name;
		
		$row_data[] = modal_anchor(get_uri("KPI_Values/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_value'), "data-post-id" => $data->id))
					. modal_anchor(get_uri("KPI_Values/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_value'), "data-post-id" => $data->id))
					. js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_value'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("KPI_Values/delete"), "data-action" => "delete-confirmation"));
		
        return $row_data;
    }
	
	function view($id_kpi_value = 0) {

        if ($id_kpi_value) {
            $options = array("id" => $id_kpi_value);
            $info_kpi_valor = $this->KPI_Values_model->get_details($options)->row();
            if ($info_kpi_valor) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_kpi_valor;
				
				$cliente = $this->Clients_model->get_one($info_kpi_valor->id_cliente);
				$view_data['cliente'] = $cliente->company_name;
				
				$fase = $this->Phases_model->get_one($info_kpi_valor->id_fase);
				$view_data['fase'] = $fase->nombre;
				
				$proyecto = $this->Projects_model->get_one($info_kpi_valor->id_proyecto);
				$view_data['proyecto'] = $proyecto->title;
				
				$view_data['tipo_unidad'] = $this->Unity_type_model->get_one($info_kpi_valor->id_tipo_unidad)->nombre;
				$view_data['unidad'] = $this->Unity_model->get_one($info_kpi_valor->id_unidad)->nombre;
				
				if($info_kpi_valor->tipo_valor == "simple"){
					
					$tipo_formulario = $this->Form_types_model->get_one($info_kpi_valor->id_tipo_formulario);
					$view_data["tipo_formulario"] = $tipo_formulario->nombre;
										
					$formulario = $this->Forms_model->get_one($info_kpi_valor->id_formulario);
					$view_data["formulario"] = $formulario;
					
					if(!$formulario->fijo){
					
						// Unidad Fija
						if($info_kpi_valor->id_campo_unidad == 0){
							$campo_unidad_fija = json_decode($formulario->unidad, TRUE);
							$nombre_unidad = $campo_unidad_fija["nombre_unidad"];
							$view_data["campo_unidad"] = $nombre_unidad;
						} else {
							$campo_unidad = $this->Fields_model->get_one($info_kpi_valor->id_campo_unidad);
							$view_data["campo_unidad"] = $campo_unidad->nombre;
						}
					
					} else {

						//campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
						$campo_unidad = $this->Fixed_fields_model->get_one(14);
						$view_data["campo_unidad"] = $campo_unidad->nombre;

					}

					// Valor categoría
					$kpi_valores_condicion_categoria = $this->KPI_Values_condition_model->get_one_where(array(
						"id_kpi_valores" => $info_kpi_valor->id,
						"is_category" => 1,
						"deleted" => 0
					));
										
					$view_data["kpi_valores_condicion_categoria"] = $kpi_valores_condicion_categoria;
					
					$categoria = $this->Categories_model->get_one($kpi_valores_condicion_categoria->valor);
					$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $categoria->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
					if($categoria_alias->alias){
						$nombre_categoria = $categoria_alias->alias;
					}else{
						$nombre_categoria = $categoria->nombre;
					}
					$view_data["categoria_valor"] = $categoria;
					$view_data["nombre_categoria"] = $nombre_categoria;

					// Valor tipo tratamiento
					$kpi_valores_condicion_tipo_tratamiento = $this->KPI_Values_condition_model->get_one_where(array(
						"id_kpi_valores" => $info_kpi_valor->id,
						"is_tipo_tratamiento" => 1,
						"deleted" => 0
					));
					$view_data["tipo_tratamiento"] = $this->Tipo_tratamiento_model->get_one($kpi_valores_condicion_tipo_tratamiento->valor)->nombre;
					
					// Valores campos dinámicos	
					$kpi_valores_condicion_campos_dinamicos = $this->KPI_Values_condition_model->get_all_where(array(
						"id_kpi_valores" => $info_kpi_valor->id,
						"is_category" => 0,
						"is_tipo_tratamiento" => 0,
						"deleted" => 0
					))->result_array();
					
					$array_kpi_valores_condicion_campos_dinamicos = array();
					foreach($kpi_valores_condicion_campos_dinamicos as $campo_dinamico){
						$array_kpi_valores_condicion_campos_dinamicos[$campo_dinamico["id_campo"]] = $campo_dinamico["valor"];
					}
					
					$array_campos_dinamicos = array();
					foreach($array_kpi_valores_condicion_campos_dinamicos as $id_campo => $valor){
						$campo = $this->Fields_model->get_one($id_campo);
						$array_campos_dinamicos[$campo->nombre] = $valor;
					}

					$view_data["array_campos_dinamicos"] = $array_campos_dinamicos;
					
					if($formulario->fijo){
						
						// Campo fijo "Tipo Educación Ambiental" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 12
						$campo_tipo_edu_amb = $this->Fixed_fields_model->get_one(12);
						// Campo fijo "Tipo de Inducción" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 13
						$campo_tipo_induccion = $this->Fixed_fields_model->get_one(13);
						
						$valor_campo_tipo_edu_amb = $this->KPI_Values_condition_model->get_one_where(array(
							"id_kpi_valores" => $info_kpi_valor->id,
							"id_campo_fijo" => 12,
							"deleted" => 0
						));

						$valor_campo_tipo_induccion = $this->KPI_Values_condition_model->get_one_where(array(
							"id_kpi_valores" => $info_kpi_valor->id,
							"id_campo_fijo" => 13,
							"deleted" => 0
						));

						$view_data["campo_tipo_edu_amb"] = $campo_tipo_edu_amb;
						$view_data["campo_tipo_induccion"] = $campo_tipo_induccion;
						$view_data["valor_campo_tipo_edu_amb"] = $valor_campo_tipo_edu_amb->valor;
						$view_data["valor_campo_tipo_induccion"] = $valor_campo_tipo_induccion->valor;
						
					}
					
				}
				
				if($info_kpi_valor->tipo_valor == "compound"){
					
					$valor_inicial = $this->KPI_Values_model->get_one($info_kpi_valor->valor_inicial);
					$view_data["valor_inicial"] = $valor_inicial->nombre_valor;
					
					//$valor_calculo = $this->KPI_Values_model->get_one($info_kpi_valor->valor_calculo);
					//$view_data["valor_calculo"] = $valor_calculo->nombre_valor;

					$operacion_compuesta = $info_kpi_valor->operacion_compuesta;
					$array_operacion_compuesta = json_decode($operacion_compuesta, TRUE);
					$view_data["array_operacion_compuesta"] = $array_operacion_compuesta;

				}

				$creado_por = $this->Users_model->get_one($info_kpi_valor->created_by);
				$view_data["creado_por"] = ($creado_por->id) ? $creado_por->first_name . " " . $creado_por->last_name : "-";
				
				$modificado_por = $this->Users_model->get_one($info_kpi_valor->modified_by);
				$view_data["modificado_por"] = ($modificado_por->id) ? $modificado_por->first_name . " " . $modificado_por->last_name : "-";
				
				$this->load->view('kpi_values/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

	function get_projects_of_client_phase(){
	
		$id_cliente = $this->input->post('id_cliente');
		$id_fase = $this->input->post('id_fase');
		$label_column = 'col-md-3';
		$field_column = 'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyecto_rel_fase = $this->Project_rel_phases_model->get_all_where(array(
			"id_fase" => $id_fase,
			"deleted" => 0
		))->result();
		
		$proyectos = array();
		foreach($proyecto_rel_fase as $rel){
			$proyecto = $this->Projects_model->get_one($rel->id_proyecto);
			if($proyecto->client_id == $id_cliente){
				$proyectos[$proyecto->id] = $proyecto->title;
			}
		}
		
		//$proyectos = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="id_proyecto" class="'.$label_column.'">'.lang('project').'</label>';
		$html .= '<div class="'.$field_column.'">';
		$html .= form_dropdown("id_proyecto", array("" => "-") + $proyectos, "", "id='id_proyecto' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function get_campos_tipo_valor(){
		
		$tipo_valor = $this->input->post('tipo_valor');
		$id_proyecto = $this->input->post('id_proyecto');
		
		$label_column = 'col-md-3';
		$field_column = 'col-md-9';
		$html = '';
		
		if($tipo_valor == "simple"){
			
			$array_tipos_formulario = array();
			$tipos_formulario = array("" => "-") + $this->Form_types_model->get_dropdown_list(array("nombre"), "id");
			foreach($tipos_formulario as $id => $nombre){
				if($id != 2){ // Si el tipo de formulario es distinto de Mantenedora
					$array_tipos_formulario[$id] = $nombre;
				}
			}
			
			
			$html .= '<div class="form-group">';
			$html .= 	'<label for="tipo_formulario" class="'.$label_column.'">'.lang('form_type').'</label>';
			$html .= 	'<div class="'.$field_column.'">';
			$html .= 		form_dropdown("id_tipo_formulario", array("" => "-") + $array_tipos_formulario, "", "id='id_tipo_formulario' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
			
			$html .= '<div id="tipo_valor_formulario_group">';
			$html .= 	'<div class="form-group">';
			$html .= 		'<label for="formulario" class="'.$label_column.'">'.lang('form').'</label>';
			$html .= 		'<div class="'.$field_column.'">';
			$html .= 			form_dropdown("id_formulario", array("" => "-") , "", "id='id_formulario' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
					
			$html .= '<div id="campos_formulario_group">';
			$html .= 	'<div class="form-group">';
			$html .= 		'<label for="id_campo_unidad" class="'.$label_column.'">'.lang('indicator_field').'</label>';
			$html .= 		'<div class="'.$field_column.'">';
			$html .= 			form_dropdown("id_campo_unidad", array("" => "-"), "", "id='id_campo_unidad' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 		'</div>';
			$html .= 	'</div>';
			
			
			
			
			$html .= 	'<div class="form-group">';
			$html .= 		'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
			$html .= 		'<div class="'.$field_column.'">';
			//$html .= 		'<div id="mensaje_validacion_sector_or_territory" class="pb10"></div>';
			//$html .= 			form_multiselect("condicion", array(), NULL, "id='condicion' class='multiple' multiple='multiple'");
			$html .= "Seleccionar Formulario";
			$html .= 		'</div>';
			$html .= 	'</div>';
			$html .= '</div>';
			
			
			
			
	
			$array_operadores = array(
				"" => "-",
				"+" => lang("addition") . " ( + )" ,
				"-" => lang("subtraction") . " ( - )",
				"*" => lang("multiplication") . " ( * )",
				"/" => lang("division") . " ( / )",
			);
			
			$html .= '<div class="form-group">';
			$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('operation').'</label>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		form_dropdown("operador", $array_operadores, "", "id='operador' class='select2'");
			$html .= 	'</div>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		form_input(array(
								"id" => "valor_operador",
								"name" => "valor_operador",
								"value" => $model_info->valor_operador,
								"class" => "form-control",
								"placeholder" => lang('value'),
								//"autofocus" => true,
								//"data-rule-required" => true,
								//"data-msg-required" => lang("field_required"),
								"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
								"data-msg-regex" => lang("number_or_decimal_required"),
								"autocomplete"=> "off",
								"maxlength" => "255"
							));
			$html .= 	'</div>';
			$html .= '</div>';
			
		}
		
		if($tipo_valor == "compound"){
			
			//$array_valores = $this->KPI_Values_model->get_dropdown_list(array("nombre_valor"), 'id', array("tipo_valor" => "simple"));
			
			
			$array_valores = array();
			$valores = $this->KPI_Values_model->get_all_where(array(
				"tipo_valor" => "simple",
				"id_proyecto" => $id_proyecto,
				"deleted" => 0
			))->result();
			
			foreach($valores as $valor){
				$array_valores[$valor->id] = $valor->nombre_valor;
			}
			
			/*
			$html .= '<div class="form-group">';
			$html .= 	'<label for="valor_inicial" class="'.$label_column.'">'.lang('initial_value').'</label>';
			$html .= 	'<div class="'.$field_column.'">';
			$html .= 		form_dropdown("valor_inicial", array("" => "-") + $array_valores, "", "id='valor_inicial' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
			*/
			$array_operadores = array(
				"" => "-",
				"+" => lang("addition") . " ( + )" ,
				"-" => lang("subtraction") . " ( - )",
				"*" => lang("multiplication") . " ( * )",
				"/" => lang("division") . " ( / )",
			);
			/*
			$html .= '<div class="form-group">';
			$html .= 	'<label for="operacion" class="'.$label_column.'">'.lang('compound_operation').'</label>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		form_dropdown("operador", $array_operadores, "", "id='operador' class='select2'");
			$html .= 	'</div>';
			$html .= 	'<div class="col-md-4">';
			$html .= 		form_dropdown("valor_calculo", array("" => "-") + $array_valores, "", "id='valor_calculo' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
			*/
			
			
			
			
			$html .= '<div class="form-group">';
			$html .= '<label for="valor_inicial" class="col-md-3">'.lang('initial_value').'</label>';
			$html .= '	<div class="col-md-9">';
			$html .= 	form_dropdown("valor_inicial", array("" => "-") + $array_valores, $model_info->valor_inicial, "id='valor_inicial' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= '	</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group" id="modelo" style="display:none;">';
			$html .= '	<label for="description" class="col-md-3 control-label"></label>';
			$html .= '	<div class="col-md-4">';
			$html .=    form_dropdown("operador[]", $array_operadores, "", "id='' class='select2'");
			$html .= '	</div>';
			$html .= '	<div class="col-md-4">';
			$html .=    form_dropdown("valor_calculo[]", array("" => "-") + $array_valores, "", "id='' class='select2'");
			$html .= '	</div>';
			$html .= '	<div class="col-md-1">';
			$html .= '		<button type="button" class="btn btn-sm btn-danger remover_opcion"><i class="fa fa-trash-o"></i></button>';
			$html .= '	</div>';
			$html .= '</div>';
			
			$html .= '<div class="form-group">';
			$html .= '	<label for="valor_operacion" class="col-md-3">'.lang('operation_values').'</label>';
			$html .= '	<div class="col-md-9">';
			$html .= '		<button type="button" id="agregar_valor_operacion" class="btn btn-xs btn-success col-sm-1"><i class="fa fa-plus"></i></button>';
			$html .= '		<button type="button" id="eliminar_valor_operacion" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1"><i class="fa fa-minus"></i></button>';
			$html .= '	</div>';
			$html .= '</div>';
			
			
			$html .= '<div id="grupo_valores_operacion">';
				
			$html .= '	<div class="form-group">';
			$html .= '		<label for="description" class="col-md-3"></label>';
			$html .= '		<div class="col-md-4">';
			$html .= 			form_dropdown("operador[1]", $array_operadores, "", "id='operador' class='select2'");
			$html .= '		</div>';
			$html .= '		<div class="col-md-4">';
			$html .= 			form_dropdown("valor_calculo[1]", array("" => "-") + $array_valores, "", "id='valor_calculo' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= '		</div>';
		
			$html .= '	</div>';
			
			
			
			
			
			
			
			
			
			
			
			
		
		}
		
		echo $html;
		
	}
	
	function get_tipo_valor_formulario_group(){
		
		$id_cliente = $this->input->post("id_cliente");
		$label_column = 'col-md-3';
		$field_column = 'col-md-9';
		$id_proyecto = $this->input->post("id_proyecto");
		$id_tipo_formulario = $this->input->post("id_tipo_formulario");
		
		$formularios_rel_proyecto = $this->Form_rel_project_model->get_all_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		))->result();
		
		$array_formularios = array();
		foreach($formularios_rel_proyecto as $rel){
			
			$formulario = $this->Forms_model->get_one($rel->id_formulario);

			// Los formularios de tipo Otros Registros que se despliegan para el valor deben tener asociados al menos 
			// 1 campo dinámico de tipo unidad o numero.
			$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array(
				"id_formulario" => $formulario->id,
				"deleted" => 0
			))->result();
			
			foreach($campos_rel_formulario as $rel){
				$campo = $this->Fields_model->get_one($rel->id_campo);
				if($formulario->id_tipo_formulario == $id_tipo_formulario && $id_tipo_formulario == 3){
					if($campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 15){
						$array_formularios[$formulario->id] = $formulario->nombre;
					}
				} elseif($formulario->id_tipo_formulario == $id_tipo_formulario) {
					$array_formularios[$formulario->id] = $formulario->nombre;
				}
			}
			
			if($id_tipo_formulario == "3"){
				// Agrego formulario fijo a lista de formularios
				$campo_fijo_rel_form_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_all_where(array(
					"id_proyecto" => $id_proyecto,
					"deleted" => 0
				))->result();
				
				foreach($campo_fijo_rel_form_rel_proyecto as $rel){
	
					$formulario_fijo = $this->Forms_model->get_one($rel->id_formulario);
					if($formulario_fijo->codigo_formulario_fijo == "or_educacion_ambiental"){
						$array_formularios[$formulario_fijo->id] = $formulario_fijo->nombre;
						break;
					}
				}
			}
			
		}

		$html .= "<div id='tipo_valor_formulario_group'>";
		$html .= 	'<div class="form-group">';
		$html .= 		'<label for="formulario" class="'.$label_column.'">'.lang('form').'</label>';
		$html .= 		'<div class="'.$field_column.'">';
		$html .= 			form_dropdown("id_formulario", array("" => "-") + $array_formularios, "", "id='id_formulario' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= 		'</div>';
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
	function get_fields_of_form(){
		
		$id_formulario = $this->input->post('id_formulario');
		$label_column = 'col-md-3';
		$field_column = 'col-md-9';
		
		if($id_formulario){
		
			$formulario = $this->Forms_model->get_one($id_formulario);
			
			$array_campos_unidad = array();
			
			if(!$formulario->fijo){
			
				if($formulario->id_tipo_formulario == "1"){
					
					// Unidad Fija
					$campo_unidad_fija = json_decode($formulario->unidad, TRUE);
					$nombre_unidad = $campo_unidad_fija["nombre_unidad"];
					$unidad_id = 0; // Se asume que el id = 0 es el campo de tipo unidad fijo
					$array_campos_unidad[$unidad_id] = $nombre_unidad;
		
				}
				
				$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array(
					"id_formulario" => $formulario->id,
					"deleted" => 0
				))->result();
				
				// Campos de tipo unidad dinámicos del formulario
				foreach($campos_rel_formulario as $rel){
					$campo = $this->Fields_model->get_one($rel->id_campo);
					if($campo->id_tipo_campo == 15){
						$array_campos_unidad[$campo->id] = $campo->nombre;
					}
				}
				
			} else {
				
				if($formulario->id_tipo_formulario == "3"){
					// Unidad Fija
					// Campo fijo de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 14
					$campo_fijo_unidad = $this->Fixed_fields_model->get_one(14); 
					$array_campos_unidad[$campo_fijo_unidad->id] = $campo_fijo_unidad->nombre;				
				}
				
			}
			
			$html = "";
			$html .= '<div class="form-group">';
			$html .= 	'<label for="id_campo_unidad" class="'.$label_column.'">'.lang('indicator_field').'</label>';
			$html .= 	'<div class="'.$field_column.'">';
			$html .= 		form_dropdown("id_campo_unidad", array("" => "-") + $array_campos_unidad, "", "id='id_campo_unidad' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html .= 	'</div>';
			$html .= '</div>';
				
			if($formulario->id){
			
				if($formulario->id_tipo_formulario == "1"){
					
					// Campos Fijo categoria si el formulario es registro ambiental
					$categories = $this->Categories_model->get_categories_of_material_of_form($formulario->id)->result();
					$array_categorias = array();
					foreach($categories as $index => $key){
						$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $formulario->id_cliente, "deleted" => 0));
						if($row_alias->alias){
							$nombre = $row_alias->alias;
						}else{
							$nombre = $key->nombre;
						}
						$array_categorias[$key->id] = $nombre;
					}
	
					// Campos Dinámicos
					$array_campos_dinamicos = array();
					foreach($campos_rel_formulario as $rel){
						$campo = $this->Fields_model->get_one($rel->id_campo);
						if($campo->id_tipo_campo == 6 || $campo->id_tipo_campo == 16 || $campo->id_tipo_campo == 9){
							$array_campos_dinamicos[$campo->id] = $campo->nombre;
						}
					}
					
					$html .= '<div class="form-group">';
					$html .= 	'<div class="col-md-12 p0">';
					$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
					
					
					if(count($array_categorias)){
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		lang("category");
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("categoria", array("" => "-") + $array_categorias, "", "id='categoria' class='select2 condicion'");
						$html .= 	'</div>';
						
						$html .= 	'</div>';
					}
					
					if($formulario->flujo == "Residuo"){
						
						if(count($array_categorias)){
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
						}
						
						$array_tipo_tratamiento = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"), 'id');
			
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		lang("type_of_treatment");
						$html .= 	'</div>';
						$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("tipo_tratamiento", array("" => "-") + $array_tipo_tratamiento, "", "id='tipo_tratamiento' class='select2 condicion'");
						$html .= 	'</div>';
						
						$html .= 	'</div>';
					}
					
					foreach($array_campos_dinamicos as $id_campo => $nombre_campo){
			
						$campo = $this->Fields_model->get_one($id_campo);
						
						if($campo->id_tipo_campo == 6){
							
							$opciones_campo = json_decode($campo->opciones, TRUE);
							$array_opciones_campo = array();
				
							
							foreach($opciones_campo as $index => $opcion){
								$array_opciones_campo[$opcion["value"]] = $opcion["text"];
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", $array_opciones_campo, $campo->default_value, "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
						}
						
						if($campo->id_tipo_campo == 9){
							
							$opciones_campo = json_decode($campo->opciones, TRUE);
							$array_opciones_campo = array();
							foreach($opciones_campo as $opcion){
								$array_opciones_campo[$opcion["value"]] = $opcion["text"];
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							//$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $campo->default_value, "id='campos_dinamicos' class='select2 condicion'");
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, "", "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
						} 
						
						if($campo->id_tipo_campo == 16){
							
							// Mantenedoras
							$default_value = json_decode($campo->default_value, TRUE);
							$id_mantenedora = $default_value["mantenedora"];
							$id_campo_mantenedora = $default_value["field_value"];
			
							// Buscar los valores de la mantenedora para dejarlos como opciones del select
							$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
								"id_formulario" => $id_mantenedora,
								"deleted" => 0
							));
							
							$valores_formulario = $this->Form_values_model->get_all_where(array(
								"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
								"deleted" => 0
							))->result();
			
							$array_opciones_campo = array();
							foreach($valores_formulario as $valor_formulario){
								
								$datos = json_decode($valor_formulario->datos, TRUE);
								$array_opciones_campo[$datos[$id_campo_mantenedora]] = $datos[$id_campo_mantenedora];
								
							}
							
							$html .= 	'<div class="col-md-12 p0">';
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		$nombre_campo;
							$html .= 	'</div>';
							$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
							$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, "", "id='campos_dinamicos' class='select2 condicion'");
							$html .= 	'</div>';
							
							$html .= 	'</div>';
						}
			
					}
					
					if(!count($array_campos_dinamicos)){
						
						$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
						$html .= 	'<div id="mensaje_form" class="col-md-9" style="padding-bottom: 10px;">';
						$html .= 	'* El formulario "' . $formulario->nombre . '"  no tiene campos de tipo selección, selección desde mantenedora o radio buttons.';
						$html .= 	'</div>';
						
					}
					
					$html .= '</div>';
					
				}
				
				
				if($formulario->id_tipo_formulario == "3"){
					
					if(!$formulario->fijo){
					
						// Campos Dinámicos
						$array_campos_dinamicos = array();
						foreach($campos_rel_formulario as $rel){
							$campo = $this->Fields_model->get_one($rel->id_campo);
							if($campo->id_tipo_campo == 6 || $campo->id_tipo_campo == 16 || $campo->id_tipo_campo == 9){
								$array_campos_dinamicos[$campo->id] = $campo->nombre;
							}
						}
						
						$html .= '<div class="form-group">';
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
						
						
						$loop_count = 0;
						foreach($array_campos_dinamicos as $id_campo => $nombre_campo){
				
							$campo = $this->Fields_model->get_one($id_campo);
							
							if($loop_count > 0){
								$html .= 	'<div class="col-md-12 p0">';
								$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							}
							
							
							if($campo->id_tipo_campo == 6){
								
								$opciones_campo = json_decode($campo->opciones, TRUE);
								$array_opciones_campo = array();
					
								
								foreach($opciones_campo as $index => $opcion){
									$array_opciones_campo[$opcion["value"]] = $opcion["text"];
								}
		
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", $array_opciones_campo, $campo->default_value, "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
								
							}
							
							if($campo->id_tipo_campo == 9){
								
								$opciones_campo = json_decode($campo->opciones, TRUE);
								$array_opciones_campo = array();
								foreach($opciones_campo as $opcion){
									$array_opciones_campo[$opcion["value"]] = $opcion["text"];
								}
				
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								//$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, $campo->default_value, "id='campos_dinamicos' class='select2 condicion'");
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, "", "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
								
							} 
							
							if($campo->id_tipo_campo == 16){
								
								// Mantenedoras
								$default_value = json_decode($campo->default_value, TRUE);
								$id_mantenedora = $default_value["mantenedora"];
								$id_campo_mantenedora = $default_value["field_value"];
				
								// Buscar los valores de la mantenedora para dejarlos como opciones del select
								$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
									"id_formulario" => $id_mantenedora,
									"deleted" => 0
								));
								
								$valores_formulario = $this->Form_values_model->get_all_where(array(
									"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
									"deleted" => 0
								))->result();
				
								$array_opciones_campo = array();
								foreach($valores_formulario as $valor_formulario){
									
									$datos = json_decode($valor_formulario->datos, TRUE);
									$array_opciones_campo[$datos[$id_campo_mantenedora]] = $datos[$id_campo_mantenedora];
									
								}
		
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		$nombre_campo;
								$html .= 	'</div>';
								$html .= 	'<div class="col-md-4" style="padding-bottom: 10px;">';
								$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", array("" => "-") + $array_opciones_campo, "", "id='campos_dinamicos' class='select2 condicion'");
								$html .= 	'</div>';
								
								$html .= 	'</div>';
							
							}
							
							$loop_count++;
							
						}
						
						if(!count($array_campos_dinamicos)){
							
							$html .= 	'<label for="condicion" class="'.$label_column.'"></label>';
							$html .= 	'<div id="mensaje_form" class="col-md-9" style="padding-bottom: 10px;">';
							$html .= 	'* El formulario "' . $formulario->nombre . '"  no tiene campos de tipo selección, selección desde mantenedora o radio buttons.';
							$html .= 	'</div>';
							
						}
						
						$html .= '</div>';
					
					} else {
						
						// Campo fijo "Tipo Educación Ambiental" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 12
						$campo_tipo_edu_amb = $this->Fixed_fields_model->get_one(12);
						$opciones_campo_tipo_edu_amb = json_decode($campo_tipo_edu_amb->opciones, TRUE);
						$array_opciones_campo_tipo_edu_amb = array();
						foreach($opciones_campo_tipo_edu_amb as $opcion){
							$array_opciones_campo_tipo_edu_amb[$opcion["value"]] = $opcion["text"];
						}
						
						// Campo fijo "Tipo de Inducción" de formulario Otros Registros fijo "Educación Ambiental". Siempre es id 13
						$campo_tipo_induccion = $this->Fixed_fields_model->get_one(13);
						$opciones_campo_tipo_induccion = json_decode($campo_tipo_induccion->opciones, TRUE);
						$array_opciones_campo_tipo_induccion = array();
						foreach($opciones_campo_tipo_induccion as $opcion){
							$array_opciones_campo_tipo_induccion[$opcion["value"]] = $opcion["text"];
						}
						
						$html .= '<div class="form-group">';
						
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="" class="col-md-3">'.lang('condition').'</label>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 			$campo_tipo_edu_amb->nombre;
						$html .= 		'</div>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("campos_or_fijos[".$campo_tipo_edu_amb->id."]", array("" => "-") + $array_opciones_campo_tipo_edu_amb, "", "id='' class='select2 condicion'");
						$html .= 		'</div>';
						$html .= 	'</div>';
						
						$html .= 	'<div class="col-md-12 p0">';
						$html .= 	'<label for="" class="col-md-3"></label>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 			$campo_tipo_induccion->nombre;
						$html .= 		'</div>';
						$html .= 		'<div class="col-md-4" style="padding-bottom: 10px;">';
						$html .= 		form_dropdown("campos_or_fijos[".$campo_tipo_induccion->id."]", array("" => "-") + $array_opciones_campo_tipo_induccion, "", "id='' class='select2 condicion'");
						$html .= 		'</div>';
						$html .= 	'</div>';
						
						$html .= '</div>';
						
					}
					
				}
				
			} else {
				
				$html .= '<label for="condicion" class="3">'.lang('condition').'</label>';
	
				$html .= '<div class="col-md-4" style="padding-bottom: 10px;">';
				$html .= 		$nombre_campo;
				$html .= '</div>';
				$html .= '<div class="col-md-4" style="padding-bottom: 10px;">';
				$html .= 		form_dropdown("campos_dinamicos[".$campo->id."]", $array_opciones_campo, $campo->default_value, "id='campos_dinamicos' class='select2 condicion'");
				$html .= '</div>';
				
				$html .= '</div>';
				
			}	
	
			echo $html;
		
		} else {
			
			$html .= 	'<div class="form-group">';
			$html .= 		'<label for="condicion" class="'.$label_column.'">'.lang('condition').'</label>';
			$html .= 		'<div class="'.$field_column.'">';
			$html .= 			"Seleccionar Formulario";
			$html .= 		'</div>';
			$html .= 	'</div>';
			
		}
		
	}
	
	function get_unidades_of_tipo_unidad(){
		
		$id_tipo_unidad = $this->input->post("id_tipo_unidad");
		$unidades = $this->Unity_model->get_all_where(array(
			"id_tipo_unidad" => $id_tipo_unidad,
			"deleted" => 0
		))->result_array();
		
		$array_unidades = array("" =>"-");
        if(count($unidades)){
            foreach($unidades as $unidad){
                $array_unidades[$unidad["id"]] = $unidad["nombre"];
            }
        }
		
		$html = '<div class="col-md-4">';
		$html .= form_dropdown("unidad", $array_unidades, "", "id='unidad' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		
		echo $html;

	}
	
}