<?php

class AYN_Notif_general_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_general';
        parent::__construct($this->table);
    }

}