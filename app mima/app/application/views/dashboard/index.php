<div id="page-content" class="p20 clearfix">

    <div class="row">
    	<div class="col-lg-2"></div>
        <div class="col-lg-8 text-center" style=" padding-top: 100px">
        	<img src="<?php echo get_file_uri("assets/images/mimasoft-logo-fondo.png"); ?>" style="max-width: 100%; height: auto; margin: 0 auto;" class="img-responsive mx-auto d-block">
        </div>
        <div class="col-lg-2"></div>

    	<!--
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12 mb20 text-center">
                    <div class="bg-white">
                    
                    
                        <div class="box">
                            <div class="box-content widget-container b-r">
                                <div class="panel-body ">
                                    <h1 class="">0</h1>
                                    <span class="text-off uppercase">Proyectos Abiertos</span>
                                </div>
                            </div>
                            <div class="box-content widget-container ">
                                <div class="panel-body ">
                                    <h1>0</h1>
                                    <span class="text-off uppercase">Subproyectos</span>
                                </div>
                            </div>
                        </div>
                        <div class="box b-t bg-white">
                            <div class="box-content widget-container b-r">
                                <div class="panel panel-default mb0 no-boxshadow">
                                    <div class="panel-body ">
                                        <h1>0</h1>
                                        <span class="text-off uppercase">Clientes Habilitados</span>
                        
                                    </div>
                                </div>
                            </div>
                            <div class="box-content widget-container">
                                <div class="panel panel-default mb0 b-l no-boxshadow">
                                    <div class="panel-body ">
                                        <h1 class="">1</h1>
                                        <span class="text-off uppercase">Clientes Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    /*if ($show_invoice_statistics) {
                        invoice_statistics_widget();
                    } else if($show_project_timesheet){
                        project_timesheet_statistics_widget();
                    }*/
                    ?> 
                </div>
            </div>


        </div>-->
		<!--
        <div class="col-md-7 widget-container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-clock-o"></i>&nbsp;  <?php echo lang("project_timeline"); ?>
                </div>
                <div id="project-timeline-container">
                    <div class="panel-body"> 
                        <?php
                        activity_logs_widget(array("log_for" => "project", "limit" => 10));
                        ?>
                    </div>
                </div>
            </div>
        </div>
		-->
        
    </div>

</div>
<?php
load_js(array(
    "assets/js/flot/jquery.flot.min.js",
    "assets/js/flot/jquery.flot.pie.min.js",
    "assets/js/flot/jquery.flot.resize.min.js",
    "assets/js/flot/curvedLines.js",
    "assets/js/flot/jquery.flot.tooltip.min.js",
));
?>
<script type="text/javascript">
    $(document).ready(function () {
        
        initScrollbar('#project-timeline-container', {
            setHeight: 955
        });

    });
</script>    

