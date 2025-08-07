<!-- <input type="hidden" name="id" value="<?php //echo $model_info->id; ?>" /> -->
<!-- <input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" /> -->
<input type="hidden" name="agreement_monitoring_id" value="<?php echo $agreement_monitoring_id; ?>" />
<input type="hidden" name="value_agreement_id" value="<?php echo $value_agreement_id; ?>" />
<input type="hidden" name="id_stakeholder" value="<?php echo $id_stakeholder; ?>" />

<div class="form-group">
  <label for="code" class="col-md-3"><?php echo lang('code'); ?></label>
    <div class="col-md-9">
        <?php echo $value_agreement->codigo; ?>
    </div>
</div>

<div class="form-group">
  <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
    <div class="col-md-9">
        <?php echo $value_agreement->nombre_acuerdo; ?>
    </div>
</div>

<div class="form-group">
  <label for="managing" class="col-md-3"><?php echo lang('managing'); ?></label>
    <div class="col-md-9">
        <?php echo $nombre_gestor; ?>
    </div>
</div>

<div class="form-group">
  <label for="stakeholder" class="col-md-3"><?php echo lang('stakeholder'); ?></label>
    <div class="col-md-9">
        <?php echo $stakeholder->nombre; ?>
    </div>
</div>

<div class="form-group">
  <label for="processing_status" class="col-md-3"><?php echo lang('processing_status'); ?></label>
    <div class="col-md-9">
        <?php
        	echo form_dropdown("processing_status", $dropdown_estados_tramitacion, $model_info->estado_tramitacion, "id='processing_status' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
  <label for="activities_status" class="col-md-3"><?php echo lang('activities_status'); ?></label>
    <div class="col-md-9">
        <?php
        	echo form_dropdown("activities_status", $dropdown_estados_actividades, $model_info->estado_actividades, "id='activities_status' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
  <label for="financial_status" class="col-md-3"><?php echo lang('financial_status'); ?></label>
    <div class="col-md-9">
        <?php
        	echo form_dropdown("financial_status", $dropdown_estados_financieros, $model_info->estado_financiero, "id='financial_status' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
  <label for="observations" class="col-md-3"><?php echo lang('observations'); ?></label>
    <div class="col-md-9">
       <?php
        echo form_textarea(array(
            "id" => "observaciones",
            "name" => "observaciones",
            "value" => $model_info->observaciones,
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

<div class="form-group">
  <label for="file" class="col-md-3"><?php echo lang('upload_evidence_file'); ?></label>
    <div class="col-md-9">
    	<div id="dropzone_bulk" class="">
			<?php
            
            echo $this->load->view("includes/compliance_evaluation_file_uploader", array(
                "upload_url" => get_uri("agreements_monitoring/upload_file"),
                "validation_url" =>get_uri("agreements_monitoring/validate_file"),
                //"html_name" => 'test',
                //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
            ), true);
            ?>
            <?php //$this->load->view("includes/dropzone_preview"); ?>
        </div>
    </div>
</div>

<?php 
	if ($html_archivos_evidencia){ 
		echo $html_archivos_evidencia;
	}
?>

<!--Script here--> 
<script type="text/javascript">
    $(document).ready(function () {
		
	   $('#processing_status, #activities_status, #financial_status').select2();
	   
	   $('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
	   
	   function format(state) {
			
			array = state.text.split('#');
			var color = array[array.length - 1]; //último elemento del array (color)
			
			var nombre_estado = state.text.substring(0, state.text.lastIndexOf("#"));
	
			if(state.text != '-'){
				return "<div class='pull-left' style='background-color: #" + color + "; border: 1px solid black; height:15px; width:15px; border-radius: 50%;'></div>" + "&nbsp;&nbsp;" + nombre_estado;
				//<div style="background-color:'.$color_estado.'; border: 1px solid black; height:15px; width:15px; border-radius: 50%;"></div>
			}else{
				return state.text;
			}
			
		}
		
		$("#processing_status").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		
		$("#activities_status").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		
		$("#financial_status").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		
	   
    });
</script>