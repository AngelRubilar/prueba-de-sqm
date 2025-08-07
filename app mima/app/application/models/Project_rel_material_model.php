<?php

class Project_rel_material_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'materiales_proyecto';
        parent::__construct($this->table);
    }
	/* Elimina los materiales relacionados a un proyecto */
    function delete_materials_related_to_project($project_id){
		
		$project_rel_materials_table = $this->db->dbprefix('materiales_proyecto');
		$sql = "DELETE FROM $project_rel_materials_table WHERE";
		$sql .= " $project_rel_materials_table.id_proyecto = $project_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function delete_materials_rel_project($project_id){
		
		$project_rel_materials_table = $this->db->dbprefix('materiales_proyecto');
        
		$delete = "UPDATE $project_rel_materials_table 
		SET $project_rel_materials_table.deleted = 1 
		WHERE $project_rel_materials_table.id_proyecto = $project_id; ";
        
		$this->db->query($delete);
		
	}


}
