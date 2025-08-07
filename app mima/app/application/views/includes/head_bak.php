<head>
    <?php $this->load->view('includes/meta'); ?>
    <?php $this->load->view('includes/helper_js'); ?>
    <?php $this->load->view('includes/plugin_language_js'); ?>

    <?php
    load_css(array(
        "assets/bootstrap/css/bootstrap.min.css",
        //"assets/js/font-awesome-4.7.0/css/font-awesome.min.css",
		//"assets/js/font-awesome-5.0.13/css/font-awesome.min.css",
		"assets/js/font-awesome-5.3.1/css/all.css",
		"assets/js/font-awesome-5.3.1/css/v4-shims.css",
        "assets/js/datatable/css/jquery.dataTables.min.css",
        "assets/js/datatable/TableTools/css/dataTables.tableTools.min.css",
		"assets/js/datatable/css/dataTables.checkboxes.css",
        "assets/js/select2/select2.css",
		//"assets/js/select2 v4/css/select2.css",
        "assets/js/select2/select2-bootstrap.min.css",
        "assets/js/bootstrap-datepicker/css/datepicker3.css",
        "assets/js/bootstrap-timepicker/css/bootstrap-timepicker.min.css",
		"assets/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css",
        "assets/js/bootstrap-datetimepicker/css/bootstrap-datetimepicker-standalone.css",
		"assets/js/bootstrap-colorpicker/css/colorpicker.css",
        "assets/js/x-editable/css/bootstrap-editable.css",
        "assets/js/dropzone/dropzone.min.css",
        "assets/js/magnific-popup/magnific-popup.css",
        //"assets/js/malihu-custom-scrollbar/jquery.mCustomScrollbar.min.css",
		"assets/js/malihu-custom-scrollbar/jquery.mCustomScrollbar.css",
		"assets/js/loudev-multi-select/css/multi-select.css",
		"assets/js/jquery-ui.css",// slider
        "assets/js/awesomplete/awesomplete.css",
        "assets/js/slick-1.8.1/slick/slick.css",
        "assets/js/slick-1.8.1/slick/slick-theme.css",
        "assets/js/slick-1.8.1/slick/custom.css",
        "assets/css/font.css",
        "assets/css/style.css",
        "assets/css/custom-style.css",

        "assets/js/leaflet/leaflet.css", // Mapas
        "assets/js/velocity/leaflet-velocity.css", // Mapas
        "assets/js/timedimension/leaflet.timedimension.control.min.css", // Mapas
        //"assets/js/leaflet-isolines/dist/leaflet-isolines.css", // Mapas

        "assets/js/calheatmap/cal-heatmap.css",  // CalHeatMap
		
    ));
	
    load_js(array(
        "assets/js/jquery-1.11.3.min.js",
        "assets/bootstrap/js/bootstrap.min.js",
        //"assets/js/jquery-validation/jquery.validate.min.js",
		"assets/js/jquery-validation/jquery.validate.js",
        "assets/js/jquery-validation/jquery.form.js",
        "assets/js/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js",
        //"assets/js/datatable/js/jquery.dataTables.min.js",
		"assets/js/datatable/js/jquery.dataTables.js",
		"assets/js/datatable/js/accent-neutralise.js",
		"assets/js/datatable/js/dataTables.checkboxes.js",
        "assets/js/select2/select2.js",
		//"assets/js/select2 v4/js/select2.js",
		"assets/js/rut/jquery.rut.js",
		"assets/js/fullcalendar/moment.min.js",
		"assets/js/loudev-multi-select/js/jquery.multi-select.2.js",
        "assets/js/datatable/TableTools/js/dataTables.tableTools.min.js",
		//"assets/js/datatable/js/dataTables.responsive.js",
        //"assets/js/datatable/js/datetime-moment.js",
        "assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
        "assets/js/bootstrap-timepicker/js/bootstrap-timepicker.js",
		"assets/js/bootstrap-colorpicker/js/bootstrap-colorpicker.js",
        "assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js",
        "assets/js/x-editable/js/bootstrap-editable.min.js",
        "assets/js/dropzone/dropzone.min.js",
        //"assets/js/highcharts/highcharts.js",
        "assets/js/highcharts/highstock.js",
		"assets/js/highcharts/highcharts-more.js",
		"assets/js/highcharts/variable-pie.js",
        "assets/js/highcharts/windbarb.js",
        "assets/js/highcharts/pattern-fill.js",
        "assets/js/highcharts/exporting.js",
        "assets/js/highcharts/accessibility.js",

        "assets/js/magnific-popup/jquery.magnific-popup.min.js",
		"assets/js/sortable/sortable.js",
		"assets/js/jquery-ui.js",// slider
		"assets/js/bootstrap-maxlength/src/bootstrap-maxlength.js",
		"assets/js/lookingfor/jquery.lookingfor.js",
        //"assets/js/notificatoin_handler.js",
        "assets/js/slick-1.8.1/slick/slick.js",
		"assets/js/ayn_handler.js",
        "assets/js/general_helper.js",
        //"assets/js/app.min.js"
        "assets/js/app.js",
        
        "assets/js/leaflet/leaflet.js", // Mapas
        "assets/js/heatmap/heatmap.js", // Mapas
        "assets/js/heatmap/leaflet-heatmap.js", // Mapas
        "assets/js/leaflet-arrows/leaflet-arrows.js", // Mapas
        "assets/js/leaflet-arrows/WindScale.js", // Mapas
        "assets/js/velocity/leaflet-velocity.js", // Mapas
        "assets/js/timedimension/iso8601.min.js", // Mapas
        "assets/js/timedimension/leaflet.timedimension.js", // Mapas
        "assets/js/timedimension/leaflet.timedimension.util.js", // Mapas
        "assets/js/timedimension/leaflet.timedimension.layer.js", // Mapas
        "assets/js/timedimension/leaflet.timedimension.player.js", // Mapas
        "assets/js/timedimension/leaflet.timedimension.control.js",  // Mapas
        "assets/js/timedimension/leaflet.timedimension.layer.geojson.js", // Mapas
        //"assets/js/leaflet-isolines/dist/leaflet-isolines.js", // Mapas

        "assets/js/turf/turf.min.js", // Mapas

        "assets/js/calheatmap/d3.min.js",  // CalHeatMap
        "assets/js/calheatmap/d3.v3.min.js",  // CalHeatMap
        "assets/js/calheatmap/cal-heatmap.js",  // CalHeatMap

    ));
    ?>
</head>