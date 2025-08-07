<?php

class Reports_model extends Crud_model {

    private $table = null;

    function __construct() {
        //$this->table = 'reports';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
        $users_table = $this->db->dbprefix('users');
        //$invoices_table = $this->db->dbprefix('invoices');
        //$invoice_payments_table = $this->db->dbprefix('invoice_payments');
        //$invoice_items_table = $this->db->dbprefix('invoice_items');
        //$taxes_table = $this->db->dbprefix('taxes');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $clients_table.id=$id";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $clients_table.*, contacts_table.primary_contact, contacts_table.primary_contact_id,  project_table.total_projects
        FROM $clients_table 
        LEFT JOIN (SELECT $users_table.client_id, $users_table.id AS primary_contact_id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact FROM $users_table WHERE $users_table.deleted=0 AND $users_table.is_primary_contact=1 GROUP BY $users_table.client_id, primary_contact_id) AS contacts_table ON contacts_table.client_id= $clients_table.id
        LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY client_id) AS project_table ON project_table.client_id = $clients_table.id      
        WHERE $clients_table.deleted=0 $where";

        return $this->db->query($sql);
    }
	
	function get_categories_of_project($id_cliente, $id_proyecto, $id_tipo_unidad, $id_material) {
        $formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
        $formularios_table = $this->db->dbprefix('formularios');
        $formulario_rel_materiales_table = $this->db->dbprefix('formulario_rel_materiales');
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
        
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT ";
		$sql .= "$formularios_table.*, $formulario_rel_materiales_table.id_material, $formulario_rel_materiales_rel_categorias_table.id_categoria ";
		$sql .= "FROM $formulario_rel_proyecto_table, $formularios_table, $formulario_rel_materiales_table, $formulario_rel_materiales_rel_categorias_table ";
		$sql .= "WHERE ";
		$sql .= "$formulario_rel_proyecto_table.deleted = 0 AND ";
		$sql .= "$formulario_rel_proyecto_table.id_formulario = $formularios_table.id AND ";
		$sql .= "$formulario_rel_proyecto_table.id_proyecto = $id_proyecto AND ";
		$sql .= "$formularios_table.deleted = 0 AND ";
		$sql .= "$formularios_table.id_cliente = $id_cliente AND ";
		$sql .= "$formularios_table.id_tipo_formulario = 1 AND ";
		$sql .= "$formularios_table.flujo = 'Residuo' AND ";
		$sql .= "$formularios_table.unidad LIKE '%\"tipo_unidad_id\":\"$id_tipo_unidad\"%}' AND ";
		$sql .= "$formulario_rel_materiales_table.deleted = 0 AND ";
		$sql .= "$formulario_rel_materiales_table.id_formulario = $formularios_table.id AND ";
		$sql .= "$formulario_rel_materiales_table.id_material = $id_material AND ";
		$sql .= "$formulario_rel_materiales_rel_categorias_table.deleted = 0 AND ";
		$sql .= "$formulario_rel_materiales_rel_categorias_table.id_formulario = $formularios_table.id AND ";
		$sql .= "$formulario_rel_materiales_rel_categorias_table.id_material = $formulario_rel_materiales_table.id_material ";

        return $this->db->query($sql);
    }

}
