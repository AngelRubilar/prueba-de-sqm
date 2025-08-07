<?php

class Reports_configuration_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'reports_configuration_settings';
        parent::__construct($this->table);
    }
	
	/* Crea la configuración por defecto del proyecto. Se llama a este método si un proyecto no tiene configuración */
	function save_default_settings($id_cliente, $id_proyecto, $id_configuracion_reporte = NULL){
	
		$default_reports_config = array(
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"project_data" => 1,
			"rca_compromises" => 1,
			"reportable_compromises" => 1,
			"ambiental_events" => 1,
			"consumptions" => 1,
			"waste" => 1,
			"ambiental_education" => 1,
			"project_modifications" => 1,
			"permittings" => 1,
			"relevant_topics" => 1,
			"deleted" => 0,
			"compromises" => 1,
		);
		
		//$materials = $this->Materials_model->get_details()->result();
		$materials = $this->Materials_model->get_materials_of_project($id_proyecto)->result();
		$json_materials = array();
		$json_materials2 = array();
		
		foreach($materials as $mat){
			$json_materials["id"] = $mat->id;
			$json_materials["estado"] = 1;
			$json_materials2[] = $json_materials;
		}
		
		$default_reports_config["materials"] = json_encode($json_materials2);
		
		if($id_configuracion_reporte){
			$this->save($default_reports_config, $id_configuracion_reporte);	
		} else {
			$this->save($default_reports_config);	
		}	
	
	}
	
	function delete_reports_configuration($id){
		
        $reports_configuration_settings = $this->db->dbprefix('reports_configuration_settings');
		
        $sql = "DELETE FROM $reports_configuration_settings WHERE";
        $sql .= " $reports_configuration_settings.id = $id";
		
		$this->db->query($sql);
		
	}
	

}