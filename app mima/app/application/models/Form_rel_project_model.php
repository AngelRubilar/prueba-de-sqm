<?php

class Form_rel_project_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'formulario_rel_proyecto';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		$formularios_table = $this->db->dbprefix('formularios');
		$tipo_formulario_table = $this->db->dbprefix('tipo_formulario');
		$users_table = $this->db->dbprefix('users');
        $form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $form_rel_project_table.id=$id";
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
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $form_rel_project_table.id_proyecto=$id_proyecto";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $form_rel_project_table.id_formulario=$id_formulario";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $form_rel_project_table.*, $formularios_table.id_tipo_formulario, $formularios_table.id_cliente, $formularios_table.numero, $formularios_table.nombre, $formularios_table.descripcion, $formularios_table.codigo, $formularios_table.flujo, $formularios_table.unidad, $formularios_table.icono, $tipo_formulario_table.nombre AS tipo_formulario, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS creado_por, $form_rel_project_table.id_proyecto AS proyecto FROM $formularios_table, $tipo_formulario_table, $users_table, $form_rel_project_table WHERE";
		//$sql = "SELECT $form_rel_project_table.*, $formularios_table.*, $tipo_formulario_table.nombre AS tipo_formulario, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS creado_por, $form_rel_project_table.id_proyecto AS proyecto FROM $formularios_table, $tipo_formulario_table, $users_table, $form_rel_project_table WHERE";
		$sql .= " $formularios_table.deleted=0";
		$sql .= " AND $tipo_formulario_table.id = $formularios_table.id_tipo_formulario";
		$sql .= " AND $users_table.id = $formularios_table.created_by";
		$sql .= " AND $form_rel_project_table.id_formulario = $formularios_table.id";
		$sql .= " $where";
        return $this->db->query($sql);
    }

    function get_primary_contact($contact_id = 0) {
        $users_table = $this->db->dbprefix('users');

        $sql = "SELECT $users_table.id
        FROM $users_table
        WHERE $users_table.deleted=0 AND $users_table.client_id=$contact_id AND $users_table.is_primary_contact=1";
        $result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->id;
        }
    }
	
	function delte_form_rel_project_model($id){
		
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
		
        $sql = "UPDATE $formulario_rel_proyecto SET $formulario_rel_proyecto.deleted=1 WHERE $formulario_rel_proyecto.id=$id; ";
        $this->db->query($sql);
	}
	

}
