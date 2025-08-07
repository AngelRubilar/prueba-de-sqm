<?php

class Agreements_evidences_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evidencias_acuerdos';
        parent::__construct($this->table);
    }
		
}
