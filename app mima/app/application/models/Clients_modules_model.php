<?php

class Clients_modules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'clients_modules';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $clients_modules_table = $this->db->dbprefix('clients_modules');
		
  		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= "AND $clients_modules_table.id=$id";
		}
		
		$modules = get_array_value($options, "modules");
        if (count($modules)) {
			$modules_list = implode(', ', $modules);
            $where .= " AND $clients_modules_table.id IN ($modules_list)";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $clients_modules_table.* FROM $clients_modules_table WHERE 1 $where";
		
        return $this->db->query($sql);
    }
	
	function get_details_edit_mode($profile_id, $options = array()){
		
		$clients_modules_table = $this->db->dbprefix('clients_modules');
		$clients_modules_rel_profiles_table = $this->db->dbprefix('clients_modules_rel_profiles');
		$clients_submodules_table = $this->db->dbprefix('clients_submodules');
		
		$where = "";
        $modules = get_array_value($options, "modules");
        if (count($modules)) {
			$modules_list = implode(', ', $modules);
            $where .= " AND $clients_modules_table.id IN ($modules_list)";
		}
		
        $this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $clients_modules_rel_profiles_table.*, $clients_modules_table.name AS nombre_modulo,";
		$sql .= " $clients_submodules_table.name AS nombre_submodulo";
		$sql .= " FROM $clients_modules_rel_profiles_table";
		$sql .= " INNER JOIN $clients_modules_table";
		$sql .= " ON $clients_modules_rel_profiles_table.id_client_module = $clients_modules_table.id";
		$sql .= " LEFT JOIN $clients_submodules_table";
		$sql .= " ON $clients_modules_rel_profiles_table.id_client_submodule = $clients_submodules_table.id";
		$sql .= " WHERE $clients_modules_rel_profiles_table.id_profile = $profile_id";
		$sql .= " $where";
		$sql .= " ORDER BY $clients_modules_rel_profiles_table.id_client_module, $clients_modules_rel_profiles_table.id_client_submodule";
		
        return $this->db->query($sql);
		
	}
	
	function get_module_name($id_module) {
		
        $clients_modules_table = $this->db->dbprefix('clients_modules');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $clients_modules_table.name FROM $clients_modules_table";
		$sql .= " WHERE $clients_modules_table.id = $id_module";
		
        return $this->db->query($sql);
    }
	
	function get_modules_and_submodules($options = array()){
		
		$clients_modules_table = $this->db->dbprefix('clients_modules');
		$clients_submodules_table = $this->db->dbprefix('clients_submodules');
		
		$where = "";
        $modules = get_array_value($options, "modules");
        if (count($modules)) {
			$modules_list = implode(', ', $modules);
            $where .= " AND $clients_modules_table.id IN ($modules_list)";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');

		$sql = "SELECT $clients_modules_table.id AS id_client_module, $clients_modules_table.name AS nombre_modulo,";
		$sql .= " $clients_submodules_table.id AS id_client_submodule, $clients_submodules_table.name AS nombre_submodulo";
		$sql .= " FROM $clients_modules_table";
		$sql .= " LEFT JOIN $clients_submodules_table";
		$sql .= " ON $clients_modules_table.id = $clients_submodules_table.id_client_module";
		$sql .= " AND $clients_modules_table.deleted = 0";
		$sql .= " AND $clients_submodules_table.deleted = 0";
		$sql .= " WHERE 1 $where";

		return $this->db->query($sql);
		
	}
	
	function get_project_modules_and_submodules($options = array()){
		
		$clients_modules_table = $this->db->dbprefix('clients_modules');
		$clients_submodules_table = $this->db->dbprefix('clients_submodules');
		
		$where = "";
        $clients_modules = get_array_value($options, "clients_modules");
        if (count($clients_modules)) {
			$in = implode(", ", $clients_modules);
            $where .= "AND $clients_modules_table.id IN ($in)";
        }
		
		$clients_submodules = get_array_value($options, "clients_submodules");
        if ($clients_submodules) {
			$in = implode(", ", $clients_submodules);
			$where .= " AND ( $clients_submodules_table.id IS NULL OR $clients_submodules_table.id IN ($in) )";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $clients_modules_table.id AS id_modulo, $clients_modules_table.name AS nombre_modulo,";
		$sql .= " $clients_submodules_table.id AS id_submodulo, $clients_submodules_table.name AS nombre_submodulo";
		$sql .= " FROM $clients_modules_table";
		$sql .= " LEFT JOIN $clients_submodules_table";
		$sql .= " ON $clients_modules_table.id = $clients_submodules_table.id_client_module";
		$sql .= " WHERE 1";
		$sql .= " $where";
		$sql .= " AND $clients_modules_table.deleted = 0";
		$sql .= " AND ($clients_submodules_table.deleted = 0 OR $clients_submodules_table.deleted IS NULL)";
				
		return $this->db->query($sql);
		
	}
	
}