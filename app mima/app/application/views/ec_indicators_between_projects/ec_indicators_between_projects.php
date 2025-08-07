<!-- SECCIÓN ÍNDICE DE CIRCULARIDAD -->
<div class="form-group">
	
	<div class="panel panel-default mb15">
        <div class="page-title clearfix">
            <h1><?php echo lang('circularity_index'); ?></h1>
        </div>
        <div class="panel-body">

            <div class="col-md-6">
            
                <table class="table table-striped">
                	<thead>
                        <tr>
                            <th class="text-center"><?php echo lang("project"); ?></th>
                            <th class="text-center"><?php echo lang("cf"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                        <tr>
                            <td class="text-left"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                            <td class="text-right"><?php echo to_number_client_format($variables_proyecto["cf"], $id_cliente); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("circularity_index"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_circularity_index" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
        
        </div> 
        
    </div>
    
</div>
<!-- FIN SECCIÓN ÍNDICE DE CIRCULARIDAD -->

<!-- SECCIÓN INDICADORES PARCIALES -->
<div class="form-group">

	<div class="panel panel-default mb15">
        <div class="page-title clearfix">
            <h1><?php echo lang('partial_indicators'); ?></h1>
        </div>
        <div class="panel-body">

            <div class="col-md-6">
            
                <table class="table table-striped">
                	<thead>
                        <tr>
                            <th class="text-center"><?php echo lang("project"); ?></th>
                            <th class="text-center"><?php echo lang("v")."/".lang("ti"); ?></th>
                            <th class="text-center"><?php echo lang("w")."/".lang("variable_to"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                        <tr>
                            <td class="text-left"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                            <td class="text-right"><?php echo to_number_client_format($variables_proyecto["input"], $id_cliente); ?></td>
                            <td class="text-right"><?php echo to_number_client_format($variables_proyecto["output"], $id_cliente); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("partial_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_indicadores_parciales" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
        
        </div> 
        
    </div>

</div>
<!-- FIN SECCIÓN INDICADORES PARCIALES -->

<!-- SECCIÓN VARIABLES -->
<div class="form-group">

    <div class="panel panel-default mb15">
        <div class="page-title clearfix">
            <h1><?php echo lang('variables'); ?></h1>
        </div>
        <div class="panel-body">
			
            <div class="col-md-12">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("input_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_variables_input" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
            
            <div class="col-md-12">
        
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="5" class="text-left" style="vertical-align:middle;"><h4><?php echo lang("inputs"); ?></h4></th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo lang("project"); ?></th>
                            <th class="text-center"><?php echo lang("rci")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("rcu")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("res")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("v")." (".$unidad_masa_config.")"; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                            <tr>
                                <td class="text-left"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["rci"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["rui"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["res"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["v"], $id_cliente); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        
            </div>
            
        </div> 
        
    </div>

    <div class="panel panel-default mb15">
        <div class="panel-body">

           <div class="col-md-12">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("output_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_variables_output" style="height: 240px;"></div>
                   </div>
                </div>
            </div>
            
            <div class="col-md-12">
        
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="7" class="text-left" style="vertical-align:middle;"><h4><?php echo lang("outputs"); ?></h4></th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo lang("project"); ?></th>
                            <th class="text-center"><?php echo lang("rco")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("ruo")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("wrci")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("wrco")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("o")." (".$unidad_masa_config.")"; ?></th>
                            <th class="text-center"><?php echo lang("wo")." (".$unidad_masa_config.")"; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
                            <tr>
                                <td class="text-left"><?php echo $array_nombres_proyectos[$id_proyecto]; ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["rco"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["ruo"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["wrci"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["wrco"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["o"], $id_cliente); ?></td>
                                <td class="text-right"><?php echo to_number_client_format($variables_proyecto["wo"], $id_cliente); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        
            </div>
        
        </div> 
        
    </div>
    
</div>
<!-- FIN SECCIÓN VARIABLES -->	


<!-- 
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
-->

<script type="text/javascript">

	$(document).ready(function(){
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparatorClient;
		var thousands_separator = AppHelper.settings.thousandSeparatorClient;
		var decimal_numbers = AppHelper.settings.decimalNumbersClient;	
		
		// SECCIÓN ÍNDICE DE CIRCULARIDAD
		
		var image_grafico_circularity_index;		
		$("#grafico_circularity_index").highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'variablepie',
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
				text: ''
			},
			credits: {
				enabled: false
			},
			exporting: {
				chartOptions: {
					plotOptions: {
						series: {
							dataLabels: {
								enabled: true,
								format: "{value:,." + decimal_numbers + "f}", 
							}
						}
					}
				},
				filename: "<?php echo lang('circularity_index'); ?>",
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
			yAxis: {
				min: 0,
				max: 1,
				//tickInterval: 0,
				labels: {
					enabled: false,
					style: {
						fontSize:'11px'
					},
					format: "{value:,." + decimal_numbers + "f}", 
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},
					format: "{value:." + decimal_numbers + "f}", */
				}
			},
			tooltip: {
				//headerFormat: '',
				//pointFormat: 'Cf: <b>{point.z}</b><br/>'
				headerFormat: '<table>',
				pointFormatter: function(){
					return '<tr><td style="color:'+this.color+';padding:0">'+this.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.z, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
					
				},
				footerFormat: '</table>',
				//pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b>: <b>{point.z}</b><br/>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				variablepie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: "{point.z:,." + decimal_numbers + "f}", 
						/*formatter: function(){
							return (numberFormat(this.point.z, decimal_numbers, decimals_separator, thousands_separator));
						},*/
						//format: '{point.z}',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
							fontSize: "9px",
							distance: -30
						},
						crop: false,
					},
					showInLegend: true
				}
			},
			series: [{
				//minPointSize: 10,
				innerSize: '20%',
				zMin: 0,
				zMax: 1,
				data: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
				{
					name: '<?php echo $array_nombres_proyectos[$id_proyecto]; ?>',
					y: 1,
					z: <?php echo $variables_proyecto["cf"]; ?>,
				},
				<?php } ?>
				]
				
			}]
		});
		
		
		
		// FIN SECCIÓN ÍNDICE DE CIRCULARIDAD
		
		// SECCIÓN INDICADORES PARCIALES
		
		var image_grafico_indicadores_parciales;
		$("#grafico_indicadores_parciales").highcharts({
			chart: {
				type: "column"
			},
			title: {
				text: ""
			},
			credits: {
				enabled: false
			},
			exporting: {
				chartOptions: {
					plotOptions: {
						series: {
							dataLabels: {
								enabled: true,
							}
						}
					}
				},
				filename: "<?php echo lang('partial_indicators'); ?>",
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
			xAxis: {
				categories: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						"<?php echo $array_nombres_proyectos[$id_proyecto]; ?>",
					<?php } ?>
				]
			},
			yAxis: [
				{
					min: 0, 
					title:{
						text: ""
					},
					format: "{value:,." + decimal_numbers + "f}", 
					/*formatter: function(){
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
					},*/
					opposite: false,
					labels: {
						enabled: false,
						style: {
							fontSize:'11px'
						},
						format: "{value:,." + decimal_numbers + "f}",
						/*formatter: function(){
							return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator);
						},
						format: "{value:." + decimal_numbers + "f}",*/
					}
				}
			],
			legend: {
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			},
			tooltip: {
				headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br><table>",
				pointFormatter: function(){
					//console.log(this);
					return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
				},
				footerFormat:"</table>",
				useHTML: true
			},
			plotOptions: {
				column: {
					grouping: false,
					shadow: false,
					borderWidth: 0,
					pointPadding: 0.2,
					dataLabels: {
						enabled: true,
						color: "#000000",
						align: "center",
						format: "{y:,." + decimal_numbers + "f}", 
						/*formatter: function(){
							//return this.y
							return numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator);
						},*/
						style: {
							fontSize: "10px",fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			},
			series: [{
				name: '<?php echo lang("ti"); ?>',
				data: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					<?php echo $variables_proyecto["ti"]; ?>,
				<?php } ?>
				],
				pointPadding: 0.3,
				pointPlacement: -0.2,
				color: '#FF8E1A',
			}, {
				name: '<?php echo lang("v"); ?>',
				data: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					<?php echo $variables_proyecto["v"]; ?>,
				<?php } ?>
				],
				pointPadding: 0.4,
				pointPlacement: -0.2,
				color: '#2A7B9B',
			}, {
				name: '<?php echo lang("variable_to"); ?>',
				data: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					<?php echo $variables_proyecto["to"]; ?>,
				<?php } ?>
				],
				pointPadding: 0.3,
				pointPlacement: 0.2,
				color: '#C70039',
			}, {
				name: '<?php echo lang("w"); ?>',
				data: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					<?php echo $variables_proyecto["w"]; ?>,
				<?php } ?>
				],
				pointPadding: 0.4,
				pointPlacement: 0.2,
				color: '#57C785',
			}]
			
		});
		
		
		
		
		// SECCIÓN VARIABLES
		var image_grafico_variables_input;
		$('#grafico_variables_input').highcharts({
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
				filename: "<?php echo lang("variables")." - ".lang("input_indicators"); ?>",
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
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					'<?php echo $array_nombres_proyectos[$id_proyecto]; ?>',
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
					return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+' %)</td></tr>';
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
			series : [
				{
					name: '<?php echo lang("rci"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["rci"]; ?>,
					<?php } ?>
					],
					color: '#3D3D6B'
				},
				{
					name: '<?php echo lang("rcu"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["rui"]; ?>,
					<?php } ?>
					],
					color: '#57C785'
				},
				{
					name: '<?php echo lang("res"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["res"]; ?>,
					<?php } ?>
					],
					color: '#FFC300'
				},
				{
					name: '<?php echo lang("v"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["v"]; ?>,
					<?php } ?>
					],
					color: '#2A7B9B'
				},
			]
		});
		
		
		
		
		var image_grafico_variables_output;
		$('#grafico_variables_output').highcharts({
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
				filename: "<?php echo lang("variables")." - ".lang("output_indicators"); ?>",
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
			*/
			},
			xAxis: {
				min: 0,
				categories: [
				<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
					'<?php echo $array_nombres_proyectos[$id_proyecto]; ?>',
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
					format: "{value:." + decimal_numbers + "f}", 
					*/
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
					return '<tr><td style="color:'+this.series.color+';padding:0;">'+this.series.name+':</td>'+'<td style="padding:0;"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b> ('+numberFormat(this.percentage, decimal_numbers, decimals_separator, thousands_separator)+' %)</td></tr>';
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
			series : [
				{
					name: '<?php echo lang("rco"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["rco"]; ?>,
					<?php } ?>
					],
					color: '#FF5733'
				},
				{
					name: '<?php echo lang("ruo"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["ruo"]; ?>,
					<?php } ?>
					],
					color: '#FFC300'
				},
				{
					name: '<?php echo lang("wrci"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["wrci"]; ?>,
					<?php } ?>
					],
					color: '#ADD45C'
				},
				{
					name: '<?php echo lang("wrco"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["wrco"]; ?>,
					<?php } ?>
					],
					color: '#00BAAD'
				},
				{
					name: '<?php echo lang("o"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["o"]; ?>,
					<?php } ?>
					],
					color: '#3D3D6B'
				},
				{
					name: '<?php echo lang("wo"); ?>',
					data: [
					<?php foreach($array_proyectos_variables as $id_proyecto => $variables_proyecto) { ?>
						<?php echo $variables_proyecto["wo"]; ?>,
					<?php } ?>
					],
					color: '#900C3F'
				}
			]
		});
		
		
		
		// FIN SECCIÓN VARIABLES
		
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
			
			$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.dataLabels.style.fontSize = "20px";
			$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.dataLabels.style.fontWeight = "normal";
			//$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.size = 150;
			$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.dataLabels.formatter = null;
			//$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.dataLabels.format = '{point.z}';
			$('#grafico_circularity_index').highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.enabled = true;
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.style.fontSize = "15px";
			
			var chart = $('#grafico_circularity_index').highcharts().options.chart;
			var title = $('#grafico_circularity_index').highcharts().options.title;
			var series = $('#grafico_circularity_index').highcharts().options.series;
			var plotOptions = $('#grafico_circularity_index').highcharts().options.plotOptions;
			var colors = $('#grafico_circularity_index').highcharts().options.colors;
			var exporting = $('#grafico_circularity_index').highcharts().options.exporting;
			var credits = $('#grafico_circularity_index').highcharts().options.credits;
			var legend = $('#grafico_circularity_index').highcharts().options.legend;
			var yAxis = $('#grafico_circularity_index').highcharts().options.yAxis;
			
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
				"yAxis":yAxis,
			});
			
			obj.type = 'image/png';
			obj.width = '1600';
			obj.scale = '2';
			obj.async = true;
			
			var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
			obj.globaloptions = JSON.stringify(globalOptions);
	
			image_grafico_circularity_index = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
							
			$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.dataLabels.enabled = false;
			//$('#grafico_circularity_index').highcharts().options.plotOptions.variablepie.size = null;
			$('#grafico_circularity_index').highcharts().options.legend.itemStyle.fontSize = "9px;";
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.enabled = false
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.style.fontSize = "11px"
			
			
			
			
			
			
			$('#grafico_indicadores_parciales').highcharts().options.legend.itemStyle.fontSize = "15px";
			//$('#grafico_indicadores_parciales').highcharts().options.title.text = "<?php echo lang("partial_indicators"); ?>";
			$('#grafico_indicadores_parciales').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			
			var chart = $('#grafico_indicadores_parciales').highcharts().options.chart;
			var title = $('#grafico_indicadores_parciales').highcharts().options.title;
			var series = $('#grafico_indicadores_parciales').highcharts().options.series;
			var plotOptions = $('#grafico_indicadores_parciales').highcharts().options.plotOptions;
			var colors = $('#grafico_indicadores_parciales').highcharts().options.colors;
			var exporting = $('#grafico_indicadores_parciales').highcharts().options.exporting;
			var credits = $('#grafico_indicadores_parciales').highcharts().options.credits;
			var legend = $('#grafico_indicadores_parciales').highcharts().options.legend;
			var xAxis = $('#grafico_indicadores_parciales').highcharts().options.xAxis;
			var yAxis = $('#grafico_indicadores_parciales').highcharts().options.yAxis;
			
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
	
			image_grafico_indicadores_parciales = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_indicadores_parciales').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_indicadores_parciales').highcharts().options.legend.itemStyle.fontSize = "12px;";
			
			
			
			
			
			$('#grafico_variables_input').highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#grafico_variables_input').highcharts().options.title.text = "<?php echo lang("input_indicators"); ?>";
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			
			var chart = $('#grafico_variables_input').highcharts().options.chart;
			var title = $('#grafico_variables_input').highcharts().options.title;
			var series = $('#grafico_variables_input').highcharts().options.series;
			var plotOptions = $('#grafico_variables_input').highcharts().options.plotOptions;
			var colors = $('#grafico_variables_input').highcharts().options.colors;
			var exporting = $('#grafico_variables_input').highcharts().options.exporting;
			var credits = $('#grafico_variables_input').highcharts().options.credits;
			var legend = $('#grafico_variables_input').highcharts().options.legend;
			var xAxis = $('#grafico_variables_input').highcharts().options.xAxis;
			var yAxis = $('#grafico_variables_input').highcharts().options.yAxis;
			
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
	
			image_grafico_variables_input = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_variables_input').highcharts().options.legend.itemStyle.fontSize = "12px;";
			
			
			
			
			$('#grafico_variables_output').highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#grafico_variables_output').highcharts().options.title.text = "<?php echo lang("output_indicators"); ?>";
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			
			var chart = $('#grafico_variables_output').highcharts().options.chart;
			var title = $('#grafico_variables_output').highcharts().options.title;
			var series = $('#grafico_variables_output').highcharts().options.series;
			var plotOptions = $('#grafico_variables_output').highcharts().options.plotOptions;
			var colors = $('#grafico_variables_output').highcharts().options.colors;
			var exporting = $('#grafico_variables_output').highcharts().options.exporting;
			var credits = $('#grafico_variables_output').highcharts().options.credits;
			var legend = $('#grafico_variables_output').highcharts().options.legend;
			var xAxis = $('#grafico_variables_output').highcharts().options.xAxis;
			var yAxis = $('#grafico_variables_output').highcharts().options.yAxis;
			
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
	
			image_grafico_variables_output = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_variables_output').highcharts().options.legend.itemStyle.fontSize = "12px;";
		
		
		
						
			var id_pais = $('#pais').val();
			var id_fase = $('#fase').val();
			var id_tech = $('#tecnologia').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val(); 
			
			$.ajax({
				url:  '<?php echo_uri("EC_Indicators_between_projects/get_pdf") ?>',
				type:  'post',
				data: {
					id_pais: id_pais,
					id_fase: id_fase,
					id_tech: id_tech,
					start_date: start_date, 
					end_date: end_date,
					image_grafico_circularity_index: image_grafico_circularity_index,
					image_grafico_indicadores_parciales: image_grafico_indicadores_parciales,
					image_grafico_variables_input: image_grafico_variables_input,
					image_grafico_variables_output: image_grafico_variables_output
				},
				//dataType:'json',
				success: function(respuesta){
										
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