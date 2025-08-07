<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories_alias extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
		
        $this->access_only_allowed_members();
		
		//FILTRO CLIENTE		
		$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
		$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		foreach($clientes as $id => $company_name){
			$array_clientes[] = array("id" => $id, "text" => $company_name);
		}
		$view_data['clientes_dropdown'] = json_encode($array_clientes);
		
		//FILTRO CATEGORIA
		$array_categorias[] = array("id" => "", "text" => "- ".lang("category")." -");
		$categorias = $this->Categories_model->get_dropdown_list(array("nombre"), 'id');
		foreach($categorias as $id => $nombre){
			$array_categorias[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['categorias_dropdown'] = json_encode($array_categorias);

        $this->template->rander("categories_alias/index", $view_data);
		
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

        $view_data["view"] = $this->input->post('view');
        $view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), "id");
		$view_data["categorias"] = array("" => "-");
		
        if($id){
			$view_data['model_info'] = $this->Categories_alias_model->get_one($id);
			
			$categorias = array();
			if($view_data['model_info']->id_cliente){
				$categorias_del_cliente = $this->Categories_alias_model->get_categories_related_to_client($view_data['model_info']->id_cliente)->result_array();
				foreach($categorias_del_cliente as $cc){
					$categorias[$cc["id"]] = $cc["nombre"];			
				}
			}
			$view_data["categorias"] = $categorias;
		   
		   
        }

        $this->load->view('categories_alias/modal_form', $view_data);
    }

    

    /* insert or update a client */

    function save() {
        $id = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric",
        ));

        $data = array(
            "id_cliente" => $this->input->post('client'),
			"id_categoria" => $this->input->post('categoria'),
			"alias" => trim($this->input->post('alias')),
        );
		
        if($id){
            $data["modified_by"] = $this->login_user->id;
            $data["modified"] = get_current_utc_time();
            
        }else{
            $data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
        }
		
		// Validamos que previamente no exista un alias para cliente y categoria ingresado
		$array_validacion = array(
            "id_cliente" => $this->input->post('client'),
			"id_categoria" => $this->input->post('categoria'),
			"deleted" => 0,
        );
		if ($this->Categories_alias_model->is_alias_exists($array_validacion, $id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_alias_in_client')));
			exit();
		}
		
        $save_id = $this->Categories_alias_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {
        //$this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric" 
        ));

        $id = $this->input->post('id');
        if ($this->input->post('undo')) {
            if ($this->Categories_alias_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Categories_alias_model->delete($id)) {
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
			"id_cliente" => $this->input->post("id_cliente"),
			"id_categoria" => $this->input->post("id_categoria"),
		);
		
        $list_data = $this->Categories_alias_model->get_details($options)->result();
        
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
        $data = $this->Categories_alias_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
        
        $row_data = array(
			$data->id,
            modal_anchor(get_uri("categories_alias/view/" . $data->id), $data->alias, array("title" => lang('view_category_alias'))),
			$data->categoria ? $data->categoria : "-",
            $data->cliente ? $data->cliente : "-",
        );

        $row_data[] = modal_anchor(get_uri("categories_alias/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_category_alias'))) .
        modal_anchor(get_uri("categories_alias/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_category_alias'), "data-post-id" => $data->id))

                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_category_alias'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("categories_alias/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($id = 0) {
       

        if ($id) {
            $options = array("id" => $id);
            $model_info = $this->Categories_alias_model->get_details($options)->row();
            if ($model_info){
                $view_data['model'] = $model_info;
                $this->load->view('categories_alias/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

	function get_categories_of_client(){
		
		$id_cliente = $this->input->post('id_cliente');
		$categorias = array();
		
		if($id_cliente){
			$categorias_del_cliente = $this->Categories_alias_model->get_categories_related_to_client($id_cliente)->result_array();
			foreach($categorias_del_cliente as $cc){
				$categorias[$cc["id"]] = $cc["nombre"];			
			}
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="categoria" class="col-md-3">'.lang('category').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("categoria", array("" => "-") + $categorias, "", "id='categoria' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
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