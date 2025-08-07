<?php

class Fields_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'campos';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $fields_table = $this->db->dbprefix('campos');
		$field_types_table = $this->db->dbprefix('tipo_campo');
		$projects_table = $this->db->dbprefix('projects');
		$users_table = $this->db->dbprefix('users');
		
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $fields_table.id=$id";
        }
		
		$id_tipo_campo = get_array_value($options, "id_tipo_campo");
        if ($id_tipo_campo) {
            $where .= " AND $fields_table.id_tipo_campo=$id_tipo_campo";
        }
		
		$id_cliente = get_array_value($options, "id_cliente");
        if ($id_cliente) {
            $where .= " AND $fields_table.id_cliente=$id_cliente";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $fields_table.id_proyecto=$id_proyecto";
        }
		
		$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $fields_table.nombre='$nombre'";
        }
		
		$html_name = get_array_value($options, "html_name");
        if ($html_name) {
            $where .= " AND $fields_table.html_name='$html_name'";
        }
		
		$obligatorio = get_array_value($options, "obligatorio");
        if ($obligatorio) {
            $where .= " AND $fields_table.obligatorio=$obligatorio";
        }
		
		$habilitado = get_array_value($options, "habilitado");
        if ($habilitado) {
            $where .= " AND $fields_table.habilitado=$habilitado";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $fields_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $fields_table.modified_by=$modified_by";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $fields_table.*, $field_types_table.nombre AS tipo_campo, $projects_table.title AS proyecto,  CONCAT($users_table.first_name, ' ', $users_table.last_name) AS creado_por FROM $fields_table, $field_types_table, $projects_table, $users_table WHERE";
		$sql .= " $fields_table.deleted=0";
		$sql .= " AND $field_types_table.id = $fields_table.id_tipo_campo";
		$sql .= " AND $projects_table.id = $fields_table.id_proyecto";
		$sql .= " AND $users_table.id = $fields_table.created_by";
		$sql .= " $where";
		
        return $this->db->query($sql);
    }
	
	function get_details_api($options = array()) {
        $fields_table = $this->db->dbprefix('campos');
		$field_types_table = $this->db->dbprefix('tipo_campo');
		$projects_table = $this->db->dbprefix('projects');
		$users_table = $this->db->dbprefix('users');
		//
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');

        $where = "";
        /*$id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $fields_table.id=$id";
        }
		
		$id_tipo_campo = get_array_value($options, "id_tipo_campo");
        if ($id_tipo_campo) {
            $where .= " AND $fields_table.id_tipo_campo=$id_tipo_campo";
        }
		
		$id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $fields_table.id_proyecto=$id_proyecto";
        }*/
		//EN PRUEBA
		$id_formulario = get_array_value($options, "id_formulario");
        if ($id_formulario) {
            $where .= " AND $field_rel_form_table.id_formulario = $id_formulario";
        }
		//FIN EN PRUEBA
		/*$nombre = get_array_value($options, "nombre");
        if ($nombre) {
            $where .= " AND $fields_table.nombre='$nombre'";
        }
		
		$html_name = get_array_value($options, "html_name");
        if ($html_name) {
            $where .= " AND $fields_table.html_name='$html_name'";
        }
		
		$obligatorio = get_array_value($options, "obligatorio");
        if ($obligatorio) {
            $where .= " AND $fields_table.obligatorio=$obligatorio";
        }
		
		$habilitado = get_array_value($options, "habilitado");
        if ($habilitado) {
            $where .= " AND $fields_table.habilitado=$habilitado";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $fields_table.created_by=$created_by";
        }
		
		$modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $fields_table.modified_by=$modified_by";
        }*/
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		//EN PRUEBA
        $sql = "SELECT $fields_table.*, $field_types_table.nombre AS tipo_campo, $projects_table.title AS proyecto,  CONCAT($users_table.first_name, ' ', $users_table.last_name) AS creado_por FROM $fields_table, $field_types_table, $projects_table, $users_table, $field_rel_form_table WHERE";
		//FIN EN PRUEBA
		$sql .= " $fields_table.deleted=0";
		$sql .= " AND $field_types_table.id = $fields_table.id_tipo_campo";
		$sql .= " AND $projects_table.id = $fields_table.id_proyecto";
		$sql .= " AND $users_table.id = $fields_table.created_by";
		$sql .= " $where";
		//$sql .= " LIMIT 5";
		
        return $this->db->query($sql);
    }
	
	function get_fields_of_projects_where_not($id_project, $options = array()){
		
		$fields_table = $this->db->dbprefix('campos');
		$where = "";
		
		$id_tipo_campo = get_array_value($options, "id_tipo_campo");
        if ($id_tipo_campo) {
            $where .= " AND $fields_table.id_tipo_campo != $id_tipo_campo";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.* FROM $fields_table WHERE";
		$sql .= " $fields_table.deleted = 0 AND";
		$sql .= " $fields_table.id_proyecto = $id_project";
		$sql .= " $where";
		$sql .= " ORDER BY $fields_table.nombre ASC";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_form($form_id) {
        $form_table = $this->db->dbprefix('formularios');
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		//SELECT c.* FROM campos c, campo_rel_formulario crf, formularios f WHERE c.id = crf.id_campo AND crf.id_formulario = f.id AND f.id = 
        $sql = "SELECT $fields_table.* FROM $form_table, $field_rel_form_table, $fields_table WHERE";
		$sql .= " $fields_table.deleted=0";
		$sql .= " AND $fields_table.id = $field_rel_form_table.id_campo";
		$sql .= " AND $field_rel_form_table.id_formulario = $form_table.id";
		$sql .= " AND $form_table.id = $form_id";
		
        return $this->db->query($sql);
    }
	
	function get_fields_of_feeder($id_tipo_campo, $id_mantenedora){
		
		$fields_table = $this->db->dbprefix('campos');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.* ";
		$sql .= "FROM $fields_table WHERE";
		$sql .= " $fields_table.id_tipo_campo = $id_tipo_campo";
		$sql .= " AND $fields_table.default_value LIKE '%\"mantenedora\":\"$id_mantenedora\"%'";
		
		return $this->db->query($sql);
	
	}
	
	function get_unity_fields_of_ra($id_cliente, $id_proyecto, $flujo){
		
		$form_rel_project_table = $this->db->dbprefix('formulario_rel_proyecto');
		$forms_table = $this->db->dbprefix('formularios');
		$field_rel_form_table = $this->db->dbprefix('campo_rel_formulario');
		$fields_table = $this->db->dbprefix('campos');
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
		
		$sql = "SELECT formularios.*, campos.id AS id_campo, campos.opciones FROM ";
		$sql .= "$form_rel_project_table, $forms_table, $field_rel_form_table, $fields_table WHERE";
		$sql .= " $form_rel_project_table.id_proyecto = $id_proyecto";
		$sql .= " AND $form_rel_project_table.deleted = 0";
		$sql .= " AND $forms_table.id = formulario_rel_proyecto.id_formulario";
		$sql .= " AND $forms_table.id_tipo_formulario = 1";
		$sql .= " AND $forms_table.id_cliente = $id_cliente";
		$sql .= " AND $forms_table.flujo = '$flujo'";
		$sql .= " AND $forms_table.deleted = 0 ";
		$sql .= " AND $field_rel_form_table.id_formulario = formularios.id";
		$sql .= " AND $field_rel_form_table.deleted = 0";
		$sql .= " AND $fields_table.id = campo_rel_formulario.id_campo";
		if($flujo == "Consumo"){
			//$sql .= " AND $fields_table.id_tipo_campo = 15";
		}
		$sql .= " AND $fields_table.deleted = 0";
		
		
		/*
		SELECT formularios.*, campos.id AS id_campo, campos.opciones FROM 
		dev_mimasoft_sistema.formulario_rel_proyecto, 
		dev_mimasoft_sistema.formularios, 
		dev_mimasoft_sistema.campo_rel_formulario, 
		dev_mimasoft_sistema.campos 
		WHERE 
		formulario_rel_proyecto.id_proyecto = 1 
		AND formulario_rel_proyecto.deleted = 0 
		AND formularios.id = formulario_rel_proyecto.id_formulario 
		AND formularios.id_tipo_formulario = 1 
		AND formularios.id_cliente = 1 
		AND formularios.flujo = "Consumo" 
		AND formularios.deleted = 0 
		AND campo_rel_formulario.id_formulario = formularios.id 
		AND campo_rel_formulario.deleted = 0 
		AND campos.id = campo_rel_formulario.id_campo
		AND campos.id_tipo_campo = 15
		AND campos.deleted = 0
		*/
		
		return $this->db->query($sql);
	
	}
	
	function get_fields_of_project($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		$sql .= " AND $fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	//no se incluyes campos de tipo selección desde mantenedora, archivo y separación.
	function get_fields_of_project_for_compromise($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		//$sql .= " AND $fields_table.id_tipo_campo != 16";
		//$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 12, 16)";
		$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 16)";
		$sql .= " AND $fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_project_for_permitting($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		//$sql .= " AND $fields_table.id_tipo_campo != 16";
		//$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 12, 16)"; 
		$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 16)"; 
		$sql .= " AND $fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_project_for_stakeholder($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		//$sql .= " AND $fields_table.id_tipo_campo != 16";
		$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 16)"; 
		$sql .= " AND $fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_project_for_agreement($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		//$sql .= " AND $fields_table.id_tipo_campo != 16";
		$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 16)"; 
		$sql .= " AND $fields_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_project_for_feedback($id_proyecto){
		
		$fields_table = $this->db->dbprefix('campos');
		$projects_table = $this->db->dbprefix('projects');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $fields_table.id, $fields_table.nombre";
		$sql .= " FROM $fields_table, $projects_table";
		$sql .= " WHERE $fields_table.id_proyecto = $projects_table.id";
		$sql .= " AND $projects_table.id = $id_proyecto";
		//$sql .= " AND $fields_table.id_tipo_campo != 16";
		//$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 12, 16)";
		$sql .= " AND $fields_table.id_tipo_campo NOT IN (10, 16)";
		$sql .= " AND $fields_table.deleted = 0";
		//$sql .= " GROUP BY $fields_table.nombre ASC";
		
		return $this->db->query($sql);
		
	}
	
	function delete_fields($id){
		
		$campos = $this->db->dbprefix('campos');

		$sql = "UPDATE $campos SET $campos.deleted=1 WHERE $campos.id=$id; ";
		$this->db->query($sql);
	}
	
	function get_fields_of_fixed_form($form_id) {
        
		$form_table = $this->db->dbprefix('formularios');
		$campo_fijo_rel_formulario_rel_proyecto_table = $this->db->dbprefix('campo_fijo_rel_formulario_rel_proyecto');
		$fixed_fields_table = $this->db->dbprefix('campos_fijos');

        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $fixed_fields_table.*";
		$sql .= " FROM $form_table, $campo_fijo_rel_formulario_rel_proyecto_table, $fixed_fields_table";
		$sql .= " WHERE $fixed_fields_table.deleted=0";
		$sql .= " AND $fixed_fields_table.id = $campo_fijo_rel_formulario_rel_proyecto_table.id_campo_fijo";
		$sql .= " AND $campo_fijo_rel_formulario_rel_proyecto_table.id_formulario = $form_table.id";
		$sql .= " AND $form_table.id = $form_id";
		
        return $this->db->query($sql);
    }

}
