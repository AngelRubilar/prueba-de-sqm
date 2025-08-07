<?php

class Values_stakeholders_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_stakeholders';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        
		$stakeholders_values_table = $this->db->dbprefix('valores_stakeholders');
		$stakeholders_matrix_config_table = $this->db->dbprefix('stakeholders_matrix_config');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $stakeholders_values_table.id = $id ";
        }
		
		$id_stakeholder_matrix_config = get_array_value($options, "id_stakeholder_matrix_config");
        if ($id_stakeholder_matrix_config) {
            $where .= " AND $stakeholders_matrix_config_table.id = $id_stakeholder_matrix_config ";
        }
        
		$id_tipo_organizacion = get_array_value($options, "id_tipo_organizacion");
        if ($id_tipo_organizacion) {
            $where .= " AND $stakeholders_values_table.id_tipo_organizacion = $id_tipo_organizacion ";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $stakeholders_matrix_config_table.id_proyecto, $stakeholders_values_table.*
				FROM $stakeholders_values_table, $stakeholders_matrix_config_table 
				WHERE $stakeholders_matrix_config_table.id = $stakeholders_values_table.id_stakeholder_matrix_config
				AND $stakeholders_values_table.deleted = 0 
				$where 
				ORDER BY $stakeholders_values_table.created DESC";
		
        return $this->db->query($sql);
    }
	
	function get_number_of_stakeholders_by_type_organization($id_proyecto){
		
		$stakeholders_values_table = $this->db->dbprefix('valores_stakeholders');
		$tipos_organizaciones_table = $this->db->dbprefix('tipos_organizaciones');
		$stakeholders_matrix_config_table = $this->db->dbprefix('stakeholders_matrix_config');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $stakeholders_values_table.id_tipo_organizacion, $tipos_organizaciones_table.nombre, COUNT(*) AS cant_tipo_org_stakeholder";
		$sql .= " FROM $stakeholders_values_table, $tipos_organizaciones_table, $stakeholders_matrix_config_table";
		$sql .= " WHERE $stakeholders_values_table.id_tipo_organizacion = $tipos_organizaciones_table.id";
		$sql .= " AND $stakeholders_matrix_config_table.id = $stakeholders_values_table.id_stakeholder_matrix_config";
		$sql .= " AND $stakeholders_matrix_config_table.id_proyecto = $id_proyecto";
		$sql .= " AND $stakeholders_values_table.deleted = 0";
		$sql .= " AND $tipos_organizaciones_table.deleted = 0";
		$sql .= " AND $stakeholders_matrix_config_table.deleted = 0";
		$sql .= " GROUP BY $stakeholders_values_table.id_tipo_organizacion";
		
		return $this->db->query($sql);
		
	}
	
	
}
