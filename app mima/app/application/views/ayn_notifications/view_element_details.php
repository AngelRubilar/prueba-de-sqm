<?php echo form_open("", array("id" => "view_element_details-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
	
    <?php if(!$deleted_element){ ?>
    
		<?php if($module_level == "general"){ ?>
    		
            <?php if($id_modulo == "1"){ // Ayuda y Soporte ?>
            
				<?php if($id_submodulo == "4" && $event == "send_email"){ // Contacto ?>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->nombre; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('email'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->correo; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('subject'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->asunto; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('content'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->contenido; ?>
                        </div>
                    </div>
            
                <?php } ?>
    		
			<?php } ?>
        
        <?php } ?>
        
        
        
        
        <?php if($module_level == "project"){ ?>
    
        	<?php if($id_modulo == "11"){ // Administración Cliente ?>
				
				<?php if($id_submodulo == "20"){ // Configuración Panel Principal ?>
                    
                        <style>
							#ajaxModal > .modal-dialog {
								width:50% !important;
							}
                        </style>
                        
                        <div class="modal-body clearfix">
                        
                            <ul class="nav nav-tabs classic" role="tablist">
                                <li role="presentation" class="active"><a data-toggle="tab" href="#notif_environmental_footprints"><?php echo lang("environmental_footprints"); ?></a></li>
                                <li role="presentation"><a data-toggle="tab" href="#notif_consumptions"><?php echo lang("consumptions"); ?></a></li>
                                <li role="presentation"><a data-toggle="tab" href="#notif_waste"><?php echo lang("waste"); ?></a></li>
                                <li role="presentation"><a data-toggle="tab" href="#notif_compromises"><?php echo lang("compromises"); ?></a></li>
                                <li role="presentation"><a data-toggle="tab" href="#notif_permittings"><?php echo lang("permittings"); ?></a></li>
                            </ul>
                            
                            <div class="tab-content">
                                <div role="tablist" class="tab-pane fade active in" id="">
                                    <div class="tab-content">
                                        <div id="notif_environmental_footprints" class="tab-pane fade in active">
                                           
                                            <h4><?php echo lang("environmental_footprints"); ?></h4>
                                            <table class="table">
                                                <thead>
                                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                                    <th class="text-center"><?php echo lang("enabled"); ?></th>
                                                </thead>
                                                <tbody>
                                                
                                                    <?php if($client_environmental_footprints_settings) { ?>
                                                
                                                        <?php foreach ($client_environmental_footprints_settings as $index => $setting) { ?> <!-- $index 0, 1, 2 -->
                                                            <tr>
                                                                <td><?php echo lang($setting->informacion); ?></td>
																<?php if($index == 0 ) { ?>
                                                                    <td class="text-center">
                                                                        <?php echo ($setting->habilitado == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                                    </td>
                                                                <?php } ?>
                                                                
                                                                <?php if($index == 1) { ?>
                                                                    <td class="text-center">
                                                                        <?php echo ($setting->habilitado == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                                    </td>
                                                                <?php } ?>
                                                            </tr>
                                                        <?php } ?>
                                                
                                                    <?php } else { ?>
                                                    
                                                        <tr>
                                                            <td><?php echo lang("total_impacts"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo lang("impacts_by_functional_units"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                        <div id="notif_consumptions" class="tab-pane fade">
                                        
                                            <h4><?php echo lang("consumptions"); ?></h4>
                                            <table class="table table-striped">
                                                <thead>
                                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                                    <th class="text-center">
                                                        <label for="chk_consumption_table_all" ><b><?php echo lang('table'); ?></b></label><br>                      
                                                    </th>
                                                    <th class="text-center">
                                                        <label for="chk_consumption_graphic_all" ><b><?php echo lang('graphic'); ?></b></label><br>                   
                                                    </th>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($categorias_proyecto_form_consumo as $cat){?>
                                                        <?php $alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_categoria" => $cat->id_categoria, "deleted" => 0)); ?>
                                                        <tr>
                                                            <td><?php echo $alias_categoria->alias ? $alias_categoria->alias : $cat->nombre; ?></td>
                                                            <td style="text-align: center;">
                                                                <?php echo ($cat->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <?php echo ($cat->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                        <div id="notif_waste" class="tab-pane fade">
                                        
                                            <h4><?php echo lang("waste"); ?></h4>
                                            <table class="table table-striped">
                                                <thead>
                                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                                    <th class="text-center">
                                                        <label for="chk_waste_table_all" ><b><?php echo lang('table'); ?></b></label><br>
                                                    </th>
                                                    <th class="text-center">
                                                        <label for="chk_waste_graphic_all" ><b><?php echo lang('graphic'); ?></b></label><br>
                                                    </th>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($categorias_proyecto_form_residuo as $cat){?>
                                                        <?php $alias_categoria = $this->Categories_alias_model->get_one_where(array("id_cliente" => $this->login_user->client_id, "id_categoria" => $cat->id_categoria, "deleted" => 0)); ?>
                                                    
                                                        <tr>
                                                            <td><?php echo $alias_categoria->alias ? $alias_categoria->alias : $cat->nombre; ?></td>
                                                            <td style="text-align: center;">
                                                                <?php echo ($cat->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <input type="hidden" name="waste_graphic[<?php echo $cat->id_categoria?>]" value="0-<?php echo $cat->id_form?>"/>
                                                                <?php echo ($cat->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                        <div id="notif_compromises" class="tab-pane fade">
                                        
                                            <h4><?php echo lang("compromises"); ?></h4>
                                            
                                            <table class="table">
                                                <thead>
                                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                                    <th class="text-center"><?php echo lang("enabled"); ?></th>
                                                </thead>
                                                <tbody>
                                                    <?php if($client_compromises_settings) { ?>
                                                        <tr>
                                                            <td><?php echo lang("table"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_compromises_settings->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo lang("graphs"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_compromises_settings->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td><?php echo lang("table"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_compromises_settings->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo lang("graphs"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_compromises_settings->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table> 

                                        </div>
                                        <div id="notif_permittings" class="tab-pane fade">
                                        
                                            <h4><?php echo lang("permittings"); ?></h4>
                                            <table class="table">
                                                <thead>
                                                    <th class="text-center"><?php echo lang("info"); ?></th>
                                                    <th class="text-center"><?php echo lang("enabled"); ?></th>
                                                </thead>
                                                <tbody>
                                                    <?php if($client_permitting_settings) { ?>
                                                        <tr>
                                                            <td><?php echo lang("table"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_permitting_settings->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo lang("graphs"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_permitting_settings->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td><?php echo lang("table"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_permitting_settings->tabla == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo lang("graphs"); ?></td>
                                                            <td class="text-center">
                                                                <?php echo ($client_permitting_settings->grafico == 1) ? "<i class='fa fa-check' aria-hidden='true'></i>" : "<i class='fa fa-times' aria-hidden='true'></i>"; ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table> 
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                <?php } ?>
                
			<?php } ?>
        
        <?php } ?>
        
        
        
        
        <?php if($module_level == "admin"){?>
    
        	<?php if($id_modulo == "4"){ // Proyectos ?>
        	
            	<div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('project_name'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->title; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('environmental_authorization'); ?></label>
                    <div class="col-md-9">
                        <?php echo ($element->environmental_authorization) ? $element->environmental_authorization : "-"; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('start_date'); ?></label>
                    <div class="col-md-9">
                        <?php echo ($element->start_date != "0000-00-00") ? $element->start_date : "-"; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('deadline'); ?></label>
                    <div class="col-md-9">
                        <?php echo ($element->deadline != "0000-00-00") ? $element->deadline : "-"; ?>
                    </div>
                </div>
                
                 <?php if(count($miembros_de_proyecto)) { ?>
                    <div class="form-group">
                        <label for="miembros" class="col-md-3"><?php echo lang('members'); ?></label>
                        <div class="col-md-9">
                            <?php 
                                $array_nombres = array();
								foreach($miembros_de_proyecto as $index => $miembro){
									$array_nombres[$index] = $miembro["first_name"]." ".$miembro["last_name"];
								}
                                echo implode(', ', $array_nombres);
                            ?>
                        </div>
                    </div>
                <?php } ?>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('description'); ?></label>
                    <div class="col-md-9">
                        <?php echo ($element->description) ? $element->description : "-"; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('status'); ?></label>
                    <div class="col-md-9">
                        <?php
                        if($element->status == "open"){
                            $status = '<span class="label label-success">'.lang("open").'</span>';
                        }elseif($element->status == "closed"){
                            $status = '<span class="label label-warning">'.lang("closed").'</span>';
                        }elseif($element->status == "canceled"){
                            $status = '<span class="label label-danger">'.lang("canceled").'</span>';
                        }else{
                            $status = '-';
                        }
                        echo $status;
                        ?>
                    </div>
                </div>
                
                <?php if(count($procesos_unitarios)) { ?>
                    <div class="form-group">
                        <label for="pu" class="col-md-3"><?php echo lang('unit_processes'); ?></label>
                        <div class="col-md-9">
                            <?php 
                                $array_nombres = array();
                                foreach($procesos_unitarios as $index => $pu){
                                    $array_nombres[$index] = $pu["nombre"];
                                }
                                $nombres = implode(', ', $array_nombres);
                                echo $nombres;
                            ?>
                        </div>
                    </div>
                <?php } ?>
                
                <?php if($huellas) { ?>
                    <div class="form-group">
                        <label for="pu" class="col-md-3"><?php echo lang('footprints'); ?></label>
                        <div class="col-md-9">
                            <?php
                                $array_nombres = array();
                                $array_icono = array();
                                foreach($huellas as $index => $pu){
                                    $array_nombres[$index] = $pu["nombre"];
                                    $array_icono[$index] = $pu["icono"];
                                    echo '<div class="col-md" style="display:block; float:left; margin-right: 10px; margin-bottom: 5px;"><img heigth="20" width="20" src="/assets/images/impact-category/'.$pu["icono"].' "/> <span class="label label-primary">'.$pu["nombre"].'</span> </div>';
                                }
                            ?>
                        </div>
                    </div>
                <?php } ?>
                
			<?php } ?>
            
            
            <?php if($id_modulo == "7"){ // Unidades Funcionales ?>
            
        		<div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->nombre; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('client'); ?></label>
                    <div class="col-md-9">
                        <?php echo $cliente; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('project'); ?></label>
                    <div class="col-md-9">
                        <?php echo $proyecto; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('subproject'); ?></label>
                    <div class="col-md-9">
                        <?php echo $subproyecto; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('unit'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->unidad; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="name" class="col-md-3"><?php echo lang('value'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->valor; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->created; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
                    <div class="col-md-9">
                        <?php echo ($element->modified) ? $element->modified:'-'; ?>
                    </div>
                </div>
        
            <?php } ?>
            
            
            <?php if($id_modulo == "8"){ // Compromisos ?>
        		
                <div class="form-group">
                    <label for="date_filed" class="col-md-3"><?php echo lang('compromise_number'); ?></label>
                    <div class="col-md-9">
                        <?php echo $element->numero_compromiso; ?>
                    </div>
                </div>
                
				<?php if($event == "comp_rca_add"){ ?>
					
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('name'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->nombre_compromiso; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('phases'); ?></label>
                        <div class="col-md-9">
                            <?php
                                echo $html_fases;
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('reportability'); ?></label>
                        <div class="col-md-9">
                            <?php echo ($element->reportabilidad == 1) ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>'; ?>
                        </div>
                    </div>
                    
				<?php } ?>
                
                <?php if($event == "comp_rep_add"){ ?>
					
                    <div class="form-group">
                        <label for="reportable_matrix_name" class="col-md-3"><?php echo lang('reportable_matrix_name'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->nombre_compromiso; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="considering" class="col-md-3"><?php echo lang('considering'); ?></label>
                        <div class="col-md-9">
                            <?php echo ($element->considerando) ? $element->considerando : '-'; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="condition_or_commitment" class="col-md-3"><?php echo lang('condition_or_commitment'); ?></label>
                        <div class="col-md-9">
                            <?php echo ($element->condicion_o_compromiso) ? $element->condicion_o_compromiso : '-'; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description" class="col-md-3"><?php echo lang('short_description'); ?></label>
                        <div class="col-md-9">
                            <?php echo ($element->descripcion) ? $element->descripcion : '-'; ?>
                        </div>
                    </div>
                    
				<?php } ?>
                
                
                <?php 
				
					if($tipo_matriz == "rca"){
						$id_compromiso = $this->Values_compromises_rca_model->get_one($element->id)->id_compromiso;
						$id_proyecto = $this->Compromises_rca_model->get_one($id_compromiso)->id_proyecto;
					}else{
						$id_compromiso = $this->Values_compromises_reportables_model->get_one($element->id)->id_compromiso;
						$id_proyecto = $this->Compromises_reportables_model->get_one($id_compromiso)->id_proyecto;
					}
										
					//$html = '';
					foreach($campos_compromiso as $campo){

						if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
							
							$html .= '<div class="form-group">';
							$html .= '<div class="col-md-12">';
						
						} else {
							
							$html .= '<div class="form-group">';
							$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
							$html .= '<div class="col-md-9">';
							
						}
							
						$datos_campo = $this->Fields_model->get_one($campo["id_campo"]);
						$id_tipo_campo = $datos_campo->id_tipo_campo;
						$etiqueta = $datos_campo->nombre;
						$name = $datos_campo->html_name;
						$default_value = $datos_campo->default_value;
						
						$opciones = $datos_campo->opciones;
						$array_opciones = json_decode($opciones, true);
						$options = array();
						foreach($array_opciones as $opcion){
							$options[$opcion['value']] = $opcion['text'];
						}
						
						if($tipo_matriz == "rca"){
							$row_elemento = $this->Values_compromises_rca_model->get_details(array("id" => $element->id))->result();
						}else{
							$row_elemento = $this->Values_compromises_reportables_model->get_details(array("id" => $element->id))->result();
						}
													
						$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
						$default_value = $decoded_default[$campo["id_campo"]];
							
						if($id_tipo_campo == 5){
							$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
							$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
							$default_value = $default_value1.' - '.$default_value2;
						}
						
						if($id_tipo_campo == 11){
							$default_value = $datos_campo->default_value;
						}
						
						if($id_tipo_campo == 7){
							$default_value_multiple = (array)$default_value;
						}
						
						//Input text
						if($id_tipo_campo == 1){
							$html .= $default_value;
						}
						
						//Texto Largo
						if($id_tipo_campo == 2){
							$html .= $default_value;
						}
						
						//Número
						if($id_tipo_campo == 3){
							$html .= $default_value;
						}
						
						//Fecha
						if($id_tipo_campo == 4){
							$html .= get_date_format($default_value,$id_proyecto);
						}
						
						//Periodo
						if($id_tipo_campo == 5){
							 $html .= $default_value;
						}
						
						//Selección
						if($id_tipo_campo == 6){
							$html .= $default_value;// es el value, no el text
						}
						
						//Selección Múltiple
						if($id_tipo_campo == 7){
							$html .= implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
						}
						
						//Rut
						if($id_tipo_campo == 8){
							$html .= $default_value;
						}
						
						//Radio Buttons
						if($id_tipo_campo == 9){
							//$html = $value;// es el value, no la etiqueta
							$html .= $default_value;
						}
						
						//Archivo
						if($id_tipo_campo == 10){
							
							if($default_value ){
								
								$html .= '<div class="col-md-8">';
								$html .= $default_value;
								$html .= '</div>';
								
								$html .= '<div class="col-md-4">';
								$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
								$html .= '<tbody><tr><td class="option text-center">';
								$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
								$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
								$html .= '</td>';
								$html .= '</tr>';
								$html .= '</thead>';
								$html .= '</table>';
								$html .= '</div>';
								
							} else {
								
								$html .= '<div class="col-md-8">';
								$html .= '-';
								$html .= '</div>';
							}

						}
						
						//Texto Fijo
						if($id_tipo_campo == 11){
							$html .= $default_value;
						}
						
						//Divisor: Se muestra en la vista
						if($id_tipo_campo == 12){
							$html .= "<hr>";
						}
						
						//Correo
						if($id_tipo_campo == 13){
							$html .= $default_value;
						}
						
						//Hora
						if($id_tipo_campo == 14){
							$html .= convert_to_general_settings_time_format($id_proyecto, $default_value);
						}
						
						///Unidad
						if($id_tipo_campo == 15){
							$simbolo = $array_opciones[0]["symbol"];
							$html .= $default_value?$default_value:"-".' '.$simbolo;
						}
						
						//Selección desde Mantenedora
						if($id_tipo_campo == 16){
							
							$html .= $default_value;
							
						}
						
						if($html == ""){$html .= "-";}
						
						$html .= '</div>';
						$html .= '</div>';

					}
					
					echo $html;
				
				?>
                
                <?php if ($tipo_matriz == "rca"){ ?>
            
                <div class="form-group">
                  <label for="compliance_action_control" class="col-md-3"><?php echo lang('compliance_action_control'); ?></label>
                    <div class=" col-md-9">
                        <?php
                            echo $element->accion_cumplimiento_control;
                        ?>
                    </div>
                </div>
                
                <div class="form-group">
                  <label for="execution_frequency" class="col-md-3"><?php echo lang('execution_frequency'); ?></label>
                    <div class=" col-md-9">
                        <?php
                            echo $element->frecuencia_ejecucion;
                        ?>
                    </div>
                </div>
                
                <?php } ?>
                
                <?php if ($tipo_matriz == "reportable"){ ?>
                <div class="form-group">
                    <label for="planning" class="col-md-3"><?php echo lang('planning'); ?></label>
                    <div class="col-md-9">
                        <?php
                            echo $html_planes;
                        ?>
                    </div>
                </div>
                <?php } ?>
                
                <div class="form-group">
                    <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo $element->created;
                        ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo ($element->modified)?$element->modified:'-';
                        ?>
                    </div>
                </div>
                
			<?php } ?>
            
            
            <?php if($id_modulo == "9"){ // Permisos ?>
            
            	<?php if($event == "permitting_add"){ ?>
                
                    <div class="form-group">
                        <label for="date_filed" class="col-md-3"><?php echo lang('permitting_number'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->numero_permiso; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_filed" class="col-md-3"><?php echo lang('name'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->nombre_permiso; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-md-3"><?php echo lang('phases'); ?></label>
                        <div class="col-md-9">
                            <?php
                                echo $html_fases;
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="entity" class="col-md-3"><?php echo lang('entity'); ?></label>
                        <div class="col-md-9">
                            <?php echo $element->entidad; ?>
                        </div>
                    </div>
                                        
                    <?php 
					
						$id_permiso = $this->Values_permitting_model->get_one($element->id)->id_permiso;
						$id_proyecto = $this->Permitting_model->get_one($id_permiso)->id_proyecto;
                        
                       //$html = '';
                        foreach($campos_permiso as $campo){
							
							if($campo["id_tipo_campo"] == 11 || $campo["id_tipo_campo"] == 12){
								
								$html .= '<div class="form-group">';
								$html .= '<div class="col-md-12">';
							
							} else {
								
								$html .= '<div class="form-group">';
								$html .= '<label for="'.$campo["html_name"].'" class="col-md-3">'.$campo["nombre_campo"].'</label>';
								$html .= '<div class="col-md-9">';
								
							}
							
							$datos_campo = $this->Fields_model->get_one($campo["id_campo"]);
							$id_tipo_campo = $datos_campo->id_tipo_campo;
							$etiqueta = $datos_campo->nombre;
							$name = $datos_campo->html_name;
							$default_value = $datos_campo->default_value;
							
							$opciones = $datos_campo->opciones;
							$array_opciones = json_decode($opciones, true);
							$options = array();
							foreach($array_opciones as $opcion){
								$options[$opcion['value']] = $opcion['text'];
							}
							
							$row_elemento = $this->Values_permitting_model->get_details(array("id" => $element->id))->result();
							$decoded_default = json_decode($row_elemento[0]->datos_campos, true);
							
							$default_value = $decoded_default[$campo["id_campo"]];
							if($id_tipo_campo == 5){
								$default_value1 = $default_value["start_date"]?$default_value["start_date"]:"";
								$default_value2 = $default_value["end_date"]?$default_value["end_date"]:"";
								$default_value = $default_value1.' - '.$default_value2;
							}
							if($id_tipo_campo == 11){
								$default_value = $datos_campo->default_value;
							}
							if($id_tipo_campo == 7){
								$default_value_multiple = (array)$default_value;
							}
							
							
							//Input text
							if($id_tipo_campo == 1){
								$html .= $default_value;
							}
							
							//Texto Largo
							if($id_tipo_campo == 2){
								$html .= $default_value;
							}
							
							//Número
							if($id_tipo_campo == 3){
								$html .= $default_value;
							}
							
							//Fecha
							if($id_tipo_campo == 4){
								$html .= get_date_format($default_value,$id_proyecto);
							}
							
							//Periodo
							if($id_tipo_campo == 5){
								 $html .= $default_value;
							}
							
							//Selección
							if($id_tipo_campo == 6){
								$html .= $default_value;// es el value, no el text
							}
							
							//Selección Múltiple
							if($id_tipo_campo == 7){
								$html .= implode(", ", $default_value_multiple);//siempre es un arreglo, aunque tenga 1
							}
							
							//Rut
							if($id_tipo_campo == 8){
								$html .= $default_value;
							}
							
							//Radio Buttons
							if($id_tipo_campo == 9){
								$html .= $default_value;
								//$html = $value;// es el value, no la etiqueta
							}
							
							//Archivo
							if($id_tipo_campo == 10){
								
								if($default_value ){
									
									$html .= '<div class="col-md-8">';
									$html .= $default_value;
									$html .= '</div>';
									
									$html .= '<div class="col-md-4">';
									$html .= '<table id="table_delete_'.$id_campo.'" class="table_delete"><thead><tr><th></th></tr></thead>';
									$html .= '<tbody><tr><td class="option text-center">';
									$html .= anchor(get_uri("environmental_records/download_file/".$id_elemento."/".$id_campo), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));
									$html .= '<input type="hidden" name="'.$name.'" value="'.$default_value.'" />';				
									$html .= '</td>';
									$html .= '</tr>';
									$html .= '</thead>';
									$html .= '</table>';
									$html .= '</div>';
									
								} else {
									
									$html .= '<div class="col-md-8">';
									$html .= '-';
									$html .= '</div>';
								}
								
								
								
							}
							
							//Texto Fijo
							if($id_tipo_campo == 11){
								$html .= $default_value;
							}
							
							//Divisor: Se muestra en la vista
							if($id_tipo_campo == 12){
								$html .= "<hr>";
							}
							
							//Correo
							if($id_tipo_campo == 13){
								$html .= $default_value;
							}
							
							//Hora
							if($id_tipo_campo == 14){
								$html .= convert_to_general_settings_time_format($id_proyecto, $default_value);
							}
							
							///Unidad
							if($id_tipo_campo == 15){
								$simbolo = $array_opciones[0]["symbol"];
								$html .= $default_value?$default_value:"-".' '.$simbolo;
							}
							
							//Selección desde Mantenedora
							if($id_tipo_campo == 16){
								
								$html .= $default_value;
								
							}
							
							if($html == ""){$html .= "-";}
							

							$html .= '</div>';
							$html .= '</div>';
                 
                        }
                        
                        echo $html;
                    
                    ?>
                    
                    <div class="form-group">
                        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
                        <div class="col-md-9">
                            <?php
                            echo $element->created;
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
                        <div class="col-md-9">
                            <?php
                            echo ($element->modified)?$element->modified:'-';
                            ?>
                        </div>
                    </div>

            	<?php } ?>
			
			<?php } ?>
            
            
		<?php } ?>
        
	
    <?php } else { ?>
    	
        <div class="panel panel-default">
            <div class="">              
                <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                    <?php echo lang('deleted_element_msj'); ?>
                </div>
            </div>	  
        </div>
        
     <?php } ?>
	
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

</script> 