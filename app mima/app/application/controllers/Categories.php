<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();

        $this->template->rander("categories/index");
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
        $view_data['model_info'] = $this->Categories_model->get_one($id);
        //$view_data["categorias_disponibles"] = $this->Categories_model->get_all()->result_array();
		
		//agregadow
		//$view_data["materiales"] = array("" => "-")+ $this->Materials_model->get_dropdown_list(array("id" => "nombre"));
		$view_data["subcategorias_disponibles"] = $this->Subcategories_model->get_all_where(array("deleted" => 0))->result_array();
		
		//$view_data["material_rel_categoria"] = $this->Materials_rel_category_model->get_one_where(array("id_categoria" => $id));
		

        if($id){
           $view_data["subcategorias_de_categoria"] = $this->Subcategories_model->get_subcategory_of_category($id)->result_array();
		   
        }

        $this->load->view('categories/modal_form', $view_data);
    }

    

    /* insert or update a client */

    function save() {
		
        $id = $this->input->post('id');
        $subcategorias_multiselect = $this->input->post('subcategorias');
		$nombre_categoria = $this->input->post('name');
		
        validate_submitted_data(array(
            "id" => "numeric",
        ));

        $data = array(
            "nombre" => $nombre_categoria,
        );
		
		if ($this->Categories_model->is_category_name_exists($data["nombre"], $id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_category_name')));
			exit(); 
		}
		
        if($id){
        
			$data["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
			
             //Edit category rel subcategory
            $delete_cat_rel_sub = $this->Subcategory_rel_categories_model->delete_category_rel_subcategory($id);
            if($delete_cat_rel_sub){
                if($subcategorias_multiselect){
                    foreach($subcategorias_multiselect as $id_subcategoria){
						$data_m_rel_c["id_subcategoria"] = (int)$id_subcategoria;
						$data_m_rel_c["id_categoria"] = $id;
						$data_m_rel_c["modified_by"] = $this->login_user->id;
						$data_m_rel_c["modified"] = get_current_utc_time();
						$save_data_m_rel_c = $this->Subcategory_rel_categories_model->save($data_m_rel_c);
					}
                
                }

            }
            $save_id = $this->Categories_model->save($data, $id);

            
        }else{
			
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			
            $save_id = $this->Categories_model->save($data);
            //save relation
            if($subcategorias_multiselect){
				foreach($subcategorias_multiselect as $id_subcategoria){
					$data_m_rel_c["id_subcategoria"] =(int)$id_subcategoria;
					$data_m_rel_c["id_categoria"] = $save_id;
					$data_m_rel_c["created_by"] = $this->login_user->id;
					$data_m_rel_c["created"] = get_current_utc_time();
					$save_data_m_rel_c = $this->Subcategory_rel_categories_model->save($data_m_rel_c);
				}
			
			}
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
            if ($this->Categories_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Categories_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
        
        $list_data = $this->Categories_model->get_details()->result();
        
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
        $data = $this->Categories_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
        
		$subcategorias_rel_categoria = $this->Subcategory_rel_categories_model->get_all_where(array(
			"id_categoria" => $data->id,
			"deleted" => 0
		))->result_array();
		
		$subcategorias = "";
		foreach($subcategorias_rel_categoria as $rel){
			$subcategoria = $this->Subcategories_model->get_one($rel["id_subcategoria"]);
			$subcategorias .= $subcategoria->nombre."<br>";
		}
		
        $row_data = array(
			$data->id,
            modal_anchor(get_uri("categories/view/" . $data->id), $data->nombre, array("title" => lang('view_category'))),
			$subcategorias
		);

        $row_data[] = modal_anchor(get_uri("categories/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_category'))) .
        modal_anchor(get_uri("categories/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_category'), "data-post-id" => $data->id))

                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_materials'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("categories/delete"), "data-action" => "delete-confirmation"));
				

        return $row_data;
    }

    /* load client details view */

    function view($id = 0) {
		
        if ($id) {
            $options = array("id" => $id);
            $model_info = $this->Categories_model->get_details($options)->row();
            if ($model_info) {
                $view_data['model'] = $model_info;
				$view_data["subcategorias2"] = $this->Subcategories_model->get_subcategory_of_category($id)->result_array();
                $this->load->view('categories/view', $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

	function get_subcategories_of_category(){
		
		$id_categoria = $this->input->post("categoria");
		
		$select_subcategorias = array();
		if($id_categoria){
			$subcategorias = $this->Categories_model->get_subcategories_of_category($id_categoria)->result();
			
			foreach($subcategorias as $sub){
				$select_subcategorias[$sub->id] = $sub->nombre;
			}
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="subcategoria" class="col-md-3">'.lang('subcategory').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("subcategory", array("" => "-") + $select_subcategorias, "", "id='subcategory' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}

}

/* End of file clients.php */
/* Location: ./application/controllers/clients.php */