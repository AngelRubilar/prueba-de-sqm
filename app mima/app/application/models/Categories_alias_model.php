<?php

class Categories_alias_model extends Crud_model {

    private $table;
	private $bd_mimasoft_fc;

    function __construct() {
		$this->load->helper('database');
		//$this->bd_mimasoft_fc = $this->load->database('dev_mimasoft_fc', TRUE);
        $this->table = 'categorias_alias';
        parent::__construct($this->table);
    }

	function get_details($options = array()) {
		
		$clients_table = $this->db->dbprefix('clients');
		$categories_alias_table = $this->db->dbprefix('categorias_alias');
		
		$categories_db = getFCBD();
		//$categories_db = 'dev_mimasoft_fc';
		$categories_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		//$categories_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('categorias');
		
		$where = "";
        $id = get_array_value($options, "id");
        if($id) {
            $where .= " AND $categories_alias_table.id=$id";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $categories_alias_table.id_cliente=$id_cliente";
        }
		
		$id_categoria = get_array_value($options, "id_categoria");
        if ($id_categoria) {
            $where .= " AND $categories_alias_table.id_categoria=$id_categoria";
        }
		    
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_alias_table.*, $clients_table.company_name as cliente, $categories_table.nombre as categoria";
		$sql .= " FROM $clients_table, $categories_db.$categories_table, $categories_alias_table WHERE";
		$sql .= " $clients_table.deleted=0";
		$sql .= " AND $categories_table.deleted=0";
		$sql .= " AND $categories_alias_table.deleted=0";
		$sql .= " AND $clients_table.id = $categories_alias_table.id_cliente";
		$sql .= " AND $categories_table.id = $categories_alias_table.id_categoria";
		$sql .= " $where ";
		//SELECT ca.*, c.company_name, cats.nombre FROM clients c, dev_mimasoft_fc.categorias cats, categorias_alias ca WHERE c.id = ca.id_cliente, dev_mimasoft_fc.cats.id = ca.id_categoria
		return $this->db->query($sql);
		
    }
	
	function get_categories_related_to_client($client_id){
		
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		$formulario_rel_materiales_table = $this->db->dbprefix('formulario_rel_materiales');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		$projects_table = $this->db->dbprefix('projects');
		
		
		$categories_db = getFCBD();
		$categories_table = $this->load->database(getFCBD(), TRUE)->dbprefix('categorias');
		//$categories_db = 'dev_mimasoft_fc';
		//$categories_table = $this->load->database('dev_mimasoft_fc', TRUE)->dbprefix('categorias');
		    
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $categories_table.*";
		$sql .= " FROM $categories_db.$categories_table, $formulario_rel_materiales_rel_categorias_table, $formulario_rel_proyecto_table, $projects_table WHERE";
		$sql .= " $categories_table.deleted=0";
		$sql .= " AND $categories_table.id = $formulario_rel_materiales_rel_categorias_table.id_categoria ";
		//$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_formulario_rel_materiales = $formulario_rel_materiales_table.id";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_formulario = $formulario_rel_proyecto_table.id_formulario";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.client_id = $client_id ";
		
		//echo $sql;
		
		//SELECT * FROM dev_mimasoft_fc.categorias c, formulario_rel_materiales_rel_categorias fmc, formulario_rel_materiales fm, formulario_rel_proyecto fp, projects p WHERE c.id = fmc.id_categoria AND fmc.id_formulario_rel_materiales = fm.id AND fm.id_formulario = fp.id_formulario AND fp.id_proyecto = p.id AND p.client_id = 1

        return $this->db->query($sql);

    }
	
	function is_alias_exists($data, $id = 0) {
        $result = $this->get_all_where($data);
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

	
}
