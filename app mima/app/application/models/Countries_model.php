<?php

class Countries_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'paises';
        parent::__construct($this->table);
    }

}
