<?php

class Fixed_fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'campos_fijos';
        parent::__construct($this->table);
    }
	
	function get_fixed_fields($options = array()){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$fixed_field_rel_form_rel_project_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');
		$fixed_fields_table = $this->db->dbprefix('campos_fijos');
		
		$where = "";
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $formularios_table.id_cliente = $id_cliente ";
        }
		
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $fixed_field_rel_form_rel_project_table.id_proyecto = $id_proyecto ";
        }
		
		$codigo_formulario_fijo = get_array_value($options, "codigo_formulario_fijo");
        if ($codigo_formulario_fijo) {
            $where .= " AND $formularios_table.codigo_formulario_fijo = '$codigo_formulario_fijo' ";
        }
		
		$nombre_campo = get_array_value($options, "nombre_campo");
        if ($nombre_campo) {
            $where .= " AND $fixed_fields_table.nombre = '$nombre_campo' ";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $formularios_table.id AS id_formulario, $fixed_fields_table.*";
		$sql .= " FROM $fixed_fields_table, $fixed_field_rel_form_rel_project_table, $formularios_table";
		$sql .= " WHERE";
		$sql .= " $fixed_fields_table.deleted = 0 AND";
		$sql .= " $fixed_fields_table.id = $fixed_field_rel_form_rel_project_table.id_campo_fijo AND";
		$sql .= " $fixed_field_rel_form_rel_project_table.deleted = 0 AND";
		$sql .= " $formularios_table.deleted = 0 AND";
		$sql .= " $formularios_table.id = $fixed_field_rel_form_rel_project_table.id_formulario AND";
		$sql .= " $formularios_table.fijo = 1";
		$sql .= " $where";
		
		return $this->db->query($sql);
		
	}

}
