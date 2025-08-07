<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("communities")." - ".lang("summary"); ?>
</h2>
<div align="center">
	<?php $hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));  ?>
    <?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

<br>

<?php if($puede_ver == 1) { ?>

	<?php if ($id_agreement_matrix_config) { ?>
        <!-- Sección Stakeholders -->
        <h2><?php echo lang('stakeholders'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
            	<?php if($grafico_categorias_totales_sh){ ?>
                    <td align="center"><img src="<?php echo $grafico_categorias_totales_sh; ?>" style="height:300px; width:450px;" /></td>
                <?php } else { ?>
                    <td align="center"><div style="font-size:20px">&nbsp;</div><?php echo lang("no_information_available"); ?></td>
                <?php } ?>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("stakeholder_category"); ?></th>
                    <th style="text-align: center;">N°</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($stakeholders_categories as $sc) { ?>
                    <tr>
                        <td style="text-align: left;"><?php echo lang($sc->nombre); ?></td>
                        <td style="text-align: right;"><?php echo to_number_project_format($sc->cant_tipo_org_stakeholder, $info_proyecto->id); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <!-- Fin Sección Stakeholders -->
        
        <br pagebreak="true">
        
        <!-- Sección Acuerdos -->
        
        <h2><?php echo lang('agreements'); ?></h2>
        <div style="font-size:20px">&nbsp;</div>
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
            	<?php if($grafico_estado_tramitacion){ ?>
                	<td align="center"><img src="<?php echo $grafico_estado_tramitacion; ?>" style="height:300px; width:450px;" /></td>
                <?php } else { ?>
                	<td align="center"><div style="font-size:20px">&nbsp;</div><?php echo lang("no_information_available"); ?></td>
                <?php } ?>
                <?php if($grafico_estado_actividades){ ?>
                	<td align="center"><img src="<?php echo $grafico_estado_actividades; ?>" style="height:300px; width:450px;" /></td>
                <?php } else { ?>
                	<td align="center"><div style="font-size:20px">&nbsp;</div><?php echo lang("no_information_available"); ?></td>
                <?php } ?>
                <?php if($grafico_estado_financiero){ ?>
					<td align="center"><img src="<?php echo $grafico_estado_financiero; ?>" style="height:300px; width:450px;" /></td>
                <?php } else { ?>
                	<td align="center"><div style="font-size:20px">&nbsp;</div><?php echo lang("no_information_available"); ?></td>
                <?php } ?>
            </tr>
        </table>
        <div style="font-size:20px">&nbsp;</div>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("agreement") ?></th>
                    <th style="text-align: center;"><?php echo lang("stakeholder") ?></th>
                    <th style="text-align: center;"><?php echo lang("processing_status") ?></th>
                    <th style="text-align: center;"><?php echo lang("activities_status") ?></th>
                    <th style="text-align: center;"><?php echo lang("financial_status") ?></th>
                    <th style="text-align: center;"><?php echo lang("observations") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_acuerdos_consolidado as $data){ ?>
                    
                    <tr>
                        <td style="text-align: center;"><?php echo $data["nombre_acuerdo"]; ?></td>
                        <td style="text-align: center;"><?php echo $data["nombre_stakeholder"]; ?></td>
                        <td style="text-align: center;">
                            <?php 
                                $estado = $data["estado_tramitacion"];
                                if($estado != "-"){
                                    $array_estado = explode("nombre_estado:", $estado);
                                    $pdf->SetFont($fontawesome, '', 9, '', false);
                                    echo '<span style="font-family:'.$fontawesome.'">'.$array_estado[0].'</span><br>';
                                    $pdf->SetFont('helvetica', '', 9);
                                    echo $array_estado[1];
                                } else {
                                    echo $estado;
                                }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php 
                                $estado = $data["estado_actividades"];
                                if($estado != "-"){
                                    $array_estado = explode("nombre_estado:", $estado);
                                    $pdf->SetFont($fontawesome, '', 9, '', false);
                                    echo '<span style="font-family:'.$fontawesome.'">'.$array_estado[0].'</span><br>';
                                    $pdf->SetFont('helvetica', '', 9);
                                    echo $array_estado[1];
                                } else {
                                    echo $estado;
                                }
                            ?>
                        </td>
                        <td style="text-align: center;">
                            <?php 
                                $estado = $data["estado_financiero"];
                                if($estado != "-"){
                                    $array_estado = explode("nombre_estado:", $estado);
                                    $pdf->SetFont($fontawesome, '', 9, '', false);
                                    echo '<span style="font-family:'.$fontawesome.'">'.$array_estado[0].'</span><br>';
                                    $pdf->SetFont('helvetica', '', 9);
                                    echo $array_estado[1];
                                } else {
                                    echo $estado;
                                }
                            ?>
                        </td>
                        <td style="text-align: center;"><?php echo $data["observaciones"]; ?></td>
                    </tr>
                    
                <?php } ?>
            </tbody>
        </table>
        
        <!-- Fin Sección Acuerdos -->
        
        <br pagebreak="true">
        
        <!-- Sección Feedback -->
        <h2><?php echo lang('feedback'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
            	<?php if($grafico_categorias_totales_sh){ ?>
                	<td align="center"><img src="<?php echo $grafico_feedback_visit_purpose; ?>" style="height:300px; width:450px;" /></td>
                <?php } else { ?>
                    <td align="center"><div style="font-size:20px">&nbsp;</div><?php echo lang("no_information_available"); ?></td>
                <?php } ?>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("stakeholder_category"); ?></th>
                    <th style="text-align: center;">N°</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($number_of_visits_by_type_of_stakeholder as $nv) { ?>
                    <tr>
                        <td style="text-align: left;"><?php echo lang($nv->nombre); ?></td>
                        <td style="text-align: right;"><?php echo to_number_project_format($nv->numero_visitas, $info_proyecto->id); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    
    <?php } else { ?>
        
        <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('communities_matrix_not_enabled'); ?>
    
    <?php } ?>
<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>
</body>