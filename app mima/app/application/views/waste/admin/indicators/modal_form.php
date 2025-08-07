<?php echo form_open(get_uri("indicators/save"), array("id" => "indicators-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <?php $this->load->view("waste/admin/indicators/indicator_form_fields"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
		
		$("#indicators-form .select2").select2();
		
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

		$('#category').multiSelect({
			selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
			selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
			//selectionFooter: "<div class='multiselect-header col-md-12'><div class='col-md-6'><a id='subir_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-up' aria-hidden='true'></i></a></div><div class='col-md-6'><a id='bajar_campo' class='btn btn-xs btn-default'><i class='fa fa-arrow-down' aria-hidden='true'></i></a></div></div>",
			keepOrder: true,
			afterSelect: function(value){
				$('#category option[value="'+value+'"]').remove();
				$('#category').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
			},
			afterDeselect: function(value){ 
				$('#category option[value="'+value+'"]').removeAttr('selected');
				
			},
		});
		
		$('#clients').change(function(){
			
			var id_client = $(this).val();
			select2LoadingStatusOn($('#project'));
			
			$.ajax({
				url: '<?php echo_uri("clients/get_projects_of_client_json") ?>',
				type: 'post',
				data: {id_client:id_client},
				dataType: 'json',
				success: function(respuesta){
					
					select2LoadingStatusOff($('#project'));
					
					$('#project').html('');
					$.each(respuesta, function(index, row) {
						$('#project').append($("<option />").val(row.id).text(row.text));
					});
					$('#project').select2();
				}
			});
			
		});	
		
		$('#project').change(function(){
			
			$('#fields_category').html("");
			var id_project = $(this).val();
			
			$.ajax({
				url:  '<?php echo_uri("indicators/get_categories") ?>',
				type:  'post',
				data: {id_project:id_project},
				success: function(result){
					
					$('#fields_category').html(result);
					$('#category').multiSelect({
						selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available_fields"); ?>" + "</div>",
						selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected_fields"); ?>" + "</div>",
						keepOrder: true,
						afterSelect: function(value){
							$('#category option[value="'+value+'"]').remove();
							$('#category').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
							
						},
						afterDeselect: function(value){ 
							$('#category option[value="'+value+'"]').removeAttr('selected');
							
						},
					});
	
				}
			});
			
		});	
		
        $("#icon").select2().select2("val", '<?php echo $icon_selected ?>');
        
        function format(state) {
            var iconos = "";
            if(state.text == "-"){
                iconos = state.text;
            } else {
				iconos = "<i class='fa "+state.text+"'> "+state.text+"</i>"
            }
			
            return iconos;
        }
        
        $("#icon").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        });
		
        $("#indicators-form").appForm({
            onSuccess: function(result) {
                if (result.success === "true") {
                    appAlert.success(result.message, {duration: 10000});
					
                    setTimeout(function() {
                        location.reload();
                    }, 500);
					
                } else {
                    $("#indicators-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

    });
</script>
<style>
.multiselect-header{
  text-align: center;
  padding: 3px;
  background: #7988a2;
  color: #fff;
}
</style>