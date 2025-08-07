<?php

class Clients_modules_rel_profiles_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'clients_modules_rel_profiles';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
        $clients_modules_rel_profiles_table = $this->db->dbprefix('clients_modules_rel_profiles');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $clients_modules_rel_profiles_table.* FROM $clients_modules_rel_profiles_table";
		
        return $this->db->query($sql);
    }
	
	function get_setting_of_profile($profile_id){
		
		$clients_modules_rel_profiles_table = $this->db->dbprefix('clients_modules_rel_profiles');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $clients_modules_rel_profiles_table.* FROM $clients_modules_rel_profiles_table";
		$sql .= " WHERE $clients_modules_rel_profiles_table.id_profile = $profile_id";
		
        return $this->db->query($sql);
		
	}
	
	function delete_clients_modules_rel_profiles($id){

	$clients_modules_rel_profiles = $this->db->dbprefix('clients_modules_rel_profiles');
	$sql = "DELETE FROM $clients_modules_rel_profiles WHERE";
	$sql .= " $clients_modules_rel_profiles.id = $id";

	if($this->db->query($sql)){
		return true;
	} else {
		return false;
	}
}
	
	
	
}
