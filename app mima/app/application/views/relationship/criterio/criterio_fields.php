<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" /> 
<!--<input type="hidden" name="criterio_id" value="<?php echo $model_info->client_id; ?>" />-->
<?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
?>
<!-- listar todos los clientes -->
<div class="form-group">
    <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		echo form_dropdown("client", $clientes, array($model_info->id_cliente), "id='client_id' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
		?>
    </div>
</div>
 <!-- listar todos los clientes -->

<!-- listar todos los proyectos -->
<div class="form-group">
    <label for="project" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("project", $proyectos, array($model_info->id_proyecto), "id='project' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>
 <!-- listar todos los proyectos -->
 
<!-- listar todos los formularios tipo RA -->
<div class="form-group">
    <label for="form" class="<?php echo $label_column; ?>"><?php echo lang('environmental_records'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("form", $formularios, array($model_info->id_formulario), "id='form' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>
<!-- listar todos los formularios tipo RA -->

<!-- listar todos los materiales -->
<div class="form-group">
    <label for="material" class="<?php echo $label_column; ?>"><?php echo lang('material'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_dropdown("material", $materiales, array($model_info->id_material), "id='material' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
        ?>
    </div>
</div>
<!-- listar todos los materiales -->

<!-- listar todos los criterio SP -->
<div class="form-group">
    <label for="subproject_rule" class="<?php echo $label_column; ?>"><?php echo lang('subproject_rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        //echo form_dropdown("subproject_rule", $campos, array($model_info->id_campo_sp), "id='subproject_rule' class='select2 validate-hidden' data-sigla=''");
        echo form_dropdown("subproject_rule", $campos, array($id_campo_sp), "id='subproject_rule' class='select2 validate-hidden' data-sigla=''");
        ?>
    </div>
</div>
<!-- listar todos los criterio SP -->
<!-- listar todos los criterio PU -->
<div class="form-group">
    <label for="unit_processes_rule" class="<?php echo $label_column; ?>"><?php echo lang('unit_processes_rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        ///echo form_dropdown("unit_processes_rule", $campos, array($model_info->id_campo_pu), "id='unit_processes_rule' class='select2 validate-hidden' data-sigla=''");
        echo form_dropdown("unit_processes_rule", $campos, array($id_campo_pu), "id='unit_processes_rule' class='select2 validate-hidden' data-sigla=''");
        ?>
    </div>
</div>
<!-- listar todos los criterio SP -->
<!-- listar todos los criterio FC -->
<div class="form-group">
    <label for="fc_rule" class="<?php echo $label_column; ?>"><?php echo lang('fc_rule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        //echo form_dropdown("fc_rule", $campos, array($model_info->id_campo_fc), "id='fc_rule' class='select2 validate-hidden' data-sigla=''");
        echo form_dropdown("fc_rule", $campos, array($id_campo_fc), "id='fc_rule' class='select2 validate-hidden' data-sigla=''");
        ?>
    </div>
</div>
<!-- listar todos los criterio FC -->

<div class="form-group">
    <label for="label" class="<?php echo $label_column; ?>"><?php echo lang('label'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "label",
            "name" => "label",
            "value" => $model_info->etiqueta,
            "class" => "form-control",
            "placeholder" => lang('label'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {
	
	$('#client_id').focus();
	$('#criterio-form .select2').select2();
	
	$('input[type="text"][maxlength]').maxlength({
		//alwaysShow: true,
		threshold: 245,
		warningClass: "label label-success",
		limitReachedClass: "label label-danger",
		appendToParent:true
	});
	
	$('#client_id').change(function(){
		
		var id_client = $(this).val();
		select2LoadingStatusOn($('#project'));
		
		$.ajax({
			url: '<?php echo_uri("clients/get_projects_of_client_json") ?>',
			type: 'post',
			data: {id_client:id_client},
			dataType: 'json',
			success: function(respuesta){
				
				select2LoadingStatusOff($('#project'));
				
				$('#project').html('');
				$.each(respuesta, function(index, row) {
					$('#project').append($("<option />").val(row.id).text(row.text));
				});
				$('#project').select2();
			}
		});
	});
	
	$('#project').change(function(){
	//$(document).on('change', '#project', function() {
		var id_project = $(this).val();
		select2LoadingStatusOn($('#form'));
		
		$.ajax({
			url: '<?php echo_uri("relationship/get_er_of_project_json") ?>',
			type: 'post',
			data: {id_project:id_project},
			dataType:'json',
			success: function(respuesta){
				
				select2LoadingStatusOff($('#form'));
				
				$('#form').html("");
				$.each((respuesta), function() {
					$('#form').append($("<option />").val(this.id).text(this.text));
				});
				$('#form').select2();
				
			}
		});
	});
	
	$('#form').change(function(){
	//$(document).on('change', '#project', function() {
		var id_form = $(this).val();
		select2LoadingStatusOn($('#material'));
		select2LoadingStatusOn($('#subproject_rule'));
		select2LoadingStatusOn($('#unit_processes_rule'));
		select2LoadingStatusOn($('#fc_rule'));
		
		$.ajax({
			url: '<?php echo_uri("relationship/get_materials_of_form") ?>',
			type: 'post',
			data: {id_form:id_form},
			dataType:'json',
			success: function(respuesta){
				
				select2LoadingStatusOff($('#material'));
				
				$('#material').html("");
				$.each((respuesta), function() {
					$('#material').append($("<option />").val(this.id).text(this.text));
				});
				$('#material').select2();
				
			}
		});
		
		$.ajax({
			url: '<?php echo_uri("relationship/get_fields_of_form") ?>',
			type: 'post',
			data: {id_form:id_form},
			dataType: 'json',
			success: function(respuesta){
				
				// CRITERIO SUBPROYECTO
				select2LoadingStatusOff($('#subproject_rule'));
				$('#subproject_rule').html("");
				$.each((respuesta), function() {
					$('#subproject_rule').append($("<option />").val(this.id).text(this.text));
				});
				$('#subproject_rule').select2();
				
				// CRITERIO PROCESO UNITARIO
				select2LoadingStatusOff($('#unit_processes_rule'));
				$('#unit_processes_rule').html("");
				$.each((respuesta), function() {
					$('#unit_processes_rule').append($("<option />").val(this.id).text(this.text));
				});
				$('#unit_processes_rule').select2();
				
				// CRITERIO FACTORES CARACTERIZACION
				select2LoadingStatusOff($('#fc_rule'));
				$('#fc_rule').html("");
				$.each((respuesta), function() {
					$('#fc_rule').append($("<option />").val(this.id).text(this.text));
				});
				$('#fc_rule').select2();
				
			}
		});
		
		
	});
	
});

</script>    