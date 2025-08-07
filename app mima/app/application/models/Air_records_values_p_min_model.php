<?php

class Air_records_values_p_min_model extends Crud_model {

    function __construct() {
        $this->table = 'air_records_values_p_min';
        parent::__construct($this->table);
    }

    /**
     * Elimina los registros que sean menores o iguales al id_upload recibido
     */
    function delete_old_values_from_an_id_upload($id_upload = 0){

        $air_records_values_p_min_table = $this->db->dbprefix('air_records_values_p_min');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "DELETE FROM $air_records_values_p_min_table";
        $sql .= " WHERE $air_records_values_p_min_table.id_upload <= $id_upload";
		
        return $this->db->query($sql);

    }

    function reset_ids(){
        
        $air_records_values_p_min_table = $this->db->dbprefix('air_records_values_p_min');

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        $this->db->query("CREATE TEMPORARY TABLE new_id_table AS SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_min_table");
		$this->db->query("UPDATE $air_records_values_p_min_table INNER JOIN new_id_table ON $air_records_values_p_min_table.id = new_id_table.id SET $air_records_values_p_min_table.id = new_id_table.new_id");
        $this->db->query("DROP TABLE new_id_table");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
        $this->db->trans_complete();

        return $this->db->trans_status();

    }

}
