<?php
/**
 * Archivo Modelo Sectores
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Sectores
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Sectores
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Sectores
 * @property private $table El nombre de la tabla de base de datos de la entidad Sectores
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_sectors_model extends Crud_model {

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
        $this->table = 'air_sectors';
        parent::__construct($this->table);
    }

    /**
     * get_details
     * 
	 * Consulta datos de un Sector específico o Sectores asociados a un Cliente y/o Proyecto
	 *
	 * @author Gustavo Pinochet Altamirano
     * @access public
     * @param array $options <br/>
     *      $options['id'] => (int) id de Sector. <br/>
     *      $options['name'] => (string) Nombre de Sector. <br/>
     *      $options['id_client'] => (int) id de Cliente. <br/>
     *      $options['id_project'] => (int) id de Proyecto. <br/>
     *      $options['created_by'] => (int) id de Usuario creador del Sector. <br/>
     * @uses string $this->db->dbprefix('air_sectors') tabla air_sectors
	 * @return object
	 */
    function get_details($options = array()) {
		
        $air_sectors_table = $this->db->dbprefix('air_sectors');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $air_sectors_table.id=$id";
        }
		
		$name = get_array_value($options, "name");
        if ($name) {
            $where .= " AND $air_sectors_table.name = $name";
        }
		
		$id_client = get_array_value($options, "id_client");
        if ($id_client) {
            $where .= " AND $air_sectors_table.id_client = $id_client";
        }
		
		$id_project = get_array_value($options, "id_project");
        if ($id_project) {
            $where .= " AND $air_sectors_table.id_project = $id_project";
        }
		
		$created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $air_sectors_table.created_by=$created_by";
        }

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $sql = "SELECT $air_sectors_table.* FROM $air_sectors_table WHERE";
		$sql .= " $air_sectors_table.deleted = 0";
		$sql .= " $where";
		
        return $this->db->query($sql);
		
    }
    
    /**
     * is_air_sector_name_exists
     *
     * Validación para no duplicar el nombre de los Sectores.
     * Utilizado en la creación / edición de un Sector, para validar que el nombre
     * que se está ingresando, no sea el mismo de otro sector ya existente.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
     * @param string $name Nombre del Sector
     * @param int $id_client id del cliente 
     * @param int $id_project id del proyecto
     * @param int $id id del sector
	 * @return boolean Si el nombre de sector ingresado ya existe, retorna true.
	 */
	function is_air_sector_name_exists($name, $id_client, $id_project, $id = 0) {
        $result = $this->get_all_where(array("name" => $name, "id_client" => $id_client, "id_project" => $id_project, "deleted" => 0));
        if ($result->num_rows() && $result->row()->id != $id) {
            return $result->row();
        } else {
            return false;
        }
    }

}
