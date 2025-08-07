<?php

class Mimasoft_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'mimasoft';
        parent::__construct($this->table);
    }
	
}