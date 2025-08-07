<input type="hidden" name="id_alert_config" value="<?php echo $id_alert_config; ?>" />
<input type="hidden" name="id_client" value="<?php echo $id_client; ?>" />
<input type="hidden" name="id_project" value="<?php echo $id_project; ?>" />
<input type="hidden" name="id_module" value="<?php echo $id_modulo; ?>" />
<input type="hidden" name="id_submodule" value="<?php echo $id_submodulo; ?>" />

<input type="hidden" name="id_air_station" value="<?php echo $id_air_station; ?>" />
<input type="hidden" name="id_air_sector" value="<?php echo $id_air_sector; ?>" />
<input type="hidden" name="id_air_variable" value="<?php echo $id_air_variable; ?>" />
<input type="hidden" name="accordeon" value="<?php echo $accordeon; ?>" />

<div class="form-group">
    <label for="module" class="<?php echo $label_column; ?>"><?php echo lang('module'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $modulo;
        ?>
    </div>
</div>

<div class="form-group">
    <label for="submodule" class="<?php echo $label_column; ?>"><?php echo lang('sector'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $name_sector;
        ?>
    </div>
</div>

<div class="form-group">
    <label for="submodule" class="<?php echo $label_column; ?>"><?php echo lang('variable'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $name_variable;
        ?>
    </div>
</div>


<?php if($accordeon == "forecast_alerts"){ ?>

    <div id="body_modelo" style="display:none;">

            <div id="body_nc_active">
                <?php echo form_checkbox("nc_active[]", "1", false, ""); ?>
                <input type='hidden' value='0' name='nc_active[]' class="nc_active_hidden">
            </div>

            <div id="body_nc_name">
                <?php
                    echo form_input(array(
                        //"id" => "nc_name",
                        "name" => "nc_name[]",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => lang('name'),
                        //"autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
                        "autocomplete"=> "off",
                        "maxlength" => "255"
                    ));
                ?>
            </div>

            <div id="body_nc_color">
                <div class="input-group colorpicker-component colorpicker-default nc_color col-md-2">
                    <input type="hidden" name="nc_color[]" value="" />
                    <span class="input-group-addon"><i id="coloricon" style="border: solid black 1px;"></i></span>
                </div>
            </div>

            <div id="body_nc_range">
                <!--
                <div class="col-md-12 p0 multi-column">
                    <div class="col-md-5 p0">
                        <?php
                            echo form_input(array(
                                //"id" => "min_value",
                                "name" => "min_value[]",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => lang('min_value'),
                                //"autofocus" => true,
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                "data-msg-regex" => lang("number_or_decimal_required"),
                                "autocomplete"=> "off",
                                "maxlength" => "255"
                            ));
                        ?>
                    </div>
                    <div class="col-md-2 p0">
                    -
                    </div>
                    <div class="col-md-5 p0">
                        <?php
                            echo form_input(array(
                                //"id" => "max_value",
                                "name" => "max_value[]",
                                "value" => "",
                                "class" => "form-control",
                                "placeholder" => lang('max_value'),
                                //"autofocus" => true,
                                "data-rule-required" => true,
                                "data-msg-required" => lang("field_required"),
                                "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                "data-msg-regex" => lang("number_or_decimal_required"),
                                "autocomplete"=> "off",
                                "maxlength" => "255"
                            ));
                        ?>
                    </div>
                </div>
                -->

                <?php
                    echo form_input(array(
                        //"id" => "min_value",
                        "name" => "min_value[]",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => lang('min_value'),
                        //"autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => lang("field_required"),
                        "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                        "data-msg-regex" => lang("number_or_decimal_required"),
                        "autocomplete"=> "off",
                        "maxlength" => "255"
                    ));
                ?>

            </div>

            <div id="body_nc_btn_delete">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
            </div>

        </tr>
    </div>

    <div class="form-group">
        <label for="planning" class="col-md-3"><?php echo lang('quality_levels'); ?></label>
        <div class="col-md-9">
            
            <button type="button" id="agregar_planificacion" class="btn btn-xs btn-success col-sm-1" onclick="addOptions();"><i class="fa fa-plus"></i></button>
            <button type="button" id="eliminar_planificacion" class="btn btn-xs btn-danger col-sm-offset-1 col-sm-1" onclick="removeOptions();"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    
    <div id="grupo_nc">
        
        <?php if(count($array_data_alert_config)){ ?>
            
            <div class="form-group multi-column">
                <table id="table_config_nc" class="table table-hover">
                    <tr>
                        <th class="text-center"><?php echo lang("activated"); ?></th>
                        <th class="text-center"><?php echo lang("name"); ?></th>
                        <th class="text-center"><?php echo lang("color"); ?></th>
                        <!--<th class="text-center"><?php echo lang("range"); ?></th>-->
                        <th class="text-center"><?php echo lang("min_value"); ?></th>
                        <th class="text-center"><i class="fa fa-bars"></i></th>
                    </tr>
                    <?php foreach($array_data_alert_config as $data_nc){ ?>
                        <tr>
                            <td class="text-center">
                                <?php echo form_checkbox("nc_active[]", "1", $data_nc["nc_active"] ? true : false, ""); ?>
                                <input type='hidden' value='0' name='nc_active[]' class="nc_active_hidden">
                            </td>
                            <td class="text-center">
                                <?php
                                    echo form_input(array(
                                        //"id" => "nc_name",
                                        "name" => "nc_name[]",
                                        "value" => $data_nc["nc_name"],
                                        "class" => "form-control",
                                        "placeholder" => lang('name'),
                                        //"autofocus" => true,
                                        "data-rule-required" => true,
                                        "data-msg-required" => lang("field_required"),
                                        "autocomplete"=> "off",
                                        "maxlength" => "255"
                                    ));
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="input-group colorpicker-component colorpicker-default nc_color col-md-2">
                                    <input type="hidden" name="nc_color[]" value="<?php echo $data_nc["nc_color"]; ?>" />
                                    <span class="input-group-addon"><i id="coloricon" style="border: solid black 1px;"></i></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <!--
                                <div class="col-md-12 p0">
                                    <div class="col-md-5 p0">
                                        <?php
                                            echo form_input(array(
                                                //"id" => "min_value",
                                                "name" => "min_value[]",
                                                "value" => $data_nc["min_value"],
                                                "class" => "form-control",
                                                "placeholder" => lang('min_value'),
                                                //"autofocus" => true,
                                                "data-rule-required" => true,
                                                "data-msg-required" => lang("field_required"),
                                                "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                                "data-msg-regex" => lang("number_or_decimal_required"),
                                                "autocomplete"=> "off",
                                                "maxlength" => "255"
                                            ));
                                        ?>
                                    </div>
                                    <div class="col-md-2 p0">
                                    -
                                    </div>
                                    <div class="col-md-5 p0">
                                        <?php
                                            echo form_input(array(
                                                //"id" => "max_value",
                                                "name" => "max_value[]",
                                                "value" => $data_nc["max_value"],
                                                "class" => "form-control",
                                                "placeholder" => lang('max_value'),
                                                //"autofocus" => true,
                                                "data-rule-required" => true,
                                                "data-msg-required" => lang("field_required"),
                                                "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                                "data-msg-regex" => lang("number_or_decimal_required"),
                                                "autocomplete"=> "off",
                                                "maxlength" => "255"
                                            ));
                                        ?>
                                    </div>
                                </div>
                                -->
                                <?php
                                    echo form_input(array(
                                        //"id" => "min_value",
                                        "name" => "min_value[]",
                                        "value" => $data_nc["min_value"],
                                        "class" => "form-control",
                                        "placeholder" => lang('min_value'),
                                        //"autofocus" => true,
                                        "data-rule-required" => true,
                                        "data-msg-required" => lang("field_required"),
                                        "data-rule-regex" => "^(?!-0(\.0+)?$)-?(0|[1-9]\d*)(\.\d+)?$",
                                        "data-msg-regex" => lang("number_or_decimal_required"),
                                        "autocomplete"=> "off",
                                        "maxlength" => "255"
                                    ));
                                ?>
                            </td>
                            <td class="text-center">
                                <div id="body_nc_btn_delete">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeOption($(this));"><i class="fa fa-trash-o"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        <?php } ?>

    </div>

<?php } ?>


<?php if($accordeon == "action_plan"){ ?>

    <div class="form-group multi-column">

        <table class="table table-hover">

            <tr>
                <th class="text-center"><?php echo lang("activated"); ?></th>
                <th class="text-center"><?php echo lang("name"); ?></th>
                <th class="text-center"><?php echo lang("min_value"); ?></th>
                <th class="text-center"><?php echo lang("action_plan"); ?></th>
                <th class="text-center"><?php echo lang("email"); ?></th>
                <th class="text-center"><?php echo lang("web"); ?></th>
            </tr>
            
            <?php if(count($alert_config_forecast_alerts)) { ?>
                <?php foreach($alert_config_forecast_alerts as $config_forecast_alert) { ?>
                    <tr>
                        <td class="text-center">
                            <?php echo form_checkbox("ap_active[]", "1", $config_forecast_alert["ap_active"] ? true : false, ""); ?>
                            <input type='hidden' value='0' name='ap_active[]' class="ap_active_hidden">
                        </td>

                        <td><?php echo $config_forecast_alert["nc_name"]; ?></td>
                        <td><?php echo $config_forecast_alert["min_value"]; ?></td>
                    
                        <td>
                            <?php
                                echo form_textarea(array(
                                    //"id" => "ap_action_plan",
                                    "name" => "ap_action_plan[]",
                                    "value" => $config_forecast_alert["ap_action_plan"],
                                    "class" => "form-control",
                                    "placeholder" => lang('action_plan'),
                                    "autofocus" => false,
                                    "autocomplete"=> "off",
                                    "maxlength" => "2000"
                                ));
                            ?>
                        </td>

                        <td class="text-center">
                            <?php echo form_checkbox("ap_email[]", "1", $config_forecast_alert["ap_email"] ? true : false, ""); ?>
                            <input type='hidden' value='0' name='ap_email[]' class="ap_active_hidden">
                        </td>

                        <td class="text-center">
                            <?php echo form_checkbox("ap_web[]", "1", $config_forecast_alert["ap_web"] ? true : false, ""); ?>
                            <input type='hidden' value='0' name='ap_web[]' class="ap_active_hidden">
                        </td>

                    </tr>
                <?php } ?>

            <?php } else { ?>
                
                <tr>
                    <td class="text-center" colspan="6"><?php echo lang("no_forecast_alerts_configured"); ?></td>
                </tr>

            <?php } ?>
        </table>

    </div>

    <div class="form-group">
        <label for="groups" class="<?php echo $label_column; ?>"><?php echo lang('groups'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
                echo form_multiselect("groups[]", $array_client_groups, $selected_client_groups, "id='groups' class='select2 multiple' multiple='multiple'");
            ?>
        </div>
    </div>

    <div id="users_group">
        <div class="form-group">
            <label for="users" class="<?php echo $label_column; ?>"><?php echo lang('users'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                    echo form_multiselect("users[]", $array_client_users, $selected_client_users, "id='users_admin_config' class='select2 multiple' multiple='multiple'");
                ?>
            </div>
        </div>
    </div>

<?php } ?>

<script type="text/javascript">

    $(document).ready(function () {
		
        $('[data-toggle="tooltip"]').tooltip();
        $('#alert_config_air-form .nc_color').colorpicker({format: 'hex'});

        $('textarea[maxlength]').maxlength({
			//alwaysShow: true,
			threshold: 1990,
			warningClass: "label label-success",
			limitReachedClass: "label label-danger",
			appendToParent:true
		});
		
    });

    <?php if($accordeon == "forecast_alerts"){ ?>

        function addOptions(){

            if(!$('#alert_config_air-form #grupo_nc #table_config_nc').is(':visible')){
                
                $('#alert_config_air-form #grupo_nc').html(
                    '<div class="form-group multi-column">'
                        +'<table id="table_config_nc" class="table table-hover">'
                            +'<tr>'
                                +'<th class="text-center">'+'<?php echo lang("activated"); ?>'+'</th>'
                                +'<th class="text-center">'+'<?php echo lang("name"); ?>'+'</th>'
                                +'<th class="text-center">'+'<?php echo lang("color"); ?>'+'</th>'
                                //+'<th class="text-center">'+'<?php echo lang("range"); ?>'+'</th>'
                                +'<th class="text-center">'+'<?php echo lang("min_value"); ?>'+'</th>'
                                +'<th class="text-center"><i class="fa fa-bars"></i></th>'
                            +'</tr>'
                        +'</table>'
                    +'</div>'
                );

            }

            $('#table_config_nc:last-child').append(
                '<tr>'
                    +'<td class="text-center">'
                        +$('#body_modelo #body_nc_active').html()
                    +'</td>'
                    +'<td class="text-center">'
                        +$('#body_modelo #body_nc_name').html()
                    +'</td>'
                    +'<td class="text-center">'
                        +$('#body_modelo #body_nc_color').html()
                    +'</td>'
                    +'<td class="text-center multi-column">'
                        +$('#body_modelo #body_nc_range').html()
                    +'</td>'
                    +'<td class="text-center">'
                        +$('#body_modelo #body_nc_btn_delete').html()
                    +'</td>'
                +'</tr>'
            );

            var rows = $('#table_config_nc tr').length;
            console.log(rows);

            $('#alert_config_air-form input[name^="nc_name"], input[name^="min_value"], input[name^="max_value"]').maxlength({
                //alwaysShow: true,
                threshold: 245,
                warningClass: "label label-success",
                limitReachedClass: "label label-danger",
                appendToParent:true
            });
            $('#alert_config_air-form .nc_color').colorpicker({format: 'hex'});

        }
        
        function removeOptions(){
            $('#table_config_nc tr').last().remove();
            var rows = $('#table_config_nc tr').length;
            if(rows == 1){
                $('#alert_config_air-form #grupo_nc').html("");
            }
        }

        function removeOption(element){
            var rows = $('#table_config_nc tr').length;
            if(rows == 2){
                $('#alert_config_air-form #grupo_nc').html("");
            } else {
                element.closest('tr').remove();
            }
        }

    <?php } ?>

    <?php if($accordeon == "action_plan"){ ?>

        $("#groups, #users_admin_config").select2();

        $("#groups").change(function(){
			
			var id_client = '<?php echo $id_client; ?>';
			var id_project = '<?php echo $id_project; ?>';
			var groups = $(this).val();
			var evento = "admin_config";
						
			$.ajax({
                url:  '<?php echo_uri("general_settings/get_user_members_of_groups") ?>',
                type:  'post',
                data: {id_client: id_client, id_project: id_project, groups: groups, evento: evento},
                //dataType:'json',
                success: function(respuesta){
                    $('#users_group').html(respuesta);    
                    $('#users_admin_config').select2();
                }
                
            });

		});

    <?php } ?>

</script>