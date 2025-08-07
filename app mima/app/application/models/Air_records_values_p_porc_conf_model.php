<?php

class Air_records_values_p_porc_conf_model extends Crud_model {

    function __construct() {
        $this->table = 'air_records_values_p_porc_conf';
        parent::__construct($this->table);
    }

    /**
     * Elimina los registros que sean menores o iguales al id_upload recibido
     */
    function delete_old_values_from_an_id_upload($id_upload = 0){

        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "DELETE FROM $air_records_values_p_porc_conf_table";
        $sql .= " WHERE $air_records_values_p_porc_conf_table.id_upload <= $id_upload";
		
        return $this->db->query($sql);

    }

    function reset_ids(){
        
        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        $this->db->query("CREATE TEMPORARY TABLE new_id_table AS SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_porc_conf_table");
		$this->db->query("UPDATE $air_records_values_p_porc_conf_table INNER JOIN new_id_table ON $air_records_values_p_porc_conf_table.id = new_id_table.id SET $air_records_values_p_porc_conf_table.id = new_id_table.new_id");
        $this->db->query("DROP TABLE new_id_table");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
        $this->db->trans_complete();

        return $this->db->trans_status();

    }




    function get_last_upload_data_1D_by_date($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');
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

        $ids_records = get_array_value($options, "ids_records");
        if (count($ids_records)) {
            $where .= " AND $air_records_values_p_table.id_record IN (".implode(",",$ids_records).")";
        }

        $date = get_array_value($options, "date");
        if($date){
            $where .= " AND $air_records_values_p_table.date = '$date'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');

        // SI LA VARIABLE ES PM10, BUSCA VALORES DE PORCENTAJE DE CONFIABILIDAD
        if($id_variable == 9){

            $sql = "SELECT $air_records_values_p_table.*,";


            for($time = 0; $time <= 23; $time++){
                $t = ($time < 10) ? "0$time" : "$time";
                $sql .= ($t != 23) ? "MAX($air_records_values_p_porc_conf_table.time_$t) as time_max_porc_conf_$t, " : "MAX($air_records_values_p_porc_conf_table.time_$t) as time_max_porc_conf_$t";
            }

            $sql .= " FROM $air_records_values_p_table";

            $sql .= " LEFT JOIN $air_records_values_p_porc_conf_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_porc_conf_table.id_values_p";

            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";

        } else {

            $sql = "SELECT $air_records_values_p_table.*";
            $sql .= " FROM $air_records_values_p_table";
            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";

        }

        /*
            -- CONSULTA QUE TRAE EL VALOR DE UNA FECHA / HORA CON EL MAYOR PORCENTAJE DE CERTEZA 
            SELECT air_records_values_p.time_00, air_records_values_p_porc_conf.time_00 as time_porc_conf_00
            FROM air_records_values_p
            LEFT JOIN air_records_values_p_porc_conf
            ON air_records_values_p.id = air_records_values_p_porc_conf.id_values_p
            WHERE air_records_values_p_porc_conf.time_00 = (
                SELECT MAX(time_00) FROM air_records_values_p_porc_conf
            );
        */
       
        return $this->db->query($sql);

    }

    /**
     * Devuelve el dato de una fecha / hora con el mayor porcentaje de certeza de los 3 modelos
     */
    function get_max_reliability_data_per_hour($options = array()){

        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');
        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');
        $air_records_table = $this->db->dbprefix('air_records');
        $air_models_table = $this->db->dbprefix('air_models');
        
        $where = "";
        $id_variable = get_array_value($options, "id_variable");
        if($id_variable){
            $where .= " AND $air_records_values_p_table.id_variable = $id_variable";
        }

        $ids_records = get_array_value($options, "ids_records");
        if (count($ids_records)) {
            $where .= " AND $air_records_values_p_table.id_record IN (".implode(",",$ids_records).")";
        }

        $date = get_array_value($options, "date");
        if($date){
            $where .= " AND $air_records_values_p_table.date = '$date'";
        }

        $hour = get_array_value($options, "hour"); // 00 a 23

        $this->db->query('SET SQL_BIG_SELECTS=1');

        // SI LA VARIABLE ES PM10, BUSCA VALORES DE PORCENTAJE DE CONFIABILIDAD
        if($id_variable == 9){

            $sql = "SELECT $air_records_values_p_table.id, $air_records_values_p_table.id_record, $air_records_values_p_table.id_variable, $air_records_values_p_table.id_upload, $air_models_table.name as model_name, $air_records_values_p_table.time_$hour as value, $air_records_values_p_porc_conf_table.time_$hour as porc_conf";
           
            $sql .= " FROM $air_records_values_p_table";

            $sql .= " INNER JOIN $air_records_table";
            $sql .= " ON $air_records_values_p_table.id_record = $air_records_table.id";

            $sql .= " INNER JOIN $air_models_table";
            $sql .= " ON $air_models_table.id = $air_records_table.id_air_model";

            $sql .= " LEFT JOIN $air_records_values_p_porc_conf_table";
            $sql .= " ON $air_records_values_p_table.id = $air_records_values_p_porc_conf_table.id_values_p";

            $sql .= " WHERE $air_records_values_p_porc_conf_table.time_$hour = (";
            $sql .= "   SELECT MAX($air_records_values_p_porc_conf_table.time_$hour) FROM $air_records_values_p_porc_conf_table";
            $sql .= "   INNER JOIN $air_records_values_p_table";
            $sql .= "   ON $air_records_values_p_table.id = $air_records_values_p_porc_conf_table.id_values_p";
            $sql .= "   WHERE $air_records_values_p_table.deleted = 0 ";
            $sql .= "   $where";
            $sql .= ")";

            $sql .= " AND $air_records_values_p_table.deleted = 0 ";
            $sql .= " $where";
            $sql .= " ORDER BY porc_conf, $air_records_values_p_table.id DESC";

        } else {

            $sql = "SELECT $air_records_values_p_table.*";
            $sql .= " FROM $air_records_values_p_table";
            $sql .= " WHERE $air_records_values_p_table.deleted = 0";
            $sql .= " $where";

        }

        return $this->db->query($sql);

    }

}
