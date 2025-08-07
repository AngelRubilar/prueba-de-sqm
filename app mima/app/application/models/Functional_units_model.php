<?php

class Functional_units_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'unidades_funcionales';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $functional_units_table = $this->db->dbprefix('unidades_funcionales');
		$proyecto_rel_fases_table = $this->db->dbprefix('proyecto_rel_fases');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $functional_units_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $functional_units_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $functional_units_table.id_proyecto=$id_proyecto";
        }
		
		$id_subproyecto = get_array_value($options, "id_subproyecto");
        if ($id_subproyecto) {
            $where .= " AND $functional_units_table.id_subproyecto=$id_subproyecto";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $functional_units_table.* FROM $functional_units_table WHERE $functional_units_table.deleted=0 $where";
		
        return $this->db->query($sql);
    }
	
	
	/*Functional units list for multiselect edit view*/
	function get_functional_units_of_projects($project_id){ 
		
		$array_fu = array();
		$array_fu_project = array();
		$proyecto_rel_fases_table = $this->db->dbprefix('proyecto_rel_fases');		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $proyecto_rel_fases_table.* from $proyecto_rel_fases_table WHERE";
		$sql .= " $proyecto_rel_fases_table.id_proyecto = $project_id";
		
		$query = $this->db->query($sql);

		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_fu["id"] = $value["id_unidad_funcional"];
					$array_fu["nombre"] =  $this->get_one($value["id_unidad_funcional"])->nombre;
					$array_fu_project_rel[$innerRow] = $array_fu;
				}	
			}
		}
				
		return $array_fu_project_rel;
	}
	
	
	function delete_functional_unit($id){
		
		$unidades_funcionales = $this->db->dbprefix('unidades_funcionales');
        $sql = "UPDATE $unidades_funcionales SET $unidades_funcionales.deleted=1 WHERE $unidades_funcionales.id=$id; ";
        $this->db->query($sql);
		
	}
	

}
