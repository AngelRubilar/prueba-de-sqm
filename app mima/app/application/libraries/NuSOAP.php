<?php

if (!defined('BASEPATH'))exit('No direct script access allowed');

require_once APPPATH."/third_party/nusoap-0.9.5/lib/nusoap.php";

class NuSOAP extends nusoap_base {
	
    public function __construct() {
        parent::__construct();
    }

}