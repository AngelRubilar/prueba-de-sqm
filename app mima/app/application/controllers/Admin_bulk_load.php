<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_bulk_load extends MY_Controller {
	
    function __construct() {
        parent::__construct();
		$this->load->helper('email');
        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        //$this->access_only_allowed_members();

        $view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		
        $this->template->rander("admin_bulk_load/index", $view_data);
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
        
        if($id_proyecto){
            foreach($forms as $form){
                $formularios[] = array("id" => $form->id, "text" => $form->nombre);
            }
        }
        
        echo json_encode($formularios);
		
    }
	
	
	
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.	    
	}
	
	function download_form_template() {
		
        $file_data = serialize(array(array("file_name" => "Importacion masiva - PRO (Formularios).xlsx")));
        download_app_files("files/carga_masiva_admin/", $file_data, false);
		
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

    function save() {
		
		$id_cliente = $this->input->post('client');
        $id_proyecto = $this->input->post('project');
		$file = $this->input->post('archivo_importado');

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "client" => "numeric",
			"project" => "numeric",
			"file" => "required",
        ));
		
		$archivo_subido = move_temp_file($file, "files/carga_masiva_admin/", "", "", $file);
		if($archivo_subido){
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_admin/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_admin/'.$archivo_subido);
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			
			// TABLA CAMPOS PARA MANTENEDORA
			$worksheet = $excelObj->getSheet(0);// Campos - Mantenedoras 
			$lastRow = $worksheet->getHighestRow();
			
			// INFORMACION DE CAMPOS 
			$opciones_tipo_campos = $this->Field_types_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
			$opciones_tipo_campos_mantenedora = $opciones_tipo_campos;
			unset($opciones_tipo_campos_mantenedora[16]);
			$opciones_unidades = $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
			
			$html = '<table class="table table-responsive table-striped">';
			
			$html .= '<thead><tr>';
			$html .= '<th></th>';
			
			if($worksheet->getCell('A1')->getValue() == 'Nombre'){
					$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('B1')->getValue() == 'Tipo de Campo'){
					$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('C1')->getValue() == 'Valor por defecto'){
					$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('D1')->getValue() == 'Opciones'){
					$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('E1')->getValue() == 'Unidad'){
					$html .= '<th>'.$worksheet->getCell('E1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('E1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('F1')->getValue() == 'Obligatorio'){
					$html .= '<th>'.$worksheet->getCell('F1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('F1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if($worksheet->getCell('G1')->getValue() == 'Deshabilitado'){
					$html .= '<th>'.$worksheet->getCell('G1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('G1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			
			// DATOS DEL CUERPO
			$arreglo_datos = $worksheet->rangeToArray('A2:G'.$lastRow);
			$excel_row = 2;
			
			$validacion_nombres = array();
			foreach($arreglo_datos as $row){
				
				$html .= '<tr>';
				$html .= '<td>'.$excel_row.'</td>';
				
				// CELDA NOMBRE
				$nombre = trim($row[0]);
				if(!in_array($nombre, $validacion_nombres) && strlen($nombre) != 0){
					$html .= '<td>'.$nombre.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$nombre.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				$validacion_nombres[] = $nombre;
				
				// CELDA TIPO DE CAMPO
				$tipo_campo = trim($row[1]);
				if(!in_array($tipo_campo, $opciones_tipo_campos_mantenedora)){
					$html .= '<td>'.$tipo_campo.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$tipo_campo.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				
				// CELDA VALOR POR DEFECTO
				$default_value = trim($row[2]);
				$options_value = trim($row[3]);// PARA EL CASO DE SELECCION
				$array_result = $this->validate_default_value($default_value, $tipo_campo, $options_value);
				if($array_result){
					$html .= '<td>'.$default_value.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$default_value.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				// HASTA ACÁ SE DEJA EN STAND BY PARA AGILIZAR EL USO DEL SCRIPT
				
				$excel_row++;
				
			}
			
			
			//var_dump($arreglo_datos);
			
			$html .= '</tbody>';
			$html .= '</table>';
			
			/*if($num_errores > 0){
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
			}else{*/
				//echo json_encode(array("success" => true, 'message' => "Si funka ñeke ñeke"));
				$this->bulk_load($id_cliente, $id_proyecto, $archivo_subido);
			//}
			
			exit();

		}
		
    }
	
	function validate_default_value($default_value, $tipo_campo, $options_value = NULL){
		
		$result = array();
		
		if($tipo_campo == 'Input text'){
			if(strlen($default_value) > 255){
				$result = array("result" => false, "msg" => "");
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Texto Largo'){
			if(strlen($default_value) > 2000){
				$result = array("result" => false, "msg" => "");
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Número'){
			if(strlen($default_value) > 0){
				if(is_numeric($default_value)){
					$result = array("result" => true);
				}else{
					$result = array("result" => false, "msg" => "");
				}
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Fecha'){
			if(strlen($default_value) > 0){
				if($this->validateDate($default_value)){
					$result = array("result" => true);
				}else{
					$result = array("result" => false, "msg" => "");
				}
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Periodo'){
			if(strlen($default_value) > 0){
				if(strlen($valor_columna) == 21){// YYYY-MM-DD/YYYY-MM-DD
					$array_periodo = explode("/", $valor_columna);
					$fecha_desde = $array_periodo[0];
					$fecha_hasta = $array_periodo[1];
					if($this->validateDate($fecha_desde) && $this->validateDate($fecha_hasta)){
						if((strtotime($fecha_hasta)) >= (strtotime($fecha_desde))){
							$result = array("result" => true);
						}else{
							$result = array("result" => false, "msg" => "");
						}
					}else{
						$result = array("result" => false, "msg" => "");
					}
				}else{
					$result = array("result" => false, "msg" => "");
				}
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Selección'){
			$opciones = explode("/", $options_value);
			
			if(in_array($default_value, $opciones)){
				$result = array("result" => true);
			}else{
				$result = array("result" => false, "msg" => "");
			}
		}
		
		if($tipo_campo == 'Rut'){
			// POR AHORA NO ESTAMOS VALIDANDO CAMPO RUT
			if(strlen($default_value) > 0 && (strlen($default_value) == 11 || strlen($default_value) == 12)){
				$result = array("result" => true);
			}elseif(strlen($default_value) == 0){
				$result = array("result" => true);
			}else{
				$result = array("result" => false, "msg" => "");
			}
		}
		
		if($tipo_campo == 'Radio Buttons'){
			$opciones = explode("/", $options_value);
			
			if(in_array($default_value, $opciones)){
				$result = array("result" => true);
			}else{
				$result = array("result" => false, "msg" => "");
			}
		}
		
		if($tipo_campo == 'Archivo'){
			$result = array("result" => true);
		}
		
		if($tipo_campo == 'Texto Fijo'){
			
			if($default_value == strip_tags($default_value)){
				$result = array("result" => false, "msg" => "");
			}else{
				$result = array("result" => true);
			}
		}
		
		if($tipo_campo == 'Divisor'){
			$result = array("result" => true);
		}
		
		if($tipo_campo == 'Correo'){
			if(strlen($default_value) > 0){
				if(valid_email($valor_columna)){
					$result = array("result" => true);
				}else{
					$result = array("result" => false, "msg" => "");
				}
			}else{
				$result = array("result" => true);
			}
		}
		
	}
	
	function validateDate($date){
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') == $date;
	}
	
	function bulk_load($id_cliente, $id_proyecto, $archivo_subido){
		
		//$this->load->library('excel');
		//$formulario = $this->Forms_model->get_one($id_formulario);
		
		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_admin/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_admin/'.$archivo_subido);
		
		// DEFINICIONES PARA MANTENEDORAS
		$array_insert_campos_mantenedoras = array();
		$array_insert_campos_mantenedoras_log = array();
		
		$array_insert_mantenedoras = array();
		$array_insert_mantenedoras_log = array();
		
		$array_insert_mantenedoras_rel_proyecto = array();
		$array_insert_mantenedoras_rel_proyecto_log = array();
		
		$array_insert_mantenedoras_rel_campos = array();
		$array_insert_mantenedoras_rel_campos_log = array();

		// DEFINICIONES PARA RA Y OR
		$array_insert_campos_formularios = array();
		$array_insert_campos_formularios_log = array();
		
		$array_insert_formularios = array();
		$array_insert_formularios_log = array();
		
		$array_insert_formularios_rel_proyecto = array();
		$array_insert_formularios_rel_proyecto_log = array();
		
		$array_insert_formularios_rel_campos = array();
		$array_insert_formularios_rel_campos_log = array();
		
		$array_insert_ra_rel_categorias = array();
		$array_insert_ra_rel_categorias_log = array();
		
		$array_insert_ra_rel_materiales = array();
		$array_insert_ra_rel_materiales_log = array();

		//
		
		$opciones_tipo_campos = $this->Field_types_model->get_dropdown_list(array("id"), "nombre", array("deleted" => 0));
		$opciones_unidades = $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$tipos_tratamientos = $this->Tipo_tratamiento_model->get_dropdown_list(array("id"), "nombre", array("deleted" => 0));
		$materiales_nombres = $this->Materials_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$materiales_ides = $this->Materials_model->get_dropdown_list(array("id"), "nombre", array("deleted" => 0));
		$categorias_nombres = $this->Categories_model->get_dropdown_list(array("nombre"), "id", array("deleted" => 0));
		$categorias_ides = $this->Categories_model->get_dropdown_list(array("id"), "nombre", array("deleted" => 0));
		
		// TABLA CAMPOS PARA MANTENEDORA
		$worksheet = $excelObj->getSheet(0);// Campos - Mantenedoras 
		$lastRow = $worksheet->getHighestRow();
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$tipo_campo = $worksheet->getCell('B'.$row)->getValue();
			$id_tipo_campo = $opciones_tipo_campos[$tipo_campo];
			$array_row["id_tipo_campo"] = $id_tipo_campo;
			
			$array_row["id_cliente"] = $id_cliente;
			$array_row["id_proyecto"] = $id_proyecto;
			
			$nombre_campo = $worksheet->getCell('A'.$row)->getValue();
			$array_row["nombre"] = trim($nombre_campo);
			
			$array_row["html_name"] = "";// Lo modificaremos despues por que necesita el id
			//$array_row["html_name"] = $this->clean($save_id.'_'.$nombre_campo);
			
			$default_value = $worksheet->getCell('C'.$row)->getValue();
			if($id_tipo_campo == 5){
				$array_periodo = explode("/", $default_value);
				$fecha_desde = $array_periodo[0];
				$fecha_hasta = $array_periodo[1];
				$array_row["default_value"] = json_encode(array('start_date' => $fecha_desde, 'end_date' => $fecha_hasta));
			}elseif($id_tipo_campo == 6){
				$opciones_campo = $worksheet->getCell('D'.$row)->getValue();
				$array_opciones = explode("/", $opciones_campo);
				if($default_value == "-" || $default_value == ""){
					$array_row["default_value"] = "";
				}elseif(!in_array($default_value, $array_opciones)){
					$array_row["default_value"] = "";
				}else{
					$array_row["default_value"] = $default_value;
				}
			}elseif($id_tipo_campo == 10){
				$array_row["default_value"] = "";
			}elseif($id_tipo_campo == 12){
				$array_row["default_value"] = "<hr>";
			}else{
				$array_row["default_value"] = $default_value;
			}
			
			$opciones_campo = $worksheet->getCell('D'.$row)->getValue();
			if($id_tipo_campo == 6){
				$array_values = explode("/", $opciones_campo);
				$array_opciones = array();
				foreach($array_values as $index => $value){
					if($index == 0){
						$array_opciones[] = array('value' => "", 'text' => $value);
					}else{
						$array_opciones[] = array('value' => $value, 'text' => $value);
					}
				}
				$array_row["opciones"] = json_encode($array_opciones);
			}elseif($id_tipo_campo == 9){
				$array_values = explode("/", $opciones_campo);
				$array_opciones = array();
				foreach($array_values as $index => $label){
					$array_opciones[] = array('value' => $label, 'text' => $label);
				}
				$array_row["opciones"] = json_encode($array_opciones);
			}elseif($id_tipo_campo == 15){
				$unidad = $worksheet->getCell('E'.$row)->getValue();
				//var_dump($unidad);
				//var_dump($opciones_unidades);
				if(!in_array($unidad, $opciones_unidades)){
					$array_row["opciones"] = "";
				}else{
					$fila_unidad = $this->Unity_model->get_one_where(array("nombre" => $unidad, "deleted" => 0));
					if($fila_unidad->id){
						$id_tipo_unidad = $fila_unidad->id_tipo_unidad;
						$id_unidad = $fila_unidad->id;
						$array_unidad = array();
						$array_unidad[] = array('id_tipo_unidad' => $id_tipo_unidad, 'id_unidad' => $id_unidad);
						$array_row["opciones"] = json_encode($array_unidad);
					}else{
						$array_row["opciones"] = "";
					}
				}
			}else{
				$array_row["opciones"] = "";
			}
			
			$obligatorio = ($worksheet->getCell('F'.$row)->getValue() == "Si") ? 1 : 0;
			$array_row["obligatorio"] = $obligatorio;
			
			$habilitado = ($worksheet->getCell('G'.$row)->getValue() == "Si") ? 1 : 0;
			$array_row["habilitado"] = $habilitado;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Fields_model->save($array_row);
			
			if($save_id) {
				$array_insert_campos_mantenedoras_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_campos_mantenedoras[] = $array_row;
				
				if($id_tipo_campo == 5){
					$array_row["html_name"] = json_encode(array(
						'start_name' => $this->clean($save_id.'_'.$nombre_campo.'_start'), 
						'end_name' => $this->clean($save_id.'_'.$nombre_campo.'_end')
					));
				}else{
					$array_row["html_name"] = $this->clean($save_id.'_'.$nombre_campo);
				}
				$save_id = $this->Fields_model->save($array_row, $save_id);
			}else{
				$array_insert_campos_mantenedoras_log[$row] = 0;
			}
			
		}// FIN FOR ROW
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_campos_mantenedoras_log)){
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_campos_mantenedoras_log, true) > 1){
				$fila_error = array_search(0, $array_insert_campos_mantenedoras_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 1', 'carga' => true));
				exit();
			}
		}
		
		
		// TABLA MANTENEDORAS
		$worksheet = $excelObj->getSheet(1);// Mantenedoras 
		$lastRow = $worksheet->getHighestRow();
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$array_row["id_tipo_formulario"] = 2;
			
			$array_row["id_cliente"] = $id_cliente;
			//$array_row["id_proyecto"] = $id_proyecto;
			
			$numero_mantenedora = $worksheet->getCell('C'.$row)->getValue();
			$array_row["numero"] = trim($numero_mantenedora);
			
			$nombre_mantenedora = $worksheet->getCell('A'.$row)->getValue();
			$array_row["nombre"] = trim($nombre_mantenedora);
			
			$descripcion_mantenedora = $worksheet->getCell('B'.$row)->getValue();
			$array_row["descripcion"] = trim($descripcion_mantenedora);
			
			$codigo_mantenedora = ($project_info->sigla).$numero_mantenedora.$nombre_mantenedora;
			$array_row["codigo"] = $codigo_mantenedora;
			
			$array_row["flujo"] = NULL;
			
			$icono_mantenedora = $worksheet->getCell('D'.$row)->getValue();
			$array_row["icono"] = trim($icono_mantenedora);
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Forms_model->save($array_row);
			
			if($save_id) {
				$array_insert_mantenedoras_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_mantenedoras[] = $array_row;
			}else{
				$array_insert_mantenedoras_log[$row] = 0;
			}
		}
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_mantenedoras_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_mantenedoras_log, true) > 1){
				$fila_error = array_search(0, $array_insert_mantenedoras_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 2', 'carga' => true));
				exit();
			}
		}else{
			
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$array_row = array();
				$array_row["id_formulario"] = $id_elemento;
				$array_row["id_proyecto"] = $id_proyecto;
				$array_row["created_by"] = $this->login_user->id;
				$array_row["created"] = get_current_utc_time();
				$array_row["modified_by"] = NULL;
				$array_row["modified"] = NULL;
				$array_row["deleted"] = 0;
				$save_id = $this->Form_rel_project_model->save($array_row);
				$array_row["id"] = $save_id;
				
				$array_insert_mantenedoras_rel_proyecto[] = $array_row;
				$array_insert_mantenedoras_rel_proyecto_log[$id_elemento] = $save_id;
			}
		}
		
		
		
		
		// TABLA MANTENEDORA REL CAMPOS
		$worksheet = $excelObj->getSheet(2);// Mantenedoras - campos 
		$lastRow = $worksheet->getHighestRow();
		
		// NOMBRES CAMPOS
		$id_campos_mantenedoras = array();
		foreach($array_insert_campos_mantenedoras as $campo_mantenedora) {
			$id_campos_mantenedoras[$campo_mantenedora['nombre']] = $campo_mantenedora['id'];
		}
		
		// NOMBRES MANTENEDORAS
		$id_mantenedoras = array();
		foreach($array_insert_mantenedoras as $mantenedora) {
			$id_mantenedoras[$mantenedora['nombre']] = $mantenedora['id'];
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$nombre_campo = trim($worksheet->getCell('B'.$row)->getValue());
			$id_campo = $id_campos_mantenedoras[$nombre_campo];
			$array_row["id_campo"] = $id_campo;
			
			$nombre_mantenedora = trim($worksheet->getCell('A'.$row)->getValue());
			$id_mantenedora = $id_mantenedoras[$nombre_mantenedora];
			$array_row["id_formulario"] = $id_mantenedora;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Field_rel_form_model->save($array_row);
			
			if($save_id) {
				$array_insert_mantenedoras_rel_campos_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_mantenedoras_rel_campos[] = $array_row;
			}else{
				$array_insert_mantenedoras_rel_campos_log[$row] = 0;
			}
			
		}// FIN FOR ROW
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_mantenedoras_rel_campos_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL PROYECTO CREADOS
			foreach($array_insert_mantenedoras_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL CAMPO CREADOS
			foreach($array_insert_mantenedoras_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_mantenedoras_rel_campos_log, true) > 1){
				$fila_error = array_search(0, $array_insert_mantenedoras_rel_campos_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 3', 'carga' => true));
				exit();
			}
		}
		
		

		// TABLA CAMPOS PARA RA y OR
		$worksheet = $excelObj->getSheet(3);// Campos - RA y OR 
		$lastRow = $worksheet->getHighestRow();
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$tipo_campo = trim($worksheet->getCell('B'.$row)->getValue());
			$id_tipo_campo = $opciones_tipo_campos[$tipo_campo];
			$array_row["id_tipo_campo"] = $id_tipo_campo;
			
			$array_row["id_cliente"] = $id_cliente;
			$array_row["id_proyecto"] = $id_proyecto;
			
			$nombre_campo = $worksheet->getCell('A'.$row)->getValue();
			$array_row["nombre"] = trim($nombre_campo);
			
			$array_row["html_name"] = "";// Lo modificaremos despues por que necesita el id
			//$array_row["html_name"] = $this->clean($save_id.'_'.$nombre_campo);
			
			$default_value = $worksheet->getCell('C'.$row)->getValue();
			if($id_tipo_campo == 5){
				$array_periodo = explode("/", $default_value);
				$fecha_desde = $array_periodo[0];
				$fecha_hasta = $array_periodo[1];
				$array_row["default_value"] = json_encode(array('start_date' => $fecha_desde, 'end_date' => $fecha_hasta));
			}elseif($id_tipo_campo == 6){
				$opciones_campo = $worksheet->getCell('D'.$row)->getValue();
				$array_opciones = explode("/", $opciones_campo);
				if($default_value == "-" || $default_value == ""){
					$array_row["default_value"] = "";
				}elseif(!in_array($default_value, $array_opciones)){
					$array_row["default_value"] = "";
				}else{
					$array_row["default_value"] = $default_value;
				}
			}elseif($id_tipo_campo == 10){
				$array_row["default_value"] = "";
			}elseif($id_tipo_campo == 12){
				$array_row["default_value"] = "<hr>";
			}elseif($id_tipo_campo == 16){
				$formulario_mantenedora = trim($worksheet->getCell('F'.$row)->getValue());
				$id_mantenedora = $id_mantenedoras[$formulario_mantenedora];
				$formulario_campo = trim($worksheet->getCell('G'.$row)->getValue());
				$id_campo = $id_campos_mantenedoras[$formulario_campo];
				$default_value = array(
					"mantenedora" => $id_mantenedora, 
					"field_label" => $id_campo, 
					"field_value" => $id_campo
				);
				$array_row["default_value"] = json_encode($default_value);
			}else{
				$array_row["default_value"] = $default_value;
			}
			
			$opciones_campo = $worksheet->getCell('D'.$row)->getValue();
			if($id_tipo_campo == 6){
				$array_values = explode("/", $opciones_campo);
				$array_opciones = array();
				foreach($array_values as $index => $value){
					if($index == 0){
						$array_opciones[] = array('value' => "", 'text' => $value);
					}else{
						$array_opciones[] = array('value' => $value, 'text' => $value);
					}
				}
				$array_row["opciones"] = json_encode($array_opciones);
			}elseif($id_tipo_campo == 9){
				$array_values = explode("/", $opciones_campo);
				$array_opciones = array();
				foreach($array_values as $index => $label){
					$array_opciones[] = array('value' => $label, 'text' => $label);
				}
				$array_row["opciones"] = json_encode($array_opciones);
			}elseif($id_tipo_campo == 15){
				$unidad = $worksheet->getCell('E'.$row)->getValue();
				if(!in_array($unidad, $opciones_unidades)){
					$array_row["opciones"] = "";
				}else{
					$fila_unidad = $this->Unity_model->get_one_where(array("nombre" => $unidad, "deleted" => 0));
					if($fila_unidad->id){
						$id_tipo_unidad = $fila_unidad->id_tipo_unidad;
						$id_unidad = $fila_unidad->id;
						$array_unidad = array();
						$array_unidad[] = array('id_tipo_unidad' => $id_tipo_unidad, 'id_unidad' => $id_unidad);
						$array_row["opciones"] = json_encode($array_unidad);
					}else{
						$array_row["opciones"] = "";
					}
				}
			}else{
				$array_row["opciones"] = "";
			}
			
			$obligatorio = ($worksheet->getCell('H'.$row)->getValue() == "Si") ? 1 : 0;
			$array_row["obligatorio"] = $obligatorio;
			
			$habilitado = ($worksheet->getCell('I'.$row)->getValue() == "Si") ? 1 : 0;
			$array_row["habilitado"] = $habilitado;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Fields_model->save($array_row);
			
			if($save_id) {
				$array_insert_campos_formularios_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_campos_formularios[] = $array_row;
				
				if($id_tipo_campo == 5){
					$array_row["html_name"] = json_encode(array(
						'start_name' => $this->clean($save_id.'_'.$nombre_campo.'_start'), 
						'end_name' => $this->clean($save_id.'_'.$nombre_campo.'_end')
					));
				}else{
					$array_row["html_name"] = $this->clean($save_id.'_'.$nombre_campo);
				}
				$save_id = $this->Fields_model->save($array_row, $save_id);
			}else{
				$array_insert_campos_formularios_log[$row] = 0;
			}
			
		}// FIN FOR ROW
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_campos_formularios_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL PROYECTO CREADOS
			foreach($array_insert_mantenedoras_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL CAMPO CREADOS
			foreach($array_insert_mantenedoras_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// ELIMINAR CAMPOS PARA RA y OR CREADOS
			foreach($array_insert_campos_formularios_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_campos_formularios_log, true) > 1){
				$fila_error = array_search(0, $array_insert_campos_formularios_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 4', 'carga' => true));
				exit();
			}
		}
		
		
		
		// TABLA RA y OR
		$worksheet = $excelObj->getSheet(4);// Formularios 
		$lastRow = $worksheet->getHighestRow();
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$nombre_formulario = $worksheet->getCell('A'.$row)->getValue();
			$array_row["nombre"] = trim($nombre_formulario);
			
			$descripcion_formulario = $worksheet->getCell('B'.$row)->getValue();
			$array_row["descripcion"] = trim($descripcion_formulario);
			
			$numero_formulario = $worksheet->getCell('C'.$row)->getValue();
			$array_row["numero"] = trim($numero_formulario);
			
			$icono_formulario = $worksheet->getCell('D'.$row)->getValue();
			$array_row["icono"] = trim($icono_formulario);
			
			$tipo_formulario = $worksheet->getCell('E'.$row)->getValue();
			if($tipo_formulario == "RA"){
				
				$array_row["id_tipo_formulario"] = 1;
				
				$flujo_formulario = trim($worksheet->getCell('F'.$row)->getValue());
				if(in_array($flujo_formulario, array("Consumo", "Residuo", "No Aplica"))){
					$array_row["flujo"] = $flujo_formulario;
				}else{
					$array_row["flujo"] = "";
				}
				
				$nombre_unidad_formulario = trim($worksheet->getCell('G'.$row)->getValue());
				$unidad_formulario = trim($worksheet->getCell('H'.$row)->getValue());
				
				$fila_unidad = $this->Unity_model->get_one_where(array("nombre" => $unidad_formulario, "deleted" => 0));
				if($fila_unidad->id){
					$id_tipo_unidad = $fila_unidad->id_tipo_unidad;
					$id_unidad = $fila_unidad->id;
					$array_row["unidad"] = json_encode(array(
						'nombre_unidad' => $nombre_unidad_formulario, 
						'tipo_unidad_id' => $id_tipo_unidad,
						'unidad_id' => $id_unidad,
					));
				}else{
					$array_row["unidad"] = "";
				}
				
				
				if($flujo_formulario == "Residuo"){
					$tipo_tratamiento_formulario = trim($worksheet->getCell('I'.$row)->getValue());
					$habilitado_formulario = trim($worksheet->getCell('J'.$row)->getValue());
					
					if(!in_array($tipo_tratamiento_formulario, array("Disposición", "Reutilización", "Reciclaje"))){
						$tipo_tratamiento = "";
						$habilitado = "0";
					}else{
						$tipo_tratamiento = $tipos_tratamientos[$tipo_tratamiento_formulario];
						$habilitado = ($habilitado_formulario == "Si")?"1":"0";
					}
					$array_row["tipo_tratamiento"] = json_encode(array(
						'tipo_tratamiento' => $tipo_tratamiento, 
						'disabled_field' => $habilitado,
					));
				}else{
					$array_row["tipo_tratamiento"] = NULL;
				}
				
				
			}elseif($tipo_formulario == "OR"){
				$array_row["id_tipo_formulario"] = 3;
				$array_row["flujo"] = NULL;
			}else{
				$array_row["id_tipo_formulario"] = "";
			}
			
			$codigo_formulario = ($project_info->sigla).$numero_formulario.$nombre_formulario;
			$array_row["codigo"] = $codigo_formulario;
			
			$array_row["id_cliente"] = $id_cliente;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Forms_model->save($array_row);
			
			if($save_id) {
				$array_insert_formularios_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_formularios[] = $array_row;
			}else{
				$array_insert_formularios_log[$row] = 0;
			}
		}
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_formularios_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL PROYECTO CREADOS
			foreach($array_insert_mantenedoras_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL CAMPO CREADOS
			foreach($array_insert_mantenedoras_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// ELIMINAR CAMPOS PARA RA y OR CREADOS
			foreach($array_insert_campos_formularios_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS CREADOS
			foreach($array_insert_formularios_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_formularios_log, true) > 1){
				$fila_error = array_search(0, $array_insert_formularios_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 5', 'carga' => true));
				exit();
			}
		}else{
			
			foreach($array_insert_formularios_log as $fila_excel => $id_elemento){
				$array_row = array();
				$array_row["id_formulario"] = $id_elemento;
				$array_row["id_proyecto"] = $id_proyecto;
				$array_row["created_by"] = $this->login_user->id;
				$array_row["created"] = get_current_utc_time();
				$array_row["modified_by"] = NULL;
				$array_row["modified"] = NULL;
				$array_row["deleted"] = 0;
				$save_id = $this->Form_rel_project_model->save($array_row);
				$array_row["id"] = $save_id;
				
				$array_insert_formularios_rel_proyecto[] = $array_row;
				$array_insert_formularios_rel_proyecto_log[$id_elemento] = $save_id;
			}
		}
		
		
		
		// TABLA FORMULARIOS REL CAMPOS
		$worksheet = $excelObj->getSheet(5);// Formularios - campos 
		$lastRow = $worksheet->getHighestRow();
		
		// NOMBRES CAMPOS
		$id_campos_formularios = array();
		foreach($array_insert_campos_formularios as $campo_formulario) {
			$id_campos_formularios[$campo_formulario['nombre']] = $campo_formulario['id'];
		}
		
		// NOMBRES FORMULARIOS
		$id_formularios = array();
		foreach($array_insert_formularios as $formulario) {
			$id_formularios[$formulario['nombre']] = $formulario['id'];
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$nombre_campo = trim($worksheet->getCell('B'.$row)->getValue());
			$id_campo = $id_campos_formularios[$nombre_campo];
			$array_row["id_campo"] = $id_campo;
			
			$nombre_formulario = trim($worksheet->getCell('A'.$row)->getValue());
			$id_formulario = $id_formularios[$nombre_formulario];
			$array_row["id_formulario"] = $id_formulario;
			
			$array_row["created_by"] = $this->login_user->id;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified_by"] = NULL;
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$save_id = $this->Field_rel_form_model->save($array_row);
			
			if($save_id) {
				$array_insert_formularios_rel_campos_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_formularios_rel_campos[] = $array_row;
			}else{
				$array_insert_formularios_rel_campos_log[$row] = 0;
			}
			
		}// FIN FOR ROW
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_formularios_rel_campos_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL PROYECTO CREADOS
			foreach($array_insert_mantenedoras_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL CAMPO CREADOS
			foreach($array_insert_mantenedoras_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// ELIMINAR CAMPOS PARA RA y OR CREADOS
			foreach($array_insert_campos_formularios_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS CREADOS
			foreach($array_insert_formularios_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL PROYECTO CREADOS
			foreach($array_insert_formularios_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL CAMPO CREADOS
			foreach($array_insert_formularios_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_formularios_rel_campos_log, true) > 1){
				$fila_error = array_search(0, $array_insert_formularios_rel_campos_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 6', 'carga' => true));
				exit();
			}
		}
		
		
		
		// TABLA RA REL CATEGORIAS
		$worksheet = $excelObj->getSheet(6);// RA - Materiales 
		$lastRow = $worksheet->getHighestRow();
		$array_materiales = array();
		
		// NOMBRES FORMULARIOS
		$id_formularios = array();
		$nombre_formularios = array();
		foreach($array_insert_formularios as $formulario) {
			if($formulario['id_tipo_formulario'] == 1){
				$id_formularios[$formulario['nombre']] = $formulario['id'];
				$nombre_formularios[$formulario['id']] = $formulario['nombre'];
			}
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			
			$nombre_formulario = trim($worksheet->getCell('A'.$row)->getValue());
			$id_formulario = $id_formularios[$nombre_formulario];
			$array_row["id_formulario"] = $id_formulario;
			
			$nombre_material = trim($worksheet->getCell('B'.$row)->getValue());
			$id_material = $materiales_ides[$nombre_material];
			$array_row["id_material"] = $id_material;
			
			$nombre_categoria = trim($worksheet->getCell('C'.$row)->getValue());
			$id_categoria = $categorias_ides[$nombre_categoria];
			$array_row["id_categoria"] = $id_categoria;
			
			$array_row["deleted"] = 0;
			
			$save_id = $this->Form_rel_materiales_rel_categorias_model->save($array_row);
			
			if($save_id) {
				$array_insert_ra_rel_categorias_log[$row] = $save_id;
				$array_row["id"] = $save_id;
				$array_insert_ra_rel_categorias[] = $array_row;
				
				if(!in_array($id_material, $array_materiales)){
					$array_materiales[] = $id_material;
					$array_row_mat = array();
					$array_row_mat["id_formulario"] = $id_formulario;
					$array_row_mat["id_material"] = $id_material;
					$array_row_mat["deleted"] = 0;
					$save_id = $this->Form_rel_material_model->save($array_row_mat);
					if($save_id){
						$array_insert_ra_rel_materiales_log[] = $save_id;
					}
				}
			}else{
				$array_insert_ra_rel_categorias_log[$row] = 0;
			}
			
		}// FIN FOR ROW
		
		// VALIDAR ERRORES
		if(in_array(0, $array_insert_ra_rel_categorias_log)){
			
			// ELIMINAR CAMPOS CREADOS
			foreach($array_insert_campos_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS CREADAS
			foreach($array_insert_mantenedoras_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL PROYECTO CREADOS
			foreach($array_insert_mantenedoras_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR MANTENEDORAS REL CAMPO CREADOS
			foreach($array_insert_mantenedoras_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// ELIMINAR CAMPOS PARA RA y OR CREADOS
			foreach($array_insert_campos_formularios_log as $fila_excel => $id_elemento){
				$this->Fields_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS CREADOS
			foreach($array_insert_formularios_log as $fila_excel => $id_elemento){
				$this->Forms_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL PROYECTO CREADOS
			foreach($array_insert_formularios_rel_proyecto_log as $fila_excel => $id_elemento){
				$this->Form_rel_project_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL CAMPO CREADOS
			foreach($array_insert_formularios_rel_campos_log as $fila_excel => $id_elemento){
				$this->Field_rel_form_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL MATERIALES CATEGORIAS CREADOS
			foreach($array_insert_ra_rel_categorias_log as $fila_excel => $id_elemento){
				$this->Form_rel_materiales_rel_categorias_model->delete($id_elemento);
			}
			
			// ELIMINAR FORMULARIOS REL MATERIALES CREADOS
			foreach($array_insert_ra_rel_materiales_log as $id_elemento){
				$this->Form_rel_material_model->delete($id_elemento);
			}
			
			// IDENTIFICAR FILA DEL ERROR
			if(array_search(0, $array_insert_ra_rel_categorias_log, true) > 1){
				$fila_error = array_search(0, $array_insert_ra_rel_materiales_log, true);
				echo json_encode(array("success" => false, 'message' => 'Error en fila '.$fila_error.' de hoja 6', 'carga' => true));
				exit();
			}
		}

		
		echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		
		
		/*if(in_array(0, $array_insert_campos_mantenedoras_log)){
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}else{
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}*/
		
		
		/*$bulk_load = $this->Form_values_model->bulk_load($array_insert_campos_mantenedoras);
		if($bulk_load){
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}*/
		
		
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

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */