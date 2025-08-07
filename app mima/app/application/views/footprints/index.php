<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang('footprints'); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('footprints'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("footprints/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_footprint'), array("class" => "btn btn-default", "title" => lang('add_footprint'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="footprints-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#footprints-table").appTable({
            source: '<?php echo_uri("footprints/list_data"); ?>',
			filterDropdown: [
				{name: "id_unidad", class: "w200", options: <?php echo $unidades_dropdown; ?>},
				{name: "id_tipo_unidad", class: "w200", options: <?php echo $tipos_unidad_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("unit_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("indicator"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("icon"); ?>", "class": "text-center dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>