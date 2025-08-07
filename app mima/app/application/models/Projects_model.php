<?php

class Projects_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'projects';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $projects_table = $this->db->dbprefix('projects');
        $project_members_table = $this->db->dbprefix('project_members');
        $clients_table = $this->db->dbprefix('clients');
		
        $tasks_table = $this->db->dbprefix('tasks');
        $where = "";

        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $projects_table.id=$id";
        }

        $client_id = get_array_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $projects_table.client_id=$client_id";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $projects_table.status='$status'";
        }
		
        $project_label = get_array_value($options, "project_label");
        if ($project_label) {
            $where .= " AND (FIND_IN_SET('$project_label', $projects_table.labels)) ";
        }
        
        
        $deadline = get_array_value($options, "deadline");
        if ($deadline) {
            $now = get_my_local_time("Y-m-d");
            if ($deadline === "expired") {
                $where .= " AND ($projects_table.deadline !='0000-00-00' AND $projects_table.deadline<'$now')";
            } else {
                $where .= " AND ($projects_table.deadline !='0000-00-00' AND $projects_table.deadline<='$deadline')";
            }
        }
        

        $extra_join = "";
        $extra_where = "";
        $user_id = get_array_value($options, "user_id");

        if (!$client_id && $user_id) {
            $extra_join = " LEFT JOIN (SELECT $project_members_table.user_id, $project_members_table.project_id FROM $project_members_table WHERE $project_members_table.user_id=$user_id AND $project_members_table.deleted=0 GROUP BY $project_members_table.project_id) AS project_members_table ON project_members_table.project_id= $projects_table.id ";
            $extra_where = " AND project_members_table.user_id=$user_id";
        }
               
        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_query_info = $this->prepare_custom_field_query_string("projects", $custom_fields, $projects_table);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");

        
        $sql = "SELECT $projects_table.*, $clients_table.company_name, total_points_table.total_points, completed_points_table.completed_points $select_custom_fieds
        FROM $projects_table
        LEFT JOIN $clients_table ON $clients_table.id= $projects_table.client_id
        LEFT JOIN (SELECT project_id, SUM(points) AS total_points FROM $tasks_table WHERE deleted=0 GROUP BY project_id) AS  total_points_table ON total_points_table.project_id= $projects_table.id
        LEFT JOIN (SELECT project_id, SUM(points) AS completed_points FROM $tasks_table WHERE deleted=0 AND status='done' GROUP BY project_id) AS  completed_points_table ON completed_points_table.project_id= $projects_table.id
        $extra_join   
        $join_custom_fieds    
        WHERE $projects_table.deleted=0 $where $extra_where
        ORDER BY $projects_table.id ASC";
        return $this->db->query($sql);
    }
	
	function get_projects_of_member($id_user = 0, $id_cliente = 0) {
        $projects_table = $this->db->dbprefix('projects');
        $project_members_table = $this->db->dbprefix('project_members');
		$clients_table = $this->db->dbprefix('clients');
		$techs_table = $this->db->dbprefix('subrubros');
		
        $sql = "SELECT $projects_table.*, $techs_table.nombre AS tecnologia FROM $projects_table, $project_members_table, $techs_table 
		WHERE $project_members_table.deleted=0 
		AND $projects_table.deleted=0 
		AND $project_members_table.user_id=$id_user 
		AND $projects_table.client_id=$id_cliente 
		AND $techs_table.id=$projects_table.id_tecnologia 
		AND $projects_table.id=$project_members_table.project_id 
        ORDER BY $projects_table.id ASC";
        return $this->db->query($sql);
    }
	

    function get_label_suggestions() {
        $projects_table = $this->db->dbprefix('projects');
        $sql = "SELECT GROUP_CONCAT(labels) as label_groups
        FROM $projects_table
        WHERE $projects_table.deleted=0";
        return $this->db->query($sql)->row()->label_groups;
    }

    function count_project_status($options = array()) {
        $projects_table = $this->db->dbprefix('projects');
        $project_members_table = $this->db->dbprefix('project_members');

        $extra_join = "";
        $extra_where = "";
        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $extra_join = " LEFT JOIN (SELECT $project_members_table.user_id, $project_members_table.project_id FROM $project_members_table WHERE $project_members_table.user_id=$user_id AND $project_members_table.deleted=0 GROUP BY $project_members_table.project_id) AS project_members_table ON project_members_table.project_id= $projects_table.id ";
            $extra_where = " AND project_members_table.user_id=$user_id";
        }

        $sql = "SELECT $projects_table.status, COUNT($projects_table.id) as total
        FROM $projects_table
              $extra_join    
        WHERE $projects_table.deleted=0 AND ($projects_table.status='open' OR  $projects_table.status='completed') $extra_where
        GROUP BY $projects_table.status";
        $result = $this->db->query($sql)->result();

        $info = new stdClass();
        $info->open = 0;
        $info->completed = 0;
        foreach ($result as $value) {
            $status = $value->status;
            $info->$status = $value->total;
        }
        return $info;
    }


    function delete_project_and_sub_items($project_id) {
        $projects_table = $this->db->dbprefix('projects');
        $tasks_table = $this->db->dbprefix('tasks');
        $milestones_table = $this->db->dbprefix('milestones');
        $project_files_table = $this->db->dbprefix('project_files');
        $project_comments_table = $this->db->dbprefix('project_comments');
        $activity_logs_table = $this->db->dbprefix('activity_logs');
        $notifications_table = $this->db->dbprefix('notifications');

        //get project files info to delete the files from directory 
        $project_files_sql = "SELECT * FROM $project_files_table WHERE $project_files_table.deleted=0 AND $project_files_table.project_id=$project_id; ";
        $project_files = $this->db->query($project_files_sql)->result();

        //get project comments info to delete the files from directory 
        $project_comments_sql = "SELECT * FROM $project_comments_table WHERE $project_comments_table.deleted=0 AND $project_comments_table.project_id=$project_id; ";
        $project_comments = $this->db->query($project_comments_sql)->result();

        //delete the project and sub items
        $delete_project_sql = "UPDATE $projects_table SET $projects_table.deleted=1 WHERE $projects_table.id=$project_id; ";
        $this->db->query($delete_project_sql);
		
		//OBSERVACIÓN: La tabla tasks se encuentra vacia, al parecer no esta en uso.
        $delete_tasks_sql = "UPDATE $tasks_table SET $tasks_table.deleted=1 WHERE $tasks_table.project_id=$project_id; ";
        $this->db->query($delete_tasks_sql);
		
		//OBSERVACIÓN: La tabla milestones se encuentra vacia, al parecer no esta en uso.
        $delete_milestones_sql = "UPDATE $milestones_table SET $milestones_table.deleted=1 WHERE $milestones_table.project_id=$project_id; ";
        $this->db->query($delete_milestones_sql);

		//OBSERVACIÓN: La tabla project_files se encuentra vacia, al parecer no esta en uso.
        $delete_files_sql = "UPDATE $project_files_table SET $project_files_table.deleted=1 WHERE $project_files_table.project_id=$project_id; ";
        $this->db->query($delete_files_sql);

		//OBSERVACIÓN: La tabla project_comments se encuentra vacia, al parecer no esta en uso.
        $delete_comments_sql = "UPDATE $project_comments_table SET $project_comments_table.deleted=1 WHERE $project_comments_table.project_id=$project_id; ";
        $this->db->query($delete_comments_sql);

		//OBSERVACIÓN: La tabla activity_logs se encuentra vacia, al parecer no esta en uso.
        $delete_activity_logs_sql = "UPDATE $activity_logs_table SET $activity_logs_table.deleted=1 WHERE $activity_logs_table.log_for='project' AND $activity_logs_table.log_for_id=$project_id; ";
        $this->db->query($delete_activity_logs_sql);

		//OBSERVACIÓN: La tabla notifications se encuentra vacia, al parecer no esta en uso.
        $delete_notifications_sql = "UPDATE $notifications_table SET $notifications_table.deleted=1 WHERE $notifications_table.project_id=$project_id; ";
        $this->db->query($delete_notifications_sql);


        //delete the files from directory
        $comment_file_path = get_setting("timeline_file_path");
        foreach ($project_comments as $comment_info) {
            if ($comment_info->files && $comment_info->files != "a:0:{}") {
                $files = unserialize($comment_info->files);
                foreach ($files as $file) {
                    $source_path = $comment_file_path . get_array_value($file, "file_name");
                    delete_file_from_directory($source_path);
                }
            }
        }



        //delete the project files from directory
        $file_path = get_setting("project_file_path");
        foreach ($project_files as $file) {
            delete_file_from_directory($file_path . $file->project_id . "/" . $file->file_name);
        }

        return true;
    }
	
	
	
	//Función que retorna todos los proyectos que tengan habilitado un módulo en particular.
	function get_available_compromises_projects($module_id, $options = array(), $tipo_evaluacion){

		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		if($tipo_evaluacion == "rca"){
			$campo_matriz = "$projects_table.matriz_compromisos_rca";
		}
		if($tipo_evaluacion == "reportables"){
			$campo_matriz = "$projects_table.matriz_compromisos_reportables";
		}
		
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $projects_table.id=$id_proyecto";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $clients_table.id=$id_cliente";
        }
		
		$matriz_creada_rca = get_array_value($options, "matriz_creada_rca");
        if ($matriz_creada_rca) {
			if($matriz_creada_rca == "si"){
				$where .= " AND $projects_table.matriz_compromisos_rca = 1";
			}
			if($matriz_creada_rca == "no"){
				$where .= " AND $projects_table.matriz_compromisos_rca = 0";
			} 
        }
		
		$matriz_creada_reportables = get_array_value($options, "matriz_creada_reportables");
        if ($matriz_creada_reportables) {
			if($matriz_creada_reportables == "si"){
				$where .= " AND $projects_table.matriz_compromisos_reportables = 1";
			}
			if($matriz_creada_reportables == "no"){
				$where .= " AND $projects_table.matriz_compromisos_reportables = 0";
			} 
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $projects_table.id, $clients_table.company_name, $projects_table.title, $campo_matriz, $module_availability_settings_table.available 
				FROM $clients_table, $projects_table, $module_availability_settings_table 
				WHERE $clients_table.id = $module_availability_settings_table.id_cliente 
				AND $projects_table.id = $module_availability_settings_table.id_proyecto
				AND $module_availability_settings_table.id_modulo_cliente = $module_id
				AND $module_availability_settings_table.available = 1 
				AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $module_availability_settings_table.deleted = 0
				$where";
				
		return $this->db->query($sql);
	}
	
	//Función que retorna todos los proyectos que tengan habilitado un módulo en particular.
	function get_available_permitting_projects($module_id, $options = array()){

		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $projects_table.id=$id_proyecto";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $clients_table.id=$id_cliente";
        }
		
		$matriz_creada = get_array_value($options, "matriz_creada");
        if ($matriz_creada) {
			if($matriz_creada == "si"){
				$where .= " AND $projects_table.matriz_permisos = 1";
			}
			if($matriz_creada == "no"){
				$where .= " AND $projects_table.matriz_permisos = 0";
			} 
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $projects_table.id, $clients_table.company_name, $projects_table.title, $projects_table.matriz_permisos, $module_availability_settings_table.available 
				FROM $clients_table, $projects_table, $module_availability_settings_table 
				WHERE $clients_table.id = $module_availability_settings_table.id_cliente 
				AND $projects_table.id = $module_availability_settings_table.id_proyecto
				AND $module_availability_settings_table.id_modulo_cliente = $module_id
				AND $module_availability_settings_table.available = 1 
				AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $module_availability_settings_table.deleted = 0
				$where";
			
		return $this->db->query($sql);
	}
	
	//Función que retorna todos los proyectos que tengan habilitado un módulo en particular.
	function get_available_stakeholders_projects($module_id, $options = array()){

		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $projects_table.id=$id_proyecto";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $clients_table.id=$id_cliente";
        }
		
		$matriz_creada = get_array_value($options, "matriz_creada");
        if ($matriz_creada) {
			if($matriz_creada == "si"){
				$where .= " AND $projects_table.matriz_stakeholders = 1";
			}
			if($matriz_creada == "no"){
				$where .= " AND $projects_table.matriz_stakeholders = 0";
			} 
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $projects_table.id, $clients_table.company_name, $projects_table.title, $projects_table.matriz_stakeholders, $module_availability_settings_table.available 
				FROM $clients_table, $projects_table, $module_availability_settings_table 
				WHERE $clients_table.id = $module_availability_settings_table.id_cliente 
				AND $projects_table.id = $module_availability_settings_table.id_proyecto
				AND $module_availability_settings_table.id_modulo_cliente = $module_id
				AND $module_availability_settings_table.available = 1 
				AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $module_availability_settings_table.deleted = 0
				$where";
			
		return $this->db->query($sql);
	}
	
	//Función que retorna todos los proyectos que tengan habilitado un módulo en particular.
	function get_available_agreements_projects($module_id, $options = array()){

		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $projects_table.id=$id_proyecto";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $clients_table.id=$id_cliente";
        }
		
		$matriz_creada = get_array_value($options, "matriz_creada");
        if ($matriz_creada) {
			if($matriz_creada == "si"){
				$where .= " AND $projects_table.matriz_acuerdos = 1";
			}
			if($matriz_creada == "no"){
				$where .= " AND $projects_table.matriz_acuerdos = 0";
			} 
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $projects_table.id, $clients_table.company_name, $projects_table.title, $projects_table.matriz_acuerdos, $module_availability_settings_table.available 
				FROM $clients_table, $projects_table, $module_availability_settings_table 
				WHERE $clients_table.id = $module_availability_settings_table.id_cliente 
				AND $projects_table.id = $module_availability_settings_table.id_proyecto
				AND $module_availability_settings_table.id_modulo_cliente = $module_id
				AND $module_availability_settings_table.available = 1 
				AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $module_availability_settings_table.deleted = 0
				$where";
			
		return $this->db->query($sql);
	}

	//Función que retorna todos los proyectos que tengan habilitado un módulo en particular.
	function get_available_feedback_projects($module_id, $options = array()){

		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$module_availability_settings_table = $this->db->dbprefix('module_availability_settings');
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $projects_table.id=$id_proyecto";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $clients_table.id=$id_cliente";
        }
		
		$matriz_creada = get_array_value($options, "matriz_creada");
        if ($matriz_creada) {
			if($matriz_creada == "si"){
				$where .= " AND $projects_table.matriz_feedback = 1";
			}
			if($matriz_creada == "no"){
				$where .= " AND $projects_table.matriz_feedback = 0";
			} 
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $projects_table.id, $clients_table.company_name, $projects_table.title, $projects_table.matriz_feedback, $module_availability_settings_table.available 
				FROM $clients_table, $projects_table, $module_availability_settings_table 
				WHERE $clients_table.id = $module_availability_settings_table.id_cliente 
				AND $projects_table.id = $module_availability_settings_table.id_proyecto
				AND $module_availability_settings_table.id_modulo_cliente = $module_id
				AND $module_availability_settings_table.available = 1 
				AND $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $module_availability_settings_table.deleted = 0
				$where";
			
		return $this->db->query($sql);
	}
	
	// Función que retorna un listado de proyectos para el filtro de proyectos del módulo KPI Reporte (vista cliente)
	function get_project_for_kpi_report_filter($options = array()){
		
		$proyectos_table = $this->db->dbprefix('projects');
		$proyecto_rel_fases_table = $this->db->dbprefix('proyecto_rel_fases');
		$fases_table = $this->db->dbprefix('fases');
		
		$where = "";
		
		$id_pais = get_array_value($options, "id_pais");
        if ($id_pais) {
            $where .= " AND $proyectos_table.id_pais = $id_pais";
        }
		
		$id_fase = get_array_value($options, "id_fase");
        if ($id_fase) {
            $where .= " AND $proyecto_rel_fases_table.id_fase = $id_fase";
        }
		
		$id_tech = get_array_value($options, "id_tech");
        if ($id_tech) {
            $where .= " AND $proyectos_table.id_tech = $id_tech";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $proyectos_table.client_id = $id_cliente";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $proyectos_table.id, $proyectos_table.title";
		$sql .= " FROM $proyectos_table, $proyecto_rel_fases_table, $fases_table";
		$sql .= " WHERE $proyectos_table.id = $proyecto_rel_fases_table.id_proyecto";
		$sql .= " AND $fases_table.id = $proyecto_rel_fases_table.id_fase";
		$sql .= " AND $proyectos_table.deleted = 0";
		$sql .= " AND $proyecto_rel_fases_table.deleted = 0";
		$sql .= " AND $fases_table.deleted = 0";
		$sql .= " $where";
			
		return $this->db->query($sql);
	
	}

	// Función que retorna un listado de proyectos para el filtro de proyectos del módulo Economía Circular (vista cliente)
	function get_project_for_circular_economy_filter($options = array()){
		
		$proyectos_table = $this->db->dbprefix('projects');
		$proyecto_rel_fases_table = $this->db->dbprefix('proyecto_rel_fases');
		$fases_table = $this->db->dbprefix('fases');
		
		$where = "";
		
		$id_pais = get_array_value($options, "id_pais");
        if ($id_pais) {
            $where .= " AND $proyectos_table.id_pais = $id_pais";
        }
		
		$id_fase = get_array_value($options, "id_fase");
        if ($id_fase) {
            $where .= " AND $proyecto_rel_fases_table.id_fase = $id_fase";
        }
		
		$id_tech = get_array_value($options, "id_tech");
        if ($id_tech) {
            $where .= " AND $proyectos_table.id_tech = $id_tech";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $proyectos_table.client_id = $id_cliente";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $proyectos_table.id, $proyectos_table.title";
		$sql .= " FROM $proyectos_table, $proyecto_rel_fases_table, $fases_table";
		$sql .= " WHERE $proyectos_table.id = $proyecto_rel_fases_table.id_proyecto";
		$sql .= " AND $fases_table.id = $proyecto_rel_fases_table.id_fase";
		$sql .= " AND $proyectos_table.deleted = 0";
		$sql .= " AND $proyecto_rel_fases_table.deleted = 0";
		$sql .= " AND $fases_table.deleted = 0";
		$sql .= " $where";
			
		return $this->db->query($sql);
	
	}

}