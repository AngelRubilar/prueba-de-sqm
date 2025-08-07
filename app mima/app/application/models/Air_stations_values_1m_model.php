<?php

class Air_stations_values_1m_model extends Crud_model
{

    private $table = null;

    function __construct()
    {
        $this->table = 'air_stations_values_1m';
        parent::__construct($this->table);
    }

    function get_details($options)
    {
        $air_stations_values_1m_table = $this->db->dbprefix('air_stations_values_1m');

        $where = '';

        $id_station = get_array_value($options, 'id_station');
        if ($id_station) {
            $where .= " AND $air_stations_values_1m_table.id_station = $id_station";
        }

        $id_variable = get_array_value($options, 'id_variable');
        if ($id_variable) {
            $where .= " AND $air_stations_values_1m_table.data->'$.\"$id_variable\"' != \"\"";
            $where .= " AND $air_stations_values_1m_table.data->'$.\"$id_variable\"' NOT LIKE \"null\"";
        }


        $start_timestamp = get_array_value($options, 'start_timestamp');
        if ($start_timestamp) {
            $where .= " AND $air_stations_values_1m_table.timestamp >= $start_timestamp";
        }

        $end_timestamp = get_array_value($options, 'end_timestamp');
        if ($end_timestamp) {
            $where .= " AND $air_stations_values_1m_table.timestamp <= $end_timestamp";
        }

        $start_date = get_array_value($options, 'start_date');
        if ($start_date) {
            $where .= " AND CAST(CONCAT($air_stations_values_1m_table.date, ' ', $air_stations_values_1m_table.hour, ':', $air_stations_values_1m_table.minute) AS  DATETIME) >= '$start_date'";
        }

        $end_date = get_array_value($options, 'end_date');
        if ($end_date) {
            $where .= " AND CAST(CONCAT($air_stations_values_1m_table.date, ' ', $air_stations_values_1m_table.hour, ':', $air_stations_values_1m_table.minute) AS  DATETIME) <= '$end_date'";
        }


        $sql = "SELECT $air_stations_values_1m_table.*,
                $air_stations_values_1m_table.data->'$.\"$id_variable\"' AS var_value
                FROM $air_stations_values_1m_table
                WHERE $air_stations_values_1m_table.deleted = 0
                $where";
        //log_message('error', 'get_details: ' . $sql);

        return $this->db->query($sql);

    }

    // Obtener el ultimo registro de una variable. Este no debe estar vacio "".
    function get_last_value($options)
    {

        $air_stations_values_1m_table = $this->db->dbprefix('air_stations_values_1m');

        $where = '';

        $id_station = get_array_value($options, 'id_station');
        if ($id_station) {
            $where .= " AND $air_stations_values_1m_table.id_station = $id_station";
        }

        $id_variable = get_array_value($options, 'id_variable');


        $sql = "SELECT $air_stations_values_1m_table.*,
                $air_stations_values_1m_table.data->'$.\"$id_variable\"' AS var_value
                FROM $air_stations_values_1m_table
                WHERE $air_stations_values_1m_table.deleted = 0
                AND $air_stations_values_1m_table.data->'$.\"$id_variable\"' != \"\"
                $where
                ORDER BY  $air_stations_values_1m_table.timestamp DESC
                LIMIT 1";
        //log_message('error', 'get_last_value: ' . $sql);
        return $this->db->query($sql);

    }

    function get_last_values($options)
    {
        $air_stations_values_1m_table = $this->db->dbprefix('air_stations_values_1m');

        $where = '';

        $id_station = get_array_value($options, 'id_station');
        if ($id_station) {
            $where .= " AND $air_stations_values_1m_table.id_station = $id_station";
        }

        $id_variable = get_array_value($options, 'id_variable');

        $timestamp = get_array_value($options, 'timestamp');
        if ($timestamp) {
            $where .= " AND $air_stations_values_1m_table.timestamp > $timestamp";
        }

        $str_datetime = get_array_value($options, 'str_datetime');
        if ($str_datetime) {
            $where .= " AND CAST($air_stations_values_1m_table.date, ' ', $air_stations_values_1m_table.hour, ':', $air_stations_values_1m_table.minute) AS DATETIME > $str_datetime";
        }

        $sql = "SELECT $air_stations_values_1m_table.*,
                $air_stations_values_1m_table.data->'$.\"$id_variable\"' AS var_value";
        $sql .= "WHERE $air_stations_values_1m_table.deleted = 0";
        $sql .= $where;

        //log_message('error', 'get_last_values: ' . $sql);
        return $this->db->query($sql);
    }

