<?php

class Logs_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'logs';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        
        $logs_table = $this->db->dbprefix('logs');
        $clients_table = $this->db->dbprefix('clients');
        $home_modules_info_table = $this->db->dbprefix('home_modules_info');
        $projects_table = $this->db->dbprefix('projects');
        $logs_modules_table = $this->db->dbprefix('logs_modules');
        $users_table = $this->db->dbprefix('users');

        $where = "";
		$id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $logs_table.id = $id ";
        }

        $id_cliente = get_array_value($options, 'id_cliente');
        if($id_cliente){
            $where .= " AND $clients_table.id = $id_cliente";
        }

        $id_proyecto = get_array_value($options, 'id_proyecto');
        if($id_proyecto){
            $where .= " AND $projects_table.id = $id_proyecto";
        }

        $id_home_modules_info = get_array_value($options, 'id_home_modules_info');
        if($id_home_modules_info){
            $where .= " AND $home_modules_info_table.id = $id_home_modules_info";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT ";
            $sql .= "$logs_table.*,";
            $sql .= "$clients_table.company_name AS client_name,";
            $sql .= "$home_modules_info_table.nombre AS menu_name,";
            $sql .= "$projects_table.title AS project_name,";
            $sql .= "$logs_modules_table.module AS module_name, ";
            $sql .= "user_details.user_name ";
            //$array_variables = get_array_value($options, "variables");
            //$sql .= " IF(IFNULL($acustic_stations_values_m_table.dato->'$.\"$id_variable\"', '$null_value') = 'null','$null_value',$acustic_stations_values_m_table.dato->'$.\"$id_variable\"') AS '$variable',";
        $sql .= "FROM $logs_table ";
        $sql .= "LEFT JOIN $clients_table ON $clients_table.id = $logs_table.id_cliente ";
        $sql .= "LEFT JOIN $home_modules_info_table ON $home_modules_info_table.id = $logs_table.id_home_modules_info ";
        $sql .= "LEFT JOIN $projects_table ON $projects_table.id = $logs_table.id_proyecto ";
        $sql .= "LEFT JOIN $logs_modules_table ON $logs_modules_table.id = $logs_table.id_module ";
        $sql .= "LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS user_name, $users_table.image AS user_avatar FROM $users_table WHERE $users_table.deleted=0) AS user_details ON user_details.id=$logs_table.id_usuario ";
        $sql .= "WHERE $clients_table.deleted = 0 AND ($projects_table.deleted = 0 OR $logs_table.id_proyecto IS NULL) ";
        $sql .= $where;
        //$sql .= " ORDER BY $acustic_dates_table.fecha, $acustic_hours_table.id, $acustic_minutes_table.id ASC";
        
        set_time_limit(500);
        
        return $this->db->query($sql); 

    }
}
