<?php
class Subindustries_model extends Crud_model {

    private $table;
    function __construct() {
        $this->table = 'subrubros';
        parent::__construct($this->table);
    }
}
