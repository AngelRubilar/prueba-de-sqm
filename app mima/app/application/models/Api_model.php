<?php

class Api_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'users_api_session';
        parent::__construct($this->table);
    }

    // RECIBE EL ID DE USUARIO CLIENTE POR PARAMETRO Y RETORNA LOS DATOS DE LOS PROYECTOS EN LOS QUE ES MIEMBRO
    function get_projects_of_member($id_usuario = NULL) {
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
        $projects_members_table = $this->db->dbprefix('project_members');
        $users_table = $this->db->dbprefix('users');
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $projects_table.* 
        FROM $projects_table 
        LEFT JOIN $projects_members_table ON $projects_table.id = $projects_members_table.project_id 
        LEFT JOIN $users_table ON $projects_members_table.user_id = $users_table.id 
        WHERE $projects_members_table.user_id = $id_usuario AND 
        $projects_table.deleted = 0 AND 
        $projects_members_table.deleted = 0 AND 
        $users_table.deleted = 0
		ORDER BY $projects_table.id ";

        //echo $sql;
        return $this->db->query($sql);
    }

    function get_values_of_form($options = array(), $page = 1) {
        $valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		
		$end_page = ($page * 5);
		$start_page = $end_page - 5;
		$limit = "LIMIT $start_page, $end_page";

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $valores_formularios_table.id=$id";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $formulario_rel_proyecto_table.id_formulario=$id_formulario";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto=$id_proyecto";
        }
		
		$datos = get_array_value($options, "datos");
        if ($datos) {
            $where .= " AND $valores_formularios_table.datos='$datos'";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $valores_formularios_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $valores_formularios_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $formulario_rel_proyecto_table.id_formulario, $formulario_rel_proyecto_table.id_proyecto, $valores_formularios_table.* FROM $valores_formularios_table, $formulario_rel_proyecto_table WHERE";
		$sql .= " $valores_formularios_table.deleted=0 AND $formulario_rel_proyecto_table.deleted=0";
		$sql .= " AND $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
        $sql .= " $where";
        $sql .= " ORDER BY $valores_formularios_table.id DESC $limit";
		
        return $this->db->query($sql);
    }
	
	public function user_exist_in_api($email){
		$sql .= "SELECT api.user_id , api.login_date";
		$sql .= "FROM `users` u, users_api_session api";
		$sql .= "WHERE email='$email'";
		$sql .= "AND api.user_id = u.id";
		return $this->db->query($sql)-result();
	}

}
