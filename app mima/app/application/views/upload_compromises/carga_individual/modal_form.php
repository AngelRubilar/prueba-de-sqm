<?php echo form_open(get_uri("upload_compromises/save_carga_individual/".$id_compromiso_proyecto."/".$tipo_matriz), array("id" => "individual_upload-form", "class" => "general-form", "role" => "form", "autocomplete" => "off")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("upload_compromises/carga_individual/carga_individual_form_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#individual_upload-form").appForm({
            onSuccess: function(result) {
                $("#individual_upload-table").appTable({newData: result.data, dataId: result.id});
            }
        });
    });
</script>    