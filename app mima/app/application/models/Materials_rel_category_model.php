<?php

class Materials_rel_category_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'material_rel_categoria';
        parent::__construct($this->table);
    }
	
	function get_categories_related_to_material($material_id){
	
		$material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $material_rel_categoria_table.* FROM $material_rel_categoria_table WHERE";
		$sql.= " $material_rel_categoria_table.id_material = $material_id";
		
		return $this->bd_mimasoft_fc->query($sql);
		
	}
	
	function get_categories_related_to_form_and_material($id_formulario, $material_id){
	
		$material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $formulario_rel_materiales_rel_categorias.* FROM $formulario_rel_materiales_rel_categorias WHERE";
		$sql.= " $formulario_rel_materiales_rel_categorias.id_formulario = $id_formulario AND";
		$sql.= " $formulario_rel_materiales_rel_categorias.id_material = $material_id";
		
		return $this->bd_mimasoft_fc->query($sql);
		
	}
	
	/* Elimina la relación material-categoría al borrar una categoria */
    function delete_material_rel_category($id_categoria){
        
        $material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
        $sql = "DELETE FROM $material_rel_categoria_table WHERE";
        $sql .= " $material_rel_categoria_table.id_categoria = $id_categoria";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	/* Elimina la relación material-categoría al borrar un material */
    function delete_material_rel_category2($id_material){
        
        $material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
        $sql = "DELETE FROM $material_rel_categoria_table WHERE";
        $sql .= " $material_rel_categoria_table.id_material = $id_material";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	function get_other_categories_used_in_materials($id_material){
		
		$material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $material_rel_categoria_table.* FROM $material_rel_categoria_table WHERE";
		$sql .= " $material_rel_categoria_table.deleted=0";
		$sql .= " AND $material_rel_categoria_table.id_material != $id_material";
		
		return $this->bd_mimasoft_fc->query($sql);
	}
	
}