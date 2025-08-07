<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Air_synoptic_data_upload extends MY_Controller {
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_modulo_cliente = 15;
		$this->id_submodulo_cliente = 30;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		
		if($id_proyecto){
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}

    }

    function index() {

		$id_proyecto = $this->session->project_context;
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$id_cliente = $proyecto->client_id;
		
		$view_data["project_info"] = $proyecto;
		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$view_data["puede_agregar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
		$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

		### GENERAR REGISTRO EN LOGS_MODEL ###
        $this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_synoptic_upload');


		$options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto
		);
		
        $registros = $this->Air_synoptic_data_model->get_details($options)->result();
		$arrayFechas = array();
		foreach($registros as $index => $reg){
			if(!$reg->modified){
				$arrayFechas[$index] = $reg->created;
			} else {
				$arrayFechas[$index] = $reg->modified;
			}
		}
		$fecha_modificacion = (max($arrayFechas)) ? time_date_zone_format(max($arrayFechas), $id_proyecto) : "-";
		
		$view_data["num_registros"] = count($registros);
		$view_data["fecha_modificacion"] = $fecha_modificacion;

        $this->template->rander("air_synoptic_data_upload/index", $view_data);
	}
	
	function modal_form() {
		
        $id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');

		$model_info = $this->Air_synoptic_data_model->get_one($id);
        $view_data['model_info'] = $model_info;
		
		// DATOS SINOPTICOS 24 HRS
		$data_pmca_24_hrs_t1 = json_decode($model_info->pmca_24_hrs_t1);
		$view_data['pmca_24_hrs_t1'] = $data_pmca_24_hrs_t1->value;
		$view_data['prob_pmca_24_hrs_t1'] = $data_pmca_24_hrs_t1->percentage;

		$data_pmca_24_hrs_t2 = json_decode($model_info->pmca_24_hrs_t2);
		$view_data['pmca_24_hrs_t2'] = $data_pmca_24_hrs_t2->value;
		$view_data['prob_pmca_24_hrs_t2'] = $data_pmca_24_hrs_t2->percentage;

		$data_pmca_24_hrs_t3 = json_decode($model_info->pmca_24_hrs_t3);
		$view_data['pmca_24_hrs_t3'] = $data_pmca_24_hrs_t3->value;
		$view_data['prob_pmca_24_hrs_t3'] = $data_pmca_24_hrs_t3->percentage;

		// DATOS SINOPTICOS 48 HRS
		$data_pmca_48_hrs_t1 = json_decode($model_info->pmca_48_hrs_t1);
		$view_data['pmca_48_hrs_t1'] = $data_pmca_48_hrs_t1->value;
		$view_data['prob_pmca_48_hrs_t1'] = $data_pmca_48_hrs_t1->percentage;

		$data_pmca_48_hrs_t2 = json_decode($model_info->pmca_48_hrs_t2);
		$view_data['pmca_48_hrs_t2'] = $data_pmca_48_hrs_t2->value;
		$view_data['prob_pmca_48_hrs_t2'] = $data_pmca_48_hrs_t2->percentage;

		$data_pmca_48_hrs_t3 = json_decode($model_info->pmca_48_hrs_t3);
		$view_data['pmca_48_hrs_t3'] = $data_pmca_48_hrs_t3->value;
		$view_data['prob_pmca_48_hrs_t3'] = $data_pmca_48_hrs_t3->percentage;

		// DATOS SINOPTICOS 72 HRS
		$data_pmca_72_hrs_t1 = json_decode($model_info->pmca_72_hrs_t1);
		$view_data['pmca_72_hrs_t1'] = $data_pmca_72_hrs_t1->value;
		$view_data['prob_pmca_72_hrs_t1'] = $data_pmca_72_hrs_t1->percentage;

		$data_pmca_72_hrs_t2 = json_decode($model_info->pmca_72_hrs_t2);
		$view_data['pmca_72_hrs_t2'] = $data_pmca_72_hrs_t2->value;
		$view_data['prob_pmca_72_hrs_t2'] = $data_pmca_72_hrs_t2->percentage;

		$data_pmca_72_hrs_t3 = json_decode($model_info->pmca_72_hrs_t3);
		$view_data['pmca_72_hrs_t3'] = $data_pmca_72_hrs_t3->value;
		$view_data['prob_pmca_72_hrs_t3'] = $data_pmca_72_hrs_t3->percentage;

		$pmca_options = array(
			"" => "-",
			"1" => "1",
			"2" => "2",
			"3" => "3",
			"4" => "4"
		);
		$view_data["pmca_options"] = $pmca_options;

		$view_data["evidence_file_name"] = remove_file_prefix($view_data['model_info']->evidence_file);
		
        $this->load->view('air_synoptic_data_upload/modal_form', $view_data);
    }

	function save() {

        $id = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$file = $this->input->post('archivo_importado');

		$file_type_to_delete = $this->input->post('file_type_to_delete'); //ID DE ARCHIVOS A ELIMINAR
		if($id && $file_type_to_delete){
			foreach($file_type_to_delete as $file_type){

				$file_name = $this->Air_synoptic_data_model->get_one($id)->$file_type;
				$file_path = "files/air_synoptic_data_files/client_".$id_cliente."/project_".$id_proyecto."/synoptic_data_".$id."/".$file_name;

				$data[$file_type] = "";
				$data["modified_by"] = $this->login_user->id;
				$data["modified"] = get_current_utc_time();
				$save_id = $this->Air_synoptic_data_model->save($data, $id);
				
				delete_file_from_directory($file_path);

			}
		}

		// VALIDO QUE SOLO SE PUEDA INGRESAR UN REGISTRO POR DÍA (A PARTIR DEL CAMPO "FECHA")
		$date = $this->input->post('date');
		if($this->Air_synoptic_data_model->is_synoptic_data_exists($date, $id_cliente, $id_proyecto, $id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_synoptic_data_msj')));
			exit(); 
		}

		$pmca_24_hrs_t1 = $this->input->post('pmca_24_hrs_t1');
		$prob_pmca_24_hrs_t1 = $this->input->post('prob_pmca_24_hrs_t1');
		$data_24_t1 = json_encode(array('value' => $pmca_24_hrs_t1, 'percentage' => $prob_pmca_24_hrs_t1));

		$pmca_24_hrs_t2 = $this->input->post('pmca_24_hrs_t2');
		$prob_pmca_24_hrs_t2 = $this->input->post('prob_pmca_24_hrs_t2');
		$data_24_t2 = json_encode(array('value' => $pmca_24_hrs_t2, 'percentage' => $prob_pmca_24_hrs_t2));
		
		$pmca_24_hrs_t3 = $this->input->post('pmca_24_hrs_t3');
		$prob_pmca_24_hrs_t3 = $this->input->post('prob_pmca_24_hrs_t3');
		$data_24_t3 = json_encode(array('value' => $pmca_24_hrs_t3, 'percentage' => $prob_pmca_24_hrs_t3));
		

		$pmca_48_hrs_t1 = $this->input->post('pmca_48_hrs_t1');
		$prob_pmca_48_hrs_t1 = $this->input->post('prob_pmca_48_hrs_t1');
		$data_48_t1 = json_encode(array('value' => $pmca_48_hrs_t1, 'percentage' => $prob_pmca_48_hrs_t1));

		$pmca_48_hrs_t2 = $this->input->post('pmca_48_hrs_t2');
		$prob_pmca_48_hrs_t2 = $this->input->post('prob_pmca_48_hrs_t2');
		$data_48_t2 = json_encode(array('value' => $pmca_48_hrs_t2, 'percentage' => $prob_pmca_48_hrs_t2));
		
		$pmca_48_hrs_t3 = $this->input->post('pmca_48_hrs_t3');
		$prob_pmca_48_hrs_t3 = $this->input->post('prob_pmca_48_hrs_t3');
		$data_48_t3 = json_encode(array('value' => $pmca_48_hrs_t3, 'percentage' => $prob_pmca_48_hrs_t3));
		
		
		$pmca_72_hrs_t1 = $this->input->post('pmca_72_hrs_t1');
		$prob_pmca_72_hrs_t1 = $this->input->post('prob_pmca_72_hrs_t1');
		$data_72_t1 = json_encode(array('value' => $pmca_72_hrs_t1, 'percentage' => $prob_pmca_72_hrs_t1));

		$pmca_72_hrs_t2 = $this->input->post('pmca_72_hrs_t2');
		$prob_pmca_72_hrs_t2 = $this->input->post('prob_pmca_72_hrs_t2');
		$data_72_t2 = json_encode(array('value' => $pmca_72_hrs_t2, 'percentage' => $prob_pmca_72_hrs_t2));
		
		$pmca_72_hrs_t3 = $this->input->post('pmca_72_hrs_t3');
		$prob_pmca_72_hrs_t3 = $this->input->post('prob_pmca_72_hrs_t3');
		$data_72_t3 = json_encode(array('value' => $pmca_72_hrs_t3, 'percentage' => $prob_pmca_72_hrs_t3));
		
		
		$data = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"date" => $date,
			"pmca_24_hrs_t1" => $data_24_t1,
			"pmca_24_hrs_t2" => $data_24_t2,
			"pmca_24_hrs_t3" => $data_24_t3,
			"pmca_48_hrs_t1" => $data_48_t1,
			"pmca_48_hrs_t2" => $data_48_t2,
			"pmca_48_hrs_t3" => $data_48_t3,
			"pmca_72_hrs_t1" => $data_72_t1,
			"pmca_72_hrs_t2" => $data_72_t2,
			"pmca_72_hrs_t3" => $data_72_t3,
			"observations" => $this->input->post('observations'),
			"deleted" => 0
		);
		
		if($id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			$save_id = $this->Air_synoptic_data_model->save($data, $id);
		} else {
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			$save_id = $this->Air_synoptic_data_model->save($data);
		}
		
        if ($save_id) {

			if($file){
				$crear_carpeta = $this->create_air_synoptic_data_folder($save_id);			
				$archivo_subido = move_temp_file($file, "files/air_synoptic_data_files/client_".$id_cliente."/project_".$id_proyecto."/synoptic_data_".$save_id."/");
				$data_file = array("evidence_file" => $archivo_subido);
				$save_id = $this->Air_synoptic_data_model->save($data_file, $save_id);
			}
			
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"deleted" => 0
			);
			$registros = $this->Air_synoptic_data_model->get_all_where($options)->result();
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
			$num_registros = count($registros);

			echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
		} else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

        if ($this->input->post('undo')) {
            if ($this->Air_synoptic_data_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Air_synoptic_data_model->delete($id)) {

				$options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"deleted" => 0
				);
				$registros = $this->Air_synoptic_data_model->get_all_where($options)->result();
				$arrayFechas = array();
				foreach($registros as $index => $reg){
					if(!$reg->modified){
						$arrayFechas[$index] = $reg->created;
					} else {
						$arrayFechas[$index] = $reg->modified;
					}
				}
				$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
				$num_registros = count($registros);

				echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('record_deleted')));

            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

	function delete_multiple() {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;		
		$id_user = $this->session->user_id;
		$data_ids = json_decode($this->input->post('data_ids'));
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->profile_access($id_user, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->Air_synoptic_data_model->get_one($id);
				if($id_user != $row->created_by){
					$eliminar = FALSE;
					break;
				}
			}
			if($puede_eliminar == 3){ // Ninguno
				$eliminar = FALSE;
				break;
			}
		}
		
		if(!$eliminar){
			echo json_encode(array("success" => false, 'message' => lang("record_cannot_be_deleted_by_profile")));
			exit();
		}
		
		$deleted_values = false;
		foreach($data_ids as $id){
			if($this->Air_synoptic_data_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			
			$options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"deleted" => 0
			);
			$registros = $this->Air_synoptic_data_model->get_all_where($options)->result();
			$arrayFechas = array();
			foreach($registros as $index => $reg){
				if(!$reg->modified){
					$arrayFechas[$index] = $reg->created;
				} else {
					$arrayFechas[$index] = $reg->modified;
				}
			}
			$fecha_modificacion = time_date_zone_format(max($arrayFechas), $id_proyecto);
			$num_registros = count($registros);

			echo json_encode(array("success" => true, 'fecha_modificacion' => $fecha_modificacion, 'num_registros' => $num_registros, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	

    }

    function list_data() {

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto
		);
		
        $list_data = $this->Air_synoptic_data_model->get_details($options)->result();
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
		
        $data = $this->Air_synoptic_data_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {

		$id_usuario = $this->session->user_id;
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		if($data->evidence_file){
			$evidence_file_name = remove_file_prefix($data->evidence_file);
			$evidence_file = anchor(get_uri("Air_synoptic_data_upload/download_file/".$data->id."/evidence_file"), "<i class='fa fa-cloud-download'></i>", array("title" => $evidence_file_name));	
		} else {
			$evidence_file = '-';
		}	

		$tooltip_observations = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->observations.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$observations = ($data->observations) ? $tooltip_observations : "-";
		
		$pmca_24_hrs_t1 = json_decode($data->pmca_24_hrs_t1)->value;
		$pmca_24_hrs_t2 = json_decode($data->pmca_24_hrs_t2)->value;
		$pmca_24_hrs_t3 = json_decode($data->pmca_24_hrs_t3)->value;
		$pmca_48_hrs_t1 = json_decode($data->pmca_48_hrs_t1)->value;
		$pmca_48_hrs_t2 = json_decode($data->pmca_48_hrs_t2)->value;
		$pmca_48_hrs_t3 = json_decode($data->pmca_48_hrs_t3)->value;
		$pmca_72_hrs_t1 = json_decode($data->pmca_72_hrs_t1)->value;
		$pmca_72_hrs_t2 = json_decode($data->pmca_72_hrs_t2)->value;
		$pmca_72_hrs_t3 = json_decode($data->pmca_72_hrs_t3)->value;

		$row_data[] = get_date_format($data->date, $data->id_project);
		$row_data[] = $pmca_24_hrs_t1 ? $pmca_24_hrs_t1 : '-';
		$row_data[] = $pmca_24_hrs_t2 ? $pmca_24_hrs_t2 : '-';
		$row_data[] = $pmca_24_hrs_t3 ? $pmca_24_hrs_t3 : '-';
		$row_data[] = $pmca_48_hrs_t1 ? $pmca_48_hrs_t1 : '-';
		$row_data[] = $pmca_48_hrs_t2 ? $pmca_48_hrs_t2 : '-';
		$row_data[] = $pmca_48_hrs_t3 ? $pmca_48_hrs_t3 : '-';
		$row_data[] = $pmca_72_hrs_t1 ? $pmca_72_hrs_t1 : '-';
		$row_data[] = $pmca_72_hrs_t2 ? $pmca_72_hrs_t2 : '-';
		$row_data[] = $pmca_72_hrs_t3 ? $pmca_72_hrs_t3 : '-';
		//$row_data[] = $evidence_file;
		//$row_data[] = $observations;
		$row_data[] = time_date_zone_format($data->created, $data->id_project);
		//$row_data[] = ($data->modified) ? time_date_zone_format($data->modified, $data->id_project) : "-";

		$view = modal_anchor(get_uri("air_synoptic_data_upload/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_synoptic_data'), "data-post-id" => $data->id));
		//$edit = modal_anchor(get_uri("air_synoptic_data_upload/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_synoptic_data'), "data-post-id" => $data->id));
		$edit = "";
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_synoptic_data'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("air_synoptic_data_upload/delete"), "data-action" => "delete-confirmation", "data-custom" => true));
		
		//Validaciones de Perfil
		if($puede_editar == 1 && $puede_eliminar ==1){
			$row_data[] = $view.$edit.$delete;		
		} else if($puede_editar == 1 && $puede_eliminar == 2){
			$row_data[] = $view.$edit;
			if($id_usuario == $data->created_by){
				$botones = array_pop($row_data);
				$botones = $botones.$delete;
				$row_data[] = $botones;
			}
		} else if($puede_editar == 1 && $puede_eliminar == 3){
			$row_data[] = $view.$edit;
		} else if($puede_editar == 2 && $puede_eliminar == 1){
			$row_data[] = $view;
			$botones = array_pop($row_data);
			if($id_usuario == $data->created_by){
				$botones = $botones.$edit.$delete;
			} else {
				$botones = $botones.$delete;
			}
			$row_data[] = $botones;
		} else if($puede_editar == 2 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 2 && $puede_eliminar == 3){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$edit;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 1){
			$row_data[] = $view.$delete;
		} else if($puede_editar == 3 && $puede_eliminar == 2){
			if($id_usuario == $data->created_by){
				$row_data[] = $view.$delete;
			} else {
				$row_data[] = $view;
			}
		} else if($puede_editar == 3 && $puede_eliminar == 3){
			$row_data[] = $view;
		}
        return $row_data;
    }

	function view($id_synoptic_data = 0) {

        if ($id_synoptic_data) {

            $options = array("id" => $id_synoptic_data);
            $model_info = $this->Air_synoptic_data_model->get_details($options)->row();
            if ($model_info) {
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $model_info;


				// DATOS SINOPTICOS 24 HRS
				$data_pmca_24_hrs_t1 = json_decode($model_info->pmca_24_hrs_t1);
				$view_data['pmca_24_hrs_t1'] = $data_pmca_24_hrs_t1->value ? $data_pmca_24_hrs_t1->value : '-';
				$view_data['ws_margarita_str_pmca_24_hrs_t1'] = $data_pmca_24_hrs_t1->ws_margarita_str ? $data_pmca_24_hrs_t1->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_24_hrs_t1'] = $data_pmca_24_hrs_t1->hora_ws_min ? $data_pmca_24_hrs_t1->hora_ws_min : '-';

				$data_pmca_24_hrs_t2 = json_decode($model_info->pmca_24_hrs_t2);
				$view_data['pmca_24_hrs_t2'] = $data_pmca_24_hrs_t2->value ? $data_pmca_24_hrs_t2->value : '-';
				$view_data['ws_margarita_str_pmca_24_hrs_t2'] = $data_pmca_24_hrs_t2->ws_margarita_str ? $data_pmca_24_hrs_t2->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_24_hrs_t2'] = $data_pmca_24_hrs_t2->hora_ws_min ? $data_pmca_24_hrs_t2->hora_ws_min : '-';

				$data_pmca_24_hrs_t3 = json_decode($model_info->pmca_24_hrs_t3);
				$view_data['pmca_24_hrs_t3'] = $data_pmca_24_hrs_t3->value ? $data_pmca_24_hrs_t3->value : '-';
				$view_data['ws_margarita_str_pmca_24_hrs_t3'] = $data_pmca_24_hrs_t3->ws_margarita_str ? $data_pmca_24_hrs_t3->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_24_hrs_t3'] = $data_pmca_24_hrs_t3->hora_ws_min ? $data_pmca_24_hrs_t3->hora_ws_min : '-';


				// DATOS SINOPTICOS 48 HRS
				$data_pmca_48_hrs_t1 = json_decode($model_info->pmca_48_hrs_t1);
				$view_data['pmca_48_hrs_t1'] = $data_pmca_48_hrs_t1->value ? $data_pmca_48_hrs_t1->value : '-';
				$view_data['ws_margarita_str_pmca_48_hrs_t1'] = $data_pmca_48_hrs_t1->ws_margarita_str ? $data_pmca_48_hrs_t1->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_48_hrs_t1'] = $data_pmca_48_hrs_t1->hora_ws_min ? $data_pmca_48_hrs_t1->hora_ws_min : '-';

				$data_pmca_48_hrs_t2 = json_decode($model_info->pmca_48_hrs_t2);
				$view_data['pmca_48_hrs_t2'] = $data_pmca_48_hrs_t2->value ? $data_pmca_48_hrs_t2->value : '-';
				$view_data['ws_margarita_str_pmca_48_hrs_t2'] = $data_pmca_48_hrs_t2->ws_margarita_str ? $data_pmca_48_hrs_t2->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_48_hrs_t2'] = $data_pmca_48_hrs_t2->hora_ws_min ? $data_pmca_48_hrs_t2->hora_ws_min : '-';

				$data_pmca_48_hrs_t3 = json_decode($model_info->pmca_48_hrs_t3);
				$view_data['pmca_48_hrs_t3'] = $data_pmca_48_hrs_t3->value ? $data_pmca_48_hrs_t3->value : '-';
				$view_data['ws_margarita_str_pmca_48_hrs_t3'] = $data_pmca_48_hrs_t3->ws_margarita_str ? $data_pmca_48_hrs_t3->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_48_hrs_t3'] = $data_pmca_48_hrs_t3->hora_ws_min ? $data_pmca_48_hrs_t3->hora_ws_min : '-';



				// DATOS SINOPTICOS 72 HRS
				$data_pmca_72_hrs_t1 = json_decode($model_info->pmca_72_hrs_t1);
				$view_data['pmca_72_hrs_t1'] = $data_pmca_72_hrs_t1->value ? $data_pmca_72_hrs_t1->value : '-';
				$view_data['ws_margarita_str_pmca_72_hrs_t1'] = $data_pmca_72_hrs_t1->ws_margarita_str ? $data_pmca_72_hrs_t1->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_72_hrs_t1'] = $data_pmca_72_hrs_t1->hora_ws_min ? $data_pmca_72_hrs_t1->hora_ws_min : '-';

				$data_pmca_72_hrs_t2 = json_decode($model_info->pmca_72_hrs_t2);
				$view_data['pmca_72_hrs_t2'] = $data_pmca_72_hrs_t2->value ? $data_pmca_72_hrs_t2->value : '-';
				$view_data['ws_margarita_str_pmca_72_hrs_t2'] = $data_pmca_72_hrs_t2->ws_margarita_str ? $data_pmca_72_hrs_t2->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_72_hrs_t2'] = $data_pmca_72_hrs_t2->hora_ws_min ? $data_pmca_72_hrs_t2->hora_ws_min : '-';

				$data_pmca_72_hrs_t3 = json_decode($model_info->pmca_72_hrs_t3);
				$view_data['pmca_72_hrs_t3'] = $data_pmca_72_hrs_t3->value ? $data_pmca_72_hrs_t3->value : '-';
				$view_data['ws_margarita_str_pmca_72_hrs_t3'] = $data_pmca_72_hrs_t3->ws_margarita_str ? $data_pmca_72_hrs_t3->ws_margarita_str : '-';
				$view_data['hora_ws_min_pmca_72_hrs_t3'] = $data_pmca_72_hrs_t3->hora_ws_min ? $data_pmca_72_hrs_t3->hora_ws_min : '-';
				
				// $evidence_file_name = remove_file_prefix($model_info->evidence_file);
				// $html_evidence_file = $model_info->evidence_file ? anchor(get_uri("air_synoptic_data_upload/download_file/".$id_synoptic_data."/evidence_file"), "<i class='fa fa-cloud-download'></i>", array("title" => $evidence_file_name))." ".$evidence_file_name : "-";
				// $view_data['html_evidence_file'] = $html_evidence_file;

				$created_by = $this->Users_model->get_one($view_data['model_info']->created_by);
				$creador = $created_by->first_name." ".$created_by->last_name;
				
				// if($view_data['model_info']->modified_by){
				// 	$modified_by = $this->Users_model->get_one($view_data['model_info']->modified_by);
				// 	$modificador = ($modified_by->id)?$modified_by->first_name." ".$modified_by->last_name:"-";
				// }else{
				// 	$modificador = "-";
				// }
				
				$view_data['created_by'] = $creador;
				// $view_data['modified_by'] = $modificador;
				
				$this->load->view('air_synoptic_data_upload/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

	function upload_file() {
        upload_file_to_temp();
    }

	function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

	function create_air_synoptic_data_folder($id_synoptic_data) {

		$air_synoptic_data = $this->Air_synoptic_data_model->get_one($id_synoptic_data);
		$id_cliente = $air_synoptic_data->id_client;
		$id_proyecto = $air_synoptic_data->id_project;
		
		if(!file_exists(__DIR__.'/../../files/air_synoptic_data_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/synoptic_data_'.$id_synoptic_data)) {
			if(mkdir(__DIR__.'/../../files/air_synoptic_data_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/synoptic_data_'.$id_synoptic_data, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
		
	}

	function download_file($id_synoptic_data, $file_type = "evidence_file") {

		$air_synoptic_data = $this->Air_synoptic_data_model->get_one($id_synoptic_data);
		$id_cliente = $air_synoptic_data->id_client;
		$id_proyecto = $air_synoptic_data->id_project;
		
		if(!$air_synoptic_data){
			redirect("forbidden");
		}
		
		$filename = $air_synoptic_data->$file_type;
        $file_data = serialize(array(array("file_name" => $filename)));

        download_app_files("files/air_synoptic_data_files/client_".$id_cliente."/project_".$id_proyecto."/synoptic_data_".$id_synoptic_data."/", $file_data);
		
    }

	function delete_file() {
				
		$id = $this->input->post("id");
		$file_type = $this->input->post("file_type");

		$air_synoptic_data = $this->Air_synoptic_data_model->get_one($id);
		
		if(!$air_synoptic_data){
			redirect("forbidden");
		}

		$campo_nuevo = "";
		if($file_type == "evidence_file"){
			$campo_nuevo = $this->load->view("includes/air_synoptic_evidence_file_uploader", array(
				"upload_url" => get_uri("air_synoptic_data_upload/upload_file"),
				"validation_url" =>get_uri("air_synoptic_data_upload/validate_file")
			), true);
		}

		echo json_encode(array("success" => true, 'message' => lang('file_deleted'), 'new_field' => $campo_nuevo, 'file_type' => $file_type, 'id_campo' => $id));

    }

	function get_excel(){
		
		$id_usuario = $this->session->user_id;
		$id_proyecto = $this->session->project_context;
		$id_cliente = $this->login_user->client_id;
		
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$project_info = $this->Projects_model->get_one($id_proyecto);
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto
		);
		$list_data = $this->Air_synoptic_data_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_excel($data);
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
		$nombre_columnas[] = array("nombre_columna" => lang("date"), "id_tipo_campo" => "date");
		$nombre_columnas[] = array("nombre_columna" => lang("pmca_24_hrs_t1"), "id_tipo_campo" => "pmca_24_hrs_t1");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_24_hrs_t2"), "id_tipo_campo" => "pmca_24_hrs_t2");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_24_hrs_t3"), "id_tipo_campo" => "pmca_24_hrs_t3");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_48_hrs_t1"), "id_tipo_campo" => "pmca_48_hrs_t1");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_48_hrs_t2"), "id_tipo_campo" => "pmca_48_hrs_t2");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_48_hrs_t3"), "id_tipo_campo" => "pmca_48_hrs_t3");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_72_hrs_t1"), "id_tipo_campo" => "pmca_72_hrs_t1");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_72_hrs_t2"), "id_tipo_campo" => "pmca_72_hrs_t2");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		$nombre_columnas[] = array("nombre_columna" => lang("pmca_72_hrs_t3"), "id_tipo_campo" => "pmca_72_hrs_t3");
		$nombre_columnas[] = array("nombre_columna" => "ws_margarita_str", "id_tipo_campo" => "ws_margarita_str");
		$nombre_columnas[] = array("nombre_columna" => "hora_ws_min", "id_tipo_campo" => "hora_ws_min");

		//$nombre_columnas[] = array("nombre_columna" => lang("backup_document"), "id_tipo_campo" => "backup_document");
		//$nombre_columnas[] = array("nombre_columna" => lang("observations"), "id_tipo_campo" => "observations");
		$nombre_columnas[] = array("nombre_columna" => lang("created_date"), "id_tipo_campo" => "created_date");
		//$nombre_columnas[] = array("nombre_columna" => lang("modified_date"), "id_tipo_campo" => "modified_date");
		
		// HEADER
		$fecha = get_date_format(date('Y-m-d'), $id_proyecto);
		$hora = convert_to_general_settings_time_format($id_proyecto, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $id_proyecto));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("synoptic_data"))
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

		$array_pmca = array('pmca_24_hrs_t1','pmca_24_hrs_t2','pmca_24_hrs_t3','pmca_48_hrs_t1','pmca_48_hrs_t2','pmca_48_hrs_t3','pmca_72_hrs_t1','pmca_72_hrs_t2','pmca_72_hrs_t3');
		
		foreach($result as $res){

			foreach($nombre_columnas as $index_columnas => $columna){
				
				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$doc->getActiveSheet()->getColumnDimension($name_col)->setAutoSize(true);
				$valor = $res[$index_columnas];
				
				if(!is_array($columna)){
					
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
					
				} else {
					
					if($columna["id_tipo_campo"] == "date" || $columna["id_tipo_campo"] == "created_date" || $columna["id_tipo_campo"] == "modified_date"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif( in_array($columna["id_tipo_campo"], $array_pmca) ){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == 'ws_margarita_str' ){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);	
						
					} elseif($columna["id_tipo_campo"] == "backup_document" || $columna["id_tipo_campo"] == "observations"){
					
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
				
				//if($columna["id_tipo_campo"] != "unity"){
					$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				//}
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
		/*foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}*/
		
		$nombre_hoja = strlen(lang("synoptic_data")) > 31 ? substr(lang("synoptic_data"), 0, 28).'...' : lang("synoptic_data");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".$project_info->sigla."_".lang("synoptic_data")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
	
	}

	private function _make_row_excel($data) {

		$row_data = array();	
		$row_data[] = get_date_format($data->date, $data->id_project);
		
		$pmca_24_hrs_t1 = json_decode($data->pmca_24_hrs_t1);
		$pmca_24_hrs_t2 = json_decode($data->pmca_24_hrs_t2);
		$pmca_24_hrs_t3 = json_decode($data->pmca_24_hrs_t3);
		$pmca_48_hrs_t1 = json_decode($data->pmca_48_hrs_t1);
		$pmca_48_hrs_t2 = json_decode($data->pmca_48_hrs_t2);
		$pmca_48_hrs_t3 = json_decode($data->pmca_48_hrs_t3);
		$pmca_72_hrs_t1 = json_decode($data->pmca_72_hrs_t1);
		$pmca_72_hrs_t2 = json_decode($data->pmca_72_hrs_t2);
		$pmca_72_hrs_t3 = json_decode($data->pmca_72_hrs_t3);

		$row_data[] = $pmca_24_hrs_t1->value ? $pmca_24_hrs_t1->value : '-';
		$row_data[] = $pmca_24_hrs_t1->ws_margarita_str ? $pmca_24_hrs_t1->ws_margarita_str : '-';
		$row_data[] = $pmca_24_hrs_t1->hora_ws_min ? $pmca_24_hrs_t1->hora_ws_min : '-';
		
		$row_data[] = $pmca_24_hrs_t2->value ? $pmca_24_hrs_t2->value : '-';
		$row_data[] = $pmca_24_hrs_t2->ws_margarita_str ? $pmca_24_hrs_t2->ws_margarita_str : '-';
		$row_data[] = $pmca_24_hrs_t2->hora_ws_min ? $pmca_24_hrs_t2->hora_ws_min : '-';
		
		$row_data[] = $pmca_24_hrs_t3->value ? $pmca_24_hrs_t3->value : '-';
		$row_data[] = $pmca_24_hrs_t3->ws_margarita_str ? $pmca_24_hrs_t3->ws_margarita_str : '-';
		$row_data[] = $pmca_24_hrs_t3->hora_ws_min ? $pmca_24_hrs_t3->hora_ws_min : '-';
		
		$row_data[] = $pmca_48_hrs_t1->value ? $pmca_48_hrs_t1->value : '-';
		$row_data[] = $pmca_48_hrs_t1->ws_margarita_str ? $pmca_48_hrs_t1->ws_margarita_str : '-';
		$row_data[] = $pmca_48_hrs_t1->hora_ws_min ? $pmca_48_hrs_t1->hora_ws_min : '-';
		
		$row_data[] = $pmca_48_hrs_t2->value ? $pmca_48_hrs_t2->value : '-';
		$row_data[] = $pmca_48_hrs_t2->ws_margarita_str ? $pmca_48_hrs_t2->ws_margarita_str : '-';
		$row_data[] = $pmca_48_hrs_t2->hora_ws_min ? $pmca_48_hrs_t2->hora_ws_min : '-';
		
		$row_data[] = $pmca_48_hrs_t3->value ? $pmca_48_hrs_t3->value : '-';
		$row_data[] = $pmca_48_hrs_t3->ws_margarita_str ? $pmca_48_hrs_t3->ws_margarita_str : '-';
		$row_data[] = $pmca_48_hrs_t3->hora_ws_min ? $pmca_48_hrs_t3->hora_ws_min : '-';
		
		$row_data[] = $pmca_72_hrs_t1->value ? $pmca_72_hrs_t1->value : '-';
		$row_data[] = $pmca_72_hrs_t1->ws_margarita_str ? $pmca_72_hrs_t1->ws_margarita_str : '-';
		$row_data[] = $pmca_72_hrs_t1->hora_ws_min ? $pmca_72_hrs_t1->hora_ws_min : '-';
		
		$row_data[] = $pmca_72_hrs_t2->value ? $pmca_72_hrs_t2->value : '-';
		$row_data[] = $pmca_72_hrs_t2->ws_margarita_str ? $pmca_72_hrs_t2->ws_margarita_str : '-';
		$row_data[] = $pmca_72_hrs_t2->hora_ws_min ? $pmca_72_hrs_t2->hora_ws_min : '-';
		
		$row_data[] = $pmca_72_hrs_t3->value ? $pmca_72_hrs_t3->value : '-';
		$row_data[] = $pmca_72_hrs_t3->ws_margarita_str ? $pmca_72_hrs_t3->ws_margarita_str : '-';
		$row_data[] = $pmca_72_hrs_t3->hora_ws_min ? $pmca_72_hrs_t3->hora_ws_min : '-';
		
		//$row_data[] = ($data->evidence_file) ? remove_file_prefix($data->evidence_file) : "-";;
		//$row_data[] = ($data->observations) ? $data->observations : "-";
		$row_data[] = time_date_zone_format($data->created, $data->id_project);
		//$row_data[] = ($data->modified) ? time_date_zone_format($data->modified, $data->id_project) : "-";

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

}
