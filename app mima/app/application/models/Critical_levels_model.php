<?php

class Critical_levels_model extends Crud_model {

    private $table = null;
	
    function __construct() {
		
        $this->table = 'criticidades';
        parent::__construct($this->table);
    }


    function get_details($options = array()) {
		
		
        $criticidades_table = $this->db->dbprefix('criticidades');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $criticidades_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $criticidades_table.nombre='$nombre'";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $criticidades_table.* FROM $criticidades_table WHERE $criticidades_table.deleted=0 $where";
		
        return $this->db->query($sql);
    }
	
}
