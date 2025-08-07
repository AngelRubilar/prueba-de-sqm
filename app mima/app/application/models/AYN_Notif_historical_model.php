<?php

class AYN_Notif_historical_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_historical';
        parent::__construct($this->table);
    }
	
	function count_notifications($id_user, $last_notification_checke_at = "0"){
		
		$ayn_notif_historical_table = $this->db->dbprefix('ayn_notif_historical');
		$ayn_notif_historical_users_table = $this->db->dbprefix('ayn_notif_historical_users');
		
		$sql = "SELECT COUNT($ayn_notif_historical_table.id) AS total_notifications";
		$sql .= " FROM $ayn_notif_historical_table, $ayn_notif_historical_users_table";
		$sql .= " WHERE $ayn_notif_historical_table.id = $ayn_notif_historical_users_table.id_notif_historical";
		$sql .= " AND $ayn_notif_historical_users_table.id_user = $id_user";
		$sql .= " AND $ayn_notif_historical_users_table.viewed IS NULL";
		$sql .= " AND $ayn_notif_historical_table.id_user != $id_user";
		$sql .= " AND timestamp($ayn_notif_historical_table.notified_date)>timestamp('$last_notification_checke_at')";
		$sql .= " AND $ayn_notif_historical_table.deleted = 0";
		$sql .= " AND $ayn_notif_historical_users_table.deleted = 0";
		
		$result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->total_notifications;
        }
		
	}
			
	function get_notifications($id_user, $offset = 0, $limit = NULL, $options = array()){
		
		$ayn_notif_historical_table = $this->db->dbprefix('ayn_notif_historical');
		$ayn_notif_historical_users_table = $this->db->dbprefix('ayn_notif_historical_users');
		$users_table = $this->db->dbprefix('users');
		
		$where = "";
        
		$id_admin_module = get_array_value($options, "id_admin_module");
        if ($id_admin_module) {
            $where .= " AND $ayn_notif_historical_table.id_admin_module = $id_admin_module";
        }
		
		$id_client_module = get_array_value($options, "id_client_module");
        if ($id_client_module) {
            $where .= " AND $ayn_notif_historical_table.id_client_module = $id_client_module";
        }
		
		$id_client_context_module = get_array_value($options, "id_client_context_module");
        if ($id_client_context_module) {
            $where .= " AND $ayn_notif_historical_table.id_client_context_module = $id_client_context_module";
        }
		
		$actions = get_array_value($options, "actions");
		if(count($actions)){
			if(in_array("own", $actions) && in_array("others", $actions)){
			} elseif(in_array("own", $actions)){
				$where .= " AND $ayn_notif_historical_table.id_user = $id_user";
			} elseif(in_array("others", $actions)){
				$where .= " AND $ayn_notif_historical_table.id_user != $id_user";
			}
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS $ayn_notif_historical_table.*, CONCAT($users_table.first_name,' ',$users_table.last_name) AS user_name,";
		$sql .= " $users_table.image AS user_image, $ayn_notif_historical_users_table.id_user AS id_user_notified, $ayn_notif_historical_users_table.viewed";
		$sql .= " FROM $ayn_notif_historical_table, $ayn_notif_historical_users_table, $users_table";
		$sql .= " WHERE $ayn_notif_historical_table.id = $ayn_notif_historical_users_table.id_notif_historical";
		$sql .= " AND $users_table.id = $ayn_notif_historical_table.id_user";
		$sql .= " AND $ayn_notif_historical_users_table.id_user = $id_user";
		//$sql .= " AND $ayn_notif_historical_table.id_user != $id_user";
		$sql .= " AND $ayn_notif_historical_table.deleted = 0";
		$sql .= " AND $ayn_notif_historical_users_table.deleted = 0";
		$sql .= " $where";
		if($limit){
			$sql .= " ORDER BY $ayn_notif_historical_table.id DESC LIMIT $offset, $limit";
		} else {
			$sql .= " ORDER BY $ayn_notif_historical_table.id DESC";
		}
		
		$data = new stdClass();
        $data->result = $this->db->query($sql)->result();
        $data->found_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->row()->found_rows;
        
		return $data;

	}
	
	function set_notification_status_as_read($id_notification, $id_user = 0) {
        
		$ayn_notif_historical_users_table = $this->db->dbprefix('ayn_notif_historical_users');
		$viewed_date = get_current_utc_time();
		
		$sql = "UPDATE $ayn_notif_historical_users_table";
        $sql .= " SET $ayn_notif_historical_users_table.viewed = 1,";
		$sql .= " $ayn_notif_historical_users_table.viewed_date = '$viewed_date'";
		$sql .= " WHERE $ayn_notif_historical_users_table.id_notif_historical = $id_notification";
		$sql .= " AND $ayn_notif_historical_users_table.id_user = $id_user";
		
        return $this->db->query($sql);
    }
	
}