<?php

class Databases_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'bases_de_datos';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $databases_table = $this->bd_mimasoft_fc->dbprefix('bases_de_datos');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $databases_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $databases_table.nombre=$nombre";
        }
 
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $databases_table.* FROM $databases_table WHERE";
		$sql .= " $databases_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	function delete_bd_rel_methodology($id_bd){
        
        $bd_rel_metodologia_table = $this->bd_mimasoft_fc->dbprefix('bd_rel_metodologia');
        $sql = "DELETE FROM $bd_rel_metodologia_table WHERE";
        $sql .= " $bd_rel_metodologia_table.id_bd = $id_bd";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	

}
