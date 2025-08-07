<?php

class AYN_Alert_projects_model extends Crud_model {

    private $table = null;
	private $bd_mimasoft_fc;
	
    function __construct() {
		$this->load->helper('database');
		$this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        $this->table = 'ayn_alert_projects';
        parent::__construct($this->table);
    }
	
	// Para listar elementos del acordeón Registros ambientales de Configuración de Alertas
	function get_categories_and_units_of_forms_projects($id_proyecto){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		$formulario_rel_materiales_rel_categorias_table = $this->db->dbprefix('formulario_rel_materiales_rel_categorias');
		
		$fc_db = getFCBD();
		$categorias_table = $this->bd_mimasoft_fc->dbprefix('categorias');
		$tipo_unidad_table = $this->bd_mimasoft_fc->dbprefix('tipo_unidad');
		$unidad_table = $this->bd_mimasoft_fc->dbprefix('unidad');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		/* $sql = "SELECT $formularios_table.id, $formulario_rel_materiales_rel_categorias_table.id_categoria, $categorias_table.nombre AS nombre_categoria,";
		$sql .= " $formularios_table.unidad->'$.tipo_unidad_id' AS id_tipo_unidad, $tipo_unidad_table.nombre AS nombre_tipo_unidad,";
		$sql .= " $formularios_table.unidad->'$.unidad_id' AS id_unidad, $unidad_table.nombre AS nombre_unidad,";
		$sql .= " $formulario_rel_proyecto_table.id_proyecto";
		$sql .= " FROM $formularios_table, $formulario_rel_proyecto_table, $formulario_rel_materiales_rel_categorias_table,";
		$sql .= " $fc_db.$categorias_table, $fc_db.$tipo_unidad_table, $fc_db.$unidad_table";
		$sql .= " WHERE $formularios_table.id = $formulario_rel_proyecto_table.id_formulario";
		$sql .= " AND $formularios_table.id = $formulario_rel_materiales_rel_categorias_table.id_formulario";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.id_categoria = $categorias_table.id";
		$sql .= " AND $formularios_table.unidad->'$.tipo_unidad_id' = $tipo_unidad_table.id";
		$sql .= " AND $formularios_table.unidad->'$.unidad_id' = $unidad_table.id";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " AND $formulario_rel_materiales_rel_categorias_table.deleted = 0";
		$sql .= " GROUP BY $formulario_rel_materiales_rel_categorias_table.id_categoria, $formularios_table.id_tipo_unidad, $formularios_table.id_formulario,"; */
		
		$sql = "SELECT 
            $formularios_table.id, 
            $formulario_rel_materiales_rel_categorias_table.id_categoria, 
            $categorias_table.nombre AS nombre_categoria,
            JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.tipo_unidad_id')) AS id_tipo_unidad, 
            $tipo_unidad_table.nombre AS nombre_tipo_unidad,
            JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.unidad_id')) AS id_unidad, 
            $unidad_table.nombre AS nombre_unidad,
            $formulario_rel_proyecto_table.id_proyecto
        FROM 
            $formularios_table
        JOIN 
            $formulario_rel_proyecto_table ON $formularios_table.id = $formulario_rel_proyecto_table.id_formulario
        JOIN 
            $formulario_rel_materiales_rel_categorias_table ON $formularios_table.id = $formulario_rel_materiales_rel_categorias_table.id_formulario
        JOIN 
            $fc_db.$categorias_table ON $formulario_rel_materiales_rel_categorias_table.id_categoria = $categorias_table.id
        JOIN 
            $fc_db.$tipo_unidad_table ON JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.tipo_unidad_id')) = $tipo_unidad_table.id
        JOIN 
            $fc_db.$unidad_table ON JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.unidad_id')) = $unidad_table.id
        WHERE 
            $formularios_table.id_tipo_formulario = 1
            AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto
            AND $formularios_table.deleted = 0
            AND $formulario_rel_proyecto_table.deleted = 0
            AND $formulario_rel_materiales_rel_categorias_table.deleted = 0
        GROUP BY 
            $formulario_rel_materiales_rel_categorias_table.id_categoria, 
            JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.tipo_unidad_id')),
            $formularios_table.id, 
            $categorias_table.nombre,
            $tipo_unidad_table.nombre,
            JSON_UNQUOTE(JSON_EXTRACT($formularios_table.unidad, '$.unidad_id')), 
            $unidad_table.nombre,
            $formulario_rel_proyecto_table.id_proyecto";
		return $this->db->query($sql);
		
	}
	
	// Para traer configuración de acordeones
	function get_alert_projects_config($options = array()){
		
		$alert_projects_table = $this->db->dbprefix('ayn_alert_projects');
		
		$where = "";
		$id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $alert_projects_table.id = $id";
		}
		
