<?php

class AYN_Notif_projects_admin_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_projects_admin';
        parent::__construct($this->table);
    }

}