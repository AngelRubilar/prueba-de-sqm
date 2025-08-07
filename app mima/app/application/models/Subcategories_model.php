<?php

class Subcategories_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'subcategorias';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
        $subcategories_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $subcategories_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $subcategories_table.nombre=$nombre";
        }
        
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $subcategories_table.* FROM $subcategories_table WHERE";
		$sql .= " $subcategories_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	//Usada en categoría
	function get_subcategory_of_category($category_id){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		$categoria_rel_subcategoria = $this->bd_mimasoft_fc->dbprefix('categoria_rel_subcategoria');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $subcategory_table.* FROM $categories_table, $categoria_rel_subcategoria,$subcategory_table WHERE";
		$sql .= " $categories_table.deleted=0 AND $subcategory_table.deleted=0";
		$sql .= " AND $categoria_rel_subcategoria.id_subcategoria = $subcategory_table.id ";
		$sql .= " AND $categoria_rel_subcategoria.id_categoria = $categories_table.id";
		$sql .= " AND $categories_table.id = $category_id";

        return $this->bd_mimasoft_fc->query($sql);

    }
	
}
