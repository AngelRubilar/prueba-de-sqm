<!-- SECCIÓN INDICE DE CIRCULARIDAD -->
<div class="form-group">
	
    <div class="panel panel-default mb15">
        <div class="page-title clearfix">
            <h1><?php echo lang('circularity_index'); ?></h1>
        </div>
        <div class="panel-body">

            <div class="col-md-6 text-center" style="line-height: 10;">
            	<span style="font-size:20px;"><?php echo lang("cf")." = ".to_number_client_format($cf, $id_cliente); ?></span>
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
<!-- FIN SECCIÓN INDICE DE CIRCULARIDAD -->

<!-- SECCIÓN INDICADORES PARCIALES -->
<div class="form-group">
	
    <div class="panel panel-default mb15">
        <div class="page-title clearfix">
            <h1><?php echo lang('partial_indicators'); ?></h1>
        </div>
        <div class="panel-body">

            <div class="col-md-6 text-center" style="line-height: 10;">
            	<span style="font-size:20px;">
					<?php
						//echo $v." / ".$ti." = ".$input;
						echo lang("v")." / ".lang("ti")." = ".to_number_client_format($input, $id_cliente);
					?>
                </span>
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("input_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_indicadores_input" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
        
        </div> 
        
    </div>
    
    <div class="panel panel-default mb15">

        <div class="panel-body">

            <div class="col-md-6 text-center" style="line-height: 10;">
            	<span style="font-size:20px;">
					<?php 
						//echo $w." / ".$to." = ".$output;
						echo lang("w")." / ".lang("variable_to")." = ".to_number_client_format($output, $id_cliente);
					?>
                </span>
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("output_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_indicadores_output" style="height: 240px;"></div>
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

            <div class="col-md-6">
        
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center" style="vertical-align:middle;"><h4><?php echo lang("inputs"); ?></h4></th>
                    </tr>
                    <tr>
                    	<th class="text-center"><?php echo lang("variable"); ?></th>
                        <th class="text-center"><?php echo lang("total")." (".$unidad_masa_config.")"; ?></th>
                        <th class="text-center"><?php echo lang("information"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left"><?php echo lang("rci"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($rci, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("rci_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("rcu"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($rui, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("rui_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("res"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($res, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("res_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("v"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($v, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("v_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                </tbody>
            </table>
        
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("input_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_variables_input" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
        
        </div> 
        
    </div>

    <div class="panel panel-default mb15">
        <div class="panel-body">

            <div class="col-md-6">
        
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center" style="vertical-align:middle;"><h4><?php echo lang("outputs"); ?></h4></th>
                    </tr>
                    <tr>
                    	<th class="text-center"><?php echo lang("variable"); ?></th>
                        <th class="text-center"><?php echo lang("total")." (".$unidad_masa_config.")"; ?></th>
                        <th class="text-center"><?php echo lang("information"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left"><?php echo lang("rco"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($rco, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("rco_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("ruo"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($ruo, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("ruo_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("wrci"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($wrci, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("wrci_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("wrco"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($wrco, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("wrco_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("o"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($o, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("o_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                    <tr>
                        <td class="text-left"><?php echo lang("wo"); ?></td>
                        <td class="text-right"><?php echo to_number_client_format($wo, $id_cliente); ?></td>
                        <td class="text-center"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang("wo_desc"); ?>"><i class="fa fa-question-circle"></i></span></td>
                    </tr>
                </tbody>
            </table>
        
            </div>
        
            <div class="col-md-6">
                <div class="panel panel-default">
                   <div class="page-title clearfix panel-success">
                      <div class="pt10 pb10 text-center"> <?php echo lang("output_indicators"); ?> </div>
                   </div>
                   <div class="panel-body">
                      <div id="grafico_variables_output" style="height: 240px;"></div>
                   </div>
                </div>
             </div>
        
        </div> 
        
    </div>
    
</div>
<!-- FIN SECCIÓN VARIABLES -->
<script type="text/javascript">

	$(document).ready(function(){
		
		//General Settings
		var decimals_separator = AppHelper.settings.decimalSeparatorClient;
		var thousands_separator = AppHelper.settings.thousandSeparatorClient;
		var decimal_numbers = AppHelper.settings.decimalNumbersClient;	
		
		$('[data-toggle="tooltip"]').tooltip();
		
		var image_grafico_circularity_index;
		
		$("#grafico_circularity_index").highcharts({
			
			chart: {
				polar: true
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
							}
						}
					}
				},
				filename: "<?php echo lang('circularity_index'); ?>",
				buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang("export_to_png"); ?>",
							onclick: function() {
								this.exportChart();
							},
							separator: false
						}]
					}
				}
			},	
			pane: {
				startAngle: 0,
				endAngle: 360
			},
			xAxis: {
				tickInterval: 360,
				min: 0,
				max: 360,
				labels: {
					enabled: false
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
				headerFormat: '<table>',
				pointFormatter: function(){
					return '<tr><td style="color:'+this.series.color+';padding:0">'+this.series.name+': </td>'+'<td style="padding:0"><b>'+(numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator))+'</b></td></tr>';
				},
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0,
					groupPadding: 0,
					dataLabels: {
						enabled: true,
						format: "{y:,." + decimal_numbers + "f}", 
						/*formatter: function(){
							return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
						},*/
					}
				}
			},
			series: [{
				type: 'column',
				name: '<?php echo lang("cf"); ?>',
				data: [<?php echo $cf; ?>],
				//data: [0.6],
				pointPlacement: 'between'
			}]
			
		});
				

		// SECCIÓN INDICADORES PARCIALES
		var image_grafico_indicadores_input;
		$("#grafico_indicadores_input").highcharts({
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
				filename: "<?php echo lang("partial_indicators")." - ".lang('output_indicators'); ?>",
				buttons: {
					contextButton: {
						menuItems: [{
							text: "<?php echo lang("export_to_png"); ?>",
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
					"<?php echo lang("inputs"); ?>"
				]
			},
			yAxis: {
				min: 0, 
				title:{
					text: ""
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
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			},
			tooltip: {
				headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
				pointFormatter: function(){
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
							return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
						},*/
						style: {
							fontSize: "10px",fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			},
			series: [
				{
					name: "<?php echo lang('ti'); ?>",
					data: [
						<?php echo $ti; ?>
					],
					pointPadding: 0.3,
					pointPlacement: "center",
					color: '#FF8E1A'
				}, {
					name: "<?php echo lang('v'); ?>",
					data: [
						<?php echo $v; ?>
					],
					pointPadding: 0.4,
					pointPlacement: 0,
					color: '#2A7B9B'
				}
			]
		});
		
		
		
		
		var image_grafico_indicadores_output;
		$("#grafico_indicadores_output").highcharts({
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
				filename: "<?php echo lang("partial_indicators")." - ".lang('output_indicators'); ?>",
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
					"<?php echo lang("outputs"); ?>"
				]
			},
			yAxis: {
				min: 0, 
				title:{
					text: ""
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
				align: "center",
				verticalAlign: "bottom",
				backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "white",
				shadow: false
			},
			tooltip: {
				headerFormat: "<span style=\'font-size:10px\'>{point.key}</span><br>",
				pointFormatter: function(){
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
							return (numberFormat(this.y, decimal_numbers, decimals_separator, thousands_separator));
						},*/
						style: {
							fontSize: "10px",fontFamily: "Segoe ui, sans-serif"
						}
					}
				}
			},
			series: [
				{
					name: "<?php echo lang('variable_to'); ?>",
					data: [
						<?php echo $to; ?>
					],
					pointPadding: 0.3,
					pointPlacement: "center",
					color: '#C70039'
				}, {
					name: "<?php echo lang('w'); ?>",
					data: [
						<?php echo $w; ?>
					],
					pointPadding: 0.4,
					pointPlacement: 0,
					color: '#57C785'
				}
			]
		});
		
		
		
		// FIN SECCIÓN INDICADORES PARCIALES
		
		// SECCIÓN VARIABLES
		var image_grafico_variables_input;
		$('#grafico_variables_input').highcharts({
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
				filename: "<?php echo lang("variables")." - ".lang("input_indicators"); ?>",
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
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
					{
						name: '<?php echo lang("rci"); ?>',
						y: <?php echo $rci; ?>,
						color: '#3D3D6B'
					},
					{
						name: '<?php echo lang("rcu"); ?>',
						y: <?php echo $rui; ?>,
						color: '#57C785'
					},
					{
						name: '<?php echo lang("res"); ?>',
						y: <?php echo $res; ?>,
						color: '#FFC300'
					},
					{
						name: '<?php echo lang("v"); ?>',
						y: <?php echo $v; ?>,
						color: '#2A7B9B'
					},
				]
			}]
		});
		
		
		
		var grafico_variables_output;			
		$('#grafico_variables_output').highcharts({
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
				filename: "<?php echo lang("variables")." - ".lang("output_indicators"); ?>",
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
			series: [{
				name: 'Porcentaje',
				colorByPoint: true,
				data: [
					{
						name: '<?php echo lang("rco"); ?>',
						y: <?php echo $rco; ?>,
						color: '#FF5733'
					},
					{
						name: '<?php echo lang("ruo"); ?>',
						y: <?php echo $ruo; ?>,
						color: '#FFC300'
					},
					{
						name: '<?php echo lang("wrci"); ?>',
						y: <?php echo $wrci; ?>,
						color: '#ADD45C'
					},
					{
						name: '<?php echo lang("wrco"); ?>',
						y: <?php echo $wrco; ?>,
						color: '#00BAAD'
					},
					{
						name: '<?php echo lang("o"); ?>',
						y: <?php echo $o; ?>,
						color: '#3D3D6B'
					},
					{
						name: '<?php echo lang("wo"); ?>',
						y: <?php echo $wo; ?>,
						color: '#900C3F'
					},
				]
			}]
		});
		
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
			
			$('#grafico_circularity_index').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
			$('#grafico_circularity_index').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#grafico_circularity_index').highcharts().options.plotOptions.pie.size = 150;
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
				"yAxis":yAxis
			});
			
			obj.type = 'image/png';
			obj.width = '1600';
			obj.scale = '2';
			obj.async = true;
			
			var globalOptions = {lang:{numericSymbols: null, thousandsSep: thousands_separator, decimalPoint: decimals_separator}};
			obj.globaloptions = JSON.stringify(globalOptions);
	
			image_grafico_circularity_index = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_circularity_index').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_circularity_index').highcharts().options.plotOptions.pie.size = null;
			$('#grafico_circularity_index').highcharts().options.legend.itemStyle.fontSize = "9px;";
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.enabled = false;
			$('#grafico_circularity_index').highcharts().options.yAxis[0].labels.style.fontSize = "11px";
			
			
			
			
			
			$('#grafico_indicadores_input').highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#grafico_indicadores_input').highcharts().options.title.text = "<?php echo lang("input_indicators"); ?>";
			$('#grafico_indicadores_input').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			
			var chart = $('#grafico_indicadores_input').highcharts().options.chart;
			var title = $('#grafico_indicadores_input').highcharts().options.title;
			var series = $('#grafico_indicadores_input').highcharts().options.series;
			var plotOptions = $('#grafico_indicadores_input').highcharts().options.plotOptions;
			var colors = $('#grafico_indicadores_input').highcharts().options.colors;
			var exporting = $('#grafico_indicadores_input').highcharts().options.exporting;
			var credits = $('#grafico_indicadores_input').highcharts().options.credits;
			var legend = $('#grafico_indicadores_input').highcharts().options.legend;
			var xAxis = $('#grafico_indicadores_input').highcharts().options.xAxis;
			var yAxis = $('#grafico_indicadores_input').highcharts().options.yAxis;
			
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
	
			image_grafico_indicadores_input = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_indicadores_input').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_indicadores_input').highcharts().options.legend.itemStyle.fontSize = "12px;";
			
			
			
			
			
			$('#grafico_indicadores_output').highcharts().options.legend.itemStyle.fontSize = "15px";
			$('#grafico_indicadores_output').highcharts().options.title.text = "<?php echo lang("output_indicators"); ?>";
			$('#grafico_indicadores_output').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			
			var chart = $('#grafico_indicadores_output').highcharts().options.chart;
			var title = $('#grafico_indicadores_output').highcharts().options.title;
			var series = $('#grafico_indicadores_output').highcharts().options.series;
			var plotOptions = $('#grafico_indicadores_output').highcharts().options.plotOptions;
			var colors = $('#grafico_indicadores_output').highcharts().options.colors;
			var exporting = $('#grafico_indicadores_output').highcharts().options.exporting;
			var credits = $('#grafico_indicadores_output').highcharts().options.credits;
			var legend = $('#grafico_indicadores_output').highcharts().options.legend;
			var xAxis = $('#grafico_indicadores_output').highcharts().options.xAxis;
			var yAxis = $('#grafico_indicadores_output').highcharts().options.yAxis;
			
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
	
			image_grafico_indicadores_output = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_indicadores_output').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_indicadores_output').highcharts().options.legend.itemStyle.fontSize = "12px;";
			
			
			
			
			
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.size = 150;
			$('#grafico_variables_input').highcharts().options.legend.itemStyle.fontSize = "15px";
			
			var chart = $('#grafico_variables_input').highcharts().options.chart;
			var title = $('#grafico_variables_input').highcharts().options.title;
			var series = $('#grafico_variables_input').highcharts().options.series;
			var plotOptions = $('#grafico_variables_input').highcharts().options.plotOptions;
			var colors = $('#grafico_variables_input').highcharts().options.colors;
			var exporting = $('#grafico_variables_input').highcharts().options.exporting;
			var credits = $('#grafico_variables_input').highcharts().options.credits;
			var legend = $('#grafico_variables_input').highcharts().options.legend;
			
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
	
			image_grafico_variables_input = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_variables_input').highcharts().options.plotOptions.pie.size = null;
			$('#grafico_variables_input').highcharts().options.legend.itemStyle.fontSize = "9px;";
			
			
			
			
			
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.enabled = true;
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.style.fontSize = "20px";
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.style.fontWeight = "normal";
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.size = 150;
			$('#grafico_variables_output').highcharts().options.legend.itemStyle.fontSize = "15px";
			
			var chart = $('#grafico_variables_output').highcharts().options.chart;
			var title = $('#grafico_variables_output').highcharts().options.title;
			var series = $('#grafico_variables_output').highcharts().options.series;
			var plotOptions = $('#grafico_variables_output').highcharts().options.plotOptions;
			var colors = $('#grafico_variables_output').highcharts().options.colors;
			var exporting = $('#grafico_variables_output').highcharts().options.exporting;
			var credits = $('#grafico_variables_output').highcharts().options.credits;
			var legend = $('#grafico_variables_output').highcharts().options.legend;
			
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
	
			image_grafico_variables_output = AppHelper.highchartsExportUrlQuery+'/'+getChartName(obj)+'.png';
			
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.dataLabels.enabled = false;
			$('#grafico_variables_output').highcharts().options.plotOptions.pie.size = null;
			$('#grafico_variables_output').highcharts().options.legend.itemStyle.fontSize = "9px;";
			
			
			
			
			
			var id_proyecto = $('#proyecto').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val(); 
			
			$.ajax({
				url:  '<?php echo_uri("EC_Indicators_by_project/get_pdf") ?>',
				type:  'post',
				data: {
					id_proyecto:id_proyecto, 
					start_date:start_date, 
					end_date:end_date,
					image_grafico_circularity_index: image_grafico_circularity_index,
					image_grafico_indicadores_input: image_grafico_indicadores_input,
					image_grafico_indicadores_output: image_grafico_indicadores_output,
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