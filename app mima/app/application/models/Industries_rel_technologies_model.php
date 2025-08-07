<?php

class Industries_rel_technologies_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'industrias_rel_tecnologias';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
        $industries_rel_technologies_table = $this->db->dbprefix('industrias_rel_tecnologias');
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $industries_rel_technologies_table.id=$id";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $industries_rel_technologies_table.*
        FROM $industries_rel_technologies_table 
        WHERE $industries_rel_technologies_table.deleted=0 $where";

        return $this->db->query($sql);
    }

}
