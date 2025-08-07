<div id="page-content" class="p20 clearfix">

    <?php foreach($home_modules_info as $home_module_info) { ?>
        <?php $icono = base_url("assets/images/icons/".$home_module_info->icono); ?>
        	
			<?php if($proyectos_modulo_disponible) { ?>
             
				<?php if($home_module_info->nombre == "Servicios") { ?>
                    <a href="<?php echo get_uri("inicio_projects"); ?>" class="white-link">
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12 col-sm-12 widget-container">
                                <div class="panel panel-sky mb0 shadow-2">
                                    <div class="panel-body menu-item-home">
                                        <div class="col-xs-4 col-sm-4 col-md-2">
                                            <!--<div class="">-->
                                                <span class="avatar avatar-lg">
                                                <img class="menu-item-img-home shadow-2" src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">
                                                </span>
                                            <!--</div>-->
                                        </div>
                                        <div class="col-xs-8 col-sm-8 col-md-3">
                                            <h2><?php echo $home_module_info->nombre; ?></h2>
                                        </div>
                                        <div class="hidden-xs hidden-sm col-md-7">
                                            <p style="text-align:justify;"><?php echo $home_module_info->descripcion; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            
			<?php } ?>
            
            <?php if($kpi_modulo_disponible) { ?>
            
                <?php if($home_module_info->nombre == "Indicadores de Sostenibilidad (KPI)") { ?>
                
                	<?php if($puede_ver_reporte_kpi == 3 && $puede_ver_graf_por_proyecto == 3
							&& $puede_ver_graf_entre_proyectos == 3){ ?>

                     <?php } else { ?>
                     	
                        	<?php 
                                $uri = "";
                                if($puede_ver_reporte_kpi != 3) {
                                    $uri = get_uri("KPI_Report");
                                } 
                                else if($puede_ver_graf_por_proyecto != 3) {
                                    $uri = get_uri("KPI_Charts_by_project");
                                } 
                                else if($puede_ver_graf_entre_proyectos != 3) {
                                    $uri = get_uri("KPI_Charts_between_projects");
                                }
                            ?>  
                            
                            <a href="<?php echo $uri; ?>" class="white-link">
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-md-12 col-sm-12 widget-container">
                                        <div class="panel panel-sky mb0 shadow-2">
                                            <div class="panel-body menu-item-home">
                                                <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                                                    <div class="">
                                                        <span class="avatar avatar-lg">
                                                        <img class="menu-item-img-home shadow-2" src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-xs-8 col-sm-8 col-md-4 col-lg-3">
                                                    <h2><?php echo $home_module_info->nombre; ?></h2>
                                                </div>
                                                <div class="hidden-xs hidden-sm col-md-6 col-lg-7">
                                                    <p style="text-align:justify;"><?php echo $home_module_info->descripcion; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        
                     <?php } ?>
                     
                <?php } ?>
            
            <?php } ?>
            
            
            
            <?php if($economia_circular_modulo_disponible) { ?>
            	
                <?php if($home_module_info->nombre == "Economía Circular") { ?>
				
                	<?php if($puede_ver_ec_ind_por_proyecto == 3 && $puede_ver_ec_ind_entre_proyectos == 3){ ?>

                    <?php } else { ?>
                	
						<?php 
                            $uri = "";
                            if($puede_ver_ec_ind_por_proyecto != 3) {
                                $uri = get_uri("EC_Indicators_by_project");
                            } 
                            else if($puede_ver_ec_ind_entre_proyectos != 3) {
                                $uri = get_uri("EC_Indicators_between_projects");
                            }
                        ?>
                        
                        <a href="<?php echo $uri; ?>" class="white-link">
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-12 col-sm-12 widget-container">
                                    <div class="panel panel-sky mb0 shadow-2">
                                        <div class="panel-body menu-item-home">
                                            <div class="col-xs-4 col-sm-4 col-md-2">
                                                <!--<div class="">-->
                                                    <span class="avatar avatar-lg">
                                                    <img class="menu-item-img-home shadow-2" src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">
                                                    </span>
                                                <!--</div>-->
                                            </div>
                                            <div class="col-xs-8 col-sm-8 col-md-3">
                                                <h2><?php echo $home_module_info->nombre; ?></h2>
                                            </div>
                                            <div class="hidden-xs hidden-sm col-md-7">
                                                <p style="text-align:justify;"><?php echo $home_module_info->descripcion; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                	<?php } ?>
                    
                <?php } ?>
                
            <?php } ?>
            
			
            
            <?php if($ayuda_soporte_modulo_disponible) { ?>
	
				<?php if($home_module_info->nombre == "Ayuda y Soporte") { ?>
	
					<?php if($puede_ver_ayuda_soporte_faq == 3 &&
							$puede_ver_ayuda_soporte_glossary == 3 &&
							$puede_ver_ayuda_soporte_what_is_mimasoft == 3 &&
							$puede_ver_ayuda_soporte_contact == 3) { ?>
	
					<?php } else { ?>
	
							<?php 
                                $uri = "";
                                if($puede_ver_ayuda_soporte_what_is_mimasoft != 3) {
                                    $uri = get_uri("What_is_mimasoft");
                                } 
                                else if($puede_ver_ayuda_soporte_faq != 3) {
                                    $uri = get_uri("Faq");
                                } 
                                else if($puede_ver_ayuda_soporte_glossary != 3) {
                                    $uri = get_uri("Wiki");
                                }
								else if($puede_ver_ayuda_soporte_contact != 3) {
                                    $uri = get_uri("Contact");
                                }
                            ?> 
	
<!--						<a href="<?php echo get_uri("what_is_mimasoft"); ?>" class="white-link">-->
							<a href="<?php echo $uri; ?>" class="white-link">
							<div class="row" style="margin-bottom: 20px;">
								<div class="col-md-12 col-sm-12 widget-container">
									<div class="panel panel-sky mb0 shadow-2">
										<div class="panel-body menu-item-home">
											<div class="col-xs-4 col-sm-4 col-md-2">
												<div class="">
													<span class="avatar avatar-lg">
													<img class="menu-item-img-home shadow-2" src="<?php echo $icono; ?>" alt="..." style="background-color:#FFF;" class="mCS_img_loaded">
													</span>
												</div>
											</div>
											<div class="col-xs-8 col-sm-8 col-md-3">
												<h2><?php echo $home_module_info->nombre; ?></h2>
											</div>
											<div class="hidden-xs hidden-sm col-md-7">
												<p style="text-align:justify;"><?php echo $home_module_info->descripcion; ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</a>
	
					<?php } ?>
                    
                <?php } ?>
			<?php } ?>
            
    <?php } ?>
    
    <?php if(!$proyectos_modulo_disponible && !$acuerdos_territorio_modulo_disponible && !$acuerdos_distribucion_modulo_disponible
			&& !$recordbook_modulo_disponible && !$kpi_modulo_disponible && !$ayuda_soporte_modulo_disponible) { ?>  
        
            <div class="panel panel-default mb15">
                <div class="panel-body menu-item-home">              
                    <div class="app-alert alert alert-warning alert-dismissible mb0" style="float: left;">
                        <?php echo lang("no_modules_availables"); ?>
                    </div>
                </div>	  
            </div>
	
			  <?php echo form_open(get_uri("home/save"), array("id" => "contact-form", "class" => "general-form", "role" => "form")); ?>

				<div class="panel col-md-12">
				  <div class="panel-default panel-heading">
					<h4 style="font-size:16px"><?php echo lang('contact'); ?></h4>
				  </div>
				  <div class="panel-body">
					<!--<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />-->
					<input type="hidden" name="contact" value="<?php echo $client_info->contact; ?>" />

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

    <?php } ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#contact-form").appForm({
            isModal: false,
            onSuccess: function (res) {
				appAlert.success(res.message, {duration: 10000});  
                document.getElementById('asunto').value = "";
				document.getElementById('contenido').value = "";
            }
        });
    });
</script>