        $id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $alert_projects_table.id_client = $id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $alert_projects_table.id_project = $id_project";
        }
		
		$id_client_module = get_array_value($options, "id_client_module");
        if ($id_client_module) {
            $where .= " AND $alert_projects_table.id_client_module = $id_client_module";
        }
		
		$id_client_submodule = get_array_value($options, "id_client_submodule");
        if ($id_client_submodule) {
            $where .= " AND $alert_projects_table.id_client_submodule = $id_client_submodule";
        }
		
		$alert_config = get_array_value($options, "alert_config");
        if (count($alert_config)) {

			if($id_client_module == "2"){ // Registros ambientales
				$id_categoria = $alert_config["id_categoria"];
				$id_tipo_unidad = $alert_config["id_tipo_unidad"];
				$id_unidad = $alert_config["id_unidad"];
				$where .= " AND $alert_projects_table.alert_config->'$.id_categoria' = '$id_categoria'";
				$where .= " AND $alert_projects_table.alert_config->'$.id_tipo_unidad' = '$id_tipo_unidad'";
				if($id_unidad){
				$where .= " AND $alert_projects_table.alert_config->'$.id_unidad' = '$id_unidad'";
				}
			}
			
			if($id_client_module == "6"){ // Compromisos
				if($id_client_submodule == "4" || $id_client_submodule == "22"){ // Evaluación de Compromisos RCA || Evaluación de Compromisos Reportables
					$id_planificacion = $alert_config["id_planificacion"];
					$id_valor_compromiso = $alert_config["id_valor_compromiso"];
					$tipo_evaluacion = $alert_config["tipo_evaluacion"];
					if($id_planificacion){
						$where .= " AND $alert_projects_table.alert_config->'$.id_planificacion' = '$id_planificacion'";
					} else {
						//$where .= " AND $alert_projects_table.alert_config->'$.id_valor_compromiso' = '$id_valor_compromiso'";
						$where .= " AND $alert_projects_table.alert_config->'$.tipo_evaluacion' = '$tipo_evaluacion'";
					}
				}
			}
			
			if($id_client_module == "7"){ // Permisos
				$id_valor_permiso = $alert_config["id_valor_permiso"];
				$where .= " AND $alert_projects_table.alert_config->'$.id_valor_permiso' = '$id_valor_permiso'";
			}
			
			if($id_client_module == "12"){ // Recordbook
				$id_valor_recordbook = $alert_config["id_valor_recordbook"];
				$where .= " AND $alert_projects_table.alert_config->'$.id_valor_recordbook' = '$id_valor_recordbook'";
			}

			if($id_client_module == "14"){ // Pronóstico (Aire)
				$air_config = $alert_config["air_config"];
				$id_air_station = $alert_config["id_air_station"];
				$id_air_sector = $alert_config["id_air_sector"];
				$id_air_variable = $alert_config["id_air_variable"];
				$where .= " AND $alert_projects_table.alert_config->'$.air_config' = '$air_config'";
				if($id_air_station){
					$where .= " AND $alert_projects_table.alert_config->'$.id_air_station' = '$id_air_station'";
				}
				if($id_air_sector){
					$where .= " AND $alert_projects_table.alert_config->'$.id_air_sector' = '$id_air_sector'";
				}
				if($id_air_variable){
					$where .= " AND $alert_projects_table.alert_config->'$.id_air_variable' = '$id_air_variable'";
				}
			}
			
		}
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		
		$sql = "SELECT $alert_projects_table.*, CAST($alert_projects_table.alert_config->'$.id_air_station' AS UNSIGNED) AS id_station";
		$sql .= " FROM $alert_projects_table";
		$sql .= " WHERE $alert_projects_table.deleted = 0";
		$sql .= " $where";
				
		return $this->db->query($sql);
		
	}
	
	// Trae la suma del campo unidad de los elementos de los formularios de tipo registro ambiental de un proyecto, 
	// que tengan la categoría y tipo de unidad de la configuración de alertas
	function get_sum_unit_field_of_ra_forms($options = array()){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');
		
		$where = "";
        $id_proyecto = get_array_value($options, "id_proyecto");
        if ($id_proyecto) {
            $where .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";
        }
		
		$id_categoria = get_array_value($options, "id_categoria");
        if ($id_categoria) {
            $where .= " AND $valores_formularios_table.datos->'$.id_categoria' = $id_categoria";
        }
		
		$id_unidad = get_array_value($options, "id_unidad");
        if ($id_unidad) {
            $where .= " AND $formularios_table.unidad->'$.unidad_id' = $id_unidad";
        }
				
		$this->db->query('SET SQL_BIG_SELECTS=1');
				
		$sql = "SELECT SUM($valores_formularios_table.datos->'$.unidad_residuo') AS suma_elementos";
		$sql .= " FROM $formularios_table, $formulario_rel_proyecto_table, $valores_formularios_table";
		$sql .= " WHERE $formularios_table.id = $formulario_rel_proyecto_table.id_formulario";
		$sql .= " AND $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " $where";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " AND $valores_formularios_table.deleted = 0";
		$sql .= " AND $formularios_table.deleted = 0";
				
		return $this->db->query($sql);

	}
	
}