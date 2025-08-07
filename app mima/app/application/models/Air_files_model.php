<?php
/**
 * Archivo Modelo Archivos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Archivos
 * @author Alvaro Donoso Albornoz
 * @version 1.0
 */

/**
 * Modelo Archivos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Archivos
 * @property private $table El nombre de la tabla de base de datos de la entidad Archivos
 * @author Alvaro Donoso Albornoz
 * @version 1.0
 */
class Air_files_model extends Crud_model {

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
        $this->table = 'air_files';
        parent::__construct($this->table);
    }

    /**
     * get_details
     * 
     * Consulta los Archivos cargados por los operadores/usuarios asociados a  
	 * Sectores de un Cliente / Proyecto, para enlistarlos en el panel principal.
	 *
	 * @author Alvaro Donoso Albornoz
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
        
        $air_files_table = $this->db->dbprefix('air_files');
        $clients_table = $this->db->dbprefix('clients');
        $projects_table = $this->db->dbprefix('projects');
		$air_sectors_table = $this->db->dbprefix('air_sectors');
        $users_table = $this->db->dbprefix('users');

        $where = "";
        $id = get_array_value($options, "id");
        if($id){
            $where .= " AND $air_files_table.id = $id";
        }

        $id_client = get_array_value($options, "id_client");
        if($id_client){
            $where .= " AND $air_files_table.id_client = $id_client";
        }

        $id_project = get_array_value($options, "id_project");
        if($id_project){
            $where .= " AND $air_files_table.id_project = $id_project";
        }

        $id_air_sector = get_array_value($options, "id_air_sector");
        if($id_air_sector){
            $where .= " AND $air_files_table.id_air_sector = $id_air_sector";
        }

        $id_user = get_array_value($options, "id_user");
        if($id_user){
            $where .= " AND $air_files_table.uploaded_by = $id_user";
        }

        $start_date = get_array_value($options, "start_date");
        if ($start_date) {
            $where .= " AND ($air_files_table.date)>='$start_date'";
        }
        $end_date = get_array_value($options, "end_date");
        if ($end_date) {
            $where .= " AND ($air_files_table.date)<='$end_date'";
        }
        
        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = " SELECT $air_files_table.*, $clients_table.company_name AS name_client, $projects_table.title AS name_project,";
        $sql .= " $air_sectors_table.name AS name_sector, CONCAT($users_table.first_name,' ', $users_table.last_name) AS user_name ";
        $sql .= " FROM $air_files_table";
        $sql .= " INNER JOIN $clients_table";
        $sql .= " ON $air_files_table.id_client = $clients_table.id";
        $sql .= " INNER JOIN $projects_table";
        $sql .= " ON $air_files_table.id_project = $projects_table.id";
        $sql .= " INNER JOIN $air_sectors_table";
        $sql .= " ON $air_files_table.id_air_sector = $air_sectors_table.id";
        $sql .= " LEFT JOIN $users_table";
        $sql .= " ON $air_files_table.uploaded_by = $users_table.id";
        $sql .= " WHERE $clients_table.deleted = 0";
        $sql .= " AND $projects_table.deleted = 0";
        $sql .= " AND $air_sectors_table.deleted = 0";
        $sql .= " AND $users_table.deleted = 0";
        $sql .= " AND $air_files_table.deleted = 0";
        $sql .= " $where";
        $sql .= " ORDER BY $air_files_table.id ASC";

        return $this->db->query($sql);

    }

}
