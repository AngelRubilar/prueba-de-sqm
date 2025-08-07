<?php

class AYN_Alert_historical_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_alert_historical';
        parent::__construct($this->table);
    }	
	
	function count_alerts($id_user, $last_alert_checke_at = "0"){
		
		$ayn_alert_historical_table = $this->db->dbprefix('ayn_alert_historical');
		$ayn_alert_historical_users_table = $this->db->dbprefix('ayn_alert_historical_users');
		
		$sql = "SELECT COUNT($ayn_alert_historical_table.id) AS total_alerts";
		$sql .= " FROM $ayn_alert_historical_table, $ayn_alert_historical_users_table";
		$sql .= " WHERE $ayn_alert_historical_table.id = $ayn_alert_historical_users_table.id_alert_historical";
		$sql .= " AND $ayn_alert_historical_users_table.id_user = $id_user";
		$sql .= " AND $ayn_alert_historical_users_table.viewed IS NULL";
		
		$sql .= " AND $ayn_alert_historical_table.web_only = 1";
		
		//$sql .= " AND $ayn_alert_historical_table.id_user != $id_user";
		$sql .= " AND timestamp($ayn_alert_historical_table.alert_date)>timestamp('$last_alert_checke_at')";
		$sql .= " AND $ayn_alert_historical_table.deleted = 0";
		$sql .= " AND $ayn_alert_historical_users_table.deleted = 0";
				
		$result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->total_alerts;
        }
		
	}
			
	function get_alerts($id_user, $offset = 0, $limit = NULL, $options = array()){
		
		$ayn_alert_historical_table = $this->db->dbprefix('ayn_alert_historical');
		$ayn_alert_historical_users_table = $this->db->dbprefix('ayn_alert_historical_users');
		$users_table = $this->db->dbprefix('users');
		
		$where = "";
        
		$id_client_module = get_array_value($options, "id_client_module");
        if ($id_client_module) {
            $where .= " AND $ayn_alert_historical_table.id_client_module = $id_client_module";
        }
		
		$actions = get_array_value($options, "actions");
		if(count($actions)){
			if(in_array("own", $actions) && in_array("others", $actions)){
			} elseif(in_array("own", $actions)){
				$where .= " AND $ayn_alert_historical_table.id_user = $id_user";
			} elseif(in_array("others", $actions)){
				$where .= " AND $ayn_alert_historical_table.id_user != $id_user";
			}
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS $ayn_alert_historical_table.*, CONCAT($users_table.first_name,' ',$users_table.last_name) AS user_name,";
		$sql .= " $users_table.image AS user_image, $ayn_alert_historical_users_table.id_user AS id_user_alerted, $ayn_alert_historical_users_table.viewed";
		$sql .= " FROM $ayn_alert_historical_table, $ayn_alert_historical_users_table, $users_table";
		$sql .= " WHERE $ayn_alert_historical_table.id = $ayn_alert_historical_users_table.id_alert_historical";
		$sql .= " AND $users_table.id = $ayn_alert_historical_table.id_user";
		$sql .= " AND $ayn_alert_historical_users_table.id_user = $id_user";
		
		$sql .= " AND $ayn_alert_historical_table.web_only = 1";
		
		//$sql .= " AND $ayn_alert_historical_table.id_user != $id_user";
		$sql .= " AND $ayn_alert_historical_table.deleted = 0";
		$sql .= " AND $ayn_alert_historical_users_table.deleted = 0";
		$sql .= " $where";
		if($limit){
			$sql .= " ORDER BY $ayn_alert_historical_table.id DESC LIMIT $offset, $limit";
		} else {
			$sql .= " ORDER BY $ayn_alert_historical_table.id DESC";
		}
				
		$data = new stdClass();
        $data->result = $this->db->query($sql)->result();
        $data->found_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->row()->found_rows;
        
		return $data;

	}

	
	function set_alert_status_as_read($id_alert, $id_user = 0) {
				
		$ayn_alert_historical_users_table = $this->db->dbprefix('ayn_alert_historical_users');
		$viewed_date = get_current_utc_time();
		
		$sql = "UPDATE $ayn_alert_historical_users_table";
        $sql .= " SET $ayn_alert_historical_users_table.viewed = 1,";
		$sql .= " $ayn_alert_historical_users_table.viewed_date = '$viewed_date'";
		$sql .= " WHERE $ayn_alert_historical_users_table.id_alert_historical = $id_alert";
		$sql .= " AND $ayn_alert_historical_users_table.id_user = $id_user";
		
        return $this->db->query($sql);
    }
	
	
	function get_alert_historical($options = array()){
		
		$alert_historical_table = $this->db->dbprefix('ayn_alert_historical');
		
		$where = "";
        $id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $alert_historical_table.id_client = $id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $alert_historical_table.id_project = $id_project";
        }
		
		$id_client_module = get_array_value($options, "id_client_module");
        if ($id_client_module) {
            $where .= " AND $alert_historical_table.id_client_module = $id_client_module";
        }
		
		$id_client_submodule = get_array_value($options, "id_client_submodule");
        if ($id_client_submodule) {
            $where .= " AND $alert_historical_table.id_client_submodule = $id_client_submodule";
        }
		
		$alert_config = get_array_value($options, "alert_config");
        if (count($alert_config)) {

			if($id_client_module == "6"){ // Compromisos
				if($id_client_submodule == "4" || $id_client_submodule == "22"){ // Evaluación de Compromisos RCA || Evaluación de Compromisos Reportables
					
					$id_planificacion = $alert_config["id_planificacion"];
					$id_valor_compromiso = $alert_config["id_valor_compromiso"];
					$tipo_evaluacion = $alert_config["tipo_evaluacion"];
					
					$where .= ($id_planificacion) ? " AND $alert_historical_table.alert_config->'$.id_planificacion' = '$id_planificacion'" : "";
					$where .= ($id_valor_compromiso) ? " AND $alert_historical_table.alert_config->'$.id_valor_compromiso' = '$id_valor_compromiso'" : "";
					$where .= ($tipo_evaluacion) ? " AND $alert_historical_table.alert_config->'$.tipo_evaluacion' = '$tipo_evaluacion'" : "";	
						
				}
			}
			
		}
		
		$id_element = get_array_value($options, "id_element");
        if ($id_element) {
            $where .= " AND $alert_historical_table.id_element = $id_element";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $alert_historical_table.*";
		$sql .= " FROM $alert_historical_table";
		$sql .= " WHERE $alert_historical_table.deleted = 0";
		$sql .= " $where";
				
		return $this->db->query($sql);
		
	}
	
	
}