<?php

class Client_indicators_model extends Crud_model {

	
    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'client_indicators';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$client_indicators = $this->db->dbprefix('client_indicators');
		
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $client_indicators.id=$id";
        }

        $sql = "SELECT $client_indicators.*
        FROM $client_indicators   
        WHERE $client_indicators.deleted=0 $where";
		
		return $this->db->query($sql);
		
	}
	
	
	function delete_client_indicators($id){
		
		$client_indicators = $this->db->dbprefix('client_indicators');

		$sql = "UPDATE $client_indicators SET $client_indicators.deleted=1 WHERE $client_indicators.id=$id; ";
		$this->db->query($sql);
	}
	
	
}
