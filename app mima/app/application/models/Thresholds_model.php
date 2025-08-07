<?php

class Thresholds_model extends Crud_model {

	
    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'thresholds';
        parent::__construct($this->table);
    }
	
	function get_details($options = array()){
		
		$thresholds_table = $this->db->dbprefix('thresholds');
		
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $thresholds_table.id=$id";
        }
		
		$id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $thresholds_table.id_client=$id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $thresholds_table.id_project=$id_project";
        }
		
		$id_module = get_array_value($options, "id_module");
        if ($id_module) {
            $where .= " AND $thresholds_table.id_module=$id_module";
        }
		
		$id_form = get_array_value($options, "id_form");
        if ($id_form) {
            $where .= " AND $thresholds_table.id_form=$id_form";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $where .= " AND $thresholds_table.id_material=$id_material";
        }

        $sql = "SELECT $thresholds_table.*
        FROM $thresholds_table   
        WHERE $thresholds_table.deleted=0 $where";
		
		return $this->db->query($sql);
		
	}
	
	function get_material_flow_project($id_project, $flujo){
		
		$dev_mimasoft_fc2 = getFCBD();
		$materiales = $this->load->database(getFCBD(), TRUE)->dbprefix('materiales');
		$formularios = $this->db->dbprefix('formularios');
		$projects = $this->db->dbprefix('projects');
		$formulario_rel_materiales = $this->db->dbprefix('formulario_rel_materiales');
		$formulario_rel_proyecto = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $materiales.id AS id_material, $materiales.nombre AS nombre_material, $formularios.flujo,";
		$sql .= " $projects.id AS id_project, $formularios.id AS id_formulario";
		$sql .=" FROM $dev_mimasoft_fc2.$materiales, $formulario_rel_materiales, $formularios, $formulario_rel_proyecto,$projects";
		$sql .=" WHERE $materiales.id = $formulario_rel_materiales.id_material";
		$sql .=" AND $formulario_rel_materiales.id_formulario = $formularios.id";
		$sql .=" AND $formulario_rel_proyecto.id_formulario = $formularios.id";
		$sql .=" AND $formulario_rel_proyecto.id_proyecto = $projects.id";
		$sql .=" AND $formularios.flujo = '$flujo'";
		$sql .=" AND $formularios.deleted = 0";
		$sql .=" AND $projects.id = $id_project";
		$sql .=" AND $materiales.deleted = 0";
		
		return $this->db->query($sql);
	}
	
	
	function delete_thresholds($id){
		
		$thresholds = $this->db->dbprefix('thresholds');

		$sql = "UPDATE $thresholds SET $thresholds.deleted=1 WHERE $thresholds.id=$id; ";
		$this->db->query($sql);
		
	}
	
}
