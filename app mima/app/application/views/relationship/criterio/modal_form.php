<?php echo form_open(get_uri("relationship/save_criterio"), array("id" => "criterio-form", "class" => "general-form", "role" => "form", "autocomplete" => "off")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("relationship/criterio/criterio_fields"); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#criterio-form").appForm({
            onSuccess: function(result) {
                $("#criterio-table").appTable({newData: result.data, dataId: result.id});
            }
        });
    });
</script>    