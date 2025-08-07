<?php
/**
 * Archivo Modelo Modelos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Modelos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

/**
 * Modelo Modelos
 * 
 * @package MIMAire
 * @subpackage Models
 * @category Modelos
 * @property private $table El nombre de la tabla de base de datos de la entidad Modelos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_models_model extends Crud_model {

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
        $this->table = 'air_models';
        parent::__construct($this->table);
    }

}
