<!-- <input type="hidden" name="id" value="<?php //echo $model_info->id; ?>" /> -->
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>" />
<input type="hidden" name="id_responsable" value="<?php echo $id_responsable; ?>" />
<input type="hidden" name="proposito_visita" value="<?php echo $proposito_visita; ?>" />
<input type="hidden" name="id_valor_feedback" value="<?php echo $valor_feedback->id; ?>" />

<input type="hidden" name="select_proposito_visita" value="<?php echo $select_proposito_visita; ?>" />
<input type="hidden" name="select_responsable" value="<?php echo $select_responsable; ?>" />

<!--
<input type="hidden" name="select_evaluado" value="<?php echo $select_evaluado; ?>" />
<input type="hidden" name="select_valor_compromiso" value="<?php echo $select_valor_compromiso; ?>" />
-->

<div class="form-group">
  <label for="date" class="col-md-3"><?php echo lang('date'); ?></label>
    <div class="col-md-9">
        <?php echo get_date_format($valor_feedback->fecha, $id_proyecto); ?>
    </div>
</div>

<div class="form-group">
  <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
    <div class="col-md-9">
        <?php echo $valor_feedback->nombre; ?>
    </div>
</div>

<div class="form-group">
  <label for="visit_purpose" class="col-md-3"><?php echo lang('visit_purpose'); ?></label>
    <div class="col-md-9">
        <?php echo lang($valor_feedback->proposito_visita); ?>
    </div>
</div>

<!--
<div class="form-group">
  <label for="responsable" class="col-md-3"><?php echo lang('responsible'); ?></label>
    <div class="col-md-9">
        <?php echo $responsable; ?>
    </div>
</div>
-->

<div class="form-group">
  <label for="answer" class="col-md-3"><?php echo lang('answer'); ?></label>
    <div class="col-md-9">
       <?php
        echo form_textarea(array(
            "id" => "answer",
            "name" => "answer",
            "value" => $feedback_evaluation_info->respuesta,
            "class" => "form-control",
            "placeholder" => lang('answer'),
            "autofocus" => false,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "2000"
        ));
        ?>
    </div>
</div>

<div class="form-group">
  <label for="answer_status" class="col-md-3"><?php echo lang('answer_status'); ?></label>
    <div class="col-md-9">
        <?php
			
			$estados = array(
				"" => "-",
				"Abierto" => "Abierto",
				"Cerrado" => "Cerrado",
			);
		
        	echo form_dropdown("answer_status", $estados, array($feedback_evaluation_info->estado_respuesta), "id='answer_status' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>

<div class="form-group">
  <label for="file" class="col-md-3"><?php echo lang('upload_evidence_file'); ?></label>
    <div class="col-md-9">
    	<div id="dropzone_bulk" class="">
			<?php
            
            echo $this->load->view("includes/feedback_evaluation_file_uploader", array(
                "upload_url" => get_uri("feedback_monitoring/upload_file"),
                "validation_url" =>get_uri("feedback_monitoring/validate_file"),
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
       // $('[data-toggle="tooltip"]').tooltip();
	   $('#compliance_evaluation-form .select2').select2();
	   
	   $('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
	   
	   /*
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
		
		$("#estado").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		*/
	   
    });
</script>