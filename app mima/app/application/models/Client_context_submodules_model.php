<?php

class Client_context_submodules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_context_submodules';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $client_context_submodules_table = $this->db->dbprefix('client_context_submodules');
		
  		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= "AND $client_context_submodules_table.id=$id";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $client_context_submodules_table.* FROM $client_context_submodules_table WHERE 1 $where";
		
        return $this->db->query($sql);
    }
	
}
