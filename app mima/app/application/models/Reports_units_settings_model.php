<?php

class Reports_units_settings_model extends Crud_model {

    private $table = null;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'reports_units_settings';
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        parent::__construct($this->table);
    }
	
	function delete_reports_units_settings($project_id, $client_id){
		
		$reports_units_settings_table = $this->db->dbprefix('reports_units_settings');
		$sql = "DELETE FROM $reports_units_settings_table WHERE";
		$sql .= " $reports_units_settings_table.id_proyecto = $project_id";
		$sql .= " AND $reports_units_settings_table.id_cliente = $client_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	function save_default_settings($id_cliente, $id_proyecto){
		$tipos_de_unidad = $this->Unity_type_model->get_all()->result();
		
		foreach($tipos_de_unidad as $tipo_unidad){
			
			$unidad = $this->Unity_model->get_one_where(array("id_tipo_unidad" => $tipo_unidad->id));
			
			$default_report_units = array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_tipo_unidad" => $tipo_unidad->id,
				"id_unidad" => $unidad->id,
			);

			$this->save($default_report_units);
			
		}
	
	}
	
	function delete_reports_units_settings_by_project($id){
		
		$reports_units_settings = $this->db->dbprefix('reports_units_settings');

		$sql = "UPDATE $reports_units_settings SET $reports_units_settings.deleted=1 WHERE $reports_units_settings.id=$id; ";
		$this->db->query($sql);
	}

	function get_units(array $options){

		$fc_bd = getFCBD();
		// $unit_table = $this->load->database(getFCBD(), TRUE)->dbprefix('unidad');
		$reports_units_settings = $this->db->dbprefix('reports_units_settings');
		$unit_table = $this->bd_mimasoft_fc->dbprefix('unidad');

		$where = "";
        $id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $reports_units_settings.id_cliente = $id_cliente";
        }

		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $reports_units_settings.id_proyecto = $id_proyecto";
        }

		$id_tipo_unidad = get_array_value($options, "id_tipo_unidad");
        if ($id_tipo_unidad) {
            $where .= " AND $reports_units_settings.id_tipo_unidad = $id_tipo_unidad";
        }

		$sql = "SELECT $reports_units_settings.id AS id_unidad_reporte, $fc_bd.$unit_table.* ";
		$sql .= " FROM $reports_units_settings"; 
		$sql .= " LEFT JOIN $fc_bd.$unit_table ON $reports_units_settings.id_unidad = $fc_bd.$unit_table.id";
		$sql .= " WHERE $reports_units_settings.deleted = 0";
		$sql .= $where;
		$sql .= " AND $fc_bd.$unit_table.deleted = 0";

		return $this->db->query($sql);
	}
	

}