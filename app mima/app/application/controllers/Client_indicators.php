<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_indicators extends MY_Controller {
	
	private $id_modulo_cliente;
	private $id_submodulo_cliente;
	
    function __construct() {
        parent::__construct();
        //$this->init_permission_checker("client");
		$this->id_modulo_cliente = 8;
		$this->id_submodulo_cliente = 9;
		
		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;
		$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		
		// Bloqueo de URL cuando la Disponibilidad de Módulos (nivel Cliente) para Proyectos esté deshabilitada.
		$this->block_url_client_context($id_cliente, 3);
		
    }

    function index() {
	
		$project = $this->Projects_model->get_one($this->session->project_context);
		$indicators = $this->Indicators_model->get_all_where(array("id_project" => $project->id, "id_client" => $project->client_id, "deleted" => 0))->result();
		$view_data["project_info"] = $project;
		//$indicators = $this->Indicators_model->get_details()->result();
		if(!empty($indicators)){
			$array_indicators_name = array();
			foreach($indicators as $indicator){
				$array_indicators_name[$indicator->id] = $indicator->indicator_name;
			}
			$view_data['indicators_names']= $array_indicators_name;
			$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
			$view_data["puede_eliminar"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
			
			$this->template->rander("waste/client/indicators/index", $view_data);
			
		}else{
			$this->template->rander("waste/client/indicators/index", $view_data);
		}
    }

	function list_data($id_indicator){
		
		$proyecto = $this->Projects_model->get_one($this->session->project_context);
		$id_projecto = $proyecto->id;
		
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		
		if(isset($id_indicator)){
			
			$list_data = $this->Client_indicators_model->get_all_where(array("id_indicador" => $id_indicator, "deleted" => 0))->result();
			$result = array();
			foreach ($list_data as $data) {
				
				if($puede_ver == 1){//Todos
					$result[] = $this->_make_row($data,$id_projecto);
				} 
				if($puede_ver == 2){ //Propios
					if($id_usuario == $data->created_by){
						$result[] = $this->_make_row($data,$id_projecto);
					}
				}
				if($puede_ver == 3){ //Ninguno
					$result[1] = array();
				}
				
			}
			echo json_encode(array("data" => $result));
			
		}

	}
	
    function _row_data($id,$id_project) {
        $options = array(
            "id" => $id
        );
		
        $data = $this->Client_indicators_model->get_details($options)->row();
		
        return $this->_make_row($data, $id_project);
    }
	
	function _make_row($data, $id_proyecto){
		
		$id_usuario = $this->session->user_id;
		$puede_ver = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");
		$puede_editar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "editar");
		$puede_eliminar = $this->profile_access($id_usuario, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$f_desde = get_date_format($data->f_desde, $id_proyecto);
		$f_hasta = get_date_format($data->f_hasta, $id_proyecto);

		$row_data = array();
		$row_data[] = $data->id;
		$row_data[] = $data->created_by;
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$row_data[] = $puede_eliminar;
		}
		
		$row_data[] = modal_anchor(get_uri("client_indicators/view/".$data->id), to_number_project_format($data->valor, $id_proyecto), array("class" => "view", "title" => lang('view_period'), "data-post-id" => $data->id));
		$row_data[] = $f_desde;
		$row_data[] = $f_hasta;
		$row_data[] = get_date_format($data->created, $id_proyecto);
		$row_data[] = $data->modified ? get_date_format($data->modified, $id_proyecto) : "-";
		
		$view =  modal_anchor(get_uri("client_indicators/view/".$data->id), "<i class='fa fa-eye'></i>", array("class" => "view", "title" => lang('view_period'), "data-post-id" => $data->id));
		$edit = modal_anchor(get_uri("client_indicators/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_period'), "data-post-id" => $data->id));
		$delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_period'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("client_indicators/delete"), "data-action" => "delete-confirmation"));
		
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
	
	function modal_form($id_indicator){

		$id = $this->input->post('id');
		
		if(isset($id)){
			$client_indicators_info = $this->Client_indicators_model->get_one($id);
			$view_data['id_client_indicator'] = $client_indicators_info->id;
			$view_data['value'] = $client_indicators_info->valor;
			$view_data['date_since'] =$client_indicators_info->f_desde;
			$view_data['date_until'] = $client_indicators_info->f_hasta;
			$view_data['id_indicator'] = $client_indicators_info->id_indicador;
		}

		if(isset($id_indicator)){
			$view_data['id_indicator'] = $id_indicator;
		}
		$project = $this->Projects_model->get_one($this->session->project_context);
		$view_data['id_project'] = $project->id;
		$view_data['label_column'] = "col-md-3";
		$view_data['field_column'] = "col-md-9";
		$this->load->view('waste/client/indicators/modal_form', $view_data);
		
	}
	
    function view($client_indicators_id = 0) {
        //$this->access_only_allowed_members();

        if ($client_indicators_id) {
			
			$proyecto = $this->Projects_model->get_one($this->session->project_context);
			$id_proyecto = $proyecto->id;
            $client_indicators_info = $this->Client_indicators_model->get_one($client_indicators_id);
			
            if ($client_indicators_info){
				
				$view_data['model_info'] = $client_indicators_info;
                $view_data['value'] = to_number_project_format($client_indicators_info->valor, $id_proyecto);
                $view_data['date_since'] = get_date_format($client_indicators_info->f_desde, $id_proyecto);
                $view_data['date_until'] = get_date_format($client_indicators_info->f_hasta, $id_proyecto);
				$view_data['id_proyecto'] = $id_proyecto;
				
				$created_by = $this->Users_model->get_one($view_data['model_info']->created_by);
				$creador = $created_by->first_name." ".$created_by->last_name;
				if($view_data['model_info']->modified_by){
					$modified_by = $this->Users_model->get_one($view_data['model_info']->modified_by);
					$modificador = ($modified_by->id)?$modified_by->first_name." ".$modified_by->last_name:"-";
				}else{
					$modificador = "-";
				}
				
				$view_data['created_by'] = $creador;
				$view_data['modified_by'] = $modificador;

				$this->load->view('waste/client/indicators/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function save(){
		
		$id_client_indicator = $this->input->post('id_client_indicator');
		
		$value = $this->input->post('value');
		$fecha_desde = $this->input->post('date_since');
		$fecha_hasta = $this->input->post('date_until');
		$id_indicator = $this->input->post('id_indicator');
		$id_project = $this->input->post('id_project');
		
		$save_options = array(
			"valor" => $value,
			"f_desde"=> $fecha_desde ,
			"f_hasta"=> $fecha_hasta,
			"id_indicador"=> $id_indicator,
		);
		
		if($id_client_indicator){
			$save_options["modified_by"] = $this->login_user->id;
			$save_options["modified"] = get_current_utc_time();
		}else{
			$save_options["created_by"] = $this->login_user->id;
			$save_options["created"] = get_current_utc_time();
		}
		
		$save = $this->Client_indicators_model->save($save_options, $id_client_indicator);
		
		if ($save) {
			echo json_encode(array("success" => true,"data" => $this->_row_data($save,$id_project), 'id' => $save, 'message' => lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
		}
		
	}
	
    function delete() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
        if ($this->input->post('undo')) {
            if ($this->Client_indicators_model->delete($id, true)) {
                echo json_encode(array("success" =>true, "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Client_indicators_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function delete_multiple(){

		$data_ids = json_decode($this->input->post('data_ids'));
		$id_user = $this->session->user_id;
		
		// VALIDACIÓN DE ELIMINACIÓN DE REGISTROS SEGÚN PERFIL
		$puede_eliminar = $this->profile_access($id_user, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");

		$eliminar = TRUE;
		foreach($data_ids as $id){
			if($puede_eliminar == 2){ // Propios
				$row = $this->Client_indicators_model->get_one($id);
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
			if($this->Client_indicators_model->delete($id)) {
				$deleted_values = true;
			} else {
				$deleted_values = false;
				break;
			}
		}
					
		if($deleted_values){
			echo json_encode(array("success" => true, 'message' => lang('multiple_record_deleted')));
		} else {
			echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted_2')));
		}	
		
		
	}

	function get_load_table(){
		
		$id_indicador = $this->input->post('id_indicador');
		$indicator_info = $this->Indicators_model->get_one($id_indicador);
		$indicator_name = clean($indicator_info->indicator_name);
		$client_info = $this->Clients_model->get_one($indicator_info->id_client);
		$project_info = $this->Projects_model->get_one($indicator_info->id_project);
		$filename = $client_info->sigla.'_'.$project_info->sigla.'_'.$indicator_name.'_'.date("Y-m-d");

		$puede_agregar = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "agregar");
		$puede_eliminar = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "eliminar");
		
		$html = '';
		$html .='<div class="page-title panel-sky clearfix">' .'<h1>'. $indicator_name .'</h1>';
		$html .='<div class="title-button-group">';
		$html .='<div class="btn-group" role="group">';
		
		if($puede_eliminar != 3){ // 3 = Perfil Eliminar Ninguno 
			$html .= '<span style="cursor: not-allowed;">'.js_anchor("<i class='fa fa-trash'></i> ".lang("delete_selected"), array('title' => lang('delete_periods'), "id" => "delete_selected_rows", "class" => "delete btn btn-danger", "data-action" => "delete-confirmation", "data-custom" => true, "disabled" => "disabled", "style" => "pointer-events: none;")).'</span>';
		}
		
		$html .='<button type="button" class="btn btn-success" id="excel" data-filename="'.$filename.'"><i class="fa fa-table"></i> '.lang('export_to_excel').'</button>';
		$html .='</div>';
		
		if($puede_agregar != 1) {
			$html .=  modal_anchor("", "<i class='fa fa-plus-circle'></i> " . lang('add_period'), array("class" => "btn btn-default", "title" => lang('add_period'), "disabled" => "disabled"));
		} else {
			$html .=  modal_anchor(get_uri("client_indicators/modal_form/".$id_indicador), "<i class='fa fa-plus-circle'></i> " . lang('add_period'), array("class" => "btn btn-default", "title" => lang('add_period')));
		}

		$html .='</div>';
		$html .='</div>';
		$html .='<div class="table-responsive">';
		$html .='<table id="client_indicators-table" class="display" cellspacing="0" width="100%"> ';
		$html .='</table>';
		$html .='</div>';			
		echo $html;				
							
	}
}