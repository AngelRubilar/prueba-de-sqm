<div id="page-content" class="p20 clearfix">

	<?php if($puede_ver != 3) { ?>

		<div class="row">
			<div class="col-md-8">
				<div id="panel_legend" class="panel panel-default p0">
					<div class="panel-body">

					<div class="col-md-1"></div>
					<?php foreach($array_legend as $legend_name => $legend_values){ ?>
						<div class="col-lg-2 col-md-2 col-xs-6 p0">
							<div class="col-lg-2 col-md-2 p0">
								<div class="mt5" style="width: 25px; height: 25px; border: 1px solid black; background-color: <?php echo $legend_values["color"]; ?>"></div>
							</div>
							<label class="col-lg-10 col-md-10" style="padding-right: 0px;">
								<div class="col-md-12 p0">
									<strong><?php echo strtoupper($legend_name); ?></strong>
								</div>
								<div class="col-md-12 p0">
									<strong><?php echo $legend_values["range"]; ?></strong>
								</div>
							</label>
						</div>
					<?php } ?>
					<div class="col-md-1"></div>

					</div>
				</div>
			</div>
			<div class="col-md-4" style="padding-left:0px;">
				<div id="panel_pmca" class="panel panel-default p0">
					<div class="panel-body" style="padding-left:10px;padding-right:10px">
						<table  class="table table-bordered table-responsive mb0">
							<tbody>
								<tr>
									<td class="text-center"><strong><?php echo strtoupper(lang("work_shift")); ?></strong></td>
									<td class="text-center"><strong>00:00 - 08:00</strong></td>
									<td class="text-center"><strong>08:00 - 16:00</strong></td>
									<td class="text-center"><strong>16:00 - 00:00</strong></td>
								</tr>
								<tr>
									<td class="text-center"><strong>PMCA</strong></td>
									<td class="text-center"><strong><?php echo $html_pmca_forecast_for_today['pmca_24_hrs_t1']; ?></strong></td>
									<td class="text-center"><strong><?php echo $html_pmca_forecast_for_today['pmca_24_hrs_t2']; ?></strong></td>
									<td class="text-center"><strong><?php echo $html_pmca_forecast_for_today['pmca_24_hrs_t3']; ?></strong></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>







		
		<!--
		<div class="panel panel-default">
			<div class="panel-body p0">
				<div class="col-md-1"></div>
				<?php foreach($array_legend as $legend_name => $legend_values){ ?>
					<div class="col-lg-2 col-md-2 col-xs-6 p0">
						<div class="col-lg-2 col-md-2">
							<div class="mt5" style="width: 25px; height: 25px; border: 1px solid black; background-color: <?php echo $legend_values["color"]; ?>"></div>
						</div>
						<label class="col-lg-10 col-md-10" style="padding-right: 0px;">
							<div class="col-md-12 p0">
								<strong><?php echo strtoupper($legend_name); ?></strong>
							</div>
							<div class="col-md-12 p0">
								<strong><?php echo $legend_values["range"]; ?></strong>
							</div>
						</label>
					</div>
				<?php } ?>
				<div class="col-md-1"></div>
			</div>
		</div>
		-->

		<div id="div_station_charts">
			<div id="div_panel_chart_e1" class="panel panel-default">
				<div class="page-title clearfix">
					<h1>Estación Santa Margarita</h1>
				</div>
				<div class="panel-body p0">
						
					<div class="col-md-12">
						<div id="chart_e1" style="margin: 0 auto;">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div id="div_panel_chart_e2" class="panel panel-default">
				<div class="page-title clearfix">
					<h1>Estación Lo Campo</h1>
				</div>
				<div class="panel-body p0">
					
					<div class="col-md-12">
						<div id="chart_e2" style="margin: 0 auto;">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	<?php } else { ?>

		<div class="row"> 
			<div class="col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body p0">
						<div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
							<div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
							<div class="progress">
								<div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>	

</div>

<script type="text/javascript">

	$('#page-container > div').removeClass('scrollable-page');

	

	$(document).ready(function(){

		// ADAPTAR ALTURA DE PANELES DE LEYENDA Y GRÁFICOS PARA PANTALLAS GRANDES
		var height_panel_legend = $("#panel_legend").height();
		var height_panel_pmca = $("#panel_pmca").height();
		var height_legend_pmca = Math.max(height_panel_legend, height_panel_pmca);
		$('#panel_legend, #panel_pmca').css('min-height', height_legend_pmca + 'px');

		var height_window = $(window).height();
		var height_topbar = $('#navbar').height();
		var height_between_panels = 20; // px
		var height_div_station_charts = height_window - height_topbar - height_legend_pmca - (height_between_panels * 2);
		
		$('#div_station_charts').css('max-height', height_div_station_charts+'px');
		$('#div_station_charts').css('overflow', 'hidden');

		var height_div_charts = (height_div_station_charts / 2) - height_between_panels;

		$('#div_panel_chart_e1, #div_panel_chart_e2').css('max-height', height_div_charts+'px');
		$('#div_panel_chart_e1, #div_panel_chart_e2').css('overflow', 'hidden');

		//var charts_height = height_div_charts  - (height_between_panels * 2);
		var height_page_title = $(".page-title").height();
		var charts_height = height_div_charts - height_page_title;


		/*

		//$('#content').height($(window).height() - $('#navbar').height());
		console.log( $("#page-content").height() - $('#navbar').height() );

		console.log("navbar height: ", $('#navbar').height());
		var div_station_charts_height = ($("#page-content").height() - $('#navbar').height());
		var div_charts_height = parseInt(div_station_charts_height / 2);

		//$('#div_station_charts').css('max-height', div_station_charts_height+'px');
		//$('#div_station_charts').css('overflow', 'hidden');

		$('#div_panel_chart_e1, #div_panel_chart_e2').css('max-height', div_charts_height+'px');
		$('#div_panel_chart_e1, #div_panel_chart_e2').css('overflow', 'hidden');

		var page_title_height = $(".page-title").height();

		console.log("div_station_charts_height: ", div_station_charts_height);
		console.log("div_charts_height: ", div_charts_height);
		console.log("page_title_height: ", page_title_height);

		var charts_height = div_charts_height - page_title_height;

		*/

		//$('#page-content').height($(window).height() - $('#navbar').height());


		//$('#div_station_charts').css('max-height', '300px');
		//$('#div_station_charts').css('overflow', 'hidden');

		// GENERAL SETTINGS
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;

		let array_days_name = [
			'<?php echo lang("sunday"); ?>', 
			'<?php echo lang("monday"); ?>', 
			'<?php echo lang("tuesday"); ?>', 
			'<?php echo lang("wednesday"); ?>', 
			'<?php echo lang("thursday"); ?>', 
			'<?php echo lang("friday"); ?>', 
			'<?php echo lang("saturday"); ?>', 
		];

		let array_days_short_name = [
			'<?php echo lang("sun"); ?>', 
			'<?php echo lang("mon"); ?>', 
			'<?php echo lang("tue"); ?>', 
			'<?php echo lang("wed"); ?>', 
			'<?php echo lang("thu"); ?>', 
			'<?php echo lang("fri"); ?>', 
			'<?php echo lang("sat"); ?>', 
		];


		Highcharts.Tooltip.prototype.hide = function(){}; // MANTENER TOOLTIPS SIEMPRE VISIBLES CUANDO EL CURSOR ESTÁ FUERA DEL FOCO DEL GRÁFICO
		var chart_categories = []; // CATEGORÍAS (USADAS PARA AMBOS MODELOS Y GRÁFICOS)


		/* GRÁFICO ESTACIÓN SANTA MARGARITA */

		// DATOS MODELO NEURONAL
		var chart_e1_data_neur_model = []; // DATOS
		var chart_e1_ranges_neur_model = []; // RANGOS
		var chart_e1_timestamp_values_p_neur_model = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

		// DATOS PRONÓSTICO 48 HORAS
		var chart_e1_neur_model_values_p = <?php echo json_encode($chart_e1_neur_model_values_p); ?>;
		var chart_e1_neur_model_ranges_p = <?php echo json_encode($chart_e1_neur_model_ranges_p); ?>;
		var chart_e1_neur_model_formatted_dates = <?php echo json_encode($chart_e1_neur_model_formatted_dates); ?>;

		// ALERTA (COLORES Y VALORES MÍNIMOS, USADOS PARA AMBOS MODELOS Y GRÁFICOS)
		var array_alerts = <?php echo json_encode($array_alerts); ?>;

		Object.keys(chart_e1_neur_model_values_p).forEach(function(date, idx, array) {
			var values_p = chart_e1_neur_model_values_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(values_p).forEach(function(time) {
				var value_p = parseFloat(values_p[time]);
				var hour = time.substring(5, 7);

				chart_e1_data_neur_model.push([chart_e1_neur_model_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
				chart_categories.push(day_short_name+" "+hour+" hrs");
				
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				chart_e1_timestamp_values_p_neur_model[timestamp] = value_p;
			});
		});

		Object.keys(chart_e1_neur_model_ranges_p).forEach(function(date, idx, array) {
			var ranges_p = chart_e1_neur_model_ranges_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(ranges_p).forEach(function(time) {
				var range_p = ranges_p[time];

				var hour = time.substring(5, 7);
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				chart_e1_ranges_neur_model[chart_e1_timestamp_values_p_neur_model[timestamp]] = range_p;
			});
		});


		// DATOS MODELO NUMÉRICO
		var chart_e1_data_num_model = []; // DATOS
		var chart_e1_ranges_num_model = []; // RANGOS
		var chart_e1_timestamp_values_p_num_model = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

		// DATOS PRONÓSTICO 48 HORAS
		var chart_e1_num_model_values_p = <?php echo json_encode($chart_e1_num_model_values_p); ?>;
		var chart_e1_num_model_ranges_p = <?php echo json_encode($chart_e1_num_model_ranges_p); ?>;
		var chart_e1_num_formatted_dates = <?php echo json_encode($chart_e1_num_formatted_dates); ?>;

		Object.keys(chart_e1_num_model_values_p).forEach(function(date, idx, array) {
			var values_p = chart_e1_num_model_values_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(values_p).forEach(function(time) {
				var value_p = parseFloat(values_p[time]);
				var hour = time.substring(5, 7);

				chart_e1_data_num_model.push([chart_e1_num_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
				
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				chart_e1_timestamp_values_p_num_model[timestamp] = value_p;
			});
		});

		Object.keys(chart_e1_num_model_ranges_p).forEach(function(date, idx, array) {
			var ranges_p = chart_e1_num_model_ranges_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(ranges_p).forEach(function(time) {
				var range_p = ranges_p[time];

				var hour = time.substring(5, 7);
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				chart_e1_ranges_num_model[chart_e1_timestamp_values_p_num_model[timestamp]] = range_p;
			});
		});


		$('#chart_e1').highcharts('StockChart', {
			chart: {
				height: charts_height,
				marginBottom: 0,
				/*events: {
					load: function() {

						var chart = this;
						var current_date = new Date();
						var current_time = current_date.getHours();
						
						var subtitle = '<span style="font-size: 20px;"><b>' + current_time+":00 hrs" + '</b></span>';
						chart.setTitle(null, { 
							text: subtitle,
							useHTML: true
						});
						
						var points = [];
						
						// SETEO DE PUNTOS A SER SELECCIONADOS
						for (var i = 0; i < chart.series.length - 1; i++) {
							//console.log(chart.series[i].data);
							points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
						}
						chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
						chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						

						setInterval(function(){ 

							var current_date = new Date();
							var current_time = current_date.getHours();

							var subtitle = '<span style="font-size: 20px;"><b>' + current_time+":00 hrs" + '</b></span>';
							chart.setTitle(null, { 
								text: subtitle,
							});

							chart.xAxis[0].removePlotLine('plot-line-1');
							var points = [];
							
							// SETEO DE PUNTOS A SER SELECCIONADOS
							for (var i = 0; i < chart.series.length - 1; i++) {
								//console.log(chart.series[i].data);
								points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
							}
							chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
							chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						}, 15000); // CADA 15 SEGUNDOS
					}
				}*/
				events: {
					load: function() {

						var chart = this;
						var points = [];
						var current_date = new Date();
						var current_time = current_date.getHours();
						// SETEO DE PUNTOS A SER SELECCIONADOS
						for (var i = 0; i < chart.series.length - 1; i++) {
							//console.log(chart.series[i].data);
							points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
						}
						chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
						chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						setInterval(function(){ 

							chart.xAxis[0].removePlotLine('plot-line-1');
							var points = [];
							var current_date = new Date();
							var current_time = current_date.getHours();
							// SETEO DE PUNTOS A SER SELECCIONADOS
							for (var i = 0; i < chart.series.length - 1; i++) {
								//console.log(chart.series[i].data);
								points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
							}
							chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
							chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						}, 15000); // CADA 15 SEGUNDOS
					}
				}
			},
			credits: {
				enabled: false
			},
			scrollbar: {
				enabled: false
			},
			rangeSelector: {
				enabled: false
			},
			tooltip: {
				//crosshairs: [true, true],
				formatter: function() {
					var texto_fecha = '<b><span style="font-size: 15px;">' + this.points[0].key; + '</span></b>';
					return [texto_fecha].concat(
						this.points ?
							this.points.map(function (point) {
								if(point.series.name == "<?php echo lang("neuronal"); ?>"){
									return  '<b><span style="font-size: 15px;">' + point.series.name + '</span></b><br>'
									+ '<span style="font-size: 15px;">' + chart_e1_ranges_neur_model[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span>';
								} else {
									return  '<b><span style="font-size: 15px;">' + point.series.name + '</span></b><br>'
									+ '<span style="font-size: 15px;">' + chart_e1_ranges_num_model[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span>';
								}
							}) : []
					);
				},
				/*style: {
					fontSize: "30px"
				},*/
				useHTML: true,
				// valueDecimals: 2,
				split: true
			},
			exporting: {
				enabled: false
			},
			xAxis: {
				//crosshair: true
				crosshair: {
					id: 'plot-line-1',
					width: 2,
					color: 'black',
					//dashStyle: 'shortdot'
				},
				//type: 'datetime',
				labels: {
					formatter: function() {
						if(this.pos < 24){
							return '<span style="color:black;font-weight:bold;">'+chart_categories[this.value]+'</span>';
						}else{
							return chart_categories[this.value];
						}
					}
				},
				plotBands: [	//Franjas de color por turno
					{from: 0, to: 8, color: '#F0F0F0'},
					{from: 8, to: 20, color: '#F7F7F7'},
					{from: 20, to: 32, color: '#F0F0F0'},
					{from: 32, to: 44, color: '#F7F7F7'},
					{from: 44, to: 56, color: '#F0F0F0'},
					{from: 56, to: 68, color: '#F7F7F7'},
					{from: 68, to: 71, color: '#F0F0F0'},
				],
				tickInterval: 1,
			},
			yAxis: [{
				labels: {
					formatter: function () {
						//return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + "<?php echo $unit; ?>";
						//return (this.value > 0 ? '+' : '') + this.value + '%';
					}
				},
				lineColor: 'silver',
				lineWidth: 1,
				min: 0,
				max: 500,
				tickInterval: 100
			}, {
				labels: {
					formatter: function () {
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) /*+ " <?php echo $unit; ?>"*/;
						//return this.value;
					}
				},
				lineColor: 'silver',
				lineWidth: 1,
				linkedTo: 0,
				opposite: false,
				/*plotLines: [{
					value: 0,
					width: 2,
					color: 'silver'
				}],*/
				min: 0,
				max: 500,
				tickInterval: 100
			}],
			legend: {
				enabled: false,
				y: -20,
				floating: true
			},
			plotOptions: {
				series: {
					//compare: 'percent'
					//showInNavigator: true,
					connectNulls: false,
					marker: {
						enabled: true,
						radius: 4
					},
					point: {
						events: {
							mouseOver: function () {
								var chart =  this.series.chart;
								var axis = chart.xAxis[0];
								axis.removePlotLine('plot-line-1');
								axis.addPlotLine({
									id: 'plot-line-1',
									value: this.x,
									width: 2,
									color: 'black',
									zIndex: 3
								});
							}
						}
					}
				}
			},
			series: [
				{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: chart_e1_data_neur_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
					name: '<?php echo lang("neuronal"); ?>',
					zones: array_alerts
				},
				{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: chart_e1_data_num_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
					name: '<?php echo lang("numerical"); ?>',
					zones: array_alerts
				}
			]
		});



		/* GRÁFICO ESTACIÓN LO CAMPO */

		// DATOS MODELO NEURONAL
		var chart_e2_data_neur_model = []; // DATOS
		var chart_e2_ranges_neur_model = []; // RANGOS
		var chart_e2_timestamp_values_p_neur_model = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

		// DATOS PRONÓSTICO 48 HORAS
		var chart_e2_neur_model_values_p = <?php echo json_encode($chart_e2_neur_model_values_p); ?>;
		var chart_e2_neur_model_ranges_p = <?php echo json_encode($chart_e2_neur_model_ranges_p); ?>;
		var chart_e2_neur_formatted_dates = <?php echo json_encode($chart_e2_neur_formatted_dates); ?>;

		// ALERTA (COLORES Y VALORES MÍNIMOS, USADOS PARA AMBOS MODELOS Y GRÁFICOS)
		var array_alerts = <?php echo json_encode($array_alerts); ?>;

		Object.keys(chart_e2_neur_model_values_p).forEach(function(date, idx, array) {
			var values_p = chart_e2_neur_model_values_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(values_p).forEach(function(time) {
				var value_p = parseFloat(values_p[time]);
				var hour = time.substring(5, 7);

				chart_e2_data_neur_model.push([chart_e2_neur_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
				chart_categories.push(day_short_name+" "+hour+" hrs");
				
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				chart_e2_timestamp_values_p_neur_model[timestamp] = value_p;
			});
		});

		Object.keys(chart_e2_neur_model_ranges_p).forEach(function(date, idx, array) {
			var ranges_p = chart_e2_neur_model_ranges_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(ranges_p).forEach(function(time) {
				var range_p = ranges_p[time];

				var hour = time.substring(5, 7);
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				chart_e2_ranges_neur_model[chart_e2_timestamp_values_p_neur_model[timestamp]] = range_p;
			});
		});


		// DATOS MODELO NUMÉRICO
		var chart_e2_data_num_model = []; // DATOS
		var chart_e2_ranges_num_model = []; // RANGOS
		var chart_e2_timestamp_values_p_num_model = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

		// DATOS PRONÓSTICO 48 HORAS
		var chart_e2_num_model_values_p = <?php echo json_encode($chart_e2_num_model_values_p); ?>;
		var chart_e2_num_model_ranges_p = <?php echo json_encode($chart_e2_num_model_ranges_p); ?>;
		var chart_e2_num_formatted_dates = <?php echo json_encode($chart_e2_num_formatted_dates); ?>;

		Object.keys(chart_e2_num_model_values_p).forEach(function(date, idx, array) {
			var values_p = chart_e2_num_model_values_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(values_p).forEach(function(time) {
				var value_p = parseFloat(values_p[time]);
				var hour = time.substring(5, 7);

				chart_e2_data_num_model.push([chart_e2_num_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
				
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				chart_e2_timestamp_values_p_num_model[timestamp] = value_p;
			});
		});

		Object.keys(chart_e2_num_model_ranges_p).forEach(function(date, idx, array) {
			var ranges_p = chart_e2_num_model_ranges_p[date];

			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(ranges_p).forEach(function(time) {
				var range_p = ranges_p[time];

				var hour = time.substring(5, 7);
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				chart_e2_ranges_num_model[chart_e2_timestamp_values_p_num_model[timestamp]] = range_p;
			});
		});

		$('#chart_e2').highcharts('StockChart', {
			chart: {
				height: charts_height,
				marginBottom: 0,
				events: {
					load: function() {

						var chart = this;
						var points = [];
						var current_date = new Date();
						var current_time = current_date.getHours();
						// SETEO DE PUNTOS A SER SELECCIONADOS
						for (var i = 0; i < chart.series.length - 1; i++) {
							//console.log(chart.series[i].data);
							points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
						}
						chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
						chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						setInterval(function(){ 

							chart.xAxis[0].removePlotLine('plot-line-1');
							var points = [];
							var current_date = new Date();
							var current_time = current_date.getHours();
							// SETEO DE PUNTOS A SER SELECCIONADOS
							for (var i = 0; i < chart.series.length - 1; i++) {
								//console.log(chart.series[i].data);
								points.push(chart.series[i].data[current_time]); // CONTROLAR POSICIÓN INICIAL DEL CROSSHAIR Y TOOLTIPS
							}
							chart.xAxis[0].drawCrosshair(null, points[0]); // MOSTRAR EL CROSSHAIR
							chart.tooltip.refresh(points); // REFRESCANDO TOOLTIP CON NUEVOS PUNTOS

						}, 15000); // CADA 15 SEGUNDOS
					}
				}
			},
			credits: {
				enabled: false
			},
			scrollbar: {
				enabled: false
			},
			rangeSelector: {
				enabled: false
			},
			tooltip: {
				//crosshairs: [true, true],
				formatter: function() {
					var texto_fecha = '<b><span style="font-size: 15px;">' + this.points[0].key; + '</span></b>';
					return [texto_fecha].concat(
						this.points ?
							this.points.map(function (point) {
								if(point.series.name == "<?php echo lang("neuronal"); ?>"){
									return  '<b><span style="font-size: 15px;">' + point.series.name + '</span></b><br>'
									+ '<span style="font-size: 15px;">' + chart_e2_ranges_neur_model[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span>';
								} else {
									return  '<b><span style="font-size: 15px;">' + point.series.name + '</span></b><br>'
									+ '<span style="font-size: 15px;">' + chart_e2_ranges_num_model[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span>';
								}
							}) : []
					);
				},
				/*style: {
					fontSize: "30px"
				},*/
				useHTML: true,
				// valueDecimals: 2,
				split: true
			},
			exporting: {
				enabled: false
			},
			xAxis: {
				//crosshair: true
				crosshair: {
					id: 'plot-line-1',
					width: 2,
					color: 'black',
					//dashStyle: 'shortdot'
				},
				//type: 'datetime',
				labels: {
					formatter: function() {
						if(this.pos < 24){
							return '<span style="color:black;font-weight:bold;">'+chart_categories[this.value]+'</span>';
						}else{
							return chart_categories[this.value];
						}
					}
				},
				plotBands: [	//Franjas de color por turno
					{from: 0, to: 8, color: '#F0F0F0'},
					{from: 8, to: 20, color: '#F7F7F7'},
					{from: 20, to: 32, color: '#F0F0F0'},
					{from: 32, to: 44, color: '#F7F7F7'},
					{from: 44, to: 56, color: '#F0F0F0'},
					{from: 56, to: 68, color: '#F7F7F7'},
					{from: 68, to: 71, color: '#F0F0F0'},
				],
				tickInterval: 1,
			},
			yAxis: [{
				labels: {
					formatter: function () {
						//return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + "<?php echo $unit; ?>";
						//return (this.value > 0 ? '+' : '') + this.value + '%';
					}
				},
				lineColor: 'silver',
				lineWidth: 1,
				min: 0,
				max: 500,
				tickInterval: 100
			}, {
				labels: {
					formatter: function () {
						return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) /*+ " <?php echo $unit; ?>"*/;
						//return this.value;
					}
				},
				lineColor: 'silver',
				lineWidth: 1,
				linkedTo: 0,
				opposite: false,
				/*plotLines: [{
					value: 0,
					width: 2,
					color: 'silver'
				}],*/
				min: 0,
				max: 500,
				tickInterval: 100
			}],
			legend: {
				enabled: false,
				y: -20,
				floating: true
			},
			plotOptions: {
				series: {
					//compare: 'percent'
					//showInNavigator: true,
					connectNulls: false,
					marker: {
						enabled: true,
						radius: 4
					},
					point: {
						events: {
							mouseOver: function () {
								var chart =  this.series.chart;
								var axis = chart.xAxis[0];
								axis.removePlotLine('plot-line-1');
								axis.addPlotLine({
									id: 'plot-line-1',
									value: this.x,
									width: 2,
									color: 'black',
									zIndex: 3
								});
							}
						}
					}
				}
			},
			series: [
				{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: chart_e2_data_neur_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
					name: '<?php echo lang("neuronal"); ?>',
					zones: array_alerts
				},
				{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: chart_e2_data_num_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
					name: '<?php echo lang("numerical"); ?>',
					zones: array_alerts
				}
			]
		});

		// ACTUALIZAR VISTA CADA 24 HORAS
		setInterval(function(){ 
			var now = new Date();
			//console.log(now.getHours());
			if (now.getHours() == 0 && now.getMinutes() == 0) {
				window.location.reload();
			}
		}, 60000); // CADA 60 SEGUNDOS

		// OCULTAR NAVEGADOR EN AMBOS GRÁFICOS
		$(".highcharts-navigator, .highcharts-areaspline-series, .highcharts-navigator-xaxis, .highcharts-navigator-yaxis").remove();
		//$('#chart_e1, #chart_e2').highcharts().reflow();
	});
</script>