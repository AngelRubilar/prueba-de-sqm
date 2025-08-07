<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

	<div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?$model_info->modified:'-';
            ?>
        </div>
    </div>

    <div class="panel">
        <div class="table-responsive">
            <table id="individual_upload-table" class="display" width="100%">            
            </table>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>
<style>
#ajaxModal > .modal-dialog {
    width:80% !important;
}
</style>
<script type="text/javascript">
    $(document).ready(function () {

		$("#individual_upload-table").appTable({
			//source: '<?php //echo_uri("upload_compromises/list_data_carga_individual/". $id_compromiso_proyecto) ?>',
			source: '<?php echo_uri("stakeholders_matrix_config/list_data_view_matrix/". $id_stakeholder_matrix_config) ?>',
			//order: [[1, "asc"]],
			columns: [
				{title: "ID", "class": "text-center w10"},
				{title: "<?php echo lang("stakeholder") ?>", "class": ""},
				{title: "<?php echo lang("rut") ?>", "class": ""},
				{title: "<?php echo lang("type_of_organization") ?>", "class": ""},
				{title: "<?php echo lang("locality") ?>", "class": ""}
				<?php echo $columnas_campos ?>,
				{title: "<?php echo lang("contact") ?>", "class": ""},
				{title: "<?php echo lang("contact_phone") ?>", "class": ""},
				{title: "<?php echo lang("contact_email") ?>", "class": ""},
				{title: "<?php echo lang("contact_address") ?>", "class": ""}
				
				
			],
			rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).find('[data-toggle="tooltip"]').tooltip();
			},
			//printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
			//xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
		});
		
		$('.column-show-hide-popover').click(function(e){
			e.preventDefault();
		})
		
    });
</script>