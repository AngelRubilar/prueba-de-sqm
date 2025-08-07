<?php

class Field_rel_form_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'campo_rel_formulario';
        parent::__construct($this->table);
    }
	
	function delete_fields_related_to_form($form_id){
		
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$sql = "DELETE FROM $field_rel_form_table WHERE";
		$sql .= " $field_rel_form_table.id_formulario = $form_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function get_fields_related_to_form($form_id){
		
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $field_rel_form_table.* FROM $field_rel_form_table WHERE";
		$sql.= " $field_rel_form_table.id_formulario = $form_id";
		
		return $this->db->query($sql);
	}
	
	function get_fields_details_related_to_form($form_id){
		
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $fields_table.id, $fields_table.nombre, $fields_table.id_tipo_campo";
		$sql.= " FROM $fields_table, $field_rel_form_table WHERE";
		$sql.= " $fields_table.deleted = 0 AND $field_rel_form_table.deleted = 0 AND";
		$sql.= " $fields_table.id = $field_rel_form_table.id_campo AND $field_rel_form_table.id_formulario = $form_id";
		
		return $this->db->query($sql);
	}
	
	function get_fields_related_to_form_with_options($options = array()){
		
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
		$field_type_table = $this->db->dbprefix('tipo_campo');
		
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $fields_table.id=$id";
        }
		
		$tipo_campo = get_array_value($options, "nombre");
        if ($tipo_campo) {
            $where .= " AND $field_type_table.nombre='$tipo_campo'";
        }
				
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $field_rel_form_table.id_formulario=$id_formulario";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $fields_table.* FROM $field_rel_form_table, $fields_table, $field_type_table WHERE";
		$sql.= " $fields_table.id = $field_rel_form_table.id_campo AND $field_type_table.id = $fields_table.id_tipo_campo";
		$sql.= " AND $field_rel_form_table.deleted = 0 AND $fields_table.deleted = 0";
		$sql.= " $where";
		
		return $this->db->query($sql);
	}
	
	/* Retorna los campos de tipo selección y selección desde mantenedora de un formulario*/
	function get_fields_for_environmental_record_rule($id_formulario){
		
		// id 6  -> Selección
		// id 16 -> Selección desde Mantenedora
		// id 9  -> Radio Buttons
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
		$field_type_table = $this->db->dbprefix('tipo_campo');		
		
		$where = "";
		if ($id_formulario) {
            $where .= " AND $field_rel_form_table.id_formulario = $id_formulario";
        }

		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $fields_table.* FROM $field_rel_form_table, $fields_table, $field_type_table WHERE";
		$sql.= " $fields_table.id = $field_rel_form_table.id_campo AND $field_type_table.id = $fields_table.id_tipo_campo";
		$sql.= " AND $field_rel_form_table.deleted = 0 AND $fields_table.deleted = 0";
		$sql.= " AND $fields_table.id_tipo_campo in (6, 16, 9)";
		$sql.= " $where";
		
		return $this->db->query($sql);
		
	}
	
}
