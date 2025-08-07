<?php

class Clients_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'clients';
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
            $where .= " AND $clients_table.id=$id";
        }
		
		$habilitado = get_array_value($options, "habilitado");
        if ($habilitado) {
			if($habilitado == "activo"){
				$where .= " AND $clients_table.habilitado=1";
			}
			if($habilitado == "inactivo"){
				$where .= " AND $clients_table.habilitado=0";
			} 
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1'); 
        
        $sql = "SELECT $clients_table.*, contacts_table.primary_contact, contacts_table.primary_contact_id,  project_table.total_projects
        FROM $clients_table 
        LEFT JOIN (SELECT $users_table.client_id, $users_table.id AS primary_contact_id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact FROM $users_table WHERE $users_table.deleted=0 AND $users_table.is_primary_contact=1 GROUP BY $users_table.client_id, primary_contact_id) AS contacts_table ON contacts_table.client_id= $clients_table.id
        LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY client_id) AS project_table ON project_table.client_id = $clients_table.id      
        WHERE $clients_table.deleted=0 $where";

		
        return $this->db->query($sql);
    }
	
	function is_sigla_exists($sigla, $id = 0) {
        $result = $this->get_all_where(array("sigla" => $sigla, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }
	function is_company_name_exists($company_name, $id = 0) {
        $result = $this->get_all_where(array("company_name" => $company_name, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

}
