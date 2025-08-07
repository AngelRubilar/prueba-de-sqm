<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_bulk_load extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
		$this->load->helper('email');

        //check permission to access this module
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 11;
		$this->id_submodulo_cliente = 21;
		
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
		$id_cliente = $this->login_user->client_id;
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		//$tipos_de_formularios = $this->Form_types_model->get_details()->result();
		$tipos_de_formularios = array("" => "-") + $this->Form_types_model->get_dropdown_list(array("nombre"), "id");
		
		$view_data["tipos_de_formularios"] = $tipos_de_formularios;
		$view_data["project_info"] = $proyecto;
		
		//Configuración perfil de usuario
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_editar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		
        $this->template->rander("setting_bulk_load/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $client_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Clients_model->get_one($client_id);
        //$view_data["currency_dropdown"] = $this->get_currency_dropdown_select2_data();

        //get custom fields
        //$view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $this->load->view('clients/modal_form', $view_data);
    }

    function get_forms_of_form_type() {
        $id_form_type = $this->input->post('id_form_type');
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        
		//get_dropdown_list(array("nombre"), "id");a
        $forms = $this->Forms_model->get_forms_of_project(array("id_proyecto" => $id_proyecto, "id_tipo_formulario" => $id_form_type))->result();
        $formularios = array();
        $formularios[] = array("id" => "", "text" => "-");
        
		// Formularios fijos de proyecto
		$formularios_fijos_proyecto = $this->Fixed_field_rel_form_rel_project_model->get_fixed_forms_related_to_project(array(
			"id_proyecto" => $id_proyecto,
			"id_tipo_formulario" => $id_form_type
		))->result();
		
		
        if($id_proyecto){
            foreach($forms as $form){
                $formularios[] = array("id" => $form->id, "text" => $form->nombre);
            }
			
			foreach($formularios_fijos_proyecto as $form){
				$formularios[] = array("id" => $form->id, "text" => $form->nombre);
			}
        }
        
        echo json_encode($formularios);
		
    }
	
	function get_excel_template_of_form() {
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
        $id_formulario = $this->input->post('id_form');
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_proyecto = $this->Projects_model->get_one($id_proyecto);
		$info_formulario = $this->Forms_model->get_one($id_formulario);
				
		if(!$info_cliente->id && !$info_proyecto->id && !$info_formulario->id) {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
			exit();
		}

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle($info_formulario->nombre)
							 ->setSubject($info_formulario->nombre)
							 ->setDescription($info_formulario->nombre)
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$doc->setActiveSheetIndex(0);
		
		// CREAR HOJA PARA OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN
		$doc->createSheet();
		// APUNTAR A NUEVA HOJA
		$doc->setActiveSheetIndex(1);
		// CAMBIARLE NOMBRE A HOJA
		$doc->getActiveSheet()->setTitle('options');
		
		// VOLVER A APUNTAR A PRIMERA HOJA
		$doc->setActiveSheetIndex(0);
		
		if(!$info_formulario->fijo){
			$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
		} else {
			$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $info_formulario->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
		}
		
		
		$num_columnas = 0;
		
		// REGISTRO AMBIENTAL
		if($info_formulario->id_tipo_formulario == 1){

			// FILA NOMBRE COLUMNAS | -----------------------------------------------------

			$doc->getActiveSheet()->setCellValue('A1', lang('date_filed'));
			$doc->getActiveSheet()->setCellValue('B1', lang('category'));

			$formulario_unidad = json_decode($info_formulario->unidad, true);
			$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
			$campo_unidad = $formulario_unidad["nombre_unidad"];
			$nombre_unidad = $campo_unidad . " (" . $unidad->nombre. ")";
			$doc->getActiveSheet()->setCellValue('C1', $nombre_unidad);

			if($info_formulario->flujo == "Consumo"){
				$doc->getActiveSheet()->setCellValue('D1', lang('type'));
				
				$data_tipo_origen = json_decode($info_formulario->tipo_origen);
				$id_tipo_origen = $data_tipo_origen->type_of_origin;
				$disabled_field_tipo_origen = (boolean)$data_tipo_origen->disabled_field;
				$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
				
				if($id_tipo_origen == "1"){ // id 1: matter
					
					$array_tipos_origen = array("" => "-");
					$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
						"id_tipo_origen" => $id_tipo_origen,
						"deleted" => 0
					))->result();
					
					foreach($tipos_origen_materia as $tipo_origen_materia){
						$array_tipos_origen[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
					}
				}
				
				if($id_tipo_origen == "2"){ // id 2: energy
					
					$default_matter = 2;
					$array_tipos_origen = array("" => "-");
					$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
					foreach($tipos_origen as $tipo_origen){
						$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
					}
				}
				
				$columna = 4;
				
			}elseif($info_formulario->flujo == "Residuo"){
				
				$doc->getActiveSheet()->setCellValue('D1', lang('type_of_treatment'));
				$array_tipo_tratamiento = array();
				$array_tipo_tratamiento["1"] = "Disposición";
				$array_tipo_tratamiento["2"] = "Reutilización";
				$array_tipo_tratamiento["3"] = "Reciclaje";

				$doc->getActiveSheet()->setCellValue('E1', lang('retirement_date'));

				$columna = 5;
			
			}elseif($info_formulario->flujo == "No Aplica"){
				
				$doc->getActiveSheet()->setCellValue('D1', lang('type'));
				
				$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
				$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
				$disabled_field_no_aplica = (boolean)$data_tipo_no_aplica->disabled_field;
				
				$array_tipos_no_aplica = array("" => "-");
				$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
				
				foreach($tipos_no_aplica as $tipo_no_aplica){
					$array_tipos_no_aplica[$tipo_no_aplica->id] = lang($tipo_no_aplica->nombre);
				}
				
				$columna = 4;
			}else{
				
			}
			
			// CAMPOS DINAMICOS
			foreach($campos_formulario as $campo){
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 15){ // UNIDAD
					$column_options = json_decode($campo->opciones, true);
					$id_unidad = $column_options[0]["id_unidad"];
					$unidad = $this->Unity_model->get_one($id_unidad);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre.' ('.$unidad->nombre.')');
				} else {
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre);
				}
				
				if($campo->default_value && $campo->id_tipo_campo != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
					
					if($campo->id_tipo_campo == 5){	
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					} else {
						$valor_por_defecto = $campo->default_value;
					}
					
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
					
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
					}
					if($campo->id_tipo_campo == 2){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
					}
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					}
					
				} else if(!$campo->default_value && $campo->id_tipo_campo != 16){
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
							$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
							$comentario->getFont()->setBold(true);
						
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						
						}else{
	
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
						}
						
					}
					
					

				}

				$columna++;
			}
			
			
			// FILA DEMO | -----------------------------------------------------
			
			// COLUMNA FECHA
			$doc->getActiveSheet()->setCellValue('A2', lang('excel_test_date'));
			// PARA DEJAR FECHA COMO TEXTO
			$doc->getActiveSheet()->getStyle('A2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			// COLUMNA CATEGORIA
			$cats = $this->Categories_model->get_categories_of_material_of_form($id_formulario)->result();
			$categorias = array();
			foreach($cats as $cat){
				$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $cat->id, 'id_cliente' => $id_cliente, "deleted" => 0));
				if($row_alias->alias){
					$categorias[] = $row_alias->alias;
				}else{
					$categorias[] = $cat->nombre;
				}
			}
			
			// GUARDO OPCIONES DE SELECT CATEGORIAS EN HOJA OPCIONES (ETIQUETAS)
			$doc->setActiveSheetIndex(1);
			$fila_opcion = 1;
			foreach($categorias as $categoria){
				$doc->getActiveSheet()->setCellValue('A'.$fila_opcion, $categoria);
				$fila_opcion++;
			}
			
			$doc->setActiveSheetIndex(0);
			
			$objValidation = $doc->getActiveSheet()->getCell('B2')->getDataValidation();     
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
			$objValidation->setAllowBlank(false);
			$objValidation->setShowInputMessage(true);
			$objValidation->setShowErrorMessage(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setErrorTitle(lang('excel_error_title'));
			$objValidation->setError(lang('excel_error_text'));
			
			$cantidad_categorias = count($categorias);
			$objValidation->setFormula1('options!$A$1:$A$'.$cantidad_categorias);
			$doc->getActiveSheet()->setCellValue('B2', $categorias[0]);

			//COLUMNA UNIDAD
			$doc->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$doc->getActiveSheet()->setCellValueExplicit('C2', '30.45', PHPExcel_Cell_DataType::TYPE_STRING);
			
			if($info_formulario->flujo == "Consumo"){
				
				// GUARDO OPCIONES DE SELECT TIPO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				// CONSULTO DEFINICION DE MATERIA O ENERGIA
				//$disabled_field = (boolean)$data_tipo_origen->disabled_field;
				//$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
				
				if($id_tipo_origen == 1){// MATERIA
				
					if($disabled_field_tipo_origen){
						$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $array_tipos_origen[$default_matter]);
					}else{
						foreach($array_tipos_origen as $tipo_origen){
							$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $tipo_origen);
							$fila_opcion++;
						}
					}
				}elseif($id_tipo_origen == 2){// ENERGIA
					
					$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $array_tipos_origen[$id_tipo_origen]);
					
				}else{
					
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO
				$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				if($disabled_field_tipo_origen){
					$cantidad_tipo_origen = 1;
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_origen);
					$doc->getActiveSheet()->setCellValue('D2', $array_tipos_origen[$default_matter]);
				}else{
					$cantidad_tipo_origen = count($array_tipos_origen);
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_origen);
					
					if(!$default_matter){
						$doc->getActiveSheet()->setCellValue('D2', $array_tipos_origen[""]);
					}else{
						$doc->getActiveSheet()->setCellValue('D2', $array_tipos_origen[$default_matter]);
					}
					
				}
				

				$columna = 4;

			}elseif($info_formulario->flujo == "Residuo"){
				
				// GUARDO OPCIONES DE SELECT TIPO TRATAMIENTO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				// CONSULTO LA DEFINICION DEL TIPO DE TRATAMIENTO PARA VER SI ESTÁ DESHABILITADO
				$data_tipo_tratamiento = json_decode($info_formulario->tipo_tratamiento);
				$id_tipo_tratamiento_defecto = $data_tipo_tratamiento->tipo_tratamiento;
				$disabled_field = (boolean)$data_tipo_tratamiento->disabled_field;
				if($disabled_field){
					$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
				}else{
					foreach($array_tipo_tratamiento as $tipo_tratamiento){
						$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $tipo_tratamiento);
						$fila_opcion++;
					}
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO TRATAMIENTO
				$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				//$objValidation->setFormula1('"'.implode(",", $array_tipo_tratamiento).'"');
				//$doc->getActiveSheet()->setCellValue('C2', $array_tipo_tratamiento[1]);
				if($disabled_field){
					$cantidad_tipo_tratamiento = 1;
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_tratamiento);
					$doc->getActiveSheet()->setCellValue('D2', $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
				}else{
					
					$cantidad_tipo_tratamiento = count($array_tipo_tratamiento);
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_tratamiento);
					
					if($id_tipo_tratamiento_defecto == ""){
						$doc->getActiveSheet()->setCellValue('D2', $array_tipo_tratamiento[1]);
					}else{
						$doc->getActiveSheet()->setCellValue('D2', $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
					}
					
				}
				
				//COLUMNA FECHA DE RETIRO
				$doc->getActiveSheet()->setCellValue('E2', lang('excel_test_date'));
				// PARA DEJAR FECHA COMO TEXTO
				$doc->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

				$columna = 5;
			}elseif($info_formulario->flujo == "No Aplica"){
				
				// GUARDO OPCIONES DE SELECT TIPO EN HOJA OPCIONES
				$doc->setActiveSheetIndex(1);
				$fila_opcion = 1;
				
				if($disabled_field_no_aplica){
					$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $array_tipos_no_aplica[$default_type]);
				}else{
					foreach($array_tipos_no_aplica as $tipo_no_aplica){
						$doc->getActiveSheet()->setCellValue('B'.$fila_opcion, $tipo_no_aplica);
						$fila_opcion++;
					}
				}
				
				$doc->setActiveSheetIndex(0);
				
				// COLUMNA TIPO
				$objValidation = $doc->getActiveSheet()->getCell('D2')->getDataValidation();     
				$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
				$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle(lang('excel_error_title'));
				$objValidation->setError(lang('excel_error_text'));
				
				if($disabled_field_no_aplica){
					$cantidad_tipo_no_aplica = 1;
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_no_aplica);
					$doc->getActiveSheet()->setCellValue('D2', $array_tipos_no_aplica[$default_type]);
				}else{
					$cantidad_tipo_no_aplica = count($array_tipos_no_aplica);
					$objValidation->setFormula1('options!$B$1:$B$'.$cantidad_tipo_no_aplica);
					
					if(!$default_type){
						$doc->getActiveSheet()->setCellValue('D2', $array_tipos_no_aplica[""]);
					}else{
						$doc->getActiveSheet()->setCellValue('D2', $array_tipos_no_aplica[$default_type]);
					}
					
				}
				
				$columna = 4;
			}else{
				
			}
			
			$columna_opciones = 2;
			
			foreach($campos_formulario as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 1){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_text'));
				}
				if($campo->id_tipo_campo == 2){	
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_textarea'));
				}
				if($campo->id_tipo_campo == 3){
					//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$numero_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_number');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $numero_ejemplo);
				}
				if($campo->id_tipo_campo == 4){
					$doc->getActiveSheet()->setCellValue(
						$this->getNameFromNumber($columna).'2', 
						($campo->default_value) ? $campo->default_value : lang('excel_test_date')
					);
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				}
				if($campo->id_tipo_campo == 5){
					
					if($campo->default_value){
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					}
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($valor_por_defecto) ? $valor_por_defecto : lang('excel_test_period'));
				
				}
				if($campo->id_tipo_campo == 6){
					
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_select'));
					$datos_campo = json_decode($campo->opciones);

					$array_opciones = array();
					foreach($datos_campo as $row){
						$label = $row->text;
						$value = $row->value;
						$array_opciones[] = $value;
					}
					
					array_shift($array_opciones);
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_opciones as $opcion){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					
					$cantidad_opciones_seleccion = count($array_opciones);
					if($cantidad_opciones_seleccion > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				if($campo->id_tipo_campo == 7){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_select_multiple'));
					
				}
				if($campo->id_tipo_campo == 8){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_rut'));
				}
				if($campo->id_tipo_campo == 9){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
					$datos_campo = json_decode($campo->opciones);
					
					$array_opciones = array();
					foreach($datos_campo as $row){
						$label = $row->text;
						$value = $row->value;
						$array_opciones[] = $label;
					}

					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
					$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
					
					if($array_opciones[0]){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					
				}
				/*if($campo->id_tipo_campo == 11){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_html'));
				}*/
				if($campo->id_tipo_campo == 13){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_mail'));
				}
				if($campo->id_tipo_campo == 14){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_time'));
				}
				
				if($campo->id_tipo_campo == 15){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$unidad_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_unity');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				if($campo->id_tipo_campo == 16){
					$datos_campo = json_decode($campo->default_value);
					$id_mantenedora = $datos_campo->mantenedora;
					$id_campo_label = $datos_campo->field_label;
					$id_campo_value = $datos_campo->field_value;
					$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
					$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
					
					$array_opciones = array();
					foreach($datos as $index => $row){
						$fila = json_decode($row->datos, true);
						$label = $fila[$id_campo_label];
						$value = $fila[$id_campo_value];
						
						$array_opciones[] = $value;
					}
					
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN DESDE MANTENEDORA EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_opciones as $opcion){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);

					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					
					$cantidad_opciones_mantenedora = count($array_opciones);
					if($cantidad_opciones_mantenedora > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_mantenedora);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}	
				$columna++;
			}

		}else{// SI NO ES REGISTRO AMBIENTAL

			$columna = 0;
			$columna_opciones = 0; //A
			
			if($info_formulario->id_tipo_formulario == 3){
				if(!$info_formulario->fijo){
					$columna = 1;
					$doc->getActiveSheet()->setCellValue('A1', lang('date'));
					// COLUMNA FECHA
					$doc->getActiveSheet()->setCellValue('A2', lang('excel_test_date'));
					// PARA DEJAR FECHA COMO TEXTO
					$doc->getActiveSheet()->getStyle('A2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				}
			}
			
			foreach($campos_formulario as $campo){
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $campo->nombre);
				
				if($campo->default_value && $campo->id_tipo_campo != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
					
					if($campo->id_tipo_campo == 5){	
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					} else {
						$valor_por_defecto = $campo->default_value;
					}

					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
					
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
					}
					if($campo->id_tipo_campo == 2){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
					}
					
					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					}

				} else if(!$campo->default_value && $campo->id_tipo_campo != 16){

					if($campo->habilitado){ //SI EL CAMPO ESTÁ DESHABILITADO
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

						if($campo->obligatorio){
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						} else {
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}
						
					} else { //SI EL CAMPO ESTÁ HABILITADO
						
						if($campo->obligatorio){
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
							$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
							$comentario->getFont()->setBold(true);
						
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
							
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
							$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
						}else{
							
							if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 13){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("input_text_maxlength_msg"));
							}
							if($campo->id_tipo_campo == 2){
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
								$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"))->getFont()->setBold(true);
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
								$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("textarea_maxlength_msg"));
							}
							
						}
						
					}

				}

				$columna++;
			}
			
			$columna = 0;
			if($info_formulario->id_tipo_formulario == 3){
				if(!$info_formulario->fijo){
					$columna = 1;
				}
			}
			
			
			foreach($campos_formulario as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				
				if($campo->id_tipo_campo == 1){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_text'));
				}
				if($campo->id_tipo_campo == 2){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_textarea'));
				}
				if($campo->id_tipo_campo == 3){
					//$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$numero_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_number');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_number'));
				}
				if($campo->id_tipo_campo == 4){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_date'));
					// PARA DEJAR FECHA COMO TEXTO
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					
				}
				if($campo->id_tipo_campo == 5){
					if($campo->default_value){
						$periodo = json_decode($campo->default_value);
						$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
					}
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($valor_por_defecto) ? $valor_por_defecto : lang('excel_test_period'));
				}
				if($campo->id_tipo_campo == 6){
					
					if($campo->html_name == "2_or_unidades_funcionales"){
						
						$unidades_funcionales_proyecto = $this->Functional_units_model->get_all_where(array(
							"id_cliente" => $id_cliente,
							"id_proyecto" => $id_proyecto,
							"deleted" => 0
						))->result();
						
						$array_opciones = array();
						foreach($unidades_funcionales_proyecto as $uf){
							$label = $uf->nombre;
							$value = $uf->id;
							//$array_opciones[$value] = $label;
							$array_opciones[] = $label;
						}
						
						//array_shift($array_opciones);
						//var_dump($array_opciones);
						
					} else {
						
						$datos_campo = json_decode($campo->opciones);
						
						$array_opciones = array();
						foreach($datos_campo as $row){
							$label = $row->text;
							$value = $row->value;
							$array_opciones[] = $label;
						}
						
						array_shift($array_opciones);
						
						//var_dump($array_opciones);
						
					}

					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_opciones as $opcion){
						//var_dump($opcion);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
						$fila_opcion++;
					}

					$doc->setActiveSheetIndex(0);
					
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					
					$cantidad_opciones_seleccion = count($array_opciones);
					if($cantidad_opciones_seleccion > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				if($campo->id_tipo_campo == 7){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_select_multiple'));
				}
				if($campo->id_tipo_campo == 8){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_rut'));
				}
				if($campo->id_tipo_campo == 9){
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
					
					$datos_campo = json_decode($campo->opciones);
					
					$array_opciones = array();
					foreach($datos_campo as $row){
						$label = $row->text;
						$value = $row->value;
						$array_opciones[] = $label;
					}

					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
					$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
					
					if($array_opciones[0]){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : $array_opciones[0]);
					}
					
					
				}
				
				if($campo->id_tipo_campo == 13){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_mail'));
				}
				if($campo->id_tipo_campo == 14){
					$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_time'));
				}
				if($campo->id_tipo_campo == 15){
					$unidad_ejemplo = ($campo->default_value) ? $campo->default_value : lang('excel_test_unity');
					$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo->default_value) ? $campo->default_value : lang('excel_test_unity'));
				}
				if($campo->id_tipo_campo == 16){
					$datos_campo = json_decode($campo->default_value);
					$id_mantenedora = $datos_campo->mantenedora;
					$id_campo_label = $datos_campo->field_label;
					$id_campo_value = $datos_campo->field_value;
					$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
					$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
					
					$array_opciones = array();
					foreach($datos as $index => $row){
						$fila = json_decode($row->datos, true);
						$label = $fila[$id_campo_label];
						$value = $fila[$id_campo_value];
						
						$array_opciones[] = $value;
					}
					
					// GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN DESDE MANTENEDORA EN HOJA OPCIONES
					$doc->setActiveSheetIndex(1);
					$fila_opcion = 1;
					foreach($array_opciones as $opcion){
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna_opciones).$fila_opcion, $opcion);
						$fila_opcion++;
					}
					
					$doc->setActiveSheetIndex(0);
					
					$objValidation = $doc->getActiveSheet()->getCell($this->getNameFromNumber($columna).'2')->getDataValidation();     
					$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);     
					$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);     
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle(lang('excel_error_title'));
					$objValidation->setError(lang('excel_error_text'));
					$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
					$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
					
					$cantidad_opciones_mantenedora = count($array_opciones);
					if($cantidad_opciones_mantenedora > 0){
						$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_mantenedora);
						$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					}
					
					$columna_opciones++;
					
				}
				$columna++;
			}
		}
		
		foreach(range('A', $this->getNameFromNumber($columna)) as $columnID) {
			$doc->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		//$doc->getActiveSheet()->getProtection()->setSheet(true);
		//$doc->getActiveSheet()->getStyle('A2:B2')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

		$nombre_hoja = strlen($info_formulario->nombre)>31?substr($info_formulario->nombre, 0, 28).'...':$info_formulario->nombre;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		$nombre_archivo = $info_cliente->sigla.'_'.$info_formulario->codigo.'_plantilla';
		
		// OCULTO HOJA OPTIONS
		$doc->getSheetByName('options')->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);

		//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		//header('Content-Disposition: attachment;filename="bulk_load_template.xlsx"'); //tell browser what's the file name
		//header('Cache-Control: max-age=0'); //no cache
		
		//var_dump($doc);
		
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007'); 
		
		if (!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/')) {
			mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/', 0777, true);
		}
		
		$objWriter->save('files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/'.$nombre_archivo.'.xlsx');

		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/form_'.$id_formulario.'/'.$nombre_archivo.'.xlsx')) {
			echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
			exit();
		}
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<div class="col-md-12">';
		$html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
		$html .= '<a href="'.get_uri("setting_bulk_load/download_form_template/".$id_cliente."/".$id_proyecto."/".$id_formulario).'">'.$nombre_archivo.'.xlsx</a>';
		$html .= '</div>';
		$html .= '</div>';
		
		echo json_encode($html);
		exit();
		
    }
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.	    
	}
	
	function download_form_template($id_cliente, $id_proyecto, $id_formulario) {
		
		if(!$id_cliente && !$id_proyecto && !$id_formulario){
			redirect("forbidden");
		}
		
		$info_cliente = $this->Clients_model->get_one($id_cliente);
		$info_formulario = $this->Forms_model->get_one($id_formulario);
		
		$nombre_archivo = $info_cliente->sigla.'_'.$info_formulario->codigo.'_plantilla';
        $file_data = serialize(array(array("file_name" => $nombre_archivo.".xlsx")));
        download_app_files("files/mimasoft_files/client_".$id_cliente."/project_".$id_proyecto."/form_".$id_formulario."/", $file_data, false);
		
    }
	
	function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}
	
	function validateDate($date){
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}


    function save() {
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$id_tipo_formulario = $this->input->post('form_type');
        $id_formulario = $this->input->post('form');
		$file = $this->input->post('archivo_importado');
		
		$info_formulario = $this->Forms_model->get_one($id_formulario);

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "form_type" => "numeric",
			"form" => "numeric",
			"file" => "required",
        ));
		
		$archivo_subido = move_temp_file($file, "files/carga_masiva/", "", "", $file);
		if($archivo_subido){
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			if(!$info_formulario->fijo){
				$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
			} else {
				$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
					"codigo_formulario_fijo" => $info_formulario->codigo_formulario_fijo,
					"deleted" => 0
				))->result();
			}
			
			$html = '<table class="table table-responsive table-striped">';

			if($id_tipo_formulario == 1){// SI ES REGISTRO AMBIENTAL
			
				$cats = $this->Categories_model->get_categories_of_material_of_form($id_formulario)->result();
				$categorias = array();
				foreach($cats as $cat){
					$row_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $cat->id, 'id_cliente' => $id_cliente, "deleted" => 0));
					if($row_alias->alias){
						$categorias[] = $row_alias->alias;
					}else{
						$categorias[] = $cat->nombre;
					}
				}
				
				//INFO FORMULARIO
				$info_formulario = $this->Forms_model->get_one($id_formulario);

				//$list_data = $this->Environmental_records_model->get_values_of_record($id_formulario)->result();
				$html .= '<thead><tr>';
				$html .= '<th></th>';
				
				// CAMPO FECHA DE REGISTRO
				if(lang('date_filed') == $worksheet->getCell('A1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				// CAMPO CATEGORIA
				if(lang('category') == $worksheet->getCell('B1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				// CAMPO UNIDAD
				$formulario_unidad = json_decode($info_formulario->unidad, true);
				$unidad = $this->Unity_model->get_one($formulario_unidad["unidad_id"]);
				$campo_unidad = $formulario_unidad["nombre_unidad"];
				$nombre_unidad = $campo_unidad . " (" . $unidad->nombre. ")";
				
				if($nombre_unidad == $worksheet->getCell('C1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
					$num_errores++;
				}
				
				if($info_formulario->flujo == "Consumo"){
					
					$data_tipo_origen = json_decode($info_formulario->tipo_origen);
					$id_tipo_origen = $data_tipo_origen->type_of_origin;
					$disabled_field_tipo_origen = (boolean)$data_tipo_origen->disabled_field;
					$default_matter = ($data_tipo_origen->default_matter)?$data_tipo_origen->default_matter:NULL;
					
					if($id_tipo_origen == "1"){ // id 1: matter
						
						$array_tipos_origen = array();//array("" => "-");
						$tipos_origen_materia = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $id_tipo_origen,
							"deleted" => 0
						))->result();
						
						foreach($tipos_origen_materia as $tipo_origen_materia){
							$array_tipos_origen[$tipo_origen_materia->id] = lang($tipo_origen_materia->nombre);
						}
					}
					
					if($id_tipo_origen == "2"){ // id 2: energy
						
						$default_matter = 2;
						//$array_tipos_origen = array("" => "-");
						$tipos_origen = $this->EC_Types_of_origin_model->get_all()->result();
						foreach($tipos_origen as $tipo_origen){
							$array_tipos_origen[$tipo_origen->id] = lang($tipo_origen->nombre);
						}
					}
					
					if(lang('type') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					
					$cont = 4;
				}elseif($info_formulario->flujo == "Residuo"){
					
					// CAMPO TIPO DE TRATAMIENTO
					$array_tipo_tratamiento = array();
					$array_tipo_tratamiento["1"] = "Disposición";
					$array_tipo_tratamiento["2"] = "Reutilización";
					$array_tipo_tratamiento["3"] = "Reciclaje";

					if(lang('type_of_treatment') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					
					// CAMPO FECHA DE RETIRO
					
					if(lang('retirement_date') == $worksheet->getCell('E1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('E1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('E1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					
					$cont = 5;
				}elseif($info_formulario->flujo == "No Aplica"){
					
					$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
					$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
					$disabled_field_no_aplica = (boolean)$data_tipo_no_aplica->disabled_field;
					
					$array_tipos_no_aplica = array();//array("" => "-");
					$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
					
					foreach($tipos_no_aplica as $tipo_no_aplica){
						$array_tipos_no_aplica[$tipo_no_aplica->id] = lang($tipo_no_aplica->nombre);
					}
					
					if(lang('type') == $worksheet->getCell('D1')->getValue()){
						$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
						$num_errores++;
					}
					
					$cont = 4;
				}else{
					
				}
				
				foreach($campos_formulario as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
					
					//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
					if($campo->nombre == $valor_columna){
						$html .= '<th>'.$valor_columna.'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;
				}
				$html .= '</tr></thead>';
				$html .= '<tbody>';
				
				// DATOS DEL CUERPO
				for($row = 2; $row <= $lastRow; $row++){
					$html .= '<tr>';
					$html .= '<td>'.$row.'</td>';
					
					// CELDA FECHA
					$fecha_excel = $worksheet->getCell('A'.$row)->getValue();
					if($this->validateDate($fecha_excel)){
						$html .= '<td>'.$fecha_excel.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA CATEGORIA
					$categoria_excel = $worksheet->getCell('B'.$row)->getValue();
					//var_dump($worksheet->getCell('B'.$row));
					if(in_array($categoria_excel, $categorias)){
						$html .= '<td>'.$categoria_excel.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$categoria_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					// CELDA UNIDAD
					$unidad_excel = $worksheet->getCell('C'.$row)->getValue();
					if(strlen(trim($unidad_excel)) > 0){
						if(is_numeric($unidad_excel)){
							$html .= '<td>'.$unidad_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$unidad_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$unidad_excel.' '.$msg_formato.'</td>';
						$num_errores++;
					}
					
					if($info_formulario->flujo == "Consumo"){
						
						// CONSULTO LA DEFINICION DEL TIPO DE ORIGEN PARA VER SI ESTÁ DESHABILITADO
						if($id_tipo_origen == "1"){ // id 1: matter
							if($disabled_field_tipo_origen){
								$array_tipos_origen = array($default_matter => $array_tipos_origen[$default_matter]);
							}
						}
						if($id_tipo_origen == "2"){ // id 2: energy
							$array_tipos_origen = array($id_tipo_origen => $array_tipos_origen[$id_tipo_origen]);
						}
						//
						
						// CELDA TIPO
						$tipo_origen_excel = $worksheet->getCell('D'.$row)->getValue();
						if(in_array($tipo_origen_excel, $array_tipos_origen)){
							$html .= '<td>'.$tipo_origen_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_origen_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						$cont = 4;
						
					}elseif($info_formulario->flujo == "Residuo"){
						
						// CONSULTO LA DEFINICION DEL TIPO DE TRATAMIENTO PARA VER SI ESTÁ DESHABILITADO
						$data_tipo_tratamiento = json_decode($info_formulario->tipo_tratamiento);
						$id_tipo_tratamiento_defecto = $data_tipo_tratamiento->tipo_tratamiento;
						$disabled_field = (boolean)$data_tipo_tratamiento->disabled_field;
						if($disabled_field){
							$array_tipo_tratamiento = array($id_tipo_tratamiento_defecto => $array_tipo_tratamiento[$id_tipo_tratamiento_defecto]);
						}
						//
						
						// CELDA TIPO DE TRATAMIENTO
						$tipo_tratamiento_excel = $worksheet->getCell('D'.$row)->getValue();
						if(in_array($tipo_tratamiento_excel, $array_tipo_tratamiento)){
							$html .= '<td>'.$tipo_tratamiento_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_tratamiento_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}

						// CELDA FECHA DE RETIRO
						$fecha_excel = $worksheet->getCell('E'.$row)->getValue();
						if($this->validateDate($fecha_excel) || $fecha_excel == ""){
							$html .= '<td>'.$fecha_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						$cont = 5;
					}elseif($info_formulario->flujo == "No Aplica"){
						
						// CONSULTO LA DEFINICION DEL TIPO DE NO APLICA PARA VER SI ESTÁ DESHABILITADO
						if($disabled_field_no_aplica){
							$array_tipos_no_aplica = array($default_type => $array_tipos_no_aplica[$default_type]);
						}
						//
						
						// CELDA TIPO
						$tipo_no_aplica_excel = $worksheet->getCell('D'.$row)->getValue();
						if(in_array($tipo_no_aplica_excel, $array_tipos_no_aplica)){
							$html .= '<td>'.$tipo_no_aplica_excel.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$tipo_no_aplica_excel.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						$cont = 4;
						
					}else{
						
					}
					
					// OTRAS CELDAS
					
					foreach($campos_formulario as $campo){
						if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1){
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
							
						}
						if($campo->id_tipo_campo == 2){
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
						}
						if($campo->id_tipo_campo == 3){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if(is_numeric($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
											
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
							
						}
						if($campo->id_tipo_campo == 4){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if($this->validateDate($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || $this->validateDate($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
							
						}
						if($campo->id_tipo_campo == 5){
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
										if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
											$html .= '<td>'.$valor_columna.'</td>';
										}else{
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
											$num_errores++;
										}
										
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								
								if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
										if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
											$html .= '<td>'.$valor_columna.'</td>';
										}else{
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
											$num_errores++;
										}
										
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}elseif(strlen($valor_columna) == 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}
							
						}
						if($campo->id_tipo_campo == 6){
							$ops = json_decode($campo->opciones);
							$opciones = array();
							foreach($ops as $op){
								if($campo->obligatorio){
									if($op->value == ""){continue;}
								}else{
									if($op->value == ""){
										$opciones[] = "";
										continue;
									}
								}
								$opciones[] = $op->value;
							}
							
							if(in_array($valor_columna, $opciones)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							
							
						}
						/*if($campo->id_tipo_campo == 7){//select_multiple
							
						}*/
						if($campo->id_tipo_campo == 8){
							// POR AHORA NO ESTAMOS VALIDANDO CAMPO RUT
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
							
						}
						if($campo->id_tipo_campo == 9){
							// CAMPO RADIO, SIEMPRE SERA OBLIGATORIO
							
							$ops = json_decode($campo->opciones);
							$opciones = array();
							foreach($ops as $op){
								$opciones[] = $op->value;
							}
							
							if(in_array($valor_columna, $opciones)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						
						}
						if($campo->id_tipo_campo == 13){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(valid_email($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || valid_email($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						if($campo->id_tipo_campo == 14){
							// ESTE TIPO DE CAMPO RECIBE EN INGRESO LA HORA EN FORMATO 24HRS SIEMPRE
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 5){// 12:00
									if(preg_match('/\d{2}:\d{2}/', $valor_columna)){
										$hora = explode(":", $valor_columna);
										if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
											$html .= '<td>'.$valor_columna.'</td>';
										} else {
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
											$num_errores++;
    									}
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}elseif(strlen(trim($valor_columna)) == 0){
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || preg_match('/\d{2}:\d{2}/', $valor_columna)){
									$hora = explode(":", $valor_columna);
									if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
										$html .= '<td>'.$valor_columna.'</td>';
									} else {
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}

						}
						if($campo->id_tipo_campo == 15){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if(is_numeric($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
											
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						if($campo->id_tipo_campo == 16){
							$datos_campo = json_decode($campo->default_value);
							$id_mantenedora = $datos_campo->mantenedora;
							$id_campo_label = $datos_campo->field_label;
							$id_campo_value = $datos_campo->field_value;
							$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
							//$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
							
							$array_opciones = array();
							foreach($datos as $elemento){
								$fila = json_decode($elemento->datos, true);
								$label = $fila[$id_campo_label];
								$value = $fila[$id_campo_value];
								
								$array_opciones[] = $value;
							}
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(in_array($valor_columna, $array_opciones)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
								
							}else{
								if($valor_columna == "" || in_array($valor_columna, $array_opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						$cont++;
					}
					
					
					$html .= '</tr>';
					
					
					
				}
				
				$html .= '</tbody>';
				$html .= '</table>';
				
				if($num_errores > 0){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
				}else{
					$this->bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido);
					//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
				}
				
				exit();
				
			}else{// SI NO ES REGISTRO AMBIENTAL
				
				
				$html .= '<thead><tr>';
				$html .= '<th></th>';

				$cont = 0;
				
				if($id_tipo_formulario == 3){
					if(!$info_formulario->fijo){
						if(lang('date') == $worksheet->getCell('A1')->getValue()){
							$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
						}else{
							$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
							$num_errores++;
						}
						$cont = 1;
					}
				}
				
				foreach($campos_formulario as $campo){
					if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
					
					//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
					if($campo->nombre == $valor_columna){
						$html .= '<th>'.$valor_columna.'</th>';
					}else{
						$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
						$num_errores++;
					}
					$cont++;
				}
				
				$html .= '</tr></thead>';
				$html .= '<tbody>';
				
				// DATOS DEL CUERPO
				for($row = 2; $row <= $lastRow; $row++){
					$html .= '<tr>';
					$html .= '<td>'.$row.'</td>';

					// OTRAS CELDAS
					$cont = 0;
					if($id_tipo_formulario == 3){
						
						if(!$info_formulario->fijo){
							
							// CELDA FECHA
							$fecha_excel = $worksheet->getCell('A'.$row)->getValue();
							if($this->validateDate($fecha_excel)){
								$html .= '<td>'.$fecha_excel.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$fecha_excel.' '.$msg_formato.'</td>';
								$num_errores++;
							}
							$cont = 1;
							
						}

					}
				
					foreach($campos_formulario as $campo){
						if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1){
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
							
						}
						if($campo->id_tipo_campo == 2){
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
						}
						if($campo->id_tipo_campo == 3){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if(is_numeric($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
											
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
							
						}
						if($campo->id_tipo_campo == 4){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if($this->validateDate($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || $this->validateDate($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
							
						}
						if($campo->id_tipo_campo == 5){
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
										if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
											$html .= '<td>'.$valor_columna.'</td>';
										}else{
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
											$num_errores++;
										}
										
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								
								if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
										if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
											$html .= '<td>'.$valor_columna.'</td>';
										}else{
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_date_range.'</td>';
											$num_errores++;
										}
										
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
									
								}elseif(strlen($valor_columna) == 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}
							
						}
						if($campo->id_tipo_campo == 6){
							
							if($campo->html_name == "2_or_unidades_funcionales"){
								
								//$nombre_uf = $valor_columna;
								$opciones = array();
								$ops = $this->Functional_units_model->get_all_where(array(
									"id_cliente" => $id_cliente,
									"id_proyecto" => $id_proyecto,
									"deleted" => 0
								))->result();
								
								foreach($ops as $op){
									if($campo->obligatorio){
										if($op->nombre == ""){continue;}
									}else{
										if($op->nombre == ""){
											$opciones[] = "";
											continue;
	
										}
									}
									$opciones[] = $op->nombre;
								}
								
								if(in_array($valor_columna, $opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
								
							} else {
								
								$ops = json_decode($campo->opciones);
								$opciones = array();
								foreach($ops as $op){
									if($campo->obligatorio){
										if($op->value == ""){continue;}
									}else{
										if($op->value == ""){
											$opciones[] = "";
											continue;
	
										}
									}
									$opciones[] = $op->value;
								}
								
								if(in_array($valor_columna, $opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
								
							}
							
						}
						/*if($campo->id_tipo_campo == 7){//select_multiple
							
						}*/
						if($campo->id_tipo_campo == 8){
							// POR AHORA NO ESTAMOS VALIDANDO CAMPO RUT
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								$html .= '<td>'.$valor_columna.'</td>';
							}
							
						}
						if($campo->id_tipo_campo == 9){
							// CAMPO RADIO, SIEMPRE SERA OBLIGATORIO
							
							$ops = json_decode($campo->opciones);
							$opciones = array();
							foreach($ops as $op){
								$opciones[] = $op->value;
							}
							
							if(in_array($valor_columna, $opciones)){
								$html .= '<td>'.$valor_columna.'</td>';
							}else{
								$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
								$num_errores++;
							}
						
						}
						if($campo->id_tipo_campo == 13){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(valid_email($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || valid_email($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						if($campo->id_tipo_campo == 14){
							// ESTE TIPO DE CAMPO RECIBE EN INGRESO LA HORA EN FORMATO 24HRS SIEMPRE
							
							if($campo->obligatorio){
								if(strlen($valor_columna) == 5){// 12:00 PM
									if(preg_match('/\d{2}:\d{2}/', $valor_columna)){
										$hora = explode(":", $valor_columna);
										if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
											$html .= '<td>'.$valor_columna.'</td>';
										} else {
											$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
											$num_errores++;
    									}
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}elseif(strlen(trim($valor_columna)) == 0){
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || preg_match('/\d{2}:\d{2}/', $valor_columna)){
									$hora = explode(":", $valor_columna);
									if( ($hora[0] >= "00" && $hora[0] <= "23") && ($hora[1] >= "00" && $hora[1] <= "59") ){
										$html .= '<td>'.$valor_columna.'</td>';
									} else {
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}

						}
						if($campo->id_tipo_campo == 15){
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									
									if(is_numeric($valor_columna)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
											
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
							}else{
								if($valor_columna == "" || is_numeric($valor_columna)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						if($campo->id_tipo_campo == 16){
							
							$datos_campo = json_decode($campo->default_value);
							$id_mantenedora = $datos_campo->mantenedora;
							$id_campo_label = $datos_campo->field_label;
							$id_campo_value = $datos_campo->field_value;
							$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
							//$info_mantenedora = $this->Forms_model->get_one($id_mantenedora);
							
							$array_opciones = array();
							foreach($datos as $elemento){
								$fila = json_decode($elemento->datos, true);
								$label = $fila[$id_campo_label];
								$value = $fila[$id_campo_value];
								
								$array_opciones[] = $value;
							}
							/*var_dump($valor_columna);
							var_dump($array_opciones);
							var_dump(in_array($valor_columna, $array_opciones));exit();*/
							
							if($campo->obligatorio){
								if(strlen(trim($valor_columna)) > 0){
									if(in_array($valor_columna, $array_opciones)){
										$html .= '<td>'.$valor_columna.'</td>';
									}else{
										$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
										$num_errores++;
									}
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_obligatorio.'</td>';
									$num_errores++;
								}
								
							}else{
								if($valor_columna == "" || in_array($valor_columna, $array_opciones)){
									$html .= '<td>'.$valor_columna.'</td>';
								}else{
									$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
									$num_errores++;
								}
							}
							
						}
						
						$cont++;
					}
					
					
					$html .= '</tr>';
					
					
					
				}
				
				$html .= '</tbody>';
				$html .= '</table>';
				
				
				if($num_errores > 0){
					echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
				}else{
					$this->bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido);
					//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
				}
				
				exit();
				
				
			}
			
			

		}
		

    }
	
	function bulk_load($id_cliente, $id_proyecto, $id_formulario, $archivo_subido){
		
		//$this->load->library('excel');
		$formulario = $this->Forms_model->get_one($id_formulario);
		
		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva/'.$archivo_subido);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();

		$array_insert = array();
		
		if(!$formulario->fijo){
			$campos_formulario = $this->Forms_model->get_fields_of_form($id_formulario)->result();
		} else {
			$campos_formulario = $this->Fixed_fields_model->get_all_where(array(
				"codigo_formulario_fijo" => $formulario->codigo_formulario_fijo,
				"deleted" => 0
			))->result();
		}
		
		if($formulario->id_tipo_formulario == 1){// SI ES REGISTRO AMBIENTAL

			$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $id_formulario, "id_proyecto" => $id_proyecto, "deleted" => 0));
			$id_formulario_rel_proyecto = $formulario_rel_proyecto->id;
			$info_formulario = $this->Forms_model->get_one($id_formulario);
			
			// POR CADA FILA
			for($row = 2; $row <= $lastRow; $row++){
				
				$array_row = array();
				$array_row["id_formulario_rel_proyecto"] = $id_formulario_rel_proyecto;
				
				$array_json = array();
				$valor_fecha = $worksheet->getCell('A'.$row)->getValue();
				$label_categoria = $worksheet->getCell('B'.$row)->getValue();
				
				// DEBO TRAER EL ID DE LA CATEGORIA DONDE EL NOMBRE SEA IGUAL AL INGRESADO O TENGA UN ALIAS IGUAL AL INGRESADO
				$categoria = $this->Categories_model->get_one_where(array("nombre" => $label_categoria, "deleted" => 0));
				if(!$categoria->id){
					$categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $id_cliente, "alias" => $label_categoria, "deleted" => 0));
					$id_categoria = $categoria->id_categoria;
				}else{
					$id_categoria = $categoria->id;
				}
				
				$array_json["fecha"] = $valor_fecha;
				$array_json["id_categoria"] = (int)$id_categoria;
				
				// CAMPO UNIDAD
				$valor_unidad = $worksheet->getCell('C'.$row)->getValue();
				$datos_unidad_form = json_decode($info_formulario->unidad, "true");
				$tipo_unidad = $this->Unity_type_model->get_one($datos_unidad_form["tipo_unidad_id"])->nombre;
				$unidad = $this->Unity_model->get_one($datos_unidad_form["unidad_id"])->nombre;
				
				$array_json["unidad_residuo"] = (float)$valor_unidad;
				$array_json["tipo_unidad"] = $tipo_unidad;
				$array_json["unidad"] = $unidad;
				
				if($info_formulario->flujo == "Consumo"){
					
					$tipo = $worksheet->getCell('D'.$row)->getValue();
					
					$data_tipo_origen = json_decode($info_formulario->tipo_origen);
					$id_tipo_origen = $data_tipo_origen->type_of_origin;
					
					if($id_tipo_origen == 1){
						// DEBO TRAER EL ID DEL MATERIAL DONDE EL NOMBRE SEA IGUAL AL INGRESADO
						$materias = $this->EC_Types_of_origin_matter_model->get_all_where(array(
							"id_tipo_origen" => $id_tipo_origen,
							//"nombre" => $tipo,
							"deleted" => 0
						))->result();
						
						foreach($materias as $materia){
							if($tipo == lang($materia->nombre)){
								$type_of_origin_matter = $materia->id;
							}
						}
						
					}
					
					$cont = 4;
				}elseif($info_formulario->flujo == "Residuo"){
					
					// CAMPO TIPO DE TRATAMIENTO
					$tipo_tratamiento = $worksheet->getCell('D'.$row)->getValue();
					
					if($tipo_tratamiento == "Disposición"){
						$id_tipo_tratamiento = "1";
					}elseif($tipo_tratamiento == "Reutilización"){
						$id_tipo_tratamiento = "2";
					}elseif($tipo_tratamiento == "Reciclaje"){
						$id_tipo_tratamiento = "3";
					}
					
					// CAMPO FECHA DE RETIRO
					$fecha_retiro = $worksheet->getCell('E'.$row)->getValue();
					
					$cont = 5;
				}elseif($info_formulario->flujo == "No Aplica"){
					
					$tipo = $worksheet->getCell('D'.$row)->getValue();
					
					$data_tipo_no_aplica = json_decode($info_formulario->tipo_por_defecto);
					$default_type = ($data_tipo_no_aplica->default_type)?$data_tipo_no_aplica->default_type:NULL;
					
					// DEBO TRAER EL ID DEL TIPO DONDE EL NOMBRE SEA IGUAL AL INGRESADO
					$tipos_no_aplica = $this->EC_Types_no_apply_model->get_all()->result();
					
					foreach($tipos_no_aplica as $tipo_na){
						if($tipo == lang($tipo_na->nombre)){
							$type_id = $tipo_na->id;
						}
					}
					
					$cont = 4;
				}else{
					
				}
				
				// CAMPOS DINAMICOS
				foreach($campos_formulario as $campo){
					
					if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
						continue;
					}
					if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
						$array_json["$campo->id"] = NULL;
						continue;
					}
					
					$letra_columna = $this->getNameFromNumber($cont);
					$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
					//echo var_dump($letra_columna.$row.' - '.$campo->id_tipo_campo.': '.$valor_columna);
					
					if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
						// CAMPO DESHABILITADO = 1
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 5){
						if($campo->obligatorio){
							$array_periodo = explode("/", $valor_columna);
							$fecha_desde = $array_periodo[0];
							$fecha_hasta = $array_periodo[1];
							$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
						}else{
							
							if($campo->habilitado == 1){
								if(trim($valor_columna) == ""){
									$json_periodo = array("start_date" => "", "end_date" => "");
								}else{
									$periodo = json_decode($campo->default_value);
									$json_periodo = array("start_date" => $periodo->start_date, "end_date" => $periodo->end_date);
								}
								
							}else{
								if(trim($valor_columna) == ""){
									$json_periodo = array("start_date" => "", "end_date" => "");
								}else{
									$array_periodo = explode("/", $valor_columna);
									$fecha_desde = $array_periodo[0];
									$fecha_hasta = $array_periodo[1];
									$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
								}
							}
						}
						
						$array_json["$campo->id"] = $json_periodo;
					}
					if($campo->id_tipo_campo == 6){
						
						/*$ops = json_decode($campo->opciones);
						$opciones = array();
						foreach($ops as $op){
							if($campo->obligatorio){
								if($op->value == ""){continue;}
							}else{
								if($op->value == ""){
									$opciones[""] = "";
									continue;

								}
							}
							$opciones[$op->text] = $op->value;
						}
						
						$array_json["$campo->id"] = $opciones[$valor_columna];*/
						
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
						
					}
					if($campo->id_tipo_campo == 8){// RUT
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 9){// RADIO
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 13){// CORREO
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 14){// HORA
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 15){// UNIDAD
						if($campo->habilitado == 1){
							$array_json["$campo->id"] = $campo->default_value;
						}else{
							$array_json["$campo->id"] = $valor_columna;
						}
					}
					if($campo->id_tipo_campo == 16){
						/*$datos_campo = json_decode($campo->default_value);
						$id_mantenedora = $datos_campo->mantenedora;
						$id_campo_label = $datos_campo->field_label;
						$id_campo_value = $datos_campo->field_value;
						$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
						
						$array_opciones = array();
						foreach($datos as $elemento){
							$fila = json_decode($elemento->datos, true);
							$label = $fila[$id_campo_label];
							$value = $fila[$id_campo_value];
							$array_opciones[$label] = $value;
						}
						
						$array_json["$campo->id"] = $array_opciones[$valor_columna];*/
						$array_json["$campo->id"] = $valor_columna;
						
					}
					$cont++;

				}
				
				
				if($info_formulario->flujo == "Consumo"){
					
					$array_json['type_of_origin'] = $id_tipo_origen;
					if($type_of_origin_matter){
						$array_json['type_of_origin_matter'] = $type_of_origin_matter;
					}
					
				}elseif($info_formulario->flujo == "Residuo"){
					
					$array_json["tipo_tratamiento"] = $id_tipo_tratamiento;
					$array_json["fecha_retiro"] = $fecha_retiro;
					$array_json["nombre_archivo_retiro"] = NULL;
					$array_json["nombre_archivo_recepcion"] = NULL;
					
				}elseif($info_formulario->flujo == "No Aplica"){
					$array_json['default_type'] = $type_id;
				} else {
					
				}
				
				$json_datos = json_encode($array_json);
				$array_row["datos"] = $json_datos;
				$array_row["created_by"] = $this->login_user->id;
				$array_row["modified_by"] = NULL;
				$array_row["created"] = get_current_utc_time();
				$array_row["modified"] = NULL;
				$array_row["deleted"] = 0;
				
				$array_insert[] = $array_row;
			}// FIN FOR ROW
			
		}else{// SI NO ES REGISTRO AMBIENTAL
			
			if(!$formulario->fijo){

				$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $id_formulario, "id_proyecto" => $id_proyecto, "deleted" => 0));
				$id_formulario_rel_proyecto = $formulario_rel_proyecto->id;
				
				// POR CADA FILA
				for($row = 2; $row <= $lastRow; $row++){
					
					$array_row = array();
					$array_row["id_formulario_rel_proyecto"] = $id_formulario_rel_proyecto;
					
					$array_json = array();
					$cont = 0;
					
					if($formulario->id_tipo_formulario == 3){
						$cont = 1;
						$valor_fecha = $worksheet->getCell('A'.$row)->getValue();
						$array_json["fecha"] = $valor_fecha;
					}
								
					foreach($campos_formulario as $campo){
						
						if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
							$array_json["$campo->id"] = NULL;
							continue;
						}
						
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
							// CAMPO DESHABILITADO = 1
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 5){
							if($campo->obligatorio){
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
								
							}else{
								if($campo->habilitado == 1){
									if(trim($valor_columna) == ""){
										$json_periodo = array("start_date" => "", "end_date" => "");
									}else{
										$periodo = json_decode($campo->default_value);
										$json_periodo = array("start_date" => $periodo->start_date, "end_date" => $periodo->end_date);
									}
									
								}else{
									if(trim($valor_columna) == ""){
										$json_periodo = array("start_date" => "", "end_date" => "");
									}else{
										$array_periodo = explode("/", $valor_columna);
										$fecha_desde = $array_periodo[0];
										$fecha_hasta = $array_periodo[1];
										$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
									}
								}
							}
							
							$array_json["$campo->id"] = $json_periodo;
						}
						if($campo->id_tipo_campo == 6){
							/*$ops = json_decode($campo->opciones);
							$opciones = array();
							foreach($ops as $op){
								if($campo->obligatorio){
									if($op->value == ""){continue;}
								}else{
									if($op->value == ""){
										$opciones[""] = "";
										continue;
	
									}
								}
								$opciones[$op->text] = $op->value;
							}
							
							$array_json["$campo->id"] = $opciones[$valor_columna];*/
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
							
						}
						if($campo->id_tipo_campo == 8){// RUT
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 9){// RADIO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 13){// CORREO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 14){// HORA
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 15){// UNIDAD
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 16){
							/*$datos_campo = json_decode($campo->default_value);
							$id_mantenedora = $datos_campo->mantenedora;
							$id_campo_label = $datos_campo->field_label;
							$id_campo_value = $datos_campo->field_value;
							$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
							
							$array_opciones = array();
							foreach($datos as $elemento){
								$fila = json_decode($elemento->datos, true);
								$label = $fila[$id_campo_label];
								$value = $fila[$id_campo_value];
								$array_opciones[$label] = $value;
							}
							
							$array_json["$campo->id"] = $array_opciones[$valor_columna];*/
							$array_json["$campo->id"] = $valor_columna;
						}
						
						$cont++;
					}
					
					$json_datos = json_encode($array_json);
					$array_row["datos"] = $json_datos;
					$array_row["created_by"] = $this->login_user->id;
					$array_row["modified_by"] = NULL;
					$array_row["created"] = get_current_utc_time();
					$array_row["modified"] = NULL;
					$array_row["deleted"] = 0;
					
					$array_insert[] = $array_row;
					
				}// FIN FOR ROW
			
			} else { // Si el formulario es fijo
				
				// POR CADA FILA
				for($row = 2; $row <= $lastRow; $row++){
					
					$array_row = array();
					$array_row["id_formulario"] = $id_formulario;
					
					$array_json = array();
					$cont = 0;
								
					foreach($campos_formulario as $campo){
						
						if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
							continue;
						}
						if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
							$array_json["$campo->id"] = NULL;
							continue;
						}
						
						$letra_columna = $this->getNameFromNumber($cont);
						$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
						
						if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
							// CAMPO DESHABILITADO = 1
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 5){
							if($campo->obligatorio){
								$array_periodo = explode("/", $valor_columna);
								$fecha_desde = $array_periodo[0];
								$fecha_hasta = $array_periodo[1];
								$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
								
							}else{
								if($campo->habilitado == 1){
									if(trim($valor_columna) == ""){
										$json_periodo = array("start_date" => "", "end_date" => "");
									}else{
										$periodo = json_decode($campo->default_value);
										$json_periodo = array("start_date" => $periodo->start_date, "end_date" => $periodo->end_date);
									}
									
								}else{
									if(trim($valor_columna) == ""){
										$json_periodo = array("start_date" => "", "end_date" => "");
									}else{
										$array_periodo = explode("/", $valor_columna);
										$fecha_desde = $array_periodo[0];
										$fecha_hasta = $array_periodo[1];
										$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
									}
								}
							}
							
							$array_json["$campo->id"] = $json_periodo;
						}
						if($campo->id_tipo_campo == 6){
							
							if($campo->html_name = "2_or_unidades_funcionales"){
								//$nombre_uf = $valor_columna;
								$uf = $this->Functional_units_model->get_one_where(array(
									"id_cliente" => $id_cliente,
									"id_proyecto" => $id_proyecto,
									"nombre" => $valor_columna,
									"deleted" => 0
								));
								
								$array_json["$campo->id"] = $uf->id;
								
							} else {
								
								$array_json["$campo->id"] = $valor_columna;
							
							}
							
						}
						if($campo->id_tipo_campo == 8){// RUT
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 9){// RADIO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 13){// CORREO
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 14){// HORA
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 15){// UNIDAD
							if($campo->habilitado == 1){
								$array_json["$campo->id"] = $campo->default_value;
							}else{
								$array_json["$campo->id"] = $valor_columna;
							}
						}
						if($campo->id_tipo_campo == 16){
							$array_json["$campo->id"] = $valor_columna;
						}
						
						$cont++;
					}
					
					$json_datos = json_encode($array_json);
					$array_row["datos"] = $json_datos;
					$array_row["created_by"] = $this->login_user->id;
					$array_row["modified_by"] = NULL;
					$array_row["created"] = get_current_utc_time();
					$array_row["modified"] = NULL;
					$array_row["deleted"] = 0;
					
					$array_insert[] = $array_row;
					
				}// FIN FOR ROW
				
			}	
		
		}
		
		if(!$formulario->fijo){
			$bulk_load = $this->Form_values_model->bulk_load($array_insert);
			if($bulk_load){
				
				// Guardar histórico notificaciones
				$id_cliente = $this->login_user->client_id;
				$id_proyecto = $this->session->project_context;	
				$id_user = $this->session->user_id;
			
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "bulk_load",
					"id_element" => $formulario->id
				);
				ayn_save_historical_notification($options);
			
				echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
			}else{
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
			}
		} else { // Si el formulario es fijo
			$bulk_load = $this->Fixed_form_values_model->bulk_load($array_insert);
			if($bulk_load){
				
				// Guardar histórico notificaciones
				$id_cliente = $this->login_user->client_id;
				$id_proyecto = $this->session->project_context;	
				$id_user = $this->session->user_id;
			
				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_user" => $id_user,
					"module_level" => "project",
					"id_client_module" => $this->id_modulo_cliente,
					"id_client_submodule" => $this->id_submodulo_cliente,
					"event" => "bulk_load",
					"id_element" => $formulario->id
				);
				ayn_save_historical_notification($options);
				
				echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
			}else{
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
			}
		}

	}
	
	function create_client_folder($client_id) {
		if(!file_exists(__DIR__.'/../../files/client_'.$client_id)) {
			if(mkdir(__DIR__.'/../../files/client_'.$client_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
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
		
		$file_name = $this->input->post("file_name");
		
		if (!$file_name){
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}

		$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		if ($file_ext == 'xlsx') {
			echo json_encode(array("success" => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('invalid_file_type') . " ($file_name)"));
		}
		
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
	
	/* get instructions for bulk load */

    function get_intructions() {
        //$this->access_only_allowed_members();
		$html = $this->load->view('setting_bulk_load/intructions', $view_data, true);
		echo $html;
    }

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */