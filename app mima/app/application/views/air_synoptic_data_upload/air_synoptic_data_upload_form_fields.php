<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

<div class="form-group">
	<label for="date_filed" class="col-md-3"><?php echo lang('date'); ?></label>
	<div class=" col-md-9">
		<?php
			echo form_input(array(
				"id" => "date",
				"name" => "date",
				"value" => $model_info->date,
				"class" => "form-control datepicker",
				"placeholder" => lang('date'),
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 24 HRS 00:00 08:00 T1-->
<div class="form-group">
	<label for="pmca_24_hrs_t1" class=" col-md-3"><?php echo lang('pmca_24_hrs_t1'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_24_hrs_t1", $pmca_options, $pmca_24_hrs_t1, "id='pmca_24_hrs_t1' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_24_hrs_t1" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_24_hrs_t1",
				"name" => "prob_pmca_24_hrs_t1",
				"value" => $prob_pmca_24_hrs_t1,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 24 HRS 08:00 16:00 T2-->
<div class="form-group">
	<label for="pmca_24_hrs_t2" class=" col-md-3"><?php echo lang('pmca_24_hrs_t2'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_24_hrs_t2", $pmca_options, $pmca_24_hrs_t2, "id='pmca_24_hrs_t2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_24_hrs_t2" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_24_hrs_t2",
				"name" => "prob_pmca_24_hrs_t2",
				"value" => $prob_pmca_24_hrs_t2,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 24 HRS 16:00 00:00 T3-->
<div class="form-group">
	<label for="pmca_24_hrs_t3" class=" col-md-3"><?php echo lang('pmca_24_hrs_t3'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_24_hrs_t3", $pmca_options, $pmca_24_hrs_t3, "id='pmca_24_hrs_t3' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_24_hrs_t3" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_24_hrs_t3",
				"name" => "prob_pmca_24_hrs_t3",
				"value" => $prob_pmca_24_hrs_t3,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 48 HRS 00:00 08:00 T1-->
<div class="form-group">
	<label for="pmca_48_hrs_t1" class=" col-md-3"><?php echo lang('pmca_48_hrs_t1'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_48_hrs_t1", $pmca_options, $pmca_48_hrs_t1, "id='pmca_48_hrs_t1' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_48_hrs_t1" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_48_hrs_t1",
				"name" => "prob_pmca_48_hrs_t1",
				"value" => $prob_pmca_48_hrs_t1,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 48 HRS 08:00 16:00 T2-->
<div class="form-group">
	<label for="pmca_48_hrs_t2" class=" col-md-3"><?php echo lang('pmca_48_hrs_t2'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_48_hrs_t2", $pmca_options, $pmca_48_hrs_t2, "id='pmca_48_hrs_t2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_48_hrs_t2" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_48_hrs_t2",
				"name" => "prob_pmca_48_hrs_t2",
				"value" => $prob_pmca_48_hrs_t2,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 48 HRS 16:00 00:00 T3-->
<div class="form-group">
	<label for="pmca_48_hrs_t3" class=" col-md-3"><?php echo lang('pmca_48_hrs_t3'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_48_hrs_t3", $pmca_options, $pmca_48_hrs_t3, "id='pmca_48_hrs_t3' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_48_hrs_t3" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_48_hrs_t3",
				"name" => "prob_pmca_48_hrs_t3",
				"value" => $prob_pmca_48_hrs_t3,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 72 HRS 00:00 08:00 T1-->
<div class="form-group">
	<label for="pmca_72_hrs_t1" class=" col-md-3"><?php echo lang('pmca_72_hrs_t1'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_72_hrs_t1", $pmca_options, $pmca_72_hrs_t1, "id='pmca_72_hrs_t1' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_72_hrs_t1" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_72_hrs_t1",
				"name" => "prob_pmca_72_hrs_t1",
				"value" => $prob_pmca_72_hrs_t1,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 72 HRS 08:00 16:00 T2-->
<div class="form-group">
	<label for="pmca_72_hrs_t2" class=" col-md-3"><?php echo lang('pmca_72_hrs_t2'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_72_hrs_t2", $pmca_options, $pmca_72_hrs_t2, "id='pmca_72_hrs_t2' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_72_hrs_t2" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_72_hrs_t2",
				"name" => "prob_pmca_72_hrs_t2",
				"value" => $prob_pmca_72_hrs_t2,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!-- 72 HRS 16:00 00:00 T3-->
<div class="form-group">
	<label for="pmca_72_hrs_t3" class=" col-md-3"><?php echo lang('pmca_72_hrs_t3'); ?></label>
	<div class=" col-md-4">
		<?php
			echo form_dropdown("pmca_72_hrs_t3", $pmca_options, $pmca_72_hrs_t3, "id='pmca_72_hrs_t3' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		?>
	</div>
	
	<label for="prob_pmca_72_hrs_t3" class=" col-md-1"> Prob </label>
	<div class="col-md-4">
		<?php 
			echo form_input(array(
				"id" => "prob_pmca_72_hrs_t3",
				"name" => "prob_pmca_72_hrs_t3",
				"value" => $prob_pmca_72_hrs_t3,
				"class" => "form-control",
				"placeholder" => '%',
				"data-rule-required" => true,
				"data-msg-required" => lang("field_required"),
				"data-rule-range" => '0, 100',
				"data-msg-range" => lang('percentage_not_between_0_100'),
				"autocomplete" => "off",
			));
		?>
	</div>
</div>

<!--DOCUMENTO DE RESPALDO -->
<?php if(!$model_info->evidence_file){ ?>

	<div class="form-group">
		<label for="backup_document" class="col-md-3"><?php echo lang('backup_document'); ?></label>
		<div id="dropzone_bulk" class="col-md-9">
			<?php
				echo $this->load->view("includes/air_synoptic_evidence_file_uploader", array(
					"upload_url" => get_uri("air_synoptic_data_upload/upload_file"),
					"validation_url" =>get_uri("air_synoptic_data_upload/validate_file")
				), true);
			?>
		</div>
	</div>

<?php } else { ?>

	<div id="dropzone_group_pdf">	
        <div class="form-group">
        <label for="backup_document" class=" col-md-3"><?php echo lang('backup_document'); ?> </label>
            <div class="col-md-9">
                <?php 
					$html = '<div class="col-md-8">';
					$html .= $evidence_file_name;
					$html .= '</div>';
					$html .= '<div class="col-md-4">';
					$html .= '<table id="table_delete_evidence_file" class="table_delete"><thead><tr><th></th></tr></thead>';
					$html .= '<tbody><tr><td class="option text-center">';
					$html .= anchor(get_uri("air_synoptic_data_upload/download_file/".$model_info->id."/evidence_file"), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
					$html .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $model_info->id, "data-file_type" => "evidence_file", "data-action-url" => get_uri("air_synoptic_data_upload/delete_file"), "data-action" => "delete-confirmation"));
					//$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
					$html .= '</td>';
					$html .= '</tr>';
					$html .= '</thead>';
					$html .= '</table>';
					$html .= '</div>';
				
					echo $html;
				?>
            </div>
        </div>
	</div>

<?php } ?>

<!-- OBSERVACIONES -->
<div class="form-group">
	<label for="observations" class="col-md-3"><?php echo lang('observations'); ?></label>
	<div class="col-md-9">
		<?php
		echo form_textarea(array(
			"id" => "observations",
			"name" => "observations",
			"value" => $model_info->observations,
			"class" => "form-control",
			"placeholder" => lang('observations'),
			"autofocus" => false,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
		));
		?>
	</div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('#air_synoptic_data_upload-form .select2').select2();
		setDatePicker("#air_synoptic_data_upload-form .datepicker");
		setTimePicker('#air_synoptic_data_upload-form .timepicker');

    });
</script>