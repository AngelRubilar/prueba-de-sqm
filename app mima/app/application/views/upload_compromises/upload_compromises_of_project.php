<div id="page-content" class="p20 pt0 clearfix">
    <div class="row">
        <div class="col-md-12">
        	<?php if($id_compromiso_proyecto) { ?>
                <ul id="project-tabs" data-toggle="ajax-tab" class="nav nav-tabs classic mb0" role="tablist">
                    
                        <li class="active"><a id="tab_carga_individual" role="presentation" href="<?php echo_uri("upload_compromises/carga_individual/". $id_compromiso_proyecto."/".$tipo_matriz); ?>" data-target="#carga_individual"><?php echo lang('individual_load'); ?></a></li>
                        <li><a id="tab_carga_masiva" role="presentation" href="<?php echo_uri("upload_compromises/carga_masiva/". $id_compromiso_proyecto."/".$tipo_matriz); ?>" data-target="#carga_masiva"><?php echo lang('bulk_load'); ?></a></li>
                    
                </ul>
            
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="carga_individual" style="min-height: 200px;"></div>
                    <div role="tabpanel" class="tab-pane fade" id="carga_masiva"></div>
                </div>
            <?php } else { ?>
                <div class="panel">
                    <div class="panel-body">
                    
                    	<div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                        <?php if($tipo_matriz == "rca"){ ?>
                            <?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_rca_matrix_not_enabled'); ?>
                        <?php }else{ ?>  
                        	<?php echo lang('the_project').' "'.$nombre_proyecto.'" '.lang('compromise_reportable_matrix_not_enabled'); ?>
                        <?php } ?>   
                        </div>
                    
                       
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	
	/* $('#tab_carga_individual').click(function(){
		
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", '<?php echo_uri("upload_compromises/carga_individual") ?>');
		
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "id_compromiso");
		hiddenField.setAttribute("value", '<?php echo $id_compromiso_proyecto ?>');
		
		form.appendChild(hiddenField);
		
		document.body.appendChild(form);
		form.submit();
		
	}); */
	
	/* $('#tab_carga_individual').click(function(){
		
		var id_compromiso_proyecto = '<?php echo $id_compromiso_proyecto ?>';
		
		$.ajax({
			url:  '<?php echo_uri("upload_compromises/carga_individual") ?>',
			type:  'post',
			data: {id_compromiso_proyecto: id_compromiso_proyecto},
			//dataType:'json',
			success: function(respuesta){
				
				if (id_compromiso_proyecto){
					$('#configuraciones').html("");
					$('#configuraciones').html(respuesta);
					//$('#tab_carga_individual').trigger('click');
				} else {
					$('#configuraciones').html("");
				}				
			}
		});
	}); */
});
</script>
