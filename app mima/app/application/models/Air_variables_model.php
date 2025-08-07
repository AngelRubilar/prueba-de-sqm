<?php
/**
 * Archivo Modelo de Variables
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo de Variables
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @property private $table El nombre de la tabla de base de datos de la entidad Variables
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_variables_model extends Crud_model {

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
        $this->table = 'air_variables';
        $this->bd_mimasoft_fc = $this->load->database(getFCBD(), TRUE);
        parent::__construct($this->table);
    }

    /**
     * get_variables_of_sector
     * 
     * Consulta las Variables asociadas a las Estaciones de un Sector
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param int $id_sector id de Sector
     * @param array $options <br/>
     *      $options['id_sector'] => (int) id del Sector. <br/>
     *      $options['id_air_variable_type'] => (int) id de Tipo de Variable (Calidad del aire o Meteorológica). <br/>
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_stations_rel_variables') tabla air_stations_rel_variables
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
	 * @return object
	 */
    function get_variables_of_sector($id_sector = NULL, $options = array()) {

        $sectores_table = $this->db->dbprefix('air_sectors');
        $estaciones_table = $this->db->dbprefix('air_stations');
        $estaciones_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');
        $variables_table = $this->db->dbprefix('air_variables');
    
        $where = "";
        if ($id_sector) {
            $where .= " WHERE $sectores_table.id = $id_sector";
        }

        $id_air_variable_type = get_array_value($options, "id_air_variable_type");
        if ($id_air_variable_type) {
            $where .= " AND $variables_table.id_air_variable_type = $id_air_variable_type";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $sectores_table.id_client, $sectores_table.id_project, $sectores_table.id, $sectores_table.name, $estaciones_table.name as station_name, $estaciones_table.is_receptor as is_station_receptor, $variables_table.id AS id_variable, $variables_table.name AS variable_name, $variables_table.sigla, $variables_table.icono ";
        $sql .= " FROM $sectores_table";
        $sql .= " LEFT JOIN $estaciones_table";
        $sql .= " ON $sectores_table.id = $estaciones_table.id_air_sector AND $estaciones_table.deleted = 0 AND $sectores_table.deleted = 0";
        $sql .= " LEFT JOIN $estaciones_rel_variables_table";
        $sql .= " ON $estaciones_table.id = $estaciones_rel_variables_table.id_air_station AND $estaciones_rel_variables_table.deleted = 0";
        $sql .= " LEFT JOIN $variables_table";
        $sql .= " ON $estaciones_rel_variables_table.id_air_variable = $variables_table.id AND $variables_table.deleted = 0";
        $sql .= $where;
        $sql .= " GROUP BY $sectores_table.id_client, $sectores_table.id_project, $sectores_table.id, $sectores_table.name, $estaciones_table.name, $estaciones_table.is_receptor, $variables_table.id, $variables_table.name, $variables_table.sigla, $variables_table.icono";
    
        return $this->db->query($sql);
    }
    
    /**
     * get_variables_of_station
     * 
     * Consulta las Variables asociadas a una Estación
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $id_estacion id de Estación <br/>
     * @uses string $this->db->dbprefix('air_stations') tabla air_stations
     * @uses string $this->db->dbprefix('air_stations_rel_variables') tabla air_stations_rel_variables
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
	 * @return object
	 */
    function get_variables_of_station($id_estacion = NULL, $id_variable = NULL) {
        $estaciones_table = $this->db->dbprefix('air_stations');
        $estaciones_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');
        $variables_table = $this->db->dbprefix('air_variables');
    
        $where = "";
        if ($id_estacion) {
            $where .= " WHERE $estaciones_table.id = $id_estacion";
        }

        if ($id_variable){
            $where .= " AND $variables_table.id = $id_variable"; 
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $estaciones_table.*,
                $variables_table.id AS id_variable, $variables_table.name AS variable_name, $variables_table.sigla, $variables_table.icono, $variables_table.id_unit_type";
        $sql .= " FROM $estaciones_table";
        $sql .= " LEFT JOIN $estaciones_rel_variables_table";
        $sql .= " ON $estaciones_table.id = $estaciones_rel_variables_table.id_air_station AND $estaciones_rel_variables_table.deleted = 0 AND $estaciones_table.deleted = 0 ";
        $sql .= " LEFT JOIN $variables_table";
        $sql .= " ON $estaciones_rel_variables_table.id_air_variable = air_variables.id AND $variables_table.deleted = 0";
        $sql .= $where;
        
        return $this->db->query($sql);
    }

    /**
     * get_details
     * 
     * Consulta los datos de una Variable y su Tipo de Unidad
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id'] => (int) id de Variable. <br/>
     * @uses string $this->db->dbprefix('air_variables') tabla air_variables
     * @uses string $this->db->dbprefix('air_variables_types') tabla air_variables_types
     * @uses string $this->db->dbprefix('tipo_unidad') tabla tipo_unidad
	 * @return object
	 */
    function get_details($options = array()){

        $air_variables_table = $this->db->dbprefix('air_variables');
        $air_variables_types_table = $this->db->dbprefix('air_variables_types');
		$bd_mimasoft_fc = getFCBD();
        $unit_type_table = $this->bd_mimasoft_fc->dbprefix('tipo_unidad');
        
        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_variables_table.id = $id";
        }
		
        $this->db->query('SET SQL_BIG_SELECTS=1');
		
        $sql = "SELECT $air_variables_table.*, $air_variables_types_table.name AS name_variable_type, $unit_type_table.nombre AS name_unit_type";
        $sql .= " FROM  $air_variables_table, $air_variables_types_table, $bd_mimasoft_fc.$unit_type_table";
		$sql .= " WHERE $air_variables_table.id_air_variable_type = $air_variables_types_table.id";
        $sql .= " AND $air_variables_table.id_unit_type = $unit_type_table.id";
        $sql .= " AND $air_variables_table.deleted = 0";
        $sql .= " AND $air_variables_types_table.deleted = 0";
        $sql .= " AND $unit_type_table.deleted = 0";
        $sql .= " $where";

        return $this->db->query($sql);

    }

}

