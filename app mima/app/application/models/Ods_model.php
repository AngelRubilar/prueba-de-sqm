<?php

class Ods_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ods';
        parent::__construct($this->table);
    }

}
