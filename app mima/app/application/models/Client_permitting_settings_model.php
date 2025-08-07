<?php
class Client_permitting_settings_model extends Crud_model {
	
    private $table = null;

    function __construct() {
        $this->table = 'client_permitting_settings';
        parent::__construct($this->table);
    }
	
	
	function get_details($options = array()){
	
		$client_permitting_settings = $this->db->dbprefix('client_permitting_settings');

		$where = "";

		$id = get_array_value($options, "id");
		if ($id) {
			$where .= " AND $client_permitting_settings.id=$id";
		}

		$sql = "SELECT $client_permitting_settings.*
		FROM $client_permitting_settings   
		WHERE $client_permitting_settings.deleted=0 $where";

		return $this->db->query($sql);
	
	}
	
	function save_default_settings($id_cliente, $id_proyecto){
		
		$default_client_permitting_settings = array(		
			"id_cliente" => $id_cliente,
			"id_proyecto" => $id_proyecto,
			"tabla" => 1,
			"grafico" => 1,
			"deleted" => 0
		);
		
		$this->save($default_client_permitting_settings);
		
	}
}