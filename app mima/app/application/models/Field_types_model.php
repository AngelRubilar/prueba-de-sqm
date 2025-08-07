<?php

class Field_types_model extends Crud_model {

    private $table = null;

//NOT USED 
    function __construct() {
		
        $this->table = 'tipo_campo';
        parent::__construct($this->table);
    }


    function get_details($options = array()) {
		
		
        $field_types_table = $this->db->dbprefix('tipo_campo');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $field_types_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $field_types_table.nombre='$nombre'";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $field_types_table.* FROM $field_types_table WHERE $field_types_table.deleted=0 $where";
		
        return $this->db->query($sql);
    }
	
}
