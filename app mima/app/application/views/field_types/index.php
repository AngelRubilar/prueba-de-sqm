<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href='dashboard'><?php echo lang(dashboard) ?> /</a>
  <a class="breadcrumb-item" href='#'><?php echo lang(administration) ?> /</a>
  <a class="breadcrumb-item" href=<? get_uri(); ?>><?php echo lang(field_types) ?></a>
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('field_types'); ?></h1>
            <div class="title-button-group">
                <?php //echo modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_client'), array("class" => "btn btn-default", "title" => lang('add_client'))); ?>
            </div>
        </div>
        <div class="table-responsive">
        <table id="field-types-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
            
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#field-types-table").appTable({
            source: '<?php echo_uri("field_types/list_data") ?>',
            columns: [
                {title: "<?php echo lang("id") ?>", "class": "text-center w50"},
                {title: "<?php echo lang("field_type") ?>"},
                {title: "<?php echo lang("description") ?>"},
				//{title: "<?php echo lang("enabled") ?>"},
                //{title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ], 
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
        });
    });
</script>