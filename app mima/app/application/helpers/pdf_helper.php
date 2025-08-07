<?php


if(!function_exists('generar_pdf_informe_acv'))
{
    function generar_pdf_informe_acv($opciones_informe_acv = array()) {
		
		$id_cliente = $opciones_informe_acv["id_cliente"];
		$id_proyecto = $opciones_informe_acv["id_proyecto"];
		$id_unidad_funcional = $opciones_informe_acv["id_unidad_funcional"];
		$tipo_informe = $opciones_informe_acv["tipo_informe"];
		$version_archivo = $opciones_informe_acv["version_archivo"];
		
		$ci = get_instance();
        $ci->load->library('Pdf');
		
        $ci->pdf->SetCreator(PDF_CREATOR);
        $ci->pdf->SetAuthor('Autor');
        $ci->pdf->SetTitle('Informe ACV');
        $ci->pdf->SetSubject('Informe ACV');
        $ci->pdf->SetKeywords('TCPDF, PDF');

		// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        $ci->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $ci->pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

		// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $ci->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $ci->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $ci->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $ci->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $ci->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $ci->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $ci->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//relación utilizada para ajustar la conversión de los píxeles
        $ci->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// ---------------------------------------------------------
		// establecer el modo de fuente por defecto
        $ci->pdf->setFontSubsetting(true);

		// Establecer el tipo de letra
		 
		//Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
		// Helvetica para reducir el tamaño del archivo.
		//$pdf->SetFont('freemono', '', 14, '', true);
		
		// Añadir una página
		// Este método tiene varias opciones, consulta la documentación para más información.
        $ci->pdf->AddPage();

		//fijar efecto de sombra en el texto
        $ci->pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Establecemos el contenido para imprimir
        /* $provincia = $this->input->post('provincia');
        $provincias = $this->pdfs_model->getProvinciasSeleccionadas($provincia);
        foreach($provincias as $fila)
        {
            $prov = $fila['p.provincia'];
        } */
        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #fff; font-weight: bold; background-color: #222}";
        $html .= "td{background-color: #AAC7E3; color: #fff}";
        $html .= "</style>";
        //$html .= "<h2>Localidades de ".$prov."</h2><h4>Actualmente: ".count($provincias)." localidades</h4>";
        $html .= "<table width='100%'>";
        $html .= "<tr><th>Id localidad</th><th>Localidades</th></tr>";
        
        //provincias es la respuesta de la función getProvinciasSeleccionadas($provincia) del modelo
        /* foreach ($provincias as $fila) 
        {
            $id = $fila['l.id'];
            $localidad = $fila['l.localidad'];

            $html .= "<tr><td class='id'>" . $id . "</td><td class='localidad'>" . $localidad . "</td></tr>";
        } */
        $html .= "</table>";

		// Imprimimos el texto con writeHTMLCell()
        $ci->pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        
		$nombre_archivo = utf8_decode('informe_acv_v'.$version_archivo.'.pdf');
		
		if($tipo_informe == "Resumen"){ 
			$type_of_report = "summary";
		}
		if($tipo_informe == "Completo"){ 
			$type_of_report = "full";
		}
		
		if(!file_exists(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/acv_reports/functional_unit_'.$id_unidad_funcional.'/'.$type_of_report)) {
			mkdir(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/acv_reports/functional_unit_'.$id_unidad_funcional.'/'.$type_of_report, 0777, TRUE);
		}

		$ci->pdf->Output(__DIR__.'/../../files/mimasoft_files/client_'.$id_cliente.'/project_'.$id_proyecto.'/acv_reports/functional_unit_'.$id_unidad_funcional.'/'.$type_of_report.'/'.$nombre_archivo, 'F');
		
		return $nombre_archivo;
    }

}