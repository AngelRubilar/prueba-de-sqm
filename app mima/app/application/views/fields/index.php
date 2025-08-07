<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("fields"); ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('fields'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("fields/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_field'), array("class" => "btn btn-default", "title" => lang('add_field'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="fields-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#fields-table").appTable({
            source: '<?php echo_uri("fields/list_data"); ?>',
			filterDropdown: [
				{name: "id_tipo_campo", class: "w200", options: <?php echo $tipos_campo_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>}
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("field_name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("field_type"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("created_by"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
        });
    });
</script>