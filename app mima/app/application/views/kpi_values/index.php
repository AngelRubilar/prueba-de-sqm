<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("kpi"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri("KPI_Values"); ?>"><?php echo lang("values") ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('values'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("KPI_Values/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_value'), array("class" => "btn btn-default", "title" => lang('add_value'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="kpi_values-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#kpi_values-table").appTable({
            source: '<?php echo_uri("KPI_Values/list_data") ?>',
			filterDropdown: [
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_fase", class: "w200", options: <?php echo $fases_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("value_name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("phase"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("created_by"); ?>", "class": "text-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            	$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
		
		$(document).on('click', '.delete', function(){
			initScrollbar(".modal-body", {setHeight: 50});
		});
		
    });
</script>