<?php

class Values_agreements_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_acuerdos';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        
		$agreements_values_table = $this->db->dbprefix('valores_acuerdos');
		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $agreements_values_table.id = $id ";
        }
		
		$id_agreement_matrix_config = get_array_value($options, "id_agreement_matrix_config");
        if ($id_agreement_matrix_config) {
            $where .= " AND $agreements_matrix_config_table.id = $id_agreement_matrix_config ";
        }
		
		$gestor = get_array_value($options, "gestor");
        if ($gestor) {
            $where .= " AND $agreements_values_table.gestor = $gestor ";
        }
		
		$id_stakeholder = get_array_value($options, "id_stakeholder");
        if ($id_stakeholder) {
			$where .= " AND $agreements_values_table.stakeholders LIKE '%\"$id_stakeholder\"%'";
        }
		
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $agreements_matrix_config_table.id_proyecto, $agreements_values_table.*
				FROM $agreements_values_table, $agreements_matrix_config_table 
				WHERE $agreements_matrix_config_table.id = $agreements_values_table.id_agreement_matrix_config
				AND $agreements_values_table.deleted = 0 
				$where 
				ORDER BY $agreements_values_table.created DESC";
		
        return $this->db->query($sql);
    }
	
	function get_agreements_that_have_stakeholder($id_stakeholder, $id_agreements_matrix){
		
		$agreements_values_table = $this->db->dbprefix('valores_acuerdos');
		
		$sql = "SELECT $agreements_values_table.*";
		$sql .= " FROM $agreements_values_table";
		$sql .= " WHERE $agreements_values_table.stakeholders LIKE '%\"$id_stakeholder\"%' ";
		$sql .= " AND $agreements_values_table.id_agreement_matrix_config = $id_agreements_matrix";
		$sql .= " AND $agreements_values_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}

}
