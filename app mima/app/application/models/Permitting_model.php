<?php

class Permitting_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'permisos';
        parent::__construct($this->table);
    }

	function get_fields_of_permitting($id_permiso){

		$permitting_table = $this->db->dbprefix('permisos');
		$permitting_rel_campos_table = $this->db->dbprefix('permisos_rel_campos');
		$campos_table = $this->db->dbprefix('campos');
		
		$sql = "SELECT $permitting_rel_campos_table.id AS id_rel, $permitting_table.id AS id_permiso, $campos_table.id_tipo_campo,
				$campos_table.id AS id_campo, $campos_table.nombre AS nombre_campo, $campos_table.html_name, $campos_table.obligatorio,
				$campos_table.opciones, $campos_table.default_value, $campos_table.habilitado
				FROM $campos_table, $permitting_table, $permitting_rel_campos_table 
				WHERE $permitting_table.id = $permitting_rel_campos_table.id_permiso
				AND $campos_table.id = $permitting_rel_campos_table.id_campo 
				AND $permitting_table.id = $id_permiso 
				AND $campos_table.deleted = 0 
				AND $permitting_rel_campos_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_evaluated_of_compromise($id_compromiso){

		$evaluated_compromises_table = $this->db->dbprefix('evaluados_compromisos');
		
		$sql = "SELECT $evaluated_compromises_table.* 
				FROM $evaluated_compromises_table 
				WHERE $evaluated_compromises_table.id_compromiso = $id_compromiso";
		
		return $this->db->query($sql);
		
	}
	
	/* Trae cantidad de evaluaciones que tienen estados de categoría aplica y no aplica. */
	function get_total_applicable_permitting($id_permiso){
		
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');
		$permitting_table = $this->db->dbprefix('permisos');
		$values_permitting_table = $this->db->dbprefix('valores_permisos');
		
		//$sql = "SELECT COUNT($permitting_procedure_evaluation_table.id) AS total_permisos_aplicables";
		$sql = "SELECT $permitting_procedure_evaluation_table.*";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $permitting_table, $values_permitting_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id ";
		$sql .= " AND $permitting_procedure_evaluation_table.id_valor_permiso = $values_permitting_table.id";
		$sql .= " AND $values_permitting_table.id_permiso = $permitting_table.id";
		$sql .= " AND $permitting_table.id = $id_permiso";
		//$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
				 
		return $this->db->query($sql);
		
	}
	
	/*
		Trae los estados (etiqueta del cliente) con sus cantidades.
		las cantidades corresponden a la cantidad de evaluaciones a las que se les ha asignado ese estado.
	*/
	function get_total_quantities_of_status_evaluated($id_permiso){
		
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');
		$permitting_table = $this->db->dbprefix('permisos');
		$values_permitting_table = $this->db->dbprefix('valores_permisos');
		
		//$sql =  " SELECT tabla_estado.id_evaluacion, tabla_estado.id_valor_permiso, tabla_estado.nombre_estado, tabla_estado.id_estado, tabla_estado.nombre_estado, tabla_estado.categoria, tabla_estado.color, COUNT(tabla_estado.categoria) AS cantidad_categoria,";
		/*
		$sql .= " (";
		$sql .= 	" SELECT COUNT(tabla_estado.categoria) * 100 /";
		$sql .= 	" (";
		$sql .= 		" SELECT COUNT($permitting_procedure_evaluation_table.id)";
		$sql .= 		" FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table";
		$sql .= 		" WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= 		" AND ($permitting_procedure_evaluation_table.id_estados_tramitacion_permisos != 0 OR $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos != null)";
		$sql .= 		" AND $permitting_procedure_evaluation_table.deleted = 0";
		$sql .= 		" AND $permitting_procedure_status_table.deleted = 0";
		//$sql .= 		" AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= 		" AND $permitting_procedure_status_table.categoria IN ('Aplica')";
		$sql .= 	" )";
		$sql .= " ) AS porcentaje";
		*/
		//$sql .= " FROM";
		//$sql .= " (";
		$sql .= 	" SELECT $permitting_procedure_evaluation_table.id AS id_evaluacion, $permitting_procedure_evaluation_table.id_evaluado, $permitting_procedure_evaluation_table.id_valor_permiso, $permitting_procedure_status_table.id AS id_estado, $permitting_procedure_status_table.nombre_estado, $permitting_procedure_status_table.categoria, $permitting_procedure_status_table.color";
		$sql .= 	" FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $permitting_table, $values_permitting_table";
		$sql .= 	" WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= 	" AND $permitting_table.id = $values_permitting_table.id_permiso";
		$sql .= 	" AND $values_permitting_table.id = $permitting_procedure_evaluation_table.id_valor_permiso";
		$sql .= 	" AND $permitting_table.id = $id_permiso";
		$sql .= 	" AND $permitting_procedure_status_table.deleted = 0";
		$sql .= 	" AND $permitting_procedure_evaluation_table.deleted = 0";
		//$sql .= 	" AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= 	" AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		//$sql .= " ) AS tabla_estado";
		//$sql .= " GROUP BY tabla_estado.id_estado";
		
		return $this->db->query($sql);
		
	}
	
	function get_total_applicable_procedures_by_evaluated($id_evaluado){
		
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');
		$evaluated_procedures_table = $this->db->dbprefix('evaluados_permisos');
		
		/*
		$sql = "SELECT $evaluated_procedures_table.id as id_evaluado, $evaluated_procedures_table.nombre_evaluado, COUNT($permitting_procedure_evaluation_table.id) AS total_tramitaciones_aplicables";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $evaluated_procedures_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $evaluated_procedures_table.id";
		//$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica')";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
		*/
		
		$sql = "SELECT $permitting_procedure_evaluation_table.id as id_evaluacion, $evaluated_procedures_table.id as id_evaluado, $evaluated_procedures_table.nombre_evaluado, $permitting_procedure_evaluation_table.id_valor_permiso,";
		$sql .= " $permitting_procedure_evaluation_table.fecha_evaluacion";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $evaluated_procedures_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $evaluated_procedures_table.id";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0 ";
		
		return $this->db->query($sql);
		
	}
	
	//Mostrar solo los estados de categoría "cumple" que son reportables. (realizado, en curso, pendiente, no realizado, son estados creados). 
	
	function get_reportable_compromises($id_compromiso){
		
		$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos');
		$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$values_compromises_table = $this->db->dbprefix('valores_compromisos');
		
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

		return $this->db->query($sql);
		
	}
	
	function get_fields_of_permitting_status($id_permiso){
		
		//$evaluated_compromises_table = $this->db->dbprefix('evaluados_compromisos');
		$evaluated_procedures_table = $this->db->dbprefix('evaluados_permisos');
		
		$sql =  " SELECT $evaluated_procedures_table.id, $evaluated_procedures_table.nombre_evaluado";
		$sql .= " FROM $evaluated_procedures_table";
		$sql .= " WHERE $evaluated_procedures_table.id_permiso = $id_permiso";
		$sql .= " AND $evaluated_procedures_table.deleted = 0";

		return $this->db->query($sql);
		
	}
	
	/* Consulta para sección Estados de Cumplimiento del módulo Cumplimiento de Compromisos*/
	function get_data_of_procedure_status($id_permiso){
		
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');
		$values_permitting_table = $this->db->dbprefix('valores_permisos');
		$evaluated_procedures_table = $this->db->dbprefix('evaluados_permisos');
		$permitting_table = $this->db->dbprefix('permisos');
		
		$sql  = "SELECT $permitting_procedure_evaluation_table.id AS id_evaluacion, $values_permitting_table.id AS id_valor_permiso,";
		$sql .= " $values_permitting_table.nombre_permiso, $evaluated_procedures_table.id AS id_evaluado, $evaluated_procedures_table.nombre_evaluado,";
		$sql .= " $permitting_procedure_status_table.id AS id_estado, $permitting_procedure_status_table.nombre_estado, $permitting_procedure_evaluation_table.fecha_evaluacion";
		$sql .= " FROM $permitting_table, $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $values_permitting_table, $evaluated_procedures_table";
		
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_valor_permiso = $values_permitting_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $evaluated_procedures_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $values_permitting_table.id_permiso = $permitting_table.id";
		$sql .= " AND $permitting_table.id = $id_permiso";
		$sql .= " AND $values_permitting_table.deleted = 0";
		$sql .= " AND $evaluated_procedures_table.deleted = 0";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
		$sql .= " ORDER BY id_valor_permiso, $evaluated_procedures_table.nombre_evaluado";
		
		return $this->db->query($sql);
		
	}
	
	// SE MODIFICÓ EL METODO, YA QUE TRAIA A NIVEL GLOBAL DE CLIENTE Y NO DE PROYECTO
	function get_status_in_evaluations($id_cliente, $id_proyecto){
		
		$permittings_table = $this->db->dbprefix('permisos');
		$permitting_values_table = $this->db->dbprefix('valores_permisos');
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');

		/*$sql  = "SELECT $permitting_procedure_evaluation_table.id as id_evaluacion, $permitting_procedure_evaluation_table.fecha_evaluacion,";
		$sql .= " $permitting_procedure_evaluation_table.id_evaluado, $permitting_procedure_evaluation_table.id_valor_permiso,";
		$sql .= " $permitting_procedure_status_table.id AS id_estado, $permitting_procedure_status_table.nombre_estado";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		//$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_status_table.id_cliente = $id_cliente";
		//$sql .= " GROUP BY id_estado";*/
		
		$sql  = "SELECT $permitting_procedure_evaluation_table.id as id_evaluacion, $permitting_procedure_evaluation_table.fecha_evaluacion,";
		$sql .= " $permitting_procedure_evaluation_table.id_evaluado, $permitting_procedure_evaluation_table.id_valor_permiso,";
		$sql .= " $permitting_procedure_status_table.id AS id_estado, $permitting_procedure_status_table.nombre_estado";
		$sql .= " FROM $permittings_table, $permitting_values_table,";
		$sql .= " $permitting_procedure_evaluation_table, $permitting_procedure_status_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_status_table.id_cliente = $id_cliente";
		$sql .= " AND $permitting_procedure_evaluation_table.id_valor_permiso = $permitting_values_table.id";
		$sql .= " AND $permitting_values_table.deleted = 0";
		$sql .= " AND $permitting_values_table.id_permiso = $permittings_table.id";
		$sql .= " AND $permittings_table.deleted = 0";
		$sql .= " AND $permittings_table.id_proyecto = $id_proyecto";
		
		return $this->db->query($sql);
		
	}
	
	/* Retorna la cantidad de estados por evaluado y estado en las evaluaciones */
	function get_quantity_of_status_evaluated($id_estado, $id_evaluado){
		
		//$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos');
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		
		//$sql  = "SELECT COUNT($permitting_procedure_evaluation_table.id) as cantidad";
		$sql  = "SELECT $permitting_procedure_evaluation_table.*";
		$sql .= " FROM $permitting_procedure_evaluation_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $id_estado";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0";
		
		return $this->db->query($sql);
		
	}
	
	function get_quantity_of_status_evaluated_until_date($id_estado, $id_evaluado, $until = NULL){
		
		$evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		$status_table = $this->db->dbprefix('estados_tramitacion_permisos');
	
		$sql  = "SELECT COUNT(tabla_final.id) AS total FROM (";
		$sql .= "	SELECT MAX(rango_fechas.fecha_evaluacion), rango_fechas.* FROM (";
		$sql .= "		SELECT";
		$sql .= "			$evaluation_table.*,";
		$sql .= "			$status_table.nombre_estado ";
		$sql .= "		FROM";
		$sql .= "			$evaluation_table, ";
		$sql .= "			$status_table";
		$sql .= "		WHERE";
		$sql .= "			$evaluation_table.deleted = 0 AND ";
		$sql .= "			$evaluation_table.id_evaluado = $id_evaluado AND ";
		$sql .= "			DATE($evaluation_table.fecha_evaluacion) <= '$until' AND ";
		$sql .= "			$status_table.deleted = 0 AND ";
		$sql .= "			$status_table.id = $evaluation_table.id_estados_tramitacion_permisos AND ";
		$sql .= "			$status_table.categoria != 'No Aplica' ";
		$sql .= "		ORDER BY $evaluation_table.fecha_evaluacion DESC";
		$sql .= "	) AS rango_fechas ";
		$sql .= "	GROUP BY rango_fechas.id_valor_permiso ";
		$sql .= "	ORDER BY rango_fechas.id_valor_permiso";
		$sql .= ") AS tabla_final ";
		$sql .= "WHERE tabla_final.id_estados_tramitacion_permisos = $id_estado";
		//echo $sql;
		
		/*sql  = "SELECT COUNT(evaluaciones.id) AS total ";
		$sql .= "FROM $evaluation_table evaluaciones ";
		$sql .= "INNER JOIN (";
		$sql .= "	SELECT ";
		$sql .= "		evaluaciones_agrupadas_fecha_max.id_valor_permiso, ";
		$sql .= "		max(evaluaciones_agrupadas_fecha_max.fecha_evaluacion) as fecha_evaluacion_max";
		$sql .= "	FROM $evaluation_table evaluaciones_agrupadas_fecha_max ";
		$sql .= "	WHERE ";
		$sql .= "		evaluaciones_agrupadas_fecha_max.deleted = 0 AND ";
		$sql .= "		evaluaciones_agrupadas_fecha_max.id_evaluado = $id_evaluado AND ";
		$sql .= "		DATE(evaluaciones_agrupadas_fecha_max.fecha_evaluacion) <= '$until' AND ";
		$sql .= "		evaluaciones_agrupadas_fecha_max.id_estados_tramitacion_permisos = $id_estado ";
		$sql .= "	GROUP BY evaluaciones_agrupadas_fecha_max.id_valor_permiso";
		$sql .= "	) evaluaciones_agrupadas ";
		$sql .= "ON evaluaciones_agrupadas.id_valor_permiso = evaluaciones.id_valor_permiso AND evaluaciones.fecha_evaluacion = evaluaciones_agrupadas.fecha_evaluacion_max ";
		$sql .= "LEFT JOIN $status_table ";
		$sql .= "ON evaluaciones.id_estados_tramitacion_permisos = $status_table.id ";
		$sql .= "WHERE ";
		$sql .= "	$status_table.deleted = 0 AND ";
		$sql .= "	$status_table.categoria != 'No Aplica'";*/
		
		return $this->db->query($sql)->row();
		
	}
	
	function get_percentage_of_status_evaluated($id_estado, $id_evaluado){
		
		//$compromises_compliance_evaluation_table = $this->db->dbprefix('evaluaciones_cumplimiento_compromisos');
		$permitting_procedure_evaluation_table = $this->db->dbprefix('evaluaciones_tramitacion_permisos');
		//$compromises_compliance_status_table = $this->db->dbprefix('estados_cumplimiento_compromisos');
		$permitting_procedure_status_table = $this->db->dbprefix('estados_tramitacion_permisos');
		//$evaluated_compromises_table = $this->db->dbprefix('evaluados_compromisos');
		$evaluated_procedures_table = $this->db->dbprefix('evaluados_permisos');
		
		$sql = "SELECT (";
		$sql .= " ( (SELECT COUNT($permitting_procedure_evaluation_table.id_estados_tramitacion_permisos) as cantidad";
		$sql .= " FROM $permitting_procedure_evaluation_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $id_estado";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0) * 100 ) /";
		$sql .= " (SELECT COUNT($permitting_procedure_evaluation_table.id) AS total_tramitaciones_aplicables";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $evaluated_procedures_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $evaluated_procedures_table.id";
		//$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .=  "AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0)";
		$sql .= " ) AS porcentaje FROM";
		$sql .= " (SELECT COUNT($permitting_procedure_evaluation_table.id) AS total_tramitaciones_aplicables";
		$sql .= " FROM $permitting_procedure_evaluation_table, $permitting_procedure_status_table, $evaluated_procedures_table";
		$sql .= " WHERE $permitting_procedure_evaluation_table.id_estados_tramitacion_permisos = $permitting_procedure_status_table.id";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $evaluated_procedures_table.id";
		//$sql .= " AND $permitting_procedure_status_table.categoria IN ('Aplica', 'No Aplica')";
		$sql .= " AND $permitting_procedure_status_table.categoria IN ('Cumple', 'No Cumple', 'Pendiente')";
		$sql .= " AND $permitting_procedure_evaluation_table.id_evaluado = $id_evaluado";
		$sql .= " AND $permitting_procedure_status_table.deleted = 0";
		$sql .= " AND $permitting_procedure_evaluation_table.deleted = 0) AS tabla_virtual";
		
		return $this->db->query($sql);
		
	}
	
	
	function delete_permitting($id){
		
		$permisos = $this->db->dbprefix('permisos');
		
        $sql = "UPDATE $permisos SET $permisos.deleted=1 WHERE $permisos.id=$id; ";
        $this->db->query($sql);
		
	}
	
	
}