    /** Obtener registros en un periodo entre la fecha del último valor ingresado menos X segundos 
     * y la fecha del último valor ingresado  */
    function get_last_period_values($options)
    {
        $air_stations_values_1m_table = $this->db->dbprefix('air_stations_values_1m');

        $id_station = get_array_value($options, 'id_station');

        $seconds = get_array_value($options, 'seconds');

        // Metodo 1: Demora alrededor de 7.6 segundos
        // Se obtiene el mayor valor de los timestamp de la estación
        $sql = "SELECT MAX($air_stations_values_1m_table.timestamp) AS last
                 FROM $air_stations_values_1m_table
                WHERE $air_stations_values_1m_table.id_station = $id_station
                AND $air_stations_values_1m_table.timestamp IS NOT NULL
                AND $air_stations_values_1m_table.deleted = 0";

        $last_timestamp = $this->db->query($sql)->result()[0]->last;
        $first_timestamp = $last_timestamp - $seconds;

        // Si no hay datos en la tabla
        if (!$last_timestamp) {
            $last_timestamp = 0;
        }

        // Se obtienen los datos entre el rango de tiempo de una hora
        $sql = "SELECT * FROM $air_stations_values_1m_table
                WHERE $air_stations_values_1m_table.timestamp BETWEEN $first_timestamp AND $last_timestamp
                AND $air_stations_values_1m_table.id_station = $id_station
                AND $air_stations_values_1m_table.timestamp IS NOT NULL
                AND $air_stations_values_1m_table.deleted = 0;";

        // Metodo 2: Demora alrededor de 11 segundos, 4 más que el metodo anterior
        /* $sql = "SELECT * FROM $air_stations_values_1m_table AS air_values_table
                WHERE air_values_table.timestamp >= (SELECT MAX($air_stations_values_1m_table.timestamp) - $seconds AS last
                    FROM $air_stations_values_1m_table
                    WHERE $air_stations_values_1m_table.deleted = 0
                    AND $air_stations_values_1m_table.id_station = $id_station
                    AND $air_stations_values_1m_table.timestamp IS NOT NULL)
                AND air_values_table.id_station = $id_station
                AND air_values_table.deleted = 0
                ORDER BY timestamp ASC;"; */
        //log_message('error', 'get_last_period_values: ' . $sql);
        return $this->db->query($sql);
    }

    function get_last_period_values_sp($options)
    {
        $air_stations_values_1m_table = $this->db->dbprefix('air_stations_values_1m');

        $id_station = get_array_value($options, 'id_station');

        $seconds = get_array_value($options, 'seconds');

        $query = $this->db->query("CALL get_last_period_values('air_stations_values_1m', $id_station, $seconds);");
        $result = $query->result();

        mysqli_next_result($this->db->conn_id);
        $query->free_result();
        //log_message('error', 'get_last_period_values_sp: ' . $sql);
        return $result;
    }

    /**
     * Obtiene una fila de la tabla que cumpla con las condiciones personalizadas.
     *
     * @param array $where_conditions Condiciones personalizadas para la consulta.
     * @return object|null La fila obtenida que cumple con las condiciones o null si no se encuentra ninguna fila.
     */
    function get_one_where_custom($where_conditions = [])
    {
        $query = $this->db->limit(1)->get_where($this->table, $where_conditions); // busca una sola fila que coincida
        $row = $query->row();
        if (!$row) {
            $row = new stdClass(); // Create an empty row object
        }
        return $row;
    }
}