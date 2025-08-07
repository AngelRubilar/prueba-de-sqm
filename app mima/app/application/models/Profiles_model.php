<?php

class Profiles_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'profiles';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $profiles_table = $this->db->dbprefix('profiles');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $profiles_table.id=$id";
        }
		
		$name = get_array_value($options, "name");
        if ($name) {
            $where .= " AND $profiles_table.name=$name";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $profiles_table.created_by=$created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $profiles_table.* FROM $profiles_table WHERE";
		$sql .= " $profiles_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
}