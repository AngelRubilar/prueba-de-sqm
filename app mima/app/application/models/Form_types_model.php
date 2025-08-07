<?php

class Form_types_model extends Crud_model {

    private $table = null;

    function __construct() {
		
        $this->table = 'tipo_formulario';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
		
        $form_types_table = $this->db->dbprefix('tipo_formulario');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $form_types_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $form_types_table.nombre='$nombre'";
        }
		
		$created_by = get_array_value($options, "created_by");
		if ($created_by) {
			$where .= " AND $form_types_table.id=$created_by";
		}
		
		$modified_by = get_array_value($options, "modified_by");
		if ($modified_by) {
			$where .= " AND $form_types_table.id=$modified_by";
		}
		
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $form_types_table.* FROM $form_types_table WHERE $form_types_table.deleted=0 $where";
		
        return $this->db->query($sql);
    }

    

}
