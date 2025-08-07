<?php

class Feedback_matrix_config_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'feedback_matrix_config';
        parent::__construct($this->table);
    }

	function get_fields_of_feedback_matrix($id_feedback_matrix){

		$feedback_matrix_config_table = $this->db->dbprefix('feedback_matrix_config');
		$feedback_matrix_config_rel_campos_table = $this->db->dbprefix('feedback_matrix_config_rel_campos');
		$campos_table = $this->db->dbprefix('campos');
		
		$sql = "SELECT $feedback_matrix_config_rel_campos_table.id AS id_rel, $feedback_matrix_config_table.id AS id_feedback_matrix_config, $campos_table.id_tipo_campo,
				$campos_table.id AS id_campo, $campos_table.nombre AS nombre_campo, $campos_table.html_name, $campos_table.obligatorio,
				$campos_table.opciones, $campos_table.habilitado, $campos_table.default_value
				FROM $campos_table, $feedback_matrix_config_table, $feedback_matrix_config_rel_campos_table 
				WHERE $feedback_matrix_config_table.id = $feedback_matrix_config_rel_campos_table.id_feedback_matrix_config
				AND $campos_table.id = $feedback_matrix_config_rel_campos_table.id_campo 
				AND $feedback_matrix_config_table.id = $id_feedback_matrix 
				AND $campos_table.deleted = 0 
				AND $feedback_matrix_config_rel_campos_table.deleted = 0 
				AND $feedback_matrix_config_table.deleted = 0";
		$sql .= " ORDER BY $feedback_matrix_config_rel_campos_table.id";
		
		return $this->db->query($sql);
		
	}
	
}
