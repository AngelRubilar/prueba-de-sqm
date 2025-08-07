<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_permittings extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $this->template->rander("upload_permittings/index", $view_data);
    }

	function carga_individual($id_permiso_proyecto) {
		
		//Obtener las columnas (campos y evaluados) de la matriz de cumplimiento del proyecto
		$json_string_campos = "";
		$columnas_campos = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result_array();
		
		foreach($columnas_campos as $columna){
			if($columna["id_tipo_campo"] == 1){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 2){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
			}else if($columna["id_tipo_campo"] == 3){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
			}else if($columna["id_tipo_campo"] >= 4 && $columna["id_tipo_campo"] <= 9){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 10){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
			}else if(($columna["id_tipo_campo"] == 11) || ($columna["id_tipo_campo"] == 12)){
				continue;
			}else if($columna["id_tipo_campo"] == 13 || $columna["id_tipo_campo"] == 14){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 15){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
			}else if($columna["id_tipo_campo"] == 16){
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
			}else{
				$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '"}';
			}
		}
		
		/* $json_string_evaluados = "";
		$columnas_evaluados = $this->Compromises_model->get_evaluated_of_compromise($id_compromiso_proyecto)->result_array();
		
		foreach($columnas_evaluados as $columna){
			$json_string_evaluados .= ',' . '{"title":"' . $columna["nombre_evaluado"] . '"}';
		} */
		
		$view_data["columnas_campos"] = $json_string_campos;
		//$view_data["columnas_evaluados"] = $json_string_evaluados;
		$view_data["id_permiso_proyecto"] = $id_permiso_proyecto;
		
        $this->load->view('upload_permittings/carga_individual/index', $view_data);
    }
	
	function modal_form_carga_individual($id_permiso_proyecto){
			
		$id_elemento = $this->input->post('id');
		$campos_permiso = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result_array();
		
		$view_data["campos_permiso"] = $campos_permiso;
		$view_data["id_permiso_proyecto"] = $id_permiso_proyecto;
		$view_data["Upload_permittings_controller"] = $this;

		$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
		$fases_dropdown = array();
		foreach($fases_disponibles as $fase){
			$fases_dropdown[$fase["id"]] = lang($fase["nombre_lang"]);
		}
		$view_data["fases_disponibles"] = $fases_dropdown;
		
		if($id_elemento){ //edit
			
			$model_info = $this->Values_permitting_model->get_one($id_elemento);
			$view_data['model_info'] = $model_info;
			
			$fases_decoded = json_decode($model_info->fases);
			$view_data['fases_permiso'] = $fases_decoded;
			
		} 

		$this->load->view('upload_permittings/carga_individual/modal_form', $view_data);
		
	}
	
	function save_carga_individual($id_permiso_proyecto){
		
		$id_elemento = $this->input->post('id'); //para la edición, este es el id de un elemento (valores_compromisos)
		
		$numero_permiso = $this->input->post('numero_permiso');
		$nombre_permiso = $this->input->post('nombre_permiso');
		$fases = $this->input->post('phases');
		$json_fases = json_encode($fases);
		$entidad = $this->input->post('entidad');
		//$reportabilidad = ($this->input->post('reportability')) ? 1 : 0;
		
		$array_datos = array();
		//$columnas = $this->Forms_model->get_fields_of_form($id_compromiso_proyecto)->result();
		$columnas = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
		
		foreach($columnas as $columna){
			
			// VERIFICO SI EL CAMPO EN LOOP VIENE DESHABILITADO
			$deshabilitado = $columna->habilitado;
			$default_value = $columna->default_value;
			
			if($columna->id_tipo_campo == 5){

				if($deshabilitado){
					$array_datos[$columna->id_campo] = json_decode($default_value, true);
				}else{
					$json_name = $columna->html_name;
					$array_name = json_decode($json_name, true);
					$start_name = $array_name["start_name"];
					$end_name = $array_name["end_name"];
					
					$array_datos[$columna->id_campo] = array(
						"start_date" => $this->input->post($start_name),
						"end_date" => $this->input->post($end_name)
					);
				}
				
			} else if($columna->id_tipo_campo == 11){
				//CAMPO TIPO TEXTO FIJO NO SE GUARDA
			} else {

				if($deshabilitado){
					$array_datos[$columna->id_campo] = $default_value;
				}else{
					$array_datos[$columna->id_campo] =  $this->input->post($columna->html_name);
				}
			}
			
			// cuando sea periodo
			/* if($columna->id_tipo_campo == 5){
				$json_name = $columna->html_name;
				$array_name = json_decode($json_name, true);
				$start_name = $array_name["start_name"];
				$end_name = $array_name["end_name"];
				
				$array_datos[$columna->id] = array(
					"start_date" => $this->input->post($start_name),
					"end_date" => $this->input->post($end_name)
				);
			}else if($columna->id_tipo_campo == 10){ //archivo
				$array_datos[$columna->id] = $this->input->post($columna->html_name);
				$array_files[$columna->id] = $this->input->post($columna->html_name);
				
			}else{
				$array_datos[$columna->id] = $this->input->post($columna->html_name);
			} */	
		}

		$json_datos = json_encode($array_datos);

		$data = array(
			"id_permiso" => $id_permiso_proyecto,
			"numero_permiso" => $numero_permiso,
			"nombre_permiso" => $nombre_permiso,
			"fases" => $json_fases,
			"entidad" => $entidad,
			//"reportabilidad" => $reportabilidad,
            "datos_campos" => $json_datos, 
        );
		
		if($id_elemento){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}

		$save_id = $this->Values_permitting_model->save($data, $id_elemento);
		
		if ($save_id) {
			$columnas = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
            echo json_encode(array("success" => true, "data" => $this->_row_data_carga_individual($save_id, $columnas, $id_permiso_proyecto), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}

	function list_data_carga_individual($id_permiso_proyecto = 0){
		
		$options = array(
			"id_permiso" => $id_permiso_proyecto
		);
		
		$list_data = $this->Values_permitting_model->get_details($options)->result();
		$columnas = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
		$result = array();
		 
		foreach($list_data as $data) {
			$result[] = $this->_make_row_carga_individual($data, $columnas, $id_permiso_proyecto);
		}
		
		echo json_encode(array("data" => $result));	
	}
	
	function _row_data_carga_individual($id, $columnas, $id_permiso_proyecto){
		
		$options = array(
            "id" => $id
        );

		$data = $this->Values_permitting_model->get_details($options)->row();

        return $this->_make_row_carga_individual($data, $columnas, $id_permiso_proyecto);
		
	}
	
	function _make_row_carga_individual($data, $columnas, $id_permiso_proyecto){
		
		$id_proyecto = $this->Permitting_model->get_one($id_permiso_proyecto)->id_proyecto;
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->numero_permiso;
		$row_data[] = $data->nombre_permiso;
		
		$fases_decoded = json_decode($data->fases);
		$html_fases = "";
		foreach($fases_decoded as $id_fase){
			$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
			$nombre_fase = lang($nombre_lang);
			$html_fases .= "&bull; " . $nombre_fase . "<br>";
		}
		
		$row_data[] = $html_fases;
		$row_data[] = $data->entidad;
		//$row_data[] = ($data->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
		
		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if(($columna->id_tipo_campo == 1) || ($columna->id_tipo_campo == 3) || ($columna->id_tipo_campo == 6)
						|| ($columna->id_tipo_campo == 7) || ($columna->id_tipo_campo == 8) || ($columna->id_tipo_campo == 13)
						|| ($columna->id_tipo_campo == 15)){
						if($arreglo_fila[$columna->id_campo]){
							$valor_campo = $arreglo_fila[$columna->id_campo];
						}else{
							$valor_campo = '-';
						}
					}elseif($columna->id_tipo_campo == 2){ // Si es text area
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					}elseif($columna->id_tipo_campo == 4){//si es fecha.
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? get_date_format($arreglo_fila[$columna->id_campo],$id_proyecto) : "-";
					}elseif($columna->id_tipo_campo == 5){// si es periodo
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						if($start_date || $end_date){
							$start_date = get_date_format($start_date, $id_proyecto);
							$end_date = get_date_format($end_date, $id_proyecto);
							$valor_campo = $start_date.' - '.$end_date;
						} else {
							$valor_campo =  '-';
						}
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}elseif($columna->id_tipo_campo == 14){
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]) : "-";
					}
					else{
						$valor_campo = $arreglo_fila[$columna->id_campo];
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
		
		$view = modal_anchor(get_uri("upload_permittings/preview/" .$id_permiso_proyecto), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang('view_permitting'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("upload_permittings/modal_form_carga_individual/".$id_permiso_proyecto), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_permitting'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_permitting'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("upload_permittings/delete"), "data-action" => "delete-confirmation"));
		
		$row_data[] = $view . $edit . $delete;
		
		return $row_data;
		
	}
	
	function carga_masiva($id_permiso_proyecto) {
		
		$id_permiso = $id_permiso_proyecto;
		$permiso = $this->Permitting_model->get_one($id_permiso);
		
		$id_proyecto = $permiso->id_proyecto;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_cliente = $proyecto->client_id;
		
		$excel_template = $this->get_excel_template_of_permitting($id_permiso_proyecto, $id_cliente, $id_proyecto);
		
		$view_data["id_cliente"] = $id_cliente;
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["id_permiso"] = $id_permiso;
		$view_data["excel_template"] = $excel_template;
		
        $this->load->view('upload_permittings/carga_masiva/index', $view_data);
    }
	
	function save_carga_masiva(){
		
		$id_cliente = $this->input->post('id_cliente');
		$id_proyecto = $this->input->post('id_proyecto');
		$id_permiso_proyecto = $this->input->post('id_permiso');
		
		$file = $this->input->post('archivo_importado');

		$archivo_subido = move_temp_file($file, "files/carga_masiva_permisos/client_".$id_cliente."/project_".$id_proyecto."/", "", "", $file);
		
		if($archivo_subido){
			
			$this->load->library('excel');
			
			$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();
			
			// COMPROBACION DE DATOS CORRECTOS
			$num_errores = 0;
			$msg_obligatorio = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_obligatory_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_formato = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_format_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_columna = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_column_field').'"><i class="fa fa-question-circle"></i></span>';
			$msg_date_range = '<span class="help" data-container="body" data-toggle="tooltip" title="" data-original-title="'.lang('bulk_load_invalid_date_range_field').'"><i class="fa fa-question-circle"></i></span>';
			
			$campos_permiso = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
			
			$html = '<table class="table table-responsive table-striped">';
			$html .= '<thead><tr>';
			$html .= '<th></th>';
			
			$valor_columna_a1 = $worksheet->getCell('A1')->getValue();
			$valor_columna_b1 = $worksheet->getCell('B1')->getValue();
			$valor_columna_c1 = $worksheet->getCell('C1')->getValue();
			//$valor_columna_d1 = $worksheet->getCell('D1')->getValue();
						
			/*if($valor_columna_a1 != lang('name')){ $num_errores++; var_dump("error a1 :D");}
			if($valor_columna_b1 != lang('phases')){ $num_errores++; var_dump("error b1 :D");}
			if($valor_columna_c1 != lang('reportability')){ $num_errores++; var_dump("error c1 :D");}
			
			exit();*/
			//$cont = 0;
			
			if(lang('permitting_number') == $worksheet->getCell('A1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('A1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('A1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if(lang('name') == $worksheet->getCell('B1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('B1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('B1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if(lang('phases') == $worksheet->getCell('C1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('C1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			if(lang('entity') == $worksheet->getCell('D1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('D1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			
			/*
			if(lang('reportability') == $worksheet->getCell('D1')->getValue()){
					$html .= '<th>'.$worksheet->getCell('D1')->getValue().'</th>';
			}else{
				$html .= '<th class="error app-alert alert-danger">'.$worksheet->getCell('C1')->getValue().' '.$msg_columna.'</th>';
				$num_errores++;
			}
			*/
			
			//$cont = 4;
			$cont = 4;
			
			foreach($campos_permiso as $campo){
				
				if($campo->id_tipo_campo == 10 || $campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.'1')->getValue();
				//var_dump($campo->nombre_campo);
				//var_dump($valor_columna);
				//echo "se compara valor excel:".$valor_columna." con valor base de datos:".$campo->nombre."<br>";
				if($campo->nombre_campo == $valor_columna){
					$html .= '<th>'.$valor_columna.'</th>';
				}else{
					$html .= '<th class="error app-alert alert-danger">'.$valor_columna.' '.$msg_columna.'</th>';
					$num_errores++;
				}
				$cont++;
			}
			
			$html .= '</tr></thead>';
			$html .= '<tbody>';
			
			// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ
			$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
			$array_fases_disponibles = array();
			foreach($fases_disponibles as $fase){
				$array_fases_disponibles[] = lang($fase["nombre_lang"]);
			}
			
			// DATOS DEL CUERPO
			for($row = 2; $row <= $lastRow; $row++){
				$html .= '<tr>';
				$html .= '<td>'.$row.'</td>';
				
				//NUMERO PERMISO
				$numero_permiso = $worksheet->getCell('A'.$row)->getValue();
				if(strlen(trim($numero_permiso)) > 0){

					if(is_numeric($numero_permiso)){
						$html .= '<td>'.$numero_permiso.'</td>';
					}else{
						$html .= '<td class="error app-alert alert-danger">'.$numero_permiso.' '.$msg_formato.'</td>';
						$num_errores++;
					}

				}else{
					$html .= '<td class="error app-alert alert-danger">'.$numero_permiso.' '.$msg_obligatorio.'</td>';
					$num_errores++;
				}
				
				// CELDA NOMBRE
				$nombre_permiso = $worksheet->getCell('B'.$row)->getValue();
				if(strlen(trim($nombre_permiso)) > 0){
					$html .= '<td>'.$nombre_permiso.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$nombre_permiso.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				
				// CELDA FASES
				$fases = $worksheet->getCell('C'.$row)->getValue();
				$array_fases = explode(',', $fases);
				$array_fases_final = array();
				$error_fases = FALSE;
				
				foreach($array_fases as $nombre_fase){
					$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
					if(!in_array($nombre_fase_limpia, $array_fases_disponibles)){
						$error_fases = TRUE;
					}
					$array_fases_final[] = $nombre_fase_limpia;
				}
				
				$html_fases = "";
				foreach($array_fases_final as $nombre_fase){
					$html_fases .= "&bull; " . $nombre_fase . "<br>";
				}
				
				if(!$error_fases){
					$html .= '<td>'.$html_fases.'</td>';
				} else {
					$html .= '<td class="error app-alert alert-danger">'.$html_fases.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				
				// CELDA ENTIDAD
				$entidad = $worksheet->getCell('D'.$row)->getValue();
				if(strlen(trim($entidad)) > 0){
					$html .= '<td>'.$entidad.'</td>';
				}else{
					$html .= '<td class="error app-alert alert-danger">'.$entidad.' '.$msg_formato.'</td>';
					$num_errores++;
				}
					
				// CELDA REPORTABILIDAD
				
				/*
				$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
				$reportabilidad_mayus = strtoupper($reportabilidad);
				
				if($reportabilidad_mayus == "SI"){
					$html .= '<td><i class="fa fa-check" aria-hidden="true"></i></td>';
				} else if($reportabilidad_mayus == "NO"){
					$html .= '<td><i class="fa fa-times" aria-hidden="true"></i></td>';
				} else {
					$html .= '<td class="error app-alert alert-danger">'.$reportabilidad.' '.$msg_formato.'</td>';
					$num_errores++;
				}
				*/
				
				// OTRAS CELDAS
				//$cont = 4;
				$cont = 4;
				foreach($campos_permiso as $campo){
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
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
						
						
					}
					if($campo->id_tipo_campo == 7){//select_multiple
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
							$opciones[] = $op->text;
						}
						
						if(in_array($valor_columna, $opciones)){
							$html .= '<td>'.$valor_columna.'</td>';
						}else{
							$html .= '<td class="error app-alert alert-danger">'.$valor_columna.' '.$msg_formato.'</td>';
							$num_errores++;
						}
					}
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
						// OJO CON ESTE, DEPENDE DEL FORMATO DE HORA
						
						if($campo->obligatorio){
							//if(strlen($valor_columna) == 8){// 12:00 PM
							if(strlen($valor_columna) == 5){// 12:00 PM
								//if(preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
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
							//if($valor_columna == "" || preg_match('/\d{2}:\d{2} (AM|PM)/', $valor_columna)){
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
					
					$cont++;
				}

				$html .= '</tr>';

			}
			
			$html .= '</tbody>';
			$html .= '</table>';			

			if($num_errores > 0){
				echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed'), 'table' => $html));
			}else{
				$this->bulk_load($id_cliente, $id_proyecto, $id_permiso_proyecto, $archivo_subido);
				//echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'table' => $html));
			}
			
			exit();

			
		}
		
	}
	
	function bulk_load($id_cliente, $id_proyecto, $id_permiso_proyecto, $archivo_subido){
		
		$permiso = $this->Permitting_model->get_one($id_permiso_proyecto);

		$excelReader = PHPExcel_IOFactory::createReaderForFile(__DIR__.'/../../files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$excelObj = $excelReader->load(__DIR__.'/../../files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$archivo_subido);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();
		$campos_permiso = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
		$array_insert = array();
		
		// CREAR ARREGLO DE LAS FASES DEL SISTEMA 1 SOLA VEZ, CON LOS LANG
		$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
		$array_fases_disponibles = array();
		foreach($fases_disponibles as $fase){
			$array_fases_disponibles[lang($fase["nombre_lang"])] = $fase["id"];
		}
		
		// POR CADA FILA
		for($row = 2; $row <= $lastRow; $row++){
			
			$array_row = array();
			$numero_permiso = (int)$worksheet->getCell('A'.$row)->getValue();
			$nombre_permiso = $worksheet->getCell('B'.$row)->getValue();
			
			//
			$fases = $worksheet->getCell('C'.$row)->getValue();
			$array_fases = explode(',', $fases);
			$array_fases_final = array();
			
			foreach($array_fases as $nombre_fase){
				$nombre_fase_limpia = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $nombre_fase)));
				$id_fase = $array_fases_disponibles[$nombre_fase_limpia];
				$array_fases_final[] = $id_fase;
			}
			$array_fases_json = json_encode($array_fases_final);
			//
			
			$entidad = $worksheet->getCell('D'.$row)->getValue();
			
			/*
			$reportabilidad = $worksheet->getCell('D'.$row)->getValue();
			$reportabilidad_mayus = strtoupper($reportabilidad);
			
			if($reportabilidad_mayus == "SI"){
				$reportabilidad = 1;
			} else {
				$reportabilidad = 0;
			}
			*/
			
			$array_campos_json = array();
			
			//$cont = 4;
			$cont = 4;
			foreach($campos_permiso as $campo){
				
				if($campo->id_tipo_campo == 11 || $campo->id_tipo_campo == 12){
					continue;
				}
				if($campo->id_tipo_campo == 10){// ARCHIVO (DEBE IR SI O SI EL ID DEL CAMPO, POR LO QUE LO AGREGAREMOS VACIO)
					$array_campos_json["$campo->id_campo"] = NULL;
					continue;
				}
				
				$letra_columna = $this->getNameFromNumber($cont);
				$valor_columna = $worksheet->getCell($letra_columna.$row)->getValue();
				//echo var_dump($letra_columna.$row.' - '.$campo->id_tipo_campo.': '.$valor_columna);
				
				if($campo->id_tipo_campo == 1 || $campo->id_tipo_campo == 2 || $campo->id_tipo_campo == 3 || $campo->id_tipo_campo == 4){
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					// CAMPO DESHABILITADO = 1
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 5){
					if($campo->obligatorio){
						$array_periodo = explode("/", $valor_columna);
						$fecha_desde = $array_periodo[0];
						$fecha_hasta = $array_periodo[1];
						$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
					}else{
						/*if(trim($valor_columna) == ""){
							$json_periodo = array("start_date" => "", "end_date" => "");
						}else{
							$array_periodo = explode("/", $valor_columna);
							$fecha_desde = $array_periodo[0];
							$fecha_hasta = $array_periodo[1];
							$json_periodo = array("start_date" => $fecha_desde, "end_date" => $fecha_hasta);
						}*/
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
					
					$array_campos_json["$campo->id_campo"] = $json_periodo;
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
					
					$array_campos_json["$campo->id_campo"] = $opciones[$valor_columna];*/
					
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
					
				}
				if($campo->id_tipo_campo == 8){// RUT
					//$array_campos_json["$campo->id_campo"] = $valor_columna;
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 9){// RADIO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 13){// CORREO
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 14){// HORA
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				if($campo->id_tipo_campo == 15){// UNIDAD
					if($campo->habilitado == 1){
						$array_campos_json["$campo->id_campo"] = $campo->default_value;
					}else{
						$array_campos_json["$campo->id_campo"] = $valor_columna;
					}
				}
				
				$cont++;
			}
			
			$array_row["id_permiso"] = $id_permiso_proyecto;
			$array_row["numero_permiso"] = $numero_permiso;
			$array_row["nombre_permiso"] = $nombre_permiso;
			$array_row["fases"] = $array_fases_json;
			$array_row["entidad"] = $entidad;
			//$array_row["reportabilidad"] = $reportabilidad;
			$json_datos_campos = json_encode($array_campos_json);
			$array_row["datos_campos"] = $json_datos_campos;
			$array_row["created_by"] = $this->login_user->id;
			$array_row["modified_by"] = NULL;
			$array_row["created"] = get_current_utc_time();
			$array_row["modified"] = NULL;
			$array_row["deleted"] = 0;
			
			$array_insert[] = $array_row;
		}// FIN FOR ROW
		
	
		$bulk_load = $this->Values_permitting_model->bulk_load($array_insert);
		if($bulk_load){
			echo json_encode(array("success" => true, 'message' => lang('bulk_load_records_saved'), 'carga' => true));
		}else{
			echo json_encode(array("success" => false, 'message' => lang('bulk_load_failed_load'), 'carga' => true));
		}
		
	}
	
	
	function preview($id_record = 0){
		
		$id_permiso_proyecto = $id_record;
		$id_elemento = $this->input->post('id');
		$campos_permiso = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result_array();
		
		$view_data['campos_permiso'] = $campos_permiso;
		$view_data['id_permiso'] = $id_record;
		
		if($id_elemento){
			$model_info = $this->Values_permitting_model->get_one($id_elemento);
			$view_data['model_info'] = $model_info;
			
			$fases_decoded = json_decode($model_info->fases);
			$html_fases = "";
			foreach($fases_decoded as $id_fase){
				$nombre_lang = $this->Phases_model->get_one($id_fase)->nombre_lang;
				$nombre_fase = lang($nombre_lang);
				$html_fases .= "&bull; " . $nombre_fase . "<br>";
			}
			$view_data['html_fases'] = $html_fases;
		}
		
		$view_data["Upload_permittings_controller"] = $this;

        $this->load->view('upload_permittings/carga_individual/view', $view_data);
		
	}
	
		
	function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		//SI EL PERMISO TIENE EVALUACION NO SE PUEDE ELIMINAR
		$evaluaciones_permiso = $this->Permitting_procedure_evaluation_model->get_all_where(array(
			"id_valor_permiso" => $id,
			"deleted" => 0
		))->result_array();
		
		if($evaluaciones_permiso){
			echo json_encode(array("success" => false, 'message' => lang('cant_delete_permission')));
			exit();
		}
		
        if ($this->input->post('undo')) {
            if ($this->Values_permitting_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Values_permitting_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	/* devolver dropdown con los proyectos de un cliente */	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente, "deleted" => 0));
		
		$html = '';
		$html .= '<div class="col-md-4 p0">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden col-md-12' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
				
	}
	
	function get_upload_permittings_of_project(){
		
		$id_cliente = $this->input->post("id_cliente");
		$id_proyecto = $this->input->post("id_proyecto");
		$view_data["nombre_proyecto"] = $this->Projects_model->get_one($id_proyecto)->title;
		$view_data["id_permiso_proyecto"] = $this->Permitting_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		
		$this->load->view("upload_permittings/upload_permittings_of_project", $view_data);
		
	}
	
	function get_field($id_campo, $id_elemento, $preview){
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		$array_opciones = json_decode($opciones, true);
		$options = array();
		foreach($array_opciones as $opcion){
			$options[$opcion['value']] = $opcion['text'];
		}
		
		$obligatorio = $datos_campo->obligatorio;
		$habilitado = $datos_campo->habilitado;

		if($id_elemento){
			$row_elemento = $this->Values_permitting_model->get_details(array("id" => $id_elemento))->result();
			$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
			$default_value = $decoded_default[$id_campo];
			
			if($id_tipo_campo == 5){
				$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
				$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			}
			if($id_tipo_campo == 11){
				$default_value = $datos_campo->default_value;
			}
			if($id_tipo_campo == 7){
				$default_value_multiple = (array)$default_value;
			}
			
			if($id_tipo_campo == 16){
					
				$datos_mantenedora = json_decode($datos_campo->default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}
			
			}
	
			
		}else{
			if($id_tipo_campo == 5){
				if($default_value){
					$default_value1 = json_decode($default_value)->start_date?json_decode($default_value)->start_date:"";
					$default_value2 = json_decode($default_value)->end_date?json_decode($default_value)->end_date:"";
				}else{
					$default_value1 = "";
					$default_value2 = "";
				}
			}else if($id_tipo_campo == 7){
				$default_value_multiple = array();
				//var_dump(json_decode($default_value, true));exit();
				foreach(json_decode($default_value, true) as $value){
					$default_value_multiple[] = $value;
				}
				
			}else{
				
			}
			
			if($id_tipo_campo == 16){
				
				$datos_mantenedora = json_decode($default_value, true);
				$id_mantenedora = $datos_mantenedora['mantenedora'];
				$id_field_label = $datos_mantenedora['field_label'];
				$id_field_value = $datos_mantenedora['field_value'];
				
				$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
				
				$array_opciones = array();
				foreach($datos as $index => $row){
					$fila = json_decode($row->datos, true);
					$label = $fila[$id_field_label];
					$value = $fila[$id_field_value];
					$array_opciones[$value] = $label;
				}

			}
			
		}
		
		//Input text
		if($id_tipo_campo == 1){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "255"
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"style" => "height:150px;",
				"autocomplete" => "off",
				"autocomplete"=> "off",
				"maxlength" => "2000"
			);
			
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_textarea($datos_campo);
		}
		
		//Número
		if($id_tipo_campo == 3){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer")
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			
			$name = json_decode($name, true);
			$name1 = $name['start_name'];
			$name2 = $name['end_name'];
			
			$datos_campo1 = array(
				"id" => $name1,
				"name" => $name1,
				"value" => $default_value1,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"autocomplete" => "off",
			);
			
			$datos_campo2 = array(
				"id" => $name2,
				"name" => $name2,
				"value" => $default_value2,
				"class" => "form-control datepicker",
				"placeholder" => "YYYY-MM-DD",
				"data-rule-greaterThanOrEqual" => "#".$name1,
				"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo1['data-rule-required'] = true;
				$datos_campo1['data-msg-required'] = lang("field_required");
				$datos_campo2['data-rule-required'] = true;
				$datos_campo2['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo1['disabled'] = true;
				$datos_campo2['disabled'] = true;
			}
			
			
			$html = '<div class="col-md-6">';
			$html .= form_input($datos_campo1);
			$html .= '</div>';
			$html .= '<div class="col-md-6">';
			$html .= form_input($datos_campo2);
			$html .= '</div>';
		}
		
		//Selección
		if($id_tipo_campo == 6){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, $options, $default_value, "id='$name' class='select2 validate-hidden' $extra");
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_multiselect($name."[]", $options, $default_value_multiple, "id='$name' class='select2 validate-hidden' $extra multiple");

		}
		
		//Rut
		if($id_tipo_campo == 8){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
				"data-rule-minlength" => 6,
				"data-msg-minlength" => lang("enter_minimum_6_characters"),
				"data-rule-maxlength" => 13,
				"data-msg-maxlength" => lang("enter_maximum_13_characters"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			
			$html = '';
			$cont = 0;
			foreach($options as $value => $label){
				$cont++;
				
				$html .= '<div class="col-md-6">';
				$html .= $label;
				$html .= '</div>';
				
				$html .= '<div class="col-md-6">';
				$datos_campo = array(
					"id" => $name.'_'.$cont,
					"name" => $name,
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
			
			
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value){
				
				if($preview){
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
					
				} else {
					
					$html = '<div class="col-md-8">';
					$html .= $default_value;
					$html .= '</div>';
					
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-obligatorio" => $obligatorio, "data-id" => $id_elemento, "data-campo" => $id_campo, "data-action-url" => get_uri("environmental_records/delete_file"), "data-action" => "delete-confirmation"));
					$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				}
				
				
			}else{
				
				$html = $this->load->view("includes/form_file_uploader", array(
					"upload_url" =>get_uri("fields/upload_file"),
					"validation_url" =>get_uri("fields/validate_file"),
					"html_name" => $name,
					"obligatorio" => $obligatorio?'data-rule-required="1" data-msg-required="'.lang("field_required").'"':"",
					"id_campo" => $id_campo,
					//"preimagen" => $default_value
				),
				true);
			}
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"autocomplete"=> "off",
				"maxlength" => "255",
				"data-rule-email" => true,
				"data-msg-email" => lang("enter_valid_email"),
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
			
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control timepicker",
				//"placeholder" => "YYYY-MM-DD",
				"placeholder" => $etiqueta,
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			
			$html = form_input($datos_campo);
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			
			//$simbolo = $array_opciones[0]["symbol"];
			$id_simbolo = $array_opciones[0]["id_unidad"];
			$simbolo = $this->Unity_model->get_one($id_simbolo);
			
			$html = '';
			$html .= '<div class="col-md-10 p0">';
			$datos_campo = array(
				"id" => $name,
				"name" => $name,
				"value" => $default_value,
				"class" => "form-control",
				"placeholder" => $etiqueta,
				"data-rule-number" => true,
				"data-msg-number" => lang("enter_a_integer"),
				"autocomplete" => "off",
			);
			if($obligatorio){
				$datos_campo['data-rule-required'] = true;
				$datos_campo['data-msg-required'] = lang("field_required");
			}
			if($habilitado){
				$datos_campo['disabled'] = true;
			}
			$html .= form_input($datos_campo);
			$html .= '</div>';
			$html .= '<div class="col-md-2">';
			$html .= $simbolo->nombre;
			$html .= '</div>';
		
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			/* $datos_mantenedora = json_decode($default_value, true);
			$id_mantenedora = $datos_mantenedora['mantenedora'];
			$id_field_label = $datos_mantenedora['field_label'];
			$id_field_value = $datos_mantenedora['field_value'];
			
			$datos = $this->Values_model->get_details(array("id_formulario" => $id_mantenedora))->result();
			
			$array_opciones = array();
			foreach($datos as $index => $row){
				$fila = json_decode($row->datos, true);
				$label = $fila[$id_field_label];
				$value = $fila[$id_field_value];
				$array_opciones[$value] = $label;
			} */
			
			//var_dump($array_opciones);
			
			$extra = "";
			if($obligatorio){
				$extra .= " data-rule-required='true', data-msg-required='".lang('field_required')."'";
			}
			if($habilitado){
				$extra .= " disabled";
			}
			
			$html = form_dropdown($name, array("" => "-") + $array_opciones, $default_value, "id='$name' class='select2 validate-hidden' $extra");
			
		}
		
		return $html;

	}
	
	function get_field_value($id_campo, $id_elemento) {
		
		$id_permiso = $this->Values_permitting_model->get_one($id_elemento)->id_permiso;
		$id_proyecto = $this->Permitting_model->get_one($id_permiso)->id_proyecto;
		
        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$datos_campo = $this->Fields_model->get_one($id_campo);
		$id_tipo_campo = $datos_campo->id_tipo_campo;
		$etiqueta = $datos_campo->nombre;
		$name = $datos_campo->html_name;
		$default_value = $datos_campo->default_value;
		
		$opciones = $datos_campo->opciones;
		$array_opciones = json_decode($opciones, true);
		$options = array();
		foreach($array_opciones as $opcion){
			$options[$opcion['value']] = $opcion['text'];
		}
		
		$row_elemento = $this->Values_permitting_model->get_details(array("id" => $id_elemento))->result();
		$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
		
		$default_value = $decoded_default[$id_campo];
		if($id_tipo_campo == 5){
			$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
			$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
			$default_value = $default_value1.' - '.$default_value2;
		}
		if($id_tipo_campo == 11){
			$default_value = $datos_campo->default_value;
		}
		if($id_tipo_campo == 7){
			$default_value_multiple = (array)$default_value;
		}
		
		
		//Input text
		if($id_tipo_campo == 1){
			$html = $default_value;
		}
		
		//Texto Largo
		if($id_tipo_campo == 2){
			$html = $default_value;
		}
		
		//Número
		if($id_tipo_campo == 3){
			$html = $default_value;
		}
		
		//Fecha
		if($id_tipo_campo == 4){
			$html = get_date_format($default_value,$id_proyecto);
		}
		
		//Periodo
		if($id_tipo_campo == 5){
			 $html = $default_value;
		}
		
		//Selección
		if($id_tipo_campo == 6){
			$html = $default_value;// es el value, no el text
		}
		
		//Selección Múltiple
		if($id_tipo_campo == 7){
			$html = implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
		}
		
		//Rut
		if($id_tipo_campo == 8){
			$html = $default_value;
		}
		
		//Radio Buttons
		if($id_tipo_campo == 9){
			$html = $default_value;
			//$html = $value;// es el value, no la etiqueta
		}
		
		//Archivo
		if($id_tipo_campo == 10){
			
			if($default_value ){
				
				$html = '<div class="col-md-8">';
				$html .= $default_value;
				$html .= '</div>';
				
				$html .= '<div class="col-md-4">';
				$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
				$html .= '<tbody><tr><td class="option text-center">';
				$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
				$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
				$html .= '</td>';
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '</table>';
				$html .= '</div>';
				
			} else {
				
				$html = '<div class="col-md-8">';
				$html .= '-';
				$html .= '</div>';
			}
			
			
			
		}
		
		//Texto Fijo
		if($id_tipo_campo == 11){
			$html = $default_value;
		}
		
		//Divisor: Se muestra en la vista
		if($id_tipo_campo == 12){
			$html = "<hr>";
		}
		
		//Correo
		if($id_tipo_campo == 13){
			$html = $default_value;
		}
		
		//Hora
		if($id_tipo_campo == 14){
			$html = convert_to_general_settings_time_format($id_proyecto, $default_value);
		}
		
		///Unidad
		if($id_tipo_campo == 15){
			$simbolo = $array_opciones[0]["symbol"];
			$html = $default_value?$default_value:"-".' '.$simbolo;
		}
		
		//Selección desde Mantenedora
		if($id_tipo_campo == 16){
			
			$html = $default_value;
			
		}
		
		if($html == ""){$html = "-";}
		
		return $html;

    }
	
	function get_excel_template_of_permitting($id_permiso_proyecto, $id_cliente, $id_proyecto){
		
		$columnas_campos = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result_array();
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;
		$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('permitting_template_excel').'_'.date("Y-m-d");
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle(lang("template_permitting"))
							 ->setSubject(lang("template_permitting"))
							 ->setDescription(lang("template_permitting"))
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		$doc->setActiveSheetIndex(0);
		
		// CREAR HOJA PARA OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN
		$doc->createSheet();
		// usar nueva hoja
		$doc->setActiveSheetIndex(1);
		//$doc->getActiveSheet()->setCellValue('A1', 'More data');
		$doc->getActiveSheet()->setTitle('options');
		//volver a usar la primera hoja
		$doc->setActiveSheetIndex(0);
		
		//$columna = 0;
		//$columna = 4;
		$columna = 4;
		
		$doc->getActiveSheet()->setCellValue('A1', lang('permitting_number'));
		$doc->getActiveSheet()->setCellValue('B1', lang('name'));
		$doc->getActiveSheet()->setCellValue('C1', lang('phases'));
		$doc->getActiveSheet()->setCellValue('D1', lang('entity'));
		//$doc->getActiveSheet()->setCellValue('D1', lang('reportability'));
		
		foreach($columnas_campos as $cc){
			
			if($cc["id_tipo_campo"] == 10 || $cc["id_tipo_campo"] == 11 || $cc["id_tipo_campo"] == 12){
				continue;
			}
			
			$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'1', $cc["nombre_campo"]);	
			
			if($cc["default_value"] && $cc["id_tipo_campo"] != 16){ //SI EL CAMPO TIENE VALOR POR DEFECTO Y NO ES SELECCIÓN DESDE MANTENEDORA
			
				if($cc["id_tipo_campo"] == 5){	
					$periodo = json_decode($cc["default_value"]);
					$valor_por_defecto = $periodo->start_date."/".$periodo->end_date;
				} else {
					$valor_por_defecto = $cc["default_value"];
				}
				
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
				$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
				$comentario->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("default_value_field") . ": ")->getFont()->setBold(true);
				$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun($valor_por_defecto);
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				}

			} else if(!$cc["default_value"] && $cc["id_tipo_campo"] != 16){
				
				if($cc["habilitado"]){ //SI EL CAMPO ESTÁ DESHABILITADO
						
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
					$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
					$comentario->getFont()->setBold(true);
				
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
					$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_disabled"))->getFont()->setBold(true);

					if($cc["obligatorio"]){
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					} else {
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					}
					
				} else { //SI EL CAMPO ESTÁ HABILITADO
					
					if($cc["obligatorio"]){
						
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setAuthor('Mimasoft');
						$comentario = $doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(lang("info"));
						$comentario->getFont()->setBold(true);
					
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun("\r\n");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->getText()->createTextRun(' - ' . lang("field_is_required"))->getFont()->setBold(true);
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setWidth("300px");
						$doc->getActiveSheet()->getComment($this->getNameFromNumber($columna).'1')->setHeight("100px");
					
					}
					
				}
	
			}			
			
					
			$columna++;
		}
		
		$columna = 4;
		
		// LISTA DEMO DE FASES A PARTIR DE LAS FASES REALES DEL SISTEMA
		$fases_disponibles = $this->Phases_model->get_all_where(array("deleted" => 0))->result_array();
		$array_fases = array();
		foreach($fases_disponibles as $fase){
			$array_fases[] = lang($fase["nombre_lang"]);
		}
		
		$doc->getActiveSheet()->setCellValueExplicit('A2', lang('excel_number_example'), PHPExcel_Cell_DataType::TYPE_STRING);
		$doc->getActiveSheet()->setCellValue('B2', lang('excel_name_example'));
		//$doc->getActiveSheet()->setCellValue('C2', lang('excel_phases_example'));
		$doc->getActiveSheet()->setCellValue('C2', implode(', ', $array_fases));
		$doc->getActiveSheet()->setCellValue('D2', lang('entity_excel_permitting_example'));
		//$doc->getActiveSheet()->setCellValue('D2', lang('excel_reportability_example'));
		
		//$options = array("id_compromiso" => $columnas_campos["id_compromiso"]);
		//$list_data = $this->Values_compromises_model->get_details($options)->result();
		
		$columna_opciones = 0; //A
		
		foreach($columnas_campos as $campo){
			
			if($campo["id_tipo_campo"] == 10 || $campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
				continue;
			}
			
			if($campo["id_tipo_campo"] == 1){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_text'));
			}
			if($campo["id_tipo_campo"] == 2){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_textarea'));
			}
			if($campo["id_tipo_campo"] == 3){
				$numero_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_number');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $numero_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			if($campo["id_tipo_campo"] == 4){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_date'));
			}
			if($campo["id_tipo_campo"] == 5){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_period'));
			}
			
			if($campo["id_tipo_campo"] == 6){
				$datos_campo = json_decode($campo["opciones"]);
				$array_opciones = array();
				foreach($datos_campo as $row){
					$label = $row->text;
					$value = $row->value;
					$array_opciones[] = $label;
				}
				array_shift($array_opciones);
				
				//GUARDO OPCIONES DE LOS CAMPOS DE TIPO SELECCIÓN EN HOJA OPCIONES
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
				//$objValidation->setPromptTitle(lang('excel_prompt_title').' "'.$campo->nombre.'"');
				//$objValidation->setPrompt(lang('excel_prompt_text').' "'.$info_mantenedora->nombre.'"');
				//$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');

				$cantidad_opciones_seleccion = count($array_opciones);
				if($cantidad_opciones_seleccion > 0){
					$objValidation->setFormula1('options!$'.$this->getNameFromNumber($columna_opciones).'$1:$'.$this->getNameFromNumber($columna_opciones).'$'.$cantidad_opciones_seleccion);
					//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}

				//if($array_opciones[0]){
				//	$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', $array_opciones[0]);
				//}
				
				$columna_opciones++;
			}
			
			if($campo["id_tipo_campo"] == 7){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_select_multiple'));
			}
			if($campo["id_tipo_campo"] == 8){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_rut'));
			}
			if($campo["id_tipo_campo"] == 9){
				//$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', lang('excel_test_radio'));
				
				$datos_campo = json_decode($campo["opciones"]);
					
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
				$objValidation->setFormula1('"'.implode(",", $array_opciones).'"');
				
				if($array_opciones[0]){
					$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : $array_opciones[0]);
				}
				
			}
			if($campo["id_tipo_campo"] == 13){
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_mail'));
			}
			if($campo["id_tipo_campo"] == 14){
				$doc->getActiveSheet()->getStyle($this->getNameFromNumber($columna).'2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$doc->getActiveSheet()->setCellValue($this->getNameFromNumber($columna).'2', ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_time'));
			}
			if($campo["id_tipo_campo"] == 15){
				$unidad_ejemplo = ($campo["default_value"]) ? $campo["default_value"] : lang('excel_test_unity');
				$doc->getActiveSheet()->setCellValueExplicit($this->getNameFromNumber($columna).'2', $unidad_ejemplo, PHPExcel_Cell_DataType::TYPE_STRING);
			}
			
			$columna++;
		}

		foreach(range('A', $this->getNameFromNumber($columna)) as $columnID) {
			$doc->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		$nombre_hoja = strlen(lang("permittings").' '.$nombre_proyecto)>31?substr(lang("permittings").' '.$nombre_proyecto, 0, 28).'...':lang("permittings").' '.$nombre_proyecto;
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		// OCULTO HOJA OPTIONS
		$doc->getSheetByName('options')->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		
		$objWriter->save('files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx');
		
		if(!file_exists(__DIR__.'/../../files/carga_masiva_permisos/client_'.$id_cliente.'/project_'.$id_proyecto.'/'.$filename.'.xlsx')) {
			echo json_encode(array("success" => false, 'message' => lang('excel_error_occurred')));
			exit();
		}
		
		$html = '';		
		$html .= '<div class="col-md-12">';
		$html .= '<div class="fa fa-file-excel-o font-22 mr10"></div>';
		$html .= '<a href="'.get_uri("upload_permittings/download_permitting_template/".$id_permiso_proyecto."/".$id_cliente."/".$id_proyecto).'">'.$filename.'.xlsx</a>';	
		$html .= '</div>';
		
		return $html;
		
	}
	
	function clean($string){
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   return strtolower(preg_replace('/[^A-Za-z0-9\_]/', '', $string)); // Removes special chars.
	}
	
	function download_permitting_template($id_permiso_proyecto, $id_cliente, $id_proyecto) {
		
		$client_info = $this->Clients_model->get_one($id_cliente);
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$nombre_proyecto = $this->Projects_model->get_one($id_proyecto)->title;	
		//$nombre_hoja = strlen(lang("permittings").' '.$nombre_proyecto)>31?substr(lang("permittings").' '.$nombre_proyecto, 0, 28).'...':lang("permittings").' '.$nombre_proyecto;
		
		if(!$id_permiso_proyecto && !$id_cliente && !$id_proyecto){
			redirect("forbidden");
		}
		
		//$nombre_archivo = $this->clean($nombre_hoja);
		$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.lang('permitting_template_excel').'_'.date("Y-m-d");
		
        $file_data = serialize(array(array("file_name" => $filename.".xlsx")));
        download_app_files("files/carga_masiva_permisos/client_".$id_cliente."/project_".$id_proyecto."/", $file_data, false);
		
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
	
}

