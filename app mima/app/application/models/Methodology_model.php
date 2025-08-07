<?php

class Methodology_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'metodologia';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
        $methodology_table = $this->bd_mimasoft_fc->dbprefix('metodologia');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $methodology_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $methodology_table.nombre=$nombre";
        }
        
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $methodology_table.* FROM $methodology_table WHERE";
		$sql .= " $methodology_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	
	function get_methodologies_of_fh($fh_id){
		$formatos_huella_table = $this->bd_mimasoft_fc->dbprefix('formatos_huella');
		$metodologia_table = $this->bd_mimasoft_fc->dbprefix('metodologia');
		$fh_rel_metodologia_table = $this->bd_mimasoft_fc->dbprefix('fh_rel_metodologia');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $metodologia_table.* FROM $formatos_huella_table, $fh_rel_metodologia_table, $metodologia_table WHERE";
		$sql .= " $formatos_huella_table.deleted=0 AND $metodologia_table.deleted=0 AND $fh_rel_metodologia_table.deleted=0";
		$sql .= " AND $fh_rel_metodologia_table.id_fh = $formatos_huella_table.id";
		$sql .= " AND $fh_rel_metodologia_table.id_metodologia = $metodologia_table.id";
        $sql .= " AND $formatos_huella_table.id = $fh_id ORDER BY $fh_rel_metodologia_table.id";
        
        return $this->bd_mimasoft_fc->query($sql);

    }
	
}
