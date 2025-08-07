<?php

class Client_context_profiles_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_context_profiles';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $client_context_profiles_table = $this->db->dbprefix('client_context_profiles');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $client_context_profiles_table.id =$id";
        }
		
		$name = get_array_value($options, "name");
        if ($name) {
            $where .= " AND $client_context_profiles_table.name = $name";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $client_context_profiles_table.created_by = $created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $client_context_profiles_table.* FROM $client_context_profiles_table WHERE";
		$sql .= " $client_context_profiles_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	
	function get_user_permitting_in_module($id_usuario, $id_modulo, $id_submodulo) {
		
        $users_table = $this->db->dbprefix('users');
		$client_context_profiles_table = $this->db->dbprefix('client_context_profiles');
		$client_context_modules_rel_profiles_table = $this->db->dbprefix('client_context_modules_rel_profiles');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT client_context_modules_rel_profiles.* ";
		$sql .= " FROM ";
		$sql .= " $users_table,";
		$sql .= " $client_context_profiles_table,";
		$sql .= " $client_context_modules_rel_profiles_table";
		$sql .= " WHERE ";
		$sql .= " $users_table.deleted = 0 AND";
		$sql .= " $users_table.id = $id_usuario AND";
		$sql .= " $client_context_profiles_table.deleted = 0 AND";
		$sql .= " $client_context_profiles_table.id = $users_table.id_client_context_profile AND";
		$sql .= " $client_context_modules_rel_profiles_table.id_client_context_profile = $client_context_profiles_table.id AND";
		$sql .= " $client_context_modules_rel_profiles_table.id_client_context_module = $id_modulo AND";
		$sql .= " $client_context_modules_rel_profiles_table.id_client_context_submodule = $id_submodulo";
		
        return $this->db->query($sql);
    }
	
}