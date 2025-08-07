<?php

class Assignment_combinations_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'asignaciones_combinaciones';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		$clients_table = $this->db->dbprefix('clients');
		$projects_table = $this->db->dbprefix('projects');
		$rules_table = $this->db->dbprefix('criterios');
        $assignment_table = $this->db->dbprefix('asignaciones');
        $combinaciones_table = $this->db->dbprefix('asignaciones_combinaciones');

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

		$sql = "SELECT $clients_table.company_name, $projects_table.title, $rules_table.etiqueta, $combinaciones_table.* ";
		$sql .= "FROM $clients_table, $projects_table, $rules_table, $assignment_table, $combinaciones_table ";
		$sql .= "WHERE $clients_table.deleted = 0 AND $projects_table.deleted = 0 AND $rules_table.deleted = 0 AND $assignment_table.deleted = 0 AND $combinaciones_table.deleted = 0 AND $projects_table.client_id = $clients_table.id AND $rules_table.id_proyecto = $projects_table.id AND $assignment_table.id_criterio = $rules_table.id AND $combinaciones_table.id_asignacion = $assignment_table.id $where ";
		
        return $this->db->query($sql);
    }
	
	/**
	* Esta funcion hace un proceso retrogrado sobre las combinaciones y devuelve el estado inicial de las opciones originales del subproyecto al momento de ingresar una asignacion
	*/
	function get_sp_rules_options_combinations_based($id_asignacion){
		
		$combinaciones_table = $this->db->dbprefix('asignaciones_combinaciones');
		
		$sql = "SELECT $combinaciones_table.id, $combinaciones_table.id_asignacion, $combinaciones_table.criterio_sp, $combinaciones_table.tipo_asignacion_sp, $combinaciones_table.sp_destino, $combinaciones_table.porcentajes_sp ";
		$sql .= "FROM $combinaciones_table ";
		$sql .= "WHERE $combinaciones_table.id_asignacion = $id_asignacion AND $combinaciones_table.deleted = 0 ";
		$sql .= "GROUP BY $combinaciones_table.criterio_sp, $combinaciones_table.tipo_asignacion_sp ORDER by $combinaciones_table.id ";
		
		return $this->db->query($sql);
		
	}
	
	/**
	* Esta funcion hace un proceso retrogrado sobre las combinaciones y devuelve el estado inicial de las opciones originales del proceso unitario al momento de ingresar una asignacion
	*/
	function get_pu_rules_options_combinations_based($id_asignacion){
		
		$combinaciones_table = $this->db->dbprefix('asignaciones_combinaciones');
		
		$sql = "SELECT $combinaciones_table.id, $combinaciones_table.id_asignacion, $combinaciones_table.criterio_pu, $combinaciones_table.tipo_asignacion_pu, $combinaciones_table.pu_destino, $combinaciones_table.porcentajes_pu ";
		$sql .= "FROM $combinaciones_table ";
		$sql .= "WHERE $combinaciones_table.id_asignacion = $id_asignacion AND $combinaciones_table.deleted = 0 ";
		$sql .= "GROUP BY $combinaciones_table.criterio_pu, $combinaciones_table.tipo_asignacion_pu ORDER by $combinaciones_table.id ";
		
		return $this->db->query($sql);
		
	}
    

}
