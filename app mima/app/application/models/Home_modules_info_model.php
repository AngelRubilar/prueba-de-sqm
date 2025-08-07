<?php

class Home_modules_info_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'home_modules_info';
        parent::__construct($this->table);
    }
	
	function get_all_ordered(){
		
		$home_modules_info_table = $this->db->dbprefix('home_modules_info');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $home_modules_info_table.*";
		$sql .= " FROM $home_modules_info_table";
		$sql .= " WHERE $home_modules_info_table.deleted = 0";
		$sql .= " ORDER BY $home_modules_info_table.orden ASC";
		
		return $this->db->query($sql);
		
	}
	
}
