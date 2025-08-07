<input type="hidden" name="id_alert_config" value="<?php echo $id_alert_config; ?>" />
<input type="hidden" name="id_client" value="<?php echo $id_client; ?>" />
<input type="hidden" name="id_project" value="<?php echo $id_project; ?>" />
<input type="hidden" name="id_modulo" value="<?php echo $id_modulo; ?>" />
<input type="hidden" name="id_submodulo" value="<?php echo $id_submodulo; ?>" />

<input type="hidden" name="nombre_item" value="<?php echo $nombre_item; ?>" />

<?php if($id_modulo == "2") { // Registros AMbientales ?>

    <input type="hidden" name="id_categoria" value="<?php echo $id_categoria; ?>" />
    <input type="hidden" name="id_unidad" value="<?php echo $id_unidad; ?>" />
    <input type="hidden" name="nombre_unidad" value="<?php echo $nombre_unidad; ?>" />
    <input type="hidden" name="id_tipo_unidad" value="<?php echo $id_tipo_unidad; ?>" />
    
<?php } elseif($id_modulo == "6") { // Compromisos ?>
	
    <?php if($id_planificacion){?>
   		<input type="hidden" name="id_planificacion" value="<?php echo $id_planificacion; ?>" />
    <?php } else {?>
   		<input type="hidden" name="id_valor_compromiso" value="<?php echo $id_valor_compromiso; ?>" />
    	<input type="hidden" name="tipo_evaluacion" value="<?php echo $tipo_evaluacion; ?>" />
    <?php } ?>

<?php } elseif($id_modulo == "7") {  // Permisos ?>

	<input type="hidden" name="id_valor_permiso" value="<?php echo $id_valor_permiso; ?>" />

<?php } elseif($id_modulo == "12") {  // Recordbook ?>

	<input type="hidden" name="id_valor_recordbook" value="<?php echo $id_valor_recordbook; ?>" />

<?php } ?>

