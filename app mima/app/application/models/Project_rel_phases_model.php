<?php

class Project_rel_phases_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'proyecto_rel_fases';
        parent::__construct($this->table);
    }

    //NOT USED
    /* Retorna la fase relacionada a un proyecto */
    function get_phases_related_to_project($project_id){
        
        $project_rel_phases_table = $this->db->dbprefix('proyecto_rel_fases');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $project_rel_phases_table.* FROM $project_rel_phases_table WHERE";
        $sql.= " $project_rel_phases_table.id_proyecto = $project_id";
        
        return $this->db->query($sql);
    }

    /* Elimina la fase relacionada a un proyecto para edición */
    function delete_phases_related_to_project($project_id){
        
        $project_rel_phases_table = $this->db->dbprefix('proyecto_rel_fases');
        $sql = "DELETE FROM $project_rel_phases_table WHERE";
        $sql .= " $project_rel_phases_table.id_proyecto = $project_id";
        
        if($this->db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	
	//Función update a 1 o eliminado
	function delete_phases_rel_project($project_id) {
        
        $project_rel_phases_table = $this->db->dbprefix('proyecto_rel_fases');
        $delete_phases_rel = "UPDATE $project_rel_phases_table SET $project_rel_phases_table.deleted=1 WHERE $project_rel_phases_table.id_proyecto=$project_id; ";
        $this->db->query($delete_phases_rel);
    }

}
