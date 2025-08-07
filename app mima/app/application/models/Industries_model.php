<?php

class Industries_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'rubros';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $industries_table = $this->db->dbprefix('rubros');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $industries_table.id=$id";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $industries_table.*
        FROM $industries_table 
        WHERE $industries_table.deleted=0 $where";

        return $this->db->query($sql);
    }
	
	function get_subindustries_of_industry($industry_id){
		
		$industries_rel_technologies_table = $this->db->dbprefix('rubros_rel_subrubro');
		$technologies_table = $this->db->dbprefix('subrubros');
		
		$this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $technologies_table.*";
		$sql .= " FROM $technologies_table, $industries_rel_technologies_table";
		$sql .= " WHERE $technologies_table.id = $industries_rel_technologies_table.id_subrubro";
		$sql .= " AND $industries_rel_technologies_table.id_rubro = $industry_id";
		$sql .= " AND $technologies_table.deleted = 0";
		//$sql .= " AND $industries_rel_technologies_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}

    function get_primary_contact($contact_id = 0) {
        $users_table = $this->db->dbprefix('users');

        $sql = "SELECT $users_table.id
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.client_id=$contact_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->id;
        }
    }

    function add_remove_star($project_id, $user_id, $type = "add") {
        $clients_table = $this->db->dbprefix('clients');

        $action = " CONCAT($clients_table.starred_by,',',':$user_id:') ";
        $where = " AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) = 0"; //don't add duplicate

        if ($type != "add") {
            $action = " REPLACE($clients_table.starred_by, ',:$user_id:', '') ";
            $where = "";
        }

        $sql = "UPDATE $clients_table SET $clients_table.starred_by = $action
        WHERE $clients_table.id=$project_id $where";
        return $this->db->query($sql);
    }

    function get_starred_clients($user_id) {
        $clients_table = $this->db->dbprefix('clients');

        $sql = "SELECT $clients_table.id,  $clients_table.company_name
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND FIND_IN_SET(':$user_id:',$clients_table.starred_by)
        ORDER BY $clients_table.company_name ASC";
        return $this->db->query($sql);
    }

}
