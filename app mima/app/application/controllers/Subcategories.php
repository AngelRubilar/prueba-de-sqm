<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subcategories extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();

        $this->template->rander("subcategories/index");
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the client's details view
        $view_data['model_info'] = $this->Subcategories_model->get_one($id);
        $view_data["categorias_disponibles"] = $this->Categories_model->get_all()->result_array();

        if($id){
			 $view_data["categorias_de_subcategoria"] = $this->Categories_model->get_category_of_subcategory($id)->result_array();
        }
		
        $this->load->view('subcategories/modal_form', $view_data);
    }

    

    /* insert or update a subcategory */

    function save() {
        $id = $this->input->post('id');
		
        $multiselect_categorias = $this->input->post('categorias');

        validate_submitted_data(array(
            "id" => "numeric",
        ));

        $data = array(
            "nombre" => $this->input->post('name'),
        );
		
        if($id){
			
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			
            $save_id = $this->Subcategories_model->save($data, $id);
        }else{
			
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			
            $save_id = $this->Subcategories_model->save($data);
        }

        if($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved'))); // se usará en este caso el view?
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
            if ($this->Subcategories_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Subcategories_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
        
        $list_data = $this->Subcategories_model->get_details()->result();
        
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
        $data = $this->Subcategories_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
        
        $row_data = array($data->id,
            modal_anchor(get_uri("subcategories/view/" . $data->id), $data->nombre, array("title" => lang('view_subcategory'))),
			//$data->material ? $data->material : "-",
			
			
            
        );

        $row_data[] = modal_anchor(get_uri("subcategories/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_subcategory'))) .
        modal_anchor(get_uri("subcategories/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_subcategory'), "data-post-id" => $data->id))

                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_subcategory'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("subcategories/delete"), "data-action" => "delete-confirmation"));
				

        return $row_data;
    }

    /* load client details view */

    function view($id = 0) {

        if ($id) {
            $options = array("id" => $id);
            $model_info = $this->Subcategories_model->get_details($options)->row();
            if ($model_info) {
                $view_data['model'] = $model_info;
          	    $view_data["categorias2"] = $this->Categories_model->get_category_of_subcategory($id)->result_array();
				
                $this->load->view('subcategories/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */