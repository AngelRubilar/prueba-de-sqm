<?php

class EC_Types_of_origin_matter_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ec_tipo_origen_materia';
        parent::__construct($this->table);
    }
		
}
