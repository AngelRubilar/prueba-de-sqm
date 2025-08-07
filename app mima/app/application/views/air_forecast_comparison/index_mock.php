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
                    </div>
                    
                    <div class="panel-body">

                        <div class="col-md-12">

                            <!-- GRÁFICO MACHINE LEARNING -->
                            <div class="col-md-4" id="chart_ml_<?php echo $station->id; ?>" style="margin: 0 auto;">
                                <?php echo lang('machine_learning'); ?>
                                
                                <div style="margin-top: 100px; text-align: center">
                                    <strong><?php echo lang("no_information_available"); ?></strong>
                                </div>

                            </div>

                            <!-- GRÁFICO MODELO NEURONAL -->
                            <div class="col-md-4" id="chart_neuronal_<?php echo $station->id; ?>" style="margin: 0 auto;">
                                <?php echo lang('neural_model'); ?>
                                
                                <div style="margin-top: 100px; text-align: center">
                                    <strong><?php echo lang("no_information_available"); ?></strong>
                                </div>

                            </div>

                            <!-- GRÁFICO MODELO NUMÉRICO -->
                            <div class="col-md-4" id="chart_numerical_<?php echo $station->id; ?>" style="margin: 0 auto;">
                                <?php echo lang('numerical'); ?>
                                
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

        let plotBands = [	//Franjas de color por turno
            {from: 0, to: 8, color: '#F0F0F0'},
            {from: 8, to: 20, color: '#F7F7F7'},
            {from: 20, to: 24, color: '#F0F0F0'}
        ];

        // MOCK DATA
        let serie_ex = [
            {
                name: 'Real',
                data: [66,54,79,70,62,67,58,79,63,61,53,41,61,74,52,48,40,76,78,63,42,54,46,40]

            }, {
                name: 'Pronostico',
                data: [42.000,58.930,47.560,36.810,38.300,68.370,65.080,50.400,36.080,68.770,65.340,61.710,36.530,44.240,52.000,32.380,64.200,34.190,60.550,57.460,67.060,43.150,60.100,67.470]
            }
        ]

        let categories = ["Mar 00 hrs","Mar 01 hrs","Mar 02 hrs","Mar 03 hrs","Mar 04 hrs","Mar 05 hrs","Mar 06 hrs","Mar 07 hrs","Mar 08 hrs","Mar 09 hrs","Mar 10 hrs","Mar 11 hrs","Mar 12 hrs","Mar 13 hrs","Mar 14 hrs","Mar 15 hrs","Mar 16 hrs","Mar 17 hrs","Mar 18 hrs","Mar 19 hrs","Mar 20 hrs","Mar 21 hrs","Mar 22 hrs","Mar 23 hrs"];

        let area_range = [[33.37,66.33],[35.09,77.69],[41.12,76.03],[25.07,54.67],[23.22,59.52],[43.09,81.29],[52.97,66.55],[30.3,77.39],[10.74,40.03],[67.31,69.17],[49.76,88.95],[44.8,61.79],[35.49,54.84],[21.43,70.66],[29.86,52.66],[7.95,56.75],[59.69,81.6],[12.98,48.41],[34.34,65.19],[47.96,85.73],[58.67,83.97],[31.18,67.78],[58.19,66.34],[59.7,67.95]];
        // FIN MOCK DATA

        <?php foreach($stations as $station){ ?>
            

            // GRÁFICO MACHINE LEARNING
            $('#chart_ml_<?php echo $station->id; ?>').highcharts('StockChart',{
                title: {
                    text: '<?php echo lang('machine_learning'); ?>'
                },
                navigator: {
                    enabled: false
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
                xAxis: {
                    //crosshair: true
                    crosshair: {
                        id: 'plot-line-1',
                        width: 1,
                        color: 'black',
                    },
                    categories: categories,
                    labels: {
                        formatter: function() {
                        // 	if(this.pos < 24){
                        // 		return '<span style="color:black;font-weight:bold;">'+chart_categories[this.value]+'</span>';
                        // 	}else{
                        // 		return chart_categories[this.value];
                        // 	}
                        return categories[this.value];
                        }
                    },
                    plotBands: plotBands,
                    tickInterval: 1,
                },
                series: serie_ex
            });


            $('#chart_ml_<?php echo $station->id; ?>').highcharts().addSeries({
                name: 'Range',
                data: area_range,
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

            //GRÁFICO MODELO NEURONAL
            $('#chart_neuronal_<?php echo $station->id; ?>').highcharts('StockChart',{
                
                title: {
                    text: '<?php echo lang('neural_model'); ?>'
                },
                navigator: {
                    enabled: false
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
                xAxis: {
                    //crosshair: true
                    crosshair: {
                        id: 'plot-line-1',
                        width: 1,
                        color: 'black',
                    },
                    categories: categories,
                    labels: {
                        formatter: function() {
                        // 	if(this.pos < 24){
                        // 		return '<span style="color:black;font-weight:bold;">'+chart_categories[this.value]+'</span>';
                        // 	}else{
                        // 		return chart_categories[this.value];
                        // 	}
                        return categories[this.value];
                        }
                    },
                    plotBands: plotBands,
                    tickInterval: 1,
                },
                series: serie_ex
            });

            $('#chart_neuronal_<?php echo $station->id; ?>').highcharts().addSeries({
                name: 'Range',
                data: area_range,
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

            // GRÁFICO MODELO NUMÉRICO
            $('#chart_numerical_<?php echo $station->id; ?>').highcharts('StockChart',{
                
                title: {
                    text: '<?php echo lang('numerical'); ?>'
                },
                navigator: {
                    enabled: false
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
                xAxis: {
                    //crosshair: true
                    crosshair: {
                        id: 'plot-line-1',
                        width: 1,
                        color: 'black',
                    },
                    categories: categories,
                    labels: {
                        formatter: function() {
                        // 	if(this.pos < 24){
                        // 		return '<span style="color:black;font-weight:bold;">'+chart_categories[this.value]+'</span>';
                        // 	}else{
                        // 		return chart_categories[this.value];
                        // 	}
                        return categories[this.value];
                        }
                    },
                    plotBands: plotBands,
                    tickInterval: 1,
                },
                series: serie_ex
            });

            $('#chart_numerical_<?php echo $station->id; ?>').highcharts().addSeries({
                name: 'Range',
                data: area_range,
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

        <?php } ?>
    });
</script>