<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php //echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("kpi")." - ".lang("charts_between_projects"); ?>
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

	<h2><?php echo lang('materials_and_waste'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_total_waste_produced"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_total_waste_produced"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("total_waste_produced")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
        
    </table>
    
    <br><br><br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_waste_recycling_totals"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_waste_recycling_totals"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("waste_recycling")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang('emissions'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_total_emissions_by_source"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_total_emissions_by_source"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("total_emissions_by_source")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang('energy'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
       		<td align="center">
				<?php if($graficos["image_grafico_total_energy_consumption"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_total_energy_consumption"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("total_energy_consumption")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br><br><br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_energy_consumption_source_type"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_energy_consumption_source_type"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("energy_consumption_source_type")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang('water'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_total_water_consumption"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_total_water_consumption"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("total_water_consumption")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br><br><br>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_water_consumption_by_origin"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_water_consumption_by_origin"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("water_consumption_by_origin")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
        
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_water_reused_by_type"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_water_reused_by_type"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("water_reused_by_type")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
    <br pagebreak="true">
    
    <h2><?php echo lang('social'); ?></h2>
    
    <table cellspacing="0" cellpadding="4" border="0">
        <tr>
        	<td align="center">
				<?php if($graficos["image_grafico_social_expenses"]) { ?>
                    <img src="<?php echo $graficos["image_grafico_social_expenses"]; ?>" style="height:300px; width:450px;" />
                <?php } else { ?>
                    <?php //echo lang("expenses")."<br>".lang("no_information_available"); ?>
                    <?php echo lang("no_information_available"); ?>
                <?php } ?>
            </td>
        </tr>
    </table>
    
</body>