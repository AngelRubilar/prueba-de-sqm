<?php

class Stakeholders_matrix_config_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'stakeholders_matrix_config';
        parent::__construct($this->table);
    }

	function get_fields_of_stakeholder_matrix($id_stakeholder_matrix){

		$stakeholders_matrix_config_table = $this->db->dbprefix('stakeholders_matrix_config');
		$stakeholders_matrix_config_rel_campos_table = $this->db->dbprefix('stakeholders_matrix_config_rel_campos');
		$campos_table = $this->db->dbprefix('campos');
		
		$sql = "SELECT $stakeholders_matrix_config_rel_campos_table.id AS id_rel, $stakeholders_matrix_config_table.id AS id_stakeholder_matrix_config, $campos_table.id_tipo_campo,
				$campos_table.id AS id_campo, $campos_table.nombre AS nombre_campo, $campos_table.html_name, $campos_table.obligatorio,
				$campos_table.opciones, $campos_table.habilitado, $campos_table.default_value
				FROM $campos_table, $stakeholders_matrix_config_table, $stakeholders_matrix_config_rel_campos_table 
				WHERE $stakeholders_matrix_config_table.id = $stakeholders_matrix_config_rel_campos_table.id_stakeholder_matrix_config
				AND $campos_table.id = $stakeholders_matrix_config_rel_campos_table.id_campo 
				AND $stakeholders_matrix_config_table.id = $id_stakeholder_matrix 
				AND $campos_table.deleted = 0 
				AND $stakeholders_matrix_config_rel_campos_table.deleted = 0 
				AND $stakeholders_matrix_config_table.deleted = 0";
		$sql .= " ORDER BY $stakeholders_matrix_config_rel_campos_table.id";
		
		return $this->db->query($sql);
		
	}
	
}
