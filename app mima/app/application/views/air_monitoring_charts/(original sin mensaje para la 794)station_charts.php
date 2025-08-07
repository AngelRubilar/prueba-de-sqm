<ul class="nav nav-tabs" role="tablist">
    <li class="active">
        <a data-toggle="tab" href="#tab_1_min_<?php echo $id_station; ?>"><?php echo lang("records_per_minute"); ?></a>
    </li>

    <li>
        <a data-toggle="tab" href="#tab_5_min_<?php echo $id_station; ?>"><?php echo lang("records_per_5_minutes"); ?></a>
    </li>

    <li>
        <a data-toggle="tab" href="#tab_15_min_<?php echo $id_station; ?>"><?php echo lang("records_per_15_minutes"); ?></a>
    </li>

    <li>
        <a data-toggle="tab" href="#tab_1_hour_<?php echo $id_station; ?>"><?php echo lang("records_per_1_hour"); ?></a>
    </li>
</ul>

<!-- PESTAÑAS DE FRECUENCIAS DE TIEMPO -->
<div role="tabpanel" class="tab-pane fade active in" id="" style="min-height: 200px;">
    <div class="tab-content">

        <!-- PESTAÑA DE REGISTROS CADA 1 MINUTO -->
        <div id="tab_1_min_<?php echo $id_station; ?>" class="tab-pane fade in active">
            <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_minute"); ?></h4>
                    </div>
                    <div class="panel-body">

                        <?php foreach($station_variables as $variable){ ?>
                        
                            <!-- DIV FILTRO POR FECHAS -->
                            <div class="col-md-12 text-right mb15">
                                <?php echo form_open(get_uri("#"), array("id" => "air_charts-form_1min_".$id_station.'_'.$variable->id_variable, "class" => "general-form", "role" => "form")); ?>
                                    
                                    <div class="col-md-3"></div>

                                    <div class="form-group col-md-3">    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "start_date_1min_".$id_station.'_'.$variable->id_variable,
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
                                    
                                    <div class="form-group col-md-3">                    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "end_date_1min_".$id_station.'_'.$variable->id_variable,
                                                "name" => "end_date",
                                                "value" => '',
                                                "class" => "form-control datetime_picker",
                                                "placeholder" => lang('until'),
                                                "data-rule-required" => "true",
                                                "data-msg-required" => lang('field_required'),
                                                "data-rule-greaterThanOrEqual" => "#start_date_1min_".$id_station.'_'.$variable->id_variable,
                                                "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>

                                    <div class="form-group col-md-3 p0"> 
                                        <button id="btn_apply_1min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-primary">
                                            <span class="fa fa-eye"></span> <?php echo lang('apply'); ?>
                                        </button>  
                                        <button type="button" id="btn_clean_1min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-default">
                                            <span class="fa fa-broom"></span> <?php echo lang('clean'); ?>
                                        </button>
                                    </div>

                                <?php echo form_close(); ?>
                            </div>

                            <!-- DIV GRÁFICO 1 MINUTO -->
                            <div class="col-md-12">
                                <div id="container_1min_<?php echo $id_station.'_'.$variable->id_variable; ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                    <div style="padding:20px;"><div class="circle-loader"></div></div>
                                </div>
                            </div>
                            
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
        </div>


        <!-- PESTAÑA DE REGISTROS CADA 5 MINUTOS -->
        <div id="tab_5_min_<?php echo $id_station; ?>" class="tab-pane fade in">
            
            <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_5_minutes"); ?></h4>    
                    </div>
                    
                    <div class="panel-body">
                        
                        <?php foreach($station_variables as $variable){ ?>
                        
                            <!-- DIV FILTRO POR FECHAS -->
                            <div class="col-md-12 text-right mb15">
                                <?php echo form_open(get_uri("#"), array("id" => "air_charts-form_5min_".$id_station.'_'.$variable->id_variable, "class" => "general-form", "role" => "form")); ?>
                                                                
                                    <div class="form-group col-md-3"></div>

                                    <div class="form-group col-md-3">    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "start_date_5min_".$id_station.'_'.$variable->id_variable,
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
                                    
                                    <div class="form-group col-md-3">                    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "end_date_5min_".$id_station.'_'.$variable->id_variable,
                                                "name" => "end_date",
                                                "value" => '',
                                                "class" => "form-control datetime_picker",
                                                "placeholder" => lang('until'),
                                                "data-rule-required" => "true",
                                                "data-msg-required" => lang('field_required'),
                                                "data-rule-greaterThanOrEqual" => "#start_date_5min_".$id_station.'_'.$variable->id_variable,
                                                "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>

                                    <div class="form-group col-md-3 p0"> 
                                        <button id="btn_apply_5min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-primary">
                                            <span class="fa fa-eye"></span> <?php echo lang('apply'); ?>
                                        </button>
                                        <button type="button" id="btn_clean_5min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-default">
                                            <span class="fa fa-broom"></span> <?php echo lang('clean'); ?>
                                        </button>
                                    </div>

                                <?php echo form_close(); ?>
                            </div>

                            <!-- DIV GRÁFICO 5 MINUTOS -->
                            <div class="col-md-12">
                                <div id="container_5min_<?php echo $id_station.'_'.$variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                    <div style="padding:20px;"><div class="circle-loader"></div></div>
                                </div>
                            </div>
                    
                        <?php } ?>
                    </div>

                </div>
            </div>

        </div>



        <!-- PESTAÑA DE REGISTROS CADA 15 MINUTOS -->
        <div id="tab_15_min_<?php echo $id_station; ?>" class="tab-pane fade in">
             <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_15_minutes"); ?></h4>    
                    </div>
                    <div class="panel-body"> 
                        
                        <?php foreach($station_variables as $variable){ ?>
                        
                            <!-- DIV FILTRO POR FECHAS -->
                            <div class="col-md-12 text-right mb15">
                                <?php echo form_open(get_uri("#"), array("id" => "air_charts-form_15min_".$id_station.'_'.$variable->id_variable, "class" => "general-form", "role" => "form")); ?>
                               
                                    <div class="form-group col-md-3"></div>

                                    <div class="form-group col-md-3">    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "start_date_15min_".$id_station.'_'.$variable->id_variable,
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
                                    
                                    <div class="form-group col-md-3">                    
                                        <?php 
                                            echo form_input(array(
                                                "id" => "end_date_15min_".$id_station.'_'.$variable->id_variable,
                                                "name" => "end_date",
                                                "value" => '',
                                                "class" => "form-control datetime_picker",
                                                "placeholder" => lang('until'),
                                                "data-rule-required" => "true",
                                                "data-msg-required" => lang('field_required'),
                                                "data-rule-greaterThanOrEqual" => "#start_date_15min_".$id_station.'_'.$variable->id_variable,
                                                "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
                                                "autocomplete" => "off",
                                            ));
                                        ?>
                                    </div>

                                    <div class="form-group col-md-3 p0"> 
                                        <button id="btn_apply_15min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-primary">
                                            <span class="fa fa-eye"></span> <?php echo lang('apply'); ?>
                                        </button>
                                        <button type="button" id="btn_clean_15min_<?php echo $id_station.'_'.$variable->id_variable; ?>" class="btn btn-default">
                                            <span class="fa fa-broom"></span> <?php echo lang('clean'); ?>
                                        </button>
                                    </div>

                                <?php echo form_close(); ?>
                            </div>

                            <!-- DIV GRÁFICO 15 MINUTOS -->
                            <div class="col-md-12">
                                <div id="container_15min_<?php echo $id_station.'_'.$variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                    <div style="padding:20px;"><div class="circle-loader"></div></div>
                                </div>
                            </div>
                    
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>
        


        <!-- PESTAÑA DE REGISTROS CADA 1 HORA -->
        <div id="tab_1_hour_<?php echo $id_station; ?>" class="tab-pane fade in">
             <div class="col-md-12 p0">
                <div class="panel panel-default mb15">
                    <div class="page-title clearfix">
                        <h4><?php echo lang("records_per_1_hour"); ?></h4>    
                    </div>
                    <div class="panel-body"> 
                        
                        <?php foreach($station_variables as $variable){ ?>
                            
                            <!-- DIV FILTRO POR FECHAS -->
                            <div class="col-md-12 text-right mb15">
                                <?php echo form_open(get_uri("#"), array("id" => "air_charts-form_1hour_".$id_station.'_'.$variable->id_variable, "class" => "general-form", "role" => "form")); ?>
                                    
                                    <div class="form-group col-md-3"></div>
                                    
                                    <div class="form-group col-md-3">    
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
                                    
                                    <div class="form-group col-md-3">                    
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
                            <div class="col-md-12">
                                <div id="container_1hour_<?php echo $id_station.'_'.$variable->id_variable;  ?>" style="height: 400px; min-width: 310px; margin-bottom:100px;">
                                    <div style="padding:20px;"><div class="circle-loader"></div></div>
                                </div>
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

        /** Se le da el formato datetime a los inputs*/
		$('.datetime_picker').datetimepicker({
			format: "YYYY-MM-DD HH:mm",
			locale: moment.locale(),
			useCurrent: false,
			showClear: true,
			showClose:true
		});
		
        

        // Dentro de este loop se crean los gráficos, funciones para filtrar por fecha y para actualizar los gráficos cada 1 minuto. Esto es para cada "frecuencia" y variable.
        <?php foreach($array_charts_data as $time_range => $chart_data) { ?>

            <?php foreach($station_variables as $variable){ ?>

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

                // Si vienen datos para la variable se muestra el gráfico, si no vienen datos se muestra un mensaje al usuario
                <?php if($chart_data[$variable->id_variable]['data']){ ?>

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
                        credits: {
                            enabled: false
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
                                            return  '<b><span>' + point.series.name + ':</span></b> ' + '<span>' + numberFormat(point.y, decimal_numbers, decimals_separator, thousands_separator) + ' (<?php echo $variable_unidad[$variable->id_variable]['nombre'] ?>)' + '</span><br>';
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

                        series: [<?php echo json_encode($chart_data[$variable->id_variable]); ?>]
                    });


                    
                    // Se usa el "ID compuesto" para crear variables Javascript para guardar el ID del último valor cargado al gráfico. Se crea una por cada variable y frecuencia de tiempo.
                    let lastSyncDataID_<?php echo $compound_id;?> = 0;

                    // Función para traer último valor registrado en la DB
                    function getLastValue_<?php echo $compound_id; ?>(){

                        $.ajax({
                            url: '<?php echo_uri('air_monitoring_charts/get_last_value'); ?>',
                            type: 'post',
                            dataType: 'json',
                            data:{
                                id_station: <?php echo $id_station; ?>,
                                id_variable: <?php echo $variable->id_variable; ?>,
                                // str_timestamp: last_datetime
                            },success: function (respuesta){
                                
                                let dateTime = parseInt(respuesta['data'][0]);
                                let varValue = eval(respuesta['data'][1]);
                                

                                // Si el id de la respuesta es igual que el de la última respuesta no se carga el punto en el gráfico
                                if(respuesta['id'] != lastSyncDataID_<?php echo $compound_id;?> && dateTime != null && varValue != null){
                                    
                                    chart_<?php echo $compound_id; ?>.series[0].addPoint([dateTime, varValue], true, true);
                                }
                                // Se guarda el Id de la respuesta en la variable js creada dinámicamente
                                lastSyncDataID_<?php echo $compound_id;?> = respuesta['id'];
                                
                            }
                        });

                    }


                    // ACTUALIZAR GRÁFICO: Función para que se actualice el gráfico cada 1 minuto
                    let interval_<?php echo $compound_id; ?> = 0;   // ID para hacer referencia al Intervalo creado. Se usa para desactivarlo
                    <?php if($time_range == '1min'){?>
                        
                        // Se almacena el ID del intervalo para poder desactivarlo cuando sea necesario
                        interval_<?php echo $compound_id; ?> = setInterval( getLastValue_<?php echo $compound_id; ?>, 60000);

                        // console.log('Agregar intervalo '+ interval_<?php echo $compound_id; ?>);
                    <?php } ?>
                    


                    /*  BOTON APLICAR: Evento para aplicar filtro de rango de fechas. 
                    Se actualiza el gráfico con los datos retornados y se desactiva la actualización automática del gráfico */
                    $("#btn_apply_<?php echo $time_range.'_'.$id_station.'_'.$variable->id_variable; ?>").on('click', function(){

                        let start_date = $("#start_date_<?php echo $compound_id;?>").val();
                        let end_date = $("#end_date_<?php echo $compound_id;?>").val();

                        if((start_date && end_date) && (end_date >= start_date)){
                            $.ajax({
                                url: '<?php echo_uri('air_monitoring_charts/get_values_by_date'); ?>',
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

                        // Se desactiva el intervalo creado
                        <?php if($time_range == '1min'){?>
                            // console.log('limpiar intervalo '+ interval_<?php echo $compound_id; ?>);
                            clearInterval(interval_<?php echo $compound_id; ?>);

                            interval_<?php echo $compound_id; ?> = null;
                        <?php } ?>
                    });


                    
                    /*  BOTON LIMPIAR: Evento para limpiar el gráfico y cargar los datos que se muestran al cargar la página. 
                    Se actualiza el gráfico con los datos retornados */
                    $("#btn_clean_<?php echo $time_range.'_'.$id_station.'_'.$variable->id_variable; ?>").on('click', function(){

                        $.ajax({
                            url: '<?php echo_uri('air_monitoring_charts/clean_data'); ?>',
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

                        // Se vuelve a crear la funcion para actualizar el gráfico
                        <?php if($time_range == '1min'){ ?>
                            // console.log('limpiar intervalo '+ interval_<?php //echo $compound_id; ?>);
                            
                            // Sólo se puede crear nuevamente la función intervalo si es que esta se eliminó antes, si no existirían 2 funciones a la vez para el mismo gráfico
                            if(interval_<?php echo $compound_id; ?> == null){
                                interval_<?php echo $compound_id; ?> = setInterval( getLastValue_<?php echo $compound_id; ?>, 60000);
                            }
                        <?php } ?>
                    });

                <?php }else{ ?> // Si no vienen datos para el gráfico se muestra un mensaje y se deshabilitan los botones e inputs tipo fecha.

                    <?php if($time_range == '1min'){
                        $msg = lang('no_data_for_variable_1m') .'<b>'. $variable->variable_name .'</b>';
                    } elseif($time_range == '5min'){
                        $msg = lang('no_data_for_variable_5m') .'<b>'. $variable->variable_name .'</b>';
                    } elseif($time_range == '15min'){
                        $msg = lang('no_data_for_variable_15m') .'<b>'. $variable->variable_name .'</b>';
                    } elseif($time_range == '1hour'){
                        $msg = lang('no_data_for_variable_1h') .'<b>'. $variable->variable_name .'</b>';
                    }?>

                    $('#start_date_<?php echo $compound_id; ?>').prop( "disabled", true);
                    $('#end_date_<?php echo $compound_id; ?>').prop( "disabled", true);
                    $('#btn_apply_<?php echo $compound_id; ?>').prop( "disabled", true);
                    $('#btn_clean_<?php echo $compound_id; ?>').prop( "disabled", true);

                    $('#container_<?php echo $compound_id; ?>').html('<div style="padding:20px; text-align:center"><?php echo $msg; ?></div>');
                    
                    $('#container_<?php echo $compound_id; ?>').css('text-align','center');
                    $('#container_<?php echo $compound_id; ?>').css('height','100px');

                <?php } ?>

            <?php } ?>
        
        <?php } ?>


        /// Al presionar una pestaña se debe ejecutar el evento resize() para que highchart calcule correctamente el tamaño del gráfico que debe mostrar.
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // $(window).resize();
            var evt = document.createEvent("HTMLEvents"); evt.initEvent("resize", false, true);  window.dispatchEvent(evt);
        });


    });
</script>