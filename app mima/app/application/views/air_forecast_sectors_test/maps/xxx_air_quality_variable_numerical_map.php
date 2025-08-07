<div id="mapa" style="height: 600px; position: relative; outline: none;"></div>

<script type="text/javascript">

	// Instanciación de Mapa Leaflet
	var map = new L.Map('mapa', {
		center: new L.LatLng(-32.8042361, -70.955305),
		zoom: 13,
		//layers: [baseLayer, heatmapLayer, arrowLayer],
		timeDimension: true,
		timeDimensionControl: true,
		timeDimensionOptions:{
			//timeInterval: "2020-01/2020-12",
			//period: "P1D"
			timeInterval: "2020-01-01T00:00:00Z/2020-01-03T23:00:00Z",
			period: "PT1H"
		},
	});

	// Construcción de layer de Leaflet (tipo de mapa, en este caso es un mapa satelital de Google)
	var baseLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{ 
		minZoom: 10,
		maxZoom: 18,
		subdomains:['mt0','mt1','mt2','mt3']
	});

	baseLayer.addTo(map); // Agrega el layer al Mapa

	// Estructura de datos para el layer de HeatMap. Puntos cardinales y sus valores
	var datos = {
		min: 0,
		max: 1,
		data: [
			{lat: -32.782083, lon:-70.96141, cont: 1},// Catemu
			{lat: -32.778000, lon:-70.96141, cont: 0.8},
			{lat: -32.775900, lon:-70.96141, cont: 0.6},
			{lat: -32.770000, lon:-70.96141, cont: 0.5},
			{lat: -32.765000, lon:-70.95941, cont: 0.3},
			{lat: -32.760000, lon:-70.95941, cont: 0.1},

			{lat: -32.800005, lon:-70.9, cont: 1},// Lo campo
			{lat: -32.79839, lon:-70.89769, cont: 0.8},
			{lat: -32.79694, lon:-70.89366, cont: 0.6},
			{lat: -32.79384, lon:-70.89173, cont: 0.5},
			{lat: -32.79146, lon:-70.88868, cont: 0.3},
			{lat: -32.78995, lon:-70.88525, cont: 0.1},

			{lat: -32.8269416, lon:-71.008616, cont: 1},// Romeral
			{lat: -32.82663, lon:-71.00528, cont: 0.8},
			{lat: -32.82778, lon:-71.00284, cont: 0.6},
			{lat: -32.82641, lon:-71.00133, cont: 0.5},
			{lat: -32.82338, lon:-70.99666, cont: 0.3},
			{lat: -32.81996, lon:-70.99271, cont: 0.1},
			{lat: -32.81714, lon:-70.98953, cont: 0.1},

			{lat: -32.78025, lon:-70.9393527, cont: 1},// Sta Margarita
			{lat: -32.77609, lon:-70.94009, cont: 0.8},
			{lat: -32.77248, lon:-70.93593, cont: 0.6},
			{lat: -32.77739, lon:-70.93112, cont: 0.7},
			{lat: -32.77133, lon:-70.92799, cont: 0.3},
			{lat: -32.76772, lon:-70.92507, cont: 0.1},

			{lat: -32.8042361, lon:-70.955305, cont: 1},// Estación Meteorológica
			{lat: -32.80214, lon:-70.94988, cont: 0.8},
			{lat: -32.79954, lon:-70.94507, cont: 0.6},
			{lat: -32.79997, lon:-70.93932, cont: 0.7},
			{lat: -32.79954, lon:-70.9349, cont: 0.3},
			{lat: -32.79817, lon:-70.92902, cont: 0.1},
		]
	};

	// Se definen las opciones del layer de Heatmap
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

	var timedimension = new L.TimeDimension.Layer(heatmapLayer, {
		updateTimeDimension: true,
		updateTimeDimensionMode: 'replace',
		addlastPoint: true
	});

	timedimension.addTo(map);

	timedimension.on('timeload', function(time){
		map.removeLayer(heatmapLayer);

		//var date = new Date(time.time).toISOString();
		var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
		var fecha = date.substring(0, 10);
		var hora = date.substring(11, 16);
		console.log(fecha+' '+hora);

		var hora_f = "time_" + hora.substr(0,2);
		console.log(hora_f);

		/*
		var y_min = -32.77341935497516;
		var y_max = -32.819306346751446;

		heatmapLayer.setData({ // Se setea el layer Heatmap con los datos de un tiempo determinado (time)
			max: 1,
			data: [
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.96141, cont: 1},// Catemu
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.96141, cont: 0.8},
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.96141, cont: 0.6},
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.96141, cont: 0.5},
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.95941, cont: 0.3},
				{lat: (y_min + (Math.random() * (y_max - y_min))), lon:-70.95941, cont: 0.1},
			]
		});
		*/

		var y_min = -32.77341935497516;
		var y_max = -32.819306346751446;

		
		//var map_data = [];

		<?php $map_data = array(); ?>

		<?php foreach($array_data_values_p as $index => $values_p){ ?>
			
			<?php 
				//var_dump($values_p); // array de 00 a 23 con sus valores
				$coord = explode(":", $index);
				$date = $coord[0];
				$lat = $coord[1];
				$lon = $coord[2];
			?>

			date = "<?php echo $date; ?>";

			console.log("fecha timedimension: " + fecha + " | fecha values_p: " + date);

			if(fecha == "<?php echo $date; ?>"){

				<?php foreach($values_p as $hora => $valor){ ?>

					if(hora_f == "<?php echo $hora; ?>"){
						console.log(fecha);
						console.log("<?php echo $date; ?>");
						//map_data.push({lat: <?php echo $lat; ?>, lon: <?php echo $lon; ?>, cont: <?php echo $valor; ?>});

						<?php 
							$map_data[$date] = array(
								"lat" => $lat,
								"lon" => $lon,
								"cont" => $valor,
							); 
						?>

					}
					

				<?php } ?>

			}

		<?php } ?>

		

		<?php //var_dump($map_data); ?>

		heatmapLayer.setData({ // Se setea el layer Heatmap con los datos de un tiempo determinado (time)
			min: <?php echo ($min_value) ? $min_value : 0; ?>,
			max: <?php echo ($max_value) ? $max_value : 0; ?>,
			//data: map_data
			data: []
		});

		
		//datos.data[5].cont = 1;
		heatmapLayer.addTo(map);
		this._update();
	//console.log(map.hasLayer(timedimension));
	});

	map.on('click', function onMapClick(e) {
		
		var value = heatmapLayer._heatmap.getValueAt({
			x: e.containerPoint.x,
			y: e.containerPoint.y
		});

		var pop = '<table style="width: 100%; font-size:17px;"><tr><td><img heigth="27" width="27" src="http://dev.aire.mimasoft.cl/assets/images/impact-category/18 huellas-02.png"></td><td><strong>Dióxido de azufre:</strong> '+value.toFixed(4)+'</td></tr></table>';

		var popup = L.popup();
		popup
			.setLatLng(e.latlng)
			.setContent(pop)
			.openOn(map);
		console.log(e.latlng);
	});

	// Creación de objetos para cada flecha del layer de Leaflet Arrow

	/*
		para la velocidad del viento, se podría sacar el máximo de los valores de la base de datos y ese sea el valor más alto del palo
	*/

	/*
	var arrayLayers = [];
	var arrayData = [{
		latlng: L.latLng(-32.8042361, -70.955305),
		degree: 350,
		distance: 20, // Tamaño del cuerpo de la flecha. Cuando haya más viento, la flecha debe ser mas grande
		title: "Demo"
	},{
		latlng: L.latLng(-32.79954, -70.9349),
		degree: 350,
		distance: 30,
		title: "Demo"
	}];

	arrayData.forEach(function(obj, index){

		var windlayer = new L.Arrow(obj, {
			distanceUnit: 'px', // El largo de la flecha puede representarse en px o kilómetros en el mapa
			arrowheadLength: 10,
			arrowheadClosingLine: true,
			color: '#000',
		});

		arrayLayers.push(windlayer);

	});

	var flechas = L.layerGroup(arrayLayers);
	map.addLayer(flechas, true);
	*/

</script>