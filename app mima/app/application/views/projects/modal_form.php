<?php echo form_open(get_uri("projects/save"), array("id" => "project-form", "class" => "general-form", "role" => "form")); ?>
<?php
$this->load->view("includes/summernote");
?>
<style>
.multiselect-header{
  text-align: center;
  padding: 3px;
  background: #7988a2;
  color: #fff;
}
</style> 
<div class="modal-body clearfix">

    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <!-- <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" /> -->
    <!-- <input type="hidden" name="id_unidad_funcional" value="<?php echo $id_unidad_funcional; ?>" /> -->
    <!-- <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" /> -->
    <!-- <input type="hidden" name="id_project_rel_fases" value="<?php echo $project_rel_fases->id; ?>" /> -->

    <?php if ($client_id) { ?>
        <!--<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />-->
         <div class="form-group">
            <label for="client_id" class=" col-md-3"><?php echo lang('client'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_dropdown("client_id", $clients_dropdown, array($model_info->client_id), "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>    
	
	
    <?php } else { ?>

        <div class="form-group">
            <label for="client_id" class=" col-md-3"><?php echo lang('client'); ?></label>
            <div class=" col-md-9">
                <?php
                echo form_dropdown("client_id", $clients_dropdown, array($model_info->client_id), "id='clienteCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <label for="client_label" class=" col-md-3"><?php echo lang('client_label'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "client_label",
                "name" => "client_label",
                "value" => $model_info->client_label,
                "class" => "form-control",
                "placeholder" => lang('client_label'),
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
        <label for="client_label_rut" class=" col-md-3"><?php echo lang('client_label_rut'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "client_label_rut",
                "name" => "client_label_rut",
                "value" => $model_info->client_label_rut,
                "class" => "form-control",
                "placeholder" => lang('client_label_rut'),
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
        <label for="legal_representative" class=" col-md-3"><?php echo lang('legal_representative'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "legal_representative",
                "name" => "legal_representative",
                "value" => $model_info->legal_representative,
                "class" => "form-control",
                "placeholder" => lang('legal_representative'),
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
        <label for="title" class=" col-md-3"><?php echo lang('project_name'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "title",
                "name" => "title",
                "value" => $model_info->title,
                "class" => "form-control",
                "placeholder" => lang('project_name'),
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
        <label for="industry" class=" col-md-3"><?php echo lang('industry'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_dropdown("industry", $industrias_dropdown, array($model_info->id_industria), "class='select2 validate-hidden', id='industria' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="subindustries_group">
        <div class="form-group">
            <label for="subindustry" class=" col-md-3"><?php echo lang('subindustry'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("subindustry", $subindustry_dropdown, array($model_info->id_tecnologia), "class='select2 validate-hidden', id='id_subindustry' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>  
    </div>
	
    <div class="form-group">
        <label for="subindustry" class=" col-md-3"><?php echo lang('technology'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_dropdown("technology", $tecnologias_dropdown, array($model_info->id_tech), "class='select2 validate-hidden', id='id_tech' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subindustry" class=" col-md-3"><?php echo lang('country'); ?></label>
        <div class="col-md-9">
            <?php
            echo form_dropdown("country", $paises_dropdown, array($model_info->id_pais), "class='select2 validate-hidden', id='country' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>

	<!--
    <div class="form-group">
        <label for="country" class=" col-md-3"><?php echo lang('country'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "country",
                "name" => "country",
                "value" => $model_info->country,
                "class" => "form-control",
                "placeholder" => lang('country'),
				"autocomplete"=> "off",
				"maxlength" => "255"
            ));
            ?>
        </div>
    </div>
    -->
    
    <div class="form-group">
        <label for="city" class=" col-md-3"><?php echo lang('city'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "city",
                "name" => "city",
                "value" => $model_info->city,
                "class" => "form-control",
                "placeholder" => lang('city'),
				"autocomplete"=> "off",
				"maxlength" => "255"
            ));
            ?>
        </div>
    </div>
        
    <div class="form-group">
        <label for="state" class=" col-md-3"><?php echo lang('town'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "state",
                "name" => "state",
                "value" => $model_info->state,
                "class" => "form-control",
                "placeholder" => lang('town'),
				"autocomplete"=> "off",
				"maxlength" => "255"
            ));
            ?>
        </div>
    </div>
	
    <div class="form-group">
        <label for="state" class=" col-md-3"><?php echo lang('environmental_authorization'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "environmental_authorization",
                "name" => "environmental_authorization",
                "value" => $model_info->environmental_authorization,
                "class" => "form-control",
                "placeholder" => lang('environmental_authorization'),
				"autocomplete"=> "off",
				"maxlength" => "255"
            ));
            ?>
        </div>
    </div>
	
     <div class="form-group">
        <label for="start_date" class=" col-md-3"><?php echo lang('start_date'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "start_date",
                "name" => "start_date",
                "value" => $model_info->start_date * 1 ? $model_info->start_date : "",
                "class" => "form-control",
                "placeholder" => lang('start_date'),
				"autocomplete"=> "off",
				//"maxlength" => "255"
            ));
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="deadline" class=" col-md-3"><?php echo lang('deadline'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "deadline",
                "name" => "deadline",
                "value" => $model_info->deadline * 1 ? $model_info->deadline : "",
                "class" => "form-control",
                "placeholder" => lang('deadline'),
                "data-rule-greaterThanOrEqual" => "#start_date",
                "data-msg-greaterThanOrEqual" => lang("end_date_must_be_equal_or_greater_than_start_date"),
				"autocomplete"=> "off",
				//"maxlength" => "255"
            ));
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="initial" class=" col-md-3"><?php echo lang('initial'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "initial",
                "name" => "initial",
                "value" => $model_info->sigla,
                "class" => "form-control",
                "placeholder" => lang('initial'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => lang("field_required"),
                "autocomplete"=> "off",
				"maxlength" => "255"
            ));
            ?>
        </div>
    </div>


    <div id="member_group">
        <?php  
    
            //if($miembros_de_proyecto){
                
                $arraySelected = array();
                $arraySelected2 = array();
                $arrayMiembrosProyecto = array();
                
                foreach($miembros_de_proyecto as $innerArray){
                    $arraySelected[] = $innerArray["id"];
                    $arraySelected2[(string)$innerArray["id"]] = $innerArray["first_name"]." ".$innerArray["last_name"];
                }
                
                foreach($miembros_disponibles as $innerArray){
                    //if(array_search($innerArray["first_name"], $arraySelected2) === FALSE){
					if(array_key_exists($innerArray["id"], $arraySelected2) === FALSE){	
                        $arrayMiembrosProyecto[(string)$innerArray["id"]] = $innerArray["first_name"]." ".$innerArray["last_name"];
                    }
                    
                }
        
                $array_final = $arraySelected2 + $arrayMiembrosProyecto;
                      
                $html = '';
                $html .= '<div class="form-group">';
                    $html .= '<label for="miembros" class="col-md-3">'.lang('member').'</label>';
                    $html .= '<div class="col-md-9">';
                    $html .= form_multiselect("miembros[]", $array_final, $arraySelected, "id='miembros' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='".lang('field_required')."'");
                    $html .= '</div>';
                $html .= '</div>';
                
                echo $html;
            //}
    
        ?>
    </div>

    <div class="form-group">
        <label for="description" class=" col-md-3"><?php echo lang('description'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description,
                "class" => "form-control",
                "placeholder" => lang('description'),
                "style" => "height:150px;",
				"autocomplete"=> "off",
				"maxlength" => "2000"
            ));
            ?>
        </div>
    </div>

    <div class="form-group">
    <label for="icono" class="col-md-3"><?php echo lang("icon"); ?></label>
        <div class="col-md-9">
            <select name="icono" id="icono" class="select2 validate-hidden" >
                <option value="">-</option>
                <?php foreach($iconos as $icono) { ?>
                    <option value="<?php echo $icono ?>" ><?php echo $icono ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="content" class=" col-md-3"><?php echo lang('content'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_textarea(array(
                "id" => "contenido",
                "name" => "contenido",
                "value" => $model_info->contenido,
                "class" => "form-control",
                "placeholder" => lang('content'),
                "style" => "height:150px;",
				"autocomplete" => "off",
            ));
            ?>
        </div>
    </div>

   
    <div class="form-group">
        <label for="status" class=" col-md-3"><?php echo lang('status'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown("status", array("open" => lang("open"), "closed" => lang("closed"), "canceled" => lang("canceled")), array($model_info->status), "class='select2'");
            ?>
        </div>
    </div>

    <hr>
    
    <div style="text-align: center;">
      <h4><?php echo lang("acv_data"); ?></h4>
        </div>
        
        <div class="form-group" style="min-height: 50px">
        	<?php
            	if($is_valor_asignado){
					$tooltip = '<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('block_project_phases_message').'"><i class="fa fa-question-circle"></i></span>';
					$disabled = 'disabled';
				} else {
					$tooltip = '';
					$disabled = '';
				}
			?>
            <label for="fases" class=" col-md-3"><?php echo lang('phase'). " " . $tooltip; ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("fases", $fases_dropdown, array($project_rel_fases->id_fase), "id='fases' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "' " . $disabled . "");
				?>
            </div>
        </div>

    <div id="pu_group">
        <?php
            
            if($model_info->id){
                
                $arraySelected = array();
                $arraySelected2 = array();
                $arrayPUProyecto = array();
                
                foreach($pu_de_proyectos as $innerArray){
                    $arraySelected[] = $innerArray["id"];
                    $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
                }       
                
                foreach($pu_disponibles as $innerArray){
                    if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                        $arrayPUProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
                    } 
                }
                
                $array_final = $arraySelected2 + $arrayPUProyecto;
                
                $html = '';
                $html .= '<div class="form-group">';
                    $html .= '<label for="pu" class="col-md-3">'.lang('unit_processes').'</label>';
                    $html .= '<div class="col-md-9">';
                    //$html .= form_multiselect("pu[]", $array_final, $arraySelected, "id='pu' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
					$html .= form_multiselect("pu[]", $array_final, $arraySelected, "id='pu' class='multiple' multiple='multiple'");
                    $html .= '</div>';
                $html .= '</div>';
                
                echo $html;
            }
    
        ?>
    </div>
    
    <div class="form-group">
        <label for="footprint_format" class=" col-md-3"><?php echo lang('footprint_format'); ?></label>
        <div class="col-md-9">
            <?php
			echo form_dropdown("footprint_format", $footprint_format_dropdown, $model_info->id_formato_huella, "id='id_footprint_format' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
            ?>
        </div>
    </div>
    
    <div id="methodologies_group">
        <div class="form-group" style="min-height: 50px">
            <label for="id_methodology" class=" col-md-3"><?php echo lang('calculation_methodology'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_dropdown("id_methodology", $methodology_dropdown, $model_info->id_metodologia, "id='metodologiaCH' class='select2 validate-hidden' data-rule-required='true', data-msg-required='" . lang('field_required') . "'");
                ?>
            </div>
        </div>
    </div>


    <div id="materials_group">
        <?php  
            if($materiales_disponibles){
                
                $arraySelected = array();
                $arraySelected2 = array();
                $arrayMaterialesProyecto = array();
				$array_materiales_ocupados = array();
                
				
                foreach($materiales_de_proyecto as $innerArray){
					$arraySelected[] = $innerArray->id;
					$arraySelected2[(string)$innerArray->id] = $innerArray->nombre;
                }
                
                foreach($materiales_disponibles as $innerArray){
                    if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                        $arrayMaterialesProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
                    }
                }
				//var_dump($materiales_de_proyecto);
				
        
                $array_final = $arraySelected2 + $arrayMaterialesProyecto;
				
				
				foreach($materiales_deshabilitados as $row){
                    $array_materiales_ocupados[] = $row->id_material;
                }
				
				$info = (count($array_materiales_ocupados) > 0)?'<span class="help" data-container="body" data-toggle="tooltip" title="'.lang('materials_disabled_info').'"><i class="fa fa-question-circle"></i></span>':'';
                
                $html = '';
                $html .= '<div class="form-group">';
                    $html .= '<label for="materiales" class="col-md-3">'.lang('materials').' '.$info.'</label>';
                    $html .= '<div class="col-md-9">';
	//$html .= form_multiselect("materiales[]", $array_final, $arraySelected, "id='materiales' class='multiple' multiple='multiple' data-rule-required='true', data-msg-required='" . lang('field_required') . "'", $array_materiales_ocupados);
	$html .= form_multiselect("materiales[]", $array_final, $arraySelected, "id='materiales' class='multiple' multiple='multiple'", $array_materiales_ocupados);
                    $html .= '</div>';
                $html .= '</div>';
                
                echo $html;
            }
    		
		
        ?>
    </div>

    <div id="footprints_group">
        <?php
            
            //if($huellas_de_proyecto){
            if($model_info->id){
                
                $arraySelected = array();
                $arraySelected2 = array();
                $arrayactividadesProyecto = array();
                
                foreach($huellas_de_proyecto as $innerArray){
                    $arraySelected[] = $innerArray["id"];
                    $arraySelected2[(string)$innerArray["id"]] = $innerArray["nombre"];
                } 
                      
                foreach($huellas_disponibles as $innerArray){
                    if(array_search($innerArray["nombre"], $arraySelected2) === FALSE){
                        $arrayactividadesProyecto[(string)$innerArray["id"]] = $innerArray["nombre"];
                    }
                }
                
                $array_final = $arraySelected2 + $arrayactividadesProyecto;
                
                $html = '';
                $html .= '<div class="form-group">';
                    $html .= '<label for="footprints" class="col-md-3">'.lang('footprints').'</label>';
                    $html .= '<div class="col-md-9">';
					$html .= form_multiselect("footprints[]", $array_final, $arraySelected, "id='footprints' class='multiple' multiple='multiple'");
                    $html .= '</div>';
                $html .= '</div>';
                
                echo $html;
            }
    
        ?>
    </div>
    
</div>

<!-- Dentro de este div se van agregando inputs de tipo hidden con los nombres de los archivos adjuntos en el campo "contenido" -->
<div id="project_content_files">
	<?php 
		if($model_info->id){
			echo $archivos_contenido; 
		}
	?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
		
		/*$('#').summernote({ 
            dialogsInBody: true
        });*/
		
        $("#title").focus();
        $("#project-form .select2").select2();
		
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

        setDatePicker("#start_date, #deadline");
        $('[data-toggle="tooltip"]').tooltip();

        $("#project-form").appForm({
            onSuccess: function (result) {
                if (typeof RELOAD_PROJECT_VIEW_AFTER_UPDATE !== "undefined" && RELOAD_PROJECT_VIEW_AFTER_UPDATE) {
                    location.reload();
                } else {
                    $("#project-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
		
		var max_file_size = <?php echo get_setting("max_file_size"); ?>;
        
        initWYSIWYGEditor("#contenido", {
			height: 200,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['fontname', ['fontname']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				//['table', ['table']],
				//['insert', ['hr', 'picture', 'video']],
				['insert', ['picture']],
				['view', ['codeview']]
			],
			maximumImageFileSize: max_file_size * 1048576, // transformación de B a MB
			callbacks: {
				onImageUpload: function (files, editor, welEditable) {
					var file_size = files[0].size / 1048576;
					if(file_size > max_file_size){
						appAlert.warning("<?php echo lang("maximum_file_size"); ?>" + ": " + max_file_size + "MB", {duration: 10000});
						setTimeout(function() {
							//location.reload();
						}, 500);
					} else {
						move_project_content_file_to_temp(files, editor, welEditable);
					}
				},
				onMediaDelete: function(files, editor, welEditable)
				{
					var image_url = $(files[0]).attr('src');
					var file_name = image_url.substring(image_url.lastIndexOf("/") + 1, image_url.length);
					$('#project_content_files input[data-file_name="' + file_name + '"]').remove();
				},onImageUploadError: function(msg){
				   console.log(msg);
				}
				
			},
			dialogsInBody: true,
			lang: "<?php echo lang('language_locale_long'); ?>"
		});
		
 		function move_project_content_file_to_temp(file, editor, welEditable) {
			
			appLoader.show();
			var data = new FormData();
			
			$.each($('input[name^="files"]').last()[0].files, function(i, file) {
				data.append(i, file);
			});
			
			$.ajax({
				url: '<?php echo_uri("projects/move_project_content_file_to_temp"); ?>',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				type: 'POST',
				success: function(data){
					
					$.each(JSON.parse(data), function(i, item) {
						var path = "<?php echo "/" . get_setting("temp_file_path") . "project_content_files/"; ?>" + item.file_name
						//$('#contenido').summernote("insertImage", path, item.file_name);
						$('#contenido').summernote("insertImage", path, function ($image) {
							$image.css('width', $image.width() / 4);
							appLoader.hide();
							return item.file_name;
						});
						
						$('#project_content_files').append('<input type="hidden" class="project_content_file" name="project_content_files[]" data-file_name="' + item.file_name + '" value="' + item.file_name + '" />');
						
					});
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus+" "+errorThrown);
					appLoader.hide();
				}
			});		
		}
        
		$('#industria').change(function(){
			
			var id_industria = $(this).val();
			select2LoadingStatusOn($('#id_subindustry'));
			
			$.ajax({
                url:  '<?php echo_uri("projects/get_subindustries_of_industry") ?>',
                type:  'post',
                data: {id_industria:id_industria},
                //dataType:'json',
                success: function(respuesta){
                    $('#subindustries_group').html(respuesta);    
                    $("#project-form #id_subindustry").select2();
                }
                
            });
			
		});
        
        $('#miembros').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#miembros option[value="'+value+'"]').remove();
                $('#miembros').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#miembros option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

        $('#fu').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#fu option[value="'+value+'"]').remove();
                $('#fu').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#fu option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

        $('#pu').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#pu option[value="'+value+'"]').remove();
                $('#pu').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#pu option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

        $('#activities').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#activities option[value="'+value+'"]').remove();
                $('#activities').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#activities option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

				
        $('#footprints').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#footprints option[value="'+value+'"]').remove();
                $('#footprints').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#footprints option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

        $('#materiales').multiSelect({
            selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
            selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
            keepOrder: true,
            afterSelect: function(value){
                $('#materiales option[value="'+value+'"]').remove();
                $('#materiales').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
            },
            afterDeselect: function(value){ 
                $('#materiales option[value="'+value+'"]').removeAttr('selected'); 
            }
        });

        $('#clienteCH').change(function(){  
            
            $('#member_group').html("");
            
            var id_cliente = $(this).val(); 
            var id_project = $('#id').val();   
            
            $.ajax({
                url:  '<?php echo_uri("clients/get_users_of_client") ?>',
                type:  'post',
                data: {id_cliente:id_cliente, id_project:id_project},
                //dataType:'json',
                success: function(respuesta){
                    $('#member_group').html(respuesta);
                    $('#miembros').multiSelect({
                        selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
                        selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
                        keepOrder: true,
                        afterSelect: function(value){
                            $('#miembros option[value="'+value+'"]').remove();
                            $('#miembros').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
                        },
                        afterDeselect: function(value){ 

                            $('#miembros option[value="'+value+'"]').removeAttr('selected'); 
                        }
                    });
                }
            });
                    
        });

        $('#fases').change(function(){

            $('#pu_group').html("");
            
            var fases = $(this).val();
            var id_project = $('#id').val();
            
			
			if(fases){
                $.ajax({
                    url:  '<?php echo_uri("projects/get_pu_phase") ?>',
                    type:  'post',
                    data: {fases:fases, id_project:id_project},
                    //dataType:'json',
                    success: function(respuesta){           
        
                        $('#pu_group').html(respuesta);
                        
                        $('#pu').multiSelect({
                                selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
                                selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
                                keepOrder: true,
                                afterSelect: function(value){
                                    $('#pu option[value="'+value+'"]').remove();
                                    $('#pu').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
                                },
                                afterDeselect: function(value){ 
                                    $('#pu option[value="'+value+'"]').removeAttr('selected'); 
                                }
                         }); 
                        
                    }
                });
			}else{
				$('#pu_group').html('');
			}

        });

        $('#id_footprint_format').change(function(){
			
			var id_footprint_format = $(this).val();
			select2LoadingStatusOn($('#metodologiaCH'));
			
			$.ajax({
                url:  '<?php echo_uri("projects/get_methodologies_of_fh") ?>',
                type:  'post',
                data: {id_footprint_format:id_footprint_format},
                //dataType:'json',
                success: function(respuesta){
                    $('#methodologies_group').html(respuesta);    
                    $("#project-form #metodologiaCH").select2();
                }
                
            });
			
		});
		
		$(document).on("change", "#metodologiaCH", function(e) {
        //$('#metodologiaCH').change(function(){  
            
            $('#footprints_group').html("");
            
            var id_metodologia = $(this).val(); 
            var id_project = $('#id').val();   
            
			if(id_metodologia){
				$.ajax({
					url:  '<?php echo_uri("projects/get_footprints_of_meth") ?>',
					type:  'post',
					data: {id_metodologia:id_metodologia, id_project:id_project},
					//dataType:'json',
					success: function(respuesta){
						$('#footprints_group').html(respuesta);
						$('#footprints').multiSelect({
							selectableHeader: "<div class='multiselect-header'>" + "<?php echo lang("available"); ?>" + "</div>",
							selectionHeader: "<div class='multiselect-header'>" + "<?php echo lang("selected"); ?>" + "</div>",
							keepOrder: true,
							afterSelect: function(value){
								$('#footprints option[value="'+value+'"]').remove();
								$('#footprints').append($("<option></option>").attr("value",value).attr('selected', 'selected'));
							},
							afterDeselect: function(value){ 
	
								$('#footprints option[value="'+value+'"]').removeAttr('selected'); 
							}
						});
					}
				});
			}else{
				$('#footprints_group').html('');
			}
                    
        });


        $("#icono").select2().select2("val", '<?php echo $model_info->icono; ?>');
		function format(state){
			var iconos = "";
            if(state.text == "-"){
                iconos = state.text;
            } else {
                iconos = "<img class='' heigth='20' width='20' src='/assets/images/icons/" + state.text + "'/>" + "&nbsp;&nbsp;" + state.text;
            }
			return iconos;
        }
        
        $("#icono").select2({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function(m) { return m; }
        });
		
		$('#ajaxModal').on('hidden.bs.modal', function (event) {
			$('.close').trigger('click');
			//reset($('input[name^="files"]'));
			//console.log($('input[name^="files"]'));
			event.stopImmediatePropagation();
		});
        
    });
</script>