<?php
/**
 * Archivo Modelo Tipos de Registro
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Registros
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Tipos de Registro
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Registros
 * @property private $table El nombre de la tabla de base de datos de la entidad Tipos de Registro
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_records_types_model extends Crud_model {

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
        $this->table = 'air_records_types';
        parent::__construct($this->table);
    }

}
