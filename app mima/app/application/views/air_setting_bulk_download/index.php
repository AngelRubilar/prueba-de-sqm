<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("customer_administrator_air"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("setting_bulk_download_air"); ?></a>
    </nav>

<?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

    <div class="panel">
            <?php echo form_open(get_uri("air_setting_bulk_download/get_data"), array("id" => "bulk_download-form", "class" => "general-form", "role" => "form")); ?>

            <div class="panel-default">
            
                <div class="page-title clearfix">
                	<h1><?php echo lang('bulk_download'); ?></h1>
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
							<span id="record_type-error" style="color: #ec5855;"></span>
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
                    
                    <div class="col-md-12">
                    	<!--<span class="pull-right"><?php echo lang("maximum_file_size").": ".get_setting("max_file_size")."MB"; ?></span>-->
                    </div>
                    
                    
                </div>
                <div class="panel-footer clearfix">

                    <button id="btn_bulk_download" type="button" class="btn btn-primary pull-right ml15"><span class="fa fa-download"></span> <?php echo lang('download'); ?></button>

					<button type="button" class="btn btn-default pull-right clean_data" data-dismiss="modal" data-action="delete-confirmation" data-custom="0" data-action-url="<?php echo get_uri("air_setting_bulk_download/clean_data");?>"><span class="fa fa-broom"></span> <?php echo lang('clean'); ?></button>
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
		
		$('#bulk_download-form .select2').select2();
		
		$('#record_type').change(function(){
		//$(document).on('change', '#project', function() {
			var id_record_type = $(this).val();

			if(!id_record_type){
				$('#record_type-error').text("<?php echo lang('field_required'); ?>");
			} else {
				$('#record_type-error').text("");
			}
			//$('#excel-error').addClass('hide');
			
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
						
						$('#sector, #receptor').html("");
						$('#sector, #receptor').append($("<option />").val("").text("-"));
						$('#sector, #receptor').select2();
						$('#grupo_plantilla').html("");
					}
				});


			}else{
				$('#model, #sector, #receptor').html("");
				$('#model, #sector, #receptor').append($("<option />").val("").text("-"));
				$('#model, #sector, #receptor').select2();
				$('#grupo_plantilla').html("");
			}
			
		});
		
		<?php if($puede_editar != 1) { ?>
			$('#bulk_download-form input[name=archivo_importado_validacion]').attr('disabled','true');
			$('#file-upload-dropzone').hide();
			$('#dropzone_bulk').addClass('dropzone m15').removeClass('col-md-12').html('<?php echo lang('disable_upload_file'); ?>').css('text-align', 'center');
			$('#btn_bulk_load').attr('disabled','true');		
		<?php } ?>

		
        /*$("#bulk_download-form").appForm({
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
        });*/

		$('#btn_bulk_download').click(function(){

			var id_record_type = $('#record_type').val();

			if(!id_record_type){
				$('#record_type-error').text("<?php echo lang('field_required'); ?>");

			} else if(id_record_type == 1){ // Monitoreo
				appAlert.warning("<?php echo lang("no_monitoring_data_msj"); ?>", {duration: 10000});
			} else { // Pronóstico

				$('#record_type-error').text("");
				appLoader.show();

				var $form = $('<form id="csv"></form>');
				$form.attr('action','<?php echo_uri("air_setting_bulk_download/get_data/"); ?>');
				$form.attr('method','POST').attr('target', '_self').appendTo('body');
				$form.append("<input type='hidden' name='id_record_type' value='"+id_record_type+"'></input>");
				$form.appForm({
					/*onSuccess: function(result) {
						console.log(result);
						
					},*/
					ajaxSubmit: false,
					isModal: false,
					onAjaxSuccess: function (result) {
						//console.log(result);
						//appLoader.hide();
						
						/*var uri = '<?php echo get_setting("temp_file_path") ?>' + result.name;
						var link = document.createElement("a");
						link.download = result.name;
						link.href = uri;
						link.click();
						
						borrar_temporal(uri);*/

						/*if(result.success){
							appAlert.success(result.message, {duration: 10000});
						}else{
							appAlert.warning(result.message, {duration: 10000});
						}*/
					},
					onSubmit: function () {
						appLoader.show();
					},
					onError: function (error) {
						//console.log(error);
					},
				});

				$form.submit();
				appLoader.hide();
			}

			
		});

		function borrar_temporal(uri){
			$.ajax({
				url:  '<?php echo_uri("air_setting_bulk_download/borrar_temporal") ?>',
				type:  'post',
				data: {uri:uri},
				//dataType:'json',
				success: function(respuesta){
					appLoader.hide();
				}
			});
		}

		$(document).on('click', '.clean_data', function() {
			var id_record_type = $("#record_type").val();
			if(!id_record_type){
				$('#record_type-error').text("<?php echo lang('field_required'); ?>");
			} else {
				$('#record_type-error').text("");
				$.each(this.attributes, function () {
					if (this.specified && this.name.match("^data-")) {
						$("#confirmDeleteButtonClean").attr(this.name, this.value);
					}
				});
				$("#confirmationModalClean").modal('show');
			}
		});

		$(document).on('click', '.modal #confirmDeleteButtonClean', function() {
				
			appLoader.show();
			
			var url = $(this).attr('data-action-url');
			var id_record_type = $('#record_type').val();
			//var id = $(this).attr('data-id');

			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {id_record_type: id_record_type},
				success: function (result) {
					if (result.success) {
						appAlert.success(result.message, {duration: 20000});
					} else {
						appAlert.error(result.message, {duration: 20000});
					}
					appLoader.hide();
				}
			});

		});

		
    });
</script>
