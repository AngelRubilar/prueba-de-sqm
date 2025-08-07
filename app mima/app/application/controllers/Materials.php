<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Materials extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load clients list view */

    function index() {
        $this->access_only_allowed_members();

        $this->template->rander("materials/index");
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
        $view_data['model_info'] = $this->Materials_model->get_one($id);
        $view_data["categorias_disponibles"] = $this->Categories_model->get_all_where(array("deleted" => 0))->result_array();
		$view_data["categorias_deshabilitadas"] = $this->Materials_rel_category_model->get_all_where(array("deleted" => 0))->result();
		
		/* foreach($view_data["categorias_deshabilitadas"] as $cat_des){
			var_dump($cat_des->id_categoria."<br>"); // string "1", etx
		}  */
		
        if($id){
             $view_data["categorias_de_material"] = $this->Categories_model->get_category_of_material($id)->result_array();
			 //categorias_deshabilitadas deberían ser todas menos las que están asociadas al id del material ($id)
			 $view_data["categorias_deshabilitadas"] = $this->Materials_rel_category_model->get_other_categories_used_in_materials($id)->result();
			 
			 /* foreach($view_data["categorias_deshabilitadas"] as $cat_des){
				var_dump($cat_des->id_categoria."<br>"); // string "1", etx
			 }  */
			 
        }

        $this->load->view('materials/modal_form', $view_data);
    }

    /* insert or update a client */

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
			
             //Edit material relation category
            $delete_material_rel_cat = $this->Materials_rel_category_model->delete_material_rel_category2($id);
            if($delete_material_rel_cat){
                if($multiselect_categorias){
                    foreach($multiselect_categorias as $id_categoria){
						$data_m_rel_c["id_categoria"] =(int)$id_categoria;
						$data_m_rel_c["id_material"] = $id;
						$data_m_rel_c["modified_by"] = $this->login_user->id;
						$data_m_rel_c["modified"] = get_current_utc_time();
						$save_data_m_rel_c = $this->Materials_rel_category_model->save($data_m_rel_c);
					}
				}

            }
            $save_id = $this->Materials_model->save($data, $id);

            
        }else{
			
			$data["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
			
            $save_id = $this->Materials_model->save($data);
            //save relation
            if($multiselect_categorias){
                    foreach($multiselect_categorias as $id_categoria){
						$data_m_rel_c["id_categoria"] =(int)$id_categoria;
						$data_m_rel_c["id_material"] = $save_id;
						$data_m_rel_c["created_by"] = $this->login_user->id;
						$data_m_rel_c["created"] = get_current_utc_time();
						$save_data_m_rel_c = $this->Materials_rel_category_model->save($data_m_rel_c);
					}
                }
        }

        

        if ($save_id) {
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
		
		$material_rel_cat = $this->Materials_rel_category_model->get_all_where(array("id_material" => $id))->result();
		foreach($material_rel_cat as $rel){
			$this->Materials_rel_category_model->delete($rel->id);
		}

        if ($this->input->post('undo')) {
            if ($this->Materials_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Materials_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
        //$custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        /*$options = array(
            "custom_fields" => $custom_fields
        );*/
        $list_data = $this->Materials_model->get_details()->result();
        
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
        $data = $this->Materials_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
        
		$categorias_de_material = $this->Categories_model->get_category_of_material($data->id)->result_array();
		$categorias = "";
		foreach($categorias_de_material as $categoria){
			$categorias .= $categoria["nombre"]."<br>";
		}
		
        $row_data = array($data->id,
            modal_anchor(get_uri("materials/view/" . $data->id), $data->nombre, array("title" => lang('view_material'))), $categorias
            
        );

        $row_data[] = modal_anchor(get_uri("materials/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_material'))) .
        modal_anchor(get_uri("materials/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_material'), "data-post-id" => $data->id))

                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_materials'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("materials/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($id_materials = 0) {
       

        if ($id_materials) {
            $options = array("id" => $id_materials);
            $model_info = $this->Materials_model->get_details($options)->row();
            if ($model_info) {
                $view_data['model'] = $model_info;
                $view_data["categorias2"] = $this->Categories_model->get_category_of_material($id_materials)->result_array();
                $this->load->view('materials/view', $view_data);
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