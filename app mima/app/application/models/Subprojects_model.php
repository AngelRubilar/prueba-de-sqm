<?php

class Subprojects_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'subproyectos';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
		
        $subprojects_table = $this->db->dbprefix('subproyectos');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $subprojects_table.id=$id";
        }
		
		$name = get_array_value($options, "nombre");
        if ($name) {
            $where .= " AND $subprojects_table.name=$name";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $subprojects_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $subprojects_table.id_proyecto=$id_proyecto";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $subprojects_table.created_by=$created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $subprojects_table.* FROM $subprojects_table WHERE";
		$sql .= " $subprojects_table.deleted=0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }
	
	function is_subproject_name_exists($nombre, $id_cliente, $id_proyecto, $id = 0) {
        $result = $this->get_all_where(array("nombre" => $nombre, "id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

	function delete_subprojects($id){

		$subproyectos = $this->db->dbprefix('subproyectos');

		$sql = "UPDATE $subproyectos SET $subproyectos.deleted=1 WHERE $subproyectos.id=$id; ";
		$this->db->query($sql);

	}

}
