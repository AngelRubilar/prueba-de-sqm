<input type="hidden" name="contact_id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="client_id" value="<?php echo $model_info->client_id; ?>" />
<div class="form-group">
    <?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
    ?>
    <label for="first_name" class="<?php echo $label_column; ?>"><?php echo lang('first_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "first_name",
            "name" => "first_name",
            "value" => $model_info->first_name,
            "class" => "form-control",
            "placeholder" => lang('first_name'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="last_name" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "last_name",
            "name" => "last_name",
            "value" => $model_info->last_name,
            "class" => "form-control",
            "placeholder" => lang('last_name'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="rut" class="<?php echo $label_column; ?>"><?php echo lang('rut'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "rut",
            "name" => "rut",
            "value" => $model_info->rut,
            "class" => "form-control",
            "placeholder" => lang('rut'),
            "autofocus" => true,
			"data-rule-minlength" => 6,
			"data-msg-minlength" => lang("enter_minimum_6_characters"),
			"data-rule-maxlength" => 13,
			"data-msg-maxlength" => lang("enter_maximum_13_characters"),
			"autocomplete" => "off",
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="phone" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "phone",
            "name" => "phone",
            "value" => $model_info->phone ? $model_info->phone : "",
            "class" => "form-control",
            "placeholder" => lang('phone')
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
            "data-rule-email" => true,
            "data-msg-email" => lang("enter_valid_email"),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
            "autocomplete" => "off"
        ));
        ?>
    </div>
</div>

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
            "autofocus" => true,
            //"data-rule-required" => true,
            //"data-msg-required" => lang("field_required"),
			"autocomplete" => "off",
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

<?php if($model_info->id){ ?>
<!--<div class="form-group">
    <label for="new_password" class="col-md-3"><?php echo lang('update_password'); ?></label>
    <div class="col-md-8">
        <?php
		echo form_checkbox("new_password", "1", false, "id='new_password'");
        ?>
    </div>
</div>-->
<?php } ?>

<!--<div id="password_update_group" <?php if($model_info->id){ ?>style="display:none;"<?php } ?>>
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
                    "autocomplete" => "off",
                    "style" => "z-index:auto;",
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
</div>-->

<script type="text/javascript">
    $(document).ready(function() {
        $("#generate_password").click(function() {
            $("#login_password").val(getRndomString(6));
        });
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
				$("#password_update_group").hide();
			}
		});
    });
</script>    