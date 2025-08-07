<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <label for="company_name" class="<?php echo $label_column; ?>"><?php echo lang('company_name'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "company_name",
            "name" => "company_name",
            "value" => $model_info->company_name,
            "class" => "form-control",
            "placeholder" => lang('company_name'),
            "autofocus" => true,
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="sigla" class="<?php echo $label_column; ?>"><?php echo lang('initial'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "sigla",
            "name" => "sigla",
            "value" => $model_info->sigla,
            "class" => "form-control",
            "placeholder" => lang('initial'),
            "data-rule-required" => true,
            "data-msg-required" => lang("field_required"),
			"autocomplete"=> "off"
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
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="giro" class="<?php echo $label_column; ?>"><?php echo lang('giro'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "giro",
            "name" => "giro",
            "value" => $model_info->giro,
            "class" => "form-control",
            "placeholder" => lang('giro'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="pais" class="<?php echo $label_column; ?>"><?php echo lang('country'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "pais",
            "name" => "pais",
            "value" => $model_info->pais,
            "class" => "form-control",
            "placeholder" => lang('country'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="ciudad" class="<?php echo $label_column; ?>"><?php echo lang('city'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "ciudad",
            "name" => "ciudad",
            "value" => $model_info->ciudad,
            "class" => "form-control",
            "placeholder" => lang('city'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="comuna" class="<?php echo $label_column; ?>"><?php echo lang('town'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "comuna",
            "name" => "comuna",
            "value" => $model_info->comuna,
            "class" => "form-control",
            "placeholder" => lang('town'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="direccion" class="<?php echo $label_column; ?>"><?php echo lang('address'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_textarea(array(
            "id" => "direccion",
            "name" => "direccion",
            "value" => $model_info->direccion ? $model_info->direccion : "",
            "class" => "form-control",
            "placeholder" => lang('address'),
			"autocomplete"=> "off"
        ));
        ?>

    </div>
</div>
<div class="form-group">
    <label for="fono" class="<?php echo $label_column; ?>"><?php echo lang('phone'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "fono",
            "name" => "fono",
            "value" => $model_info->fono,
            "class" => "form-control",
            "placeholder" => lang('phone'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
</div>
<div class="form-group">
    <label for="website" class="<?php echo $label_column; ?>"><?php echo lang('website'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "website",
            "name" => "website",
            "value" => $model_info->website,
            "class" => "form-control",
            "placeholder" => lang('website'),
			"autocomplete"=> "off"
        ));
        ?>
    </div>
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
    <div class="<?php echo $field_column; ?>">
    	<div id="cp11" class="input-group colorpicker-component colorpicker-default">
        <?php
        echo form_input(array(
            "id" => "color_sitio",
            "name" => "color_sitio",
            "value" => ($model_info->color_sitio)?$model_info->color_sitio:'#00b393',
            "class" => "form-control",
            "placeholder" => lang('site_color'),
			"autocomplete"=> "off",
			"readonly"=> true
        ));
        ?>
        <span class="input-group-addon"><i></i></span>
        </div>
    </div>
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
		echo form_checkbox("habilitado", "1", $checked, "id='habilitado'");
        ?>
    </div>
</div>

<!--
<div class="panel-body post-dropzone">
    <div class="form-group">
        <label for="logo" class=" col-md-2"><?php echo lang('site_logo'); ?></label>
        <div class=" col-md-10">
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
    
    

    
    <div class="form-group hide">
        <label class=" col-md-2"><?php echo lang('signin_page_background'); ?></label>
        <div class=" col-md-10">
            <div class="pull-left mr15">
                <img id="signin-background-preview" style="max-width: 100px; max-height: 80px;" src="<?php echo get_file_uri(get_setting("system_file_path") . "sigin-background-image.jpg"); ?>" alt="..." />
            </div>
            <div class="pull-left mr15">
                <?php $this->load->view("includes/dropzone_preview"); ?>    
            </div> 
            <div class="pull-left upload-file-button btn btn-default btn-xs">
                <span>...</span>
            </div>
        </div>
    </div>

</div> 
-->

<?php $this->load->view("includes/cropbox"); ?>
<script type="text/javascript">
    $(document).ready(function () {
		
		//$('.colorpicker-default, .colorpicker-default').colorpicker({
		$('#color_sitio').colorpicker({
			format: 'hex',
		});
		
        $('[data-toggle="tooltip"]').tooltip();
		
		$(".cropbox-upload").change(function () {
            showCropBox(this);
        });
		
    });
</script>