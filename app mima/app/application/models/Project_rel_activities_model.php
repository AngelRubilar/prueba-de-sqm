<?php

class Project_rel_activities_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'proyecto_rel_actividades';
        parent::__construct($this->table);
    }

    

}
