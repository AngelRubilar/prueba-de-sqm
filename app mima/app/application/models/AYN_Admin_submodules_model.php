<?php

class AYN_Admin_submodules_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'ayn_admin_submodules';
        parent::__construct($this->table);
    }

}