<?php

class Phases_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fases';
        parent::__construct($this->table);
    }

    

    function get_details($options = array()) {
        $fases_table = $this->db->dbprefix('fases');

        $where = "";
        $id = get_array_value($options, "id");
        $nombre = get_array_value($options, "nombre");

        if ($id) {
            $where .= " AND $fases_table.id=$id";
        }
        if ($nombre) {
            $where .= " AND $fases_table.nombre=$nombre";
        }


        //prepare full query string
        $sql = "SELECT $fases_table.id, $fases_table.nombre
        FROM $fases_table   
        WHERE $fases_table.deleted=0 $where
        ORDER BY $fases_table.id";
        return $this->db->query($sql);
    }

    
    /*Phases list for multiselect edit*/
    function get_phases_of_projects($project_id){
        
        $array_fases = array();
        $array_fases_project = array();
        $project_rel_phases_table = $this->db->dbprefix('proyecto_rel_fases');     
        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT $project_rel_phases_table.* from $project_rel_phases_table WHERE";
        $sql .= " $project_rel_phases_table.project_id = $project_id";
        
        $query = $this->db->query($sql);

        foreach($query as $row => $innerArray){
            foreach($innerArray as $innerRow => $value){
                if($value != null){
                    $array_fases["id"] = $value["id"];
                    $array_fases["nombre"] =  $this->get_one($value["id_fase"])->nombre;
                    $array_fases_project[$innerRow] = $array_fases;
                }   
            }
        }
                
        return $array_fases_project;
    }
    
    
    

}
