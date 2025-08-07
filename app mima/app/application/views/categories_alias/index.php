<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("categories_alias"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('categories_alias'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("categories_alias/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_category_alias'), array("class" => "btn btn-default", "title" => lang('add_category_alias'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="categories_alias-table" class="display" cellspacing="0" width="100%"></table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#categories_alias-table").appTable({
            source: '<?php echo_uri("categories_alias/list_data"); ?>',
			filterDropdown: [
				{name: "id_categoria", class: "w200", options: <?php echo $categorias_dropdown; ?>},
				{name: "id_cliente", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("alias"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("category"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>