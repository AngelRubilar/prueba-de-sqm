<?php
/**
 * Archivo Modelo Datos de Pronósticos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Pronósticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Datos de Pronósticos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Pronósticos
 * @property private $table El nombre de la tabla de base de datos de la entidad Datos de Pronósticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_records_values_p_model extends Crud_model {

    /**
     * table
     * @var $table
     */
    private $table = null;

    /**
     * __construct
     * 
     * Constructor
     */
    function __construct() {
        $this->table = 'air_records_values_p';
        parent::__construct($this->table);
    }

    /**
     * get_details
     * 
     * Consulta los datos de Pronóstico de las variables de Calidad del aire y/o Meteorológicas 
	 * de los Sectores / Estaciones asociadas a un Cliente / Proyecto, cargados masivamente
     * por el usuario en el módulo Administración cliente MIMAire / Carga Masiva MIMAire
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id'] => (int) id del Sector. <br/>
     *      $options['id_client'] => (int) id de Cliente. <br/>
     *      $options['id_project'] => (int) id de Proyecto. <br/>
     *      $options['id_record'] => (int) id de Registro de Pronóstico. <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
     * @uses string $this->db->dbprefix('clients') tabla clients
     * @uses string $this->db->dbprefix('projects') tabla projects
     * @uses string $this->db->dbprefix('air_records') tabla air_records
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
	 * @return object
	 */
    function get_details($options = array()) {
        
        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
        $air_records_table = $this->db->dbprefix('air_records');
		$air_variables_table = $this->db->dbprefix('air_variables');

        $where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where .= " AND $air_records_values_p_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client){
            $where .= " AND $air_records_values_p_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project){
            $where .= " AND $air_records_values_p_table.id_project = $id_project";
        }

        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }

        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = " SELECT $air_records_values_p_table.*, $clients_table.company_name AS name_client, $projects_table.title AS name_project,";
        $sql .= " $air_records_table.name AS name_record, $air_variables_table.name AS variable_name";
        $sql .= " FROM $air_records_values_p_table, $clients_table, $projects_table, $air_records_table, $air_variables_table";
        $sql .= " WHERE $air_records_values_p_table.id_client = $clients_table.id";
        $sql .= " AND $air_records_values_p_table.id_project = $projects_table.id";
        $sql .= " AND $air_records_values_p_table.id_record = $air_records_table.id";
        $sql .= " AND $air_records_values_p_table.id_variable = $air_variables_table.id";
        $sql .= " AND $air_records_values_p_table.deleted = 0";
        $sql .= " AND $clients_table.deleted = 0";
        $sql .= " AND $projects_table.deleted = 0";
        $sql .= " AND $air_records_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " $where";
        
        return $this->db->query($sql);

    }

    /**
     * get_details2
     * 
     * Consulta los datos de Pronóstico de las variables de Calidad del aire y/o Meteorológicas 
	 * de los Sectores / Estaciones asociadas a un Registro de Pronóstico (de un Cliente / Proyecto), 
     * cargados masivamente por el usuario en el módulo Administración cliente MIMAire / Carga Masiva MIMAire
     * Se utiliza en el módulo de Registros de Calidad del aire / Registros de Pronóstico
     * para cargar y mostrar los datos de Pronóstico en el appTable de las Variables de un Modelo / Sector
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_record'] => (int) id de Registro de Pronóstico. <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     *      $options['start_date'] => (date) fecha de inicio del rango de consulta de los registros. <br/>
     *      $options['end_date'] => (date) fecha de término del rango de consulta de los registros. <br/>
     *      $options['id_model'] => (int) id de Modelo. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
	 * @return object
	 */
    function get_details2($options = array()) {
        
        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
		$air_variables_table = $this->db->dbprefix('air_variables');

        $where = "";
        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }

        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        
        if($start_date && $end_date){
            $where .= " AND ($air_records_values_p_table.date >= '$start_date' AND $air_records_values_p_table.date <= '$end_date')";
        }

        $select = "";
        $id_model = get_array_value($options, "id_model");
        if($id_model == 3){ // Modelo numérico
            $select .= " $air_records_values_p_table.latitude, $air_records_values_p_table.longitude,";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql_subquery = "SELECT MAX($air_records_values_p_table.id_upload) AS last_upload";
        $sql_subquery .= " FROM $air_records_values_p_table";
        $sql_subquery .= " WHERE $air_records_values_p_table.deleted = 0";
        $sql_subquery .= " $where";

        $sql = " SELECT $air_records_values_p_table.id, $air_variables_table.name AS variable_name, $select";
        $sql .= " $air_records_values_p_table.date,";
        $sql .= " $air_records_values_p_table.time_00, $air_records_values_p_table.time_01, $air_records_values_p_table.time_02,";
        $sql .= " $air_records_values_p_table.time_03, $air_records_values_p_table.time_04, $air_records_values_p_table.time_05,";
        $sql .= " $air_records_values_p_table.time_06, $air_records_values_p_table.time_07, $air_records_values_p_table.time_08,";
        $sql .= " $air_records_values_p_table.time_09, $air_records_values_p_table.time_10, $air_records_values_p_table.time_11,";
        $sql .= " $air_records_values_p_table.time_12, $air_records_values_p_table.time_13, $air_records_values_p_table.time_14,";
        $sql .= " $air_records_values_p_table.time_15, $air_records_values_p_table.time_16, $air_records_values_p_table.time_17,";
        $sql .= " $air_records_values_p_table.time_18, $air_records_values_p_table.time_19, $air_records_values_p_table.time_20,";
        $sql .= " $air_records_values_p_table.time_21, $air_records_values_p_table.time_22, $air_records_values_p_table.time_23";
        
        $sql .= " FROM $air_records_values_p_table";

        $sql .= " INNER JOIN $air_variables_table ON $air_records_values_p_table.id_variable = $air_variables_table.id";
        $sql .= " WHERE $air_records_values_p_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " $where";
        $sql .= " AND $air_records_values_p_table.id_upload = ($sql_subquery)";
        
        return $this->db->query($sql);

    }

    /**
     * get_values_details
     * 
     * Consulta los datos de Pronóstico de las variables de Calidad del aire y/o Meteorológicas 
     * de un Sector, incluyendo coordenadas y valores, para ser mostrados en el mapa de la sección 
     * de Modelo Numérico en la vista del módulo de Pronósticos / Sector
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     *      $options['id_sector'] => (int) id de Sector. <br/>
     *      $options['id_record'] => (int) id de Registro de Pronóstico. <br/>
     *      $options['last_upload'] => (boolean) true para traer la última carga de datos. <br/>
     *      $options['first_date'] => (date) fecha de inicio del rango de consulta de los registros. <br/>
     *      $options['last_date'] => (date) fecha de término del rango de consulta de los registros. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_records')tabla air_records
	 * @return object
	 */
    function get_values_details($options = array()){

        $current_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
        $current_date = date("Y-m-d", strtotime($current_datetime));

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_sectors_table = $this->db->dbprefix('air_sectors');
        $air_records_table = $this->db->dbprefix('air_records');

        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_sector = get_array_value($options, "id_sector");
        if($id_sector){
            $where .= " AND $air_sectors_table.id = $id_sector";
        }

        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }

        $last_upload = get_array_value($options, "last_upload");
        if($last_upload){
            $where .= " AND $air_records_values_p_table.id_upload = (";
            $where .= " SELECT MAX($air_records_values_p_table.id_upload)";
            $where .= " FROM $air_records_values_p_table";
            $where .= " INNER JOIN $air_variables_table ON $air_records_values_p_table.id_variable = $air_variables_table.id";
            $where .= " INNER JOIN $air_records_table ON $air_records_values_p_table.id_record = $air_records_table.id";
            $where .= " INNER JOIN $air_sectors_table ON $air_records_table.id_air_sector = $air_sectors_table.id";
            $where .= " WHERE $air_records_values_p_table.deleted = 0";
            $where .= " AND $air_variables_table.deleted = 0";
            $where .= " AND $air_sectors_table.deleted = 0";
            $where .= " AND $air_records_table.deleted = 0";
            $where .= " AND $air_records_values_p_table.latitude IS NOT NULL ";
            $where .= " AND $air_records_values_p_table.longitude IS NOT NULL ";
            if($id_variable){
                $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
            }
            if($id_sector){
                $where .= " AND $air_sectors_table.id = $id_sector";
            }
            if($id_record){
                $where .= " AND $air_records_values_p_table.id_record = $id_record";
            }
            $where .= ")";
        }

        $first_date = get_array_value($options, "first_date");
        $last_date = get_array_value($options, "last_date");

        if($first_date && $last_date){
            $where .= " AND ($air_records_values_p_table.date >= '$first_date' AND $air_records_values_p_table.date <= '$last_date')";
        } elseif($first_date && $first_date >= $current_date){
            $where .= " AND $air_records_values_p_table.date >= '$first_date'";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT $air_records_values_p_table.*, $air_variables_table.name AS variable_name, $air_records_table.name AS record_name,";
        $sql .= " $air_sectors_table.id AS id_sector, $air_sectors_table.name AS sector_name";
        $sql .= " FROM $air_records_values_p_table";
        $sql .= " INNER JOIN $air_variables_table ON $air_records_values_p_table.id_variable = $air_variables_table.id";
        $sql .= " INNER JOIN $air_records_table ON $air_records_values_p_table.id_record = $air_records_table.id";
        $sql .= " INNER JOIN $air_sectors_table ON $air_records_table.id_air_sector = $air_sectors_table.id";
        $sql .= " WHERE $air_records_values_p_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $air_records_table.deleted = 0";
        $sql .= " AND $air_records_values_p_table.latitude IS NOT NULL ";
        $sql .= " AND $air_records_values_p_table.longitude IS NOT NULL ";
        $sql .= " $where";
        
        //$sql .= " GROUP BY $air_records_values_p_table.id";
        //$sql .= " HAVING MAX($air_records_values_p_table.id_upload)";
        //var_dump($sql);

        return $this->db->query($sql);

    }

    /**
     * get_greatest_or_least_value_p
     * 
     * Consulta el valor mínimo o máximo de los Registros asociados a una Variable de 
     * un Sector. Se utiliza para la sección de Modelo Numérico en la vista del módulo de Pronósticos / Sector.
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['greatest_or_least'] => (string) para filtrar consulta y traer el valor mínimo o el máximo. <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     *      $options['id_sector'] => (int) id de Sector. <br/>
     *      $options['id_record'] => (int) id de Registro de Pronóstico. <br/>
     *      $options['first_date'] => (date) fecha de inicio del rango de consulta de los registros. <br/>
     *      $options['last_date'] => (date) fecha de término del rango de consulta de los registros. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_records') tabla air_records
	 * @return object
	 */
    function get_greatest_or_least_value_p($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_sectors_table = $this->db->dbprefix('air_sectors');
        $air_records_table = $this->db->dbprefix('air_records');

        $select_greatest_or_least = "";
        $greatest_or_least = get_array_value($options, "greatest_or_least");
        if($greatest_or_least == "greatest"){
            $select_greatest_or_least .= "SELECT ( GREATEST( ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_00), MAX($air_records_values_p_table.time_01), MAX($air_records_values_p_table.time_02), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_03), MAX($air_records_values_p_table.time_04), MAX($air_records_values_p_table.time_05), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_06), MAX($air_records_values_p_table.time_07), MAX($air_records_values_p_table.time_08), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_09), MAX($air_records_values_p_table.time_10), MAX($air_records_values_p_table.time_11), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_12), MAX($air_records_values_p_table.time_13), MAX($air_records_values_p_table.time_14), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_15), MAX($air_records_values_p_table.time_16), MAX($air_records_values_p_table.time_17), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_18), MAX($air_records_values_p_table.time_19), MAX($air_records_values_p_table.time_20), ";
            $select_greatest_or_least .= " MAX($air_records_values_p_table.time_21), MAX($air_records_values_p_table.time_22), MAX($air_records_values_p_table.time_23) ";
            $select_greatest_or_least .= ") )";
        }
        if($greatest_or_least == "least"){
            $select_greatest_or_least .= "SELECT ( LEAST( ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_00), MIN($air_records_values_p_table.time_01), MIN($air_records_values_p_table.time_02), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_03), MIN($air_records_values_p_table.time_04), MIN($air_records_values_p_table.time_05), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_06), MIN($air_records_values_p_table.time_07), MIN($air_records_values_p_table.time_08), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_09), MIN($air_records_values_p_table.time_10), MIN($air_records_values_p_table.time_11), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_12), MIN($air_records_values_p_table.time_13), MIN($air_records_values_p_table.time_14), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_15), MIN($air_records_values_p_table.time_16), MIN($air_records_values_p_table.time_17), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_18), MIN($air_records_values_p_table.time_19), MIN($air_records_values_p_table.time_20), ";
            $select_greatest_or_least .= " MIN($air_records_values_p_table.time_21), MIN($air_records_values_p_table.time_22), MIN($air_records_values_p_table.time_23) ";
            $select_greatest_or_least .= ") )";
        }
        
        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_sector = get_array_value($options, "id_sector");
        if($id_sector){
            $where .= " AND $air_sectors_table.id = $id_sector";
        }

        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "$select_greatest_or_least AS value";
        $sql .= " FROM $air_records_values_p_table";
        $sql .= " INNER JOIN $air_variables_table ON $air_records_values_p_table.id_variable = $air_variables_table.id";
        $sql .= " INNER JOIN $air_records_table ON $air_records_values_p_table.id_record = $air_records_table.id";
        $sql .= " INNER JOIN $air_sectors_table ON $air_records_table.id_air_sector = $air_sectors_table.id";
        $sql .= " WHERE $air_records_values_p_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $air_records_table.deleted = 0";
        $sql .= " $where";
        $sql .= " GROUP BY $air_records_values_p_table.id HAVING MAX($air_records_values_p_table.id_upload)";

        return $this->db->query($sql);

    }

    /**
     * get_last_upload_data_1D_by_date
     * 
     * Consulta la última carga de datos de una variable de una fecha determinada, 
     * para cargar los datos de los gráficos y calheatmaps del módulo de Pronósticos / Sector
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     *      $options['id_record'] => (int) id de Registro de Pronóstico. <br/>
     *      $options['date'] => (date) fecha de carga de datos de la Variable. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
	 * @return object
	 */
    function get_last_upload_data_1D_by_date($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');
        $air_records_values_p_min_table = $this->db->dbprefix('air_records_values_p_min');
        $air_records_values_p_max_table = $this->db->dbprefix('air_records_values_p_max');
        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');
        
        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }

        $date = get_array_value($options, "date");
        if($date){
            $where .= " AND $air_records_values_p_table.date = '$date'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql_subquery = "SELECT MAX($air_records_values_uploads_table.id) AS last_upload";
        $sql_subquery .= " FROM $air_records_values_uploads_table";
        $sql_subquery .= " INNER JOIN $air_records_values_p_table ON $air_records_values_uploads_table.id = $air_records_values_p_table.id_upload";
        $sql_subquery .= " WHERE $air_records_values_p_table.deleted = 0";
        $sql_subquery .= " $where";

        // SI LA VARIABLE ES PM10, BUSCA VALORES MIN Y MAX DEL INTERVALO DE CONFIANZA
        if($id_variable == 9){

            $sql = "SELECT $air_records_values_p_table.*,";

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= "$air_records_values_p_min_table.time_$t as time_min_$t, ";
            }

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= "$air_records_values_p_max_table.time_$t as time_max_$t, ";
            }

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= ($t != 23) ? "$air_records_values_p_porc_conf_table.time_$t as time_porc_conf_$t, " : "$air_records_values_p_porc_conf_table.time_$t as time_porc_conf_$t";
            }

            $sql .= " FROM $air_records_values_p_table";

            $sql .= " LEFT JOIN $air_records_values_p_min_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_min_table.id_values_p";
            $sql .= " LEFT JOIN $air_records_values_p_max_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_max_table.id_values_p";

            $sql .= " LEFT JOIN $air_records_values_p_porc_conf_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_porc_conf_table.id_values_p";

            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";
            $sql .= " AND $air_records_values_p_table.id_upload = ($sql_subquery)";

        } else {

            $sql = "SELECT $air_records_values_p_table.*";
            $sql .= " FROM $air_records_values_p_table";
            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";
            $sql .= " AND $air_records_values_p_table.id_upload = ($sql_subquery)";

        }
       
        return $this->db->query($sql);

    }

    // 
    /**
     * get_current_max_variable_value
     * 
     * Consulta el valor máximo de los datos de una Variable de un Sector, de una fecha y hora determinada,
     * utilizado para las leyendas de las variables en el mapa, en la sección de modelo Numérico en el módulo
     * de Pronósticos / Sector
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_variable'] => (int) id de Variable. <br/>
     *      $options['id_sector'] => (int) id de Sector. <br/>
     *      $options['date'] => (date) fecha de carga de datos de la Variable. <br/>
     * @uses string $this->db->dbprefix('air_records_values_p') tabla air_records_values_p
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_records') tabla air_records
	 * @return object
	 */
    function get_current_max_variable_value($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_sectors_table = $this->db->dbprefix('air_sectors');
        $air_records_table = $this->db->dbprefix('air_records');
        
        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_sector = get_array_value($options, "id_sector");
        if($id_sector){
            $where .= " AND $air_sectors_table.id = $id_sector";
        }

        $date = get_array_value($options, "date");
        if($date){
            $where .= " AND $air_records_values_p_table.date = '$date'";
        }

        $select_time = "";
        $time = get_array_value($options, "time");
        if($time){
           $select_time .= "$air_records_values_p_table.$time";
        }


        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT MAX(tabla_virtual.max_value) AS max_value FROM";
        $sql .= " ( SELECT MAX( CAST($select_time AS DECIMAL(10,2)) ) AS max_value";
        $sql .= " FROM $air_records_values_p_table";

        $sql .= " INNER JOIN $air_variables_table ON $air_records_values_p_table.id_variable = $air_variables_table.id";
        $sql .= " INNER JOIN $air_records_table ON $air_records_values_p_table.id_record = $air_records_table.id";
        $sql .= " INNER JOIN $air_sectors_table ON $air_records_table.id_air_sector = $air_sectors_table.id";

        $sql .= "$where";
        $sql .= " AND $air_records_values_p_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " AND $air_records_values_p_table.latitude IS NOT NULL";
        $sql .= " AND $air_records_values_p_table.longitude IS NOT NULL";
        $sql .= " GROUP BY $air_records_values_p_table.id_upload";
        $sql .= " HAVING MAX($air_records_values_p_table.id_upload) ) AS tabla_virtual";

        return $this->db->query($sql);

    }

    function get_last_upload_from_variable($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_table = $this->db->dbprefix('air_records');
        
        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_modelo = get_array_value($options, "id_modelo");
        if($id_modelo){
            $where .= " AND $air_records_table.id_air_model = $id_modelo";
        }

        $tipo_carga = get_array_value($options, "tipo_carga");
        if($tipo_carga == "1D"){
            $where .= " AND $air_records_values_p_table.latitude IS NULL";
            $where .= " AND $air_records_values_p_table.longitude IS NULL";
        }
        if($tipo_carga == "2D"){
            $where .= " AND $air_records_values_p_table.latitude IS NOT NULL";
            $where .= " AND $air_records_values_p_table.longitude IS NOT NULL";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql_subquery = "SELECT MAX($air_records_values_p_table.id_upload) AS last_upload";
        $sql_subquery .= " FROM $air_records_values_p_table, $air_records_table";
        $sql_subquery .= " WHERE $air_records_values_p_table.id_record = $air_records_table.id";
        $sql_subquery .= " AND $air_records_values_p_table.deleted = 0";
        $sql_subquery .= " $where";

        $sql = "SELECT $air_records_values_p_table.*";
        $sql .= " FROM $air_records_values_p_table, $air_records_table";
        $sql .= " WHERE $air_records_values_p_table.id_record = $air_records_table.id";
        $sql .= " AND $air_records_values_p_table.deleted = 0";
        $sql .= " $where";
        $sql .= " AND $air_records_values_p_table.id_upload = ($sql_subquery)";

        return $this->db->query($sql);

    }

    function get_last_upload_data_from_yesterday($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');

        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $yesterday_date = get_array_value($options, "yesterday_date");
        if($yesterday_date){
            $where .= " AND $air_records_values_p_table.date >= '$yesterday_date'";
        }

        $sql = "SELECT $air_records_values_p_table.*";
        $sql .= " FROM $air_records_values_p_table";
        $sql .= " WHERE 1";
        $sql .= " AND $air_records_values_p_table.deleted = 0";
        $sql .= " $where";

        return $this->db->query($sql);

    }

    /**
     * comparación de los últimos 3 días pero las ultimas 24 hrs del pronóstico, de los últimos 3 archivos para esa estación y modelo.
     * BUSCAR EN LA TABLA air_records_values_p EL ÚLTIMO REGISTRO FILTRADO POR EL CAMPO date PARA UNA VARIABLE Y UN ID RECORD
     */
    function get_last_record_of_upload_data($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_values_p_min_table = $this->db->dbprefix('air_records_values_p_min');
        $air_records_values_p_max_table = $this->db->dbprefix('air_records_values_p_max');
        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');

        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $id_record = get_array_value($options, "id_record");
        if($id_record){
            $where .= " AND $air_records_values_p_table.id_record = $id_record";
        }
        
        $date = get_array_value($options, "date");
        if($date){
            $where .= " AND $air_records_values_p_table.date = '$date'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        // SI LA VARIABLE ES PM10 INCLUIR INTERVALO DE CONFIANZA
        if($id_variable == 9){

            $sql = "SELECT $air_records_values_p_table.*,";

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= "$air_records_values_p_min_table.time_$t as time_min_$t, ";
            }

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= "$air_records_values_p_max_table.time_$t as time_max_$t, ";
            }

            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= ($t != 23) ? "$air_records_values_p_porc_conf_table.time_$t as time_porc_conf_$t, " : "$air_records_values_p_porc_conf_table.time_$t as time_porc_conf_$t";
            }

            $sql .= " FROM $air_records_values_p_table";

            $sql .= " LEFT JOIN $air_records_values_p_min_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_min_table.id_values_p";
            $sql .= " LEFT JOIN $air_records_values_p_max_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_max_table.id_values_p";

            $sql .= " LEFT JOIN $air_records_values_p_porc_conf_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_porc_conf_table.id_values_p";

            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";
            $sql .= " ORDER BY $air_records_values_p_table.id DESC LIMIT 1";

        } else {

            $sql = "SELECT $air_records_values_p_table.*";
            $sql .= " FROM $air_records_values_p_table";
            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";
            $sql .= " ORDER BY $air_records_values_p_table.id DESC LIMIT 1";

        }

        return $this->db->query($sql);

    }

    /**
     * Elimina los registros que sean menores o iguales al id_upload recibido
     */
    function delete_old_values_from_an_id_upload($id_upload = 0){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "DELETE FROM $air_records_values_p_table";
        $sql .= " WHERE $air_records_values_p_table.id_upload <= $id_upload";
		
        return $this->db->query($sql);

    }

    function reset_ids(){
        
        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        $this->db->query("CREATE TEMPORARY TABLE new_id_table AS SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_table");
        // ACTUALIZAR EL CAMPO ID DE LA TABLA. SE ACTUALIZARÁN EN CASCADA LAS TABLAS:
        // air_records_values_p_min, air_records_values_p_max
		$this->db->query("UPDATE $air_records_values_p_table INNER JOIN new_id_table ON $air_records_values_p_table.id = new_id_table.id SET $air_records_values_p_table.id = new_id_table.new_id");
        $this->db->query("DROP TABLE new_id_table");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
        $this->db->trans_complete();

        return $this->db->trans_status();

    }

}
