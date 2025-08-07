<?php

class Project_rel_pu_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'proyecto_rel_pu';
        parent::__construct($this->table);
    }

   /* Elimina el pu relacionado a un proyecto */
    function delete_pu_related_to_project($project_id){
        
        $project_rel_pu_table = $this->db->dbprefix('proyecto_rel_pu');
        $sql = "DELETE FROM $project_rel_pu_table WHERE";
        $sql .= " $project_rel_pu_table.id_proyecto = $project_id";
        
        if($this->db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	//Función update a 1 o eliminado
	function delete_pu_rel_project($project_id) {
        
        $pu_rel_project_table = $this->db->dbprefix('proyecto_rel_pu');
        $delete_pu_rel = "UPDATE $pu_rel_project_table SET $pu_rel_project_table.deleted=1 WHERE $pu_rel_project_table.id_proyecto=$project_id; ";
        $this->db->query($delete_pu_rel);
    }

}
