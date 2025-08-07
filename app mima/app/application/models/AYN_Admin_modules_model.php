<?php

class AYN_Admin_modules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_admin_modules';
        parent::__construct($this->table);
    }
	
	function get_admin_modules_for_notification_config(){
		
		$admin_modules_table = $this->db->dbprefix('ayn_admin_modules');
		$admin_submodules_table = $this->db->dbprefix('ayn_admin_submodules');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $admin_modules_table.id AS id_modulo, $admin_modules_table.name AS nombre_modulo, ";
		$sql .= " $admin_submodules_table.id AS id_submodulo, $admin_submodules_table.name AS nombre_submodulo";
		$sql .= " FROM $admin_modules_table";
		$sql .= " LEFT JOIN $admin_submodules_table";
		$sql .= " ON $admin_modules_table.id = $admin_submodules_table.id_admin_module";
		$sql .= " WHERE $admin_modules_table.id IN (4,5,7,8,9)";
		$sql .= " AND ($admin_submodules_table.id IS NULL OR $admin_submodules_table.id IN (9,12,24,29,32) )";
		$sql .= " AND $admin_modules_table.deleted = 0";
		$sql .= " AND ($admin_submodules_table.deleted = 0 OR $admin_submodules_table.deleted IS NULL)";
		
		return $this->db->query($sql);
		
	}

}