<?php

class Contact_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'contacto';
        parent::__construct($this->table);
    }
    
    
}