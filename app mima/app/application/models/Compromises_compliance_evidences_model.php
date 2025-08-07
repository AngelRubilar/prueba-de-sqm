<?php

class Compromises_compliance_evidences_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evidencias_cumplimiento_compromisos';
        parent::__construct($this->table);
    }
	
	function delete_compromises_compliance_evidences($id){
		$evidencias_cumplimiento_compromisos = $this->db->dbprefix('evidencias_cumplimiento_compromisos');
		
        $sql = "UPDATE $evidencias_cumplimiento_compromisos SET $evidencias_cumplimiento_compromisos.deleted=1 WHERE $evidencias_cumplimiento_compromisos.id=$id; ";
        $this->db->query($sql);
	}
	
}
