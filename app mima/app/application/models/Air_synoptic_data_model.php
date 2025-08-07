<?php

class Air_synoptic_data_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'air_synoptic_data';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        
        $air_synoptic_data_table = $this->db->dbprefix('air_synoptic_data');

        $where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where .= " AND $air_synoptic_data_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client){
            $where .= " AND $air_synoptic_data_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project){
            $where .= " AND $air_synoptic_data_table.id_project = $id_project";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = " SELECT $air_synoptic_data_table.* ";
        $sql .= " FROM $air_synoptic_data_table";
        $sql .= " WHERE $air_synoptic_data_table.deleted = 0";
        $sql .= " $where";

        return $this->db->query($sql);

    }

    function is_synoptic_data_exists($date, $id_client, $id_project, $id = 0) {
        $result = $this->get_all_where(array("date" => $date, "id_client" => $id_client, "id_project" => $id_project, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

}
