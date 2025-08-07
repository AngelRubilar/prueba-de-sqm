<?php

class Values_permitting_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_permisos';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        
		$permitting_values_table = $this->db->dbprefix('valores_permisos');
		$permitting_table = $this->db->dbprefix('permisos');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $permitting_values_table.id = $id ";
        }
		
		$id_permiso = get_array_value($options, "id_permiso");
        if ($id_permiso) {
            $where .= " AND $permitting_table.id = $id_permiso ";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $permitting_table.id_proyecto, $permitting_values_table.*
				FROM $permitting_values_table, $permitting_table 
				WHERE $permitting_table.id = $permitting_values_table.id_permiso
				AND $permitting_values_table.deleted = 0 
				AND $permitting_table.deleted = 0 
				$where 
				ORDER BY $permitting_values_table.created DESC";
		
        return $this->db->query($sql);
    }
	
	function delete_values_permitting($id){
		
		$valores_permisos = $this->db->dbprefix('valores_permisos');

		$sql = "UPDATE $valores_permisos SET $valores_permisos.deleted=1 WHERE $valores_permisos.id=$id; ";
		$this->db->query($sql);
	}
}
