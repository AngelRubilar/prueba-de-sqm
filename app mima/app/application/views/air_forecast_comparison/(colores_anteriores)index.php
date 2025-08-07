<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
		<a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
		<a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
		<a class="breadcrumb-item" href=""><?php echo lang("air_forecast_comparison"); ?> </a>
    </nav>


    <?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

        <?php foreach($stations as $station){ ?>
    
            <div id="div_station_<?php echo $station->id; ?>" class="panel panel-default">

                <div class="page-title clearfix">
                    <h1><?php echo $station->name; ?> </h1>

                    <div class="p15"><span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('forecast_performanc_msj') ?>"><i class="fa fa-question-circle"></i></span></div>
                    
                    <div id="div_update_monitoring_data_info_<?php echo $station->id; ?>" class="p5 pl15 text-off"></div>
                </div>
                
                <div class="panel-body">

                    <div class="col-md-12">
                        <div class="col-md-12" id="chart_<?php echo $station->id; ?>" >
                            <div style="margin-top: 100px; text-align: center">
                                <strong><?php echo lang("no_information_available"); ?></strong>
                            </div>
                        </div>
                    </div>

                </div>
            
            </div>
        
        <?php } ?>

    <?php } else { ?><!-- Se aplica la configuración de perfil (ver ninguno) -->

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

    <?php } ?><!-- Fin configuración de perfil (ver todos) -->

</div>

