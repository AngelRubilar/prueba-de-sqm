<?php

class Errors extends MY_Controller{
	
 public function __construct() 
 {
    parent::__construct(); 
 } 

 public function not_found() 
 { 
 
    $this->output->set_status_header('404'); 
    $this->load->view('errors/err404');//loading in custom error view
 } 


}