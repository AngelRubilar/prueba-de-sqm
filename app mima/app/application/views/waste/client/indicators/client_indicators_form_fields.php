<?php
$this->load->view("includes/summernote");
?>
<input type="hidden" name="id_client_indicator" value="<?php echo $id_client_indicator ?>" />
<input type="hidden" name="id_indicator" value="<?php echo $id_indicator ?>" />
<input type="hidden" name="id_project" value="<?php echo $id_project ?>" />
<!-- Valor -->
<div class="form-group">
    <label for="value" class="<?php echo $label_column; ?>"><?php echo lang('value'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "value",
            "name" => "value",
            "value" => $value,
            "class" => "form-control",
            "placeholder" => lang('value'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"data-rule-number" => true,
			"data-msg-number" => lang("enter_a_integer"),
            "autocomplete"=> "off"
			
        ));
        ?>
    </div>
</div>

<!-- Fecha de registro "desde" datepicker -->
<div class="form-group">
  <label for="date_since" class="col-md-3"><?php echo lang('date_since'); ?></label>
    <div class=" col-md-9">
        <?php
        echo form_input(array(
            "id" => "date_since",
            "name" => "date_since",
            "value" => $date_since,
            "class" => "form-control datepicker",
            "placeholder" => lang('date_since'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off",
        ));
        ?>
    </div>
</div>
<!-- Fecha de registro "hasta" datepicker -->
<div class="form-group">
  <label for="date_until" class="col-md-3"><?php echo lang('date_until'); ?></label>
    <div class=" col-md-9">
        <?php
        echo form_input(array(
            "id" => "date_until",
            "name" => "date_until",
            "value" => $date_until,
            "class" => "form-control datepicker",
            "placeholder" => lang('date_until'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"data-rule-greaterThanOrEqual" => "#date_since",
			"data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
            "autocomplete" => "off",
        ));
        ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

		setDatePicker("#client_indicators-form .datepicker");
		setTimePicker('#environmental_records-form .timepicker');

    });
</script>