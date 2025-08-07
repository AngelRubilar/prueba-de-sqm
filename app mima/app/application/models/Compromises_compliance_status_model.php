<?php

class Compromises_compliance_status_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'estados_cumplimiento_compromisos';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $estados_cumplimiento_compromisos_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $estados_cumplimiento_compromisos_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $estados_cumplimiento_compromisos_table.id_cliente = $id_cliente";
        }
		
		$nombre_estado = get_array_value($options, "nombre_estado");
        if ($nombre_estado) {
            $where .= " AND $estados_cumplimiento_compromisos_table.nombre_estado = '$nombre_estado'";
        }
		
		$tipo_evaluacion = get_array_value($options, "tipo_evaluacion");
        if ($tipo_evaluacion) {
            $where .= " AND $estados_cumplimiento_compromisos_table.tipo_evaluacion = '$tipo_evaluacion'";
        }
		
		$categoria = get_array_value($options, "categoria");
        if ($categoria) {
            $where .= " AND $estados_cumplimiento_compromisos_table.categoria = '$categoria'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $estados_cumplimiento_compromisos_table.created_by = $created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $estados_cumplimiento_compromisos_table.* FROM $estados_cumplimiento_compromisos_table WHERE";
		$sql .= " $estados_cumplimiento_compromisos_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }
	
	function delete_compromises_compliance_status($id){
		
		$estados_cumplimiento_compromisos = $this->db->dbprefix('estados_cumplimiento_compromisos');
		
        $sql = "UPDATE $estados_cumplimiento_compromisos SET $estados_cumplimiento_compromisos.deleted=1 WHERE $estados_cumplimiento_compromisos.id=$id; ";
        $this->db->query($sql);

	}

}
