<?php

class Fixed_field_rel_form_rel_project_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'campo_fijo_rel_formulario_rel_proyecto';
        parent::__construct($this->table);
    }
	
	function get_fixed_fields_related_to_form($form_id){
		
		$fixed_field_rel_form_rel_project_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fixed_field_rel_form_rel_project_table.* FROM $fixed_field_rel_form_rel_project_table WHERE";
		$sql .= " $fixed_field_rel_form_rel_project_table.id_formulario = $form_id";
		$sql .= " AND $fixed_field_rel_form_rel_project_table.deleted = 0";
		
		return $this->db->query($sql);
	}
	
	function get_fixed_forms_related_to_project($options = array()){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$fixed_field_rel_form_rel_project_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');

		$where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $fixed_field_rel_form_rel_project_table.id_proyecto = $id_proyecto";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario = $id_tipo_formulario";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $formularios_table.*, $fixed_field_rel_form_rel_project_table.id_proyecto";
		$sql .= " FROM $fixed_field_rel_form_rel_project_table, $formularios_table";
		$sql .= " WHERE $fixed_field_rel_form_rel_project_table.id_formulario = $formularios_table.id";
		$sql .= " AND $fixed_field_rel_form_rel_project_table.deleted = 0";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " $where";
		$sql .= " GROUP BY $formularios_table.id";
		
		return $this->db->query($sql);
		
	}

}
