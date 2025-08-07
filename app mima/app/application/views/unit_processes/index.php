<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("indicators"); ?> /</a>
  <a class="breadcrumb-item" href=<?php echo get_uri(); ?>><?php echo lang("unit_processes") ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('unit_processes'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("unit_processes/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_unit_process'), array("class" => "btn btn-default", "title" => lang('add_unit_process'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="unit_processes-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#unit_processes-table").appTable({
            source: '<?php echo_uri("unit_processes/admin_list_data"); ?>',
			filterDropdown: [
				{name: "id_fase", class: "w200", options: <?php echo $fases_dropdown; ?>}
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("icon"); ?>", "class": "text-center"},
				{title: "<?php echo lang("phases"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("description"); ?>", "class": "text-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			}
            //printColumns: [0, 1, 2],
            //xlsColumns: [0, 1, 2]	
        });
    });
</script>