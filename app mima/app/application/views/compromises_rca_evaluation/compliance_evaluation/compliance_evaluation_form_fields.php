<!-- <input type="hidden" name="id" value="<?php //echo $model_info->id; ?>" /> -->
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<input type="hidden" name="id_evaluacion" value="<?php echo $id_evaluacion; ?>" />
<input type="hidden" name="id_valor_compromiso" value="<?php echo $id_valor_compromiso; ?>" />
<input type="hidden" name="id_evaluado" value="<?php echo $id_evaluado; ?>" />

<input type="hidden" name="select_evaluado" value="<?php echo $select_evaluado; ?>" />
<input type="hidden" name="select_valor_compromiso" value="<?php echo $select_valor_compromiso; ?>" />


<?php if($puede_editar == 3 && $puede_agregar == 3){ ?>
           
    <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
        <?php echo lang("no_permission_to_evaluate_message"); ?>
    </div> 

<?php } elseif( ($puede_editar == 2 && $puede_agregar == 3) && !$evaluaciones_propias) { ?>
	
    <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
        <?php echo lang("no_own_evaluations_message"); ?>
    </div> 

<?php } else { ?>

    <div class="form-group">
      <label for="status" class="col-md-3"><?php echo lang('evaluation'); ?></label>
        <div class="col-md-9">
            <?php
                //echo form_dropdown("evaluation", $evaluations_dropdown, "", "id='evaluation' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                echo form_dropdown("evaluation", $evaluations_dropdown, "", "id='evaluation' class='select2' ");
            ?>
        </div>
    </div>
    
    <div id="div_evaluacion">
        
        <?php if (!$id_evaluacion) {?>
        
         <?php if($puede_agregar == 3 && ($puede_editar == 1 || $puede_editar == 2)){ ?>
         
         <?php echo lang("no_evaluations_to_edit"); ?>
         
         
         <?php } else { ?>
         
            <div class="form-group">
              <label for="date_filed" class="col-md-3"><?php echo lang('evaluation_date'); ?></label>
                <div class=" col-md-9">
                    <?php
                        //$fecha_registro = get_date_format($fecha, $this->session->project_context);
                        echo form_input(array(
                            "id" => "fecha_evaluacion",
                            "name" => "fecha_evaluacion",
                            //"value" => $fecha_registro,
                            "class" => "form-control datepicker",
                            "placeholder" => lang('evaluation_date'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                            "autocomplete" => "off",
                        ));
                    ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="nombre_compromiso" class="col-md-3"><?php echo lang('compromise'); ?></label>
                <div class="col-md-9">
                    <?php echo $nombre_compromiso; ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="nombre_evaluado" class="col-md-3"><?php echo lang('evaluated'); ?></label>
                <div class="col-md-9">
                    <?php echo $nombre_evaluado; ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="status" class="col-md-3"><?php echo lang('status'); ?></label>
                <div class="col-md-9">
                    <?php
                        echo form_dropdown("estado", $estados, "", "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                    ?>
                </div>
            </div>
            
            <div id="grupo_no_cumple">
            </div>
            
            
            <div class="form-group">
              <label for="observations" class="col-md-3"><?php echo lang('observations'); ?> 
              <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('add_evaluations_details') ?>"><i class="fa fa-question-circle"></i></span>
              </label>
                <div class="col-md-9">
                   <?php
                    echo form_textarea(array(
                        "id" => "observaciones",
                        "name" => "observaciones",
                        "value" => $observaciones,
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
                            "upload_url" => get_uri("compromises_compliance_evaluation/upload_file"),
                            "validation_url" =>get_uri("compromises_compliance_evaluation/validate_file"),
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
         
         
         <?php } ?>
        
            
        
        <?php } else { ?>
        
         <?php if($puede_agregar == 1 && ($puede_editar == 1 || $puede_editar == 2 || $puede_editar == 3)) { ?>
            
            <div class="form-group">
              <label for="date_filed" class="col-md-3"><?php echo lang('evaluation_date'); ?></label>
                <div class=" col-md-9">
                    <?php
                        //$fecha_registro = get_date_format($fecha, $this->session->project_context);
                        echo form_input(array(
                            "id" => "fecha_evaluacion",
                            "name" => "fecha_evaluacion",
                            //"value" => $fecha_registro,
                            "class" => "form-control datepicker",
                            "placeholder" => lang('evaluation_date'),
                            "data-rule-required" => true,
                            "data-msg-required" => lang("field_required"),
                            "autocomplete" => "off",
                        ));
                    ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="nombre_compromiso" class="col-md-3"><?php echo lang('compromise'); ?></label>
                <div class="col-md-9">
                    <?php echo $nombre_compromiso; ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="nombre_evaluado" class="col-md-3"><?php echo lang('evaluated'); ?></label>
                <div class="col-md-9">
                    <?php echo $nombre_evaluado; ?>
                </div>
            </div>
            
            <div class="form-group">
              <label for="status" class="col-md-3"><?php echo lang('status'); ?></label>
                <div class="col-md-9">
                    <?php
                        echo form_dropdown("estado", $estados, "", "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                    ?>
                </div>
            </div>
            
            <div id="grupo_no_cumple">
            </div>
            
            <div class="form-group">
                <label for="observations" class="col-md-3"><?php echo lang('observations'); ?> 
                    <span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('add_evaluations_details') ?>"><i class="fa fa-question-circle"></i></span>
                </label>
                <div class="col-md-9">
                   <?php
                    echo form_textarea(array(
                        "id" => "observaciones",
                        "name" => "observaciones",
                        //"value" => $observaciones,
                        "class" => "form-control",
                        "placeholder" => lang('observations'),
                        "autofocus" => false,
                        "data-msg-required" => lang("field_required"),
                        "autocomplete" => "off",
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
                            "upload_url" => get_uri("compromises_compliance_evaluation/upload_file"),
                            "validation_url" =>get_uri("compromises_compliance_evaluation/validate_file"),
                            //"html_name" => 'test',
                            //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
                        ), true);
                        ?>
                        <?php //$this->load->view("includes/dropzone_preview"); ?>
                    </div>
                </div>
            </div>
            
            <?php } else { ?>
            
                <div class="form-group">
                  <label for="date_filed" class="col-md-3"><?php echo lang('evaluation_date'); ?></label>
                    <div class=" col-md-9">
                        <?php
                            //$fecha_registro = get_date_format($fecha, $this->session->project_context);
                            echo $fecha_evaluacion;
                        ?>
                    </div>
                </div>
                
                <div class="form-group">
                  <label for="nombre_compromiso" class="col-md-3"><?php echo lang('compromise'); ?></label>
                    <div class="col-md-9">
                        <?php echo $nombre_compromiso; ?>
                    </div>
                </div>
                
                <div class="form-group">
                  <label for="nombre_evaluado" class="col-md-3"><?php echo lang('evaluated'); ?></label>
                    <div class="col-md-9">
                        <?php echo $nombre_evaluado; ?>
                    </div>
                </div>
                
                <div class="form-group">
                  <label for="status" class="col-md-3"><?php echo lang('status'); ?></label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown("estado", $estados, $estado_evaluacion, "id='estado' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
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
                            "value" => $observaciones,
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
                                "upload_url" => get_uri("compromises_rca_evaluation/upload_file"),
                                "validation_url" =>get_uri("compromises_rca_evaluation/validate_file"),
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
            
            <?php } ?>
    
        <?php } ?>
     
    </div>

<?php } ?>
<!--Script here--> 
<script type="text/javascript">
    $(document).ready(function () {
       $('[data-toggle="tooltip"]').tooltip();
	   $('#compliance_evaluation-form .select2').select2();
	   setDatePicker("#fecha_evaluacion");
	   
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
		
		$("#estado").select2({
			formatResult: format,
			formatSelection: format,
			escapeMarkup: function(m) { return m; }
		});
		
		$('#evaluation').on('change', function(){
			
			id_evaluacion = $(this).val();			
			id_evaluado = "<?php echo $id_evaluado; ?>";
			id_valor_compromiso = "<?php echo $id_valor_compromiso; ?>";
			select_evaluado = "<?php echo $select_evaluado; ?>";
			select_valor_compromiso = "<?php echo $select_valor_compromiso; ?>";
			
			appLoader.show();
			
			$.ajax({
				url:  '<?php echo_uri("compromises_rca_evaluation/get_form_fields_of_evaluation") ?>',
				type:  'post',
				data: {
					id_evaluacion:id_evaluacion, 
					id_evaluado:id_evaluado, 
					id_valor_compromiso:id_valor_compromiso,
					select_evaluado:select_evaluado,
					select_valor_compromiso:select_valor_compromiso},
				success: function(respuesta){
					$('#div_evaluacion').html(respuesta);
					$("#estado").select2({
						formatResult: format,
						formatSelection: format,
						escapeMarkup: function(m) { return m; }
					});
					setDatePicker("#fecha_evaluacion");
					$("#grupo_no_cumple #criticidad").select2();
					setDatePicker("#plazo_cierre");
					$('[data-toggle="tooltip"]').tooltip();
					appLoader.hide();
				}
			});
			
		});
		
		
		//$('#estado').on('change', function(){
		//$(document).off().on("change", "#estado", function(e) {
		$(document).on("change", "#estado", function(event) {
			
			var id_estado = $(this).val();
			appLoader.show();
			
			$.ajax({
				url: '<?php echo_uri("compromises_rca_evaluation/get_fields_of_evaluation_status") ?>',
				type: 'post',
				data: {id_estado:id_estado},
				success: function(respuesta){
					$('#grupo_no_cumple').html(respuesta);
					$("#grupo_no_cumple #criticidad").select2();
					setDatePicker("#plazo_cierre");
					appLoader.hide();
				}
			});
			
			event.stopImmediatePropagation();
			
		});
	   
    });
</script>