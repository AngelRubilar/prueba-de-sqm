<div id="page-content" class="m20 clearfix">
<!--Breadcrumb section-->
<?php if($this->session->project_context){ ?>
    <nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="<?php echo get_uri("inicio_projects"); ?>"><?php echo lang("projects"); ?> /</a>
      <a class="breadcrumb-item" href="<?php echo get_uri("dashboard/view/".$project_info->id); ?>"><?php echo $project_info->title; ?> /</a>
      <a class="breadcrumb-item" href="#"><?php echo lang("help_and_support"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("contact"); ?></a>
    </nav>
<?php } else { ?>
	<nav class="breadcrumb"> 
      <a class="breadcrumb-item" href="#"><?php echo lang("help_and_support"); ?> /</a>
      <a class="breadcrumb-item" href=""><?php echo lang("contact"); ?></a>
    </nav>
<?php } ?>

<?php if($puede_ver == 1) { ?>

  <?php echo form_open(get_uri("contact/save"), array("id" => "contact-form", "class" => "general-form", "role" => "form")); ?>

    <div class="panel col-md-12">
      <div class="panel-default panel-heading">
        <h4 style="font-size:16px"><?php echo lang('contact'); ?></h4>
      </div>
      <div class="panel-body">
        <!--<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />-->
        <input type="hidden" name="contact" value="<?php echo $client_contact; ?>" />
        
        <div class="form-group" >
          <div class="col-md-6 col-sm-6 col-xs-6">
            <?php
                 echo form_input(array(
                "id" => "nombre",
                "name" => "nombre",
                //"value" => $model_info->nombre,
                "class" => "form-control",
                "placeholder" => lang('name'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "autocomplete" => "on",
            ));
            ?>
          </div>
          <div class="col-md-6 col-sm-6 col-xs-6">
            <?php
                 echo form_input(array(
                "id" => "correo",
                "name" => "correo",
                //"value" => $model_info->correo,
                "class" => "form-control",
                "placeholder" => lang('email'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-rule-email"=>true,
                "data-msg-email" => lang("enter_valid_email"),
                "data-msg-required" => lang("field_required"),
                "autocomplete" => "on",
            ));
            ?>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <?php
                 echo form_input(array(
                "id" => "asunto",
                "name" => "asunto",
                //"value" => $model_info->asunto,
                "class" => "form-control",
                "placeholder" => lang('issue'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "aria-required"=>true,
                "aria-invalid"=>false,
                "data-msg-required" => lang("field_required"),
                "autocomplete" => "off",
            ));
            ?>
          </div>
        </div>
        <div class="form-group">

            <div class="col-md-12 col-sm-12 col-xs-12">
            <?php
                 echo form_textarea(array(
                "id" => "contenido",
                "name" => "contenido",
                //"value" => $model_info->mensaje,
                "class" => "form-control",
                "placeholder" => lang('message'),
                //"autofocus" => true,
                //"data-rule-required" => true,
                //"data-msg-required" => lang("field_required"),
                "autocomplete" => "off",
            ));
            ?>
          </div>

        </div>
      </div>
      <div class="panel-footer" align="right">
        <button type="submit" class="btn btn-primary"><span class="fa fa-send"></span> <?php echo lang('send'); ?></button>
      </div>
      
    </div>
    <?php echo form_close(); ?> 

</div>

<?php } else { ?>
    <div class="row"> 
        <div class="col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div id="app-alert-d1via" class="app-alert alert alert-danger alert-dismissible m0" role="alert"><!--<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>-->
                        <div class="app-alert-message"><?php echo lang("content_disabled"); ?></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger hide" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#contact-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});  
                document.getElementById('asunto').value="";
				document.getElementById('contenido').value="";
				
            }
        });



        
    });
</script>