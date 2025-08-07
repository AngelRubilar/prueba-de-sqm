    <div class="panel">
            <?php echo form_open(get_uri("upload_compromises/save_carga_masiva/".$tipo_matriz), array("id" => "bulk_load-form", "class" => "general-form", "role" => "form")); ?>

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
                
                    <!-- <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" /> -->
                    <input type="hidden" name="id_cliente" value="<?php echo $id_cliente; ?>" />
                    <input type="hidden" name="id_proyecto" value="<?php echo $id_proyecto; ?>" />
                    <input type="hidden" name="id_compromiso" value="<?php echo $id_compromiso; ?>" />
                    
                    <div class="form-group">
                        <?php echo $excel_template; ?>
                    </div>
                    
                    <div id="dropzone_bulk" class="col-md-12">
						<?php
                        
                        echo $this->load->view("includes/bulk_file_uploader", array(
                            "upload_url" => get_uri("upload_compromises/upload_file"),
                            "validation_url" =>get_uri("upload_compromises/validate_file"),
                            //"html_name" => 'test',
                            //"obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
                        ), true);
                        ?>
                        <?php //$this->load->view("includes/dropzone_preview"); ?>
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
			<div id="resume_table" class="table-responsive"></div>
        </div>
    </div>
    
<script type="text/javascript">
    $(document).ready(function () {

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
        });
		
    });
</script>