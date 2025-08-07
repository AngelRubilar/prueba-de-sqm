<?php

class Technologies_model extends Crud_model {

    private $table;
	

    function __construct() {
        $this->table = 'tecnologias';
        parent::__construct($this->table);
    }

 
	

}
