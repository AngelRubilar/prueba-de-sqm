<?php

class Stakeholders_rel_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'stakeholders_matrix_config_rel_campos';
        parent::__construct($this->table);
    }
	
	//Función que retorna un array con los id y nombres de campos de los stakeholders para multiselect de campos de la matriz de configuracion stakeholders.
	function get_stakeholders_matrix_fields($id_stakeholder_matrix_config){
		
		$stakeholders_rel_fields_table = $this->db->dbprefix('stakeholders_matrix_config_rel_campos');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $stakeholders_rel_fields_table, $fields_table";
		$sql .= " WHERE $stakeholders_rel_fields_table.id_campo = $fields_table.id";
		$sql .= " AND $stakeholders_rel_fields_table.id_stakeholder_matrix_config = $id_stakeholder_matrix_config";
		$sql .= " AND $fields_table.deleted = 0";
		$sql .= " AND $stakeholders_rel_fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields_related_to_stakeholder_matrix($id_stakeholder_matrix_config){
		
		$stakeholders_rel_fields_table = $this->db->dbprefix('stakeholders_matrix_config_rel_campos');
		
		$sql = "UPDATE $stakeholders_rel_fields_table 
				SET $stakeholders_rel_fields_table.deleted = 1 
				WHERE $stakeholders_rel_fields_table.id_stakeholder_matrix_config = $id_stakeholder_matrix_config";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
}
