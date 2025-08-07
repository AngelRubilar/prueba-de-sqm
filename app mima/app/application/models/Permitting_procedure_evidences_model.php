<?php

class Permitting_procedure_evidences_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evidencias_tramitacion_permisos';
        parent::__construct($this->table);
    }
	
	function delete_permitting_procedure_evidences($id){
		
		$evidencias_tramitacion_permisos = $this->db->dbprefix('evidencias_tramitacion_permisos');

		$sql = "UPDATE $evidencias_tramitacion_permisos SET $evidencias_tramitacion_permisos.deleted=1 WHERE $evidencias_tramitacion_permisos.id=$id; ";
		$this->db->query($sql);
		
	}
	
	
}
