<?php echo form_open(get_uri("dashboard/save_file/".$id_sector), array("id" => "air_files-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
		<!--<label for="file" class="col-md-3"><?php echo lang('file'); ?> </label>-->
		<div class="col-md-12">
			<?php                      
				echo $this->load->view("includes/form_file_uploader", array(			
					"upload_url" => get_uri("dashboard/upload_file"),
					"validation_url" =>get_uri("dashboard/validate_file"),
                    "html_name" => 'file_name',
                    "id_campo" => 'air_file',
					//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
				), true);
			?>
		</div>
	</div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" id="save_form" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>
<style>

.modal-footer{
	position: relative;
	z-index: 999;
	background-color: #FFF;
	height: 80px;
}

</style>
<script type="text/javascript">
    $(document).ready(function() {
        $("#air_files-form").appForm({
            onSuccess: function(result) {
                $("#files-table_<?php echo $id_sector; ?>").appTable({newData: result.data, dataId: result.id});
            }
        });
    });
</script>  