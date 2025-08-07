<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Generals extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
    }

    function index() {
		$this->access_only_allowed_members();
		$access_info = $this->get_access_info("invoice");
        $this->template->rander("generals/index");
    }
	
	//modificar
	function modal_form() {
		
        $this->access_only_allowed_members();
        $client_context_profile_id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-2";
        $view_data['field_column'] = "col-md-10";

        $view_data["view"] = $this->input->post('view');
        $view_data['model_info'] = $this->Client_context_profiles_model->get_one($client_context_profile_id);
		
		if($client_context_profile_id){
			$view_data['client_context_modules'] = $this->Client_context_modules_model->get_details_edit_mode($client_context_profile_id)->result();
		} else {
			$view_data['client_context_modules'] = $this->Client_context_modules_model->get_modules_and_submodules()->result();
		}

        $this->load->view('generals/modal_form', $view_data);
    }
	
	
	function save() {

        $client_context_profile_id = $this->input->post('id');
		
		if($client_context_profile_id){
			$modules_rel_profiles_ids = $this->Client_context_modules_rel_profiles_model->get_setting_of_profile($client_context_profile_id)->result();
		}
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));
		
		$data_profile = array(
			"name" => $this->input->post("name"),
			"deleted" => 0
		);

		if(!$client_context_profile_id){
			$data_profile["created_by"] = $this->login_user->id;
			$data_profile["created"] = get_current_utc_time();
		} else {
			$data_profile["modified_by"] = $this->login_user->id;
			$data_profile["modified"] = get_current_utc_time();
		}

        $save_id = $this->Client_context_profiles_model->save($data_profile, $client_context_profile_id);	
		$client_context_modules = $this->Client_context_modules_model->get_modules_and_submodules()->result();
	
		//MODULOS
		$permisos_modulo_ver = array();
		$permisos_modulo_agregar = array();
		$permisos_modulo_editar = array();
		$permisos_modulo_borrar = array();
		$permisos_modulo_auditar = array();
		
		//SUBMODULOS
		$permisos_submodulo_ver = array();
		$permisos_submodulo_agregar = array();
		$permisos_submodulo_editar = array();
		$permisos_submodulo_borrar = array();
		$permisos_submodulo_auditar = array();
		
		foreach($client_context_modules as $module){
			if($module->id_client_context_submodule) {
				$permisos_submodulo_ver[] = $this->input->post($module->id_client_context_submodule."-permisos_submodulo_ver");
				$permisos_submodulo_agregar[] = $this->input->post($module->id_client_context_submodule."-permisos_submodulo_agregar");
				$permisos_submodulo_editar[] = $this->input->post($module->id_client_context_submodule."-permisos_submodulo_editar");
				$permisos_submodulo_borrar[] = $this->input->post($module->id_client_context_submodule."-permisos_submodulo_borrar");
				$permisos_submodulo_auditar[] = $this->input->post($module->id_client_context_submodule."-permisos_submodulo_auditar");
			} else {
				$permisos_modulo_ver[] = $this->input->post($module->id_client_context_module."-permisos_modulo_ver");
				$permisos_modulo_agregar[] = $this->input->post($module->id_client_context_module."-permisos_modulo_agregar");
				$permisos_modulo_editar[] = $this->input->post($module->id_client_context_module."-permisos_modulo_editar");
				$permisos_modulo_borrar[] = $this->input->post($module->id_client_context_module."-permisos_modulo_borrar");
				$permisos_modulo_auditar[] = $this->input->post($module->id_client_context_module."-permisos_modulo_auditar");
			}
		}
		
		//UPDATE
		if($modules_rel_profiles_ids){	
				
			foreach($modules_rel_profiles_ids as $rel){
				
				//MODULOS
				foreach($permisos_modulo_ver as $permiso){	
					$array_permiso = explode('-', $permiso);
					$data = array(
						"ver" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_agregar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"agregar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_editar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"editar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_borrar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"eliminar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_auditar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"auditar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				//SUBMODULOS
				foreach($permisos_submodulo_ver as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"ver" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_agregar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"agregar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_editar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"editar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_borrar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"eliminar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_auditar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"auditar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}

			}
		//INSERT	
		} else {
		
			$registros_modulo_insertados = array();
			
			foreach($permisos_modulo_ver as $permiso){	
				$array_permiso = explode('-', $permiso);
				$data = array(
					"id_client_context_profile" => $save_id,
					"id_client_context_module" => $array_permiso[0],
					"ver" => $array_permiso[2]
				);
				$save_permiso = $this->Client_context_modules_rel_profiles_model->save($data);
				$registros_modulo_insertados[] = $save_permiso;
			}
			
			foreach($registros_modulo_insertados as $id_registro){
				
				foreach($permisos_modulo_agregar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"agregar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_editar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"editar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_borrar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"eliminar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_modulo_auditar as $permiso){
					$array_permiso = explode('-', $permiso);
					$data = array(
						"auditar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $array_permiso[0],
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}

			}
			
			$registros_submodulo_insertados = array();
				
			foreach($permisos_submodulo_ver as $permiso){
				$array_permiso = explode('-', $permiso);
				$id_submodule = $array_permiso[0];
				$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
				$data = array(
					"id_client_context_profile" => $save_id,
					"id_client_context_module" => $id_client_module,
					"id_client_context_submodule" => $id_submodule,
					"ver" => $array_permiso[2]
				);
				$save_permiso = $this->Client_context_modules_rel_profiles_model->save($data);
				$registros_submodulo_insertados[] = $save_permiso;
			}
			
			foreach($registros_submodulo_insertados as $id_registro){
				
				foreach($permisos_submodulo_agregar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"agregar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_editar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"editar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_borrar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"eliminar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
				foreach($permisos_submodulo_auditar as $permiso){
					$array_permiso = explode('-', $permiso);
					$id_submodule = $array_permiso[0];
					$id_client_module = $this->Client_context_submodules_model->get_one($id_submodule)->id_client_context_module;
					$data = array(
						"auditar" => $array_permiso[2]
					);
					$where = array(
						"id_client_context_profile" => $save_id,
						"id_client_context_module" => $id_client_module,
						"id_client_context_submodule" => $id_submodule,
					);
					$save_permiso = $this->Client_context_modules_rel_profiles_model->update_where($data, $where);
				}
				
			}

		}

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	

	function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
		
		$profile_users = $this->Users_model->get_all_where(array("id_client_context_profile" => $id, "deleted" => 0))->result();
		if($profile_users){
			echo json_encode(array("success" => false, 'message' => lang('profile_warning')));
			exit();
		}
				
        if ($this->input->post('undo')) {
			
			$clients_modules_rel_profiles_model = $this->Client_context_modules_rel_profiles_model->get_all_where(array("id_client_context_profile" => $id))->result();
			foreach($clients_modules_rel_profiles_model as $cmp){
				$rel_id = (int)$cmp->id;
				$this->Client_context_modules_rel_profiles_model->delete_client_context_modules_rel_profiles($rel_id);
			}
			
            if ($this->Client_context_profiles_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
		
			$clients_modules_rel_profiles_model = $this->Client_context_modules_rel_profiles_model->get_all_where(array("id_client_context_profile" => $id))->result();
			foreach($clients_modules_rel_profiles_model as $cmp){
				$rel_id = (int)$cmp->id;
				$this->Client_context_modules_rel_profiles_model->delete_client_context_modules_rel_profiles($rel_id);
			}
			
            if ($this->Client_context_profiles_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }
	
	function list_data() {

        $this->access_only_allowed_members();

        $list_data = $this->Client_context_profiles_model->get_details()->result();
		
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
		
        $data = $this->Client_context_profiles_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
        $row_data = array(
			$data->id,
			modal_anchor(get_uri("generals/view/" . $data->id), $data->name, array("title" => lang('view_profile'))), 
			//$user->first_name, 
			//$data->created
		);

        $row_data[] =  modal_anchor(get_uri("generals/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_profile')))
				.  modal_anchor(get_uri("generals/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_profile'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_profile'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("generals/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }
	
	function view($client_context_profile_id = 0) {
        $this->access_only_allowed_members();

        if ($client_context_profile_id) {
            $options = array("id" => $client_context_profile_id);
            $profile_info = $this->Client_context_profiles_model->get_details($options)->row();
            if ($profile_info) {
			
                $view_data['profile_info'] = $profile_info;
				$view_data['profile'] = $this->Client_context_profiles_model->get_one($client_context_profile_id);
				$view_data['clients_modules'] = $this->Client_context_modules_model->get_details_edit_mode($client_context_profile_id)->result();
				$this->load->view('generals/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
}

