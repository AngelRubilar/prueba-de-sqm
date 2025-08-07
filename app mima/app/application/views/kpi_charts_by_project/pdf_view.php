<body style="font-family: Times New Roman, Times, serif; font-size: 12px;">
<br><br>
<h1 align="center" style="font-family: Times New Roman, Times, serif;">
	<?php echo $info_proyecto->title; ?>
</h1>
<h2 align="center" style="text-decoration: underline; font-family: Times New Roman, Times, serif;">
	<?php echo lang("kpi")." - ".lang("charts_by_project"); ?>
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

	<?php foreach($array_estructuras_graficos as $item => $subitems) { ?>

		<!-- Item Materiales y Residuos -->
		<?php if($item == "materials_and_waste"){ ?>
        
        <h2><?php echo lang('materials_and_waste'); ?></h2>
        
        	<?php foreach($subitems as $nombre_subitem => $series){ ?>
            
            	<!-- Subitem Total residuos producidos -->
				<?php if($nombre_subitem == "total_waste_produced"){ ?>
                    
                    <?php 
                        $valor_total_final = 0;
                        foreach($series as $serie){
                            if($serie){
                                $valor_total_final = $valor_total_final + $serie;
                            }
                        }
                    ?>

                    <table style="padding-top:40px;">
                        <tr>
                            <td>
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
                            </td>
                            <td>
                                <table cellspacing="0" cellpadding="4" border="1">
                                    <thead>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;" colspan="2"><?php echo lang('total_waste_produced'); ?></th>
                                        </tr>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th style="text-align:center;"><?php echo $unidad_masa_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("non_hazardous_industrial_waste"); ?></td>
                                            <td style="text-align:right;">
                                                <?php 
                                                    if($series["non_hazardous_industrial_waste"] === NULL){
                                                        echo lang("no_information_available");
                                                    }else if($series["non_hazardous_industrial_waste"] === 0){
                                                        echo to_number_client_format($series["non_hazardous_industrial_waste"], $info_cliente->id);
                                                    } else {
                                                        echo to_number_client_format($series["non_hazardous_industrial_waste"], $info_cliente->id);
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("hazardous_industrial_waste"); ?></td>
                                            <td style="text-align:right;">
                                            <?php 
                                                if($series["hazardous_industrial_waste"] === NULL){
                                                    echo lang("no_information_available");
                                                }else if($series["hazardous_industrial_waste"] === 0){
                                                    echo to_number_client_format($series["hazardous_industrial_waste"], $info_cliente->id);
                                                } else {
                                                    echo to_number_client_format($series["hazardous_industrial_waste"], $info_cliente->id);
                                                }
                                            ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><strong><?php echo lang("total"); ?></strong></td>
                                            <td style="text-align:right;"><strong><?php echo to_number_client_format($valor_total_final, $info_cliente->id); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                        
            	<?php } ?> 
                <!-- Fin Subitem Total residuos producidos -->
                
                <!-- Subitem Reciclaje de residuos - totales -->
                 <?php if($nombre_subitem == "waste_recycling_totals"){ ?>
                 
                 	<table style="padding-top:40px;">
                        <tr>
                            <td>
                                <table cellspacing="0" cellpadding="4" border="0">
                                    <tr>
                                        <td align="center">
                                        	<?php if($graficos["image_grafico_waste_recycling_totals"]) { ?>
                                        		<img src="<?php echo $graficos["image_grafico_waste_recycling_totals"]; ?>" style="height:300px; width:450px;" />
                                        	<?php } else { ?>
                                            	<?php //echo lang("waste_recycling_totals")."<br>".lang("no_information_available"); ?>
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
                                            <th style="text-align:center; vertical-align:middle;" colspan="2"><?php echo lang('waste_recycling_totals'); ?></th>
                                        </tr>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th style="text-align:center;"><?php echo $unidad_masa_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("waste_without_recycling"); ?></td>
                                            <td style="text-align:right;">
                                            <?php 
												if($series["waste_without_recycling"] === NULL){
													echo lang("no_information_available");
												}else if($series["waste_without_recycling"] === 0){
													echo to_number_client_format($series["waste_without_recycling"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["waste_without_recycling"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("rises_recycled"); ?></td>
                                        	<td style="text-align:right;">
                                            <?php 
												if($series["rises_recycled"] === NULL){
													echo lang("no_information_available");
												}else if($series["rises_recycled"] === 0){
													echo to_number_client_format($series["rises_recycled"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["rises_recycled"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("respel_recycled"); ?></td>
                                       		<td style="text-align:right;">
                                            <?php 
												if($series["respel_recycled"] === NULL){
													echo lang("no_information_available");
												}else if($series["respel_recycled"] === 0){
													echo to_number_client_format($series["respel_recycled"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["respel_recycled"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                 
                 
                 <?php } ?> 
                <!-- Fin Subitem Reciclaje de residuos - totales -->
                <br><br><br>
                <!-- Subitem Reciclaje de residuos - mensuales -->
                <?php if($nombre_subitem == "waste_recycling_monthly"){ ?>
                	
                	<table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center">
								<?php if($graficos["image_grafico_waste_recycling_monthly"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_waste_recycling_monthly"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("waste_recycling_monthly")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                
                <?php } ?> 
                 <!-- Fin Subitem Reciclaje de residuos - mensuales -->
                
        	<?php } ?> 
            
        	<br><br><br>
            
        <?php } ?>
        <!-- Fin Item Materiales y Residuos -->
        
        
        <!-- Item Emisiones -->
        <?php if($item == "emissions"){ ?>
        	
            <br pagebreak="true">
            
        	<?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                 <!-- Subitem Total de emisiones por fuente -->
                 <?php if($nombre_subitem == "total_emissions_by_source"){ ?>
                 	<h2><?php echo lang("emissions"); ?></h2>
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
            	<?php } ?>
                <!-- Fin Subitem Total de emisiones por fuente -->
                
            <?php } ?>
                        
        <?php } ?>
        <!-- Fin Item Emisiones -->
        
        
        <!-- Item Energia -->
        <?php if($item == "energy"){ ?>
        	
            <br pagebreak="true">
            
        	<?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                <!-- Subitem Consumo de energía por tipo de fuente -->
                <?php if($nombre_subitem == "energy_consumption_source_type"){ ?>
                    
                    <?php 
                        $valor_total_final = 0;
                        foreach($series as $serie){
                            if($serie){
                                $valor_total_final = $valor_total_final + $serie;
                            }
                        }
                    ?>
                    <h2><?php echo lang("energy"); ?></h2>
                    <table style="padding-top:40px;">
                        <tr>
                            <td>
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
                            </td>
                            <td>
                                <table cellspacing="0" cellpadding="4" border="1">
                                    <thead>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;" colspan="2"><?php echo lang('energy_consumption_source_type'); ?></th>
                                        </tr>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th style="text-align:center;"><?php echo $unidad_energia_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("renewable"); ?></td>
                                        	<td style="text-align:right;">
                                            <?php 
												if($series["renewable"] === NULL){
													echo lang("no_information_available");
												}else if($series["renewable"] === 0){
													echo to_number_client_format($series["renewable"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["renewable"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("not_renewable"); ?></td>
                                        	<td style="text-align:right;">
                                            <?php 
												if($series["not_renewable"] === NULL){
													echo lang("no_information_available");
												}else if($series["not_renewable"] === 0){
													echo to_number_client_format($series["not_renewable"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["not_renewable"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><strong><?php echo lang("total"); ?></strong></td>
                                            <td style="text-align:right;"><strong><?php echo to_number_client_format($valor_total_final, $info_cliente->id); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
    
                <?php } ?>
                <!-- Fin Subitem Consumo de energía por tipo de fuente -->
				
                <br><br><br>
                
                <!-- Subitem Consumo energia -->
                <?php if($nombre_subitem == "energy_consumption"){ ?>
                
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                            <td align="center">
                            	<?php if($graficos["image_grafico_energy_consumption"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_energy_consumption"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("energy_consumption")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                
                <?php } ?>
                <!-- Fin Subitem Consumo energia -->	
                
			<?php } ?>

        <?php } ?>
        <!-- Fin Item Energia -->
        
        
        <!-- Item Agua -->
        <?php if($item == "water"){ ?>
        	
            <br pagebreak="true">
            
        	<h2><?php echo lang("water"); ?></h2>
            
        	<?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                <!-- Subitem Consumo de agua por procedencia -->
                <?php if($nombre_subitem == "water_consumption_by_origin"){ ?>
        			
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
                    <br><br><br>
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                        	<td align="center">
                            	<?php if($graficos["image_grafico_water_consumption_by_origin_2"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_water_consumption_by_origin_2"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("water_consumption_by_origin")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
					<br><br><br>
				<?php } ?>
                <!-- Fin Consumo de agua por procedencia -->
                
                <!-- Subitem Agua reutilizada por tipo -->
                <?php if($nombre_subitem == "water_reused_by_type"){ ?>
                	
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
                    <br><br><br>
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                        	<td align="center">
                            	<?php if($graficos["image_grafico_water_reused_by_type_2"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_water_reused_by_type_2"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("water_reused_by_type")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    
                <?php } ?>
                <!-- Function Subitem Agua reutilizada por tipo -->
                
			<?php } ?>
                
        <?php } ?>
        <!-- Fin Item Agua -->
        
        <!-- Item Social -->
        <?php if($item == "social"){ ?>
        
        	<br pagebreak="true">
            
        	<h2><?php echo lang("social"); ?></h2>
            
        	<?php foreach($subitems as $nombre_subitem => $series){ ?>

                <!-- Subitem Proporción de gastos dedicada a proveedores locales -->
                <?php if($nombre_subitem == "proportion_expenses_dedicated_local_suppliers"){ ?>
                    
                    <?php 
                        $valor_total_final = 0;
                        foreach($series as $serie){
                            if($serie){
                                $valor_total_final = $valor_total_final + $serie;
                            }
                        }
                    ?>
                    
                    <table style="padding-top:40px;">
                        <tr>
                            <td>
                                <table cellspacing="0" cellpadding="4" border="0">
                                    <tr>
                                    	<td align="center">
											<?php if($graficos["image_grafico_proportion_expenses_dedicated_local_suppliers"]) { ?>
                                                <img src="<?php echo $graficos["image_grafico_proportion_expenses_dedicated_local_suppliers"]; ?>" style="height:300px; width:450px;" />
                                            <?php } else { ?>
                                                <?php //echo lang("proportion_expenses_dedicated_local_suppliers")."<br>".lang("no_information_available"); ?>
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
                                            <th style="text-align:center; vertical-align:middle;" colspan="2"><?php echo lang('proportion_expenses_dedicated_local_suppliers'); ?></th>
                                        </tr>
                                        <tr style="background-color: <?php echo $info_cliente->color_sitio; ?>;">
                                            <th style="text-align:center; vertical-align:middle;"><?php echo lang("expense_type"); ?></th>
                                            <th style="text-align:center;"><?php echo lang("euros"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("expenditure_local_suppliers"); ?></td>
                                        	<td style="text-align:right;">
                                            <?php 
												if($series["expenditure_local_suppliers"] === NULL){
													echo lang("no_information_available");
												}else if($series["expenditure_local_suppliers"] === 0){
													echo to_number_client_format($series["expenditure_local_suppliers"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["expenditure_local_suppliers"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><?php echo lang("other_expenses"); ?></td>
                                        	<td style="text-align:right;">
                                            <?php 
												if($series["other_expenses"] === NULL){
													echo lang("no_information_available");
												}else if($series["other_expenses"] === 0){
													echo to_number_client_format($series["other_expenses"], $info_cliente->id);
												} else {
													echo to_number_client_format($series["other_expenses"], $info_cliente->id);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;"><strong><?php echo lang("total"); ?></strong></td>
                                            <td style="text-align:right;"><strong><?php echo to_number_client_format($valor_total_final, $info_cliente->id); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
        		<?php } ?>
                <!-- Fin Subitem Proporción de gastos dedicada a proveedores locales -->
                
                <br><br><br>
                
                <!-- Subitem Gasto en proveedores locales -->
				<?php if($nombre_subitem == "expenditure_local_suppliers"){ ?>
                	
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                        	<td align="center">
								<?php if($graficos["image_grafico_expenditure_local_suppliers"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_expenditure_local_suppliers"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("expenditure_local_suppliers")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    
                <?php } ?>
                <!-- Fin Subitem Gasto en proveedores locales -->
               
                
                <!-- Subitem Soluciones, acciones e instalaciones -->
				<?php if($nombre_subitem == "solutions_actions_facilities"){ ?>
                
                	<table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                        	<td align="center">
								<?php if($graficos["image_grafico_solutions_actions_facilities"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_solutions_actions_facilities"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("solutions_actions_facilities")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                
                <?php } ?>
                <!-- Fin Subitem Soluciones, acciones e instalaciones -->
               
               <br><br><br>
                
                <!-- Subitem Soluciones donadas y beneficiarios -->
				<?php if($nombre_subitem == "donated_solutions_beneficiaries"){ ?>
                	
                    <table cellspacing="0" cellpadding="4" border="0">
                        <tr>
                        	<td align="center">
								<?php if($graficos["image_grafico_donated_solutions_beneficiaries"]) { ?>
                                    <img src="<?php echo $graficos["image_grafico_donated_solutions_beneficiaries"]; ?>" style="height:300px; width:450px;" />
                                <?php } else { ?>
                                    <?php //echo lang("donated_solutions_beneficiaries")."<br>".lang("no_information_available"); ?>
                                    <?php echo lang("no_information_available"); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    
				<?php } ?>
                <!-- Fin Subitem Soluciones donadas y beneficiarios -->
                
			<?php } ?>
		
		<?php } ?>
        <!-- Fin Item Social -->
        
	<?php } ?>
    
</body>