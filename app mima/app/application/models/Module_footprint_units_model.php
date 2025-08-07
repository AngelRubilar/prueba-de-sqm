<?php

class Module_footprint_units_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'module_footprint_units';
        parent::__construct($this->table);
    }
	
	function delete_footprints($project_id, $client_id){
		
		$module_footprint_units_table = $this->db->dbprefix('module_footprint_units');
		$sql = "DELETE FROM $module_footprint_units_table WHERE";
		$sql .= " $module_footprint_units_table.id_proyecto = $project_id";
		$sql .= " AND $module_footprint_units_table.id_cliente = $client_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function save_default_settings($id_cliente, $id_proyecto){
		
		$tipos_de_unidad = $this->Unity_type_model->get_all()->result();
		
		foreach($tipos_de_unidad as $tipo_unidad){
			if($tipo_unidad->id == 1 || $tipo_unidad->id == 2 || $tipo_unidad->id == 7) {
				$unidad = $this->Unity_model->get_one_where(array("id_tipo_unidad" => $tipo_unidad->id)); // :)
				
				$default_footprints_units = array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $tipo_unidad->id,
					"id_unidad" => $unidad->id,
				);	
				$this->save($default_footprints_units);
			}
		}
		
	}

	function delete_module_footprint_units($id){
		
		$module_footprint_units = $this->db->dbprefix('module_footprint_units');

		$sql = "UPDATE $module_footprint_units SET $module_footprint_units.deleted=1 WHERE $module_footprint_units.id=$id; ";
		$this->db->query($sql);
		
	}
	

}