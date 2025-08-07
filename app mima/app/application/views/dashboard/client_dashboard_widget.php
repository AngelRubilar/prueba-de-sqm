<div class="col-md-12 text-center mb15">
	<label class="label label-success large">
		<?php 
			if($value_p_next_hour->id){
				echo lang("reliability")." ".$next_hour." hrs: ".lang("model")." ".$model_next_hour.", ".$porc_conf_next_hour."%"; 
			} else {
				echo lang("reliability")." ".$next_hour." hrs: ".lang("no_information_available"); 
			}
		?>
	</label>
</div>
<div class="col-md-12">
	<div id="calheatmap_<?php echo $station->id; ?>"></div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		
		// CalHeatMap
		// Configuración de variables para fecha de inicio del CalHeatmap
		var first_datetime = "<?php echo $first_datetime; ?>";
		var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
		var year = date.substring(0,4);
		var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
		var day = parseInt(date.substring(8,10));				
		var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

		var calheatmap_data = []; 
		var calheatmap_ranges = []; // Rangos
		var calheatmap_porc_conf = [];
		var calheatmap_models_p = []

		// Unidades de variable según configuración Unidades de Reporte
		var unit = <?php echo json_encode($unit); ?>;

		// Colores y rangos de Alertas CalHeatmap
		var array_alerts_colors = <?php echo json_encode($array_alerts_colors); ?>;
		var array_alerts_ranges = <?php echo json_encode($array_alerts_ranges); ?>;

		
		// Datos pronóstico 72 hrs
		var array_values_p = <?php echo json_encode($array_values_p); ?>;
		var array_ranges_p = <?php echo json_encode($array_ranges_p); ?>;
		var array_porc_conf_p = <?php echo json_encode($array_porc_conf_p); ?>;
		var array_values_models_p = <?php echo json_encode($array_values_models_p); ?>;
		

		Object.keys(array_values_p).forEach(function(date, idx, array) {
			
			var values_p = array_values_p[date];
			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(values_p).forEach(function(time) {

				var value_p = parseFloat(values_p[time]);
				// var hour = time.substring(5, 7);
				var hour = time;
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;

				if(array_alerts_ranges.includes(value_p.toString())){
					calheatmap_data[timestamp] = value_p + 1;
				} else {
					calheatmap_data[timestamp] = value_p;
				}

			});
		});

		Object.keys(array_ranges_p).forEach(function(date, idx, array) {

			var ranges_p = array_ranges_p[date];
			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(ranges_p).forEach(function(time) {

				var range_p = ranges_p[time];
				// var hour = time.substring(5, 7);
				var hour = time;
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				calheatmap_ranges[timestamp] = range_p;
			});

		});

		Object.keys(array_porc_conf_p).forEach(function(date, idx, array) {

			var porc_conf_p = array_porc_conf_p[date];
			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(porc_conf_p).forEach(function(time) {

				var porc_conf = porc_conf_p[time];
				// var hour = time.substring(5, 7);
				var hour = time;
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				calheatmap_porc_conf[timestamp] = porc_conf;
			});

		});

		Object.keys(array_values_models_p).forEach(function(date, idx, array) {

			var models_p = array_values_models_p[date];
			var datetime = new Date(date);
			var day_name = array_days_name[datetime.getUTCDay()];
			var day_short_name = array_days_short_name[datetime.getUTCDay()];

			Object.keys(models_p).forEach(function(time) {

				var model_p = models_p[time];
				// var hour = time.substring(5, 7);
				var hour = time;
				var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
				
				calheatmap_models_p[timestamp] = model_p;
			});

		});
		


		var calheatmap_<?php echo $station->id; ?> = new CalHeatMap();
		calheatmap_<?php echo $station->id; ?>.init({
			itemSelector: "#calheatmap_<?php echo $station->id; ?>",
			domain: "day",
			colLimit: ($(window).width() < 1070) ? null : 1,
			subDomain: "x_hour",
			range: 1, // CANTIDAD DE DÍAS
			cellSize: 30, // TAMAÑO DE CADA CELDA DE HORA
			displayLegend: true,
			domainGutter: 10, // DISTANCIA ENTRE DÍAS
			tooltip: true,
			// verticalOrientation: ($(window).width() < 1070) ? true : false,
			start: new Date(year, month, day, hour),
			domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
			subDomainTextFormat: "%H",
			subDomainTitleFormat: {
				empty: "<?php echo lang("out_of_forecast_range"); ?>",
				//filled: "{date}, la concentración de "+ "<?php echo $variable->sigla; ?>" +" se estima que será de {count} " + unit.nombre
				filled: "{date}"
			},
			subDomainDateFormat: function(date) {
				var d = new Date(date);
				var h = d.getHours();
				h = ("0" + h).slice(-2);

				var datetime = d.getTime()/1000; // TIMESTAMP

				if("<?php echo $variable->id; ?>"){

					return "<?php echo lang("reliability").": "; ?>" + numberFormat(calheatmap_porc_conf[datetime], decimal_numbers, decimals_separator, thousands_separator) + "%"
					+ "<br>" + "<?php echo lang("model").": "; ?>" + calheatmap_models_p[datetime];
				
				} else {
					return "<?php echo lang("no_information_available"); ?>"
				}
			
			},
			/*domainLabelFormat: function(date) {
				
				var d = new Date(date);
				//var datetime = d.getTime()/1000; // timestamp
				var y = d.getFullYear();
				var m = d.getMonth() + 1;
				var d = d.getDate();
				
				var formatted_date = d+"-"+m+"-"+y;
				return formatted_date;

			},*/
			itemName: [unit.nombre, unit.nombre],
			//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
			legend: array_alerts_ranges,
			legendTitleFormat: {
				//lower: (array_alerts_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
				lower: (array_alerts_ranges.length > 0) ? (Math.min.apply(Math, array_alerts_ranges) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
				inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
				upper: "<?php echo lang("more_than"); ?> {max} ({name})"
			},
			legendHorizontalPosition: "center",
			legendMargin: [0, 0, 0, 0],
			data: calheatmap_data,
			onComplete: function() { // https://php.developreference.com/article/19345650/cal-heatmap+-+legendColors+as+array+of+color+values%3F
				setTimeout(function(){
					/*['#ffadad','#ff9696','#ff8282','#fc6d6d','#ff5454','#f51818'].forEach(function(d,i){
						d3.selectAll("rect.r" + i).style("fill", d);
					});*/
					array_alerts_colors.forEach(function(d,i){
						i++;
						d3.selectAll("div#calheatmap_<?php echo $station->id; ?> rect.r" + i).style("fill", d);
						d3.selectAll("div#calheatmap_<?php echo $station->id; ?> > svg > svg.graph-legend > g > rect:nth-child(" + i + ")").style("fill", d);
					});
				}, 10);
			}
		});


	});
</script>
