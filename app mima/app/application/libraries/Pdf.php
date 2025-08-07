<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."third_party/tcpdf/tcpdf.php";
class Pdf extends TCPDF { 
    public function __construct() { 
        parent::__construct(); 
    }
	
	public function Header() {
        $this->Rect(0, 0, 35, 4, 'F', array(), array(147, 78, 142));// VIOLETA
		$this->Rect(35, 0, 175, 4, 'F', array(), array(79, 187, 144));// VERDOSA
		
		//$image_file = get_file_uri('assets/images/mimasoft-logo-fondo.png');
        //$this->Image($image_file, 160, 10, 35, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }

    // Page footer
    public function Footer() {
        $this->Rect(0, 293, 210, 5, 'F', array(), array(147, 78, 142));// VIOLETA
		
		$icono_ubicacion = get_file_uri('assets/images/mimasoft-pdf-ubicacion.png');
		$icono_fono = get_file_uri('assets/images/mimasoft-pdf-fono.png');
		$icono_mail = get_file_uri('assets/images/mimasoft-pdf-mail.png');
		$icono_pagina = get_file_uri('assets/images/mimasoft-pdf-pagina.png');
		
		//Image($file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false...
		$this->Image($icono_ubicacion, 10, 282, 7, 7, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->writeHTMLCell(75, 5, 18, 284, '<p>Santa Victoria 0309 | Providencia I Santiago | Chile</p>', '', 1, 0, true, 'L', true);
		
		$this->Image($icono_fono, 95, 282, 7, 7, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->writeHTMLCell(30, 5, 103, 284, '<p>+562 3245 0513</p>', '', 1, 0, true, 'L', true);
		
		$this->Image($icono_mail, 131, 282, 7, 7, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->writeHTMLCell(30, 5, 140, 284, '<p>info@mimasoft.cl</p>', '', 1, 0, true, 'L', true);
		
		$this->Image($icono_pagina, 169, 282, 7, 7, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->writeHTMLCell(30, 5, 177, 284, '<p>www.mimasoft.cl</p>', '', 1, 0, true, 'L', true);
		
    }
}