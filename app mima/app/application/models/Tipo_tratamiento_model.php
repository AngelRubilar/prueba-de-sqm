<?php
class Tipo_tratamiento_model extends Crud_model{
	
    private $table = null;

    function __construct() {
        $this->table = 'tipo_tratamiento';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
	
		$tipo_tratamiento = $this->db->dbprefix('tipo_tratamiento');
		$where = "";

		$id = get_array_value($options, "id");
		if ($id) {
			$where .= " AND $tipo_tratamiento.id=$id";
		}

		$sql = "SELECT $tipo_tratamiento.*
		FROM $tipo_tratamiento   
		WHERE $tipo_tratamiento.deleted=0 $where";

		return $this->db->query($sql);
	
	}
	
}