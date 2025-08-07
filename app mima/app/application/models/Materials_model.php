<?php

class Materials_model extends Crud_bd_fc_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'materiales';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $materiales_table = $this->bd_mimasoft_fc->dbprefix('materiales');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $materiales_table.id=$id";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $materiales_table.nombre=$nombre";
        }
		
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $materiales_table.* FROM $materiales_table WHERE";
		$sql .= " $materiales_table.deleted=0";
		$sql .= " $where";
		
        return $this->bd_mimasoft_fc->query($sql);
    }
	
	/* Retorna array con los materiales pertenecientes a un formulario (id y nombre)*/
	function get_materials_of_form($form_id){
		
		$array_materiales = array();
		$array_materiales_form = array();
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $form_rel_materials_table.* from $form_rel_materials_table WHERE";
		$sql .= " $form_rel_materials_table.id_formulario = $form_id";
		$sql .= " AND $form_rel_materials_table.deleted = 0";
		
		$query = $this->db->query($sql);

		foreach($query as $row => $innerArray){
			foreach($innerArray as $innerRow => $value){
				if($value != null){
					$array_materiales["id"] = $value["id_material"];
					$array_materiales["nombre"] =  $this->get_one($value["id_material"])->nombre;
					$array_materiales_form[$innerRow] = $array_materiales;
				}	
			}
		}
				
		return $array_materiales_form;
	}
	
	function get_materials_of_project($project_id){
		
		$project_rel_materials_table = $this->db->dbprefix('materiales_proyecto');
		$materials_db = getFCBD();
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $materials_table.* FROM $materials_db.$materials_table";
		$sql .= " LEFT JOIN $project_rel_materials_table ON $materials_table.id = $project_rel_materials_table.id_material";
		$sql .= " AND $project_rel_materials_table.id_material = $materials_table.id";
		$sql .= " WHERE $project_rel_materials_table.id_proyecto = $project_id AND $project_rel_materials_table.deleted = 0 AND $materials_table.deleted = 0";
		
        return $this->db->query($sql);
	}
	
	
	function get_materials_of_projects($project_id){
		
		$materials_db = getFCBD();
		$materials_table = $this->load->database(getFCBD(), TRUE)->dbprefix('materiales');
		$project_rel_materials_table = $this->db->dbprefix('materiales_proyecto'); 
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $materials_table.* FROM $materials_db.$materials_table, $project_rel_materials_table WHERE";
		$sql .= " $materials_table.deleted=0";
		$sql .= " AND $project_rel_materials_table.deleted = 0";
		$sql .= " AND $project_rel_materials_table.id_material = $materials_table.id";
		$sql .= " AND $project_rel_materials_table.id_proyecto = $project_id";
        
        return $this->db->query($sql);
    }
	
	
	// ESTA FUNCION RETORNA LOS MATERIALES ESCOGIDOS DENTRO DE LOS FORMULARIOS QUE PERTENECEN A UN PROYECTO
	function get_materials_used_in_project($project_id){
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $form_rel_materials_table.* FROM $form_rel_project_table, $form_rel_materials_table WHERE";
		$sql .= " $form_rel_project_table.id_proyecto = $project_id";
		$sql .= " AND $form_rel_materials_table.id_formulario = $form_rel_project_table.id_formulario";
		$sql .= " AND $form_rel_project_table.deleted = 0";
		
		return $this->db->query($sql);
	}
	
	function get_material_of_category($id_categoria){
		$categories_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$materials_table = $this->bd_mimasoft_fc->dbprefix('materiales');
		$material_rel_categoria = $this->bd_mimasoft_fc->dbprefix('material_rel_categoria');
		    
        $this->bd_mimasoft_fc->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $materials_table.* FROM $categories_table, $material_rel_categoria, $materials_table WHERE";
		$sql .= " $categories_table.deleted = 0";
		$sql .= " AND $material_rel_categoria.deleted = 0";
		$sql .= " AND $materials_table.deleted = 0";
		$sql .= " AND $material_rel_categoria.id_material = $materials_table.id ";
		$sql .= " AND $material_rel_categoria.id_categoria = $categories_table.id";
		$sql .= " AND $categories_table.id = $id_categoria";

        return $this->bd_mimasoft_fc->query($sql);

    }


}
