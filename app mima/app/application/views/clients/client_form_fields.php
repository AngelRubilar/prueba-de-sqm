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
            "autocomplete"=> "off",
			"maxlength" => "255"
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
            "autocomplete"=> "off",
			"maxlength" => "255"
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
            "autocomplete"=> "off",
			"maxlength" => "255"
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
            "autocomplete"=> "off",
			"maxlength" => "255"
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
            "autocomplete"=> "off",
			"maxlength" => "255"
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
            "autocomplete"=> "off",
			"maxlength" => "2000"
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
            "data-rule-number" => true,
            "data-msg-number" => lang("enter_just_numbers"),
            "autocomplete"=> "off",
			//"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <label for="contacto" class="<?php echo $label_column; ?>"><?php echo lang('contact_email'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        echo form_input(array(
            "id" => "contacto",
            "name" => "contacto",
            "value" => $model_info->contacto,
            "class" => "form-control",
            "placeholder" => lang('email'),
			"data-rule-email" => true,
			"data-msg-email" => lang("enter_valid_email"),
			"data-rule-required" => true,
			"data-msg-required" => lang("field_required"),
			"autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>

<div class="form-group" id="modelo" style="display:none;">
    <label for="client_groups" class="col-md-3 control-label"></label>
    <div class="col-md-8">
        <input type="text" class="form-control" name="client_groups[]" maxlength="255" placeholder="<?php echo lang('groups'); ?>" autocomplete="off" >
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
    </div>
</div>

<div class="form-group">
    <label for="planning" class="col-md-3"><?php echo lang('groups'); ?></label>
    <div class="col-md-9">
        <button type="button" id="agregar_grupo" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>
        <button type="button" id="eliminar_grupo" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>
    </div>
</div>

<?php
	if(count($array_client_groups)){
		
		$html_client_group = '';
		
		foreach($array_client_groups as $client_group){
			$html_client_group .= '<div class="form-group grupo">';
				$html_client_group .= '<label for="client_group" class="col-md-3"></label>';
				$html_client_group .= '<div class="col-md-8">';
				$html_client_group .= '<input type="text" class="form-control" name="client_groups[]['.$client_group["id"].']" maxlength="255" placeholder="'.lang('group').'" value="'.$client_group["group_name"].'" autocomplete="off">';
				$html_client_group .= '</div>';
				$html_client_group .= '<div class="col-md-1">';
				$html_client_group .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>';
				$html_client_group .= '</div>';
			$html_client_group .= '</div>';
			
			$html_client_group .= '<input type="hidden" name="client_groups_id['.$client_group["id"].']" value="'.$client_group["id"].'">';
			
		}
		
		echo $html_client_group;
	
	}
?>

<div id="grupo_grupos">
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
            "autocomplete"=> "off",
			"maxlength" => "255"
        ));
        ?>
    </div>
</div>
<!--
<div class="form-group">
    <label for="logo" class=" col-md-3"><?php echo lang('site_logo'); ?></label>
    <div class=" col-md-9">
        <div class="pull-left mr15">
            <img id="site-logo-preview" src="" alt="..." />
        </div>
        <div class="pull-left file-upload btn btn-default btn-xs">
            <span>...</span>
            <input id="site_logo_file" class="cropbox-upload upload" name="site_logo_file" type="file" data-height="40" data-width="175" data-preview-container="#site-logo-preview" data-input-field="#site_logo" />
        </div>
        <input type="hidden" id="site_logo" name="site_logo" value=""  />
    </div>
</div>
 -->

<?php
$url_logo = "";

if($model_info->id){
	if($model_info->logo){
		$last_modif = filemtime("files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png");
		$url_logo = get_file_uri("files/mimasoft_files/client_".$model_info->id."/".$model_info->logo.".png?=".$last_modif);
	} else {
		$url_logo = get_file_uri("files/system/default-site-logo.png");
	}
} else {
	$url_logo = get_file_uri("files/system/default-site-logo.png");
}

