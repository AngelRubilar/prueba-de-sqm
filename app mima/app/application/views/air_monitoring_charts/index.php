<div id="page-content" class="p20 clearfix" style="min-height:600px;">

    <nav class="breadcrumb">
        <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
        <a class="breadcrumb-item" href="<?php echo get_uri("air_monitoring_charts"); ?>"><?php echo lang("air_charts"); ?></a>
    </nav>

    <?php if($puede_ver != 3) { ?>

        <div class="page-title clearfix mb20">
            <h1><i class="fa fa-industry" title=""></i> <?php echo lang('air_charts'); ?></h1>
        </div>

    
            
        <!-- Tabs de estaciones -->
        <div class="col-sm-3 col-lg-2"> <!-- required for floating -->
            <!-- Nav tabs -->
            <ul id = "station_ul" class="nav nav-tabs vertical" >
                <?php 
                $loop_count = 0;
                $class_active = "";
                            
                foreach($stations as $station){
                    // if($loop_count == 0 && !$preselected_station){
                    //     $class_active = "active";
                    // }elseif($preselected_station == $station['station_info']->id){
                    //     $class_active = "active";
                    // } 
                    if($loop_count == 0) $class_active = "active";
                    ?>
                
                    <li class= <?php echo $class_active;?> > 
                        <a href="#tab<?php echo $station->id; ?>" id="anchor_tab_<?php echo $station->id; ?>" class="anchor_station" data-toggle="tab" id_station="<?php echo $station->id; ?>"> 
                            <?php echo $station->name ?>
                        </a>
                    </li>
                        
                    <?php
                    $loop_count++;
                    $class_active = "";
                } ?>
            </ul>
        </div>
        <!-- Fin Tabs de estaciones -->

        <!-- Panel de estacion seleccionada en tabs -->
        <div role="tabpanel" class="tab-pane fade active in" >
            <!-- Tab panes -->
            <div class="tab-content">

                <?php 
                $loop_count = 0;
                $class_active = "";

                foreach($stations as $station){ 
                    // if($loop_count == 0 && !$preselected_station){
                    //     $class_active = "active";
                    // }elseif($preselected_station == $station['station_info']->id){
                    //     $class_active = "active";
                    // }

                    if($loop_count == 0) 
                        $class_active = "active"; ?>

                    <div class="tab-pane fade in <?php echo $class_active;?>" id="tab<?php echo $station->id; ?>">

                        <div class="col-sm-9 col-lg-10">
                            
                            <div class="panel panel-default">

                                <div class="panel-heading">
                                    <h4><?php echo $station->name; ?></h4>
                                </div>

                                
                                <div class="panel-body">
                                    <div class="charts_group_<?php echo $station->id; ?>" data-first_call=true>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                    $loop_count++;
                    $class_active = "";
                } ?>

            </div>
        </div>


    <?php } else { ?>

    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">Ã—</span></button>-->
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
  $(document).ready(function () {
    var firstLoad = true;
    console.log('empieza la funcion')
    function request_chart(id_station) {
      $.ajax({
        url: '<?php echo_uri('air_monitoring_charts/get_station_charts'); ?>',
        type: 'post',
        data: { id_station: id_station },
        beforeSend: function () {
          $('.charts_group_' + id_station).html('<div class="circle-loader"></div>');
        },
        success: function (respuesta) {
          $('.charts_group_' + id_station).html('');
          $('.charts_group_' + id_station).append(respuesta);
          $('.charts_group_' + id_station).data('first_call', false);
        }
      });
    }

    // Cargar la 1era pestaña al cargarse la página
    var id_station = <?php echo $stations[0]->id; ?>;
    if (firstLoad) {
      console.log('firstload')
      request_chart(id_station);
      firstLoad = false;
    }

    // Se ejecuta al presionar un tab correspondiente a una estación
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      id_station = $(this).attr("id_station");

      let first_call = $('.charts_group_' + id_station).data('first_call');

      // Solo se carga la pestaña la primera vez que se hace click en esta.
      if (first_call) {
        console.log('segunda vez')
        console.log(id_station);
        request_chart(id_station);
      }
    });
  });
</script>