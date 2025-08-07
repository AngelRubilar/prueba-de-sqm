<?php
class Indicators_model extends Crud_model{
	
	
	private $table = null;
	
	function __construct(){
		
        $this->table = 'indicators';
        parent::__construct($this->table);
		
	}
	
	function get_details($options = array()){
		
		$indicators_table = $this->db->dbprefix('indicators');
		
        $where = "";
		
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $indicators_table.id=$id";
        }
		
		$id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $indicators_table.id_client=$id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $indicators_table.id_project=$id_project";
        }

        $sql = "SELECT $indicators_table.*
        FROM $indicators_table   
        WHERE $indicators_table.deleted=0 $where";
		
		return $this->db->query($sql);
		
	}
	
	
	function get_categories_type_waste($form_id){
		
		$mima_fc = getFCBD();
		$categorias_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias'); 
		//$formularios = $this->db->dbprefix('formularios');
		 
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categorias_table.* FROM $mima_fc.$categorias_table, $formulario_rel_materiales_rel_categorias_table WHERE";
		$sql .= " $categorias_table.deleted=0";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_categoria= $categorias_table.id";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_formulario = $form_id";

        return $this->db->query($sql);
	}
	
	
	function delete_indicators($id){
		
		$indicators = $this->db->dbprefix('indicators');

		$sql = "UPDATE $indicators SET $indicators.deleted=1 WHERE $indicators.id=$id; ";
		$this->db->query($sql);
	}

}