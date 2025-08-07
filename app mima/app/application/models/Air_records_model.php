<?php
/**
 * Archivo Modelo Registros
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Registros
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Registros
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Registros
 * @property private $table El nombre de la tabla de base de datos de la entidad Registros
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_records_model extends Crud_model {

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
        $this->table = 'air_records';
        parent::__construct($this->table);
    }

    /**
     * get_details
     * 
     * Consulta los Registros de Aire de tipo Pronóstico de las variables de Calidad del aire y Meteorológicas 
	 * de los Sectores / Estaciones asociadas a un Cliente / Proyecto, para enlistarlos en la vista principal del módulo.
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id'] => (int) id del Sector. <br/>
     *      $options['id_client'] => (int) id de Cliente. <br/>
     *      $options['id_project'] => (int) id de Proyecto. <br/>
     *      $options['id_air_sector'] => (int) id de Sector. <br/>
     *      $options['id_air_station'] => (int) id de Estación. <br/>
     *      $options['id_air_model'] => (int) id de Modelo. <br/>
     *      $options['id_air_record_type'] => (int) id de Tipo de Registro (Monitoreo o Pronóstico). <br/>
     * @uses string $this->db->dbprefix('air_records') tabla air_records
     * @uses string $this->db->dbprefix('clients') tabla clients
     * @uses string $this->db->dbprefix('projects') tabla projects
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_models')tabla air_models
	 * @return object
	 */
    function get_details($options = array()) {
        
        $air_records_table = $this->db->dbprefix('air_records');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$air_sectors_table = $this->db->dbprefix('air_sectors');
        $air_stations_table = $this->db->dbprefix('air_stations');
        $air_models_table = $this->db->dbprefix('air_models');

        $where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where .= " AND $air_records_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client){
            $where .= " AND $air_records_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project){
            $where .= " AND $air_records_table.id_project = $id_project";
        }

        $id_air_sector = get_array_value($options, "id_air_sector");
        if($id_air_sector){
            $where .= " AND $air_records_table.id_air_sector = $id_air_sector";
        }

        $id_air_station = get_array_value($options, "id_air_station");
        if($id_air_station){
            $where .= " AND $air_records_table.id_air_station = $id_air_station";
        }

        $id_air_model = get_array_value($options, "id_air_model");
        if($id_air_model){
            $where .= " AND $air_records_table.id_air_model = $id_air_model";
        }

        $id_air_record_type = get_array_value($options, "id_air_record_type");
        if($id_air_record_type){
            $where .= " AND $air_records_table.id_air_record_type = $id_air_record_type";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = " SELECT $air_records_table.*, $clients_table.company_name AS name_client, $projects_table.title AS name_project,";
        $sql .= " $air_sectors_table.name AS name_sector, $air_stations_table.name AS name_station, $air_models_table.name AS name_model";
        $sql .= " FROM $air_records_table";
        $sql .= " INNER JOIN $clients_table";
        $sql .= " ON $air_records_table.id_client = $clients_table.id";
        $sql .= " INNER JOIN $projects_table";
        $sql .= " ON $air_records_table.id_project = $projects_table.id";
        $sql .= " INNER JOIN $air_sectors_table";
        $sql .= " ON $air_records_table.id_air_sector = $air_sectors_table.id";
        $sql .= " LEFT JOIN $air_stations_table";
        $sql .= " ON $air_records_table.id_air_station = $air_stations_table.id";
        $sql .= " INNER JOIN $air_models_table";
        $sql .= " ON $air_records_table.id_air_model = $air_models_table.id";
        $sql .= " WHERE $clients_table.deleted = 0";
        $sql .= " AND $projects_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $air_models_table.deleted = 0";
        $sql .= " $where";
        $sql .= " ORDER BY $air_records_table.id ASC";

        return $this->db->query($sql);

    }

    /**
    * get_details_minutes
    * 
    * Obtiene los registros de las variables de una estación para la frecuencia minutos dentro de un rango de fechas, horas, días, variables específicas. Además, trae junto a cada registro la fecha, hora, minuto, día de la semana en que se capturo el dato.
    * 
    * @author Christopher Mauricio Sam Venegas
    * @access public
    * @param array $options Arreglo con los parametros delimitadores de la query.
    * @uses Tabla air_stations_values_1m
    * @return Object retorna un objeto Result con todos los registros de la frecuencia minutos dentro de los parametros especificados.
    */
    function get_details_minutes($options = array()) {
        
        $air_stations_values_m_table = $this->db->dbprefix('air_stations_values_1m');

        $null_value = '-';
        $null_replace = get_array_value($options, "null_value");
        if (isset($null_replace)) {
            $null_value = $null_replace;
        }

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_stations_values_m_table.id = $id ";
        }
        
        $id_estacion = get_array_value($options, "id_estacion");
        if ($id_estacion) {
            $where .= " AND $air_stations_values_m_table.id_station = $id_estacion ";
        }
        
        $days_where = "";
        $days = get_array_value($options, "days");
        if (count($days) > 0 && count($days) < 7) {
            //$days_where .= " AND hours.weekday IN (".implode(',', $days).") ";
            // $days_where .= " AND $acustic_dates_table.weekday IN (".implode(',', $days).") ";
            $days_where .= " AND WEEKDAY($air_stations_values_m_table.date) IN  (".implode(',', $days).") ";
        }

        $hours_where = "";
        $hours = get_array_value($options, "hours");
        if (count($hours) > 0 && count($hours) < 24) {
            $hours_where .= " AND $air_stations_values_m_table.hour IN (".implode(',', $hours).") ";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        $start_hour = get_array_value($options, "start_hour");
        $end_hour = get_array_value($options, "end_hour");
        $start_minute = get_array_value($options, "start_minute");
        $end_minute = get_array_value($options, "end_minute");

        /*if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT(hours.fecha,' ',hours.hora,':',hours.minuto) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }*/

        if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT($air_stations_values_m_table.date,' ',$air_stations_values_m_table.hour,':',$air_stations_values_m_table.minute) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT ";
        $sql .= "$air_stations_values_m_table.id as id_dato,";
        $sql .= "$air_stations_values_m_table.date,";
        $sql .= "$air_stations_values_m_table.hour,";
        $sql .= "$air_stations_values_m_table.minute";
        // $sql .= "$air_stations_values_m_table.data ";

        $array_variables = get_array_value($options, "variables");
        foreach($array_variables as $id_variable => $variable) {

            // Si el valor almacenado en la variable de la columna json es el texto "null" o si no hay valor para esa variable se mostrara un guion "-", pero si hay un valor para la variable se retorna su valor correspondiente. 
            $sql .= ",  IF( $air_stations_values_m_table.data->'$.\"$id_variable\"' LIKE 'null' OR $air_stations_values_m_table.data->'$.\"$id_variable\"' IS NULL , '-', $air_stations_values_m_table.data->'$.\"$id_variable\"' ) AS '$variable' ";
            
        }

        $sql .= "FROM $air_stations_values_m_table ";
        $sql .= "WHERE $air_stations_values_m_table.deleted = 0 ";
        $sql .= $days_where;
        $sql .= $hours_where;
        $sql .= $where;
        $sql .= " ORDER BY $air_stations_values_m_table.date, $air_stations_values_m_table.hour, $air_stations_values_m_table.minute ASC";
            
        set_time_limit(500);
        return $this->db->query($sql); 

    }

    function get_details_5_min($options = array()) {
        
        $air_stations_values_m_table = $this->db->dbprefix('air_stations_values_5m');

        $null_value = '-';
        $null_replace = get_array_value($options, "null_value");
        if (isset($null_replace)) {
            $null_value = $null_replace;
        }

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_stations_values_m_table.id = $id ";
        }
        
        $id_estacion = get_array_value($options, "id_estacion");
        if ($id_estacion) {
            $where .= " AND $air_stations_values_m_table.id_station = $id_estacion ";
        }
        
        $days_where = "";
        $days = get_array_value($options, "days");
        if (count($days) > 0 && count($days) < 7) {
            //$days_where .= " AND hours.weekday IN (".implode(',', $days).") ";
            // $days_where .= " AND $acustic_dates_table.weekday IN (".implode(',', $days).") ";
            $days_where .= " AND WEEKDAY($air_stations_values_m_table.date) IN  (".implode(',', $days).") ";
        }

        $hours_where = "";
        $hours = get_array_value($options, "hours");
        if (count($hours) > 0 && count($hours) < 24) {
            $hours_where .= " AND $air_stations_values_m_table.hour IN (".implode(',', $hours).") ";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        $start_hour = get_array_value($options, "start_hour");
        $end_hour = get_array_value($options, "end_hour");
        $start_minute = get_array_value($options, "start_minute");
        $end_minute = get_array_value($options, "end_minute");

        /*if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT(hours.fecha,' ',hours.hora,':',hours.minuto) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }*/

        if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT($air_stations_values_m_table.date,' ',$air_stations_values_m_table.hour,':',$air_stations_values_m_table.minute) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT ";
        $sql .= "$air_stations_values_m_table.id as id_dato,";
        $sql .= "$air_stations_values_m_table.date,";
        $sql .= "$air_stations_values_m_table.hour,";
        $sql .= "$air_stations_values_m_table.minute";
        // $sql .= "$air_stations_values_m_table.data ";

        $array_variables = get_array_value($options, "variables");
        foreach($array_variables as $id_variable => $variable) {

            // Si el valor almacenado en la variable de la columna json es el texto "null" o si no hay valor para esa variable se mostrara un guion "-", pero si hay un valor para la variable se retorna su valor correspondiente. 
            $sql .= ",  IF( $air_stations_values_m_table.data->'$.\"$id_variable\"' LIKE 'null' OR $air_stations_values_m_table.data->'$.\"$id_variable\"' IS NULL , '-', $air_stations_values_m_table.data->'$.\"$id_variable\"' ) AS '$variable' ";
            
        }

        $sql .= "FROM $air_stations_values_m_table ";
        $sql .= "WHERE $air_stations_values_m_table.deleted = 0 ";
        $sql .= $days_where;
        $sql .= $hours_where;
        $sql .= $where;
        $sql .= " ORDER BY $air_stations_values_m_table.date, $air_stations_values_m_table.hour, $air_stations_values_m_table.minute ASC";
            
        set_time_limit(500);
        return $this->db->query($sql); 

    }

    function get_details_15_min($options = array()) {
        
        $air_stations_values_m_table = $this->db->dbprefix('air_stations_values_15m');

        $null_value = '-';
        $null_replace = get_array_value($options, "null_value");
        if (isset($null_replace)) {
            $null_value = $null_replace;
        }

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_stations_values_m_table.id = $id ";
        }
        
        $id_estacion = get_array_value($options, "id_estacion");
        if ($id_estacion) {
            $where .= " AND $air_stations_values_m_table.id_station = $id_estacion ";
        }
        
        $days_where = "";
        $days = get_array_value($options, "days");
        if (count($days) > 0 && count($days) < 7) {
            //$days_where .= " AND hours.weekday IN (".implode(',', $days).") ";
            // $days_where .= " AND $acustic_dates_table.weekday IN (".implode(',', $days).") ";
            $days_where .= " AND WEEKDAY($air_stations_values_m_table.date) IN  (".implode(',', $days).") ";
        }

        $hours_where = "";
        $hours = get_array_value($options, "hours");
        if (count($hours) > 0 && count($hours) < 24) {
            $hours_where .= " AND $air_stations_values_m_table.hour IN (".implode(',', $hours).") ";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        $start_hour = get_array_value($options, "start_hour");
        $end_hour = get_array_value($options, "end_hour");
        $start_minute = get_array_value($options, "start_minute");
        $end_minute = get_array_value($options, "end_minute");

        /*if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT(hours.fecha,' ',hours.hora,':',hours.minuto) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }*/

        if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT($air_stations_values_m_table.date,' ',$air_stations_values_m_table.hour,':',$air_stations_values_m_table.minute) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT ";
        $sql .= "$air_stations_values_m_table.id as id_dato,";
        $sql .= "$air_stations_values_m_table.date,";
        $sql .= "$air_stations_values_m_table.hour,";
        $sql .= "$air_stations_values_m_table.minute";
        // $sql .= "$air_stations_values_m_table.data ";

        $array_variables = get_array_value($options, "variables");
        foreach($array_variables as $id_variable => $variable) {

            // Si el valor almacenado en la variable de la columna json es el texto "null" o si no hay valor para esa variable se mostrara un guion "-", pero si hay un valor para la variable se retorna su valor correspondiente. 
            $sql .= ",  IF( $air_stations_values_m_table.data->'$.\"$id_variable\"' LIKE 'null' OR $air_stations_values_m_table.data->'$.\"$id_variable\"' IS NULL , '-', $air_stations_values_m_table.data->'$.\"$id_variable\"' ) AS '$variable' ";
            
        }

        $sql .= "FROM $air_stations_values_m_table ";
        $sql .= "WHERE $air_stations_values_m_table.deleted = 0 ";
        $sql .= $days_where;
        $sql .= $hours_where;
        $sql .= $where;
        $sql .= " ORDER BY $air_stations_values_m_table.date, $air_stations_values_m_table.hour, $air_stations_values_m_table.minute ASC";
            
        set_time_limit(500);
        return $this->db->query($sql); 

    }

    function get_details_1_hour($options = array()) {
        
        $air_stations_values_1h_table = $this->db->dbprefix('air_stations_values_1h');

        $null_value = '-';
        $null_replace = get_array_value($options, "null_value");
        if (isset($null_replace)) {
            $null_value = $null_replace;
        }

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_stations_values_1h_table.id = $id ";
        }
        
        $id_estacion = get_array_value($options, "id_estacion");
        if ($id_estacion) {
            $where .= " AND $air_stations_values_1h_table.id_station = $id_estacion ";
        }
        
        $days_where = "";
        $days = get_array_value($options, "days");
        if (count($days) > 0 && count($days) < 7) {
            //$days_where .= " AND hours.weekday IN (".implode(',', $days).") ";
            // $days_where .= " AND $acustic_dates_table.weekday IN (".implode(',', $days).") ";
            $days_where .= " AND WEEKDAY($air_stations_values_1h_table.date) IN  (".implode(',', $days).") ";
        }

        $hours_where = "";
        $hours = get_array_value($options, "hours");
        if (count($hours) > 0 && count($hours) < 24) {
            $hours_where .= " AND $air_stations_values_1h_table.hour IN (".implode(',', $hours).") ";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        $start_hour = get_array_value($options, "start_hour");
        $end_hour = get_array_value($options, "end_hour");

        /*if ($start_date && $start_hour && $start_minute && $end_date && $end_hour && $end_minute) {
            $where .= " AND CAST(CONCAT(hours.fecha,' ',hours.hora,':',hours.minuto) AS DATETIME) BETWEEN CAST('$start_date $start_hour:$start_minute' AS DATETIME) AND CAST('$end_date $end_hour:$end_minute' AS DATETIME)";
        }*/

        if ($start_date && $start_hour && $end_date && $end_hour) {
            $where .= " AND CAST(CONCAT($air_stations_values_1h_table.date,' ',$air_stations_values_1h_table.hour,':00') AS DATETIME) BETWEEN CAST('$start_date $start_hour:00' AS DATETIME) AND CAST('$end_date $end_hour:00' AS DATETIME)";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        
        $sql = "SELECT ";
        $sql .= "$air_stations_values_1h_table.id as id_dato,";
        $sql .= "$air_stations_values_1h_table.date,";
        $sql .= "$air_stations_values_1h_table.hour";
        // $sql .= "$air_stations_values_1h_table.data ";

        $array_variables = get_array_value($options, "variables");
        foreach($array_variables as $id_variable => $variable) {

            // Si el valor almacenado en la variable de la columna json es el texto "null" o si no hay valor para esa variable se mostrara un guion "-", pero si hay un valor para la variable se retorna su valor correspondiente. 
            $sql .= ",  IF( $air_stations_values_1h_table.data->'$.\"$id_variable\"' LIKE 'null' OR $air_stations_values_1h_table.data->'$.\"$id_variable\"' IS NULL , '-', $air_stations_values_1h_table.data->'$.\"$id_variable\"' ) AS '$variable' ";
            
        }


        $sql .= "FROM $air_stations_values_1h_table ";
        $sql .= "WHERE $air_stations_values_1h_table.deleted = 0 ";
        $sql .= $days_where;
        $sql .= $hours_where;
        $sql .= $where;
        $sql .= " ORDER BY $air_stations_values_1h_table.date, $air_stations_values_1h_table.hour ASC";
            
        set_time_limit(500);
        return $this->db->query($sql); 

    }

    /**
     * get_data
     * 
     * Consulta los Registros de Aire de tipo Pronóstico del sistema mediante una consulta amigable 
	 * para ser descargados por parte del equipo de particulas.
	 *
	 * @author Álvaro Donoso
     * @access public
     *     
     * @uses string $this->db->dbprefix('air_records') tabla air_records
     * @uses string $this->db->dbprefix('clients') tabla clients
     * @uses string $this->db->dbprefix('projects') tabla projects
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_models')tabla air_models
	 * @return object
	 */
    function get_data($options = array()) {
        
        
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');
        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_table = $this->db->dbprefix('air_records');
        $air_models_table = $this->db->dbprefix('air_models');
        $air_stations_table = $this->db->dbprefix('air_stations');
		$air_variables_table = $this->db->dbprefix('air_variables');

        $where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where .= " AND $air_records_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client){
            $where .= " AND $air_records_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project){
            $where .= " AND $air_records_table.id_project = $id_project";
        }

        $id_air_sector = get_array_value($options, "id_air_sector");
        if($id_air_sector){
            $where .= " AND $air_records_table.id_air_sector = $id_air_sector";
        }

        $id_air_station = get_array_value($options, "id_air_station");
        if($id_air_station){
            $where .= " AND $air_records_table.id_air_station = $id_air_station";
        }

        $id_air_model = get_array_value($options, "id_air_model");
        if($id_air_model){
            $where .= " AND $air_records_table.id_air_model = $id_air_model";
        }

        $id_air_record_type = get_array_value($options, "id_air_record_type");
        if($id_air_record_type){
            $where .= " AND $air_records_table.id_air_record_type = $id_air_record_type";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT ";
        $sql .= " $projects_table.title as 'project_name',";
        $sql .= " $air_records_values_uploads_table.model_creation_date AS 'model_creation_date',";
        $sql .= " $air_models_table.name AS 'model',";
        $sql .= " $air_records_table.name AS 'Estación - Modelo',";
        $sql .= " $air_stations_table.name AS 'station',";
        $sql .= " $air_variables_table.name AS 'variable',";
        $sql .= " $air_variables_table.sigla AS 'variable_initials',";
        $sql .= " $air_records_values_p_table.*";
        $sql .= " FROM ";
        $sql .= " $air_records_values_p_table";
        $sql .= " LEFT JOIN $air_records_values_uploads_table ON $air_records_values_uploads_table.id = $air_records_values_p_table.id_upload ";
        $sql .= " LEFT JOIN $clients_table ON $clients_table.id = $air_records_values_p_table.id_client ";
        $sql .= " LEFT JOIN $projects_table ON $projects_table.id = $air_records_values_p_table.id_project ";
        $sql .= " LEFT JOIN $air_records_table ON $air_records_table.id = $air_records_values_p_table.id_record ";
        $sql .= " LEFT JOIN $air_models_table ON $air_models_table.id = $air_records_table.id_air_model";
        $sql .= " LEFT JOIN $air_stations_table ON $air_stations_table.id = $air_records_table.id_air_station ";
        $sql .= " LEFT JOIN $air_variables_table ON $air_variables_table.id = $air_records_values_p_table.id_variable";
        $sql .= " WHERE 1 $where";

        return $this->db->query($sql);

    }

}
