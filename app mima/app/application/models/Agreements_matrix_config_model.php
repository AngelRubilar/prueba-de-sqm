<?php

class Agreements_matrix_config_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'agreements_matrix_config';
        parent::__construct($this->table);
    }

	function get_fields_of_agreements_matrix($id_agreement_matrix){

		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');
		$agreements_matrix_config_rel_campos_table = $this->db->dbprefix('agreements_matrix_config_rel_campos');
		$campos_table = $this->db->dbprefix('campos');
		
		$sql = "SELECT $agreements_matrix_config_rel_campos_table.id AS id_rel, $agreements_matrix_config_table.id AS id_agreement_matrix_config, $campos_table.id_tipo_campo,
				$campos_table.id AS id_campo, $campos_table.nombre AS nombre_campo, $campos_table.html_name, $campos_table.obligatorio,
				$campos_table.opciones, $campos_table.habilitado, $campos_table.default_value
				FROM $campos_table, $agreements_matrix_config_table, $agreements_matrix_config_rel_campos_table 
				WHERE $agreements_matrix_config_table.id = $agreements_matrix_config_rel_campos_table.id_agreement_matrix_config
				AND $campos_table.id = $agreements_matrix_config_rel_campos_table.id_campo 
				AND $agreements_matrix_config_table.id = $id_agreement_matrix 
				AND $campos_table.deleted = 0 
				AND $agreements_matrix_config_rel_campos_table.deleted = 0 
				AND $agreements_matrix_config_table.deleted = 0";
		$sql .= " ORDER BY $agreements_matrix_config_rel_campos_table.id";
		
		return $this->db->query($sql);
		
	}
	
}
