<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("stations"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('stations'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("air_stations/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_station'), array("id" => "agregar", "class" => "btn btn-default", "title" => lang('add_station'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="air_stations-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div> 
</div>

<script type="text/javascript"> 
    $(document).ready(function () {
		
        $("#air_stations-table").appTable({
            source: '<?php echo_uri("air_stations/list_data"); ?>',
			filterDropdown: [
				{name: "id_project", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_client", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description"); ?>", "class": "text-center"},
				{title: "<?php echo lang("sector"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
        });

    });
</script>