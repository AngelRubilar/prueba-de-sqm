<div id="page-content" class="p20 clearfix">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("waste"); ?> /</a>
 	  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("indicators"); ?></a>
    </nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('indicators'); ?></h1>
            <div class="title-button-group">
				<!--<div class="btn-group" role="group">
					<button type="button" class="btn btn-success" id="excel"><i class='fa fa-table'></i> <?php echo lang('export_to_excel')?></button>
				</div>--> 
                <?php echo modal_anchor(get_uri("indicators/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_indicator'), array("class" => "btn btn-default", "title" => lang('add_indicator'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="indicators-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
	
</div>
<script type="text/javascript">
$(document).ready(function () {

	$("#indicators-table").appTable({
		source: '<?php echo_uri("indicators/list_data"); ?>',
		filterDropdown: [
			{name: "id_categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>},
			{name: "id_project", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
			{name: "id_client", class: "w200", options: <?php echo $clientes_dropdown; ?>}
		],
		columns: [
			{title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
			{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("indicator_name"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
			{title: "<?php echo lang("color"); ?>", "class": "text-center",
				render: function (data, type, row) {
					return '<center>'+data+'</center>';
				}
			
			},
			{title: "<?php echo lang("categories"); ?>", "class": "text-left dt-head-center"},
			{title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
		]
	});


});
</script>