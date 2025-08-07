<?php

class KPI_Values_condition_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'kpi_valores_condicion';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $kpi_valores_condicion_table = $this->db->dbprefix('kpi_valores_condicion');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $kpi_valores_condicion_table.id = $id";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $kpi_valores_condicion_table.* FROM $kpi_valores_condicion_table WHERE";
		$sql .= " $kpi_valores_condicion_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }


}
