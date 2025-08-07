<?php

class Categories_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'categorias';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
        $categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$mat_rel_cat_table= $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$material_table= $this->bd_mimasoft_fc->dbprefix('materiales');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $categories_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $categories_table.nombre=$nombre";
        }
        
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_table.*  FROM $categories_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " $where";
		return $this->bd_mimasoft_fc->query($sql);
		
		/*$sql = "SELECT $categories_table.*, $material_table.nombre AS material
		FROM $categories_table ";
		$sql .= " LEFT JOIN $mat_rel_cat_table ON $categories_table.id = $mat_rel_cat_table.id_categoria";
		$sql .= " LEFT JOIN $material_table ON $mat_rel_cat_table.id_material= $material_table.id";
		$sql .= " WHERE";
		$sql .= " $categories_table.deleted=0"; 
		$sql .= " $where";*/
		
		/*
		SELECT categorias.*, materiales.nombre as material
		FROM categorias
		LEFT JOIN material_rel_categoria ON categorias.id = material_rel_categoria.id_categoria
		LEFT JOIN materiales ON material_rel_categoria.id_material= materiales.id
		WHERE
		categorias.deleted=0*/
		
    }
	
	//Usada en material
	function get_category_of_material($material_id){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		$material_rel_categoria = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_table.* FROM $categories_table, $material_rel_categoria,$materials_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " AND $material_rel_categoria.id_material = $materials_table.id ";
		$sql .= " AND $material_rel_categoria.id_categoria = $categories_table.id";
		$sql .= " AND $materials_table.id = $material_id";

        return $this->bd_mimasoft_fc->query($sql);

    }
	
	//Usada en subcategoría
	function get_category_of_subcategory($subcategory_id){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		$categoria_rel_subcategoria = $this->bd_mimasoft_fc->dbprefix('categoria_rel_subcategoria');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_table.* FROM $categories_table, $categoria_rel_subcategoria,$subcategory_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " AND $categoria_rel_subcategoria.id_subcategoria = $subcategory_table.id ";
		$sql .= " AND $categoria_rel_subcategoria.id_categoria = $categories_table.id";
		$sql .= " AND $subcategory_table.id = $subcategory_id";

        return $this->bd_mimasoft_fc->query($sql);

    }
	
	function get_subcategories_of_category($category_id){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		$categoria_rel_subcategoria = $this->bd_mimasoft_fc->dbprefix('categoria_rel_subcategoria');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $subcategory_table.* FROM $categories_table, $categoria_rel_subcategoria,$subcategory_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " AND $categoria_rel_subcategoria.id_subcategoria = $subcategory_table.id ";
		$sql .= " AND $categoria_rel_subcategoria.id_categoria = $categories_table.id";
		$sql .= " AND $categories_table.id = $category_id";

        return $this->bd_mimasoft_fc->query($sql);

    }
	
	function get_category_of_factor($id){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_table.* FROM $categories_table, $factors_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " AND $categories_table.id = $factors_table.id_categoria ";
		$sql .= " AND $factors_table.id = $id";

        return $this->bd_mimasoft_fc->query($sql);

    }
	
	
	//Usada en RA
	function get_categories_of_material_of_form($form_id){
		
		//$mima_fc = 'dev_mimasoft_fc';
		//$categorias_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('categorias');
		$mima_fc = getFCBD();
		$categorias_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias'); 
		 
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categorias_table.* FROM $mima_fc.$categorias_table, $formulario_rel_materiales_rel_categorias_table WHERE";
		$sql .= " $categorias_table.deleted=0";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_categoria= $categorias_table.id";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_formulario = $form_id";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.deleted = 0";
		
		
		/*SELECT categorias.* FROM dev_mimasoft_fc.categorias, dev_mimasoft_sistema.formulario_rel_materiales_rel_categorias
        Where formulario_rel_materiales_rel_categorias.id_categoria= categorias.id
        AND formulario_rel_materiales_rel_categorias.id_formulario= 10
       */
	        
        return $this->db->query($sql);
	}

	function is_category_name_exists($category_name, $id = 0) {
        $result = $this->get_all_where(array("nombre" => $category_name, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }
	/*
	function get_categories_of_material($id_material){
		$dev_mimasoft_fc2 = getFCBD();
		$materiales = $this->load->database(getFCBD(), TRUE)->dbprefix('materiales');
		$categorias = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		$material_rel_categoria = $this->load->database(getFCBD(), TRUE)->dbprefix('material_rel_categoria');
		
		$sql =" SELECT $categorias.*";
		$sql .=" FROM $dev_mimasoft_fc2.$categorias, $dev_mimasoft_fc2.$materiales, $dev_mimasoft_fc2.$material_rel_categoria";
		$sql .=" WHERE $materiales.id = $id_material";
		$sql .=" AND $materiales.id = $material_rel_categoria.id_material";
		$sql .=" AND $categorias.id = $material_rel_categoria.id_categoria";
		$sql .=" AND $categorias.deleted = 0";
		
		
	}
	*/
	
	function get_categories_for_indicators_filter(){
		
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		
		$sql = "SELECT $categories_table.*";
		$sql .= " FROM $categories_table";
		$sql .= " WHERE $categories_table.id IN (29, 30, 31, 32, 33)";
		$sql .= " AND $categories_table.deleted = 0";
		
		return $this->bd_mimasoft_fc->query($sql);
		
	}
	
	/*
		Función que devuelve las categorías de los materiales seleccionados 
		en los proyectos de un cliente.
	*/
	function get_categories_of_materials_client_projects($id_cliente){
		
		/*
		$mima_fc = getFCBD();
		$projects_table = $this->db->dbprefix('projects');
		$project_materials_table = $this->db->dbprefix('materiales_proyecto');
		$material_rel_category_table = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		 
		$sql .= "SELECT $projects_table.id AS id_proyecto, $categories_table.id AS id_categoria, $categories_table.nombre AS nombre_categoria";
		$sql .= " FROM $projects_table, $project_materials_table, $mima_fc.$material_rel_category_table, $mima_fc.$categories_table";
		$sql .= " WHERE $project_materials_table.id_proyecto = $projects_table.id";
		$sql .= " AND $project_materials_table.id_material = $material_rel_category_table.id_material";
		$sql .= " AND $material_rel_category_table.id_categoria = $categories_table.id";
		$sql .= " AND $projects_table.client_id = $id_cliente";
		$sql .= " AND $projects_table.deleted = 0";
		$sql .= " AND $project_materials_table.deleted = 0";
		$sql .= " GROUP BY id_categoria";
		*/
		
		$mima_fc = getFCBD();
		$projects_table = $this->db->dbprefix('projects');
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$forms_table = $this->db->dbprefix('formularios');
		$forms_rel_projects_table = $this->db->dbprefix('formulario_rel_proyecto');
		$forms_rel_mat_rel_cat_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = " SELECT $categories_table.id AS id_categoria, $categories_table.nombre AS nombre_categoria,";
		$sql .= " $forms_table.id AS id_formulario, $forms_table.unidad->'$.tipo_unidad_id' AS id_tipo_unidad";
		$sql .= " FROM $projects_table, $mima_fc.categorias, $forms_rel_projects_table, $forms_rel_mat_rel_cat_table, $forms_table";
		$sql .= " WHERE $forms_rel_projects_table.id_proyecto = $projects_table.id";
		$sql .= " AND $forms_rel_projects_table.id_formulario = $forms_table.id";
		$sql .= " AND $forms_rel_mat_rel_cat_table.id_formulario = $forms_table.id";
		$sql .= " AND $forms_rel_mat_rel_cat_table.id_categoria = $categories_table.id";
		$sql .= " AND $forms_table.id_tipo_formulario = 1"; // Registro Ambiental
		$sql .= " AND $projects_table.client_id = $id_cliente";
		//$sql .= " AND $forms_table.unidad->'$.tipo_unidad_id' IN (2, 3, 4, 9)";
		$sql .= " AND $forms_table.unidad->'$.tipo_unidad_id' IN (1, 2, 3, 4, 9)";
		$sql .= " AND $projects_table.deleted = 0";
		$sql .= " AND $forms_table.deleted = 0";
		$sql .= " AND $forms_rel_mat_rel_cat_table.deleted = 0";
		$sql .= " GROUP BY id_categoria, id_tipo_unidad, id_formulario";
				
		return $this->db->query($sql);

	}
	
}
