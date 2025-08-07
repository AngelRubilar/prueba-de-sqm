<div id="mapa" style="height: 450px; position: relative; outline: none;"></div>

<script type="text/javascript">

	var id_air_quality_variable = "<?php echo (count($array_qual_data_values_p) > 0) ?  $air_quality_variable->id : ""; ?>";
	var id_meteorological_variable = "<?php echo (count($array_meteo_data_values_p) > 0) ?  $meteorological_variable->id : ""; ?>";
	
	console.log("id_air_quality_variable: " + id_air_quality_variable);
	console.log("id_meteorological_variable: " + id_meteorological_variable);

	// Instanciación de Mapa Leaflet
	// Si existe una fecha mínima y máxima en los datos, setea el time interval con esas fechas. Si no, se setea un time interval por defecto.
	var time_interval = "";
	<?php if($min_date && $max_date){ ?>
		time_interval = "<?php echo $min_date.'T00:00:00Z/'.$max_date.'T23:00:00Z'; ?>";
	<?php } else { ?>
		time_interval = "2020-01-01T00:00:00Z/2020-01-03T23:00:00Z";
	<?php } ?>

	// Instanciación de Mapa Leaflet
	var map = new L.Map('mapa', {
		center: new L.LatLng(<?php echo $sector_info->latitude; ?>, <?php echo $sector_info->longitude; ?>), // Dónde se enfoca el mapa al mostrarse
		zoom: 12,
		timeDimension: true,
		timeDimensionControl: ("<?php echo $min_date; ?>" && "<?php echo $max_date; ?>") ? true : false,
		timeDimensionOptions:{
			timeInterval: time_interval,
			period: "PT1H"
		},
	});

	// Construcción de layer de Leaflet (tipo de mapa, en este caso es un mapa satelital de Google)
	var baseLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{ 
		//attribution: '...',
		minZoom: 10,
		maxZoom: 18,
		subdomains:['mt0','mt1','mt2','mt3']
	});

	baseLayer.addTo(map); // Agrega el layer al Mapa

	// Layer Heatmap
	var heatmapLayer = new HeatmapOverlay({
		radius: 0.005,
		opacity: 0.5,
		maxOpacity: 1,
		minOpacity: 0.5,
		blur: 0.85,
		scaleRadius: true,
		useLocalExtrema: false,
		latField: 'lat',
		lngField: 'lon',
		valueField: 'cont',
		gradient: {
			'0.0' : 'rgb(128,128,128)',
			'0.1' : 'rgb(79,9,207)',
			'0.2' : 'rgb(8,55,206)',
			'0.3' : 'rgb(8,184,216)',
			'0.4' : 'rgb(14,234,138)',
			'0.5' : 'rgb(20,229,9)',
			'0.6' : 'rgb(139,214,8)',
			'0.7' : 'rgb(248,193,26)',
			'0.8' : 'rgb(243,56,19)',
			'0.9' : 'rgb(219,9,103)',
			'1.0' : 'rgb(219,9,103)'
		}
	});

	// Layer Timedimension
	var timedimension = new L.TimeDimension.Layer(heatmapLayer, {
		updateTimeDimension: true,
		updateTimeDimensionMode: 'replace',
		addlastPoint: true
	});

	// Si hay variable de Calidad del aire seleccionada al ingresar al Sector
	if(id_air_quality_variable){

		// Estructura de datos para el layer de HeatMap.
		var array_qual_data_values_p = <?php echo json_encode($array_qual_data_values_p); ?>;

		// Agrega layer Heatmap al mapa
		heatmapLayer.addTo(map);
		timedimension.addTo(map);
		var flechas; // variable para el LayerGroup de flechas

		timedimension.on('timeload', function(time){

			map.removeLayer(heatmapLayer);

			var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
			var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
			var hora = date.substring(11, 16); 			// Ej: 00:00
			var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
			var array_data_qual = [];

			$.each(array_qual_data_values_p[fecha], function(key, value) {
				var array_latlon = key.split(":");
				var lat = array_latlon[0];
				var lon = array_latlon[1];
				var cont = value[time_hora];
				array_data_qual.push({lat: lat, lon: lon, cont: cont});
			});

			heatmapLayer.setData({
				min: <?php echo ($qual_min_value) ? $qual_min_value : 0; ?>,
				max: <?php echo ($qual_max_value) ? $qual_max_value : 0; ?>,
				data: array_data_qual
			})

			heatmapLayer.addTo(map);

			if(id_meteorological_variable){

				if(map.hasLayer(flechas)){
					map.removeLayer(flechas);
				}
				
				// Estructura de datos para el layer de Arrows.
				var array_meteo_data_values_p = <?php echo json_encode($array_meteo_data_values_p); ?>;
				//console.log(array_meteo_data_values_p);

				// Creación de objetos para cada flecha del layer de Leaflet Arrow
				// Idea: para la velocidad del viento, se podría sacar el máximo de los valores de la base de datos y ese sea el valor más alto del cuerpo de la flecha			

				var arrayLayers = [];
				var array_data_meteo = [];

				$.each(array_meteo_data_values_p[fecha], function(key, value) {
					var array_latlon = key.split(":");
					var lat = array_latlon[0];
					var lon = array_latlon[1];
					var cont = value[time_hora];
					array_data_meteo.push({
						latlng: L.latLng(lat, lon),
						degree: 350,
						distance: 20,
						title: "Demo"
					});
				});

				array_data_meteo.forEach(function(obj, index){

					var windlayer = new L.Arrow(obj, {
						distanceUnit: 'px', // El largo de la flecha puede representarse en px o kilómetros en el mapa
						arrowheadLength: 6,
						arrowheadClosingLine: false,
						//stretchFactor: 0.8,
						weight: 1,
						color: '#000'
					});

					arrayLayers.push(windlayer);

				});

				flechas = L.layerGroup(arrayLayers);
				map.addLayer(flechas, true);

			}

			this._update();

		});

		map.on('click', function onMapClick(e) {
			
			var value = heatmapLayer._heatmap.getValueAt({
				x: e.containerPoint.x,
				y: e.containerPoint.y
			});

			var pop = '<table style="width: 100%; font-size:17px;"><tr><td><img heigth="27" width="27" src="/assets/images/air_variables/'+'<?php echo $air_quality_variable->icono; ?>'+'"></td><td><strong>'+' &nbsp; <?php echo $air_quality_variable->name; ?>'+':</strong> '+value.toFixed(4)+'</td></tr></table>';

			var popup = L.popup({
				maxWidth: 500
			});
			popup
				.setLatLng(e.latlng)
				.setContent(pop)
				.openOn(map);
			console.log(e.latlng);
		});

	} else if(id_meteorological_variable){

		timedimension.addTo(map);
		var flechas; // variable para el LayerGroup de flechas

		timedimension.on('timeload', function(time){

			if(map.hasLayer(flechas)){
				map.removeLayer(flechas);
			}

			var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
			var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
			var hora = date.substring(11, 16); 			// Ej: 00:00
			var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
			
			// Estructura de datos para el layer de Arrows.
			var array_meteo_data_values_p = <?php echo json_encode($array_meteo_data_values_p); ?>;
			//console.log(array_meteo_data_values_p);

			// Creación de objetos para cada flecha del layer de Leaflet Arrow
			// Idea: para la velocidad del viento, se podría sacar el máximo de los valores de la base de datos y ese sea el valor más alto del cuerpo de la flecha			

			var arrayLayers = [];
			var array_data_meteo = [];

			$.each(array_meteo_data_values_p[fecha], function(key, value) {
				var array_latlon = key.split(":");
				var lat = array_latlon[0];
				var lon = array_latlon[1];
				var cont = value[time_hora];
				array_data_meteo.push({
					latlng: L.latLng(lat, lon),
					degree: 350,
					distance: 20,
					title: "Demo"
				});
			});

			array_data_meteo.forEach(function(obj, index){

				var windlayer = new L.Arrow(obj, {
					distanceUnit: 'px', // El largo de la flecha puede representarse en px o kilómetros en el mapa
					arrowheadLength: 6,
					arrowheadClosingLine: false,
					//stretchFactor: 0.8,
					weight: 1,
					color: '#000'
				});

				arrayLayers.push(windlayer);

			});

			flechas = L.layerGroup(arrayLayers);
			map.addLayer(flechas, true);

			this._update();

		});

	}


</script>