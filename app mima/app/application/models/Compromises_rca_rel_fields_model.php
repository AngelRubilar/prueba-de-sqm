<?php

class Compromises_rca_rel_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'compromisos_rca_rel_campos';
        parent::__construct($this->table);
    }
	
	//Función que retorna un array con los id y nombres de campos de los compromisos para multiselect de campos del compromiso.
	function get_compromise_fields($id_compromiso){
		
		$compromises_rel_fields_table = $this->db->dbprefix('compromisos_rca_rel_campos');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $compromises_rel_fields_table, $fields_table";
		$sql .= " WHERE $compromises_rel_fields_table.id_campo = $fields_table.id";
		$sql .= " AND $compromises_rel_fields_table.id_compromiso = $id_compromiso";
		$sql .= " AND $fields_table.deleted = 0";
		$sql .= " AND $compromises_rel_fields_table.deleted = 0";
		$sql .= " ORDER BY $compromises_rel_fields_table.id";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields_related_to_compromise($id_compromiso){
		
		$compromises_rel_fields_table = $this->db->dbprefix('compromisos_rca_rel_campos');
		
		$sql = "UPDATE $compromises_rel_fields_table 
				SET $compromises_rel_fields_table.deleted = 1 
				WHERE $compromises_rel_fields_table.id_compromiso = $id_compromiso";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function delete_compromises_rel_fields($id){
		
		$compromisos_rel_campos = $this->db->dbprefix('compromisos_rca_rel_campos');

		$sql = "UPDATE $compromisos_rel_campos SET $compromisos_rel_campos.deleted=1 WHERE $compromisos_rel_campos.id=$id; ";
		$this->db->query($sql);
		
	}
	
}
