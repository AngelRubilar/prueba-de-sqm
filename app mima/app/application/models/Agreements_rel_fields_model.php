<?php

class Agreements_rel_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'agreements_matrix_config_rel_campos';
        parent::__construct($this->table);
    }
	
	//Función que retorna un array con los id y nombres de campos de los acuerdos para multiselect de campos de la matriz de configuracion acuerdos.
	function get_agreements_matrix_fields($id_agreement_matrix_config){
		
		$agreements_rel_fields_table = $this->db->dbprefix('agreements_matrix_config_rel_campos');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $agreements_rel_fields_table, $fields_table";
		$sql .= " WHERE $agreements_rel_fields_table.id_campo = $fields_table.id";
		$sql .= " AND $agreements_rel_fields_table.id_agreement_matrix_config = $id_agreement_matrix_config";
		$sql .= " AND $fields_table.deleted = 0";
		$sql .= " AND $agreements_rel_fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields_related_to_agreement_matrix($id_agreement_matrix_config){
		
		$agreements_rel_fields_table = $this->db->dbprefix('agreements_matrix_config_rel_campos');
		
		$sql = "UPDATE $agreements_rel_fields_table 
				SET $agreements_rel_fields_table.deleted = 1 
				WHERE $agreements_rel_fields_table.id_agreement_matrix_config = $id_agreement_matrix_config";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
}
