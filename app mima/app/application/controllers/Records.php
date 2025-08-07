<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Records extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();

        //$access_info = $this->get_access_info("invoice");
        //$view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
       // $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $this->template->rander("clients/index");
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

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }

    /* insert or update a client */

    function save() {
        $client_id = $this->input->post('id');

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required",
			"sigla" => "required",
        ));
		
		
        $data = array(
            "company_name" => $this->input->post('company_name'),
			"sigla" => $this->input->post('sigla'),
			"rut" => $this->input->post('rut'),
			"giro" => $this->input->post('giro'),
            "pais" => $this->input->post('pais'),
            "ciudad" => $this->input->post('ciudad'),
            "comuna" => $this->input->post('comuna'),
            "direccion" => $this->input->post('direccion'),
            "fono" => $this->input->post('fono'),
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

        if ($this->login_user->is_admin) {
            //$data["currency_symbol"] = $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "";
            //$data["currency"] = $this->input->post('currency') ? $this->input->post('currency') : "";
            //$data["disable_online_payment"] = $this->input->post('disable_online_payment') ? $this->input->post('disable_online_payment') : 0;
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
        if ($save_id) {
			// crear carpeta cliente
            $crea_carpeta = $this->create_client_folder($save_id);
			
			// en caso de que se ingrese logo, agregarlo a la carpeta
			if($this->input->post('site_logo')){
				$value = str_replace("~", ":", $this->input->post('site_logo'));
				$value = move_temp_file("site-logo.png", "files/client_".$save_id."/", "", $value, "site-logo_".$save_id.".png");
				$data["logo"] = "site-logo_".$save_id;
				$this->Clients_model->save($data, $save_id);
			}

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
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

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
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
		
        $list_data = $this->Clients_model->get_details()->result();
		
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );
        $data = $this->Clients_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
		
        $row_data = array($data->id,
            //anchor(get_uri("clients/view/" . $data->id), $data->company_name),
			modal_anchor(get_uri("clients/view/" . $data->id), $data->company_name, array("class" => "", "title" => lang('view_client'), "data-post-id" => $data->id)),
            $data->sigla,
            to_decimal_format($data->total_projects)
        );

        $row_data[] = modal_anchor(get_uri("clients/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "", "title" => lang('view_client'), "data-post-id" => $data->id)).
				modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_client'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($id_record) {
        //$this->access_only_allowed_members();

        if ($id_record) {
            $options = array("id" => $id_record);
            $record_info = $this->Forms_model->get_details($options)->row();
            if ($record_info){
				
                $view_data['record_info'] = $record_info;

                $this->template->rander("environmental_records/records/index", $view_data);
				//$this->load->view('clients/view', $view_data);
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

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="col-md-3">'.lang('project').'</label>';
		$html .= '<div class="col-md-9">';
		$html .= form_dropdown("project", array("" => "-") + $proyectos_de_cliente, "", "id='project' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	/* devolver dropdown con los proyectos de un cliente (especial fields) */
	
	function get_projects_of_client2(){
	
		$id_cliente = $this->input->post('id_client');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
		$proyectos_de_cliente = $this->Projects_model->get_dropdown_list(array("title"), "id", array("client_id" => $id_cliente));
		
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label for="project" class="col-md-2">'.lang('project').'</label>';
		$html .= '<div class="col-md-10">';
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
		
		$proyectos_de_cliente = $this->Projects_model->get_details(array("client_id" => $id_cliente))->result();
		$array_proyectos = array();
		$array_proyectos[] = array("id" => "", "text" => "-");
		foreach($proyectos_de_cliente as $proyecto){
			$array_proyectos[] = array("id" => $proyecto->id, "text" => $proyecto->title);
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
	
	/* Devuelte un html con los miembros de un cliente "Usado en projects modal"*/
	function get_users_of_client(){
    
        $id_cliente = $this->input->post('id_cliente');
		$tipo = "client";
        /*if (!$this->login_user->id) {
            redirect("forbidden");
        }*/
		
      

		$miembros = $this->Users_model->get_all_where(array("user_type" => $tipo,"client_id" => $id_cliente, "deleted" => 0))->result();
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
	
	
    /* add-remove start mark from client */

    function add_remove_star($client_id, $type = "add") {
        if ($client_id) {
            $view_data["client_id"] = $client_id;

            if ($type === "add") {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "add");
                $this->load->view('clients/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                $this->load->view('clients/star/not_starred', $view_data);
            }
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id)->result();
        $this->load->view('clients/star/clients_list', $view_data);
    }

    /* load projects tab  */

    function projects($client_id) {
        $this->access_only_allowed_members();

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = $client_id;
        $this->load->view("clients/projects/index", $view_data);
    }

    /* load payments tab  */

    function payments($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/payments/index", $view_data);
        }
    }

    /* load tickets tab  */

    function tickets($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tickets", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/tickets/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("clients/invoices/index", $view_data);
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id) {

        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->result();
        $view_data['client_id'] = $client_id;
        $this->load->view("clients/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->input->post('id'));
        $client_id = $this->input->post('client_id') ? $this->input->post('client_id') : $view_data['model_info']->client_id;

        $this->access_only_allowed_members();

        $view_data['client_id'] = $client_id;
        $this->load->view('clients/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');
        $this->access_only_allowed_members();


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $new_file_name = move_temp_file($file_name, $target_path);
                if ($new_file_name) {
                    $data = array(
                        "client_id" => $client_id,
                        "file_name" => $new_file_name,
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->access_only_allowed_members();

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("clients/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("clients/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

        $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->client_id) {
                redirect("forbidden");
            }

            $view_data['can_comment_on_files'] = false;

            $view_data["file_url"] = get_file_uri(get_general_file_path("client", $file_info->client_id) . $file_info->file_name);;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            $this->load->view("clients/files/view", $view_data);
        } else {
            show_404();
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

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->load->view("users/account_settings", $view_data);
    }

    /* load contacts tab  */

    function contacts($client_id) {
        $this->access_only_allowed_members();
		
        if ($client_id) {
            $view_data['client_id'] = $client_id;
            $this->load->view("clients/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();
		
		$user_id = $this->input->post('id');

        $view_data['model_info'] = $this->Users_model->get_one($user_id);
        $view_data['model_info']->client_id = $this->input->post('client_id');
        $this->load->view('clients/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members_or_client_contact($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('clients/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $this->load->view('users/social_links', $view_data);
        }
    }
	
	/* load contact details view */

	function view_contact($contact_id = 0) {
        $this->access_only_allowed_members();

        if ($contact_id) {
            $options = array("id" => $contact_id);
            $contact_info = $this->Users_model->get_details($options)->row();
            if ($contact_info) {
				
				//$view_data["preview"] = $this->get_preview_form($user_info->id);
                $view_data['user_info'] = $contact_info;
				$view_data['user'] = $this->Users_model->get_one($contact_id);
                //$view_data["tab"] = $tab;

				$this->load->view('users/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	

    /* insert/update a contact */

    function save_contact() {
		
        $contact_id = $this->input->post('contact_id');
        $client_id = $this->input->post('client_id');
		$cambia_contrasena = $this->input->post("new_password");

       // $this->access_only_allowed_members_or_contact_personally($contact_id);
		
        $user_data = array(
			"first_name" => $this->input->post("first_name"),
			"last_name" => $this->input->post("last_name"),
			"rut" => $this->input->post("rut"),
			"email" => trim($this->input->post('email')),
			"phone" => $this->input->post("phone"),
			"cargo" => $this->input->post("position"),
			"gender" => $this->input->post("gender")
        );
		
		if($contact_id){
			$user_data["modified_by"] = $this->login_user->id;
			$user_data["modified"] = get_current_utc_time();
		}else{
			$user_data["created_by"] = $this->login_user->id;
			$user_data["created"] = get_current_utc_time();
		}
		
        validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric",
			"email" => "required|valid_email",
        ));

        if (!$contact_id) {
            //inserting new contact. client_id is required
			
            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            //$user_data["email"] = trim($this->input->post('email'));
            //$user_data["password"] = md5($this->input->post('login_password'));
            //$user_data["created_at"] = get_current_utc_time();
			
			//validate duplicate email address
			if ($this->Users_model->is_email_exists($user_data["email"])) {
				echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
				exit();
			}
			
        }else{
			
			if($cambia_contrasena){
				validate_submitted_data(array(
					"login_password" => "required",
				));
				$user_data["password"] = md5($this->input->post('login_password'));
			}
			
			//validate duplicate email address
			if ($this->Users_model->is_email_exists($user_data["email"], $contact_id)) {
				echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
				exit();
			}
			
		}
		
        $save_id = $this->Users_model->save($user_data, $contact_id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function edit_contact() {
        $this->access_only_allowed_members();
		
		$user_id = $this->input->post('id');

        $view_data['model_info'] = $this->Users_model->get_one($user_id);
        $view_data['model_info']->client_id = $this->input->post('client_id');
        $this->load->view('clients/contacts/modal_form', $view_data);
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->input->post('facebook'),
            "twitter" => $this->input->post('twitter'),
            "linkedin" => $this->input->post('linkedin'),
            "googleplus" => $this->input->post('googleplus'),
            "digg" => $this->input->post('digg'),
            "youtube" => $this->input->post('youtube'),
            "pinterest" => $this->input->post('pinterest'),
            "instagram" => $this->input->post('instagram'),
            "github" => $this->input->post('github'),
            "tumblr" => $this->input->post('tumblr'),
            "vine" => $this->input->post('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $this->Social_links_model->save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => lang('record_updated')));
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

    /* delete or undo a contact */

    function delete_contact() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->input->post('id');

        if ($this->input->post('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        //$this->access_only_allowed_members_or_client_contact($client_id);

        $options = array("user_type" => "client", "client_id" => $client_id);
        $list_data = $this->Users_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->row();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";

        //$contact_link = anchor(get_uri("clients/contact_profile/" . $data->id), $full_name);
        if ($this->login_user->user_type === "client") {
            $contact_link = $full_name; //don't show clickable link to client
        }
		
        $row_data = array(
            $user_avatar,
            $full_name,
            $data->cargo,
            $data->email,
            $data->phone ? $data->phone : "-",
            //$data->skype ? $data->skype : "-"
        );

		$row_data[] =  modal_anchor(get_uri("clients/view_contact/" . $data->id), "<i class='fa fa-eye'></i>").
				  modal_anchor(get_uri("clients/edit_contact"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_contact'), "data-post-id" => $data->id, "data-post-client_id" => $data->client_id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("clients/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* open invitation modal */

    function invitation_modal() {


        validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members_or_client_contact($client_id);

        $view_data["client_info"] = $this->Clients_model->get_one($client_id);
        $this->load->view('clients/contacts/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation() {

        $client_id = $this->input->post('client_id');
        $email = trim($this->input->post('email'));

        validate_submitted_data(array(
            "client_id" => "required|numeric",
            "email" => "required|valid_email|trim"
        ));

        $this->access_only_allowed_members_or_client_contact($client_id);

        $email_template = $this->Email_templates_model->get_final_template("client_contact_invitation");

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();

        //make the invitation url with 24hrs validity
        $key = encode_id($this->encrypt->encode('client|' . $email . '|' . (time() + (24 * 60 * 60)) . '|' . $client_id), "signup");
        $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $key);

        //send invitation email
        $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
        if (send_app_mail($email, $email_template->subject, $message)) {
            echo json_encode(array('success' => true, 'message' => lang("invitation_sent")));
        } else {
            echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
        }
    }

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            $this->template->rander("clients/contacts/users", $view_data);
        }
    }

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */