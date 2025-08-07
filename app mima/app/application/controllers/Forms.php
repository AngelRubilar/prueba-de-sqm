<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Forms extends MY_Controller {
	
	private $id_admin_module;
	private $id_admin_submodule;

    function __construct() {
        parent::__construct();
		
		$this->id_admin_module = 5; // Registros
		$this->id_admin_submodule = 12; // Formularios

    	//check permission to access this module
        $this->init_permission_checker("client");
		$this->load->helper('directory');
    }

    /* load forms list view */

    function index() {
        $this->access_only_allowed_members();

        $access_info = $this->get_access_info("invoice");
		
		//FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		//FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		//FILTRO CATEGORIA (TIPO DE FORMULARIO)
		$array_tipos_formularios[] = array("id" => "", "text" => "- ".lang("category")." -");
		$tipos_formulario = $this->Form_types_model->get_dropdown_list(array("nombre"), 'id');
		foreach($tipos_formulario as $id => $nombre){
			$array_tipos_formularios[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['tipos_formularios_dropdown'] = json_encode($array_tipos_formularios);
		
        $this->template->rander("forms/index", $view_data);
    }

    /* load form add /edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $form_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		//view='details' needed only when loding from the client's details view
        $view_data["view"] = $this->input->post('view');
		$view_data['model_info'] = $this->Forms_model->get_one($form_id);
		$view_data["tipo_formulario"] = array("" => "-") + $this->Form_types_model->get_dropdown_list(array("nombre"), "id");
		$view_data["proyectos"] = array("" => "-") + $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $view_data['model_info']->id_cliente));
		$view_data["form_rel_project"] = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form_id, "deleted" => 0));
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");		
		$view_data["field_rel_form"] = $this->Field_rel_form_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result_array();
		$iconos = directory_map('./assets/images/icons/');
		sort($iconos);
		$view_data["iconos"] = $iconos;
		
		//$view_data["materiales_disponibles"] = $this->Materials_model->get_all()->result_array();
		if($form_id){
			if(!$view_data['model_info']->fijo){
				$view_data["materiales_disponibles"] = $this->Materials_model->get_materials_of_project($view_data["form_rel_project"]->id_proyecto)->result();
			} else {
				$view_data["materiales_disponibles"] = array();
			}
		}else{
			$view_data["materiales_disponibles"] = array();
		}
		
		if($form_id){
			
			if(!$view_data['model_info']->fijo){
			
				$array_tipos_unidades_disponibles = array();
				$array_tipos_unidades_disponibles["-"] = "-";
				$unit_type = $this->Unity_type_model->get_all()->result();
				foreach($unit_type as $ut){
					$array_tipos_unidades_disponibles[$ut->id] = $ut->nombre;
				}
				$view_data['tipos_unidades_disponibles'] = $array_tipos_unidades_disponibles;
				$selected_tipe_unit = array();
				$selected_unit = array();
				$data_unidad = json_decode($view_data['model_info']->unidad);
				
				$tipo_unidad_id = (int)$data_unidad->tipo_unidad_id;
				$unidad_id = (int)$data_unidad->unidad_id;
				
				$array_unit_symbol = array();
				$array_unit_symbol[""] = "-";
				$data_unit_symbol = $this->Unity_model->get_units_of_unit_type($tipo_unidad_id)->result();
				foreach($data_unit_symbol as $data){
					$array_unit_symbol[$data->id] = $data->nombre;
				}
				$view_data["unidades_disponibles"] = $array_unit_symbol;
				
				if($tipo_unidad_id){
					$view_data["tipo_unidad_seleccionado"] = $tipo_unidad_id;
				}else{
					$view_data["tipo_unidad_seleccionado"] = "-";
				}
	
				$view_data["unidad_seleccionado"] = $unidad_id;
				$view_data["unidad_residuo"] = $data_unidad->nombre_unidad;
				
				$formulario = $this->Forms_model->get_one($form_id);
				
				// DEFINICION TIPO DE TRATAMIENTO
				//if($formulario->id_tipo_formulario == 1 && $formulario->flujo == "Residuo")
				$view_data["tipos_tratamiento"] = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"));
				if($formulario->tipo_tratamiento){
					$data_tipo_tratamiento = json_decode($formulario->tipo_tratamiento);
					$view_data["tipo_tratamiento"] = $data_tipo_tratamiento->tipo_tratamiento;
					$view_data["disabled_field"] = (boolean)$data_tipo_tratamiento->disabled_field;
				}else{
					$view_data["tipo_tratamiento"] = "";
					$view_data["disabled_field"] = false;
				}
				
				// DEFINICION TIPO DE ORIGEN
				$array_tipos_origen = array("" => "-");
				$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
				foreach($tipos_origen as $tipo_origen){
					$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
				}
				$view_data["array_tipos_origen"] = $array_tipos_origen;
				if($formulario->tipo_origen){
					$data_tipo_origen = json_decode($formulario->tipo_origen);
					$view_data["type_of_origin"] = $data_tipo_origen->type_of_origin;
					$view_data["default_matter"] = $data_tipo_origen->default_matter;
					$view_data["type_of_origin_name"] = $this->EC_Types_of_origin_model->get_one($data_tipo_origen->type_of_origin)->nombre; 
					$view_data["disabled_type_of_origin"] = (boolean)$data_tipo_origen->disabled_field;
										
					$array_materias_por_defecto = array("" => "-");
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $data_tipo_origen->type_of_origin,
						"deleted" => 0
					))->result();
					
					foreach($tipos_origen_materia as $tipo_origen_materia){
						$array_materias_por_defecto[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
					}
					$view_data["array_materias_por_defecto"] = $array_materias_por_defecto;
					
				}else{
					$view_data["tipo_origen"] = "";
					$view_data["disabled_type_of_origin"] = false;
				}
				
				// DEFINICION TIPO POR DEFECTO
				$array_tipos_por_defecto = array("" => "-");
				$tipos_por_defecto = $this->EC_Types_no_apply_model->get_all()->result();
				foreach($tipos_por_defecto as $tipo_por_defecto){
					$array_tipos_por_defecto[$tipo_por_defecto->id] = lang($tipo_por_defecto->nombre);
				}
				$view_data["array_tipos_por_defecto"] = $array_tipos_por_defecto;
				if($formulario->tipo_por_defecto){
					$data_tipo_por_defecto = json_decode($formulario->tipo_por_defecto);
					$view_data["tipo_por_defecto"] = $data_tipo_por_defecto->default_type;
					$view_data["default_type_disabled"] = (boolean)$data_tipo_por_defecto->disabled_field;
				}else{
					$view_data["tipo_por_defecto"] = "";
					$view_data["default_type_disabled"] = false;
				}
				
				$view_data["campos_de_formulario"] = $this->Fields_model->get_fields_of_form($form_id)->result_array();
	
				if($formulario->id_tipo_formulario == 2){
					$view_data["campos_de_proyecto"] = $this->Fields_model->get_fields_of_projects_where_not($view_data["form_rel_project"]->id_proyecto, array("id_tipo_campo" => 16))->result_array();
				} else {
					$view_data["campos_de_proyecto"] = $this->Fields_model->get_details(array("id_proyecto" => $view_data["form_rel_project"]->id_proyecto))->result_array();
				}
				
				$view_data["cliente"] = $this->Clients_model->get_one_where(array('id' => $view_data['model_info']->id_cliente, "deleted" => 0)); //sigla cliente
				$view_data["proyecto"] = $this->Projects_model->get_one_where(array('id' => $view_data["form_rel_project"]->id_proyecto, "deleted" => 0)); //sigla proyecto
				$view_data["sigla_proyecto"] = $view_data["proyecto"]->sigla;
				$view_data["sigla_cliente"] = $view_data["cliente"]->sigla;
				$view_data["materiales_de_formulario"] = $this->Materials_model->get_materials_of_form($form_id);
				
				$array_materiales_formulario = array();
				$categorias_disponibles = array();
				
				foreach($view_data["materiales_de_formulario"] as $material_formulario){
					
					$array_materiales_formulario[] = $material_formulario["id"];
					
					$material_rel_categoria = $this->Materials_rel_category_model->get_all_where(array("id_material" => $material_formulario["id"], "deleted" => 0))->result();
					foreach($material_rel_categoria as $mat_rel_cat){
						$categoria = $this->Categories_model->get_one($mat_rel_cat->id_categoria);
						$categorias_disponibles[$categoria->id] = $categoria->nombre;
					}
					
				}
				
				$view_data["array_materiales_formulario"] = $array_materiales_formulario;
				$view_data["categorias_disponibles"] = $categorias_disponibles;
				$view_data["categorias_de_formulario"] = $this->Categories_model->get_categories_of_material_of_form($form_id)->result(); 
				
				// VALIDACION EDICION DE CAMPOS
				if($formulario->id_tipo_formulario == 1){
					$list_data = $this->Environmental_records_model->get_values_of_record($form_id)->result();
				}
				if($formulario->id_tipo_formulario == 2){
					$list_data = $this->Feeders_model->get_values_of_record($form_id)->result();
				}
				if($formulario->id_tipo_formulario == 3){
					$list_data = $this->Other_records_model->get_values_of_record($form_id)->result();
				}
			
				if(count($list_data) > 0){
					$view_data["campos_disabled"] = true;
					$view_data["unidad_disabled"] = true;
					$view_data["tipo_tratamiento_disabled"] = true;
					$view_data["type_of_origin_disabled"] = true;
					$view_data["disabled_default_type"] = true;
					//$view_data["materiales_disabled"] = true;
				}
				
				//MATERIALES DESHABILITADOS
				/*$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form_id, "deleted" => 0));
				$valores_formulario = $this->Values_model->get_all_where(array("id_formulario_rel_proyecto" => $formulario_rel_proyecto->id, "deleted" => 0))->result();
				$array_materiales_deshabilitados = array();
				
				foreach($valores_formulario as $valores){
					$datos_decode = json_decode($valores->datos);
					if(isset($datos_decode->id_categoria)){
						$material_de_categoria = $this->Materials_model->get_material_of_category($datos_decode->id_categoria)->result();
						foreach($material_de_categoria as $material){
							$array_materiales_deshabilitados[] = $material->id;
						}
					}
				}*/
				
				$id_cliente = $formulario->id_cliente;
				$id_proyecto = $this->Form_rel_project_model->get_one_where(array(
					"id_formulario" => $form_id,
					"deleted" => 0 
				))->id_proyecto;
				
				
				// DESHABILITAR MATERIALES DEL FORMULARIO, QUE SE USEN EN CRITERIO (RELACIONAMIENTO)
				$criterio = $this->Rule_model->get_one_where(array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_formulario" => $form_id,
					"deleted" => 0
				));
				$array_materiales_deshabilitados = array();
				foreach($view_data["materiales_de_formulario"] as $material_formulario){
					if($criterio->id_material == $material_formulario["id"]){
						$array_materiales_deshabilitados[] = $material_formulario["id"];
					}
				}
				$view_data["materiales_deshabilitados"] = $array_materiales_deshabilitados;
				
				// DESHABILITAR CATEGORÍAS DE FORMULARIO, QUE SE USEN EN CÁLCULO (RELACIONAMIENTO)
				$calculo = $this->Calculation_model->get_one_where(array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_criterio" => $criterio->id,
					"deleted" => 0
				));
				$array_categorias_deshabilitadas = array();
				foreach($view_data["categorias_de_formulario"] as $categoria_formulario){
					if($calculo->id_categoria == $categoria_formulario->id){
						$array_categorias_deshabilitadas[] = $categoria_formulario->id;
					}
				}
				$view_data["categorias_deshabilitadas"] = $array_categorias_deshabilitadas;
				
				// DESHABILITAR CAMPOS UNIDAD FIJO SI ESTÁ SIENDO USADO EN CÁLCULO
				$view_data["unidad_disabled_by_calculation"] = false;
				$array_id_campo_unidad_calculo = json_decode($calculo->id_campo_unidad, TRUE);
				foreach($array_id_campo_unidad_calculo as $id_campo_unidad){
					if($id_campo_unidad == "0"){
						$view_data["unidad_disabled_by_calculation"] = true;
					}
				}
				
				// DESHABILITAR CAMPOS UNIDAD DINÁMICOS SI ESTÁN SIENDO USADOS EN CÁLCULO
				$array_campos_unidad_formulario_deshabilitados = array();
				foreach($view_data["campos_de_formulario"] as $campo_formulario){
					if($campo_formulario["id_tipo_campo"] == "15"){ // UNIDAD
						if(in_array($campo_formulario["id"], $array_id_campo_unidad_calculo)){
							$array_campos_unidad_formulario_deshabilitados[] = $campo_formulario["id"];
						}
					}
				}
				$view_data["campos_unidad_formulario_deshabilitados"] = $array_campos_unidad_formulario_deshabilitados;
				
			} else {
				
				$view_data["cliente"] = $this->Clients_model->get_one_where(array('id' => $view_data['model_info']->id_cliente, "deleted" => 0)); //sigla cliente
				$campo_fijo_rel_formulario_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array("id_formulario" => $view_data['model_info']->id, "deleted" => 0));
				$proyecto = $this->Projects_model->get_one($campo_fijo_rel_formulario_rel_proyecto->id_proyecto);
				
				$view_data["proyecto"] = $this->Projects_model->get_one_where(array('id' => $proyecto->id, "deleted" => 0)); //sigla proyecto
				$view_data["sigla_proyecto"] = $view_data["proyecto"]->sigla;
				$view_data["sigla_cliente"] = $view_data["cliente"]->sigla;
				
				$view_data["campos_de_formulario"] = $this->Fields_model->get_fields_of_fixed_form($form_id)->result_array();
				$view_data["campos_de_proyecto"] = $this->Fixed_fields_model->get_all_where(array(
					"codigo_formulario_fijo" => $view_data['model_info']->codigo_formulario_fijo,
					"deleted" => 0
				))->result_array();
			}
		}
		
        $this->load->view('forms/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }
	/* insert or update a form */
	
	function save() {

		$form_id = $this->input->post('id');
		$form_rel_project_id = $this->input->post('id_form_rel_project');
		$multiselect_campos = $this->input->post('campos');
		$multiselect_materiales = $this->input->post('materiales');
		
		//$multiselect_categorias = $this->input->post('categorias');
		$a_categorias = $this->input->post('categorias');
		$multiselect_categorias = array_unique($a_categorias);
		
		$id_cliente = $this->input->post('client');
		//validar que se seleccione al menos una categoría por material seleccionado		
		$array_materiales_de_categoria = array(); //todas las categorias de los materiales seleccionados.
		foreach($multiselect_categorias as $id_categoria){
			$material_de_categoria = $this->Materials_rel_category_model->get_one_where(array("id_categoria" => $id_categoria, "deleted" => 0));
			$array_materiales_de_categoria[] = $material_de_categoria->id_material;
		}

		$existe_material = true;
		foreach($multiselect_materiales as $id_material){
			if(!in_array($id_material, $array_materiales_de_categoria)){
				$existe_material = false;
			} 
		}
		
		if(!$existe_material){
			echo json_encode(array("success" => false, 'message' => lang('missing_categories')));
			exit();
		}

		//Validar que no se puedan crear más de 1 formulario no borrado con el mismo código
		if ($this->Forms_model->is_code_exists($this->input->post("codigo"), $form_id)) {
			echo json_encode(array("success" => false, 'message' => lang('repeated_code')));
			exit(); 
		}
		
		// Validar que si se está en un flujo residuo, no se puede agregar una categoria
		// que ya exista en un formulario con flujo consumo, y viceversa
		
		$json_tipo_tratamiento = NULL;		
		if($this->input->post("flow") == "Residuo"){		
			
			$opciones = array(
				"flujo" => "Residuo",
				"id_cliente" => $id_cliente,
				"id_proyecto" => $this->input->post('project'),
			);
			
			$formularios_flujos_residuo = $this->Forms_model->get_forms_of_project($opciones)->result();
			
			foreach($formularios_flujos_residuo as $form_residuo){
				if($form_residuo->id == $form_id){
					continue;
				}
				$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form($form_residuo->id)->result();
				foreach($form_rel_mat_rel_cat as $rel){
					foreach($multiselect_categorias as $id_categoria){
						if($id_categoria == $rel->id_categoria){
							echo json_encode(array("success" => false, 'message' => lang('category_exists_in_waste_form')));
							exit();
						}				
					}
				}
			}
			
			// TIPO DE TRATAMIENTO POR DEFECTO
			$type_of_treatment = $this->input->post('type_of_treatment');
			$disabled_field = (!$this->input->post('disabled_field'))?"0":$this->input->post('disabled_field');
			
			if($type_of_treatment == "" && $disabled_field){
				echo json_encode(array("success" => false, 'message' => lang('not_default_and_disabled')));
				exit();
			}
			
			$json_data = array("tipo_tratamiento" => $type_of_treatment, "disabled_field" => $disabled_field);
			$json_tipo_tratamiento = json_encode($json_data);
			
			if($form_id){
				// SI SE ESTA EDITANDO UN FORMULARIO CON REGISTROS, INGRESAR FORZADAMENTE TIPO DE TRATAMIENTO DEFINIDAS
				// DADO QUE LOS CAMPOS DE TIPO DE TRATAMIENTO ESTÁN DESHABILITADOS
				$list_data = $this->Environmental_records_model->get_values_of_record($form_id)->result();
				
				if(count($list_data) > 0){
					$info_formulario = $this->Forms_model->get_one($form_id);//{"nombre_unidad":"Masa","tipo_unidad_id":1,"unidad_id":1}
					$tipo_tratamiento_decoded = json_decode($info_formulario->tipo_tratamiento);
					
					$type_of_treatment = $tipo_tratamiento_decoded->tipo_tratamiento;
					$disabled_field = $tipo_tratamiento_decoded->disabled_field;
					
					$json_data = array("tipo_tratamiento" => $type_of_treatment, "disabled_field" => $disabled_field);
					$json_tipo_tratamiento = json_encode($json_data);
				}
			}
			
		}
		
		if($this->input->post("flow") == "Consumo"){
			
			$opciones = array(
				"flujo" => "Consumo",
				"id_cliente" => $id_cliente,
				"id_proyecto" => $this->input->post('project')
			);
			$formularios_flujos_consumo = $this->Forms_model->get_forms_of_project($opciones)->result();
	
			
			foreach($formularios_flujos_consumo as $form_consumo){
				if($form_consumo->id == $form_id){
					continue;
				}
				$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form($form_consumo->id)->result();
				foreach($form_rel_mat_rel_cat as $rel){
					foreach($multiselect_categorias as $id_categoria){
						if($id_categoria == $rel->id_categoria){
							echo json_encode(array("success" => false, 'message' => lang('category_exists_in_consumption_form')));
							exit();
						}				
					}
				}
			}
			
			// TIPO DE ORIGEN
			
			$type_of_origin = $this->input->post("type_of_origin");
			$disabled_field = (!$this->input->post('disabled_field'))?"0":$this->input->post('disabled_field');
			
			if($type_of_origin == "" && $disabled_field){
				echo json_encode(array("success" => false, 'message' => lang('not_default_and_disabled')));
				exit();
			}
			
			$default_matter = $this->input->post("default_matter");
			if($type_of_origin == "2"){	
				$json_data = array("type_of_origin" => $type_of_origin, "disabled_field" => "1");
			} else {
				if($default_matter){
					$json_data = array("type_of_origin" => $type_of_origin, "default_matter" => $default_matter, "disabled_field" => $disabled_field);
				} else {
					$json_data = array("type_of_origin" => $type_of_origin, "disabled_field" => $disabled_field);
				}
			}
			$json_type_of_origin = json_encode($json_data);
			
			if($form_id){
				// SI SE ESTA EDITANDO UN FORMULARIO CON REGISTROS, INGRESAR FORZADAMENTE TIPO DE ORIGEN DEFINIDAS
				// DADO QUE LOS CAMPOS DE TIPO DE ORIGEN ESTÁN DESHABILITADOS
				$list_data = $this->Environmental_records_model->get_values_of_record($form_id)->result();
				if(count($list_data) > 0){
					$info_formulario = $this->Forms_model->get_one($form_id);
					$tipo_origen_decoded = json_decode($info_formulario->tipo_origen);
					
					$type_of_origin = $tipo_origen_decoded->type_of_origin;
					$default_matter = $tipo_origen_decoded->default_matter;
					$disabled_field = $tipo_origen_decoded->disabled_field;
					
					if($type_of_origin == "2"){	
						$json_data = array("type_of_origin" => $type_of_origin, "disabled_field" => "1");
					} else {
						if($default_matter){
							$json_data = array("type_of_origin" => $type_of_origin, "default_matter" => $default_matter, "disabled_field" => $disabled_field);
						} else {
							$json_data = array("type_of_origin" => $type_of_origin, "disabled_field" => $disabled_field);
						}
					}
					
					$json_type_of_origin = json_encode($json_data);
					
				}
			}
		}
		
		if($this->input->post("flow") == "No Aplica"){
			
			$opciones = array(
				"flujo" => "No Aplica",
				"id_cliente" => $id_cliente,
				"id_proyecto" => $this->input->post('project'),
			);
			
			$formularios_flujos_residuo = $this->Forms_model->get_forms_of_project($opciones)->result();
			
			foreach($formularios_flujos_residuo as $form_residuo){
				if($form_residuo->id == $form_id){
					continue;
				}
				$form_rel_mat_rel_cat = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form($form_residuo->id)->result();
				foreach($form_rel_mat_rel_cat as $rel){
					foreach($multiselect_categorias as $id_categoria){
						if($id_categoria == $rel->id_categoria){
							echo json_encode(array("success" => false, 'message' => lang('category_exists_in_no_apply_form')));
							exit();
						}				
					}
				}
			}
			
			$default_type = $this->input->post("default_type");
			$disabled_field = (!$this->input->post('disabled_field'))?"0":$this->input->post('disabled_field');
			
			if($default_type == "" && $disabled_field){
				echo json_encode(array("success" => false, 'message' => lang('not_default_and_disabled')));
				exit();
			}
			
			$json_data = array("default_type" => $default_type, "disabled_field" => $disabled_field);
			$json_default_type = json_encode($json_data);
			
		}
		
		$id_proyecto = $this->input->post('project');
		
		$waste_unit = $this->input->post('waste_unit');
		$unit_field = $this->input->post('unit_field');
		$unit_symbol = $this->input->post('unit_symbol');
		
		if($form_id){
			// SI SE ESTA EDITANDO UN FORMULARIO CON REGISTROS, INGRESAR FORZADAMENTE UNIDADES PREVIAMENTE DEFINIDAS
			// DADO QUE LOS CAMPOS DE UNIDAD FIJA ESTÁN DESHABILITADOS
			if($this->input->post('tipo_formulario') == 1){
				$list_data = $this->Environmental_records_model->get_values_of_record($form_id)->result();
				
				if(count($list_data) > 0){
					$info_formulario = $this->Forms_model->get_one($form_id);//{"nombre_unidad":"Masa","tipo_unidad_id":1,"unidad_id":1}
					$unidad_decoded = json_decode($info_formulario->unidad);
					
					$waste_unit = $unidad_decoded->nombre_unidad;
					$unit_field = $unidad_decoded->tipo_unidad_id;
					$unit_symbol = $unidad_decoded->unidad_id;
				}
			}
		}
		
		if($this->input->post('tipo_formulario') == 1){
			if(!$unit_field){
				echo json_encode(array("success" => false, 'message' => lang('unit_type_warning')));
				exit();
			}
			
			if(!$unit_symbol){
				echo json_encode(array("success" => false, 'message' => lang('unit_warning')));
				exit();
			}
			$json_data = array("nombre_unidad" => $waste_unit, "tipo_unidad_id" => (int)$unit_field, "unidad_id" => (int)$unit_symbol);
			$json_unidad = json_encode($json_data);
		}

		$save_id = false;
		
		$data_form = array(
			"id_tipo_formulario" => $this->input->post('tipo_formulario'),
			"id_cliente" => $this->input->post('client'),
			"nombre" => $this->input->post('nombre'),
			"descripcion" => htmlspecialchars(trim($this->input->post('descripcion'))),
			"numero" => $this->input->post("form_number"),
			"codigo" => $this->input->post("codigo"),
			"flujo" => $this->input->post("flow"),
			"unidad" => $json_unidad,
			"tipo_tratamiento" => $json_tipo_tratamiento,
			"tipo_origen" => $json_type_of_origin,
			"tipo_por_defecto" => $json_default_type,
			"icono" => $this->input->post("icono"),
		);
		
		$data_form_rel_project = array(
			"id_proyecto" => $this->input->post('project'),
		);
		
		$data_field_rel_form = array();
		$data_form_rel_materials = array();		
		$data_form_rel_mat_rel_cat = array();
		
		// edit
		if($form_id){
			
			// Arma array de las categorías del formulario actuales, para saber si han sido modificadas por admin (Para Notificaciones)
			$categorias_formulario = $this->Form_rel_materiales_rel_categorias_model->get_categories_related_to_form($form_id)->result();
			$array_categorias_formulario = array();
			foreach($categorias_formulario as $rel){
				$array_categorias_formulario[] = $rel->id_categoria;
			}
			// Fin
			
			$info_formulario = $this->Forms_model->get_one($form_id);

			if(!$info_formulario->fijo) {

				$data_form["modified_by"] = $this->login_user->id;
				$data_form["modified"] = get_current_utc_time();
				$data_form_rel_project["modified_by"] = $this->login_user->id;
				$data_form_rel_project["modified"] = get_current_utc_time();
				$data_field_rel_form["modified_by"] = $this->login_user->id;
				$data_field_rel_form["modified"] = get_current_utc_time();
				$this->Form_rel_project_model->save($data_form_rel_project, $form_rel_project_id);	
				
				if($multiselect_campos)	{
					$delete_field_rel_form = $this->Field_rel_form_model->delete_fields_related_to_form($form_id);
					if($delete_field_rel_form){
						foreach($multiselect_campos as $id_campo){
							$id_campo = (int)$id_campo;
							$data_field_rel_form['id_campo'] = $id_campo;
							$data_field_rel_form['id_formulario'] = $form_id;
							$save_field_rel_form = $this->Field_rel_form_model->save($data_field_rel_form);
						}
					}
				}
				
				if($multiselect_materiales){
					$form_rel_materiales = $this->Form_rel_material_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result();
					if($form_rel_materiales){
						foreach($form_rel_materiales as $form_rel_material){
							$this->Form_rel_material_model->delete($form_rel_material->id);
						}
					}
					foreach($multiselect_materiales as $id_material){
						$id_material = (int)$id_material;
						$data_form_rel_materials["id_formulario"] = $form_id;
						$data_form_rel_materials["id_material"] = $id_material;
						$save_form_rel_material = $this->Form_rel_material_model->save($data_form_rel_materials);
					}
				}			
				
				///buscar formulario_rel_materiales por id_formulario
				$formulario_rel_materiales = $this->Form_rel_material_model->get_materials_related_to_form($form_id)->result();
				
				//categorias del formulario antes de ser editadas
				if($multiselect_categorias){
					$form_rel_materiales_rel_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result();
					if($form_rel_materiales_rel_categorias){
						foreach($form_rel_materiales_rel_categorias as $form_rel_material_rel_categoria){
							$this->Form_rel_materiales_rel_categorias_model->delete($form_rel_material_rel_categoria->id);
						}
					}
					foreach($multiselect_categorias as $id_categoria){
						//buscar material de $id_categoria
						$material_de_categoria = $this->Materials_model->get_material_of_category($id_categoria)->row();
						$id_categoria = (int)$id_categoria;
						$data_form_rel_mat_rel_cat["id_formulario"] = $form_id;
						$data_form_rel_mat_rel_cat["id_material"] = $material_de_categoria->id; 
						$data_form_rel_mat_rel_cat["id_categoria"] = $id_categoria;
						$this->Form_rel_materiales_rel_categorias_model->save($data_form_rel_mat_rel_cat);
					}
				}
				
				// Actualizar configuración de factores de transformación cuando admin elimine una categoria del formulario
				// si el admin elemina una categoria del formulario, se debe eliminar las filas de config que tengan la categoria
				// eliminada si es que esta categoria no se repite en otros formularios del cliente.
				
				// Traigo la configuración actual de factores de transformación del cliente
				$config_factores_transformacion = $this->EC_Client_transformation_factors_config_model->get_all_where(array(
					"id_cliente" => $id_cliente,
					"deleted" => 0
				))->result();
				$array_config_factores_transformacion = array();
				foreach($config_factores_transformacion as $config){
					$array_config_factores_transformacion[] = $config->id_categoria;
				}
				
				// Traigo las categorias (ya actualizadas) de todos los proyectos del cliente.
				$categorias_proyectos = $this->Categories_model->get_categories_of_materials_client_projects($id_cliente)->result();
				$array_categorias_proyectos = array();
				foreach($categorias_proyectos as $categoria){
					$array_categorias_proyectos[] = $categoria->id_categoria;
				}
				
				foreach($array_config_factores_transformacion as $id_categoria_config){
					if(!in_array($id_categoria_config, $array_categorias_proyectos)){
						$config = $this->EC_Client_transformation_factors_config_model->get_one_where(array(
							"id_cliente" => $id_cliente,
							"id_categoria" => $id_categoria_config,
							"deleted" => 0
						));
						$delete_config = $this->EC_Client_transformation_factors_config_model->delete($config->id);						
					} 
				}
				
				
				
				$formulario = $this->Forms_model->get_one($form_id);
				$save_id = $this->Forms_model->save($data_form, $form_id);
				$formulario_editado = $this->Forms_model->get_one($save_id);
				
				$valores_asociados_fomulario = $this->Form_values_model->get_forms_values_of_form($form_id)->result();
				foreach($valores_asociados_fomulario as $valores_formulario){
					
					$datos = json_decode($valores_formulario->datos,true);
					$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $unit_field, "deleted" => 0))->nombre;
					$unidad = $this->Unity_model->get_one_where(array("id" => $unit_symbol, "deleted" => 0))->nombre;
					
					$datos["tipo_unidad"] = $tipo_unidad; 
					$datos["unidad"] = $unidad; 
					
					$new_datos = json_encode($datos);
					$valores_formulario->datos = $new_datos;
					$update_values = (array)$valores_formulario;
					$this->Form_values_model->save($update_values,$update_values["id"]);
					
				}
				
				if($formulario->flujo == "Consumo"){
					
					// CONSULTO CATEGORIAS A NIVEL DE PROYECTO
					$categorias_proyecto = $this->Client_consumptions_settings_model->get_categories_of_client_project($id_cliente, $id_proyecto)->result();
					// CREO UN ARREGLO CON ESAS ID_CATEGORIAS
					$array_id_categorias_proyecto = array();
					foreach($categorias_proyecto as $objeto_categoria){
						$array_id_categorias_proyecto[] = $objeto_categoria->id_categoria;
					}
					// POR CADA CATEGORIA DEL PROYECTO
					foreach($array_id_categorias_proyecto as $id_categoria){
						// VERIFICO SI EXISTE UN REGISTRO ASOCIADO A LA CATEGORIA (INDIFERENTE DE SU ESTADO)
						$existe = $this->Client_consumptions_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria));
						if($existe->id){
							$this->Client_consumptions_settings_model->delete($existe->id, true);
						}else{
							$data_consumptions_settings = array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria, "tabla" => 1, "grafico" => 1, "deleted" => 0);
							$this->Client_consumptions_settings_model->save($data_consumptions_settings);
						}
					}
					
					$categorias_tabla = $this->Client_consumptions_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
					foreach($categorias_tabla as $objeto_categoria_tabla){
						if(!in_array($objeto_categoria_tabla->id_categoria, $array_id_categorias_proyecto)){
							$this->Client_consumptions_settings_model->delete($objeto_categoria_tabla->id);
						}
					}
					
				
				}elseif($formulario->flujo == "Residuo"){
					$categorias_proyecto = $this->Client_waste_settings_model->get_categories_of_client_project($id_cliente, $id_proyecto)->result();
					
					$array_id_categorias_proyecto = array();
					foreach($categorias_proyecto as $objeto_categoria){
						$array_id_categorias_proyecto[] = $objeto_categoria->id_categoria;
					}
					
					foreach($array_id_categorias_proyecto as $id_categoria){
						$existe = $this->Client_waste_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria));
						if($existe->id){
							$this->Client_waste_settings_model->delete($existe->id, true);
						}else{
							$data_consumptions_settings = array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria, "tabla" => 1, "grafico" => 1, "deleted" => 0);
							$this->Client_waste_settings_model->save($data_consumptions_settings);
						}
					}
					
					$categorias_tabla = $this->Client_waste_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
					foreach($categorias_tabla as $objeto_categoria_tabla){
						if(!in_array($objeto_categoria_tabla->id_categoria, $array_id_categorias_proyecto)){
							$this->Client_waste_settings_model->delete($objeto_categoria_tabla->id);
						}
					}
	
				}else{// NO APLICA
					
				}
				
				$crea_carpeta = $this->create_form_folder($id_cliente, $id_proyecto, $form_id);
				
				
			} else { // Si el formulario es fijo
				// Guardar histórico notificaciones
				// Edición de formulario - Nombre
				if($info_formulario->nombre != $this->input->post('nombre')){
					$options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "form_edit_name",
						"id_element" => $info_formulario->id
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de formulario - Categoría
				$categorias_modificado = FALSE;
				sort($array_categorias_formulario);
				sort($multiselect_categorias);
				
				if($array_categorias_formulario != $multiselect_categorias){
					$categorias_modificado = TRUE;
				}
				
				if($categorias_modificado){
					$options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "form_edit_cat",
						"id_element" => $info_formulario->id
					);
					ayn_save_historical_notification($options);
				}

				$crea_carpeta = $this->create_form_folder($id_cliente, $id_proyecto, $form_id);
				
				$data_form = array(
					"nombre" => $this->input->post('nombre'),
					"descripcion" => $this->input->post('descripcion'),
					"numero" => $this->input->post("form_number"),
					"icono" => $this->input->post("icono"),
					"modified_by" => $this->login_user->id,
					"modified" => get_current_utc_time()
				);
				
				$save_id = $this->Forms_model->save($data_form, $info_formulario->id);
				
				if ($save_id) {
					echo json_encode(array("success" => true, "data" => $this->_row_data_formularios_fijos($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
				} else {
					echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
				}
				exit();
			}
				
		}else{ //insert
			
			$data_form["created_by"] = $this->login_user->id;
			$data_form["created"] = get_current_utc_time();
			$data_form_rel_project["created_by"] = $this->login_user->id;
			$data_form_rel_project["created"] = get_current_utc_time();
			$data_field_rel_form["created_by"] = $this->login_user->id;
			$data_field_rel_form["created"] = get_current_utc_time();
			$save_id = $this->Forms_model->save($data_form);
			$formulario = $this->Forms_model->get_one($save_id);
			
			foreach($multiselect_campos as $id_campo){
				$id_campo = (int)$id_campo;
				$id_formulario = (int)$save_id;
				$data_field_rel_form['id_campo'] = $id_campo;
				$data_field_rel_form['id_formulario'] = $save_id;
				$save_field_rel_form = $this->Field_rel_form_model->save($data_field_rel_form);
			}
			
			$data_form_rel_project['id_formulario'] = $save_id;
			$this->Form_rel_project_model->save($data_form_rel_project, $form_rel_project_id);
			
			if($multiselect_materiales){
				foreach($multiselect_materiales as $id_material){
					$id_material = (int)$id_material;
					$data_form_rel_materials["id_formulario"] = $save_id;
					$data_form_rel_materials["id_material"] = $id_material;
					$this->Form_rel_material_model->save($data_form_rel_materials);
				}
			}
			//buscar formulario_rel_materiales por id_formulario
			$formulario_rel_materiales = $this->Form_rel_material_model->get_materials_related_to_form($save_id)->result();
			
			if($multiselect_categorias){
				foreach($multiselect_categorias as $id_categoria){
					//buscar material de $id_categoria
					$material_de_categoria = $this->Materials_model->get_material_of_category($id_categoria)->row();
					$id_categoria = (int)$id_categoria;
					$data_form_rel_mat_rel_cat["id_formulario"] = $save_id;
					$data_form_rel_mat_rel_cat["id_material"] = $material_de_categoria->id; 
					$data_form_rel_mat_rel_cat["id_categoria"] = $id_categoria;
					$this->Form_rel_materiales_rel_categorias_model->save($data_form_rel_mat_rel_cat);
				}
			}
			
			// SINCRONIZACION DE DATOS DE CATEGORIAS ASOCIADAS A PROYECTO EN SETTINGS DE CLIENTE
			if($formulario->flujo == "Consumo"){
				// CONSULTO CATEGORIAS A NIVEL DE PROYECTO
				$categorias_proyecto = $this->Client_consumptions_settings_model->get_categories_of_client_project($id_cliente, $id_proyecto)->result();
				// CREO UN ARREGLO CON ESAS ID_CATEGORIAS
				$array_id_categorias_proyecto = array();
				foreach($categorias_proyecto as $objeto_categoria){
					$array_id_categorias_proyecto[] = $objeto_categoria->id_categoria;
				}
				// POR CADA CATEGORIA DEL PROYECTO
				foreach($array_id_categorias_proyecto as $id_categoria){
					// VERIFICO SI EXISTE UN REGISTRO ASOCIADO A LA CATEGORIA (INDIFERENTE DE SU ESTADO)
					$existe = $this->Client_consumptions_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria));
					if($existe->id){
						$this->Client_consumptions_settings_model->delete($existe->id, true);
					}else{
						$data_consumptions_settings = array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria, "tabla" => 1, "grafico" => 1, "deleted" => 0);
						$this->Client_consumptions_settings_model->save($data_consumptions_settings);
					}
				}
				$categorias_tabla = $this->Client_consumptions_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
				foreach($categorias_tabla as $objeto_categoria_tabla){
					if(!in_array($objeto_categoria_tabla->id_categoria, $array_id_categorias_proyecto)){
						$this->Client_consumptions_settings_model->delete($objeto_categoria_tabla->id);
					}
				}

			}elseif($formulario->flujo == "Residuo"){
				$categorias_proyecto = $this->Client_waste_settings_model->get_categories_of_client_project($id_cliente, $id_proyecto)->result();
				$array_id_categorias_proyecto = array();
				foreach($categorias_proyecto as $objeto_categoria){
					$array_id_categorias_proyecto[] = $objeto_categoria->id_categoria;
				}
				
				foreach($array_id_categorias_proyecto as $id_categoria){
					$existe = $this->Client_waste_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria));
					if($existe->id){
						$this->Client_waste_settings_model->delete($existe->id, true);
					}else{
						$data_consumptions_settings = array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "id_categoria" => $id_categoria, "tabla" => 1, "grafico" => 1, "deleted" => 0);
						$this->Client_waste_settings_model->save($data_consumptions_settings);
					}
				}
				
				$categorias_tabla = $this->Client_waste_settings_model->get_all_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0))->result();
				foreach($categorias_tabla as $objeto_categoria_tabla){
					if(!in_array($objeto_categoria_tabla->id_categoria, $array_id_categorias_proyecto)){
						$this->Client_waste_settings_model->delete($objeto_categoria_tabla->id);
					}
				}
	
			}else{// NO APLICA
				
			}
			$crea_carpeta = $this->create_form_folder($id_cliente, $id_proyecto, $save_id);
		}
		
		if ($save_id) {
			
			// Guardar histórico notificaciones
			if(!$form_id){
				
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $this->login_user->id,
					"module_level" => "admin",
					"id_admin_module" => $this->id_admin_module,
					"id_admin_submodule" => $this->id_admin_submodule,
					"event" => "form_add",
					"id_element" => $save_id
				);
				ayn_save_historical_notification($options);
			
			} else {
			
				// Edición de formulario - Nombre
				if($info_formulario->nombre != $this->input->post('nombre')){
					$options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "form_edit_name",
						"id_element" => $info_formulario->id
					);
					ayn_save_historical_notification($options);
				}
				
				// Edición de formulario - Categoría
				$categorias_modificado = FALSE;
				sort($array_categorias_formulario);
				sort($multiselect_categorias);
				
				if($array_categorias_formulario != $multiselect_categorias){
					$categorias_modificado = TRUE;
				}
				
				if($categorias_modificado){
					$options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "form_edit_cat",
						"id_element" => $info_formulario->id
					);
					ayn_save_historical_notification($options);
				}
				
			}
			
			echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
	}
	
	function create_form_folder($client_id, $project_id, $form_id) {
		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$client_id.'/project_'.$project_id.'/form_'.$form_id)) {
			if(mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$client_id.'/project_'.$project_id.'/form_'.$form_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
	}
	
    /* delete or undo a form */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		$form = $this->Forms_model->get_one($id);
		
		if(!$form->fijo){
			
			$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form->id, "deleted" => 0));
			$id_proyecto = $formulario_rel_proyecto->id_proyecto;
			
			$this->delete_cascade_form($id);
		
			if ($this->input->post('undo')) {
				if ($this->Forms_model->delete($id, true)) {
					echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
				} else {
					echo json_encode(array("success" => false, lang('error_occurred')));
				}
			} else {
				if ($this->Forms_model->delete($id)) {
					
					// Guardar histórico notificaciones
					$options = array(
						"id_client" => $form->id_cliente,
						"id_project" => $id_proyecto,
						"id_user" => $this->login_user->id,
						"module_level" => "admin",
						"id_admin_module" => $this->id_admin_module,
						"id_admin_submodule" => $this->id_admin_submodule,
						"event" => "form_delete",
						"id_element" => $id
					);
					ayn_save_historical_notification($options);
					
					echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
				} else {
					echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
				}
			}
		
		} else { // Si el formulario es fijo no se puede eliminar
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
		}
    }

    /* list of data, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"id_tipo_formulario" => $this->input->post("id_tipo_formulario"),
		);
		
        $list_data = $this->Forms_model->get_details($options)->result();
        $result = array();
		
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
		
		$list_data_formularios_fijos = $this->Forms_model->get_details_formularios_fijos($options)->result();
		foreach($list_data_formularios_fijos as $data){
			$result[] = $this->_make_row_formularios_fijos($data);
		}
		
        echo json_encode(array("data" => $result));
    }
	
	private function _make_row_formularios_fijos($data){
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$campo_fijo_rel_formulario_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array("id_formulario" => $data->id, "deleted" => 0));
		$proyecto = $this->Projects_model->get_one($campo_fijo_rel_formulario_rel_proyecto->id_proyecto);
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		
        $row_data = array($data->id,
			modal_anchor(get_uri("forms/view/" . $data->id), $data->nombre, array("title" => lang('view_form'))), 
			$cliente->company_name, $proyecto->title,
            ($data->descripcion) ? $tooltip_descripcion : "-",
			$data->tipo_formulario,
        );
		
		$row_data[] = modal_anchor(get_uri("forms/view_data/"), "<i class='fa fa-list-alt'></i>", array("class" => "view", "title" => $data->nombre, "data-post-id" => $data->id));

        $row_data[] = modal_anchor(get_uri("forms/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_form')))
				.modal_anchor(get_uri("forms/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_form'), "data-post-id" => $data->id))
				.'<span style="cursor: not-allowed;">' .js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_form'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri(), "data-action" => "delete-confirmation", "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';

        return $row_data;
		
	}
	
	private function _row_data_formularios_fijos($id){
		
		$options = array(
            "id" => $id,
        );
		
		$data = $this->Forms_model->get_details_formularios_fijos($options)->row();	
        return $this->_make_row_formularios_fijos($data);
		
	}

    /* return a row of form list table */

    private function _row_data($id) {
        
        $options = array(
            "id" => $id,
           
        );
        $data = $this->Forms_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of form list table */

    private function _make_row($data) {
		
		$cliente = $this->Clients_model->get_one($data->id_cliente);
		$forrmulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $data->id, "deleted" => 0));
		$proyecto = $this->Projects_model->get_one($forrmulario_rel_proyecto->id_proyecto);
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		
        $row_data = array($data->id,
			modal_anchor(get_uri("forms/view/" . $data->id), $data->nombre, array("title" => lang('view_form'))), 
			$cliente->company_name, $proyecto->title,
            ($data->descripcion) ? $tooltip_descripcion : "-",
			$data->tipo_formulario,
        );
		
		$row_data[] = modal_anchor(get_uri("forms/view_data/"), "<i class='fa fa-list-alt'></i>", array("class" => "view", "title" => $data->nombre, "data-post-id" => $data->id));

        $row_data[] = modal_anchor(get_uri("forms/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_form')))
				.modal_anchor(get_uri("forms/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_form'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_form'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("forms/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load form details view */

    function view($form_id = 0) {
        $this->access_only_allowed_members();

        if ($form_id) {
            $options = array("id" => $form_id);
            $form_info = $this->Forms_model->get_details($options)->row();
			$form_fijo_info = $this->Forms_model->get_details_formularios_fijos($options)->row();
			
            if ($form_info) {
				
				//$view_data["preview"] = $this->get_preview_form($form_info->id);
                $view_data['form_info'] = $form_info;
				
				$data_unidad = json_decode($form_info->unidad, true);
				$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $data_unidad["tipo_unidad_id"], "deleted" => 0))->nombre;
				$unidad = $this->Unity_model->get_one_where(array("id" => $data_unidad["unidad_id"], "deleted" => 0))->nombre;
				$view_data["nombre_unidad"] = $data_unidad["nombre_unidad"];
				$view_data["tipo_unidad"] = $tipo_unidad;
				$view_data["unidad"] = $unidad;
				
				$data_tipo_tratamiento = json_decode($form_info->tipo_tratamiento, true);
				$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $data_tipo_tratamiento["tipo_tratamiento"], "deleted" => 0))->nombre;
				$view_data["tipo_tratamiento"] = $tipo_tratamiento;
				$view_data["disabled_field"] = (bool)$data_tipo_tratamiento["disabled_field"];
				
				$data_type_of_origin = json_decode($form_info->tipo_origen, true);
				$type_of_origin = $this->EC_Types_of_origin_model->get_one_where(array("id" => $data_type_of_origin["type_of_origin"], "deleted" => 0))->nombre;
				$view_data["type_of_origin"] = lang($type_of_origin);
				$default_matter = $this->EC_Types_of_origin_matter_model->get_one_where(array("id" => $data_type_of_origin["default_matter"], "deleted" => 0))->nombre;
				$view_data["default_matter"] = lang($default_matter);
				$view_data["type_of_origin_disabled_field"] = (bool)$data_type_of_origin["disabled_field"];
				
				$data_tipo_por_defecto = json_decode($form_info->tipo_por_defecto, true);
				$tipo_por_defecto = $this->EC_Types_no_apply_model->get_one_where(array("id" => $data_tipo_por_defecto["default_type"], "deleted" => 0))->nombre;
				$view_data["default_type"] = lang($tipo_por_defecto);
				$view_data["disabled_field"] = (bool)$data_tipo_por_defecto["disabled_field"];
				
				$view_data['cliente'] = $this->Clients_model->get_one($view_data['form_info']->id_cliente);
				$proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $view_data['form_info']->id));
				$view_data['proyecto'] = $this->Projects_model->get_one($proyecto->id_proyecto);
                //$view_data["tab"] = $tab;
				$view_data["preview_form_fields"] = $this->get_preview_form_fields($form_info->id);
               	$view_data["materiales_de_formulario"] = $this->Materials_model->get_materials_of_form($form_id);
				$view_data["categorias_de_formulario"] = $this->Categories_model->get_categories_of_material_of_form($form_id)->result();
				
				$this->load->view('forms/view', $view_data);
            } elseif($form_fijo_info){
				
				$view_data['form_info'] = $form_fijo_info;
				$view_data['cliente'] = $this->Clients_model->get_one($view_data['form_info']->id_cliente);
				
				$campo_fijo_rel_formulario_rel_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array("id_formulario" => $view_data['form_info']->id, "deleted" => 0));
				$view_data['proyecto'] = $this->Projects_model->get_one($campo_fijo_rel_formulario_rel_proyecto->id_proyecto);
				
				$view_data["preview_form_fields"] = $this->get_preview_form_fields($view_data['form_info']->id);
				
				$this->load->view('forms/view', $view_data);
				
			} else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	/* load form data details view */

    function view_data() {
		
        $this->access_only_allowed_members();
		$id_record = $this->input->post("id");
		
        if($id_record) {
			
            //$form_info = $this->Forms_model->get_details(array("id" => $id_record))->row();
			$form_info = $this->Forms_model->get_one($id_record);
			
			if(!$form_info->fijo){
				
				$proyecto_rel_formulario = $this->Form_rel_project_model->get_one_where(
					array(
						"id_formulario" => $id_record,
						"deleted" => 0
					)
				);
				$id_proyecto = $proyecto_rel_formulario->id_proyecto;
			
				// Registro Ambiental
				if($form_info->id_tipo_formulario == 1){
					
					// Filtro Categorias
					$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
					$categorias = $this->Categories_model->get_categories_of_material_of_form($id_record)->result();
					foreach($categorias as $categoria){
						$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $categoria->id, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
						$nombre_categoria = ($categoria_alias->alias) ? $categoria_alias->alias : $categoria->nombre;	
						$array_categorias[] = array("id" => $categoria->id, "text" => $nombre_categoria);
					}
					$view_data['categorias_dropdown'] = json_encode($array_categorias);
					
					//VALIDAR QUE EL FORMULARIO QUE SE ESTA VIENDO PERTENECE AL MISMO CLIENTE DEL USUARIO EN SESIÓN			
					$formulario = $this->Forms_model->get_one($id_record);
		
					$options = array("id" => $id_record);
					$registros = $this->Environmental_records_model->get_values_of_record($id_record)->result();
					$num_registros = count($registros);
					$record_info = $this->Forms_model->get_details($options)->row();
					$unidad_data_json_encode = $record_info->unidad;
					$unidad_data_json_decode = json_decode($unidad_data_json_encode, true);
					$nombre_unidad = $unidad_data_json_decode["nombre_unidad"];
					$unidad = $this->Unity_model->get_one($unidad_data_json_decode["unidad_id"]);
					$proyecto = $this->Projects_model->get_one($id_proyecto);
					
					$view_data["project_info"] = $proyecto;
					$view_data['num_registros'] = $num_registros;
					$view_data['record_info'] = $record_info;
					
					$columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$json_string = "";
					foreach($columns as $column){
						
						if($column->id_tipo_campo == 1){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 2){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center"}';
						}else if($column->id_tipo_campo == 3){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 4){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
						}elseif($column->id_tipo_campo == 5){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
						}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 10){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option"}';
						}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 15){
							$column_options = json_decode($column->opciones, true);
							$id_unidad = $column_options[0]["id_unidad"];
							$unidad = $this->Unity_model->get_one($id_unidad);
							$json_string .= ',' . '{"title":"' . $column->nombre . ' ('.$unidad->nombre.')' .  '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 16){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else{
							$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
						}
					}
					
					$string_columnas = "";
					if($record_info->flujo == "Residuo"){
						
						$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
						$string_columnas .= ',{"title":"'.$nombre_unidad.' ('.$unidad->nombre.')", "class": "text-right dt-head-center"}';
						$string_columnas .= ',{"title":"'.lang("type_of_treatment").'", "class": "text-left dt-head-center"}';
						$string_columnas .= $json_string;
						$string_columnas .= ',{"title":"'.lang("retirement_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("retirement_evidence").'", "class": "text-center w100 no_breakline option"}';
						$string_columnas .= ',{"title":"'.lang("reception_evidence").'", "class": "text-center w100 no_breakline option"}';
						$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						
					}else if(($record_info->flujo == "Consumo")||($record_info->flujo == "No Aplica")){
						
						$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
						$string_columnas .= ',{"title":"'.$nombre_unidad.' ('.$unidad->nombre.')", "class": "text-right dt-head-center"}';
						$string_columnas .= ',{"title":"'.lang("type").'", "class": "text-left dt-head-center"}';
						$string_columnas .= $json_string;
						$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					}else{
						$string_columnas .= ',{"title":"'.lang("date_filed").'", "class": "text-left dt-head-center w100 no_breakline sorting_asc", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("category").'", "class": "text-left dt-head-center"}';
						$string_columnas .= $json_string;
						$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
						
					}
					
					$view_data["columnas"] = $string_columnas;
					
					$amount_columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$cantidad_columnas = array();
					foreach($amount_columns as $columns){
						if(($columns->id_tipo_campo == 11) || ($columns->id_tipo_campo == 12)){
							continue;
						}else{
							$cantidad_columnas[] = $columns;
						}
					}
					$view_data["cantidad_columnas"] = count($cantidad_columnas);
					
					$arrayFechas = array();
					foreach($registros as $index => $reg){
						if(!$reg->modified){
							$arrayFechas[$index] = $reg->created;
						} else {
							$arrayFechas[$index] = $reg->modified;
						}
					}
					
					$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
					$view_data["fecha_modificacion"] = $fecha_modificacion;
					
				}elseif($form_info->id_tipo_formulario == 2){
					
					$registros = $this->Feeders_model->get_values_of_record($id_record)->result();
					$num_registros = count($registros);
					$record_info = $this->Forms_model->get_details(array("id" => $id_record))->row();
					$proyecto = $this->Projects_model->get_one($id_proyecto);
					$view_data["project_info"] = $proyecto;
					
					$view_data['num_registros'] = $num_registros;				
					$view_data['record_info'] = $record_info;
					
					$columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$json_string = "";
					foreach($columns as $column){
						
						if($column->id_tipo_campo == 1){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 2){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center"}';
						}else if($column->id_tipo_campo == 3){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 4){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
						}elseif($column->id_tipo_campo == 5){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
						}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 10){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option"}';
						}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 15){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 16){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else{
							$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
						}
						
					}
					
					$string_columnas = "";
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$view_data["columnas"] = $string_columnas;
					
					$amount_columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$cantidad_columnas = array();
					foreach($amount_columns as $columns){
						if(($columns->id_tipo_campo == 11) || ($columns->id_tipo_campo == 12)){
							continue;
						}else{
							$cantidad_columnas[] = $columns;
						}
					}
					$view_data["cantidad_columnas"] = count($cantidad_columnas);
					
					$arrayFechas = array();
					foreach($registros as $index => $reg){
						if(!$reg->modified){
							$arrayFechas[$index] = $reg->created;
						} else {
							$arrayFechas[$index] = $reg->modified;
						}
					}
					
					$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
					$view_data["fecha_modificacion"] = $fecha_modificacion;
					
				}elseif($form_info->id_tipo_formulario == 3){
					
					$options = array("id" => $id_record);
					$registros = $this->Other_records_model->get_values_of_record($id_record)->result();
					$num_registros = count($registros);
					$record_info = $this->Forms_model->get_details($options)->row();
					$proyecto = $this->Projects_model->get_one($id_proyecto);
					$view_data["project_info"] = $proyecto;
					
					$view_data['num_registros'] = $num_registros;
					$view_data['record_info'] = $record_info;
					
					$columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					
					$json_string = "";
					foreach($columns as $column){
						
						if($column->id_tipo_campo == 1){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 2){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center"}';
						}else if($column->id_tipo_campo == 3){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 4){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
						}elseif($column->id_tipo_campo == 5){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
						}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 10){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option"}';
						}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
							continue;
						}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else if($column->id_tipo_campo == 15){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
						}else if($column->id_tipo_campo == 16){
							$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
						}else{
							$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
						}
						
					}
					
					$string_columnas = "";
					$string_columnas .= $json_string;
					$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
					$view_data["columnas"] = $string_columnas;
					
					$amount_columns = $this->Forms_model->get_fields_of_form($record_info->id)->result();
					$cantidad_columnas = array();
					foreach($amount_columns as $columns){
						if(($columns->id_tipo_campo == 11) || ($columns->id_tipo_campo == 12)){
							continue;
						}else{
							$cantidad_columnas[] = $columns;
						}
					}
					$view_data["cantidad_columnas"] = count($cantidad_columnas);
					//$view_data["cantidad_columnas"] = count($cantidad_columnas);
					
					$arrayFechas = array();
					foreach($registros as $index => $reg){
						if(!$reg->modified){
							$arrayFechas[$index] = $reg->created;
						} else {
							$arrayFechas[$index] = $reg->modified;
						}
					}
					
					$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
					$view_data["fecha_modificacion"] = $fecha_modificacion;
					
			
				}
			
				$this->load->view('forms/view_data', $view_data);
				
            }elseif($form_info->fijo){
				
				$proyecto_rel_formulario = $this->Fixed_field_rel_form_rel_project_model->get_one_where(
					array(
						"id_formulario" => $id_record,
						"deleted" => 0
					)
				);
				$id_proyecto = $proyecto_rel_formulario->id_proyecto;
				
				$options = array("id" => $id_record);
				$registros = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
				$num_registros = count($registros);
				$record_info = $this->Forms_model->get_details_formularios_fijos($options)->row();
				
				$proyecto = $this->Projects_model->get_one($id_proyecto);
				$view_data["project_info"] = $proyecto;
					
				$view_data['num_registros'] = $num_registros;
				$view_data['record_info'] = $record_info;

				$columns = $this->Fixed_fields_model->get_all_where(array(
					"codigo_formulario_fijo" => $record_info->codigo_formulario_fijo,
					"deleted" => 0
				))->result();
				
				$json_string = "";
				if($record_info->codigo_formulario_fijo != "or_responsables") {
					$json_string .= ',{title: "'.lang("date").'", "class": "text-center w50"}';
				}
				
				foreach($columns as $column){
					
					if($column->id_tipo_campo == 1){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
					}else if($column->id_tipo_campo == 2){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center"}';
					}else if($column->id_tipo_campo == 3){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
					}else if($column->id_tipo_campo == 4){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center", type: "extract-date"}';
					}elseif($column->id_tipo_campo == 5){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center no_breakline"}';
					}else if($column->id_tipo_campo >= 6 && $column->id_tipo_campo <= 9){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
					}else if($column->id_tipo_campo == 10){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-center option"}';
					}else if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
						continue;
					}else if($column->id_tipo_campo == 13 || $column->id_tipo_campo == 14){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
					}else if($column->id_tipo_campo == 15){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-right dt-head-center"}';
					}else if($column->id_tipo_campo == 16){
						$json_string .= ',' . '{"title":"' . $column->nombre . '", "class": "text-left dt-head-center"}';
					}else{
						$json_string .= ',' . '{"title":"' . $column->nombre . '"}';
					}
					
				}
				
				$string_columnas = "";
				$string_columnas .= $json_string;
				$string_columnas .= ',{"title":"'.lang("created_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
				$string_columnas .= ',{"title":"'.lang("modified_date").'", "class": "text-left dt-head-center w100 no_breakline", type: "extract-date"}';
				$view_data["columnas"] = $string_columnas;
				
				$cantidad_columnas = array();
				foreach($columns as $column){
					if(($column->id_tipo_campo == 11) || ($column->id_tipo_campo == 12)){
						continue;
					}else{
						$cantidad_columnas[] = $column;
					}
				}
				$view_data["cantidad_columnas"] = count($cantidad_columnas);
				
				$arrayFechas = array();
				foreach($registros as $index => $reg){
					if(!$reg->modified){
						$arrayFechas[$index] = $reg->created;
					} else {
						$arrayFechas[$index] = $reg->modified;
					}
				}
				
				$fecha_modificacion = ($record_info->modified > max($arrayFechas)) ? $record_info->modified : max($arrayFechas);
				$view_data["fecha_modificacion"] = $fecha_modificacion;
				
				$this->load->view('forms/view_data', $view_data);
				
			} else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function view_list_data($id_record = 0) {
		
		// Filtro AppTable
		$options = array(
			"id_categoria" => $this->input->post('id_categoria')
		);
		
        //$this->access_only_allowed_members();
		
		//$form_info = $this->Forms_model->get_details(array("id" => $id_record))->row();
		$form_info = $this->Forms_model->get_one($id_record);
		if($form_info->id_tipo_formulario == 1){
			 $list_data = $this->Environmental_records_model->get_values_of_record($id_record, $options)->result();
		}elseif($form_info->id_tipo_formulario == 2){
			$list_data = $this->Feeders_model->get_values_of_record($id_record)->result();
		}elseif($form_info->id_tipo_formulario == 3){
			if($form_info->fijo == 0){
				$list_data = $this->Other_records_model->get_values_of_record($id_record)->result();
			}else{
				$list_data = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
			}
		}
		
		if($form_info->fijo == 0){
			$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();
		}else{
			$columnas = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $form_info->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
		}
		
        $result = array(); 
        foreach ($list_data as $data) {			
			$result[] = $this->_view_make_row($data, $columnas, $id_record);
        }
        echo json_encode(array("data" => $result));
    }
	
	
	/* prepare a row of client list table */

    private function _view_make_row($data, $columnas, $id_record) {
		
		$record_info = $this->Forms_model->get_one($id_record);
		$flujo = $record_info->flujo;
		
		if($record_info->fijo == 0){
			$proyecto_rel_formulario = $this->Form_rel_project_model->get_one_where(
				array(
					"id_formulario" => $id_record,
					"deleted" => 0
				)
			);
		}else{
			$proyecto_rel_formulario = $this->Fixed_field_rel_form_rel_project_model->get_one_where(
				array(
					"id_formulario" => $id_record,
					"deleted" => 0
				)
			);
		}
		
		$id_proyecto = $proyecto_rel_formulario->id_proyecto;
		$proyecto = $this->Projects_model->get_one($id_proyecto);

		$row_data = array();
		$row_data[] = $data->id;
		$datos = json_decode($data->datos, true);
		
		if($record_info->id_tipo_formulario == 1){
		
			$id_categoria = $datos["id_categoria"];
			$categoria_original = $this->Categories_model->get_one_where(array('id' => $id_categoria, "deleted" => 0));
			$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
			
			if($categoria_alias->alias){
				$nombre_categoria = $categoria_alias->alias;
			}else{
				$nombre_categoria = $categoria_original->nombre;
			}
			
			$row_data[] = get_date_format($datos["fecha"], $id_proyecto);
			$row_data[] = $nombre_categoria;
			
			if(($record_info->flujo == "Residuo")||($record_info->flujo == "Consumo")||($record_info->flujo == "No Aplica")){
				
				if(isset($datos["unidad_residuo"])){
					$row_data[] = to_number_project_format($datos["unidad_residuo"], $id_proyecto);
				}else{
					$row_data[] = "-";
				}
			}
	
			if($record_info->flujo == "Residuo"){
				$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $datos["tipo_tratamiento"], "deleted" => 0));
				if(isset($datos["tipo_tratamiento"])){
					
					if($datos["tipo_tratamiento"] == $tipo_tratamiento->id){
						$row_data[] = $tipo_tratamiento->nombre;
					}
					
				}else{
					$row_data[] = "-";
				}
			}
			
			if($record_info->flujo == "Consumo"){
				
				$elemento = $this->Form_values_model->get_one($data->id);
				$datos_elemento = json_decode($elemento->datos, true);
				
				if($datos_elemento["type_of_origin"] == "1"){
					if(isset($datos_elemento["type_of_origin_matter"])){
						$type_of_origin_matter = $this->EC_Types_of_origin_matter_model->get_one($datos_elemento["type_of_origin_matter"]);
						$row_data[] = lang($type_of_origin_matter->nombre);
					} else {
						$row_data[] = "-";
					}
				} else {
					$type_of_origin = $this->EC_Types_of_origin_model->get_one($datos_elemento["type_of_origin"]);
					$row_data[] = lang($type_of_origin->nombre);
				}
				
			}
			
			if($record_info->flujo == "No Aplica"){
				
				$elemento = $this->Form_values_model->get_one($data->id);
				$datos_elemento = json_decode($elemento->datos, true);
				
				if(isset($datos_elemento["default_type"])){
					$default_type = $this->EC_Types_no_apply_model->get_one($datos_elemento["default_type"]);
					$row_data[] = lang($default_type->nombre);
				} else {
					$row_data[] = "-";
				}
				
			}
	
			if($data->datos){
				$arreglo_fila = json_decode($data->datos, true);
				$cont = 0;
				
				foreach($columnas as $columna) {
					$cont++;
					// Si existe el campo dentro de los valores del registro
					if(isset($arreglo_fila[$columna->id])){
						
						if($columna->id_tipo_campo == 2){ // Si es text area
							
							$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
							$valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
						
						}elseif($columna->id_tipo_campo == 3){//si es numero.
							$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
						}elseif($columna->id_tipo_campo == 4){//si es fecha.
						
							if($arreglo_fila[$columna->id]){
								$valor_campo = get_date_format($arreglo_fila[$columna->id], $id_proyecto);
							}else{
								$valor_campo = '-';
							}
							
						}elseif($columna->id_tipo_campo == 5){// si es periodo
						
							$start_date = $arreglo_fila[$columna->id]['start_date'];
							$start_date = get_date_format($start_date, $id_proyecto);
							$end_date = $arreglo_fila[$columna->id]['end_date'];
							$end_date = get_date_format($end_date, $id_proyecto);
							$valor_campo = $start_date.' - '.$end_date;
							
						}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
							continue;
						}elseif($columna->id_tipo_campo == 10){
							if($arreglo_fila[$columna->id]){
								$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
								$valor_campo = anchor(get_uri("environmental_records/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
							}else{
								$valor_campo = '-';
							}
						}elseif($columna->id_tipo_campo == 15){
							$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
						}elseif($columna->id_tipo_campo == 14){// si es Hora
							$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]);
						}else{
							if($arreglo_fila[$columna->id] == ""){
								$valor_campo = '-';
							}else{
								$valor_campo = $arreglo_fila[$columna->id];
							}
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
			
			$fecha_created = explode(' ',$data->created); 
			$fecha_modified = explode(' ',$data->modified);
	
			if($record_info->flujo == "Residuo"){
				
				if(isset($datos["fecha_retiro"])){
					$row_data[] = get_date_format($datos["fecha_retiro"],$id_proyecto);
				}else{
					$row_data[] = "-";
				}
				
				$elemento = $this->Form_values_model->get_one($data->id);
				$datos_elemento = json_decode($elemento->datos, true);
				
				if(isset($datos["nombre_archivo_retiro"])){
					//$row_data[] = $datos["nombre_archivo_retiro"];	
					if($datos_elemento["nombre_archivo_retiro"]){
						$row_data[] = anchor(get_uri("environmental_records/download_file/".$data->id."/nombre_archivo_retiro"), "<i class='fa fa fa-cloud-download'></i>", array("title" => remove_file_prefix($datos_elemento["nombre_archivo_retiro"])));
					} else {
						$row_data[] = "-";
					}
				}else{
					$row_data[] = "-";
				}
	
				if(isset($datos["nombre_archivo_recepcion"])){
					//$row_data[] = $datos["nombre_archivo_recepcion"];	
					if($datos_elemento["nombre_archivo_recepcion"]){
						$row_data[] = anchor(get_uri("environmental_records/download_file/".$data->id."/nombre_archivo_recepcion"), "<i class='fa fa fa-cloud-download'></i>", array("title" => remove_file_prefix($datos_elemento["nombre_archivo_recepcion"])));
					} else {
						$row_data[] = "-";
					}
				}else{
					$row_data[] = "-";
				}
			}
			
			$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
			$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
			
			
		}elseif($record_info->id_tipo_formulario == 2){
			
			if($data->datos){
				$arreglo_fila = json_decode($data->datos, true);
				$cont = 0;
				
				foreach($columnas as $columna) {
					$cont++;
					
					// Si existe el campo dentro de los valores del registro
					if(isset($arreglo_fila[$columna->id])){
						
						if($columna->id_tipo_campo == 2){ // Si es text area
							
							$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
							$valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
						
						}elseif($columna->id_tipo_campo == 3){//si es numero.
							$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
						}elseif($columna->id_tipo_campo == 4){//si es fecha.
							
							if($arreglo_fila[$columna->id]){
								$valor_campo = get_date_format($arreglo_fila[$columna->id], $id_proyecto);
							}else{
								$valor_campo = '-';
							}
							
						}elseif($columna->id_tipo_campo == 5){// si es periodo
						
							$start_date = $arreglo_fila[$columna->id]['start_date'];
							$start_date = get_date_format($start_date, $id_proyecto);
							$end_date = $arreglo_fila[$columna->id]['end_date'];
							$end_date = get_date_format($end_date, $id_proyecto);
							$valor_campo = $start_date.' - '.$end_date;
							
						}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
							continue;
						}elseif(($columna->id_tipo_campo == 10)){
							if($arreglo_fila[$columna->id]){
								$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
								$valor_campo = anchor(get_uri("feeders/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
							}else{
								$valor_campo = '-';
							}
						}elseif($columna->id_tipo_campo == 15){
							$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
						}elseif($columna->id_tipo_campo == 14){
							$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]);
						}else{
							if($arreglo_fila[$columna->id] == ""){
								$valor_campo = '-';
							}else{
								$valor_campo = $arreglo_fila[$columna->id];
							}
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
			
			$fecha_created = explode(' ', $data->created); 
			$fecha_modified = explode(' ', $data->modified);
			
			$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
			$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
			
			
		}elseif($record_info->id_tipo_formulario == 3){
			
			// NO FIJO
			if(!$record_info->fijo){
				$row_data[] = $datos["fecha"] ? get_date_format($datos["fecha"], $id_proyecto) : "-";
				
				if($data->datos){
					$arreglo_fila = json_decode($data->datos, true);   
					$cont = 0;
					
					foreach($columnas as $columna) {
						$cont++;
		
						// Si existe el campo dentro de los valores del registro
						if(isset($arreglo_fila[$columna->id])){
							
							if($columna->id_tipo_campo == 2){ // Si es text area
								
								$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
								$valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
							
							}elseif($columna->id_tipo_campo == 3){//si es numero.
								$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
							}elseif($columna->id_tipo_campo == 4){//si es fecha.
								
								if($arreglo_fila[$columna->id]){
									$valor_campo = get_date_format($arreglo_fila[$columna->id], $id_proyecto);
								}else{
									$valor_campo = '-';
								}
								
							}elseif($columna->id_tipo_campo == 5){// si es periodo
							
								$start_date = $arreglo_fila[$columna->id]['start_date'];
								$start_date = get_date_format($start_date, $id_proyecto);
								$end_date = $arreglo_fila[$columna->id]['end_date'];
								$end_date = get_date_format($end_date, $id_proyecto);
								$valor_campo = $start_date.' - '.$end_date;
								
							}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
								continue;
							}elseif(($columna->id_tipo_campo == 10)){
								if($arreglo_fila[$columna->id]){
									$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
									$valor_campo = anchor(get_uri("other_records/download_file/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
								}else{
									$valor_campo = '-';
								}
							}elseif($columna->id_tipo_campo == 15){
								$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
							}elseif($columna->id_tipo_campo == 14){
								$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]);
							}else{
								if($arreglo_fila[$columna->id] == ""){
									$valor_campo = '-';
								}else{
									$valor_campo = $arreglo_fila[$columna->id];
								}
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
				
				$fecha_created = explode(' ', $data->created); 
				$fecha_modified = explode(' ', $data->modified);
				
				$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
				$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
				
			}else{// FIJO
				
				if($record_info->codigo_formulario_fijo != "or_responsables"){
					$row_data[] = $datos["fecha"] ? get_date_format($datos["fecha"], $id_proyecto) : "-";
				} 
				
				if($data->datos){
			
					$arreglo_fila = json_decode($data->datos, true);   
					$cont = 0;
					foreach($columnas as $columna) {
						$cont++;
						
						// Si existe el campo dentro de los valores del registro
						if(isset($arreglo_fila[$columna->id])){
							
							if($columna->id_tipo_campo == 2){ // Si es text area
								
								$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id].'"><i class="fas fa-info-circle fa-lg"></i></span>';
								$valor_campo = ($arreglo_fila[$columna->id]) ? $tooltip_textarea : "-";
							
							}elseif($columna->id_tipo_campo == 3){//si es numero.
								$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
							}elseif($columna->id_tipo_campo == 4){//si es fecha.
								
								if($arreglo_fila[$columna->id]){
									$valor_campo = get_date_format($arreglo_fila[$columna->id], $id_proyecto);
								}else{
									$valor_campo = '-';
								}
								
							}elseif($columna->id_tipo_campo == 5){// si es periodo
							
								$start_date = $arreglo_fila[$columna->id]['start_date'];
								$start_date = get_date_format($start_date, $id_proyecto);
								$end_date = $arreglo_fila[$columna->id]['end_date'];
								$end_date = get_date_format($end_date, $id_proyecto);
								$valor_campo = $start_date.' - '.$end_date;
								
							}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
								continue;
							}elseif(($columna->id_tipo_campo == 10)){
								if($arreglo_fila[$columna->id]){
									$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
									$valor_campo = anchor(get_uri("other_records/download_file_fixed_forms/".$data->id."/".$columna->id), "<i class='fa fa-cloud-download'></i>", array("title" => $nombre_archivo));	
								}else{
									$valor_campo = '-';
								}
							}elseif($columna->id_tipo_campo == 15){
								$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
							}elseif($columna->id_tipo_campo == 14){
								$valor_campo = convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]);
							}else{
								if($arreglo_fila[$columna->id] == ""){
									$valor_campo = '-';
								}else{
									$valor_campo = $arreglo_fila[$columna->id];
								}
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
				
				$fecha_created = explode(' ', $data->created); 
				$fecha_modified = explode(' ', $data->modified);
				
				$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
				$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
				
			}
			
		}
		
        return $row_data;
		
    }
	
	function get_flow_radio(){
		
		$label_column = "col-md-3";
        $field_column = "col-md-9";
		
		$html = '<div class="form-group">';
		
			$html.= '<label for="flow" class="'.$label_column.'">'.lang('flow').'</label>';
			$html.= '<div class="'.$field_column.'">';
				$html.= '<div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">';
				$html.= lang("consumption");
				$html.= '</div>';
				$html.= '<div class="col-md-9 col-sm-9 col-xs-9">';
				
					$datos_campo = array(
						"id" => "consumo",
						"name" => "flow",
						"value" => "Consumo",
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required")
					);
					
				$html.=  form_radio($datos_campo);
			$html.= '</div>';
			
			$html.= '<div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">';
			$html.= lang("one_waste");
				$html.= '</div>';
				$html.= '<div class="col-md-9 col-sm-9 col-xs-9">';
				
					$datos_campo = array(
						"id" => "residuo",
						"name" => "flow",
						"value" => "Residuo",
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required")
					);
				$html.=  form_radio($datos_campo);
			$html.= '</div>';
			
			$html.= '<div class="col-md-3 col-sm-3 col-xs-3" style="padding-left: 0;">';
			$html.= lang("does_not_apply");
				$html.= '</div>';
				$html.= '<div class="col-md-9 col-sm-9 col-xs-9">';
				
					$datos_campo = array(
						"id" => "no_aplica",
						"name" => "flow",
						"value" => "No Aplica",
						"data-rule-required" => true,
						"data-msg-required" => lang("field_required")
					);
				$html.=  form_radio($datos_campo);
			$html.= '</div>';
			
		$html.= '</div>';
		
		echo $html;
		
	}
	
	
	function get_unit_fields(){
		
		$array_tipos_unidades_disponibles = array("" => "-") + $this->Unity_type_model->get_dropdown_list(array("nombre"), "id");

		$html ='';
		$html.= '<div class="form-group">';
			$html.= '<label for="waste_unit" class="col-md-3">'.lang("unit_field_name") .'</label>';
			$html.= '<div class="col-md-9">';
				$html.= form_input(array(
					 "id" => "waste_unit",
					 "name" => "waste_unit",
					 "value" => "",
					 "class" => "form-control",
					 "placeholder" => lang('unit_field_name'),
					 "autofocus" => true,
					 "data-rule-required" => true,
					 "data-msg-required" => lang("field_required"),
					 "autocomplete"=> "off",
					 "maxlength" => "255"
				));
			$html.= '</div>';
		$html.= '</div>';

		$html.= '<div class="form-group multi-column">';
			$html.= '<label for="unit_field" class="col-md-3">'.lang("unit_type").'</label>';
			$html.= '<div class="col-md-4">';
				$html .= form_dropdown("unit_field", $array_tipos_unidades_disponibles, "", "id='unit_field' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html.= '</div>';

			$html.= '<div id="symbol_group">';
				$html.= '<div class="col-md-4">';
					$html.= form_dropdown("unit_symbol", array("" => "-"), "", "id='unit_symbol' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html.= '</div>';
			$html.= '</div>';
		$html.= '</div>';

		echo $html;

	}
	
	function get_type_of_treatment_definition(){
		
		$tipos_tratamiento = $this->Tipo_tratamiento_model->get_dropdown_list(array("nombre"));
		
		$html = '';
		$html.= '<div class="form-group">';
			$html.= '<label for="type_of_treatment" class="col-md-3">'.lang('default_type_of_treatment').'</label>';
			$html.= '<div class="col-md-4">';
				$html .= form_dropdown("type_of_treatment", array("" => "-") + $tipos_tratamiento, "", "id='type_of_treatment' class='select2 validate-hidden'");
			$html.= '</div>';
		//$html.= '</div>';
		
		//$html.= '<div class="form-group">';
			$html.= '<label for="disabled_field" class="col-md-3">'.lang('disabled_field');
				$html.= ' <span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_description').'"><i class="fa fa-question-circle"></i></span>';
			$html.= '</label>';
			$html.= '<div class="col-md-2">'.form_checkbox("disabled_field", "1", false, "id='disabled_field' disabled");
			$html.= '</div>';
		$html.= '</div>';
		

		echo $html;

	}
	
	function get_categorias_of_material(){
		
		$array_materiales = $this->input->post("array_materiales");
		$array_categorias = $this->input->post("array_categorias");
		$categorias_material = array();
		
		if($array_materiales){
			
			foreach($array_materiales as $id_material){
				
				//buscar las relaciones entre $id_material y sus categorias
				$material_rel_categorias = $this->Materials_rel_category_model->get_categories_related_to_material($id_material)->result();
				
				foreach($material_rel_categorias as $rel){
					$categoria = $this->Categories_model->get_one($rel->id_categoria);
					$categorias_material[$categoria->id] = $categoria->nombre;
				}
				
			}
			if(isset($array_categorias)){
				$html = '';
				$html .= '<div class="form-group">';
		
					$html .= '<label for="categorias" class="col-md-3">'.lang('categories').'</label>';
					$html .= '<div class="col-md-9">';
					$html .= form_multiselect("categorias[]", $categorias_material,$array_categorias, "id='categorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					$html .= '</div>';
				$html .= '</div>';
			}else{
				$html = '';
				$html .= '<div class="form-group">';
		
					$html .= '<label for="categorias" class="col-md-3">'.lang('categories').'</label>';
					$html .= '<div class="col-md-9">';
					$html .= form_multiselect("categorias[]", $categorias_material,"", "id='categorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					$html .= '</div>';
				$html .= '</div>';
			}			
			echo $html;
			
		} else {

			$html = '';
			$html .= '<div class="form-group">';
		
				$html .= '<label for="categorias" class="col-md-3">'.lang('categories').'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_multiselect("categorias[]", $categorias_material, "", "id='categorias' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				$html .= '</div>';
			$html .= '</div>';
			
			echo $html;

		}
		
		
	}
	
	/* Devuelve la vista previa de los campos de un formulario */
	function get_preview_form_fields($form_id){
		
		$html = "";
		
		if($form_id){
			
			$formulario = $this->Forms_model->get_one($form_id);
			$form_data = $formulario->unidad;
			$data_unidad_residuo = json_decode($form_data);
			$data_tipo_unidad = $this->Unity_type_model->get_one($data_unidad_residuo->tipo_unidad_id);
			$data_unidad = $this->Unity_model->get_one($data_unidad_residuo->unidad_id);
			$nombre_unidad_residuo = $data_unidad_residuo->nombre_unidad;
			$tipo_unidad_residuo = $data_tipo_unidad->nombre;
			$unidad_residuo = $data_unidad->nombre;
			
			if(!$formulario->fijo){
				
				if($formulario->id_tipo_formulario == 1){
					
					$html .= '<div class="form-group">';
					$html .= '<label for="date_filed" class="col-md-3">'.lang("date_filed").'</label>';
					$html .= 	'<div class="col-md-9">';
					$html .= 		form_input(array(
										"id" => "date_filed",
										"name" => "date_filed",
										//"value" => $fecha_registro,
										"class" => "form-control datepicker fecha",
										"placeholder" => lang('date_filed'),
										"data-rule-required" => true,
										"data-msg-required" => lang("field_required"),
										"autocomplete" => "off",
									));
					$html .= 	'</div>';
					$html .= '</div>';
					
					$categorias = $this->Categories_model->get_categories_of_material_of_form($form_id)->result();
					
					$array_cat = array();
					foreach($categorias as $index => $key){
						$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $key->id, 'id_cliente' => $this->login_user->client_id, "deleted" => 0));
						if($row_alias->alias){
							$nombre = $row_alias->alias;
						}else{
							$nombre = $key->nombre;
						}
						$array_cat[$key->id] = $nombre;
					}
					
					$count_cat = count($array_cat);
					
					$html .= '<div class="form-group">';
					$html .= 	'<label for="category" class="col-md-3">'.lang('category').'</label>';
					$html .= 	'<div class=" col-md-9">';
					
					if($count_cat == 1){
						$html .= form_dropdown("category", $array_cat, $array_cat, "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', disabled='disabled', data-msg-required='" . lang('field_required') . "'");
					} else {
						$html .= form_dropdown("category", array("" => "-") + $array_cat, "", "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					}
					
					$html .= 	'</div>';
					$html .= '</div>';
					
					if(($formulario->flujo == "Residuo") || ($formulario->flujo == "Consumo") || ($formulario->flujo == "No Aplica")) {
						
						$html .= '<div id="waste_unit_group">';
						$html .= 	'<div class="form-group">';
						$html .= 	'<label for="waste_unit" class="col-md-3">'.$nombre_unidad_residuo." (".$tipo_unidad_residuo.")".'</label>';
						$html .= 		'<div class="col-md-9">';
						$html .= 			'<div class="col-md-10 p0">';
						$html .= 				form_input(array(
													"id" => "waste_unit",
													"name" => "waste_unit",
													//"value" => $unidad,
													"class" => "form-control",
													"placeholder" => $nombre_unidad_residuo,
													"data-rule-required" => true,
													"data-msg-required" => lang("field_required"),
													"data-rule-number" => true,
													"data-msg-number" => lang("enter_a_integer"),
													"autocomplete" => "off",
												));
						$html .= 			'</div>';
						$html .= 			'<div class="col-md-2">';
						$html .= 				$unidad_residuo;
						$html .= 			'</div>';
						$html .= 		'</div>';
						$html .= 	'</div>';
						$html .= '</div>';
						
					}
					
				}
				
				$form_fields = $this->Field_rel_form_model->get_fields_related_to_form($form_id)->result_array(); //devuelve filas de tabla campo_rel_formulario
	
				foreach($form_fields as $form_field){	
					$field_info = $this->Fields_model->get_one($form_field['id_campo']);
					$field_type = $this->Field_types_model->get_one($field_info->id_tipo_campo);
					//Obtener vistas de los campos
					$html .= $this->get_fields_form_view($field_type, $field_info);
					//var_dump($html);
					//var_dump($field_info->id." - ".$field_info->nombre." - ".$field_type->nombre."<br/>");
				}
				
				if($formulario->flujo == "Residuo"){
						
					$tipos_tratamientos = $this->Tipo_tratamiento_model->get_all_where(array("deleted" => 0))->result_array();
					$array_tipo_tratamiento = array();
					foreach($tipos_tratamientos as $tipo_tratamiento){
						$array_tipo_tratamiento[$tipo_tratamiento["id"]] = $tipo_tratamiento["nombre"];
					}
					
					$html .= '<div id="type_of_treatment_group">';
					$html .= 	'<div class="form-group">';
					$html .= 		'<label for="type_of_treatment" class="col-md-3">'.lang('type_of_treatment').'</label>';
					$html .= 		'<div class="col-md-9">';
					$html .= 			form_dropdown("type_of_treatment",array("" => "-") + $array_tipo_tratamiento, "", "id='type_of_treatment' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					$html .= 		'</div>';
					$html .= 	'</div>';
					$html .= '</div>';
					
					$html .= '<div id="retirement_date_group">';
					$html .= 	'<div class="form-group">';
					$html .= 		'<label for="retirement_date" class="col-md-3">'.lang('retirement_date').'</label>';
					$html .=		'<div class="col-md-9">';
					$html .=			form_input(array(
											"id" => "retirement_date",
											"name" => "retirement_date",
											//"value" => $fecha_retiro,
											"class" => "form-control datepicker",
											"placeholder" => lang('retirement_date'),
											//"data-rule-required" => true,
											//"data-msg-required" => lang("field_required"),
											"autocomplete" => "off",
										));
					$html .= 		'</div>';
					$html .= 	'</div>';
					$html .= '</div>';
					
					$html .= '<div class="form-group">';
					$html .= 	'<label for="retirement_evidence" class="col-md-3">'.lang('retirement_evidence').'</label>';
					$html .= 		'<div id="dropzone_retirement_evidence" class="col-md-9">';
					$html .= 			$this->load->view("includes/retirement_evidence_uploader", array(
											"upload_url" => get_uri("fields/upload_file"),
											"validation_url" =>get_uri("fields/validate_file")
										), true);
					$html .= 		'</div>';
					$html .= '</div>';
					
					$html .= '<div class="form-group">';
					$html .= 	'<label for="reception_evidence" class="col-md-3">'.lang('reception_evidence').'</label>';
					$html .= 		'<div id="dropzone_reception_evidence" class="col-md-9">';
					$html .= 			$this->load->view("includes/reception_evidence_uploader", array(
											"upload_url" => get_uri("fields/upload_file"),
											"validation_url" =>get_uri("fields/validate_file")
										), true);
					$html .= 		'</div>';
					$html .= '</div>';
		
				}
			
			} else { // Si el formulario es fijo
				
				$form_fields = $this->Fixed_field_rel_form_rel_project_model->get_fixed_fields_related_to_form($form_id)->result_array();
				$html = "";
				foreach($form_fields as $form_field){	
					$field_info = $this->Fixed_fields_model->get_one($form_field['id_campo_fijo']);
					$field_type = $this->Field_types_model->get_one($field_info->id_tipo_campo);
					$html .= $this->get_fields_form_view($field_type, $field_info);
				}

			}
			
		}
		
		return $html;
	}
	
	/* Obtener html de campos del formulario para vista previa */
	function get_fields_form_view($field_type, $field_info){
		
		$html = "";

		if($field_type->nombre == "Input text"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';				
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control",
					//"autofocus" => true,
					"autocomplete" => "off",
					$disabled => ""
				));				
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Texto Largo"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';												
				$html .= form_textarea(array(
					"id" => "default_value_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control",
					"style" => "height:150px;",
					"autocomplete" => "off",
					"maxlength" => 1000,
					$disabled => ""
				));
			    $html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Número"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';					
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control",
					//"autofocus" => true,
					"autocomplete" => "off",
					$disabled => ""
				));
				$html .= '</div>';
			$html .= '</div>';
					
		}
		
		if($field_type->nombre == "Fecha"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_input(array(
					"id" => "default_date_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control fecha",
					"autocomplete" => "off",
					$disabled => ""
				));
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Periodo"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;		
			$date_default = "";
			if($field_info->default_value){
				$date_default = json_decode($field_info->default_value);
			}
			
			$date_name = "";
			if($field_info->html_name){
				$date_name = json_decode($field_info->html_name);
			}
			
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
					$html .= '<div class="col-md-5">';
					$html .= form_input(array(
						"id" => "default_date_field1",
						"name" => $date_name->start_name,
						"value" => $date_default->start_date,
						"placeholder" => "YYYY-MM-DD",
						"class" => "form-control fecha",
						"autocomplete" => "off",
						$disabled => ""						
					));
					$html .= '</div>';
					
					$html .= '<div class="col-md-5">';
					$html .= form_input(array(
						"id" => "default_date_field2",
						"name" => $date_name->end_name,
						"value" => $date_default->end_date,
						"placeholder" => "YYYY-MM-DD",
						"class" => "form-control fecha",
						"data-rule-greaterThanOrEqual" => "#default_date_field1",
						"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
						"autocomplete" => "off",
						$disabled => ""	
					));
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($field_type->nombre == "Selección"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;				
			$array_opciones = array();
			if($field_info->opciones){
				$opciones = json_decode($field_info->opciones);
				foreach($opciones as $index => $opcion){
					if($index == 0){
						$array_opciones[''] = $opcion->text;
					}else{
						$array_opciones[$opcion->value] = $opcion->value;
					}
				}
			}
			
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';	
				$html .= form_dropdown($field_info->html_name, $array_opciones, $field_info->default_value, "id='default_value_field' class='select2' $disabled");
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Selección Múltiple"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$array_opciones = array();
			if($field_info->opciones){
				$opciones = json_decode($field_info->opciones);
				foreach($opciones as $index => $opcion){
					if($index == 0){
						$array_opciones[''] = $opcion->text;
					}else{
						$array_opciones[$opcion->value] = $opcion->value;
					}
				}
			}
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_multiselect($field_info->html_name, $array_opciones, json_decode($field_info->default_value), "id='default_value_field' class='select2' $disabled");
				$html .= '</div>';
			$html .= '</div>';
				
		}
		
		if($field_type->nombre == "Rut"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$html .= '<div class="form-group">';
				$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control rut",
					"autofocus" => true,
					"data-rule-minlength" => 6,
					"data-msg-minlength" => lang("enter_minimum_6_characters"),
					"data-rule-maxlength" => 13,
					"data-msg-maxlength" => lang("enter_maximum_13_characters"),
					"autocomplete" => "off",
					$disabled => ""
				));
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Radio Buttons"){
					
			$array_opciones = array();
			if($field_info->opciones){
				
				$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
				$html .= '<div class="form-group">';
					$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
						$html .= '<div class="col-md-9">';
						$opciones = json_decode($field_info->opciones);
						
						$cont = 0;
						foreach($opciones as $value => $label){
							$cont++;
							
							$html .= '<div class="col-md-6">';
							$html .= $label->text;
							$html .= '</div>';
							
							$html .= '<div class="col-md-6">';
							$datos_campo = array(
								"id" => $field_info->html_name.'_'.$cont,
								"name" => $field_info->html_name,
								"value" => $value,
								"class" => "toggle_specific",
								//$disabled => "",
							);
							if($value == $default_value){
								$datos_campo["checked"] = true;
							}
							if($obligatorio){
								$datos_campo['data-rule-required'] = true;
								$datos_campo['data-msg-required'] = lang("field_required");
							}
							if($habilitado){
								$datos_campo['disabled'] = true;
							}
							$html .= form_radio($datos_campo);
							$html .= '</div>';
							
						}
				
				$html .= 	'</div>';
				$html .= '</div>';
				
			}
		}
		
		if($field_type->nombre == "Archivo"){
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
					$html .= $this->load->view("includes/form_file_uploader", array(
						"upload_url" => get_uri("fields/upload_file"),
						"validation_url" => get_uri("fields/validate_project_file"),
						"id_campo" => $field_info->id,
					),
					true);
				$html .= '</div>';
			$html .= '</div>';
		}
		
		if($field_type->nombre == "Texto Fijo"){
			
			/*
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-2">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-10">';
					$html .= $field_info->default_value;
				$html .= '</div>';
			$html .= '</div>';	
			*/
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
					$html .= $field_info->default_value;
				$html .= '</div>';
			$html .= '</div>';	
			
		}
		
		if($field_type->nombre == "Divisor"){
			
			/*
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-2">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-10">';
					$html .= $field_info->default_value;
				$html .= '</div>';
			$html .= '</div>';
			*/
			
			$html .= '<div class="form-group">';
				$html .= '<div class="col-md-12">';
					$html .= $field_info->default_value;
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Correo"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
				$html .= form_input(array(
					"id" => "default_value_field",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"class" => "form-control",
					"placeholder" => $field_info->nombre,
					"autofocus" => true,
					"autocomplete" => "off",
					"data-rule-email" => true,
					"data-msg-email" => lang("enter_valid_email"),
					//"data-rule-required" => true,
					//"data-msg-required" => lang("field_required"),
					"autocomplete" => "off",
					$disabled => ""
				));
				$html .= '</div>';
			$html .= '</div>';
			
		}
		
		if($field_type->nombre == "Hora"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';			
				$html .= '<div class="col-md-9">';
				$html .= form_input(array(
					"id" => "time_preview",
					"name" => $field_info->html_name,
					"value" => $field_info->default_value,
					"placeholder" => $field_info->nombre,
					"class" => "form-control time_preview",
					//"autofocus" => true,
					"autocomplete" => "off",
					$disabled => ""
				));
				$html .= '</div>';
			$html .= '</div>';	
						
		}
		
		if($field_type->nombre == "Unidad"){
					
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;
			$seleccionada = "";
			//
			$symbol_seleccionado = "";
			//
			if($field_info->opciones){
				$opciones = json_decode($field_info->opciones);
				$seleccionada = $opciones[0]->id_tipo_unidad;
				//
				$symbol_seleccionado = $opciones[0]->id_unidad;
				//
			}
			
			$unidad = $this->Unity_model->get_one_where(array("id_tipo_unidad" => $seleccionada));
			
			$html .= '<div class="form-group">';
			$html .= '<label for="tipo_formulario" class="col-md-3">'.$field_info->nombre.'</label>';			
				$html .= '<div class="col-md-9">';
					$html .= '<div class="col-md-10">';
					$html .= form_input(array(
						"id" => "default_value",
						"name" => $field_info->html_name,
						"value" => $field_info->default_value,
						"class" => "form-control",
						"placeholder" => $field_info->nombre,
						//"autofocus" => true,
						"autocomplete" => "off",
						$disabled => ""
					));
					$html .= '</div>';					
				/*
				$arrayUnidades = "";
				if($seleccionada == "Masa"){
					$arrayUnidades = array("g" => "g", "Kg" =>"Kg", "Ton" => "Ton");
				}
				if($seleccionada == "Volumen"){
					$arrayUnidades = array("cc - ml" => "cc - ml", "l" => "l", "m3" => "m3");
				}
				if($seleccionada == "Longitud"){
					$arrayUnidades = array("m" => "m", "km" => "km");
				}
				if($seleccionada == "Superficie"){
					$arrayUnidades = array("m2" => "m2", "Km2" => "Km2", "Ha" => "Ha");
				}
				if($seleccionada == "Potencia"){
					$arrayUnidades = array("kW" => "kW", "MW" => "MW");
				}
				if($seleccionada == "Energía"){
					$arrayUnidades = array("kWh" => "kWh", "MWh" => "MWh", "J" => "J");
				}
				$nombres= implode(', ', $arrayUnidades);
				*/	
				// DROPDOWN UNIDAD
	
					//$html .= '<label for="unit" class="col-md-2">'.lang('unit').'</label>';
					$html .= '<div class="col-md-2">';
					//$html .= $nombres;
					$html .=  $unidad->nombre;
					//$html .= form_dropdown("unit_field", array("" => "-") + $arrayUnidades, "", "id='' class='select2' $disabled");
					$html .= '</div>';
								
/* 				// FILA POR DEFECTO (volumen, masa, etc)
				
					$html .= '<label for="unit_field" class="col-md-2">'.lang('unit_type').'</label>';
					$html .= '<div class="col-md-2">';
					$html .= '<label for="unit_field" class="col-md-2 unit_type" id="">'.$seleccionada.'</label>';
					$html .= '</div>'; */
				$html .= '</div>';
			$html .= '</div>';

		}
		
		if($field_type->nombre == "Selección desde Mantenedora"){
			
			$disabled = ($field_info->habilitado == 1) ? "disabled" : null;	
			
			$valores = json_decode($field_info->default_value);
			$id_mantenedora = $valores->mantenedora;
			$id_campo_label = $valores->field_label;
			$id_campo_value = $valores->field_value;

			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			$array_opciones = array();
			foreach($datos as $index => $row){
				$fila = json_decode($row->datos, true);
				$label = $fila[$id_campo_label];
				$value = $fila[$id_campo_value];
				$array_opciones[$value] = $label;
			}
			
			$html .= '<div class="form-group">';
			$html .= '<label for="unit" class="col-md-3">'.$field_info->nombre.'</label>';
				$html .= '<div class="col-md-9">';
					$html .= form_dropdown($field_info->html_name, array("" => "-") + $array_opciones, "", "id='default_value_field' class='select2' $disabled");
				$html .= '</div>';
			$html .= '</div>';
		}
		
		return $html;

	}
	
	/* construye el campo y retorna el html */
	function get_preview_form($form_id){
		$html = "";
		
		if($form_id){
            $options = array("id" => $form_id);
            $form_info = $this->Forms_model->get_details($options)->row();
            
		}
		return $html;
		
	}
	
	/* retorna los materiales registrados en BD FC */
	function get_materials_fc(){
		$materiales = $this->Materials_model->get_dropdown_list(array("nombre"), "id");
		$html .= '<div class="form-group">';
			$html .= '<label for="default_value_field" class="col-md-3">'.lang('materials').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_multiselect("materiales[]", $materiales, "", "id='materiales' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		echo $html;
	}
	
	/* retorna los materiales relacionados a un proyecto */
	function get_materials_of_project(){
				
		$materiales_de_proyecto = $this->Materials_model->get_materials_of_project($this->input->post('project'))->result();
   
		$arraySelected = array();
		$arraySelected2 = array();
		$arrayMaterialesProyecto = array();
		
		foreach($materiales_de_proyecto as $innerArray){
			$arraySelected[] = $innerArray->id;
			$arraySelected2[(string)$innerArray->id] = $innerArray->nombre;
		}
		
		foreach($materiales_disponibles as $innerArray){
			if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
				$arrayMaterialesProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
			}
			
		}

		$array_final = $arraySelected2 + $arrayMaterialesProyecto;
			  
		$html = '';
		$html .= '<div class="form-group">';
			$html .= '<label for="materiales" class="col-md-3">'.lang('materials').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_multiselect("materiales[]", $array_final, "", "id='materiales' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;

	}
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	}

    /* add-remove start mark from client */

    function add_remove_star($client_id, $type = "add") {
        if ($client_id) {
            $view_data["client_id"] = $client_id;

            if ($type === "add") {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "add");
                $this->load->view('clients/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                $this->load->view('clients/star/not_starred', $view_data);
            }
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id)->result();
        $this->load->view('clients/star/clients_list', $view_data);
    }

    /* load projects tab  */
/* 
    function projects($client_id) {
        $this->access_only_allowed_members();

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = $client_id;
        $this->load->view("clients/projects/index", $view_data);
    }
*/

    /* load payments tab  */

    function payments($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/payments/index", $view_data);
        }
    }

    /* load tickets tab  */

    function tickets($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tickets", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/tickets/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/invoices/index", $view_data);
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id) {

        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->result();
        $view_data['client_id'] = $client_id;
        $this->load->view("clients/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->input->post('id'));
        $client_id = $this->input->post('client_id') ? $this->input->post('client_id') : $view_data['model_info']->client_id;

        $this->access_only_allowed_members();

        $view_data['client_id'] = $client_id;
        $this->load->view('clients/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        validate_submitted_data(array(
            "id" => "numeric",
            "field_id" => "required|numeric"
        ));

        $field_id = $this->input->post('field_id');
        $this->access_only_allowed_members();


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $field_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "client_id" => $field_id,
                        "file_name" => $new_file_name,
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("clients/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("clients/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

        $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->client_id) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = false;

            $view_data["file_url"] = get_file_uri(get_general_file_path("client", $file_info->client_id) . $file_info->file_name);;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            $this->load->view("clients/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }
        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->file_name)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        if ($this->General_files_model->delete($id)) {

            delete_file_from_directory(get_general_file_path("client", $info->client_id) . $info->file_name);

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['client_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = $tab;
        if ($view_data['user_info']->user_type === "client") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            $this->template->rander("clients/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->load->view("users/account_settings", $view_data);
    }

    /* load contacts tab  */

    function contacts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->input->post('client_id');

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();
        $this->load->view('clients/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members_or_client_contact($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $this->load->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->input->post('contact_id');
        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $user_data = array(
            "first_name" => $this->input->post('first_name'),
            "last_name" => $this->input->post('last_name'),
            "phone" => $this->input->post('phone'),
            "skype" => $this->input->post('skype'),
            "job_title" => $this->input->post('job_title'),
            "gender" => $this->input->post('gender'),
            "note" => $this->input->post('note')
        );

        validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric"
        ));


        if (!$contact_id) {
            //inserting new contact. client_id is required

            validate_submitted_data(array(
                "email" => "required|valid_email",
                "login_password" => "required",
            ));

            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["email"] = trim($this->input->post('email'));
            $user_data["password"] = md5($this->input->post('login_password'));
            $user_data["created_at"] = get_current_utc_time();

            //validate duplicate email address
            if ($this->Users_model->is_email_exists($user_data["email"])) {
                echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
                exit();
            }
        }

        //by default, the first contact of a client is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->input->post('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }


        $save_id = $this->Users_model->save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->save($user_data, $primary_contact);
            }

            //send login details to user only for first time. when creating  a new contact
            if (!$contact_id && $this->input->post('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info");

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->input->post('login_password');
                $parser_data["DASHBOARD_URL"] = base_url();

                $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
                send_app_mail($this->input->post('email'), $email_template->subject, $message);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->input->post('facebook'),
            "twitter" => $this->input->post('twitter'),
            "linkedin" => $this->input->post('linkedin'),
            "googleplus" => $this->input->post('googleplus'),
            "digg" => $this->input->post('digg'),
            "youtube" => $this->input->post('youtube'),
            "pinterest" => $this->input->post('pinterest'),
            "instagram" => $this->input->post('instagram'),
            "github" => $this->input->post('github'),
            "tumblr" => $this->input->post('tumblr'),
            "vine" => $this->input->post('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $this->Social_links_model->save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => lang('record_updated')));
    }

    //save account settings of a client contact (user)
    function save_account_settings($user_id) {
        $this->access_only_allowed_members_or_contact_personally($user_id);

        validate_submitted_data(array(
            "email" => "required|valid_email"
        ));

        if ($this->Users_model->is_email_exists($this->input->post('email'), $user_id)) {
            echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $this->input->post('email')
        );

        //don't reset password if user doesn't entered any password
        if ($this->input->post('password')) {
            $account_data['password'] = md5($this->input->post('password'));
        }

        //only admin can disable other users login permission
        if ($this->login_user->is_admin) {
            $account_data['disable_login'] = $this->input->post('disable_login');
        }


        if ($this->Users_model->save($account_data, $user_id)) {
            echo json_encode(array("success" => true, 'message' => lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($user_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->input->post("profile_image"));

        if ($profile_image) {
            $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image);
            $image_data = array("image" => $profile_image);
            $this->Users_model->save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                $profile_image = move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name);
                $image_data = array("image" => $profile_image);
                $this->Users_model->save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->input->post('id');

        if ($this->input->post('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        $this->access_only_allowed_members_or_client_contact($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "client", "client_id" => $client_id, "custom_fields" => $custom_fields);
        $list_data = $this->Users_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->row();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='label-info label'>" . lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("clients/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "client") {
            $contact_link = $full_name; //don't show clickable link to client
        }


        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("clients/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* open invitation modal */

    function invitation_modal() {


        validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members_or_client_contact($client_id);

        $view_data["client_info"] = $this->Clients_model->get_one($client_id);
        $this->load->view('clients/contacts/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation() {

        $client_id = $this->input->post('client_id');
        $email = trim($this->input->post('email'));

        validate_submitted_data(array(
            "client_id" => "required|numeric",
            "email" => "required|valid_email|trim"
        ));

        $this->access_only_allowed_members_or_client_contact($client_id);

        $email_template = $this->Email_templates_model->get_final_template("client_contact_invitation");

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();

        //make the invitation url with 24hrs validity
        $key = encode_id($this->encrypt->encode('client|' . $email . '|' . (time() + (24 * 60 * 60)) . '|' . $client_id), "signup");
        $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $key);

        //send invitation email
        $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
        if (send_app_mail($email, $email_template->subject, $message)) {
            echo json_encode(array('success' => true, 'message' => lang("invitation_sent")));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
        }
    }

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            $this->template->rander("clients/contacts/users", $view_data);
        }
    }
	
	
	function get_unit_symbol(){
		
		$unit_type_id = $this->input->post('unit_type_id');
		$data_unit_symbol = $this->Unity_model->get_units_of_unit_type($unit_type_id)->result();
		$array_unit_symbol = array();
		foreach($data_unit_symbol as $data){
			$array_unit_symbol[$data->id] = $data->nombre;
		}
		$result = json_encode($array_unit_symbol);
		echo $result;
	}
	
	function delete_cascade_form($form_id){
	
		//ELIMINA FORMULARIOS, REALCION_PROYECTO_FORMULARIO Y VALORES FORMULARIOS
		$form_rel_project = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $form_id, "deleted" => 0));
		if($form_rel_project->id){
	
			$forms_values = $this->Form_values_model->get_all_where(array("id_formulario_rel_proyecto" => $form_rel_project->id, "deleted" => 0))->result();
			if($forms_values){
				foreach($forms_values as $fv){
					$this->Form_values_model->delete($fv->id);
				}
			}
			
			$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result();
			if($campos_rel_formulario){
				foreach($campos_rel_formulario as $crf){
					$this->Field_rel_form_model->delete($crf->id);
				}
			}
			
			$form_rel_materiales = $this->Form_rel_material_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result();
			if($form_rel_materiales){
				foreach($form_rel_materiales as $form_rel_material){
					$this->Form_rel_material_model->delete($form_rel_material->id);
				}
			}
			
			$form_rel_materiales_rel_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $form_id, "deleted" => 0))->result();
			if($form_rel_materiales_rel_categorias){
				foreach($form_rel_materiales_rel_categorias as $form_rel_material_rel_categoria){
					$this->Form_rel_materiales_rel_categorias_model->delete($form_rel_material_rel_categoria->id);
				}
			}
			
			//$this->Forms_model->delete($form_id);
			$this->Form_rel_project_model->delete($form_rel_project->id);

			
		}
	
	}
	
	function get_consumption_fields(){
		
		$array_tipos_origen = array("" => "-");
		$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
		foreach($tipos_origen as $tipo_origen){
			$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
		}

		$html = '';
		$html.= '<div class="form-group">';
			$html.= '<label for="type_of_origin" class="col-md-3">'.lang('type_of_origin').'</label>';
			$html.= '<div class="col-md-4">';
				$html .= form_dropdown("type_of_origin", $array_tipos_origen, "", "id='type_of_origin' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
			$html.= '</div>';
		//$html.= '</div>';
		
		//$html.= '<div class="form-group">';
			$html.= '<label for="disabled_field" class="col-md-3">'.lang('disabled_field');
				$html.= ' <span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_description').'"><i class="fa fa-question-circle"></i></span>';
			$html.= '</label>';
			$html.= '<div class="col-md-2">'.form_checkbox("disabled_field", "1", false, "id='disabled_field' disabled");                      
			$html.= '</div>';
		$html.= '</div>';
		
		echo $html;
		
	}
	
	function get_consumption_fields_type_of_origin(){
		
		$id_tipo_origen = $this->input->post("id_type_of_origin");
		$html = '';
		
		if($id_tipo_origen == 1){ // matter
			
			$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
				"id_tipo_origen" => $id_tipo_origen,
				"deleted" => 0
			))->result();
			
			$array_materias_por_defecto = array("" => "-");
			foreach($tipos_origen_materia as $tipo_origen_materia){
				$array_materias_por_defecto[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
			}
			
			$html.= '<div class="form-group">';
			$html.= '<label for="type_of_origin" class="col-md-3">'.lang('default_matter').'</label>';
			$html.= '<div class="col-md-4">';
			$html.= form_dropdown("default_matter", array("" => "-") + $array_materias_por_defecto, "", "id='default_matter' class='select2 validate-hidden'");
			$html.= '</div>';
			
		}
		
		echo $html;
		
	}
	
	function get_no_apply_fields(){
				
		$array_default_type = array("" => "-");
		$tipos_por_defecto = $this->EC_Types_no_apply_model->get_all()->result();
		foreach($tipos_por_defecto as $tipo_por_defecto){
			$array_default_type[$tipo_por_defecto->id] = lang($tipo_por_defecto->nombre);
		}
		
		$html.= '<div class="form-group">';
		$html.= '<label for="default_type" class="col-md-3">'.lang('default_type').'</label>';
		$html.= '<div class="col-md-4">';
		$html.= form_dropdown("default_type", array("" => "-") + $array_default_type, "", "id='default_type' class='select2 validate-hidden'");
		$html.= '</div>';
		
		$html.= '<label for="disabled_field" class="col-md-3">'.lang('disabled_field');
		$html.= ' <span class="help" data-container="body" data-toggle="tooltip" title="'.lang('disabled_field_description').'"><i class="fa fa-question-circle"></i></span>';
		$html.= '</label>';
		$html.= '<div class="col-md-2">'.form_checkbox("disabled_field", "1", false, "id='disabled_field' disabled");                      
		$html.= '</div>';
		$html.= '</div>';
		
		echo $html;
	}
	
}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */