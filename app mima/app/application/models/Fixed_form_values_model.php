<?php
class Fixed_form_values_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'valores_formularios_fijos';
        parent::__construct($this->table);
    }
	
	function get_functional_unit_value($options){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$valores_formularios_fijos_table = $this->db->dbprefix('valores_formularios_fijos');
		
		$where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where = " AND $valores_formularios_fijos_table.id = $id";
        }
		
		$id_tipo_formulario = get_array_value($options, "id_tipo_formulario");
        if($id_tipo_formulario){
            $where .= " AND $formularios_table.id_tipo_formulario = $id_tipo_formulario";
        }
		
		$id_formulario = get_array_value($options, "id_formulario");
        if($id_formulario){
            $where .= " AND $formularios_table.id = $id_formulario";
        }
		
		$id_uf = get_array_value($options, "id_uf");
		if($id_uf){
			$where .= " AND $valores_formularios_fijos_table.datos->'$.\"24\"' = \"$id_uf\""; // 24 = id de campo fijo Unidad Funcional
		}
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $valores_formularios_fijos_table.*";
		$sql .= " FROM $valores_formularios_fijos_table, $formularios_table";
		$sql .= " WHERE $valores_formularios_fijos_table.id_formulario = $formularios_table.id";
		$sql .= " AND $valores_formularios_fijos_table.deleted = 0";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " $where";
				
        return $this->db->query($sql);
		
	}

}
