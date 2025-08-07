<?php echo form_open("", array("id" => "users-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
   
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('field_name'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->first_name ? $user_info->first_name : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('last_names'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->last_name ? $user_info->last_name : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('rut'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->rut ? $user_info->rut : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('email'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->email ? $user_info->email : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('position'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->cargo ? $user_info->cargo : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="gender" class="col-md-3"><?php echo lang('gender'); ?></label>
        <div class="col-md-9">
            <?php
				if($user_info->gender == "male"){
					echo lang("male");
				}
				if($user_info->gender == "female"){
					echo lang("female");
				}
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('role'); ?></label>
        <div class="col-md-9">
            <?php			
				$rol = ($user_info->is_admin) ? "Administrador" : "Cliente";
            	echo $rol;            
			?>
        </div>
    </div>
    
    <?php if ($user_info->client_id) { ?>
        <div class="form-group">
            <label for="client" class="col-md-3"><?php echo lang('client'); ?></label>
            <div class="col-md-9">
                <?php			
                	echo $cliente;
                ?>
            </div>
        </div>
    <?php } ?>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('project_profile'); ?></label>
        <div class="col-md-9">
            <?php
				$perfil = ($user_info->is_admin) ? "Administrador" : $user_info->perfil;
				echo $perfil;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="field_name" class="col-md-3"><?php echo lang('general_profile'); ?></label>
        <div class="col-md-9">
            <?php
				if($user_info->is_admin){
					echo "Administrador";
				} else {	
					echo ($user_info->id_client_context_profile) ? $perfil_general : "-";
				}
            ?>
        </div>
    </div>
    
    <?php if(!$user_info->is_admin){ ?>
        <div class="form-group">
            <label for="group" class="col-md-3"><?php echo lang('group'); ?></label>
            <div class="col-md-9">
                <?php
                    echo $client_group;
                ?>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <label for="change_map_type" class="col-md-3"><?php echo lang('change_map_type')." (".lang("mimaire").")"; ?></label>
        <div class="col-md-9">
            <?php
				if($user_info->change_map_type == "1"){
					echo lang("yes");
				}
				if($user_info->change_map_type == "0"){
					echo lang("no");
				}
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="can_delete_files" class="col-md-3"><?php echo lang('can_delete_files')." (".lang("mimaire").")"; ?></label>
        <div class="col-md-9">
            <?php
				if($user_info->can_delete_files == "1"){
					echo lang("yes");
				}
				if($user_info->can_delete_files == "0"){
					echo lang("no");
				}
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $user_info->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($user_info->modified)?$user_info->modified:'-';
            ?>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#users-form").appForm({
            onSuccess: function(result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function() {
                        location.reload();
                    }, 500);

                } else {
                    $("#users-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
        //$("#company_name").focus();
    });
</script>    