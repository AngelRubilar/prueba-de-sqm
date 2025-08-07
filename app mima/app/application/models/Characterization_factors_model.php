<?php

class Characterization_factors_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        //$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
		$this->table = 'factores';
		parent::__construct($this->table);		
    }

    function get_details($options = array()) {
		/*
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		$methodology_table = $this->bd_mimasoft_fc->dbprefix('metodologia');
		$footprint_table = $this->bd_mimasoft_fc->dbprefix('huellas');
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		$category_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $factors_table.id=$id";
        }
		
		$id_metodologia = get_array_value($options, "id_metodologia");
        if ($id_metodologia) {
            $where .= " AND $factors_table.id_metodologia=$id_metodologia";
        }
		
		$id_huella = get_array_value($options, "id_huella");
        if ($id_huella) {
            $where .= " AND $factors_table.id_huella=$id_huella";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $where .= " AND $factors_table.id_material=$id_material";
        }
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		
		$sql = " SELECT $factors_table.id, $factors_table.id_tipo_unidad, $factors_table.id_unidad, $methodology_table.nombre AS nombre_metodologia, $materials_table.nombre AS nombre_material, $footprint_table.nombre AS
				nombre_huella, $category_table.nombre AS nombre_categoria, $subcategory_table.nombre AS nombre_subcategoria, 
				$factors_table.factor FROM $factors_table, $materials_table, $methodology_table, $footprint_table, $category_table, $subcategory_table WHERE";
		$sql .= " $factors_table.deleted=0";
		$sql .= " AND $factors_table.id_metodologia = $methodology_table.id";
		$sql .= " AND $factors_table.id_huella = $footprint_table.id";
		$sql .= " AND $factors_table.id_categoria = $category_table.id";
		$sql .= " AND $factors_table.id_subcategoria = $subcategory_table.id";
		$sql .= " AND $factors_table.id_material = $materials_table.id";
		//$sql .= " $where LIMIT 6250";
		$sql .= " $where ";
		
		set_time_limit(80);
        return $this->bd_mimasoft_fc->query($sql); 
		*/
		
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		$methodology_table = $this->bd_mimasoft_fc->dbprefix('metodologia');
		$footprint_table = $this->bd_mimasoft_fc->dbprefix('huellas');
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		$category_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		
		$extra_join = "";
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $factors_table.id=$id";
        }
		
		$id_metodologia = get_array_value($options, "id_metodologia");
        if ($id_metodologia) {
            $extra_join .= " AND $factors_table.id_metodologia = $id_metodologia";
        }
		
		$id_huella = get_array_value($options, "id_huella");
        if ($id_huella) {
            $extra_join .= " AND $factors_table.id_huella = $id_huella";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $extra_join .= " AND $factors_table.id_material = $id_material";
        }
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		
		$sql = " SELECT $factors_table.id, $factors_table.id_tipo_unidad, $factors_table.id_unidad, $methodology_table.nombre AS nombre_metodologia, $materials_table.nombre AS nombre_material, $footprint_table.nombre AS	nombre_huella, $category_table.nombre AS nombre_categoria, $subcategory_table.nombre AS nombre_subcategoria, $factors_table.factor FROM $factors_table";
		
		$sql .= " INNER JOIN $methodology_table ON $factors_table.id_metodologia = $methodology_table.id";
		$sql .= " INNER JOIN $footprint_table ON $factors_table.id_huella = $footprint_table.id";
		$sql .= " INNER JOIN $category_table ON $factors_table.id_categoria = $category_table.id";
		$sql .= " INNER JOIN $subcategory_table ON $factors_table.id_subcategoria = $subcategory_table.id";
		$sql .= " INNER JOIN $materials_table ON $factors_table.id_material = $materials_table.id";
		$sql .= " $extra_join ";
		$sql .= " WHERE 1 $where ";
		$sql .= " AND $factors_table.deleted=0";
		set_time_limit(100);
		
        return $this->bd_mimasoft_fc->query($sql); 

    }
	
	function is_factor_exists($data, $id = 0) {
        $result = $this->get_all_where($data);
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }
	
	function get_details2($options = array()) {
		
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		$footprint_format_table = $this->bd_mimasoft_fc->dbprefix('formatos_huella');
		$methodology_table = $this->bd_mimasoft_fc->dbprefix('metodologia');
		$databases_table = $this->bd_mimasoft_fc->dbprefix('bases_de_datos');
		$footprint_table = $this->bd_mimasoft_fc->dbprefix('huellas');
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		$category_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$subcategory_table = $this->bd_mimasoft_fc->dbprefix('subcategorias');
		$unidad_table = $this->bd_mimasoft_fc->dbprefix('unidad');
		
		$extra_join = "";
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $factors_table.id=$id";
        }
		
		$id_metodologia = get_array_value($options, "id_metodologia");
        if ($id_metodologia) {
            $extra_join .= " AND $factors_table.id_metodologia = $id_metodologia";
        }
		
		$id_bd = get_array_value($options, "id_bd");
        if ($id_bd) {
            $extra_join .= " AND $factors_table.id_bd = $id_bd";
        }
		
		$id_huella = get_array_value($options, "id_huella");
        if ($id_huella) {
            $extra_join .= " AND $factors_table.id_huella = $id_huella";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $extra_join .= " AND $factors_table.id_material = $id_material";
        }
		
		$id_formato_huella = get_array_value($options, "id_formato_huella");
        if ($id_formato_huella) {
            $extra_join .= " AND $factors_table.id_formato_huella = $id_formato_huella";
        }
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		
		$sql = " SELECT $factors_table.id, $factors_table.id_tipo_unidad, $unidad_table.nombre AS nombre_unidad, $methodology_table.nombre AS nombre_metodologia, $footprint_format_table.nombre AS nombre_formato_huella, $databases_table.nombre AS nombre_bd, $materials_table.nombre AS nombre_material, $footprint_table.nombre AS nombre_huella, $category_table.nombre AS nombre_categoria, $subcategory_table.nombre AS nombre_subcategoria, $factors_table.factor, $factors_table.created, $factors_table.modified FROM $factors_table";
		
		$sql .= " INNER JOIN $footprint_format_table ON $factors_table.id_formato_huella = $footprint_format_table.id";
		$sql .= " INNER JOIN $methodology_table ON $factors_table.id_metodologia = $methodology_table.id";
		$sql .= " INNER JOIN $databases_table ON $factors_table.id_bd = $databases_table.id";
		$sql .= " INNER JOIN $footprint_table ON $factors_table.id_huella = $footprint_table.id";
		$sql .= " INNER JOIN $category_table ON $factors_table.id_categoria = $category_table.id";
		$sql .= " INNER JOIN $subcategory_table ON $factors_table.id_subcategoria = $subcategory_table.id";
		$sql .= " INNER JOIN $materials_table ON $factors_table.id_material = $materials_table.id";
		$sql .= " INNER JOIN $unidad_table ON factores.id_unidad = $unidad_table.id";
		$sql .= " $extra_join";
		$sql .= " WHERE 1 $where";
		$sql .= " AND $factors_table.deleted=0";
		
		set_time_limit(100);
        return $this->bd_mimasoft_fc->query($sql); 

    }
	
	function get_databases_of_fc($options = array()) {
		
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		$databases_table = $this->bd_mimasoft_fc->dbprefix('bases_de_datos');
		
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $factors_table.id=$id";
        }
		
		$id_bd = get_array_value($options, "id_bd");
        if ($id_bd) {
            $where .= " AND $factors_table.id_bd = $id_bd";
        }
		
		$id_formato_huella = get_array_value($options, "id_formato_huella");
        if ($id_metodologia) {
            $where .= " AND $factors_table.id_formato_huella = $id_formato_huella";
        }
		
		$id_metodologia = get_array_value($options, "id_metodologia");
        if ($id_metodologia) {
            $where .= " AND $factors_table.id_metodologia = $id_metodologia";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $where .= " AND $factors_table.id_material = $id_material";
        }
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $databases_table.* FROM $factors_table ";
		$sql .= " LEFT JOIN $databases_table ON $factors_table.id_bd = $databases_table.id";
		$sql .= " WHERE 1 $where";
		$sql .= " GROUP by $databases_table.id";
		
        return $this->bd_mimasoft_fc->query($sql); 

    }
	
	function get_categories_of_fc($options = array()) {
		
		$factors_table = $this->bd_mimasoft_fc->dbprefix('factores');
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		
		$where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $factors_table.id=$id";
        }
		
		$id_bd = get_array_value($options, "id_bd");
        if ($id_bd) {
            $where .= " AND $factors_table.id_bd = $id_bd";
        }
		
		$id_formato_huella = get_array_value($options, "id_formato_huella");
        if ($id_metodologia) {
            $where .= " AND $factors_table.id_formato_huella = $id_formato_huella";
        }
		
		$id_metodologia = get_array_value($options, "id_metodologia");
        if ($id_metodologia) {
            $where .= " AND $factors_table.id_metodologia = $id_metodologia";
        }
		
		$id_material = get_array_value($options, "id_material");
        if ($id_material) {
            $where .= " AND $factors_table.id_material = $id_material";
        }
		
		$this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $categories_table.* FROM $factors_table ";
		$sql .= " LEFT JOIN $categories_table ON $factors_table.id_categoria = $categories_table.id";
		$sql .= " WHERE 1 $where";
		$sql .= " GROUP by $categories_table.id";
		
        return $this->bd_mimasoft_fc->query($sql); 

    }
	

}
