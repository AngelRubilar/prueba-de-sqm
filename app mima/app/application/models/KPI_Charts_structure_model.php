<?php

class KPI_Charts_structure_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'kpi_estructura_graficos';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $kpi_estructura_graficos_table = $this->db->dbprefix('kpi_estructura_graficos');
		$clientes_table = $this->db->dbprefix('clients');
		$fases_table = $this->db->dbprefix('fases');
		$proyectos_table = $this->db->dbprefix('projects');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $kpi_estructura_graficos_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $kpi_estructura_graficos_table.id_cliente = $id_cliente";
        }
		
		$id_fase = get_array_value($options, "id_fase");
        if ($id_fase) {
            $where .= " AND $kpi_estructura_graficos_table.id_fase = $id_fase";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $kpi_estructura_graficos_table.id_proyecto = $id_proyecto";
        }
		
		$item = get_array_value($options, "item");
        if ($item) {
            $where .= " AND $kpi_estructura_graficos_table.item = '$item'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
				
        $sql = "SELECT $kpi_estructura_graficos_table.id, $clientes_table.company_name AS nombre_cliente,"; 
		$sql .= " $fases_table.nombre AS nombre_fase, $proyectos_table.title AS nombre_proyecto, $kpi_estructura_graficos_table.id_cliente,";
		$sql .= " $kpi_estructura_graficos_table.id_fase, $kpi_estructura_graficos_table.id_proyecto,";
		$sql .= " $kpi_estructura_graficos_table.item, $kpi_estructura_graficos_table.subitem, $kpi_estructura_graficos_table.tipo_grafico,";
		$sql .= " $kpi_estructura_graficos_table.series, $kpi_estructura_graficos_table.submodulo_grafico";
		$sql .= " FROM $kpi_estructura_graficos_table, $clientes_table, $fases_table, $proyectos_table";
		$sql .= " WHERE $kpi_estructura_graficos_table.id_cliente = $clientes_table.id";
		$sql .= " AND $kpi_estructura_graficos_table.id_fase = $fases_table.id";
		$sql .= " AND $kpi_estructura_graficos_table.id_proyecto = $proyectos_table.id";
		//$sql .= " AND $kpi_estructura_graficos_table.id_fase IN (2, 3)";
		$sql .= " AND $kpi_estructura_graficos_table.deleted = 0";
		$sql .= " AND $clientes_table.deleted = 0";
		$sql .= " AND $fases_table.deleted = 0";
		$sql .= " AND $proyectos_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }

}