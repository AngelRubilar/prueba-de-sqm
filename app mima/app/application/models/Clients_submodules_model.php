<?php

class Clients_submodules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'clients_submodules';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $clients_submodules_table = $this->db->dbprefix('clients_submodules');
		
  		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= "AND $clients_submodules_table.id=$id";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $clients_submodules_table.* FROM $clients_submodules_table WHERE 1 $where";
		
        return $this->db->query($sql);
    }
	
}
