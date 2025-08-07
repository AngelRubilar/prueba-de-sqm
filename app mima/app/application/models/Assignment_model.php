<?php

class Assignment_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'asignaciones';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $assignment_table = $this->db->dbprefix('asignaciones');
        $rules_table = $this->db->dbprefix('criterios');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$subprojects_table= $this->db->dbprefix('subproyectos');
		$unit_processes_table= $this->db->dbprefix('procesos_unitarios');
		$combinaciones_table= $this->db->dbprefix('asignaciones_combinaciones');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $assignment_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $assignment_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $assignment_table.id_proyecto=$id_proyecto";
        }
		
        $id_criterio = get_array_value($options, "id_criterio");
        if ($id_criterio) {
            $where .= " AND $assignment_table.id_criterio=$id_criterio";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 

		$sql = "SELECT $assignment_table.*, $clients_table.company_name AS company_name, $projects_table.title AS title, $rules_table.etiqueta AS etiqueta FROM $assignment_table, $clients_table, $projects_table, $rules_table 
WHERE $assignment_table.deleted = 0 AND $rules_table.deleted = 0 AND $clients_table.id = $assignment_table.id_cliente AND $projects_table.id = $assignment_table.id_proyecto AND $rules_table.id = $assignment_table.id_criterio $where";

        return $this->db->query($sql);
    }
	
	function get_details_porcentaje_destino($options = array()){
		
		$assignment_table = $this->db->dbprefix('asignaciones');
        $rules_table = $this->db->dbprefix('criterios');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$subprojects_table= $this->db->dbprefix('subproyectos');
		$unit_processes_table= $this->db->dbprefix('procesos_unitarios');

        $where = "";
        
		$id_criterio = get_array_value($options, "id_criterio");
		if ($id_criterio) {
            $where .= " AND $assignment_table.id_criterio=$id_criterio";
        }
		
		$tipo_asignacion = get_array_value($options, "tipo_asignacion");
		if ($tipo_asignacion) {
			if($tipo_asignacion == "sp"){
				$where .= " AND $assignment_table.tipo_asignacion_sp = 'Porcentual'";
			}
			if($tipo_asignacion == "pu"){
				$where .= " AND $assignment_table.tipo_asignacion_pu = 'Porcentual'";
			}
        }
		
		$sql = "SELECT * FROM (SELECT $assignment_table.*, $unit_processes_table.nombre AS pu 
				FROM (SELECT $assignment_table.*, $subprojects_table.nombre AS subprojects 
				FROM (SELECT $assignment_table.*, $clients_table.company_name AS company_name, $projects_table.title AS title, $rules_table.etiqueta AS etiqueta 
				FROM $assignment_table, $clients_table, $projects_table, $rules_table 
				WHERE $assignment_table.deleted=0 AND $clients_table.id = $assignment_table.id_cliente AND $projects_table.id=$assignment_table.id_proyecto AND $rules_table .id= $assignment_table.id_criterio) AS asignaciones 
				LEFT JOIN $subprojects_table ON $assignment_table.sp_destino = $subprojects_table.id) AS asignaciones 
				LEFT JOIN $unit_processes_table ON $assignment_table.pu_destino = $unit_processes_table.id) AS asignaciones 
				WHERE 1 $where";
				
		return $this->db->query($sql);
		
	}	
	
	function delete_assignment($id){
		
		$asignaciones = $this->db->dbprefix('asignaciones');
        $sql = "UPDATE $asignaciones SET $asignaciones.deleted=1 WHERE $asignaciones.id=$id; ";
        $this->db->query($sql);
		
	}

}
