<?php

class EC_Types_no_apply_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ec_tipo_no_aplica';
        parent::__construct($this->table);
    }
		
}
