<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="first_name" class="<?php echo $label_column; ?>"><?php echo lang('name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "first_name",
            "name" => "first_name",
            "value" => $model_info->first_name,
            "class" => "form-control",
            "placeholder" => lang('name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="last_names" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "last_name",
            "name" => "last_name",
            "value" => $model_info->last_name,
            "class" => "form-control",
            "placeholder" => lang('last_name'),
            //"autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="rut" class="<?php echo $label_column; ?>"><?php echo lang('rut_dni'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "rut",
            "name" => "rut",
            "value" => $model_info->rut,
            "class" => "form-control",
            "placeholder" => lang('rut_dni'),
            //"autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			//"data-rule-minlength" => 6,
			//"data-msg-minlength" => lang("enter_minimum_6_characters"),
			//"data-rule-maxlength" => 13,
			//"data-msg-maxlength" => lang("enter_maximum_13_characters"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<?php if(!$model_info->phone){
	$fono = "+56";
}else{
	$fono = $model_info->phone;
}?>

<div class="form-group">
    <label for="phone" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "phone",
            "name" => "phone",
            "value" => $fono,
            "class" => "form-control",
            "placeholder" => lang('phone'),
            //"autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="email" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "email",
            "name" => "email",
            "value" => $model_info->email,
            "class" => "form-control",
            "placeholder" => lang('email'),
            //"autofocus" => true,
			"data-rule-email" => true,
			"data-msg-email" => lang("enter_valid_email"),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
<!-- CARGO -->
<div class="form-group">
    <label for="position" class="<?php echo $label_column; ?>"><?php echo lang('position'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "position",
            "name" => "position",
            "value" => $model_info->cargo,
            "class" => "form-control",
            "placeholder" => lang('position'),
            //"autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="gender" class="<?php echo $label_column; ?>"><?php echo lang('gender'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "gender_male",
            "name" => "gender",
            "data-msg-required" => lang("field_required"),
                ), "male", ($model_info->gender == "female") ? false : true);
        ?>
        <label for="gender_male" class="mr15"><?php echo lang('male'); ?></label> <?php
        echo form_radio(array(
            "id" => "gender_female",
            "name" => "gender",
            "data-msg-required" => lang("field_required"),
                ), "female", ($model_info->gender == "female") ? true : false);
        ?>
        <label for="gender_female" class=""><?php echo lang('female'); ?></label>
    </div>
</div>

