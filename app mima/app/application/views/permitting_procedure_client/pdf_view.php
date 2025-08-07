<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("permittings")." - ".lang("permittings_procedure"); ?>
</h2>
<div align="center">
    <?php $hora = convert_to_general_settings_time_format($info_proyecto->id, convert_date_utc_to_local(get_current_utc_time("H:i:s"), "H:i:s", $info_proyecto->id));  ?>
	<?php echo lang("datetime_download") . ": " . get_date_format(date('Y-m-d'), $info_proyecto->id).' '.lang("at").' '.$hora; ?>
</div>

<br>
<?php if($puede_ver == 1) { ?>

	<?php if ($id_permiso) { ?>
        <!-- Sección Resumen de Tramitación -->
        <h2><?php echo lang('procedure_summary'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td align="center"><img src="<?php echo $grafico_cumplimientos_totales; ?>" style="height:300px; width:450px;" /></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th rowspan="2" style="text-align: center;"><?php echo lang("general_procedure_status"); ?></th>
                    <th colspan="2" style="text-align: center;"><?php echo lang("total"); ?></th>
                </tr>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;"><strong><?php echo lang("total_applicable_procedures"); ?></strong></td>
                    <td style="text-align: right;"><?php echo to_number_project_format($total_permisos_aplicables, $info_proyecto->id); ?></td>
                    <td style="text-align: right;"><?php echo to_number_project_format(100, $info_proyecto->id); ?> %</td>
                </tr>
                <?php foreach($total_cantidades_estados_evaluados_permisos as $estado) { ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $estado["nombre_estado"] //. " (".$estado["categoria"].")"; ?></td>
                        <td style="text-align: right;"><?php echo to_number_project_format($estado["cantidad_categoria"], $info_proyecto->id); ?></td>
                        <td style="text-align: right;"><?php echo to_number_project_format(($estado["cantidad_categoria"] * 100) / $total_permisos_aplicables, $info_proyecto->id); ?> %</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Fin Sección Resumen de Tramitación -->
        
        <br pagebreak="true">
        
        <!-- Sección Resumen por Evaluado -->
        <h2><?php echo lang('summary_by_evaluated'); ?></h2>
        
        <table cellspacing="0" cellpadding="4" border="0">
        
        <?php $loop = 1; ?>
        <?php foreach($graficos_resumen_evaluados as $grafico) { ?>
            
            <?php if($loop % 2 == 1){ ?>
                <tr>
            <?php } ?>
            
            <?php if(strpos($grafico, 'grafico_resumen_evaluado_') !== false){ ?>
            
                <?php 
                    $grafico_evaluado_compromiso = str_split($grafico, 25);		
                    $id_evaluado = $grafico_evaluado_compromiso[1];
                    $evaluado = $this->Evaluated_permitting_model->get_one($id_evaluado);
                ?>
                <td align="center">
                    <div style="font-size:20px">&nbsp;</div>
                    <?php echo $evaluado->nombre_evaluado . " - " . lang("no_information_available"); ?>
                </td>			
                
            <?php } else { ?>
                <td align="center"><img src="<?php echo $grafico; ?>"/></td>
            <?php } ?>    
        
            <?php if($loop % 2 == 0 || $loop == count($graficos_resumen_evaluados)) {?>
                </tr>
            <?php } ?>
            
            <?php $loop++; ?>
        
        <? } ?>
        </table>
        
        <div style="font-size:20px">&nbsp;</div>
        
        <?php foreach($evaluados_matriz_permiso as $evaluado) { ?>
        
            <table cellspacing="0" cellpadding="4" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo lang("compliance_status"); ?></th>
                        <th colspan="2" style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;"><?php echo $evaluado["nombre_evaluado"]; ?></th>                                            
                    </tr>
                    <tr>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">N°</th>
                        <th style="text-align: center; background-color: <?php echo $info_cliente->color_sitio; ?>;">%</th>                                             
                    </tr>
                </thead>
                <tbody>
                    <tr>
                       <th style="text-align: center;"><?php echo lang("total_applicable_procedures"); ?></th>
                       <td style="text-align: right;"><?php echo to_number_project_format(array_sum($array_total_por_evaluado_permisos[$evaluado->id]), $info_proyecto->id); ?></td>
                       <td style="text-align: right;"><?php echo to_number_project_format(100, $info_proyecto->id); ?> %</td>
                    </tr>
                   <?php foreach($array_estados_evaluados_permisos as $id_estado => $estado_evaluado) { ?>
                        <tr>
                           <td class="text-left"><?php echo $total_cantidades_estados_evaluados_permisos[$id_estado]["nombre_estado"]; ?></td>
                           <?php foreach($estado_evaluado["evaluados"] as $id_evaluado => $evaluado) { ?>
                           <?php
                             $total_evaluado = array_sum($array_total_por_evaluado_permisos[$id_evaluado]);
                             if($total_evaluado == 0){
                                 $porcentaje = 0;
                             } else {
                                 $porcentaje = ($evaluado["cant"] * 100) / ($total_evaluado); 
                             }
                            ?>
                            <td style="text-align: right;"><?php echo to_number_project_format($evaluado["cant"], $info_proyecto->id); ?></td>
                            <td style="text-align: right;"><?php echo to_number_project_format($porcentaje, $info_proyecto->id); ?> %</td>
                           <?php } ?>
                        </tr>
                    
                    <?php } ?>
                </tbody>       
            </table>
            <div style="font-size:20px">&nbsp;</div>
        
        <?php } ?>
        
        <!-- Fin Sección Resumen por Evaluado -->
        
        <br pagebreak="true">
        
        <!-- Sección Estados de Tramitación -->
        <h2><?php echo lang('status_procedure'); ?></h2>
        <table cellspacing="0" cellpadding="4" border="1">
            <thead>
                <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                    <th style="text-align: center;"><?php echo lang("permitting_number"); ?></th>
                    <th style="text-align: center;"><?php echo lang("name"); ?></th>
                    <?php foreach($columnas_evaluados_estados_tramitacion as $columna){ ?>
                        <th style="text-align: center;"><?php echo $columna["nombre_evaluado"]; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($result as $index_columnas => $res){ ?>
                <tr>
                    <td style="text-align: center;"><?php echo $res["numero_permiso"]; ?></td>
                    <td style="text-align: center;"><?php echo $res["nombre_permiso"]; ?></td>
                    <?php foreach($columnas_evaluados_estados_tramitacion as $columna){ ?>
                        <td style="text-align: center;">
                            <?php 
                                $estado = $res[$columna["id"]];
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
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <!-- Fin Sección Estados de Tramitación -->
    <?php } else { ?>
    
        <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('permitting_matrix_not_enabled'); ?>
    
    <?php } ?>
<?php } else { ?>
    
	<?php echo lang('content_disabled'); ?>

<?php } ?>
</body>