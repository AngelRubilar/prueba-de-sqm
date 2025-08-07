<?php
class Form_values_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_formularios';
        parent::__construct($this->table);
    }

    function get_details($options = array(), $order = array()) {
        $form_values_table = $this->db->dbprefix('valores_formularios');
		$forms_table = $this->db->dbprefix('formularios');
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $form_values_table.id=$id";
        }
		
		$id_formulario_rel_proyecto = get_array_value($options, "id_formulario_rel_proyecto");
        if ($id_formulario_rel_proyecto) {
            $where .= " AND $form_rel_project_table.id=$id_formulario_rel_proyecto";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $form_rel_project_table.id_proyecto=$id_proyecto";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $forms_table.id=$id_formulario";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $forms_table.id_tipo_formulario=$id_tipo_formulario";
        }
		
		$id_categoria = get_array_value($options, "id_categoria");
        if ($id_categoria) {
			$where .= " AND $form_values_table.datos LIKE '%\"id_categoria\":\"$id_categoria\"%'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $form_values_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $form_values_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $form_values_table.* FROM $form_rel_project_table, $forms_table, $form_values_table WHERE";
		$sql .= " $form_values_table.id_formulario_rel_proyecto = $form_rel_project_table.id AND";
		$sql .= " $forms_table.id = $form_rel_project_table.id_formulario AND";
		$sql .= " $form_rel_project_table.deleted = 0 AND $forms_table.deleted = 0 AND $form_values_table.deleted = 0";
		$sql .= " $where";
		$sql .= " ORDER BY $form_values_table.created DESC";
		
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
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if ($id_tipo_formulario) {
            $where .= " AND $formularios_table.id_tipo_formulario=$id_tipo_formulario";
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
	
	
	function get_forms_values_of_forms_by_flux($project_id,$flux){
		
		$valores_formularios = $this->db->dbprefix('valores_formularios');
		$projects = $this->db->dbprefix('projects');
		$formularios = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $valores_formularios.*";
		$sql .=" FROM $valores_formularios,$projects,$formularios,$formulario_rel_proyecto";
		$sql .=" WHERE $formularios.flujo = '$flux'";
		$sql .=" AND $projects.id = $project_id";
		$sql .=" AND $valores_formularios.id_formulario_rel_proyecto = $formulario_rel_proyecto.id";
		$sql .=" AND $formulario_rel_proyecto.id_formulario = $formularios.id";
		$sql .=" AND $formulario_rel_proyecto.id_proyecto = $projects.id";
		$sql .=" AND $valores_formularios.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	/*
	function get_forms_values_of_form($project_id,$id_form){
		
		$valores_formularios = $this->db->dbprefix('valores_formularios');
		$projects = $this->db->dbprefix('projects');
		$formularios = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $valores_formularios.*";
		$sql .=" FROM $valores_formularios,$projects,$formularios,$formulario_rel_proyecto";
		$sql .=" WHERE $formularios.id = '$id_form'";
		$sql .=" AND $projects.id = $project_id";
		$sql .=" AND $valores_formularios.id_formulario_rel_proyecto = $formulario_rel_proyecto.id";
		$sql .=" AND $formulario_rel_proyecto.id_formulario = $formularios.id";
		$sql .=" AND $formulario_rel_proyecto.id_proyecto = $projects.id";
		$sql .=" AND $valores_formularios.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	*/
	function get_forms_values_of_form($form_id){
		
		$valores_formularios = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $valores_formularios.*";
		$sql .=" FROM $formulario_rel_proyecto";
		$sql .=" LEFT JOIN $valores_formularios ON $formulario_rel_proyecto.id = $valores_formularios.id_formulario_rel_proyecto";
		$sql .=" WHERE $formulario_rel_proyecto.id_formulario = $form_id AND $valores_formularios.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function delete_form_value($form_value_id){
		
		$valores_formularios = $this->db->dbprefix('valores_formularios');
		
        $sql = "UPDATE $valores_formularios SET $valores_formularios.deleted=1 WHERE $valores_formularios.id=$form_value_id; ";
        $this->db->query($sql);
		
	}
	
	
	/*
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
	
	
    function add_remove_star($project_id, $user_id, $type = "add") {
        $clients_table = $this->db->dbprefix('clients');

        $action = " CONCAT($clients_table.starred_by,',',':$user_id:') ";
        $where = " AND FIND_IN_SET(':$user_id:',$clients_table.starred_by) = 0"; //don't add duplicate

        if ($type != "add") {
            $action = " REPLACE($clients_table.starred_by, ',:$user_id:', '') ";
            $where = "";
        }

        $sql = "UPDATE $clients_table SET $clients_table.starred_by = $action
        WHERE $clients_table.id=$project_id $where";
        return $this->db->query($sql);
    }

    function get_starred_clients($user_id) {
        $clients_table = $this->db->dbprefix('clients');

        $sql = "SELECT $clients_table.id,  $clients_table.company_name
        FROM $clients_table
        WHERE $clients_table.deleted=0 AND FIND_IN_SET(':$user_id:',$clients_table.starred_by)
        ORDER BY $clients_table.company_name ASC";
        return $this->db->query($sql);
    }
	*/

    function get_last_value_of_form($options = array()){

        $valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

        $id_form = get_array_value($options, "id_form");

        $sql = "SELECT $valores_formularios_table.*";
		$sql .=" FROM $formulario_rel_proyecto_table";
		$sql .=" LEFT JOIN $valores_formularios_table ON $formulario_rel_proyecto_table.id = $valores_formularios_table.id_formulario_rel_proyecto";
		$sql .=" WHERE $formulario_rel_proyecto_table.id_formulario = $id_form AND $valores_formularios_table.deleted = 0";
        $sql .= " $where";
        $sql .= " ORDER BY $valores_formularios_table.id DESC LIMIT 1";
		
		return $this->db->query($sql);

    }

}
