<?php

class AYN_Notif_projects_admin_users_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_notif_projects_admin_users';
        parent::__construct($this->table);
    }

}