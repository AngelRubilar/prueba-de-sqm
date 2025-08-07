<?php

class Project_rel_footprints_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'proyecto_rel_huellas';
        parent::__construct($this->table);
    }
	
    /* Elimina la(s) huella relacionada a un proyecto */
    function delete_footprints_related_to_project($project_id){
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
        $sql = "DELETE FROM $project_rel_footprints_table WHERE";
        $sql .= " $project_rel_footprints_table.id_proyecto = $project_id";
        
        if($this->db->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	//Función update a 1 o eliminado
	function delete_footprints_rel_project($project_id) {
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
        $delete_footprints_rel = "UPDATE $project_rel_footprints_table SET $project_rel_footprints_table.deleted=1 WHERE $project_rel_footprints_table.id_proyecto=$project_id; ";
        $this->db->query($delete_footprints_rel);
    }
	
	
	//Función para obtener las huellas de un proyecto.
	function get_footprints_of_project($project_id) {
        
        $project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
		
		$footprints_db = getFCBD();
		$footprints_table = $this->load->database(getFCBD(), TRUE)->dbprefix('huellas');
		//$footprints_db = 'dev_mimasoft_fc';
		//$footprints_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('huellas');
		
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $footprints_table.* FROM $project_rel_footprints_table, $footprints_db.$footprints_table 
		WHERE $footprints_table.id = $project_rel_footprints_table.id_huella 
		AND $project_rel_footprints_table.id_proyecto = $project_id AND $project_rel_footprints_table.deleted = 0 AND $footprints_table.deleted = 0
		ORDER BY $project_rel_footprints_table.id";
				
        return $this->db->query($sql);
    }
	
	/*
	//Función para obtener las huellas de un proyecto.
	function get_footprints_of_project($project_id, $client_id){
		
		$project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
		$module_footprint_units_table = $this->db->dbprefix('module_footprint_units');
		
		$footprints_db = getFCBD();
		$footprints_table = $this->load->database(getFCBD(), TRUE)->dbprefix('huellas');
		$unidad_table = $this->load->database(getFCBD(), TRUE)->dbprefix('unidad');
		
		$sql = "SELECT $footprints_table.*, $module_footprint_units_table.id_unidad AS id_unidad_huella_config, $unidad_table.nombre AS nombre_unidad_huella";
		$sql .= " FROM $project_rel_footprints_table, $footprints_db.$footprints_table, $module_footprint_units_table, $footprints_db.$unidad_table";
		$sql .= " WHERE $footprints_table.id = $project_rel_footprints_table.id_huella";
		$sql .= " AND $footprints_table.id_tipo_unidad = $module_footprint_units_table.id_tipo_unidad";
		$sql .= " AND $module_footprint_units_table.id_unidad = $unidad_table.id";
		$sql .= " AND $project_rel_footprints_table.id_proyecto = $project_id";
		$sql .= " AND $module_footprint_units_table.id_cliente = $client_id";
		$sql .= " AND $project_rel_footprints_table.deleted = 0";
		$sql .= " AND $footprints_table.deleted = 0";
		$sql .= " AND $module_footprint_units_table.deleted = 0";
		$sql .= " ORDER BY $project_rel_footprints_table.id";
		
		return $this->db->query($sql);
		
	}
	*/
	
	function get_footprints_of_project_json($project_id){
	
		$project_rel_footprints_table = $this->db->dbprefix('proyecto_rel_huellas');
		$footprints_db = getFCBD();
		$footprints_table = $this->load->database(getFCBD(), TRUE)->dbprefix('huellas');
		//$footprints_db = 'dev_mimasoft_fc';
		//$footprints_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('huellas');
		
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $footprints_table.* FROM $project_rel_footprints_table, $footprints_db.$footprints_table 
		WHERE $footprints_table.id = $project_rel_footprints_table.id_huella 
		AND $project_rel_footprints_table.id_proyecto = $project_id AND $project_rel_footprints_table.deleted = 0 AND $footprints_table.deleted = 0 ORDER BY $project_rel_footprints_table.id";
		$fields_for_table = $this->db->query($sql)->result();

        $json_string = "";
        foreach ($fields_for_table as $column) {
			//$nombre_unidad_huella = $this->Unity_model->get_one($column->id_unidad)->nombre;
			$id_unidad_huella_config = $this->Module_footprint_units_model->get_one_where(array(
				"id_cliente" => $this->login_user->client_id, 
				"id_proyecto" => $this->session->project_context, 
				"id_tipo_unidad" => $column->id_tipo_unidad, 
				"deleted" => 0
			))->id_unidad;

			$nombre_unidad_huella = $this->Unity_model->get_one($id_unidad_huella_config)->nombre;
			
            $json_string .= ',' . '{"title":"' . $column->nombre . '<br /> ('.$nombre_unidad_huella.' '.$column->indicador.')", "class": "text-right dt-head-center"}';
        }

        return $json_string;
		
	}

}