?>
<div class="form-group">
	<?php //$info = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('site_logo_info').'"><i class="fa fa-question-circle"></i></span>'; ?>
    <label for="logo" class="<?php echo $label_column; ?>"><?php echo lang('site_logo')//.' '.($info); ?></label>
    <div class="<?php echo $field_column; ?>">
        <div class="pull-left mr15">
            <img id="site-logo-preview" src="<?php echo $url_logo; ?>" alt="..." />
        </div>
        <div class="pull-left mr15">
            <?php $this->load->view("includes/dropzone_preview"); ?>
        </div>
        <div class="pull-left file-upload btn btn-default btn-xs">
            <span>...</span>
            <input id="site_logo_file" class="cropbox-upload upload" name="site_logo_file" type="file" data-height="60" data-width="175" data-preview-container="#site-logo-preview" data-input-field="#site_logo" />
        </div>
        <input type="hidden" id="site_logo" name="site_logo" value=""/>
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
            "value" => ($model_info->color_sitio)?$model_info->color_sitio:'',
            "class" => "form-control",
            "placeholder" => lang('site_color'),
            "autocomplete"=> "off",
            "readonly"=> true
        ));
        ?>
        <span class="input-group-addon"><i id="coloricon" style="border: solid black 1px;"></i></span>
        </div>
    </div>

    <div class="<?php echo $field_column; ?>" align="right">
        <div >
        <a id="default" title="Seleccionar color por defecto de mimasoft" href="#">Color por defecto</a>
        </div>
    </div>

</div>
<div class="form-group">
    <label for="habilitado" class="<?php echo $label_column; ?>"><?php echo lang('site_status'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        if($model_info->id){
            echo form_checkbox("habilitado", "1", ($model_info->habilitado) ? true : false, "id='habilitado' ");
        }else{
            echo form_checkbox("habilitado", "1", ($model_info->habilitado) ? true : false, "id='habilitado' checked  ");
        }
        
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


<script type="text/javascript">
    $(document).ready(function () {
		
		$('input[type="text"][maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
		$('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
        
        $('#cp11').colorpicker({
            format: 'hex',
			extensions: [{
			  name: 'swatches',
			  colors: {
				'#000000': '#000000',
				'#ffffff': '#ffffff',
				'#FF0000': '#FF0000',
				'#777777': '#777777',
				'#337ab7': '#337ab7',
				'#5cb85c': '#5cb85c',
				'#5bc0de': '#5bc0de',
				'#f0ad4e': '#f0ad4e',
				'#d9534f': '#d9534f',
				'#8a6d3b': '#8a6d3b',
			  },
			  namesAsValues: true
			}],
			template: '<div class="colorpicker dropdown-menu"><div class="colorpicker-palette"></div><div class="colorpicker-color"><div /></div></div>'
        });

        $('#default').click(function(){
            $('#color_sitio').val("#00b393");
            $('#coloricon').css('background-color', '#00b393');
        });
        
        $('[data-toggle="tooltip"]').tooltip();
        /* $('#rut').rut({
            formatOn: 'keyup',
			formatOn: 'keyup',
            minimumLength: 8,
            validateOn: 'change'
        });  */
		
		//Botón eliminar incluye validación de nombre de compañía, esto cierra el mensaje
		$('#ajaxModal').on('hidden.bs.modal', function () {
			$('.app-alert').hide();
		});

    }); 
	
	function addOptions(){
		$('#client-form #grupo_grupos').append($("<div/>").addClass('form-group grupo').html($('#client-form #modelo').html()));
		//$('#client-form .grupo').last().find('input').attr('data-rule-required', true);
		//$('#client-form .grupo').last().find('input').attr('data-msg-required', '<?php echo lang("field_required"); ?>');
		//$('#client-form .grupo').last().find('input').attr('aria-required', true);
		$('#client-form .grupo').last().find('input').maxlength({
			//alwaysShow: true,
			threshold: 245,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
	}
	
	function removeOptions(){
		$('#client-form .grupo').last().remove();
	}
	
	function removeOption(element){
		element.closest('#client-form .grupo').remove();
	}
</script>