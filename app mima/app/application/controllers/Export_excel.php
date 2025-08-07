<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class xExport_excel extends MY_Controller {

    function __construct() {
        parent::__construct();
		
        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();


        $this->template->rander("clients/index");
    }

    /* load client add/edit modal */

    /*function modal_form() {
        
    }*/
		
	function excel($form_type = 0, $id_record = 0){
		$options = array("id" => $id_record);
		$record_info = $this->Forms_model->get_details($options)->row();
		$nombre_doc = str_replace(" ","_", $record_info->nombre);
		$forms = $this->Form_types_model->get_details()->result();
		
		$id_usuario = $this->session->user_id;

		foreach($forms as $value){
			
			if(($value->id == 1)&&($form_type == $value->id)){
				
				$puede_ver = $this->profile_access($id_usuario, 2, 0, "ver"); //2: modulo registros ambientales

				$list_data = $this->Environmental_records_model->get_values_of_record($id_record)->result();
				$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();
				
				$nombre_columnas = $this->name_columns($columnas,$form_type,$id_record);

				$result = array();
				foreach($list_data as $data){
					if($puede_ver == 1){
						$result[] = $this->make_row($data, $columnas, $id_record);
					}
					if($puede_ver == 2){
						if($id_usuario == $data->created_by){
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}					
					if($puede_ver == 3){
						$numero_columnas = count($columnas) + 4;
						if(is_int($numero_columnas)){
							$result[$numero_columnas] = array();
						} else {
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}				
				}
						
			}
			if(($value->id == 2)&&($form_type == $value->id)){
				
				$puede_ver = $this->profile_access($id_usuario, 3, 0, "ver"); //3: modulo mantenedoras
				
				$list_data = $this->Feeders_model->get_values_of_record($id_record)->result();
				$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();

				$nombre_columnas = $this->name_columns($columnas,$form_type);
				$result = array();
				foreach($list_data as $data){
					if($puede_ver == 1){
						$result[] = $this->make_row($data, $columnas, $id_record);
					}
					if($puede_ver == 2){
						if($id_usuario == $data->created_by){
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}					
					if($puede_ver == 3){
						$numero_columnas = count($columnas) + 4;
						if(is_int($numero_columnas)){
							$result[$numero_columnas] = array();
						} else {
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}				
				}
				
			}
			if(($value->id == 3)&&($form_type == $value->id)){
				
				$puede_ver = $this->profile_access($id_usuario, 4, 0, "ver"); //4: modulo otros registros
				
				$list_data = $this->Other_records_model->get_values_of_record($id_record)->result();
				$columnas = $this->Forms_model->get_fields_of_form($id_record)->result();
				
				$nombre_columnas = $this->name_columns($columnas,$form_type);
				
				$result = array();
				foreach($list_data as $data){
					if($puede_ver == 1){
						$result[] = $this->make_row($data, $columnas, $id_record);
					}
					if($puede_ver == 2){
						if($id_usuario == $data->created_by){
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}					
					if($puede_ver == 3){
						$numero_columnas = count($columnas) + 4;
						if(is_int($numero_columnas)){
							$result[$numero_columnas] = array();
						} else {
							$result[] = $this->make_row($data, $columnas, $id_record);
						}
					}				
				}
			}
		}

		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle($record_info->nombre)
							 ->setSubject($record_info->nombre)
							 ->setDescription($record_info->nombre)
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
							 
		$client_info = $this->Clients_model->get_one($record_info->id_cliente);
		$project_info = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $record_info->id, "deleted" => 0));
		
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
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

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $project_info->id_proyecto);
		$hora = convert_to_general_settings_time_format($project_info->id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $project_info->id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', $record_info->tipo_formulario)
			->setCellValue('C2', $record_info->nombre)
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
						
					} elseif($columna["id_tipo_campo"] == "date_filed"){ // FECHA DE REGISTRO (PRECARGADO) (REGISTROS AMBIENTALES CONSUMO, RESIDUO, NO APLICA)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "category"){ // CATEGORÍA (PRECARGADO) (REGISTROS AMBIENTALES CONSUMO, RESIDUO, NO APLICA)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "unity"){ // UNIDAD (PRECARGADO) (REGISTROS AMBIENTALES CONSUMO, RESIDUO, NO APLICA)
						
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						//$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_GENERAL );
					
					} elseif($columna["id_tipo_campo"] == "type"){ //  TIPO (PRECARGADO) (REGISTROS AMBIENTALES CONSUMO, NO APLICA)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "type_of_treatment"){ // TIPO DE TRATAMIENTO (PRECARGADO) (REGISTROS AMBIENTALES RESIDUO)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == "retirement_date"){ // FECHA DE RETIRO (PRECARGADO) (REGISTROS AMBIENTALES RESIDUO)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
						
					} elseif($columna["id_tipo_campo"] == "retirement_evidence"){ // EVIDENCIA DE RETIRO (PRECARGADO) (REGISTROS AMBIENTALES RESIDUO)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "reception_evidence"){ // EVIDENCIA DE RECEPCIÓN (PRECARGADO) (REGISTROS AMBIENTALES RESIDUO)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} elseif($columna["id_tipo_campo"] == "date"){ // FECHA (PRECARGADO) (OTROS REGISTROS)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/
		
		$nombre_hoja = strlen($record_info->nombre) > 31?substr($record_info->nombre, 0, 28).'...':$record_info->nombre;
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla.'_'.$record_info->codigo.'_'.date("Y-m-d");
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
				
	}

	function excel_acv_report(){

		$tabla = $this->input->post('tabla');
		$columns_number = $this->input->post('columns_number');
		$nombre_archivo = $this->input->post('filename');
		$nombre_doc = str_replace("\"", "", $nombre_archivo);
		$nombre_doc = str_replace(" ", "_", $nombre_doc);
		$nombre_tabla_html = str_replace("\"", "", $this->input->post('nombre_tabla'));
			
		$tabla = json_decode($tabla, true);
		
		$id_cliente = str_replace("\"", "", $this->input->post('id_cliente'));
		$id_proyecto = str_replace("\"", "", $this->input->post('id_proyecto'));
		$id_subproyecto = str_replace("\"", "", $this->input->post('id_subproyecto'));
		$id_unidad_funcional = str_replace("\"", "", $this->input->post('id_unidad_funcional'));
		$fecha_desde_original = str_replace("\"", "", $this->input->post('fecha_desde'));
		$fecha_hasta_original = str_replace("\"", "", $this->input->post('fecha_hasta'));
		$fecha_desde = get_date_format($fecha_desde_original,$id_proyecto);
		$fecha_hasta = get_date_format($fecha_hasta_original,$id_proyecto);

		$nombre_cliente = $this->Clients_model->get_one($id_cliente)->company_name;
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
		$nombre_subproyecto = $this->Subprojects_model->get_one($id_subproyecto)->nombre;
		$nombre_unidad_funcional = $this->Functional_units_model->get_one($id_unidad_funcional)->nombre;

		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
		} else {
			$color_sitio = "00b393";
		}
		
		// ESTILOS
		$styleHeaderData = array(
			'font' => array(
				'bold' => true,
			)
		);
		
		$styleHeaderColumnsArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
		);
		
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
		$objDrawing->setHeight(30);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B2');
		$doc->getActiveSheet()->getStyle('A1:B2')->applyFromArray($styleArray);
		
		// HEADER
		$letra = $this->getNameFromNumber($columns_number - 1);
		//$letra = $this->getNameFromNumber($columns_number + 2);
		//$doc->getActiveSheet()->mergeCells('D1:'.$letra.'1');
		$doc->getActiveSheet()->mergeCells('A3:B3');
		$doc->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleHeaderColumnsArray);
		$doc->getActiveSheet()->getStyle('A12:'.$letra.'12')->applyFromArray($styleHeaderData);
		$doc->getActiveSheet()->getStyle('A4:A10')->applyFromArray($styleHeaderData);
		//$doc->getActiveSheet()->getStyle('D2:'.$letra.'2')->applyFromArray($styleHeaderColumnsArray);
		$doc->setActiveSheetIndex(0)
		
			->setCellValue('A3', str_replace("\"","",$nombre_archivo))
            ->setCellValue('A4', 'Fecha')
			->setCellValue('B4', date('d/m/Y').' a las '.date('H:i:s'))
			->setCellValue('A5', 'Cliente')
			->setCellValue('B5', $nombre_cliente)
			->setCellValue('A6', 'Proyecto')
			->setCellValue('B6', $nombre_proyecto)
			->setCellValue('A7', 'Subproyecto')
			->setCellValue('B7', $nombre_subproyecto)
			->setCellValue('A8', 'Unidad Funcional')
			->setCellValue('B8', $nombre_unidad_funcional)
			->setCellValue('A9', 'Desde')
			->setCellValue('B9', $fecha_desde)
			->setCellValue('A10', 'Hasta')
			->setCellValue('B10', $fecha_hasta);
		
		$doc->setActiveSheetIndex(0);
		//$doc->getActiveSheet()->fromArray($tabla, NULL,"D1");
		$remover_cabecera_tabla = array_shift($tabla);
		$doc->getActiveSheet()->fromArray($tabla, NULL,"A12");
		
		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A12:'.$letra.'12');
		
		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			
			if(($nombre_tabla_html == "procesos_unitarios-table") || ($nombre_tabla_html == "modelo_caracterizacion-table")
				|| ($nombre_tabla_html == "category_indicators-table")){
				
				if($cell == 'B'){
					$doc->getActiveSheet()->getColumnDimension('B')->setWidth(70);
					$doc->getActiveSheet()->getStyle('B12:B'.$doc->getActiveSheet()->getHighestRow())
					->getAlignment()->setWrapText(true)->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY));
					$doc->getActiveSheet()->getStyle('A12:A'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
				} else {
					$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
				}
				
			} else if($nombre_tabla_html == "categorias_impacto-table"){
				
				if($cell == 'D'){
					$doc->getActiveSheet()->getColumnDimension('D')->setWidth(70);
					$doc->getActiveSheet()->getStyle('D12:D'.$doc->getActiveSheet()->getHighestRow())
					->getAlignment()->setWrapText(true)->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY));
					$doc->getActiveSheet()->getStyle('A12:C'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
				} else {
					$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
				}
				
			} else { 
				
				$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
				
			}
			
			
		}
		
		$nombre_hoja = strlen(str_replace("\"", "", $nombre_archivo)) > 31?substr(str_replace("\"", "", $nombre_archivo), 0, 28).'...':str_replace("\"", "", $nombre_archivo);
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $nombre_doc.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;

	}

	function excel_acv_report_2(){

		$tabla = $this->input->post('tabla');
		$columns_number = $this->input->post('columns_number');
		$rows_number = $this->input->post('rows_number');
		$rows_number = (int)$rows_number + 3; // -1 + 4
		$nombre_archivo = $this->input->post('filename');
		$nombre_doc = str_replace("\"", "", $nombre_archivo);
		$nombre_doc = str_replace(" ", "_", $nombre_doc);

		$nombre_tabla_html = str_replace("\"","",$this->input->post('nombre_tabla'));
		
		$tabla = json_decode($tabla, true);

		$id_cliente = str_replace("\"", "", $this->input->post('id_cliente'));
		$id_proyecto = str_replace("\"", "", $this->input->post('id_proyecto'));
		$id_subproyecto = str_replace("\"", "", $this->input->post('id_subproyecto'));
		$id_unidad_funcional = str_replace("\"", "", $this->input->post('id_unidad_funcional'));
		$fecha_desde_original = str_replace("\"", "", $this->input->post('fecha_desde'));
		$fecha_hasta_original = str_replace("\"", "", $this->input->post('fecha_hasta'));
		$fecha_desde = get_date_format($fecha_desde_original,$id_proyecto);
		$fecha_hasta = get_date_format($fecha_hasta_original,$id_proyecto);
		
		$nombre_cliente = $this->Clients_model->get_one($id_cliente)->company_name;
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
		$nombre_subproyecto = $this->Subprojects_model->get_one($id_subproyecto)->nombre;
		$nombre_unidad_funcional = $this->Functional_units_model->get_one($id_unidad_funcional)->nombre;

		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$client_info = $this->Clients_model->get_one($id_cliente);
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
		} else {
			$color_sitio = "00b393";
		}
		// ESTILOS
		$styleHeaderData = array(
			'font' => array(
				'bold' => true,
			)
		);
		
		$styleHeaderColumnsArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
		);
		
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
		$objDrawing->setHeight(30);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B2');
		$doc->getActiveSheet()->getStyle('A1:B2')->applyFromArray($styleArray);
		
		// HEADER
		$letra = $this->getNameFromNumber($columns_number - 1);
		$doc->getActiveSheet()->mergeCells('A3:B3');
		$doc->getActiveSheet()->getStyle('A3:B3')->applyFromArray($styleHeaderColumnsArray);
		$doc->getActiveSheet()->getStyle('A13:A'.$rows_number.'')->applyFromArray($styleHeaderColumnsArray);
		$doc->getActiveSheet()->getStyle('A4:A19')->applyFromArray($styleHeaderData);
		
		$doc->setActiveSheetIndex(0)
			->setCellValue('A3', str_replace("\"","",$nombre_archivo))
			->setCellValue('A4', 'Fecha')
			->setCellValue('B4', date('d/m/Y').' a las '.date('H:i:s'))
			->setCellValue('A5', 'Cliente')
			->setCellValue('B5', $nombre_cliente)
			->setCellValue('A6', 'Proyecto')
			->setCellValue('B6', $nombre_proyecto)
			->setCellValue('A7', 'Subproyecto')
			->setCellValue('B7', $nombre_subproyecto)
			->setCellValue('A8', 'Unidad Funcional')
			->setCellValue('B8', $nombre_unidad_funcional)
			->setCellValue('A9', 'Desde')
			->setCellValue('B9', $fecha_desde)
			->setCellValue('A10', 'Hasta')
			->setCellValue('B10', $fecha_hasta);
		
		$doc->setActiveSheetIndex(0);
		$remover_cabecera_tabla = array_shift($tabla);
		$doc->getActiveSheet()->fromArray($tabla, NULL,"A12");
			
		//LOGO EMPRESA
		if($nombre_tabla_html == "info_empresa-table"){
			$objDrawingLogo = new PHPExcel_Worksheet_Drawing();
			$objDrawingLogo->setName('Logo Sitio');
			$objDrawingLogo->setDescription('Logo Sitio');
			$objDrawingLogo->setPath('./'.$url_logo);
			$objDrawingLogo->setHeight(30);
			$objDrawingLogo->setOffsetY(6);
			$objDrawingLogo->setOffsetX(20);
			$objDrawingLogo->setWorksheet($doc->getActiveSheet());
			$objDrawingLogo->setCoordinates('B16');
			$doc->getActiveSheet()->getRowDimension(16)->setRowHeight(30);
		}
		
		// FILTROS
		//$doc->getActiveSheet()->setAutoFilter('A4:'.$letra.'4');
		// ANCHO COLUMNAS
		
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			
			if(($nombre_tabla_html == "info_proyecto-table")){
				
				if($cell == 'B'){
					$doc->getActiveSheet()->getColumnDimension('B')->setWidth(70);
					$doc->getActiveSheet()->getStyle('B19:B'.$doc->getActiveSheet()->getHighestRow())
					->getAlignment()->setWrapText(true); 
					$doc->getActiveSheet()->getStyle("A19")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
					$doc->getActiveSheet()->getStyle("B19")->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY));
					
				} else {
					$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
				}
				
			} else {
				
				$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
			}
			
		}
   
		//$doc->getActiveSheet()->getStyle("A19")->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
		//$doc->getActiveSheet()->getStyle("B19")->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY));
		$nombre_hoja = strlen(str_replace("\"", "", $nombre_archivo)) > 31?substr(str_replace("\"", "", $nombre_archivo), 0, 28).'...':str_replace("\"", "", $nombre_archivo);
		$doc->getActiveSheet()->setTitle($nombre_hoja);
 
		$filename= $nombre_doc.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;

	}
	
	
	function excel_indicadores(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		$puede_ver = $this->profile_access($id_usuario, 8, 9, "ver");
		
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		$id_indicador = $this->input->post('id_indicador');
		$indicador_info = $this->Indicators_model->get_one($id_indicador);

		$indicadores_cliente = $this->Client_indicators_model->get_all_where(array("id_indicador" => $id_indicador, "deleted" => 0))->result();
		$result = array();
		
		foreach($indicadores_cliente as $data){
			if($puede_ver == 1){//Todos
				$result[] = $this->_make_row_excel_indicadores($data, $id_proyecto);
			} 
			if($puede_ver == 2){ //Propios
				if($id_usuario == $data->created_by){
					$result[] = $this->_make_row_excel_indicadores($data, $id_proyecto);
				}
			}
			if($puede_ver == 3){ //Ninguno
				$result[1] = array();
			}
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
		$objDrawing->setOffsetX(30);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);
		
		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("value"), "id_tipo_campo" => "value");
		$nombre_columnas[] = array("nombre_columna" => lang("since"), "id_tipo_campo" => "since");
		$nombre_columnas[] = array("nombre_columna" => lang("until"), "id_tipo_campo" => "until");
		$nombre_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
		$nombre_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");

		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', $indicador_info->indicator_name)
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
					
					if($columna["id_tipo_campo"] == "value"){
					
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

						
					} elseif($columna["id_tipo_campo"] == "since" || $columna["id_tipo_campo"] == "until"
					|| $columna["id_tipo_campo"] == "created_date"|| $columna["id_tipo_campo"] == "modified_date"){
					
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
	
	private function _make_row_excel_indicadores($data, $id_proyecto){
		
		$id_usuario = $this->session->user_id;
		$row_data = array();
		$row_data[] = to_number_project_format($data->valor, $id_proyecto);
		$row_data[] = $f_desde = get_date_format($data->f_desde, $id_proyecto);
		$row_data[] = $f_hasta = get_date_format($data->f_hasta, $id_proyecto);
		$row_data[] = get_date_format($data->created, $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($data->modified, $id_proyecto) : "-";
		
		return $row_data;
		
	}
	
	private function make_row($data, $columnas, $id_record){
		
		$options = array("id" => $id_record);
		$record_info = $this->Forms_model->get_details($options)->row();
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;

		$row_data = array();
		//$row_data[] = $data->id;
		$datos = json_decode($data->datos, true);

		$id_formulario = $this->Form_rel_project_model->get_one_where(array("id" => $data->id_formulario_rel_proyecto, "deleted" => 0))->id_formulario;
		$formulario = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
		$datos_unidad = json_decode($formulario->unidad, true);
		$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $datos_unidad["tipo_unidad_id"], "deleted" => 0))->nombre;
		$unidad = $this->Unity_model->get_one_where(array("id" => $datos_unidad["unidad_id"], "deleted" => 0))->nombre;
		
		if($formulario->id_tipo_formulario == 1){
			
			$row_data[] = get_date_format($datos["fecha"], $id_proyecto);
			$id_categoria = $datos["id_categoria"];
			
			$categoria_original = $this->Categories_model->get_one_where(array('id' => $id_categoria, "deleted" => 0));
			$categoria_alias = $this->Categories_alias_model->get_one_where(array('id_categoria' => $id_categoria,'id_cliente' => $this->login_user->client_id, "deleted" => 0));
			
			if($categoria_alias->alias){
				$nombre_categoria = $categoria_alias->alias;
			}else{
				$nombre_categoria = $categoria_original->nombre;
			}
				
			if(!empty($nombre_categoria)){
				$row_data[] = $nombre_categoria;
			}
			
			$row_data[] = to_number_project_format($datos["unidad_residuo"], $id_proyecto);
		}
		
		if($formulario->id_tipo_formulario == 3){
			$row_data[] = $datos["fecha"] ? get_date_format($datos["fecha"], $id_proyecto) : "-";
		}
		
		if($formulario->flujo == "Consumo"){
			
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
					if($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id]) ? get_date_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id]['start_date'];
						$end_date = $arreglo_fila[$columna->id]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 10){ // ARCHIVO
						$valor_campo = ($arreglo_fila[$columna->id]) ? remove_file_prefix($arreglo_fila[$columna->id]) : '-';
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id]) ? to_number_project_format($arreglo_fila[$columna->id], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id] == "") ? '-' : $arreglo_fila[$columna->id];
					}
				} else {
					if(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}
					$valor_campo = '-';
				}
			
			$row_data[] = $valor_campo;	
			}
					
		}
		
		//$row_data[] = $data->created;
		//$row_data[] = $data->modified ? $data->modified : "-";
		//$row_data[] = time_date_zone_format($data->created,$id_proyecto);
		//$row_data[] = $data->modified ? time_date_zone_format($data->modified,$id_proyecto) : "-";
	
		if($formulario->flujo == "Residuo"){
			
			$tipo_tratamiento = $this->Tipo_tratamiento_model->get_one_where(array("id" => $datos["tipo_tratamiento"], "deleted" => 0));
			if(isset($datos["tipo_tratamiento"])){
				if($datos["tipo_tratamiento"] == $tipo_tratamiento->id){
					$row_data[] = $tipo_tratamiento->nombre;
				}
			}else{
				$row_data[] = "-";
			}
			
			if(isset($datos["fecha_retiro"])){
				$row_data[] = get_date_format($datos["fecha_retiro"], $id_proyecto);
			}else{
				$row_data[] = "-";
			}

			if(isset($datos["nombre_archivo_retiro"])){
				//$row_data[] = $datos["nombre_archivo_retiro"];	
				if($datos["nombre_archivo_retiro"]){
					$row_data[] = remove_file_prefix($datos["nombre_archivo_retiro"]);
				} else {
					$row_data[] = "-";
				}
			}else{
				$row_data[] = "-";
			}

			if(isset($datos["nombre_archivo_recepcion"])){
				//$row_data[] = $datos["nombre_archivo_recepcion"];	
				if($datos["nombre_archivo_recepcion"]){
					$row_data[] = remove_file_prefix($datos["nombre_archivo_recepcion"]);
				} else {
					$row_data[] = "-";
				}
			}else{
				$row_data[] = "-";
			}
			
		}

		$fecha_created = explode(' ', $data->created); 
		$fecha_modified = explode(' ', $data->modified);
		$row_data[] = get_date_format($fecha_created["0"], $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($fecha_modified["0"], $id_proyecto) : "-";
		
		if($record_info->id_tipo_formulario != 1){
			//array_shift($row_data);
		}
		
		return $row_data;
	}
	
	private function name_columns($columnas, $form_type = 0, $id_formulario){
		
		if($id_formulario){
			$formulario = $this->Forms_model->get_one_where(array("id" => $id_formulario, "deleted" => 0));
			$datos_unidad = json_decode($formulario->unidad,true);
			$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $datos_unidad["tipo_unidad_id"], "deleted" => 0))->nombre;
			$unidad = $this->Unity_model->get_one_where(array("id" => $datos_unidad["unidad_id"], "deleted" => 0))->nombre;
		}
		
		if($form_type == 1){
			$nom_columnas = array();
			$nom_columnas[] = array("nombre_columna" => lang("date_filed"), "id_tipo_campo" => "date_filed");
			$nom_columnas[] = array("nombre_columna" => lang("category"), "id_tipo_campo" => "category");
			$nom_columnas[] = array("nombre_columna" => $datos_unidad["nombre_unidad"]." (".$unidad.")", "id_tipo_campo" => "unity");
			
			if($formulario->flujo == "Consumo" || $formulario->flujo == "No Aplica"){
				$nom_columnas[] = array("nombre_columna" => lang("type"), "id_tipo_campo" => "type");
			}
			
			foreach($columnas as $nombre_columna){
				if(($nombre_columna->id_tipo_campo == 11)||($nombre_columna->id_tipo_campo == 12)){
					continue;
				}
				if($nombre_columna->id_tipo_campo == 15){ // UNIDAD
					$column_options = json_decode($nombre_columna->opciones, true);
					$id_unidad = $column_options[0]["id_unidad"];
					$unidad = $this->Unity_model->get_one($id_unidad);
					$nom_columnas[] = array("nombre_columna" => $nombre_columna->nombre.' ('.$unidad->nombre.')', "id_tipo_campo" => $nombre_columna->id_tipo_campo);
				} else {
					$nom_columnas[] = array("nombre_columna" => $nombre_columna->nombre, "id_tipo_campo" => $nombre_columna->id_tipo_campo);
				}
			}
			
			if($formulario->flujo == "Residuo"){
				
				$nom_columnas[] = array("nombre_columna" => lang("type_of_treatment"), "id_tipo_campo" => "type_of_treatment");
				$nom_columnas[] = array("nombre_columna" => lang("retirement_date"), "id_tipo_campo" => "retirement_date");
				$nom_columnas[] = array("nombre_columna" => lang("retirement_evidence"), "id_tipo_campo" => "retirement_evidence");
				$nom_columnas[] = array("nombre_columna" => lang("reception_evidence"), "id_tipo_campo" => "reception_evidence");

			}

			$nom_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
			$nom_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");
			
		}else{

			$nom_columnas = array();
			
			if($form_type == 3){
				$nom_columnas[] = array("nombre_columna" => lang("date"), "id_tipo_campo" => "date");
			}
			
			foreach($columnas as $nombre_columna){
				if(($nombre_columna->id_tipo_campo == 11)||($nombre_columna->id_tipo_campo == 12)){
					continue;
				}
				$nom_columnas[] = array("nombre_columna" =>  $nombre_columna->nombre, "id_tipo_campo" => $nombre_columna->id_tipo_campo);
			}
			
			$nom_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
			$nom_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");	
								
		}
		
		return $nom_columnas;
	}
	
	function excel_fixed_forms($form_type = 0, $id_record = 0){
		
		$options = array("id" => $id_record);
		$record_info = $this->Forms_model->get_details_formularios_fijos($options)->row();
		$nombre_doc = str_replace(" ","_", $record_info->nombre);
		$forms = $this->Form_types_model->get_details()->result();
		$formulario = $this->Forms_model->get_one($record_info->id);
		
		$id_usuario = $this->session->user_id;
		
		foreach($forms as $value){
			
			if(($value->id == 1)&&($form_type == $value->id)){
				// Por ahora no hay formularios fijos de tipo Registro Ambiental
			}
			if(($value->id == 2)&&($form_type == $value->id)){
				// Por ahora no hay formularios fijos de tipo Mantenedora
			}
			if(($value->id == 3)&&($form_type == $value->id)){
				
				$puede_ver = $this->profile_access($id_usuario, 4, 0, "ver"); //4: modulo otros registros

				$list_data = $this->Other_records_model->get_values_of_record_fixed_form($id_record)->result();
				$columnas = $this->Fixed_fields_model->get_all_where(array(
					"codigo_formulario_fijo" => $formulario->codigo_formulario_fijo,
					"deleted" => 0
				))->result();
				
				$nombre_columnas = $this->_name_columns_fixed_forms($columnas,$form_type, $formulario->id);
				
				$result = array();
				foreach($list_data as $data){
					if($puede_ver == 1){
						$result[] = $this->_make_row_fixed_forms($data, $columnas, $id_record);
					}
					if($puede_ver == 2){
						if($id_usuario == $data->created_by){
							$result[] = $this->_make_row_fixed_forms($data, $columnas, $id_record);
						}
					}					
					if($puede_ver == 3){
						$numero_columnas = count($columnas) + 4;
						if(is_int($numero_columnas)){
							$result[$numero_columnas] = array();
						} else {
							$result[] = $this->_make_row_fixed_forms($data, $columnas, $id_record);
						}
					}				
				}
			}
			
		}
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle($record_info->nombre)
							 ->setSubject($record_info->nombre)
							 ->setDescription($record_info->nombre)
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$client_info = $this->Clients_model->get_one($record_info->id_cliente);
		$form_rel_project = $this->Fixed_field_rel_form_rel_project_model->get_one_where(array("id_formulario" => $record_info->id, "deleted" => 0));
		
		if($client_info->id){
			if($client_info->color_sitio){
				$color_sitio = str_replace('#', '', $client_info->color_sitio);
			} else {
				$color_sitio = "00b393";
			}
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
		
		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $form_rel_project->id_proyecto);
		$hora = convert_to_general_settings_time_format($form_rel_project->id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $form_rel_project->id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', $record_info->tipo_formulario)
			->setCellValue('C2', $record_info->nombre)
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

					} elseif($columna["id_tipo_campo"] == "date_filed"){ // FECHA (PRECARGADO) (TODOS MENOS RESPONSABLE)
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
		for ($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/
		
		$nombre_hoja = strlen($record_info->nombre) > 31?substr($record_info->nombre, 0, 28).'...':$record_info->nombre;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla.'_'.$record_info->codigo.'_'.date("Y-m-d");
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
		
	}
	
	function _make_row_fixed_forms($data, $columnas, $id_record){

		$record_info = $this->Forms_model->get_one($id_record);
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_proyecto = $proyecto->id;
		
		$row_data = array();
		
		$datos = json_decode($data->datos, true);
		
		$id_formulario = $data->id_formulario;
		$formulario = $this->Forms_model->get_one($id_formulario);
		$datos_unidad = json_decode($formulario->unidad, true);
		$tipo_unidad = $this->Unity_type_model->get_one_where(array("id" => $datos_unidad["tipo_unidad_id"], "deleted" => 0))->nombre;
		$unidad = $this->Unity_model->get_one_where(array("id" => $datos_unidad["unidad_id"], "deleted" => 0))->nombre;
		
		if($data->datos){
			$arreglo_fila = json_decode($data->datos, true);
			$cont = 0;
			foreach($columnas as $columna) {
				$cont++;
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id])){
					if($columna->id_tipo_campo == 3){//si es numero.
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
						
					}elseif($columna->id_tipo_campo == 6){ // si es selección
						if($formulario->codigo_formulario_fijo == "or_unidades_funcionales"){
							if($arreglo_fila[$columna->id]){
								$id_uf = $arreglo_fila[$columna->id];
								$nombre_uf = $this->Functional_units_model->get_one($id_uf)->nombre;
								$valor_campo = $nombre_uf;
							} else {
								$valor_campo = '-';
							}
						}
						
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}elseif($columna->id_tipo_campo == 10){
						if($arreglo_fila[$columna->id]){
							$nombre_archivo = remove_file_prefix($arreglo_fila[$columna->id]);
							$valor_campo = $nombre_archivo;	
						}else{
							$valor_campo = '-';
						}
					}elseif($columna->id_tipo_campo == 15){
						$valor_campo = to_number_project_format($arreglo_fila[$columna->id], $id_proyecto);
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
		
		return $row_data;
		
	}
	
	private function _name_columns_fixed_forms($columnas, $form_type = 0, $id_formulario){
		
		$formulario = $this->Forms_model->get_one($id_formulario);
		$nom_columnas = array();
		
		if($form_type == 3){
			
			foreach($columnas as $nombre_columna){
				if(($nombre_columna->id_tipo_campo == 11)||($nombre_columna->id_tipo_campo == 12)){
					continue;
				}
				$nom_columnas[] = array("nombre_columna" => $nombre_columna->nombre, "id_tipo_campo" => $nombre_columna->id_tipo_campo);
			}
			
			$nom_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
			$nom_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");
			
		}
		
		return $nom_columnas;
		
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
	

}