<?php

class General_settings_clients_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'general_settings_clients';
        parent::__construct($this->table);
    }
	
	/* Crea la configuración por defecto del cliente */
	function save_default_settings($id_cliente){
		
		$default_general_settings = array(
			"id_cliente" => $id_cliente,
			"thousands_separator" => 1,
			"decimals_separator" => 2,
			"decimal_numbers" => 2,
			"date_format" => "d/m/Y",
			"timezone" => "UTC",
			"time_format" =>"24_hours",
		);
		
		$this->save($default_general_settings);
	}
	
	function get_setting($id_cliente = 0) {
        $result = $this->db->get_where($this->table, array('id_cliente' => $id_cliente), 1);
        if ($result->num_rows() == 1) {
            return $result->row();
        }
    }
	
}