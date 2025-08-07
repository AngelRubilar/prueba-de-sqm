<?php

class Fairs_model extends Crud_model {

    private $table;

    function __construct() {
        $this->table = 'ferias';
        parent::__construct($this->table);
    }

}
