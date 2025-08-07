<?php

class General_settings_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'general_settings';
        parent::__construct($this->table);
    }
	
	/* Crea la configuración por defecto del proyecto. Se llama a este método si un proyecto no tiene configuración */
	function save_default_settings($id_cliente, $id_proyecto){
		
		$default_general_settings = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"thousands_separator" => 1,
			"decimals_separator" => 2,
			"decimal_numbers" => 2,
			"date_format" => "d/m/Y",
			"timezone" => "UTC",
			"time_format" =>"24_hours",
			"language" => "spanish",
			"general_color" => "#00b393",
		);
		
		$this->save($default_general_settings);
	}
	
	function get_setting($id_cliente = 0, $id_proyecto = 0) {
        $result = $this->db->get_where($this->table, array('id_cliente' => $id_cliente, 'id_proyecto' => $id_proyecto), 1);
        if ($result->num_rows() == 1) {
            return $result->row();
        }
    }
	
	function delete_general_settings($id){
		
        $general_settings = $this->db->dbprefix('general_settings');
		
        $sql = "DELETE FROM $general_settings WHERE";
        $sql .= " $general_settings.id = $id";
		
		$this->db->query($sql);
		
	}
	
	
}