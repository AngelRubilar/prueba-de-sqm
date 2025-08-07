<div id="page-content" class="p20 clearfix">

<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("profiles") . " " . lang("generals"); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('profiles') . " " . lang("generals"); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("generals/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_profile'), array("class" => "btn btn-default", "title" => lang('add_profile'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="profiles-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#profiles-table").appTable({
            source: '<?php echo_uri("generals/list_data"); ?>',
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				//{title: "<?php echo lang("created_by"); ?>", "class": "text-left dt-head-center"},
				//{title: "<?php echo lang("created_date"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
		
    });
</script>