<script type="text/javascript">
    $(document).ready(function () {

        // GENERAL SETTINGS
		var decimals_separator = AppHelper.settings.decimalSeparator;
		var thousands_separator = AppHelper.settings.thousandSeparator;
		var decimal_numbers = AppHelper.settings.decimalNumbers;

		$('[data-toggle="tooltip"]').tooltip();

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


		// Highcharts.Tooltip.prototype.hide = function(){}; // MANTENER TOOLTIPS SIEMPRE VISIBLES CUANDO EL CURSOR ESTÁ FUERA DEL FOCO DEL GRÁFICO
		// var chart_categories = []; // CATEGORÍAS (USADAS PARA AMBOS MODELOS Y GRÁFICOS)

		/* DATOS MONITOREO POR DEFECTO PARA TODOS LOS GRÁFICOS */
		var default_data_m = <?php echo json_encode($chart_default_data_m); ?>; // DATOS

		let chart_default_data_m = [];
		for(var i in default_data_m){
			chart_default_data_m.push([i,default_data_m[i]]);
		}
        
		/* FIN DATOS MONITOREO POR DEFECTO PARA TODOS LOS GRÁFICOS */


        /* DATOS PRONÓSTICO */
        <?php foreach($stations as $station){ ?>

            (function(){ // Esto se hace para encapsular el alcance de las variables, para que no se sobre-escriban en cada interación

                // ALERTA (COLORES Y VALORES MÍNIMOS, USADOS PARA AMBOS MODELOS Y GRÁFICOS)
                var array_alerts_yAxis_plotBands = <?php echo json_encode($array_alerts_yAxis_plotBands[$station->id]); ?>;
                var array_alerts_yAxis_tickPositions = <?php echo json_encode($array_alerts_yAxis_tickPositions[$station->id]); ?>;
                var yAxis_max = <?php echo $yAxis_max[$station->id]; ?>;

                var chart_categories = []; // CATEGORÍAS (USADAS PARA AMBOS MODELOS Y GRÁFICOS)

                /* GRÁFICO ESTACIÓN ITERACIÓN ACTUAL | MODELO MACHINE LEARNING */
                var chart_data_ml = []; // DATOS DE PRONOSTICO
                var chart_ranges_ml = []; // RANGOS (Rango en que cae el valor de pronostico dentro de las alertas)
                var chart_timestamp_values_p_ml = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

                // DATOS PRONÓSTICO 72 HORAS HACIA ATRÁS
                var chart_ml_values_p = <?php echo json_encode($chart_ml_values_p[$station->id]) ?>;
                var chart_ml_ranges_p = <?php echo json_encode($chart_ml_ranges_p[$station->id]); ?>;
                var chart_ml_intervalo_confianza = <?php echo json_encode($chart_ml_intervalo_confianza[$station->id]); ?>;
                var chart_ml_porc_conf = <?php echo json_encode($chart_ml_porc_conf[$station->id]); ?>;
                var chart_ml_formatted_dates = <?php echo json_encode($chart_ml_formatted_dates[$station->id]); ?>;

                Object.keys(chart_ml_values_p).forEach(function(date, idx, array) {
                    var values_p = chart_ml_values_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(values_p).forEach(function(time) {
                        var value_p = parseFloat(values_p[time]);
                        var hour = time.substring(5, 7);

                        chart_data_ml.push([chart_ml_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
                        chart_categories.push(day_short_name+" "+hour+" hrs");
                        
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        chart_timestamp_values_p_ml[timestamp] = value_p;
                    });
                });
                
                Object.keys(chart_ml_ranges_p).forEach(function(date, idx, array) {
                    var ranges_p = chart_ml_ranges_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(ranges_p).forEach(function(time) {
                        var range_p = ranges_p[time];

                        var hour = time.substring(5, 7);
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        
                        chart_ranges_ml[chart_timestamp_values_p_ml[timestamp]] = range_p;
                    });
                });


                /* GRÁFICO ESTACIÓN ITERACIÓN ACTUAL | MODELO NEURONAL */
                var chart_data_neur = []; // DATOS
                var chart_ranges_neur = []; // RANGOS
                var chart_timestamp_values_p_neur = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

                // DATOS PRONÓSTICO 72 HORAS HACIA ATRÁS
                var chart_neur_values_p = <?php echo json_encode($chart_neur_values_p[$station->id]); ?>;
                var chart_neur_ranges_p = <?php echo json_encode($chart_neur_ranges_p[$station->id]); ?>;
                var chart_neur_intervalo_confianza = <?php echo json_encode($chart_neur_intervalo_confianza[$station->id]); ?>;
                var chart_neur_porc_conf = <?php echo json_encode($chart_neur_porc_conf[$station->id]); ?>;
                var chart_neur_formatted_dates = <?php echo json_encode($chart_neur_formatted_dates[$station->id]); ?>;

                Object.keys(chart_neur_values_p).forEach(function(date, idx, array) {
                    var values_p = chart_neur_values_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(values_p).forEach(function(time) {
                        var value_p = parseFloat(values_p[time]);
                        var hour = time.substring(5, 7);

                        chart_data_neur.push([chart_neur_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
                        chart_categories.push(day_short_name+" "+hour+" hrs");
                        
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        chart_timestamp_values_p_neur[timestamp] = value_p;
                    });
                });

                Object.keys(chart_neur_ranges_p).forEach(function(date, idx, array) {
                    var ranges_p = chart_neur_ranges_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(ranges_p).forEach(function(time) {
                        var range_p = ranges_p[time];

                        var hour = time.substring(5, 7);
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        
                        chart_ranges_neur[chart_timestamp_values_p_neur[timestamp]] = range_p;
                    });
                });


                /* GRÁFICO ESTACIÓN ITERACIÓN ACTUAL | MODELO NUMÉRICO */
                var chart_data_num = []; // DATOS
                var chart_ranges_num = []; // RANGOS
                var chart_timestamp_values_p_num = []; // DATOS, KEY: TIMESTAMP, VALUE: VALOR (PARA SETEAR RANGOS)

                // DATOS PRONÓSTICO 72 HORAS HACIA ATRÁS
                var chart_num_values_p = <?php echo json_encode($chart_num_values_p[$station->id]); ?>;
                var chart_num_ranges_p = <?php echo json_encode($chart_num_ranges_p[$station->id]); ?>;
                var chart_num_intervalo_confianza = <?php echo json_encode($chart_num_intervalo_confianza[$station->id]); ?>;
                var chart_num_porc_conf = <?php echo json_encode($chart_num_porc_conf[$station->id]); ?>;
                var chart_num_formatted_dates = <?php echo json_encode($chart_num_formatted_dates[$station->id]); ?>;

                Object.keys(chart_num_values_p).forEach(function(date, idx, array) {
                    var values_p = chart_num_values_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(values_p).forEach(function(time) {
                        var value_p = parseFloat(values_p[time]);
                        var hour = time.substring(5, 7);

                        chart_data_num.push([chart_num_formatted_dates[date]+" "+hour+":00 hrs", value_p]);
                        chart_categories.push(day_short_name+" "+hour+" hrs");
                        
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        chart_timestamp_values_p_num[timestamp] = value_p;
                    });
                });

                Object.keys(chart_num_ranges_p).forEach(function(date, idx, array) {
                    var ranges_p = chart_num_ranges_p[date];

                    var datetime = new Date(date);
                    var day_name = array_days_name[datetime.getUTCDay()];
                    var day_short_name = array_days_short_name[datetime.getUTCDay()];

                    Object.keys(ranges_p).forEach(function(time) {
                        var range_p = ranges_p[time];

                        var hour = time.substring(5, 7);
                        var timestamp = new Date(date+" "+hour+":00:00").getTime()/1000;
                        
                        chart_ranges_num[chart_timestamp_values_p_num[timestamp]] = range_p;
                    });
                });
                /* FIN DATOS PRONÓSTICO */

                            
                $('#chart_<?php echo $station->id; ?>').highcharts('StockChart', {
                    chart: {
                        height: 390,
                        marginBottom: 40,
                    },
                    title: {
                        text: ''
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
                            
                            
                            var texto_fecha = '<b><span>' + this.points[0].key; + '</span></b>';
                            return [texto_fecha].concat(
                                this.points ?
                                    this.points.map(function (point) {
                                        if(point.series.name == "<?php echo lang("machine_learning"); ?>") {

                                            return  '<b><span style="color:' + "#058DC7" + '">' + point.series.name + '</span>: </b>'
                                            + '<span>' + chart_ranges_ml[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span><br>'
                                            + '<span style="color:' + "#058DC7" + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
                                            + numberFormat(chart_ml_intervalo_confianza[point.x][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
                                            + numberFormat(chart_ml_intervalo_confianza[point.x][1], decimal_numbers, decimals_separator, thousands_separator)
                                            + ' (' + "<?php echo $unit; ?>" + ') <br>'
                                            + '<span style="color:' + "#058DC7" + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
									        + numberFormat(chart_ml_porc_conf[point.x], decimal_numbers, decimals_separator, thousands_separator) + '%';

                                        } else if(point.series.name == "<?php echo lang("neuronal"); ?>") {

                                            return  '<b><span style="color:' + "#0A5010" + '">' + point.series.name + '</span>: </b>'
                                            + '<span>' + chart_ranges_neur[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span><br>'
                                            + '<span style="color:' + "#0A5010" + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
                                            + numberFormat(chart_neur_intervalo_confianza[point.x][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
                                            + numberFormat(chart_neur_intervalo_confianza[point.x][1], decimal_numbers, decimals_separator, thousands_separator)
                                            + ' (' + "<?php echo $unit; ?>" + ') <br>'
                                            + '<span style="color:' + "#0A5010" + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
                                            + numberFormat(chart_neur_porc_conf[point.x], decimal_numbers, decimals_separator, thousands_separator) + '%';

                                        } else if(point.series.name == "<?php echo lang("numerical"); ?>") {

                                            return  '<b><span style="color:' + "#AD0000" + '">' + point.series.name + '</span>: </b>'
                                            + '<span>' + chart_ranges_num[point.y] + " (" + "<?php echo $unit; ?>" + ")" + '</span><br>'
                                            + '<span style="color:' + "#AD0000" + '">\u25CF</span> ' + '<?php echo lang("confidence_interval"); ?>: '
                                            + numberFormat(chart_num_intervalo_confianza[point.x][0], decimal_numbers, decimals_separator, thousands_separator) + ' - ' 
                                            + numberFormat(chart_num_intervalo_confianza[point.x][1], decimal_numbers, decimals_separator, thousands_separator)
                                            + ' (' + "<?php echo $unit; ?>" + ') <br>'
                                            + '<span style="color:' + "#AD0000" + '">\u25CF</span> ' + '<?php echo lang("reliability_alert_range"); ?>: '
                                            + numberFormat(chart_num_porc_conf[point.x], decimal_numbers, decimals_separator, thousands_separator) + '%';

                                        } else if(point.series.name == "<?php echo lang("real"); ?>"){
                                            return  '<b><span>' + point.series.name + '</span></b><br>'
                                            + '<span>' + numberFormat(point.y, decimal_numbers, decimals_separator, thousands_separator) + " (" + "<?php echo $unit; ?>" + ")" + '</span>';
                                        } else {
                                            return false;
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
                            width: 1,
                            color: 'black',
                            //dashStyle: 'shortdot'
                        },
                        //type: 'datetime',
                        labels: {
                            formatter: function() {
                                return chart_categories[this.value];
                            }
                        },
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
                        //lineWidth: 1,
                        min: 0,
                        max: yAxis_max,
                        // tickInterval: 50,  
                        tickPositions: array_alerts_yAxis_tickPositions,
                        plotBands: array_alerts_yAxis_plotBands,
                    }, {
                        labels: {
                            formatter: function () {
                                return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) /*+ " <?php echo $unit; ?>"*/;
                                //return this.value;
                            }
                        },
                        lineColor: 'silver',
                        //lineWidth: 1,
                        linkedTo: 0,
                        opposite: false,
                        /*plotLines: [{
                            value: 0,
                            width: 2,
                            color: 'silver'
                        }],*/
                        min: 0,
                        max: yAxis_max,
                        // tickInterval: 100,
                        tickPositions: array_alerts_yAxis_tickPositions
                    }],
                    legend: {
                        enabled: true,
                        // y: 30,
                        // floating: true
                    },
                    plotOptions: {
                        series: {
                            lineWidth: 1,
                            states: {
                                hover: {
                                    enabled: true,
                                    lineWidth: 1
                                }
                            },
                            //compare: 'percent'
                            //showInNavigator: true,
                            connectNulls: false,
                            marker: {
                                enabled: true,
                                radius: 4
                            },
                        }
                    },
                    series: [
                        {
                            accessibility: {
                                keyboardNavigation: {
                                    enabled: false
                                }
                            },
                            data: chart_default_data_m,
                            //lineColor: Highcharts.getOptions().colors[1],
                            color: '#000000',
                            fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
                            name: '<?php echo lang("real") ?>',
                            // zones: array_alerts,
                            marker: {
                                radius: 3
                            }
                        },
                        {
                            id: "serie_data_ml",
                            accessibility: {
                                keyboardNavigation: {
                                    enabled: false
                                }
                            },
                            data: chart_data_ml,
                            //lineColor: Highcharts.getOptions().colors[1],
                            color: "#058DC7",
                            fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
                            name: '<?php echo lang("machine_learning"); ?>',
                            // zones: array_alerts,
                            marker: {
                                radius: 3
                            }
                        },
                        {
                            id: "serie_data_neur",
                            accessibility: {
                                keyboardNavigation: {
                                    enabled: false
                                }
                            },
                            data: chart_data_neur,
                            //lineColor: Highcharts.getOptions().colors[1],
                            color: "#0A5010",
                            fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
                            name: '<?php echo lang("neuronal"); ?>',
                            // zones: array_alerts,
                            marker: {
                                radius: 3
                            }
                        },
                        {
                            id: "serie_data_num",
                            accessibility: {
                                keyboardNavigation: {
                                    enabled: false
                                }
                            },
                            data: chart_data_num,
                            //lineColor: Highcharts.getOptions().colors[1],
                            color: "#AD0000",
                            fillOpacity: 0, // TRANSPARENCIA PARA EL ÁREA
                            name: '<?php echo lang("numerical"); ?>',
                            // zones: array_alerts,
                            marker: {
                                radius: 3
                            }
                        }
                    ]
                });
                
                $('#chart_<?php echo $station->id; ?>').highcharts().addSeries({
                    name: 'chart_ml_intervalo_confianza',
                    data: chart_ml_intervalo_confianza,
                    type: 'arearange',
                    lineWidth: 0,
                    linkedTo: 'serie_data_ml',
                    color: "#058DC7",
                    fillOpacity: 0.3,
                    zIndex: 0,
                    marker: {
                        enabled: false
                    }
                });

                $('#chart_<?php echo $station->id; ?>').highcharts().addSeries({
                    name: 'chart_neur_intervalo_confianza',
                    data: chart_neur_intervalo_confianza,
                    type: 'arearange',
                    lineWidth: 0,
                    linkedTo: 'serie_data_neur',
                    color: "#0A5010",
                    fillOpacity: 0.3,
                    zIndex: 0,
                    marker: {
                        enabled: false
                    }
                });

                $('#chart_<?php echo $station->id; ?>').highcharts().addSeries({
                    name: 'chart_num_intervalo_confianza',
                    data: chart_num_intervalo_confianza,
                    type: 'arearange',
                    lineWidth: 0,
                    linkedTo: 'serie_data_num',
                    color: "#AD0000",
                    fillOpacity: 0.3,
                    zIndex: 0,
                    marker: {
                        enabled: false
                    }
                });
                /* FIN GRÁFICO ESTACIÓN ITERACIÓN ACTUAL | MODELO MACHINE LEARNING */


                /* TRAER DATOS DE MONITOREO DESDE API SGS ESTACIÓN ITERACIÓN ACTUAL */
                function get_monitoring_data_<?php echo $station->id; ?>() {
                    $.ajax({
                        <?php if($station->id == 2){ // HOTEL MINA CONSULTA A API METEODATA ?>
							url: '<?php echo_uri("Air_forecast_comparison/get_meteodata_monitoring_data"); ?>',
							data: { api_station_code: "mlp_es_hm" },
						<?php } else { // EL RESTO A API SGS ?>
							url: '<?php echo_uri("Air_forecast_comparison/get_sgs_monitoring_data"); ?>',
							data: { api_station_code: <?php echo $stations_api_code[$station->id]; ?> },
						<?php } ?>
						type:  'post',
                        dataType:'json',
                        beforeSend: function(){
                            $("#div_update_monitoring_data_info_<?php echo $station->id; ?>").html('<i class="fa fa-refresh fa-spin"></i> Obteniendo datos de monitoreo...');
                        },
                        success: function(result){
                            if(result.success){
                                let chart_data_m = [];
                                for(var i in result.data){
                                    chart_data_m.push([i,result.data[i]]);
                                }
                                $('#chart_<?php echo $station->id; ?>').highcharts().series[0].setData(chart_data_m, false);	
                                $('#chart_<?php echo $station->id; ?>').highcharts().redraw();
                            }
                            $("#div_update_monitoring_data_info_<?php echo $station->id; ?>").html(result.message);
                        }
                    });
                }

                get_monitoring_data_<?php echo $station->id; ?>(); // SE EJECUTA AL INGRESAR AL MÓDULO Y CADA 5 MINUTOS
                
                setInterval(get_monitoring_data_<?php echo $station->id; ?>, 600000); // 10 minutos
                /* FIN TRAER DATOS DE MONITOREO DESDE API SGS ESTACIÓN ITERACIÓN ACTUAL */

                // OCULTAR NAVEGADOR EN AMBOS GRÁFICOS
                $(".highcharts-navigator, .highcharts-areaspline-series, .highcharts-navigator-xaxis, .highcharts-navigator-yaxis").remove();
                
                $('#chart_<?php echo $station->id; ?>').highcharts().reflow();


            })();
        <?php } ?> // FIN LOOP ESTACIONES
    });
</script>