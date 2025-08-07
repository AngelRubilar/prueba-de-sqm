<div id="page-content" class="p20 clearfix" style="min-height:600px;">

    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("air_monitoring_efficiency"); ?>"><?php echo lang("air_efficiency"); ?></a>
    </nav>

    <?php if($puede_ver != 3) { ?>

        <?php foreach($array_data as $id_station => $data){ ?>

            <div class="panel panel-default">
                <div class="page-title clearfix">
                    <h1><?php echo lang('air_efficiency')." | ".$data["station"]->name." | ".lang("records_per_1_hour"); ?></h1>
                </div>
                <div class="panel-body"> 

                    <?php foreach($data["station_variables"] as $variable){ ?>

                        <?php if($variable->id_variable == 9){ // PM10 ?>
                
                            <!-- DIV FILTRO POR FECHAS -->
                            <div class="col-md-12 text-right mb15">
                                <?php echo form_open(get_uri("#"), array("id" => "air_charts-form_1hour_".$id_station.'_'.$variable->id_variable, "class" => "general-form", "role" => "form")); ?>
                                    
                                    <div class="form-group col-md-5"></div>
                                    
                                    <div class="form-group col-md-2">    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "start_date_1hour_".$id_station.'_'.$variable->id_variable,
                                                "name" => "start_date ",
                                                "value" => '',
                                                "class" => "form-control datetime_picker",
                                                "data-rule-required" => "true",
                                                "data-msg-required" => lang('field_required'),
                                                "placeholder" => lang('since'),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>
                                    
                                    <div class="form-group col-md-2">                    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "end_date_1hour_".$id_station.'_'.$variable->id_variable,
                                                "name" => "end_date",
                                                "value" => '',
                                                "class" => "form-control datetime_picker",
                                                "placeholder" => lang('until'),
                                                "data-rule-required" => "true",
                                                "data-msg-required" => lang('field_required'),
                                                "data-rule-greaterThanOrEqual" => "#start_date_1hour_".$id_station.'_'.$variable->id_variable,
                                                "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>

                                    <div class="form-group col-md-3 p0"> 
                                        <button id="btn_apply_1hour_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-primary">
                                            <span class="fa fa-eye"></span> <?php echo lang('apply'); ?>
                                        </button>
                                        <button type="button" id="btn_clean_1hour_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-default">
                                            <span class="fa fa-broom"></span> <?php echo lang('clean'); ?>
                                        </button>
                                    </div>

                                <?php echo form_close(); ?>
                            </div>

                            <!-- DIV GRÁFICO 1 HORA -->
                            <div id="container_1hour_<?php echo $id_station.'_'.$variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                <div style="padding:20px;"><div class="circle-loader"></div></div>
                            </div>

                        <?php } ?>

                    <?php } ?>

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

<script type="text/javascript">
    $(document).ready(function(){

        // General Settings
        var decimals_separator = AppHelper.settings.decimalSeparator;
        var thousands_separator = AppHelper.settings.thousandSeparator;
        var decimal_numbers = AppHelper.settings.decimalNumbers;

        /** Se le da el formato datetime a los inputs*/
		$('.datetime_picker').datetimepicker({
			format: "YYYY-MM-DD HH:mm",
			locale: moment.locale(),
			useCurrent: false,
			showClear: true,
			showClose:true
		});

        <?php foreach($array_data as $id_station => $data){ ?>

            // Dentro de este loop se crean los gráficos, funciones para filtrar por fecha y para actualizar los gráficos cada 1 minuto. Esto es para cada "frecuencia" y variable.
            <?php foreach($data["array_charts_data"] as $time_range => $chart_data) { ?>

                <?php foreach($data["station_variables"] as $variable){ ?>

                    <?php if($variable->id_variable == 9){ // PM10 ?>

                        // "ID compuesto" generado para crear variables que sean unicas por cada gráfico.
                        <?php $compound_id = $time_range.'_'.$id_station.'_'.$variable->id_variable; ?>

                        // Evitar que los formularios que tienen los filtros de fecha se ejecuten
                        $("#air_charts-form_<?php echo $compound_id; ?>").appForm({
                            ajaxSubmit: false
                        });

                        $("#air_charts-form_<?php echo $compound_id; ?>").submit(function(e){
                            e.preventDefault();
                            return false;
                        });

                        // console.log('container_<?php echo $compound_id; ?>');

                        // CREAR GRÁFICO para la variable y frecuencia de tiempo correspondiente. Se almacena en una variable para poder manipularlo
                        const chart_<?php echo $compound_id; ?> = new Highcharts.stockChart('container_<?php echo $compound_id; ?>',{
                            
                            rangeSelector: {
                                // selected:1,
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
                                text: '<?php echo $variable->variable_name .' ('.$variable->sigla .')'?>'
                            },

                            xAxis: {
                                type: 'datetime',
                                labels:{
                                    formatter:function(){
                                        var date = new Date(this.value);
                                        fecha_f =  moment.utc(date , 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat) + ' HH:mm');
                                        return fecha_f;
                                    },
                                    rotation: -22
                                }
                            },yAxis: [
                                {
                                    labels: {
                                        formatter: function () {
                                            return numberFormat(this.value, 0, decimals_separator, thousands_separator) + ' %';
                                        }
                                    },
                                    lineColor: '#FF0000',
                                    lineWidth: 1
                                }, {
                                    labels: {
                                        formatter: function () {
                                            return numberFormat(this.value, 0, decimals_separator, thousands_separator) + ' %';
                                        }
                                    },
                                    lineColor: '#FF0000',
                                    lineWidth: 1,
                                    linkedTo: 0,
                                    opposite: false
                                }
                            ],

                            tooltip: {
                                //crosshairs: [true, true],
                                formatter: function() {

                                    var date = new Date(this.points[0].key);
                                    var texto_fecha =  '<b><span>' + moment.utc(date , 'YYYY-MM-DD').format(date_format_to_moment(AppHelper.settings.dateFormat) + ' HH:mm') + ' hrs. </span></b>';

                                    return [texto_fecha].concat(
                                        this.points ?
                                            this.points.map(function (point) {
                                                return  '<b><span>' + point.series.name + ':</span></b> ' + '<span>' + numberFormat(point.y, 0, decimals_separator, thousands_separator) + '%' + '</span><br>';
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

                            legend: false,
                            credits: false,

                            series: [<?php echo json_encode($chart_data[$variable->id_variable]); ?>]
                        });


                        /*  BOTON APLICAR: Evento para aplicar filtro de rango de fechas. 
                        Se actualiza el gráfico con los datos retornados y se desactiva la actualización automática del gráfico */
                        $("#btn_apply_<?php echo $time_range.'_'.$id_station.'_'.$variable->id_variable; ?>").on('click', function(){

                            let start_date = $("#start_date_<?php echo $compound_id;?>").val();
                            let end_date = $("#end_date_<?php echo $compound_id;?>").val();

                            if((start_date && end_date) && (end_date >= start_date)){
                                $.ajax({
                                    url: '<?php echo_uri('air_monitoring_efficiency/get_values_by_date'); ?>',
                                    type: 'post',
                                    dataType: 'json',
                                    data:{
                                        time_range: "<?php echo $time_range; ?>",
                                        start_date: start_date,
                                        end_date: end_date,
                                        id_station: <?php echo $id_station; ?>,
                                        id_variable: <?php echo $variable->id_variable; ?>,

                                    },
                                    beforeSend: function(){
                                        appLoader.show();
                                    },
                                    success: function (respuesta){
                                        
                                        if(respuesta.success === true){
                                            chart_<?php echo $compound_id; ?>.series[0].setData(respuesta.data[<?php echo $variable->id_variable; ?>]['data'], true);
                                        }

                                        if (respuesta.success === false) {
                                            appAlert.success(respuesta.message, {duration: 10000});
                                        }

                                        appLoader.hide();

                                    }
                                });
                            }


                        });


                        
                        /*  BOTON LIMPIAR: Evento para limpiar el gráfico y cargar los datos que se muestran al cargar la página. 
                        Se actualiza el gráfico con los datos retornados */
                        $("#btn_clean_<?php echo $time_range.'_'.$id_station.'_'.$variable->id_variable; ?>").on('click', function(){

                            $.ajax({
                                url: '<?php echo_uri('air_monitoring_efficiency/clean_data'); ?>',
                                type: 'post',
                                dataType: 'json',
                                data:{
                                    time_range: "<?php echo $time_range; ?>",
                                    id_station: <?php echo $id_station; ?>,
                                    id_variable: <?php echo $variable->id_variable; ?>,

                                },
                                beforeSend: function(){
                                    appLoader.show();
                                },
                                success: function (respuesta){
                                    
                                    if(respuesta.success === true){
                                        chart_<?php echo $compound_id; ?>.series[0].setData(respuesta.data[<?php echo $variable->id_variable; ?>]['data'], true);

                                        $("#start_date_<?php echo $compound_id;?>").val('');
                                        $("#end_date_<?php echo $compound_id;?>").val('');
                                    }

                                    if (respuesta.success === false) {
                                        appAlert.success(respuesta.message, {duration: 10000});
                                    }

                                    appLoader.hide();

                                }
                            });

                        });

                    <?php } ?>

                <?php } ?>
            
            <?php } ?>

        <?php } ?>

    });
</script>