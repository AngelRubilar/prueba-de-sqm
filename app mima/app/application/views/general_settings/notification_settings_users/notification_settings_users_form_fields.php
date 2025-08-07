<input type="hidden" name="id_client" value="<?php echo $id_client; ?>" />
<input type="hidden" name="id_project" value="<?php echo $id_project; ?>" />
<input type="hidden" name="id_modulo" value="<?php echo $id_modulo; ?>" />
<input type="hidden" name="id_submodulo" value="<?php echo $id_submodulo; ?>" />
<input type="hidden" name="item" value="<?php echo $item; ?>" />

<!-- id's de configuraciones para su edición -->
<input type="hidden" name="id_notif_config_add" value="<?php echo $id_notif_config_add; ?>" />
<input type="hidden" name="id_notif_config_edit" value="<?php echo $id_notif_config_edit; ?>" />
<input type="hidden" name="id_notif_config_delete" value="<?php echo $id_notif_config_delete; ?>" />
<input type="hidden" name="id_notif_config_bulk_load" value="<?php echo $id_notif_config_bulk_load; ?>" />


<div class="form-group">
    <label for="module" class="<?php echo $label_column; ?>"><?php echo lang('module'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $modulo;
        ?>
    </div>
</div>

<div class="form-group">
    <label for="submodule" class="<?php echo $label_column; ?>"><?php echo lang('submodule'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $submodulo;
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-group" id="accordion_modal_form_edit">
            	                
                <?php foreach($eventos as $evento => $opciones) { ?>
          
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse_edit_<?php echo $evento; ?>" class="accordion-toggle_edit">
                                    <h4 style="font-size:16px; float:unset !important;">
                                        <i class="fa fa-minus-circle font-16"></i> <?php echo lang($evento); ?>
                                    </h4>
                                </a>
                            </h4>
                        </div>
                        
                        <div id="collapse_edit_<?php echo $evento; ?>" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="col-md-12" style="text-align: justify;">
                                    
                                    <div class="form-group">
                                        <label for="groups" class="<?php echo $label_column; ?>"><?php echo lang('groups'); ?></label>
                                        <div class="<?php echo $field_column; ?>">
                                            <?php
                                                echo form_multiselect("groups_".$evento."[]", $array_client_groups, $opciones["selected_client_groups"], "id='groups_".$evento."' class='select2 multiple' multiple='multiple'");
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div id="users_group_<?php echo $evento; ?>">
                                        <div class="form-group">
                                            <label for="users" class="<?php echo $label_column; ?>"><?php echo lang('users'); ?></label>
                                            <div class="<?php echo $field_column; ?>">
                                                <?php
                                                    echo form_multiselect("users_".$evento."[]", $array_client_users, $opciones["selected_client_users"], "id='users_".$evento."' class='select2 multiple' multiple='multiple'");
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="notification_email" class="<?php echo $label_column; ?>"><?php echo lang('notification_email'); ?></label>
                                        <div class="<?php echo $field_column; ?>">
                                            <?php
                                                echo form_checkbox("notification_email_".$evento, "1", ($opciones["is_email_notification"]) ? true : false, "id='notification_email_".$evento."' ");
                                            ?>
                                        </div>
                                    </div> 
                                    
                                    <div class="form-group">
                                        <label for="notification_web" class="<?php echo $label_column; ?>"><?php echo lang('notification_web'); ?></label>
                                        <div class="<?php echo $field_column; ?>">
                                            <?php
                                                echo form_checkbox("notification_web_".$evento, "1", ($opciones["is_web_notification"]) ? true : false, "id='notification_web_".$evento."' ");
                                            ?>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                
                <?php } ?>
                        
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        
		//$('[data-toggle="tooltip"]').tooltip();
		$('#notification_config_users-form .select2').select2();
		
		<?php foreach($eventos as $evento => $opciones) { ?>
		
			$("#groups_<?php echo $evento; ?>").change(function(){
				
				var id_client = '<?php echo $id_client; ?>';
				var id_project = '<?php echo $id_project; ?>';
				var groups = $(this).val();
				var evento = '<?php echo $evento; ?>';
							
				$.ajax({
					url:  '<?php echo_uri("general_settings/get_user_members_of_groups") ?>',
					type:  'post',
					data: {id_client: id_client, id_project: id_project, groups: groups, evento: evento},
					//dataType:'json',
					success: function(respuesta){					
						$("#users_group_<?php echo $evento; ?>").html(respuesta);    
						$("#users_<?php echo $evento; ?>").select2();
					}
					
				});
				
			});
		
		<?php } ?>
		
		$(document).on('click', 'a.accordion-toggle_edit', function () {
			
			var icon = $(this).find('i');
			
			if($(this).hasClass('collapsed')){
				icon.removeClass('fa fa-minus-circle font-16');
				icon.addClass('fa fa-plus-circle font-16');
			} else {
				icon.removeClass('fa fa-plus-circle font-16');
				icon.addClass('fa fa-minus-circle font-16');
			}
						
		});
		
    });
</script>