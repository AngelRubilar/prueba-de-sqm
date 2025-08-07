<?php
/**
 * Archivo Modelo Estaciones
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Estaciones
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Estaciones
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Estaciones
 * @property private $table El nombre de la tabla de base de datos de la entidad Estaciones
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_stations_model extends Crud_model
{

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
    function __construct()
    {
        $this->load->helper('database');
        $this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        $this->table = 'air_stations';
        parent::__construct($this->table);
    }

    /**
     * get_details
     * 
     * Consulta datos de una Estación específica o Estaciones asociados a un Cliente y/o Proyecto / Sector
     *
     * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id'] => (int) id de Estación. <br/>
     *      $options['id_client'] => (int) id de Cliente. <br/>
     *      $options['id_project'] => (int) id de Proyecto. <br/>
     *      $options['id_air_sector'] => (int) id de Sector. <br/>
     *      $options['id_air_record_type'] => (int) id de Tipo de Registro (Monitoreo o Pronóstico). <br/>
     *      $options['id_air_model'] => (int) id de Modelo. <br/>
     *      $options['created_by'] => (int) id de Usuario creador de la Estación. <br/>
     *      $options['modified_by'] => (int) id del último usuario que actualizó la Estación. <br/>
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @return object
     */
    /*function get_details($options = array())
    {

        $air_stations_table = $this->db->dbprefix('air_stations');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_stations_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $air_stations_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $air_stations_table.id_project = $id_project";
        }

        $id_air_sector = get_array_value($options, "id_air_sector");
        if ($id_air_sector) {
            $where .= " AND $air_stations_table.id_air_sector = $id_air_sector";
        }

        $id_air_record_type = get_array_value($options, "id_air_record_type");
        if ($id_air_record_type) {
            $where .= " AND $air_stations_table.id_air_record_type = $id_air_record_type";
        }

        $id_air_model = get_array_value($options, "id_air_model");
        if ($id_air_model) {
            $where .= " AND $air_stations_table.id_air_model = $id_air_model";
        }

        $is_active = get_array_value($options, "is_active");
        if ($is_active) {
            $where .= " AND $air_stations_table.is_active = $is_active";
        }

        $is_monitoring = get_array_value($options, "is_monitoring");
        if ($is_monitoring) {
            $where .= " AND $air_stations_table.is_monitoring = $is_monitoring";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $air_stations_table.created_by = $created_by";
        }

        $modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $air_stations_table.modified_by = $modified_by";
        }

        $ids = get_array_value($options, "ids");
        if (count($ids)) {
            $where .= " AND $air_stations_table.id IN (" . implode(",", $ids) . ")";
        }

        $order_by = get_array_value($options, "order_by");
        if (count($order_by)) {

            $field = $order_by[0]; // EJ: is_receptor
            $order = $order_by[1]; // EJ: ASC || DESC
            $where .= " ORDER BY $air_stations_table.$field $order";

        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT $air_stations_table.*";
        $sql .= " FROM $air_stations_table";
        $sql .= " WHERE $air_stations_table.deleted = 0 AND $air_stations_table.id_air_sector = $id_air_sector";
        $sql .= " $where";

        return $this->db->query($sql);

    }*/

    function get_details($options = array())
    {
        $air_stations_table = $this->db->dbprefix('air_stations');

        $where = " WHERE $air_stations_table.deleted = 0"; // Iniciamos con el filtro de registros no eliminados

        // Filtro por id
        $id = get_array_value($options, "id");
        log_message('error', "id primer log .$id");
        if ($id) {
            $where .= " AND $air_stations_table.id = $id";
        }

        // Filtro por id_client
        $id_client = get_array_value($options, "id_client");
        log_message('error', "id segundo log .$id_client");
        if ($id_client) {
            $where .= " AND $air_stations_table.id_client = $id_client";
        }

        // Filtro por id_project
        $id_project = get_array_value($options, "id_project");
        log_message('error', "id tercer log .$id_project");
        if ($id_project) {
            $where .= " AND $air_stations_table.id_project = $id_project";
        }

        // Filtro por id_air_sector
        $id_air_sector = get_array_value($options, "id_air_sector");
        log_message('error', "id tercer log $id_air_sector");
        if ($id_air_sector) {
            $where .= " AND $air_stations_table.id_air_sector = $id_air_sector";
        }

        // Filtro por id_air_record_type
        $id_air_record_type = get_array_value($options, "id_air_record_type");
        if ($id_air_record_type) {
            $where .= " AND $air_stations_table.id_air_record_type = $id_air_record_type";
        }

        // Filtro por id_air_model
        $id_air_model = get_array_value($options, "id_air_model");
        if ($id_air_model) {
            $where .= " AND $air_stations_table.id_air_model = $id_air_model";
        }

        // Filtro por is_active
        if (isset($options['is_active'])) {
            $is_active = $options['is_active'];
            $where .= " AND $air_stations_table.is_active = $is_active";
        }

        // Filtro por is_monitoring
        if (isset($options['is_monitoring'])) {
            $is_monitoring = $options['is_monitoring'];
            $where .= " AND $air_stations_table.is_monitoring = $is_monitoring";
        }

        // Filtro por created_by
        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $air_stations_table.created_by = $created_by";
        }

        // Filtro por modified_by
        $modified_by = get_array_value($options, "modified_by");
        if ($modified_by) {
            $where .= " AND $air_stations_table.modified_by = $modified_by";
        }

        // Filtro por array de ids
        $ids = get_array_value($options, "ids");
        if (!empty($ids) && is_array($ids)) {
            $where .= " AND $air_stations_table.id IN (" . implode(",", $ids) . ")";
        }

        // Filtro por orden
        $order_by = get_array_value($options, "order_by");
        if (!empty($order_by) && is_array($order_by) && count($order_by) == 2) {
            $field = $order_by[0]; // Campo por el cual se ordenará
            $order = $order_by[1]; // ASC o DESC
            $where .= " ORDER BY $air_stations_table.$field $order";
        }

        // Habilitar consultas grandes si es necesario
        $this->db->query('SET SQL_BIG_SELECTS=1');

        // Construcción de la consulta final
        $sql = "SELECT $air_stations_table.* FROM $air_stations_table";
        $sql .= $where;

        return $this->db->query($sql);
    }

    /**
     * get_variables_of_station
     * 
     * Consulta el detalle de las Variables asociadas a una Estación
     *
     * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_air_station'] => (int) id de Estación. <br/>
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_stations_rel_variables') tabla air_stations_rel_variables
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_variables_types') tabla air_variables_types
     * @uses string $this->db->dbprefix('tipo_unidad') tabla tipo_unidad
     * @return object
     */
    function get_variables_of_station($options = array())
    {

        $air_stations_table = $this->db->dbprefix('air_stations');
        $air_stations_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');
        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_variables_types_table = $this->db->dbprefix('air_variables_types');
        $fc_db = getFCBD();
        $tipo_unidad_table = $this->load->database(getFCBD(), TRUE)->dbprefix('tipo_unidad');

        $where = "";
        $id_air_station = get_array_value($options, "id_air_station");
        if ($id_air_station) {
            $where .= " AND $air_stations_table.id = $id_air_station";
        }

        $sql_subquery = "(SELECT $air_variables_table.id AS id_air_variable, $air_variables_table.name AS name_air_variable,";
        $sql_subquery .= " $air_variables_table.id_air_variable_type, $air_variables_types_table.name AS name_variable_type,";
        $sql_subquery .= " $air_variables_table.id_unit_type AS id_unit_type_variable, $tipo_unidad_table.nombre AS name_unit_type_variable,";
        $sql_subquery .= " $air_variables_table.sigla AS sigla_variable,  $air_variables_table.alias as variable_alias, $air_variables_table.icono AS icono_variable";
        $sql_subquery .= " FROM $air_variables_table, $air_variables_types_table, $fc_db.$tipo_unidad_table";
        $sql_subquery .= " WHERE $air_variables_table.id_air_variable_type = $air_variables_types_table.id";
        $sql_subquery .= " AND $air_variables_table.id_unit_type = $tipo_unidad_table.id) AS table_variables";

        $sql = "SELECT table_variables.*, $air_stations_table.id AS id_air_station";
        $sql .= " FROM $air_stations_table, $air_stations_rel_variables_table, $sql_subquery";
        $sql .= " WHERE $air_stations_table.id = $air_stations_rel_variables_table.id_air_station";
        $sql .= " AND $air_stations_rel_variables_table.id_air_variable = table_variables.id_air_variable";
        $sql .= " AND $air_stations_table.deleted = 0";
        $sql .= " AND $air_stations_rel_variables_table.deleted = 0";
        $sql .= " $where";

        return $this->db->query($sql);

    }

    /**
     * get_variables_by_station
     * 
     * Consulta el detalle de una Estación, su Sector y de sus Variables asociadas
     * de un Cliente / Proyecto
     *
     * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id_client'] => (int) id de Cliente. <br/>
     *      $options['id_project'] => (int) id de Proyecto. <br/>
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_stations_rel_variables') tabla air_stations_rel_variables
     * @return object
     */
    function get_variables_by_station($options = array())
    {

        $air_stations_table = $this->db->dbprefix('air_stations');
        $air_sectors_table = $this->db->dbprefix('air_sectors');
        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_stations_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');

        $where = "";
        $id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $air_stations_table.id_client = $id_client";
            $where .= " AND $air_sectors_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $air_stations_table.id_project = $id_project";
            $where .= " AND $air_sectors_table.id_project = $id_project";
        }

        $sql = " SELECT $air_stations_table.id AS id_air_station, $air_stations_table.name AS name_station,";
        $sql .= " $air_sectors_table.id AS id_air_sector, $air_sectors_table.name AS name_sector,";
        $sql .= " $air_variables_table.id AS id_air_variable, $air_variables_table.name AS name_variable";
        $sql .= " FROM $air_stations_table, $air_sectors_table, $air_variables_table, $air_stations_rel_variables_table";
        $sql .= " WHERE $air_stations_table.id_air_sector = $air_sectors_table.id";
        $sql .= " AND $air_stations_table.id = $air_stations_rel_variables_table.id_air_station";
        $sql .= " AND $air_stations_rel_variables_table.id_air_variable = $air_variables_table.id";
        $sql .= " AND $air_stations_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $air_stations_rel_variables_table.deleted = 0";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " $where";
        $sql .= " GROUP BY $air_stations_table.id, $air_variables_table.id";

        return $this->db->query($sql);

    }

    /**
     * is_load_code_exists
     * 
     * Valida que un código de carga de estación no se repita
     *
     * @author Gustavo Pinochet Altamirano
     * @access public
     * @param $load_code código de carga
     * @param $id de la estación
     * @return object
     */
    function is_load_code_exists($load_code, $id = 0)
    {
        $result = $this->get_all_where(array("load_code" => $load_code, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

    /**
     * is_load_code_api_exists
     * 
     * Valida que un código de carga vía API de estación no se repita
     *
     * @author Gustavo Pinochet Altamirano
     * @access public
     * @param $load_code_api código de carga de API
     * @param $id de la estación
     * @return object
     */
    function is_load_code_api_exists($load_code_api, $id = 0)
    {
        $result = $this->get_all_where(array("load_code_api" => $load_code_api, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

    function get_stations_by_load_codes($load_codes_api = [])
    {
        $placeholders = implode(',', array_fill(0, count($load_codes_api), '?'));
        $sql = "SELECT * FROM air_stations WHERE load_code_api IN ($placeholders)";
        //log_message('error', 'get_stations_by_load_codes ' . $sql . ' ' . json_encode($load_codes_api));
        $result = $this->db->query($sql, $load_codes_api)->result();
        //log_message('error', 'get_stations_by_load_codes ' . json_encode($result));
        return $this->db->query($sql, $load_codes_api);
    }


}
