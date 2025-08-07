<?php

class Values_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_formularios';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $valores_formularios_table.id=$id";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $formulario_rel_proyecto_table.id_formulario=$id_formulario";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$datos = get_array_value($options, "datos");
        if ($datos) {
            $where .= " AND $valores_formularios_table.datos='$datos'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $valores_formularios_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $valores_formularios_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formulario_rel_proyecto_table.id_formulario, $formulario_rel_proyecto_table.id_proyecto, $valores_formularios_table.* FROM $valores_formularios_table, $formulario_rel_proyecto_table WHERE";
		$sql .= " $valores_formularios_table.deleted=0 AND $formulario_rel_proyecto_table.deleted=0";
		$sql .= " AND $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	

    function get_primary_contact($contact_id = 0) {
        $users_table = $this->db->dbprefix('users');

        $sql = "SELECT $users_table.id
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.client_id=$contact_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->id;
        }
    }
	
	function get_fields_of_form($form_id) {
        $form_table = $this->db->dbprefix('formularios');
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		//SELECT c.* FROM campos c, campo_rel_formulario crf, formularios f WHERE c.id = crf.id_campo AND crf.id_formulario = f.id AND f.id = 
        $sql = "SELECT $fields_table.* FROM $form_table, $field_rel_form_table, $fields_table WHERE";
		$sql .= " $fields_table.deleted=0";
		$sql .= " AND $fields_table.id = $field_rel_form_table.id_campo";
		$sql .= " AND $field_rel_form_table.id_formulario = $form_table.id";
		$sql .= " AND $form_table.id = $form_id";
		
        return $this->db->query($sql);
    }


}
