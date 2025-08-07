<?php

class Unity_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'unidad';
        parent::__construct($this->table);
    }
/*
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
    }*/
	
	function get_units_of_unit_type($id_tipo_unidad){
		
		$unit_table = $this->bd_mimasoft_fc->dbprefix('unidad');
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $unit_table.* FROM $unit_table WHERE";
		$sql .= " $unit_table.deleted=0";
		$sql .= " AND $unit_table.id_tipo_unidad = $id_tipo_unidad";
		
		return $this->bd_mimasoft_fc->query($sql);
		
	}
	
	function get_units_of_unit_type2($id_tipo_unidad){
		$unit_table = $this->bd_mimasoft_fc->dbprefix('unidad');
		$unit_type_table = $this->bd_mimasoft_fc->dbprefix('tipo_unidad');
		
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $unit_table.* FROM $unit_table, $unit_type_table WHERE";
		$sql .= " $unit_table.deleted=0";
		$sql .= " AND $unit_table.id_tipo_unidad = $unit_type_table.id ";
		$sql .= " AND $unit_table.id_tipo_unidad = $id_tipo_unidad ";
		return $this->bd_mimasoft_fc->query($sql);

       /* $query= $this->bd_mimasoft_fc->query($sql);
		
		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_materiales["id"] = $value["id"];
					$array_materiales["nombre"] =  $this->get_one($value["id"])->nombre;
					$array_materiales_form[$innerRow] = $array_materiales;
				}	
			}
		}
				
		return $array_materiales_form;*/

    }

	
}
