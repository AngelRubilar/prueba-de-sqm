<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("model"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("subcategories"); ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('subcategories'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("subcategories/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_subcategory'), array("class" => "btn btn-default", "title" => lang('add_subcategory'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="subcategories-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#subcategories-table").appTable({
            source: '<?php echo_uri("subcategories/list_data"); ?>',
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2])
        });
    });
</script>