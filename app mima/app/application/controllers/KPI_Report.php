<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KPI_Report extends MY_Controller {
	
	private $id_client_context_module;
	private $id_client_context_submodule;
	
    function __construct() {
        parent::__construct();
        $this->init_permission_checker("client");
		
		$this->id_client_context_module = 2;
		$this->id_client_context_submodule = 5;
		$id_cliente = $this->login_user->client_id;
		//$this->block_url_client_context($id_cliente, $this->id_client_context_module);
		
		// KPI
		$kpi_disponibilidad_modulo = $this->Client_module_availability_model->get_one_where(array(
			"id_cliente" => $this->login_user->client_id,
			"id_modulo" => 2,
			"deleted" => 0
		));
		if(!$kpi_disponibilidad_modulo->disponible){
			$this->access_only_allowed_members();
		}

    }

    function index() {
		
		if ($this->login_user->user_type === "staff") {
			
			// Filtro Cliente
			$array_clientes[] = array("id" => "", "text" => "- ".lang("client")." -");
			$clientes = $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
			foreach($clientes as $id => $company_name){
				$array_clientes[] = array("id" => $id, "text" => $company_name);
			}
			$view_data['clientes_dropdown'] = json_encode($array_clientes);
			
			// Filtro Fase
			$array_fases[] = array("id" => "", "text" => "- ".lang("phase")." -");
			$fases = $this->Phases_model->get_dropdown_list(array("nombre"), 'id');
			foreach($fases as $id => $nombre_fase){
				if($id == 2 || $id == 3){
					$array_fases[] = array("id" => $id, "text" => $nombre_fase);
				}
			}
			$view_data['fases_dropdown'] = json_encode($array_fases);
			
			// Filtro Proyecto
			$array_proyectos[] = array("id" => "", "text" => "- ".lang("project")." -");
			$proyectos = $this->Projects_model->get_dropdown_list(array("title"), 'id');
			foreach($proyectos as $id => $title){
				$array_proyectos[] = array("id" => $id, "text" => $title);
			}
			$view_data['proyectos_dropdown'] = json_encode($array_proyectos);
			
			$this->template->rander("kpi_report/index", $view_data);

		} else {
			
			$id_usuario = $this->session->user_id;
						
			$this->session->set_userdata('menu_kpi_active', TRUE);
			$this->session->set_userdata('menu_project_active', NULL);
			$this->session->set_userdata('client_area', NULL);
			$this->session->set_userdata('project_context', NULL);
			$this->session->set_userdata('menu_agreements_active', NULL);	
			$this->session->set_userdata('menu_help_and_support_active', NULL);
			$this->session->set_userdata('menu_recordbook_active', NULL);
			$this->session->set_userdata('menu_ec_active', NULL);
			
			$view_data["puede_ver"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "ver");
			
			$view_data["label_column"] = "col-md-3";
			$view_data["field_column"] = "col-md-9";
			
			$paises = array("" => "-") + $this->Countries_model->get_dropdown_list(array("nombre"), "id");
			
			$array_fases = array("" => "-");
			$fases = $this->Phases_model->get_all()->result();
			foreach($fases as $fase){
				if($fase->id == 2 || $fase->id == 3){
					$array_fases[$fase->id] = $fase->nombre;			
				}
			}
			
			$tecnologias = array("" => "-") + $this->Technologies_model->get_dropdown_list(array("nombre"), "id");
			
			$view_data["paises"] = $paises;
			$view_data["fases"] = $array_fases;
			$view_data["tecnologias"] = $tecnologias;
			$view_data["proyectos"] = array("" => "-");
			
            $this->template->rander("kpi_report/client/index", $view_data);
		
		}
		
		
    }
	
	function modal_form() {
				
        $id_kpi_estructura_reporte = $this->input->post('id');
		
        validate_submitted_data(array(
            "id" => "numeric"
        ));
		
        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view');
		
		$view_data["clientes"] = array("" => "-") + $this->Clients_model->get_dropdown_list(array("company_name"), 'id');
		$view_data["fases"] = array("" => "-") + $this->Phases_model->get_dropdown_list(array("nombre"), 'id');
		$view_data["proyectos"] = array("" => "-");
		
		if($id_kpi_estructura_reporte){
						
			$opciones_kpi_estructura_reporte = array("id" => $id_kpi_estructura_reporte);
			$kpi_estructura_reporte = $this->KPI_Report_structure_model->get_details($opciones_kpi_estructura_reporte)->row();
			$view_data["model_info"] = $kpi_estructura_reporte;
			
			/*
			// Unidad | id: 0
			$array_valores_unidad_fija = array("" => "-");
			$valores_unidad_fija = $this->KPI_Values_model->get_all_where(array(
				"id_cliente" => $kpi_estructura_reporte->id_cliente,
				"id_proyecto" => $kpi_estructura_reporte->id_proyecto,
				"id_fase" => $kpi_estructura_reporte->id_fase,
				"id_campo_unidad" => 0,
				"deleted" => 0
			))->result();
			
			foreach($valores_unidad_fija as $valor){
				$array_valores_unidad_fija[$valor->id] = $valor->nombre_valor;
			}
			$view_data["valores_unidad_fija"] = $array_valores_unidad_fija;
			*/
			
			$valores = $this->KPI_Values_model->get_all_where(array(
				"id_cliente" => $kpi_estructura_reporte->id_cliente,
				"id_proyecto" => $kpi_estructura_reporte->id_proyecto,
				"id_fase" => $kpi_estructura_reporte->id_fase,
				"deleted" => 0
			))->result();
			
			$array_valores_unidad = array("" => "-");
			foreach($valores as $valor){
				if($valor->id_tipo_unidad == 9){
					$array_valores_unidad[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_unidad_fija"] = $array_valores_unidad;

			// MWh | id: 21
			$array_valores_mwh = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 4){
						$array_valores_mwh[$valor->id] = $valor->nombre_valor;
					}
				}	
				*/
				if($valor->id_tipo_unidad == 4){
					$array_valores_mwh[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_mwh"] = $array_valores_mwh;
			
			// T | id: 1
			$array_valores_t = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 1){
						$array_valores_t[$valor->id] = $valor->nombre_valor;
					}
				}	
				*/
				if($valor->id_tipo_unidad == 1){
					$array_valores_t[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_t"] = $array_valores_t;
			
			// m3 | id: 3
			$array_valores_m3 = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 2){
						$array_valores_m3[$valor->id] = $valor->nombre_valor;
					}
				}	
				*/
				if($valor->id_tipo_unidad == 2){
					$array_valores_m3[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_m3"] = $array_valores_m3;
			
			// ha | id: 14
			$array_valores_ha = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 7){
						$array_valores_ha[$valor->id] = $valor->nombre_valor;
					}
				}
				*/
				if($valor->id_tipo_unidad == 7){
					$array_valores_ha[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_ha"] = $array_valores_ha;
			
			// MW | id: 19
			$array_valores_mw = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 6){
						$array_valores_mw[$valor->id] = $valor->nombre_valor;
					}
				}	
				*/
				if($valor->id_tipo_unidad == 6){
					$array_valores_mw[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_mw"] = $array_valores_mw;
			
			// hrs | id: 17
			$array_valores_hrs = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 8){
						$array_valores_hrs[$valor->id] = $valor->nombre_valor;
					}
				}
				*/
				if($valor->id_tipo_unidad == 8){
					$array_valores_hrs[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_hrs"] = $array_valores_hrs;
			
			// l | id: 4
			$array_valores_l = array("" => "-");
			foreach($valores as $valor){
				/*
				$campo_unidad = $this->Fields_model->get_one($valor->id_campo_unidad);
				if($campo_unidad->opciones){
					$opciones_campo = json_decode($campo_unidad->opciones, TRUE);
					$id_unidad = $opciones_campo[0]["id_unidad"];
					$id_tipo_unidad = $this->Unity_model->get_one($id_unidad)->id_tipo_unidad;
					if($id_tipo_unidad == 2){
						$array_valores_l[$valor->id] = $valor->nombre_valor;
					}
				}	
				*/
				if($valor->id_tipo_unidad == 2){
					$array_valores_l[$valor->id] = $valor->nombre_valor;
				}
			}
			$view_data["valores_l"] = $array_valores_l;
			
			// Datos
			$datos = json_decode($kpi_estructura_reporte->datos, TRUE);
			$array_datos = array();
			foreach($datos as $index => $dato){
				$unidad = $this->Unity_model->get_one($dato["unidad"]);
				$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
				$dato["nombre_tipo_unidad"] = $tipo_unidad->nombre;
				$array_datos[$index] = $dato;
			}
			$view_data["datos"] = $array_datos;
			
		} 
		
        $this->load->view('kpi_report/modal_form', $view_data);
    }
	
	
	function save() {
		
		$id_kpi_reporte = $this->input->post('id');
		
		validate_submitted_data(array(
            "id" => "numeric",
        ));

		// $id_kpi_reporte debería venir siempre
		if($id_kpi_reporte){

			$kpi_report = $this->KPI_Report_structure_model->get_one($id_kpi_reporte);
			
			if($kpi_report->id_fase == "2"){
				
				$data_kpi_report = array(
					"construction_sites_considered" 	=> array(
															"valor" => $this->input->post("sitio_constru_considerado"),
															"codigo" => "D164",
															"descripcion" => lang("desc_construction_sites_considered"),
															"unidad" => "18" // Unidad
													   ),
					"network_electricity_consumption" 				=> array(
															"valor" => $this->input->post("consu_electr_red"),
															"codigo" => "D10",
															"descripcion" => lang("desc_network_electricity_consumption"),
															"unidad" => "21" // MWh
													   ),
					"electricity_consumption_renewable_source" 	=> array(
															"valor" => $this->input->post("consu_electr_fuente_renov"),
															"codigo" => "D179",
															"descripcion" => lang("desc_electricity_consumption_renewable_source"),
															"unidad" => "21" // MWh
													   ),
					"electricity_consumption_diesel" 			=> array(
															"valor" => $this->input->post("consu_electr_diesel"),
															"codigo" => "D180",
															"descripcion" => lang("desc_electricity_consumption_diesel"),
															"unidad" => "21" // MWh
													   ),
					"petroleum" 						=> array(
															"valor" => $this->input->post("petroleo"),
															"codigo" => "D13",
															"descripcion" => lang("desc_petroleum"),
															"unidad" => "1" // t
													   ),
					"gasoline" 						=> array(
															"valor" => $this->input->post("gasolina"),
															"codigo" => "D15",
															"descripcion" => lang("desc_gasoline"),
															"unidad" => "1" // t
													   ),
					"glp" 							=> array(
															"valor" => $this->input->post("glp"),
															"codigo" => "D17",
															"descripcion" => lang("desc_glp"),
															"unidad" => "1" // t
													   ),
					"natural_gas" 					=> array(
															"valor" => $this->input->post("gas_natural"),
															"codigo" => "D19",
															"descripcion" => lang("desc_natural_gas"),
															//"unidad" => "3" // m3
															"unidad" => "25" // m3 x 10^3
													   ),
					"biodiesel" 			=> array(
															"valor" => $this->input->post("biodiesel_alcohol"),
															"codigo" => "D205",
															"descripcion" => lang("desc_biodiesel"),
															"unidad" => "1" // t
													   ),
					"concrete" 						=> array(
															"valor" => $this->input->post("concreto"),
															"codigo" => "D182",
															"descripcion" => lang("desc_concrete"),
															"unidad" => "1" // t
													   ),
					"recycled_aggregates_concrete" => array(
															"valor" => $this->input->post("concreto_agregados_reciclados"),
															"codigo" => "D183",
															"descripcion" => lang("desc_recycled_aggregates_concrete"),
															"unidad" => "1" // t
													   ),
					"sand_gravel_construction" 			=> array(
															"valor" => $this->input->post("arena_grava_constr"),
															"codigo" => "D29",
															"descripcion" => lang("desc_sand_gravel_construction"),
															"unidad" => "1" // t
													   ),
					"structures_steel_pipes" 			=> array(
															"valor" => $this->input->post("estr_acero_tuberias"),
															"codigo" => "D184",
															"descripcion" => lang("desc_structures_steel_pipes"),
															"unidad" => "1" // t
													   ),
					"reinforcement_bars_concrete" 		=> array(
															"valor" => $this->input->post("barras_refuerzo_hormigon"),
															"codigo" => "D185",
															"descripcion" => lang("desc_reinforcement_bars_concrete"),
															"unidad" => "1" // t
													   ),
					"sustainable_iron" 			=> array(
															"valor" => $this->input->post("hierro_sostenible"),
															"codigo" => "D186",
															"descripcion" => lang("desc_sustainable_iron"),
															"unidad" => "1" // t
													   ),
					"cement_lime" 					=> array(
															"valor" => $this->input->post("cemento_cal"),
															"codigo" => "D31",
															"descripcion" => lang("desc_cement_lime"),
															"unidad" => "1" // t
													   ),
					"biodegradable_oil" 				=> array(
															"valor" => $this->input->post("aceite_biodeg"),
															"codigo" => "D187",
															"descripcion" => lang("desc_biodegradable_oil"),
															"unidad" => "1" // t
													   ),
					"no_biodegradable_oil" 				=> array(
															"valor" => $this->input->post("aceite_no_biodeg"),
															"codigo" => "D34",
															"descripcion" => lang("desc_no_biodegradable_oil"),
															"unidad" => "1" // t
													   ),								   
					"dielectric_oil" 			=> array(
															"valor" => $this->input->post("aceite_dielectrico"),
															"codigo" => "D35",
															"descripcion" => lang("desc_dielectric_oil"),
															"unidad" => "1" // t
													   ),								   
					"other_oil" 					=> array(
															"valor" => $this->input->post("otro_aceite"),
															"codigo" => "D36",
															"descripcion" => lang("desc_other_oil"),
															"unidad" => "1" // t
													   ),
					"excavated_ground" 				=> array(
															"valor" => $this->input->post("suelo_excavado"),
															"codigo" => "D189",
															"descripcion" => lang("desc_excavated_ground"),
															"unidad" => "3" // m3
													   ),
					"reused_ground" 			=> array(
															"valor" => $this->input->post("suelo_reutilizado"),
															"codigo" => "D190",
															"descripcion" => lang("desc_reused_ground"),
															"unidad" => "3" // m3
													   ),
					"of_which_reused_on_site" 				=> array(
															"valor" => $this->input->post("reutilizado_obra"),
															"codigo" => "D191",
															"descripcion" => lang("desc_of_which_reused_on_site"),
															"unidad" => "3" // m3
													   ),								   
					"of_which_contaminated_ground_rehab" 		=> array(
															"valor" => $this->input->post("suelo_contaminado_rehab"),
															"codigo" => "D192",
															"descripcion" => lang("desc_of_which_contaminated_ground_rehab"),
															"unidad" => "3" // m3
													   ),								   
					"concrete_bricks_mortar" 	=> array(
															"valor" => $this->input->post("hormigon_ladrillo_mortero"),
															"codigo" => "D194",
															"descripcion" => lang("desc_concrete_bricks_mortar"),
															"unidad" => "3" // m3
													   ),								   									   
					"aggregates_demolition" 			=> array(
															"valor" => $this->input->post("agregados_demolicion"),
															"codigo" => "D195",
															"descripcion" => lang("desc_aggregates_demolition"),
															"unidad" => "3" // m3
													   ),								   
					"structures_demolition" 		=> array(
															"valor" => $this->input->post("demolicion_estructuras"),
															"codigo" => "D196",
															"descripcion" => lang("desc_structures_demolition"),
															"unidad" => "1" // t
													   ),								   
					"ui_drinking_water" 					=> array(
															"valor" => $this->input->post("ui_agua_pot"),
															"codigo" => "D41",
															"descripcion" => lang("desc_ui_drinking_water"),
															"unidad" => "3" // m3
													   ),								   
					"ui_non_potable_water_surface" 		=> array(
															"valor" => $this->input->post("ui_agua_no_pot_superf"),
															"codigo" => "D39",
															"descripcion" => lang("desc_ui_non_potable_water_surface"),
															"unidad" => "3" // m3
													   ),
					"ui_non_potable_water_well" 			=> array(
															"valor" => $this->input->post("ui_agua_no_pot_pozos"),
															"codigo" => "D40",
															"descripcion" => lang("desc_ui_non_potable_water_well"),
															"unidad" => "3" // m3
													   ),								   
					"ui_non_potable_water_rain" 		=> array(
															"valor" => $this->input->post("ui_agua_no_pot_lluvia"),
															"codigo" => "D201",
															"descripcion" => lang("desc_ui_non_potable_water_rain"),
															"unidad" => "3" // m3
													   ),								   
					"ui_non_potable_water_plants_ext" 	=> array(
															"valor" => $this->input->post("ui_agua_no_pot_planta_ext"),
															"codigo" => "D202",
															"descripcion" => lang("desc_ui_non_potable_water_plants_ext"),
															"unidad" => "3" // m3
													   ),								   												   
					"ui_non_potable_water_plants_site" 	=> array(
															"valor" => $this->input->post("ui_agua_no_pot_planta_sitio"),
															"codigo" => "D203",
															"descripcion" => lang("desc_ui_non_potable_water_plants_site"),
															"unidad" => "3" // m3
													   ),								   												   
					"uc_drinking_water" 					=> array(
															"valor" => $this->input->post("uc_agua_pot"),
															"codigo" => "D200",
															"descripcion" => lang("desc_uc_drinking_water"),
															"unidad" => "3" // m3
													   ),								   
					"uc_non_potable_water_surface" 		=> array(
															"valor" => $this->input->post("uc_agua_no_pot_superf"),
															"codigo" => "D198",
															"descripcion" => lang("desc_uc_non_potable_water_surface"),
															"unidad" => "3" // m3
													   ),								   
					"uc_non_potable_water_well" 			=> array(
															"valor" => $this->input->post("uc_agua_no_pot_pozos"),
															"codigo" => "D199",
															"descripcion" => lang("desc_uc_non_potable_water_well"),
															"unidad" => "3" // m3
													   ),								   												   
					"uc_non_potable_water_rain" 		=> array(
															"valor" => $this->input->post("uc_agua_no_pot_lluvia"),
															"codigo" => "D201",
															"descripcion" => lang("desc_uc_non_potable_water_rain"),
															"unidad" => "3" // m3
													   ),
					"uc_non_potable_water_plants_ext" 	=> array(
															"valor" => $this->input->post("uc_agua_no_pot_planta_ext"),
															"codigo" => "D202",
															"descripcion" => lang("desc_uc_non_potable_water_plants_ext"),
															"unidad" => "3" // m3
													   ),								   
					"uc_non_potable_water_plants_site" 	=> array(
															"valor" => $this->input->post("uc_agua_no_pot_planta_sitio"),
															"codigo" => "D203",
															"descripcion" => lang("desc_uc_non_potable_water_plants_site"),
															"unidad" => "3" // m3
													   ),								   
					"accidental_spills" 		=> array(
															"valor" => $this->input->post("derrames_accidentales"),
															"codigo" => "D59",
															"descripcion" => lang("desc_accidental_spills"),
															"unidad" => "3" // m3
													   ),								   
					"significant_events" 		=> array(
															"valor" => $this->input->post("eventos_significativos"),
															"codigo" => "D175",
															"descripcion" => lang("desc_significant_events"),
															"unidad" => "18" // Unidad
													   ),								   
					"np_waste_production" 		=> array(
															"valor" => $this->input->post("np_produccion_residuos"),
															"codigo" => "D100",
															"descripcion" => lang("desc_np_waste_production"),
															"unidad" => "1" // t
													   ),								   
					"np_waste_recycling" 		=> array(
															"valor" => $this->input->post("np_residuos_reciclaje"),
															"codigo" => "D101",
															"descripcion" => lang("desc_np_waste_recycling"),
															"unidad" => "1" // t
													   ),								   								   
					"np_reused_waste" 		=> array(
															"valor" => $this->input->post("np_desechos_reutilizados"),
															"codigo" => "D102",
															"descripcion" => lang("desc_np_reused_waste"),
															"unidad" => "1" // t
													   ),								   
					"p_waste_production" 				=> array(
															"valor" => $this->input->post("p_prod_residuos"),
															"codigo" => "D152",
															"descripcion" => lang("desc_p_waste_production"),
															"unidad" => "1" // t
													   ),								   
					"p_waste_recycling" 		=> array(
															"valor" => $this->input->post("p_residuos_transferidos"),
															"codigo" => "D153",
															"descripcion" => lang("desc_p_waste_recycling"),
															"unidad" => "1" // t
													   ),								   
					"p_reused_waste" 		=> array(
															"valor" => $this->input->post("p_desechos_reutilizados"),
															"codigo" => "-",
															"descripcion" => lang("desc_p_reused_waste"),
															"unidad" => "1" // t
													   ),								   
					"occupied_surface_construction" 	=> array(
															"valor" => $this->input->post("superficie_ocupada_constr"),
															"codigo" => "D159",
															"descripcion" => lang("desc_occupied_surface_construction"),
															"unidad" => "14" // ha
													   ),		
					"total_co2_offset" 				=> array(
															"valor" => $this->input->post("total_co2_offset"),
															"codigo" => "-",
															"descripcion" => lang("desc_total_co2_offset"),
															"unidad" => "1" // t
													   ),								   
					"n_biodiversity_projects" 	=> array(
															"valor" => $this->input->post("n_proyectos_biodiversidad"),
															"codigo" => "-",
															"descripcion" => lang("desc_n_biodiversity_projects"),
															"unidad" => "18" // Unidad
													   ),
				);

			}

			if($kpi_report->id_fase == "3"){
			
				$data_kpi_report = array(
					"installed_capacity"				=> array(
																"valor" => $this->input->post("capacidad_instalada"),
																"codigo" => "1",
																"descripcion" => lang("desc_installed_capacity"),
																"unidad" => "19" // MW
														   ),
					"n_gen_wind_turbine"				=> array(
																"valor" => $this->input->post("n_gen_turbina_eolica"),
																"codigo" => "2",
																"descripcion" => lang("desc_n_gen_wind_turbine"),
																"unidad" => "18" // Unidad
														   ),								 
													 
					"occupied_surface"				=> array(
																"valor" => $this->input->post("superficie_ocupada"),
																"codigo" => "3",
																"descripcion" => lang("desc_occupied_surface"),
																"unidad" => "14" // ha
														   ),								 
					"operating_hours"				=> array(
																"valor" => $this->input->post("horas_funcionamiento"),
																"codigo" => "4",
																"descripcion" => lang("desc_operating_hours"),
																"unidad" => "17" // hrs
														   ),								 										 
					"network_electricity_consumption"					=> array(
																"valor" => $this->input->post("consu_electr_red"),
																"codigo" => "5",
																"descripcion" => lang("desc_network_electricity_consumption"),
																"unidad" => "21" // MWh
														   ),
					"electricity_autoconsumption" 					=> array(
																"valor" => $this->input->post("autoconsu_electr"),
																"codigo" => "6",
																"descripcion" => lang("desc_electricity_autoconsumption"),
																"unidad" => "21" // MWh
														   ),
					"electricity_consumption_from_diesel" 				=> array(
																"valor" => $this->input->post("consu_electr_diesel"),
																"codigo" => "7",
																"descripcion" => lang("desc_electricity_consumption_from_diesel"),
																"unidad" => "21" // MWh
														   ),
					"petroleum_diesel" 					=> array(
																"valor" => $this->input->post("petroleo_diesel"),
																"codigo" => "8",
																"descripcion" => lang("desc_petroleum_diesel"),
																"unidad" => "1" // t
														   ),								   
					"gasoline" 							=> array(
																"valor" => $this->input->post("gasolina"),
																"codigo" => "9",
																"descripcion" => lang("desc_gasoline"),
																"unidad" => "1" // t
														   ),								   
					"glp" 								=> array(
																"valor" => $this->input->post("glp"),
																"codigo" => "10",
																"descripcion" => lang("desc_glp"),
																"unidad" => "1" // t
														   ),								   
					"natural_gas" 						=> array(
																"valor" => $this->input->post("gas_natural"),
																"codigo" => "11",
																"descripcion" => lang("desc_natural_gas"),
																"unidad" => "3" // m3
														   ),								   
					"biodiesel_alcohol" 				=> array(
																"valor" => $this->input->post("biodiesel_alcohol"),
																"codigo" => "12",
																"descripcion" => lang("desc_biodiesel_alcohol"),
																"unidad" => "1" // t
														   ),								   
					"sf6_present_on_plant" 			=> array(
																"valor" => $this->input->post("sf6_presente_en_planta"),
																"codigo" => "13",
																"descripcion" => lang("desc_sf6_present_on_plant"),
																"unidad" => "4" // l
														   ),								   
					"biodegradable_oil" 					=> array(
																"valor" => $this->input->post("aceite_biodeg"),
																"codigo" => "14",
																"descripcion" => lang("desc_biodegradable_oil"),
																"unidad" => "1" // t
														   ),
					"no_biodegradable_oil" 					=> array(
																"valor" => $this->input->post("aceite_no_biodeg"),
																"codigo" => "15",
																"descripcion" => lang("desc_no_biodegradable_oil"),
																"unidad" => "1" // t
														   ),
					"dielectric_oil" 				=> array(
																"valor" => $this->input->post("aceite_dielectrico"),
																"codigo" => "16",
																"descripcion" => lang("desc_dielectric_oil"),
																"unidad" => "1" // t
														   ),								   
					"oil_containing_pcb" 					=> array(
																"valor" => $this->input->post("aceite_con_pcb"),
																"codigo" => "17",
																"descripcion" => lang("desc_oil_containing_pcb"),
																"unidad" => "1" // t
														   ),								   
					"others_no_biodegradable_oils" 			=> array(
																"valor" => $this->input->post("otros_aceites_no_biodeg"),
																"codigo" => "18",
																"descripcion" => lang("desc_others_no_biodegradable_oils"),
																"unidad" => "1" // t
														   ),								   
					"decrease_oil_good_practices" 		=> array(
																"valor" => $this->input->post("dism_aceite_buenas_practicas"),
																"codigo" => "19",
																"descripcion" => lang("desc_decrease_oil_good_practices"),
																"unidad" => "4" // l
														   ),								   
					"waste_production_np" 			=> array(
																"valor" => $this->input->post("np_produccion_residuos"),
																"codigo" => "20",
																"descripcion" => lang("desc_waste_production_np"),
																"unidad" => "1" // t
														   ),
					"np_recycled_waste" 			=> array(
																"valor" => $this->input->post("np_residuos_reciclados"),
																"codigo" => "21",
																"descripcion" => lang("desc_np_recycled_waste"),
																"unidad" => "1" // t
														   ),								   
					"dangerous_waste_production" 			=> array(
																"valor" => $this->input->post("prod_residuos_peligrosos"),
																"codigo" => "22",
																"descripcion" => lang("desc_dangerous_waste_production"),
																"unidad" => "1" // t
														   ),								   
					"dangerous_waste_recycled" 	=> array(
																"valor" => $this->input->post("residuos_peligrosos_reciclados"),
																"codigo" => "23",
																"descripcion" => lang("desc_dangerous_waste_recycled"),
																"unidad" => "1" // t
														   ),								   
					"drinking_water_consumption_river" 				=> array(
																"valor" => $this->input->post("consu_agua_pot_rios"),
																"codigo" => "25",
																"descripcion" => lang("desc_drinking_water_consumption_river"),
																"unidad" => "3" // m3
														   ),								   
					"drinking_water_consumption_well" 				=> array(
																"valor" => $this->input->post("consu_agua_pot_pozos"),
																"codigo" => "26",
																"descripcion" => lang("desc_drinking_water_consumption_well"),
																"unidad" => "3" // m3
														   ),								   
					"drinking_water_consumption_plants" 			=> array(
																"valor" => $this->input->post("consu_agua_pot_plantas"),
																"codigo" => "27",
																"descripcion" => lang("desc_drinking_water_consumption_plants"),
																"unidad" => "3" // m3
														   ),								   
					"drinking_water_consumption_wastewater_treatment_plant" 			=> array(
																"valor" => $this->input->post("consu_agua_pot_depura"),
																"codigo" => "28",
																"descripcion" => lang("desc_drinking_water_consumption_wastewater_treatment_plant"),
																"unidad" => "3" // m3
														   ),								   
					"drinking_water_consumption_system_harvest" 			=> array(
																"valor" => $this->input->post("consu_agua_pot_lluvia"),
																"codigo" => "29",
																"descripcion" => lang("desc_drinking_water_consumption_system_harvest"),
																"unidad" => "3" // m3
														   ),								   
					"non_potable_water_consumption_river" 				=> array(
																"valor" => $this->input->post("consu_agua_pot_rios"),
																"codigo" => "25",
																"descripcion" => lang("desc_non_potable_water_consumption_river"),
																"unidad" => "3" // m3
														   ),								   
					"non_potable_water_consumption_well" 			=> array(
																"valor" => $this->input->post("consu_agua_no_pot_pozo"),
																"codigo" => "26",
																"descripcion" => lang("desc_non_potable_water_consumption_well"),
																"unidad" => "3" // m3
														   ),
					"non_potable_water_consumption_plants_water" 	=> array(
																"valor" => $this->input->post("consu_agua_no_pot_planta_agua"),
																"codigo" => "27",
																"descripcion" => lang("desc_non_potable_water_consumption_plants_water"),
																"unidad" => "3" // m3
														   ),								   
					"non_potable_water_consumption_plants_res_water" => array(
																"valor" => $this->input->post("consu_agua_no_pot_planta_agua_res"),
																"codigo" => "28",
																"descripcion" => lang("desc_non_potable_water_consumption_plants_res_water"),
																"unidad" => "3" // m3
														   ),								   
					"non_potable_water_consumption_system_harvest" 			=> array(
																"valor" => $this->input->post("consu_agua_no_pot_lluvia"),
																"codigo" => "29",
																"descripcion" => lang("desc_non_potable_water_consumption_system_harvest"),
																"unidad" => "3" // m3
														   ),								   
					"n_biodiversity_projects" 		=> array(
																"valor" => $this->input->post("n_proyectos_biodiversidad"),
																"codigo" => "30",
																"descripcion" => lang("desc_n_biodiversity_projects"),
																"unidad" => "18" // Unidad
														   ),								   
					"n_dead_birds" 					=> array(
																"valor" => $this->input->post("n_aves_muertas"),
																"codigo" => "31",
																"descripcion" => lang("desc_n_dead_birds"),
																"unidad" => "18" // Unidad
														   ),
					"n_species_uicn" 					=> array(
																"valor" => $this->input->post("n_especies_uicn"),
																"codigo" => "32",
																"descripcion" => lang("desc_n_species_uicn"),
																"unidad" => "18" // Unidad
														   ),
					"accidental_spills_ground_water" 		=> array(
																"valor" => $this->input->post("derrames_accident_suelo_agua"),
																"codigo" => "33",
																"descripcion" => lang("desc_accidental_spills_ground_water"),
																"unidad" => "4" // l
														   ),
					"n_environmental_events" 			=> array(
																"valor" => $this->input->post("n_eventos_ambientales"),
																"codigo" => "34",
																"descripcion" => lang("desc_n_environmental_events"),
																"unidad" => "18" // Unidad
														   ),
					"total_stop_days_site" 			=> array(
																"valor" => $this->input->post("total_dias_parado_sitio"),
																"codigo" => "35",
																"descripcion" => lang("desc_total_stop_days_site"),
																"unidad" => "18" // Unidad
														   ),
					"n_local_hired_employees" 				=> array(
																"valor" => $this->input->post("n_pers_contrat_local"),
																"codigo" => "36",
																"descripcion" => lang("desc_n_local_hired_employees"),
																"unidad" => "18" // Unidad
														   ),
					"n_plant_hired_employees" 			=> array(
																"valor" => $this->input->post("n_pers_contrat_planta"),
																"codigo" => "37",
																"descripcion" => lang("desc_n_plant_hired_employees"),
																"unidad" => "18" // Unidad
														   ),
					"n_turnover_employees" 				=> array(
																"valor" => $this->input->post("n_rotacion_empleados"),
																"codigo" => "38",
																"descripcion" => lang("desc_n_turnover_employees"),
																"unidad" => "18" // Unidad
														   ),
					"total_trained_local_people" 				=> array(
																"valor" => $this->input->post("total_pers_capacit"),
																"codigo" => "39",
																"descripcion" => lang("desc_total_trained_local_people"),
																"unidad" => "18" // Unidad
														   ),
					"total_hired_trained_local_people" 		=> array(
																"valor" => $this->input->post("total_pers_capacit_contrat"),
																"codigo" => "40",
																"descripcion" => lang("desc_total_hired_trained_local_people"),
																"unidad" => "18" // Unidad
														   ),
					"n_training_hours" 					=> array(
																"valor" => $this->input->post("n_horas_entrena"),
																"codigo" => "41",
																"descripcion" => lang("desc_n_training_hours"),
																"unidad" => "18" // Unidad
														   ),
					"n_stakeholders_complaints" 					=> array(
																"valor" => $this->input->post("n_quejas_stakeh"),
																"codigo" => "42",
																"descripcion" => lang("desc_n_stakeholders_complaints"),
																"unidad" => "18" // Unidad
														   ),
					"noise_levels_near_population" 			=> array(
																"valor" => $this->input->post("niveles_ruido_cerca_pob"),
																"codigo" => "43",
																"descripcion" => lang("desc_noise_levels_near_population"),
																"unidad" => "18" // Unidad
														   ),								   
					"sustainable_actions_plant" 				=> array(
																"valor" => $this->input->post("acciones_sost_planta"),
																"codigo" => "44",
																"descripcion" => lang("desc_sustainable_actions_plant"),
																"unidad" => "18" // Unidad
														   ),								   
					"n_donated_solutions" 					=> array(
																"valor" => $this->input->post("n_soluc_donadas"),
																"codigo" => "45",
																"descripcion" => lang("desc_n_donated_solutions"),
																"unidad" => "18" // Unidad
														   ),								   
					"n_beneficiaries_donated_solutions" 			=> array(
																"valor" => $this->input->post("n_benef_soluc_donadas"),
																"codigo" => "46",
																"descripcion" => lang("desc_n_beneficiaries_donated_solutions"),
																"unidad" => "18" // Unidad
														   ),								   
					"n_people_from_local_communities" 				=> array(
																"valor" => $this->input->post("n_pers_comun_local"),
																"codigo" => "47",
																"descripcion" => lang("desc_n_people_from_local_communities"),
																"unidad" => "18" // Unidad
														   ),	
					"expenses_local_suppliers" 				=> array(
																"valor" => $this->input->post("gastos_prov_local"),
		
																"codigo" => "48",
																"descripcion" => lang("desc_expenses_local_suppliers"),
																"unidad" => "18" // Unidad
														   ),
					"opex_total" 						=> array(
																"valor" => $this->input->post("opex_total"),
																"codigo" => "49",
																"descripcion" => lang("desc_opex_total"),
																"unidad" => "18" // Unidad
														   ),									   
					"environmental_expenses" 				=> array(
																"valor" => $this->input->post("gastos_ambientales"),
																"codigo" => "50",
																"descripcion" => lang("desc_environmental_expenses"),
																"unidad" => "18" // Unidad
														   ),									   
					"enel_hours_worked" 			=> array(
																"valor" => $this->input->post("enel_horas_trabajadas"),
																"codigo" => "51",
																"descripcion" => lang("desc_enel_hours_worked"),
																//"unidad" => "18" // Unidad
																"unidad" => "17" // Horas
														   ),
					"enel_accidents" 					=> array(
																"valor" => $this->input->post("enel_accidentes"),
																"codigo" => "52",
																"descripcion" => lang("desc_enel_accidents"),
																"unidad" => "18" // Unidad
														   ),								   
					"enel_first_aid" 				=> array(
																"valor" => $this->input->post("enel_primeros_aux"),
																"codigo" => "53",
																"descripcion" => lang("desc_enel_first_aid"),
																"unidad" => "18" // Unidad
														   ),								   
					"enel_near_miss" 					=> array(
																"valor" => $this->input->post("enel_near_miss"),
																"codigo" => "54",
																"descripcion" => lang("desc_enel_near_miss"),
																"unidad" => "18" // Unidad
														   ),
					"enel_lost_days" 				=> array(
																"valor" => $this->input->post("enel_dias_perdidos"),
																"codigo" => "55",
																"descripcion" => lang("desc_enel_lost_days"),
																//"unidad" => "18" // Unidad
																"unidad" => "16" // Días
														   ),								   
					"contractor_hours_worked" 			=> array(
																"valor" => $this->input->post("contrat_horas_trabajadas"),
																"codigo" => "51",
																"descripcion" => lang("desc_contractor_hours_worked"),
																//"unidad" => "18" // Unidad
																"unidad" => "17" // Horas
														   ),									   
					"contractor_accidents" 				=> array(
																"valor" => $this->input->post("contrat_accidentes"),
																"codigo" => "52",
																"descripcion" => lang("desc_contractor_accidents"),
																"unidad" => "18" // Unidad
														   ),
					"contractor_first_aid" 				=> array(
																"valor" => $this->input->post("contrat_primeros_aux"),
																"codigo" => "53",
																"descripcion" => lang("desc_contractor_first_aid"),
																"unidad" => "18" // Unidad
														   ),								   
					"contractor_near_miss" 				=> array(
																"valor" => $this->input->post("contrat_near_miss"),
																"codigo" => "54",
																"descripcion" => lang("desc_contractor_near_miss"),
																"unidad" => "18" // Unidad
														   ),									   
					"contractor_lost_days" 			=> array(
																"valor" => $this->input->post("contrat_dias_perdidos"),
																"codigo" => "55",
																"descripcion" => lang("desc_contractor_lost_days"),
																//"unidad" => "18" // Unidad
																"unidad" => "16" // Días
														   ),								   
												   																																   
				);
			
			
			
			}
			
			$json_data_kpi_report = json_encode($data_kpi_report);
			$data = array(
				"is_valor_asignado" => 1,
				"datos" => $json_data_kpi_report,
				"modified_by" => $this->login_user->id,
				"modified" => get_current_utc_time()
			);
			$save_id = $this->KPI_Report_structure_model->save($data, $kpi_report->id);

		} else {
			
			$data_kpi_report["created_by"] = $this->login_user->id;
			$data_kpi_report["created"] = get_current_utc_time();
			$save_id = $this->KPI_Report_structure_model->save($data_kpi_report);
			
		}
		
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }
	
	function list_data() {
		
		$options = array(
			"id_cliente" => $this->input->post('id_cliente'),
			"id_fase" => $this->input->post('id_fase'),
			"id_proyecto" => $this->input->post('id_proyecto')
		);
		
        $list_data = $this->KPI_Report_structure_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
			$result[] = $this->_make_row($data);
        }
		
        echo json_encode(array("data" => $result));
    }
	
	private function _row_data($id) {
        
        $options = array(
            "id" => $id,
        );
		
        $data = $this->KPI_Report_structure_model->get_details($options)->row();
        return $this->_make_row($data);
    }
	
	private function _make_row($data) {
		
		$row_data[] = $data->id;
		$row_data[] = $data->nombre_cliente;
		$row_data[] = $data->nombre_fase;
		$row_data[] = $data->nombre_proyecto;
		
		$row_data[] = modal_anchor(get_uri("KPI_Report/view/" . $data->id), "<i class='fa fa-eye'></i>", array("class" => "edit", "title" => lang('view_kpi_report'), "data-post-id" => $data->id))
					. modal_anchor(get_uri("KPI_Report/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_kpi_report'), "data-post-id" => $data->id));
		
        return $row_data;
    }
	
	function view($id_kpi_reporte = 0) {

        if ($id_kpi_reporte) {
            $options = array("id" => $id_kpi_reporte);
            $info_kpi_reporte = $this->KPI_Report_structure_model->get_details($options)->row();
            if ($info_kpi_reporte) {
				
				$view_data["label_column"] = "col-md-3";
				$view_data["field_column"] = "col-md-9";
				$view_data['model_info'] = $info_kpi_reporte;

				// Datos
				$datos = json_decode($info_kpi_reporte->datos, TRUE);
				$array_datos = array();
				foreach($datos as $index => $dato){
					$unidad = $this->Unity_model->get_one($dato["unidad"]);
					$tipo_unidad = $this->Unity_type_model->get_one($unidad->id_tipo_unidad);
					$dato["nombre_tipo_unidad"] = $tipo_unidad->nombre;
					$array_datos[$index] = $dato;
				}
				$view_data["datos"] = $array_datos;
				
				$this->load->view('kpi_report/view', $view_data);
				
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
	
	function get_project_filter(){
		
		$opciones_proyectos = array(
			"id_pais" => $this->input->post('id_pais'),
			"id_fase" => $this->input->post('id_fase'),
			"id_tech" => $this->input->post('id_tecnologia'),
			"id_cliente" => $this->login_user->client_id
		);
		
		$array_proyectos = array("" => "-");
		$proyectos = $this->Projects_model->get_project_for_kpi_report_filter($opciones_proyectos)->result();
		foreach($proyectos as $proyecto){
			$array_proyectos[$proyecto->id] = $proyecto->title;
		}
		
		$html = '<div class="form-group col-md-4">';
		$html .= '<label for="proyecto" class="col-md-3">'.lang('project').'</label>';
		$html .= 	'<div class="col-md-9">';
		$html .= 		form_dropdown("proyecto", $array_proyectos, "", "id='proyecto' class='select2 validate-hidden' data-rule-required='true' data-msg-required='" . lang('field_required') . "'");
		$html .= 	'</div>';
		$html .= '</div>';
		
		echo $html;
	
	}
	
	function get_kpi_report(){
		
		$id_usuario = $this->session->user_id;
		$view_data["puede_editar"] = $this->general_profile_access($id_usuario, $this->id_client_context_module, $this->id_client_context_submodule, "editar");
		
		$id_proyecto = $this->input->post('id_proyecto');
		$proyecto = $this->Projects_model->get_one($id_proyecto);
		$view_data["id_cliente"] = $proyecto->client_id;
		$proyecto_rel_fase = $this->Project_rel_phases_model->get_one_where(array(
			"id_proyecto" => $proyecto->id,
			"deleted" => 0
		));

		$fecha_desde = $this->input->post('start_date');
		$fecha_hasta = $this->input->post('end_date');
		
		$view_data["id_pais"] = $proyecto->id_pais;
		$view_data["id_fase"] = $proyecto_rel_fase->id_fase;
		$view_data["id_tecnologia"] = $proyecto->id_tech;
		$view_data["id_proyecto"] = $id_proyecto;
		$view_data["fecha_desde"] = $fecha_desde;
		$view_data["fecha_hasta"] = $fecha_hasta;
		
		$kpi_estructura_reporte = $this->KPI_Report_structure_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		
		$array_kpi_reporte_datos = json_decode($kpi_estructura_reporte->datos, TRUE);
		$view_data["datos_kpi_reporte"] = $array_kpi_reporte_datos;
		
		$data_plantilla_reporte = array(
			"id_pais" => $proyecto->id_pais,
			"id_fase" => $proyecto_rel_fase->id_fase,
			"id_tecnologia" => $proyecto->id_tech,
			"id_proyecto" => $id_proyecto,
			"fecha_desde" => $fecha_desde,
			"fecha_hasta" => $fecha_hasta
		);
		
		$plantilla = $this->KPI_Report_templates_model->get_one_where($data_plantilla_reporte);
		
		if($plantilla->id){
			
			$view_data["id_plantilla"] = $plantilla->id;
			$plantilla_completa = TRUE;
			$json_datos = $plantilla->datos;
			$array_datos = json_decode($json_datos, TRUE);
			$view_data["datos_plantilla"] = $array_datos;
			foreach($array_datos as $nombre_indicador => $datos){
				if(!$datos["valor"] && !$datos["valor_cliente"]){
					if(!$array_kpi_reporte_datos[$nombre_indicador]["valor"]){
						$plantilla_completa = FALSE;
						break;
					}
				}
			}
			
		} else {
			$plantilla_completa = FALSE;
		}
		
		$view_data["plantilla_completa"] = $plantilla_completa;
		
		$html_kpi_report = $this->load->view('kpi_report/client/kpi_report', $view_data, TRUE);

		echo $html_kpi_report;
		
	}
	
	function save_report(){

		$data_plantilla_reporte = array(
			"id_pais" => $this->input->post("id_pais"),
			"id_fase" => $this->input->post("id_fase"),
			"id_tecnologia" => $this->input->post("id_tecnologia"),
			"id_proyecto" => $this->input->post("id_proyecto"),
			"fecha_desde" => $this->input->post("fecha_desde"),
			"fecha_hasta" => $this->input->post("fecha_hasta"),
		);
		
		$plantilla = $this->KPI_Report_templates_model->get_one_where($data_plantilla_reporte);
		
		$valores_indicadores = $this->input->post("valor");
		$array_valores_indicadores = array();
		foreach($valores_indicadores as $nombre_indicador => $valor_indicador){
			$array_valores_indicadores[$nombre_indicador] = array(
				"valor" => $valor_indicador["valor"] ? $valor_indicador["valor"] : "",
				"valor_cliente" => $valor_indicador["valor_cliente"] ? $valor_indicador["valor_cliente"] : "",
			);
		}
		$array_valores_indicadores_json = json_encode($array_valores_indicadores);
		if($plantilla->id){
			
			$plantilla_completa = TRUE;
			$data_plantilla_reporte["datos"] = $array_valores_indicadores_json;
			$data_plantilla_reporte["modified_by"] = $this->login_user->id;
			$data_plantilla_reporte["modified"] = get_current_utc_time();
			$save_id = $this->KPI_Report_templates_model->save($data_plantilla_reporte, $plantilla->id);
			
			$plantilla_editada = $this->KPI_Report_templates_model->get_one($save_id);
			$datos_plantilla_editada = json_decode($plantilla_editada->datos, TRUE);
			//var_dump($datos_plantilla_editada);
			foreach($datos_plantilla_editada as $nombre_indicador => $datos){
				if(!$datos["valor"] && !$datos["valor_cliente"]){
					$plantilla_completa = FALSE;
					break;
				}
			}
			
		} else {
			
			$plantilla_completa = TRUE;
			
			$data_plantilla_reporte["datos"] = $array_valores_indicadores_json;
			$data_plantilla_reporte["created_by"] = $this->login_user->id;
			$data_plantilla_reporte["created"] = get_current_utc_time();
			$save_id = $this->KPI_Report_templates_model->save($data_plantilla_reporte);
			
			$plantilla_editada = $this->KPI_Report_templates_model->get_one($save_id);
			$datos_plantilla_editada = json_decode($plantilla_editada->datos, TRUE);
			foreach($datos_plantilla_editada as $nombre_indicador => $datos){
				if(!$datos["valor"] && !$datos["valor_cliente"]){
					$plantilla_completa = FALSE;
					break;
				}
			}

		}
				
		if($save_id) {
        	echo json_encode(array("success" => true, 'message' => lang('record_saved'), 'plantilla_completa' => $plantilla_completa, "id_plantilla" => $save_id));
		} else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
		
	}
	
	function get_excel(){
		
		$id_cliente = $this->login_user->client_id;
		$client_info = $this->Clients_model->get_one($id_cliente);
		
		$id_plantilla = $this->input->post("id_plantilla");
		$plantilla = $this->KPI_Report_templates_model->get_one($id_plantilla);
		
		$id_proyecto = $plantilla->id_proyecto;
		$fecha_desde = $plantilla->fecha_desde;
		$fecha_hasta = $plantilla->fecha_hasta;
		
		$datos_plantilla = json_decode($plantilla->datos, TRUE);
		
		$kpi_estructura_reporte = $this->KPI_Report_structure_model->get_one_where(array(
			"id_proyecto" => $id_proyecto,
			"deleted" => 0
		));
		$datos_kpi_reporte = json_decode($kpi_estructura_reporte->datos, TRUE);
		
		$result = array();
		foreach($datos_kpi_reporte as $nombre_indicador => $datos_indicador){
			
			$valor = $this->KPI_Values_model->get_one($datos_indicador["valor"]);
			$valores_condicion = $this->KPI_Values_condition_model->get_all_where(array(
				"id_kpi_valores" => $valor->id,
				"deleted" => 0
			))->result_array();
			$formulario_valor = $this->Forms_model->get_one($valor->id_formulario);
			$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
				"id_formulario" => $formulario_valor->id,
				"deleted" => 0
			));
			$elementos_formulario = $this->Form_values_model->get_all_where(array(
				"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
				"deleted" => 0
			))->result_array();
			$total_valor = 0;
			
			if($valor->id && $valor->tipo_valor == "simple") {

				if(count($valores_condicion)){
								
					$array_datos_valores_condicion = array();
					
					foreach($valores_condicion as $valor_condicion){
						
						$valor_condicion_categoria = ($valor_condicion["is_category"]) ? $valor_condicion["valor"] : NULL;
						$valor_condicion_tipo_tratamiento = ($valor_condicion["is_tipo_tratamiento"]) ? $valor_condicion["valor"] : NULL;
						$valor_condicion_id_campo = ($valor_condicion["id_campo"]) ? $valor_condicion["valor"] : NULL;
						$valor_condicion_id_campo_fijo = ($valor_condicion["id_campo_fijo"]) ? $valor_condicion["valor"] : NULL;
						
						if($valor_condicion_categoria){
							$array_datos_valores_condicion["id_categoria"] = $valor_condicion_categoria;
						}
						if($valor_condicion_tipo_tratamiento){
							$array_datos_valores_condicion["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
						}
						if($valor_condicion_id_campo){
							$array_datos_valores_condicion[$valor_condicion["id_campo"]] = $valor_condicion_id_campo;
						}
						if($valor_condicion_id_campo_fijo){
							$array_datos_valores_condicion[$valor_condicion["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
						}

					}

				}
				
				if(count($elementos_formulario)){

					foreach($elementos_formulario as $elemento){
				
						$datos = json_decode($elemento["datos"], TRUE);
						$fecha_elemento = $datos["fecha"];

						$elemento_campos_dinamicos = array();
						foreach($datos as $key => $value){
							if(array_key_exists($key, $array_datos_valores_condicion)){
								$elemento_campos_dinamicos[$key] = $datos[$key];
							}
						}
						
						// Si los datos de las condiciones del valor son iguales a los del elemento del formulario del valor, suma los valores de las unidades de los elementos del formulario
						if($array_datos_valores_condicion == $elemento_campos_dinamicos){
							if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
								$campo_indicador = $valor->id_campo_unidad;
								if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
									$valor_unidad_fija = $datos["unidad_residuo"];
									$total_valor = $total_valor + $valor_unidad_fija;
								} else {
									$valor_unidad_dinamica = $datos[$campo_indicador]; 
									$total_valor = $total_valor + $valor_unidad_dinamica;
								}
							}
						}

					}

				}
				
				if($valor->operador){
					
					$operador = $valor->operador;
					$valor_operador = $valor->valor_operador;
					
					if($operador == "+"){
						$total_valor = $total_valor + $valor_operador;
					}
					if($operador == "-"){
						$total_valor = $total_valor - $valor_operador;
					}
					if($operador == "*"){
						$total_valor = $total_valor * $valor_operador;
					}
					if($operador == "/"){
						$total_valor = $total_valor / $valor_operador;
					}
						
				}
				
				$id_unidad_valor = $valor->id_unidad;
				$id_tipo_unidad_valor = $valor->id_tipo_unidad;
				$id_unidad_indicador = $datos_indicador["unidad"];
				
				// transformar valores en reporte, que tengan una unidad distinta a la unidad del indicador.
				// si el valor tiene una unidad distinta a la del indicador, transformar a la unidad del indicador.
				if($id_unidad_valor != $id_unidad_indicador){
					if($id_unidad_indicador != 18){ // Unidad de tipo unidad (id 18) no tiene conversión
						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => $id_tipo_unidad_valor,
								"id_unidad_origen" => $id_unidad_valor,
								"id_unidad_destino" => $id_unidad_indicador
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;
						$total_valor = $total_valor * $valor_transformacion;
					}
				} 
				
				unset($array_datos_valores_condicion);

			}
			
			
			if($valor->id && $valor->tipo_valor == "compound") {
				
				// Cálculo valor inicial
				$valor_inicial = $this->KPI_Values_model->get_one($valor->valor_inicial);
				$valores_condicion_inicial = $this->KPI_Values_condition_model->get_all_where(array(
					"id_kpi_valores" => $valor_inicial->id,
					"deleted" => 0
				))->result_array();
				$formulario_valor_inicial = $this->Forms_model->get_one($valor_inicial->id_formulario);
				
				if(!$formulario_valor_inicial->fijo){
					$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
						"id_formulario" => $formulario_valor_inicial->id,
						"deleted" => 0
					));
					$elementos_formulario_inicial = $this->Form_values_model->get_all_where(array(
						"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
						"deleted" => 0
					))->result_array();
				} else {
					$elementos_formulario_inicial = $this->Fixed_form_values_model->get_all_where(array(
						"id_formulario" => $formulario_valor_inicial->id,
						"deleted" => 0
					))->result_array();
				}
				
				$total_valor_inicial = 0;
				
				if($valor_inicial->id && count($valores_condicion_inicial)) {
					
					$array_datos_valores_condicion_inicial = array();
					
					foreach($valores_condicion_inicial as $valor_condicion_inicial){
						
						$valor_condicion_categoria = ($valor_condicion_inicial["is_category"]) ? $valor_condicion_inicial["valor"] : NULL;
						$valor_condicion_tipo_tratamiento = ($valor_condicion_inicial["is_tipo_tratamiento"]) ? $valor_condicion_inicial["valor"] : NULL;
						$valor_condicion_id_campo = ($valor_condicion_inicial["id_campo"]) ? $valor_condicion_inicial["valor"] : NULL;
						$valor_condicion_id_campo_fijo = ($valor_condicion_inicial["id_campo_fijo"]) ? $valor_condicion_inicial["valor"] : NULL;
						
						if($valor_condicion_categoria){
							$array_datos_valores_condicion_inicial["id_categoria"] = $valor_condicion_categoria;
						}
						if($valor_condicion_tipo_tratamiento){
							$array_datos_valores_condicion_inicial["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
						}
						if($valor_condicion_id_campo){
							$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo"]] = $valor_condicion_id_campo;
						}
						if($valor_condicion_id_campo_fijo){
							$array_datos_valores_condicion_inicial[$valor_condicion_inicial["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
						}

					}
					
				}
				
				if(count($elementos_formulario_inicial)){

					foreach($elementos_formulario_inicial as $elemento){
				
						$datos = json_decode($elemento["datos"], TRUE);
						$fecha_elemento = $datos["fecha"];

						$elemento_campos_dinamicos = array();
						foreach($datos as $key => $value){
							if(array_key_exists($key, $array_datos_valores_condicion_inicial)){
								$elemento_campos_dinamicos[$key] = $datos[$key];
							}
						}
						
						if($array_datos_valores_condicion_inicial == $elemento_campos_dinamicos){
							
							if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
								$campo_indicador = $valor_inicial->id_campo_unidad;
								if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
									$valor_unidad_fija = $datos["unidad_residuo"];
									$total_valor_inicial = $total_valor_inicial + $valor_unidad_fija;
								} else {
									$valor_unidad_dinamica = $datos[$campo_indicador]; 
									$total_valor_inicial = $total_valor_inicial + $valor_unidad_dinamica;
								}
							}	

						}

					}

				}
				
				if($valor_inicial->operador){
				
					$operador = $valor_inicial->operador;
					$valor_operador = $valor_inicial->valor_operador;
					
					if($operador == "+"){
						$total_valor_inicial = $total_valor_inicial + $valor_operador;
					}
					if($operador == "-"){
						$total_valor_inicial = $total_valor_inicial - $valor_operador;
					}
					if($operador == "*"){
						$total_valor_inicial = $total_valor_inicial * $valor_operador;
					}
					if($operador == "/"){
						$total_valor_inicial = $total_valor_inicial / $valor_operador;
					}
					
				}
				// Fin Cálculo valor inicial

				// Cálculo valores operación
				$array_operacion_compuesta = json_decode($valor->operacion_compuesta, TRUE);
				
				$total_valor_calculo_final = 0;
				$array_valores_operacion_compuesta = array();
				
				foreach($array_operacion_compuesta as $index => $operacion_compuesta){
					
					$operador = key($operacion_compuesta);
					$id_valor = $operacion_compuesta[key($operacion_compuesta)];

					$valor_calculo = $this->KPI_Values_model->get_one($id_valor);
					$valores_condicion_calculo = $this->KPI_Values_condition_model->get_all_where(array(
						"id_kpi_valores" => $valor_calculo->id,
						"deleted" => 0
					))->result_array();
					$formulario_valor_calculo = $this->Forms_model->get_one($valor_calculo->id_formulario);
					
					if(!$formulario_valor_calculo->fijo){
						$formulario_rel_proyecto = $this->Form_rel_project_model->get_one_where(array(
							"id_formulario" => $formulario_valor_calculo->id,
							"deleted" => 0
						));
						$elementos_formulario_calculo = $this->Form_values_model->get_all_where(array(
							"id_formulario_rel_proyecto" => $formulario_rel_proyecto->id,
							"deleted" => 0
						))->result_array();
					} else {
						$elementos_formulario_calculo = $this->Fixed_form_values_model->get_all_where(array(
							"id_formulario" => $formulario_valor_calculo->id,
							"deleted" => 0
						))->result_array();
					}
					
					$total_valor_calculo = 0;
					
					if($valor_calculo->id && count($valores_condicion_calculo)) {
						
						$array_datos_valores_condicion_calculo = array();
						
						foreach($valores_condicion_calculo as $valor_condicion_calculo){
							
							$valor_condicion_categoria = ($valor_condicion_calculo["is_category"]) ? $valor_condicion_calculo["valor"] : NULL;
							$valor_condicion_tipo_tratamiento = ($valor_condicion_calculo["is_tipo_tratamiento"]) ? $valor_condicion_calculo["valor"] : NULL;
							$valor_condicion_id_campo = ($valor_condicion_calculo["id_campo"]) ? $valor_condicion_calculo["valor"] : NULL;
							$valor_condicion_id_campo_fijo = ($valor_condicion_calculo["id_campo_fijo"]) ? $valor_condicion_calculo["valor"] : NULL;
							
							if($valor_condicion_categoria){
								$array_datos_valores_condicion_calculo["id_categoria"] = $valor_condicion_categoria;
							}
							if($valor_condicion_tipo_tratamiento){
								$array_datos_valores_condicion_calculo["tipo_tratamiento"] = $valor_condicion_tipo_tratamiento;
							}
							if($valor_condicion_id_campo){
								$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo"]] = $valor_condicion_id_campo;
							}
							if($valor_condicion_id_campo_fijo){
								$array_datos_valores_condicion_calculo[$valor_condicion_calculo["id_campo_fijo"]] = $valor_condicion_id_campo_fijo;
							}
	
						}
						
					}
					
					if(count($elementos_formulario_calculo)){
	
						foreach($elementos_formulario_calculo as $elemento){
					
							$datos = json_decode($elemento["datos"], TRUE);
							$fecha_elemento = $datos["fecha"];
	
							$elemento_campos_dinamicos = array();
							foreach($datos as $key => $value){
								if(array_key_exists($key, $array_datos_valores_condicion_calculo)){
									$elemento_campos_dinamicos[$key] = $datos[$key];
								}
							}
							
							if($array_datos_valores_condicion_calculo == $elemento_campos_dinamicos){
								
								if($fecha_elemento >= $fecha_desde && $fecha_elemento <= $fecha_hasta){
									$campo_indicador = $valor_calculo->id_campo_unidad;
									if($campo_indicador == "0"){ // Si el campo indicador es la unidad fija del formulario
										$valor_unidad_fija = $datos["unidad_residuo"];
										$total_valor_calculo = $total_valor_calculo + $valor_unidad_fija;
									} else {
										$valor_unidad_dinamica = $datos[$campo_indicador]; 
										$total_valor_calculo = $total_valor_calculo + $valor_unidad_dinamica;
									}
								}	
	
							}

						}
	
					}
					
					if($valor_calculo->operador){
					
						$valor_operador = $valor_calculo->valor_operador;
						
						if($valor_calculo->operador == "+"){
							$total_valor_calculo = $total_valor_calculo + $valor_operador;
						}
						if($valor_calculo->operador == "-"){
							$total_valor_calculo = $total_valor_calculo - $valor_operador;
						}
						if($valor_calculo->operador == "*"){
							$total_valor_calculo = $total_valor_calculo * $valor_operador;
						}
						if($valor_calculo->operador == "/"){
							$total_valor_calculo = $total_valor_calculo / $valor_operador;
						}
						
					}
					
					if($operador){
						
						$array_valores_operacion_compuesta[] = array(
							$operador => $total_valor_calculo
						);

					}
					
				}
				// Fin Cálculo valores operación (cada valor se almacena en $array_valores_operacion_compuesta
				
				// Cálculo total final
				$total_valor_calculo_final = $total_valor_inicial;
				
				foreach($array_valores_operacion_compuesta as $valor_operacion_compuesta){
					
					$operador = key($valor_operacion_compuesta);
					$total_valor_calculo = $valor_operacion_compuesta[key($valor_operacion_compuesta)];
					
					if($operador == "+"){
						$total_valor_calculo_final = $total_valor_calculo_final + $total_valor_calculo;
					}
					if($operador == "-"){
						$total_valor_calculo_final = $total_valor_calculo_final - $total_valor_calculo;
					}
					if($operador == "*"){
						$total_valor_calculo_final = $total_valor_calculo_final * $total_valor_calculo;
					}
					if($operador == "/"){
						$total_valor_calculo_final = $total_valor_calculo_final / $total_valor_calculo;
					}

				}
				
				$total_valor = $total_valor_calculo_final;
				// Fin Cálculo total final
				
				$id_unidad_valor = $valor->id_unidad;
				$id_tipo_unidad_valor = $valor->id_tipo_unidad;
				$id_unidad_indicador = $datos_indicador["unidad"];
				
				// transformar valores en reporte, que tengan una unidad distinta a la unidad del indicador.
				// si el valor tiene una unidad distinta a la del indicador, transformar a la unidad del indicador.
				if($id_unidad_valor != $id_unidad_indicador){
					if($id_unidad_indicador != 18){ // Unidad de tipo unidad (id 18) no tiene conversión
						$fila_conversion = $this->Conversion_model->get_one_where(
							array(
								"id_tipo_unidad" => $id_tipo_unidad_valor,
								"id_unidad_origen" => $id_unidad_valor,
								"id_unidad_destino" => $id_unidad_indicador
							)
						);
						$valor_transformacion = $fila_conversion->transformacion;
						$total_valor = $total_valor * $valor_transformacion;
					}
				} 
				
			}
			
			$unidad = $this->Unity_model->get_one($datos_indicador["unidad"]);
			$unidad_config = $this->Reports_units_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "id_tipo_unidad" => $unidad->id_tipo_unidad));
			$unidad_client_config = $this->Unity_model->get_one($unidad_config->id_unidad);
			
			$nombre_unidad = "";
			if($nombre_indicador == "operating_hours" || $nombre_indicador == "enel_hours_worked" 
			|| $nombre_indicador == "contractor_hours_worked" || $nombre_indicador == "enel_lost_days"
			|| $nombre_indicador == "contractor_lost_days"){
				$nombre_unidad = $unidad_client_config->nombre_real;
			} else if($nombre_indicador == "expenses_local_suppliers" || $nombre_indicador == "opex_total"
						|| $nombre_indicador == "environmental_expenses"){
				$nombre_unidad = "€";
			} else if($nombre_indicador == "noise_levels_near_population"){
				$nombre_unidad = "db";
			} else {
				$nombre_unidad = $unidad_client_config->nombre;
			}
			
			if($valor->id){
				$valor = $total_valor;
			} else {
				$valor = $datos_plantilla[$nombre_indicador]["valor_cliente"];
			}
			
			$result[] = array(
				$datos_indicador["codigo"],
				lang($nombre_indicador),
				$valor ? to_number_client_format($valor, $id_cliente) : '-',
				$nombre_unidad,
				$datos_indicador["descripcion"]
			);
			
		}
		
		$this->load->library('excel');		
		
		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("Mimasoft")
							 ->setLastModifiedBy("Mimasoft")
							 ->setTitle("")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("mimasoft")
							 ->setCategory("excel");
		
		if($client_info->color_sitio){
			$color_sitio = str_replace('#', '', $client_info->color_sitio);
		} else {
			$color_sitio = "00b393";
		}
		
		// ESTILOS
		$styleArray = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'rotation' => 90,
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => $color_sitio)
			),
		);
		
		// LOGO
		if($client_info->id){
			if($client_info->logo){
				$url_logo = "files/mimasoft_files/client_".$client_info->id."/".$client_info->logo.".png";
			} else {
				$url_logo = "files/system/default-site-logo.png";
			}
		} else {
			$url_logo = "files/system/default-site-logo.png";
		}
		
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('./'.$url_logo);
		$objDrawing->setHeight(35);
		$objDrawing->setOffsetY(6);
		$objDrawing->setOffsetX(20);
		$objDrawing->setWorksheet($doc->getActiveSheet());
		$doc->getActiveSheet()->mergeCells('A1:B3');
		$doc->getActiveSheet()->getStyle('A1:B3')->applyFromArray($styleArray);
		
		$nombre_columnas = array();
		$nombre_columnas[] = array("nombre_columna" => lang("code"), "id_tipo_campo" => "code");
		$nombre_columnas[] = array("nombre_columna" => lang("name"), "id_tipo_campo" => "name");
		$nombre_columnas[] = array("nombre_columna" => lang("value"), "id_tipo_campo" => "value");
		$nombre_columnas[] = array("nombre_columna" => lang("unit"), "id_tipo_campo" => "unit");
		$nombre_columnas[] = array("nombre_columna" => lang("description"), "id_tipo_campo" => "description");

		// HEADER
		$hoy = date('d-m-Y');
		$fecha = date(get_setting_client_mimasoft($client_info->id, "date_format"), strtotime($hoy));
		$hora = format_to_time_clients($client_info->id, get_current_utc_time("H:i:s"));
		
		$letra = $this->getNameFromNumber(count($nombre_columnas)-1);
		$doc->getActiveSheet()->getStyle('A5:'.$letra.'5')->applyFromArray($styleArray);
		$doc->setActiveSheetIndex(0)
            ->setCellValue('C1', lang("kpi_report"))
			->setCellValue('C2', $client_info->company_name)
            ->setCellValue('C3', 'Fecha: '.$fecha.' a las '.$hora);
					
		$doc->setActiveSheetIndex(0);	
		
		// SETEO DE CABECERAS DE CONTENIDO A LA HOJA DE EXCEL
		//$doc->getActiveSheet()->fromArray($nombre_columnas, NULL,"A5");
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		foreach($nombre_columnas as $index => $columna){
			$valor = (!is_array($columna)) ? $columna : $columna["nombre_columna"];
			$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row = 5, $valor);
			$col++;
		}
		
		// CARGA DE CONTENIDO A LA HOJA DE EXCEL
		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$row = 6; // EMPEZANDO DE LA FILA 6 
		foreach($result as $res){

			foreach($nombre_columnas as $index_columnas => $columna){
				
				$name_col = PHPExcel_Cell::stringFromColumnIndex($col);
				$valor = $res[$index_columnas];
				
				if(!is_array($columna)){
					
					$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
					
				} else {
					
					if($columna["id_tipo_campo"] == "code" || $columna["id_tipo_campo"] == "name"
					|| $columna["id_tipo_campo"] == "unit" || $columna["id_tipo_campo"] == "description"){
					
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);

						
					} elseif($columna["id_tipo_campo"] == "value"){
					
						$doc->getActiveSheet()->setCellValueExplicitByColumnAndRow($col, $row, $valor);
						$style = array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
							)
						);
						$doc->getActiveSheet()->getStyle($name_col.$row)->applyFromArray($style);
					
					} else {
						
						$doc->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valor);
						
					}
	
				}
				
				$doc->getActiveSheet()->getStyle($name_col.$row)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
				$col++;
			}
			
			$col = 0;
			$row++;

		}
		//$doc->getActiveSheet()->fromArray($result, NULL,"A6");
		
		// FILTROS
		$doc->getActiveSheet()->setAutoFilter('A5:'.$letra.'5');
		
		// ANCHO COLUMNAS
		$lastColumn = $doc->getActiveSheet()->getHighestColumn();	
		$lastColumn++;
		$cells = array();
		for($column = 'A'; $column != $lastColumn; $column++) {
			$cells[] = $column;	
		}
		foreach($cells as $cell){
			$doc->getActiveSheet()->getColumnDimension($cell)->setAutoSize(true);
		}
		
		$doc->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
		$doc->getActiveSheet()->getColumnDimension('B')->setWidth('40');
		$doc->getActiveSheet()->getStyle('B6:B'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
		
		$doc->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
		$doc->getActiveSheet()->getColumnDimension('C')->setWidth('15');
		$doc->getActiveSheet()->getStyle('C6:C'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
		
		$doc->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
		$doc->getActiveSheet()->getColumnDimension('D')->setWidth('15');
		$doc->getActiveSheet()->getStyle('D6:D'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
		
		$doc->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
		$doc->getActiveSheet()->getColumnDimension('E')->setWidth('100');
		$doc->getActiveSheet()->getStyle('E6:E'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
		
		//$doc->getActiveSheet()->getStyle('C6:C'.$doc->getActiveSheet()->getHighestRow())->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$doc->getActiveSheet()->getProtection()->setPassword('ulearn1324');
		$doc->getActiveSheet()->getProtection()->setSheet(true);
		
		// FORMATO TEXTO A TODAS LAS CELDAS DE CONTENIDO
		//$doc->getActiveSheet()->getStyle('A6:AB'.$doc->getActiveSheet()->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		//$doc->getActiveSheet()->setCellValueExplicit('Z6', '30.45', PHPExcel_Cell_DataType::TYPE_STRING);
		
		$nombre_hoja = strlen(lang("kpi_report")) > 31 ? substr(lang("kpi_report"), 0, 28).'...' : lang("kpi_report");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);
		
		$filename = $client_info->sigla."_".lang("kpi_report")."_".date('Y-m-d');
		$filename = $filename.'.xlsx'; //save our workbook as this file name
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		
		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');  
		$objWriter->save('php://output');
		exit;
		
	}
	
	function getNameFromNumber($num){
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return (string)$letter;
		}
	}	
	
}