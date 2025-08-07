<?php

class Permitting_procedure_status_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'estados_tramitacion_permisos';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()) {
		
        $estados_tramitacion_permisos_table = $this->db->dbprefix('estados_tramitacion_permisos');
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $estados_tramitacion_permisos_table.id = $id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $estados_tramitacion_permisos_table.id_cliente = $id_cliente";
        }
		
		$nombre_estado = get_array_value($options, "nombre_estado");
        if ($nombre_estado) {
            $where .= " AND $estados_tramitacion_permisos_table.nombre_estado =$nombre_estado";
        }
		
		$categoria = get_array_value($options, "categoria");
        if ($categoria) {
            $where .= " AND $estados_tramitacion_permisos_table.categoria = '$categoria'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $estados_tramitacion_permisos_table.created_by = $created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $estados_tramitacion_permisos_table.* FROM $estados_tramitacion_permisos_table WHERE";
		$sql .= " $estados_tramitacion_permisos_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }
	
	function delete_permitting_procedure_status($id){
		
		$estados_tramitacion_permisos = $this->db->dbprefix('estados_tramitacion_permisos');

		$sql = "UPDATE $estados_tramitacion_permisos SET $estados_tramitacion_permisos.deleted=1 WHERE $estados_tramitacion_permisos.id=$id; ";
		$this->db->query($sql);
		
	}

}
