<?php

class Types_of_organization_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'tipos_organizaciones';
        parent::__construct($this->table);
    }
		
}
