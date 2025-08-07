<?php

class Feedback_rel_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'feedback_matrix_config_rel_campos';
        parent::__construct($this->table);
    }
	
	//Función que retorna un array con los id y nombres de campos de los acuerdos para multiselect de campos de la matriz de configuracion feedback.
	function get_feedback_matrix_fields($id_feedback_matrix_config){
		
		$feedback_rel_fields_table = $this->db->dbprefix('feedback_matrix_config_rel_campos');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $feedback_rel_fields_table, $fields_table";
		$sql .= " WHERE $feedback_rel_fields_table.id_campo = $fields_table.id";
		$sql .= " AND $feedback_rel_fields_table.id_feedback_matrix_config = $id_feedback_matrix_config";
		$sql .= " AND $fields_table.deleted = 0";
		$sql .= " AND $feedback_rel_fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields_related_to_feedback_matrix($id_feedback_matrix_config){
		
		$feedback_rel_fields_table = $this->db->dbprefix('feedback_matrix_config_rel_campos');
		
		$sql = "UPDATE $feedback_rel_fields_table 
				SET $feedback_rel_fields_table.deleted = 1 
				WHERE $feedback_rel_fields_table.id_feedback_matrix_config = $id_feedback_matrix_config";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
}
