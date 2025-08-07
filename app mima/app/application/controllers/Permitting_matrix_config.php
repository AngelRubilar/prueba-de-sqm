<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Permitting_matrix_config extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		//$access_info = $this->get_access_info("invoice");
		
		// FILTRO CLIENTE
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		// FILTRO PROYECTO
		$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
		$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
		foreach($proyectos as $id => $title){
			$array_proyectos[] = array("id" => $id, "text" => $title);
		}
		$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
		
		// FILTRO MATRIZ CREADA
		$array_matriz_creada[] = array("id" => "", "text" => "- ".lang("created_matrix")." -");
		$array_matriz_creada[] = array("id" => "si", "text" => lang("yes"));
		$array_matriz_creada[] = array("id" => "no", "text" => lang("no"));
		$view_data['matriz_creada_dropdown'] = json_encode($array_matriz_creada);
		
        $this->template->rander("permitting_matrix_config/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $project_id = $this->input->post('id');
		$id_permitting = $this->input->post('id_permitting');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		$view_data['id_proyecto'] = $project_id;
		$view_data['id_permiso'] = $id_permitting;
				
		if($id_permitting){ //edit
			$view_data['campos_permiso'] = $this->Permitting_rel_fields_model->get_permitting_fields($id_permitting)->result_array();
			$view_data['evaluados_permiso'] = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $id_permitting, "deleted" => 0))->result_array();
		}

		if($project_id){
			$view_data['campos_proyecto'] = $this->Fields_model->get_fields_of_project_for_permitting($project_id)->result_array(); //array para multiselect de campos del proyecto
		}		
        //validar si la matriz ya tiene valores, si los tiene, se debe deshabilitar los campos que utiliza la matriz.
		$valores_permiso = $this->Values_permitting_model->get_all_where(array("id_permiso" => $id_permitting, "deleted" => 0))->result();
		$array_id_campos_permiso = array();
		foreach($valores_permiso as $valor_permiso){
			foreach(json_decode($valor_permiso->datos_campos) as $index => $vc){
				$array_id_campos_permiso[] = $index;
			}	
		}

		$view_data['array_campos_ocupados'] = $array_id_campos_permiso;
		
        $this->load->view('permitting_matrix_config/modal_form', $view_data);
		
    }
	
	
	function save() {
	
		$id_proyecto = $this->input->post('id');
		$id_permiso = $this->Permitting_model->get_one_where(array("id_proyecto"=>$id_proyecto,"deleted"=> 0))->id;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;

		$campos = $this->input->post('fields');
		$evaluados = $this->input->post('evaluado');

		$data_permiso = array(
			"id_proyecto" => $id_proyecto,
		);
		
		$data_campos = array();
		$data_evaluados = array();
		
		if (!$id_permiso) { //insert
		
            $data_permiso["created"] = get_current_utc_time();
            $data_permiso["created_by"] = $this->login_user->id;
			$save_id = $this->Permitting_model->save($data_permiso);
			
			foreach($campos as $campo){
				if(strstr($campo, 'fijo_')){continue;}
				$data_campos['id_permiso'] = $save_id;
				$data_campos['id_campo'] = $campo;
				$data_campos['created'] = get_current_utc_time();
				$data_campos["created_by"] = $this->login_user->id;
				$save_id_campo = $this->Permitting_rel_fields_model->save($data_campos);
			}
			
			foreach($evaluados as $evaluado){
				$data_evaluados['id_permiso'] = $save_id;
				$data_evaluados['nombre_evaluado'] = $evaluado;
				$data_evaluados['created'] = get_current_utc_time();
				$data_evaluados["created_by"] = $this->login_user->id;
				$save_id_evaluado = $this->Evaluated_permitting_model->save($data_evaluados);
			}
			
			// crear carpeta para plantilla excel, para la carga masiva de permisos del proyecto.
            $crea_carpeta = $this->create_permitting_folder($id_cliente, $id_proyecto);
			

        } else { //edit 

			//si la matriz de permisos tiene elementos, no se puede editar.
			//$valores_permiso = $this->Values_permitting_model->get_all_where(array("id_permiso" => $id_permiso, "deleted" => 0))->result();
			
			//if($valores_permiso){
				
				//echo json_encode(array("success" => false, 'message' => lang('cant_edit_compromise_matrix')));
				//exit();
				
			//} else {
				
				//SI LA MATRIZ DE PERMISOS TIENE EVALUACIONES, NO SE DEBE PODER EDITAR.
				
				//busco los ids de los evaluados asociados a la matriz
				//itero el array y pregunto en cada iteración si ese id de evaluado tiene una evaluación
				//si el id tiene una evaluación, detengo el loop y almaceno en un boolean = true
				//luego si el boolean es = true, envio mensaje de que no se puede editar matriz, y corto la ejecución.
				
				$hay_evaluaciones = FALSE;
				$evaluados_matriz = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $id_permiso, "deleted" => 0))->result_array();
				foreach($evaluados_matriz as $evaluado){
					$evaluaciones_evaluado_matriz = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_evaluado" => $evaluado["id"], "deleted" => 0))->result_array();
					if($evaluaciones_evaluado_matriz){
						$hay_evaluaciones = TRUE;
						break;
					} 
				}
				
				if($hay_evaluaciones){
					echo json_encode(array("success" => false, 'message' => lang('cant_edit_permitting_matrix')));
					exit();
				}
				
				$data_permiso["modified"] = get_current_utc_time();
				$data_permiso["modified_by"] = $this->login_user->id;
				$save_id = $this->Permitting_model->save($data_permiso, $id_permiso);
			
				$delete_campos_permiso = $this->Permitting_rel_fields_model->delete_fields_related_to_permitting($id_permiso);
				if($delete_campos_permiso){
					foreach($campos as $campo){
						$data_campos['id_permiso'] = $save_id;
						$data_campos['id_campo'] = $campo;
						$data_campos['created'] = get_current_utc_time();
						$data_campos["created_by"] = $this->login_user->id;
						$save_id_campo = $this->Permitting_rel_fields_model->save($data_campos);
					}	
				} else {
					var_dump("problemas al editar campos");
				}
				
				$delete_evaluados_permiso = $this->Evaluated_permitting_model->delete_evaluated_related_to_permitting($id_permiso);
				if($delete_evaluados_permiso) {
					foreach($evaluados as $evaluado){
						$data_evaluados['id_permiso'] = $save_id;
						$data_evaluados['nombre_evaluado'] = $evaluado;
						$data_evaluados['created'] = get_current_utc_time();
						$data_evaluados["created_by"] = $this->login_user->id;
						$save_id_evaluado = $this->Evaluated_permitting_model->save($data_evaluados);
					}
				} else {
					var_dump("problemas al editar evaluados");
				}

			//}
			
		}
		
		$data_project = array("matriz_permisos" => 1);
		$save_project_matrix = $this->Projects_model->save($data_project, $id_proyecto);
		
        if ($save_id && $save_project_matrix) {	
            //echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
            echo json_encode(array("success" => true, "data" => $this->_row_data($id_proyecto), 'id' => $id_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));        
		} else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
    }
	
	function create_permitting_folder($client_id, $project_id) {

		if(!file_exists(__DIR__.'/../../files/carga_masiva_permisos/client_'.$client_id.'/project_'.$project_id)) {
			if(mkdir(__DIR__.'/../../files/carga_masiva_permisos/client_'.$client_id.'/project_'.$project_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}
	
	/* function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Subprojects_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Subprojects_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    } */
	
	function list_data() {

        $this->access_only_allowed_members();
		$id_permitting_module = 7; // Tramitación de Permisos
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"matriz_creada" => $this->input->post("matriz_creada")
		);
		
		$list_data = $this->Projects_model->get_available_permitting_projects($id_permitting_module, $options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id_proyecto) {
        
        $id_permitting_module = 7; // Tramitación de Permisos
		$options = array(
            "id_proyecto" => $id_proyecto,
			"deleted" => 0
        );
		
		$data = $this->Projects_model->get_available_permitting_projects($id_permitting_module, $options)->row();
        
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_permitting = $this->Permitting_model->get_one_where(array("id_proyecto" => $data->id,"deleted" => 0))->id;
		
		//SI EXISTEN EVALUACIONES DE PERMISOS DE LA MATRIZ, BLOQUEAR EL BOTÓN DE EDICIÓN.
		$hay_evaluaciones = FALSE;
		
		$valores_permiso = $this->Values_permitting_model->get_all_where(array(
			"id_permiso" => $id_permitting,
			"deleted" => 0
		))->result_array();
		
		foreach($valores_permiso as $valor_permiso){
			
			$evaluaciones_permiso = $this->Permitting_procedure_evaluation_model->get_all_where(array(
				"id_valor_permiso" => $valor_permiso["id"],
				"deleted" => 0
			))->result_array();

			if($evaluaciones_permiso){
				$hay_evaluaciones = TRUE;
				break;
			}
		
		}
		
		if($data->matriz_permisos == 1){ //proyecto que tiene habilitado el módulo Permisos
			
			$boton_ver = modal_anchor(get_uri("permitting_matrix_config/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id,  "data-post-id_permitting" => $id_permitting));
			if($hay_evaluaciones){
				$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_matrix_config/modal_form" */), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			} else {
				$boton_editar = modal_anchor(get_uri("permitting_matrix_config/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id, "data-post-id_permitting" => $id_permitting));
			}
			$boton_agregar = '<span style="cursor: not-allowed;">'. modal_anchor(get_uri(/* "compromises_matrix_config/modal_form" */), "<i class='fa fa-plus'></i>", array("class" => "edit", "title" => lang('add_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			$matriz_creada = '<i class="fa fa-check" aria-hidden="true"></i>';
			
		} else {
			
			//$boton_ver = modal_anchor(get_uri("permitting_matrix_config/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id));
			$boton_ver = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "permitting_matrix_config/view/" . $data->id */), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			//$boton_editar = modal_anchor(get_uri("permitting_matrix_config/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id));
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_matrix_config/modal_form" */), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			$boton_agregar = modal_anchor(get_uri("permitting_matrix_config/modal_form"), "<i class='fa fa-plus'></i>", array("class" => "edit", "title" => lang('add_matrix'), "data-post-id" => $data->id));
			$matriz_creada = '<i class="fa fa-times" aria-hidden="true"></i>';
		
		}
		
        $row_data = array($data->id, $data->company_name, $data->title, $matriz_creada);

        $row_data[] = $boton_ver . $boton_editar . $boton_agregar;

        return $row_data;
    }
	
	function view($id_proyecto = 0) {
		
        //$this->access_only_allowed_members();
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$id_permiso_proyecto = $this->input->post('id_permitting');
		
        if ($id_permiso_proyecto) {

			$permiso = $this->Permitting_model->get_one($id_permiso_proyecto);
			$columnas_campos = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result_array();
			
			$json_string_campos = '';
			
			foreach($columnas_campos as $columna){
				
				if($columna["id_tipo_campo"] == 1){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 2){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center"}';
				}else if($columna["id_tipo_campo"] == 3){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-right dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 4){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center", type: "extract-date"}';
				}elseif($columna["id_tipo_campo"] == 5){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center no_breakline"}';
				}else if($columna["id_tipo_campo"] >= 6 && $columna["id_tipo_campo"] <= 9){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-left dt-head-center"}';
				}else if($columna["id_tipo_campo"] == 10){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class": "text-center option"}';
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

			$view_data["model_info"] = $permiso;
            $view_data["columnas_campos"] = $json_string_campos;
			$view_data["id_permiso_proyecto"] = $id_permiso_proyecto;
			
			$this->load->view('permitting_matrix_config/view', $view_data);

        } else {
            show_404();
        }
    }
	
	
	function list_data_view_matrix($id_permiso_proyecto = 0){

		$options = array(
			"id_permiso" => $id_permiso_proyecto
		);
		
		$list_data = $this->Values_permitting_model->get_details($options)->result();
		$columnas = $this->Permitting_model->get_fields_of_permitting($id_permiso_proyecto)->result();
		$result = array();

		foreach($list_data as $data) {
			$result[] = $this->_make_row_view_matrix($data, $columnas, $id_permiso_proyecto);
		}

		echo json_encode(array("data" => $result));	
		
	}
	
	function _make_row_view_matrix($data, $columnas, $id_permiso_proyecto){
		
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
					if($columna->id_tipo_campo == 2){ // TEXT AREA
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					} elseif($columna->id_tipo_campo == 3){ // NÚMERO
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 4){ // FECHA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? get_date_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} elseif($columna->id_tipo_campo == 5){ // PERIODO
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = ($start_date && $end_date) ? get_date_format($start_date, $id_proyecto).' - '.get_date_format($end_date, $id_proyecto) : '-';
					} elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){ // TEXTO FIJO || DIVISOR
						continue;
					} elseif($columna->id_tipo_campo == 14){ // HORA
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? convert_to_general_settings_time_format($id_proyecto, $arreglo_fila[$columna->id_campo]) : '-';
					} elseif($columna->id_tipo_campo == 15){ // UNIDAD
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? to_number_project_format($arreglo_fila[$columna->id_campo], $id_proyecto) : '-';
					} else {
						$valor_campo = ($arreglo_fila[$columna->id_campo] == "") ? '-' : $arreglo_fila[$columna->id_campo];
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
		
		return $row_data;
	}
	

}

