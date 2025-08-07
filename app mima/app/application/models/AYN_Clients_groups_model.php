<?php

class AYN_Clients_groups_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_clients_groups';
        parent::__construct($this->table);
    }

}