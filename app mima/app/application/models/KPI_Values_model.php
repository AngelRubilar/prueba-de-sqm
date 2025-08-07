<?php

class KPI_Values_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'kpi_valores';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $kpi_valores_table = $this->db->dbprefix('kpi_valores');
		$clientes_table = $this->db->dbprefix('clients');
		$fases_table = $this->db->dbprefix('fases');
		$proyectos_table = $this->db->dbprefix('projects');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $kpi_valores_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $kpi_valores_table.id_cliente = $id_cliente";
        }
		
		$id_fase = get_array_value($options, "id_fase");
        if ($id_fase) {
            $where .= " AND $kpi_valores_table.id_fase = $id_fase";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $kpi_valores_table.id_proyecto = $id_proyecto";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $kpi_valores_table.id, $kpi_valores_table.nombre_valor, $clientes_table.company_name AS nombre_cliente,";
		$sql .= " $fases_table.nombre AS nombre_fase, $proyectos_table.title AS nombre_proyecto, $kpi_valores_table.id_fase,";
		$sql .= " $kpi_valores_table.id_cliente, $kpi_valores_table.id_proyecto, $kpi_valores_table.created_by,";
		$sql .= " $kpi_valores_table.id_campo_unidad, $kpi_valores_table.tipo_valor, $kpi_valores_table.id_tipo_formulario,";
		$sql .= " $kpi_valores_table.id_formulario, $kpi_valores_table.created, $kpi_valores_table.modified,";
		$sql .= " $kpi_valores_table.operador, $kpi_valores_table.valor_operador, $kpi_valores_table.valor_inicial,";
		$sql .= " $kpi_valores_table.operacion_compuesta, $kpi_valores_table.id_tipo_unidad, $kpi_valores_table.id_unidad";
		$sql .= " FROM $kpi_valores_table, $clientes_table, $fases_table, $proyectos_table";
		$sql .= " WHERE $kpi_valores_table.id_cliente = $clientes_table.id";
		$sql .= " AND $kpi_valores_table.id_fase = $fases_table.id";
		$sql .= " AND $kpi_valores_table.id_proyecto = $proyectos_table.id";
		$sql .= " AND $kpi_valores_table.deleted = 0";
		$sql .= " AND $clientes_table.deleted = 0";
		$sql .= " AND $fases_table.deleted = 0";
		$sql .= " AND $proyectos_table.deleted = 0";
		$sql .= " $where";
	
        return $this->db->query($sql);
		
    }


}
