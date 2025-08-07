<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_waste_detail extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        //$this->init_permission_checker("client");
		$this->id_modulo_cliente = 8;
		$this->id_submodulo_cliente = 8;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
    }
	
	function index(){
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$array_materiales = array();
		$array_materiales[""] = "-";
		$materials = $this->Thresholds_model->get_material_flow_project($proyecto->id,"Residuo")->result();
		foreach($materials as $material){
			$array_materiales[$material->id_material]=$material->nombre_material;
		}
		$client_info = $this->Clients_model->get_one($proyecto->client_id);
		$view_data['dropdown_material'] = $array_materiales;
		$view_data['id_project'] = $proyecto->id;
		$view_data['id_cliente'] = $proyecto->client_id;
		$view_data['client_info'] = $client_info;
		$view_data['project_info'] = $proyecto;
		//$this->access_only_allowed_members();
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");	
	
		// Filtro Categoría	
		$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
		$formularios_proyecto = $this->Forms_model->get_forms_of_project(array(
			"id_proyecto" => $proyecto->id,
			"id_tipo_formulario" => 1,
			"flujo" => "Residuo"
		))->result();
		
		foreach($formularios_proyecto as $formulario){

			$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array(
				"id_formulario" => $formulario->id,
				"deleted" => 0
			))->result();
			
			foreach($form_rel_mat_rel_cat as $rel){
				
				$categoria = $this->Categories_model->get_one($rel->id_categoria);
				$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $categoria->id, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
				$nombre_categoria = ($categoria_alias->alias) ? $categoria_alias->alias : $categoria->nombre;
				$array_categorias[$categoria->id] = array("id" => $categoria->id, "text" => $nombre_categoria);
			}
			
		}
		$view_data['categorias_dropdown'] = json_encode($array_categorias);

		// Filtro Tratamiento
		$forms_data = $this->Form_values_model->get_forms_values_of_forms_by_flux($proyecto->id, "Residuo")->result();
		$array_tipo_tratamiento[] = array("id" => "", "text" => "- ".lang("treatment")." -");
		foreach($forms_data as $form_data){
			$data = json_decode($form_data->datos, TRUE);
			if($data["tipo_tratamiento"]){
				$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $data["tipo_tratamiento"], "deleted" => 0));
				$array_tipo_tratamiento[$tipo_tratamiento->id] = array("id" => $tipo_tratamiento->id, "text" => $tipo_tratamiento->nombre);
			}
		}
		$view_data['tratamientos_dropdown'] = json_encode($array_tipo_tratamiento);
		
		$view_data['general_settings'] = $this->General_settings_model->get_one_where(array("id_proyecto" => $proyecto->id, "deleted" => 0));
		
		$this->template->rander("waste/client/detail/index", $view_data);
	}
	
	function list_data_table($id_project,$id_material,$date_since,$date_until){
		
		// Filtros AppTable
		$id_tratamiento = $this->input->post("id_tratamiento");
		$id_categoria = $this->input->post("id_categoria");
	
		$categories = $this->Categories_model->get_category_of_material($id_material)->result();
		$forms_data = $this->Form_values_model->get_forms_values_of_forms_by_flux($id_project,"Residuo")->result();
		$list_data = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					if(isset($data["fecha_retiro"])){
						$reg_date = $data["fecha"];
						$date = $data["fecha_retiro"];
						$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
						$verificacion_fecha_registro = $this->check_in_range($date_since,$date_until,$reg_date);
						if($verificacion_fecha_registro == true){
							if($verificacion_fecha == true){
								if(isset($data["fecha_retiro"])){
									$list_data[$form_data->id] = $data;
								}	
							}
						}
					}
				}
			}
		}

        $result = array();
        foreach ($list_data as $key => $data) {
			if($id_categoria && !$id_tratamiento){
				if($data["id_categoria"] == $id_categoria){
					$result[] = $this->_make_row($data,$id_project,$key);
				}
			}elseif(!$id_categoria && $id_tratamiento){
				if($data["tipo_tratamiento"] == $id_tratamiento){
					$result[] = $this->_make_row($data,$id_project,$key);
				}
			}elseif($id_categoria && $id_tratamiento){
				if($data["id_categoria"] == $id_categoria && $data["tipo_tratamiento"] == $id_tratamiento){
					$result[] = $this->_make_row($data,$id_project,$key);
				}
			}else {
				$result[] = $this->_make_row($data,$id_project,$key);
			}		
        }
        echo json_encode(array("data" => $result));
	}
	
	function download_file($id,$id_campo){
		
		$file_info = $this->Form_values_model->get_one($id);
		
		if(!$file_info){
			redirect("forbidden");
		}
		
		$datos = json_decode($file_info->datos,true);
		$filename = $datos[$id_campo];
		
		$datos_formulario = $this->Form_rel_project_model->get_details(array("id" => $file_info->id_formulario_rel_proyecto))->result();
		$id_cliente = $datos_formulario[0]->id_cliente;
		$id_proyecto = $datos_formulario[0]->id_proyecto;
		$id_formulario = $datos_formulario[0]->id_formulario;
		
		//serilize the path
        $file_data = serialize(array(array("file_name" => $filename)));
        download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/elemento_".$id."/", $file_data);
		
	}
	
	function _make_row($data, $id_project, $id_form_value){

		$material = $this->Materials_model->get_material_of_category($data["id_categoria"])->result();
		$material_nombre = $material["0"]->nombre;
		
		$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $data["id_categoria"], 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
		if($alias->alias){
			$nombre_categoria = $alias->alias;
		}else{
			$categoria = $this->Categories_model->get_one($data["id_categoria"]);
			$nombre_categoria = $categoria->nombre;
		}
		
		$unidad_value = to_number_project_format($data["unidad_residuo"], $id_project)." (".$data["unidad"].")";
		
		$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $data["tipo_tratamiento"], "deleted" => 0));
		if($data["tipo_tratamiento"] == $tipo_tratamiento->id){
			$tipo_tratamiento = $tipo_tratamiento->nombre;
		}

		if(isset($data["nombre_archivo_retiro"])){
			//$evidencia_retiro = $data["nombre_archivo_retiro"];
			$evidencia_retiro = anchor(get_uri("client_waste_detail/download_file/".$id_form_value."/nombre_archivo_retiro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => $data["nombre_archivo_retiro"]));
		}else{
			$evidencia_retiro = "-";
		}
		
		if(isset($data["nombre_archivo_recepcion"])){
			//$evidencia_recepcion = $data["nombre_archivo_recepcion"];
			$evidencia_recepcion = anchor(get_uri("client_waste_detail/download_file/".$id_form_value."/nombre_archivo_recepcion"), "<i class='fa fa fa-cloud-download'></i>", array("title" => $data["nombre_archivo_recepcion"]));
		}else{
			$evidencia_recepcion = "-";
		}
		$fecha_retiro = get_date_format($data["fecha_retiro"], $id_project);
		$row_data = array(
			$material_nombre,
			$nombre_categoria,
			//$data["unidad_residuo"],
			$unidad_value,
			$tipo_tratamiento,
			$fecha_retiro,
			$evidencia_retiro,
			$evidencia_recepcion
			
		);

		return $row_data;
	}
	
	function list_data(){
		
		$id_material = $this->input->post('id_material');
		$date_since = $this->input->post('date_since');
		$date_until = $this->input->post('date_until');
		$id_project = $this->input->post('id_project');
		$id_cliente = $this->input->post('id_cliente');

		$id_unidad_volumen_destino = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_project, "id_tipo_unidad" => 2, "deleted" => 0))->id_unidad;
		$id_unidad_masa_destino = $this->Reports_units_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_project, "id_tipo_unidad" => 1, "deleted" => 0))->id_unidad;
		$unidad_volumen_destino = $this->Unity_model->get_one($id_unidad_volumen_destino)->id;
		$unidad_masa_destino = $this->Unity_model->get_one($id_unidad_masa_destino)->id;
		$nombre_unidad_volumen_config = $this->Unity_model->get_one($id_unidad_volumen_destino)->nombre;
		$nombre_unidad_masa_config = $this->Unity_model->get_one($id_unidad_masa_destino)->nombre;
		$indicator_data = $this->Indicators_model->get_all_where(array("id_client" => $id_cliente, "id_project" => $id_project,"deleted"=>0))->result();
		$categories = $this->Categories_model->get_category_of_material($id_material)->result();
		$forms_data = $this->Form_values_model->get_forms_values_of_forms_by_flux($id_project,"Residuo")->result();
		$general_settings = $this->General_settings_model->get_one_where(array("id_proyecto" => $id_project));
		$decimal_numbers = $general_settings->decimal_numbers;
		$decimals_separator = ($general_settings->decimals_separator == 1) ? "." : ",";
		$thousands_separator = ($general_settings->thousands_separator == 1)? "." : ",";
		$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result_array();
		
		/* INDICADORES */
		$array_data = array();
		$array_indicator_rel_form_total = array();
		foreach($indicator_data as $indicator){
			$data_indicadores = json_decode($indicator->categories,"true");
			foreach($data_indicadores as $key => $val){
				foreach($categories as $categorie){
					if($categorie->id == $key){
						foreach($forms_data as $form_data){
							$data_formularios = json_decode($form_data->datos,"true");
							if($key == $data_formularios["id_categoria"]){
								$date = $data_formularios["fecha"];
								$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
								if($verificacion_fecha == true){
									$array_data[$indicator->indicator_name]["unidad_indicador"] = $indicator->unit;
									$array_data[$indicator->indicator_name]["unidad_formulario"] = $data_formularios["unidad"];
									$array_data[$indicator->indicator_name]["forms_values"][$key][] = $data_formularios["unidad_residuo"];
								}
							}
						}
					}
				}
			}

			$data_indicators_values = $this->Client_indicators_model->get_all_where(array("id_indicador" => $indicator->id,"deleted"=>0))->result();
			foreach($data_indicators_values as $data_indicator_value){
				if($indicator->id == $data_indicator_value->id_indicador){
					$value = $this->get_total_value_of_indicator($date_since,$date_until,$data_indicator_value);
					if(is_null($value)){
						continue;
					}else{
						$array_data[$indicator->indicator_name]["color"] = $indicator->color;
						$array_data[$indicator->indicator_name]["icon"] = $indicator->icon;
						$array_data[$indicator->indicator_name]["id_fontawesome"] = $indicator->id_fontawesome;
						$array_data[$indicator->indicator_name]["valor_indicador"][]=$value;
					}
				}
			}
		}

		/*
		foreach($indicator_data as $indicator){
			$data_indicadores = json_decode($indicator->categories,"true");
			foreach($data_indicadores as $key => $val){
				foreach($forms_data as $form_data){
					$data_formularios = json_decode($form_data->datos,"true");
					if($key == $data_formularios["id_categoria"]){
						$date = $data_formularios["fecha"];
						$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
						if($verificacion_fecha == true){
							$array_data[$indicator->indicator_name]["unidad_indicador"] = $indicator->unit;
							$array_data[$indicator->indicator_name]["unidad_formulario"] = $data_formularios["unidad"];
							$array_data[$indicator->indicator_name]["forms_values"][$key][] = $data_formularios["unidad_residuo"];
						}
					}
				}
			}
			
			$data_indicators_values = $this->Client_indicators_model->get_all_where(array("id_indicador" => $indicator->id,"deleted"=>0))->result();
			foreach($data_indicators_values as $data_indicator_value){
				if($indicator->id == $data_indicator_value->id_indicador){
					$value = $this->get_total_value_of_indicator($date_since,$date_until,$data_indicator_value);
					if(is_null($value)){
						continue;
					}else{
						$array_data[$indicator->indicator_name]["icon"] = $indicator->icon;
						$array_data[$indicator->indicator_name]["valor_indicador"][]=$value;
					}
				}
			}
		}
		*/

		foreach($array_data as $key => $value){
			$array_data[$key]["valor_indicador"] = array_sum($value["valor_indicador"]);
			foreach($value["forms_values"] as $k => $val){
				$array_data[$key]["forms_values"][$k] = array_sum($val);
			}
		}
		
		foreach($array_data as $key => $value){
			foreach($value["forms_values"] as $k => $v){
				$array_data[$key]["forms_values"][$k] = $v /$value["valor_indicador"];
			}
		}

		/* FIN INDICADORES */
		
		/*CATEGORIAS Y DATOS GRAFICO MASA*/
		$array_categorias_masa = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if($data["tipo_unidad"] == "Masa"){
							$array_categorias_masa[$categorie->id]=$categorie->nombre;
						}
					}
				}
			}
		}

		$array_tratamiento_valores = array();
		$array_data_masa = array();
		foreach($categories as $categorie){
			foreach($forms_data as $value){
				$datos = json_decode($value->datos,"true");
				if($categorie->id == $datos["id_categoria"]){	
					$date = $datos["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if($datos["tipo_unidad"] == "Masa"){
							
							foreach($tipos_tratamientos as $tipo_tratamiento){
								if($datos["tipo_tratamiento"] == $tipo_tratamiento["id"]){
									$valor = $datos["unidad_residuo"];
									$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_masa_destino,$valor);
									$array_tratamiento_valores[$tipo_tratamiento["nombre"]][$datos["id_categoria"]][] = $valor_final;
									
								}else{
									$array_tratamiento_valores[$tipo_tratamiento["nombre"]][$datos["id_categoria"]][] = 0;
								}
							}
							/*
							if($datos["tipo_tratamiento"] == 1){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_masa_destino,$valor);
								$array_tratamiento_valores["disposicion"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores["disposicion"][$datos["id_categoria"]][] = 0;
							}
							if($datos["tipo_tratamiento"] == 2){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_masa_destino,$valor);
								$array_tratamiento_valores["reutilización"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores["reutilización"][$datos["id_categoria"]][] = 0;
							}
							if($datos["tipo_tratamiento"] == 3){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_masa_destino,$valor);
								$array_tratamiento_valores["reciclaje"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores["reciclaje"][$datos["id_categoria"]][] = 0;
							}
							*/
						}
					}	
				}
			}
		}

		foreach($array_tratamiento_valores as $key => $value){
			foreach($value as $key2 => $val){
				$array_data_masa[$key][$key2][]= array_sum($val);
			}
		}
		/*FIN CATEGORIAS Y DATOS GRAFICO MASA*/
		
		/*CATEGORIAS Y DATOS GRAFICO VOLUMEN*/
		$array_categorias_volumen = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if($data["tipo_unidad"] == "Volumen"){
							$array_categorias_volumen[$categorie->id]=$categorie->nombre;
						}	
					}
				}
			}
		}
		
		$array_data_volumen = array();
		$array_tratamiento_valores_volumen = array();
		foreach($categories as $categorie){
			foreach($forms_data as $value){
				$datos = json_decode($value->datos,"true");
				if($categorie->id == $datos["id_categoria"]){
					$date = $datos["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if($datos["tipo_unidad"] == "Volumen"){

							foreach($tipos_tratamientos as $tipo_tratamiento){
								if($datos["tipo_tratamiento"] == $tipo_tratamiento["id"]){
									$valor = $datos["unidad_residuo"];
									$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_volumen_destino,$valor);
									$array_tratamiento_valores_volumen[$tipo_tratamiento["nombre"]][$datos["id_categoria"]][] = $valor_final;
								}else{
									$array_tratamiento_valores_volumen[$tipo_tratamiento["nombre"]][$datos["id_categoria"]][] = 0;
								}
							}
							/*
							if($datos["tipo_tratamiento"] == 1){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_volumen_destino,$valor);
								$array_tratamiento_valores_volumen["disposicion"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores_volumen["disposicion"][$datos["id_categoria"]][] = 0;
							}
							if($datos["tipo_tratamiento"] == 2){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_volumen_destino,$valor);
								$array_tratamiento_valores_volumen["reutilización"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores_volumen["reutilización"][$datos["id_categoria"]][] = 0;
							}
							if($datos["tipo_tratamiento"] == 3){
								$valor = $datos["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($datos["tipo_unidad"],$datos["unidad"],$unidad_volumen_destino,$valor);
								$array_tratamiento_valores_volumen["reciclaje"][$datos["id_categoria"]][] = $valor_final;
							}else{
								$array_tratamiento_valores_volumen["reciclaje"][$datos["id_categoria"]][] = 0;
							}
							*/
						}
					}
				}
			}
		}

		foreach($array_tratamiento_valores_volumen as $key => $value){
			foreach($value as $key2 => $val){
				$array_data_volumen[$key][$key2][]= array_sum($val);
			}
		}
		/* FIN CATEGORIAS Y DATOS GRAFICO VOLUMEN*/
		
		/*DATA GRAFICO UMBRALES MASA*/
		
		$array_categorias_umbrales = array();
		$umbrales = $this->Thresholds_model->get_all_where(array("id_client" =>$id_cliente, "id_project" =>$id_project,"deleted" => 0))->result();
		foreach($categories as $cat){
			
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos, true);
				if($cat->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since, $date_until, $date);
					if($verificacion_fecha == true){
						foreach($umbrales as $umb){
							if($umb->id_unit_type == 1){
								$array_categorias_umbrales[$cat->id]["nombre_categoria"] = $cat->nombre;
							}
						}
					}
				}
			}
			
		}
		
		$array_data_umbrales = array();
		foreach($array_categorias_umbrales as $key => $cat){
			foreach($umbrales as $umb){
				if($key == $umb->id_category){
					$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $umb->id_unit_type));
					$unidad = $this->Unity_model->get_one_where(array("id" => $umb->id_unit));
					$valor = $umb->threshold_value;
					$valor_umbral_conv = $this->trasformacion_unidad($tipo_unidad->nombre, $unidad->nombre, $unidad_masa_destino, $valor);
					$array_categorias_umbrales[$key]["umbrales"] = $umb;
					$array_categorias_umbrales[$key]["umbrales"]->threshold_value = $valor_umbral_conv;
					//$array_categorias_umbrales[$key]["umbrales"] = $umb;

				}
			}
		}

		foreach($array_categorias_umbrales as $key => $value){
			if(!$value["umbrales"]){
				$array_categorias_umbrales[$key]["umbrales"]->threshold_value = 0;
			} 
		}
		
		$array_formularios = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					
					if($verificacion_fecha == true){
						if(!isset($data["fecha_retiro"])){
							if($data["tipo_unidad"] == "Masa"){
								$valor = $data["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_masa_destino,$valor);
								$array_formularios[$categorie->id][]= $valor_final;
							}
							
						}else{
							
							$fecha_retiro = $data["fecha_retiro"];
							$start_ts = strtotime($date_since);
							$end_ts = strtotime($date_until);
							$date_ts = strtotime($fecha_retiro);

							if(($date_ts > $start_ts) && ($date_ts > $end_ts)){
								if($data["tipo_unidad"] == "Masa"){
									$valor = $data["unidad_residuo"];
									$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_masa_destino,$valor);
									$array_formularios[$categorie->id][]= $valor_final;
								}
							}
							
						}
						
					}
				}
			}
		}
		//print_r($array_formularios);exit;
		/*
		$array_formularios = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if(!isset($data["fecha_retiro"])){
							if($data["tipo_unidad"] == "Masa"){
								$valor = $data["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_masa_destino,$valor);
								$array_formularios[$categorie->id][]= $valor_final;
							}
						}
					}
				}
			}
		}
		*/
		
		$array_valor_total_formularios = array();
		foreach($array_formularios as $key => $value){
				$array_valor_total_formularios[$key] = array_sum($value);
		}
		
		/*foreach($array_categorias_umbrales as $key => $value){
			foreach($array_valor_total_formularios as $k => $v){
				if($key == $k){
					$array_categorias_umbrales[$key]["valores_formularios"] = $v;	
				}
			}	
		}*/
		
		$array_almacenados_umbrales = array();
		foreach($array_valor_total_formularios as $id_categoria => $total){
			$array_almacenados_umbrales[$id_categoria]["nombre_categoria"] = $unidad = $this->Categories_model->get_one($id_categoria)->nombre;
			$array_almacenados_umbrales[$id_categoria]["valores_formularios"] = $total;

			if($array_categorias_umbrales[$id_categoria]["umbrales"]->threshold_value){
				$array_almacenados_umbrales[$id_categoria]["umbrales"] = $array_categorias_umbrales[$id_categoria]["umbrales"]->threshold_value;
			}else{
				$array_almacenados_umbrales[$id_categoria]["umbrales"] = 0;
			}
		}
		
		
		
		/* FIN DATA GRAFICO UMBRALES MASA*/
		
		/*DATA GRAFICO UMBRALES VOLUMEN*/
		$array_categorias_umbrales_volumen = array();
		$umbrales = $this->Thresholds_model->get_all_where(array("id_client" =>$id_cliente, "id_project" =>$id_project,"deleted" => 0))->result();
		foreach($categories as $cat){
			
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos, true);
				if($cat->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since, $date_until, $date);
					if($verificacion_fecha == true){
						foreach($umbrales as $umb){
							if($umb->id_unit_type == 2){
								$array_categorias_umbrales_volumen[$cat->id]["nombre_categoria"] = $cat->nombre;
							}
						}
					}
				}
			}
			
		}

		$array_data_umbrales = array();
		foreach($array_categorias_umbrales_volumen as $key => $cat){
			foreach($umbrales as $umb){
				if($key == $umb->id_category){
					$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $umb->id_unit_type));
					$unidad = $this->Unity_model->get_one_where(array("id" => $umb->id_unit));
					$valor = $umb->threshold_value;
					$valor_umbral_conv = $this->trasformacion_unidad($tipo_unidad->nombre,$unidad->nombre,$unidad_volumen_destino,$valor);
					$array_categorias_umbrales_volumen[$key]["umbrales"] = $umb;	
					$array_categorias_umbrales_volumen[$key]["umbrales"]->threshold_value = $valor_umbral_conv;
					//$array_categorias_umbrales_volumen[$key]["umbrales"] = $umb;
				}
			}
		}
		
		foreach($array_categorias_umbrales_volumen as $key => $value){
			if(!$value["umbrales"]){
				$array_categorias_umbrales_volumen[$key]["umbrales"]->threshold_value = 0;
			} 
		}
		
		$array_formularios_volumen = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					
					if($verificacion_fecha == true){
						if(!isset($data["fecha_retiro"])){
							if($data["tipo_unidad"] == "Volumen"){
								$valor = $data["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_volumen_destino,$valor);
								$array_formularios_volumen[$categorie->id][]= $valor_final;
							}
						}
					}else{
						
						$fecha_retiro = $data["fecha_retiro"];
						$start_ts = strtotime($date_since);
						$end_ts = strtotime($date_until);
						$date_ts = strtotime($fecha_retiro);

						if(($date_ts > $start_ts) && ($date_ts > $end_ts)){
							if($data["tipo_unidad"] == "Volumen"){
								$valor = $data["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_volumen_destino,$valor);
								$array_formularios_volumen[$categorie->id][]= $valor_final;
							}
						}
					}
				}
			}
		}
		
		/*
		$array_formularios_volumen = array();
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos,"true");
				if($categorie->id == $data["id_categoria"]){
					$date = $data["fecha"];
					$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
					if($verificacion_fecha == true){
						if(!isset($data["fecha_retiro"])){
							if($data["tipo_unidad"] == "Volumen"){
								$valor = $data["unidad_residuo"];
								$valor_final = $this->trasformacion_unidad($data["tipo_unidad"],$data["unidad"],$unidad_volumen_destino,$valor);
								$array_formularios_volumen[$categorie->id][]= $valor_final;
							}
						}
					}
				}
			}
		}
		*/
		$array_valor_total_formularios_volumen = array();
		foreach($array_formularios_volumen as $key => $value){
				$array_valor_total_formularios_volumen[$key] = array_sum($value);
		}
		
		/*foreach($array_categorias_umbrales_volumen as $key => $value){
			foreach($array_valor_total_formularios_volumen as $k => $v){
				if($key == $k){
					$array_categorias_umbrales_volumen[$key]["valores_formularios"] = $v;	
				}
			}	
		}*/
		
		$array_almacenados_umbrales_volumen = array();
		foreach($array_valor_total_formularios_volumen as $id_categoria => $total){
			$array_almacenados_umbrales_volumen[$id_categoria]["nombre_categoria"] = $unidad = $this->Categories_model->get_one($id_categoria)->nombre;
			$array_almacenados_umbrales_volumen[$id_categoria]["valores_formularios"] = $total;

			if($array_categorias_umbrales_volumen[$id_categoria]["umbrales"]->threshold_value){
				$array_almacenados_umbrales_volumen[$id_categoria]["umbrales"] = $array_categorias_umbrales_volumen[$id_categoria]["umbrales"]->threshold_value;
			}else{
				$array_almacenados_umbrales_volumen[$id_categoria]["umbrales"] = 0;
			}
		}
		
		/* FIN DATA GRAFICO UMBRALES VOLUMEN*/
		
		$indicators_and_values = $array_data;
		$indicators_view = $this->indicator_view($indicators_and_values, $id_project);
		$grafico_masa = $this->graficos_masa($array_categorias_masa, $array_data_masa, $nombre_unidad_masa_config, $decimal_numbers, $decimals_separator, $thousands_separator);
		$grafico_volumen = $this->grafico_volumen($array_categorias_volumen, $array_data_volumen, $nombre_unidad_volumen_config, $decimal_numbers, $decimals_separator, $thousands_separator);
		$grafico_umbrales_masa = $this->grafico_umbrales_masa($array_almacenados_umbrales, $nombre_unidad_masa_config, $decimal_numbers, $decimals_separator, $thousands_separator);
		$grafico_umbrales_volumen = $this->grafico_umbrales_volumen($array_almacenados_umbrales_volumen, $nombre_unidad_volumen_config, $decimal_numbers, $decimals_separator ,$thousands_separator);
		
		echo json_encode(array("indicators_view" => $indicators_view,"grafico_masa"=>$grafico_masa,"grafico_volumen"=>$grafico_volumen,"grafico_umbrales_masa" =>$grafico_umbrales_masa,"grafico_umbrales_volumen" =>$grafico_umbrales_volumen));
		
	}
	
	function grafico_umbrales_masa($array_data, $unidad, $decimal_numbers, $decimals_separator, $thousands_separator){
		
		$project_info = $this->Projects_model->get_one($this->session->project_context);
		$client_info = $this->Clients_model->get_one($this->login_user->client_id);
		
		$html ='';
		$html .=' <script type="text/javascript">';
		$html .= '$("#contenedor_grafico_masa").highcharts({';
		$html .= '	chart: {type: "column"},';
		$html .= '	title: {text: "'.lang("waste_stored_mass").'"},';
		$html .= '	credits: {enabled: false},';
		//$html .= '	exporting: {enabled: false },';
		
		$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("threshold").'_'.$unidad.'_'.date("Y-m-d");
		
		$html .= 	'exporting: {';
		$html .= 		'chartOptions:{';
		//$html .= 			'title: {';
		//$html .= 				'text:""';
		//$html .= 			'}';
		$html .= 			'plotOptions: {';
		$html .= 				'series: {';
		$html .= 					'dataLabels: {';
		$html .= 						'enabled: true,';
		$html .= 					'}';
		$html .= 				'}';
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
		
		$html .= '	xAxis: {';
		$html .= '		categories: [';						
							foreach($array_data as $key => $value){		
								$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
								if($alias->alias){
									$nombre_categoria = $alias->alias;
								}else{
									$nombre_categoria = $value["nombre_categoria"];
								}
								$html .='"'.$nombre_categoria.'",';
								//$html .='"'.$value["nombre_categoria"].'",';
							}
		$html .= '		]';
		$html .= '	},';
		$html .= '	yAxis: [
						{ 
							min: 0, 
							title: { text: ""}
						},
						{
							title:{';
									$html .= 'text: "'.$unidad.'"';
		$html .= '				  },
							labels:{
								format: "{value:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");}
							},
							stackLabels: {
								enabled: true,
								format: "{total:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return numberFormat(this.total, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");},
								//format: "{total:." + decimal_numbers + "f}",
							},
						}
					],';
		$html .='	legend: {
						align: "center",
						verticalAlign: "bottom",
						backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
						shadow: false
					},';
		$html .='	tooltip: {
						headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
						pointFormatter: function(){
							return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"
						},
						footerFormat:"</table>",
						useHTML: true
					},';
		$html .= '	plotOptions: {
						column: {
							grouping: false,
							shadow: false,
							borderWidth: 0,
							pointPadding: 0.2,
							dataLabels: {
								enabled: true,
								color: "#000000",
								align: "center",
								format: "{y:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},
								style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}
							}
						}
					},';
		$html .= '	series: [';
		$html .= '{
				name: "'.lang("threshold").'",
				//color: "rgba(248,161,63,1)",
				color: "#d9534f",
				data: [';
						foreach($array_data as $key => $value){
							//$html .=''.$value["umbrales"]->threshold_value.',';
							$html .=''.$value["umbrales"].',';
						}
		$html .= '],
				pointPadding: 0.3,
				pointPlacement: "center",
				yAxis: 1
			}, {
				name: "'.lang("stored").'",
				//color: "rgba(186,60,61,.9)",
				color: "#90ed7d",
				data: [';
					foreach($array_data as $key => $value){
						if(isset($value["valores_formularios"])){
							$html .=''.$value["valores_formularios"].',';
						}else{
							$html .='0,';
						}
					}
		$html .='],
				pointPadding: 0.4,
				pointPlacement: 0,
				yAxis: 1
			}';
		$html .= ']';
		$html .= '});';
		$html .=' </script>';
		return $html;
	}
	
	function grafico_umbrales_volumen($array_data, $unidad, $decimal_numbers, $decimals_separator, $thousands_separator){
		
		$project_info = $this->Projects_model->get_one($this->session->project_context);
		$client_info = $this->Clients_model->get_one($this->login_user->client_id);
		
		$html ='';
		$html .=' <script type="text/javascript">';
		$html .= '$("#contenedor_grafico_volumen").highcharts({';
		$html .= '	chart: {type: "column"},';
		$html .= '	title: {text: "'.lang("waste_stored_volume").'"},';
		$html .= '	credits: {enabled: false},';
		//$html .= '	exporting: {enabled: false },';
		
		$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("threshold").'_'.$unidad.'_'.date("Y-m-d");
		
		$html .= 	'exporting: {';
		$html .= 		'chartOptions:{';
		//$html .= 			'title: {';
		//$html .= 				'text:""';
		//$html .= 			'}';
		$html .= 			'plotOptions: {';
		$html .= 				'series: {';
		$html .= 					'dataLabels: {';
		$html .= 						'enabled: true,';
		$html .= 					'}';
		$html .= 				'}';
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
		
		$html .= '	xAxis: {';
		$html .= '		categories: [';
							foreach($array_data as $key => $value){

								$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
								if($alias->alias){
									$nombre_categoria = $alias->alias;
								}else{
									$nombre_categoria = $value["nombre_categoria"];
								}
								$html .='"'.$nombre_categoria.'",';
								//$html .='"'.$value["nombre_categoria"].'",';
							}
		$html .= '		]';
		$html .= '	},';
		$html .= '	yAxis: [
						{ 
							min: 0, 
							title: { text: ""}
						},
						{
							title:{';
									$html .= 'text: "'.$unidad.'"';
		$html .= '				  },
							labels:{
								format: "{value:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");}
							},
							stackLabels: {
								enabled: true,
								format: "{total:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return numberFormat(this.total, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");},
								//format: "{total:." + decimal_numbers + "f}",
							},
						}
					],';
		$html .='	legend: {
						align: "center",
						verticalAlign: "bottom",
						backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
						shadow: false
					},';
		$html .='	tooltip: {
						headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
						pointFormatter: function(){
							return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"
						},
						footerFormat:"</table>",
						useHTML: true
					},';
		$html .= '	plotOptions: {
						column: {
							grouping: false,
							shadow: false,
							borderWidth: 0,
							pointPadding: 0.2,
							dataLabels: {
								enabled: true,
								color: "#000000",
								align: "center",
								format: "{y:,." + "'.$decimal_numbers.'" + "f}",
								//formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},
								style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}
							}
						}
					},';
		$html .= '	series: [';
		$html .= '{
				name: "'.lang("threshold").'",
				//color: "rgba(248,161,63,1)",
				color: "#d9534f",
				data: [';
				
					foreach($array_data as $key => $value){
						//$html .=''.$value["umbrales"]->threshold_value.',';
							$html .=''.$value["umbrales"].',';
					}
				
		$html .= '],
				pointPadding: 0.3,
				pointPlacement: "center",
				yAxis: 1
			}, {
				name: "'.lang("stored").'",
				//color: "rgba(186,60,61,.9)",
				color: "#90ed7d",
				data: [';
		
					foreach($array_data as $key => $value){
						if(isset($value["valores_formularios"])){
							$html .=''.$value["valores_formularios"].',';
						}else{
							$html .='0,';
						}
						
					}
					
		$html .='],
				pointPadding: 0.4,
				pointPlacement: 0,
				yAxis: 1
			}';
			
		$html .= ']';
		$html .= '});';
		$html .=' </script>';
		return $html;
	}

	function grafico_volumen($array_categorias,$array_datos,$nombre_unidad_volumen_config,$decimal_numbers,$decimals_separator,$thousands_separator){
		
		$project_info = $this->Projects_model->get_one($this->session->project_context);
		$client_info = $this->Clients_model->get_one($this->login_user->client_id);
		
		$html ='';
		$html .=' <script type="text/javascript">';
		$html .=' $("#vertical_stack_bar_2").highcharts({';
		$html .=' chart: { 
					zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},
					type: "column",
					events: {load: function(event){}}
				  },';
		$html .=' title: { text:"'.lang('waste_in_volume').'"},';
		$html .=' credits: { enabled: false },';
		//$html .=' exporting: { enabled: false },';
		
		$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("treatment").'_'.$nombre_unidad_volumen_config.'_'.date("Y-m-d");
		
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
		
		$html .=' xAxis: {';
		$html .=' 		categories: [';
						foreach($array_categorias as $key => $value){

						$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
						if($alias->alias){
							$nombre_categoria = $alias->alias;
						}else{
							$categoria = $this->Categories_model->get_one($key);
							$nombre_categoria = $categoria->nombre;
						}
						$html .='"'.$nombre_categoria.'",';
						}
		$html .='], ';
		$html .='}, ';
		$html .=' yAxis: {
				min: 0,
				title: {';
					$html .= 'text: "'.$nombre_unidad_volumen_config.'"';
		$html .='},
					labels:{
						format: "{value:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");}
					},
					stackLabels: {
						enabled: true,
						format: "{total:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return numberFormat(this.total, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");},
						//format: "{total:." + decimal_numbers + "f}",
					},
			},';
		$html .=' legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			},';
		$html .=' tooltip: {
				headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
				pointFormatter: function(){
						return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"
					},
				footerFormat:"</table>",
				useHTML: true
			},';
		$html .=' plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					borderWidth: 0,
					pointPadding: 0.2,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},
						style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}
					}
				}
			},';
		$html .=' series: [';
		$html .=' {';
		$html .=' name: "'.lang("provision").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Disposición"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}
							}
						}
					}
		$html .=' ]';
		$html .=' },';
		$html .=' {';
		$html .=' name: "'.lang("reuse").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Reutilización"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}
							}
						}
					}
		$html .=' ],';
		$html .=' color: "#b3b3b3"';
		$html .=' },';
		$html .=' {';
		$html .=' name: "'.lang("recycling").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Reciclaje"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}	
							}
						}
					}
		$html .=' ],';
		$html .='color: "rgb(144, 237, 125)"';
		$html .=' }';
		$html .=']';
		$html .='});';
		$html .=' </script>';
		return $html;
	}
	
	function graficos_masa($array_categorias,$array_datos,$nombre_unidad_masa_config,$decimal_numbers,$decimals_separator,$thousands_separator){
		
		$project_info = $this->Projects_model->get_one($this->session->project_context);
		$client_info = $this->Clients_model->get_one($this->login_user->client_id);
		
		$html ='';
		$html .=' <script type="text/javascript">';
		$html .=' $("#vertical_stack_bar_1").highcharts({';
		$html .=' chart: { 
					zoomType: "x",reflow: true,vresetZoomButton: {position: {align: "left",x: 0}},
					type: "column",
					events: {load: function(event){}}
				  },';
		$html .=' title: { text:"'.lang('waste_in_bulk').'"},';
		$html .=' credits: { enabled: false },';
		//$html .=' exporting: { enabled: false },';
		
		$nombre_exportacion = $client_info->sigla.'_'.$project_info->sigla.'_'.lang("treatment").'_'.$nombre_unidad_masa_config.'_'.date("Y-m-d");
		
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
		
		$html .=' xAxis: {';
		$html .=' 		categories: [';
						foreach($array_categorias as $key => $value){
							$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
							if($alias->alias){
								$nombre_categoria = $alias->alias;
							}else{
								$categoria = $this->Categories_model->get_one($key);
								$nombre_categoria = $categoria->nombre;
							}
							$html .='"'.$nombre_categoria.'",';
						}
		$html .='], ';
		$html .='}, ';
		$html .=' yAxis: {
				min: 0,
				title: {';
					$html .= 'text: "'.$nombre_unidad_masa_config.'"';
		$html .='},
					labels:{
						format: "{value:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return numberFormat(this.value, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");}
					},
					stackLabels: {
						enabled: true,
						format: "{total:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return numberFormat(this.total, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'");},
						//format: "{total:." + decimal_numbers + "f}",
					},
			},';
		$html .=' legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			},';
		$html .=' tooltip: {
				headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
				pointFormatter: function(){
						return "<tr><td style=\'color:"+this.series.color+";padding:0\'>"+this.series.name+":</td>"+"<td style=\'padding:0\'><b>"+(numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"))+" '.$unidad_masa.'</b></td></tr>"
					},
				footerFormat:"</table>",
				useHTML: true
			},';
		$$html .=' plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					borderWidth: 0,
					pointPadding: 0.2,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + "'.$decimal_numbers.'" + "f}",
						//formatter: function(){return (numberFormat(this.y, "'.$decimal_numbers.'", "'.$decimals_separator.'", "'.$thousands_separator.'"));},
						style: {fontSize: "10px",fontFamily: "Segoe ui, sans-serif"}
					}
				}
			},';
		$html .=' series: [';
		$html .=' {';
		$html .=' name: "'.lang("provision").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Disposición"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}
							}
						}
					}
		$html .=' ]';
		$html .=' },';
		$html .=' {';
		$html .=' name: "'.lang("reuse").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Reutilización"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}
							}
						}
					}
		$html .=' ],';
		$html .=' color: "#b3b3b3"';
		$html .=' },';
		$html .=' {';
		$html .=' name: "'.lang("recycling").'",';
		$html .=' data:[';
					foreach($array_categorias as $key => $value){
						foreach($array_datos["Reciclaje"] as $key2 => $datos){
							foreach($datos as $d){
								if($key == $key2){
									$html .=''.$d.',';
								}	
							}
						}
					}
		$html .=' ],';
		$html .='color: "rgb(144, 237, 125)"';
		$html .=' }';
		$html .=']';
		$html .='});';
		$html .=' </script>';
		return $html;
	}
	
	function indicator_view($data,$id_project){
		$html ='';
		foreach($data as $key => $indicator){
			if(isset($indicator["forms_values"])){
				if(isset($indicator["valor_indicador"])){
					$html .=' <div class="col-md-4">';
					$html .=' <div class="panel-body">';
					$html .=' <h1 style="font-size: 15px; padding-left:15px;">'.$key.'</h1>';
					$html .=' <div class="col-md-4" style="text-align: center">';
					
					$fontawesome_indicador = $this->Fontawesome_model->get_one($indicator["id_fontawesome"]);
					
					$html .=' <div class="widget-icon"><i class="fa '.$fontawesome_indicador->clase.'" style="color: '.$indicator["color"].'"></i></div>';
					$html .=' </div>';
					$html .=' <div class="col-md-6">';
					foreach($indicator["forms_values"] as $k => $value){
						$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $k, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
						if($alias->alias){
							$nombre_categoria = $alias->alias;
						}else{
							$categoria = $this->Categories_model->get_one($k);
							$nombre_categoria = $categoria->nombre;
						}
						$html .=' <p>'.$nombre_categoria.': '.to_number_project_format($value,$id_project).' '.$indicator["unidad_formulario"].'/'.$indicator["unidad_indicador"].'</p>';
					}
					$html .=' </div>';
					$html .=' </div>';
					$html .=' </div>';
				}
			}
		}
		return $html;
	}
		
	function get_total_value_of_indicator($date_since,$date_until,$data_indicator_value){

		$array = array();
		$range_date_sice = $this->check_in_range($date_since,$date_until,$data_indicator_value->f_desde);
		if(($range_date_sice == true)){
			$numbers_of_days = $this->numbers_of_days($data_indicator_value->f_desde,$data_indicator_value->f_hasta);
			
			if($data_indicator_value->f_hasta > $date_until){
				$rest_days = $this->numbers_of_days($date_until,$data_indicator_value->f_hasta);
				$total_days = $numbers_of_days - $rest_days;
			}else{
				$total_days = $numbers_of_days;
			}
			
			$total_value = $data_indicator_value->valor / $total_days;
		}
		return $total_value;
	}
	
	function numbers_of_days($date_since,$date_until){

		
		$date1=date_create($date_since);
		$date2=date_create($date_until);
		//$diff=date_diff($date1,$date2);
		//return $diff->days;
		$date_diff=date_diff($date1,$date2);
		$diff = (($date_diff->days)+1);
		
		return $diff;
	}
	
	function check_in_range($start_date,$end_date,$date){
		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date);
		$date_ts = strtotime($date);
		
		if(($date_ts >= $start_ts) && ($date_ts <= $end_ts)){
		 return true;
		}else{
		  return false;
		}
	}

	function trasformacion_unidad($tipo_unidad,$unidad,$unidad_destino,$valor){
		
		$tipo_unida_data = $this->Unity_type_model->get_one_where(array("nombre" => $tipo_unidad));
		$id_tipo_unidad = $tipo_unida_data->id;
		
		$unidad_data = $this->Unity_model->get_one_where(array("nombre" => $unidad));
		$id_unidad = $unidad_data->id;

		$fila_conversion = $this->Conversion_model->get_one_where(
			array(
				"id_tipo_unidad" => $id_tipo_unidad,
				"id_unidad_origen" => $id_unidad,
				"id_unidad_destino" => $unidad_destino
			)
		);
		
		$valor_transformacion = $fila_conversion->transformacion;
		$valor_final = $valor * $valor_transformacion;
		return $valor_final;

	}
	
	function get_excel_ultimos_retiros(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);

		$id_material = $this->input->post("id_material");
		$date_since = $this->input->post("date_since");
		$date_until = $this->input->post("date_until");
		
		$categories = $this->Categories_model->get_category_of_material($id_material)->result();
		$forms_data = $this->Form_values_model->get_forms_values_of_forms_by_flux($id_proyecto, "Residuo")->result();
		$list_data = array();
		
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos, TRUE);
				if($categorie->id == $data["id_categoria"]){
					if(isset($data["fecha_retiro"])){
						$reg_date = $data["fecha"];
						$date = $data["fecha_retiro"];
						$verificacion_fecha = $this->check_in_range($date_since, $date_until, $date);
						$verificacion_fecha_registro = $this->check_in_range($date_since, $date_until, $reg_date);
						if($verificacion_fecha_registro == TRUE){
							if($verificacion_fecha == TRUE){
								if(isset($data["fecha_retiro"])){
									$list_data[$form_data->id] = $data;
								}	
							}
						}
					}
				}
			}
		}
		
		$result = array();
        foreach ($list_data as $key => $data) {
			$result[] = $this->_make_row_excel_ultimos_retiros($data, $id_proyecto, $key);	
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
		$nombre_columnas[] = array("nombre_columna" => lang("material"), "id_tipo_campo" => "material");
		$nombre_columnas[] = array("nombre_columna" => lang("category"), "id_tipo_campo" => "category");
		$nombre_columnas[] = array("nombre_columna" => lang("quantity"), "id_tipo_campo" => "quantity");
		$nombre_columnas[] = array("nombre_columna" => lang("treatment"), "id_tipo_campo" => "treatment");
		$nombre_columnas[] = array("nombre_columna" => lang("retirement_date"), "id_tipo_campo" => "retirement_date");
		$nombre_columnas[] = array("nombre_columna" => lang("retirement_evidence"), "id_tipo_campo" => "retirement_evidence");
		$nombre_columnas[] = array("nombre_columna" => lang("reception_evidence"), "id_tipo_campo" => "reception_evidence");
		
		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("retirements"))
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
				$valor = $res[$index_columnas];
				
				if(!is_array($columna)){
					
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
					
				} else {
					
					if($columna["id_tipo_campo"] == "material" || $columna["id_tipo_campo"] == "category"
					|| $columna["id_tipo_campo"] == "treatment" || $columna["id_tipo_campo"] == "retirement_date"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

						
					} elseif($columna["id_tipo_campo"] == "quantity"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	

					} elseif($columna["id_tipo_campo"] == "retirement_evidence" || $columna["id_tipo_campo"] == "reception_evidence"){
					
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

		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');
		
		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}

		$nombre_hoja = strlen(lang("retirements")) > 31 ? substr(lang("retirements"), 0, 28).'...' : lang("retirements");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("retirements")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
		
	}
	
	private function _make_row_excel_ultimos_retiros($data, $id_proyecto, $key){
		
		$material = $this->Materials_model->get_material_of_category($data["id_categoria"])->result();
		$material_nombre = $material["0"]->nombre;
		
		$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $data["id_categoria"], 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
		if($alias->alias){
			$nombre_categoria = $alias->alias;
		}else{
			$categoria = $this->Categories_model->get_one($data["id_categoria"]);
			$nombre_categoria = $categoria->nombre;
		}
		
		$unidad_value = to_number_project_format($data["unidad_residuo"], $id_proyecto)." (".$data["unidad"].")";
		
		$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $data["tipo_tratamiento"], "deleted" => 0));
		$tipo_tratamiento = ($data["tipo_tratamiento"] == $tipo_tratamiento->id) ? $tipo_tratamiento->nombre : "-";
		
		$evidencia_retiro = (isset($data["nombre_archivo_retiro"])) ? remove_file_prefix($data["nombre_archivo_retiro"]) : "-";
		$evidencia_recepcion = (isset($data["nombre_archivo_recepcion"])) ? remove_file_prefix($data["nombre_archivo_recepcion"]) : "-";
		$fecha_retiro = get_date_format($data["fecha_retiro"], $id_proyecto);
		
		$row_data = array(
			$material_nombre,
			$nombre_categoria,
			$unidad_value,
			$tipo_tratamiento,
			$fecha_retiro,
			$evidencia_retiro,
			$evidencia_recepcion	
		);
		
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
		
		$imagenes_graficos = $this->input->post("imagenes_graficos");
		
		$view_data["grafico_residuos_masa"] = $imagenes_graficos["image_residuos_masa"];
		$view_data["grafico_residuos_volumen"] = $imagenes_graficos["image_residuos_volumen"];
		$view_data["grafico_residuos_almacenados_masa"] = $imagenes_graficos["image_residuos_almacenados_masa"];
		$view_data["grafico_residuos_almacenados_volumen"] = $imagenes_graficos["image_residuos_almacenados_volumen"];
		
		$puede_ver = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_ver"] = $puede_ver;
		
		$id_material = $this->input->post('id_material');
		$date_since = $this->input->post('date_since');
		$date_until = $this->input->post('date_until');
		
		$indicator_data = $this->Indicators_model->get_all_where(array(
			"id_client" => $id_cliente, "id_project" => $id_proyecto, "deleted" => 0
		))->result();
		
		$categories = $this->Categories_model->get_category_of_material($id_material)->result();
		$forms_data = $this->Form_values_model->get_forms_values_of_forms_by_flux($id_proyecto, "Residuo")->result();
		
		// Indicadores
		$array_data = array();
		foreach($indicator_data as $indicator){
			$data_indicadores = json_decode($indicator->categories, TRUE);
			foreach($data_indicadores as $key => $val){
				foreach($categories as $categorie){
					if($categorie->id == $key){
						foreach($forms_data as $form_data){
							$data_formularios = json_decode($form_data->datos, TRUE);
							if($key == $data_formularios["id_categoria"]){
								$date = $data_formularios["fecha"];
								$verificacion_fecha = $this->check_in_range($date_since,$date_until,$date);
								if($verificacion_fecha == TRUE){
									$array_data[$indicator->indicator_name]["unidad_indicador"] = $indicator->unit;
									$array_data[$indicator->indicator_name]["unidad_formulario"] = $data_formularios["unidad"];
									$array_data[$indicator->indicator_name]["forms_values"][$key][] = $data_formularios["unidad_residuo"];
								}
							}
						}
					}
				}
			}

			$data_indicators_values = $this->Client_indicators_model->get_all_where(array(
				"id_indicador" => $indicator->id, "deleted" => 0
			))->result();
			
			foreach($data_indicators_values as $data_indicator_value){
				if($indicator->id == $data_indicator_value->id_indicador){
					$value = $this->get_total_value_of_indicator($date_since, $date_until, $data_indicator_value);
					if(is_null($value)){
						continue;
					}else{
						$array_data[$indicator->indicator_name]["color"] = $indicator->color;
						$array_data[$indicator->indicator_name]["icon"] = $indicator->icon;
						$array_data[$indicator->indicator_name]["id_fontawesome"] = $indicator->id_fontawesome;
						$array_data[$indicator->indicator_name]["valor_indicador"][] = $value;
					}
				}
			}
		}
		
		foreach($array_data as $key => $value){
			$array_data[$key]["valor_indicador"] = array_sum($value["valor_indicador"]);
			foreach($value["forms_values"] as $k => $val){
				$array_data[$key]["forms_values"][$k] = array_sum($val);
			}
		}
		
		foreach($array_data as $key => $value){
			foreach($value["forms_values"] as $k => $v){
				$array_data[$key]["forms_values"][$k] = $v /$value["valor_indicador"];
			}
		}

		$view_data["indicators_and_values"] = $array_data;
		
		// Últimos Retiros
		foreach($categories as $categorie){
			foreach($forms_data as $form_data){
				$data = json_decode($form_data->datos, TRUE);
				if($categorie->id == $data["id_categoria"]){
					if(isset($data["fecha_retiro"])){
						$reg_date = $data["fecha"];
						$date = $data["fecha_retiro"];
						$verificacion_fecha = $this->check_in_range($date_since, $date_until ,$date);
						$verificacion_fecha_registro = $this->check_in_range($date_since, $date_until, $reg_date);
						if($verificacion_fecha_registro == true){
							if($verificacion_fecha == true){
								if(isset($data["fecha_retiro"])){
									$list_data[$form_data->id] = $data;
								}	
							}
						}
					}
				}
			}
		}

        $result = array();
        foreach ($list_data as $key => $data) {
			$result[] = $this->_make_row_pdf($data, $id_proyecto, $key);	
        }
		
		$view_data["ultimos_retiros"] = $result;

		// create new PDF document
        $this->load->library('Pdf');
		
		// set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Autor');
        $this->pdf->SetTitle($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("waste")."_".lang("detail")."_".date('Y-m-d'));
        $this->pdf->SetSubject($info_cliente->sigla."_".$info_proyecto->sigla."_".lang("waste")."_".lang("detail")."_".date('Y-m-d'));
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

		// Add a page
		// This method has several options, check the source code documentation for more information
        $this->pdf->AddPage();
		
		$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
		$this->pdf->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		
		$html = '<br><br>';
		$html .= '<h1 align="center" style="font-family: Times New Roman, Times, serif;">'.$info_proyecto->title.'</h1>'; 
		$html .= '<div align="center">';
		$html .= '<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">';
		$html .= lang("waste") . " - " . lang("detail");
		$html .= '</h2>';
		$html .= '<div align="center">';
		$hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));
		$html .= lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora;
		$html .= '</div>';
		$html .= '</div>';
		//$html .= '<br><br>';
	
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$html = "";
		if($puede_ver == 1) { 
		
			$html .= '<div style="width: 100%;">';
			$html .= '<table>';
			
			$loop = 1;
			foreach($array_data as $key => $indicator){ 
				
				if(isset($indicator["forms_values"])){
					if(isset($indicator["valor_indicador"])){
					
						if($loop % 2 == 1){
							$html .= '<tr>';
						}
						
						$html .= '<td>';
		
						$html .= '<table style="float: left;">';
						$html .= '<tr>';
						$html .= '<td style="width:100px;">';
						
						$html .= '<h1 style="font-size: 15px; text-align: center;">'.$key.'</h1>';
						
						$this->pdf->SetFont($fontawesome, '', 40, '', false);
						$rgb_color = $this->hex_to_rgb($indicator["color"]);
						$this->pdf->SetTextColor($rgb_color[0], $rgb_color[1], $rgb_color[2]);
						
						$fontawesome_indicador = $this->Fontawesome_model->get_one($indicator["id_fontawesome"]);
		
						$html .= '<span style="font-family:'.$fontawesome.';text-align: center; font-size: 60px; color: '.$indicator["color"].'">';
						$html .= '&#x'.$fontawesome_indicador->unicode.';';
						$html .= '</span>';
						$html .= '<br><br><br>';
						$html .= '</td>';
						
						//$html .= "<br><br>";
						$html .= '<td style="width: 200px;">';
						
						$this->pdf->SetFont('helvetica', '',9);
						$this->pdf->SetTextColor(0, 0, 0);
		
						foreach($indicator["forms_values"] as $k => $value){
							$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $k, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
							if($alias->alias){
								$nombre_categoria = $alias->alias;
							}else{
								$categoria = $this->Categories_model->get_one($k);
								$nombre_categoria = $categoria->nombre;
							}
							$html .= '<p>'.$nombre_categoria.': '.to_number_project_format($value,$id_proyecto).' '.$indicator["unidad_formulario"].'/'.$indicator["unidad_indicador"].'</p>';
						}
						$html .= '<br><br><br>';
						$html .= '</td>';
						$html .= '</tr>';
						$html .= '</table>';
						
						$html .= '</td>';
						
						if($loop % 2 == 0 || $loop == count($array_data)){
							$html .= '</tr>';
						}
						
						$loop++;
					}
				}
	
			}
			
			$html .= '</table>';
			$html .= '</div>';
		
		}
		
		$this->pdf->writeHTML($html, true, false, true, false, '');
		
		$html = $this->load->view('waste/client/detail/pdf_view', $view_data, true);

		$this->pdf->SetFont('helvetica', '',9);
		$this->pdf->Ln(4);
		$this->pdf->writeHTML($html, true, false, true, false, '');

		$pdf_file_name = $info_cliente->sigla."_".$info_proyecto->sigla."_".lang("waste")."_".lang("detail")."_".date('Y-m-d').".pdf";
		
		$tmp = get_setting("temp_file_path");
		$this->pdf->Output(getcwd() . '/' . $tmp.$pdf_file_name, "F");

		echo $pdf_file_name;
		
	}
	
	function _make_row_pdf($data, $id_proyecto, $key){
		
		$material = $this->Materials_model->get_material_of_category($data["id_categoria"])->result();
		$material_nombre = $material["0"]->nombre;
		
		$alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $data["id_categoria"], 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
		if($alias->alias){
			$nombre_categoria = $alias->alias;
		}else{
			$categoria = $this->Categories_model->get_one($data["id_categoria"]);
			$nombre_categoria = $categoria->nombre;
		}
		
		$unidad_value = to_number_project_format($data["unidad_residuo"], $id_proyecto)." (".$data["unidad"].")";
		
		$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $data["tipo_tratamiento"], "deleted" => 0));
		if($data["tipo_tratamiento"] == $tipo_tratamiento->id){
			$tipo_tratamiento = $tipo_tratamiento->nombre;
		}
		
		$evidencia_retiro = (isset($data["nombre_archivo_retiro"])) ? remove_file_prefix($data["nombre_archivo_retiro"]) : "-";
		$evidencia_recepcion = (isset($data["nombre_archivo_recepcion"])) ? remove_file_prefix($data["nombre_archivo_recepcion"]) : "-";

		$fecha_retiro = get_date_format($data["fecha_retiro"], $id_proyecto);
		
		$row_data = array(
			"material" => $material_nombre,
			"categoria" => $nombre_categoria,
			"cantidad" => $unidad_value,
			"tipo_tratamiento" => $tipo_tratamiento,
			"fecha_retiro" => $fecha_retiro,
			//$evidencia_retiro,
			//$evidencia_recepcion
		);

		return $row_data;
		
	}
	
	function borrar_temporal(){
		$uri = $this->input->post('uri');
		delete_file_from_directory($uri);
	}
	
	/*
		Función que recibe un código hexadecimal, lo transforma en rgb y lo retorna.
	*/
	private function hex_to_rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
	
}
