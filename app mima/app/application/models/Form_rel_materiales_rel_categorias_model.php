<?php
class Form_rel_materiales_rel_categorias_model extends Crud_model {

    private $table = null;

    function __construct() {
		$this->load->helper('database');
        $this->table = 'formulario_rel_materiales_rel_categorias';
        parent::__construct($this->table);
    }
	/* Elimina los materiales relacionados a un formulario */
    /* function delete_materials_related_to_form($form_id){
		
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');
		$sql = "DELETE FROM $form_rel_materials_table WHERE";
		$sql .= " $form_rel_materials_table.id_formulario = $form_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	} */
	
	/* Retorna los materiales relacionados a un formulario */
	/* function get_materials_related_to_form($form_id){
		
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $form_rel_materials_table.* FROM $form_rel_materials_table WHERE";
		$sql.= " $form_rel_materials_table.id_formulario = $form_id";
		
		return $this->db->query($sql);
	} */
	
	function get_categories_related_to_form($id_formulario){
		
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $formulario_rel_materiales_rel_categorias_table.* ";
		$sql .= "FROM $formulario_rel_materiales_rel_categorias_table ";
		$sql .= "WHERE $formulario_rel_materiales_rel_categorias_table.id_formulario = $id_formulario AND ";
		$sql .= "$formulario_rel_materiales_rel_categorias_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_categories_related_to_form_and_material($id_formulario, $material_id){
	
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $formulario_rel_materiales_rel_categorias_table.* FROM $formulario_rel_materiales_rel_categorias_table WHERE";
		$sql.= " $formulario_rel_materiales_rel_categorias_table.deleted = 0 AND";
		$sql.= " $formulario_rel_materiales_rel_categorias_table.id_formulario = $id_formulario AND";
		$sql.= " $formulario_rel_materiales_rel_categorias_table.id_material = $material_id";
		
		return $this->db->query($sql);
		
	}
	
	/* Elimina los materiales relacionados a un formulario */
    function delete_categories_related_to_form($form_id){
		
		$form_rel_materials_rel_categories_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$sql = "DELETE FROM $form_rel_materials_rel_categories_table WHERE";
		$sql .= " $form_rel_materials_rel_categories_table.id_formulario = $form_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
		
	}
	
	/* Retorna los nombres de las categorias de los formularios de un proyecto según su tipo de flujo (Residuo o Consumo) */
	
	function get_categories_of_form($id_proyecto, $flujo){
		
		//$materials_db = 'dev_mimasoft_fc';
		$materials_db = getFCBD();
		$categorias_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		//$categorias_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('categorias');
		$form_rel_materials_rel_categories_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$forms_table = $this->db->dbprefix('formularios');
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $form_rel_materials_rel_categories_table.*, $categorias_table.nombre, $forms_table.flujo, $forms_table.id AS id_form";
		$sql.= " FROM $form_rel_materials_rel_categories_table, $materials_db.$categorias_table, $forms_table, $form_rel_project_table";
		$sql.= " WHERE $form_rel_materials_rel_categories_table.id_categoria = $categorias_table.id";
		$sql.= " AND $form_rel_materials_rel_categories_table.id_formulario = $forms_table.id";
		$sql.= " AND $form_rel_project_table.id_proyecto = $id_proyecto";
		$sql.= " AND $forms_table.flujo = '$flujo'";
		$sql.= " GROUP BY $form_rel_materials_rel_categories_table.id";
		//echo $sql;
		
		return $this->db->query($sql);
		
	}
	
	/* Retorna los nombres de las categorias de los formularios de un proyecto independiente de su tipo de flujo */
	function get_categories_of_form_2($id_proyecto){
		
		//$materials_db = 'dev_mimasoft_fc';
		$materials_db = getFCBD();
		$categorias_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		//$categorias_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('categorias');
		$form_rel_materials_rel_categories_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$forms_table = $this->db->dbprefix('formularios');
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		
		$sql = "SELECT $form_rel_materials_rel_categories_table.*, $categorias_table.nombre, $forms_table.flujo, $forms_table.id AS id_form";
		$sql.= " FROM $form_rel_materials_rel_categories_table, $materials_db.$categorias_table, $forms_table, $form_rel_project_table";
		$sql.= " WHERE $form_rel_materials_rel_categories_table.id_categoria = $categorias_table.id";
		$sql.= " AND $form_rel_materials_rel_categories_table.id_formulario = $forms_table.id";
		$sql.= " AND $form_rel_project_table.id_proyecto = $id_proyecto";
		$sql.= " GROUP BY $form_rel_materials_rel_categories_table.id";
		//echo $sql;
		
		return $this->db->query($sql);
		
	}
	

}
