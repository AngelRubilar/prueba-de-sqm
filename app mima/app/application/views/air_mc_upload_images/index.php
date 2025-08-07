<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("meteorological_conditions"); ?> /</a>
		<a class="breadcrumb-item" href=""><?php echo lang("upload_images"); ?></a>
    </nav>

<?php if($puede_ver == 1) { ?> <!-- Se aplica la configuración de perfil (ver todos) -->

    <div class="panel">
            <?php echo form_open(get_uri("air_mc_upload_images/save"), array("id" => "bulk_load-form", "class" => "general-form", "role" => "form")); ?>

            <div class="panel-default">
            
                <div class="page-title clearfix">
                	<h1><?php echo lang('upload_images'); ?></h1>
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
                    
                    <div id="dropzone_bulk" class="col-md-12">
						<?php
							echo $this->load->view("includes/bulk_file_uploader", array(
								"upload_url" => get_uri("air_mc_upload_images/upload_file"),
								"validation_url" =>get_uri("air_mc_upload_images/validate_zip_file"),
								//"html_name" => 'test',
								//"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
							), true);
						?>
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
		
		// $.ajax({
		// 	url:  '<?php echo_uri("air_mc_upload_images/get_file_field_for_bulk_load") ?>',
		// 	type:  'post',
		// 	// data: {id_record_type: id_record_type},
		// 	//dataType:'json',
		// 	success: function(respuesta){
		// 		$('#dropzone_bulk').html(respuesta);
		// 	},
		// 	beforeSend: function(){
		// 		$('#dropzone_bulk').html('<div class="circle-loader"></div>');
		// 	}
		// });

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
				$('#btn_bulk_load').removeAttr('disabled');
				appLoader.hide();
				if(result.carga){
					if(result.success){
						//appAlert.success(result.message, {duration: 10000});
						appAlert.success(result.message);
					}else{
						//appAlert.error(result.message, {duration: 10000});
						appAlert.error(result.message);
					}
				}else{
					if(result.success){
						//appAlert.success(result.message, {duration: 10000});
						appAlert.success(result.message);
					}else{
						//appAlert.error(result.message, {duration: 10000});
						appAlert.error(result.message);
					}
				}

            },
			onSubmit: function () {
				$('#btn_bulk_load').attr('disabled','true');
				appLoader.show();
			},
			/*onError: function (error) {
				alert(error);
			},*/
        });
		
    });
</script>
