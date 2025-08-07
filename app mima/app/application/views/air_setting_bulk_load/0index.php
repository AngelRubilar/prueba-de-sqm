<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("customer_administrator"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("setting_bulk_load"); ?></a>
    </nav>

<?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

    <div class="panel">
            <?php echo form_open(get_uri("air_setting_bulk_load/save"), array("id" => "bulk_load-form", "class" => "general-form", "role" => "form")); ?>

            <div class="panel-default">
            
                <div class="page-title clearfix">
                	<h1><?php echo lang('bulk_load'); ?></h1>
                </div>

                <div class="panel-body">
                
                	<div id="excel-error" class="app-alert alert alert-danger alert-dismissible hide" role="alert">
                    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                        <div class="alert-message">
                        	
                        </div>
                        <div class="progress">
                        	<div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                
                    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
                    

					<div class="form-group">
                        <label for="record_type" class="col-md-12"><?php echo lang('record_type'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("record_type", $tipos_de_registros, "", "id='record_type' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="model" class="col-md-12"><?php echo lang('model'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("model", array("" => "-"), "", "id='model' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

					<div class="form-group">
                        <label for="sector" class="col-md-12"><?php echo lang('sector'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("sector", array("" => "-"), "", "id='sector' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

					<div class="form-group">
                        <label for="receptor" class="col-md-12"><?php echo lang('receptor'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("receptor", array("" => "-"), "", "id='receptor' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>

					<div class="form-group">
                        <label for="variable" class="col-md-12"><?php echo lang('variable'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("variable", array("" => "-"), "", "id='variable' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>


					<!--
                    <div class="form-group">
                    	<div class="col-md-1">
                    		<i id="bulk_load_help" class="fa fa-question-circle" data-toggle="popover"></i>
                        </div>
                        <div class="col-md-11">
                        </div>
                    </div>
                    -->
                    
                    <div id="grupo_plantilla" class="hide">
                        <div class="circle-loader"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class=" col-md-2"></label>
                        <div class="col-md-10">
                            <?php
                            $this->load->view("includes/file_list", array("files" => $model_info->files));
                            ?>
                        </div>
                    </div>
                    
                    <div id="dropzone_bulk" class="col-md-12">
						<?php
                        
                        echo $this->load->view("includes/bulk_file_uploader", array(
                            "upload_url" => get_uri("air_setting_bulk_load/upload_file"),
                            "validation_url" =>get_uri("air_setting_bulk_load/validate_file"),
                            //"html_name" => 'test',
                            //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
                        ), true);
                        ?>
                        <?php //$this->load->view("includes/dropzone_preview"); ?>
                    </div>
                    
                    <div class="col-md-12">
                    	<span class="pull-right"><?php echo lang("maximum_file_size").": ".get_setting("max_file_size")."MB"; ?></span>
                    </div>
                    
                    
                </div>
                <div class="panel-footer clearfix">
                    <button id="btn_bulk_load" type="submit" class="btn btn-primary pull-right"><span class="fa fa-upload"></span> <?php echo lang('load'); ?></button>
                </div>
            </div>

            <?php echo form_close(); ?>
            
    </div> 
    
    <div class="panel">
    	<div class="panel-default">
			<div id="resume_table" class="table-responsive" style="max-height:500px;"></div>
        </div>
    </div>
    
</div>

<?php } else { ?><!-- Se aplica la configuración de perfil (ver ninguno) -->

    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>

<?php } ?><!-- Fin configuración de perfil (ver todos) -->

<script type="text/javascript">
    $(document).ready(function () {
		
		$.ajax({
			url: "<?php echo get_uri('Setting_bulk_load/get_intructions') ?>",
			type: 'POST',
			data: {},
			success: function (result) {
				$('#bulk_load_help').popover({
					container: 'body',
					//trigger:'hover',
					placement: 'right',
					title: '<?php echo lang('intructions'); ?>',
					html:true,
					content: result
				});
			}
		});
		
		$('body').on('click', function (e) {
			//did not click a popover toggle or popover
			if ($(e.target).data('toggle') !== 'popover'
				&& $(e.target).parents('.popover.in').length === 0) { 
				$('[data-toggle="popover"]').popover('hide');
			}
		});
				
		$('#bulk_load-form .select2').select2();
		
		$('#record_type').change(function(){
		//$(document).on('change', '#project', function() {
			var id_record_type = $(this).val();
			$('#excel-error').addClass('hide');
			
			if(id_record_type){
				
				select2LoadingStatusOn($('#model'));
				
				$.ajax({
					url:  '<?php echo_uri("air_setting_bulk_load/get_models_of_record_type") ?>',
					type:  'post',
					data: {id_record_type:id_record_type},
					dataType:'json',
					success: function(respuesta){

						$('#model').html("");
						$.each((respuesta), function() {
							$('#model').append($("<option />").val(this.id).text(this.text));
						});
						$('#model').select2();
						
						select2LoadingStatusOff($('#model'));
						
						$('#sector, #receptor, #variable').html("");
						$('#sector, #receptor, #variable').append($("<option />").val("").text("-"));
						$('#sector, #receptor, #variable').select2();
						$('#grupo_plantilla').html("");
					}
				});


			}else{
				$('#model, #sector, #receptor, #variable').html("");
				$('#model, #sector, #receptor, #variable').append($("<option />").val("").text("-"));
				$('#model, #sector, #receptor, #variable').select2();
				$('#grupo_plantilla').html("");
			}
			
		});

		$('#model').change(function(){
		//$(document).on('change', '#project', function() {
			var id_model = $(this).val();
			$('#excel-error').addClass('hide');
			
			if(id_model){
				
				select2LoadingStatusOn($('#sector'));
				
				$.ajax({
					url:  '<?php echo_uri("air_setting_bulk_load/get_sectors_of_model") ?>',
					type:  'post',
					data: {id_model:id_model},
					dataType:'json',
					success: function(respuesta){

						$('#sector').html("");
						$.each((respuesta), function() {
							$('#sector').append($("<option />").val(this.id).text(this.text));
						});
						$('#sector').select2();
						
						select2LoadingStatusOff($('#sector'));

						$('#receptor, #variable').html("");
						$('#receptor, #variable').append($("<option />").val("").text("-"));
						$('#receptor, #variable').select2();
						$('#grupo_plantilla').html("");
						
					}
				});


			}else{
				$('#sector, #receptor, #variable').html("");
				$('#sector, #receptor, #variable').append($("<option />").val("").text("-"));
				$('#sector, #receptor, #variable').select2();
				$('#grupo_plantilla').html("");
			}
			
		});

		$('#sector').change(function(){
		//$(document).on('change', '#project', function() {
			var id_model = $("#model").val();
			var id_sector = $(this).val();
			$('#excel-error').addClass('hide');
			
			if(id_model && id_sector){
				
				select2LoadingStatusOn($('#receptor'));
				
				$.ajax({
					url:  '<?php echo_uri("air_setting_bulk_load/get_receptors_of_sector") ?>',
					type:  'post',
					data: {id_model:id_model, id_sector:id_sector},
					dataType:'json',
					success: function(respuesta){

						$('#receptor').html("");
						$.each((respuesta), function() {
							$('#receptor').append($("<option />").val(this.id).text(this.text));
						});
						$('#receptor').select2();
						
						select2LoadingStatusOff($('#receptor'));

						$('#variable').html("");
						$('#variable').append($("<option />").val("").text("-"));
						$('#variable').select2();
						$('#grupo_plantilla').html("");
						
					}
				});


			}else{
				$('#receptor, #variable').html("");
				$('#receptor, #variable').append($("<option />").val("").text("-"));
				$('#receptor, #variable').select2();
				$('#grupo_plantilla').html("");
			}
			
		});

		$('#receptor').change(function(){
		//$(document).on('change', '#project', function() {
			var id_record_type = $("#record_type").val();
			var id_model = $("#model").val();
			var id_sector = $("#sector").val();
			var id_receptor = $(this).val();
			$('#excel-error').addClass('hide');
			
			if(id_receptor){
				
				select2LoadingStatusOn($('#variable'));
				
				$.ajax({
					url:  '<?php echo_uri("air_setting_bulk_load/get_variables_of_receptor") ?>',
					type:  'post',
					data: {
						id_record_type:id_record_type,
						id_model:id_model,
						id_sector:id_sector,
						id_receptor:id_receptor
					},
					dataType:'json',
					success: function(respuesta){

						$('#variable').html("");
						$.each((respuesta), function() {
							$('#variable').append($("<option/>").val(this.id).text(this.text).attr('icon', this.icon));
						});
						//$('#variable').select2();
						$("#variable").select2({
							formatResult: format,
							formatSelection: format,
							escapeMarkup: function(m) { return m; }
						});
						
						select2LoadingStatusOff($('#variable'));
						
					}
				});


			}else{
				$('#variable').html("");
				$('#variable').append($("<option />").val("").text("-"));
				$('#variable').select2();
				$('#grupo_plantilla').html("");
			}
			
		});

		function format(state) {
			var icon = $(state.element).attr('icon');
			if(state.text != '-'){
				return "<img class='' heigth='25' width='25' src='/assets/images/air_variables/" + icon + "'/>" + "&nbsp;&nbsp;" + state.text;
			}else{
				return state.text;
			}
		}

		$('#variable').change(function(){
		//$(document).on('change', '#project', function() {
			var id_record_type = $("#record_type").val();
			var id_model = $("#model").val();
			var id_sector = $("#sector").val();
			var id_receptor = $("#receptor").val();
			var id_variable = $(this).val();

			$('#excel-error').addClass('hide');
			$('#grupo_plantilla').removeClass('hide');
			$('#grupo_plantilla').html('<div class="circle-loader"></div>');
			
			if(id_variable){
				$.ajax({
					url:  '<?php echo_uri("air_setting_bulk_load/get_excel_template") ?>',
					type:  'post',
					data: {
						id_record_type:id_record_type,
						id_model:id_model,
						id_sector:id_sector,
						id_receptor:id_receptor,
						id_variable:id_variable
					},
					dataType:'json',
					success: function(respuesta){
						
						if(respuesta.success == false){
							$('#excel-error').removeClass('hide');
							$('#excel-error .alert-message').html(respuesta.message);
							$('#grupo_plantilla').html('');
						}else{
							$('#grupo_plantilla').html(respuesta);
						}
						
					}
				});
			}else{
				$('#grupo_plantilla').html('');
				
			}
		});

		
		<?php if($puede_editar != 1) { ?>
			$('#bulk_load-form input[name=archivo_importado_validacion]').attr('disabled','true');
			$('#file-upload-dropzone').hide();
			$('#dropzone_bulk').addClass('dropzone m15').removeClass('col-md-12').html('<?php echo lang('disable_upload_file'); ?>').css('text-align', 'center');
			$('#btn_bulk_load').attr('disabled','true');		
		<?php } ?>

		
        $("#bulk_load-form").appForm({
            //ajaxSubmit: false,
			isModal: false,
			onAjaxSuccess: function (result) {
				//console.log(result);
				//appAlert.success(result.message, {duration: 10000});
                //location.reload();
				
				appLoader.hide();
				if(result.carga){
					$("#resume_table").html("");
					if(result.success){
						appAlert.success(result.message, {duration: 10000});
						$("span[data-dz-remove]").click();
					}else{
						appAlert.error(result.message, {duration: 10000});
					}
				}else{
					if(result.success){
						appAlert.success(result.message, {duration: 10000});
					}else{
						$("#resume_table").html(result.table);
						$('[data-toggle="tooltip"]').tooltip();
						appAlert.error(result.message, {duration: 10000});
					}
				}
				
				
                
            },
			onSubmit: function () {
				appLoader.show();
			},
			/*onError: function (error) {
				alert(error);
			},*/
        });
		
    });
</script>
