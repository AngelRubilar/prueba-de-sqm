<?php

class EC_Client_transformation_factors_config_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ec_client_transformation_factors_config';
        parent::__construct($this->table);
    }
	
	/*
		Consultas cálculo de variables Economía Circular (módulo Indicadores por proyecto)
		Ti = RCi + RCu + RES + V
 	*/
	
	// RCi
	// Elementos de formularios de tipo RA, en donde su flujo sea Consumo, material de tipo reciclado y su campo unidad fijo sea de tipo Masa y Volumen 
	function get_elements_for_rci_variable($id_proyecto, $fecha_desde, $fecha_hasta, $tipo_unidad = NULL){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

		$where_tipo_unidad = "";
        if ($tipo_unidad) {
            $where_tipo_unidad .= " AND $valores_formularios_table.datos->'$.tipo_unidad' = '$tipo_unidad'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $formularios_table.id as id_formulario, $valores_formularios_table.datos->'$.id_categoria' AS id_categoria,";
		$sql .= " ROUND(SUM($valores_formularios_table.datos->'$.unidad_residuo'), 5) AS cantidad_total";
		$sql .= " FROM $formularios_table, $valores_formularios_table,$formulario_rel_proyecto_table";
		$sql .= " WHERE $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " AND $formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1"; // Formularios de tipo Registro Ambiental
		$sql .= " AND $formularios_table.flujo = 'Consumo'";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '1'"; // matter
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin_matter' = '3'"; // recycled
		//$sql .= " AND $valores_formularios_table.datos->'$.tipo_unidad' = 'Masa'"; // Masa
		$sql .= $where_tipo_unidad;
		$sql .= " AND ($valores_formularios_table.datos->'$.fecha' >= '$fecha_desde'";
		$sql .= " AND $valores_formularios_table.datos->'$.fecha' <= '$fecha_hasta')";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " AND $valores_formularios_table.deleted = 0";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " GROUP BY id_formulario, $valores_formularios_table.datos->'$.id_categoria'";
										
		return $this->db->query($sql);
		
	}
	
	// RCu
	// Elementos de formularios de tipo RA, en donde su flujo sea Consumo, material de tipo reutilizado y su campo unidad fijo sea de tipo Masa y Volumen 
	function get_elements_for_rcu_variable($id_proyecto, $fecha_desde, $fecha_hasta, $tipo_unidad = NULL){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

		$where_tipo_unidad = "";
        if ($tipo_unidad) {
            $where_tipo_unidad .= " AND $valores_formularios_table.datos->'$.tipo_unidad' = '$tipo_unidad'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $formularios_table.id as id_formulario, $valores_formularios_table.datos->'$.id_categoria' AS id_categoria,";
		$sql .= " ROUND(SUM($valores_formularios_table.datos->'$.unidad_residuo'), 5) AS cantidad_total";
		$sql .= " FROM $formularios_table, $valores_formularios_table,$formulario_rel_proyecto_table";
		$sql .= " WHERE $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " AND $formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1"; // Formularios de tipo Registro Ambiental
		$sql .= " AND $formularios_table.flujo = 'Consumo'";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '1'"; // matter
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin_matter' = '2'"; // reused
		$sql .= $where_tipo_unidad;
		$sql .= " AND ($valores_formularios_table.datos->'$.fecha' >= '$fecha_desde'";
		$sql .= " AND $valores_formularios_table.datos->'$.fecha' <= '$fecha_hasta')";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " AND $valores_formularios_table.deleted = 0";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " GROUP BY id_formulario, $valores_formularios_table.datos->'$.id_categoria'";
										
		return $this->db->query($sql);
		
	}
	
	function get_elements_for_res_variable(){
		
	}
	
	// V
	// Elementos de formularios de tipo RA, en donde su flujo sea Consumo, material de tipo virgen y su campo unidad fijo sea de tipo Masa y Volumen 
	function get_elements_for_v_variable($id_proyecto, $fecha_desde, $fecha_hasta, $tipo_unidad = NULL){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');

		$where_tipo_unidad = "";
        if ($tipo_unidad) {
            $where_tipo_unidad .= " AND $valores_formularios_table.datos->'$.tipo_unidad' = '$tipo_unidad'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $formularios_table.id as id_formulario, $valores_formularios_table.datos->'$.id_categoria' AS id_categoria,";
		$sql .= " ROUND(SUM($valores_formularios_table.datos->'$.unidad_residuo'), 5) AS cantidad_total";
		$sql .= " FROM $formularios_table, $valores_formularios_table,$formulario_rel_proyecto_table";
		$sql .= " WHERE $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " AND $formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1"; // Formularios de tipo Registro Ambiental
		$sql .= " AND $formularios_table.flujo = 'Consumo'";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '1'"; // matter
		$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin_matter' = '1'"; // virgen
		$sql .= $where_tipo_unidad;
		$sql .= " AND ($valores_formularios_table.datos->'$.fecha' >= '$fecha_desde'";
		$sql .= " AND $valores_formularios_table.datos->'$.fecha' <= '$fecha_hasta')";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " AND $valores_formularios_table.deleted = 0";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " GROUP BY id_formulario, $valores_formularios_table.datos->'$.id_categoria'";
										
		return $this->db->query($sql);
		
	}
	
	// FUNCION ESTANDAR
	function get_elements_for_conditions($id_proyecto, $fecha_desde, $fecha_hasta, $flujo, $condicion = array(), $tipo_unidad = NULL){
		
		$formularios_table = $this->db->dbprefix('formularios');
		$valores_formularios_table = $this->db->dbprefix('valores_formularios');
		$formulario_rel_proyecto_table = $this->db->dbprefix('formulario_rel_proyecto');
		
		// flujo: Consumo|Residuo|No Aplica
		// condicion: 
			// Consumo - Materia: type_of_origin":"1","type_of_origin_matter":"X" | $condicion = array(1, X);
			// Consumo - Energia: type_of_origin:"2" | $condicion = array(2);
			// Residuo: tipo_tratamiento":"X" | $condicion = array(X);
			// No Aplica: default_type":"X" | $condicion = array(X);
		// tipo_unidad: Masa|Volumen|etc...
		
		$where_condicion = "";
		if($flujo == 'Consumo'){
			if($condicion[0] == 1){// Materia
				$where_condicion .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '1'";// Es lo mismo que poner $condicion[0]
				$where_condicion .= " AND $valores_formularios_table.datos->'$.type_of_origin_matter' = '$condicion[1]'";
			}elseif($condicion[0] == 2){// Energia
				$where_condicion .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '2'";// Es lo mismo que poner $condicion[0]
			}else{
				// NO HACER NADA (Entrará acá si en el primer elemento del arreglo se pone un numero mayor a 3)
				// En sintesis traera un consolidado a nivel de consumo
			}
		}elseif($flujo == 'Residuo'){
			$where_condicion .= " AND $valores_formularios_table.datos->'$.tipo_tratamiento' = '$condicion[0]'";
		}elseif($flujo == 'No Aplica'){
			$where_condicion .= " AND $valores_formularios_table.datos->'$.default_type' = '$condicion[0]'";
		}else{
			// NO HACER NADA (Entrará acá si se deje cualquier otro String)
			// En sintesis, no agregará el filtro por flujo ni condicion a la consulta
		}

		$where_tipo_unidad = "";
        if ($tipo_unidad) {
            $where_tipo_unidad .= " AND $valores_formularios_table.datos->'$.tipo_unidad' = '$tipo_unidad'";
        }
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
        
		$sql = "SELECT $formularios_table.id as id_formulario, $valores_formularios_table.datos->'$.id_categoria' AS id_categoria,";
		$sql .= " ROUND(SUM($valores_formularios_table.datos->'$.unidad_residuo'), 5) AS cantidad_total";
		$sql .= " FROM $formularios_table, $valores_formularios_table,$formulario_rel_proyecto_table";
		$sql .= " WHERE $valores_formularios_table.id_formulario_rel_proyecto = $formulario_rel_proyecto_table.id";
		$sql .= " AND $formulario_rel_proyecto_table.id_formulario = $formularios_table.id";
		$sql .= " AND $formularios_table.id_tipo_formulario = 1"; // Formularios de tipo Registro Ambiental
		$sql .= " AND $formularios_table.flujo = '$flujo'";
		$sql .= " AND $formulario_rel_proyecto_table.id_proyecto = $id_proyecto";	
		//$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin' = '1'"; // matter
		//$sql .= " AND $valores_formularios_table.datos->'$.type_of_origin_matter' = '3'"; // recycled
		$sql .= $where_condicion;
		// AND valores_formularios.datos->'$.tipo_unidad' = 'Masa' 
		$sql .= $where_tipo_unidad;
		$sql .= " AND ($valores_formularios_table.datos->'$.fecha' >= '$fecha_desde'";
		$sql .= " AND $valores_formularios_table.datos->'$.fecha' <= '$fecha_hasta')";
		$sql .= " AND $formularios_table.deleted = 0";
		$sql .= " AND $valores_formularios_table.deleted = 0";
		$sql .= " AND $formulario_rel_proyecto_table.deleted = 0";
		$sql .= " GROUP BY id_formulario, $valores_formularios_table.datos->'$.id_categoria'";
										
		return $this->db->query($sql);
		
	}
	
}