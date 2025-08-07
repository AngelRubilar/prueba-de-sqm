<?php

class Rule_model extends Crud_model {

    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'criterios';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $rules_table = $this->db->dbprefix('criterios');
        $clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
        $forms_table = $this->db->dbprefix('formularios');
		$materials_db = getFCBD();
		//$materials_db = 'dev_mimasoft_fc';
		$materials_table = $this->load->database(getFCBD(), TRUE)->dbprefix('materiales');
		$fields_table = $this->db->dbprefix('campos');
		
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $rules_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $rules_table.id_cliente=$id_cliente";
        }
		
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $rules_table.id_proyecto=$id_proyecto";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $where .= " AND $rules_table.id_material=$id_material";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT result3.*, $fields_table.nombre AS campo_fc FROM (SELECT result2.*, $fields_table.nombre AS campo_pu FROM (SELECT result1.*, $fields_table.nombre AS campo_sp FROM (SELECT $rules_table.*, $clients_table.company_name, $projects_table.title, $forms_table.nombre AS formulario, $materials_table.nombre AS material 
		FROM $rules_table, $clients_table, $projects_table, $forms_table, $materials_db.$materials_table 
		WHERE $clients_table.id = $rules_table.id_cliente AND $projects_table.id = $rules_table.id_proyecto AND $forms_table.id = $rules_table.id_formulario AND $materials_table.id = $rules_table.id_material AND 
		$rules_table.deleted = 0 AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $forms_table.deleted = 0 $where) as result1 
		LEFT JOIN $fields_table ON result1.id_campo_sp = $fields_table.id) AS result2 
		LEFT JOIN $fields_table ON result2.id_campo_pu = $fields_table.id) AS result3 
		LEFT JOIN $fields_table ON result3.id_campo_fc = $fields_table.id"; 
		//var_dump($sql);
		
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
	
	function delete_rule($id){
		
		$criterios = $this->db->dbprefix('criterios');
        $sql = "UPDATE $criterios SET $criterios.deleted=1 WHERE $criterios.id=$id; ";
        $this->db->query($sql);
		
	}
	
	function is_field_used_in_rule($field_id){
		
		$criterios = $this->db->dbprefix('criterios');
		
		$sql = "SELECT $criterios.*";
		$sql .= " FROM $criterios";
		$sql .= " WHERE ($criterios.id_campo_sp = $field_id OR $criterios.id_campo_pu = $field_id OR $criterios.id_campo_fc = $field_id)";
		$sql .= " AND $criterios.deleted = 0";
		
		$result = $this->db->query($sql);
		
		if ($result->num_rows()) {
            return TRUE;
        } else {
			return FALSE;
		}
		
	}
	
	
}
