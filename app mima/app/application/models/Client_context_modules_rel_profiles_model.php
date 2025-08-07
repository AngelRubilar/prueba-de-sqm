<?php

class Client_context_modules_rel_profiles_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_context_modules_rel_profiles';
        parent::__construct($this->table);
    }
	
	
	function get_setting_of_profile($client_context_profile_id){
		
		$client_context_modules_rel_profiles_table = $this->db->dbprefix('client_context_modules_rel_profiles');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $client_context_modules_rel_profiles_table.* FROM $client_context_modules_rel_profiles_table";
		$sql .= " WHERE $client_context_modules_rel_profiles_table.id_client_context_profile = $client_context_profile_id";
		
        return $this->db->query($sql);
		
	}
	
	function delete_client_context_modules_rel_profiles($id){

		$client_context_modules_rel_profiles_table = $this->db->dbprefix('client_context_modules_rel_profiles');
		$sql = "DELETE FROM $client_context_modules_rel_profiles_table WHERE";
		$sql .= " $client_context_modules_rel_profiles_table.id = $id";
	
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
		
	}
	
}
