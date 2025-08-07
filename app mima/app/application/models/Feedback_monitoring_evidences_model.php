<?php

class Feedback_monitoring_evidences_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'evidencias_evaluaciones_feedback';
        parent::__construct($this->table);
    }
		
}
