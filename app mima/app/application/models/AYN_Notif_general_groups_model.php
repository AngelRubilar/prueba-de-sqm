<?php

class AYN_Notif_general_groups_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_general_groups';
        parent::__construct($this->table);
    }

}