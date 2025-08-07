<div class="form-group">

	<?php foreach($array_estructuras_graficos as $item => $subitems) { ?>
    	
        <!-- Item Materiales y Residuos -->
		<?php if($item == "materials_and_waste"){ ?>
        
            <div class="panel panel-default mb15">
                <div class="page-title clearfix">
                    <h1><?php echo lang("materials_and_waste"); ?></h1>
                </div>

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
                        
                        <div class="panel-body">
                        
                            <div class="col-md-6">
                            
                                <table class="table table-striped">
                                    <thead>
                                    	<tr>
                                        	<th class="text-center" colspan="2"><?php echo lang('total_waste_produced'); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th class="text-center"><?php echo $unidad_masa_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><?php echo lang("non_hazardous_industrial_waste"); ?></td>
                                            <td class="text-right">
												<?php 
                                                	if($series["non_hazardous_industrial_waste"] === NULL){
														echo lang("no_information_available");
													}else if($series["non_hazardous_industrial_waste"] === 0){
														echo to_number_client_format($series["non_hazardous_industrial_waste"], $id_cliente);
													} else {
														echo to_number_client_format($series["non_hazardous_industrial_waste"], $id_cliente);
													}
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo lang("hazardous_industrial_waste"); ?></td>
                                            <td class="text-right">
                                            <?php 
												if($series["hazardous_industrial_waste"] === NULL){
													echo lang("no_information_available");
												}else if($series["hazardous_industrial_waste"] === 0){
													echo to_number_client_format($series["hazardous_industrial_waste"], $id_cliente);
												} else {
													echo to_number_client_format($series["hazardous_industrial_waste"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><strong><?php echo lang("total"); ?></strong></td>
                                            <td class="text-right"><strong><?php echo to_number_client_format($valor_total_final, $id_cliente); ?></strong></td>
                                        </tr>    
                                    </tbody>
                                </table>
                        
                        	</div>
                
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("total_waste_produced"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_total_waste_produced" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>  

                    <?php } ?>
                    
                    <!-- Subitem Reciclaje de residuos - totales -->
                    <?php if($nombre_subitem == "waste_recycling_totals"){ ?>
                    
                        <div class="panel-body">

                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <thead>
                                    	<tr>
                                        	<th class="text-center" colspan="2"><?php echo lang('waste_recycling_totals'); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th class="text-center"><?php echo $unidad_masa_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><?php echo lang("waste_without_recycling"); ?></td>
                                            <td class="text-right">
                                            <?php 
												if($series["waste_without_recycling"] === NULL){
													echo lang("no_information_available");
												}else if($series["waste_without_recycling"] === 0){
													echo to_number_client_format($series["waste_without_recycling"], $id_cliente);
												} else {
													echo to_number_client_format($series["waste_without_recycling"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo lang("rises_recycled"); ?></td>
                                        	<td class="text-right">
                                            <?php 
												if($series["rises_recycled"] === NULL){
													echo lang("no_information_available");
												}else if($series["rises_recycled"] === 0){
													echo to_number_client_format($series["rises_recycled"], $id_cliente);
												} else {
													echo to_number_client_format($series["rises_recycled"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo lang("respel_recycled"); ?></td>
                                       		<td class="text-right">
                                            <?php 
												if($series["respel_recycled"] === NULL){
													echo lang("no_information_available");
												}else if($series["respel_recycled"] === 0){
													echo to_number_client_format($series["respel_recycled"], $id_cliente);
												} else {
													echo to_number_client_format($series["respel_recycled"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr> 
                                    </tbody>
                                </table>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("waste_recycling_totals"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_waste_recycling_totals" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                            
                        </div>  

                    <?php } ?>
                    
                    <!-- Subitem Reciclaje de residuos - mensuales -->
                    <?php if($nombre_subitem == "waste_recycling_monthly"){ ?>
						
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("waste_recycling_monthly"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_waste_recycling_monthly" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                            
                        </div>  

                    <?php } ?>
                    
                <?php } ?>

        	</div>

        <?php } ?>
        
        
        <!-- Item Emisiones -->
        <?php if($item == "emissions"){ ?>
        
        	<div class="panel panel-default mb15">
                <div class="page-title clearfix">
                    <h1><?php echo lang("emissions"); ?></h1>
                </div>
                
                <?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                    <!-- Subitem Total de emisiones por fuente -->
                    <?php if($nombre_subitem == "total_emissions_by_source"){ ?>
                	
						<div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("total_emissions_by_source"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_total_emissions_by_source" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>
                    
					<?php } ?>
                    
                <?php } ?>
                
        	</div>
        
        <?php } ?>
        
        
        <!-- Item Energia -->
        <?php if($item == "energy"){ ?>
        
        	<div class="panel panel-default mb15">
                <div class="page-title clearfix">
                    <h1><?php echo lang("energy"); ?></h1>
                </div>
                
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
                        
                        <div class="panel-body">
                        
                            <div class="col-md-6">
                            
                                <table class="table table-striped">
                                    <thead>
                                    	<tr>
                                        	<th class="text-center" colspan="2"><?php echo lang('energy_consumption_source_type'); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="vertical-align:middle;"><?php echo lang("waste_type"); ?></th>
                                            <th class="text-center"><?php echo $unidad_energia_config; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><?php echo lang("renewable"); ?></td>
                                        	<td class="text-right">
                                            <?php 
												if($series["renewable"] === NULL){
													echo lang("no_information_available");
												}else if($series["renewable"] === 0){
													echo to_number_client_format($series["renewable"], $id_cliente);
												} else {
													echo to_number_client_format($series["renewable"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo lang("not_renewable"); ?></td>
                                        	<td class="text-right">
                                            <?php 
												if($series["not_renewable"] === NULL){
													echo lang("no_information_available");
												}else if($series["not_renewable"] === 0){
													echo to_number_client_format($series["not_renewable"], $id_cliente);
												} else {
													echo to_number_client_format($series["not_renewable"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><strong><?php echo lang("total"); ?></strong></td>
                                            <td class="text-right"><strong><?php echo to_number_client_format($valor_total_final, $id_cliente); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                        
                        	</div>
                
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("energy_consumption_source_type"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_energy_consumption_source_type" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>
                        
					<?php } ?>
                    
                    
                    <!-- Subitem Consumo energia -->
                    <?php if($nombre_subitem == "energy_consumption"){ ?>
						
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("energy_consumption"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_energy_consumption" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                            
                        </div>  

                    <?php } ?>
                    
                    
                    
        		<?php } ?>
       		
            </div>
            
        <?php } ?>
        
        
        <!-- Item Agua -->
        <?php if($item == "water"){ ?>
        	
            <div class="panel panel-default mb15">
                <div class="page-title clearfix">
                    <h1><?php echo lang("water"); ?></h1>
                </div>

				<?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                    <!-- Subitem Total residuos producidos -->
                    <?php if($nombre_subitem == "water_consumption_by_origin"){ ?>

                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("water_consumption_by_origin"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_water_consumption_by_origin" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>
                        
                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("water_consumption_by_origin"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_water_consumption_by_origin_2" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                            
                        </div>  

					<?php } ?>
                    
                    <!-- Subitem Agua reutilizada por tipo -->
                    <?php if($nombre_subitem == "water_reused_by_type"){ ?>
        				
                        <div class="panel-body">
                        
                            <div class="col-md-12">
                            
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("water_reused_by_type"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_water_reused_by_type" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>
                        
                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("water_reused_by_type"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_water_reused_by_type_2" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                            
                        </div>  
                        
					<?php } ?>
                    
				<?php } ?>
                
        	</div>  
		
		<?php } ?>
        
        
        <!-- Item Social -->
        <?php if($item == "social"){ ?>
        	
            <div class="panel panel-default mb15">
                <div class="page-title clearfix">
                    <h1><?php echo lang("social"); ?></h1>
                </div>

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

                        <div class="panel-body">
                        
                            <div class="col-md-6">
                            
                                <table class="table table-striped">
                                    <thead>
                                    	<tr>
                                        	<th class="text-center" colspan="2"><?php echo lang('proportion_expenses_dedicated_local_suppliers'); ?></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="vertical-align:middle;"><?php echo lang("expense_type"); ?></th>
                                            <th class="text-center"><?php echo lang("euros"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><?php echo lang("expenditure_local_suppliers"); ?></td>
                                        	<td class="text-right">
                                            <?php 
												if($series["expenditure_local_suppliers"] === NULL){
													echo lang("no_information_available");
												}else if($series["expenditure_local_suppliers"] === 0){
													echo to_number_client_format($series["expenditure_local_suppliers"], $id_cliente);
												} else {
													echo to_number_client_format($series["expenditure_local_suppliers"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><?php echo lang("other_expenses"); ?></td>
                                        	<td class="text-right">
                                            <?php 
												if($series["other_expenses"] === NULL){
													echo lang("no_information_available");
												}else if($series["other_expenses"] === 0){
													echo to_number_client_format($series["other_expenses"], $id_cliente);
												} else {
													echo to_number_client_format($series["other_expenses"], $id_cliente);
												}
											?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><strong><?php echo lang("total"); ?></strong></td>
                                            <td class="text-right"><strong><?php echo to_number_client_format($valor_total_final, $id_cliente); ?></strong></td>
                                        </tr>    
                                    </tbody>
                                </table>
                        
                        	</div>
                
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("proportion_expenses_dedicated_local_suppliers"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_proportion_expenses_dedicated_local_suppliers" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>  

                    <?php } ?>
                    
                    <!-- Subitem Gasto en proveedores locales -->
                    <?php if($nombre_subitem == "expenditure_local_suppliers"){ ?>

                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("expenditure_local_suppliers"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_expenditure_local_suppliers" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>  

                    <?php } ?>
                    
                    <!-- Subitem Soluciones, acciones e instalaciones -->
                    <?php if($nombre_subitem == "solutions_actions_facilities"){ ?>

                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("solutions_actions_facilities"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_solutions_actions_facilities" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>  

                    <?php } ?>
                    
                    <!-- Subitem Soluciones donadas y beneficiarios -->
                    <?php if($nombre_subitem == "donated_solutions_beneficiaries"){ ?>

                        <div class="panel-body">
                        
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                   <div class="page-title clearfix panel-success">
                                      <!--<h3>Cambio climático</h3> -->
                                      <div class="pt10 pb10 text-center"><?php echo lang("donated_solutions_beneficiaries"); ?></div>
                                   </div>
                                   <div class="panel-body">
                                         <!--<div id="grafico_carbono"margin: 0 auto;"> -->
                                      <div id="grafico_donated_solutions_beneficiaries" style="height: 240px;"></div>
                                   </div>
                                </div>
                            </div>
                        
                   		</div>  

                    <?php } ?>

				<?php } ?>
                
		<?php } ?>

    <?php } ?>

</div>
	
<div id="php-dump">

	<?php
	
		//print_r($array_estructuras_graficos);
		//var_dump(null);
	?>
	
</div>

<script type="text/javascript">

$(document).ready(function(){
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparatorClient;
		var thousands_separator = AppHelper.settings.thousandSeparatorClient;
		var decimal_numbers = AppHelper.settings.decimalNumbersClient;	
		
		 <?php foreach($array_estructuras_graficos as $item => $subitems) { ?>
		 
		 	<?php if($item == "materials_and_waste"){ ?>
			
				<?php foreach($subitems as $nombre_subitem => $series){ ?>
						
						<!-- Subitem Total residuos producidos -->
						<?php if($nombre_subitem == "total_waste_produced"){ ?>
							
							<?php 
								$valor_total_final = 0;
								foreach($series as $serie){
									$serie_num_val = $serie ?: 0;
									if($serie_num_val >= 0){
										$valor_total_final = $valor_total_final + $serie_num_val;
									}
								}
							?>
							
							<?php //if(!$series["non_hazardous_industrial_waste"] && !$series["hazardous_industrial_waste"]) { ?>
							
							
							<?php if(!is_numeric($series["non_hazardous_industrial_waste"]) && !is_numeric($series["hazardous_industrial_waste"])) { ?>
							
								$('#grafico_total_waste_produced').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
								
							<?php } else { ?>
								
								$('#grafico_total_waste_produced').highcharts({
										chart: {
											plotBackgroundColor: null,
											plotBorderWidth: null,
											plotShadow: false,
											type: 'pie',
											events: {
											   load: function() {
												   if (this.options.chart.forExport) {
													   Highcharts.each(this.series, function (series) {
														   series.update({
															   dataLabels: {
																   enabled: true,
																}
															}, false);
														});
														this.redraw();
													}
												}
											}
										},
										title: {
											text: '',
										},
										credits: {
											enabled: false
										},
										tooltip: {
											formatter: function() {
												return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+' %';
											},
										},
										plotOptions: {
											pie: {
											//size: 80,
											allowPointSelect: true,
											cursor: 'pointer',
											dataLabels: {
												enabled: false,
												format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
												style: {
													color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
													fontSize: "9px",
													distance: -30
												},
												crop: false
											},
											showInLegend: true
											}
										},
										legend: {
											enabled: true,
											itemStyle:{
												fontSize: "9px"
											}
										},
										exporting: {
											<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("materials_and_waste")."_".lang("total_waste_produced")."_".$fecha_actual; ?>
											filename: "<?php echo str_replace(' ', '_', $filename) ?>",
											buttons: {
												contextButton: {
													menuItems: [{
														text: "<?php echo lang('export_to_png'); ?>",
														onclick: function() {
															this.exportChart();
														},
														separator: false
													}]
												}
											}
										},
										colors : ['#808000', '#333300'],
										series: [{
											name: 'Porcentaje',
											colorByPoint: true,
											data: [
												{
													name: '<?php echo lang("non_hazardous_industrial_waste"); ?>',
													y: <?php echo $valor_total_final == 0 ? 0 : ($series["non_hazardous_industrial_waste"] * 100) / $valor_total_final; ?>
												},
												{
													name: '<?php echo lang("hazardous_industrial_waste"); ?>',
													y: <?php echo $valor_total_final == 0 ? 0 : ($series["hazardous_industrial_waste"] * 100) / $valor_total_final; ?>
												},
											]
										}]
									});

							<?php } ?>

						<?php } ?>
						
						<!-- Subitem Reciclaje de residuos - totales -->
						<?php if($nombre_subitem == "waste_recycling_totals"){ ?>
							
							<?php 
								$valor_total_final = 0;
								foreach($series as $serie){
									$serie_num_val = $serie ?$serie:0;
									if($serie_num_val >= 0){
										$valor_total_final = $valor_total_final + $serie_num_val;
									}
								}
							?>
							
							<?php //if(!$series["waste_without_recycling"] && !$series["rises_recycled"] && !$series["respel_recycled"]) { ?>
							
							
							<?php if(!is_numeric($series["waste_without_recycling"]) && !is_numeric($series["rises_recycled"]) && !is_numeric($series["respel_recycled"])) { ?>
							
								$('#grafico_waste_recycling_totals').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
									
							<?php } else { ?>
								
								$('#grafico_waste_recycling_totals').highcharts({
										chart: {
											plotBackgroundColor: null,
											plotBorderWidth: null,
											plotShadow: false,
											type: 'pie',
											events: {
											   load: function() {
												   if (this.options.chart.forExport) {
													   Highcharts.each(this.series, function (series) {
														   series.update({
															   dataLabels: {
																   enabled: true,
																}
															}, false);
														});
														this.redraw();
													}
												}
											}
										},
										title: {
											text: '',
										},
										credits: {
											enabled: false
										},
										tooltip: {
											formatter: function() {
												return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+' %';
											},
										},
										plotOptions: {
											pie: {
											//size: 80,
											allowPointSelect: true,
											cursor: 'pointer',
											dataLabels: {
												enabled: false,
												format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
												style: {
													color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
													fontSize: "9px",
													distance: -30
												},
												crop: false
											},
											showInLegend: true
											}
										},
										legend: {
											enabled: true,
											itemStyle:{
												fontSize: "9px"
											}
										},
										exporting: {
											<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("materials_and_waste")."_".lang("waste_recycling_totals")."_".$fecha_actual; ?>
											filename: "<?php echo str_replace(' ', '_', $filename) ?>",
											buttons: {
												contextButton: {
													menuItems: [{
														text: "<?php echo lang('export_to_png'); ?>",
														onclick: function() {
															this.exportChart();
														},
														separator: false
													}]
												}
											}
										},
										colors : ['#542A00', '#993300', '#996600'],
										series: [{
											name: 'Porcentaje',
											colorByPoint: true,
											data: [
												{
													name: '<?php echo lang("waste_without_recycling"); ?>',
													y: <?php echo $valor_total_final == 0 ? 0 : (($series["waste_without_recycling"] * 100) / $valor_total_final); ?>
												},
												{
													name: '<?php echo lang("rises_recycled"); ?>',
													y: <?php echo $valor_total_final == 0 ? 0 : (($series["rises_recycled"] * 100) / $valor_total_final); ?>
												},
												{
													name: '<?php echo lang("respel_recycled"); ?>',
													y: <?php echo $valor_total_final == 0 ? 0 : (($series["respel_recycled"] * 100) / $valor_total_final); ?>
												},
											]
										}]
									});

							<?php } ?>

						<?php } ?>
						
						<!-- Subitem Reciclaje de residuos - mensuales -->
						<?php if($nombre_subitem == "waste_recycling_monthly"){ ?>
							
							<?php //if(!$series["waste_without_recycling"] && !$series["rises_recycled"] && !$series["respel_recycled"]) { ?>
							
							var image_grafico_waste_recycling_monthly;					 
							<?php if(!is_numeric($series["waste_without_recycling"]) && !is_numeric($series["rises_recycled"]) && !is_numeric($series["respel_recycled"])) { ?>
							
								$('#grafico_waste_recycling_monthly').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
									
							<?php } else { ?>
								
								$('#grafico_waste_recycling_monthly').highcharts({
									chart: {
										zoomType: 'x',
										reflow: true,
										vresetZoomButton: {
											position: {
												align: 'left',
												x: 0
											}
										},
										type: 'column',
										events: {
											load: function(event){
												
											}
										} 
									},
									title: {
										text: ''
									},
									credits: {
										enabled: false
									},
									exporting:{
										enabled: true,
										
										/*
										yAxis: {
											min: 0,
											title: '%',	
											labels:{ 
												formatter: function(){
													return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
												} 
											},
										},
										
										chartOptions: {
											series: [
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
										   
										   ],
										},
										*/
										
										<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("materials_and_waste")."_".lang("waste_recycling_monthly")."_".$fecha_actual; ?>
										filename: "<?php echo str_replace(' ', '_', $filename) ?>",
										buttons: {
											contextButton: {
												menuItems: [{
													text: '<?php echo lang("export_to_png"); ?>',
													onclick: function(){
														this.exportChart()
													},
													separator: false
												}]
											}
										}
									},
									xAxis: {
										//min: 0,
										categories: [
										<?php foreach($rango_fechas as $anio => $meses) { ?>
											<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
												'<?php echo $nombre_mes . "-" . $anio; ?>',
											<?php } ?>
										<?php } ?>
										],
										//crosshair: true
									},
									yAxis: {
										min: 0,
										title: {
											text: '%'
										},
										labels: {
											format: "{value:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
											},*/
										},
										stackLabels: {
											enabled: true,
											format: "{total:,." + decimal_numbers + "f}",
											//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
											//format: "{total:." + decimal_numbers + "f}",
										}
									},
									legend: {
										align: 'center',
										verticalAlign: 'bottom',
										backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
										shadow: false
									},
									tooltip: {
										headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
										pointFormatter: function(){
											return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%)</td></tr>';
										},
										footerFormat: '</table>',
										useHTML: true,
										//shared: true
									},
									plotOptions: {
										column: {
											stacking: 'percent',
											pointPadding: 0.2,
											borderWidth: 0,
											dataLabels: {
												enabled: true,
												color: '#000000',
												align: 'center',
												format: "{y:,." + decimal_numbers + "f}",
												/*formatter: function(){
													return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
												},*/
												style: {
													fontSize: '10px',
													fontFamily: 'Segoe ui, sans-serif'
												}
											}
										}
									},
									subtitle: {
										text: ''
									},
									colors : ['#542A00', '#993300', '#996600'],
									series : [
										<?php foreach($array_valores_waste_recycling_monthly as $serie => $valores) { ?>
											{
												name: '<?php echo lang($serie); ?>',
												data: [
													<?php foreach($valores as $anio => $meses){ ?>													
														<?php foreach($meses as $mes => $valor){ ?>
															<?php echo $valor ?$valor: 0; ?>,
														<?php } ?>
													<?php } ?>
													],
											},
										<?php } ?>
									]

								});
	
							<?php } ?>
							
						<?php } ?>
						
				<?php } ?>
			
		 	<?php } ?>
			
			
			<!-- Item Emisiones -->
			<?php if($item == "emissions"){ ?>
			
			
				<?php foreach($subitems as $nombre_subitem => $series){ ?>
                    
                    <!-- Subitem Total residuos producidos -->
                    <?php if($nombre_subitem == "total_emissions_by_source"){ ?>

						<?php //if(!$series["direct_emissions"] && !$series["indirect_emissions_energy"] && !$series["other_indirect_emissions"]) { ?>
						
						var image_grafico_total_emissions_by_source;
						<?php if(!is_numeric($series["direct_emissions"]) && !is_numeric($series["indirect_emissions_energy"]) && !is_numeric($series["other_indirect_emissions"])) { ?>

							$('#grafico_total_emissions_by_source').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
														
							$('#grafico_total_emissions_by_source').highcharts({
								
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								exporting:{
									enabled: true,
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("emissions")."_".lang("total_emissions_by_source")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									type: 'category',
								},
								yAxis: {
									min: 0,
									title: {
										text: '<?php echo lang("co2_emissions")." (".$unidad_masa_config.")"; ?>',
									},
									labels: {
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}",*/
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									enabled: false,
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								credits: {
									enabled: false
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								tooltip: {
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								colors : ['#003300', '#00CC00', '#007800'],
								series: [{
									name: '<?php echo lang("co2_emissions")." (".$unidad_masa_config.")"; ?>',
									data: [
										['<?php echo lang("direct_emissions"); ?>', <?php echo (float)$series["direct_emissions"] ?: 0; ?>],
										['<?php echo lang("indirect_emissions_energy"); ?>', <?php echo (float)$series["indirect_emissions_energy"] ?: 0; ?>],
										['<?php echo lang("other_indirect_emissions"); ?>', <?php echo (float)$series["other_indirect_emissions"] ?: 0; ?>],
									],
								}]

							});

						<?php } ?>

					<?php } ?>
				
				<?php } ?>
				
			<?php } ?>
			
			
			<!-- Item Energia -->
        	<?php if($item == "energy"){ ?>
			
				<?php foreach($subitems as $nombre_subitem => $series){ ?>

					<!-- Subitem Consumo de energía por tipo de fuente -->
					<?php if($nombre_subitem == "energy_consumption_source_type"){ ?>

						<?php 
							$valor_total_final = 0;
							foreach($series as $serie){
								$serie_num_val = $serie ?$serie: 0;
								if($serie_num_val >= 0){
									$valor_total_final = $valor_total_final + $serie_num_val;
								}
							}
						?>
						
						<?php //if(!$series["renewable"] && !$series["not_renewable"]) { ?>
						
						var image_grafico_energy_consumption_source_type;
						<?php if(!is_numeric($series["renewable"]) && !is_numeric($series["not_renewable"])) { ?>
						
							$('#grafico_energy_consumption_source_type').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
							
						<?php } else { ?>
						
							$('#grafico_energy_consumption_source_type').highcharts({
									chart: {
										plotBackgroundColor: null,
										plotBorderWidth: null,
										plotShadow: false,
										type: 'pie',
										events: {
										   load: function() {
											   if (this.options.chart.forExport) {
												   Highcharts.each(this.series, function (series) {
													   series.update({
														   dataLabels: {
															   enabled: true,
															}
														}, false);
													});
													this.redraw();
												}
											}
										}
									},
									title: {
										text: '',
									},
									credits: {
										enabled: false
									},
									tooltip: {
										formatter: function() {
											return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
										},
									},
									plotOptions: {
										pie: {
											allowPointSelect: true,
											cursor: 'pointer',
											dataLabels: {
												enabled: false,
												format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
												style: {
													color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
													fontSize: "9px",
													distance: -30
												},
												crop: false
											},
										showInLegend: true
										}
									},
									legend: {
										enabled: true,
										itemStyle:{
											fontSize: "9px"
										}
									},
									exporting: {
										<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("energy")."_".lang("energy_consumption_source_type")."_".$fecha_actual; ?>
										filename: "<?php echo str_replace(' ', '_', $filename) ?>",
										buttons: {
											contextButton: {
												menuItems: [{
													text: "<?php echo lang('export_to_png'); ?>",
													onclick: function() {
														this.exportChart();
													},
													separator: false
												}]
											}
										}
									},
									colors : ['#FFC000', '#808080'],
									series: [{
										name: 'Porcentaje',
										colorByPoint: true,
										data: [
											{
												name: '<?php echo lang("renewable"); ?>',
												y: <?php echo $valor_total_final == 0 ? 0 : ($series["renewable"] * 100) / $valor_total_final; ?>
											},
											{
												name: '<?php echo lang("not_renewable"); ?>',
												y: <?php echo $valor_total_final == 0 ? 0 : ($series["not_renewable"] * 100) / $valor_total_final; ?>
											},
										]
									}]
								});

						<?php } ?>
					
					<?php } ?>
					
					
					<!-- Subitem Consumo energia -->
					<?php if($nombre_subitem == "energy_consumption"){ ?>
						
						<?php 
							$valor_total_final = 0;
							foreach($series as $serie){
								$serie_num_val = $serie ?$serie: 0;
								if($serie_num_val >= 0){
									$valor_total_final = $valor_total_final + $serie_num_val;
								}
							}
						?>
						
						<?php //if(!$series["renewable"] && !$series["not_renewable"]) { ?>
						
						var image_grafico_energy_consumption;
						<?php if(!is_numeric($series["renewable"]) && !is_numeric($series["not_renewable"])) { ?>
						
							$('#grafico_energy_consumption').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_energy_consumption').highcharts({
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
									events: {
										load: function(event){
											
										}
									} 
								},
								title: {
									text: ''
								},
								credits: {
									enabled: false
								},
								exporting:{
									enabled: true,
									
									/*
									yAxis: {
										min: 0,
										title: '%',	
										labels: {
											formatter: function(){
												return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
											},
										}
									},
									
									chartOptions: {
										series: [
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
									   
									   ],
									},
									*/
									
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("energy")."_".lang("energy_consumption")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									//min: 0,
									categories: [
									<?php foreach($rango_fechas as $anio => $meses) { ?>
										<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
											'<?php echo $nombre_mes . "-" . $anio; ?>',
										<?php } ?>
									<?php } ?>
									],
									//crosshair: true
								},
								yAxis: {
									min: 0,
									title: {
										text: '%'
									},
									labels: {
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}",*/
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								tooltip: {
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%)</td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								plotOptions: {
									column: {
										stacking: 'percent',
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								subtitle: {
									text: ''
								},
								colors : ['#FFC000', '#808080'],
								series : [
									<?php foreach($array_valores_energy_consumption as $serie => $valores) { ?>
										{
											name: '<?php echo lang($serie); ?>',
											data: [
												<?php foreach($valores as $anio => $meses){ ?>													
													<?php foreach($meses as $mes => $valor){ ?>
														<?php echo $valor ?$valor:0; ?>,
													<?php } ?>
												<?php } ?>
												],
										},
									<?php } ?>
								]
							});

						<?php } ?>
						
					<?php } ?>
					
					
				
				<?php } ?>
				
			<?php } ?>
			
			
			
			
			<!-- Item Agua -->
        	<?php if($item == "water"){ ?>
			
				<?php foreach($subitems as $nombre_subitem => $series){ ?>

					<!-- Subitem Consumo de agua por procedencia -->
					<?php if($nombre_subitem == "water_consumption_by_origin"){ ?>

						<?php //if(!$series["chart_bars"]["drinking_water"] && !$series["chart_bars"]["natural_source"] && !$series["chart_bars"]["reused_water"]) { ?>
						
						var image_grafico_water_consumption_by_origin;
						<?php if(!is_numeric($series["chart_bars"]["drinking_water"]) && !is_numeric($series["chart_bars"]["natural_source"]) && !is_numeric($series["chart_bars"]["reused_water"])) { ?>
						
							$('#grafico_water_consumption_by_origin').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_water_consumption_by_origin').highcharts({
								
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								exporting:{
									enabled: true,
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("water")."_".lang("water_consumption_by_origin")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									type: 'category',
								},
								yAxis: {
									min: 0,
									title: {
										text: '<?php echo lang("water_consumed")." (".$unidad_volumen_config.")"; ?>',
									},
									labels: {
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}",*/
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									enabled: false,
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								credits: {
									enabled: false
								},
								plotOptions: {
									column: {
										//stacking: 'percent',
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								tooltip: {
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									//shared: true
								},
								colors : ['#006699', '#009999', '#66CCFF'],
								series: [{
									name: '<?php echo lang("water_consumed")." (".$unidad_volumen_config.")"; ?>',
									data: [
										['<?php echo lang("drinking_water")?>', <?php echo (float)$series["chart_bars"]["drinking_water"] ?: 0; ?>],
										['<?php echo lang("natural_source")?>', <?php echo (float)$series["chart_bars"]["natural_source"] ?: 0; ?>],
										['<?php echo lang("reused_water")?>', <?php echo (float)$series["chart_bars"]["reused_water"] ?: 0; ?>],
									],
								}]

							});
							
						<?php } ?>		
						
						
						<?php //if(!$series["chart_bars_stacked_percentage"]["drinking_water"] && !$series["chart_bars_stacked_percentage"]["natural_source"] && !$series["chart_bars_stacked_percentage"]["reused_water"]) { ?>
						
						var image_grafico_water_consumption_by_origin_2;
						<?php if(!is_numeric($series["chart_bars_stacked_percentage"]["drinking_water"]) && !is_numeric($series["chart_bars_stacked_percentage"]["natural_source"]) && !is_numeric($series["chart_bars_stacked_percentage"]["reused_water"])) { ?>
						
							$('#grafico_water_consumption_by_origin_2').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
								$('#grafico_water_consumption_by_origin_2').highcharts({
									chart: {
										zoomType: 'x',
										reflow: true,
										vresetZoomButton: {
											position: {
												align: 'left',
												x: 0
											}
										},
										type: 'column',
										events: {
											load: function(event){
												
											}
										} 
									},
									title: {
										text: ''
									},
									credits: {
										enabled: false
									},
									exporting:{
										enabled: true,
										
										/*
										yAxis: {
											min: 0,
											title: '%',	
											labels: {
												formatter: function(){
													return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
												},
											}
										},
										
										chartOptions: {
											series: [
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
										   
										   ],
										},
										*/
										
										<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("water")."_".lang("water_consumption_by_origin")."_".$fecha_actual; ?>
										filename: "<?php echo str_replace(' ', '_', $filename) ?>",
										buttons: {
											contextButton: {
												menuItems: [{
													text: '<?php echo lang("export_to_png"); ?>',
													onclick: function(){
														this.exportChart()
													},
													separator: false
												}]
											}
										}
									},
									xAxis: {
										min: 0,
										categories: [
										<?php foreach($rango_fechas as $anio => $meses) { ?>
											<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
												'<?php echo $nombre_mes . "-" . $anio; ?>',
											<?php } ?>
										<?php } ?>
										],
										crosshair: true
									},
									yAxis: {
										min: 0,
										title: {
											text: '%'
										},
										labels: {
											format: "{value:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
											},
											format: "{value:." + decimal_numbers + "f}",*/
										},
										stackLabels: {
											enabled: true,
											format: "{total:,." + decimal_numbers + "f}",
											//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
											//format: "{total:." + decimal_numbers + "f}",
										}
									},
									legend: {
										align: 'center',
										verticalAlign: 'bottom',
										backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
										shadow: false
									},
									tooltip: {
										//pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
										headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
										pointFormatter: function(){
											return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%)</td></tr>';
										},
										footerFormat: '</table>',
										useHTML: true,
										shared: true
									},
									plotOptions: {
										column: {
											stacking: 'percent',
											pointPadding: 0.2,
											borderWidth: 0,
											dataLabels: {
												enabled: true,
												color: '#000000',
												align: 'center',
												format: "{y:,." + decimal_numbers + "f}",
												/*formatter: function(){
													return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
												},*/
												style: {
													fontSize: '10px',
													fontFamily: 'Segoe ui, sans-serif'
												}
											}
										}
									},
									subtitle: {
										text: ''
									},
									colors : ['#006699', '#009999', '#66CCFF'],
									series : [
										<?php foreach($array_water_consumption_by_origin as $serie => $valores) { ?>
											{
												name: '<?php echo lang($serie); ?>',
												data: [
													<?php foreach($valores as $anio => $meses){ ?>													
														<?php foreach($meses as $mes => $valor){ ?>
															<?php echo $valor ?$valor:0; ?>,
														<?php } ?>
													<?php } ?>
													],
											},
										<?php } ?>
									]
								});

						<?php } ?>						

					<?php } ?>
					

					<!-- Subitem Consumo de agua por procedencia -->
					<?php if($nombre_subitem == "water_reused_by_type"){ ?>

						<?php //if(!$series["chart_bars_percentage"]["treated_water"] && !$series["chart_bars_percentage"]["rainwater_collector"]) { ?>
						
						var image_grafico_water_reused_by_type;
						<?php if(!is_numeric($series["chart_bars_percentage"]["treated_water"]) && !is_numeric($series["chart_bars_percentage"]["rainwater_collector"])) { ?>
						
							$('#grafico_water_reused_by_type').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_water_reused_by_type').highcharts({
								
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								exporting:{
									enabled: true,
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("water")."_".lang("water_reused_by_type")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									type: 'category',
								},
								yAxis: {
									<?php $total = (float)$series["chart_bars_percentage"]["treated_water"] + (float)$series["chart_bars_percentage"]["rainwater_collector"]; ?>
									min: 0,
									title: {
										text: '<?php echo lang("water_consumed")." (".$unidad_volumen_config.")"; ?>',
										//text: '%',
									},
									labels: {
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter:function() {
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}",*/
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									enabled: false,
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								credits: {
									enabled: false
								},
								plotOptions: {
									<?php $total = (float)$series["chart_bars_percentage"]["treated_water"] + (float)$series["chart_bars_percentage"]["rainwater_collector"]; ?>
									
									column: {
										//stacking: 'percent',
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									},
									
								},
								
								tooltip: {
									//pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> </td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								colors : ['#000099', '#0066FF'],
								series: [{
									name: '<?php echo lang("water_consumed")." (".$unidad_volumen_config.")"; ?>',
									data: [
										['<?php echo lang("treated_water"); ?>', <?php echo (float)$series["chart_bars_percentage"]["treated_water"] ?: 0; ?>],
										['<?php echo lang("rainwater_collector"); ?>', <?php echo (float)$series["chart_bars_percentage"]["rainwater_collector"] ?: 0; ?>],
									]
								}]

							});

						<?php } ?>		
						

						<?php //if(!$series["chart_columns_percentage"]["treated_water"] && !$series["chart_columns_percentage"]["rainwater_collector"]) { ?>
						
						var image_grafico_water_reused_by_type_2;
						<?php if(!is_numeric($series["chart_columns_percentage"]["treated_water"]) && !is_numeric($series["chart_columns_percentage"]["rainwater_collector"])) { ?>
						
							$('#grafico_water_reused_by_type_2').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
								$('#grafico_water_reused_by_type_2').highcharts({
									chart: {
										zoomType: 'x',
										reflow: true,
										vresetZoomButton: {
											position: {
												align: 'left',
												x: 0
											}
										},
										type: 'column',
										events: {
											load: function(event){
												
											}
										} 
									},
									title: {
										text: ''
									},
									credits: {
										enabled: false
									},
									exporting:{
										enabled: true,
										
										/*
										yAxis: {
											min: 0,
											title: '%',	
											labels:{ 
												formatter: function(){
													return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
												} 
											},
										},
										
										chartOptions: {
											series: [
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
											{
												dataLabels: {
													style: {
														fontSize: "6px",
														fontWeight: "normal"
													}
												}
										   	},
										   
										   ],
										},
										*/
										
										<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("water")."_".lang("water_reused_by_type")."_".$fecha_actual; ?>
										filename: "<?php echo str_replace(' ', '_', $filename) ?>",
										buttons: {
											contextButton: {
												menuItems: [{
													text: '<?php echo lang("export_to_png"); ?>',
													onclick: function(){
														this.exportChart()
													},
													separator: false
												}]
											}
										}
									},
									xAxis: {
										min: 0,
										categories: [
										<?php foreach($rango_fechas as $anio => $meses) { ?>
											<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
												'<?php echo $nombre_mes . "-" . $anio; ?>',
											<?php } ?>
										<?php } ?>
										],
										crosshair: true
									},
									yAxis: {
										min: 0,
										title: {
											text: '%'
										},
										labels:{ 
											format: "{value:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
											},
											format: "{value:." + decimal_numbers + "f}", */
										},
										stackLabels: {
											enabled: true,
											format: "{total:,." + decimal_numbers + "f}",
											//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
											//format: "{total:." + decimal_numbers + "f}",
										}
									},
									legend: {
										align: 'center',
										verticalAlign: 'bottom',
										backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
										shadow: false
									},
									tooltip: {
										//pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
										headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
										pointFormatter: function(){
											return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%)</td></tr>';
										},
										footerFormat: '</table>',
										useHTML: true,
										shared: true
									},
									plotOptions: {
										column: {
											stacking: 'percent',
											pointPadding: 0.2,
											borderWidth: 0,
											dataLabels: {
												enabled: true,
												color: '#000000',
												align: 'center',
												format: "{total:,." + decimal_numbers + "f}",
												/*formatter: function(){
													return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
												},*/
												style: {
													fontSize: '10px',
													fontFamily: 'Segoe ui, sans-serif'
												}
											}
										}
									},
									subtitle: {
										text: ''
									},
									colors : ['#000099', '#0066FF'],
									series : [
										<?php foreach($array_water_reused_by_type as $serie => $valores) { ?>
											{
												name: '<?php echo lang($serie); ?>',
												data: [
													<?php foreach($valores as $anio => $meses){ ?>													
														<?php foreach($meses as $mes => $valor){ ?>
															<?php echo $valor ?$valor: 0; ?>,
														<?php } ?>
													<?php } ?>
													],
											},
										<?php } ?>
									]
								});

						<?php } ?>						
						
					<?php } ?>
					
				<?php } ?>
				
			<?php } ?>
			
			
			<?php if($item == "social"){ ?>
        	
				<?php foreach($subitems as $nombre_subitem => $series){ ?>

                    <!-- Subitem Proporción de gastos dedicada a proveedores locales -->
                    <?php if($nombre_subitem == "proportion_expenses_dedicated_local_suppliers"){ ?>
                        
                        <?php 
                            $valor_total_final = 0;
							foreach($series as $serie){
								$serie_num_val = $serie ?$serie: 0;
								if($serie_num_val >= 0){
									$valor_total_final = $valor_total_final + $serie_num_val;
								}
							}
                        ?>

						<?php //if(!$series["expenditure_local_suppliers"] && !$series["other_expenses"]) { ?>
						
						var image_grafico_proportion_expenses_dedicated_local_suppliers;
						<?php if(!is_numeric($series["expenditure_local_suppliers"]) && !is_numeric($series["other_expenses"])) { ?>
						
							$('#grafico_proportion_expenses_dedicated_local_suppliers').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
							
						<?php } else { ?>
						
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts({
									chart: {
										plotBackgroundColor: null,
										plotBorderWidth: null,
										plotShadow: false,
										type: 'pie',
										events: {
										   load: function() {
											   if (this.options.chart.forExport) {
												   Highcharts.each(this.series, function (series) {
													   series.update({
														   dataLabels: {
															   enabled: true,
															}
														}, false);
													});
													this.redraw();
												}
											}
										}
									},
									title: {
										text: '',
									},
									credits: {
										enabled: false
									},
									tooltip: {
										formatter: function() {
											return '<b>'+ this.point.name +'</b>: '+ numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator) +' %';
										},
									},
									plotOptions: {
										pie: {
											allowPointSelect: true,
											cursor: 'pointer',
											dataLabels: {
												enabled: false,
												//formatter: function(){
												//	return numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%';
												//},
												format: '<b>{point.name}</b>: {point.percentage:.' + decimal_numbers + 'f} %',
												style: {
													color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
													fontSize: "9px",
													distance: -30
												},
												crop: false
											},
											showInLegend: true
										}
									},
									legend: {
										enabled: true,
										itemStyle:{
											fontSize: "9px"
										}
									},
									exporting: {
										<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("social")."_".lang("proportion_expenses_dedicated_local_suppliers")."_".$fecha_actual; ?>
										filename: "<?php echo str_replace(' ', '_', $filename) ?>",
										buttons: {
											contextButton: {
												menuItems: [{
													text: "<?php echo lang('export_to_png'); ?>",
													onclick: function() {
														this.exportChart();
													},
													separator: false
												}]
											}
										}
									},
									colors : ['#990000', '#CC0000'],
									series: [{
										name: 'Porcentaje',
										colorByPoint: true,
										data: [
											{
												name: '<?php echo lang("expenditure_local_suppliers"); ?>',
												y: <?php echo $valor_total_final == 0 ? 0 : ($series["expenditure_local_suppliers"] * 100) / $valor_total_final; ?>
											},
											{
												name: '<?php echo lang("other_expenses"); ?>',
												y: <?php echo $valor_total_final == 0 ? 0 : ($series["other_expenses"] * 100) / $valor_total_final; ?>
											},
										]
									}]
								});
								
								// Sección Social 

						<?php } ?>						
						
					<?php } ?>
					

					<!-- Subitem Gasto en proveedores locales -->
					<?php if($nombre_subitem == "expenditure_local_suppliers"){ ?>

						<?php //if(!$series["expenditure_local_suppliers"] && !$series["other_expenses"]) { ?>
						
						var image_grafico_expenditure_local_suppliers;
						<?php if(!is_numeric($series["expenditure_local_suppliers"]) && !is_numeric($series["other_expenses"])) { ?>
						
							$('#grafico_expenditure_local_suppliers').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_expenditure_local_suppliers').highcharts({
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
									events: {
										load: function(event){
											
										}
									} 
								},
								title: {
									text: ''
								},
								credits: {
									enabled: false
								},
								exporting:{
									enabled: true,
									
									/*
									yAxis: {
										min: 0,
										title: '%',	
										labels:{ 
											formatter: function(){
												return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
											} 
										},
									},
									
									chartOptions: {
										series: [
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
									   
									   ],
									},
									*/
									
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("social")."_".lang("expenditure_local_suppliers")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									min: 0,
									categories: [
									<?php foreach($rango_fechas as $anio => $meses) { ?>
										<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
											'<?php echo $nombre_mes . "-" . $anio; ?>',
										<?php } ?>
									<?php } ?>
									],
									crosshair: true
								},
								yAxis: {
									min: 0,
									title: {
										text: '%'
									},
									labels:{
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}", */
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								tooltip: {
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+'%)</td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								plotOptions: {
									column: {
										stacking: 'percent',
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								subtitle: {
									text: ''
								},
								colors : ['#990000', '#CC0000'],
								series : [
									<?php foreach($array_expenditure_local_suppliers as $serie => $valores) { ?>
										{
											name: '<?php echo lang($serie); ?>',
											data: [
												<?php foreach($valores as $anio => $meses){ ?>													
													<?php foreach($meses as $mes => $valor){ ?>
														<?php echo $valor ?$valor: 0; ?>,
													<?php } ?>
												<?php } ?>
												],
										},
									<?php } ?>
								]
							});

						<?php } ?>
						
					<?php } ?>

					
					<!-- Subitem Soluciones, acciones e instalaciones -->
					<?php if($nombre_subitem == "solutions_actions_facilities"){ ?>

						<?php //if(!$series["solutions_donated_to_community"] && !$series["sustainable_actions_on_site"] && !$series["facilities_for_workers"]) { ?>
						
						var image_grafico_solutions_actions_facilities;
						<?php if(!is_numeric($series["solutions_donated_to_community"]) && !is_numeric($series["sustainable_actions_on_site"]) && !is_numeric($series["facilities_for_workers"])) { ?>
						
							$('#grafico_solutions_actions_facilities').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_solutions_actions_facilities').highcharts({
								chart: {
									zoomType: 'x',
									reflow: true,
									vresetZoomButton: {
										position: {
											align: 'left',
											x: 0
										}
									},
									type: 'column',
									events: {
										load: function(event){
											
										}
									} 
								},
								title: {
									text: ''
								},
								credits: {
									enabled: false
								},
								exporting:{
									enabled: true,
									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("social")."_".lang("solutions_actions_facilities")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								xAxis: {
									min: 0,
									type: 'category',
									crosshair: true
								},
								yAxis: {
									min: 0,
									title: {
										text: 'N°',
									},
									labels:{
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}", */
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								},
								legend: {
									enabled: false,
									align: 'center',
									verticalAlign: 'bottom',
									backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
									shadow: false
								},
								plotOptions: {
									column: {
										//stacking: 'percent',
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								tooltip: {
									//pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								subtitle: {
									text: ''
								},
								colors : ['#FF7C80', '#660033', '#CC0066'],
								series: [{
									name: 'N°',
									data: [
										['<?php echo lang("solutions_donated_to_community");?>', <?php echo (float)$series["solutions_donated_to_community"] ?: 0; ?>],
										['<?php echo lang("sustainable_actions_on_site");?>', <?php echo (float)$series["sustainable_actions_on_site"] ?: 0; ?>],
										['<?php echo lang("facilities_for_workers");?>', <?php echo (float)$series["facilities_for_workers"] ?: 0; ?>],
									],
								}]
							});
	
						<?php } ?>
						
					<?php } ?>					

					
					<!-- Subitem Soluciones donadas y beneficiarios -->
					<?php if($nombre_subitem == "donated_solutions_beneficiaries"){ ?>

						<?php //if(!$series["solutions_donated_to_community"] && !$series["beneficiaries"]) { ?>
						
						var image_grafico_donated_solutions_beneficiaries;
						<?php if(!is_numeric($series["solutions_donated_to_community"]) && !is_numeric($series["beneficiaries"])) { ?>
						
							$('#grafico_donated_solutions_beneficiaries').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"});
								
						<?php } else { ?>
							
							$('#grafico_donated_solutions_beneficiaries').highcharts({
							
								chart: {
									zoomType: 'xy'
								},
								title: {
									text: ''
								},
								subtitle: {
									text: ''
								},
								xAxis: [{
									categories: [
									<?php foreach($rango_fechas as $anio => $meses) { ?>
										<?php foreach($meses as $numero_mes => $nombre_mes) { ?>
											'<?php echo $nombre_mes . "-" . $anio; ?>',
										<?php } ?>
									<?php } ?>
									],
									crosshair: true
								}],
								yAxis: [{ // Primary yAxis
									title: {
										text: '<?php echo lang("n_beneficiaries"); ?>',
										style: {
											color: Highcharts.getOptions().colors[1]
										}
									},
									labels: {
										style: {
											color: Highcharts.getOptions().colors[1]
										},
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}", */
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									}
								}, { // Secondary yAxis
									title: {
										text: '<?php echo lang("n_solutions"); ?>',
										style: {
											color: Highcharts.getOptions().colors[0]
										}
									},
									labels: {
										style: {
											color: Highcharts.getOptions().colors[0]
										},
										format: "{value:,." + decimal_numbers + "f}",
										/*formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
										},
										format: "{value:." + decimal_numbers + "f}", */
									},
									stackLabels: {
										enabled: true,
										format: "{total:,." + decimal_numbers + "f}",
										//formatter: function(){return numberFormat(this.total, decimal_numbers, decimals_separator, thousands_separator);},
										//format: "{total:." + decimal_numbers + "f}",
									},
									opposite: true
								}],
								tooltip: {
									headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
									pointFormatter: function(){
										return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
									},
									footerFormat: '</table>',
									useHTML: true,
									shared: true
								},
								credits: {
									enabled: false
								},
								exporting:{
									enabled: true,
									/*
									yAxis: [
										{
											min: 0,
											title: '%',	
											labels:{ 
												formatter: function(){
													return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
												} 
											},
										},
										{
											min: 0,
											title: '%',	
											labels:{ 
												formatter: function(){
													return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
												} 
											},
										}
									],
									chartOptions: {
										series: [
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
										{
											dataLabels: {
												style: {
													fontSize: "6px",
													fontWeight: "normal"
												}
											}
										},
									   
									   ],
									},
									*/

									<?php $filename = $sigla_cliente."_".$sigla_proyecto."_".lang("social")."_".lang("donated_solutions_beneficiaries")."_".$fecha_actual; ?>
									filename: "<?php echo str_replace(' ', '_', $filename) ?>",
									buttons: {
										contextButton: {
											menuItems: [{
												text: '<?php echo lang("export_to_png"); ?>',
												onclick: function(){
													this.exportChart()
												},
												separator: false
											}]
										}
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0,
										dataLabels: {
											enabled: true,
											color: '#000000',
											align: 'center',
											format: "{y:,." + decimal_numbers + "f}",
											/*formatter: function(){
												return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
											},*/
											style: {
												fontSize: '10px',
												fontFamily: 'Segoe ui, sans-serif'
											}
										}
									}
								},
								legend: {
									backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
								},
								colors : ['#FF7C80', '#990033'],
								series : [
									<?php foreach($array_donated_solutions_beneficiaries as $serie => $valores) { ?>
										<?php if($serie == "solutions_donated_to_community") { ?>
											{
												name: '<?php echo lang($serie); ?>',
												type: 'column',
												yAxis: 1,
												data: [
													<?php foreach($valores as $anio => $meses){ ?>													
														<?php foreach($meses as $mes => $valor){ ?>
															<?php echo $valor ?$valor: 0; ?>,
														<?php } ?>
													<?php } ?>
													],
											},
										<?php } ?>
										<?php if($serie == "beneficiaries") { ?>
											{
												name: '<?php echo lang($serie); ?>',
												type: 'spline',
												data: [
													<?php foreach($valores as $anio => $meses){ ?>													
														<?php foreach($meses as $mes => $valor){ ?>
															<?php echo $valor ?$valor: 0; ?>,
														<?php } ?>
													<?php } ?>
													],
											},
										<?php } ?>	
									<?php } ?>
								]
							});		
							
						<?php } ?>
					
					<?php } ?>
					

				<?php } ?>
				
			<?php } ?>
						
		 <?php } ?>
		 
		function borrar_temporal(uri){
				
			$.ajax({
				url:  '<?php echo_uri("compromises_compliance_client/borrar_temporal") ?>',
				type:  'post',
				data: {uri:uri},
				//dataType:'json',
				success: function(respuesta){
					appLoader.hide();
				}
	
			});
	
		}
		
		$("#export_pdf").on('click', function(e) {
			
			appLoader.show();

			<?php foreach($array_estructuras_graficos as $item => $subitems) { ?>
		 
				<?php if($item == "materials_and_waste"){ ?>
				
					<?php foreach($subitems as $nombre_subitem => $series){ ?>
							
							<!-- Subitem Total residuos producidos -->
							<?php if($nombre_subitem == "total_waste_produced"){ ?>
				
								var image_grafico_total_waste_produced;
								
								<?php if(!is_numeric($series["non_hazardous_industrial_waste"]) && !is_numeric($series["hazardous_industrial_waste"])) { ?>
								
								<?php } else { ?>
								
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.size = 150;
									$('#grafico_total_waste_produced').highcharts().options.legend.itemStyle.fontSize = "15px";
									$('#grafico_total_waste_produced').highcharts().options.title.style.fontSize = "23px";
									
									var chart = $('#grafico_total_waste_produced').highcharts().options.chart;
									var title = $('#grafico_total_waste_produced').highcharts().options.title;
									var series = $('#grafico_total_waste_produced').highcharts().options.series;
									var plotOptions = $('#grafico_total_waste_produced').highcharts().options.plotOptions;
									var colors = $('#grafico_total_waste_produced').highcharts().options.colors;
									var exporting = $('#grafico_total_waste_produced').highcharts().options.exporting;
									var credits = $('#grafico_total_waste_produced').highcharts().options.credits;
									var legend = $('#grafico_total_waste_produced').highcharts().options.legend;
									
									var obj = {};
									obj.options = JSON.stringify({
										"chart":chart,
										"title":title,
										"series":series,
										"plotOptions":plotOptions,
										"colors":colors,
										"exporting":exporting,
										"credits":credits,
										"legend":legend
									});
									
									obj.type = 'image/png';
									obj.width = '1600';
									obj.scale = '2';
									obj.async = true;
									
									var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
									obj.globaloptions = JSON.stringify(globalOptions);
									
									image_grafico_total_waste_produced = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
									
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
									$('#grafico_total_waste_produced').highcharts().options.plotOptions.pie.size = null;
									$('#grafico_total_waste_produced').highcharts().options.legend.itemStyle.fontSize = "9px;";
								
								<?php } ?>
								
							<?php } ?>	
								
							
							<!-- Subitem Reciclaje de residuos - totales -->
							<?php if($nombre_subitem == "waste_recycling_totals"){ ?>	
								
								var image_grafico_waste_recycling_totals;
								
								<?php if(!is_numeric($series["waste_without_recycling"]) && !is_numeric($series["rises_recycled"]) && !is_numeric($series["respel_recycled"])) { ?>								
								
								<?php } else { ?>
									
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "15px";
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.size = 150;
									$('#grafico_waste_recycling_totals').highcharts().options.legend.itemStyle.fontSize = "15px";
									$('#grafico_waste_recycling_totals').highcharts().options.title.style.fontSize = "23px";
									
									var chart = $('#grafico_waste_recycling_totals').highcharts().options.chart;
									var title = $('#grafico_waste_recycling_totals').highcharts().options.title;
									var series = $('#grafico_waste_recycling_totals').highcharts().options.series;
									var plotOptions = $('#grafico_waste_recycling_totals').highcharts().options.plotOptions;
									var colors = $('#grafico_waste_recycling_totals').highcharts().options.colors;
									var exporting = $('#grafico_waste_recycling_totals').highcharts().options.exporting;
									var credits = $('#grafico_waste_recycling_totals').highcharts().options.credits;
									var legend = $('#grafico_waste_recycling_totals').highcharts().options.legend;
									
									var obj = {};
									obj.options = JSON.stringify({
										"chart":chart,
										"title":title,
										"series":series,
										"plotOptions":plotOptions,
										"colors":colors,
										"exporting":exporting,
										"credits":credits,
										"legend":legend
									});
									
									obj.type = 'image/png';
									obj.width = '1600';
									obj.scale = '2';
									obj.async = true;
									
									var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
									obj.globaloptions = JSON.stringify(globalOptions);
									
									image_grafico_waste_recycling_totals = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
									
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
									$('#grafico_waste_recycling_totals').highcharts().options.plotOptions.pie.size = null;
									$('#grafico_waste_recycling_totals').highcharts().options.legend.itemStyle.fontSize = "9px;";
									
								<?php } ?>
								
							<?php } ?>		
							
							<!-- Subitem Reciclaje de residuos - mensuales -->
							<?php if($nombre_subitem == "waste_recycling_monthly"){ ?>
								
								var image_grafico_waste_recycling_monthly;					 
								
								<?php if(!is_numeric($series["waste_without_recycling"]) && !is_numeric($series["rises_recycled"]) && !is_numeric($series["respel_recycled"])) { ?>
																		
								<?php } else { ?>											
									
									$('#grafico_waste_recycling_monthly').highcharts().options.title.text = "<?php echo lang("waste_recycling_monthly"); ?>";
									$('#grafico_waste_recycling_monthly').highcharts().options.legend.itemStyle.fontSize = "15px";
									
									var chart = $('#grafico_waste_recycling_monthly').highcharts().options.chart;
									var title = $('#grafico_waste_recycling_monthly').highcharts().options.title;
									var series = $('#grafico_waste_recycling_monthly').highcharts().options.series;
									var plotOptions = $('#grafico_waste_recycling_monthly').highcharts().options.plotOptions;
									var colors = $('#grafico_waste_recycling_monthly').highcharts().options.colors;
									var exporting = $('#grafico_waste_recycling_monthly').highcharts().options.exporting;
									var credits = $('#grafico_waste_recycling_monthly').highcharts().options.credits;
									var legend = $('#grafico_waste_recycling_monthly').highcharts().options.legend;
									var xAxis = $('#grafico_waste_recycling_monthly').highcharts().options.xAxis;
									var yAxis = $('#grafico_waste_recycling_monthly').highcharts().options.yAxis;
									
									var obj = {};
									obj.options = JSON.stringify({
										"chart":chart,
										"title":title,
										"series":series,
										"plotOptions":plotOptions,
										"colors":colors,
										"exporting":exporting,
										"credits":credits,
										"legend":legend,
										"xAxis":xAxis,
										"yAxis":yAxis
									});
									
									obj.type = 'image/png';
									obj.width = '1600';
									obj.scale = '2';
									obj.async = true;
									
									var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
									obj.globaloptions = JSON.stringify(globalOptions);
									
									image_grafico_waste_recycling_monthly = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
									
									$('#grafico_waste_recycling_monthly').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
									$('#grafico_waste_recycling_monthly').highcharts().options.legend.itemStyle.fontSize = "12px;";			
									
							<?php } ?>
							
						<?php } ?>
						
					<?php } ?>	
				
				<?php } ?>	
				
				<!-- Item Emisiones -->
				<?php if($item == "emissions"){ ?>
				
					<?php foreach($subitems as $nombre_subitem => $series){ ?>
						
						<!-- Subitem Total residuos producidos -->
						<?php if($nombre_subitem == "total_emissions_by_source"){ ?>	

							var image_grafico_total_emissions_by_source;
							
							<?php if(!is_numeric($series["direct_emissions"]) && !is_numeric($series["indirect_emissions_energy"]) && !is_numeric($series["other_indirect_emissions"])) { ?>
	
							<?php } else { ?>
															
								$('#grafico_total_emissions_by_source').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
								$('#grafico_total_emissions_by_source').highcharts().options.title.text = "<?php echo lang("total_emissions_by_source"); ?>";
								$('#grafico_total_emissions_by_source').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
								$('#grafico_total_emissions_by_source').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
								$('#grafico_total_emissions_by_source').highcharts().options.legend.itemStyle.fontSize = "15px";
								
								var chart = $('#grafico_total_emissions_by_source').highcharts().options.chart;
								var title = $('#grafico_total_emissions_by_source').highcharts().options.title;
								var series = $('#grafico_total_emissions_by_source').highcharts().options.series;
								var plotOptions = $('#grafico_total_emissions_by_source').highcharts().options.plotOptions;
								var colors = $('#grafico_total_emissions_by_source').highcharts().options.colors;
								var exporting = $('#grafico_total_emissions_by_source').highcharts().options.exporting;
								var credits = $('#grafico_total_emissions_by_source').highcharts().options.credits;
								var legend = $('#grafico_total_emissions_by_source').highcharts().options.legend;
								var xAxis = $('#grafico_total_emissions_by_source').highcharts().options.xAxis;
								var yAxis = $('#grafico_total_emissions_by_source').highcharts().options.yAxis;
								
								var obj = {};
								obj.options = JSON.stringify({
									"chart":chart,
									"title":title,
									"series":series,
									"plotOptions":plotOptions,
									"colors":colors,
									"exporting":exporting,
									"credits":credits,
									"legend":legend,
									"xAxis":xAxis,
									"yAxis":yAxis
								});
								
								obj.type = 'image/png';
								obj.width = '1600';
								obj.scale = '2';
								obj.async = true;
								
								var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
								obj.globaloptions = JSON.stringify(globalOptions);
								
								image_grafico_total_emissions_by_source = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
								
								$('#grafico_total_emissions_by_source').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
								$('#grafico_total_emissions_by_source').highcharts().options.legend.itemStyle.fontSize = "9px;";
	
							<?php } ?>
							
						<?php } ?>	
					
					<?php } ?>
							
				<?php } ?>	


				<!-- Item Energia -->
				<?php if($item == "energy"){ ?>
				
					<?php foreach($subitems as $nombre_subitem => $series){ ?>
	
						<!-- Subitem Consumo de energía por tipo de fuente -->
						<?php if($nombre_subitem == "energy_consumption_source_type"){ ?>
							
							var image_grafico_energy_consumption_source_type;
							<?php if(!is_numeric($series["renewable"]) && !is_numeric($series["not_renewable"])) { ?>
	
							<?php } else { ?>
							
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.size = 150;
								$('#grafico_energy_consumption_source_type').highcharts().options.legend.itemStyle.fontSize = "15px";
								
								var chart = $('#grafico_energy_consumption_source_type').highcharts().options.chart;
								var title = $('#grafico_energy_consumption_source_type').highcharts().options.title;
								var series = $('#grafico_energy_consumption_source_type').highcharts().options.series;
								var plotOptions = $('#grafico_energy_consumption_source_type').highcharts().options.plotOptions;
								var colors = $('#grafico_energy_consumption_source_type').highcharts().options.colors;
								var exporting = $('#grafico_energy_consumption_source_type').highcharts().options.exporting;
								var credits = $('#grafico_energy_consumption_source_type').highcharts().options.credits;
								var legend = $('#grafico_energy_consumption_source_type').highcharts().options.legend;
								
								var obj = {};
								obj.options = JSON.stringify({
									"chart":chart,
									"title":title,
									"series":series,
									"plotOptions":plotOptions,
									"colors":colors,
									"exporting":exporting,
									"credits":credits,
									"legend":legend
								});
								
								obj.type = 'image/png';
								obj.width = '1600';
								obj.scale = '2';
								obj.async = true;
								
								var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
								obj.globaloptions = JSON.stringify(globalOptions);
								
								image_grafico_energy_consumption_source_type = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
								
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
								$('#grafico_energy_consumption_source_type').highcharts().options.plotOptions.pie.size = null;
								$('#grafico_energy_consumption_source_type').highcharts().options.legend.itemStyle.fontSize = "9px;";
	
							<?php } ?>
						
						<?php } ?>
				
					<!-- Subitem Consumo energia -->
					<?php if($nombre_subitem == "energy_consumption"){ ?>

						var image_grafico_energy_consumption;
						<?php if(!is_numeric($series["renewable"]) && !is_numeric($series["not_renewable"])) { ?>
						
								
						<?php } else { ?>
							
							$('#grafico_energy_consumption').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_energy_consumption').highcharts().options.title.text = "<?php echo lang("energy_consumption"); ?>";
							$('#grafico_energy_consumption').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_energy_consumption').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_energy_consumption').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_energy_consumption').highcharts().options.chart;
							var title = $('#grafico_energy_consumption').highcharts().options.title;
							var series = $('#grafico_energy_consumption').highcharts().options.series;
							var plotOptions = $('#grafico_energy_consumption').highcharts().options.plotOptions;
							var colors = $('#grafico_energy_consumption').highcharts().options.colors;
							var exporting = $('#grafico_energy_consumption').highcharts().options.exporting;
							var credits = $('#grafico_energy_consumption').highcharts().options.credits;
							var legend = $('#grafico_energy_consumption').highcharts().options.legend;
							var xAxis = $('#grafico_energy_consumption').highcharts().options.xAxis;
							var yAxis = $('#grafico_energy_consumption').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_energy_consumption = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_energy_consumption').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_energy_consumption').highcharts().options.legend.itemStyle.fontSize = "12px;";
							
							
						<?php } ?>
						
					<?php } ?>
					
				<?php } ?>
				
			<?php } ?>
				
				
			
			<!-- Item Agua -->
        	<?php if($item == "water"){ ?>
			
				<?php foreach($subitems as $nombre_subitem => $series){ ?>

					<!-- Subitem Consumo de agua por procedencia -->
					<?php if($nombre_subitem == "water_consumption_by_origin"){ ?>
						
						var image_grafico_water_consumption_by_origin;
						
						<?php if(!is_numeric($series["chart_bars"]["drinking_water"]) && !is_numeric($series["chart_bars"]["natural_source"]) && !is_numeric($series["chart_bars"]["reused_water"])) { ?>
						
						<?php } else { ?>
							
							$('#grafico_water_consumption_by_origin').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_water_consumption_by_origin').highcharts().options.title.text = "<?php echo lang("water_consumption_by_origin"); ?>";
							$('#grafico_water_consumption_by_origin').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_water_consumption_by_origin').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_water_consumption_by_origin').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_water_consumption_by_origin').highcharts().options.chart;
							var title = $('#grafico_water_consumption_by_origin').highcharts().options.title;
							var series = $('#grafico_water_consumption_by_origin').highcharts().options.series;
							var plotOptions = $('#grafico_water_consumption_by_origin').highcharts().options.plotOptions;
							var colors = $('#grafico_water_consumption_by_origin').highcharts().options.colors;
							var exporting = $('#grafico_water_consumption_by_origin').highcharts().options.exporting;
							var credits = $('#grafico_water_consumption_by_origin').highcharts().options.credits;
							var legend = $('#grafico_water_consumption_by_origin').highcharts().options.legend;
							var xAxis = $('#grafico_water_consumption_by_origin').highcharts().options.xAxis;
							var yAxis = $('#grafico_water_consumption_by_origin').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_water_consumption_by_origin = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_water_consumption_by_origin').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_water_consumption_by_origin').highcharts().options.legend.itemStyle.fontSize = "9px;";
							
						<?php } ?>	
						
						
						var image_grafico_water_consumption_by_origin_2;
						
						<?php if(!is_numeric($series["chart_bars_stacked_percentage"]["drinking_water"]) && !is_numeric($series["chart_bars_stacked_percentage"]["natural_source"]) && !is_numeric($series["chart_bars_stacked_percentage"]["reused_water"])) { ?>
									
						<?php } else { ?>
							
							$('#grafico_water_consumption_by_origin_2').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_water_consumption_by_origin_2').highcharts().options.title.text = "<?php echo lang("water_consumption_by_origin"); ?>";
							$('#grafico_water_consumption_by_origin_2').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_water_consumption_by_origin_2').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_water_consumption_by_origin_2').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_water_consumption_by_origin_2').highcharts().options.chart;
							var title = $('#grafico_water_consumption_by_origin_2').highcharts().options.title;
							var series = $('#grafico_water_consumption_by_origin_2').highcharts().options.series;
							var plotOptions = $('#grafico_water_consumption_by_origin_2').highcharts().options.plotOptions;
							var colors = $('#grafico_water_consumption_by_origin_2').highcharts().options.colors;
							var exporting = $('#grafico_water_consumption_by_origin_2').highcharts().options.exporting;
							var credits = $('#grafico_water_consumption_by_origin_2').highcharts().options.credits;
							var legend = $('#grafico_water_consumption_by_origin_2').highcharts().options.legend;
							var xAxis = $('#grafico_water_consumption_by_origin_2').highcharts().options.xAxis;
							var yAxis = $('#grafico_water_consumption_by_origin_2').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_water_consumption_by_origin_2 = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_water_consumption_by_origin_2').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_water_consumption_by_origin_2').highcharts().options.legend.itemStyle.fontSize = "12px;";	
	
						<?php } ?>						
	
					<?php } ?>
					
					
					<!-- Subitem Consumo de agua por procedencia -->
					<?php if($nombre_subitem == "water_reused_by_type"){ ?>
						
						var image_grafico_water_reused_by_type;
						
						<?php if(!is_numeric($series["chart_bars_percentage"]["treated_water"]) && !is_numeric($series["chart_bars_percentage"]["rainwater_collector"])) { ?>
														
						<?php } else { ?>
							
							$('#grafico_water_reused_by_type').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_water_reused_by_type').highcharts().options.title.text = "<?php echo lang("water_reused_by_type"); ?>";
							$('#grafico_water_reused_by_type').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_water_reused_by_type').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_water_reused_by_type').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_water_reused_by_type').highcharts().options.chart;
							var title = $('#grafico_water_reused_by_type').highcharts().options.title;
							var series = $('#grafico_water_reused_by_type').highcharts().options.series;
							var plotOptions = $('#grafico_water_reused_by_type').highcharts().options.plotOptions;
							var colors = $('#grafico_water_reused_by_type').highcharts().options.colors;
							var exporting = $('#grafico_water_reused_by_type').highcharts().options.exporting;
							var credits = $('#grafico_water_reused_by_type').highcharts().options.credits;
							var legend = $('#grafico_water_reused_by_type').highcharts().options.legend;
							var xAxis = $('#grafico_water_reused_by_type').highcharts().options.xAxis;
							var yAxis = $('#grafico_water_reused_by_type').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_water_reused_by_type = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_water_reused_by_type').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_water_reused_by_type').highcharts().options.legend.itemStyle.fontSize = "9px;";		
							
						<?php } ?>		
						
						
						var image_grafico_water_reused_by_type_2;
						
						<?php if(!is_numeric($series["chart_columns_percentage"]["treated_water"]) && !is_numeric($series["chart_columns_percentage"]["rainwater_collector"])) { ?>
								
						<?php } else { ?>
							
							$('#grafico_water_reused_by_type_2').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_water_reused_by_type_2').highcharts().options.title.text = "<?php echo lang("water_reused_by_type"); ?>";
							$('#grafico_water_reused_by_type_2').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_water_reused_by_type_2').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_water_reused_by_type_2').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_water_reused_by_type_2').highcharts().options.chart;
							var title = $('#grafico_water_reused_by_type_2').highcharts().options.title;
							var series = $('#grafico_water_reused_by_type_2').highcharts().options.series;
							var plotOptions = $('#grafico_water_reused_by_type_2').highcharts().options.plotOptions;
							var colors = $('#grafico_water_reused_by_type_2').highcharts().options.colors;
							var exporting = $('#grafico_water_reused_by_type_2').highcharts().options.exporting;
							var credits = $('#grafico_water_reused_by_type_2').highcharts().options.credits;
							var legend = $('#grafico_water_reused_by_type_2').highcharts().options.legend;
							var xAxis = $('#grafico_water_reused_by_type_2').highcharts().options.xAxis;
							var yAxis = $('#grafico_water_reused_by_type_2').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_water_reused_by_type_2 = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_water_reused_by_type_2').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_water_reused_by_type_2').highcharts().options.legend.itemStyle.fontSize = "12px;";	
							
						<?php } ?>	
											
					<?php } ?>
					
				<?php } ?>
						
			<?php } ?>		
						
											
			<?php if($item == "social"){ ?>
        	
				<?php foreach($subitems as $nombre_subitem => $series){ ?>

                    <!-- Subitem Proporción de gastos dedicada a proveedores locales -->
                    <?php if($nombre_subitem == "proportion_expenses_dedicated_local_suppliers"){ ?>
						
						var image_grafico_proportion_expenses_dedicated_local_suppliers;
						
						<?php if(!is_numeric($series["expenditure_local_suppliers"]) && !is_numeric($series["other_expenses"])) { ?>
						
							$('#grafico_proportion_expenses_dedicated_local_suppliers').html("<strong><?php echo lang("no_information_available") ?></strong>").css({"text-align":"center", "vertical-align":"middle", "display":"flex","align-items":"center","justify-content":"center"}); 
							
						<?php } else { ?>
													
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.size = 150;
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.chart;
							var title = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.title;
							var series = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.series;
							var plotOptions = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions;
							var colors = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.colors;
							var exporting = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.exporting;
							var credits = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.credits;
							var legend = $('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.legend;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_proportion_expenses_dedicated_local_suppliers = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.plotOptions.pie.size = null;
							$('#grafico_proportion_expenses_dedicated_local_suppliers').highcharts().options.legend.itemStyle.fontSize = "9px;";	
							
						<?php } ?>						
						
					<?php } ?>
					

					<!-- Subitem Gasto en proveedores locales -->
					<?php if($nombre_subitem == "expenditure_local_suppliers"){ ?>
						
						var image_grafico_expenditure_local_suppliers;
						
						<?php if(!is_numeric($series["expenditure_local_suppliers"]) && !is_numeric($series["other_expenses"])) { ?>
														
						<?php } else { ?>
							
							$('#grafico_expenditure_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_expenditure_local_suppliers').highcharts().options.title.text = "<?php echo lang("expenditure_local_suppliers"); ?>";
							$('#grafico_expenditure_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_expenditure_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_expenditure_local_suppliers').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_expenditure_local_suppliers').highcharts().options.chart;
							var title = $('#grafico_expenditure_local_suppliers').highcharts().options.title;
							var series = $('#grafico_expenditure_local_suppliers').highcharts().options.series;
							var plotOptions = $('#grafico_expenditure_local_suppliers').highcharts().options.plotOptions;
							var colors = $('#grafico_expenditure_local_suppliers').highcharts().options.colors;
							var exporting = $('#grafico_expenditure_local_suppliers').highcharts().options.exporting;
							var credits = $('#grafico_expenditure_local_suppliers').highcharts().options.credits;
							var legend = $('#grafico_expenditure_local_suppliers').highcharts().options.legend;
							var xAxis = $('#grafico_expenditure_local_suppliers').highcharts().options.xAxis;
							var yAxis = $('#grafico_expenditure_local_suppliers').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_expenditure_local_suppliers = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_expenditure_local_suppliers').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_expenditure_local_suppliers').highcharts().options.legend.itemStyle.fontSize = "12px;";			

						<?php } ?>
						
					<?php } ?>

					
					<!-- Subitem Soluciones, acciones e instalaciones -->
					<?php if($nombre_subitem == "solutions_actions_facilities"){ ?>
						
						var image_grafico_solutions_actions_facilities;
						
						<?php if(!is_numeric($series["solutions_donated_to_community"]) && !is_numeric($series["sustainable_actions_on_site"]) && !is_numeric($series["facilities_for_workers"])) { ?>
														
						<?php } else { ?>
							
							$('#grafico_solutions_actions_facilities').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
							$('#grafico_solutions_actions_facilities').highcharts().options.title.text = "<?php echo lang("solutions_actions_facilities"); ?>";
							$('#grafico_solutions_actions_facilities').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
							$('#grafico_solutions_actions_facilities').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
							$('#grafico_solutions_actions_facilities').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_solutions_actions_facilities').highcharts().options.chart;
							var title = $('#grafico_solutions_actions_facilities').highcharts().options.title;
							var series = $('#grafico_solutions_actions_facilities').highcharts().options.series;
							var plotOptions = $('#grafico_solutions_actions_facilities').highcharts().options.plotOptions;
							var colors = $('#grafico_solutions_actions_facilities').highcharts().options.colors;
							var exporting = $('#grafico_solutions_actions_facilities').highcharts().options.exporting;
							var credits = $('#grafico_solutions_actions_facilities').highcharts().options.credits;
							var legend = $('#grafico_solutions_actions_facilities').highcharts().options.legend;
							var xAxis = $('#grafico_solutions_actions_facilities').highcharts().options.xAxis;
							var yAxis = $('#grafico_solutions_actions_facilities').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_solutions_actions_facilities = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_solutions_actions_facilities').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
							$('#grafico_solutions_actions_facilities').highcharts().options.legend.itemStyle.fontSize = "9px;";
	
						<?php } ?>
						
					<?php } ?>					

					
					<!-- Subitem Soluciones donadas y beneficiarios -->
					<?php if($nombre_subitem == "donated_solutions_beneficiaries"){ ?>
						
						var image_grafico_donated_solutions_beneficiaries;
						
						<?php if(!is_numeric($series["solutions_donated_to_community"]) && !is_numeric($series["beneficiaries"])) { ?>
														
						<?php } else { ?>
							
							$('#grafico_donated_solutions_beneficiaries').highcharts().options.title.text = "<?php echo lang("donated_solutions_beneficiaries"); ?>";
							$('#grafico_donated_solutions_beneficiaries').highcharts().options.legend.itemStyle.fontSize = "15px";
							
							var chart = $('#grafico_donated_solutions_beneficiaries').highcharts().options.chart;
							var title = $('#grafico_donated_solutions_beneficiaries').highcharts().options.title;
							var series = $('#grafico_donated_solutions_beneficiaries').highcharts().options.series;
							var plotOptions = $('#grafico_donated_solutions_beneficiaries').highcharts().options.plotOptions;
							var colors = $('#grafico_donated_solutions_beneficiaries').highcharts().options.colors;
							var exporting = $('#grafico_donated_solutions_beneficiaries').highcharts().options.exporting;
							var credits = $('#grafico_donated_solutions_beneficiaries').highcharts().options.credits;
							var legend = $('#grafico_donated_solutions_beneficiaries').highcharts().options.legend;
							var xAxis = $('#grafico_donated_solutions_beneficiaries').highcharts().options.xAxis;
							var yAxis = $('#grafico_donated_solutions_beneficiaries').highcharts().options.yAxis;
							
							var obj = {};
							obj.options = JSON.stringify({
								"chart":chart,
								"title":title,
								"series":series,
								"plotOptions":plotOptions,
								"colors":colors,
								"exporting":exporting,
								"credits":credits,
								"legend":legend,
								"xAxis":xAxis,
								"yAxis":yAxis
							});
							
							obj.type = 'image/png';
							obj.width = '1600';
							obj.scale = '2';
							obj.async = true;
							
							var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
							obj.globaloptions = JSON.stringify(globalOptions);
							
							image_grafico_donated_solutions_beneficiaries = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
							$('#grafico_donated_solutions_beneficiaries').highcharts().options.legend.itemStyle.fontSize = "12px;";
							
						<?php } ?>
					
					<?php } ?>
					
				<?php } ?>
				
			<?php } ?>						
									
		<?php } ?>								

		var id_proyecto = $('#proyecto').val();
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val(); 
		
		$.ajax({
			url:  '<?php echo_uri("KPI_Charts_by_project/get_pdf") ?>',
			type:  'post',
			data: {
				id_proyecto:id_proyecto, 
				start_date:start_date, 
				end_date:end_date,
				image_grafico_total_waste_produced: image_grafico_total_waste_produced,
				image_grafico_waste_recycling_totals: image_grafico_waste_recycling_totals,
				image_grafico_waste_recycling_monthly: image_grafico_waste_recycling_monthly,
				image_grafico_total_emissions_by_source: image_grafico_total_emissions_by_source,
				image_grafico_energy_consumption_source_type: image_grafico_energy_consumption_source_type,
				image_grafico_energy_consumption: image_grafico_energy_consumption,
				image_grafico_water_consumption_by_origin: image_grafico_water_consumption_by_origin,
				image_grafico_water_consumption_by_origin_2: image_grafico_water_consumption_by_origin_2,
				image_grafico_water_reused_by_type: image_grafico_water_reused_by_type,
				image_grafico_water_reused_by_type_2: image_grafico_water_reused_by_type_2,
				image_grafico_proportion_expenses_dedicated_local_suppliers: image_grafico_proportion_expenses_dedicated_local_suppliers,
				image_grafico_expenditure_local_suppliers: image_grafico_expenditure_local_suppliers,
				image_grafico_solutions_actions_facilities: image_grafico_solutions_actions_facilities,
				image_grafico_donated_solutions_beneficiaries: image_grafico_donated_solutions_beneficiaries,
			},
			//dataType:'json',
			success: function(respuesta){;
									
				var uri = '<?php echo get_setting("temp_file_path") ?>' + respuesta;
				console.log(uri);
				var link = document.createElement("a");
				link.download = respuesta;
				link.href = uri;
				link.click();
				
				borrar_temporal(uri);
			}
		});
		
	});
	
	function getChartName(obj){
		var tmp = null;
		$.support.cors = true;
		$.ajax({
			async: false,
			type: 'post',
			dataType: 'text',
			url : AppHelper.highchartsExportUrl,
			data: obj,
			crossDomain:true,
			success: function (data) {
				tmp = data.replace(/files\//g,'');
				tmp = tmp.replace(/.png/g,'');
			}
		});
		return tmp;
	}
		
});

</script>