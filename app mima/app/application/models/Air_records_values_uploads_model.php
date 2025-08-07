<?php
/**
 * Archivo Modelo Carga de Pronósticos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Pronósticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Carga de Pronósticos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Pronósticos
 * @property private $table El nombre de la tabla de base de datos de la entidad Carga de Pronósticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_records_values_uploads_model extends Crud_model {

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
        $this->table = 'air_records_values_uploads';
        parent::__construct($this->table);
    }

    function get_first_id_to_delete($options = array()){

        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');

        $where = "";
        $created = get_array_value($options, "created");
        if ($created) {
            $where .= " AND $air_records_values_uploads_table.created < '$created'";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $air_records_values_uploads_table.*";
        $sql .= " FROM $air_records_values_uploads_table";
        $sql .= " WHERE 1";
		$sql .= $where;
        $sql .= " ORDER BY $air_records_values_uploads_table.id DESC";
        $sql .= " LIMIT 1";
		
        return $this->db->query($sql);

    }

    /**
     * Elimina los registros que sean menores o iguales al id recibido
     */
    function delete_old_values_from_an_id($id = 0){

        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "DELETE FROM $air_records_values_uploads_table";
        $sql .= " WHERE $air_records_values_uploads_table.id <= $id";
		
        return $this->db->query($sql);

    }

    function reset_ids(){
        
        $air_records_values_uploads_table = $this->db->dbprefix('air_records_values_uploads');
        $air_records_values_p_table = $this->db->dbprefix('air_records_values_p');
        $air_records_values_p_min_table = $this->db->dbprefix('air_records_values_p_min');
        $air_records_values_p_max_table = $this->db->dbprefix('air_records_values_p_max');
        $air_records_values_p_porc_conf_table = $this->db->dbprefix('air_records_values_p_porc_conf');

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");

        // CREAR NUEVOS id PARA LAS TABLAS
        // $this->db->query("INSERT INTO new_id_air_records_values_uploads (id, new_id) SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_uploads_table");
        // $this->db->query("INSERT INTO new_id_air_records_values_p (id, new_id) SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_table");
        // $this->db->query("INSERT INTO new_id_air_records_values_p_min (id, new_id) SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_min_table");
        // $this->db->query("INSERT INTO new_id_air_records_values_p_max (id, new_id) SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS new_id FROM $air_records_values_p_max_table");

        $this->db->query("SET @row_number = 0;");
        $this->db->query("INSERT INTO new_id_air_records_values_uploads (id, new_id) SELECT id, (@row_number:=@row_number+1) AS new_id FROM $air_records_values_uploads_table");
        $this->db->query("SET @row_number = 0;");
        $this->db->query("INSERT INTO new_id_air_records_values_p (id, new_id) SELECT id, (@row_number:=@row_number+1) AS new_id FROM $air_records_values_p_table");
        $this->db->query("SET @row_number = 0;");
        $this->db->query("INSERT INTO new_id_air_records_values_p_min (id, new_id) SELECT id, (@row_number:=@row_number+1) AS new_id FROM $air_records_values_p_min_table");
        $this->db->query("SET @row_number = 0;");
        $this->db->query("INSERT INTO new_id_air_records_values_p_max (id, new_id) SELECT id, (@row_number:=@row_number+1) AS new_id FROM $air_records_values_p_max_table");
        $this->db->query("SET @row_number = 0;");
        $this->db->query("INSERT INTO new_id_air_records_values_p_porc_conf (id, new_id) SELECT id, (@row_number:=@row_number+1) AS new_id FROM $air_records_values_p_porc_conf_table");




        // REINICIAR CAMPO id DE TABLA air_records_values_uploads
		$this->db->query("UPDATE $air_records_values_uploads_table INNER JOIN new_id_air_records_values_uploads ON $air_records_values_uploads_table.id = new_id_air_records_values_uploads.id SET $air_records_values_uploads_table.id = new_id_air_records_values_uploads.new_id");
        // RESETEAR EL AUTO_INCREMENT DE TABLA air_records_values_uploads
        $this->db->query("SET @max_id = (SELECT MAX(id) + 1 FROM $air_records_values_uploads_table)");
        $this->db->query("SET @sql = CONCAT('ALTER TABLE $air_records_values_uploads_table AUTO_INCREMENT = ', @max_id)");
        $this->db->query("PREPARE st FROM @sql");
        $this->db->query("EXECUTE st");




        // REINICIAR CAMPO id DE TABLA air_records_values_p
        $this->db->query("UPDATE $air_records_values_p_table INNER JOIN new_id_air_records_values_p ON $air_records_values_p_table.id = new_id_air_records_values_p.id SET $air_records_values_p_table.id = new_id_air_records_values_p.new_id");
        // RESETEAR EL AUTO_INCREMENT DE TABLA air_records_values_p
        $this->db->query("SET @max_id = (SELECT MAX(id) + 1 FROM $air_records_values_p_table)");
        $this->db->query("SET @sql = CONCAT('ALTER TABLE $air_records_values_p_table AUTO_INCREMENT = ', @max_id)");
        $this->db->query("PREPARE st FROM @sql");
        $this->db->query("EXECUTE st");


        

        // REINICIAR CAMPO id_upload DE TABLA air_records_values_p
        $this->db->query("UPDATE $air_records_values_p_table INNER JOIN new_id_air_records_values_uploads ON $air_records_values_p_table.id_upload = new_id_air_records_values_uploads.id SET $air_records_values_p_table.id_upload = new_id_air_records_values_uploads.new_id");




        // REINICIAR CAMPO id DE TABLA air_records_values_p_min
        $this->db->query("UPDATE $air_records_values_p_min_table INNER JOIN new_id_air_records_values_p_min ON $air_records_values_p_min_table.id = new_id_air_records_values_p_min.id SET $air_records_values_p_min_table.id = new_id_air_records_values_p_min.new_id");
        // RESETEAR EL AUTO_INCREMENT DE TABLA air_records_values_p_min
        $this->db->query("SET @max_id = (SELECT MAX(id) + 1 FROM $air_records_values_p_min_table)");
        $this->db->query("SET @sql = CONCAT('ALTER TABLE $air_records_values_p_min_table AUTO_INCREMENT = ', @max_id)");
        $this->db->query("PREPARE st FROM @sql");
        $this->db->query("EXECUTE st");

        // REINICIAR CAMPO id_values_p DE TABLA air_records_values_p_min
        $this->db->query("UPDATE $air_records_values_p_min_table INNER JOIN new_id_air_records_values_p ON $air_records_values_p_min_table.id_values_p = new_id_air_records_values_p.id SET $air_records_values_p_min_table.id_values_p = new_id_air_records_values_p.new_id");

        // REINICIAR CAMPO id_upload DE TABLA air_records_values_p_min
        $this->db->query("UPDATE $air_records_values_p_min_table INNER JOIN new_id_air_records_values_uploads ON $air_records_values_p_min_table.id_upload = new_id_air_records_values_uploads.id SET $air_records_values_p_min_table.id_upload = new_id_air_records_values_uploads.new_id");




        // REINICIAR CAMPO id DE TABLA air_records_values_p_max
        $this->db->query("UPDATE $air_records_values_p_max_table INNER JOIN new_id_air_records_values_p_max ON $air_records_values_p_max_table.id = new_id_air_records_values_p_max.id SET $air_records_values_p_max_table.id = new_id_air_records_values_p_max.new_id");
        // RESETEAR EL AUTO_INCREMENT DE TABLA air_records_values_p_max
        $this->db->query("SET @max_id = (SELECT MAX(id) + 1 FROM $air_records_values_p_max_table)");
        $this->db->query("SET @sql = CONCAT('ALTER TABLE $air_records_values_p_max_table AUTO_INCREMENT = ', @max_id)");
        $this->db->query("PREPARE st FROM @sql");
        $this->db->query("EXECUTE st");

        // REINICIAR CAMPO id_values_p DE TABLA air_records_values_p_max
        $this->db->query("UPDATE $air_records_values_p_max_table INNER JOIN new_id_air_records_values_p ON $air_records_values_p_max_table.id_values_p = new_id_air_records_values_p.id SET $air_records_values_p_max_table.id_values_p = new_id_air_records_values_p.new_id");

        // REINICIAR CAMPO id_upload DE TABLA air_records_values_p_max
        $this->db->query("UPDATE $air_records_values_p_max_table INNER JOIN new_id_air_records_values_uploads ON $air_records_values_p_max_table.id_upload = new_id_air_records_values_uploads.id SET $air_records_values_p_max_table.id_upload = new_id_air_records_values_uploads.new_id");




        // REINICIAR CAMPO id DE TABLA air_records_values_p_porc_conf
        $this->db->query("UPDATE $air_records_values_p_porc_conf_table INNER JOIN new_id_air_records_values_p_porc_conf ON $air_records_values_p_porc_conf_table.id = new_id_air_records_values_p_porc_conf.id SET $air_records_values_p_porc_conf_table.id = new_id_air_records_values_p_porc_conf.new_id");
        // RESETEAR EL AUTO_INCREMENT DE TABLA air_records_values_p_porc_conf
        $this->db->query("SET @max_id = (SELECT MAX(id) + 1 FROM $air_records_values_p_porc_conf_table)");
        $this->db->query("SET @sql = CONCAT('ALTER TABLE $air_records_values_p_porc_conf_table AUTO_INCREMENT = ', @max_id)");
        $this->db->query("PREPARE st FROM @sql");
        $this->db->query("EXECUTE st");

        // REINICIAR CAMPO id_values_p DE TABLA air_records_values_p_porc_conf
        $this->db->query("UPDATE $air_records_values_p_porc_conf_table INNER JOIN new_id_air_records_values_p ON $air_records_values_p_porc_conf_table.id_values_p = new_id_air_records_values_p.id SET $air_records_values_p_porc_conf_table.id_values_p = new_id_air_records_values_p.new_id");

        // REINICIAR CAMPO id_upload DE TABLA air_records_values_p_porc_conf
        $this->db->query("UPDATE $air_records_values_p_porc_conf_table INNER JOIN new_id_air_records_values_uploads ON $air_records_values_p_porc_conf_table.id_upload = new_id_air_records_values_uploads.id SET $air_records_values_p_porc_conf_table.id_upload = new_id_air_records_values_uploads.new_id");


        

        $this->db->query("TRUNCATE TABLE new_id_air_records_values_uploads");
        $this->db->query("TRUNCATE TABLE new_id_air_records_values_p");
        $this->db->query("TRUNCATE TABLE new_id_air_records_values_p_min");
        $this->db->query("TRUNCATE TABLE new_id_air_records_values_p_max");
        $this->db->query("TRUNCATE TABLE new_id_air_records_values_p_porc_conf");

        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
        $this->db->trans_complete();

        return $this->db->trans_status();

    }

}
