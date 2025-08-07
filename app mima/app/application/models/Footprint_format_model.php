<?php

class Footprint_format_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'formatos_huella';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $formatos_huella_table = $this->bd_mimasoft_fc->dbprefix('formatos_huella');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $formatos_huella_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $formatos_huella_table.nombre=$nombre";
        }
 
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formatos_huella_table.* FROM $formatos_huella_table WHERE";
		$sql .= " $formatos_huella_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	function delete_fh_rel_methodology($id_fh){
        
        $fh_rel_metodologia_table = $this->bd_mimasoft_fc->dbprefix('fh_rel_metodologia');
        $sql = "DELETE FROM $fh_rel_metodologia_table WHERE";
        $sql .= " $fh_rel_metodologia_table.id_fh = $id_fh";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	

}
