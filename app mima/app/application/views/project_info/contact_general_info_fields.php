<input type="hidden" name="contact_id" value="<?php echo $user_info->id; ?>" />
<input type="hidden" name="client_id" value="<?php echo $user_info->client_id; ?>" />
<div class="form-group">
    <?php
    $label_column = isset($label_column) ? $label_column : "col-md-3";
    $field_column = isset($field_column) ? $field_column : "col-md-9";
    ?>

	 <label for="client" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $client_info->company_name; ?>
    </div>

</div>

<div class="form-group">
 	<label for="first_name" class="<?php echo $label_column; ?>"><?php echo lang('first_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->first_name; ?>
    </div>   
</div>

<div class="form-group">
    <label for="last_name" class="<?php echo $label_column; ?>"><?php echo lang('last_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->last_name; ?>
    </div>
</div>

<div class="form-group">
    <label for="rut" class="<?php echo $label_column; ?>"><?php echo lang('rut'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->rut; ?>
    </div>
</div>

<div class="form-group">
    <label for="phone" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->phone ? $user_info->phone : ""; ?>
    </div>
</div>
<div class="form-group">
    <label for="email" class="<?php echo $label_column; ?>"><?php echo lang('email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->email; ?>
    </div>
</div>

<div class="form-group">
    <label for="position" class="<?php echo $label_column; ?>"><?php echo lang('position'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo $user_info->cargo; ?>
    </div>
</div>
<div class="form-group">
    <label for="gender" class="<?php echo $label_column; ?>"><?php echo lang('gender'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php echo ($user_info->gender == "female") ?  lang("female") : lang("male"); ?>
    </div>
</div>
