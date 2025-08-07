<?php

class Methodology_rel_footprints_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'metodologia_rel_huella';
        parent::__construct($this->table);
    }
	
	/* Elimina las huellas relacionadas a una metodologia */
    function delete_huellas_related_to_methodology($id_metodologia){
        
        $metodologia_rel_huella_table = $this->bd_mimasoft_fc->dbprefix('metodologia_rel_huella');
        $sql = "DELETE FROM $metodologia_rel_huella_table WHERE";
        $sql .= " $metodologia_rel_huella_table.id_metodologia = $id_metodologia";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	

}
