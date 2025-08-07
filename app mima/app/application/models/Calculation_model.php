<?php

class Calculation_model extends Crud_model {

    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'calculos';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
        $calculos_table = $this->db->dbprefix('calculos');
        $rules_table = $this->db->dbprefix('criterios');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$fields_table= $this->db->dbprefix('campos');
		
		$materials_db = getFCBD();
		$category_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		$subcategory_table = $this->load->database(getFCBD(), TRUE)->dbprefix('subcategorias');
		$databases_table = $this->load->database(getFCBD(), TRUE)->dbprefix('bases_de_datos');
		
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $calculos_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where = " AND $calculos_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where = " AND $calculos_table.id_proyecto=$id_proyecto";
        }
		
		$id_criterio = get_array_value($options, "id_criterio");
        if ($id_criterio) {
            $where = " AND $calculos_table.id_criterio=$id_criterio";
        }
		
		$id_bd = get_array_value($options, "id_bd");
        if ($id_bd) {
            $where = " AND $calculos_table.id_bd=$id_bd";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
		
		$sql = "SELECT $calculos_table.id, $calculos_table.id_campo_unidad, $clients_table.company_name, $projects_table.title, $rules_table.etiqueta, $calculos_table.criterio_fc, $databases_table.nombre AS nombre_bd, $category_table.nombre AS nombre_categoria, $subcategory_table.nombre AS nombre_subcategoria, $calculos_table.etiqueta AS etiqueta_calculo, $calculos_table.id_criterio, $calculos_table.created, $calculos_table.modified";
		$sql .= " FROM $clients_table, $projects_table, $rules_table, $calculos_table, 
				$materials_db.$databases_table, $materials_db.$category_table, $materials_db.$subcategory_table WHERE";
		$sql .= " $calculos_table.deleted = 0";
		$sql .= " AND $clients_table.id = $calculos_table.id_cliente";
		$sql .= " AND $projects_table.id = $calculos_table.id_proyecto";
		$sql .= " AND $rules_table.id = $calculos_table.id_criterio";
		//$sql .= " AND $fields_table.id = $calculos_table.id_campo_unidad";
		$sql .= " AND $databases_table.id = $calculos_table.id_bd";
		$sql .= " AND $category_table.id = $calculos_table.id_categoria";
		$sql .= " AND $subcategory_table.id = $calculos_table.id_subcategoria";
		$sql .= " $where";

        return $this->db->query($sql);

    }
	
	function get_calculations_field_ids_of_project($client_id, $project_id) {
		
		$rules_table = $this->db->dbprefix('criterios');
        $calculos_table = $this->db->dbprefix('calculos');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $rules_table.id_formulario, $rules_table.id_material, $rules_table.id_campo_fc, $calculos_table.* ";
		$sql .= "FROM $rules_table, $calculos_table WHERE";
		$sql .= " $rules_table.deleted = 0 AND $calculos_table.deleted = 0";
		$sql .= " AND $calculos_table.id_criterio = $rules_table.id";
		$sql .= " AND $calculos_table.id_cliente = $client_id";
		$sql .= " AND $calculos_table.id_proyecto = $project_id";
		
        return $this->db->query($sql);

    }
	

	function get_records_of_forms_for_calculation($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $start_date = NULL, $end_date = NULL) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
		
		$sql_rango_fechas = "";
		if($start_date && $end_date){
			$sql_rango_fechas .= " AND ($form_values_table.datos->'$.fecha' >= '$start_date'";
			$sql_rango_fechas .= " AND $form_values_table.datos->'$.fecha' <= '$end_date')";
		}

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		$sql .= $sql_rango_fechas;
		
        return $this->db->query($sql);

    }
	
	/* 
		Este método no está siendo utilizado, pero eventualmente podría. Se debe optimizar la consulta, 
		en el filtro que se hace para el rango de fechas.
	*/
	function get_records_of_forms_for_calculation_acv_report($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $start_date, $end_date) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
			
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		//$sql .= " AND SUBSTRING($form_values_table.datos, 11,10) BETWEEN '$start_date' AND '$end_date'";
		$sql .= " AND ($form_values_table.datos->'$.fecha' >= '$start_date'";
		$sql .= " AND $form_values_table.datos->'$.fecha' <= '$end_date')";
		
        return $this->db->query($sql);

    }
	
	function get_records_of_forms_for_unit_processes($project_id, $form_id, $id_campo_fc, $criterio_fc, $id_categoria, $id_campo_sp, $criterio_sp, $id_campo_pu, $criterio_pu) {
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
        $form_values_table = $this->db->dbprefix('valores_formularios');
		
		$sql_criterio_fc = "";
		if($id_campo_fc && $criterio_fc){
			
			if(is_numeric($id_campo_fc) && is_numeric($criterio_fc)){// id de campo | valor numerico
			
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}elseif(is_numeric($id_campo_fc) && !is_numeric($criterio_fc)){// id de campo | valor string
			
				//$criterio_fc_encoded = json_encode($criterio_fc);
				//$sql_criterio_fc .= " AND $form_values_table.datos->'$[$id_campo_fc]' = '$criterio_fc_encoded'";
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.\"".$id_campo_fc."\"' = '$criterio_fc'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_criterio_fc .= " AND $form_values_table.datos->'$.".$id_campo_fc."' = '$criterio_fc'";
				
			}
		}
		
		$sql_categoria = "";
		if($id_categoria){
			$sql_categoria .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		}
		
		$sql_campo_sp = "";
		if($id_campo_sp){
			
			if(is_numeric($id_campo_sp) && is_numeric($criterio_sp)){// id de campo | valor numerico
			
				//$sql_campo_sp .= " AND $form_values_table.datos->'$[$id_campo_sp]' = '$criterio_sp'";
				$sql_campo_sp .= " AND $form_values_table.datos->'$.\"".$id_campo_sp."\"' = '$criterio_sp'";
				
			}elseif(is_numeric($id_campo_sp) && !is_numeric($criterio_sp)){// id de campo | valor string
			
				//$criterio_sp_encoded = json_encode($criterio_sp);
				//$sql_campo_sp .= " AND $form_values_table.datos->'$[$id_campo_sp]' = '$criterio_sp_encoded'";
				$sql_campo_sp .= " AND $form_values_table.datos->'$.\"".$id_campo_sp."\"' = '$criterio_sp_encoded'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_campo_sp .= " AND $form_values_table.datos->'$.".$id_campo_sp."' = '$criterio_sp'";
				
			}
		}
		
		$sql_campo_pu = "";
		if($id_campo_pu){
			
			if(is_numeric($id_campo_pu) && is_numeric($criterio_pu)){// id de campo | valor numerico
			
				//$sql_campo_pu .= " AND $form_values_table.datos->'$[$id_campo_pu]' = '$criterio_pu'";
				$sql_campo_pu .= " AND $form_values_table.datos->'$.\"".$id_campo_pu."\"' = '$criterio_pu'";
				
			}elseif(is_numeric($id_campo_pu) && !is_numeric($criterio_sp)){// id de campo | valor string
			
				//$criterio_pu_encoded = json_encode($criterio_pu);
				//$sql_campo_pu .= " AND $form_values_table.datos->'$[$id_campo_pu]' = '$criterio_pu_encoded'";
				$sql_campo_pu .= " AND $form_values_table.datos->'$.\"".$sql_campo_pu."\"' = '$criterio_pu_encoded'";
				
			}else{// campo fijo, ej: type_of_origin_matter, tipo_tratamiento, default_type, etc
			
				$sql_campo_pu .= " AND $form_values_table.datos->'$.".$id_campo_pu."' = '$criterio_pu'";
				
			}
		}
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_values_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= $sql_criterio_fc;
		$sql .= $sql_categoria;
		$sql .= $sql_campo_sp;
		$sql .= $sql_campo_pu;
		
        return $this->db->query($sql);

    }
	
	function get_records_of_project($id_proyecto, $flujo_formulario) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.id_tipo_formulario = 1";
		$sql .= " AND $form_table.flujo = '$flujo_formulario'";
		$sql .= " AND $form_rel_project_table.id_proyecto = $id_proyecto";
		
        return $this->db->query($sql);

    }
	
	function get_records_of_category_of_form($id_categoria, $id_formulario, $flujo_formulario) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.id_tipo_formulario = 1";
		$sql .= " AND $form_table.flujo = '$flujo_formulario'";
		$sql .= " AND $form_rel_project_table.id_formulario = $id_formulario";
		//$sql .= " AND $form_values_table.datos LIKE '%\"id_categoria\":\"$id_categoria\"%'";
		$sql .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
        return $this->db->query($sql);

    }
	
	function get_records_for_each_category($id_categoria) {
		
		$form_table = $this->db->dbprefix('formularios');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_values_table = $this->db->dbprefix('valores_formularios');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $form_values_table.* ";
		$sql .= "FROM $form_table, $form_rel_project_table, $form_values_table WHERE";
		$sql .= " $form_table.deleted = 0 AND $form_rel_project_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " AND $form_rel_project_table.id_formulario = $form_table.id";
		$sql .= " AND $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id";
		$sql .= " AND $form_table.flujo = 'Consumo'";
		//$sql .= " AND $form_values_table.datos = LIKE '%\"id_categoria\":\"$id_categoria\"%'";
		$sql .= " AND $form_values_table.datos->'$.id_categoria' = $id_categoria";
		
        return $this->db->query($sql);

    }
	
	function delete_calculation($id){
		
		$calculos = $this->db->dbprefix('calculos');
        $sql = "UPDATE $calculos SET $calculos.deleted=1 WHERE $calculos.id=$id; ";
        $this->db->query($sql);
		
	}

}
