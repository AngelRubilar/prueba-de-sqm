<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback_matrix_config extends MY_Controller {

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
		
        $this->template->rander("feedback_matrix_config/index", $view_data);
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $project_id = $this->input->post('id');
		$id_feedback_matrix_config = $this->input->post('id_feedback_matrix_config');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";
		$view_data['id_proyecto'] = $project_id;
				
		if($id_feedback_matrix_config){ //edit
			$view_data['campos_feedback_matrix_config'] = $this->Feedback_rel_fields_model->get_feedback_matrix_fields($id_feedback_matrix_config)->result_array();
		}

		if($project_id){
			$view_data['campos_proyecto'] = $this->Fields_model->get_fields_of_project_for_feedback($project_id)->result_array(); //array para multiselect de campos del proyecto
		}		
        //validar si la matriz ya tiene valores, si los tiene, se debe deshabilitar los campos que utiliza la matriz.
		$valores_feedback = $this->Values_feedback_model->get_all_where(array("id_feedback_matrix_config" => $id_feedback_matrix_config, "deleted" => 0))->result();
		$array_id_campos_feedback = array();
		foreach($valores_feedback as $valor_feedback){
			foreach(json_decode($valor_feedback->datos_campos) as $index => $vc){
				$array_id_campos_feedback[] = $index;
			}	
		}

		$view_data['array_campos_ocupados'] = $array_id_campos_feedback;
		
        $this->load->view('feedback_matrix_config/modal_form', $view_data);
		
    }
	
	
	function save() {
	
		$id_proyecto = $this->input->post('id');
		$id_feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $id_proyecto, "deleted" => 0))->id;
		$id_cliente = $this->Projects_model->get_one($id_proyecto)->client_id;
		
		$campos = $this->input->post('fields');
		
		$data_feedback_matrix_config = array(
			"id_proyecto" => $id_proyecto
		);
		
		$data_campos = array();
		
		if (!$id_feedback_matrix_config) { //insert
		
            $data_feedback_matrix_config["created"] = get_current_utc_time();
            $data_feedback_matrix_config["created_by"] = $this->login_user->id;
			$save_id = $this->Feedback_matrix_config_model->save($data_feedback_matrix_config);
			
			foreach($campos as $campo){
				if(strstr($campo, 'fijo_')){continue;}
				$data_campos['id_feedback_matrix_config'] = $save_id;
				$data_campos['id_campo'] = $campo;
				$data_campos['created'] = get_current_utc_time();
				$data_campos["created_by"] = $this->login_user->id;
				$save_id_campo = $this->Feedback_rel_fields_model->save($data_campos);
			}
			
			// crear carpeta para plantilla excel, para la carga masiva de stakeholders del proyecto.
            $crea_carpeta = $this->create_feedback_folder($id_cliente, $id_proyecto);
			

        } else { //edit 

			//si la matriz de compromisos tiene elementos, no se puede editar.
			//$valores_compromiso = $this->Values_compromises_model->get_all_where(array("id_compromiso" => $id_compromiso, "deleted" => 0))->result();
			
			//if($valores_compromiso){
				
				//echo json_encode(array("success" => false, 'message' => lang('cant_edit_compromise_matrix')));
				//exit();
				
			//} else {
				
				//NO SE PUEDE EDITAR LA MATRIZ SI UNO DE SUS FEEDBACK ESTÁ SIENDO USADO EN "SEGUIMIENTO DE FEEDBACK"
				
				$hay_seguimiento = FALSE;
				$valores_feedback = $this->Values_feedback_model->get_all_where(array("id_feedback_matrix_config" => $id_feedback_matrix_config, "deleted" => 0))->result_array();
				foreach($valores_feedback as $feedback){
					$seguimiento_feedback = $this->Feedback_monitoring_model->get_all_where(array("id_valor_feedback" => $feedback["id"], "deleted" => 0))->result_array();
					if($seguimiento_feedback){
						$hay_seguimiento = TRUE;
						break;
					} 
				}
				
				if($hay_seguimiento){
					echo json_encode(array("success" => false, 'message' => lang('cant_edit_feedback_matrix')));
					exit();
				}
				
				
				$data_feedback_matrix_config["modified"] = get_current_utc_time();
				$data_feedback_matrix_config["modified_by"] = $this->login_user->id;
				$save_id = $this->Feedback_matrix_config_model->save($data_feedback_matrix_config, $id_feedback_matrix_config);
			
				$delete_campos_feedback_matrix = $this->Feedback_rel_fields_model->delete_fields_related_to_feedback_matrix($id_feedback_matrix_config);
				if($delete_campos_feedback_matrix){
					foreach($campos as $campo){
						$data_campos['id_feedback_matrix_config'] = $save_id;
						$data_campos['id_campo'] = $campo;
						$data_campos['created'] = get_current_utc_time();
						$data_campos["created_by"] = $this->login_user->id;
						$save_id_campo = $this->Feedback_rel_fields_model->save($data_campos);
					}	
				} else {
					var_dump("problemas al editar campos");
				}
				
			//}
			
		}
		
		$data_project = array("matriz_feedback" => 1);
		$save_project_matrix = $this->Projects_model->save($data_project, $id_proyecto);
		
        if ($save_id && $save_project_matrix) {	
            //echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
            echo json_encode(array("success" => true, "data" => $this->_row_data($id_proyecto), 'id' => $id_proyecto, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));        
		} else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
    }
	
	function create_feedback_folder($client_id, $project_id) {

		if(!file_exists(__DIR__.'/../../files/carga_masiva_feedback/client_'.$client_id.'/project_'.$project_id)) {
			if(mkdir(__DIR__.'/../../files/carga_masiva_feedback/client_'.$client_id.'/project_'.$project_id, 0777, TRUE)){
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
		$id_feedback_module = 9; //Cambiar por id del módulo (tabla clients_modules)
		
		$options = array(
			"id_cliente" => $this->input->post("id_cliente"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"matriz_creada" => $this->input->post("matriz_creada")
		);
		
		$list_data = $this->Projects_model->get_available_feedback_projects($id_feedback_module, $options)->result();
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id_proyecto) {
        
        $id_feedback_module = 9; //Cambiar por id del módulo (tabla clients_modules)
		$options = array(
            "id_proyecto" => $id_proyecto,
			"deleted" => 0
        );
		
		$data = $this->Projects_model->get_available_feedback_projects($id_feedback_module, $options)->row();
        
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$id_feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $data->id, "deleted" => 0))->id;
		/*
		//SI EXISTEN EVALUACIONES DE FEEDBACKS DE LA MATRIZ, BLOQUEAR EL BOTÓN DE EDICIÓN. (PENDIENTE, CAMBIAR)
		$hay_evaluaciones = FALSE;
		
		$valores_feedback = $this->Values_feedback_model->get_all_where(array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config,
			"deleted" => 0
		))->result_array();
		
		foreach($valores_feedback as $valor_feedback){
			
			$evaluaciones_feedback = $this->Feedback_monitoring_model->get_all_where(array(
				"id_valor_feedback" => $valor_feedback["id"],
				"deleted" => 0
			))->result_array();

			if($evaluaciones_feedback){
				$hay_evaluaciones = TRUE;
				break;
			}
		
		}
		*/
		
		if($data->matriz_feedback == 1){ //proyecto que tiene habilitado el módulo Acuerdos
			
			$boton_ver = modal_anchor(get_uri("feedback_matrix_config/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id,  "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config));
			
			$boton_editar = modal_anchor(get_uri("feedback_matrix_config/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id, "data-post-id_feedback_matrix_config" => $id_feedback_matrix_config));
			
			$boton_agregar = '<span style="cursor: not-allowed;">'. modal_anchor(get_uri(/* "compromises_matrix_config/modal_form" */), "<i class='fa fa-plus'></i>", array("class" => "edit", "title" => lang('add_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			$matriz_creada = '<i class="fa fa-check" aria-hidden="true"></i>';
			
		} else {
			
			//$boton_ver = modal_anchor(get_uri("compromises_matrix_config/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id));
			$boton_ver = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_matrix_config/view/" . $data->id */), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			//$boton_editar = modal_anchor(get_uri("compromises_matrix_config/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id));
			$boton_editar = '<span style="cursor: not-allowed;">' . modal_anchor(get_uri(/* "compromises_matrix_config/modal_form" */), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_matrix'), "data-post-id" => $data->id, "style" => "pointer-events: none; background-color: #dcdcdc;")).'</span>';
			$boton_agregar = modal_anchor(get_uri("feedback_matrix_config/modal_form"), "<i class='fa fa-plus'></i>", array("class" => "edit", "title" => lang('add_matrix'), "data-post-id" => $data->id));
			$matriz_creada = '<i class="fa fa-times" aria-hidden="true"></i>';
		
		}
		
        $row_data = array($data->id, $data->company_name, $data->title, $matriz_creada);

        $row_data[] = $boton_ver . $boton_editar . $boton_agregar;

        return $row_data;
    }
	
	function view($id_proyecto = 0) {
		
        $this->access_only_allowed_members();
		$view_data["label_column"] = "col-md-3";
		$view_data["field_column"] = "col-md-9";
		
		$id_feedback_matrix_config = $this->input->post('id_feedback_matrix_config');
		
        if ($id_feedback_matrix_config) {
			
			$feedback = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config);
			$columnas_campos = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result_array();
			
			$json_string_campos = '';
			foreach($columnas_campos as $columna){
				if($columna["id_tipo_campo"] == 11 || $columna["id_tipo_campo"] == 12){ continue; }				
				if($columna["id_tipo_campo"] == 2){
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '", "class" : "text-center"}';
				} else {
					$json_string_campos .= ',' . '{"title":"' . $columna["nombre_campo"] . '"}';
				}				
			}
			
			$view_data["model_info"] = $feedback;
            $view_data["columnas_campos"] = $json_string_campos;
			$view_data["id_feedback_matrix_config"] = $id_feedback_matrix_config;
			
			$this->load->view('feedback_matrix_config/view', $view_data);

        } else {
            show_404();
        }
    }
	
	
	function list_data_view_matrix($id_feedback_matrix_config = 0){
		
		$options = array(
			"id_feedback_matrix_config" => $id_feedback_matrix_config
		);
		
		$list_data = $this->Values_feedback_model->get_details($options)->result();
		$columnas = $this->Feedback_matrix_config_model->get_fields_of_feedback_matrix($id_feedback_matrix_config)->result();
		$result = array();
		 
		foreach($list_data as $data) {
			$result[] = $this->_make_row_view_matrix($data, $columnas, $id_feedback_matrix_config);
		}
		
		echo json_encode(array("data" => $result));	
		
	}
	
	function _make_row_view_matrix($data, $columnas, $id_feedback_matrix_config){
		
		$id_proyecto = $this->Feedback_matrix_config_model->get_one($id_feedback_matrix_config)->id_proyecto;
		
		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = get_date_format($data->fecha, $id_proyecto);
		$row_data[] = $data->nombre;
		
		$tipo_stakeholder = $this->Types_of_organization_model->get_one($data->id_tipo_organizacion);
		$row_data[] = lang($tipo_stakeholder->nombre);
		
		$row_data[] = lang($data->proposito_visita);
		
		//RESPONSABLE
		$responsable = $this->Users_model->get_one($data->responsable);
		$nombre_responsable = $responsable->first_name . " " . $responsable->last_name;
		$row_data[] = $nombre_responsable;
		
		//$row_data[] = $data->respuesta;
		//$row_data[] = $data->estado_respuesta;
		//$row_data[] = "[evidencias]";
		
		/*
		//PERIODO
		$periodo = json_decode($data->periodo);	
		$row_data[] = get_date_format($periodo->start_date, $id_proyecto) .  " - " . get_date_format($periodo->end_date, $id_proyecto);
		
		//GESTOR
		$gestor = $this->Users_model->get_one($data->gestor);
		$nombre_gestor = $gestor->first_name . " " . $gestor->last_name;
		$row_data[] = $nombre_gestor;
		
		$row_data[] = $data->observaciones;
		$row_data[] = "[archivos de evidencia del acuerdo]";
		*/
		if($data->datos_campos){
			$arreglo_fila = json_decode($data->datos_campos, true);
			$cont = 0;
			
			foreach($columnas as $columna) {
				$cont++;
				
				// Si existe el campo dentro de los valores del registro
				if(isset($arreglo_fila[$columna->id_campo])){
					
					if($columna->id_tipo_campo == 2){ // Si es text area
						
						$tooltip_textarea = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$arreglo_fila[$columna->id_campo].'"><i class="fas fa-info-circle fa-lg"></i></span>';
						$valor_campo = ($arreglo_fila[$columna->id_campo]) ? $tooltip_textarea : "-";
					
					}elseif($columna->id_tipo_campo == 4){//si es fecha.
						$valor_campo = get_date_format($arreglo_fila[$columna->id_campo],$id_proyecto);
					}elseif($columna->id_tipo_campo == 5){// si es periodo
						$start_date = $arreglo_fila[$columna->id_campo]['start_date'];
						$end_date = $arreglo_fila[$columna->id_campo]['end_date'];
						$valor_campo = $start_date.' - '.$end_date;
					}elseif(($columna->id_tipo_campo == 11)||($columna->id_tipo_campo == 12)){
						continue;
					}else{
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
		
		/*
		//STAKEHOLDERS
		$stakeholders = json_decode($data->stakeholders);
		$html_stakeholders = (count($stakeholders) > 1) ? "<ul>" : "";
		foreach($stakeholders as $sh){
			$stakeholder = $this->Values_stakeholders_model->get_one($sh);
			$html_stakeholders .= (count($stakeholders) > 1) ? "<li>" . $stakeholder->nombres . " " . $stakeholder->apellidos . "</li>" : $stakeholder->nombres . " " . $stakeholder->apellidos;
		}
		$html_stakeholders .= (count($stakeholders) > 1) ? "</ul>" : "";
		$row_data[] = $html_stakeholders;
		*/
		return $row_data;
	}
	

}

