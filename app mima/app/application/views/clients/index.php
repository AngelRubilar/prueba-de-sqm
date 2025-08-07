<div id="page-content" class="p20 clearfix">
    
<!--Breadcrumb section-->
<nav class="breadcrumb">
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("clients"); ?></a>
</nav>
    
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('clients'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("clients/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_client'), array("class" => "btn btn-default", "title" => lang('add_client'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="client-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $("#client-table").appTable({
            source: '<?php echo_uri("clients/list_data"); ?>',
			filterDropdown: [
				{name: "habilitado", class: "w200", options: <?php echo $estados_cliente_dropdown; ?>}
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("company_name"); ?>", "class": "text-left dt-head-center"},
                {title: "<?php echo lang("initial"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("site_status"); ?>", "class": "text-center dt-head-center"},
                {title: "<?php echo lang("projects"); ?>", "class": "text-right dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ]//,
          // printColumns: [0, 1, 2, 3, 4],
         // xlsColumns: [0, 1, 2, 3, 4]
        }); 
    });   
</script>