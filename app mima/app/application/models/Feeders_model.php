<?php

class Feeders_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'formularios';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $formularios_table = $this->db->dbprefix('formularios');
		$tipo_formulario_table = $this->db->dbprefix('tipo_formulario');
		$users_table = $this->db->dbprefix('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $formularios_table.id=$id";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario=$id_tipo_formulario";
        }
				
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $formularios_table.nombre='$nombre'";
        }
		
		$descripcion = get_array_value($options, "descripcion");
        if ($descripcion) {
            $where .= " AND $formularios_table.descripcion='$descripcion'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $formularios_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $formularios_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formularios_table.*, $tipo_formulario_table.nombre AS tipo_formulario,
		  CONCAT($users_table.first_name, ' ', $users_table.last_name) AS creado_por FROM $formularios_table, $tipo_formulario_table, 					          $users_table WHERE";
		$sql .= " $formularios_table.deleted=0";
		$sql .= " AND $tipo_formulario_table.id = $formularios_table.id_tipo_formulario";
		$sql .= " AND $users_table.id = $formularios_table.created_by";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	function get_values_of_record($id_form) {
		
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');

        $sql = "SELECT $valores_formularios_table.*
                FROM $formulario_rel_proyecto_table, $valores_formularios_table
                WHERE $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id AND $formulario_rel_proyecto_table.id_formulario = $id_form AND $formulario_rel_proyecto_table.deleted=0 AND 
				$valores_formularios_table.deleted=0 AND $formulario_rel_proyecto_table.deleted=0
                ORDER BY $valores_formularios_table.id ASC";
				
        return $this->db->query($sql);
    }
	
}
