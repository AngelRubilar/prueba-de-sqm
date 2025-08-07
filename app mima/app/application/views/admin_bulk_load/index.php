<div id="page-content" class="clearfix p20">

	<!--Breadcrumb section-->
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("records"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri(); ?>"><?php echo lang("bulk_creation"); ?></a>
    </nav>

    <div class="panel">
		<?php echo form_open(get_uri("admin_bulk_load/save"), array("id" => "bulk_load-form", "class" => "general-form", "role" => "form")); ?>

        <div class="panel-default">
        
            <div class="page-title clearfix">
                <h1><?php echo lang('bulk_creation'); ?></h1>
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
                    <label for="form" class="col-md-12"><?php echo lang('client'); ?></label>
                    <div class="col-md-12">
                        <?php
                        echo form_dropdown("client", $clientes, "", "id='client' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                        ?>
                    </div>
                </div>
                
                <div id="proyectos_group">
                    <div class="form-group">
                        <label for="form" class="col-md-12"><?php echo lang('project'); ?></label>
                        <div class="col-md-12">
                            <?php
                            echo form_dropdown("project", array("" => "-"), "", "id='project' class='select2 validate-hidden' data-sigla='' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
                
                <div id="grupo_plantilla">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="fa fa-file-excel-o font-22 mr10"></div>
                            	<a href="<?php echo get_uri("admin_bulk_load/download_form_template"); ?>">Importacion masiva - PRO (Formularios).xlsx</a>
                            </div>
                    </div>
                </div>
                
                
                <div id="dropzone_bulk" class="col-md-12">
                    <?php
                    
                    echo $this->load->view("includes/bulk_file_uploader", array(
                        "upload_url" => get_uri("admin_bulk_load/upload_file"),
                        "validation_url" => get_uri("admin_bulk_load/validate_file"),
                        //"html_name" => 'test',
                        "obligatorio" => 'data-rule-required="1" data-msg-required="'.lang("field_required"),
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
    
</div>


<script type="text/javascript">
    $(document).ready(function () {
		
		$('#bulk_load-form .select2').select2();
		
		$('#client').change(function(){
				
			var id_client = $(this).val();	
			select2LoadingStatusOn($('#project'));
					
			$.ajax({
				url: '<?php echo_uri("clients/get_projects_of_client"); ?>',
				type: 'post',
				data: {id_client:id_client, col_label:'col-md-12', col_projects:'col-md-12'},
				success: function(respuesta){
					$('#proyectos_group').html(respuesta);
					$('#project').select2();
				}
			});
				
		});	

		
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