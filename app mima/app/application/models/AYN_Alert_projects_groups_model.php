<?php

class AYN_Alert_projects_groups_model extends Crud_model {

    private $table = null;
	
    function __construct() {
        $this->table = 'ayn_alert_projects_groups';
        parent::__construct($this->table);
    }
	
}