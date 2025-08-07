<?php

class Compromises_rca_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'compromisos_rca';
        parent::__construct($this->table);
    }

	function get_fields_of_compromise($id_compromiso){

		$compromises_table = $this->db->dbprefix('compromisos_rca');
		$compromises_rel_campos_table = $this->db->dbprefix('compromisos_rca_rel_campos');
		$campos_table = $this->db->dbprefix('campos');
		
		$sql = "SELECT $compromises_rel_campos_table.id AS id_rel, $compromises_table.id AS id_compromiso, $campos_table.id_tipo_campo,
				$campos_table.id AS id_campo, $campos_table.nombre AS nombre_campo, $campos_table.html_name, $campos_table.obligatorio,
				$campos_table.opciones, $campos_table.default_value, $campos_table.habilitado
				FROM $campos_table, $compromises_table, $compromises_rel_campos_table 
				WHERE $compromises_table.id = $compromises_rel_campos_table.id_compromiso
				AND $campos_table.id = $compromises_rel_campos_table.id_campo 
				AND $compromises_table.id = $id_compromiso 
				AND $campos_table.deleted = 0 
				AND $compromises_rel_campos_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_evaluated_of_compromise($id_compromiso){

		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		
		$sql = "SELECT $evaluated_compromises_table.* 
				FROM $evaluated_compromises_table 
				WHERE $evaluated_compromises_table.id_compromiso = $id_compromiso";
		
		return $this->db->query($sql);
		
	}
	
	/* Trae cantidad de evaluaciones que tienen estados de categoría cumple y no cumple. */
	function get_total_applicable_compromises($id_compromiso){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$compromises_table = $this->db->dbprefix('compromisos_rca');
		$values_compromises_table = $this->db->dbprefix('valores_compromisos_rca');
		
		/*
		SELECT COUNT(evaluaciones_cumplimiento_compromisos.id) AS total_compromisos_aplicables
		FROM evaluaciones_cumplimiento_compromisos, estados_cumplimiento_compromisos, compromisos, valores_compromisos
		WHERE evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
		AND evaluaciones_cumplimiento_compromisos.id_valor_compromiso = valores_compromisos.id
		AND valores_compromisos.id_compromiso = compromisos.id
		AND compromisos.id = 2
		AND estados_cumplimiento_compromisos.categoria IN ('Cumple', 'No Cumple')
		AND estados_cumplimiento_compromisos.deleted = 0
		AND evaluaciones_cumplimiento_compromisos.deleted = 0
		*/

		//$sql = "SELECT COUNT($compromises_compliance_evaluation_table.id) AS total_compromisos_aplicables";
		$sql = " SELECT $compromises_compliance_evaluation_table.*";
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $compromises_table, $values_compromises_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id ";
		$sql .= " AND $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= " AND $values_compromises_table.id_compromiso = $compromises_table.id";
		$sql .= " AND $compromises_table.id = $id_compromiso";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
				 
		return $this->db->query($sql);
		
	}
	
	/*
		Trae los estados (etiqueta del cliente) con sus cantidades.
		las cantidades corresponden a la cantidad de evaluaciones a las que se les ha asignado ese estado.
	*/
	function get_total_quantities_of_status_evaluated($id_compromiso){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$compromises_table = $this->db->dbprefix('compromisos_rca');
		$values_compromises_table = $this->db->dbprefix('valores_compromisos_rca');
		
		//$sql =  " SELECT tabla_estado.id_evaluacion, tabla_estado.id_valor_compromiso, tabla_estado.nombre_estado, tabla_estado.id_estado, tabla_estado.nombre_estado, tabla_estado.categoria, tabla_estado.color, COUNT(tabla_estado.categoria) AS cantidad_categoria";
		/*
		$sql .= " (";
		$sql .= 	" SELECT COUNT(tabla_estado.categoria) * 100 /";
		$sql .= 	" (";
		$sql .= 		" SELECT COUNT($compromises_compliance_evaluation_table.id)";
		$sql .= 		" FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table";
		$sql .= 		" WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= 		" AND ($compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso != 0 OR $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso != null)";
		$sql .= 		" AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= 		" AND $compromises_compliance_status_table.deleted = 0";
		$sql .= 		" AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple')";
		$sql .= 	" )";
		$sql .= " ) AS porcentaje";
		*/
		//$sql .= " FROM";
		//$sql .= " (";
		$sql .= 	" SELECT $compromises_compliance_evaluation_table.id AS id_evaluacion, $compromises_compliance_evaluation_table.id_evaluado, $compromises_compliance_evaluation_table.id_valor_compromiso, $compromises_compliance_status_table.id AS id_estado, $compromises_compliance_status_table.nombre_estado, $compromises_compliance_status_table.categoria, $compromises_compliance_status_table.color";
		$sql .= 	" FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $compromises_table, $values_compromises_table";
		$sql .= 	" WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= 	" AND $compromises_table.id = $values_compromises_table.id_compromiso";
		$sql .= 	" AND $values_compromises_table.id = $compromises_compliance_evaluation_table.id_valor_compromiso";
		$sql .= 	" AND $compromises_table.id = $id_compromiso";
		$sql .= 	" AND $compromises_compliance_status_table.deleted = 0";
		$sql .= 	" AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= 	" AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		//$sql .= " ) AS tabla_estado";
		//$sql .= " GROUP BY tabla_estado.id_estado";
		
		//var_dump($sql);
		
		return $this->db->query($sql);
		
	}
	
	function get_total_applicable_compromises_by_evaluated($id_evaluado){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		
		/*
		$sql = "SELECT $evaluated_compromises_table.id as id_evaluado, $evaluated_compromises_table.nombre_evaluado, COUNT($compromises_compliance_evaluation_table.id) AS total_compromisos_aplicables, ";
		$sql .= "$compromises_compliance_evaluation_table.created AS fecha_creacion_evaluacion, $compromises_compliance_evaluation_table.modified AS fecha_modificacion_evaluacion"; 
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $evaluated_compromises_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple')";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		*/
		
		$sql = "SELECT $compromises_compliance_evaluation_table.id as id_evaluacion, $evaluated_compromises_table.id as id_evaluado, $evaluated_compromises_table.nombre_evaluado, $compromises_compliance_evaluation_table.id_valor_compromiso,";
		$sql .= " $compromises_compliance_evaluation_table.fecha_evaluacion";
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $evaluated_compromises_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id ";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente') ";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0 ";
		
		return $this->db->query($sql);
		
	}
	
	//Mostrar solo los estados de categoría "cumple" que son reportables. (realizado, en curso, pendiente, no realizado, son estados creados). 
	
	function get_reportable_compromises($id_compromiso){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$values_compromises_table = $this->db->dbprefix('valores_compromisos_rca');
		
		/*
		SELECT tabla_virtual.*, COUNT(tabla_virtual.id_estado) AS sub_total, 
			(SELECT COUNT(tabla_virtual.id_estado) * 100 / 
			(SELECT COUNT(evaluaciones_cumplimiento_compromisos.id) 
			FROM evaluaciones_cumplimiento_compromisos, estados_cumplimiento_compromisos, valores_compromisos
			WHERE evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
			AND evaluaciones_cumplimiento_compromisos.id_valor_compromiso = valores_compromisos.id
			AND valores_compromisos.reportabilidad = 1
			AND estados_cumplimiento_compromisos.categoria = "Cumple"
			AND valores_compromisos.id_compromiso = 1
            AND evaluaciones_cumplimiento_compromisos.deleted = 0
            AND valores_compromisos.deleted = 0
            AND estados_cumplimiento_compromisos.deleted = 0
			AND (evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso != 0 
			OR evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso != null) )) AS porcentaje
		FROM
		(SELECT evaluaciones_cumplimiento_compromisos.id AS id_evaluacion, estados_cumplimiento_compromisos.id AS id_estado, estados_cumplimiento_compromisos.nombre_estado, estados_cumplimiento_compromisos.categoria, estados_cumplimiento_compromisos.color, valores_compromisos.reportabilidad
		FROM estados_cumplimiento_compromisos, valores_compromisos, evaluaciones_cumplimiento_compromisos
		WHERE evaluaciones_cumplimiento_compromisos.id_valor_compromiso = valores_compromisos.id
		AND evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
		AND valores_compromisos.reportabilidad = 1
		AND estados_cumplimiento_compromisos.categoria = "Cumple"
		AND valores_compromisos.id_compromiso = 1
        AND evaluaciones_cumplimiento_compromisos.deleted = 0
        AND valores_compromisos.deleted = 0
        AND estados_cumplimiento_compromisos.deleted = 0) AS tabla_virtual
		GROUP BY tabla_virtual.id_estado
		*/
		
		/*
		$sql =  "SELECT tabla_virtual.*, COUNT(tabla_virtual.id_estado) AS sub_total,";
		$sql .= 	" (SELECT COUNT(tabla_virtual.id_estado) * 100 / ";
		$sql .= 	" (SELECT COUNT($compromises_compliance_evaluation_table.id) ";
		$sql .= 	" FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $values_compromises_table";
		$sql .= 	" WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= 	" AND $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= 	" AND $values_compromises_table.reportabilidad = 1";
		$sql .= 	" AND $compromises_compliance_status_table.categoria = 'Cumple'";
		$sql .= 	" AND $values_compromises_table.id_compromiso = $id_compromiso";		
		$sql .= 	" AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= 	" AND $values_compromises_table.deleted = 0";
		$sql .= 	" AND $compromises_compliance_status_table.deleted = 0";
		$sql .= 	" AND ($compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso != 0";
		$sql .= 	" OR $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso != null) )) AS porcentaje";
		$sql .= " FROM";
		$sql .= " (SELECT $compromises_compliance_evaluation_table.id AS id_evaluacion, $compromises_compliance_status_table.id AS id_estado,";
		$sql .= " $compromises_compliance_status_table.nombre_estado, $compromises_compliance_status_table.categoria, $compromises_compliance_status_table.color, $values_compromises_table.reportabilidad";
		$sql .= " FROM $compromises_compliance_status_table, $values_compromises_table, $compromises_compliance_evaluation_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $values_compromises_table.reportabilidad = 1";
		$sql .= " AND $compromises_compliance_status_table.categoria = 'Cumple'";
		$sql .= " AND $values_compromises_table.id_compromiso = $id_compromiso";		
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " AND $values_compromises_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0) AS tabla_virtual";	
		$sql .= " GROUP BY tabla_virtual.id_estado";
		*/
		
		
		
		
		
		/*
		
		SELECT evaluaciones_cumplimiento_compromisos.id AS id_evaluacion, evaluaciones_cumplimiento_compromisos.id_evaluado, 
		evaluaciones_cumplimiento_compromisos.id_valor_compromiso, evaluaciones_cumplimiento_compromisos.fecha_evaluacion, 
		estados_cumplimiento_compromisos.id AS id_estado, estados_cumplimiento_compromisos.nombre_estado, 
		estados_cumplimiento_compromisos.categoria, estados_cumplimiento_compromisos.color, valores_compromisos.reportabilidad
		
		FROM estados_cumplimiento_compromisos, valores_compromisos, evaluaciones_cumplimiento_compromisos
		
		WHERE evaluaciones_cumplimiento_compromisos.id_valor_compromiso = valores_compromisos.id
		AND evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
		AND valores_compromisos.reportabilidad = 1
		AND estados_cumplimiento_compromisos.categoria = "Cumple"
		AND valores_compromisos.id_compromiso = 1
		AND evaluaciones_cumplimiento_compromisos.deleted = 0
		AND valores_compromisos.deleted = 0
		AND estados_cumplimiento_compromisos.deleted = 0
		
		ORDER BY evaluaciones_cumplimiento_compromisos.id_evaluado, evaluaciones_cumplimiento_compromisos.id_valor_compromiso, evaluaciones_cumplimiento_compromisos.fecha_evaluacion DESC
				
		// EN CONTROLADOR, ITERAR LAS FILAS DE ESTA CONSULTA, Y ARMAR UN ARRAY SOLO CON LAS ÚLTIMAS EVALUACIONES DE LA COMBINACIÓN
		// DE id_evaluado e id_valor_compromiso.
		
		*/
		
		$sql = "SELECT * FROM";
		$sql .= " (SELECT $compromises_compliance_evaluation_table.id AS id_evaluacion, $compromises_compliance_evaluation_table.id_evaluado,";
		$sql .= "$compromises_compliance_evaluation_table.id_valor_compromiso, $compromises_compliance_evaluation_table.fecha_evaluacion,";
		$sql .= "$compromises_compliance_status_table.id AS id_estado, $compromises_compliance_status_table.nombre_estado,";
		$sql .= "$compromises_compliance_status_table.categoria, $compromises_compliance_status_table.color, $values_compromises_table.reportabilidad";
		
		$sql .= " FROM $compromises_compliance_status_table, $values_compromises_table, $compromises_compliance_evaluation_table";
		
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $values_compromises_table.reportabilidad = 1";
		//$sql .= " AND $compromises_compliance_status_table.categoria = 'Cumple'";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple')";
		$sql .= " AND $values_compromises_table.id_compromiso = $id_compromiso";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " AND $values_compromises_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		
		$sql .= " ORDER BY $compromises_compliance_evaluation_table.id_evaluado, $compromises_compliance_evaluation_table.id_valor_compromiso, $compromises_compliance_evaluation_table.fecha_evaluacion DESC)";
		
		$sql .= " AS tabla_virtual";
		$sql .= " GROUP BY tabla_virtual.id_evaluado, tabla_virtual.id_valor_compromiso";
		
		return $this->db->query($sql);
		
	}
	
	function get_fields_of_compliance_status($id_compromiso){
		
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		
		/*
		SELECT evaluados_compromisos.id, evaluados_compromisos.nombre_evaluado
		FROM evaluados_compromisos
		WHERE evaluados_compromisos.id_compromiso = 1
		AND evaluados_compromisos.deleted = 0
		*/
		
		$sql =  " SELECT $evaluated_compromises_table.id, $evaluated_compromises_table.nombre_evaluado";
		$sql .= " FROM $evaluated_compromises_table";
		$sql .= " WHERE $evaluated_compromises_table.id_compromiso = $id_compromiso";
		$sql .= " AND $evaluated_compromises_table.deleted = 0";

		return $this->db->query($sql);
		
	}
	
	/* Consulta para sección Estados de Cumplimiento del módulo Cumplimiento de Compromisos*/
	function get_data_of_compliance_status($id_compromiso, $options = array()){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$values_compromises_table = $this->db->dbprefix('valores_compromisos_rca');
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		$compromises_table = $this->db->dbprefix('compromisos_rca');
		
		$where = "";
		
		$reportabilidad = get_array_value($options, "reportabilidad");
		if ($reportabilidad) {
			if($reportabilidad == "si"){
				$where .= " AND $values_compromises_table.reportabilidad = 1";
			}
			if($reportabilidad == "no"){
				$where .= " AND $values_compromises_table.reportabilidad = 0";
			} 
        }
		
		$sql  = "SELECT $compromises_compliance_evaluation_table.id AS id_evaluacion, $values_compromises_table.id AS id_valor_compromiso, $values_compromises_table.reportabilidad,";
		$sql .= " $values_compromises_table.nombre_compromiso, $evaluated_compromises_table.id AS id_evaluado, $evaluated_compromises_table.nombre_evaluado,";
		$sql .= " $compromises_compliance_status_table.id AS id_estado, $compromises_compliance_status_table.nombre_estado, $compromises_compliance_evaluation_table.fecha_evaluacion";
		$sql .= " FROM $compromises_table, $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $values_compromises_table, $evaluated_compromises_table";
		
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $values_compromises_table.id_compromiso = $compromises_table.id";
		$sql .= " AND $compromises_table.id = $id_compromiso";
		$sql .= " $where";
		
		/*
		$sql .= " AND $compromises_compliance_evaluation_table.fecha_evaluacion =";
		$sql .= " (SELECT MAX($compromises_compliance_evaluation_table.fecha_evaluacion)";
		$sql .= " FROM $compromises_compliance_evaluation_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_valor_compromiso = $values_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $values_compromises_table.id_compromiso = $compromises_table.id";
		$sql .= " AND $compromises_table.id = $id_compromiso";
		$sql .= " AND $values_compromises_table.deleted = 0";
		$sql .= " AND $evaluated_compromises_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0)";
		*/
		
		$sql .= " AND $values_compromises_table.deleted = 0";
		$sql .= " AND $evaluated_compromises_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " ORDER BY id_valor_compromiso, $evaluated_compromises_table.nombre_evaluado";
		
		//var_dump($sql);
		
		return $this->db->query($sql);
		
	}
	
	// SE MODIFICÓ EL METODO, YA QUE TRAIA A NIVEL GLOBAL DE CLIENTE Y NO DE PROYECTO
	function get_status_in_evaluations($id_cliente, $id_proyecto){
		
		$compromises_table = $this->db->dbprefix('compromisos_rca');
		$compromises_values_table = $this->db->dbprefix('valores_compromisos_rca');
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		
		/*$sql  = "SELECT $compromises_compliance_evaluation_table.id as id_evaluacion, $compromises_compliance_evaluation_table.fecha_evaluacion,";
		$sql .= " $compromises_compliance_evaluation_table.id_evaluado, $compromises_compliance_evaluation_table.id_valor_compromiso,";
		$sql .= " $compromises_compliance_status_table.id AS id_estado, $compromises_compliance_status_table.nombre_estado";
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.id_cliente = $id_cliente";
		//$sql .= " GROUP BY id_estado";
		*/
		
		$sql  = "SELECT $compromises_compliance_evaluation_table.id as id_evaluacion, $compromises_compliance_evaluation_table.fecha_evaluacion,";
		$sql .= " $compromises_compliance_evaluation_table.id_evaluado, $compromises_compliance_evaluation_table.id_valor_compromiso,";
		$sql .= " $compromises_compliance_status_table.id AS id_estado, $compromises_compliance_status_table.nombre_estado";
		$sql .= " FROM $compromises_table, $compromises_values_table,";
		$sql .= " $compromises_compliance_evaluation_table, $compromises_compliance_status_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_status_table.id_cliente = $id_cliente";
		$sql .= " AND $compromises_compliance_evaluation_table.id_valor_compromiso = $compromises_values_table.id";
		$sql .= " AND $compromises_values_table.deleted = 0";
		$sql .= " AND $compromises_values_table.id_compromiso = $compromises_table.id";
		$sql .= " AND $compromises_table.deleted = 0";
		$sql .= " AND $compromises_table.id_proyecto = $id_proyecto";
		//$sql .= " GROUP BY id_estado";
		
		return $this->db->query($sql);
		
	}
	
	/* Retorna la cantidad de estados por evaluado y estado en las evaluaciones */
	function get_quantity_of_status_evaluated($id_estado, $id_evaluado){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		
		$sql  = "SELECT $compromises_compliance_evaluation_table.*";
		$sql .= " FROM $compromises_compliance_evaluation_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $id_estado";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0";
		
		/*$sql  = "SELECT $compromises_compliance_evaluation_table.* FROM ";
		$sql .= " $compromises_compliance_evaluation_table,";
		$sql .= " (SELECT $compromises_compliance_evaluation_table.id AS id_ultima_evaluacion, $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso AS id_ultimo_estado, MAX($compromises_compliance_evaluation_table.fecha_evaluacion) AS fecha_ultima_evaluacion FROM $compromises_compliance_evaluation_table WHERE $compromises_compliance_evaluation_table.deleted = 0 AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado GROUP BY $compromises_compliance_evaluation_table.id_valor_compromiso, $compromises_compliance_evaluation_table.id_evaluado) as tabla_ultimos";
		$sql .= " WHERE";
		$sql .= " $compromises_compliance_evaluation_table.deleted = 0 AND";
		$sql .= " $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado AND";
		$sql .= " $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $id_estado AND ";
		$sql .= " $compromises_compliance_evaluation_table.id = tabla_ultimos.id_ultima_evaluacion AND";
		$sql .= " tabla_ultimos.id_ultimo_estado = $id_estado";*/

		/*$sql  = "SELECT";
		$sql .= " $compromises_compliance_evaluation_table.id_valor_compromiso, ";
		$sql .= " MAX($compromises_compliance_evaluation_table.fecha_evaluacion) AS max_fecha_evaluacion";
		$sql .= " FROM";
		$sql .= " $compromises_compliance_evaluation_table";
		$sql .= " WHERE";
		$sql .= " $compromises_compliance_evaluation_table.deleted = 0 AND";
		$sql .= " $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado AND";
		$sql .= " $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $id_estado";
		$sql .= " GROUP BY $compromises_compliance_evaluation_table.id_valor_compromiso";*/
		

		/*$sql  = "SELECT * FROM (";
		$sql .= " SELECT $compromises_compliance_evaluation_table.id_valor_compromiso,";
		$sql .= " $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso,";
		$sql .= " MAX($compromises_compliance_evaluation_table.fecha_evaluacion) AS max_fecha_evaluacion";
		$sql .= " FROM $compromises_compliance_evaluation_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " GROUP BY $compromises_compliance_evaluation_table.id_valor_compromiso) AS ultimas_evaluaciones";
		$sql .= " WHERE ultimas_evaluaciones.id_estados_cumplimiento_compromiso = $id_estado";*/
		
		return $this->db->query($sql);
		
	}
	
	
	function get_quantity_of_status_evaluated_until_date($id_estado, $id_evaluado, $until = NULL){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
	
		$sql  = "SELECT COUNT(tabla_final.id) AS total FROM (";
		$sql .= "	SELECT MAX(rango_fechas.fecha_evaluacion), rango_fechas.* FROM (";
		$sql .= "		SELECT";
		$sql .= "			$compromises_compliance_evaluation_table.*,";
		$sql .= "			$compromises_compliance_status_table.nombre_estado ";
		$sql .= "		FROM";
		$sql .= "			$compromises_compliance_evaluation_table, ";
		$sql .= "			$compromises_compliance_status_table";
		$sql .= "		WHERE";
		$sql .= "			$compromises_compliance_evaluation_table.deleted = 0 AND ";
		$sql .= "			$compromises_compliance_evaluation_table.id_evaluado = $id_evaluado AND ";
		$sql .= "			DATE($compromises_compliance_evaluation_table.fecha_evaluacion) <= '$until' AND ";
		$sql .= "			$compromises_compliance_status_table.deleted = 0 AND ";
		$sql .= "			$compromises_compliance_status_table.id = $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso AND ";
		$sql .= "			$compromises_compliance_status_table.categoria != 'No Aplica' ";
		$sql .= "		ORDER BY $compromises_compliance_evaluation_table.fecha_evaluacion DESC";
		$sql .= "	) AS rango_fechas ";
		$sql .= "	GROUP BY rango_fechas.id_valor_compromiso ";
		$sql .= "	ORDER BY rango_fechas.id_valor_compromiso";
		$sql .= ") AS tabla_final ";
		$sql .= "WHERE tabla_final.id_estados_cumplimiento_compromiso = $id_estado";
		
		return $this->db->query($sql)->row();
		
	}
	
	function get_percentage_of_status_evaluated($id_estado, $id_evaluado){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos_rca');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$evaluated_compromises_table = $this->db->dbprefix('evaluados_rca_compromisos');
		
		/*
		SELECT (
		( (SELECT COUNT(evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso) as cantidad
		FROM evaluaciones_cumplimiento_compromisos
		WHERE evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = 1
		AND evaluaciones_cumplimiento_compromisos.id_evaluado = 5
		AND evaluaciones_cumplimiento_compromisos.deleted = 0) * 100 ) /
		
		(SELECT COUNT(evaluaciones_cumplimiento_compromisos.id) AS total_compromisos_aplicables
		FROM evaluaciones_cumplimiento_compromisos, estados_cumplimiento_compromisos, evaluados_compromisos
		WHERE evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
		AND evaluaciones_cumplimiento_compromisos.id_evaluado = evaluados_compromisos.id
		AND estados_cumplimiento_compromisos.categoria IN ('Cumple', 'No Cumple')
		AND evaluaciones_cumplimiento_compromisos.id_evaluado = 5
		AND estados_cumplimiento_compromisos.deleted = 0
		AND evaluaciones_cumplimiento_compromisos.deleted = 0)
		
		) AS porcentaje FROM
		
		(SELECT COUNT(evaluaciones_cumplimiento_compromisos.id) AS total_compromisos_aplicables
		FROM evaluaciones_cumplimiento_compromisos, estados_cumplimiento_compromisos, evaluados_compromisos
		WHERE evaluaciones_cumplimiento_compromisos.id_estados_cumplimiento_compromiso = estados_cumplimiento_compromisos.id
		AND evaluaciones_cumplimiento_compromisos.id_evaluado = evaluados_compromisos.id
		AND estados_cumplimiento_compromisos.categoria IN ('Cumple', 'No Cumple')
		AND evaluaciones_cumplimiento_compromisos.id_evaluado = 5
		AND estados_cumplimiento_compromisos.deleted = 0
		AND evaluaciones_cumplimiento_compromisos.deleted = 0) AS tabla_virtual
		*/
		
		$sql = "SELECT (";
		$sql .= " ( (SELECT COUNT($compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso) as cantidad";
		$sql .= " FROM $compromises_compliance_evaluation_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $id_estado";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0) * 100 ) /";
		$sql .= " (SELECT COUNT($compromises_compliance_evaluation_table.id) AS total_compromisos_aplicables";
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $evaluated_compromises_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple')";
		$sql .=  "AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $compromises_compliance_status_table.deleted = 0";
		$sql .= " AND $compromises_compliance_evaluation_table.deleted = 0)";
		$sql .= " ) AS porcentaje FROM";
		$sql .= " (SELECT COUNT($compromises_compliance_evaluation_table.id) AS total_compromisos_aplicables";
		$sql .= " FROM $compromises_compliance_evaluation_table, $compromises_compliance_status_table, $evaluated_compromises_table";
		$sql .= " WHERE $compromises_compliance_evaluation_table.id_estados_cumplimiento_compromiso = $compromises_compliance_status_table.id";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $evaluated_compromises_table.id";
		$sql .= " AND $compromises_compliance_status_table.categoria IN ('Cumple', 'No Cumple')";
		$sql .= " AND $compromises_compliance_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND estados_cumplimiento_compromisos.deleted = 0";
		$sql .= " AND evaluaciones_cumplimiento_compromisos.deleted = 0) AS tabla_virtual";
		
		return $this->db->query($sql);
		
	}
	
	
	function delete_compromises($id){
		
		$compromisos = $this->db->dbprefix('compromisos_rca');
		
        $sql = "UPDATE $compromisos SET $compromisos.deleted=1 WHERE $compromisos.id=$id; ";
        $this->db->query($sql);
	}
	
	
}
