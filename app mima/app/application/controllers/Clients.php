<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clients extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();
		
		//FILTRO CLIENTE ACTIVO/INACTIVO
		$estados_cliente = array();
		$estados_cliente[] = array("id" => "", "text" => "- ".lang("status")." -");
		$estados_cliente[] = array("id" => "activo", "text" => lang("active"));
		$estados_cliente[] = array("id" => "inactivo", "text" => lang("inactive"));
		$view_data["estados_cliente_dropdown"] = json_encode($estados_cliente);
		
        $this->template->rander("clients/index", $view_data);
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
		
		$array_client_groups = $this->AYN_Clients_groups_model->get_all_where(array(
			"id_client" => $client_id,
			"deleted" => 0
		))->result_array();
		
		$view_data["array_client_groups"] = $array_client_groups;

        $this->load->view('clients/modal_form', $view_data);
    }

    /* insert or update a client */

    function save() {
        $client_id = $this->input->post('id');
        //$this->access_only_allowed_members_or_client_contact($client_id);
		
		$client_groups_id = (array)$this->input->post("client_groups_id");
		$client_groups = (array)$this->input->post("client_groups");
		array_shift($client_groups);
		//$client_groups = array_filter($client_groups);
		
		// Validación de nombres de grupos repetidos
		$array_grupos = array();
		$array_grupos_cliente = array();
		if(count($client_groups)){
			foreach($client_groups as $index => $group){
				if(is_array($group)){
					$group_value = array_values($group);
					$array_grupos[] = $group_value[0];
					$array_grupos_cliente[] = key($group);
				} else {
					$array_grupos[] = $group;
				}
			}
			if(count($client_groups) !== count(array_unique($array_grupos))){
				echo json_encode(array("success" => false, 'message' => lang('duplicated_group_names_msj')));
				exit();
			}
			
			// Validación de grupos sin nombre.
			foreach($array_grupos as $id_grupo){
				if(empty($id_grupo)){
					echo json_encode(array("success" => false, 'message' => lang('no_name_group_msj')));
					exit();
				}
			}
			
		}
		
        validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required",
			"sigla" => "required",
        ));
		
        $data = array(
            "company_name" => $this->input->post('company_name'),
			"sigla" => trim($this->input->post('sigla')),
			"rut" => $this->input->post('rut'),
			"giro" => $this->input->post('giro'),
            "pais" => $this->input->post('pais'),
            "ciudad" => $this->input->post('ciudad'),
            "comuna" => $this->input->post('comuna'),
            "direccion" => $this->input->post('direccion'),
            "fono" => $this->input->post('fono'),
			"contacto" => $this->input->post('contacto'),
            "website" => $this->input->post('website'),
            //"logo" => $this->input->post('logo'),
			"color_sitio" => $this->input->post('color_sitio'),
			"habilitado" => $this->input->post('habilitado'),
        );
		
		if($client_id){
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		}else{
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}

        if (is_null($data["habilitado"])){
            $data["habilitado"] = 0;
        }
		
		if ($this->Clients_model->is_sigla_exists($data["sigla"], $client_id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_initial')));
			exit(); 
		}
		
		if ($this->Clients_model->is_company_name_exists($data["company_name"], $client_id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_company_name')));
			exit(); 
		}	
		
        $save_id = $this->Clients_model->save($data, $client_id);
		
		//GUARDAR CONFIGURACIÓN DE DISPONIBILIDAD DE MÓDULOS A NIVEL DE CLIENTE
		$array_client_module_availability_settings = array();
		$client_module_availability_settings = $this->Client_module_availability_model->get_all_where(array("id_cliente" => $save_id, "deleted" => 0))->result_array();
		foreach($client_module_availability_settings as $setting){
			$array_client_module_availability_settings[] = $setting["id_modulo"];
		}
		$client_context_modules = $this->Client_context_modules_model->get_details()->result();
		foreach($client_context_modules as $mod){
			if($mod->nivel_cliente){
				if(!in_array($mod->id, $array_client_module_availability_settings)){
					$data = array(
						"id_cliente" => $save_id,
						"id_modulo" => $mod->id,
						"disponible" => 1,
						"deleted" => 0
					);
					$this->Client_module_availability_model->save($data);
				}
			}
		}
		// FIN GUARDAR CONFIGURACIÓN DE DISPONIBILIDAD DE MÓDULOS A NIVEL DE CLIENTE
		
		if($client_id){
			$id_cliente = (int)$client_id;
			$clients_rel_project = $this->Forms_model->get_all_where(array("id_cliente" => $id_cliente, "deleted" => 0))->result();
			foreach($clients_rel_project as $value){

				$project_rel_form = $this->Form_rel_project_model->get_one_where(array("id_formulario" => $value->id, "deleted" => 0));
				$project = $this->Projects_model->get_one($project_rel_form->id_proyecto);

				$nombre_form = $name = str_replace(' ', '', $value->nombre);
				$sigla_cliente = $data["sigla"];
				$sigla_project = $project->sigla;
				$numero_form = $value->numero;
				
				$codigo = $numero_form.$sigla_cliente.$sigla_project.$nombre_form;
				
				$data_client = array("codigo" => $codigo);
				$save_client = $this->Forms_model->save($data_client, $value->id);

			}
			
			// Guardar grupos del cliente.
			$data_client_groups = array();
			
			if(count($client_groups)){
				
				foreach($client_groups as $index => $group){
					
					if(is_array($group)){ // Si es array, es un grupo ya existente del cliente.
						
						$group_id = key($group);
						$group_value = array_values($group);
						$group_name = $group_value[0];
						$client_group = $this->AYN_Clients_groups_model->get_one($group_id);
						
						if($client_group->group_name != $group_name){
							$data_client_groups = array();
							$data_client_groups["group_name"] = $group_name;
							$data_client_groups["modified_by"] = $this->login_user->id;
							$data_client_groups["modified"] = get_current_utc_time();
							$client_group_save = $this->AYN_Clients_groups_model->save($data_client_groups, $group_id);
						}
																		
					} else {
						
						$data_client_groups = array();
						$data_client_groups["id_client"] = $client_id;
						$data_client_groups["group_name"] = $group;
						$data_client_groups["created_by"] = $this->login_user->id;
						$data_client_groups["created"] = get_current_utc_time();
						$save_client_group_id = $this->AYN_Clients_groups_model->save($data_client_groups);
												
					}
					
				}
				
				// Eliminar Grupos
				$array_grupos_cliente;
				foreach($client_groups_id as $id_grupo){
					if(!in_array($id_grupo, $array_grupos_cliente)){
						
						// No eliminar grupo que tenga un usuario asociado
						$group_users = $this->Users_model->get_all_where(array(
							"id_client_group" => $id_grupo,
							"deleted" => 0
						))->result_array();
						
						if(count($group_users)){
							echo json_encode(array("success" => false, 'message' => lang("no_delete_associated_group_msj"), "id" => $client_id, "type" => "client_group"));
							exit(); 
						}
						
						$client_group_delete = $this->AYN_Clients_groups_model->delete($id_grupo);
					} 
				}

			}else{// SI SE BORRAN TODOS LOS GRUPOS
			
				$client_groups = $this->AYN_Clients_groups_model->get_all_where(
				array(
					"id_client" => $client_id,
					"deleted" => 0
				))->result();
				
				foreach($client_groups as $client_group){
					$client_group_delete = $this->AYN_Clients_groups_model->delete($client_group->id);
				}
				
			}
			
			// Guardar configuración general del cliente (si no la tiene)
			// Consultar si el cliente tiene la configuración
			// Si no la tiene guardala
			$general_settings_client = $this->General_settings_clients_model->get_one_where(array(
				"id_cliente" => $client_id,
				"deleted" => 0
			));			
			if(!$general_settings_client->id){
            	$this->General_settings_clients_model->save_default_settings($client_id);
			}

		} else {
			
			// Guardar configuración general del cliente
            $this->General_settings_clients_model->save_default_settings($save_id);
			
			// Guardar grupos del cliente
			if($save_id && count($client_groups)){
				$data_client_groups = array();
				foreach($client_groups as $nombre_grupo){
					$data_client_groups["id_client"] = $save_id;
					$data_client_groups["group_name"] = $nombre_grupo;
					$data_client_groups["created_by"] = $this->login_user->id;
					$data_client_groups["created"] = get_current_utc_time();
					$save_client_group_id = $this->AYN_Clients_groups_model->save($data_client_groups);
				}
			}
			
			$this->Reports_units_settings_clients_model->save_default_settings($save_id);
			
		}

        if ($save_id) {
			// crear carpeta cliente
            $crea_carpeta = $this->create_client_folder($save_id);
			
			// en caso de que se ingrese logo, agregarlo a la carpeta
			if($this->input->post('site_logo')){
				$value = str_replace("~", ":", $this->input->post('site_logo'));
				$value = move_temp_file("site-logo.png", "files/mimasoft_files/client_".$save_id."/", "", $value, "site-logo_".$save_id.".png");
				$data["logo"] = "site-logo_".$save_id;
				$this->Clients_model->save($data, $save_id);
			}

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function create_client_folder($client_id) {
		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$client_id)) {
			if(mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$client_id, 0777, TRUE)){
				return true;
			}else{
				return false;
			}
		}
	}

    /* delete or undo a client */

    function delete() {
		
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');		
		$this->delete_cascade_client($id);
		
        if ($this->input->post('undo')) {
            if ($this->Clients_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Clients_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
		
		$options = array(
			"habilitado" => $this->input->post("habilitado")
		);

        $list_data = $this->Clients_model->get_details($options)->result();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $options = array(
            "id" => $id
        );
        $data = $this->Clients_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
		
        $row_data = array($data->id,
            //anchor(get_uri("clients/view/" . $data->id), $data->company_name),
			modal_anchor(get_uri("clients/view/" . $data->id), $data->company_name, array("class" => "", "title" => lang('view_client'), "data-post-id" => $data->id)),
            $data->sigla,
			($data->habilitado) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>',
            to_decimal_format($data->total_projects)
        );

        $row_data[] = modal_anchor(get_uri("clients/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "", "title" => lang('view_client'), "data-post-id" => $data->id)).
				modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_client'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($client_id = 0, $tab = "") {
        $this->access_only_allowed_members();

        if ($client_id) {
            $options = array("id" => $client_id);
            $client_info = $this->Clients_model->get_details($options)->row();
            if ($client_info){
				
                $view_data['client_info'] = $client_info;
                $view_data["tab"] = $tab;
				
				$view_data["client_groups"] = $this->AYN_Clients_groups_model->get_all_where(array(
					"id_client" => $client_id,
					"deleted" => 0
				))->result_array();
				
				$this->load->view('clients/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	/* devolver dropdown con los proyectos de un cliente */
	
	function get_projects_of_client(){
	
		$id_cliente = $this->input->post('id_client');
		$col_label = $this->input->post('col_label')?$this->input->post('col_label'):'col-md-3';
		$col_projects = $this->input->post('col_projects')?$this->input->post('col_projects'):'col-md-9';

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="'.$col_label.'">'.lang('project').'</label>';
		$html .= '<div class="'.$col_projects.'">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	function get_projects_of_client_json(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$array_proyectos = array();
		$array_proyectos[] = array("id" => "", "text" => "-");
		if($id_cliente){
			$proyectos_de_cliente = $this->Projects_model->get_details(array("client_id" => $id_cliente))->result();
			foreach($proyectos_de_cliente as $proyecto){
				$array_proyectos[] = array("id" => $proyecto->id, "text" => $proyecto->title);
			}
		}
		
		echo json_encode($array_proyectos);
	}
	
	/* devolver la sigla de un cliente (para formar codigo de formulario) */
	function get_sigla_of_client(){
		
		$id_cliente = $this->input->post('id_client');
		$cliente = $this->Clients_model->get_one($id_cliente);
		$sigla = $cliente->sigla;
		echo $sigla;
	}
	
	/* Devuelte un html con los miembros de un cliente. Usado en projects modal */
	function get_users_of_client(){
    
        $id_cliente = $this->input->post('id_cliente');
		
        if(!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$miembros = $this->Users_model->get_all_where(array("user_type" => "client", "client_id" => $id_cliente, "deleted" => 0))->result();
        $array_miembros = array();
        if($miembros){
            foreach($miembros as $index => $miembro){
                $array_miembros[$miembro->id] = $miembro->first_name." ".$miembro->last_name;
            }
        }
        
        $html = ''; 
            
        // FILA POR DEFECTO
        $html .= '<div class="form-group">';
            $html .= '<label for="miembros" class="col-md-3">'.lang('members').'</label>';
            $html .= '<div class="col-md-9">';
            $html .= form_multiselect("miembros[]", $array_miembros, "", "id='miembros' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            $html .= '</div>';
        $html .= '</div>';

        if($id_cliente){
            echo $html;
        } else {
            echo "";
        }

    }
	
	/* Devuelve un html con un dropdown cargado con todos los clientes */
	function get_clients_dropdown(){
		
		$clientes = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="col-md-3">'.lang('client').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("client", array("" => "-") + $clientes, "", "id='client' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
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
	
	/* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }
	
	//show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->load->view("users/account_settings", $view_data);
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
	
	private function delete_cascade_client($id){
		
		//ELIMINA ALIAS DE CATEGORÍAS
		$categories_alias = $this->Categories_alias_model->get_all_where(array("id_cliente" => $id, "deleted" => 0))->result();
		if($categories_alias){
			foreach($categories_alias as $categorie_alia){
				$this->Categories_alias_model->delete($categorie_alia->id);
			}
		}
		
		//ELIMINA USUARIOS
		$users = $this->Users_model->get_all_where(array("client_id" => $id, "deleted" => 0))->result();
		if($users){
			foreach($users as $user){
				$this->Users_model->delete($user->id);
			}
		}
		
		//ELIMINA ESTADOS DE CUMPLIMIENTO DE COMPROMISOS
		$compromises_compliance_status = $this->Compromises_compliance_status_model->get_all_where(array("id_cliente" => $id, "deleted" => 0))->result();
		if($compromises_compliance_status){
			foreach($compromises_compliance_status as $compromise_compliance_status){
				$this->Compromises_compliance_status_model->delete($compromise_compliance_status->id);
			}	
		}
		
		//ELIMINA ESTADOS DE TRAMITACIÓN DE PERMISOS
		$permittings_procedure_status = $this->Permitting_procedure_status_model->get_all_where(array("id_cliente" => $id, "deleted" => 0))->result();
		if($permittings_procedure_status){
			foreach($permittings_procedure_status as $permitting_procedure_status){
				$this->Permitting_procedure_status_model->delete($permitting_procedure_status->id);
			}
		}
		
		//ELIMINA ESTADOS DE EVALUACIÓN DE COMUNIDADES (ACUERDOS)
		$communities_evaluation_status = $this->Communities_evaluation_status_model->get_all_where(array("id_cliente" => $id,"deleted" => 0))->result();
		if($communities_evaluation_status){
			foreach($communities_evaluation_status as $communitie_evaluation_statu){
				$this->Communities_evaluation_status_model->delete($communitie_evaluation_statu->id);
			}
		}	
		
		//ELIMINA CONFIGURACIÓN DE DISPONIBILIDAD DE MÓDULOS A NIVEL DE CLIENTE
		//$this->Client_module_availability_model->save_default_settings($save_id);	
		$client_module_availability_settings = $this->Client_module_availability_model->get_all_where(array("id_cliente" => $id, "deleted" => 0))->result();
		if($client_module_availability_settings){
			foreach($client_module_availability_settings as $setting){
				$this->Client_module_availability_model->delete($setting->id);
			}
		}
		
		$projects = $this->Projects_model->get_all_where(array("client_id" => $id, "deleted" => 0))->result();
		if($projects){
			foreach($projects as $project){

				//ELIMINA RELACIONAMIENTOS
				$rules = $this->Rule_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($rules){
					foreach($rules as $rule){
						$this->Rule_model->delete($rule->id);
					}
				}
				
				$assignments = $this->Assignment_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($assignments){
					foreach($assignments as $assignment){
						$assignments_combinations = $this->Assignment_combinations_model->get_all_where(array("id_asignacion" => $assignment->id, "deleted" => 0))->result();
						if($assignments_combinations){
							foreach($assignments_combinations as $assig_comb){
								$this->Assignment_combinations_model->delete($assig_comb->id);
							}
						}	
						$this->Assignment_model->delete($assignment->id);
					}
				}

				$calculations = $this->Calculation_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($calculations){
					foreach($calculations as $calculation){
						$this->Calculation_model->delete($calculation->id);
					}	
				}
				
				//ELIMINA FORMULARIOS, REALCION_PROYECTO_FORMULARIO Y VALORES FORMULARIOS
				$form_rel_project_model = $this->Form_rel_project_model->get_all_where(array("id_proyecto" => $project->id,"deleted" => 0))->result();
				if($form_rel_project_model){
					foreach($form_rel_project_model as $frpm){

						$forms_values = $this->Form_values_model->get_all_where(array("id_formulario_rel_proyecto" => $frpm->id, "deleted" => 0))->result();
						if($forms_values){
							foreach($forms_values as $fv){
								$this->Form_values_model->delete($fv->id);
							}
						}
						
						$campos_rel_formulario = $this->Field_rel_form_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
						if($campos_rel_formulario){
							foreach($campos_rel_formulario as $crf){
								$this->Field_rel_form_model->delete($crf->id);
							}
						}
						
						$form_rel_materiales = $this->Form_rel_material_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
						if($form_rel_materiales){
							foreach($form_rel_materiales as $form_rel_material){
								$this->Form_rel_material_model->delete($form_rel_material->id);
							}
						}
						
						$form_rel_materiales_rel_categorias = $this->Form_rel_materiales_rel_categorias_model->get_all_where(array("id_formulario" => $frpm->id_formulario, "deleted" => 0))->result();
						if($form_rel_materiales_rel_categorias){
							foreach($form_rel_materiales_rel_categorias as $form_rel_material_rel_categoria){
								$this->Form_rel_materiales_rel_categorias_model->delete($form_rel_material_rel_categoria->id);
							}
						}
						
						$this->Forms_model->delete($frpm->id_formulario);
						$this->Form_rel_project_model->delete($frpm->id);

					}
				}

				//ELIMINA UNIDADES FUNCIONALES
				$functional_unit = $this->Functional_units_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($functional_unit){
					foreach($functional_unit as $fn){	
						$this->Functional_units_model->delete($fn->id);
					}
				}

				//ELIMINA COMPROMISOS
				$compromise = $this->Compromises_model->get_one_where(array("id_proyecto" => $project->id, "deleted" => 0));
				if($compromise->id){
					
					$evaluated_compromises = $this->Evaluated_compromises_model->get_all_where(array("id_compromiso" => $compromise->id ,"deleted" => 0))->result();
					if($evaluated_compromises){
						foreach($evaluated_compromises as $evaluated_compromise){
							$this->Evaluated_compromises_model->delete($evaluated_compromise->id);
						}
					}
					
					$compromises_rel_fields = $this->Compromises_rel_fields_model->get_all_where(array("id_compromiso" => $compromise->id ,"deleted" => 0))->result();
					if($compromises_rel_fields){
						foreach($compromises_rel_fields as $compromises_rel_field){
							$this->Compromises_rel_fields_model->delete($compromises_rel_field->id);
						}
					}
					
					$values_compromises = $this->Values_compromises_model->get_all_where(array("id_compromiso" => $compromise->id , "deleted" => 0))->result();
					
					if($values_compromises){
						foreach($values_compromises as $values_compromise){

							$compromises_compliance_evaluations = $this->Compromises_compliance_evaluation_model->get_all_where(array("id_valor_compromiso" => $values_compromise->id , "deleted" => 0))->result();
							if($compromises_compliance_evaluations){
								foreach($compromises_compliance_evaluations as $compromises_compliance_evaluation){

									$compromises_compliance_evidences = $this->Compromises_compliance_evidences_model->get_all_where(array("id_evaluacion_cumplimiento_compromiso" =>$compromises_compliance_evaluation->id , "deleted" => 0))->result();
									if($compromises_compliance_evidences){
										foreach($compromises_compliance_evidences as $compromise_compliance_evidences){
											$this->Compromises_compliance_evidences_model->delete($compromise_compliance_evidences->id);
										}
									}
									$this->Compromises_compliance_evaluation_model->delete($compromises_compliance_evaluation->id);
								}
							}
							$this->Values_compromises_model->delete($values_compromise->id);
						}
					}
					
					$this->Compromises_model->delete($compromise->id);
					
				}

				//ELIMINA PERMISOS
				$permitting = $this->Permitting_model->get_one_where(array("id_proyecto" => $project->id, "deleted" => 0));
				if($permitting->id){
					
					$permitting_rel_fields = $this->Permitting_rel_fields_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
					if($permitting_rel_fields){
						foreach($permitting_rel_fields as $permitting_rel_field){
							$this->Permitting_rel_fields_model->delete($permitting_rel_field->id);
						}
					}
					
					$evaluated_permittings = $this->Evaluated_permitting_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
					if($evaluated_permittings){
						foreach($evaluated_permittings as $evaluated_permitting){
							$this->Evaluated_permitting_model->delete($evaluated_permitting->id);
						}	
					}
					
					$values_permitting = $this->Values_permitting_model->get_all_where(array("id_permiso" => $permitting->id, "deleted" => 0))->result();
					if($values_permitting){
						foreach($values_permitting as $value_permitting){

							$permitting_procedure_evaluations = $this->Permitting_procedure_evaluation_model->get_all_where(array("id_valor_permiso" =>$value_permitting->id , "deleted" => 0))->result();
							if($permitting_procedure_evaluations){
								foreach($permitting_procedure_evaluations as $permitting_procedure_evaluation){

									$permittings_procedure_evidences = $this->Permitting_procedure_evidences_model->get_all_where(array("id_evaluacion_tramitacion_permisos" => $permitting_procedure_evaluation->id , "deleted" => 0))->result();
									if($permittings_procedure_evidences){
										foreach($permittings_procedure_evidences as $permitting_procedure_evidences){
											$this->Permitting_procedure_evidences_model->delete($permitting_procedure_evidences->id);
										}
									}
									$this->Permitting_procedure_evaluation_model->delete($permitting_procedure_evaluation->id);
								}
							}
							$this->Values_permitting_model->delete($value_permitting->id);
						}
					}
					$this->Permitting_model->delete($permitting->id);
					
				}
				
				//ELIMINA COMUNIDADES
				//ELIMINA ACUERDOS
				$agreement_matrix_config = $this->Agreements_matrix_config_model->get_one_where(array("id_proyecto" => $project->id, "deleted" => 0));
				if($agreement_matrix_config->id){
					
					$agreements_rel_fields = $this->Agreements_rel_fields_model->get_all_where(array("id_agreement_matrix_config" =>$agreement_matrix_config->id, "deleted" => 0))->result();
					if($agreements_rel_fields){
						foreach($agreements_rel_fields as $agreement_rel_field){
							$this->Agreements_rel_fields_model->delete($agreement_rel_field->id);
						}
					}
					
					$values_agreements = $this->Values_agreements_model->get_all_where(array("id_agreement_matrix_config" =>$agreement_matrix_config->id ,"deleted" => 0))->result();
					if($values_agreements){
						foreach($values_agreements as $value_agreement){

							$agreements_monitoring = $this->Agreements_monitoring_model->get_all_where(array("id_valor_acuerdo" =>$value_agreement->id, "deleted" => 0))->result();
							if($agreements_monitoring){
								foreach($agreements_monitoring as $agreement_monitoring){

									$agreements_evidences = $this->Agreements_evidences_model->get_all_where(array("id_evaluacion_acuerdo" =>$agreement_monitoring->id , "deleted" => 0))->result();
									if($agreements_evidences){
										foreach($agreements_evidences as $agreement_evidence){
											$this->Agreements_evidences_model->delete($agreement_evidence->id);
										}
									}
									$this->Agreements_monitoring_model->delete($agreement_monitoring->id);
								}
							}
							$this->Values_agreements_model->delete($value_agreement->id);
						}
					}
					$this->Agreements_matrix_config_model->delete($agreement_matrix_config->id);
				
				}
				
				//ELIMINA FEEDBACK
				$feedback_matrix_config = $this->Feedback_matrix_config_model->get_one_where(array("id_proyecto" => $project->id, "deleted" => 0));
				if($feedback_matrix_config->id){

					$feedbacks_rel_fields = $this->Feedback_rel_fields_model->get_all_where(array("id_feedback_matrix_config" => $feedback_matrix_config->id, "deleted" => 0))->result();
					if($feedbacks_rel_fields){
						foreach($feedbacks_rel_fields as $feedback_rel_field){
							$this->Feedback_rel_fields_model->delete($feedback_rel_field->id);
						}
					}

					$values_feedbacks = $this->Values_feedback_model->get_all_where(array("id_feedback_matrix_config" => $feedback_matrix_config->id, "deleted" => 0))->result();
					if($values_feedbacks){
						foreach($values_feedbacks as $value_feedback){

							$feedbacks_monitoring = $this->Feedback_monitoring_model->get_all_where(array("id_valor_feedback" => $value_feedback->id, "deleted" => 0))->result();
							if($feedbacks_monitoring){
								foreach($feedbacks_monitoring as $feedback_monitoring){

									$feedbacks_monitoring_evidences = $this->Feedback_monitoring_evidences_model->get_all_where(array("id_evaluacion_feedback" => $feedback_monitoring->id, "deleted" => 0))->result();
									if($feedbacks_monitoring_evidences){
										foreach($feedbacks_monitoring_evidences as $feedback_monitoring_evidences){
											$this->Feedback_monitoring_evidences_model->delete($feedback_monitoring_evidences->id);
										}
									}
									$this->Feedback_monitoring_model->delete($feedback_monitoring->id);
								}
							}
							$this->Values_feedback_model->delete($value_feedback->id);
						}
					}
					$this->Feedback_matrix_config_model->delete($feedback_matrix_config->id);
					
				}
				
				//ELIMINA STAKEHOLDERS
				$stakeholder_matrix_config = $this->Stakeholders_matrix_config_model->get_one_where(array("id_proyecto" => $project->id, "deleted" => 0));
				if($stakeholder_matrix_config->id){

					$stakeholders_rel_fields = $this->Stakeholders_rel_fields_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix_config->id, "deleted" => 0))->result();
					if($stakeholders_rel_fields){
						foreach($stakeholders_rel_fields as $stakeholder_rel_field){
							$this->Stakeholders_rel_fields_model->delete($stakeholder_rel_field->id);
						}
					}

					$values_stakeholders = $this->Values_stakeholders_model->get_all_where(array("id_stakeholder_matrix_config" => $stakeholder_matrix_config->id, "deleted" => 0))->result();
					if($values_stakeholders){
						foreach($values_stakeholders as $value_stakeholder){
							$this->Values_stakeholders_model->delete($value_stakeholder->id);
						}
					}
					$this->Stakeholders_matrix_config_model->delete($stakeholder_matrix_config->id);
					
				}
	
				//ELIMINA UMBRALES
				$thresholds = $this->Thresholds_model->get_all_where(array("id_project" => $project->id, "deleted" => 0))->result();
				if($thresholds){
					foreach($thresholds as $threshold){
						$this->Thresholds_model->delete($threshold->id);
					}
				}

				//ELIMINA INDICADORES DE RESIDUOS
				$indicators = $this->Indicators_model->get_all_where(array("id_project" => $project->id, "deleted" => 0))->result();
				if($indicators){
					foreach($indicators as $indicator){

						$client_indicators = $this->Client_indicators_model->get_all_where(array("id_indicador" => $indicator->id, "deleted" => 0))->result();
						if($client_indicators){
							foreach($client_indicators as $client_indicator){
								$this->Client_indicators_model->delete($client_indicator->id);
							}	
						}
						$this->Indicators_model->delete($indicator->id);
					}
				}

				//ELIMINA CONFIGURACIONES
				$generals_settings = $this->General_settings_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($generals_settings){
					foreach($generals_settings as $general_setting){
						$this->General_settings_model->delete($general_setting->id);
					}
				}

				$reports_configuration = $this->Reports_configuration_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($reports_configuration){
					foreach($reports_configuration as $report_configuration){
						$this->Reports_configuration_model->delete($report_configuration->id);
					}	
				}

				$modules_availabilitys = $this->Module_availability_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($modules_availabilitys){
					foreach($modules_availabilitys as $module_availability){
						$this->Module_availability_model->delete($module_availability->id);
					}
				}

				$modules_footprint_units = $this->Module_footprint_units_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($modules_footprint_units){
					foreach($modules_footprint_units as $module_footprint_unit){
						$this->Module_footprint_units_model->delete($module_footprint_unit->id);
					}
				}

				$reports_units_settings = $this->Reports_units_settings_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($reports_units_settings){
					foreach($reports_units_settings as $report_unit_setting){
						$this->Reports_units_settings_model->delete($report_unit_setting->id);
					}
				}

				//ELIMINA SUBPROYECTOS
				$subprojects = $this->Subprojects_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($subprojects){
					foreach($subprojects as $subproject){
						$this->Subprojects_model->delete($subproject->id);
					}
				}

				//ELIMINA CAMPOS
				$fields = $this->Fields_model->get_all_where(array("id_proyecto" => $project->id, "deleted" => 0))->result();
				if($fields){
					foreach($fields as $field){
						$this->Fields_model->delete($field->id);
					}
				}
				
				//ELIMINA OTROS
				$client_environmental_footprints_settings = $this->Client_environmental_footprints_settings_model->get_all_where(array("id_proyecto" => $project->id ,"deleted" => 0))->result();
				if($client_environmental_footprints_settings){
					foreach($client_environmental_footprints_settings as $client_environmental_footprint_setting){
						$this->Client_environmental_footprints_settings_model->delete($client_environmental_footprint_setting->id);
					}
				}

				$clients_waste_settings = $this->Client_waste_settings_model->get_all_where(array("id_proyecto" => $project->id ,"deleted" => 0))->result();
				if($clients_waste_settings){
					foreach($clients_waste_settings as $client_waste_setting){
						$this->Client_waste_settings_model->delete($client_waste_setting->id);
					}
				}

				$client_consumptions_settings = $this->Client_consumptions_settings_model->get_all_where(array("id_proyecto" => $project->id ,"deleted" => 0))->result();
				if($client_consumptions_settings){
					foreach($client_consumptions_settings as $client_consumption_setting){
						$this->Client_consumptions_settings_model->delete($client_consumption_setting->id);
					}
				}

				$client_compromises_settings = $this->Client_compromises_settings_model->get_all_where(array("id_proyecto" => $project->id ,"deleted" => 0))->result();
				if($client_compromises_settings){
					foreach($client_compromises_settings as $client_compromise_setting){
						$this->Client_compromises_settings_model->delete($client_compromise_setting->id);
					}
				}

				$client_permitting_settings = $this->Client_permitting_settings_model->get_all_where(array("id_proyecto" => $project->id ,"deleted" => 0))->result();
				if($client_permitting_settings){
					foreach($client_permitting_settings as $client_permitting_setting){
						$this->Client_permitting_settings_model->delete($client_permitting_setting->id);
					}
				}
				
				//ELIMINA PROYECTO Y RELACIONES
				$this->Projects_model->delete($project->id);
				$this->Project_members_model->delete_members($project->id);
				$this->Project_rel_phases_model->delete_phases_rel_project($project->id);
				$this->Project_rel_pu_model->delete_pu_rel_project($project->id);
				$this->Project_rel_footprints_model->delete_footprints_rel_project($project->id);
				$this->Project_rel_material_model->delete_materials_rel_project($project->id);
				
			}

		}
			
	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */