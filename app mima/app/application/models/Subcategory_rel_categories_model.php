<?php

class Subcategory_rel_categories_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'categoria_rel_subcategoria';
        parent::__construct($this->table);
    }
	
	function get_categories_related_to_material($material_id){
	
		$material_rel_categoria_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $material_rel_categoria_table.* FROM $material_rel_categoria_table WHERE";
		$sql.= " $material_rel_categoria_table.id_material = $material_id";
		
		return $this->bd_mimasoft_fc->query($sql);
		
	}
	
	/* Elimina la relación subcategoría-categorías al borrar una categoría */
    function delete_category_rel_subcategory($id_categoria){
        
        $categoria_rel_subcategoria_table = $this->bd_mimasoft_fc->dbprefix('categoria_rel_subcategoria');
        $sql = "DELETE FROM $categoria_rel_subcategoria_table WHERE";
        $sql .= " $categoria_rel_subcategoria_table.id_categoria = $id_categoria";
        
        if($this->bd_mimasoft_fc->query($sql)){
            return true;
        } else {
            return false;
        }
    }
	
	
	
}