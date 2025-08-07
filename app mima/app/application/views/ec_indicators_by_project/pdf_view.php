<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("circularity_index")." - ".lang("charts_by_project"); ?>
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
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <span style="font-size:20px; text-align:center;">
					<?php
                        echo lang("cf")." = ".to_number_client_format($cf, $info_cliente->id);
                    ?>
                </span>
            </td>
        </tr>
    </table>
	
    <br pagebreak="true">
    
    <h2><?php echo lang('partial_indicators'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <?php if($graficos["image_grafico_indicadores_input"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_indicadores_input"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <span style="font-size:20px; text-align:center;">
					<?php
                        echo lang("v")." / ".lang("ti")." = ".to_number_client_format($input, $info_cliente->id);
                    ?>
                </span>
            </td>
        </tr>
    </table>
    
    <br><br><br><br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <?php if($graficos["image_grafico_indicadores_output"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_indicadores_output"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td align="center">
                <span style="font-size:20px; text-align:center;">
					<?php
                        echo lang("w")." / ".lang("variable_to")." = ".to_number_client_format($output, $info_cliente->id);
                    ?>
                </span>
            </td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang('variables'); ?></h2>
    <table style="padding-top:40px;">
        <tr>
            <td>
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
            </td>
            <td>
                <table cellspacing="0" cellpadding="4" border="1">
                    <thead>
                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                            <th style="text-align:center; vertical-align:middle;" colspan="3"><?php echo lang('inputs'); ?></th>
                        </tr>
                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                        	<th style="text-align:center; vertical-align:middle; width:20%;"><?php echo lang("variable"); ?></th>
                            <th style="text-align:center; width:30%;"><?php echo lang("total")." (".$unidad_masa_config.")"; ?></th>
                            <th style="text-align:center; width:50%;"><?php echo lang("information"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                            <td style="text-align:left;"><?php echo lang("rci"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($rci, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("rci_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("rcu"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($rui, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("rui_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("res"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($res, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("res_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("v"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($v, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("v_desc"); ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
    <table style="padding-top:40px;">
        <tr>
            <td>
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
            </td>
            <td>
                <table cellspacing="0" cellpadding="4" border="1">
                    <thead>
                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                            <th style="text-align:center; vertical-align:middle;" colspan="3"><?php echo lang('outputs'); ?></th>
                        </tr>
                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                        	<th style="text-align:center; vertical-align:middle; width:20%;"><?php echo lang("variable"); ?></th>
                            <th style="text-align:center; width:30%;"><?php echo lang("total")." (".$unidad_masa_config.")"; ?></th>
                            <th style="text-align:center; width:50%;"><?php echo lang("information"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                            <td style="text-align:left;"><?php echo lang("rco"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($rci, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("rco_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("ruo"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($rui, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("ruo_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("wrci"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($res, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("wrci_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("wrco"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($v, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("wrco_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("o"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($v, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("o_desc"); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;"><?php echo lang("wo"); ?></td>
                            <td style="text-align:right;"><?php echo to_number_client_format($v, $info_cliente->id); ?></td>
                            <td style="text-align:center;"><?php echo lang("wo_desc"); ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
</body>