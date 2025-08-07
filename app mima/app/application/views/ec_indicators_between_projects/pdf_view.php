<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<!--
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
-->
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("circularity_index")." - ".lang("charts_between_projects"); ?>
</h2>
<div align="center">
    <?php
		$hoy = date('d-m-Y');
		$fecha = date(get_setting_client_mimasoft($info_cliente->id, "date_format"), strtotime($hoy));
		$hora = format_to_time_clients($info_cliente->id, get_current_utc_time("H:i:s"));
		
		echo lang("datetime_download") . ": " . $fecha.' '.lang("at").' '.$hora;
	?>
</div>

<br>

    <h2><?php echo lang('circularity_index'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
				<?php if($graficos["image_grafico_circularity_index"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_circularity_index"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th style="text-align:center;"><?php echo lang("project"); ?></th>
                <th style="text-align:center;"><?php echo lang("cf"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                <tr>
                    <td style="text-align:left;"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["cf"], $info_cliente->id); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br pagebreak="true" />
    

    <h2><?php echo lang('partial_indicators'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
				<?php if($graficos["image_grafico_indicadores_parciales"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_indicadores_parciales"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th style="text-align:center;"><?php echo lang("project"); ?></th>
                <th style="text-align:center;"><?php echo lang("v")."/".lang("ti"); ?></th>
                <th style="text-align:center;"><?php echo lang("w")."/".lang("variable_to"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                <tr>
                    <td style="text-align:left;"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["input"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["output"], $info_cliente->id); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    
    <br pagebreak="true" />
    
    <h2><?php echo lang('variables'); ?></h2>
        
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <?php if($graficos["image_grafico_variables_input"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_variables_input"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th colspan="5"style="text-align:left; vertical-align:middle;"><h4><?php echo lang("inputs"); ?></h4></th>
            </tr>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th style="text-align:center;"><?php echo lang("project"); ?></th>
                <th style="text-align:center;"><?php echo lang("rci")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("rcu")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("res")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("v")." (".$unidad_masa_config.")"; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                <tr>
                    <td style="text-align:left;"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["rci"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["rui"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["res"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["v"], $info_cliente->id); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    
    <br pagebreak="true" />
    
     <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <?php if($graficos["image_grafico_variables_output"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_variables_output"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
     <table cellspacing="0" cellpadding="4" border="1">
        <thead>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th colspan="7" class="text-left" style="vertical-align:middle;"><h4><?php echo lang("outputs"); ?></h4></th>
            </tr>
            <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                <th style="text-align:center;"><?php echo lang("project"); ?></th>
                <th style="text-align:center;"><?php echo lang("rco")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("ruo")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("wrci")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("wrco")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("o")." (".$unidad_masa_config.")"; ?></th>
                <th style="text-align:center;"><?php echo lang("wo")." (".$unidad_masa_config.")"; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                <tr>
                    <td style="text-align:left;"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["rco"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["ruo"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["wrci"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["wrco"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["o"], $info_cliente->id); ?></td>
                    <td style="text-align:right;"><?php echo to_number_client_format($variables_proyecto["wo"], $info_cliente->id); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    
</body>