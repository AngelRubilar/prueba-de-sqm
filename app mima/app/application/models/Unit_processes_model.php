<?php

class Unit_processes_model extends Crud_model {

    private $table = null;

    function __construct() {
		//$this->load->helper('database');
		//$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        $this->table = 'procesos_unitarios';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        
		$unit_processes_table = $this->db->dbprefix('procesos_unitarios');
		$fase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $unit_processes_table.id=$id";
        }

        $nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $unit_processes_table.nombre=$nombre";
        }
		
		$id_fase = get_array_value($options, "id_fase");
        if ($id_fase) {
            $where .= " AND $fase_rel_pu_table.id_fase=$id_fase";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        //$sql = "SELECT $unit_processes_table.id, $unit_processes_table.nombre, $unit_processes_table.color, $unit_processes_table.icono, $unit_processes_table.descripcion FROM $unit_processes_table WHERE $unit_processes_table.deleted=0 $where";
	 	$sql = "SELECT $unit_processes_table.id, $unit_processes_table.nombre, $unit_processes_table.color,";
        $sql .= " $unit_processes_table.icono, $unit_processes_table.descripcion, $unit_processes_table.created, $unit_processes_table.modified";
		$sql .= " FROM $unit_processes_table" ;
		$sql .= " LEFT JOIN $fase_rel_pu_table ON $unit_processes_table.id = $fase_rel_pu_table.id_proceso_unitario";
		$sql .= " WHERE $unit_processes_table.deleted=0 $where";
		$sql .= " GROUP BY $unit_processes_table.id";
		
		return $this->db->query($sql);
		
    }

    	
	
	//Unit(pu) list for multiselect
    function get_pu_of_phase($id_fase){
        
        $array_proceso_unitario = array();
        $phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT $phase_rel_pu_table.* from $phase_rel_pu_table WHERE";
        $sql .= " $phase_rel_pu_table.id_fase = $id_fase";
        
        $query = $this->db->query($sql);

        foreach($query as $row => $innerArray){
            foreach($innerArray as $innerRow => $value){
                if($value != null){
                    $array_proceso_unitario["id"] = $value["id_proceso_unitario"];
                    $array_proceso_unitario["nombre"] =  $this->get_one($value["id_proceso_unitario"])->nombre;
                    $array_unit_phase[$innerRow] = $array_proceso_unitario;
                }   
            } 
        }
                
        return $array_unit_phase;
    }
    
	
	//add view of project
	function get_unit_processes_of_phase($id_fase) {
        $fases_table = $this->db->dbprefix('fases');
		$phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');
		$pu_table = $this->db->dbprefix('procesos_unitarios');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $pu_table.* FROM $phase_rel_pu_table, $pu_table, $fases_table WHERE";
		$sql .= " $pu_table.deleted=0 AND $phase_rel_pu_table.deleted=0 AND $fases_table.deleted=0";
		$sql .= " AND $pu_table.id = $phase_rel_pu_table.id_proceso_unitario";
		$sql .= " AND $phase_rel_pu_table.id_fase = $fases_table.id";
		$sql .= " AND $fases_table.id = $id_fase";
		
        return $this->db->query($sql);
    }
	
	
	
	/*Pu list for multiselect edit in project controller*/
	function get_pu_of_projects($project_id){
		
		$array_pu = array();
		$array_pu_project = array();
		$unit_processes_table = $this->db->dbprefix('procesos_unitarios');
		$project_rel_pu_table = $this->db->dbprefix('proyecto_rel_pu');		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $unit_processes_table.* from $project_rel_pu_table, $unit_processes_table WHERE";
		$sql .= " $project_rel_pu_table.deleted = 0 AND $unit_processes_table.deleted = 0";
		$sql .= " AND $unit_processes_table.id = $project_rel_pu_table.id_proceso_unitario AND $project_rel_pu_table.id_proyecto = $project_id ORDER BY $project_rel_pu_table.id";
		
		return $this->db->query($sql);
	}
    
	function get_unit_process_details($id_proyecto) {
        
		$unit_processes_table = $this->db->dbprefix('procesos_unitarios');
        $project_rel_pu_table = $this->db->dbprefix('proyecto_rel_pu');
		
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $project_rel_pu_table.id AS id_rel, $unit_processes_table.* FROM $project_rel_pu_table, $unit_processes_table WHERE";
		$sql.= " $unit_processes_table.deleted=0";
		$sql.= " AND $project_rel_pu_table.deleted=0";
		$sql.= " AND $project_rel_pu_table.id_proyecto = $id_proyecto";
		$sql.= " AND $unit_processes_table.id = $project_rel_pu_table.id_proceso_unitario";
		$sql.= " ORDER BY $project_rel_pu_table.id";
		
        return $this->db->query($sql);
    }
	
	function get_rules_calculations_of_project($client_id, $project_id) {
		
		$rules_table = $this->db->dbprefix('criterios');
        $calculos_table = $this->db->dbprefix('calculos');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $rules_table.id AS id_criterio, $rules_table.id_formulario, $rules_table.id_material, $rules_table.id_campo_sp, $rules_table.id_campo_pu, $rules_table.id_campo_fc, $rules_table.tipo_by_criterio, $rules_table.etiqueta AS nombre_criterio, $calculos_table.* ";
		$sql .= "FROM $rules_table, $calculos_table WHERE";
		$sql .= " $rules_table.deleted = 0 AND $calculos_table.deleted = 0";
		$sql .= " AND $calculos_table.id_criterio = $rules_table.id";
		$sql .= " AND $calculos_table.id_cliente = $client_id";
		$sql .= " AND $calculos_table.id_proyecto = $project_id";
		
		/*
		SELECT criterios.id_formulario, criterios.id_material, criterios.etiqueta, calculos.*
		FROM criterios
		LEFT JOIN calculos ON criterios.id = calculos.id_criterio 
		AND calculos.id_cliente = 1 
		AND calculos.id_proyecto = 1
		*/
		
		//echo $sql.'<br>';
        return $this->db->query($sql);
    }
	
	/*function get_rules_assignations_of_project($client_id, $project_id, $pu_id) {
		
		$rules_table = $this->db->dbprefix('criterios');
        $asignaciones_table = $this->db->dbprefix('asignaciones');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $rules_table.id_formulario, $rules_table.id_material, $rules_table.id_campo_fc, $asignaciones_table.* ";
		$sql .= "FROM $rules_table, $asignaciones_table WHERE";
		$sql .= " $rules_table.deleted = 0 AND $asignaciones_table.deleted = 0";
		$sql .= " AND $asignaciones_table.id_criterio = $rules_table.id";
		$sql .= " AND $asignaciones_table.id_cliente = $client_id";
		$sql .= " AND $asignaciones_table.id_proyecto = $project_id";
		$sql .= " AND $asignaciones_table.pu_destino = $pu_id";
		//echo $sql.'<br>';
        return $this->db->query($sql);

    }*/
	function admin_list_data(){
		
		//$fc_bd = getFCBD();
		//$phases_table = $this->load->database(getFCBD(), TRUE)->dbprefix('fases');
		//$phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');
		$pu_table = $this->db->dbprefix('procesos_unitarios');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $pu_table.*  FROM $pu_table WHERE";
		$sql .= " $pu_table.deleted=0";
		
		return $this->db->query($sql);
	}
	
	function get_phases_of_pu($unit_process_id){

		$phases_table = $this->db->dbprefix('fases');
		$pu_table = $this->db->dbprefix('procesos_unitarios');
		$phase_rel_pu_table = $this->db->dbprefix('fase_rel_pu');
		    
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $phases_table.* FROM $phases_table, $phase_rel_pu_table, $pu_table WHERE";
		$sql .= " $phases_table.deleted = 0 AND $pu_table.deleted = 0 AND $phase_rel_pu_table.deleted = 0";
		$sql .= " AND $phase_rel_pu_table.id_fase = $phases_table.id ";
		$sql .= " AND $phase_rel_pu_table.id_proceso_unitario = $pu_table.id";
		$sql .= " AND $pu_table.id = $unit_process_id";
		$sql .= " ORDER BY $phase_rel_pu_table.id";

        return $this->db->query($sql);
		
	}

}
