<?php

class Client_module_availability_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_module_availability_settings';
        parent::__construct($this->table);
    }

	function save_default_settings($id_cliente){

		$client_context_modules = $this->Client_context_modules_model->get_details()->result();
		
		foreach($client_context_modules as $mod){
		
			$default_module_availability = array(
				"id_cliente" => $id_cliente,
				"id_modulo" => $mod->id,
				"disponible" => 1,
				"deleted" => 0
			);
			$this->save($default_module_availability);
			
		}

	}
	
	function get_client_setting($id_cliente){
		
		$client_context_modules_table = $this->db->dbprefix('client_context_modules');
		$client_module_availability_settings_table = $this->db->dbprefix('client_module_availability_settings');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $client_module_availability_settings_table.*, $client_context_modules_table.name, $client_module_availability_settings_table.id_modulo AS id
				FROM $client_module_availability_settings_table, $client_context_modules_table WHERE";
		$sql .= " $client_module_availability_settings_table.id_cliente = $id_cliente";
		$sql .= " AND $client_module_availability_settings_table.id_modulo = $client_context_modules_table.id";
		$sql .= " AND $client_module_availability_settings_table.deleted = 0";
				
		return $this->db->query($sql);
		
	}

}