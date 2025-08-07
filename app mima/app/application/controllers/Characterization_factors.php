<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Characterization_factors extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("client");
    }

    /* load fc list view */

    function index() {
        $this->access_only_allowed_members();
		
		// filtro metodologias
		$metodologias[] = array("id" => "", "text" => "- ".lang("calculation_metodology")." -");
		$metods = $this->Methodology_model->get_dropdown_list(array("nombre"), 'id');
		foreach($metods as $id => $met){
			$metodologias[] = array("id" => $id, "text" => $met);
		}
		$view_data['metodologias_dropdown'] = json_encode($metodologias);
		
		// filtro bases de datos
		$databases[] = array("id" => "", "text" => "- ".lang("database")." -");
		$dbs = $this->Databases_model->get_dropdown_list(array("nombre"), 'id');
		foreach($dbs as $id => $db){
			$databases[] = array("id" => $id, "text" => $db);
		}
		$view_data['bases_de_datos_dropdown'] = json_encode($databases);
		
		// filtro huellas
		$huellas[] = array("id" => "", "text" => "- ".lang("footprint")." -");
		$hue = $this->Footprints_model->get_dropdown_list(array("nombre"), 'id');
		foreach($hue as $id => $hu){
			$huellas[] = array("id" => $id, "text" => $hu);
		}
		$view_data['huellas_dropdown'] = json_encode($huellas);
		
		// filtro materiales
		$materiales[] = array("id" => "", "text" => "- ".lang("material")." -");
		$mat = $this->Materials_model->get_dropdown_list(array("nombre"), 'id');
		foreach($mat as $id => $ma){
			$materiales[] = array("id" => $id, "text" => $ma);
		}
		$view_data['materiales_dropdown'] = json_encode($materiales);
		
		// FILTRO FORMATO DE HUELLAS
		$array_formato_huellas[] = array("id" => "", "text" => "- ".lang("footprint_format")." -");
		$formato_huellas = $this->Footprint_format_model->get_dropdown_list(array("nombre"), 'id');
		foreach($formato_huellas as $id => $nombre){
			$array_formato_huellas[] = array("id" => $id, "text" => $nombre);
		}
		$view_data['formato_huellas_dropdown'] = json_encode($array_formato_huellas);
		
        $this->template->rander("characterization_factors/index", $view_data);
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $fc_id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
		
		$view_data['bases_de_datos'] = array("" => "-") + $this->Databases_model->get_dropdown_list(array("id" => "nombre"));
		$view_data['formato_huellas'] = array("" => "-") + $this->Footprint_format_model->get_dropdown_list(array("id" => "nombre"));
		$view_data['metodologias'] = array("" => "-");// + $this->Methodology_model->get_dropdown_list(array("id" => "nombre"));
       	$view_data['huellas'] = array("" => "-") + $this->Footprints_model->get_dropdown_list(array("id" => "nombre"));
		$view_data['materiales'] = array("" => "-") + $this->Materials_model->get_dropdown_list(array("id" => "nombre"));
		$view_data['tipos_de_unidad'] = array("" => "-") + $this->Unity_type_model->get_dropdown_list(array("id" => "nombre"));
		
		if($fc_id){
			$view_data['model_info'] = $this->Characterization_factors_model->get_one($fc_id);
			
			// Metodologias de la huella
			$id_footprint_format = $view_data['model_info']->id_formato_huella;
            $methodologies = $this->Methodology_model->get_methodologies_of_fh($id_footprint_format)->result_array();
		
			$methodologies_dropdown = array("" => "-");
			foreach($methodologies as $methodology){
				$methodologies_dropdown[$methodology['id']] = $methodology['nombre']; 
            }
            $view_data['metodologias'] = $methodologies_dropdown;
			
			
			$id_material = $view_data['model_info']->id_material;
			$id_categoria = $view_data['model_info']->id_categoria;
			$cat_rel_mat = $this->Categories_model->get_category_of_material($id_material)->result();
			$array_cm = array();
		
			if($cat_rel_mat){
				foreach($cat_rel_mat as $key => $cat_rel_mat){
					$array_cm[$cat_rel_mat->id] = $cat_rel_mat->nombre;
				}
			}
			
			$view_data['categorias'] = $array_cm;
			
			$subcategorias = $this->Categories_model->get_subcategories_of_category($id_categoria)->result();
			$select_subcategorias = array();
			foreach($subcategorias as $sub){
				$select_subcategorias[$sub->id] = $sub->nombre;
			}
			
			$view_data['subcategorias'] = $select_subcategorias;
			$view_data['unidades'] = array("" => "-") + $this->Unity_model->get_dropdown_list(array("nombre"), "id", array("id_tipo_unidad" => $view_data['model_info']->id_tipo_unidad));
			//var_dump($view_data['unidades']);
			
		} else {
			
			$view_data['categorias'] = array("" => "-");
			$view_data['subcategorias'] = array("" => "-");		
			$view_data['unidades'] = array("" => "-");
        	//$view_data['material_rel_categoria'] = $this->Materials_rel_category_model->get_all()->result();
			
		}
        
        $this->load->view('characterization_factors/modal_form', $view_data);
    }

    function get_currency_dropdown_select2_data() {
        $currency = array(array("id" => "", "text" => "-"));
        foreach (get_international_currency_code_dropdown() as $value) {
            $currency[] = array("id" => $value, "text" => $value);
        }
        return $currency;
    }
	
	function get_units_of_unit_type(){
		
		$id_tipo_unidad = $this->input->post('tipo_unidad');

		$select_unidades = array();
		if($id_tipo_unidad){
			$unidades = $this->Unity_model->get_units_of_unit_type($id_tipo_unidad)->result();
			
			foreach($unidades as $unit){
				$select_unidades[$unit->id] = $unit->nombre;
			}
		}
		
		$html .= '<div class="form-group">';
			$html .= '<label for="unit" class="col-md-3">'.lang('unit').'</label>';
			$html .= '<div class="col-md-9">';
			$html .= form_dropdown("unit", array("" => "-") + $select_unidades, "", "id='unit' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
		
	}
	
    function get_category_of_material(){
    
        $id_material = $this->input->post('id_material');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
		
        $array_cm = array();
		if($id_material){
			$categoria_de_material = $this->Categories_model->get_category_of_material($id_material)->result();
			if($categoria_de_material){
				foreach($categoria_de_material as $key => $categoria_de_material){
					$array_cm[$categoria_de_material->id] = $categoria_de_material->nombre;
				}
			}
		}
		
        $html = '';
        $html .= '<label for="category" class="col-md-3">'.lang('category').'</label>';
        $html .= '<div class="col-md-9">';
        $html .= form_dropdown("category", array("" => "-") + $array_cm, "", "id='category' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        $html .= '</div>';
        
        echo $html;
    }
    
    function get_subcategory_of_category(){
    
        $id_categoria = $this->input->post('id_categoria');

        if (!$this->login_user->id) {
            redirect("forbidden");
        }
        
		if($id_categoria){
        	$subcategoria_de_categoria = $this->Subcategories_model->get_dropdown_list(array("nombre"), "id", array("id_categoria" => $id_categoria));
		}else{
			$subcategoria_de_categoria = array();
		}
		
        $html = '';
        $html .= '<label for="subcategory" class="col-md-3">'.lang('subcategory').'</label>';
        $html .= '<div class="col-md-9">';
        $html .= form_dropdown("subcategory", array("" => "-") + $subcategoria_de_categoria, "", "id='subcategory' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        $html .= '</div>';
        
        echo $html;
    }

    function save() {
        
        $factor_id = $this->input->post('id');

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $data = array(
            //"id_metodologia" => $this->input->post('calculation_methodology'),
			"id_bd" => $this->input->post('database'),
			"id_formato_huella" => $this->input->post('footprint_format'),
			"id_metodologia" => $this->input->post('id_methodology'),
            "id_huella" => $this->input->post('environmental_footprint'),
            "id_material" => $this->input->post('materials'),
            "id_categoria" => $this->input->post('category'),
            "id_subcategoria" => $this->input->post('subcategory'),
			"id_tipo_unidad" => $this->input->post('unit_type'),
			"id_unidad" => $this->input->post('unit'),
            "factor" => $this->input->post('factor')
        );
		
		if($factor_id){
			//$data_subproject["modified_by"] = $this->login_user->id;
			$data["modified"] = get_current_utc_time();
		} else {
			//$data_subproject["created_by"] = $this->login_user->id;
			$data["created"] = get_current_utc_time();
		}
		
		// Validamos que previamente no exista un factor con las combinacion ingresada
		$array_validacion = $data;
		$array_validacion["deleted"] = 0;
		unset($array_validacion["factor"]);
		
		if ($this->Characterization_factors_model->is_factor_exists($array_validacion, $factor_id)) {
			echo json_encode(array("success" => false, 'message' => lang('duplicate_characterization_factor')));
			exit();
		}
		
        $save_id = $this->Characterization_factors_model->save($data, $factor_id);
		
        if ($save_id) {
            echo json_encode(array(
                "success" => true, 
                "data" => $this->_row_data($save_id), 
                "id" => $save_id, 
                "view" => $this->input->post('view'), 
                "message" => lang('record_saved')
                )
            );

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
            if ($this->Characterization_factors_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Characterization_factors_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {
		
		// filtros
		$id_metodologia = $this->input->post('id_metodologia');
		$id_bd = $this->input->post('id_bd');
		$id_huella = $this->input->post('id_huella');
		$id_material = $this->input->post('id_material');
		$options = array(
			"id_metodologia" => $id_metodologia,
			"id_bd" => $id_bd,
			"id_huella" => $id_huella,
			"id_material" => $id_material
		);

        $this->access_only_allowed_members();
        $list_data = $this->Characterization_factors_model->get_details($options)->result();
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }
	
	function list_data2() {
		
		// filtros
		$id_metodologia = $this->input->post('id_metodologia');
		$id_bd = $this->input->post('id_bd');
		$id_huella = $this->input->post('id_huella');
		$id_material = $this->input->post('id_material');
		$id_formato_huella = $this->input->post('id_formato_huella');
		
		$options = array(
			"id_metodologia" => $id_metodologia,
			"id_bd" => $id_bd,
			"id_huella" => $id_huella,
			"id_material" => $id_material,
			"id_formato_huella" => $id_formato_huella
		);

        $this->access_only_allowed_members();
        $list_data = $this->Characterization_factors_model->get_details2($options)->result_array();
        
        echo json_encode(array("data" => $list_data));
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $options = array(
            "id" => $id,
        );
        $data = $this->Characterization_factors_model->get_details2($options)->row();
        return $this->_make_row($data);
    }

    /* prepare a row of client list table */

    private function _make_row($data) {
   		//$unidad = $this->Unity_model->get_one($data->id_unidad);
        $row_data = array(
			"id" => $data->id, 
            //modal_anchor(get_uri("characterization_factors/view/" . $data->id), $data->nombre_metodologia, array("title" => lang('view_characterization_factor'))),
			"nombre_bd" => $data->nombre_bd, 
			"nombre_formato_huella" => $data->nombre_formato_huella, 
			"nombre_metodologia" => $data->nombre_metodologia,
            "nombre_huella" => $data->nombre_huella, 
			"nombre_material" => $data->nombre_material,
			"nombre_categoria" => $data->nombre_categoria,
            "nombre_subcategoria" => $data->nombre_subcategoria, 
			//"nombre_unidad" => $unidad->nombre,
			"nombre_unidad" => $data->nombre_unidad,
			"factor" => $data->factor,
			//$data->id, 
        );
        /*$row_data[] = modal_anchor(get_uri("characterization_factors/view/" . $data->id), "<i class='fa fa-eye'></i>", array("title" => lang('view_characterization_factor')))
                . modal_anchor(get_uri("characterization_factors/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_characterization_factor'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_characterization_factor'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("characterization_factors/delete"), "data-action" => "delete-confirmation"));*/
		return $row_data;
    }

    /* load client details view */

    function view($factor_id = 0) {
        $this->access_only_allowed_members();
        
        if ($factor_id) {
            $options = array("id" => $factor_id);
            $factor_info = $this->Characterization_factors_model->get_details2($options)->row();
            if ($factor_info) {
                $view_data["label_column"] = "col-md-3";
                $view_data["field_column"] = "col-md-9";
                $view_data['model_info'] = $factor_info;
				$tipo_unidad = $this->Unity_type_model->get_one($factor_info->id_tipo_unidad);
				$unidad = $this->Unity_model->get_one($factor_info->id_unidad);
				$view_data["tipo_unidad"] = $tipo_unidad->nombre;
				$view_data["unidad"] = $factor_info->nombre_unidad;
				   
                $this->load->view("characterization_factors/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }


}