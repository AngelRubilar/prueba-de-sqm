<?php

class AYN_Alert_historical_air_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_alert_historical_air';
        parent::__construct($this->table);
    }	

}
