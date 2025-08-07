<ul class="nav nav-tabs" role="tablist">
    <li>
        <!-- <a data-toggle="tab" href="#tab_1_min_<?php echo $id_station; ?>"><?php echo lang("records_per_minute"); ?></a> -->
    </li>

    <li class="active">
        <a data-toggle="tab" href="#tab_5_min_<?php echo $id_station; ?>"><?php echo lang("records_per_5_minutes"); ?></a>
    </li>

    <li>
        <a data-toggle="tab" href="#tab_15_min_<?php echo $id_station; ?>"><?php echo lang("records_per_15_minutes"); ?></a>
    </li>

    <li>
        <a data-toggle="tab" href="#tab_1_hour_<?php echo $id_station; ?>"><?php echo lang("records_per_1_hour"); ?></a>
    </li>
</ul>

<div role="tabpanel" class="tab-pane fade active in" id="" style="min-height: 200px;">
    <div class="tab-content">

        <!--
        <div id="tab_1_min_<?php echo $id_station; ?>" class="tab-pane fade in active">
            <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_minute"); ?></h4>
                        
                    </div>
                    <div class="panel-body">

                        <?php foreach($station_variables as $variable){ ?>
                        
                            <div id="container-station_<?php echo $id_station; ?>-time_range_1min-var_<?php echo $variable->id_variable; ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                <div style="padding:20px;"><div class="circle-loader"></div></div>
                            </div>
                            
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
        </div>
        -->

        <div id="tab_5_min_<?php echo $id_station; ?>" class="tab-pane fade in active">
            <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_5_minutes"); ?></h4>    
                    </div>
                    
                    <div class="panel-body">
                        
                        <?php foreach($station_variables as $variable){ ?>
                        
                            <div id="container-station_<?php echo $id_station; ?>-time_range_5min-var_<?php echo $variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                <div style="padding:20px;"><div class="circle-loader"></div></div>
                            </div>
                    
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>

        <div id="tab_15_min_<?php echo $id_station; ?>" class="tab-pane fade in">
             <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_15_minutes"); ?></h4>    
                    </div>
                    
                    <div class="panel-body"> 
                        
                        <?php foreach($station_variables as $variable){ ?>
                        
                            <div id="container-station_<?php echo $id_station; ?>-time_range_15min-var_<?php echo $variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                <div style="padding:20px;"><div class="circle-loader"></div></div>
                            </div>
                    
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>

        <div id="tab_1_hour_<?php echo $id_station; ?>" class="tab-pane fade in">
             <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_1_hour"); ?></h4>    
                    </div>
                    
                    <div class="panel-body"> 
                        
                        <?php foreach($station_variables as $variable){ ?>
                        
                            <div id="container-station_<?php echo $id_station; ?>-time_range_1hour-var_<?php echo $variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                <div style="padding:20px;"><div class="circle-loader"></div></div>
                            </div>
                    
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        
        //General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;

        <?php foreach($array_charts_data as $time_range => $chart_data) { ?>

            <?php if($time_range == "1min"){ continue; }?>

            <?php foreach($station_variables as $variable){ ?>

                Highcharts.stockChart('container-station_<?php echo $id_station; ?>-time_range_<?php echo $time_range; ?>-var_<?php echo $variable->id_variable; ?>',{
                    
                    rangeSelector: {
                        // selected: 1
                        enabled: false
                    },

                    exporting: {
                        filename: '<?php echo $variable->variable_name; ?>',
                        buttons: {
                            contextButton: {
                                menuItems: [{
                                    text: "<?php echo lang('export_to_png') ?>",
                                    onclick: function() {
                                        this.exportChart();
                                    },
                                    separator: false
                                }]
                            }
                        }
                    }, 

                    title: {
                        text: '<?php echo $variable->variable_name; ?>'
                    },

                    xAxis: {
                        type: 'datetime',
                        labels:{
                            formatter:function(){
                                var date = new Date(this.value);
                                fecha_f =  moment.utc(date , 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat) + ' HH:mm');
                                return fecha_f;
                            }
                        }
                    },yAxis: [{
                        // Habilitar para agregar una barra de desplazamiento en el eje Y, util al hacer zoom.
                        /* scrollbar: {
                            enabled: true,
                            // showFull: false
                        }, */
                        labels: {
                            formatter: function () {
                                return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' <?php echo $variable_unidad[$variable->id_variable]['nombre'] ?>';
                            }
                        },
                        lineColor: '#FF0000',
                        lineWidth: 1
                    }, {
                        labels: {
                            formatter: function () {
                                return numberFormat(this.value, decimal_numbers, decimals_separator, thousands_separator) + ' <?php echo $variable_unidad[$variable->id_variable]['nombre'] ?>';
                            }
                        },
                        lineColor: '#FF0000',
                        lineWidth: 1,
                        linkedTo: 0,
                        opposite: false
                    }],

                    series: [<?php echo json_encode($chart_data[$variable->id_variable]); ?>]
                });
            <?php } ?>
        
        <?php } ?>


        /// Al presionar una pestaña se debe ejecutar el evento resize() para que highchart calcule correctamente el tamaño del gráfico que debe mostrar.
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // $(window).resize();
            var evt = document.createEvent("HTMLEvents"); evt.initEvent("resize", false, true);  window.dispatchEvent(evt);
        });
    });
</script>