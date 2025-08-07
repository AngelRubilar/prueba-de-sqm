<?php

class Evaluated_permitting_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluados_permisos';
        parent::__construct($this->table);
    }

	function delete_evaluated_related_to_permitting($id_permitting){
	
		$evaluated_permitting_table = $this->db->dbprefix('evaluados_permisos');
		
		$sql = "UPDATE $evaluated_permitting_table 
				SET $evaluated_permitting_table.deleted = 1 
				WHERE $evaluated_permitting_table.id_permiso = $id_permitting";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	
	}
	
	//Función retorna los evaluados de la matriz de permisos de un proyecto
	function get_evaluated_related_to_project_permitting($id_proyecto){
		
		$evaluated_permitting_table = $this->db->dbprefix('evaluados_permisos');
		$permitting_table = $this->db->dbprefix('permisos');
		
		$sql = "SELECT $permitting_table.id_proyecto, $evaluated_permitting_table.id_permiso, $evaluated_permitting_table.id 
				AS id_evaluados_permisos, $evaluated_permitting_table.nombre_evaluado 
				FROM $permitting_table, $evaluated_permitting_table 
				WHERE $permitting_table.id = $evaluated_permitting_table.id_permiso
				AND $permitting_table.id_proyecto = $id_proyecto 
				AND $permitting_table.deleted = 0 
				AND $evaluated_permitting_table.deleted = 0";
		
		return $this->db->query($sql);
	
	}
	
	function deleted_evaluated_permitting($id){
		
		$evaluados_permisos = $this->db->dbprefix('evaluados_permisos');

		$sql = "UPDATE $evaluados_permisos SET $evaluados_permisos.deleted=1 WHERE $evaluados_permisos.id=$id; ";
		$this->db->query($sql);
		
	}

}
