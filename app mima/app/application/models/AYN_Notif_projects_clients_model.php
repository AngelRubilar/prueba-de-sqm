<?php

class AYN_Notif_projects_clients_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_projects_clients';
        parent::__construct($this->table);
    }

}