<div class="form-group">
    <label for="role" class="<?php echo $label_column; ?>"><?php echo lang('role'); ?></label>
    <div class="<?php echo $field_column; ?>">
    	<?php
			$perfiles = array("" => "-" , "1" => "Administrador", "2" => "Cliente");
			
			if(!$model_info->id){
				echo form_dropdown("role", $perfiles, "", "id='role' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			} else{
				if($model_info->is_admin){
					echo form_dropdown("role", $perfiles, array("1"), "id='role' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");	
				} else {
					echo form_dropdown("role", $perfiles, array("2"), "id='role' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
				}
			}	
		?>
    </div>
</div>

<div id="div_clients">
	<?php if($model_info->client_id) { ?>
    <div class="form-group">
        <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("client", $clientes, $model_info->client_id, "id='client' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    <?php } ?>
</div>

<div id="div_profile">
	<?php if($model_info->id_profile) { ?>
    <div class="form-group">
        <label for="profile" class="<?php echo $label_column; ?>"><?php echo lang('project_profile'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("profile", $profiles, $model_info->id_profile, "id='profile' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    <?php } ?>
</div>

<div id="div_general_profile">
	<?php if($model_info->id_client_context_profile) { ?>
    <div class="form-group">
        <label for="general_profile" class="<?php echo $label_column; ?>"><?php echo lang('general_profile'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_dropdown("general_profile", $general_profiles, $model_info->id_client_context_profile, "id='general_profile' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    <?php } ?>
</div>

<div id="div_client_group">
	<?php if($model_info->id && !$model_info->is_admin){ ?>
        <div class="form-group">
            <label for="client_group" class="col-md-3"><?php echo lang('group'); ?></label>
            <div class="col-md-9">
                <?php
                    echo form_dropdown("client_group", $clients_groups_dropdown, $model_info->id_client_group, "id='client_group' class='select2' ");
                ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php if($model_info->id){ ?>
<div class="form-group">
    <label for="new_password" class="col-md-3"><?php echo lang('update_password'); ?></label>
    <div class="col-md-8">
        <?php
		echo form_checkbox("new_password", "1", false, "id='new_password'");
        ?>
    </div>
</div>
<?php } ?>

<div id="password_update_group" <?php if($model_info->id){ ?>style="display:none;"<?php } ?>>
    <!--
    <div class="form-group">
        <label for="login_password" class="col-md-3"><?php echo ($model_info->id)?lang('new_password'):lang('password'); ?></label>
        <div class=" col-md-8">
            <div class="input-group">
                <?php
                $password_field = array(
                    "id" => "login_password",
                    "name" => "login_password",
                    "class" => "form-control",
                    "placeholder" => lang('password'),
                    "autocomplete" => "new-password",
                    "style" => "z-index:auto;",
					/* 	(/^
						(?=.*\d)should contain at least one digit,
						(?=.*[a-z]) should contain at least one lower case, 
						(?=.*[A-Z]) should contain at least one upper case,
						[a-zA-Z0-9]{6,} should contain at least 6 from the mentioned characters
						 $/)*/
					"data-rule-regex" => "^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{6,}$",
					"data-msg-regex" => lang("alphanumeric_required"),
					
                );
                if (!$model_info->id) {
                    //this filed is required for new record
                    $password_field["data-rule-required"] = true;
                    $password_field["data-msg-required"] = lang("field_required");
                    $password_field["data-rule-minlength"] = 6;
                    $password_field["data-msg-minlength"] = lang("enter_minimum_6_characters");
                }
                echo form_password($password_field);
                ?>
                <label for="password" class="input-group-addon clickable" id="generate_password"><span class="fa fa-key"></span> <?php echo lang('generate'); ?></label>
            </div>
        </div>
        <div class="col-md-1 p0">
            <a href="#" id="show_hide_password" class="btn btn-default" title="<?php echo lang('show_text'); ?>"><span class="fa fa-eye"></span></a>
        </div>
    </div>
    -->

    <div class="form-group">
            <label for="password" class="col-md-3"><?php echo lang('password'); ?>
            	<span class="help" data-container="body" data-toggle="tooltip" title="<?php echo lang('alphanumeric_required') ?>"><i class="fa fa-question-circle"></i></span>
            </label>
            <div class="col-md-8">
                <?php
                $password_field = array(
                    "id" => "login_password",
                    "name" => "login_password",
                    "class" => "form-control",
					"placeholder" => lang('password'),
                    "autocomplete" => "new-password",
                    "style" => "z-index:auto;",
					/* 	(/^
						(?=.*\d)should contain at least one digit,
						(?=.*[a-z]) should contain at least one lower case, 
						(?=.*[A-Z]) should contain at least one upper case,
						[a-zA-Z0-9]{6,} should contain at least 6 from the mentioned characters
						 $/)*/
					"data-rule-regex" => "^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{6,}$",
					"data-msg-regex" => lang("alphanumeric_required"),

                );
				
				//if (!$model_info->id) {					
                    //this filed is required for new record
                    $password_field["data-rule-required"] = true;
                    $password_field["data-msg-required"] = lang("field_required");
                    $password_field["data-rule-minlength"] = 6;
                    $password_field["data-msg-minlength"] = lang("enter_minimum_6_characters");
                //}
				
				echo form_password($password_field);
				
                ?>
            </div>
            <div class="col-md-1 p0">
                <a href="#" id="show_hide_password" class="btn btn-default" title="<?php echo lang('show_text'); ?>"><span class="fa fa-eye"></span></a>
            </div>
        </div>
        
        <div class="form-group">
            <label for="retype_password" class="col-md-3"><?php echo lang('retype_password'); ?></label>
            <div class="col-md-9">
                <?php
				
				$retype_password_field = array(
                    "id" => "retype_password",
                    "name" => "retype_password",
                    "class" => "form-control p10",
                    "autocomplete" => "off",
                    "style" => "z-index:auto;",
					"placeholder" => lang('retype_password'),
                );
				
				//if (!$model_info->id) {					
                    //this filed is required for new record
                    $retype_password_field["data-rule-equalTo"] = "#login_password";
                    $retype_password_field["data-msg-equalTo"] = lang("enter_same_value");
                    $retype_password_field["data-rule-required"] = true;
                    $retype_password_field["data-msg-required"] = lang("field_required");
                //}
				

				echo form_password($retype_password_field);
				
                ?>
            </div>
        </div>
</div>

<div class="form-group">
    <div class="form-group">
        <label for="language" class="<?php echo $label_column; ?>"><?php echo lang('language'); ?></label>
        <div class="<?php echo $field_column; ?>">
			<?php
				echo form_dropdown("language", $language_dropdown, $model_info->language, "id='language' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
			?>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="change_map_type" class="<?php echo $label_column; ?>"><?php echo lang('change_map_type')." (".lang("mimaire").")"; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "change_map_type_yes",
            "name" => "change_map_type",
            "data-msg-required" => lang("field_required"),
                ), "1", ($model_info->change_map_type == "0") ? false : true);
        ?>
        <label for="change_map_type_yes" class="mr15"><?php echo lang('yes'); ?></label> <?php
        echo form_radio(array(
            "id" => "change_map_type_no",
            "name" => "change_map_type",
            "data-msg-required" => lang("field_required"),
                ), "0", ($model_info->change_map_type == "0") ? true : false);
        ?>
        <label for="change_map_type_no" class=""><?php echo lang('no'); ?></label>

    </div>
</div>

<div class="form-group">
    <label for="can_delete_files" class="<?php echo $label_column; ?>"><?php echo lang('can_delete_files')." (".lang("mimaire").")"; ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_radio(array(
            "id" => "can_delete_files_yes",
            "name" => "can_delete_files",
            "data-msg-required" => lang("field_required"),
                ), "1", ($model_info->can_delete_files == "0") ? false : true);
        ?>
        <label for="can_delete_files_yes" class="mr15"><?php echo lang('yes'); ?></label> <?php
        echo form_radio(array(
            "id" => "can_delete_files_no",
            "name" => "can_delete_files",
            "data-msg-required" => lang("field_required"),
                ), "0", ($model_info->can_delete_files == "0") ? true : false);
        ?>
        <label for="can_delete_files_no" class=""><?php echo lang('no'); ?></label>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$("#generate_password").click(function() {
            $("#login_password").val(getRndomString(8));
        });
		
		$('#users-form .select2').select2();
		
		/*
		$('#rut').rut({
			formatOn: 'keyup',
			minimumLength: 8,
			validateOn: 'change'
		});
		*/
		
        $("#show_hide_password").click(function() {
            var $target = $("#login_password"),
                    type = $target.attr("type");
            if (type === "password") {
                $(this).attr("title", "<?php echo lang("hide_text"); ?>");
                $(this).html("<span class='fa fa-eye-slash'></span>");
                $target.attr("type", "text");
            } else if (type === "text") {
                $(this).attr("title", "<?php echo lang("show_text"); ?>");
                $(this).html("<span class='fa fa-eye'></span>");
                $target.attr("type", "password");
            }
        });
		
		$("#new_password").change(function() {
			if(this.checked) {
				$("#password_update_group").show();
			}else{
				$('#login_password').val("");
				$("#password_update_group").hide();
			}
		});
		
		$('#role').change(function(){	
			
			var rol = $(this).val();
			
			if(rol == "2"){
			
				$.ajax({
					url:  '<?php echo_uri("clients/get_clients_dropdown") ?>',
					type:  'post',
					//data: {rol: rol},
					//dataType:'json',
					success: function(respuesta){
						
						$('#div_clients').html(respuesta);
						$('#client').select2();
					}
				});
				
				$.ajax({
					url:  '<?php echo_uri("users/get_profiles_of_rol") ?>',
					type:  'post',
					data: {rol: rol},
					//dataType:'json',
					success: function(respuesta){
						
						$('#div_profile').html(respuesta);
						$('#profile').select2();
					}
				});
				
				$.ajax({
					url:  '<?php echo_uri("users/get_general_profiles_of_rol") ?>',
					type:  'post',
					data: {rol: rol},
					//dataType:'json',
					success: function(respuesta){
						
						$('#div_general_profile').html(respuesta);
						$('#general_profile').select2();
					}
				});	
				
				$('#div_client_group').html("");	

			} else {
				$('#div_profile').html("");
				$('#div_clients').html("");
				$('#div_general_profile').html("");
			}
	
		});
		
		$(document).on("change", "#client", function(){
			
			id_client = $(this).val();
			$.ajax({
				url:  '<?php echo_uri("users/get_clients_groups_dropdown") ?>',
				type:  'post',
				data: {id_client: id_client},
				//dataType:'json',
				success: function(respuesta){
					$('#div_client_group').html(respuesta);
					$('#client_group').select2();
				}
			});
			
		});
		
		//Botón eliminar incluye validación de correo, esto cierra el mensaje
		$('#ajaxModal').on('hidden.bs.modal', function () {
			$('.close').trigger('click');
		})
	
		//$("#first_name").rules("add", { regex: "^[a-zA-Z'.s]{1,40}$" })
	
    });
	
	
</script>