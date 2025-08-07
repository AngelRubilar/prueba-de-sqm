<?php
/**
 * Archivo Modelo de Tipos de Variable
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo de Tipos de Variable
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Variables
 * @property private $table El nombre de la tabla de base de datos de la entidad Tipos de Variable
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_variables_types_model extends Crud_model {

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
        $this->table = 'air_variables_types';
        parent::__construct($this->table);
    }

}
