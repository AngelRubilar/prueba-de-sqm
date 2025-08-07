<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Methodologies extends MY_Controller {

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

        $this->template->rander("methodologies/index");
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $methodology_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Methodology_model->get_one($methodology_id);
		$view_data["huellas"] = $this->Footprints_model->get_all()->result_array();
		
		if($methodology_id){
			$view_data["huellas_metodologia"] = $this->Footprints_model->get_footprints_of_methodology($methodology_id)->result_array();
		}
		
        //$view_data["currency_dropdown"] = $this->get_currency_dropdown_select2_data();

        //get custom fields
        //$view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        $this->load->view('methodologies/modal_form', $view_data);
    }
	

    /* insert or update a client */

    function save() {
		
        $methodology_id = $this->input->post('id');
		$huellas = $this->input->post("huellas");
		

        //$this->access_only_allowed_members_or_client_contact($client_id);

        validate_submitted_data(array(
            "id" => "numeric",
            "name" => "required",
        ));
		
		
        $data = array(
            "nombre" => $this->input->post('name'),
			"descripcion" => $this->input->post('description'),
        );
		
		if($methodology_id){ //edit
			
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			$save_id = $this->Methodology_model->save($data, $methodology_id);
			
			$delete_huellas_metodologia = $this->Methodology_rel_footprints_model->delete_huellas_related_to_methodology($methodology_id);
			
			if($delete_huellas_metodologia){
				foreach($huellas as $huella){
					$huella = (int)$huella;
					$data_huellas_metodologia["id_metodologia"] = $methodology_id;
					$data_huellas_metodologia["id_huella"] = $huella;
					$this->Methodology_rel_footprints_model->save($data_huellas_metodologia);
				}
			}
			
		}else{ //insert
			
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			
			$save_id = $this->Methodology_model->save($data);
			
			foreach($huellas as $huella){
				$data_huellas_metodologia = array(
					"id_metodologia" => $save_id,
					"id_huella" => $huella
				);
				$this->Methodology_rel_footprints_model->save($data_huellas_metodologia);
			}
		}
		
		
        if ($this->login_user->is_admin) {
            //$data["currency_symbol"] = $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "";
            //$data["currency"] = $this->input->post('currency') ? $this->input->post('currency') : "";
            //$data["disable_online_payment"] = $this->input->post('disable_online_payment') ? $this->input->post('disable_online_payment') : 0;
		}
		
        
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
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
            if ($this->Methodology_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Methodology_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
		
        $list_data = $this->Methodology_model->get_details()->result();
		
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
        $data = $this->Methodology_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
		
        $row_data = array($data->id,
			modal_anchor(get_uri("methodologies/view/" . $data->id), $data->nombre, array("class" => "", "title" => lang('view_methodology'), "data-post-id" => $data->id))
        );
		
		$tooltip_descripcion = '<span class="help" data-container="body" data-toggle="tooltip" title="'.$data->descripcion.'"><i class="fas fa-info-circle fa-lg"></i></span>';
		$descripcion = ($data->descripcion) ? $tooltip_descripcion : "-";
		
		$row_data[] = $descripcion;
		
        $row_data[] = modal_anchor(get_uri("methodologies/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "", "title" => lang('view_methodology'), "data-post-id" => $data->id)).
				modal_anchor(get_uri("methodologies/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_methodology'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_methodology'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("methodologies/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($methodology_id = 0, $tab = "") {
        $this->access_only_allowed_members();

        if ($methodology_id) {
            $options = array("id" => $methodology_id);
            $methodology_info = $this->Methodology_model->get_details($options)->row();
            if ($methodology_info){
				
                $view_data['methodology_info'] = $methodology_info;
				$view_data["huellas_metodologia"] = $this->Footprints_model->get_footprints_of_methodology($methodology_id)->result_array();
				
                //$this->template->rander("clients/view", $view_data);
				$this->load->view('methodologies/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
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