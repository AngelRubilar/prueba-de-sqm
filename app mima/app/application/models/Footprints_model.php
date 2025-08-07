<?php

class Footprints_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'huellas';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
        $footprints_table = $this->bd_mimasoft_fc->dbprefix('huellas');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $footprints_table.id=$id";
        }
		
		$id_unidad = get_array_value($options, "id_unidad");
        if ($id_unidad) {
            $where .= " AND $footprints_table.id_unidad=$id_unidad";
        }
		
		$id_tipo_unidad = get_array_value($options, "id_tipo_unidad");
        if ($id_tipo_unidad) {
            $where .= " AND $footprints_table.id_tipo_unidad=$id_tipo_unidad";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $footprints_table.nombre=$nombre";
        }
        
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $footprints_table.* FROM $footprints_table WHERE";
		$sql .= " $footprints_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	 /*Footprints list for multiselect edit in projects model*/
	function get_footprints_of_project($project_id){
		
		$array_footprint = array();
		$array_project_footprint = array();
		$project_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $project_footprints_table.* FROM $project_footprints_table WHERE";
		$sql .= " $project_footprints_table.deleted = 0 AND";
		$sql .= " $project_footprints_table.id_proyecto = $project_id";
		
		$query = $this->db->query($sql);

		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_footprint["id"] = $value["id_huella"];
					$array_footprint["nombre"] =  $this->get_one($value["id_huella"])->nombre;
					$array_footprint["icono"] =  $this->get_one($value["id_huella"])->icono;
					$array_project_footprint[$innerRow] = $array_footprint;
				}	
			}
		}
				
		return $array_project_footprint;
	}
	
	
	//add view of project
	function get_footprints_of_methodology($id_metodologia) {
        $methodology_table = $this->bd_mimasoft_fc->dbprefix('metodologia');
		$methodology_rel_huella_table = $this->bd_mimasoft_fc->dbprefix('metodologia_rel_huella');
		$footprints_table = $this->bd_mimasoft_fc->dbprefix('huellas');
		
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $footprints_table.* FROM $methodology_rel_huella_table, $footprints_table, $methodology_table WHERE";
		$sql .= " $footprints_table.deleted=0 AND $methodology_table.deleted=0";
		$sql .= " AND $footprints_table.id = $methodology_rel_huella_table.id_huella";
		$sql .= " AND $methodology_rel_huella_table.id_metodologia = $methodology_table.id";
		$sql .= " AND $methodology_table.id = $id_metodologia";
		
        return $this->bd_mimasoft_fc->query($sql);
    }

   

}
