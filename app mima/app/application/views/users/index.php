<div id="page-content" class="p20 clearfix">


<!--Breadcrumb section-->
<nav class="breadcrumb"> 
  <a class="breadcrumb-item" href="#"><?php echo lang("projects"); ?> /</a>
  <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang('users'); ?></a> 
</nav>

    <div class="panel panel-default">
        <div class="page-title clearfix">
            <h1><?php echo lang('users'); ?></h1>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("users/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_user'), array("class" => "btn btn-default", "title" => lang('add_user'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="users-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
		
        $("#users-table").appTable({
            source: '<?php echo_uri("users/list_data"); ?>',
			filterDropdown: [
				{name: "id_client_context_profile", class: "w200", options: <?php echo $perfiles_generales_dropdown; ?>},
				{name: "id_profile", class: "w200", options: <?php echo $perfiles_dropdown; ?>},
				{name: "is_admin", class: "w200", options: <?php echo $roles_dropdown; ?>},
				{name: "client_id", class: "w200", options: <?php echo $clientes_dropdown; ?>},
			],
            columns: [
                {title: "<?php echo lang("id"); ?>", "class": "text-right dt-head-center w50"},
                {title: "<?php echo lang("name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("last_name"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("rut"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("email"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("role"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("project_profile"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("general_profile"); ?>", "class": "text-left dt-head-center"},
				{title: "<?php echo lang("client"); ?>", "class": "text-left dt-head-center"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w150"}
            ],
            //printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            //xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5 , 6])
        });
    });
</script>