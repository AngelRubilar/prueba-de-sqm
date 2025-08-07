<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("indicators"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("functional_units"); ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('functional_units'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("functional_units/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_functional_unit'), array("class" => "btn btn-default", "title" => lang('add_functional_unit'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="functional_units-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#functional_units-table").appTable({
            source: '<?php echo_uri("functional_units/list_data"); ?>',
			filterDropdown: [
				{name: "id_subproyecto", class: "w200", options: <?php echo $subproyectos_dropdown; ?>},
				{name: "id_proyecto", class: "w200", options: <?php echo $proyectos_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>}
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
				{title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("subproject"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("unit"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: [0, 1, 2],
            //xlsColumns: [0, 1, 2]	
        });
    });
</script>