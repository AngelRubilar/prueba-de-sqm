<?php 
echo form_open(get_uri("relationship/save_asignacion_add"), array("id" => "asignacion-form", "class" => "general-form", "role" => "form", "autocomplete" => "off")); ?>
<div class="modal-body clearfix">

    <div class="form-widget">
        <div class="widget-title clearfix">
            <div id="general-info-label" class="col-sm-4"><i class="fa fa-circle-o"></i><strong> <?php echo lang('rule'); ?></strong></div>
            <div id="job-info-label" class="col-sm-4"><i class="fa fa-circle-o"></i><strong>  <?php echo lang('subproject'); ?></strong></div>
            <div id="account-info-label" class="col-sm-4"><i class="fa fa-circle-o"></i><strong>  <?php echo lang('unit_process'); ?></strong></div> 
        </div>

        <div class="progress ml15 mr15">
            <div id="form-progress-bar" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
            </div>
        </div>
    </div>

    <div class="tab-content mt15">
        <div role="tabpanel" class="tab-pane active" id="general-info-tab">
            
           <?php $this->load->view("relationship/asignacion/asignacion_uno_fields"); ?>
            
        </div>
        <div role="tabpanel" class="tab-pane" id="job-info-tab">
        
            <?php $this->load->view("relationship/asignacion/asignacion_dos_fields"); ?>
            
        </div>
        <div role="tabpanel" class="tab-pane" id="account-info-tab">
        	
            <?php $this->load->view("relationship/asignacion/asignacion_tres_fields"); ?>
            
        </div>
    </div>

</div>


<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button id="form-previous" type="button" class="btn btn-default hide"><span class="fa fa-arrow-circle-left"></span> <?php echo lang('previous'); ?></button>
    <button id="form-next" type="button" class="btn btn-info"><span class="fa  fa-arrow-circle-right"></span> <?php echo lang('next'); ?></button>
    <button id="form-submit" type="button" class="btn btn-primary hide"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		$("#asignacion-form .select2").select2();
		
        $("#asignacion-form").appForm({
            onSuccess: function(result) {
				$("#asignacion-table").appTable({newData: result.data, dataId: result.id});
            },
            onSubmit: function() {
                $("#form-previous").attr('disabled', 'disabled');
            },
            onAjaxSuccess: function() {
                $("#form-previous").removeAttr('disabled');
            }
        });
		$('#asignacion-form').validate().settings.ignore = "";

        $("#asignacion-form input").keydown(function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                if ($('#form-submit').hasClass('hide')) {
                    $("#form-next").trigger('click');
                } else {
                    $("#asignacion-form").trigger('submit');
                }
            }
        });

        $("#form-previous").click(function() {
            var $generalTab = $("#general-info-tab"),
                    $jobTab = $("#job-info-tab"),
                    $accountTab = $("#account-info-tab"),
                    $previousButton = $("#form-previous"),
                    $nextButton = $("#form-next"),
                    $submitButton = $("#form-submit");

            if ($accountTab.hasClass("active")) {
                $accountTab.removeClass("active");
                $jobTab.addClass("active");
                $nextButton.removeClass("hide");
                $submitButton.addClass("hide");
            } else if ($jobTab.hasClass("active")) {
                $jobTab.removeClass("active");
                $generalTab.addClass("active");
                $previousButton.addClass("hide");
                $nextButton.removeClass("hide");
                $submitButton.addClass("hide");
            }
        });

        $("#form-next").click(function() {
            var $generalTab = $("#general-info-tab"),
                    $jobTab = $("#job-info-tab"),
                    $accountTab = $("#account-info-tab"),
                    $previousButton = $("#form-previous"),
                    $nextButton = $("#form-next"),
                    $submitButton = $("#form-submit");
            //if (!$("#asignacion-form").valid()) {
			
            if ($generalTab.hasClass("active")) {
				
				if (!$("#client_id2, #project2, #criterio2").valid()) {	
					return false;
				}
				
                $generalTab.removeClass("active");
                $jobTab.addClass("active");
                $previousButton.removeClass("hide");
                $("#form-progress-bar").width("35%");
                $("#general-info-label").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                $("#team_member_id").focus();
            } else if ($jobTab.hasClass("active")) {
				
				if (!$("#rule_options_sp_group select.tipo_asignacion_sp, #rule_options_sp_group select.subproject, #rule_options_sp_group .campo_porc_total").valid()) {
					return false;
				}
				
                $jobTab.removeClass("active");
                $accountTab.addClass("active");
                $previousButton.removeClass("hide");
                $nextButton.addClass("hide");
                $submitButton.removeClass("hide");
                $("#form-progress-bar").width("72%");
                $("#job-info-label").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
            }
        });

        $("#form-submit").click(function() {
			
			if (!$("#rule_options_pu_group select.tipo_asignacion_pu, #rule_options_pu_group select.unit_process, #rule_options_pu_group .campo_porc_total").valid()) {
				return false;
			}
			
			var $modelo_sp = $('#modelo_sp').clone();
			var $modelo_pu = $('#modelo_pu').clone();
			
			if(!$("#asignacion-form").valid()){
				
				$('#modelo_sp').html('');
				$('#modelo_pu').html('');
				
				if($("#asignacion-form").valid()){
					$("#asignacion-form").submit();
				}else{
					$('#modelo_sp').replaceWith($modelo_sp);
					$('#modelo_pu').replaceWith($modelo_pu);
				}
				
			}else{
				$("#asignacion-form").submit();
			}
            
        });
		
		

    });
</script>
  