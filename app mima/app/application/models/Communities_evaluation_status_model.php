<?php

class Communities_evaluation_status_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'estados_evaluacion_comunidades';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $estados_evaluacion_comunidades_table = $this->db->dbprefix('estados_evaluacion_comunidades');
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $estados_evaluacion_comunidades_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $estados_evaluacion_comunidades_table.id_cliente = $id_cliente";
        }
		
		$nombre_estado = get_array_value($options, "nombre_estado");
        if ($nombre_estado) {
            $where .= " AND $estados_evaluacion_comunidades_table.nombre_estado =$nombre_estado";
        }
		
		$categoria = get_array_value($options, "categoria");
        if ($categoria) {
            $where .= " AND $estados_evaluacion_comunidades_table.categoria = '$categoria'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $estados_evaluacion_comunidades_table.created_by = $created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $estados_evaluacion_comunidades_table.* FROM $estados_evaluacion_comunidades_table WHERE";
		$sql .= " $estados_evaluacion_comunidades_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }
	
	function get_client_agreements_status_by_type($id_cliente, $tipo_estado, $id_agreement_matrix_config){
		
		$estados_evaluacion_comunidades_table = $this->db->dbprefix('estados_evaluacion_comunidades');
		$evaluaciones_acuerdos_table = $this->db->dbprefix('evaluaciones_acuerdos');
		$valores_acuerdos_table = $this->db->dbprefix('valores_acuerdos');
		$agreements_matrix_config_table = $this->db->dbprefix('agreements_matrix_config');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $estados_evaluacion_comunidades_table.nombre_estado, $estados_evaluacion_comunidades_table.categoria, $estados_evaluacion_comunidades_table.color, COUNT(*) as cantidad";
		$sql .= " FROM $estados_evaluacion_comunidades_table, $evaluaciones_acuerdos_table, $valores_acuerdos_table, $agreements_matrix_config_table";
		$sql .= " WHERE $estados_evaluacion_comunidades_table.id = $evaluaciones_acuerdos_table.$tipo_estado";
		$sql .= " AND $evaluaciones_acuerdos_table.id_valor_acuerdo = $valores_acuerdos_table.id";
		$sql .= " AND $valores_acuerdos_table.id_agreement_matrix_config= $agreements_matrix_config_table.id";
		//$sql .= " WHERE $estados_evaluacion_comunidades_table.categoria = '$tipo_estado'";
		$sql .= " AND $estados_evaluacion_comunidades_table.id_cliente = $id_cliente";
		$sql .= " AND $estados_evaluacion_comunidades_table.deleted = 0";
		$sql .= " AND $agreements_matrix_config_table.id = $id_agreement_matrix_config";
		$sql .= " GROUP BY $estados_evaluacion_comunidades_table.id";
					
		return $this->db->query($sql);

	}

}
