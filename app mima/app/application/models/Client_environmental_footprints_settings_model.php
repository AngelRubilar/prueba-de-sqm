<?php

class Client_environmental_footprints_settings_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'client_environmental_footprints_settings';
        parent::__construct($this->table);
    }
	
	function save_default_settings($id_cliente, $id_proyecto){
		
		$default_total_impacts = array(		
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"informacion" => "total_impacts",
			"habilitado" => 1,
			"deleted" => 0
		);
		
		$default_impacts_by_functional_units = array(		
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"informacion" => "impacts_by_functional_units",
			"habilitado" => 1,
			"deleted" => 0
		);
		
		$this->save($default_total_impacts);
		$this->save($default_impacts_by_functional_units);
		
	}
	
}