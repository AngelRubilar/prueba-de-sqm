<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="company_name" class="<?php echo $label_column; ?>"><?php echo lang('company_name'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->company_name; ?></label>
</div>
<div class="form-group">
    <label for="sigla" class="<?php echo $label_column; ?>"><?php echo lang('initial'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->sigla; ?></label>
</div>
<div class="form-group">
    <label for="rut" class="<?php echo $label_column; ?>"><?php echo lang('rut'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->rut; ?></label>
</div>
<div class="form-group">
    <label for="giro" class="<?php echo $label_column; ?>"><?php echo lang('giro'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->giro; ?></label>
</div>
<div class="form-group">
    <label for="pais" class="<?php echo $label_column; ?>"><?php echo lang('country'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->pais; ?></label>
</div>
<div class="form-group">
    <label for="ciudad" class="<?php echo $label_column; ?>"><?php echo lang('city'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->ciudad; ?></label>
</div>
<div class="form-group">
    <label for="comuna" class="<?php echo $label_column; ?>"><?php echo lang('town'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->comuna; ?></label>
</div>
<div class="form-group">
    <label for="direccion" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->direccion ? $model_info->direccion : "-"; ?></label>
</div>
<div class="form-group">
    <label for="fono" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->fono ? $model_info->fono : "-"; ?></label>
</div>
<div class="form-group">
    <label for="website" class="<?php echo $label_column; ?>"><?php echo lang('website'); ?></label>
    <label class="<?php echo $field_column; ?>"><?php echo $model_info->website ? $model_info->website : "-"; ?></label>
</div>
<div class="form-group">
    <label for="logo" class=" col-md-3"><?php echo lang('site_logo'); ?></label>
    <div class=" col-md-9">
        <div class="pull-left mr15">
            <img id="site-logo-preview" src="<?php echo get_file_uri(get_setting("system_file_path") . get_setting("site_logo")); ?>" alt="..." />
        </div>
        <div class="pull-left file-upload btn btn-default btn-xs">
            <span>...</span>
            <input id="site_logo_file" class="cropbox-upload upload" name="site_logo_file" type="file" data-height="40" data-width="175" data-preview-container="#site-logo-preview" data-input-field="#site_logo" />
        </div>
        <input type="hidden" id="site_logo" name="site_logo" value=""  />
    </div>
</div>

<div class="form-group">
    <label for="color_sitio" class="<?php echo $label_column; ?>"><?php echo lang('site_color'); ?></label>
    <label class="<?php echo $field_column; ?>">
    	<i style="border: solid black 1px; background-color: <?php echo $model_info->color_sitio; ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>
		<?php echo $model_info->color_sitio ? $model_info->color_sitio : "-"; ?> 
    </label>
    
</div>

<div class="form-group">
    <label for="habilitado" class="<?php echo $label_column; ?>"><?php echo lang('site_status'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
		if($model_info->habilitado){
			$checked = $model_info->habilitado ? true : false;
		}else{
			$checked = true;
		}
		echo form_checkbox("habilitado", "1", $checked, "id='habilitado'  onclick='return false;'");
        ?>
    </div>
</div>





<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>