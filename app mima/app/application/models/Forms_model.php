<?php

class Forms_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'formularios';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $formularios_table = $this->db->dbprefix('formularios');
		$tipo_formulario_table = $this->db->dbprefix('tipo_formulario');
		$users_table = $this->db->dbprefix('users');
		$project_rel_form_table = $this->db->dbprefix('formulario_rel_proyecto');

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
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $formularios_table.id_cliente=$id_cliente";
        }
		
		$flujo = get_array_value($options, "flujo");
        if ($flujo) {
            $where .= " AND $formularios_table.flujo='$flujo'";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $project_rel_form_table.id_proyecto=$id_proyecto";
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
        $sql = "SELECT $project_rel_form_table.id_proyecto, $formularios_table.*, $tipo_formulario_table.nombre AS tipo_formulario";
		$sql .= " FROM $formularios_table, $tipo_formulario_table, $project_rel_form_table";
		$sql .= " WHERE $formularios_table.deleted = 0";
		$sql .= " AND $project_rel_form_table.deleted = 0";
		$sql .= " AND $tipo_formulario_table.deleted = 0";
		$sql .= " AND $tipo_formulario_table.id = $formularios_table.id_tipo_formulario";
		$sql .= " AND $project_rel_form_table.id_formulario = $formularios_table.id";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	function get_forms_of_project($options = array()) {
        $formularios_table = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $formularios_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $formularios_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario=$id_tipo_formulario";
        }
		
		$flujo = get_array_value($options, "flujo");
        if ($flujo) {
            $where .= " AND $formularios_table.flujo='$flujo'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $formulario_rel_proyecto.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $formulario_rel_proyecto.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formularios_table.* FROM $formulario_rel_proyecto_table, $formularios_table WHERE";
		$sql .= " $formularios_table.deleted=0 AND $formulario_rel_proyecto_table.deleted=0";
		$sql .= " AND $formularios_table.id = $formulario_rel_proyecto_table.id_formulario";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	function get_fields_of_form_json($id_form) {
		
		$campo_rel_formulario_table = $this->db->dbprefix('campo_rel_formulario');
		$campos_table = $this->db->dbprefix('campos');

        $sql = "SELECT $campos_table.*
                FROM $campo_rel_formulario_table, $campos_table
                WHERE $campos_table.id = $campo_rel_formulario_table.id_campo AND $campo_rel_formulario_table.id_formulario = $id_form AND $campos_table.deleted=0     
                ORDER BY $campo_rel_formulario_table.id ASC";

        $fields_for_table = $this->db->query($sql)->result();

        $json_string = "";
        foreach ($fields_for_table as $column) {
            $json_string .= ',' . '{"title":"' . $column->nombre . '"}';
        }

        return $json_string;
    }
	
	function get_fields_of_form($id_form) {
		
		$campo_rel_formulario_table = $this->db->dbprefix('campo_rel_formulario');
		$campos_table = $this->db->dbprefix('campos');

        $sql = "SELECT $campos_table.*
                FROM $campo_rel_formulario_table, $campos_table
                WHERE $campos_table.id = $campo_rel_formulario_table.id_campo AND $campo_rel_formulario_table.id_formulario = $id_form AND $campos_table.deleted=0     
                ORDER BY $campo_rel_formulario_table.id ASC";

        return $this->db->query($sql);
    }
	
	function is_code_exists($code, $id = 0) {
        $result = $this->get_all_where(array("codigo" => $code, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }
	
	function delete_form($id){
		
		$formularios = $this->db->dbprefix('formularios');
		
        $sql = "UPDATE $formularios SET $formularios.deleted=1 WHERE $formularios.id=$id; ";
        $this->db->query($sql);
	}
	
	function get_details_formularios_fijos($options = array()){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$tipo_formulario_table = $this->db->dbprefix('tipo_formulario');
		$users_table = $this->db->dbprefix('users');
		$campo_fijo_rel_formulario_rel_proyecto_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');
		
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $formularios_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $formularios_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $campo_fijo_rel_formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario=$id_tipo_formulario";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formularios_table.*, $tipo_formulario_table.nombre AS tipo_formulario";
		$sql .= " FROM $formularios_table, $tipo_formulario_table, $campo_fijo_rel_formulario_rel_proyecto_table";
		$sql .= " WHERE $formularios_table.deleted = 0";
		$sql .= " AND $tipo_formulario_table.deleted = 0";
		$sql .= " AND $tipo_formulario_table.id = $formularios_table.id_tipo_formulario";
		$sql .= " AND $campo_fijo_rel_formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
		$sql .= " AND $formularios_table.fijo = 1";
		$sql .= " $where";
		$sql .= " GROUP BY $campo_fijo_rel_formulario_rel_proyecto_table.id_formulario";
		
		//echo $sql;exit();
		
		return $this->db->query($sql);
		
	}
	
	function get_fixed_forms_of_project($options = array()){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$fixed_field_rel_form_rel_project_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');
		
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $formularios_table.id=$id";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $fixed_field_rel_form_rel_project_table.id_proyecto=$id_proyecto";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario=$id_tipo_formulario";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $formularios_table.*";
		$sql .= " FROM $fixed_field_rel_form_rel_project_table, $formularios_table";
		$sql .= " WHERE $formularios_table.deleted = 0";
		$sql .= " AND $fixed_field_rel_form_rel_project_table.deleted = 0";
		$sql .= " AND $formularios_table.id = $fixed_field_rel_form_rel_project_table.id_formulario";
		$sql .= " AND $formularios_table.fijo = 1";
		$sql .= " $where";
		$sql .= " GROUP BY $formularios_table.id";
        
		return $this->db->query($sql);
		
	}

}
