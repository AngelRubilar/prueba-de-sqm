<?php

class Reports_units_settings_clients_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'reports_units_settings_clients';
        parent::__construct($this->table);
    }
	
	function delete_reports_units_settings($client_id){
		
		$reports_units_settings_clients_table = $this->db->dbprefix('reports_units_settings_clients');
		$sql = "DELETE FROM $reports_units_settings_clients_table WHERE";
		$sql .= " $reports_units_settings_clients_table.id_cliente = $client_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function save_default_settings($id_cliente){
		$tipos_de_unidad = $this->Unity_type_model->get_all()->result();
		
		foreach($tipos_de_unidad as $tipo_unidad){
			
			$unidad = $this->Unity_model->get_one_where(array("id_tipo_unidad" => $tipo_unidad->id));
			
			$default_report_units = array(
				"id_cliente" => $id_cliente,
				"id_tipo_unidad" => $tipo_unidad->id,
				"id_unidad" => $unidad->id,
			);

			$this->save($default_report_units);
			
		}
	
	}

}