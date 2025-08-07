<?php
/**
 * Archivo Modelo de relación Estación / Variable
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo de relación Estación / Variable
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @property private $table El nombre de la tabla de base de datos de la entidad de relación Estación / Variable
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_stations_rel_variables_model extends Crud_model
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
        $this->table = 'air_stations_rel_variables';
        parent::__construct($this->table);
    }

    function get_variables_of_station($id_estacion = NULL)
    {

        $air_stations_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');
        $air_stations_table = $this->db->dbprefix('air_stations');
        $air_variables_table = $this->db->dbprefix('air_variables');

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = " SELECT ";
        $sql .= " $air_stations_table.name AS station, ";
        $sql .= " $air_stations_rel_variables_table.*, ";
        $sql .= " $air_variables_table.name, ";
        $sql .= " $air_variables_table.sigla, ";
        $sql .= " $air_variables_table.id_unit_type, ";
        $sql .= " $air_variables_table.icono ";
        $sql .= " FROM $air_stations_table ";
        $sql .= " LEFT JOIN $air_stations_rel_variables_table ";
        $sql .= " ON $air_stations_table.id = $air_stations_rel_variables_table.id_air_station ";
        $sql .= " LEFT JOIN $air_variables_table ";
        $sql .= " ON $air_stations_rel_variables_table.id_air_variable = $air_variables_table.id  ";
        $sql .= " WHERE $air_stations_table.deleted = 0 AND $air_stations_rel_variables_table.deleted = 0 AND $air_variables_table.deleted = 0 ";
        $sql .= " AND $air_stations_table.id = $id_estacion ";

        return $this->db->query($sql);

    }

    function get_all_related_variables($station_ids = [])
    {
        $air_stations_rel_variables_table = $this->db->dbprefix('air_stations_rel_variables');
        $air_stations_table = $this->db->dbprefix('air_stations');
        $air_variables_table = $this->db->dbprefix('air_variables');

        //log_message('error', 'id_staciones: ' . implode(',', $station_ids));
        $id_staciones = [];
        foreach ($station_ids as $id) {
            $id_staciones[] = $id;
        }
        $this->db->query('SET SQL_BIG_SELECTS=1');
        // creamos marcadores de posicion
        $placeholders = implode(',', array_fill(0, count($id_staciones), '?'));

        // $sql = " SELECT ";
        // $sql .= " $air_stations_table.name AS station, ";
        // $sql .= " $air_stations_rel_variables_table.*, ";
        // $sql .= " $air_variables_table.name, ";
        // $sql .= " $air_variables_table.sigla, ";
        // $sql .= " $air_variables_table.sigla_api, ";
        // $sql .= " $air_variables_table.id_unit_type, ";
        // $sql .= " $air_variables_table.icono ";
        // $sql .= " FROM $air_stations_table ";
        // $sql .= " LEFT JOIN $air_stations_rel_variables_table ";
        // $sql .= " ON $air_stations_table.id = $air_stations_rel_variables_table.id_air_station ";
        // $sql .= " LEFT JOIN $air_variables_table ";
        // $sql .= " ON $air_stations_rel_variables_table.id_air_variable = $air_variables_table.id  ";
        // $sql .= " WHERE $air_stations_table.deleted = 0 AND $air_stations_rel_variables_table.deleted = 0 AND $air_variables_table.deleted = 0 ";
        // $sql .= " AND $air_stations_table.id IN ($station_ids) ";
        $sql = "
        SELECT 
            $air_stations_table.name AS station, 
            $air_stations_rel_variables_table.*, 
            $air_variables_table.name, 
            $air_variables_table.sigla, 
            $air_variables_table.sigla_api, 
            $air_variables_table.id_unit_type, 
            $air_variables_table.icono 
        FROM 
            $air_stations_table, 
            $air_stations_rel_variables_table, 
            $air_variables_table
        WHERE 
            $air_stations_table.id = $air_stations_rel_variables_table.id_air_station
            AND $air_stations_rel_variables_table.id_air_variable = $air_variables_table.id
            AND $air_stations_table.deleted = 0 
            AND $air_stations_rel_variables_table.deleted = 0 
            AND $air_variables_table.deleted = 0 
            AND $air_stations_table.id IN ($placeholders)
        ";

        return $this->db->query($sql, $station_ids);
    }

}
