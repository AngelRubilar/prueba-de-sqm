<?php

class Fontawesome_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'fontawesome';
        parent::__construct($this->table);
    }

}
