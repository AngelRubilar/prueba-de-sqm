<?php

class Evaluated_rca_compromises_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evaluados_rca_compromisos';
        parent::__construct($this->table);
    }

	function delete_evaluated_related_to_compromise($id_compromiso){
	
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		
		$sql = "UPDATE $evaluated_compromises_table 
				SET $evaluated_compromises_table.deleted = 1 
				WHERE $evaluated_compromises_table.id_compromiso = $id_compromiso";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	
	}
	
	//Función retorna los evaluados de la matriz de compromisos de un proyecto
	function get_evaluated_related_to_project_compromise($id_proyecto){
		
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		$compromises_table = $this->db->dbprefix('compromisos_rca');
		
		$sql = "SELECT $compromises_table.id_proyecto, $evaluated_compromises_table.id_compromiso, $evaluated_compromises_table.id 
				AS id_evaluados_compromisos, $evaluated_compromises_table.nombre_evaluado 
				FROM $compromises_table, $evaluated_compromises_table 
				WHERE $compromises_table.id = $evaluated_compromises_table.id_compromiso 
				AND $compromises_table.id_proyecto = $id_proyecto 
				AND $compromises_table.deleted = 0 
				AND $evaluated_compromises_table.deleted = 0";
		
		return $this->db->query($sql);
	
	}
	
	function delete_evaluated_compromises($id){
		
		$evaluados_compromisos = $this->db->dbprefix('evaluados_rca_compromisos');
		
        $sql = "UPDATE $evaluados_compromisos SET $evaluados_compromisos.deleted=1 WHERE $evaluados_compromisos.id=$id; ";
        $this->db->query($sql);

	}

}
