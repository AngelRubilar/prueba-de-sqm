<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <label for="id_cliente" class="<?php echo $label_column; ?>"><?php echo lang('client'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo $model_info->nombre_cliente;
		?>
    </div>
</div>

<div class="form-group">
    <label for="id_fase" class="<?php echo $label_column; ?>"><?php echo lang('phase'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
			echo $model_info->nombre_fase;
		?>
    </div>
</div>

<div class="form-group">
    <label for="id_proyecto" class="<?php echo $label_column; ?>"><?php echo lang('project'); ?></label>
    <div class="<?php echo $field_column; ?>">
        <?php
        	echo $model_info->nombre_proyecto;
        ?>
    </div>
</div>

<!-- Fase Construcción -->
<?php if($model_info->id_fase == 2) { ?>

    <div class="form-group">
        <label for="sitio_constru_considerado" class="<?php echo $label_column; ?>"><?php echo lang('construction_sites_considered') . " (" . $datos["construction_sites_considered"]["nombre_tipo_unidad"]. ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("sitio_constru_considerado", $valores_unidad_fija, array($datos["construction_sites_considered"]["valor"]), "id='sitio_constru_considerado' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_electr_red" class="<?php echo $label_column; ?>"><?php echo lang('network_electricity_consumption') . " (" . $datos["network_electricity_consumption"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("consu_electr_red", $valores_mwh, array($datos["network_electricity_consumption"]["valor"]), "id='consu_electr_red' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_electr_fuente_renov" class="<?php echo $label_column; ?>"><?php echo lang('electricity_consumption_renewable_source') . " (" . $datos["electricity_consumption_renewable_source"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("consu_electr_fuente_renov", $valores_mwh, array($datos["electricity_consumption_renewable_source"]["valor"]), "id='consu_electr_fuente_renov' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_electr_diesel" class="<?php echo $label_column; ?>"><?php echo lang('electricity_consumption_diesel') . " (" . $datos["electricity_consumption_diesel"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("consu_electr_diesel", $valores_mwh, array($datos["electricity_consumption_diesel"]["valor"]), "id='consu_electr_diesel' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="petroleo" class="<?php echo $label_column; ?>"><?php echo lang('petroleum') . " (" . $datos["petroleum"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("petroleo", $valores_t, array($datos["petroleum"]["valor"]), "id='petroleo' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="gasolina" class="<?php echo $label_column; ?>"><?php echo lang('gasoline') . " (" . $datos["gasoline"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("gasolina", $valores_t, array($datos["gasoline"]["valor"]), "id='gasolina' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="glp" class="<?php echo $label_column; ?>"><?php echo lang('glp') . " (" . $datos["glp"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("glp", $valores_t, array($datos["glp"]["valor"]), "id='glp' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="gas_natural" class="<?php echo $label_column; ?>"><?php echo lang('natural_gas') . " (" . $datos["natural_gas"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("gas_natural", $valores_m3, array($datos["natural_gas"]["valor"]), "id='gas_natural' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="biodiesel_alcohol" class="<?php echo $label_column; ?>"><?php echo lang('biodiesel') . " (" . $datos["biodiesel"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("biodiesel_alcohol", $valores_t, array($datos["biodiesel"]["valor"]), "id='biodiesel_alcohol' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="concreto" class="<?php echo $label_column; ?>"><?php echo lang('concrete') . " (" . $datos["concrete"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("concreto", $valores_t, array($datos["concrete"]["valor"]), "id='concreto' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="concreto_agregados_reciclados" class="<?php echo $label_column; ?>"><?php echo lang('recycled_aggregates_concrete') . " (" . $datos["recycled_aggregates_concrete"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("concreto_agregados_reciclados", $valores_t, array($datos["recycled_aggregates_concrete"]["valor"]), "id='concreto_agregados_reciclados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="arena_grava_constr" class="<?php echo $label_column; ?>"><?php echo lang('sand_gravel_construction') . " (" . $datos["sand_gravel_construction"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("arena_grava_constr", $valores_t, array($datos["sand_gravel_construction"]["valor"]), "id='arena_grava_constr' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="estr_acero_tuberias" class="<?php echo $label_column; ?>"><?php echo lang('structures_steel_pipes') . " (" . $datos["structures_steel_pipes"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("estr_acero_tuberias", $valores_t, array($datos["structures_steel_pipes"]["valor"]), "id='estr_acero_tuberias' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="barras_refuerzo_hormigon" class="<?php echo $label_column; ?>"><?php echo lang('reinforcement_bars_concrete') . " (" . $datos["reinforcement_bars_concrete"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("barras_refuerzo_hormigon", $valores_t, array($datos["reinforcement_bars_concrete"]["valor"]), "id='barras_refuerzo_hormigon' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="hierro_sostenible" class="<?php echo $label_column; ?>"><?php echo lang('sustainable_iron') . " (" . $datos["sustainable_iron"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("hierro_sostenible", $valores_t, array($datos["sustainable_iron"]["valor"]), "id='hierro_sostenible' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="cemento_cal" class="<?php echo $label_column; ?>"><?php echo lang('cement_lime') . " (" . $datos["cement_lime"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("cemento_cal", $valores_t, array($datos["cement_lime"]["valor"]), "id='cemento_cal' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_biodeg" class="<?php echo $label_column; ?>"><?php echo lang('biodegradable_oil') . " (" . $datos["biodegradable_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_biodeg", $valores_t, array($datos["biodegradable_oil"]["valor"]), "id='aceite_biodeg' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_no_biodeg" class="<?php echo $label_column; ?>"><?php echo lang('no_biodegradable_oil') . " (" . $datos["no_biodegradable_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_no_biodeg", $valores_t, array($datos["no_biodegradable_oil"]["valor"]), "id='aceite_no_biodeg' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_dielectrico" class="<?php echo $label_column; ?>"><?php echo lang('dielectric_oil') . " (" . $datos["dielectric_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_dielectrico", $valores_t, array($datos["dielectric_oil"]["valor"]), "id='aceite_dielectrico' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="otro_aceite" class="<?php echo $label_column; ?>"><?php echo lang('other_oil') . " (" . $datos["other_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("otro_aceite", $valores_t, array($datos["other_oil"]["valor"]), "id='otro_aceite' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="suelo_excavado" class="<?php echo $label_column; ?>"><?php echo lang('excavated_ground') . " (" . $datos["excavated_ground"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("suelo_excavado", $valores_m3, array($datos["excavated_ground"]["valor"]), "id='suelo_excavado' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="suelo_reutilizado" class="<?php echo $label_column; ?>"><?php echo lang('reused_ground') . " (" . $datos["reused_ground"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("suelo_reutilizado", $valores_m3, array($datos["reused_ground"]["valor"]), "id='suelo_reutilizado' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="reutilizado_obra" class="<?php echo $label_column; ?>"><?php echo lang('of_which_reused_on_site') . " (" . $datos["of_which_reused_on_site"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("reutilizado_obra", $valores_m3, array($datos["of_which_reused_on_site"]["valor"]), "id='reutilizado_obra' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="suelo_contaminado_rehab" class="<?php echo $label_column; ?>"><?php echo lang('of_which_contaminated_ground_rehab') . " (" . $datos["of_which_contaminated_ground_rehab"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("suelo_contaminado_rehab", $valores_m3, array($datos["of_which_contaminated_ground_rehab"]["valor"]), "id='suelo_contaminado_rehab' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="hormigon_ladrillo_mortero" class="<?php echo $label_column; ?>"><?php echo lang('concrete_bricks_mortar') . " (" . $datos["concrete_bricks_mortar"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("hormigon_ladrillo_mortero", $valores_m3, array($datos["concrete_bricks_mortar"]["valor"]), "id='hormigon_ladrillo_mortero' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="agregados_demolicion" class="<?php echo $label_column; ?>"><?php echo lang('aggregates_demolition') . " (" . $datos["aggregates_demolition"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("agregados_demolicion", $valores_m3, array($datos["aggregates_demolition"]["valor"]), "id='agregados_demolicion' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="demolicion_estructuras" class="<?php echo $label_column; ?>"><?php echo lang('structures_demolition') . " (" . $datos["structures_demolition"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("demolicion_estructuras", $valores_t, array($datos["structures_demolition"]["valor"]), "id='demolicion_estructuras' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_pot" class="<?php echo $label_column; ?>"><?php echo lang('ui_drinking_water') . " (" . $datos["ui_drinking_water"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_pot", $valores_m3, array($datos["ui_drinking_water"]["valor"]), "id='ui_agua_pot' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_no_pot_superf" class="<?php echo $label_column; ?>"><?php echo lang('ui_non_potable_water_surface') . " (" . $datos["ui_non_potable_water_surface"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_no_pot_superf", $valores_m3, array($datos["ui_non_potable_water_surface"]["valor"]), "id='ui_agua_no_pot_superf' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_no_pot_pozos" class="<?php echo $label_column; ?>"><?php echo lang('ui_non_potable_water_well') . " (" . $datos["ui_non_potable_water_well"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_no_pot_pozos", $valores_m3, array($datos["ui_non_potable_water_well"]["valor"]), "id='ui_agua_no_pot_pozos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_no_pot_lluvia" class="<?php echo $label_column; ?>"><?php echo lang('ui_non_potable_water_rain') . " (" . $datos["ui_non_potable_water_rain"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_no_pot_lluvia", $valores_m3, array($datos["ui_non_potable_water_rain"]["valor"]), "id='ui_agua_no_pot_lluvia' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_no_pot_planta_ext" class="<?php echo $label_column; ?>"><?php echo lang('ui_non_potable_water_plants_ext') . " (" . $datos["ui_non_potable_water_plants_ext"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_no_pot_planta_ext", $valores_m3, array($datos["ui_non_potable_water_plants_ext"]["valor"]), "id='ui_agua_no_pot_planta_ext' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="ui_agua_no_pot_planta_sitio" class="<?php echo $label_column; ?>"><?php echo lang('ui_non_potable_water_plants_site') . " (" . $datos["ui_non_potable_water_plants_site"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("ui_agua_no_pot_planta_sitio", $valores_m3, array($datos["ui_non_potable_water_plants_site"]["valor"]), "id='ui_agua_no_pot_planta_sitio' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_pot" class="<?php echo $label_column; ?>"><?php echo lang('uc_drinking_water') . " (" . $datos["uc_drinking_water"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_pot", $valores_m3, array($datos["uc_drinking_water"]["valor"]), "id='uc_agua_pot' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_no_pot_superf" class="<?php echo $label_column; ?>"><?php echo lang('uc_non_potable_water_surface') . " (" . $datos["uc_non_potable_water_surface"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_no_pot_superf", $valores_m3, array($datos["uc_non_potable_water_surface"]["valor"]), "id='uc_agua_no_pot_superf' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_no_pot_pozos" class="<?php echo $label_column; ?>"><?php echo lang('uc_non_potable_water_well') . " (" . $datos["uc_non_potable_water_well"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_no_pot_pozos", $valores_m3, array($datos["uc_non_potable_water_well"]["valor"]), "id='uc_agua_no_pot_pozos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_no_pot_lluvia" class="<?php echo $label_column; ?>"><?php echo lang('uc_non_potable_water_rain') . " (" . $datos["uc_non_potable_water_rain"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_no_pot_lluvia", $valores_m3, array($datos["uc_non_potable_water_rain"]["valor"]), "id='uc_agua_no_pot_lluvia' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_no_pot_planta_ext" class="<?php echo $label_column; ?>"><?php echo lang('uc_non_potable_water_plants_ext') . " (" . $datos["uc_non_potable_water_plants_ext"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_no_pot_planta_ext", $valores_m3, array($datos["uc_non_potable_water_plants_ext"]["valor"]), "id='uc_agua_no_pot_planta_ext' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="uc_agua_no_pot_planta_sitio" class="<?php echo $label_column; ?>"><?php echo lang('uc_non_potable_water_plants_site') . " (" . $datos["uc_non_potable_water_plants_site"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("uc_agua_no_pot_planta_sitio", $valores_m3, array($datos["uc_non_potable_water_plants_site"]["valor"]), "id='uc_agua_no_pot_planta_sitio' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="derrames_accidentales" class="<?php echo $label_column; ?>"><?php echo lang('accidental_spills') . " (" . $datos["accidental_spills"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("derrames_accidentales", $valores_m3, array($datos["accidental_spills"]["valor"]), "id='derrames_accidentales' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="eventos_significativos" class="<?php echo $label_column; ?>"><?php echo lang('significant_events') . " (" . $datos["significant_events"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("eventos_significativos", $valores_unidad_fija, array($datos["significant_events"]["valor"]), "id='eventos_significativos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="np_produccion_residuos" class="<?php echo $label_column; ?>"><?php echo lang('np_waste_production') . " (" . $datos["np_waste_production"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("np_produccion_residuos", $valores_t, array($datos["np_waste_production"]["valor"]), "id='np_produccion_residuos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="np_residuos_reciclaje" class="<?php echo $label_column; ?>"><?php echo lang('np_waste_recycling') . " (" . $datos["np_waste_recycling"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("np_residuos_reciclaje", $valores_t, array($datos["np_waste_recycling"]["valor"]), "id='np_residuos_reciclaje' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="np_desechos_reutilizados" class="<?php echo $label_column; ?>"><?php echo lang('np_reused_waste') . " (" . $datos["np_reused_waste"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("np_desechos_reutilizados", $valores_t, array($datos["np_reused_waste"]["valor"]), "id='np_desechos_reutilizados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="p_prod_residuos" class="<?php echo $label_column; ?>"><?php echo lang('p_waste_production') . " (" . $datos["p_waste_production"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("p_prod_residuos", $valores_t, array($datos["p_waste_production"]["valor"]), "id='p_prod_residuos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="p_residuos_transferidos" class="<?php echo $label_column; ?>"><?php echo lang('p_waste_recycling') . " (" . $datos["p_waste_recycling"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("p_residuos_transferidos", $valores_t, array($datos["p_waste_recycling"]["valor"]), "id='p_residuos_transferidos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="p_desechos_reutilizados" class="<?php echo $label_column; ?>"><?php echo lang('p_reused_waste') . " (" . $datos["p_reused_waste"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("p_desechos_reutilizados", $valores_t, array($datos["p_reused_waste"]["valor"]), "id='p_desechos_reutilizados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="superficie_ocupada_constr" class="<?php echo $label_column; ?>"><?php echo lang('occupied_surface_construction') . " (" . $datos["occupied_surface_construction"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// ha | id: 14
            echo form_dropdown("superficie_ocupada_constr", $valores_ha, array($datos["occupied_surface_construction"]["valor"]), "id='superficie_ocupada_constr' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="total_co2_offset" class="<?php echo $label_column; ?>"><?php echo lang('total_co2_offset') . " (" . $datos["total_co2_offset"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("total_co2_offset", $valores_t, array($datos["total_co2_offset"]["valor"]), "id='total_co2_offset' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_proyectos_biodiversidad" class="<?php echo $label_column; ?>"><?php echo lang('n_biodiversity_projects') . " (" . $datos["n_biodiversity_projects"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_proyectos_biodiversidad", $valores_unidad_fija, array($datos["n_biodiversity_projects"]["valor"]), "id='n_proyectos_biodiversidad' class='select2'");
            ?>
        </div>
    </div>
    
<?php } ?>
<!-- FIN la fase es Construcción -->



<!-- Fase Operación y Mantenimiento -->
<?php if($model_info->id_fase == 3) { ?>

	<div class="form-group">
        <label for="capacidad_instalada" class="<?php echo $label_column; ?>"><?php echo lang('installed_capacity') . " (" . $datos["installed_capacity"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MW | id: 19
            echo form_dropdown("capacidad_instalada", $valores_mw, array($datos["installed_capacity"]["valor"]), "id='capacidad_instalada' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_gen_turbina_eolica" class="<?php echo $label_column; ?>"><?php echo lang('n_gen_wind_turbine') . " (" . $datos["n_gen_wind_turbine"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_gen_turbina_eolica", $valores_unidad_fija, array($datos["n_gen_wind_turbine"]["valor"]), "id='n_gen_turbina_eolica' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="superficie_ocupada" class="<?php echo $label_column; ?>"><?php echo lang('occupied_surface') . " (" . $datos["occupied_surface"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// ha | id: 14
            echo form_dropdown("superficie_ocupada", $valores_ha, array($datos["occupied_surface"]["valor"]), "id='superficie_ocupada' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="horas_funcionamiento" class="<?php echo $label_column; ?>"><?php echo lang('operating_hours') . " (" . $datos["operating_hours"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// hrs | id: 17
            echo form_dropdown("horas_funcionamiento", $valores_hrs, array($datos["operating_hours"]["valor"]), "id='horas_funcionamiento' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_electr_red" class="<?php echo $label_column; ?>"><?php echo lang('network_electricity_consumption') . " (" . $datos["network_electricity_consumption"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("consu_electr_red", $valores_mwh, array($datos["network_electricity_consumption"]["valor"]), "id='consu_electr_red' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="autoconsu_electr" class="<?php echo $label_column; ?>"><?php echo lang('electricity_autoconsumption') . " (" . $datos["electricity_autoconsumption"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("autoconsu_electr", $valores_mwh, array($datos["electricity_autoconsumption"]["valor"]), "id='autoconsu_electr' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_electr_diesel" class="<?php echo $label_column; ?>"><?php echo lang('electricity_consumption_from_diesel') . " (" . $datos["electricity_consumption_from_diesel"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// MWh | id: 21
            echo form_dropdown("consu_electr_diesel", $valores_mwh, array($datos["electricity_consumption_from_diesel"]["valor"]), "id='consu_electr_diesel' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="petroleo_diesel" class="<?php echo $label_column; ?>"><?php echo lang('petroleum_diesel') . " (" . $datos["petroleum_diesel"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("petroleo_diesel", $valores_t, array($datos["petroleum_diesel"]["valor"]), "id='petroleo_diesel' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="gasolina" class="<?php echo $label_column; ?>"><?php echo lang('gasoline') . " (" . $datos["gasoline"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("gasolina", $valores_t, array($datos["gasoline"]["valor"]), "id='gasolina' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="glp" class="<?php echo $label_column; ?>"><?php echo lang('glp') . " (" . $datos["glp"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("glp", $valores_t, array($datos["glp"]["valor"]), "id='glp' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="gas_natural" class="<?php echo $label_column; ?>"><?php echo lang('natural_gas') . " (" . $datos["natural_gas"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("gas_natural", $valores_m3, array($datos["natural_gas"]["valor"]), "id='gas_natural' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="biodiesel_alcohol" class="<?php echo $label_column; ?>"><?php echo lang('biodiesel_alcohol') . " (" . $datos["biodiesel_alcohol"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("biodiesel_alcohol", $valores_t, array($datos["biodiesel_alcohol"]["valor"]), "id='biodiesel_alcohol' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="sf6_presente_en_planta" class="<?php echo $label_column; ?>"><?php echo lang('sf6_present_on_plant') . " (" . $datos["sf6_present_on_plant"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// l | id: 4
            echo form_dropdown("sf6_presente_en_planta", $valores_l, array($datos["sf6_present_on_plant"]["valor"]), "id='sf6_presente_en_planta' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_biodeg" class="<?php echo $label_column; ?>"><?php echo lang('biodegradable_oil') . " (" . $datos["biodegradable_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_biodeg", $valores_t, array($datos["biodegradable_oil"]["valor"]), "id='aceite_biodeg' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_no_biodeg" class="<?php echo $label_column; ?>"><?php echo lang('no_biodegradable_oil') . " (" . $datos["no_biodegradable_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_no_biodeg", $valores_t, array($datos["no_biodegradable_oil"]["valor"]), "id='aceite_no_biodeg' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_dielectrico" class="<?php echo $label_column; ?>"><?php echo lang('dielectric_oil') . " (" . $datos["dielectric_oil"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_dielectrico", $valores_t, array($datos["dielectric_oil"]["valor"]), "id='aceite_dielectrico' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="aceite_con_pcb" class="<?php echo $label_column; ?>"><?php echo lang('oil_containing_pcb') . " (" . $datos["oil_containing_pcb"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("aceite_con_pcb", $valores_t, array($datos["oil_containing_pcb"]["valor"]), "id='aceite_con_pcb' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="otros_aceites_no_biodeg" class="<?php echo $label_column; ?>"><?php echo lang('others_no_biodegradable_oils') . " (" . $datos["others_no_biodegradable_oils"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("otros_aceites_no_biodeg", $valores_t, array($datos["others_no_biodegradable_oils"]["valor"]), "id='otros_aceites_no_biodeg' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="dism_aceite_buenas_practicas" class="<?php echo $label_column; ?>"><?php echo lang('decrease_oil_good_practices') . " (" . $datos["decrease_oil_good_practices"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// l | id: 4
            echo form_dropdown("dism_aceite_buenas_practicas", $valores_l, array($datos["decrease_oil_good_practices"]["valor"]), "id='dism_aceite_buenas_practicas' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="np_produccion_residuos" class="<?php echo $label_column; ?>"><?php echo lang('waste_production_np') . " (" . $datos["waste_production_np"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("np_produccion_residuos", $valores_t, array($datos["waste_production_np"]["valor"]), "id='np_produccion_residuos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="np_residuos_reciclados" class="<?php echo $label_column; ?>"><?php echo lang('np_recycled_waste') . " (" . $datos["np_recycled_waste"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("np_residuos_reciclados", $valores_t, array($datos["np_recycled_waste"]["valor"]), "id='np_residuos_reciclados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="prod_residuos_peligrosos" class="<?php echo $label_column; ?>"><?php echo lang('dangerous_waste_production') . " (" . $datos["dangerous_waste_production"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("prod_residuos_peligrosos", $valores_t, array($datos["dangerous_waste_production"]["valor"]), "id='prod_residuos_peligrosos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="residuos_peligrosos_reciclados" class="<?php echo $label_column; ?>"><?php echo lang('dangerous_waste_recycled') . " (" . $datos["dangerous_waste_recycled"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// T | id: 1
            echo form_dropdown("residuos_peligrosos_reciclados", $valores_t, array($datos["dangerous_waste_recycled"]["valor"]), "id='residuos_peligrosos_reciclados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_rios" class="<?php echo $label_column; ?>"><?php echo lang('drinking_water_consumption_river') . " (" . $datos["drinking_water_consumption_river"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_rios", $valores_m3, array($datos["drinking_water_consumption_river"]["valor"]), "id='consu_agua_pot_rios' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_pozos" class="<?php echo $label_column; ?>"><?php echo lang('drinking_water_consumption_well') . " (" . $datos["drinking_water_consumption_well"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_pozos", $valores_m3, array($datos["drinking_water_consumption_well"]["valor"]), "id='consu_agua_pot_pozos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_plantas" class="<?php echo $label_column; ?>"><?php echo lang('drinking_water_consumption_plants') . " (" . $datos["drinking_water_consumption_plants"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_plantas", $valores_m3, array($datos["drinking_water_consumption_plants"]["valor"]), "id='consu_agua_pot_plantas' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_depura" class="<?php echo $label_column; ?>"><?php echo lang('drinking_water_consumption_wastewater_treatment_plant') . " (" . $datos["drinking_water_consumption_wastewater_treatment_plant"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_depura", $valores_m3, array($datos["drinking_water_consumption_wastewater_treatment_plant"]["valor"]), "id='consu_agua_pot_depura' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_lluvia" class="<?php echo $label_column; ?>"><?php echo lang('drinking_water_consumption_system_harvest') . " (" . $datos["drinking_water_consumption_system_harvest"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_lluvia", $valores_m3, array($datos["drinking_water_consumption_system_harvest"]["valor"]), "id='consu_agua_pot_lluvia' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_pot_rios" class="<?php echo $label_column; ?>"><?php echo lang('non_potable_water_consumption_river') . " (" . $datos["non_potable_water_consumption_river"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_pot_rios", $valores_m3, array($datos["non_potable_water_consumption_river"]["valor"]), "id='consu_agua_pot_rios' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_no_pot_pozo" class="<?php echo $label_column; ?>"><?php echo lang('non_potable_water_consumption_well') . " (" . $datos["non_potable_water_consumption_well"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_no_pot_pozo", $valores_m3, array($datos["non_potable_water_consumption_well"]["valor"]), "id='consu_agua_no_pot_pozo' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_no_pot_planta_agua" class="<?php echo $label_column; ?>"><?php echo lang('non_potable_water_consumption_plants_water') . " (" . $datos["non_potable_water_consumption_plants_water"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_no_pot_planta_agua", $valores_m3, array($datos["non_potable_water_consumption_plants_water"]["valor"]), "id='consu_agua_no_pot_planta_agua' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_no_pot_planta_agua_res" class="<?php echo $label_column; ?>"><?php echo lang('non_potable_water_consumption_plants_res_water') . " (" . $datos["non_potable_water_consumption_plants_res_water"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_no_pot_planta_agua_res", $valores_m3, array($datos["non_potable_water_consumption_plants_res_water"]["valor"]), "id='consu_agua_no_pot_planta_agua_res' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="consu_agua_no_pot_lluvia" class="<?php echo $label_column; ?>"><?php echo lang('non_potable_water_consumption_system_harvest') . " (" . $datos["non_potable_water_consumption_system_harvest"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// m3 | id: 3
            echo form_dropdown("consu_agua_no_pot_lluvia", $valores_m3, array($datos["non_potable_water_consumption_system_harvest"]["valor"]), "id='consu_agua_no_pot_lluvia' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_proyectos_biodiversidad" class="<?php echo $label_column; ?>"><?php echo lang('n_biodiversity_projects') . " (" . $datos["n_biodiversity_projects"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_proyectos_biodiversidad", $valores_unidad_fija, array($datos["n_biodiversity_projects"]["valor"]), "id='n_proyectos_biodiversidad' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_aves_muertas" class="<?php echo $label_column; ?>"><?php echo lang('n_dead_birds') . " (" . $datos["n_dead_birds"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_aves_muertas", $valores_unidad_fija, array($datos["n_dead_birds"]["valor"]), "id='n_aves_muertas' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_especies_uicn" class="<?php echo $label_column; ?>"><?php echo lang('n_species_uicn') . " (" . $datos["n_species_uicn"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_especies_uicn", $valores_unidad_fija, array($datos["n_species_uicn"]["valor"]), "id='n_especies_uicn' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="derrames_accident_suelo_agua" class="<?php echo $label_column; ?>"><?php echo lang('accidental_spills_ground_water') . " (" . $datos["accidental_spills_ground_water"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// l | id: 4
            echo form_dropdown("derrames_accident_suelo_agua", $valores_l, array($datos["accidental_spills_ground_water"]["valor"]), "id='derrames_accident_suelo_agua' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_eventos_ambientales" class="<?php echo $label_column; ?>"><?php echo lang('n_environmental_events') . " (" . $datos["n_environmental_events"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_eventos_ambientales", $valores_unidad_fija, array($datos["n_environmental_events"]["valor"]), "id='n_eventos_ambientales' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="total_dias_parado_sitio" class="<?php echo $label_column; ?>"><?php echo lang('total_stop_days_site') . " (" . $datos["total_stop_days_site"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("total_dias_parado_sitio", $valores_unidad_fija, array($datos["total_stop_days_site"]["valor"]), "id='total_dias_parado_sitio' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_pers_contrat_local" class="<?php echo $label_column; ?>"><?php echo lang('n_local_hired_employees') . " (" . $datos["n_local_hired_employees"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_pers_contrat_local", $valores_unidad_fija, array($datos["n_local_hired_employees"]["valor"]), "id='n_pers_contrat_local' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_pers_contrat_planta" class="<?php echo $label_column; ?>"><?php echo lang('n_plant_hired_employees') . " (" . $datos["n_plant_hired_employees"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_pers_contrat_planta", $valores_unidad_fija, array($datos["n_plant_hired_employees"]["valor"]), "id='n_pers_contrat_planta' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_rotacion_empleados" class="<?php echo $label_column; ?>"><?php echo lang('n_turnover_employees') . " (" . $datos["n_turnover_employees"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_rotacion_empleados", $valores_unidad_fija, array($datos["n_turnover_employees"]["valor"]), "id='n_rotacion_empleados' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="total_pers_capacit" class="<?php echo $label_column; ?>"><?php echo lang('total_trained_local_people') . " (" . $datos["total_trained_local_people"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("total_pers_capacit", $valores_unidad_fija, array($datos["total_trained_local_people"]["valor"]), "id='total_pers_capacit' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="total_pers_capacit_contrat" class="<?php echo $label_column; ?>"><?php echo lang('total_hired_trained_local_people') . " (" . $datos["total_hired_trained_local_people"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("total_pers_capacit_contrat", $valores_unidad_fija, array($datos["total_hired_trained_local_people"]["valor"]), "id='total_pers_capacit_contrat' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_horas_entrena" class="<?php echo $label_column; ?>"><?php echo lang('n_training_hours') . " (" . $datos["n_training_hours"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_horas_entrena", $valores_unidad_fija, array($datos["n_training_hours"]["valor"]), "id='n_horas_entrena' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_quejas_stakeh" class="<?php echo $label_column; ?>"><?php echo lang('n_stakeholders_complaints') . " (" . $datos["n_stakeholders_complaints"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_quejas_stakeh", $valores_unidad_fija, array($datos["n_stakeholders_complaints"]["valor"]), "id='n_quejas_stakeh' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
    <?php //$unidad = "db"; ?>
        <label for="niveles_ruido_cerca_pob" class="<?php echo $label_column; ?>"><?php echo lang('noise_levels_near_population') . " (" . $datos["noise_levels_near_population"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("niveles_ruido_cerca_pob", $valores_unidad_fija, array($datos["noise_levels_near_population"]["valor"]), "id='niveles_ruido_cerca_pob' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="acciones_sost_planta" class="<?php echo $label_column; ?>"><?php echo lang('sustainable_actions_plant') . " (" . $datos["sustainable_actions_plant"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("acciones_sost_planta", $valores_unidad_fija, array($datos["sustainable_actions_plant"]["valor"]), "id='acciones_sost_planta' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_soluc_donadas" class="<?php echo $label_column; ?>"><?php echo lang('n_donated_solutions') . " (" . $datos["n_donated_solutions"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_soluc_donadas", $valores_unidad_fija, array($datos["n_donated_solutions"]["valor"]), "id='n_soluc_donadas' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="n_benef_soluc_donadas" class="<?php echo $label_column; ?>"><?php echo lang('n_beneficiaries_donated_solutions') . " (" . $datos["n_beneficiaries_donated_solutions"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_benef_soluc_donadas", $valores_unidad_fija, array($datos["n_beneficiaries_donated_solutions"]["valor"]), "id='n_benef_soluc_donadas' class='select2'");
            ?>
        </div>
    </div>
	
    <div class="form-group">
        <label for="n_pers_comun_local" class="<?php echo $label_column; ?>"><?php echo lang('n_people_from_local_communities') . " (" . $datos["n_people_from_local_communities"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("n_pers_comun_local", $valores_unidad_fija, array($datos["n_people_from_local_communities"]["valor"]), "id='n_pers_comun_local' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
    <?php //$unidad = "€"; ?>
        <label for="gastos_prov_local" class="<?php echo $label_column; ?>"><?php echo lang('expenses_local_suppliers') . " (" . $datos["expenses_local_suppliers"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("gastos_prov_local", $valores_unidad_fija, array($datos["expenses_local_suppliers"]["valor"]), "id='gastos_prov_local' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
    <?php //$unidad = "€"; ?>
        <label for="opex_total" class="<?php echo $label_column; ?>"><?php echo lang('opex_total') . " (" . $datos["opex_total"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("opex_total", $valores_unidad_fija, array($datos["opex_total"]["valor"]), "id='opex_total' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
    <?php //$unidad = "€"; ?>
        <label for="gastos_ambientales" class="<?php echo $label_column; ?>"><?php echo lang('environmental_expenses') . " (" . $datos["environmental_expenses"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("gastos_ambientales", $valores_unidad_fija, array($datos["environmental_expenses"]["valor"]), "id='gastos_ambientales' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="enel_horas_trabajadas" class="<?php echo $label_column; ?>"><?php echo lang('enel_hours_worked') . " (" . $datos["enel_hours_worked"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("enel_horas_trabajadas", $valores_unidad_fija, array($datos["enel_hours_worked"]["valor"]), "id='enel_horas_trabajadas' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="enel_accidentes" class="<?php echo $label_column; ?>"><?php echo lang('enel_accidents') . " (" . $datos["enel_accidents"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("enel_accidentes", $valores_unidad_fija, array($datos["enel_accidents"]["valor"]), "id='enel_accidentes' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="enel_primeros_aux" class="<?php echo $label_column; ?>"><?php echo lang('enel_first_aid') . " (" . $datos["enel_first_aid"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("enel_primeros_aux", $valores_unidad_fija, array($datos["enel_first_aid"]["valor"]), "id='enel_primeros_aux' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="enel_near_miss" class="<?php echo $label_column; ?>"><?php echo lang('enel_near_miss') . " (" . $datos["enel_near_miss"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("enel_near_miss", $valores_unidad_fija, array($datos["enel_near_miss"]["valor"]), "id='enel_near_miss' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="enel_dias_perdidos" class="<?php echo $label_column; ?>"><?php echo lang('enel_lost_days') . " (" . $datos["enel_lost_days"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("enel_dias_perdidos", $valores_unidad_fija, array($datos["enel_lost_days"]["valor"]), "id='enel_dias_perdidos' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="contrat_horas_trabajadas" class="<?php echo $label_column; ?>"><?php echo lang('contractor_hours_worked') . " (" . $datos["contractor_hours_worked"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("contrat_horas_trabajadas", $valores_unidad_fija, array($datos["contractor_hours_worked"]["valor"]), "id='contrat_horas_trabajadas' class='select2'");
            ?>
        </div>
    </div>
    
	<div class="form-group">
        <label for="contrat_accidentes" class="<?php echo $label_column; ?>"><?php echo lang('contractor_accidents') . " (" . $datos["contractor_accidents"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("contrat_accidentes", $valores_unidad_fija, array($datos["contractor_accidents"]["valor"]), "id='contrat_accidentes' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="contrat_primeros_aux" class="<?php echo $label_column; ?>"><?php echo lang('contractor_first_aid') . " (" . $datos["contractor_first_aid"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("contrat_primeros_aux", $valores_unidad_fija, array($datos["contractor_first_aid"]["valor"]), "id='contrat_primeros_aux' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="contrat_near_miss" class="<?php echo $label_column; ?>"><?php echo lang('contractor_near_miss') . " (" . $datos["contractor_near_miss"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("contrat_near_miss", $valores_unidad_fija, array($datos["contractor_near_miss"]["valor"]), "id='contrat_near_miss' class='select2'");
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="contrat_dias_perdidos" class="<?php echo $label_column; ?>"><?php echo lang('contractor_lost_days') . " (" . $datos["contractor_lost_days"]["nombre_tipo_unidad"] . ")"; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
			// Unidad | id: 0
            echo form_dropdown("contrat_dias_perdidos", $valores_unidad_fija, array($datos["contractor_lost_days"]["valor"]), "id='contrat_dias_perdidos' class='select2'");
            ?>
        </div>
    </div>
    
    
<?php } ?>
<!-- FIN Fase Operación y Mantenimiento -->


<script type="text/javascript">
    $(document).ready(function () {
        
		$('[data-toggle="tooltip"]').tooltip();
		$('#kpi_report-form .select2').select2();
		
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
		
		$(document).on('click', '.delete', function(){
			initScrollbar(".modal-body", {setHeight: 50});
		});

    });
</script>