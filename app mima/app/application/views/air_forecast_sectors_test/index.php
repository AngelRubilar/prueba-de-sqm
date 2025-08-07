<div id="page-content" class="p20 clearfix">

<?php if($puede_ver != 3) { ?>

	<nav class="breadcrumb">
	<a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
	<a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
	<a class="breadcrumb-item" href="<?php echo get_uri("air_forecast_sectors"); ?>"><?php echo lang("forecasts"); ?></a> / 
	<a class="breadcrumb-item" href="<?php echo get_uri("#"); ?>"><?php echo $sector_info->name; ?></a>
	</nav>

	<?php if(false) { // OCULTAR TÍTULO Y EXPORTACIÓN EXCEL ?>
		<div class="panel panel-default">
			<div class="page-title clearfix">
				<h1><?php echo $sector_info->name; ?></h1>
				<div class="title-button-group">
					<button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
				</div>
			</div>
		</div>
	<?php } ?>

<?php } ?>

<div id="models_group">

	<?php if($puede_ver != 3) { ?>

		<?php if(in_array(3, $id_models_of_sector)){ ?>

			<div class="panel panel-default mb15">
				<div class="page-title clearfix p15">
					
					<div class="col-md-12">
						<div class="form-group col-md-6">
							<label for="air_quality_variable" class="col-md-3"><?php echo lang('air_quality_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("air_quality_variable_map", $air_quality_variables_dropdown_map, "", "id='air_quality_variable_map' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

						<div class="form-group col-md-6">
							<label for="meteorological_variable" class="col-md-3"><?php echo lang('meteorological_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("meteorological_variable_map", $meteorological_variables_dropdown_map, "", "id='meteorological_variable_map' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>
					</div>

				</div>


				<div id="div_numerical_map" class="panel-body">
					<div id="mapa" style="height: 450px; position: relative; outline: none;"></div>
				</div>

			</div>

		<?php } ?>


		<?php if(in_array(1, $id_models_of_sector)){ ?>
			<div id="div_neur_model" class="panel panel-default mb15">

				<div class="page-title clearfix">
					<h1><img src="<?php echo get_file_uri("assets/images/air_models/air_model_neuron.png"); ?>" alt="..." heigth='30' width='30'> <?php echo lang('machine_learning_model'); ?></h1>
				</div>

				<div class="panel-body">

					<div class="col-md-12">
						
						<div class="form-group col-md-4">
							<label for="air_quality_variable_neur_model" class="col-md-3"><?php echo lang('air_quality_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("air_quality_variable_neur_model", $air_quality_variables_neur_model_dropdown, "", "id='air_quality_variable_neur_model' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="receptor" class="col-md-3"><?php echo lang('receptor'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("receptor_neur_model", $receptors_neur_model_dropdown, "", "id='receptor_neur_model' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

					</div>

					<div class="col-md-12 mb15">
						<div id="chart_qual_neur_model" style="margin: 0 auto;">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

					<div class="col-md-12 mb15">
						<div id="calheatmap_qual_neur_model" class="div_calheatmap"></div>
					</div>

					<div class="col-md-12 mb15">
						<div class="table-responsive">
							<table id="qual_receptor_neur_model-table" class="display" cellspacing="0" width="100%">            
							</table>
						</div>
					</div>

				</div>

			</div>						
		<?php } ?>

		<?php if(in_array(2, $id_models_of_sector)){ ?>
			<div id="div_stat_model" class="panel panel-default mb15">

				<div class="page-title clearfix">
					<h1 id="h1_stat"><img src="<?php echo get_file_uri("assets/images/air_models/air_model_statics.png"); ?>" alt="..." heigth='30' width='30'> <?php echo lang('neuronal_model'); ?></h1>
				</div>

				<div class="panel-body">

					<div class="col-md-12">
						
						<div class="form-group col-md-4">
							<label for="air_quality_variable_stat_model" class="col-md-3"><?php echo lang('air_quality_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("air_quality_variable_stat_model", $air_quality_variables_stat_model_dropdown, "", "id='air_quality_variable_stat_model' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="receptor" class="col-md-3"><?php echo lang('receptor'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("receptor_stat_model", $receptors_stat_model_dropdown, "", "id='receptor_stat_model' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

					</div>

					<div class="col-md-12 mb15">
						<div id="chart_qual_stat_model" style="margin: 0 auto;">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

					<div class="col-md-12 mb15">
						<div id="calheatmap_qual_stat_model" class="div_calheatmap"></div>
					</div>

					<div class="col-md-12 mb15">
						<div class="table-responsive">
							<table id="qual_receptor_stat_model-table" class="display" cellspacing="0" width="100%">            
							</table>
						</div>
					</div>

				</div>

			</div>
		<?php } ?>

		<?php if(in_array(3, $id_models_of_sector)){ ?>

			<div id="div_numerical_model" class="panel panel-default mb15">
				<div class="page-title clearfix">
					<h1><img src="<?php echo get_file_uri("assets/images/air_models/air_model_numeric.png"); ?>" alt="..." heigth='30' width='30'> <?php echo lang('numerical_model'); ?></h1>
				</div>
				<div class="panel-body">
					<div class="col-md-12">
						
						<div class="col-md-4">
							<label for="air_quality_variable" class="col-md-3"><?php echo lang('air_quality_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("air_quality_variable", $air_quality_variables_dropdown, "", "id='air_quality_variable' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

						<div class="col-md-4">
							<label for="meteorological_variable" class="col-md-3"><?php echo lang('meteorological_variable'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("meteorological_variable", $meteorological_variables_dropdown, "", "id='meteorological_variable' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

						<div class="col-md-4">
							<label for="receptor" class="col-md-3"><?php echo lang('receptor'); ?></label>
							<div class="col-md-9">
								<?php
								echo form_dropdown("receptor", $receptors_dropdown, "", "id='receptor' class='select2 validate-hidden col-md-12' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
								?>
							</div>
						</div>

					</div>

				</div>

				<!--
				<div id="div_numerical_map" class="panel-body">
					<div id="mapa" style="height: 450px; position: relative; outline: none;"></div>
				</div>
				-->

				<div class="panel-body">
					
					<div class="col-md-12 mb15">
						<div id="chart_qual" style="margin: 0 auto;">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

					<div class="col-md-12 mb15">
						<div id="calheatmap_qual" class="div_calheatmap"></div>
					</div>

					<div class="col-md-12 mb15">
						<div class="table-responsive">
							<table id="qual_receptor-table" class="display" cellspacing="0" width="100%">            
							</table>
						</div>
					</div>

					<div class="col-md-12 mb15">
						<div id="chart_meteo" style="margin: 0 auto;" class="">
							<div style="margin-top: 100px; text-align: center">
								<strong><?php echo lang("no_information_available"); ?></strong>
							</div>
						</div>
					</div>

					<div class="col-md-12 mb15">
						<div id="calheatmap_meteo" class="div_calheatmap"></div>
					</div>

					<div class="col-md-12 mb15">
						<div class="table-responsive">
							<table id="meteo_receptor-table" class="display" cellspacing="0" width="100%">            
							</table>
						</div>
					</div>

				</div>

			</div>

		<?php } ?>

	<?php } else { ?>

		<div class="row"> 
			<div class="col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body">
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


<?php if($puede_ver != 3) { ?>

<style>
	/* Estilos para CalHeatMap */
	.cal-heatmap-container .subdomain-text {
		font-size: 12px;
		fill: #FFF;
	}

	.div_calheatmap > svg > svg.graph > svg:nth-child(1) > text{
		fill: #000;
		/*font-size: 16px;*/
		font-weight: bold;
	}

	html, body {
		height: auto !important;
		min-height: 100% !important;
	}


	/* Estilos para las leyendas del mapa */

	.info {
		padding: 6px 8px;
		font: 14px/16px Arial, Helvetica, sans-serif;
		background: white;
		background: rgba(255,255,255,0.8);
		box-shadow: 0 0 15px rgba(0,0,0,0.2);
		border-radius: 5px;
	}
	.info h4 {
		margin: 0 0 5px;
		color: #777;
	}

	.legend {
		line-height: 18px;
		color: #555;
	}
	.legend i {
		width: 18px;
		height: 18px;
		float: left;
		margin-right: 8px;
		opacity: 0.7;
	}
	.fixed_width{
		width: 150px;
	}

	/* if existe child 2, ejecutar el css */
	/*.leaflet-top.leaflet-right .leaflet-control:nth-child(1) {*/
	.legend_heatmap {
		float: left !important;
	}
	/*.leaflet-top.leaflet-right .leaflet-control:nth-child(2) {*/
	.legend_isoline, .leaflet-top.leaflet-right .leaflet-control:nth-child(2){
		clear: none;
	}


	/* Esconder botón "play" del timedimension 
	#mapa > div.leaflet-control-container > div.leaflet-bottom.leaflet-left > div > a.leaflet-control-timecontrol.timecontrol-play.play{
		display: none;
	} */

</style>

<script type="text/javascript">
    $(document).ready(function () {

		var min_heatmap = 15.1;

		$('.select2').select2();

		//General Settings
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

		// Array para setear formato de fechas en los calhetamap
		let array_format_date_calheatmap = [];
		array_format_date_calheatmap["d-m-Y"] = "%d-%m-%Y";
		array_format_date_calheatmap["m-d-Y"] = "%m-%d-%Y";
		array_format_date_calheatmap["Y-m-d"] = "%Y-%m-%d";
		array_format_date_calheatmap["d/m/Y"] = "%d/%m/%Y";
		array_format_date_calheatmap["m/d/Y"] = "%m/%d/%Y";
		array_format_date_calheatmap["Y/m/d"] = "%Y/%m/%d";
		array_format_date_calheatmap["d.m.Y"] = "%d.%m.%Y";
		array_format_date_calheatmap["m.d.Y"] = "%m.%d.%Y";
		array_format_date_calheatmap["Y.m.d"] = "%Y.%m.%d";


		/* Sección Modelo Numérico */

		// Si el Sector tiene el modelo Numérico (id 3)
		<?php if(in_array(3, $id_models_of_sector)){ ?>

			<?php if(count($air_quality_variables_dropdown_map) >= 2){ ?>
				<?php  $id_variable = (array_key_exists(9, $air_quality_variables_dropdown_map)) ? 9 : array_keys($air_quality_variables_dropdown_map)[1]; ?>
				<?php if($id_variable == 9){ // Si la variable es PM10, marcarla seleccionada ?>
					$('#air_quality_variable_map').val(9).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera variable del dropdown ?>
					$('#air_quality_variable_map').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>
			
			<?php if(count($air_quality_variables_dropdown) >= 2){ ?>
				<?php  $id_variable = (array_key_exists(9, $air_quality_variables_dropdown)) ? 9 : array_keys($air_quality_variables_dropdown)[1]; ?>
				<?php if($id_variable == 9){ // Si la variable es PM10, marcarla seleccionada ?>
					$('#air_quality_variable').val(9).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera variable del dropdown ?>
					$('#air_quality_variable').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>

			<?php if(count($meteorological_variables_dropdown_map) >= 2){ ?>
				$('#meteorological_variable_map').find('option:eq(1)').prop('selected', true).trigger('change');
			<?php } ?>

			<?php if(count($meteorological_variables_dropdown) >= 2){ ?>
				$('#meteorological_variable').find('option:eq(1)').prop('selected', true).trigger('change');
			<?php } ?>
			
			// Objetos variable Calidad del aire y variable Meteorológica inicial
			var air_quality_variable = <?php echo json_encode($air_quality_variable); ?>;
			var meteorological_variable = <?php echo json_encode($meteorological_variable); ?>;

			// Unidades de variables según configuración Unidades de Reporte
			var unit_qual = <?php echo json_encode($unit_qual); ?>;
			var unit_type_qual = <?php echo json_encode($unit_type_qual); ?>;
			var unit_meteo = <?php echo json_encode($unit_meteo); ?>;
			var unit_type_meteo = <?php echo json_encode($unit_type_meteo); ?>;

			var unit_meteo_vel = <?php echo json_encode($unit_meteo_vel); ?>;
			var unit_meteo_dir = <?php echo json_encode($unit_meteo_dir); ?>;

			var id_sector = <?php echo $sector_info->id; ?>;

			/* Mapa */

			// Inicia Mapa Leaflet
			//var map = L.map('map').setView([<?php echo $sector_info->latitude; ?>, <?php echo $sector_info->longitude; ?>], 300);
			var map = new L.Map('mapa', {
				center : new L.LatLng(<?php echo $sector_info->latitude; ?>, <?php echo $sector_info->longitude; ?>), // Posicionamiento del mapa
				zoom: 11,
				timeDimension: true,
				timeDimensionControl: true,
				timeDimensionControlOptions: {
					playButton: false,
					speedSlider: false,
				},
				timeDimensionOptions:{
					timeInterval: "<?php echo $first_date_map."T".$first_time_map.":00:00Z"."/".$last_date_map."T".$last_time_map.":00:00Z"; ?>",
					period: "PT1H",
					currentTime: "<?php echo strtotime($first_date_map); ?>" // Slot posicionado al principio del slide al mostrar el mapa
				},
				scrollWheelZoom: false,
				attributionControl:false
			});
			
			// Layer de tipo de mapa para Leaflet
			var baseLayer_openstreetmap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				//minZoom: 10,
				minZoom: 10,
				maxZoom: 18,
			});
			var baseLayer_google = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{ 
				//attribution: '...',
				minZoom: 10,
				maxZoom: 18,
				subdomains:['mt0','mt1','mt2','mt3']
			});

			baseLayer_openstreetmap.addTo(map); // Agrega capa base al Mapa

			<?php if(count($receptors)){ ?>

				var receptorIcon = new L.Icon({
					iconUrl: '/assets/js/leaflet/images/marker-icon-2x-green.png',
					shadowUrl: '/assets/js/leaflet/images/marker-shadow.png',
					iconSize: [25, 41],
					iconAnchor: [12, 41],
					popupAnchor: [1, -34],
					shadowSize: [41, 41]
				});

				var stationIcon = new L.Icon({
					iconUrl: '/assets/js/leaflet/images/marker-icon-2x-blue.png',
					shadowUrl: '/assets/js/leaflet/images/marker-shadow.png',
					iconSize: [25, 41],
					iconAnchor: [12, 41],
					popupAnchor: [1, -34],
					shadowSize: [41, 41]
				});

				<?php foreach($receptors as $receptor) { ?>
					<?php if($receptor->is_receptor){ ?>
						L.marker([<?php echo $receptor->latitude; ?>, <?php echo $receptor->longitude; ?>], {icon: receptorIcon}).addTo(map).bindPopup("<?php echo $receptor->name; ?>");
					<?php } else { ?>
						L.marker([<?php echo $receptor->latitude; ?>, <?php echo $receptor->longitude; ?>], {icon: stationIcon}).addTo(map).bindPopup("<?php echo $receptor->name; ?>");
					<?php } ?>	
				<?php } ?>

			<?php } ?>

			var array_alerts_qual = <?php echo json_encode($array_alerts_qual); ?>;
			var array_alerts_qual_heatmap_map_ranges = <?php echo json_encode($array_alerts_qual_heatmap_map_ranges); ?>;
			var array_alerts_qual_heatmap_map_ranges_percent = <?php echo json_encode($array_alerts_qual_heatmap_map_ranges_percent); ?>;
			var array_no_alerts_gradients = <?php echo json_encode($array_no_alerts_gradients); ?>;

			// Layer Heatmap (Calidad del Aire)
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
				onExtremaChange: function(extremaData) {
					// extremaData contains 
					// { min: <number>, max: <number>, gradient: <current gradient cfg> }
				},

				gradient : array_alerts_qual_heatmap_map_ranges_percent,
				
			});

			// Variable para la leyenda de Layer Heatmap
			var legend_heatmap;

			// Rangos y colores para la leyenda de Layer Heatmap
			var legend_ranges_heatmap = {

				<?php if(count($array_alerts_qual_legend_map_ranges)){ ?>
					<?php foreach($array_alerts_qual_legend_map_ranges as $color => $range){ ?>
						'<?php echo $range; ?>' : '<?php echo $color; ?>',
					<?php } ?>
				<?php } else { ?>
				
					'10' : 'rgb(30,101,78)', // 10 VERDE
					'50' : 'rgb(36,137,59)', // 50
					'70' : 'rgb(51,170,41)', // 70
					'90' : 'rgb(80,192,27)', // 90
					'100' : 'rgb(114,205,16)', // 100
					'200' : 'rgb(151,207,8)', // 200 
					'400' : 'rgb(184,189,3)', // 400 AMARILLO
					'500' : 'rgb(212,156,1)', // 500 NARANJO
					'800' : 'rgb(230,110,0)', // 800
					'1000' : 'rgb(244,58,0)', // 1000 
					'2000' : 'rgb(255,0,0)', // 2000 ROJO
					'3000' : 'rgb(201,0,100)', // 300
					'4000' : 'rgb(145,0,127)', // 400
					'5000' : 'rgb(64,0,138)', // 5000 MORADO

				<?php } ?>

			};


			// Variable para la leyenda de Layer Isoline
			var legend_isoline;

			// Rangos y colores para la leyenda de Layer Heatmap
			var legend_ranges_isoline = {

				<?php if(count($array_alerts_meteo_legend_map_ranges)){ ?>
					<?php foreach($array_alerts_meteo_legend_map_ranges as $color => $range){ ?>
						'<?php echo $range; ?>' : '<?php echo $color; ?>',
					<?php } ?>
				<?php } else { ?>

					'5' : 'rgb(30,101,78)', // 10 VERDE
					'6' : 'rgb(36,137,59)', // 50
					'7' : 'rgb(51,170,41)', // 70
					'8' : 'rgb(80,192,27)', // 90
					'9' : 'rgb(114,205,16)', // 100
					'10' : 'rgb(151,207,8)', // 200 
					'11' : 'rgb(184,189,3)', // 400 AMARILLO
					'12' : 'rgb(212,156,1)', // 500 NARANJO
					'13' : 'rgb(230,110,0)', // 800
					'14' : 'rgb(244,58,0)', // 1000 
					'15' : 'rgb(255,0,0)', // 2000 ROJO
					'16' : 'rgb(201,0,100)', // 300
					'17' : 'rgb(145,0,127)', // 400
					'18' : 'rgb(64,0,138)', // 5000 MORADO

				<?php } ?>

			};

			// Funcion que retorna el color de un rango para la capa de isolineas de variable meteorológica
			var array_alerts_meteo_legend_map_ranges = <?php echo json_encode($array_alerts_meteo_legend_map_ranges); ?>;
			function get_isoline_colors(value, array_alerts_meteo_legend_map_ranges) {

				if(Object.keys(array_alerts_meteo_legend_map_ranges).length !== 0){

					var colors = Object.keys(array_alerts_meteo_legend_map_ranges);
					var ranges = Object.values(array_alerts_meteo_legend_map_ranges);

					for(var i = Object.keys(array_alerts_meteo_legend_map_ranges).length - 1; i >= 0 ; i--){
						var color = colors[i];
						var range = ranges[i];
						if(parseFloat(value) >= parseFloat(range)){
							return color;
						}
					}

				} else {

					var array_colors = [
						'rgb(30,101,78)', // 10 VERDE
						'rgb(36,137,59)', // 50
						'rgb(51,170,41)', // 70
						'rgb(80,192,27)', // 90
						'rgb(114,205,16)', // 100
						'rgb(151,207,8)', // 200 
						'rgb(184,189,3)', // 400 AMARILLO
						'rgb(212,156,1)', // 500 NARANJO
						'rgb(230,110,0)', // 800
						'rgb(244,58,0)', // 1000 
						'rgb(255,0,0)', // 2000 ROJO
						'rgb(201,0,100)', // 300
						'rgb(145,0,127)', // 400
						'rgb(64,0,138)', // 5000 MORADO
					]

					return value >= 18
						? array_colors[13]
						: value >= 17
						? array_colors[12]
						: value >= 16
						? array_colors[11]
						: value >= 15
						? array_colors[10]
						: value >= 14
						? array_colors[9]
						: value >= 13
						? array_colors[8]
						: value >= 12
						? array_colors[7]
						: value >= 11
						? array_colors[6]
						: value >= 10
						? array_colors[5]
						: value >= 9
						? array_colors[4]
						: value >= 8
						? array_colors[3]
						: value >= 7
						? array_colors[2]
						: value >= 6
						? array_colors[1]
						: array_colors[0];

				}
				
			}


			// Layer Timedimension
			var timedimension = new L.TimeDimension.Layer(heatmapLayer, {
				updateTimeDimension: true,
				updateTimeDimensionMode: 'replace',
				addlastPoint: true
			});

			// Variable para el layer Isoline de variables meteorológicas
			var layerIsoline;

			// Variable para el LayerGroup de la capa Arrow
			var flechas;

			// Datos variable inicial de tipo "Calidad del Aire" (para el layer de HeatMap)
			var array_qual_data_values_p = <?php echo $array_qual_data_values_p; ?>;
			// console.log("array_qual_data_values_p: ", array_qual_data_values_p);

			// Datos variable inicial de tipo "Meteorológica" (para el layer de Arrow)
			var array_meteo_data_values_p = <?php echo $array_meteo_data_values_p; ?>;


			// Si hay variable de tipo "Calidad del aire" seleccionada al ingresar al Sector y esta tiene datos y 
			// si hay variable de tipo "Meteorológica" seleccionada al ingresar al Sector y esta tiene datos:
			<?php if(count($array_qual_data_values_p[$first_date_map]) && count($array_meteo_data_values_p[$first_date_map])) { ?>

				var fecha = "<?php echo $first_date_map; ?>"; 	// Ej: 2020-01-01
				var hora = "<?php echo $first_time_map; ?>"; 	// Ej: 00
				var time_hora = "time_" + hora; 			// Ej: time_00

				// Agrega layer Heatmap y Timedimension al mapa
				//if(max_heatmap != undefined){
				heatmapLayer.addTo(map);
				//}
				timedimension.addTo(map);

				// Si hay datos para la variable de tipo "Calidad del aire" seleccionada al ingresar al Sector
				// en la primera fecha de consulta, muestra sus datos en el mapa
				if(array_qual_data_values_p[fecha]){

					map.removeLayer(heatmapLayer);
					var array_data_qual = [];

					var array_cont = [];
					$.each(array_qual_data_values_p[fecha], function(key, value) {
						var array_latlon = key.split(":");
						var lat = array_latlon[0];
						var lon = array_latlon[1];
						var cont = value[time_hora];

						array_cont.push(value[time_hora]);

						if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
							array_data_qual.push({lat: lat, lon: lon, cont: cont});
						}
					});

					var max_heatmap = Math.max.apply(Math, array_cont);


					heatmapLayer.setData({
						min: min_heatmap,
						max: max_heatmap,
						data: array_data_qual
					})

					heatmapLayer.addTo(map);

					// Leyenda para capa Heatmap
					legend_heatmap = L.control({position: 'topright'});
					legend_heatmap.onAdd = function (map) {
						var div = L.DomUtil.create('div', 'info legend legend_heatmap fixed_width');
						div.innerHTML += '<strong>'+air_quality_variable.name+'</strong><br><br>';				
						Object.keys(legend_ranges_heatmap).reverse().forEach(function(index){
							div.innerHTML += '<div class=""><i style="background:' + legend_ranges_heatmap[index] + '"></i> ' + index + ' (' + unit_qual.nombre + ')' + '</div>';
						});
						div.innerHTML += '</div>';
						return div;
					};
					legend_heatmap.addTo(map);
					
				}

				if(map.hasLayer(flechas)){
					map.removeLayer(flechas);
				}

				if(map.hasLayer(layerIsoline)){
					map.removeLayer(layerIsoline);
				}

				// Creación de objetos para cada flecha del layer de Arrow		
				var arrayLayers = [];
				var array_data_meteo = [];

				// Si hay datos para la variable de tipo "Meteorológica" seleccionada al ingresar al Sector
				// en la primera fecha de consulta, muestra sus datos en el mapa
				if(array_meteo_data_values_p["<?php echo $first_date_map; ?>"]){

					// Si la variable meteorológica inicial es de tipo Velocidad del viento (id 1) o Dirección del viento (id 2)
					if(meteorological_variable.id == 1 || meteorological_variable.id == 2){

						$.each(array_meteo_data_values_p["<?php echo $first_date_map; ?>"], function(key, value) {
							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];

							if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
								var velocity = value[time_hora]["velocity"] * 5;
								var direction = value[time_hora]["direction"];

								var points = map.latLngToContainerPoint(L.latLng(lat, lon));

								array_data_meteo.push({
									latlng: L.latLng(lat, lon),
									degree: (direction > 0.5) ? Math.round(direction) : direction,
									distance: velocity,
									points: points,
									title: "Demo"
								});
							} else {
								var points = map.latLngToContainerPoint(L.latLng("0", "0"));

								array_data_meteo.push({
									latlng: L.latLng("0", "0"),
									degree: 0,
									distance: 0,
									points: points,
									title: "Demo"
								});
							}
						});

						// Crea los layers y el layer group para la capa Arrow
						array_data_meteo.forEach(function(obj, index){

							var windlayer = new L.Arrow(obj, {
								distanceUnit: 'px',
								arrowheadLength: 6,
								//arrowheadClosingLine: false,
								//stretchFactor: 0.8,
								weight: 1,
								color: '#000',
								popupContent: function(data) {
									var point = map.latLngToContainerPoint(data.latlng);
									var html_popup = '<table style="width: 100%; font-size:15px;">';
									if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
										var value = heatmapLayer._heatmap.getValueAt({
											x: point.x,
											y: point.y
										});
										html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
									}
									html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>';
									html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>';
									return html_popup;
								}
							});

							arrayLayers.push(windlayer);

						});

						flechas = L.layerGroup(arrayLayers);
						map.addLayer(flechas, true);

						if(map.hasLayer(baseLayer_openstreetmap)){
							$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
						}
						if(map.hasLayer(baseLayer_google)){
							$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
						}

					} else {

						var features = [];

						var meteo_data_cont = 0;
						var first_lat = 0;
						var first_lon = 0;
						var last_lat = 0;
						var last_lon = 0;

						$.each(array_meteo_data_values_p["<?php echo $first_date_map; ?>"], function(key, value) {

							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];
							features.push({
								type: 'Feature',
								geometry: {
									type: 'Point',
									coordinates: [parseFloat(lon), parseFloat(lat)]
								},
								properties:{
									z: parseFloat(cont)
								}
							});
						
							meteo_data_cont++;

							if(meteo_data_cont == 1){
								first_lat = parseFloat(lat);
								first_lon = parseFloat(lon);
							} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p["<?php echo $first_date_map; ?>"]).length){
								last_lat = parseFloat(lat);
								last_lon = parseFloat(lon);
							}

						});

						var points = {
							type: 'FeatureCollection',
							features: features
						}

						var crimeGridStyle = {
							style: function style(feature) {
								return {
									//fillColor: getColor(feature.properties.z),
									fillColor: "#FFF",
									weight: 2,
									opacity: 1,
									color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
									//dashArray: "3",
									fillOpacity: 0.4,
								};
							},
							onEachFeature: function (feature, layer) {
								layer.on('click', function (e) {
									var point = map.latLngToContainerPoint(e.latlng);
									var html_popup = '<table style="width: 100%; font-size:15px;">';
									if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
										var value = heatmapLayer._heatmap.getValueAt({
											x: point.x,
											y: point.y
										});
										html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
									}
									html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+numberFormat(feature.properties.z, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo.nombre +')</td></tr>';
									layer.bindPopup(html_popup);
								});
							}
						};
					
						var breaks = [];
						for(i = 0; i <= 100; i = i + 1){
							breaks.push(i);
						}
						var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
						layerIsoline = new L.geoJson(isolined, crimeGridStyle).addTo(map);

						// Leyenda para capa isoline
						legend_isoline = L.control({position: 'topright'});
						legend_isoline.onAdd = function (map) {
							var div = L.DomUtil.create('div', 'info legend legend_isoline fixed_width');
							div.innerHTML += '<strong>'+meteorological_variable.name+'</strong><br><br>';				
							Object.keys(legend_ranges_isoline).reverse().forEach(function(index){
								div.innerHTML += '<div class=""><i style="background:' + legend_ranges_isoline[index] + '"></i> ' + index + ' (' + unit_meteo.nombre + ')' + '</div>';
							});
							div.innerHTML += '</div>';
							return div;
						};
						legend_isoline.addTo(map);


					}	

				}

				// Handler Timedimension timeload
				timedimension.on('timeload', function(time){

					if(map.hasLayer(flechas)){
						map.removeLayer(flechas);
					}
					if(map.hasLayer(heatmapLayer)){
						map.removeLayer(heatmapLayer);
					}
					if(map.hasLayer(layerIsoline)){
						map.removeLayer(layerIsoline);
					}

					var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
					var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
					var hora = date.substring(11, 16); 			// Ej: 00:00
					var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
					var array_data_qual = [];

					var array_cont = [];

					// Si array_qual_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
					if(array_qual_data_values_p != null && array_qual_data_values_p[fecha]){

						$.each(array_qual_data_values_p[fecha], function(key, value) {
							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];

							array_cont.push(value[time_hora]);

							if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
								array_data_qual.push({lat: lat, lon: lon, cont: cont});
							}
						});

						var max_heatmap = Math.max.apply(Math, array_cont);

						if(!$.isEmptyObject(array_alerts_qual)){

							// configurar gradiente con: 
							var array_alerts_qual_heatmap_map_ranges = [];

							$.each(array_alerts_qual, function(key, value) {

								var valueToPush = [];

								if(key == 0){ // primer loop
									valueToPush["color"] = value.color;
									valueToPush["range"] = 0;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								} else if(key === array_alerts_qual.length - 1){ // último loop
									if(value.value < max_heatmap){
										valueToPush["color"] = value.color;
										valueToPush["range"] = value.value;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
										var valueToPush = [];
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									} else {
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									}
								} else {
									valueToPush["color"] = value.color;
									valueToPush["range"] = value.value;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								}

							});

							// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
							var array_alerts_qual_heatmap_map_ranges_percent = [];
							$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {
								if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
									var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
									percent = (percent > 1) ? 1.0 : percent;
									array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
								}
							});
							
							// Actualizar la gradiente de heatmapLayer
							var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
								gradient: array_alerts_qual_heatmap_map_ranges_percent
							});

						} else {

							// configurar gradiente con: 
							var array_alerts_qual_heatmap_map_ranges = [];

							$.each(array_no_alerts_gradients, function(key, value) {

								var valueToPush = [];

								if(key == 0){ // primer loop
									valueToPush["color"] = value.color;
									valueToPush["range"] = 0;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								} else if(key === array_no_alerts_gradients.length - 1){ // último loop
									if(value.value < max_heatmap){
										valueToPush["color"] = value.color;
										valueToPush["range"] = value.value;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
										var valueToPush = [];
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									} else {
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									}
								} else {
									valueToPush["color"] = value.color;
									valueToPush["range"] = value.value;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								}

							});

							// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
							var array_alerts_qual_heatmap_map_ranges_percent = [];
							$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {
								
								if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
									var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
									percent = (percent > 1) ? 1.0 : percent;
									array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
								}

							});
							
							// Actualizar la gradiente de heatmapLayer
							var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
								gradient: array_alerts_qual_heatmap_map_ranges_percent
							});
							
						}


					} else {
						// Si array_qual_data_values_p no tiene datos asociados a la fecha consultada,
						// llena con datos seteados en "0" array_data_qual, que es el arreglo de datos
						// para la capa HeatmapLayer.
						array_data_qual.push({lat: "0", lon: "0", cont: "0"});
					}

					heatmapLayer.setData({
						min: min_heatmap,
						max: max_heatmap,
						data: array_data_qual
					})

					heatmapLayer.addTo(map);


					if(array_qual_data_values_p != null && array_qual_data_values_p[fecha]){

						// Leyenda para capa Heatmap
						if(legend_heatmap != undefined){
							legend_heatmap.remove();
						}
						
						// Leyenda para capa Heatmap
						legend_heatmap = L.control({position: 'topright'});
						legend_heatmap.onAdd = function (map) {
							var div = L.DomUtil.create('div', 'info legend legend_heatmap fixed_width');
							div.innerHTML += '<strong>'+air_quality_variable.name+'</strong><br><br>';				
							Object.keys(legend_ranges_heatmap).reverse().forEach(function(index){
								div.innerHTML += '<div class=""><i style="background:' + legend_ranges_heatmap[index] + '"></i> ' + index + ' (' + unit_qual.nombre + ')' + '</div>';
							});
							return div;
						};

						// Si checkbox "Leyenda de Calidad del aire" está checkeado, agregar legend
						if($('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').is(':checked')){
							legend_heatmap.addTo(map);
						}
						
					}

					// Creación de objetos para cada flecha del layer de Leaflet Arrow		
					var arrayLayers = [];
					var array_data_meteo = [];

					// Si array_meteo_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
					if(array_meteo_data_values_p != null && array_meteo_data_values_p[fecha]){

						// Si la variable meteorológica inicial es de tipo Velocidad del viento (1) o Dirección del viento (2)
						if(meteorological_variable.id == 1 || meteorological_variable.id == 2){
							
							$.each(array_meteo_data_values_p[fecha], function(key, value) {
								var array_latlon = key.split(":");
								var lat = array_latlon[0];
								var lon = array_latlon[1];

								if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
									var velocity = value[time_hora]["velocity"] * 5;
									var direction = value[time_hora]["direction"];
									array_data_meteo.push({
										latlng: L.latLng(lat, lon),
										degree: (direction > 0.5) ? Math.round(direction) : direction,
										distance: velocity,
										title: "Demo"
									});
								} else {
									array_data_meteo.push({
										latlng: L.latLng("0", "0"),
										degree: 0,
										distance: 0,
										title: "Demo"
									});
								}
							});

							array_data_meteo.forEach(function(obj, index){

								var windlayer = new L.Arrow(obj, {
									distanceUnit: 'px', 
									arrowheadLength: 6,
									arrowheadClosingLine: false,
									//stretchFactor: 0.8,
									weight: 1,
									color: '#000',
									popupContent: function(data) {
										var point = map.latLngToContainerPoint(data.latlng);
										var html_popup = '<table style="width: 100%; font-size:15px;">';
										if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
											var value = heatmapLayer._heatmap.getValueAt({
												x: point.x,
												y: point.y
											});
											html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
										}
										html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>';
										html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>';
										return html_popup;
									},
								});

								arrayLayers.push(windlayer);

							});

							flechas = L.layerGroup(arrayLayers);
							map.addLayer(flechas, true);

							if(map.hasLayer(baseLayer_openstreetmap)){
								$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
							}
							if(map.hasLayer(baseLayer_google)){
								$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
							}

						} else {

							var features = [];

							var meteo_data_cont = 0;
							var first_lat = 0;
							var first_lon = 0;
							var last_lat = 0;
							var last_lon = 0;

							$.each(array_meteo_data_values_p[fecha], function(key, value) {

								var array_latlon = key.split(":");
								var lat = array_latlon[0];
								var lon = array_latlon[1];
								var cont = value[time_hora];
								features.push({
									type: 'Feature',
									geometry: {
										type: 'Point',
										coordinates: [parseFloat(lon), parseFloat(lat)]
									},
									properties:{
										z: parseFloat(cont)
									}
								});

								meteo_data_cont++;

								if(meteo_data_cont == 1){
									first_lat = parseFloat(lat);
									first_lon = parseFloat(lon);
								} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
									last_lat = parseFloat(lat);
									last_lon = parseFloat(lon);
								}

							});

							var points = {
								type: 'FeatureCollection',
								features: features
							}

							var crimeGridStyle = {
								style: function style(feature) {
									return {
										//fillColor: getColor(feature.properties.z),
										fillColor: "#FFF",
										weight: 2,
										opacity: 1,
										color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
										//dashArray: "3",
										fillOpacity: 0.4,
									};
								},
								onEachFeature: function (feature, layer) {
									layer.on('click', function (e) {
										var point = map.latLngToContainerPoint(e.latlng);
										var html_popup = '<table style="width: 100%; font-size:15px;">';
										if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
											var value = heatmapLayer._heatmap.getValueAt({
												x: point.x,
												y: point.y
											});
											html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
										}
										html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+numberFormat(feature.properties.z, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo.nombre +')</td></tr>';
										layer.bindPopup(html_popup);
									});
								}
							};

							var breaks = [];
							for(i = 0; i <= 100; i = i + 1){
								breaks.push(i);
							}
							var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
							layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

						}

					} 

					this._update();

				});

			
			// Si hay variable de tipo "Meteorológica" seleccionada al ingresar al Sector y esta tiene datos:
			<? } else if(count($array_meteo_data_values_p[$first_date_map])) { ?>

				timedimension.addTo(map);

				// Si hay datos para la variable de tipo "Meteorológica" seleccionada al ingresar al Sector
				// en la primera fecha de consulta, muestra sus datos en el mapa
				if(array_meteo_data_values_p["<?php echo $first_date_map; ?>"]){

					var fecha = "<?php echo $first_date_map; ?>"; 	// Ej: 2020-01-01
					var hora = "<?php echo $first_time_map; ?>"; 	// Ej: 00
					var time_hora = "time_" + hora; 			// Ej: time_00

					// Si la variable meteorológica inicial es de tipo Velocidad del viento (1) o Dirección del viento (2), agrega capa Arrow
					if(meteorological_variable.id == 1 || meteorological_variable.id == 2){

						if(map.hasLayer(flechas)){
							map.removeLayer(flechas);
						}

						// Creación de objetos para cada flecha del layer de Arrow		
						var arrayLayers = [];
						var array_data_meteo = [];

						$.each(array_meteo_data_values_p[fecha], function(key, value) {
							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];

							if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
								var velocity = value[time_hora]["velocity"] * 5;
								var direction = value[time_hora]["direction"];
								array_data_meteo.push({
									latlng: L.latLng(lat, lon),
									degree: (direction > 0.5) ? Math.round(direction) : direction,
									distance: velocity,
									title: "Demo"
								});
							} else {
								array_data_meteo.push({
									latlng: L.latLng("0", "0"),
									degree: 0,
									distance: 0,
									title: "Demo"
								});
							}

						});

						array_data_meteo.forEach(function(obj, index){

							var windlayer = new L.Arrow(obj, {
								distanceUnit: 'px',
								arrowheadLength: 6,
								arrowheadClosingLine: false,
								//stretchFactor: 0.8,
								weight: 1,
								color: '#000',
								popupContent: function(data) {
									var html_popup = '<table style="width: 100%; font-size:15px;"><tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>'
													+ '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>'
									return html_popup
								},
							});

							arrayLayers.push(windlayer);

						});

						flechas = L.layerGroup(arrayLayers);
						map.addLayer(flechas, true);

						if(map.hasLayer(baseLayer_openstreetmap)){
							$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
						}
						if(map.hasLayer(baseLayer_google)){
							$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
						}

					} else {  //Si la variable meteorológica inicial NO es de tipo Velocidad del viento (1) o Dirección del viento (2), agrega capa Isoline

						var features = [];

						var meteo_data_cont = 0;
						var first_lat = 0;
						var first_lon = 0;
						var last_lat = 0;
						var last_lon = 0;

						$.each(array_meteo_data_values_p[fecha], function(key, value) {

							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];
							features.push({
								type: 'Feature',
								geometry: {
									type: 'Point',
									coordinates: [parseFloat(lon), parseFloat(lat)]
								},
								properties:{
									z: parseFloat(cont)
								}
							});

							meteo_data_cont++;

							if(meteo_data_cont == 1){
								first_lat = parseFloat(lat);
								first_lon = parseFloat(lon);
							} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
								last_lat = parseFloat(lat);
								last_lon = parseFloat(lon);
							}

						});

						var points = {
							type: 'FeatureCollection',
							features: features
						}

						var crimeGridStyle = {
							style: function style(feature) {
								return {
									//fillColor: getColor(feature.properties.z),
									fillColor: "#FFF",
									weight: 2,
									opacity: 1,
									color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
									//dashArray: "3",
									fillOpacity: 0.4,
								};
							},
							onEachFeature: function (feature, layer) {
								var html_popup = '<table style="width: 100%; font-size:15px;">';
								html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+numberFormat(feature.properties.z, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo.nombre +')</td></tr>';
								layer.bindPopup(html_popup);
							}
						};

						var breaks = [];
						for(i = 0; i <= 100; i = i + 1){
							breaks.push(i);
						}
						var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
						layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

						// Leyenda para capa isoline
						legend_isoline = L.control({position: 'topright'});
						legend_isoline.onAdd = function (map) {
							var div = L.DomUtil.create('div', 'info legend legend_isoline fixed_width');
							div.innerHTML += '<strong>'+meteorological_variable.name+'</strong><br><br>';				
							Object.keys(legend_ranges_isoline).reverse().forEach(function(index){
								div.innerHTML += '<div class=""><i style="background:' + legend_ranges_isoline[index] + '"></i> ' + index + ' (' + unit_meteo.nombre + ')' + '</div>';
							});
							div.innerHTML += '</div>';
							return div;
						};
						legend_isoline.addTo(map);

					}

				}


				timedimension.on('timeload', function(time){

					if(map.hasLayer(flechas)){
						map.removeLayer(flechas);
					}
					if(map.hasLayer(layerIsoline)){
						map.removeLayer(layerIsoline);
					}

					var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
					var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
					var hora = date.substring(11, 16); 			// Ej: 00:00
					var time_hora = "time_" + hora.substr(0,2); // Ej: time_00

					// Creación de objetos para cada flecha del layer de Leaflet Arrow		
					var arrayLayers = [];
					var array_data_meteo = [];

					// Si array_meteo_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
					if(array_meteo_data_values_p != null && array_meteo_data_values_p[fecha]){

						// Si la variable meteorológica inicial es de tipo Velocidad del viento (1) o Dirección del viento (2)
						if(meteorological_variable.id == 1 || meteorological_variable.id == 2){
							
							$.each(array_meteo_data_values_p[fecha], function(key, value) {
								var array_latlon = key.split(":");
								var lat = array_latlon[0];
								var lon = array_latlon[1];
								
								if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
									var velocity = value[time_hora]["velocity"] * 5;
									var direction = value[time_hora]["direction"];
									array_data_meteo.push({
										latlng: L.latLng(lat, lon),
										degree: (direction > 0.5) ? Math.round(direction) : direction,
										distance: velocity,
										title: "Demo"
									});
								} else {
									array_data_meteo.push({
										latlng: L.latLng("0", "0"),
										degree: 0,
										distance: 0,
										title: "Demo"
									});
								}
							});

							array_data_meteo.forEach(function(obj, index){

								var windlayer = new L.Arrow(obj, {
									distanceUnit: 'px',
									arrowheadLength: 6,
									arrowheadClosingLine: false,
									//stretchFactor: 0.8,
									weight: 1,
									color: '#000',
									popupContent: function(data) {
										var html_popup = '<table style="width: 100%; font-size:15px;"><tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>'
														+ '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>'
										return html_popup
									},
								});

								arrayLayers.push(windlayer);

							});

							flechas = L.layerGroup(arrayLayers);
							map.addLayer(flechas, true);

							if(map.hasLayer(baseLayer_openstreetmap)){
								$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
							}
							if(map.hasLayer(baseLayer_google)){
								$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
							}

						} else {  //Si la variable meteorológica inicial NO es de tipo Velocidad del viento (1) o Dirección del viento (2)

							var features = [];

							var meteo_data_cont = 0;
							var first_lat = 0;
							var first_lon = 0;
							var last_lat = 0;
							var last_lon = 0;

							$.each(array_meteo_data_values_p[fecha], function(key, value) {

								var array_latlon = key.split(":");
								var lat = array_latlon[0];
								var lon = array_latlon[1];
								var cont = value[time_hora];
								features.push({
									type: 'Feature',
									geometry: {
										type: 'Point',
										coordinates: [parseFloat(lon), parseFloat(lat)]
									},
									properties:{
										z: parseFloat(cont)
									}
								});

								meteo_data_cont++;

								if(meteo_data_cont == 1){
									first_lat = parseFloat(lat);
									first_lon = parseFloat(lon);
								} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
									last_lat = parseFloat(lat);
									last_lon = parseFloat(lon);
								}

							});

							var points = {
								type: 'FeatureCollection',
								features: features
							}

							var crimeGridStyle = {
								style: function style(feature) {
									return {
										//fillColor: getColor(feature.properties.z),
										fillColor: "#FFF",
										weight: 2,
										opacity: 1,
										color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
										//dashArray: "3",
										fillOpacity: 0.4,
									};
								}, onEachFeature: function (feature, layer) {
									var html_popup = '<table style="width: 100%; font-size:15px;">';
									html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+numberFormat(feature.properties.z, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo.nombre +')</td></tr>';
									layer.bindPopup(html_popup);
								}
							};

							var breaks = [];
							for(i = 0; i <= 100; i = i + 1){
								breaks.push(i);
							}
							var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
							layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

						}

					}
					
					this._update();

				});

			// Si hay variable de tipo "Calidad del aire" seleccionada al ingresar al Sector y esta tiene datos
			<?php } else if(count($array_qual_data_values_p[$first_date_map])){ ?>

				// Agrega layer Heatmap y Timedimension al mapa
				heatmapLayer.addTo(map);
				timedimension.addTo(map);

				// Si hay datos para la variable de tipo "Calidad del aire" seleccionada al ingresar al Sector
				// en la primera fecha de consulta, muestra sus datos en el mapa
				if(array_qual_data_values_p["<?php echo $first_date_map; ?>"]){

					map.removeLayer(heatmapLayer);

					var fecha = "<?php echo $first_date_map; ?>"; 			// Ej: 2020-01-01
					var hora = "<?php echo $first_time_map; ?>"; 			// Ej: 00
					var time_hora = "time_" + hora; // Ej: time_00
					var array_data_qual = [];

					var array_cont = [];
					$.each(array_qual_data_values_p["<?php echo $first_date_map; ?>"], function(key, value) {
						
						var array_latlon = key.split(":");
						var lat = array_latlon[0];
						var lon = array_latlon[1];
						var cont = value[time_hora];

						array_cont.push(value[time_hora]);
						
						if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
							array_data_qual.push({lat: lat, lon: lon, cont: cont});
						}
					});

					var max_heatmap = Math.max.apply(Math, array_cont);


					heatmapLayer.setData({
						min: min_heatmap,
						max: max_heatmap,
						data: array_data_qual
					})

					heatmapLayer.addTo(map);

					// Leyenda para capa Heatmap
					legend_heatmap = L.control({position: 'topright'});
					legend_heatmap.onAdd = function (map) {
						var div = L.DomUtil.create('div', 'info legend legend_heatmap fixed_width');
						div.innerHTML += '<strong>'+air_quality_variable.name+'</strong><br><br>';				
						Object.keys(legend_ranges_heatmap).reverse().forEach(function(index){
							div.innerHTML += '<div class=""><i style="background:' + legend_ranges_heatmap[index] + '"></i> ' + index + ' (' + unit_qual.nombre + ')' + '</div>';
						});
						return div;
					};

					legend_heatmap.addTo(map);

				}

				timedimension.on('timeload', function(time){

					if(map.hasLayer(flechas)){
						map.removeLayer(flechas);
					}

					map.removeLayer(heatmapLayer);

					if(map.hasLayer(layerIsoline)){
						map.removeLayer(layerIsoline);
					}

					var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
					var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
					var hora = date.substring(11, 16); 			// Ej: 00:00
					var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
					var array_data_qual = [];
					
					var array_cont = [];

					// Si array_qual_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
					if(array_qual_data_values_p != null && array_qual_data_values_p[fecha]){

						$.each(array_qual_data_values_p[fecha], function(key, value) {
							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];

							array_cont.push(value[time_hora]);

							if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
								array_data_qual.push({lat: lat, lon: lon, cont: cont});
							}
						});

						var max_heatmap = Math.max.apply(Math, array_cont);

						if(!$.isEmptyObject(array_alerts_qual)){

							// configurar gradiente con: 
							var array_alerts_qual_heatmap_map_ranges = [];

							$.each(array_alerts_qual, function(key, value) {

								var valueToPush = [];

								if(key == 0){ // primer loop
									valueToPush["color"] = value.color;
									valueToPush["range"] = 0;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								} else if(key === array_alerts_qual.length - 1){ // último loop
									if(value.value < max_heatmap){
										valueToPush["color"] = value.color;
										valueToPush["range"] = value.value;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
										var valueToPush = [];
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									} else {
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									}
								} else {
									valueToPush["color"] = value.color;
									valueToPush["range"] = value.value;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								}

							});

							// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
							var array_alerts_qual_heatmap_map_ranges_percent = [];
							$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {

								if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
									var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
									percent = (percent > 1) ? 1.0 : percent;
									array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
								}

							});
							
							// Actualizar la gradiente de heatmapLayer
							var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
								gradient: array_alerts_qual_heatmap_map_ranges_percent
							});

						} else {

							// configurar gradiente con: 

							var array_alerts_qual_heatmap_map_ranges = [];

							$.each(array_no_alerts_gradients, function(key, value) {

								var valueToPush = [];

								if(key == 0){ // primer loop
									valueToPush["color"] = value.color;
									valueToPush["range"] = 0;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								} else if(key === array_no_alerts_gradients.length - 1){ // último loop
									if(value.value < max_heatmap){
										valueToPush["color"] = value.color;
										valueToPush["range"] = value.value;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
										var valueToPush = [];
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									} else {
										valueToPush["color"] = value.color;
										valueToPush["range"] = max_heatmap;
										array_alerts_qual_heatmap_map_ranges.push(valueToPush);
									}
								} else {
									valueToPush["color"] = value.color;
									valueToPush["range"] = value.value;
									array_alerts_qual_heatmap_map_ranges.push(valueToPush);
								}

							});


							// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
							var array_alerts_qual_heatmap_map_ranges_percent = [];
							$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {

								if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
									var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
									percent = (percent > 1) ? 1.0 : percent;
									array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
								}

							});
							
							// Actualizar la gradiente de heatmapLayer
							var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
								gradient: array_alerts_qual_heatmap_map_ranges_percent
							});

						}

					} else {
						// Si array_qual_data_values_p no tiene datos asociados a la fecha consultada,
						// llena con datos seteados en "0" array_data_qual, que es el arreglo de datos
						// para la capa HeatmapLayer.
						array_data_qual.push({lat: "0", lon: "0", cont: "0"});
					}

					heatmapLayer.setData({
						min: min_heatmap,
						max: max_heatmap,
						data: array_data_qual
					})

					heatmapLayer.addTo(map);


					if(array_meteo_data_values_p != null && array_meteo_data_values_p[fecha]){

						var features = [];

						var meteo_data_cont = 0;
						var first_lat = 0;
						var first_lon = 0;
						var last_lat = 0;
						var last_lon = 0;

						$.each(array_meteo_data_values_p[fecha], function(key, value) {

							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];
							features.push({
								type: 'Feature',
								geometry: {
									type: 'Point',
									coordinates: [parseFloat(lon), parseFloat(lat)]
								},
								properties:{
									z: parseFloat(cont)
								}
							});

							meteo_data_cont++;

							if(meteo_data_cont == 1){
								first_lat = parseFloat(lat);
								first_lon = parseFloat(lon);
							} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
								last_lat = parseFloat(lat);
								last_lon = parseFloat(lon);
							}

						});

						var points = {
							type: 'FeatureCollection',
							features: features
						}

						var crimeGridStyle = {
							style: function style(feature) {
								return {
									//fillColor: getColor(feature.properties.z),
									fillColor: "#FFF",
									weight: 2,
									opacity: 1,
									color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
									//dashArray: "3",
									fillOpacity: 0.4,
								};
							}
						};

						var breaks = [];
						for(i = 0; i <= 100; i = i + 1){
							breaks.push(i);
						}
						var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
						layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

					}

					this._update();

				});

			<? } else { ?>



				




				//heatmapLayer.addTo(map);
				timedimension.addTo(map);

				timedimension.on('timeload', function(time){

					var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
					var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
					var hora = date.substring(11, 16); 			// Ej: 00:00
					var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
					var array_data_meteo = [];

					if(map.hasLayer(heatmapLayer)){
						map.removeLayer(heatmapLayer);
					}
					if(map.hasLayer(layerIsoline)){
						map.removeLayer(layerIsoline);
					}

					var array_data_qual = [];
					var array_cont = [];

					if(array_qual_data_values_p != null && array_qual_data_values_p[fecha]){

						$.each(array_qual_data_values_p[fecha], function(key, value) {
							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];
							array_cont.push(value[time_hora]);

							if(cont >= min_heatmap){
								array_data_qual.push({lat: lat, lon: lon, cont: cont});
							}
						});

						var max_heatmap = Math.max.apply(Math, array_cont);

						if(max_heatmap >= min_heatmap) {
							heatmapLayer.setData({
								min: min_heatmap,
								max: max_heatmap,
								data: array_data_qual
							});

							heatmapLayer.addTo(map);
						}

					} 

					


					if(array_meteo_data_values_p != null && array_meteo_data_values_p[fecha]){

						var features = [];

						var meteo_data_cont = 0;
						var first_lat = 0;
						var first_lon = 0;
						var last_lat = 0;
						var last_lon = 0;

						$.each(array_meteo_data_values_p[fecha], function(key, value) {

							var array_latlon = key.split(":");
							var lat = array_latlon[0];
							var lon = array_latlon[1];
							var cont = value[time_hora];
							features.push({
								type: 'Feature',
								geometry: {
									type: 'Point',
									coordinates: [parseFloat(lon), parseFloat(lat)]
								},
								properties:{
									z: parseFloat(cont)
								}
							});

							meteo_data_cont++;

							if(meteo_data_cont == 1){
								first_lat = parseFloat(lat);
								first_lon = parseFloat(lon);
							} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
								last_lat = parseFloat(lat);
								last_lon = parseFloat(lon);
							}

						});

						var points = {
							type: 'FeatureCollection',
							features: features
						}

						var crimeGridStyle = {
							style: function style(feature) {
								return {
									//fillColor: getColor(feature.properties.z),
									fillColor: "#FFF",
									weight: 2,
									opacity: 1,
									color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
									//dashArray: "3",
									fillOpacity: 0.4,
								};
							}
						};

						var breaks = [];
						for(i = 0; i <= 100; i = i + 1){
							breaks.push(i);
						}
						var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
						layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

					}

					this._update();
				});

			<?php } ?>


			// MAP ON CLICK
			map.on('click', function onMapClick(e) {
				if(air_quality_variable != undefined){
					if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
						var value = heatmapLayer._heatmap.getValueAt({
							x: e.containerPoint.x,
							y: e.containerPoint.y
						});
						html_popup = '<table style="width: 100%; font-size:17px;"><tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
						var popup = new L.popup({
							maxWidth: 500
						});
						if($('.leaflet-popup-pane > .leaflet-popup').length == 0){
							popup
							.setLatLng(e.latlng)
							.setContent(html_popup);
							//.openOn(map);
							popup.addTo(map).openPopup();
						}
					}
				}
			});



			// Agrega menú para cambiar de capa base y mostrar/ocultar leyendas
			var base_maps = {
				"<?php echo lang("urban_map"); ?>": baseLayer_openstreetmap,
				"<?php echo lang("satelital_map"); ?>": baseLayer_google
			};
			var map_legends = {
				"<?php echo lang("qual_legend"); ?>": new L.layerGroup(),
				"<?php echo lang("meteo_legend"); ?>": new L.layerGroup(),
			};

			<?php if($user->change_map_type){ ?>
				L.control.layers(base_maps, map_legends, {position: 'topleft', collapsed: true}).addTo(map); 
			<?php } else { ?>
				L.control.layers(null, map_legends, {position: 'topleft', collapsed: true}).addTo(map); 
			<?php } ?>	

			// CAMBIAR COLOR DE FLECHAS DEL MAPA SEGÚN TIPO DE MAPA
			baseLayer_openstreetmap.on('load', function (event) {
				$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
			});
			baseLayer_google.on('load', function (event) {
				$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
			});
			
			// Evento Click de checkbox "Leyenda Variable Calidad del aire"
			//
			
			$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').on('change', function() { 
			//$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control-layers-expanded.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').on('change', function() {    
				if(legend_heatmap != undefined){
					if (map.hasLayer(heatmapLayer) && $(this).prop('checked')) {
						legend_heatmap.addTo(map);
					} else {
						legend_heatmap.remove();
					}	
				}
			});
			if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
				$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').prop('checked', true);
			}

			// Evento Click de checkbox "Leyenda Variable Meteorológica"
			$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').on('change', function() {   
			//$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control-layers-expanded.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').on('change', function() {    
				if(legend_isoline != undefined){
					if (map.hasLayer(layerIsoline) && $(this).prop('checked')) {
						legend_isoline.addTo(map);
					} else {
						legend_isoline.remove();
					}
				}
			});
			if(map.hasLayer(layerIsoline) && !$.isEmptyObject(array_meteo_data_values_p)){
				$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', true);
			}



			$("#air_quality_variable_map, #meteorological_variable_map").on('change', function(){

				var id_air_quality_variable = $('#air_quality_variable_map').val();
				var id_meteorological_variable = $('#meteorological_variable_map').val();
				var id_receptor = $("#receptor").val();

				var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
				var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
				var hora = date.substring(11, 16); 			// Ej: 00:00
				var time_hora = "time_" + hora.substr(0,2); // Ej: time_00*/

				$.ajax({
					url: '<?php echo_uri("air_forecast_sectors/get_data_variables"); ?>',
					type: 'post',
					dataType: 'json',
					data: {
						air_quality_variable: id_air_quality_variable,
						meteorological_variable: id_meteorological_variable,
						id_sector: id_sector,
						id_receptor: id_receptor,
						first_date: "<?php echo $first_date_map; ?>",
						last_date: "<?php echo $last_date_map; ?>",
						time_hora: time_hora
					},beforeSend: function() {
						//$('#div_numerical_map').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						appLoader.show();
					},
					success: function(respuesta){

						air_quality_variable = respuesta.air_quality_variable;
						meteorological_variable = respuesta.meteorological_variable;

						array_qual_data_values_p = respuesta.array_qual_data_values_p;
						array_meteo_data_values_p = respuesta.array_meteo_data_values_p;

						var unit_qual = respuesta.unit_qual;
						unit_meteo = respuesta.unit_meteo;

						var array_alerts_qual = respuesta.array_alerts_qual;
						var array_no_alerts_gradients = respuesta.array_no_alerts_gradients;
						var array_alerts_qual_legend_map_ranges = respuesta.array_alerts_qual_legend_map_ranges;
						var array_alerts_qual_heatmap_map_ranges = respuesta.array_alerts_qual_heatmap_map_ranges;
						var array_alerts_qual_heatmap_map_ranges_percent = respuesta.array_alerts_qual_heatmap_map_ranges_percent;

						array_alerts_meteo_legend_map_ranges = respuesta.array_alerts_meteo_legend_map_ranges;

						if(!$.isEmptyObject(array_alerts_qual_legend_map_ranges)){

							var legend_ranges_heatmap = {};
							$.each(array_alerts_qual_legend_map_ranges, function(color, value) {
								legend_ranges_heatmap[value] = color;
							});

						} else {

							var legend_ranges_heatmap = {
								'10' : 'rgb(30,101,78)', // 10 VERDE
								'50' : 'rgb(36,137,59)', // 50
								'70' : 'rgb(51,170,41)', // 70
								'90' : 'rgb(80,192,27)', // 90
								'100' : 'rgb(114,205,16)', // 100
								'200' : 'rgb(151,207,8)', // 200 
								'400' : 'rgb(184,189,3)', // 400 AMARILLO
								'500' : 'rgb(212,156,1)', // 500 NARANJO
								'800' : 'rgb(230,110,0)', // 800
								'1000' : 'rgb(244,58,0)', // 1000 
								'2000' : 'rgb(255,0,0)', // 2000 ROJO
								'3000' : 'rgb(201,0,100)', // 300
								'4000' : 'rgb(145,0,127)', // 400
								'5000' : 'rgb(64,0,138)', // 5000 MORADO
							}
						}


						
						if(!$.isEmptyObject(array_alerts_meteo_legend_map_ranges)){

							var legend_ranges_isoline = {};
							$.each(array_alerts_meteo_legend_map_ranges, function(color, value) {
								legend_ranges_isoline[value] = color;
							});

						} else {

							var legend_ranges_isoline = {
								'5' : 'rgb(30,101,78)', // 10 VERDE
								'6' : 'rgb(36,137,59)', // 50
								'7' : 'rgb(51,170,41)', // 70
								'8' : 'rgb(80,192,27)', // 90
								'9' : 'rgb(114,205,16)', // 100
								'10' : 'rgb(151,207,8)', // 200 
								'11' : 'rgb(184,189,3)', // 400 AMARILLO
								'12' : 'rgb(212,156,1)', // 500 NARANJO
								'13' : 'rgb(230,110,0)', // 800
								'14' : 'rgb(244,58,0)', // 1000 
								'15' : 'rgb(255,0,0)', // 2000 ROJO
								'16' : 'rgb(201,0,100)', // 300
								'17' : 'rgb(145,0,127)', // 400
								'18' : 'rgb(64,0,138)', // 5000 MORADO
							}
						}


						// Actualizar periodo (fechas y horas) de timedimension del mapa
						var first_date_map = respuesta.first_date_map;
						var last_date_map = respuesta.last_date_map;
						var first_time_map = respuesta.first_time_map;
						var last_time_map = respuesta.last_time_map;

						if(!$.isEmptyObject(array_qual_data_values_p) || !$.isEmptyObject(array_meteo_data_values_p)){
							var start_datetime = new Date(first_date_map + "T" + first_time_map + ":00:00Z");
							var end_datetime = new Date(last_date_map + "T" + last_time_map + ":00:00Z");
							var new_timedimension_period = L.TimeDimension.Util.explodeTimeRange(start_datetime, end_datetime, 'PT1H');
							map.timeDimension.setAvailableTimes(new_timedimension_period, 'replace');
							//map.timeDimension.setCurrentTime(start_datetime);
						}
						
						//var fecha = first_date_map; 			  // Ej: 2020-01-01
						//var time_hora = "time_" + first_time_map; // Ej: time_00

						if(map.hasLayer(heatmapLayer)){
							map.removeLayer(heatmapLayer);
						}
						if(legend_heatmap != undefined){
							legend_heatmap.remove();
						}
						if(map.hasLayer(layerIsoline)){
							map.removeLayer(layerIsoline);
						}
						if(legend_isoline != undefined){
							legend_isoline.remove();
						}
						if(map.hasLayer(timedimension)){
							map.removeLayer(timedimension);
						}
						if(map.hasLayer(flechas)){
							map.removeLayer(flechas);
						}
						
						if (!$.isEmptyObject(array_qual_data_values_p) /*|| array_qual_data_values_p != undefined*/){


							// Agrega layer Heatmap al mapa
							heatmapLayer.addTo(map);
							timedimension.addTo(map);

							if(array_qual_data_values_p != null){

								map.removeLayer(heatmapLayer);

								var array_data_qual = [];

								var array_cont = [];

								if(array_qual_data_values_p != null && array_qual_data_values_p[fecha] != undefined 
								&& array_qual_data_values_p[fecha] && air_quality_variable != undefined){
									$.each(array_qual_data_values_p[fecha], function(key, value) {
										var array_latlon = key.split(":");
										var lat = array_latlon[0];
										var lon = array_latlon[1];
										var cont = value[time_hora];

										array_cont.push(value[time_hora]);

										if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
											array_data_qual.push({lat: lat, lon: lon, cont: cont});
										}
									});

									var max_heatmap = Math.max.apply(Math, array_cont);

									if(!$.isEmptyObject(array_alerts_qual)){

										// configurar gradiente con: 
										var array_alerts_qual_heatmap_map_ranges = [];

										$.each(array_alerts_qual, function(key, value) {

											var valueToPush = [];

											if(key == 0){ // primer loop
												valueToPush["color"] = value.color;
												valueToPush["range"] = 0;
												array_alerts_qual_heatmap_map_ranges.push(valueToPush);
											} else if(key === array_alerts_qual.length - 1){ // último loop
												if(value.value < max_heatmap){
													valueToPush["color"] = value.color;
													valueToPush["range"] = value.value;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													var valueToPush = [];
													valueToPush["color"] = value.color;
													valueToPush["range"] = max_heatmap;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												} else {
													valueToPush["color"] = value.color;
													valueToPush["range"] = max_heatmap;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												}
											} else {
												valueToPush["color"] = value.color;
												valueToPush["range"] = value.value;
												array_alerts_qual_heatmap_map_ranges.push(valueToPush);
											}

										});


										// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
										var array_alerts_qual_heatmap_map_ranges_percent = [];
										$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {

											if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
												var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
												percent = (percent > 1) ? 1.0 : percent;
												array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
											}

										});

										// Actualizar la gradiente de heatmapLayer
										var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
											gradient: array_alerts_qual_heatmap_map_ranges_percent
										});

									} else {

										// configurar gradiente con: 

										var array_alerts_qual_heatmap_map_ranges = [];

										$.each(array_no_alerts_gradients, function(key, value) {

											var valueToPush = [];
											if(key == 0){ // primer loop
												valueToPush["color"] = value.color;
												valueToPush["range"] = 0;
												array_alerts_qual_heatmap_map_ranges.push(valueToPush);
											} else if(key === array_no_alerts_gradients.length - 1){ // último loop
												if(value.value < max_heatmap){
													valueToPush["color"] = value.color;
													valueToPush["range"] = value.value;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													var valueToPush = [];
													valueToPush["color"] = value.color;
													valueToPush["range"] = max_heatmap;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												} else {
													valueToPush["color"] = value.color;
													valueToPush["range"] = max_heatmap;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												}
											} else {
												valueToPush["color"] = value.color;
												valueToPush["range"] = value.value;
												array_alerts_qual_heatmap_map_ranges.push(valueToPush);
											}

										});


										// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
										var array_alerts_qual_heatmap_map_ranges_percent = [];
										$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {

											if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
												var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
												percent = (percent > 1) ? 1.0 : percent;
												array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
											}

										});

										// Actualizar la gradiente de heatmapLayer
										var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
											gradient: array_alerts_qual_heatmap_map_ranges_percent
										});

									}

								} else {
									// Si array_qual_data_values_p no tiene datos asociados a la fecha consultada,
									// llena con datos seteados en "0" array_data_qual, que es el arreglo de datos
									// para la capa HeatmapLayer.
									array_data_qual.push({lat: "0", lon: "0", cont: "0"});
								}


								if(max_heatmap != undefined){
									heatmapLayer.setData({
										min: min_heatmap,
										max: max_heatmap,
										data: array_data_qual
									})

									heatmapLayer.addTo(map);
								}
								

								if(array_qual_data_values_p != null && array_qual_data_values_p[fecha] != undefined 
								&& array_qual_data_values_p[fecha] && air_quality_variable != undefined){

									// Leyenda para capa Heatmap
									legend_heatmap = L.control({position: 'topright'});
									legend_heatmap.onAdd = function (map) {
										var div = L.DomUtil.create('div', 'info legend legend_heatmap fixed_width');
										div.innerHTML += '<strong>'+air_quality_variable.name+'</strong><br><br>';				
										Object.keys(legend_ranges_heatmap).reverse().forEach(function(index){
											div.innerHTML += '<div class=""><i style="background:' + legend_ranges_heatmap[index] + '"></i> ' + index + ' (' + unit_qual.nombre + ')' + '</div>';
										});
										return div;
									};

									legend_heatmap.addTo(map);

									// Check checkbox "Leyenda Variable de Calidad del aire"
									$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').prop('checked', true);

								}

								timedimension.on('timeload', function(time){

									map.removeLayer(heatmapLayer);

									var date = new Date(map.timeDimension.getCurrentTime()).toISOString();
									var fecha = date.substring(0, 10); 			// Ej: 2020-01-01
									var hora = date.substring(11, 16); 			// Ej: 00:00
									var time_hora = "time_" + hora.substr(0,2); // Ej: time_00
									var array_data_qual = [];

									var array_cont = [];

									// Si array_qual_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
									if(array_qual_data_values_p != null && array_qual_data_values_p[fecha]){

										$.each(array_qual_data_values_p[fecha], function(key, value) {
											var array_latlon = key.split(":");
											var lat = array_latlon[0];
											var lon = array_latlon[1];
											var cont = value[time_hora];

											array_cont.push(value[time_hora]);

											if(cont >= min_heatmap /*&& cont <= max_heatmap*/){
												array_data_qual.push({lat: lat, lon: lon, cont: cont});
											}
										});

										var max_heatmap = Math.max.apply(Math, array_cont);

										if(!$.isEmptyObject(array_alerts_qual)){

											// configurar gradiente con: 
											var array_alerts_qual_heatmap_map_ranges = [];

											$.each(array_alerts_qual, function(key, value) {

												var valueToPush = [];
												if(key == 0){ // primer loop
													valueToPush["color"] = value.color;
													valueToPush["range"] = 0;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												} else if(key === array_alerts_qual.length - 1){ // último loop
													if(value.value < max_heatmap){
														valueToPush["color"] = value.color;
														valueToPush["range"] = value.value;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
														var valueToPush = [];
														valueToPush["color"] = value.color;
														valueToPush["range"] = max_heatmap;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													} else {
														valueToPush["color"] = value.color;
														valueToPush["range"] = max_heatmap;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													}
												} else {
													valueToPush["color"] = value.color;
													valueToPush["range"] = value.value;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												}

											});

											// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
											var array_alerts_qual_heatmap_map_ranges_percent = [];
											$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {

												if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
													var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
													percent = (percent > 1) ? 1.0 : percent;
													array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
												}

											});

											// Actualizar la gradiente de heatmapLayer
											var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
												gradient: array_alerts_qual_heatmap_map_ranges_percent
											});

										} else {

											// configurar gradiente con: 
											var array_alerts_qual_heatmap_map_ranges = [];

											$.each(array_no_alerts_gradients, function(key, value) {

												var valueToPush = [];

												if(key == 0){ // primer loop
													valueToPush["color"] = value.color;
													valueToPush["range"] = 0;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												} else if(key === array_no_alerts_gradients.length - 1){ // último loop
													if(value.value < max_heatmap){
														valueToPush["color"] = value.color;
														valueToPush["range"] = value.value;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
														var valueToPush = [];
														valueToPush["color"] = value.color;
														valueToPush["range"] = max_heatmap;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													} else {
														valueToPush["color"] = value.color;
														valueToPush["range"] = max_heatmap;
														array_alerts_qual_heatmap_map_ranges.push(valueToPush);
													}
												} else {
													valueToPush["color"] = value.color;
													valueToPush["range"] = value.value;
													array_alerts_qual_heatmap_map_ranges.push(valueToPush);
												}

											});

											// Pasar los rangos a porcentajes para la gradiente de heatmapLayer
											var array_alerts_qual_heatmap_map_ranges_percent = [];
											$.each(array_alerts_qual_heatmap_map_ranges, function(key, value) {
												if(value.range < array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range){
													var percent = ( (value.range * 100) / array_alerts_qual_heatmap_map_ranges[array_alerts_qual_heatmap_map_ranges.length - 1].range ) / 100;
													percent = (percent > 1) ? 1.0 : percent;
													array_alerts_qual_heatmap_map_ranges_percent[percent] = value.color;
												}
											});

											// Actualizar la gradiente de heatmapLayer
											var update_array_alerts_qual_heatmap_map_ranges = heatmapLayer._heatmap.configure({
												gradient: array_alerts_qual_heatmap_map_ranges_percent
											});

										}

									} else {
										// Si array_qual_data_values_p no tiene datos asociados a la fecha consultada,
										// llena con datos seteados en "0" array_data_qual, que es el arreglo de datos
										// para la capa HeatmapLayer.
										array_data_qual.push({lat: "0", lon: "0", cont: "0"});
									}

									if(max_heatmap != undefined){
										heatmapLayer.setData({
											min: min_heatmap,
											max: max_heatmap,
											data: array_data_qual
										})

										heatmapLayer.addTo(map);
									}
									
									if(array_qual_data_values_p != null && array_qual_data_values_p[fecha] != undefined 
									&& array_qual_data_values_p[fecha] && air_quality_variable != undefined){

										// Leyenda para capa Heatmap
										legend_heatmap.remove();
										
										// Leyenda para capa Heatmap
										legend_heatmap = L.control({position: 'topright'});
										legend_heatmap.onAdd = function (map) {
											var div = L.DomUtil.create('div', 'info legend legend_heatmap fixed_width');
											div.innerHTML += '<strong>'+air_quality_variable.name+'</strong><br><br>';				
											Object.keys(legend_ranges_heatmap).reverse().forEach(function(index){
												div.innerHTML += '<div class=""><i style="background:' + legend_ranges_heatmap[index] + '"></i> ' + index + ' (' + unit_qual.nombre + ')' + '</div>';
											});
											return div;
										};

										legend_heatmap.addTo(map);

									} else {
										// Si checkbox "Leyenda de Calidad del aire" está checkeado, agregar legend
										if($('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').is(':checked')){
											legend_heatmap.addTo(map);
										}
									}

									this._update();

								});


							}

							if (!$.isEmptyObject(array_meteo_data_values_p)){

								if(map.hasLayer(flechas)){
									map.removeLayer(flechas);
								}
								if(map.hasLayer(layerIsoline)){
									map.removeLayer(layerIsoline);
								}

								// Creación de objetos para cada flecha del layer de Arrow		
								var arrayLayers = [];
								var array_data_meteo = [];

								// Si hay datos para la variable de tipo "Meteorológica" seleccionada al ingresar al Sector
								// en la primera fecha de consulta, muestra sus datos en el mapa
								if(array_meteo_data_values_p != null){

									if(array_meteo_data_values_p[fecha]){

										// Si la variable meteorológica inicial es de tipo Velocidad del viento (1) o Dirección del viento (2)
										if(meteorological_variable.id == 1 || meteorological_variable.id == 2){

											$.each(array_meteo_data_values_p[fecha], function(key, value) {
												var array_latlon = key.split(":");
												var lat = array_latlon[0];
												var lon = array_latlon[1];

												if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
													var velocity = value[time_hora]["velocity"] * 5;
													var direction = value[time_hora]["direction"];
													array_data_meteo.push({
														latlng: L.latLng(lat, lon),
														degree: (direction > 0.5) ? Math.round(direction) : direction,
														distance: velocity,
														title: "Demo"
													});
												} else {
													array_data_meteo.push({
														latlng: L.latLng("0", "0"),
														degree: 0,
														distance: 0,
														title: "Demo"
													});
												}

											});

											array_data_meteo.forEach(function(obj, index){

												var windlayer = new L.Arrow(obj, {
													distanceUnit: 'px', // El largo de la flecha puede representarse en px o kilómetros en el mapa
													arrowheadLength: 6,
													arrowheadClosingLine: false,
													//stretchFactor: 0.8,
													weight: 1,
													color: '#000',
													popupContent: function(data) {

														var point = map.latLngToContainerPoint(data.latlng);
														var html_popup = '<table style="width: 100%; font-size:15px;">';

														if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
															var value = heatmapLayer._heatmap.getValueAt({
																x: point.x,
																y: point.y
															});
															html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
														}

														html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>';
														html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>';
														return html_popup;

													},
												});

												arrayLayers.push(windlayer);

											});

											flechas = L.layerGroup(arrayLayers);
											map.addLayer(flechas, true);

											// Uncheck checkbox "Leyenda Variable Meteorológica"
											$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', false);

											if(map.hasLayer(baseLayer_openstreetmap)){
												$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
											}
											if(map.hasLayer(baseLayer_google)){
												$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
											}
											
										} else {

											var features = [];

											var meteo_data_cont = 0;
											var first_lat = 0;
											var first_lon = 0;
											var last_lat = 0;
											var last_lon = 0;

											$.each(array_meteo_data_values_p[fecha], function(key, value) {

												var array_latlon = key.split(":");
												var lat = array_latlon[0];
												var lon = array_latlon[1];
												var cont = value[time_hora];
												features.push({
													type: 'Feature',
													geometry: {
														type: 'Point',
														coordinates: [parseFloat(lon), parseFloat(lat)]
													},
													properties:{
														z: parseFloat(cont)
													}
												});

												meteo_data_cont++;

												if(meteo_data_cont == 1){
													first_lat = parseFloat(lat);
													first_lon = parseFloat(lon);
												} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
													last_lat = parseFloat(lat);
													last_lon = parseFloat(lon);
												}

											});

											var points = {
												type: 'FeatureCollection',
												features: features
											}

											var crimeGridStyle = {
												style: function style(feature) {
													return {
														//fillColor: getColor(feature.properties.z),
														fillColor: "#FFF",
														weight: 2,
														opacity: 1,
														color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
														//dashArray: "3",
														fillOpacity: 0.4,
													};
												},
												onEachFeature: function (feature, layer) {
													layer.on('click', function (e) {
														var point = map.latLngToContainerPoint(e.latlng);
														var html_popup = '<table style="width: 100%; font-size:15px;">';
														if(map.hasLayer(heatmapLayer) && !$.isEmptyObject(array_qual_data_values_p)){
															var value = heatmapLayer._heatmap.getValueAt({
																x: point.x,
																y: point.y
															});
															html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+air_quality_variable.icono+'"></td><td><strong> &nbsp; '+air_quality_variable.name+':</strong> '+numberFormat(value, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_qual.nombre +')</td></tr>';
														}
														html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+numberFormat(feature.properties.z, decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo.nombre +')</td></tr>';
														layer.bindPopup(html_popup);
													});
												}
											};

											var breaks = [];
											for(i = 0; i <= 100; i = i + 1){
												breaks.push(i);
											}
											var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
											layerIsoline = new L.geoJson(isolined, crimeGridStyle).addTo(map);

											// Leyenda para capa isoline
											legend_isoline = L.control({position: 'topright'});
											legend_isoline.onAdd = function (map) {
												var div = L.DomUtil.create('div', 'info legend legend_isoline fixed_width');
												div.innerHTML += '<strong>'+meteorological_variable.name+'</strong><br><br>';				
												Object.keys(legend_ranges_isoline).reverse().forEach(function(index){
													div.innerHTML += '<div class=""><i style="background:' + legend_ranges_isoline[index] + '"></i> ' + index + ' (' + unit_meteo.nombre + ')' + '</div>';
												});
												div.innerHTML += '</div>';
												return div;
											};
											legend_isoline.addTo(map);

											// Check checkbox "Leyenda Variable Meteorológica"
											$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', true);

										}

									} else {

										if(meteorological_variable.id != 1 || meteorological_variable.id != 2){
											// Leyenda para capa isoline
											legend_isoline = L.control({position: 'topright'});
											legend_isoline.onAdd = function (map) {
												var div = L.DomUtil.create('div', 'info legend legend_isoline fixed_width');
												div.innerHTML += '<strong>'+meteorological_variable.name+'</strong><br><br>';				
												Object.keys(legend_ranges_isoline).reverse().forEach(function(index){
													div.innerHTML += '<div class=""><i style="background:' + legend_ranges_isoline[index] + '"></i> ' + index + ' (' + unit_meteo.nombre + ')' + '</div>';
												});
												div.innerHTML += '</div>';
												return div;
											};
											legend_isoline.addTo(map);
										}

									}

								}

							} else {
								// Uncheck checkbox "Leyenda Variable Meteorológica"
								$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', false);
							}

							timedimension._update();

						} else if (!$.isEmptyObject(array_meteo_data_values_p)){

							if(map.hasLayer(heatmapLayer)){
								map.removeLayer(heatmapLayer);
							}
							if(map.hasLayer(timedimension)){
								map.removeLayer(timedimension);
							}
							if(map.hasLayer(flechas)){
								map.removeLayer(flechas);
							}
							if(map.hasLayer(layerIsoline)){
								map.removeLayer(layerIsoline);
							}

							timedimension.addTo(map);

							// Creación de objetos para cada flecha del layer de Leaflet Arrow
							var arrayLayers = [];
							var array_data_meteo = [];

							// Si array_meteo_data_values_p tiene datos asociados a la fecha consultada, muestra sus datos en el mapa
							if(array_meteo_data_values_p[fecha]){
								
								if(meteorological_variable.id == 1 || meteorological_variable.id == 2){

									$.each(array_meteo_data_values_p[fecha], function(key, value) {
										var array_latlon = key.split(":");
										var lat = array_latlon[0];
										var lon = array_latlon[1];
										
										if(value[time_hora]["velocity"] != null && value[time_hora]["direction"] != null){
											var velocity = value[time_hora]["velocity"] * 5;
											var direction = value[time_hora]["direction"];
											array_data_meteo.push({
												latlng: L.latLng(lat, lon),
												degree: (direction > 0.5) ? Math.round(direction) : direction,
												distance: velocity,
												title: "Demo"
											});
										} else {
											array_data_meteo.push({
												latlng: L.latLng("0", "0"),
												degree: 0,
												distance: 0,
												title: "Demo"
											});
										}

									});

									array_data_meteo.forEach(function(obj, index){

										var windlayer = new L.Arrow(obj, {
											distanceUnit: 'px', // El largo de la flecha puede representarse en px o kilómetros en el mapa
											arrowheadLength: 6,
											arrowheadClosingLine: false,
											//stretchFactor: 0.8,
											weight: 1,
											color: '#000',
											popupContent: function(data) {
												var html_popup = '<table style="width: 100%; font-size:15px;">';
												html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_speed.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_speed"); ?>' + ':</strong> '+numberFormat((data.distance/5), decimal_numbers, decimals_separator, thousands_separator)+' ('+ unit_meteo_vel.nombre +')</td></tr>';
												html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/air_wind_direction.png"></td><td><strong> &nbsp; ' + '<?php echo lang("wind_direction"); ?>' + ':</strong> '+data.degree+' ('+ unit_meteo_dir.nombre +')</td></tr></table>';
												return html_popup;
											},
										});

										arrayLayers.push(windlayer);

									});

									flechas = L.layerGroup(arrayLayers);
									map.addLayer(flechas, true);

									if(map.hasLayer(baseLayer_openstreetmap)){
										$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#000000");
									}
									if(map.hasLayer(baseLayer_google)){
										$("#mapa > div.leaflet-pane.leaflet-map-pane > div.leaflet-pane.leaflet-overlay-pane > svg > g > path").attr("stroke", "#FFFFFF");
									}

								} else {

									var features = [];

									var meteo_data_cont = 0;
									var first_lat = 0;
									var first_lon = 0;
									var last_lat = 0;
									var last_lon = 0;

									$.each(array_meteo_data_values_p[fecha], function(key, value) {

										var array_latlon = key.split(":");
										var lat = array_latlon[0];
										var lon = array_latlon[1];
										var cont = value[time_hora];
										features.push({
											type: 'Feature',
											geometry: {
												type: 'Point',
												coordinates: [parseFloat(lon), parseFloat(lat)]
											},
											properties:{
												z: parseFloat(cont)
											}
										});

										meteo_data_cont++;

										if(meteo_data_cont == 1){
											first_lat = parseFloat(lat);
											first_lon = parseFloat(lon);
										} else if(meteo_data_cont == Object.keys(array_meteo_data_values_p[fecha]).length){
											last_lat = parseFloat(lat);
											last_lon = parseFloat(lon);
										}

									});

									var points = {
										type: 'FeatureCollection',
										features: features
									}

									var crimeGridStyle = {
										style: function style(feature) {
											return {
												//fillColor: getColor(feature.properties.z),
												fillColor: "#FFF",
												weight: 2,
												opacity: 1,
												color: get_isoline_colors(feature.properties.z, array_alerts_meteo_legend_map_ranges),
												//dashArray: "3",
												fillOpacity: 0.4,
											};
										},
										onEachFeature: function (feature, layer) {
											layer.on('click', function (e) {
												var html_popup = '<table style="width: 100%; font-size:15px;">';
												html_popup += '<tr><td><img heigth="25" width="25" src="/assets/images/air_variables/'+meteorological_variable.icono+'"></td><td><strong> &nbsp; ' + meteorological_variable.name + ':</strong> '+feature.properties.z.toFixed(4)+' ('+ unit_meteo.nombre +')</td></tr>';
												layer.bindPopup(html_popup);
											});
										}
									};

									var breaks = [];
									for(i = 0; i <= 100; i = i + 1){
										breaks.push(i);
									}
									var isolined = turf.isolines(points, 'z', 100, breaks); // https://github.com/turf-junkyard/turf-isolines
									layerIsoline = L.geoJson(isolined, crimeGridStyle).addTo(map);

									// Leyenda para capa isoline
									legend_isoline = L.control({position: 'topright'});
									legend_isoline.onAdd = function (map) {
										var div = L.DomUtil.create('div', 'info legend legend_isoline fixed_width');
										div.innerHTML += '<strong>'+meteorological_variable.name+'</strong><br><br>';				
										Object.keys(legend_ranges_isoline).reverse().forEach(function(index){
											div.innerHTML += '<div class=""><i style="background:' + legend_ranges_isoline[index] + '"></i> ' + index + ' (' + unit_meteo.nombre + ')' + '</div>';
										});
										div.innerHTML += '</div>';
										return div;
									};
									legend_isoline.addTo(map);

									// Check checkbox "Leyenda Variable Meteorológica"
									$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', true);
									
								}
								
							}

							timedimension._update();

							// Uncheck checkbox "Leyenda Variable de Calidad del aire"
							$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').prop('checked', false);

						} else {

							// Uncheck checkbox "Leyenda Variable de Calidad del aire"
							$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(1) > div > input').prop('checked', false);
							// Uncheck checkbox "Leyenda Variable Meteorológica"
							$('#mapa > div.leaflet-control-container > div.leaflet-top.leaflet-left > div.leaflet-control-layers.leaflet-control > form > div.leaflet-control-layers-overlays > label:nth-child(2) > div > input').prop('checked', false);
						}

						appLoader.hide();
						
					}

				});


			});


			/* Gráficos, CalHeatmaps y AppTables */
			<?php if(count($receptors_dropdown) >= 2){ ?>
				<?php  $id_receptor = (array_key_exists(2, $receptors_dropdown)) ? 2 : array_keys($receptors_dropdown)[1]; ?>
				<?php if($id_receptor == 2){ // Si el receptor es Hotel Mina, marcarlo seleccionado ?>
					$('#receptor').val(2).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera estación receptora del dropdown ?>
					$('#receptor').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>
			
			var id_receptor = <?php echo ($receptor_num_model->id) ? $receptor_num_model->id : 0; ?>;

			/* Variable Calidad del Aire */

			// Gráfico
			var qual_receptor_data = []; // Datos
			var qual_receptor_categories = []; // Categorías
			var chart_qual_ranges = []; // Rangos
			
			// CalHeatMap
			var calheatmap_data_qual = []; // Datos
			var calheatmap_data_qual_final = []; 
			var calheatmap_qual_ranges = []; // Rangos

			// Colores y rangos de Alertas CalHeatMap
			var array_alerts_qual_calheatmap_colors = <?php echo json_encode($array_alerts_qual_calheatmap_colors); ?>;
			var array_alerts_qual_calheatmap_ranges = <?php echo json_encode($array_alerts_qual_calheatmap_ranges); ?>;


			// Datos pronóstico 72 hrs
			var array_receptor_qual_variable_values_p = <?php echo json_encode($array_receptor_qual_variable_values_p); ?>;
			var array_receptor_qual_variable_ranges_p = <?php echo json_encode($array_receptor_qual_variable_ranges_p); ?>;
			var qual_intervalo_confianza = <?php echo json_encode($array_qual_intervalo_confianza); ?>;
			var qual_porc_conf = <?php echo json_encode($array_qual_porc_conf); ?>;
			var array_receptor_qual_variable_formatted_dates = <?php echo json_encode($array_receptor_qual_variable_formatted_dates); ?>;


			// Alerta (colores y valores mínimos)
			var array_alerts_qual_chart = <?php echo json_encode($array_alerts_qual_chart); ?>;

			Object.keys(array_receptor_qual_variable_values_p).forEach(function(date, idx, array) {
				var values_p = array_receptor_qual_variable_values_p[date];
				
				var datetime = new Date(date);
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(values_p).forEach(function(time) {
					var value_p = parseFloat(values_p[time]);

					var hour = time.substring(5, 7);

					if(true){ 
						qual_receptor_data.push([day_name+" "+array_receptor_qual_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);

						qual_receptor_categories.push(day_short_name+" "+hour+" hrs");
					}
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
					calheatmap_data_qual[timestamp] = value_p;

					if(array_alerts_qual_calheatmap_ranges.includes(value_p.toString())){
						calheatmap_data_qual_final[timestamp] = value_p + 1;
					} else {
						calheatmap_data_qual_final[timestamp] = value_p;
					}

				});
			});

			Object.keys(array_receptor_qual_variable_ranges_p).forEach(function(date, idx, array) {
				var ranges_p = array_receptor_qual_variable_ranges_p[date];

				var datetime = new Date(date);
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(ranges_p).forEach(function(time) {
					var range_p = ranges_p[time];

					var hour = time.substring(5, 7);
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
					
					if (true){ 
						chart_qual_ranges[calheatmap_data_qual[timestamp]] = range_p;
					}
					calheatmap_qual_ranges[timestamp] = range_p;
				});
			});

			$('#chart_qual').highcharts({
				chart: {
					type: 'area',
					zoomType: 'x',
					panning: true,
					panKey: 'shift',
					/*scrollablePlotArea: {
						minWidth: 600
					},*/
					events: {
						load: function(){
							if (this.options.chart.forExport) {
								Highcharts.each(this.series, function (series) {
									series.update({
										dataLabels: {
											enabled: true,
										},
									}, false);
								});
								this.redraw();
							}
						}
					}
				},

				title: {
					text: (air_quality_variable != null) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
				},

				credits: {
					enabled: false
				},

				xAxis: {
					labels: {
						formatter: function() {
							if(this.pos < 24){
								return '<span style="color:black;font-weight:bold;">'+this.value+'</span>';
							}else{
								return this.value;
							}
						}
					},
					minRange: 5,
					title: {
						text: '<?php echo lang("hours"); ?>'
					},
					plotBands: [	//Franjas de color por turno
						{from: 0, to: 8, color: '#F0F0F0'},
						{from: 8, to: 20, color: '#F7F7F7'},
						{from: 20, to: 32, color: '#F0F0F0'},
						{from: 32, to: 44, color: '#F7F7F7'},
						{from: 44, to: 56, color: '#F0F0F0'},
						{from: 56, to: 68, color: '#F7F7F7'},
						{from: 68, to: 71, color: '#F0F0F0'},
					]
				},

				yAxis: {
					startOnTick: true,
					endOnTick: false,
					maxPadding: 0.35,
					title: {
						text: null
					},
					labels: {
						//format: '{value} ' + '(' + unit_qual.nombre + ')'
						//format: "{value:,." + decimal_numbers + "f}" + ' (' + unit_qual.nombre + ')'
						formatter: function(){
							if(air_quality_variable != null){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual.nombre + ')';
							} else {
								return '';
							}
							
						},
					},
					min: 0,
					max: 500
				},

				tooltip: {
					useHTML: true,
					//headerFormat: '<span style="font-size: 10px;">{point.key}</span> <br>',
					//pointFormat: '<span style="color:{point.color}">\u25CF</span> ' + '<?php echo lang("concentration"); ?>: ' + '{point.y} ' + unit_qual.nombre,
					formatter: function() {
						
						if(air_quality_variable){

							if(air_quality_variable.id == 9){

								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
									+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
									+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") <br>"
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
									+ numberFormat(qual_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
									+ numberFormat(qual_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator)
									+ ' (' + unit_qual.nombre + ') <br>'
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
									+ numberFormat(qual_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

							} else {
								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
									+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
									+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") ";
							}

						} else {
							return  '<?php echo lang("no_information_available"); ?>';
						}

					},
					shared: true
				},

				exporting: {
					filename: (air_quality_variable) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>',
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
					},
					chartOptions: {
						xAxis: [{
							categories: qual_receptor_categories,
							labels: {
								style: {
									fontSize: '9px'
								},
								tickInterval: 1
							}
						}]
					},
					sourceWidth: 1200
				},

				plotOptions: {
					area: {
						//size: 80,
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false,
							//format: '<b>{point.name}</b>: {point.y}',
							formatter: function(){
								return chart_qual_ranges[this.y];
							},
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
								fontSize: "9px",
								distance: -30
							},
							crop: false
						},
						showInLegend: true,
						format: "{y:,." + decimal_numbers + "f}",
					}
				},

				legend: {
					enabled: false
				},

				series: [{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: qual_receptor_data,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // transparencia para el area
					name: '',
					/*marker: {
						enabled: false,
						fillColor: '#0cba00'
					},*/
					//threshold: null,
					/*zones: [{
						color: "#3beb4c",
						value: 0
					},{
						color: "#d7e810",
						value: 350
					},{
						color: "#ed780c",
						value: 500
					},{
						color: "#ff000f",
						value: 650
					},{
						color: "#990b93",
						value: 800
					},{
						color: "#990b93"
					}]*/
					zones: array_alerts_qual_chart
				}
				]

			});

			if(air_quality_variable){
				if(air_quality_variable.id == 9){
					Highcharts.charts[0].addSeries({
						name: 'Range',
						data: qual_intervalo_confianza,
						type: 'arearange',
						lineWidth: 0,
						linkedTo: ':previous',
						color: Highcharts.getOptions().colors[0],
						fillOpacity: 0.3,
						zIndex: 0,
						marker: {
							enabled: false
						}
					});
				}
			}

			Highcharts.charts[0].xAxis[0].update({categories: qual_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);

			// CalHeatMap
			// Configuración de variables para fecha de inicio del CalHeatmap
			var first_datetime = "<?php echo ($first_datetime_qual_num) ? $first_datetime_qual_num : $first_datetime; ?>";
			var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
			var year = date.substring(0,4);
			var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
			var day = parseInt(date.substring(8,10));				
			var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

			
			
			var calheatmap_qual = new CalHeatMap();
			calheatmap_qual.init({
				itemSelector: "#calheatmap_qual",
				domain: "day",
				subDomain: "x_hour",
				range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
				cellSize: 30, // el tamaño de cada celda de hora
				displayLegend: true,
				domainGutter: 10, // distancia entre días 
				tooltip: true,
				verticalOrientation: ($(window).width() < 1070) ? true : false,
				start: new Date(year, month, day, hour),
				//domainLabelFormat: "%d-%m-%Y",// dependerá del formato de fecha del proyecto
				domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
				subDomainTextFormat: "%H",
				subDomainTitleFormat: {
					empty: "<?php echo lang("out_of_forecast_range"); ?>",
					//filled: "{date}, la concentración de "+ air_quality_variable.sigla +" se estima que será de {count} " + unit_qual.nombre
					filled: "{date}"
				},
				subDomainDateFormat: function(date) {
					var d = new Date(date);
					var h = d.getHours();
					h = ("0" + h).slice(-2);

					var datetime = d.getTime()/1000; // timestamp

					if(air_quality_variable){
						return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges[datetime] + " (" + unit_qual.nombre + ")";
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
				itemName: [unit_qual.nombre, unit_qual.nombre],
				//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
				legend: array_alerts_qual_calheatmap_ranges,
				legendTitleFormat: {
					//lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
					upper: "<?php echo lang("more_than"); ?> {max} ({name})"
				},
				legendHorizontalPosition: "center",
				legendMargin: [0, 0, 0, 0],
				data: calheatmap_data_qual_final,
				onComplete: function() { // https://php.developreference.com/article/19345650/cal-heatmap+-+legendColors+as+array+of+color+values%3F
					setTimeout(function(){
						/*['#ffadad','#ff9696','#ff8282','#fc6d6d','#ff5454','#f51818'].forEach(function(d,i){
							d3.selectAll("rect.r" + i).style("fill", d);
						});*/
						array_alerts_qual_calheatmap_colors.forEach(function(d,i){
							i++;
							d3.selectAll("div#calheatmap_qual rect.r" + i).style("fill", d);
							
							//$("div#calheatmap_qual rect").not(".r1").css("background-color", "#FFF"); 
							//d3.selectAll("div#calheatmap_qual rect:not(.r"+i+")").style("display", "none");
							//d3.selectAll("div#calheatmap_qual rect:not(.r"+i+")").style("display", "none");
							//
						});
						//$("div#calheatmap_qual rect").css("background-color", "#FFF");
						//d3.selectAll("div#calheatmap_qual rect:not(.r1)").style("display", "none");
						//d3.selectAll("div#calheatmap_qual rect:not(.r1)").style("fill", "#fff");
						
						//d3.selectAll("div#calheatmap_qual rect svg g rect:not(.r1)").text("");
						//$("li:not(.active)").css("background-color", "yellow" ); 
				
						//d3.selectAll("div#calheatmap_qual rect").style("fill", "#fff");
					}, 10);
				}
			});


			var id_air_quality_variable = (air_quality_variable) ? air_quality_variable.id : 0;
			$("#qual_receptor-table").appTable({
				source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_air_quality_variable + "/3", // Modelo Numérico
				columns: [
					{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
					{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
					{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
					// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
					// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
				],
				// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
				// 	var html_action_plan_content = aData[6];
				// 	$html_action_plan.popover(
				// 		{
				// 			container: 'body',
				// 			trigger:'hover',
				// 			placement: 'left',
				// 			title: '<?php echo lang("action_plan"); ?>',
				// 			html:true,
				// 			//content: $action_plan.attr("data-content")
				// 			content: html_action_plan_content
				// 		}
				// 	);
				// },
				order: [1, "asc"]

			});
		


			

			/* Variable Meteorológica */
			
			// Si la variable Meteorológica inicial es Velocidad del viento o Dirección del viento y hay datos para el receptor, Inicializar el Meteograma

			// Gráfico
			var vel_receptor_data = []; // Datos. Debe ser [[1,30.2],[3,234.6]] [velocidad, dirección] 
			var vel_receptor_categories = []; // Categorías
			var vel_receptor_categories_final = []; // Rangos
			var vel_receptor_categories_export = []; // Labels eje x en exportación

			// CalHeatMap
			var calheatmap_data_meteo = [];

			var chart_meteo_ranges = [];
			var calheatmap_meteo_ranges = [];

			// Datos pronóstico 72 hrs
			var array_receptor_meteo_data_values_p_vel = <?php echo json_encode($array_receptor_meteo_data_values_p_vel); ?>;
			var array_receptor_meteo_data_values_p_dir = <?php echo json_encode($array_receptor_meteo_data_values_p_dir); ?>;
			var array_receptor_meteo_data_ranges_p_vel = <?php echo json_encode($array_receptor_meteo_data_ranges_p_vel); ?>;
			var array_receptor_meteo_variable_formatted_dates = <?php echo json_encode($array_receptor_meteo_variable_formatted_dates); ?>;

			// Unidades de variables según configuración Unidades de Reporte
			var unit_meteo_vel = <?php echo json_encode($unit_meteo_vel); ?>;
			var unit_meteo_dir = <?php echo json_encode($unit_meteo_dir); ?>;

			// Alerta (colores y valores mínimos)
			var array_alerts_meteo_chart = <?php echo json_encode($array_alerts_meteo_chart); ?>;

			if(meteorological_variable.id == 1 || meteorological_variable.id == 2){

				Object.keys(array_receptor_meteo_data_values_p_vel).forEach(function(date, idx, array) {
					var values_p = array_receptor_meteo_data_values_p_vel[date];

					var datetime = new Date(date);
					var day_name = array_days_name[datetime.getUTCDay()];
					var day_short_name = array_days_short_name[datetime.getUTCDay()];

					Object.keys(values_p).forEach(function(time) {
						var value_p = parseFloat(values_p[time]);

						var hour = time.substring(5, 7);

						if(true){
							vel_receptor_categories.push(day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs");
							vel_receptor_categories_final[day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs"] = day_short_name+" "+hour+" hrs";
							vel_receptor_categories_export.push(day_short_name+" "+hour+" hrs");

							if(parseFloat(array_receptor_meteo_data_values_p_dir[date][time]) == 0 || value_p == 0){
								vel_receptor_data.push([0,0]);
							} else {
								vel_receptor_data.push([value_p, parseFloat(array_receptor_meteo_data_values_p_dir[date][time])]);
							}
						}
						
						var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
						calheatmap_data_meteo[timestamp] = value_p;
					});
				});

				Object.keys(array_receptor_meteo_data_ranges_p_vel).forEach(function(date, idx, array) {
					var ranges_p = array_receptor_meteo_data_ranges_p_vel[date];

					var datetime = new Date(date);
					var day_name = array_days_name[datetime.getUTCDay()];
					var day_short_name = array_days_short_name[datetime.getUTCDay()];

					Object.keys(ranges_p).forEach(function(time) {
						var range_p = ranges_p[time];

						var hour = time.substring(5, 7);
						var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
						
						if(true){
							chart_meteo_ranges[calheatmap_data_meteo[timestamp]] = range_p;
						}
						calheatmap_meteo_ranges[timestamp] = range_p;
					});
				});


				$('#chart_meteo').highcharts({
					
					chart: {
						type: 'area',
						zoomType: 'x',
						panning: true,
						panKey: 'shift',
						/*scrollablePlotArea: {
							minWidth: 600
						},*/
						events: {
							load: function(){
								//Highcharts.charts[1].xAxis[0].update({categories: vel_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);
								
								if (this.options.chart.forExport) {
									Highcharts.each(this.series, function (series) {
										series.update({
											dataLabels: {
												enabled: true,
											}
										}, false);
									});

									if(this.xAxis){
										Highcharts.each(this.xAxis, function (xAxis) {
											xAxis.update({
												offset: 20
											}, false);
										});
									}

									this.redraw();
								}
							}
						}
					},

					title: {
						text: '<?php echo lang("wind_speed_and_direction"); ?>'
					},

					credits: {
						enabled: false
					},

					xAxis: {
						//type: 'datetime',
						//offset: 40,
						offset: 20,
						//minRange: 5,
						title: {
							text: '<?php echo lang("hours"); ?>'
						},
						
						min: 0,
						max: 71,
						/*scrollbar: {
							enabled: true,
							showFull: true
						},*/
						//showFirstLabel: true
						labels: {
							formatter: function() {
								if(this.pos < 24){
									return '<span style="color:black;font-weight:bold;">'+ vel_receptor_categories_final[this.value] + '</span>';
								}else{
									return vel_receptor_categories_final[this.value];
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
					},

					yAxis: {
						startOnTick: true,
						endOnTick: false,
						maxPadding: 0.35,
						title: {
							text: null
						},
						labels: {
							formatter: function(){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_meteo_vel.nombre + ')';
							},
						},
						min: 0,
						max: 50
					},

					tooltip: {
						useHTML: true,
						//headerFormat:'{point.key} <br>',
						//pointFormat: 'Concentración de {point.y} ug/m3.',
						//pointFormat: '<?php echo lang("velocity"); ?> ' + '{point.value} ' + '<br> ' + '<?php echo lang("direction"); ?> ' + '{point.direction}',
						formatter: function() {

							if(this.points[1]){
								return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
									+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' +  '<?php echo lang("velocity"); ?>: ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo_vel.nombre + ')' +
									'<br/>' + '<span style="color:' + this.points[1].point.color + '">\u25CF</span> ' + '<?php echo lang("direction"); ?>: ' + numberFormat(this.points[1].point.direction, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_meteo_dir.nombre + ')';
							} else {

								return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
									+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' +  '<?php echo lang("velocity"); ?>: ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo_vel.nombre + ')';
							}

							

						},
						shared: true
					},

					exporting: {
						filename: '<?php echo lang("wind_speed_and_direction"); ?>',
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
						},
						chartOptions: {
							xAxis: [{
								categories: vel_receptor_categories_export,
								labels: {
									style: {
										fontSize: '9px'
									},
								},
							}]
						},
						sourceWidth: 1200
					},

					legend: {
						enabled: false
					},
					
					plotOptions: {
						series: {
							pointWidth: 50,
							fillOpacity: 0 // transparencia para el area
						},
						area: {
							//size: 80,
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false,
								//format: '<b>{point.name}</b>: {point.y}',
								formatter: function(){
									return chart_meteo_ranges[this.y];
								},
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
					
					series: [{
						type: 'area',
						keys: ['y', 'rotation'], // rotation is not used here
						data: vel_receptor_data,
						color: Highcharts.getOptions().colors[0],
						/*fillColor: {
							linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
							stops: [
								[0, Highcharts.getOptions().colors[0]],
								[
									1,
									Highcharts.color(Highcharts.getOptions().colors[0])
										.setOpacity(0.25).get()
								]
							]
						},*/
						pointPadding: 20,
						name: '<?php echo lang("velocity"); ?>',
						tooltip: {
							pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
								'{series.name}: {point.y} (' + unit_meteo_vel.nombre + ') <br/>'
						},
						states: {
							inactive: {
								opacity: 1
							}
						},
						zones: array_alerts_meteo_chart
					},{
						
						type: 'windbarb',
						data: vel_receptor_data,
						name: '<?php echo lang("direction"); ?>',
						scrollbar: {
							enabled: true
						},
						yOffset: -10,
						
						//vectorLength: 8,
						//lineWidth: 1,
						vectorLength: 15 ,
						lineWidth: 1,
						color: Highcharts.getOptions().colors[1],
						showInLegend: false,
						tooltip: {
							//valueSuffix: ' m/s'
							pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
								'{series.name}: {point.direction} (' + unit_meteo_dir.nombre + ') <br/>'
						},
						min: 0,
						max: 72
					}],

				});

				Highcharts.charts[1].xAxis[0].update({categories: vel_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);
				

				// CalHeatMap
				// Configuración de variables para fecha de inicio del CalHeatmap
				var first_datetime = "<?php echo ($first_datetime_meteo_num) ? $first_datetime_meteo_num : $first_datetime; ?>";
				var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
				var year = date.substring(0,4);
				var month =  parseInt(date.substring(5,7)) - 1;						// Puede ser del 1 al 12
				var day = parseInt(date.substring(8,10));				
				var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

				var array_alerts_meteo_calheatmap_colors = <?php echo json_encode($array_alerts_meteo_calheatmap_colors); ?>;
				var array_alerts_meteo_calheatmap_ranges = <?php echo json_encode($array_alerts_meteo_calheatmap_ranges); ?>;

				var calheatmap_meteo = new CalHeatMap();
				calheatmap_meteo.init({
					itemSelector: "#calheatmap_meteo",
					domain: "day",
					subDomain: "x_hour",
					range: 3, // en este caso la cantidad de días
					cellSize: 30, // el tamaño de cada celda de hora
					displayLegend: true,
					domainGutter: 10, // distancia entre días
					tooltip: true,
					verticalOrientation: ($(window).width() < 1070) ? true : false,
					//start: new Date(2020, 4, 1),
					//start: new Date(date),
					start: new Date(year, month, day, hour),
					domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
					subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
					subDomainTitleFormat: {
						empty: "<?php echo lang("out_of_forecast_range"); ?>",
						//filled: "{date}, la velocidad estimada de "+ meteorological_variable.sigla + " será de {count} " + unit_meteo.nombre
						filled: '{date}'
					},
					subDomainDateFormat: function(date) {

						var d = new Date(date);
						var h = d.getHours();
						h = ("0" + h).slice(-2);

						var datetime = d.getTime()/1000; // timestamp

						if(meteorological_variable){
							return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_f"); ?> " + unit_type_meteo.toLowerCase() + " <?php echo lang("estimated_of"); ?> "+ meteorological_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_meteo_ranges[datetime] + " (" + unit_meteo.nombre + ")";
						} else {
							return "<?php echo lang("no_information_available"); ?>"
						}

					},
					itemName: [unit_meteo.nombre, unit_meteo.nombre],
					//legend: [0, 2, 4, 6], // sacar minimo y máximo y crear escala de colores en base a esos valores
					legend: array_alerts_meteo_calheatmap_ranges,
					legendTitleFormat: {
						lower: (array_alerts_meteo_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
						inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
						upper: "<?php echo lang("more_than"); ?> {max} ({name})"
					},
					legendHorizontalPosition: "center",
					legendMargin: [0, 0, 0, 0],
					data: calheatmap_data_meteo,
					onComplete: function() {
						setTimeout(function(){
							/*['#ffadad','#ff9696','#ff8282','#fc6d6d','#ff5454','#f51818'].forEach(function(d,i){
								d3.selectAll("div#calheatmap_meteo rect.r" + i).style("fill", d);
							});*/
							array_alerts_meteo_calheatmap_colors.forEach(function(d,i){
								i++;
								d3.selectAll("div#calheatmap_meteo rect.r" + i).style("fill", d);
							});
						}, 10);
					}
				});



			} else {

				//Otras variables Meteorológicas
				
				// Datos pronóstico 72 hrs
				var array_receptor_meteo_data_values_p = <?php echo json_encode($array_receptor_meteo_data_values_p); ?>;
				var array_receptor_meteo_data_ranges_p = <?php echo json_encode($array_receptor_meteo_data_ranges_p); ?>;
				var array_receptor_meteo_variable_formatted_dates = <?php echo json_encode($array_receptor_meteo_variable_formatted_dates); ?>;

				var meteo_receptor_data = [];
				var meteo_receptor_categories = [];
				var meteo_receptor_categories_final = [];

				// CalHeatMap
				var calheatmap_data_meteo = [];

				var chart_meteo_ranges = [];
				var calheatmap_meteo_ranges = [];

				// Alerta (colores y valores mínimos)
				var array_alerts_meteo_chart = <?php echo json_encode($array_alerts_meteo_chart); ?>;

				Object.keys(array_receptor_meteo_data_values_p).forEach(function(date, idx, array) {
					var values_p = array_receptor_meteo_data_values_p[date];

					var datetime = new Date(date);
					var day_name = array_days_name[datetime.getUTCDay()];
					var day_short_name = array_days_short_name[datetime.getUTCDay()];

					Object.keys(values_p).forEach(function(time) {
						var value_p = parseFloat(values_p[time]);

						var hour = time.substring(5, 7);

						if(true){ 
							meteo_receptor_categories.push(day_short_name+" "+hour+" hrs");
							meteo_receptor_categories_final[day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs"] = day_short_name+" "+hour+" hrs";
							meteo_receptor_data.push([day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);
						}
						var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
						calheatmap_data_meteo[timestamp] = value_p;
						
					});
				});

				Object.keys(array_receptor_meteo_data_ranges_p).forEach(function(date, idx, array) {
					var ranges_p = array_receptor_meteo_data_ranges_p[date];

					var datetime = new Date(date);
					var day_name = array_days_name[datetime.getUTCDay()];
					var day_short_name = array_days_short_name[datetime.getUTCDay()];

					Object.keys(ranges_p).forEach(function(time) {
						var range_p = ranges_p[time];

						var hour = time.substring(5, 7);
						var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;

						if(true){
							chart_meteo_ranges[calheatmap_data_meteo[timestamp]] = range_p;
						}
						calheatmap_meteo_ranges[timestamp] = range_p;
					});
				});


				$('#chart_meteo').highcharts({
					chart: {
						//type: 'line',
						type: 'area',
						zoomType: 'x',
						panning: true,
						panKey: 'shift',
						/*scrollablePlotArea: {
							minWidth: 600
						},*/
						events: {
							load: function(){
								//Highcharts.charts[1].xAxis[0].update({categories: meteo_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);
								
								if (this.options.chart.forExport) {
									Highcharts.each(this.series, function (series) {
										series.update({
											dataLabels: {
												enabled: true,
											},
										}, false);
									});
									this.redraw();
								}
							}
						}
					},

					title: {
						text: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>'
					},

					credits: {
						enabled: false
					},

					xAxis: {
						offset: 20,
						//categories: meteo_receptor_categories,
						title: {
							text: '<?php echo lang("hours"); ?>'
						},
						min: 0,
						max: 71,
						labels: {
							formatter: function() {
								if(this.pos < 24){
									return '<span style="color:black;font-weight:bold;">'+ this.value + '</span>';
								}else{
									return this.value;
								}
							}
						},
						// labels: {
						// 	formatter: function() {
						// 		return meteo_receptor_categories_final[this.value];
						// 	},
						// }
						// gridLineWidth: 1,

						// tickInterval: 6
						
						plotBands: [	//Franjas de color por turno
							{from: 0, to: 8, color: '#F0F0F0'},
							{from: 8, to: 20, color: '#F7F7F7'},
							{from: 20, to: 32, color: '#F0F0F0'},
							{from: 32, to: 44, color: '#F7F7F7'},
							{from: 44, to: 56, color: '#F0F0F0'},
							{from: 56, to: 68, color: '#F7F7F7'},
							{from: 68, to: 71, color: '#F0F0F0'},
						]
					},

					yAxis: {
						startOnTick: true,
						endOnTick: false,
						maxPadding: 0.35,
						title: {
							text: null
						},
						labels: {
							format: (meteorological_variable) ? '{value} ' + '(' + unit_meteo.nombre + ')' : ''
						},
						min: 0,
						max: 50
					},

					tooltip: {
						useHTML: true,
						//headerFormat:'{point.key} <br>',
						//pointFormat: 'Concentración de {point.y} ug/m3.',
						//pointFormat: '<?php echo lang("velocity"); ?> ' + '{point.value} ' + '<br> ' + '<?php echo lang("direction"); ?> ' + '{point.direction}',
						formatter: function() {

							if(meteorological_variable){
								return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
									+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' + unit_type_meteo +': ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo.nombre + ')';
							} else {
								return  '<?php echo lang("no_information_available"); ?>';
							}

						},
						shared: true
					},

					exporting: {
						filename: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>',
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
						},
						chartOptions: {
							xAxis: [{
								categories: meteo_receptor_categories,
								labels: {
									style: {
										fontSize: '9px'
									},
									tickInterval: 1
								}
							}]
						},
						sourceWidth: 1200
					},

					legend: {
						enabled: false
					},
					
					plotOptions: {
						series: {
							pointWidth: 50,
							fillOpacity: 0 // transparencia para el area
						},
						area: {
							//size: 80,
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false,
								//format: '<b>{point.name}</b>: {point.y}',
								formatter: function(){
									return chart_meteo_ranges[this.y];
								},
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

					series: [{
						//type: 'area',
						//keys: ['y', 'rotation'], // rotation is not used here
						data: meteo_receptor_data,
						color: Highcharts.getOptions().colors[0],

						pointPadding: 20,
						name: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>',
						tooltip: {
							pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
								'{series.name}: {point.y} (' + unit_meteo.nombre + ') <br/>'
						},
						states: {
							inactive: {
								opacity: 1
							}
						},
						zones: array_alerts_meteo_chart
					}]

				});

				Highcharts.charts[1].xAxis[0].update({categories: meteo_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);


				// CalHeatMap
				// Configuración de variables para fecha de inicio del CalHeatmap
				var first_datetime = "<?php echo ($first_datetime_meteo_num) ? $first_datetime_meteo_num : $first_datetime; ?>";
				var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
				var year = date.substring(0,4);
				var month =  parseInt(date.substring(5,7)) - 1;						// Puede ser del 1 al 12
				var day = parseInt(date.substring(8,10));				
				var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

				var array_alerts_meteo_calheatmap_colors = <?php echo json_encode($array_alerts_meteo_calheatmap_colors); ?>;
				var array_alerts_meteo_calheatmap_ranges = <?php echo json_encode($array_alerts_meteo_calheatmap_ranges); ?>;

				var calheatmap_meteo = new CalHeatMap();
				calheatmap_meteo.init({
					itemSelector: "#calheatmap_meteo",
					domain: "day",
					subDomain: "x_hour",
					range: 3, // en este caso la cantidad de días
					cellSize: 30, // el tamaño de cada celda de hora
					displayLegend: true,
					domainGutter: 10, // distancia entre días
					tooltip: true,
					verticalOrientation: ($(window).width() < 1070) ? true : false,
					//start: new Date(2020, 4, 1),
					//start: new Date(date),
					start: new Date(year, month, day, hour),
					domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
					subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
					subDomainTitleFormat: {
						empty: "<?php echo lang("out_of_forecast_range"); ?>",
						//filled: "{date}, la velocidad estimada de "+ meteorological_variable.sigla + " será de {count} " + unit_meteo.nombre
						filled: '{date}'
					},
					subDomainDateFormat: function(date) {

						var d = new Date(date);
						var h = d.getHours();
						h = ("0" + h).slice(-2);

						var datetime = d.getTime()/1000; // timestamp

						if(meteorological_variable){
							return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_f"); ?> " + unit_type_meteo.toLowerCase() + " <?php echo lang("estimated_of"); ?> "+ meteorological_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_meteo_ranges[datetime] + " (" + unit_meteo.nombre + ")";
						} else {
							return "<?php echo lang("no_information_available"); ?>"
						}

					},
					itemName: [unit_meteo.nombre, unit_meteo.nombre],
					//legend: [0, 2, 4, 6], // sacar minimo y máximo y crear escala de colores en base a esos valores
					legend: array_alerts_meteo_calheatmap_ranges,
					legendTitleFormat: {
						lower: (array_alerts_meteo_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
						inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
						upper: "<?php echo lang("more_than"); ?> {max} ({name})"
					},
					legendHorizontalPosition: "center",
					legendMargin: [0, 0, 0, 0],
					data: calheatmap_data_meteo,
					onComplete: function() {
						setTimeout(function(){
							array_alerts_meteo_calheatmap_colors.forEach(function(d,i){
								i++;
								d3.selectAll("div#calheatmap_meteo rect.r" + i).style("fill", d);
							});
						}, 10);
					}
				});

			}

			var id_meteorological_variable = (meteorological_variable) ? meteorological_variable.id : 0;
			$("#meteo_receptor-table").appTable({
				source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_meteorological_variable + "/3", // Modelo Numérico
				columns: [
					{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
					{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
					{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
					// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
					// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
				],
				// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
				// 	var html_action_plan_content = aData[6];
				// 	$html_action_plan.popover(
				// 		{
				// 			container: 'body',
				// 			trigger:'hover',
				// 			placement: 'left',
				// 			title: '<?php echo lang("action_plan"); ?>',
				// 			html:true,
				// 			//content: $action_plan.attr("data-content")
				// 			content: html_action_plan_content
				// 		}
				// 	);
				// },
				order: [1, "asc"]
			});
			

			$("#receptor, #air_quality_variable, #meteorological_variable").on('change', function(){

				var id_receptor = $("#receptor").val();
				var id_air_quality_variable = $('#air_quality_variable').val();
				var id_meteorological_variable = $('#meteorological_variable').val();

				$.ajax({
					url: '<?php echo_uri("air_forecast_sectors/get_data_receptor"); ?>',
					type: 'post',
					dataType: 'json',
					data: {
						id_receptor: id_receptor,
						air_quality_variable: id_air_quality_variable,
						meteorological_variable: id_meteorological_variable,
						id_sector: id_sector,
						//first_date: "<?php echo $first_date_map; ?>",
						//last_date: "<?php echo $last_date_map; ?>"
					},beforeSend: function() {
						//$('#div_numerical_map').html('<div style="padding:20px;"><div class="circle-loader"></div><div>');
						appLoader.show();
					},
					success: function(respuesta){


						/* Variable Calidad del Aire */

						// Gráfico
						var qual_receptor_data = []; // Datos
						var qual_receptor_categories = []; // Categorías
						var chart_qual_ranges = []; // Rangos
						
						// CalHeatMap
						var calheatmap_data_qual = []; // Datos
						var calheatmap_data_qual_final = []; 
						var calheatmap_qual_ranges = []; // Rangos

						// Colores y rangos de Alertas CalHeatmap
						var array_alerts_qual_calheatmap_colors = respuesta.array_alerts_qual_calheatmap_colors;
						var array_alerts_qual_calheatmap_ranges = respuesta.array_alerts_qual_calheatmap_ranges;

						// Datos pronóstico 72 hrs
						var array_receptor_qual_variable_values_p = respuesta.array_receptor_qual_variable_values_p;
						var array_receptor_qual_variable_ranges_p = respuesta.array_receptor_qual_variable_ranges_p;
						var array_qual_intervalo_confianza = respuesta.array_qual_intervalo_confianza;
						var array_qual_porc_conf = respuesta.array_qual_porc_conf;
						var array_receptor_qual_variable_formatted_dates = respuesta.array_receptor_qual_variable_formatted_dates;

						// Alerta (colores y valores mínimos)
						var array_alerts_qual_chart = respuesta.array_alerts_qual_chart;

						// Variable
						var air_quality_variable = respuesta.air_quality_variable;

						// Unidad de variables según configuración Unidades de Reporte
						var unit_qual = respuesta.unit_qual;

						var first_datetime = respuesta.first_datetime;
						var first_datetime_qual = respuesta.first_datetime_qual;

						Object.keys(array_receptor_qual_variable_values_p).forEach(function(date, idx, array) {
							var values_p = array_receptor_qual_variable_values_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(values_p).forEach(function(time) {
								var value_p = parseFloat(values_p[time]);

								var hour = time.substring(5, 7);
								if(true){ 
									qual_receptor_data.push([day_name+" "+array_receptor_qual_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);
									qual_receptor_categories.push(day_short_name+" "+hour+" hrs");
								}
								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;

								calheatmap_data_qual[timestamp] = value_p;

								if(array_alerts_qual_calheatmap_ranges.includes(value_p.toString())){
									calheatmap_data_qual_final[timestamp] = value_p + 1;
								} else {
									calheatmap_data_qual_final[timestamp] = value_p;
								}
							
							});
						});

						Object.keys(array_receptor_qual_variable_ranges_p).forEach(function(date, idx, array) {
							var ranges_p = array_receptor_qual_variable_ranges_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(ranges_p).forEach(function(time) {
								var range_p = ranges_p[time];

								var hour = time.substring(5, 7);
								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
								
								if(true){
									chart_qual_ranges[calheatmap_data_qual[timestamp]] = range_p;
								}
								calheatmap_qual_ranges[timestamp] = range_p;
							});
						});


						// Actualización Gráfico (#chart_qual)

						// Datos
						Highcharts.charts[0].series[0].update({
							data: qual_receptor_data
						});

						// Título
						Highcharts.charts[0].title.update({
							text: (air_quality_variable) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
						});

						// Etiquetas Eje Y
						Highcharts.charts[0].yAxis[0].update({
							formatter: function(){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual.nombre + ')';
							}
						});
						
						// Rangos en la zona del Área (colores y valores mínimos de configuración de alertas)
						Highcharts.charts[0].series[0].update({
							data: qual_receptor_data,
							zones: array_alerts_qual_chart
						});

						// Intervalo de confianza
						if(Highcharts.charts[0].series[1]){
							Highcharts.charts[0].series[1].remove();
						}
						if(air_quality_variable){
							if(air_quality_variable.id == 9){
								Highcharts.charts[0].addSeries({
									name: 'Range',
									data: array_qual_intervalo_confianza,
									type: 'arearange',
									lineWidth: 0,
									linkedTo: ':previous',
									color: Highcharts.getOptions().colors[0],
									fillOpacity: 0.3,
									zIndex: 0,
									marker: {
										enabled: false
									}
								});
							}
						}

						// Tooltip
						Highcharts.charts[0].tooltip.update({
							formatter: function() {
								if(air_quality_variable){

									if(air_quality_variable.id == 9){

										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
											+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
											+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") <br>"
											+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
											+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
											+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator)
											+ ' (' + unit_qual.nombre + ') <br>'
											+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
											+ numberFormat(array_qual_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

									} else {
										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
											+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
											+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") ";
									}

								} else {
									return  '<?php echo lang("no_information_available"); ?>';
								}
							}
						});


						Highcharts.charts[0].update({
							plotOptions: {
								area: {
									dataLabels: {
										formatter: function(){
											return chart_qual_ranges[this.y];
										}
									},
								}
							}
						});

						
						$('#chart_qual').highcharts().options.exporting.chartOptions.xAxis[0].categories = qual_receptor_categories
						Highcharts.charts[0].xAxis[0].update({categories: qual_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
						$('#chart_qual').highcharts().redraw();
						
						
						// Actualización CalHeatMap

						// Configuración de variables para fecha de inicio del CalHeatmap
						var first_datetime = (first_datetime_qual) ? first_datetime_qual : first_datetime;
						var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
						var year = date.substring(0,4);
						var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
						var day = parseInt(date.substring(8,10));				
						var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

						

						// traigo el ancho de la leyenda del CalHeatmap para mantener su posición en la página al actualizar
						var graph_legend_x = $('div#calheatmap_qual > svg > svg.graph-legend').attr('x'); 

						$('#calheatmap_qual').empty();
						//delete calheatmap_qual;
						calheatmap_qual = new CalHeatMap();
						calheatmap_qual.init({
							itemSelector: "#calheatmap_qual",
							domain: "day",
							subDomain: "x_hour",
							range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
							cellSize: 30, // el tamaño de cada celda de hora
							displayLegend: true,
							domainGutter: 10, // distancia entre días 
							tooltip: true,
							verticalOrientation: ($(window).width() < 1070) ? true : false,
							start: new Date(year, month, day, hour),
							domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
							subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
							subDomainTitleFormat: {
								empty: "<?php echo lang("out_of_forecast_range"); ?>",
								//filled: "{date}, la concentración de "+ air_quality_variable.sigla +" se estima que será de {count} " + unit_qual.nombre
								filled: "{date}"
							},
							subDomainDateFormat: function(date) {
								var d = new Date(date);
								var h = d.getHours();
								h = ("0" + h).slice(-2);

								var datetime = d.getTime()/1000; // timestamp

								if(air_quality_variable){
									return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges[datetime] + " (" + unit_qual.nombre + ")";
								} else {
									return "<?php echo lang("no_information_available"); ?>"
								}
								
							},
							itemName: [unit_qual.nombre, unit_qual.nombre],
							//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
							legend: array_alerts_qual_calheatmap_ranges,
							legendTitleFormat: {
								//lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
								upper: "<?php echo lang("more_than"); ?> {max} ({name})"
							},
							legendHorizontalPosition: "center",
							legendMargin: [0, 0, 0, 0],
							data: calheatmap_data_qual_final,
							afterLoad: function() {
								setTimeout(function(){
									var x = 0;
									array_alerts_qual_calheatmap_colors.forEach(function(d,i){
										
										var cal = $("div#calheatmap_qual rect[x='" + x + "']"); 
										d3.selectAll(cal).style("fill", d);

										i++;
										d3.selectAll("div#calheatmap_qual rect.r" + i).style("fill", d);
										x = x+12;
									});

									// Conservar posición del CalHeatMap después de actualizar
									if($(window).width() > 1070){
										var domains = $('div#calheatmap_qual .graph').children('svg');
										var width = Number(domains.first().attr('width'));
										var x = 0;
										domains.each(function () {
											$(this).attr('x', x);
											x += width;
										});
										$('div#calheatmap_qual > svg > svg.graph-legend').attr('x', graph_legend_x);
									} else {
										$('div#calheatmap_qual > svg > svg.graph-legend').attr('x', "0");
									}

								}, 10);
							}
						});


						// Actualización AppTable
						$('#qual_receptor-table').DataTable().destroy();

						$("#qual_receptor-table").appTable({
							source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_air_quality_variable + "/3", // Modelo Numérico
							columns: [
								{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
								{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
								{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
								{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
								// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
								// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción	
							],
							// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
							// 	var html_action_plan_content = aData[6];
							// 	$html_action_plan.popover(
							// 		{
							// 			container: 'body',
							// 			trigger:'hover',
							// 			placement: 'left',
							// 			title: '<?php echo lang("action_plan"); ?>',
							// 			html:true,
							// 			//content: $action_plan.attr("data-content")
							// 			content: html_action_plan_content
							// 		}
							// 	);
							// },
							order: [1, "asc"]
						});



						/* Variable Meteorológica */

						// Gráfico
						var vel_receptor_data = []; // Datos. Debe ser [[1,30.2],[3,234.6]] [velocidad, dirección] 
						var vel_receptor_categories = []; // Categorías
						var vel_receptor_categories_final = []; // Rangos
						var vel_receptor_categories_export = []; // Labels eje x en exportación

						// CalHeatMap
						var calheatmap_data_meteo = [];

						var chart_meteo_ranges = [];
						var calheatmap_meteo_ranges = [];

						// Datos pronóstico 72 hrs
						var array_receptor_meteo_data_values_p_vel = respuesta.array_receptor_meteo_data_values_p_vel;
						var array_receptor_meteo_data_values_p_dir = respuesta.array_receptor_meteo_data_values_p_dir;
						var array_receptor_meteo_data_ranges_p_vel = respuesta.array_receptor_meteo_data_ranges_p_vel;
						var array_receptor_meteo_variable_formatted_dates = respuesta.array_receptor_meteo_variable_formatted_dates;

						// Unidades de variables según configuración Unidades de Reporte
						var unit_meteo_vel = respuesta.unit_meteo_vel;
						var unit_meteo_dir = respuesta.unit_meteo_dir;
						var unit_meteo = respuesta.unit_meteo;
						var unit_type_meteo = respuesta.unit_type_meteo;


						// Alerta (colores y valores mínimos)
						var array_alerts_meteo_chart = respuesta.array_alerts_meteo_chart;

						// Variable
						var meteorological_variable = respuesta.meteorological_variable;
						var variable_vel_viento = respuesta.variable_vel_viento;

						var first_datetime = respuesta.first_datetime;
						var first_datetime_meteo = respuesta.first_datetime_meteo;

						if(id_meteorological_variable == 1 || id_meteorological_variable == 2){

							Object.keys(array_receptor_meteo_data_values_p_vel).forEach(function(date, idx, array) {
								var values_p = array_receptor_meteo_data_values_p_vel[date];

								var datetime = new Date(date);
								var day_name = array_days_name[datetime.getUTCDay()];
								var day_short_name = array_days_short_name[datetime.getUTCDay()];

								Object.keys(values_p).forEach(function(time) {
									var value_p = parseFloat(values_p[time]);

									var hour = time.substring(5, 7);

									if(true){ 
										vel_receptor_categories.push(day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs");
										vel_receptor_categories_final[day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs"] = day_short_name+" "+hour+" hrs";
										vel_receptor_categories_export.push(day_short_name+" "+hour+" hrs");

										if(parseFloat(array_receptor_meteo_data_values_p_dir[date][time]) == 0 || value_p == 0){
											vel_receptor_data.push([0,0]);
										} else {
											vel_receptor_data.push([value_p, parseFloat(array_receptor_meteo_data_values_p_dir[date][time])]);
										}
									}

									var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
									calheatmap_data_meteo[timestamp] = value_p;
								});
							});

							Object.keys(array_receptor_meteo_data_ranges_p_vel).forEach(function(date, idx, array) {
								var ranges_p = array_receptor_meteo_data_ranges_p_vel[date];

								var datetime = new Date(date);
								var day_name = array_days_name[datetime.getUTCDay()];
								var day_short_name = array_days_short_name[datetime.getUTCDay()];

								Object.keys(ranges_p).forEach(function(time) {
									var range_p = ranges_p[time];

									var hour = time.substring(5, 7);
									var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
									
									if(true){
										chart_meteo_ranges[calheatmap_data_meteo[timestamp]] = range_p;
									}
									calheatmap_meteo_ranges[timestamp] = range_p;
								});
							});

							// Actualización Gráfico (#chart_meteo)
							$('#chart_meteo').highcharts({
								
								chart: {
									type: 'area',
									zoomType: 'x',
									panning: true,
									panKey: 'shift',
									/*scrollablePlotArea: {
										minWidth: 600
									},*/
									events: {
										load: function(){
											
											this.xAxis[0].update({categories: vel_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);
											
											this.update({
												exporting: {
													chartOptions: {
														xAxis: [{
															categories: vel_receptor_categories_export
														}]
													}
												},
											});

											if (this.options.chart.forExport) {
												Highcharts.each(this.series, function (series) {
													series.update({
														dataLabels: {
															enabled: true,
														}
													}, false);
												});

												if(this.xAxis){
													Highcharts.each(this.xAxis, function (xAxis) {
														xAxis.update({
															offset: 20
														}, false);
													});
												}

												this.redraw();
											}

										}
									}
								},

								title: {
									text: '<?php echo lang("wind_speed_and_direction"); ?>'
								},

								credits: {
									enabled: false
								},

								xAxis: {
									//type: 'datetime',
									//offset: 40,
									offset: 20,
									//minRange: 5,
									title: {
										text: '<?php echo lang("hours"); ?>'
									},
									
									min: 0,
									max: 71,
									/*scrollbar: {
										enabled: true,
										showFull: true
									},*/
									//showFirstLabel: true
									labels: {
										formatter: function() {
											if(this.pos < 24){
												return '<span style="color:black;font-weight:bold;">'+vel_receptor_categories_final[this.value]+'</span>';
											}else{
												return vel_receptor_categories_final[this.value];
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
									]
								},

								yAxis: {
									startOnTick: true,
									endOnTick: false,
									maxPadding: 0.35,
									title: {
										text: null
									},
									labels: {
										formatter: function(){
											return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_meteo_vel.nombre + ')';
										},
									},
									min: 0,
									max: 50
								},

								tooltip: {
									useHTML: true,
									//headerFormat:'{point.key} <br>',
									//pointFormat: 'Concentración de {point.y} ug/m3.',
									//pointFormat: '<?php echo lang("velocity"); ?> ' + '{point.value} ' + '<br> ' + '<?php echo lang("direction"); ?> ' + '{point.direction}',
									formatter: function() {

										if(this.points[1]){
											return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
												+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' +  '<?php echo lang("velocity"); ?>: ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo_vel.nombre + ')' +
												'<br/>' + '<span style="color:' + this.points[1].point.color + '">\u25CF</span> ' + '<?php echo lang("direction"); ?>: ' + numberFormat(this.points[1].point.direction, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_meteo_dir.nombre + ')';
										} else {

											return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
												+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' +  '<?php echo lang("velocity"); ?>: ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo_vel.nombre + ')';
										}

										

									},
									shared: true
								},

								exporting: {
									filename: '<?php echo lang("wind_speed_and_direction"); ?>',
									buttons: {
										contextButton: {
											menuItems: [{
												text: "<?php echo lang('export_to_png'); ?>",
												onclick: function() {

													this.update({
														exporting: {
															chartOptions: {
																xAxis: [{
																	categories: vel_receptor_categories_export
																}]
															}
														},
													})
													this.exportChart();

												},
												separator: false
											}]
										}
									},
									chartOptions: {
										xAxis: [{
											categories: vel_receptor_categories_export,
											labels: {
												style: {
													fontSize: '9px'
												},
												tickInterval: 1
											}
										}]
									},
									sourceWidth: 1200
								},

								legend: {
									enabled: false
								},
								
								plotOptions: {
									series: {
										pointWidth: 50,
										fillOpacity: 0 // transparencia para el area
									},
									area: {
										//size: 80,
										allowPointSelect: true,
										cursor: 'pointer',
										dataLabels: {
											enabled: false,
											//format: '<b>{point.name}</b>: {point.y}',
											formatter: function(){
												return chart_meteo_ranges[this.y];
											},
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
								
								series: [{
									type: 'area',
									keys: ['y', 'rotation'], // rotation is not used here
									data: vel_receptor_data,
									color: Highcharts.getOptions().colors[0],
									/*fillColor: {
										linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
										stops: [
											[0, Highcharts.getOptions().colors[0]],
											[
												1,
												Highcharts.color(Highcharts.getOptions().colors[0])
													.setOpacity(0.25).get()
											]
										]
									},*/
									pointPadding: 20,
									name: '<?php echo lang("velocity"); ?>',
									tooltip: {
										pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
											'{series.name}: {point.y} (' + unit_meteo_vel.nombre + ') <br/>'
									},
									states: {
										inactive: {
											opacity: 1
										}
									},
									zones: array_alerts_meteo_chart
								},{
									
									type: 'windbarb',
									data: vel_receptor_data,
									name: '<?php echo lang("direction"); ?>',
									scrollbar: {
										enabled: true
									},
									yOffset: -10,
									
									//vectorLength: 8,
									//lineWidth: 1,
									vectorLength: 15 ,
									lineWidth: 1,
									color: Highcharts.getOptions().colors[1],
									showInLegend: false,
									tooltip: {
										//valueSuffix: ' m/s'
										pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
											'{series.name}: {point.direction} (' + unit_meteo_dir.nombre + ') <br/>'
									},
									min: 0,
									max: 72
								}],

							});



							// Actualización CalHeatMap
							// Configuración de variables para fecha de inicio del CalHeatmap
							var first_datetime = (first_datetime_meteo) ? first_datetime_meteo : first_datetime;
							
							var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
							var year = date.substring(0,4);
							var month =  parseInt(date.substring(5,7)) - 1;						// Puede ser del 1 al 12
							var day = parseInt(date.substring(8,10));				
							var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

							var array_alerts_meteo_calheatmap_colors = respuesta.array_alerts_meteo_calheatmap_colors;
							var array_alerts_meteo_calheatmap_ranges = respuesta.array_alerts_meteo_calheatmap_ranges;

							// traigo el ancho de la leyenda del CalHeatmap para mantener su posición en la página al actualizar
							var graph_legend_x = $('div#calheatmap_meteo > svg > svg.graph-legend').attr('x'); 

							$('#calheatmap_meteo').empty();
							var calheatmap_meteo = new CalHeatMap();
							calheatmap_meteo.init({
								itemSelector: "#calheatmap_meteo",
								domain: "day",
								subDomain: "x_hour",
								range: 3, // en este caso la cantidad de días
								cellSize: 30, // el tamaño de cada celda de hora
								displayLegend: true,
								domainGutter: 10, // distancia entre días
								tooltip: true,
								verticalOrientation: ($(window).width() < 1070) ? true : false,
								//start: new Date(2020, 4, 1),
								//start: new Date(date),
								start: new Date(year, month, day, hour),
								domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
								subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
								subDomainTitleFormat: {
									empty: "<?php echo lang("out_of_forecast_range"); ?>",
									//filled: "{date}, la velocidad estimada de "+ meteorological_variable.sigla + " será de {count} " + unit_meteo.nombre
									filled: '{date}'
								},
								subDomainDateFormat: function(date) {

									var d = new Date(date);
									var h = d.getHours();
									h = ("0" + h).slice(-2);

									var datetime = d.getTime()/1000; // timestamp

									if(meteorological_variable){
										return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_f"); ?> " + unit_type_meteo.toLowerCase() + " <?php echo lang("estimated_of"); ?> "+ variable_vel_viento.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_meteo_ranges[datetime] + " (" + unit_meteo_vel.nombre + ")";
									} else {
										return "<?php echo lang("no_information_available"); ?>"
									}

								},
								itemName: [unit_meteo.nombre, unit_meteo.nombre],
								//legend: [0, 2, 4, 6], // sacar minimo y máximo y crear escala de colores en base a esos valores
								legend: array_alerts_meteo_calheatmap_ranges,
								legendTitleFormat: {
									lower: (array_alerts_meteo_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
									inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
									upper: "<?php echo lang("more_than"); ?> {max} ({name})"
								},
								legendHorizontalPosition: "center",
								legendMargin: [0, 0, 0, 0],
								data: calheatmap_data_meteo,
								onComplete: function() {
									setTimeout(function(){
										array_alerts_meteo_calheatmap_colors.forEach(function(d,i){
											i++;
											d3.selectAll("div#calheatmap_meteo rect.r" + i).style("fill", d);
										});

										// Conservar posición del CalHeatMap después de actualizar
										if($(window).width() > 1070){
											// Conservar posición del CalHeatMap después de actualizar
											var domains = $('div#calheatmap_meteo .graph').children('svg');
											var width = Number(domains.first().attr('width'));
											var x = 0;
											domains.each(function () {
												$(this).attr('x', x);
												x += width;
											});
											$('div#calheatmap_meteo > svg > svg.graph-legend').attr('x', graph_legend_x);
										} else {
											$('div#calheatmap_meteo > svg > svg.graph-legend').attr('x', "0");
										}

									}, 10);
								}
							});

							// Actualización AppTable
							$('#meteo_receptor-table').DataTable().destroy();

							$("#meteo_receptor-table").appTable({
								source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/1" + "/3", // Modelo Numérico
								columns: [
									{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
									{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
									{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
									{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
									{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
									{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
									// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
									// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
								],
								// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
								// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
								// 	var html_action_plan_content = aData[6];
								// 	$html_action_plan.popover(
								// 		{
								// 			container: 'body',
								// 			trigger:'hover',
								// 			placement: 'left',
								// 			title: '<?php echo lang("action_plan"); ?>',
								// 			html:true,
								// 			//content: $action_plan.attr("data-content")
								// 			content: html_action_plan_content
								// 		}
								// 	);
								// },
								order: [1, "asc"]
							});


						} else {


							// Datos pronóstico 72 hrs
							var array_receptor_meteo_data_values_p = respuesta.array_receptor_meteo_data_values_p;
							var array_receptor_meteo_data_ranges_p = respuesta.array_receptor_meteo_data_ranges_p;

							var array_receptor_meteo_variable_formatted_dates = respuesta.array_receptor_meteo_variable_formatted_dates;

							var meteo_receptor_data = [];
							var meteo_receptor_categories = [];
							var meteo_receptor_categories_final = [];
							var meteo_receptor_categories_final_2 = [];

							// CalHeatMap
							var calheatmap_data_meteo = [];

							var chart_meteo_ranges = [];
							var calheatmap_meteo_ranges = [];

							// Alerta (colores y valores mínimos)
							var array_alerts_meteo_chart = respuesta.array_alerts_meteo_chart;

							var first_datetime = respuesta.first_datetime;
							var first_datetime_meteo = respuesta.first_datetime_meteo;

							Object.keys(array_receptor_meteo_data_values_p).forEach(function(date, idx, array) {
								var values_p = array_receptor_meteo_data_values_p[date];

								var datetime = new Date(date);
								var day_name = array_days_name[datetime.getUTCDay()];
								var day_short_name = array_days_short_name[datetime.getUTCDay()];

								Object.keys(values_p).forEach(function(time) {
									var value_p = parseFloat(values_p[time]);

									var hour = time.substring(5, 7);
									if(true){
										meteo_receptor_categories.push(day_short_name+" "+hour+" hrs");
										meteo_receptor_categories_final[day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs"] = day_short_name+" "+hour+" hrs";
										meteo_receptor_categories_final_2.push(day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs");
										meteo_receptor_data.push([day_name+" "+array_receptor_meteo_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);
									}
									var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
									calheatmap_data_meteo[timestamp] = value_p;
									
								});
							});

							Object.keys(array_receptor_meteo_data_ranges_p).forEach(function(date, idx, array) {
								var ranges_p = array_receptor_meteo_data_ranges_p[date];

								var datetime = new Date(date);
								var day_name = array_days_name[datetime.getUTCDay()];
								var day_short_name = array_days_short_name[datetime.getUTCDay()];

								Object.keys(ranges_p).forEach(function(time) {
									var range_p = ranges_p[time];

									var hour = time.substring(5, 7);
									var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;

									if(true){
										chart_meteo_ranges[calheatmap_data_meteo[timestamp]] = range_p;
									}
									calheatmap_meteo_ranges[timestamp] = range_p;
								});
							});

							
							// Actualización Gráfico (#chart_meteo)
							$('#chart_meteo').highcharts({
								chart: {
									//type: 'line',
									type: 'area',
									zoomType: 'x',
									panning: true,
									panKey: 'shift',
									/*scrollablePlotArea: {
										minWidth: 600
									},*/
									events: {
										load: function(){

											this.xAxis[0].update({categories: meteo_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1}, true);

											if (this.options.chart.forExport) {
												Highcharts.each(this.series, function (series) {
													series.update({
														dataLabels: {
															enabled: true,
														},
													}, false);
												});
												this.redraw();
											}
										}
									}
								},

								title: {
									text: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>'
								},

								credits: {
									enabled: false
								},

								xAxis: {
									offset: 20,
									//categories: meteo_receptor_categories,
									title: {
										text: '<?php echo lang("hours"); ?>'
									},
									min: 0,
									max: 71,
									labels: {
										formatter: function() {
											if(this.pos < 24){
												return '<span style="color:black;font-weight:bold;">'+this.value+'</span>';
											}else{
												return this.value;
											}
										}
									},
									//labels: {
										//formatter: function() {
											//return meteo_receptor_categories_final[this.value];
										//},
									//}
									//gridLineWidth: 1,

									//tickInterval: 6
									plotBands: [	//Franjas de color por turno
										{from: 0, to: 8, color: '#F0F0F0'},
										{from: 8, to: 20, color: '#F7F7F7'},
										{from: 20, to: 32, color: '#F0F0F0'},
										{from: 32, to: 44, color: '#F7F7F7'},
										{from: 44, to: 56, color: '#F0F0F0'},
										{from: 56, to: 68, color: '#F7F7F7'},
										{from: 68, to: 71, color: '#F0F0F0'},
									]
								},

								yAxis: {
									startOnTick: true,
									endOnTick: false,
									maxPadding: 0.35,
									title: {
										text: null
									},
									labels: {
										format: (meteorological_variable) ? '{value} ' + '(' + unit_meteo.nombre + ')' : ''
									},
									min: 0,
									max: 50
								},

								tooltip: {
									useHTML: true,
									//headerFormat:'{point.key} <br>',
									//pointFormat: 'Concentración de {point.y} ug/m3.',
									//pointFormat: '<?php echo lang("velocity"); ?> ' + '{point.value} ' + '<br> ' + '<?php echo lang("direction"); ?> ' + '{point.direction}',
									formatter: function() {

										if(meteorological_variable){
											return  '<span style="font-size:10px">' + this.points[0].key + '</span><br/>'
												+ '<span style="color:' + this.points[0].point.color + '">\u25CF</span> ' + unit_type_meteo +': ' + chart_meteo_ranges[this.points[0].y] + ' (' + unit_meteo.nombre + ')';
										} else {
											return  '<?php echo lang("no_information_available"); ?>';
										}

									},
									shared: true
								},

								exporting: {
									filename: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>',
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
									},
									chartOptions: {
										xAxis: [{
											categories: meteo_receptor_categories,
											labels: {
												style: {
													fontSize: '9px'
												},
												tickInterval: 1
											}
										}]
									},
									sourceWidth: 1200
								},

								legend: {
									enabled: false
								},
								
								plotOptions: {
									series: {
										pointWidth: 50,
										fillOpacity: 0 // transparencia para el area
									},
									area: {
										//size: 80,
										allowPointSelect: true,
										cursor: 'pointer',
										dataLabels: {
											enabled: false,
											//format: '<b>{point.name}</b>: {point.y}',
											formatter: function(){
												return chart_meteo_ranges[this.y];
											},
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

								series: [{
									//type: 'area',
									//keys: ['y', 'rotation'], // rotation is not used here
									data: meteo_receptor_data,
									color: Highcharts.getOptions().colors[0],

									pointPadding: 20,
									name: (meteorological_variable) ? meteorological_variable.name : '<?php echo lang("meteorological_variable"); ?>',
									tooltip: {
										pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
											'{series.name}: {point.y} (' + unit_meteo.nombre + ') <br/>'
									},
									states: {
										inactive: {
											opacity: 1
										}
									},
									zones: array_alerts_meteo_chart
								}]

							});

							// Actualización CalHeatMap

							// Configuración de variables para fecha de inicio del CalHeatmap
							var first_datetime = (first_datetime_meteo) ? first_datetime_meteo : first_datetime;
							
							var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
							var year = date.substring(0,4);
							var month =  parseInt(date.substring(5,7)) - 1;						// Puede ser del 1 al 12
							var day = parseInt(date.substring(8,10));				
							var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

							var array_alerts_meteo_calheatmap_colors = respuesta.array_alerts_meteo_calheatmap_colors;
							var array_alerts_meteo_calheatmap_ranges = respuesta.array_alerts_meteo_calheatmap_ranges;

							// traigo el ancho de la leyenda del CalHeatmap para mantener su posición en la página al actualizar
							var graph_legend_x = $('div#calheatmap_meteo > svg > svg.graph-legend').attr('x'); 

							$('#calheatmap_meteo').empty();
							var calheatmap_meteo = new CalHeatMap();
							calheatmap_meteo.init({
								itemSelector: "#calheatmap_meteo",
								domain: "day",
								subDomain: "x_hour",
								range: 3, // en este caso la cantidad de días
								cellSize: 30, // el tamaño de cada celda de hora
								displayLegend: true,
								domainGutter: 10, // distancia entre días
								tooltip: true,
								verticalOrientation: ($(window).width() < 1070) ? true : false,
								//start: new Date(2020, 4, 1),
								//start: new Date(date),
								start: new Date(year, month, day, hour),
								domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
								subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
								subDomainTitleFormat: {
									empty: "<?php echo lang("out_of_forecast_range"); ?>",
									//filled: "{date}, la velocidad estimada de "+ meteorological_variable.sigla + " será de {count} " + unit_meteo.nombre
									filled: '{date}'
								},
								subDomainDateFormat: function(date) {

									var d = new Date(date);
									var h = d.getHours();
									h = ("0" + h).slice(-2);

									var datetime = d.getTime()/1000; // timestamp

									if(meteorological_variable){
										return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_f"); ?> " + unit_type_meteo.toLowerCase() + " <?php echo lang("estimated_of"); ?> "+ meteorological_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_meteo_ranges[datetime] + " (" + unit_meteo.nombre + ")";
									} else {
										return "<?php echo lang("no_information_available"); ?>"
									}

								},
								itemName: [unit_meteo.nombre, unit_meteo.nombre],
								//legend: [0, 2, 4, 6], // sacar minimo y máximo y crear escala de colores en base a esos valores
								legend: array_alerts_meteo_calheatmap_ranges,
								legendTitleFormat: {
									lower: (array_alerts_meteo_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
									inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
									upper: "<?php echo lang("more_than"); ?> {max} ({name})"
								},
								legendHorizontalPosition: "center",
								legendMargin: [0, 0, 0, 0],
								data: calheatmap_data_meteo,
								onComplete: function() {
									setTimeout(function(){
										array_alerts_meteo_calheatmap_colors.forEach(function(d,i){
											i++;
											d3.selectAll("div#calheatmap_meteo rect.r" + i).style("fill", d);
										});

										// Conservar posición del CalHeatMap después de actualizar
										if($(window).width() > 1070){
											// Conservar posición del CalHeatMap después de actualizar
											var domains = $('div#calheatmap_meteo .graph').children('svg');
											var width = Number(domains.first().attr('width'));
											var x = 0;
											domains.each(function () {
												$(this).attr('x', x);
												x += width;
											});
											$('div#calheatmap_meteo > svg > svg.graph-legend').attr('x', graph_legend_x);
										} else {
											$('div#calheatmap_meteo > svg > svg.graph-legend').attr('x', "0");
										}
										

									}, 10);
								}
							});


							// Actualización AppTable
							$('#meteo_receptor-table').DataTable().destroy();

							$("#meteo_receptor-table").appTable({
								source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_meteorological_variable + "/3", // Modelo Numérico
								columns: [
									{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
									{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
									{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
									{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
									{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
									{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
									// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
									// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
								],
								// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
								// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
								// 	var html_action_plan_content = aData[6];
								// 	$html_action_plan.popover(
								// 		{
								// 			container: 'body',
								// 			trigger:'hover',
								// 			placement: 'left',
								// 			title: '<?php echo lang("action_plan"); ?>',
								// 			html:true,
								// 			//content: $action_plan.attr("data-content")
								// 			content: html_action_plan_content
								// 		}
								// 	);
								// },
								order: [1, "asc"]
							});
							
						}

						appLoader.hide();
						
					}

				});

			});


		<?php } ?>

		// Si el Sector tiene el modelo Neuronal (id 2)
		<?php if(in_array(2, $id_models_of_sector)){ ?>

			<?php if(count($receptors_stat_model_dropdown) >= 2){ ?>
				<?php  $id_receptor = (array_key_exists(2, $receptors_stat_model_dropdown)) ? 2 : array_keys($receptors_stat_model_dropdown)[1]; ?>
				<?php if($id_receptor == 2){ // Si el receptor es Hotel Mina, marcarlo seleccionado ?>
					$('#receptor_stat_model').val(2).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera estación receptora del dropdown ?>
					$('#receptor_stat_model').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>

			<?php if(count($air_quality_variables_stat_model_dropdown) >= 2){ ?>
				<?php  $id_variable = (array_key_exists(9, $air_quality_variables_stat_model_dropdown)) ? 9 : array_keys($air_quality_variables_stat_model_dropdown)[1]; ?>
				<?php if($id_variable == 9){ // Si la variable es PM10, marcarla seleccionada ?>
					$('#air_quality_variable_stat_model').val(9).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera variable del dropdown ?>
					$('#air_quality_variable_stat_model').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>

			// Objeto variable Calidad del aire inicial
			var air_quality_variable_stat_model = <?php echo json_encode($air_quality_variable_stat_model); ?>;

			// Unidades de variable según configuración Unidades de Reporte
			var unit_qual_stat_model = <?php echo json_encode($unit_qual_stat_model); ?>;
			var unit_type_qual_stat_model = <?php echo json_encode($unit_type_qual_stat_model); ?>;

			var id_sector = <?php echo $sector_info->id; ?>;
			var id_receptor_stat_model = $("#receptor_stat_model").val();

			// Gráfico
			var qual_receptor_data_stat_model = []; // Datos
			var qual_receptor_categories_stat_model = []; // Categorías
			var chart_qual_ranges_stat_model = []; // Rangos
			
			// CalHeatMap
			var calheatmap_data_qual_stat_model = []; // Datos
			var calheatmap_data_qual_stat_model_final = []; 
			var calheatmap_qual_ranges_stat_model = []; // Rangos

			// Colores y rangos de Alertas CalHeatmap
			var array_alerts_qual_calheatmap_colors_stat_model = <?php echo json_encode($array_alerts_qual_calheatmap_colors_stat_model); ?>;
			var array_alerts_qual_calheatmap_ranges_stat_model = <?php echo json_encode($array_alerts_qual_calheatmap_ranges_stat_model); ?>;

			// Datos pronóstico 72 hrs
			var array_receptor_qual_stat_model_values_p = <?php echo json_encode($array_receptor_qual_stat_model_values_p); ?>;
			var array_receptor_qual_stat_model_ranges_p = <?php echo json_encode($array_receptor_qual_stat_model_ranges_p); ?>;
			var qual_stat_intervalo_confianza = <?php echo json_encode($array_qual_stat_intervalo_confianza); ?>;
			var qual_stat_porc_conf = <?php echo json_encode($array_qual_stat_porc_conf); ?>;
			var array_receptor_qual_stat_formatted_dates = <?php echo json_encode($array_receptor_qual_stat_formatted_dates); ?>;

			// Alerta (colores y valores mínimos)
			var array_alerts_qual_chart_stat_model = <?php echo json_encode($array_alerts_qual_chart_stat_model); ?>;

			Object.keys(array_receptor_qual_stat_model_values_p).forEach(function(date, idx, array) {
				var values_p = array_receptor_qual_stat_model_values_p[date];

				var datetime = new Date(date);
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(values_p).forEach(function(time) {
					var value_p = parseFloat(values_p[time]);

					var hour = time.substring(5, 7);
					if(true){
						qual_receptor_data_stat_model.push([day_name+" "+array_receptor_qual_stat_formatted_dates[date]+" "+hour+" hrs", value_p]);
						qual_receptor_categories_stat_model.push(day_short_name+" "+hour+" hrs");
					}
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
					calheatmap_data_qual_stat_model[timestamp] = value_p;

					if(array_alerts_qual_calheatmap_ranges_stat_model.includes(value_p.toString())){
						calheatmap_data_qual_stat_model_final[timestamp] = value_p + 1;
					} else {
						calheatmap_data_qual_stat_model_final[timestamp] = value_p;
					}

				});
			});

			Object.keys(array_receptor_qual_stat_model_ranges_p).forEach(function(date, idx, array) {
				var ranges_p = array_receptor_qual_stat_model_ranges_p[date];

				var datetime = new Date(date);
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(ranges_p).forEach(function(time) {
					var range_p = ranges_p[time];

					var hour = time.substring(5, 7);
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;

					if(true){
						chart_qual_ranges_stat_model[calheatmap_data_qual_stat_model[timestamp]] = range_p;
					}
					calheatmap_qual_ranges_stat_model[timestamp] = range_p;
				});
			});


			$('#chart_qual_stat_model').highcharts({
				chart: {
					type: 'area',
					zoomType: 'x',
					panning: true,
					panKey: 'shift',
					/*scrollablePlotArea: {
						minWidth: 600
					},*/
					events: {
						load: function(){

							// Si existen los gráficos del modelo numérico (variable Calidad del aire y variable Meteorológica)
							//var n_grafico = (Highcharts.charts[0] && Highcharts.charts[1]) ? 2 : 0;
							/*<?php if(in_array(3, $id_models_of_sector)){ ?>
								var n_grafico = 2;
							<?php } else { ?>
								var n_grafico = 0;
							<?php } ?>

							Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories_stat_model, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
							*/
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
					text: (air_quality_variable_stat_model) ? air_quality_variable_stat_model.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable_stat_model.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
				},

				credits: {
					enabled: false
				},

				xAxis: {
					labels: {
						formatter: function() {
							if(this.pos < 24){
								return '<span style="color:black;font-weight:bold;">'+this.value+'</span>';
							}else{
								return this.value;
							}
						}
					},
					minRange: 5,
					title: {
						text: '<?php echo lang("hours"); ?>'
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

				},

				yAxis: {
					startOnTick: true,
					endOnTick: false,
					maxPadding: 0.35,
					title: {
						text: null
					},
					labels: {
						formatter: function(){
							if(air_quality_variable_stat_model){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual_stat_model.nombre + ')';
							} else {
								return '';
							}
						},
					},
					min: 0,
					max: 500
				},

				tooltip: {
					useHTML: true,
					//headerFormat: '<span style="font-size: 10px;">{point.key}</span> <br>',
					//pointFormat: '<span style="color:{point.color}">\u25CF</span> ' + '<?php echo lang("concentration"); ?>: ' + '{point.y} ' + unit_qual_stat_model.nombre,
					formatter: function() {

						if(air_quality_variable_stat_model){

							if(air_quality_variable_stat_model.id == 9){

								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
									+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual_stat_model + ': '
									+ chart_qual_ranges_stat_model[this.points[0].y] + ' (' + unit_qual_stat_model.nombre + ') <br>'
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
									+ numberFormat(qual_stat_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
									+ numberFormat(qual_stat_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator)
									+ ' (' + unit_qual_stat_model.nombre + ') <br>'
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
									+ numberFormat(qual_stat_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

							} else {
								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
										+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual_stat_model + ': '
										+ chart_qual_ranges_stat_model[this.points[0].y] + " (" + unit_qual_stat_model.nombre + ") ";
							}

						} else {
							return  '<?php echo lang("no_information_available"); ?>';
						}

					},
					shared: true
				},

				exporting: {
					filename: (air_quality_variable_stat_model) ? air_quality_variable_stat_model.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable_stat_model.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>',
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
					},
					chartOptions: {
						xAxis: [{
							categories: qual_receptor_categories_stat_model,
							labels: {
								style: {
									fontSize: '9px'
								},
								tickInterval: 1
							}
						}]
					},
					sourceWidth: 1200
				},

				plotOptions: {
					area: {
						//size: 80,
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false,
							//format: '<b>{point.name}</b>: {point.y}',
							formatter: function(){
								return chart_qual_ranges_stat_model[this.y];
							},
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
					enabled: false
				},

				series: [{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: qual_receptor_data_stat_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // transparencia para el area
					name: '',
					zones: array_alerts_qual_chart_stat_model
				}
				]

			});

			<?php if(in_array(3, $id_models_of_sector)){ ?>
				var n_grafico = 2;
			<?php } else { ?>
				var n_grafico = 0;
			<?php } ?>

			if(air_quality_variable_stat_model){
				if(air_quality_variable_stat_model.id == 9){
					Highcharts.charts[n_grafico].addSeries({
						name: 'Range',
						data: qual_stat_intervalo_confianza,
						type: 'arearange',
						lineWidth: 0,
						linkedTo: ':previous',
						color: Highcharts.getOptions().colors[0],
						fillOpacity: 0.3,
						zIndex: 0,
						marker: {
							enabled: false
						}
					});
				}
			}

			Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories_stat_model, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);


			// CalHeatMap
			// Configuración de variables para fecha de inicio del CalHeatmap
			var first_datetime = "<?php echo ($first_datetime_qual_stat) ? $first_datetime_qual_stat : $first_datetime; ?>";
			var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
			var year = date.substring(0,4);
			var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
			var day = parseInt(date.substring(8,10));				
			var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

			

			var calheatmap_qual_stat_model = new CalHeatMap();
			calheatmap_qual_stat_model.init({
				itemSelector: "#calheatmap_qual_stat_model",
				domain: "day",
				subDomain: "x_hour",
				range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
				cellSize: 30, // el tamaño de cada celda de hora
				displayLegend: true,
				domainGutter: 10, // distancia entre días 
				tooltip: true,
				verticalOrientation: ($(window).width() < 1070) ? true : false,
				start: new Date(year, month, day, hour),
				domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
				subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
				subDomainTitleFormat: {
					empty: "<?php echo lang("out_of_forecast_range"); ?>",
					//filled: "{date}, la concentración de "+ air_quality_variable_stat_model.sigla +" se estima que será de {count} " + unit_qual_stat_model.nombre
					filled: "{date}"
				},
				subDomainDateFormat: function(date) {
					var d = new Date(date);
					var h = d.getHours();
					h = ("0" + h).slice(-2);

					var datetime = d.getTime()/1000; // timestamp

					if(air_quality_variable_stat_model){
						return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable_stat_model.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges_stat_model[datetime] + " (" + unit_qual_stat_model.nombre + ")";
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
				itemName: [unit_qual_stat_model.nombre, unit_qual_stat_model.nombre],
				//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
				legend: array_alerts_qual_calheatmap_ranges_stat_model,
				legendTitleFormat: {
					//lower: (array_alerts_qual_calheatmap_ranges_stat_model.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					lower: (array_alerts_qual_calheatmap_ranges_stat_model.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges_stat_model) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
					upper: "<?php echo lang("more_than"); ?> {max} ({name})"
				},
				legendHorizontalPosition: "center",
				legendMargin: [0, 0, 0, 0],
				data: calheatmap_data_qual_stat_model_final,
				onComplete: function() { // https://php.developreference.com/article/19345650/cal-heatmap+-+legendColors+as+array+of+color+values%3F
					setTimeout(function(){
						/*['#ffadad','#ff9696','#ff8282','#fc6d6d','#ff5454','#f51818'].forEach(function(d,i){
							d3.selectAll("rect.r" + i).style("fill", d);
						});*/
						array_alerts_qual_calheatmap_colors_stat_model.forEach(function(d,i){
							i++;
							d3.selectAll("div#calheatmap_qual_stat_model rect.r" + i).style("fill", d);
						});
					}, 10);
				}
			});

			var id_air_quality_variable_stat_model = (air_quality_variable_stat_model) ? air_quality_variable_stat_model.id : 0;
			$("#qual_receptor_stat_model-table").appTable({
				source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor_stat_model + "/" + id_air_quality_variable_stat_model + "/2", // Modelo Neuronal
				columns: [
					{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
					{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
					{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
					// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
					// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
				],
				// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
				// 	var html_action_plan_content = aData[6];
				// 	$html_action_plan.popover(
				// 		{
				// 			container: 'body',
				// 			trigger:'hover',
				// 			placement: 'left',
				// 			title: '<?php echo lang("action_plan"); ?>',
				// 			html:true,
				// 			//content: $action_plan.attr("data-content")
				// 			content: html_action_plan_content
				// 		}
				// 	);
				// },
				order: [1, "asc"]

			});


			$("#air_quality_variable_stat_model, #receptor_stat_model").on('change', function(){

				var id_air_quality_variable = $("#air_quality_variable_stat_model").val();
				var id_receptor = $("#receptor_stat_model").val();

				$.ajax({
					url: '<?php echo_uri("air_forecast_sectors/get_data_by_model"); ?>',
					type: 'post',
					dataType: 'json',
					data: {
						id_air_quality_variable: id_air_quality_variable,
						id_receptor: id_receptor,
						id_sector: id_sector,
						id_model: 2 // Neuronal
					},beforeSend: function() {
						appLoader.show();
					},
					success: function(respuesta){

						/* Variable Calidad del Aire */

						// Gráfico
						var qual_receptor_data = []; // Datos
						var qual_receptor_categories = []; // Categorías
						var chart_qual_ranges = []; // Rangos
						
						// CalHeatMap
						var calheatmap_data_qual = []; // Datos
						var calheatmap_data_qual_final = []; 
						var calheatmap_qual_ranges = []; // Rangos

						// Colores y rangos de Alertas CalHeatmap
						var array_alerts_qual_calheatmap_colors = respuesta.array_alerts_qual_calheatmap_colors;
						var array_alerts_qual_calheatmap_ranges = respuesta.array_alerts_qual_calheatmap_ranges;

						// Datos pronóstico 72 hrs
						var array_receptor_qual_variable_values_p = respuesta.array_receptor_qual_variable_values_p;
						var array_receptor_qual_variable_ranges_p = respuesta.array_receptor_qual_variable_ranges_p;
						var array_qual_intervalo_confianza = respuesta.array_qual_intervalo_confianza;
						var array_qual_porc_conf = respuesta.array_qual_porc_conf;
						var array_receptor_qual_variable_formatted_dates = respuesta.array_receptor_qual_variable_formatted_dates;

						// Alerta (colores y valores mínimos)
						var array_alerts_qual_chart = respuesta.array_alerts_qual_chart;

						// Variable
						var air_quality_variable = respuesta.air_quality_variable;

						// Unidad de variables según configuración Unidades de Reporte
						var unit_qual = respuesta.unit_qual;
						var unit_type_qual = respuesta.unit_type_qual;

						var first_datetime = respuesta.first_datetime;
						var first_datetime_qual = respuesta.first_datetime_qual;

						Object.keys(array_receptor_qual_variable_values_p).forEach(function(date, idx, array) {
							var values_p = array_receptor_qual_variable_values_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(values_p).forEach(function(time) {
								var value_p = parseFloat(values_p[time]);

								var hour = time.substring(5, 7);

								if(true){
									qual_receptor_data.push([day_name+" "+array_receptor_qual_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);
									qual_receptor_categories.push(day_short_name+" "+hour+" hrs");
								}
								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
								calheatmap_data_qual[timestamp] = value_p;

								if(array_alerts_qual_calheatmap_ranges.includes(value_p.toString())){
									calheatmap_data_qual_final[timestamp] = value_p + 1;
								} else {
									calheatmap_data_qual_final[timestamp] = value_p;
								}

							});
						});

						Object.keys(array_receptor_qual_variable_ranges_p).forEach(function(date, idx, array) {
							var ranges_p = array_receptor_qual_variable_ranges_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(ranges_p).forEach(function(time) {
								var range_p = ranges_p[time];

								var hour = time.substring(5, 7);
								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
								
								if(true){
									chart_qual_ranges[calheatmap_data_qual[timestamp]] = range_p;
								}
								calheatmap_qual_ranges[timestamp] = range_p;
							});
						});

						// Actualización Gráfico (#chart_qual_stat_model)

						// Datos
						//var n_grafico = (Highcharts.charts[0] && Highcharts.charts[1]) ? 2 : 0;
						<?php if(in_array(3, $id_models_of_sector)){ ?>
							var n_grafico = 2;
						<?php } else { ?>
							var n_grafico = 0;
						<?php } ?>

						Highcharts.charts[n_grafico].series[0].update({
							data: qual_receptor_data
						});

						// Título
						Highcharts.charts[n_grafico].title.update({
							text: (air_quality_variable) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
						});

						// Etiquetas Eje Y
						Highcharts.charts[n_grafico].yAxis[0].update({
							labels: {
								formatter: function(){
									return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual.nombre + ')';
								}
							}
						});
						
						// Rangos en la zona del Área (colores y valores mínimos de configuración de alertas)
						Highcharts.charts[n_grafico].series[0].update({
							data: qual_receptor_data,
							zones: array_alerts_qual_chart
						});

						// Intervalo de confianza
						if(Highcharts.charts[n_grafico].series[1]){
							Highcharts.charts[n_grafico].series[1].remove();
						}
						if(air_quality_variable){
							if(air_quality_variable.id == 9){
								Highcharts.charts[n_grafico].addSeries({
									name: 'Range',
									data: array_qual_intervalo_confianza,
									type: 'arearange',
									lineWidth: 0,
									linkedTo: ':previous',
									color: Highcharts.getOptions().colors[0],
									fillOpacity: 0.3,
									zIndex: 0,
									marker: {
										enabled: false
									}
								});
							}
						}

						// Tooltip
						Highcharts.charts[n_grafico].tooltip.update({
							formatter: function() {
								if(air_quality_variable){

									if(air_quality_variable.id == 9){

										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
												+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
												+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") <br>"
												+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
												+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
												+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator) 
												+ ' (' + unit_qual.nombre + ') <br>'
												+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
												+ numberFormat(array_qual_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

									} else {
										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
												+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
												+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") ";
									}

								} else {
									return  '<?php echo lang("no_information_available"); ?>';
								}
							}
						});

						Highcharts.charts[n_grafico].update({
							plotOptions: {
								area: {
									dataLabels: {
										formatter: function(){
											return chart_qual_ranges[this.y];
										}
									},
								}
							}
						});

						/*Highcharts.charts[n_grafico].exporting.update({
							filename: (air_quality_variable) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>',
							chartOptions: {
								xAxis: [{
									categories: qual_receptor_categories,
								}]
							},
						});*/

						//Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
						$('#chart_qual_stat_model').highcharts().options.exporting.chartOptions.xAxis[0].categories = qual_receptor_categories
						Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
						$('#chart_qual_stat_model').highcharts().redraw();

						// CalHeatMap
						// Configuración de variables para fecha de inicio del CalHeatmap
						var first_datetime = (first_datetime_qual) ? first_datetime_qual : first_datetime;
						var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
						var year = date.substring(0,4);
						var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
						var day = parseInt(date.substring(8,10));				
						var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

						

						// traigo el ancho de la leyenda del CalHeatmap para mantener su posición en la página al actualizar
						var graph_legend_x = $('div#calheatmap_qual_stat_model > svg > svg.graph-legend').attr('x'); 
						
						$('#calheatmap_qual_stat_model').empty();
						var calheatmap_qual_stat_model = new CalHeatMap();
						calheatmap_qual_stat_model.init({
							itemSelector: "#calheatmap_qual_stat_model",
							domain: "day",
							subDomain: "x_hour",
							range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
							cellSize: 30, // el tamaño de cada celda de hora
							displayLegend: true,
							domainGutter: 10, // distancia entre días 
							tooltip: true,
							verticalOrientation: ($(window).width() < 1070) ? true : false,
							start: new Date(year, month, day, hour),
							domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
							subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
							subDomainTitleFormat: {
								empty: "<?php echo lang("out_of_forecast_range"); ?>",
								//filled: "{date}, la concentración de "+ air_quality_variable_stat_model.sigla +" se estima que será de {count} " + unit_qual_stat_model.nombre
								filled: "{date}"
							},
							subDomainDateFormat: function(date) {
								var d = new Date(date);
								var h = d.getHours();
								h = ("0" + h).slice(-2);

								var datetime = d.getTime()/1000; // timestamp

								if(air_quality_variable){
									return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges[datetime] + " (" + unit_qual.nombre + ")";
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
							itemName: [unit_qual.nombre, unit_qual.nombre],
							//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
							legend: array_alerts_qual_calheatmap_ranges,
							legendTitleFormat: {
								//lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
								upper: "<?php echo lang("more_than"); ?> {max} ({name})"
							},
							legendHorizontalPosition: "center",
							legendMargin: [0, 0, 0, 0],
							data: calheatmap_data_qual_final,
							onComplete: function() {
								setTimeout(function(){
									array_alerts_qual_calheatmap_colors.forEach(function(d,i){
										i++;
										d3.selectAll("div#calheatmap_qual_stat_model rect.r" + i).style("fill", d);
									});

									// Conservar posición del CalHeatMap después de actualizar
									if($(window).width() > 1070){
										var domains = $('div#calheatmap_qual_stat_model .graph').children('svg');
										var width = Number(domains.first().attr('width'));
										var x = 0;
										domains.each(function () {
											$(this).attr('x', x);
											x += width;
										});
										$('div#calheatmap_qual_stat_model > svg > svg.graph-legend').attr('x', graph_legend_x);
									} else {
										$('div#calheatmap_qual_stat_model > svg > svg.graph-legend').attr('x', "0");
									}
									

								}, 10);
							}
						});


						// Actualización AppTable
						$('#qual_receptor_stat_model-table').DataTable().destroy();

						var id_air_quality_variable = (air_quality_variable) ? air_quality_variable.id : 0;
						$("#qual_receptor_stat_model-table").appTable({
							source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_air_quality_variable + "/2", // Modelo Neuronal
							columns: [
								{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
								{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
								{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
								{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
								// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
								// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
							],
							// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
							// 	var html_action_plan_content = aData[6];
							// 	$html_action_plan.popover(
							// 		{
							// 			container: 'body',
							// 			trigger:'hover',
							// 			placement: 'left',
							// 			title: '<?php echo lang("action_plan"); ?>',
							// 			html:true,
							// 			//content: $action_plan.attr("data-content")
							// 			content: html_action_plan_content
							// 		}
							// 	);
							// },
							order: [1, "asc"]

						});

						
						

						appLoader.hide();
						
					}
				});

			});


		<?php } ?>

		// Si el Sector tiene el modelo Machine Learning (id 1)
		<?php if(in_array(1, $id_models_of_sector)){ ?>

			<?php if(count($receptors_neur_model_dropdown) >= 2){ ?>
				<?php  $id_receptor = (array_key_exists(2, $receptors_neur_model_dropdown)) ? 2 : array_keys($receptors_neur_model_dropdown)[1]; ?>
				<?php if($id_receptor == 2){ // Si el receptor es Hotel Mina, marcarlo seleccionado ?>
					$('#receptor_neur_model').val(2).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera estación receptora del dropdown ?>
					$('#receptor_neur_model').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>

			<?php if(count($air_quality_variables_neur_model_dropdown) >= 2){ ?>
				<?php  $id_variable = (array_key_exists(9, $air_quality_variables_neur_model_dropdown)) ? 9 : array_keys($air_quality_variables_neur_model_dropdown)[1]; ?>
				<?php if($id_variable == 9){ // Si la variable es PM10, marcarla seleccionada ?>
					$('#air_quality_variable_neur_model').val(9).trigger('change');
				<?php } else { // Si no, marcar como seleccionada la primera variable del dropdown ?>
					$('#air_quality_variable_neur_model').find('option:eq(1)').prop('selected', true).trigger('change');
				<?php } ?>
			<?php } ?>

			// Objeto variable Calidad del aire inicial
			var air_quality_variable_neur_model = <?php echo json_encode($air_quality_variable_neur_model); ?>;

			// Unidades de variable según configuración Unidades de Reporte
			var unit_qual_neur_model = <?php echo json_encode($unit_qual_neur_model); ?>;
			var unit_type_qual_neur_model = <?php echo json_encode($unit_type_qual_neur_model); ?>;

			var id_sector = <?php echo $sector_info->id; ?>;
			var id_receptor_neur_model = $("#receptor_neur_model").val();

			// Gráfico
			var qual_receptor_data_neur_model = []; // Datos
			var qual_receptor_categories_neur_model = []; // Categorías
			var chart_qual_ranges_neur_model = []; // Rangos
			
			// CalHeatMap
			var calheatmap_data_qual_neur_model = []; // Datos
			var calheatmap_data_qual_neur_model_final = []; 
			var calheatmap_qual_ranges_neur_model = []; // Rangos

			// Colores y rangos de Alertas CalHeatmap
			var array_alerts_qual_calheatmap_colors_neur_model = <?php echo json_encode($array_alerts_qual_calheatmap_colors_neur_model); ?>;
			var array_alerts_qual_calheatmap_ranges_neur_model = <?php echo json_encode($array_alerts_qual_calheatmap_ranges_neur_model); ?>;

			// Datos pronóstico 72 hrs
			var array_receptor_qual_neur_model_values_p = <?php echo json_encode($array_receptor_qual_neur_model_values_p); ?>;
			var array_receptor_qual_neur_model_ranges_p = <?php echo json_encode($array_receptor_qual_neur_model_ranges_p); ?>;
			var qual_neur_intervalo_confianza = <?php echo json_encode($array_qual_neur_intervalo_confianza); ?>;
			var qual_neur_porc_conf = <?php echo json_encode($array_qual_neur_porc_conf); ?>;
			var array_receptor_qual_neur_formatted_dates = <?php echo json_encode($array_receptor_qual_neur_formatted_dates); ?>;

			// Alerta (colores y valores mínimos)
			var array_alerts_qual_chart_neur_model = <?php echo json_encode($array_alerts_qual_chart_neur_model); ?>;

			Object.keys(array_receptor_qual_neur_model_values_p).forEach(function(date, idx, array) {
				var values_p = array_receptor_qual_neur_model_values_p[date];
				
				var datetime = new Date(date);
				
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(values_p).forEach(function(time) {
					var value_p = parseFloat(values_p[time]);

					var hour = time.substring(5, 7);

					if(true){
						qual_receptor_data_neur_model.push([day_name+" "+array_receptor_qual_neur_formatted_dates[date]+" "+hour+" hrs", value_p]);
						qual_receptor_categories_neur_model.push(day_short_name+" "+hour+" hrs");					
					}
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
					calheatmap_data_qual_neur_model[timestamp] = value_p;

					if(array_alerts_qual_calheatmap_ranges_neur_model.includes(value_p.toString())){
						calheatmap_data_qual_neur_model_final[timestamp] = value_p + 1;
					} else {
						calheatmap_data_qual_neur_model_final[timestamp] = value_p;
					}

				});
			});

			Object.keys(array_receptor_qual_neur_model_ranges_p).forEach(function(date, idx, array) {
				var ranges_p = array_receptor_qual_neur_model_ranges_p[date];

				var datetime = new Date(date);
				var day_name = array_days_name[datetime.getUTCDay()];
				var day_short_name = array_days_short_name[datetime.getUTCDay()];

				Object.keys(ranges_p).forEach(function(time) {
					var range_p = ranges_p[time];

					var hour = time.substring(5, 7);
					var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
					
					if(true){
						chart_qual_ranges_neur_model[calheatmap_data_qual_neur_model[timestamp]] = range_p;
					}
					calheatmap_qual_ranges_neur_model[timestamp] = range_p;
				});
			});

			$('#chart_qual_neur_model').highcharts({
				chart: {
					type: 'area',
					zoomType: 'x',
					panning: true,
					panKey: 'shift',
					/*scrollablePlotArea: {
						minWidth: 600
					},*/
					events: {
						load: function(){

							/*var n_grafico = 0;	
							<?php if(in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
								var n_grafico = 3;
							<?php } elseif(in_array(3, $id_models_of_sector) && !in_array(2, $id_models_of_sector)){ ?>
								var n_grafico = 2;
							<?php } elseif(!in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
								var n_grafico = 1;
							<?php } ?>

							Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories_neur_model, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
							*/
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
					text: (air_quality_variable_neur_model) ? air_quality_variable_neur_model.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable_neur_model.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
				},

				credits: {
					enabled: false
				},

				xAxis: {
					labels: {
						formatter: function() {
							if(this.pos < 24){
								return '<span style="color:black;font-weight:bold;">'+this.value+'</span>';
							}else{
								return this.value;
							}
						}
					},
					minRange: 5,
					title: {
						text: '<?php echo lang("hours"); ?>'
					},
					plotBands: [	//Franjas de color por turno
						{from: 0, to: 8, color: '#F0F0F0'},
						{from: 8, to: 20, color: '#F7F7F7'},
						{from: 20, to: 32, color: '#F0F0F0'},
						{from: 32, to: 44, color: '#F7F7F7'},
						{from: 44, to: 56, color: '#F0F0F0'},
						{from: 56, to: 68, color: '#F7F7F7'},
						{from: 68, to: 71, color: '#F0F0F0'},
					]
				},

				yAxis: {
					startOnTick: true,
					endOnTick: false,
					maxPadding: 0.35,
					title: {
						text: null
					},
					labels: {
						formatter: function(){
							if(air_quality_variable_neur_model){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual_neur_model.nombre + ')';
							} else {
								return '';
							}						
						},
					},
					min: 0,
					max: 500
				},

				tooltip: {
					useHTML: true,
					//headerFormat: '<span style="font-size: 10px;">{point.key}</span> <br>',
					//pointFormat: '<span style="color:{point.color}">\u25CF</span> ' + '<?php echo lang("concentration"); ?>: ' + '{point.y} ' + unit_qual_neur_model.nombre,
					formatter: function(e) {
						
						if(air_quality_variable_neur_model){

							if(air_quality_variable_neur_model.id == 9){

								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
									+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual_neur_model + ': '
									+ chart_qual_ranges_neur_model[this.points[0].y] + " (" + unit_qual_neur_model.nombre + ") <br>"
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
									+ numberFormat(qual_neur_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
									+ numberFormat(qual_neur_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator) 
									+ ' (' + unit_qual_neur_model.nombre + ') <br>'
									+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
									+ numberFormat(qual_neur_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

							} else {
								return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
									+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual_neur_model + ': '
									+ chart_qual_ranges_neur_model[this.points[0].y] + " (" + unit_qual_neur_model.nombre + ") ";
							}

						} else {
							return  '<?php echo lang("no_information_available"); ?>';
						}

					},
					shared: true
				},

				exporting: {
					filename: (air_quality_variable_neur_model) ? air_quality_variable_neur_model.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable_neur_model.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>',
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
					},
					chartOptions: {
						xAxis: [{
							categories: qual_receptor_categories_neur_model,
							labels: {
								style: {
									fontSize: '9px'
								},
								tickInterval: 1
							}
						}]
					},
					sourceWidth: 1200
				},

				plotOptions: {
					area: {
						//size: 80,
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false,
							//format: '<b>{point.name}</b>: {point.y}',
							formatter: function(){
								return chart_qual_ranges_neur_model[this.y];
							},
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
					enabled: false
				},

				series: [{
					accessibility: {
						keyboardNavigation: {
							enabled: false
						}
					},
					data: qual_receptor_data_neur_model,
					//lineColor: Highcharts.getOptions().colors[1],
					//color: '#ff5454',
					fillOpacity: 0, // transparencia para el area
					name: '',
					zones: array_alerts_qual_chart_neur_model
				},
				]

			});

			var n_grafico = 0;	
			<?php if(in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
				var n_grafico = 3;
			<?php } elseif(in_array(3, $id_models_of_sector) && !in_array(2, $id_models_of_sector)){ ?>
				var n_grafico = 2;
			<?php } elseif(!in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
				var n_grafico = 1;
			<?php } ?>

			if(air_quality_variable_neur_model){
				if(air_quality_variable_neur_model.id == 9){
					Highcharts.charts[n_grafico].addSeries({
						name: 'Range',
						data: qual_neur_intervalo_confianza,
						type: 'arearange',
						lineWidth: 0,
						linkedTo: ':previous',
						color: Highcharts.getOptions().colors[0],
						fillOpacity: 0.3,
						zIndex: 0,
						marker: {
							enabled: false
						}
					});
				}
			}

			Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories_neur_model, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);



			// CalHeatMap
			// Configuración de variables para fecha de inicio del CalHeatmap
			var first_datetime = "<?php echo ($first_datetime_qual_neur) ? $first_datetime_qual_neur : $first_datetime; ?>";
			var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
			var year = date.substring(0,4);
			var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
			var day = parseInt(date.substring(8,10));				
			var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

			

			var calheatmap_qual_neur_model = new CalHeatMap();
			calheatmap_qual_neur_model.init({
				itemSelector: "#calheatmap_qual_neur_model",
				domain: "day",
				subDomain: "x_hour",
				range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
				cellSize: 30, // el tamaño de cada celda de hora
				displayLegend: true,
				domainGutter: 10, // distancia entre días 
				tooltip: true,
				verticalOrientation: ($(window).width() < 1070) ? true : false,
				start: new Date(year, month, day, hour),
				domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
				subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
				subDomainTitleFormat: {
					empty: "<?php echo lang("out_of_forecast_range"); ?>",
					//filled: "{date}, la concentración de "+ air_quality_variable_neur_model.sigla +" se estima que será de {count} " + unit_qual_neur_model.nombre
					filled: "{date}"
				},
				subDomainDateFormat: function(date) {
					var d = new Date(date);
					var h = d.getHours();
					h = ("0" + h).slice(-2);

					var datetime = d.getTime()/1000; // timestamp

					if(air_quality_variable_neur_model){
						return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable_neur_model.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges_neur_model[datetime] + " (" + unit_qual_neur_model.nombre + ")";
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
				itemName: [unit_qual_neur_model.nombre, unit_qual_neur_model.nombre],
				//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
				legend: array_alerts_qual_calheatmap_ranges_neur_model,
				legendTitleFormat: {
					//lower: (array_alerts_qual_calheatmap_ranges_neur_model.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					lower: (array_alerts_qual_calheatmap_ranges_neur_model.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges_neur_model) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
					inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
					upper: "<?php echo lang("more_than"); ?> {max} ({name})"
				},
				legendHorizontalPosition: "center",
				legendMargin: [0, 0, 0, 0],
				data: calheatmap_data_qual_neur_model_final,
				onComplete: function() { // https://php.developreference.com/article/19345650/cal-heatmap+-+legendColors+as+array+of+color+values%3F
					setTimeout(function(){
						/*['#ffadad','#ff9696','#ff8282','#fc6d6d','#ff5454','#f51818'].forEach(function(d,i){
							d3.selectAll("rect.r" + i).style("fill", d);
						});*/
						array_alerts_qual_calheatmap_colors_neur_model.forEach(function(d,i){
							i++;
							d3.selectAll("div#calheatmap_qual_neur_model rect.r" + i).style("fill", d);
						});
					}, 10);
				}
			});

			var id_air_quality_variable_neur_model = (air_quality_variable_neur_model) ? air_quality_variable_neur_model.id : 0;
			$("#qual_receptor_neur_model-table").appTable({
				source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor_neur_model + "/" + id_air_quality_variable_neur_model + "/1", // Modelo Machine Learning
				columns: [
					{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
					{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
					{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
					{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
					{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
					// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
					// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
				],
				// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
				// 	var html_action_plan_content = aData[6];
				// 	$html_action_plan.popover(
				// 		{
				// 			container: 'body',
				// 			trigger:'hover',
				// 			placement: 'left',
				// 			title: '<?php echo lang("action_plan"); ?>',
				// 			html:true,
				// 			//content: $action_plan.attr("data-content")
				// 			content: html_action_plan_content
				// 		}
				// 	);
				// },
				order: [1, "asc"]

			});



			$("#air_quality_variable_neur_model, #receptor_neur_model").on('change', function(){

				var id_air_quality_variable = $("#air_quality_variable_neur_model").val();
				var id_receptor = $("#receptor_neur_model").val();

				$.ajax({
					url: '<?php echo_uri("air_forecast_sectors/get_data_by_model"); ?>',
					type: 'post',
					dataType: 'json',
					data: {
						id_air_quality_variable: id_air_quality_variable,
						id_receptor: id_receptor,
						id_sector: id_sector,
						id_model: 1 // Machine Learning
					},beforeSend: function() {
						appLoader.show();
					},
					success: function(respuesta){

						/* Variable Calidad del Aire */

						// Gráfico
						var qual_receptor_data = []; // Datos
						var qual_receptor_categories = []; // Categorías
						var chart_qual_ranges = []; // Rangos
						
						// CalHeatMap
						var calheatmap_data_qual = []; // Datos
						var calheatmap_data_qual_final = []; 
						var calheatmap_qual_ranges = []; // Rangos

						// Colores y rangos de Alertas CalHeatMap
						var array_alerts_qual_calheatmap_colors = respuesta.array_alerts_qual_calheatmap_colors;
						var array_alerts_qual_calheatmap_ranges = respuesta.array_alerts_qual_calheatmap_ranges;

						// Datos pronóstico 72 hrs
						var array_receptor_qual_variable_values_p = respuesta.array_receptor_qual_variable_values_p;
						var array_receptor_qual_variable_ranges_p = respuesta.array_receptor_qual_variable_ranges_p;
						var array_qual_intervalo_confianza = respuesta.array_qual_intervalo_confianza;
						var array_qual_porc_conf = respuesta.array_qual_porc_conf;
						var array_receptor_qual_variable_formatted_dates = respuesta.array_receptor_qual_variable_formatted_dates;

						// Alerta (colores y valores mínimos)
						var array_alerts_qual_chart = respuesta.array_alerts_qual_chart;

						// Variable
						var air_quality_variable = respuesta.air_quality_variable;

						// Unidad de variables según configuración Unidades de Reporte
						var unit_qual = respuesta.unit_qual;
						var unit_type_qual = respuesta.unit_type_qual;

						var first_datetime = respuesta.first_datetime;
						var first_datetime_qual = respuesta.first_datetime_qual;

						Object.keys(array_receptor_qual_variable_values_p).forEach(function(date, idx, array) {
							var values_p = array_receptor_qual_variable_values_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(values_p).forEach(function(time) {
								var value_p = parseFloat(values_p[time]);

								var hour = time.substring(5, 7);

								if(true){
									qual_receptor_data.push([day_name+" "+array_receptor_qual_variable_formatted_dates[date]+" "+hour+" hrs", value_p]);
									qual_receptor_categories.push(day_short_name+" "+hour+" hrs");
								}

								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
								calheatmap_data_qual[timestamp] = value_p;

								if(array_alerts_qual_calheatmap_ranges.includes(value_p.toString())){
									calheatmap_data_qual_final[timestamp] = value_p + 1;
								} else {
									calheatmap_data_qual_final[timestamp] = value_p;
								}

							});
						});

						Object.keys(array_receptor_qual_variable_ranges_p).forEach(function(date, idx, array) {
							var ranges_p = array_receptor_qual_variable_ranges_p[date];

							var datetime = new Date(date);
							var day_name = array_days_name[datetime.getUTCDay()];
							var day_short_name = array_days_short_name[datetime.getUTCDay()];

							Object.keys(ranges_p).forEach(function(time) {
								var range_p = ranges_p[time];

								var hour = time.substring(5, 7);
								var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
								
								if(true){
									chart_qual_ranges[calheatmap_data_qual[timestamp]] = range_p;
								}
								calheatmap_qual_ranges[timestamp] = range_p;
							});
						});

						// Actualización Gráfico (#chart_qual_neur_model)

						// Datos
						var n_grafico = 0;	
						<?php if(in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
							var n_grafico = 3;
						<?php } elseif(in_array(3, $id_models_of_sector) && !in_array(2, $id_models_of_sector)){ ?>
							var n_grafico = 2;
						<?php } elseif(!in_array(3, $id_models_of_sector) && in_array(2, $id_models_of_sector)){ ?>
							var n_grafico = 1;
						<?php } ?>

						Highcharts.charts[n_grafico].series[0].update({
							data: qual_receptor_data
						});

						// Título
						Highcharts.charts[n_grafico].title.update({
							text: (air_quality_variable) ? air_quality_variable.name_unit_type + ' <?php echo lang("of"); ?> ' + air_quality_variable.sigla + ' <?php echo lang("forecasted_today_and_next_48_hours"); ?>' : '<?php echo lang("air_quality_variable"); ?>'
						});

						// Etiquetas Eje Y
						Highcharts.charts[n_grafico].yAxis[0].update({
							formatter: function(){
								return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' (' + unit_qual.nombre + ')';
							}
						});
						
						// Rangos en la zona del Área (colores y valores mínimos de configuración de alertas)
						Highcharts.charts[n_grafico].series[0].update({
							data: qual_receptor_data,
							zones: array_alerts_qual_chart
						});

						// Intervalo de confianza
						if(Highcharts.charts[n_grafico].series[1]){
							Highcharts.charts[n_grafico].series[1].remove();
						}
						if(air_quality_variable){
							if(air_quality_variable.id == 9){
								Highcharts.charts[n_grafico].addSeries({
									name: 'Range',
									data: array_qual_intervalo_confianza,
									type: 'arearange',
									lineWidth: 0,
									linkedTo: ':previous',
									color: Highcharts.getOptions().colors[0],
									fillOpacity: 0.3,
									zIndex: 0,
									marker: {
										enabled: false
									}
								});
							}
						}

						// Tooltip
						Highcharts.charts[n_grafico].tooltip.update({
							formatter: function() {
								if(air_quality_variable){

									if(air_quality_variable.id == 9){

										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
												+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
												+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") <br>"
												+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
												+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
												+ numberFormat(array_qual_intervalo_confianza[this.points[0].point.index][1], decimal_numbers, decimals_separator, thousands_separator)
												+ ' (' + unit_qual.nombre + ') <br>'
												+ '<span style="color:' + Highcharts.getOptions().colors[0] + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
												+ numberFormat(array_qual_porc_conf[this.points[0].point.index], decimal_numbers, decimals_separator, thousands_separator) + '%';

									} else {
										return  '<span style="font-size: 10px;">' + this.points[0].key + '</span> <br>'
												+ '<span style="color:' + this.points[0].color + '">\u25CF</span> '  + unit_type_qual + ': '
												+ chart_qual_ranges[this.points[0].y] + " (" + unit_qual.nombre + ") ";
									}

								} else {
									return  '<?php echo lang("no_information_available"); ?>';
								}
							}
						});

						Highcharts.charts[n_grafico].update({
							plotOptions: {
								area: {
									dataLabels: {
										formatter: function(){
											return chart_qual_ranges[this.y];
										}
									},
								}
							}
						});

						$('#chart_qual_neur_model').highcharts().options.exporting.chartOptions.xAxis[0].categories = qual_receptor_categories
						Highcharts.charts[n_grafico].xAxis[0].update({categories: qual_receptor_categories, labels: { style: { fontSize: '9px'}}, tickInterval: 1 }, true);
						$('#chart_qual_neur_model').highcharts().redraw();



						// CalHeatMap
						// Configuración de variables para fecha de inicio del CalHeatmap
						var first_datetime = (first_datetime_qual) ? first_datetime_qual : first_datetime;
						var date = first_datetime.substring(0, 10); 			// Ej: 2020-01-01
						var year = date.substring(0,4);
						var month =  parseInt(date.substring(5,7)) - 1;			// Puede ser del 1 al 12
						var day = parseInt(date.substring(8,10));				
						var hour = parseInt(first_datetime.substring(11, 13)); 	// Puede ser del 0 al 23

						

						// traigo el ancho de la leyenda del CalHeatmap para mantener su posición en la página al actualizar
						var graph_legend_x = $('div#calheatmap_qual_neur_model > svg > svg.graph-legend').attr('x'); 
						
						$('#calheatmap_qual_neur_model').empty();
						var calheatmap_qual_neur_model = new CalHeatMap();
						calheatmap_qual_neur_model.init({
							itemSelector: "#calheatmap_qual_neur_model",
							domain: "day",
							subDomain: "x_hour",
							range: 3, // en este caso la cantidad de días (puede ser el count del array de datos (por fecha))
							cellSize: 30, // el tamaño de cada celda de hora
							displayLegend: true,
							domainGutter: 10, // distancia entre días 
							tooltip: true,
							verticalOrientation: ($(window).width() < 1070) ? true : false,
							start: new Date(year, month, day, hour),
							domainLabelFormat: array_format_date_calheatmap[AppHelper.settings.dateFormat],
							subDomainTextFormat: "%H",// dependerá del formato de hora del proyecto
							subDomainTitleFormat: {
								empty: "<?php echo lang("out_of_forecast_range"); ?>",
								//filled: "{date}, la concentración de "+ air_quality_variable_neur_model.sigla +" se estima que será de {count} " + unit_qual_neur_model.nombre
								filled: "{date}"
							},
							subDomainDateFormat: function(date) {
								var d = new Date(date);
								var h = d.getHours();
								h = ("0" + h).slice(-2);

								var datetime = d.getTime()/1000; // timestamp

								if(air_quality_variable){
									return "<?php echo ucfirst(lang("at")); ?> " + h + " <?php echo strtolower(lang("hours")).", ".lang("the_estimated_concentration_of"); ?> " + air_quality_variable.sigla +" <?php echo lang("will_be"); ?> " + calheatmap_qual_ranges[datetime] + " (" + unit_qual.nombre + ")";
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
							itemName: [unit_qual.nombre, unit_qual.nombre],
							//legend: [0.0001, 0.0005, 0.0010, 0.0050], // sacar minimo y máximo y crear escala de colores en base a esos valores
							legend: array_alerts_qual_calheatmap_ranges,
							legendTitleFormat: {
								//lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								lower: (array_alerts_qual_calheatmap_ranges.length > 0) ? (Math.min.apply(Math, array_alerts_qual_calheatmap_ranges) > 0) ? "<?php echo lang("less_than"); ?> {min} ({name})" : "<?php echo lang("less_than_or_equal_to"); ?> {min} ({name})" : '<?php echo lang("no_information_available"); ?>',
								inner: "<?php echo lang("between"); ?> {down} <?php echo lang("and"); ?> {up} ({name})",
								upper: "<?php echo lang("more_than"); ?> {max} ({name})"
							},
							legendHorizontalPosition: "center",
							legendMargin: [0, 0, 0, 0],
							data: calheatmap_data_qual_final,
							onComplete: function() {
								setTimeout(function(){
									array_alerts_qual_calheatmap_colors.forEach(function(d,i){
										i++;
										d3.selectAll("div#calheatmap_qual_neur_model rect.r" + i).style("fill", d);
									});

									// Conservar posición del CalHeatMap después de actualizar
									if($(window).width() > 1070){
										var domains = $('div#calheatmap_qual_neur_model .graph').children('svg');
										var width = Number(domains.first().attr('width'));
										var x = 0;
										domains.each(function () {
											$(this).attr('x', x);
											x += width;
										});
										$('div#calheatmap_qual_neur_model > svg > svg.graph-legend').attr('x', graph_legend_x);
									} else {
										$('div#calheatmap_qual_neur_model > svg > svg.graph-legend').attr('x', "0");
									}
									
								}, 10);
							}
						});


						// Actualización AppTable
						$('#qual_receptor_neur_model-table').DataTable().destroy();

						var id_air_quality_variable = (air_quality_variable) ? air_quality_variable.id : 0;
						$("#qual_receptor_neur_model-table").appTable({
							source: '<?php echo_uri("air_forecast_sectors/list_data_variable/") ?>' + id_sector + "/" + id_receptor + "/" + id_air_quality_variable + "/1", // Modelo Machine Learning
							columns: [
								{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50 hide"},
								{title: "<?php echo lang("date"); ?>", "class": "text-left dt-head-center", type: "extract-date"},
								{title: "<?php echo lang("hour"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("alert"); ?>", "class": "text-center dt-head-center"},
								{title: "<?php echo lang("range"); ?>", "class": "text-left dt-head-center"},
								{title: "<?php echo lang("action_plan"); ?>", "class": "text-left dt-head-center"},
								// {title: '<i class="fa fa-bars"></i>', "class": "text-center option no_breakline"},
								// {title: '', "class": "hide"} // Columna reservada para el contenido del popover del plan de acción
							],
							// rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
							// 	var $html_action_plan = $(nRow).find('[data-toggle="popover"]');
							// 	var html_action_plan_content = aData[6];
							// 	$html_action_plan.popover(
							// 		{
							// 			container: 'body',
							// 			trigger:'hover',
							// 			placement: 'left',
							// 			title: '<?php echo lang("action_plan"); ?>',
							// 			html:true,
							// 			//content: $action_plan.attr("data-content")
							// 			content: html_action_plan_content
							// 		}
							// 	);
							// },
							order: [1, "asc"]

						});

						appLoader.hide();
						
					}
				});

			});
			
			//$('#div_stat_model').scrollTo('#h1_stat');

		<?php } ?>
		
	});
	
	
	
</script>

<script>
	$(document).ready(function(){

		$('#excel').click(function(){
			var $form = $('<form id="gg"></form>').attr('action','<?php echo_uri("Air_forecast_sectors/get_excel/".$sector_info->id); ?>').attr('method','POST').attr('target', '_self').appendTo('body');
			$form.submit();
		});

		// Posicionamiento del scroll a una sección de modelo cuando se quiera acceder a este desde el Panel Principal:

		var hash = window.location.hash;
		var top = 0;
		var left = 0;

		<?php if(in_array(1, $id_models_of_sector)){ ?> // Modelo Machine Learning

			<?php if(in_array(2, $id_models_of_sector)){ ?> // Modelo Neuronal

				if(hash == "#div_stat_model"){
					top = $("#div_stat_model").position().top + 330; // Se suman pixeles para ajustar la posición del scroll   OK !!
					left = $("#div_stat_model").position().left;
				} else if(hash == "#div_numerical_model"){
					top = $("#div_numerical_model").position().top + 680;
					left = $("#div_numerical_model").position().left;
				}
			
			<?php } elseif(in_array(3, $id_models_of_sector)){ ?> // Modelo Numérico
				if(hash == "#div_numerical_model"){
					top = $("#div_numerical_model").position().top + 330;
					left = $("#div_numerical_model").position().left;
				}
			<?php } ?>	

		<?php } elseif(in_array(2, $id_models_of_sector)) { ?> // Modelo Neuronal
				if(hash == "#div_numerical_model"){
					top = $("#div_numerical_model").position().top + 330;
					left = $("#div_numerical_model").position().left;
				}
		<?php } ?>

		if(top > 0 && left > 0){
			setTimeout(function(){
				$(".scrollable-page").mCustomScrollbar("scrollTo", {y:top, x:left}, {scrollInertia:0});
			},500);
		}

	});
</script>

<?php } ?>