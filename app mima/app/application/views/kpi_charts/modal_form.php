<?php echo form_open(get_uri("KPI_Charts/save"), array("id" => "kpi_charts-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("kpi_charts/kpi_charts_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#kpi_charts-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#kpi_charts-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
		<?php if($model_info->id) { ?>
			initScrollbar(".modal-body", {setHeight: 400});
		<?php } else { ?>
			initScrollbar(".modal-body", {setHeight: 300});
		<?php } ?>
        //$("#company_name").focus();
    });
</script>    