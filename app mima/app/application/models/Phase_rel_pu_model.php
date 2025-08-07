<?php

class Phase_rel_pu_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fase_rel_pu';
        parent::__construct($this->table);
    }

	/* Elimina los pu relacionados a una fase */
    function delete_pu_related_to_phase($id_fase){
		
		$phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');
		$sql = "DELETE FROM $phase_rel_pu_table WHERE";
		$sql .= " $phase_rel_pu_table.id_fase = $id_fase";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	
	//Obtiene el pu(proceso unitario) recibiendo como parámetro la fase
	
	function get_pu_of_phase($id_fase){
		
		$array_pu = array();
		$array_pu_phase = array();
		$phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $phase_rel_pu_table.* from $phase_rel_pu_table WHERE";
		$sql .= " $phase_rel_pu_table.id_fase = $id_fase";
		
		$query = $this->db->query($sql);

		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_pu["id"] = $value["id_proceso_unitario"];
					$array_pu["nombre"] =  $this->get_one($value["id_proceso_unitario"])->nombre;
					$array_pu_phase[$innerRow] = $array_pu;
				}	
			}
		}
				
		return $array_pu_phase;
	}
	
	/* Elimina la relación fase_rel_pu al borrar un proceso unitario */
    function delete_phases_rel_pu($id_proceso_unitario){
        
        $fase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');
        $sql = "DELETE FROM $fase_rel_pu_table WHERE";
        $sql .= " $fase_rel_pu_table.id_proceso_unitario = $id_proceso_unitario";
        
        if($this->db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
}
