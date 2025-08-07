<?php

class Faq_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'faq';
        parent::__construct($this->table);
    }
	
}