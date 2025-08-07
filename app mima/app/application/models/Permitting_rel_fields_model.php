<?php

class Permitting_rel_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'permisos_rel_campos';
        parent::__construct($this->table);
    }
	
	//Función que retorna un array con los id y nombres de campos de los permisos para multiselect de campos del permiso.
	function get_permitting_fields($id_permitting){
		
		$permitting_rel_fields_table = $this->db->dbprefix('permisos_rel_campos');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $permitting_rel_fields_table, $fields_table";
		$sql .= " WHERE $permitting_rel_fields_table.id_campo = $fields_table.id";
		$sql .= " AND $permitting_rel_fields_table.id_permiso = $id_permitting";
		$sql .= " AND $fields_table.deleted = 0";
		$sql .= " AND $permitting_rel_fields_table.deleted = 0";
		$sql .= " ORDER BY $permitting_rel_fields_table.id";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields_related_to_permitting($id_permitting){
		
		$permitting_rel_fields_table = $this->db->dbprefix('permisos_rel_campos');
		
		$sql = "UPDATE $permitting_rel_fields_table 
				SET $permitting_rel_fields_table.deleted = 1 
				WHERE $permitting_rel_fields_table.id_permiso = $id_permitting";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function delete_permitting_rel_fields($id){
		
		$permisos_rel_campos = $this->db->dbprefix('permisos_rel_campos');

		$sql = "UPDATE $permisos_rel_campos SET $permisos_rel_campos.deleted=1 WHERE $permisos_rel_campos.id=$id; ";
		$this->db->query($sql);
		
	}
	
}
