<?php

class Client_context_modules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_context_modules';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $client_context_modules_table = $this->db->dbprefix('client_context_modules');
		
  		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= "AND $client_context_modules_table.id=$id";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $client_context_modules_table.* FROM $client_context_modules_table WHERE 1 $where";
		
        return $this->db->query($sql);
    }
	
	function get_details_edit_mode($client_context_profile_id){
		
		$client_context_modules_table = $this->db->dbprefix('client_context_modules');
		$client_context_modules_rel_profiles_table = $this->db->dbprefix('client_context_modules_rel_profiles');
		$client_context_submodules_table = $this->db->dbprefix('client_context_submodules');

        $this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $client_context_modules_rel_profiles_table.*, $client_context_modules_table.name AS nombre_modulo,";
		$sql .= " $client_context_submodules_table.name AS nombre_submodulo, $client_context_modules_table.contexto";
		$sql .= " FROM $client_context_modules_rel_profiles_table";
		$sql .= " INNER JOIN $client_context_modules_table";
		$sql .= " ON $client_context_modules_rel_profiles_table.id_client_context_module = $client_context_modules_table.id";
		$sql .= " LEFT JOIN $client_context_submodules_table";
		$sql .= " ON $client_context_modules_rel_profiles_table.id_client_context_submodule = $client_context_submodules_table.id";
		$sql .= " WHERE $client_context_modules_rel_profiles_table.id_client_context_profile = $client_context_profile_id";
		$sql .= " ORDER BY $client_context_modules_table.orden ASC, $client_context_modules_rel_profiles_table.id_client_context_submodule ASC";

        return $this->db->query($sql);
		
	}
	
	function get_modules_and_submodules(){
		
		$client_context_modules_table = $this->db->dbprefix('client_context_modules');
		$client_context_submodules_table = $this->db->dbprefix('client_context_submodules');

		$this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $client_context_modules_table.id AS id_client_context_module, $client_context_modules_table.name AS nombre_modulo,";
		$sql .= " $client_context_submodules_table.id AS id_client_context_submodule, $client_context_submodules_table.name AS nombre_submodulo,";
		$sql .= " $client_context_modules_table.contexto";
		$sql .= " FROM $client_context_modules_table";
		$sql .= " LEFT JOIN $client_context_submodules_table";
		$sql .= " ON $client_context_modules_table.id = $client_context_submodules_table.id_client_context_module";
		$sql .= " AND $client_context_modules_table.deleted = 0";
		$sql .= " AND $client_context_submodules_table.deleted = 0";
		//$sql .= " WHERE $client_context_modules_table.id NOT IN (3)"; // NIVEL CLIENTE
		$sql .= " WHERE $client_context_modules_table.id != 3"; // NIVEL CLIENTE
		$sql .= " ORDER BY $client_context_modules_table.orden ASC";
		
		return $this->db->query($sql);
		
	}
	
	function get_client_context_modules_for_notification_config(){
		
		$client_context_modules_table = $this->db->dbprefix('client_context_modules');
		$client_context_submodules_table = $this->db->dbprefix('client_context_submodules');

		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $client_context_modules_table.id AS id_modulo, $client_context_modules_table.name AS nombre_modulo,";
		$sql .= " $client_context_modules_table.contexto, $client_context_submodules_table.id AS id_submodulo, $client_context_submodules_table.name AS nombre_submodulo";
		$sql .= " FROM $client_context_modules_table";
		$sql .= " LEFT JOIN $client_context_submodules_table";
		$sql .= " ON $client_context_modules_table.id = $client_context_submodules_table.id_client_context_module";
		$sql .= " WHERE $client_context_modules_table.id IN (1,2,3,4,5,6,7,8,9)";
		//$sql .= " AND ( $client_context_submodules_table.id IS NULL OR $client_context_submodules_table.id IN (4,6,7,8,10,11,12,14,16) )";
		$sql .= " AND ( $client_context_submodules_table.id IS NULL OR $client_context_submodules_table.id IN (4,6,10,14,16) )";
		$sql .= " AND $client_context_modules_table.deleted = 0";
		$sql .= " AND ($client_context_submodules_table.deleted = 0 OR $client_context_submodules_table.deleted IS NULL)";
				
		return $this->db->query($sql);
		
	}
	
}
