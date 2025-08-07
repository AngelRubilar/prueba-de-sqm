<?php

class Values_feedback_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_feedback';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        
		$feedback_values_table = $this->db->dbprefix('valores_feedback');
		$feedback_matrix_config_table = $this->db->dbprefix('feedback_matrix_config');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $feedback_values_table.id = $id ";
        }
		
		$id_feedback_matrix_config = get_array_value($options, "id_feedback_matrix_config");
        if ($id_feedback_matrix_config) {
            $where .= " AND $feedback_matrix_config_table.id = $id_feedback_matrix_config ";
        }
		
		$id_tipo_organizacion = get_array_value($options, "id_tipo_organizacion");
        if ($id_tipo_organizacion) {
            $where .= " AND $feedback_values_table.id_tipo_organizacion = $id_tipo_organizacion ";
        }
		
		$proposito_visita = get_array_value($options, "proposito_visita");
        if ($proposito_visita) {
            $where .= " AND $feedback_values_table.proposito_visita = '$proposito_visita' ";
        }
		
		$responsable = get_array_value($options, "responsable");
        if ($responsable) {
            $where .= " AND $feedback_values_table.responsable = $responsable ";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $feedback_matrix_config_table.id_proyecto, $feedback_values_table.*
				FROM $feedback_values_table, $feedback_matrix_config_table 
				WHERE $feedback_matrix_config_table.id = $feedback_values_table.id_feedback_matrix_config
				AND $feedback_values_table.deleted = 0 
				$where 
				ORDER BY $feedback_values_table.created DESC";
		
        return $this->db->query($sql);
    }
	
	function get_number_of_visits_by_type_of_stakeholder($id_proyecto = 0){
	
		$feedback_values_table = $this->db->dbprefix('valores_feedback');
		$tipos_organizaciones_table = $this->db->dbprefix('tipos_organizaciones');
		$feedback_matrix_config_table = $this->db->dbprefix('feedback_matrix_config');
		
		$sql = "SELECT $tipos_organizaciones_table.id, $tipos_organizaciones_table.nombre, COUNT(*) AS numero_visitas";
		$sql .= " FROM $feedback_values_table, $tipos_organizaciones_table, $feedback_matrix_config_table";
		$sql .= " WHERE $feedback_values_table.id_tipo_organizacion = $tipos_organizaciones_table.id";
		$sql .= " AND $feedback_matrix_config_table.id = $feedback_values_table.id_feedback_matrix_config";
		$sql .= " AND $feedback_matrix_config_table.id_proyecto = $id_proyecto";		
		$sql .= " AND $feedback_values_table.deleted = 0";
		$sql .= " AND $tipos_organizaciones_table.deleted = 0";
		$sql .= " AND $feedback_matrix_config_table.deleted = 0";
		$sql .= " GROUP BY $feedback_values_table.id_tipo_organizacion";
		
        return $this->db->query($sql);
		
	}
	
	function get_number_of_visits_by_visit_purpose($id_proyecto = 0){
		
		$feedback_values_table = $this->db->dbprefix('valores_feedback');
		$feedback_matrix_config_table = $this->db->dbprefix('feedback_matrix_config');
		
		$sql = "SELECT $feedback_values_table.proposito_visita, COUNT(*) AS numero_visitas";
		$sql .= " FROM $feedback_values_table, $feedback_matrix_config_table";
		$sql .= " WHERE $feedback_values_table.id_feedback_matrix_config = $feedback_matrix_config_table.id";
		$sql .= " AND $feedback_matrix_config_table.id_proyecto = $id_proyecto";
		$sql .= " AND $feedback_values_table.deleted = 0";
		$sql .= " AND $feedback_matrix_config_table.deleted = 0";
		$sql .= " GROUP BY $feedback_values_table.proposito_visita";
		
		return $this->db->query($sql);
		
	}
	

}