<div class="form-group">
    <label for="module" class="<?php echo $label_column; ?>"><?php echo lang('module'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $modulo;
        ?>
    </div>
</div>

<div class="form-group">
    <label for="submodule" class="<?php echo $label_column; ?>"><?php echo lang('ayn_item'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $nombre_item;
        ?>
    </div>
</div>

<?php if($id_modulo == "2") { // Registros Ambientales ?>
    
    <div class="form-group">
    	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('risk_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="risk_value" class="col-md-3"><?php echo lang('risk_value')." ".$info; ?></label>
        <div class="col-md-6">
            <?php
                echo form_input(array(
                    "id" => "risk_value",
                    "name" => "risk_value",
                    "value" => $risk_value,
                    "class" => "form-control",
                    "placeholder" => lang('risk_value'),
                    //"autofocus" => true,
                    //"data-rule-required" => true,
                    //"data-msg-required" => lang("field_required"),
					"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                    "data-msg-regex" => lang("number_or_decimal_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));
            ?>
        </div>
        <div class="col-md-3">
            <?php echo $nombre_unidad; ?>
        </div>
    </div>

<?php } elseif($id_modulo == "6") { // Compromisos ?>

	<?php if($id_submodulo == "4" || $id_submodulo == "22"){ // Evaluación de Compromisos RCA || Evaluación de Compromisos Reportables ?>
        
        <?php if($id_planificacion){ ?>
		
            <div class="form-group">
            	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('risk_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
                <label for="risk_value" class="col-md-3"><?php echo lang('risk_value')." ".$info; ?></label>
                <div class="col-md-6">
                    <?php
                        echo form_input(array(
                            "id" => "risk_value",
                            "name" => "risk_value",
                            "value" => $risk_value,
                            "class" => "form-control",
                            "placeholder" => lang('risk_value'),
							//"data-rule-required" => true,
							//"data-msg-required" => lang("field_required"),
							"data-rule-regex" => "^[1-9][0-9]*$",
							"data-msg-regex" => lang("integer_greater_than_zero"),
                            //"autofocus" => true,
                            "autocomplete"=> "off",
                            "maxlength" => "255"
                        ));
                    ?>
                </div>
                <div class="col-md-3">
                    <?php echo lang("days_before"); ?>
                </div>
            </div>
        
		<?php } else { ?>
        	
            <div class="form-group">
            	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('risk_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
                <label for="risk_value" class="col-md-3"><?php echo lang('risk_value')." ".$info; ?></label>
                <div class="col-md-9">
                    <?php
                        echo form_dropdown("risk_value", $estados, array($risk_value), "id='risk_value' class='select2'");
                    ?>
                </div>
            </div>
            
        <?php } ?>
                
	<?php } ?>
    
<?php } elseif($id_modulo == "7") { // Permisos ?>

	<div class="form-group">
    	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('risk_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="risk_value" class="col-md-3"><?php echo lang('risk_value')." ".$info; ?></label>
        <div class="col-md-9">
            <?php
                echo form_dropdown("risk_value", $estados, array($risk_value), "id='risk_value' class='select2'");
            ?>
        </div>
    </div>

<?php } elseif($id_modulo == "12") {  // Recordbook ?>

	
    <div class="form-group">
    	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('risk_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="risk_value" class="col-md-3"><?php echo lang('risk_value')." ".$info; ?></label>
        <div class="col-md-9">
            <?php
                echo form_dropdown("risk_value", $propositos_visita_dropdown, array($risk_value), "id='risk_value' class='select2'");
            ?>
        </div>
    </div>
    
<?php } ?>

<div class="form-group">
    <label for="risk_email_alert" class="<?php echo $label_column; ?>"><?php echo lang('email')." - ".lang("risk"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_checkbox("risk_email_alert", "1", ($is_risk_email_alert) ? true : false, "id='risk_email_alert' ");
        ?>
    </div>
</div> 

<div class="form-group">
    <label for="risk_web_alert" class="<?php echo $label_column; ?>"><?php echo lang('web')." - ".lang("risk"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_checkbox("risk_web_alert", "1", ($is_risk_web_alert) ? true : false, "id='risk_web_alert' ");
        ?>
    </div>
</div> 

<?php if($id_modulo == "2") { // Registros Ambientales ?>
    <div class="form-group">
        <?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('threshold_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value')." ".$info; ?></label>
        <div class="col-md-6">
            <?php
                echo form_input(array(
                    "id" => "threshold_value",
                    "name" => "threshold_value",
                    "value" => $threshold_value,
                    "class" => "form-control",
                    "placeholder" => lang('threshold_value'),
                    //"autofocus" => true,
                    //"data-rule-required" => true,
                    //"data-msg-required" => lang("field_required"),
					"data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                    "data-msg-regex" => lang("number_or_decimal_required"),
                    "autocomplete"=> "off",
                    "maxlength" => "255"
                ));
            ?>
        </div>
        <div class="col-md-3">
            <?php echo $nombre_unidad; ?>
        </div>
    </div>
<?php } elseif($id_modulo == "6") { // Compromisos ?>

	<?php if($id_submodulo == "4" || $id_submodulo == "22"){ // Evaluación de Compromisos RCA || Evaluación de Compromisos Reportables ?>
        
        <?php if($id_planificacion){ ?>
        
        	<div class="form-group">
            	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('threshold_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
                <label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value')." ".$info; ?></label>
                <div class="col-md-6">
                    <?php
                        echo form_input(array(
                            "id" => "threshold_value",
                            "name" => "threshold_value",
                            "value" => $threshold_value,
                            "class" => "form-control",
                            "placeholder" => lang('threshold_value'),
                            //"data-rule-required" => true,
                            //"data-msg-required" => lang("field_required"),
							"data-rule-regex" => "^[1-9][0-9]*$",
							"data-msg-regex" => lang("integer_greater_than_zero"),
                            "autocomplete"=> "off",
                            "maxlength" => "255"
                        ));
                    ?>
                </div>
                <div class="col-md-3">
                    <?php echo lang("days_after"); ?>
                </div>
            </div>
        
        <?php } else { ?>
        
        	<div class="form-group">
            	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('threshold_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
                <label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value')." ".$info; ?></label>
                <div class="col-md-9">
                    <?php
                        echo form_dropdown("threshold_value", $estados, array($threshold_value), "id='threshold_value' class='select2'");
                    ?>
                </div>
            </div>

        <?php } ?>
        
	<?php } ?>
    
<?php } elseif($id_modulo == "7") { // Permisos ?>

	<div class="form-group">
    	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('threshold_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value')." ".$info; ?></label>
        <div class="col-md-9">
            <?php
                echo form_dropdown("threshold_value", $estados, array($threshold_value), "id='threshold_value' class='select2'");
            ?>
        </div>
    </div>

<?php } elseif($id_modulo == "12") {  // Recordbook ?>

    <div class="form-group">
    	<?php $info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('threshold_value_msj').'"><i class="fa fa-question-circle"></i></span>'; ?>
        <label for="threshold_value" class="col-md-3"><?php echo lang('threshold_value')." ".$info; ?></label>
        <div class="col-md-9">
            <?php
                echo form_dropdown("threshold_value", $propositos_visita_dropdown, array($threshold_value), "id='threshold_value' class='select2'");
            ?>
        </div>
    </div>
    
<?php } ?>

<div class="form-group">
    <label for="threshold_email_alert" class="<?php echo $label_column; ?>"><?php echo lang('email')." - ".lang("threshold"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_checkbox("threshold_email_alert", "1", ($is_threshold_email_alert) ? true : false, "id='threshold_email_alert' ");
        ?>
    </div>
</div> 

<div class="form-group">
    <label for="threshold_web_alert" class="<?php echo $label_column; ?>"><?php echo lang('web')." - ".lang("threshold"); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo form_checkbox("threshold_web_alert", "1", ($is_threshold_web_alert) ? true : false, "id='threshold_web_alert' ");
        ?>
    </div>
</div> 

 
<div class="form-group">
	<label for="groups" class="<?php echo $label_column; ?>"><?php echo lang('groups'); ?></label>
    <div class="<?php echo $field_column; ?>">
		<?php
            //form_multiselect("groups[]", $groups, "", "id='groups' class='select2 validate-hidden multiple' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "' multiple='multiple'");
            echo form_multiselect("groups[]", $array_client_groups, $selected_client_groups, "id='groups' class='select2 multiple' multiple='multiple'");
		?>
    </div>
</div>

<div id="users_group">
    <div class="form-group">
        <label for="users" class="<?php echo $label_column; ?>"><?php echo lang('users'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_multiselect("users[]", $array_client_users, $selected_client_users, "id='users_admin_config' class='select2 multiple' multiple='multiple'");
            ?>
        </div>
    </div>
</div>

 

<script type="text/javascript">
    $(document).ready(function () {
		
		$('[data-toggle="tooltip"]').tooltip();
        		
		function format(state) {
			array = state.text.split('#');
			var color = array[array.length - 1]; //último elemento del array (color)
			var nombre_estado = state.text.substring(0, state.text.lastIndexOf("#"));
			if(state.text != '-'){
				return "<div class='pull-left' style='background-color: #" + color + "; border: 1px solid black; height:15px; width:15px; border-radius: 50%;'></div>" + "&nbsp;&nbsp;" + nombre_estado;
			}else{
				return state.text;
			}
		}
		
		<?php if($id_modulo == "6" && ($id_submodulo == "4" || $id_submodulo == "22")) { // Compromisos &&  Evaluación de Compromisos RCA || Evaluación de Compromisos Reportables ?>
			
			<?php if(!$id_planificacion) { ?>
				$("#risk_value, #threshold_value").select2({
					formatResult: format,
					formatSelection: format,
					escapeMarkup: function(m) { return m; }
				});
				$("#groups, #users_admin_config").select2();
			<?php } else { ?>
				$('#alert_config_users-form .select2').select2();	
			<?php } ?>
			
		<?php } elseif($id_modulo == "7") { // Permisos ?>
		
			$("#risk_value, #threshold_value").select2({
				formatResult: format,
				formatSelection: format,
				escapeMarkup: function(m) { return m; }
			});
			$("#groups, #users_admin_config").select2();
			
		<?php } else { ?>
		
			$('#alert_config_users-form .select2').select2();
			
		<?php } ?>
				
		$("#groups").change(function(){
			
			var id_client = '<?php echo $id_client; ?>';
			var id_project = '<?php echo $id_project; ?>';
			var groups = $(this).val();
			var evento = "admin_config";
						
			$.ajax({
                url:  '<?php echo_uri("general_settings/get_user_members_of_groups") ?>',
                type:  'post',
                data: {id_client: id_client, id_project: id_project, groups: groups, evento: evento},
                //dataType:'json',
                success: function(respuesta){
                    $('#users_group').html(respuesta);    
                    $('#users_admin_config').select2();
                }
                
            });
			

		});
		
		
    });
</script>