<?php

/**
 * Archivo Controlador para Pronosticos por Sectores (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Pronosticos
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Controlador para Pronosticos por Sectores (módulo nivel Cliente / Proyecto)
 * 
 * @package MIMAire
 * @subpackage Controllers
 * @category Pronosticos
 * @property private $id_modulo_cliente id del módulo Pronóstico (14)
 * @property private $id_submodulo_cliente id del submódulo Sectores (28)
 * @author Gustavo Pinochet Altamirano
 * @version 1.0
 */
class Air_forecast_sectors_historico extends MY_Controller
{

	/**
	 * id_modulo_cliente
	 * @var int $id_modulo_cliente
	 */
	private $id_modulo_cliente;
	/**
	 * id_submodulo_cliente
	 * @var int $id_submodulo_cliente
	 */
	private $id_submodulo_cliente;

	/**
	 * __construct
	 * 
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->init_permission_checker("client");

		$this->id_modulo_cliente = 14;
		$this->id_submodulo_cliente = 28;

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		if ($id_proyecto) {
			$this->block_url($id_cliente, $id_proyecto, $this->id_modulo_cliente);
		}
	}

	/**
	 * index
	 * 
	 * Carga datos iniciales de pronóstico de las variables de Calidad del aire y Meteorológica,
	 * y sus configuraciones asociadas.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param string $id_sector id del sector encriptado
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión 
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario 
	 * @return resource Vista principal del módulo
	 */
	function index($id_sector)
	{

		ini_set("memory_limit", "-1");

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$view_data["user"] = $this->Users_model->get_one($this->login_user->id);

		$view_data["puede_ver"] = $this->profile_access($this->session->user_id, $this->id_modulo_cliente, $this->id_submodulo_cliente, "ver");

		### GENERAR REGISTRO EN LOGS_MODEL ###
		$this->Logs_model->add_log($this->login_user->client_id, NULL, NULL, NULL, 'Access_forecast');

		// DESENCRIPTADO DE $id_sector
		//$id_sector = $this->input->get("p");
		//$id_sector_decrypt = rawurldecode($this->encrypt->decode($id_sector));
		$id_sector_decrypt = rawurldecode($this->encrypt->decode(rawurldecode($id_sector)));

		$air_sector = $this->Air_sectors_model->get_one($id_sector_decrypt); # trae minera los pelambres
		$view_data["sector_info"] = $air_sector;

		$project = $this->Projects_model->get_one($air_sector->id_project);
		$view_data["project_info"] = $project;

		// IDs DE LOS MODELOS ASOCIADOS AL SECTOR
		$id_models_of_sector = json_decode($air_sector->air_models);
		$view_data["id_models_of_sector"] = $id_models_of_sector;

		// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
		$input_date = $this->input->post('fecha');
		$first_datetime = $input_date ? new DateTime($input_date) : new DateTime(convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context));
		$first_datetime->setTime(0, 0, 0);
		//$first_datetime = $first_datetime->modify('-24 hours');
		$first_datetime = $first_datetime->format("Y-m-d H:i");

		$last_datetime = new DateTime($first_datetime);
		// $last_datetime = $last_datetime->modify('+71 hours');
		$last_datetime = $last_datetime->modify('+23 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");

		// FECHAS Y HORAS PARA LAYER TIMEDIMENSION DE MAPA (MODELO NUMÉRICO)
		$first_date_map = date("Y-m-d", strtotime($first_datetime)); // 2024-05-16 dia
		$last_date_map = date("Y-m-d", strtotime($last_datetime)); // 2024-05-16 dia
		$first_time_map = date("H", strtotime($first_datetime)); // 00 comienzo hora
		$last_time_map = date("H", strtotime($last_datetime)); // 23 termino hora

		// $view_data["first_datetime"] = $first_datetime;
		$view_data["first_date_map"] = $first_date_map;
		$view_data["last_date_map"] = $last_date_map;
		$view_data["first_time_map"] = $first_time_map;
		$view_data["last_time_map"] = $last_time_map;


		/* SECCIÓN MODELO NUMÉRICO */

		if (in_array(3, $id_models_of_sector)) {

			/* MAPA */
			// FILTRO "VARIABLES CALIDAD DEL AIRE" (VARIABLES DE TIPO "CALIDAD DEL AIRE" DEL SECTOR) PARA MAPA
			$air_quality_variables_dropdown_map = array("" => "-");
			$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 2))->result();
			foreach ($sector_variables as $variable) {
				if ($variable->id_variable != 9) {
					continue;
				} // MP10
				$air_quality_variables_dropdown_map[$variable->id_variable] = $variable->variable_name;
			}
			$view_data["air_quality_variables_dropdown_map"] = $air_quality_variables_dropdown_map;

			// FILTRO "VARIABLES CALIDAD DEL AIRE" (VARIABLES DE TIPO "CALIDAD DEL AIRE" DEL SECTOR) PARA GRÁFICOS
			$air_quality_variables_dropdown = array("" => "-");
			$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 2))->result();
			foreach ($sector_variables as $variable) {
				$air_quality_variables_dropdown[$variable->id_variable] = $variable->variable_name;
			}
			$view_data["air_quality_variables_dropdown"] = $air_quality_variables_dropdown;



			// FILTRO "VARIABLES METEOROLÓGICAS" (VARIABLES DE TIPO "METEOROLÓGICA" DEL SECTOR) PARA MAPA
			$meteorological_variables_dropdown_map = array("" => "-");
			$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 1))->result();
			foreach ($sector_variables as $variable) {
				if ($variable->id_variable != 1 && $variable->id_variable != 2) {
					continue;
				} // VEL Y DIR VIENTO
				$meteorological_variables_dropdown_map[$variable->id_variable] = $variable->variable_name;
			}
			$view_data["meteorological_variables_dropdown_map"] = $meteorological_variables_dropdown_map;

			// FILTRO "VARIABLES METEOROLÓGICAS" (VARIABLES DE TIPO "METEOROLÓGICA" DEL SECTOR) PARA GRÁFICOS
			$meteorological_variables_dropdown = array("" => "-");
			$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 1))->result();
			foreach ($sector_variables as $variable) {
				$meteorological_variables_dropdown[$variable->id_variable] = $variable->variable_name;
			}
			$view_data["meteorological_variables_dropdown"] = $meteorological_variables_dropdown;



			// FILTRO DE "RECEPTORES DEL SECTOR"
			$receptors = $this->Air_stations_model->get_all_where(
				array(
					"id_air_sector" => $air_sector->id,
					"is_active" => 1,
					"is_forecast" => 1,
					//"is_receptor" => 1,
					"deleted" => 0
				)
			)->result();

			$receptors_dropdown = array("" => "-");
			foreach ($receptors as $receptor) {
				$receptors_dropdown[$receptor->id] = $receptor->name;
			}
			$view_data["receptors"] = $receptors;
			$view_data["receptors_dropdown"] = $receptors_dropdown;

			// SI EL SECTOR TIENE LA VARIABLE PM10 (ID 9), SE CARGAN LOS DATOS INICIALES DE PM10,
			// SI NO, SE CARGAN LOS DATOS INICIALES CON LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES "CALIDAD DEL AIRE"
			$id_air_quality_variable = (array_key_exists(9, $air_quality_variables_dropdown)) ? 9 : array_keys($air_quality_variables_dropdown)[1];
			$air_quality_variable = ($id_air_quality_variable) ? $this->Air_variables_model->get_details(array("id" => $id_air_quality_variable))->row() : null;

			// SE CARGAN LOS DATOS INICIALES CON LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES "METEOROLÓGICAS"
			$id_meteorological_variable = array_keys($meteorological_variables_dropdown)[1];
			$meteorological_variable = ($id_meteorological_variable) ? $this->Air_variables_model->get_details(array("id" => $id_meteorological_variable))->row() : null;

			// CONFIGURACIÓN DE UNIDADES DE REPORTE DE VARIABLE INICIAL "CALIDAD DEL AIRE"
			$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $air_quality_variable->id_unit_type,
					"deleted" => 0
				)
			)->id_unidad;
			$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
			$view_data["unit_qual"] = $unit_qual;
			$view_data["unit_type_qual"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;

			// CONFIGURACIÓN DE UNIDADES DE REPORTE DE VARIABLE INICIAL "METEOROLÓGICA"
			$id_report_unit_setting_meteo = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $meteorological_variable->id_unit_type,
					"deleted" => 0
				)
			)->id_unidad;
			$unit_meteo = $this->Unity_model->get_one($id_report_unit_setting_meteo);
			$view_data["unit_meteo"] = $unit_meteo;
			$view_data["unit_type_meteo"] = $this->Unity_type_model->get_one($unit_meteo->id_tipo_unidad)->nombre;


			// SI HAY VARIABLE INICIAL DE TIPO "CALIDAD DEL AIRE"
			if ($air_quality_variable->id) {
				// VALOR MÁXIMO DE LA VARIABLE EN EL SECTOR, DE LA FECHA Y HORA ACTUAL (PARA CÁLCULO DE GRADIENTES DE CAPA HEATMAP)
				// $current_qual_max_value = $this->Air_records_values_p_model->get_current_max_variable_value(array(
				// 	"id_variable" => $air_quality_variable->id,
				// 	"id_sector" => $air_sector->id,
				// 	"date" => $first_date_map,
				// 	"time" => "time_".$first_time_map
				// ))->row()->max_value;
				$current_qual_max_value = 0;

				$view_data["air_quality_variable"] = $air_quality_variable;

				//TODO: mirar para que se usa
				$qual_values_p = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => $air_quality_variable->id,
						"id_sector" => $air_sector->id,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result(); // busca datos por fechas
				// log para comprobar otros datos
	
				$qual_values_p_last_upload = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => $air_quality_variable->id,
						"id_sector" => $air_sector->id,
						"last_upload" => true,
						"first_date" => $first_date_map,
						//"last_date" => $last_date_map
					)
				)->result(); // busca datos por ultima subida
			
				$first_date_last_upload = $qual_values_p_last_upload[0]->date;
				$last_date_last_upload = $qual_values_p_last_upload[count($qual_values_p_last_upload) - 1]->date;

				if ($first_date_last_upload) {
					if ($first_date_last_upload < $first_date_map) {
						$qual_values_p = $qual_values_p_last_upload;
					}
				}

				// DATOS DEL MAPA (REGISTROS DEL SECTOR / VARIABLE, COORDENADAS Y VALORES)
				$array_qual_data_values_p = array();
				foreach ($qual_values_p as $value_p) {
					$array_qual_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
						"time_00" => $value_p->time_00 < 15 ? 15 : $value_p->time_00,
						"time_01" => $value_p->time_01 < 15 ? 15 : $value_p->time_01,
						"time_02" => $value_p->time_02 < 15 ? 15 : $value_p->time_02,
						"time_03" => $value_p->time_03 < 15 ? 15 : $value_p->time_03,
						"time_04" => $value_p->time_04 < 15 ? 15 : $value_p->time_04,
						"time_05" => $value_p->time_05 < 15 ? 15 : $value_p->time_05,
						"time_06" => $value_p->time_06 < 15 ? 15 : $value_p->time_06,
						"time_07" => $value_p->time_07 < 15 ? 15 : $value_p->time_07,
						"time_08" => $value_p->time_08 < 15 ? 15 : $value_p->time_08,
						"time_09" => $value_p->time_09 < 15 ? 15 : $value_p->time_09,
						"time_10" => $value_p->time_10 < 15 ? 15 : $value_p->time_10,
						"time_11" => $value_p->time_11 < 15 ? 15 : $value_p->time_11,
						"time_12" => $value_p->time_12 < 15 ? 15 : $value_p->time_12,
						"time_13" => $value_p->time_13 < 15 ? 15 : $value_p->time_13,
						"time_14" => $value_p->time_14 < 15 ? 15 : $value_p->time_14,
						"time_15" => $value_p->time_15 < 15 ? 15 : $value_p->time_15,
						"time_16" => $value_p->time_16 < 15 ? 15 : $value_p->time_16,
						"time_17" => $value_p->time_17 < 15 ? 15 : $value_p->time_17,
						"time_18" => $value_p->time_18 < 15 ? 15 : $value_p->time_18,
						"time_19" => $value_p->time_19 < 15 ? 15 : $value_p->time_19,
						"time_20" => $value_p->time_20 < 15 ? 15 : $value_p->time_20,
						"time_21" => $value_p->time_21 < 15 ? 15 : $value_p->time_21,
						"time_22" => $value_p->time_22 < 15 ? 15 : $value_p->time_22,
						"time_23" => $value_p->time_23 < 15 ? 15 : $value_p->time_23,
					);
				}

				$view_data["air_quality_variable"] = $air_quality_variable;
				$view_data["array_qual_data_values_p"] = $array_qual_data_values_p;

				$array_current_qual_max_value = array();
				foreach ($array_qual_data_values_p[$first_date_map] as $value) {
					$array_current_qual_max_value[] = $value["time_" . $first_time_map];
				}
				$current_qual_max_value = max($array_current_qual_max_value);
			}

			if ($meteorological_variable->id) {

				$view_data["meteorological_variable"] = $meteorological_variable;

				// DATOS DEL MAPA (REGISTROS DEL SECTOR / VARIABLE, COORDENADAS Y VALORES)
				$array_meteo_data_values_p = array();

				if ($meteorological_variable->id == 1) { // VELOCIDAD DEL VIENTO

					$id_report_unit_setting_meteo = $this->Reports_units_settings_model->get_one_where(
						array(
							"id_cliente" => $id_cliente,
							"id_proyecto" => $id_proyecto,
							"id_tipo_unidad" => $meteorological_variable->id_unit_type,
							"deleted" => 0
						)
					)->id_unidad;
					$unit_meteo = $this->Unity_model->get_one($id_report_unit_setting_meteo);
					$view_data["unit_meteo_vel"] = $unit_meteo;

					$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							//"last_upload" => true,
							"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();

					$meteo_values_p_last_upload = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							"last_upload" => true,
						)
					)->result();


					$first_date_last_upload_vel = $meteo_values_p_last_upload[0]->date;
					$last_date_last_upload_vel = $meteo_values_p_last_upload[count($meteo_values_p_last_upload) - 1]->date;


					$meteo_values_p_dir = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => 2, // Dirección del viento
							"id_sector" => $air_sector->id,
							//"last_upload" => true,
							"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();

					$meteo_values_p_dir_last_upload = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => 2, // Dirección del viento
							"id_sector" => $air_sector->id,
							"last_upload" => true,
						)
					)->result();

					$first_date_last_upload_dir = $meteo_values_p_dir_last_upload[0]->date;
					$last_date_last_upload_dir = $meteo_values_p_dir_last_upload[count($meteo_values_p_dir_last_upload) - 1]->date;


					if ($first_date_last_upload_vel && $first_date_last_upload_dir) {

						$first_date_last_upload = ($first_date_last_upload_vel <= $first_date_last_upload_dir) ? $first_date_last_upload_vel : $first_date_last_upload_dir;
						$last_date_last_upload = ($first_date_last_upload_vel <= $first_date_last_upload_dir) ? $last_date_last_upload_vel : $last_date_last_upload_dir;

						if ($first_date_last_upload < $first_date_map) {
							$meteo_values_p = $meteo_values_p_last_upload;
							$meteo_values_p_dir = $meteo_values_p_dir_last_upload;
						}
					}

					$array_meteo_data_values_p_dir = array();
					foreach ($meteo_values_p_dir as $value_p_dir) {
						$array_meteo_data_values_p_dir[$value_p_dir->date][$value_p_dir->latitude . ":" . $value_p_dir->longitude] = array(
							"time_00" => $value_p_dir->time_00,
							"time_01" => $value_p_dir->time_01,
							"time_02" => $value_p_dir->time_02,
							"time_03" => $value_p_dir->time_03,
							"time_04" => $value_p_dir->time_04,
							"time_05" => $value_p_dir->time_05,
							"time_06" => $value_p_dir->time_06,
							"time_07" => $value_p_dir->time_07,
							"time_08" => $value_p_dir->time_08,
							"time_09" => $value_p_dir->time_09,
							"time_10" => $value_p_dir->time_10,
							"time_11" => $value_p_dir->time_11,
							"time_12" => $value_p_dir->time_12,
							"time_13" => $value_p_dir->time_13,
							"time_14" => $value_p_dir->time_14,
							"time_15" => $value_p_dir->time_15,
							"time_16" => $value_p_dir->time_16,
							"time_17" => $value_p_dir->time_17,
							"time_18" => $value_p_dir->time_18,
							"time_19" => $value_p_dir->time_19,
							"time_20" => $value_p_dir->time_20,
							"time_21" => $value_p_dir->time_21,
							"time_22" => $value_p_dir->time_22,
							"time_23" => $value_p_dir->time_23,
						);
					}

					foreach ($meteo_values_p as $value_p) {
						$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
							"time_00" => array("velocity" => $value_p->time_00, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_00"]),
							"time_01" => array("velocity" => $value_p->time_01, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_01"]),
							"time_02" => array("velocity" => $value_p->time_02, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_02"]),
							"time_03" => array("velocity" => $value_p->time_03, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_03"]),
							"time_04" => array("velocity" => $value_p->time_04, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_04"]),
							"time_05" => array("velocity" => $value_p->time_05, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_05"]),
							"time_06" => array("velocity" => $value_p->time_06, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_06"]),
							"time_07" => array("velocity" => $value_p->time_07, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_07"]),
							"time_08" => array("velocity" => $value_p->time_08, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_08"]),
							"time_09" => array("velocity" => $value_p->time_09, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_09"]),
							"time_10" => array("velocity" => $value_p->time_10, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_10"]),
							"time_11" => array("velocity" => $value_p->time_11, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_11"]),
							"time_12" => array("velocity" => $value_p->time_12, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_12"]),
							"time_13" => array("velocity" => $value_p->time_13, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_13"]),
							"time_14" => array("velocity" => $value_p->time_14, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_14"]),
							"time_15" => array("velocity" => $value_p->time_15, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_15"]),
							"time_16" => array("velocity" => $value_p->time_16, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_16"]),
							"time_17" => array("velocity" => $value_p->time_17, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_17"]),
							"time_18" => array("velocity" => $value_p->time_18, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_18"]),
							"time_19" => array("velocity" => $value_p->time_19, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_19"]),
							"time_20" => array("velocity" => $value_p->time_20, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_20"]),
							"time_21" => array("velocity" => $value_p->time_21, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_21"]),
							"time_22" => array("velocity" => $value_p->time_22, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_22"]),
							"time_23" => array("velocity" => $value_p->time_23, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_23"]),
						);
					}

					// SOLO SI HAY DATOS PARA AMBAS VARIABLES, SE CARGAN SUS DATOS PARA EL MAPA
					$view_data["array_meteo_data_values_p"] = (count($meteo_values_p) && count($meteo_values_p_dir)) ? $array_meteo_data_values_p : array();
				} elseif ($meteorological_variable->id == 2) { // DIRECCIÓN DEL VIENTO

					$id_report_unit_setting_meteo = $this->Reports_units_settings_model->get_one_where(
						array(
							"id_cliente" => $id_cliente,
							"id_proyecto" => $id_proyecto,
							"id_tipo_unidad" => $meteorological_variable->id_unit_type,
							"deleted" => 0
						)
					)->id_unidad;
					$unit_meteo = $this->Unity_model->get_one($id_report_unit_setting_meteo);
					$view_data["unit_meteo_dir"] = $unit_meteo;

					$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							//"last_upload" => true,
							"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();

					$meteo_values_p_last_upload = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							"last_upload" => true,
						)
					)->result();


					$first_date_last_upload_dir = $meteo_values_p_last_upload[0]->date;
					$last_date_last_upload_dir = $meteo_values_p_last_upload[count($meteo_values_p_last_upload) - 1]->date;


					$meteo_values_p_vel = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => 1, // Velocidad del viento
							"id_sector" => $air_sector->id,
							//"last_upload" => true,
							"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();

					$meteo_values_p_vel_last_upload = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => 1, // Velocidad del viento
							"id_sector" => $air_sector->id,
							"last_upload" => true,
						)
					)->result();


					$first_date_last_upload_vel = $meteo_values_p_vel_last_upload[0]->date;
					$last_date_last_upload_vel = $meteo_values_p_vel_last_upload[count($meteo_values_p_vel_last_upload) - 1]->date;


					if ($first_date_last_upload_vel && $first_date_last_upload_dir) {

						$first_date_last_upload = ($first_date_last_upload_vel <= $first_date_last_upload_dir) ? $first_date_last_upload_vel : $first_date_last_upload_dir;
						$last_date_last_upload = ($first_date_last_upload_vel <= $first_date_last_upload_dir) ? $last_date_last_upload_vel : $last_date_last_upload_dir;

						if ($first_date_last_upload < $first_date_map) {
							$meteo_values_p = $meteo_values_p_last_upload;
							$meteo_values_p_dir = $meteo_values_p_dir_last_upload;
						}
					}

					$array_meteo_data_values_p_vel = array();
					foreach ($meteo_values_p_vel as $value_p_vel) {
						$array_meteo_data_values_p_vel[$value_p_vel->date][$value_p_vel->latitude . ":" . $value_p_vel->longitude] = array(
							"time_00" => $value_p_vel->time_00,
							"time_01" => $value_p_vel->time_01,
							"time_02" => $value_p_vel->time_02,
							"time_03" => $value_p_vel->time_03,
							"time_04" => $value_p_vel->time_04,
							"time_05" => $value_p_vel->time_05,
							"time_06" => $value_p_vel->time_06,
							"time_07" => $value_p_vel->time_07,
							"time_08" => $value_p_vel->time_08,
							"time_09" => $value_p_vel->time_09,
							"time_10" => $value_p_vel->time_10,
							"time_11" => $value_p_vel->time_11,
							"time_12" => $value_p_vel->time_12,
							"time_13" => $value_p_vel->time_13,
							"time_14" => $value_p_vel->time_14,
							"time_15" => $value_p_vel->time_15,
							"time_16" => $value_p_vel->time_16,
							"time_17" => $value_p_vel->time_17,
							"time_18" => $value_p_vel->time_18,
							"time_19" => $value_p_vel->time_19,
							"time_20" => $value_p_vel->time_20,
							"time_21" => $value_p_vel->time_21,
							"time_22" => $value_p_vel->time_22,
							"time_23" => $value_p_vel->time_23,
						);
					}

					foreach ($meteo_values_p as $value_p) {
						$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
							"time_00" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_00"], "direction" => $value_p->time_00),
							"time_01" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_01"], "direction" => $value_p->time_01),
							"time_02" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_02"], "direction" => $value_p->time_02),
							"time_03" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_03"], "direction" => $value_p->time_03),
							"time_04" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_04"], "direction" => $value_p->time_04),
							"time_05" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_05"], "direction" => $value_p->time_05),
							"time_06" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_06"], "direction" => $value_p->time_06),
							"time_07" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_07"], "direction" => $value_p->time_07),
							"time_08" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_08"], "direction" => $value_p->time_08),
							"time_09" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_09"], "direction" => $value_p->time_09),
							"time_10" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_10"], "direction" => $value_p->time_10),
							"time_11" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_11"], "direction" => $value_p->time_11),
							"time_12" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_12"], "direction" => $value_p->time_12),
							"time_13" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_13"], "direction" => $value_p->time_13),
							"time_14" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_14"], "direction" => $value_p->time_14),
							"time_15" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_15"], "direction" => $value_p->time_15),
							"time_16" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_16"], "direction" => $value_p->time_16),
							"time_17" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_17"], "direction" => $value_p->time_17),
							"time_18" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_18"], "direction" => $value_p->time_18),
							"time_19" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_19"], "direction" => $value_p->time_19),
							"time_20" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_20"], "direction" => $value_p->time_20),
							"time_21" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_21"], "direction" => $value_p->time_21),
							"time_22" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_22"], "direction" => $value_p->time_22),
							"time_23" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_23"], "direction" => $value_p->time_23),
						);
					}

					// SOLO SI HAY DATOS PARA AMBAS VARIABLES, SE CARGAN SUS DATOS PARA EL MAPA
					$view_data["array_meteo_data_values_p"] = (count($meteo_values_p) && count($meteo_values_p_vel)) ? $array_meteo_data_values_p : array();
				} else { // OTRAS VARIABLES METEOROLÓGICAS

					$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							//"last_upload" => true,
							"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();

					$meteo_values_p_last_upload = $this->Air_records_values_p_model->get_values_details(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_sector" => $air_sector->id,
							"last_upload" => true,
							//"first_date" => $first_date_map,
							//"last_date" => $last_date_map
						)
					)->result();


					$first_date_last_upload = $meteo_values_p_last_upload[0]->date;
					$last_date_last_upload = $meteo_values_p_last_upload[count($meteo_values_p_last_upload) - 1]->date;


					if ($first_date_last_upload) {
						if ($first_date_last_upload < $first_date_map) {
							$meteo_values_p = $meteo_values_p_last_upload;
						}
					}

					foreach ($meteo_values_p as $value_p) {
						$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
							"time_00" => $value_p->time_00,
							"time_01" => $value_p->time_01,
							"time_02" => $value_p->time_02,
							"time_03" => $value_p->time_03,
							"time_04" => $value_p->time_04,
							"time_05" => $value_p->time_05,
							"time_06" => $value_p->time_06,
							"time_07" => $value_p->time_07,
							"time_08" => $value_p->time_08,
							"time_09" => $value_p->time_09,
							"time_10" => $value_p->time_10,
							"time_11" => $value_p->time_11,
							"time_12" => $value_p->time_12,
							"time_13" => $value_p->time_13,
							"time_14" => $value_p->time_14,
							"time_15" => $value_p->time_15,
							"time_16" => $value_p->time_16,
							"time_17" => $value_p->time_17,
							"time_18" => $value_p->time_18,
							"time_19" => $value_p->time_19,
							"time_20" => $value_p->time_20,
							"time_21" => $value_p->time_21,
							"time_22" => $value_p->time_22,
							"time_23" => $value_p->time_23,
						);
					}

					$view_data["array_meteo_data_values_p"] = $array_meteo_data_values_p;
				}
			}


			/* GRÁFICOS */

			// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
			$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
			$first_datetime = new DateTime($first_datetime);
			$first_datetime->setTime(0, 0, 0);
			// $first_datetime = $first_datetime->modify('-24 hours');
			$first_datetime = $first_datetime->format("Y-m-d H:i");
			$first_date = date("Y-m-d", strtotime($first_datetime));
			$view_data["first_datetime"] = $first_datetime;

			$last_datetime = new DateTime($first_datetime);
			// $last_datetime = $last_datetime->modify('+71 hours');
			$last_datetime = $last_datetime->modify('+72 hours');
			$last_datetime = $last_datetime->format("Y-m-d H:i");


			// ARRAY CON LAS FECHAS Y HORAS ENTRE LA PRIMERA Y ÚLTIMA FECHA DE CONSULTA, PARA EL RANGO DE FECHAS DE GRÁFICOS Y CALHEATMAPS
			// $last_datetime = new DateTime($last_datetime);
			// $last_datetime = $last_datetime->modify('+25 hours');
			// $last_datetime = $last_datetime->format("Y-m-d H:i");

			$period = new DatePeriod(
				new DateTime($first_datetime),
				new DateInterval('PT1H'),
				new DateTime($last_datetime)
			);

			$array_period = array();
			$array_times = array();
			$previous_date = $first_date;

			foreach ($period as $datetime) {
				$date = $datetime->format("Y-m-d");
				$time = $datetime->format("H");

				if ($previous_date == $date) {
					$array_times[] = "time_" . $time;
					$array_times[] = "time_min_" . $time;
					$array_times[] = "time_max_" . $time;
					$array_times[] = "time_porc_conf_" . $time;
				} else {
					$array_times = array();
					$array_times[] = "time_" . $time;
					$array_times[] = "time_min_" . $time;
					$array_times[] = "time_max_" . $time;
					$array_times[] = "time_porc_conf_" . $time;
				}

				$array_period[$date] = $array_times;
				$previous_date = $date;
			}



			// SI EL SECTOR TIENE LA ESTACIÓN HOTEL MINA (ID 2), SE CARGAN LOS GRÁFICOS INICIALES CON SUS DATOS.
			// SI NO, SE CARGAN LOS GRÁFICOS INICIALES CON LOS DATOS DE LA PRIMERA ESTACIÓN DEL FILTRO DE RECEPTORES DEL SECTOR.
			$id_receptor = (array_key_exists(2, $receptors_dropdown)) ? 2 : array_keys($receptors_dropdown)[1];
			$receptor = $this->Air_stations_model->get_one($id_receptor);
			$view_data["receptor_num_model"] = $receptor;

			// SI HAY AL MENOS UNA ESTACIÓN, BUSCA EL REGISTRO ASOCIADO AL CLIENTE / PROYECTO / SECTOR / ESTACIÓN / MODELO NUMÉRICO / TIPO DE REGISTRO: PRONÓSTICO
			if ($receptor->id) {
				$air_record = $this->Air_records_model->get_details(
					array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_air_sector" => $air_sector->id,
						"id_air_station" => $receptor->id,
						"id_air_model" => 3, // NUMÉRICO
						"id_air_record_type" => 2 // PRONÓSTICO
					)
				)->row();
			}

			// DATOS VARIABLE CALIDAD DEL AIRE
			// BUSCA LOS VALORES DE LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES DE CALIDAD DEL AIRE PARA LA ESTACIÓN
			$array_receptor_qual_variable_values_p = array();
			$array_receptor_qual_variable_ranges_p = array();
			$array_qual_intervalo_confianza = array();
			$array_qual_porc_conf = array();
			$array_receptor_qual_variable_formatted_dates = array();

			// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHETMAP
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => 14, // MÓDULO DE PRONÓSTICOS
				"id_client_submodule" => 0, // SIN SUBMÓDULOS
				"alert_config" => array(
					"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
					"id_air_station" => $receptor->id,
					"id_air_sector" => $air_sector->id,
					"id_air_variable" => $air_quality_variable->id
				),
			);
			$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
			$array_alerts_qual_chart = array();
			$array_alerts_qual_calheatmap_colors = array();
			$array_alerts_qual_calheatmap_ranges = array();
			$array_alerts = array();

			$array_alerts_qual_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA

			if (count($alert_config_forecast)) {
				$alert_config = $alert_config_forecast->alert_config;
				if (count($alert_config)) {
					foreach ($alert_config as $config) {

						if ($config->nc_active) {
							$array_alerts_qual_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
							$array_alerts_qual_calheatmap_colors[] = $config->nc_color;
							$array_alerts_qual_calheatmap_ranges[] = $config->min_value;
							$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);

							$array_alerts_qual_legend_map_ranges[$config->nc_color] = $config->min_value; // COLORES Y VALORES PARA LEYENDA DEL MAPA

						}
					}
				}
			}


			// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
			// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
			$array_alerts_qual_chart_final = array();
			foreach ($array_alerts_qual_chart as $index => $alert) {
				if ($index == 0) { //primer loop
					$prev_color = $alert["color"];
					continue;
				} else {
					$array_alerts_qual_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
				}
				$prev_color = $alert["color"];
			}


			// CÁLCULO DE GRADIENTES DE CADA VALOR MÍNIMO / COLOR, PARA CAPA HEATMAP DEL MAPA, SEGÚN CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO
			$array_alerts_qual_heatmap_map_ranges = array();
			foreach ($array_alerts_qual_chart as $index => $alert) {
				if ($index == 0) {
					$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => 0);
				} else if ($index == count($array_alerts_qual_chart) - 1) { // ÚLTIMO LOOP
					if ($alert["value"] < $current_qual_max_value) {
						$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
						$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
					} else {
						$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
					}
				} else {
					$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
				}
			}


			$array_alerts_qual_chart_final[] = array("color" => end($array_alerts_qual_chart)["color"]);

			$view_data["array_alerts_qual"] = $array_alerts_qual_chart;
			$view_data["array_alerts_qual_chart"] = $array_alerts_qual_chart_final;
			$view_data["array_alerts_qual_calheatmap_colors"] = $array_alerts_qual_calheatmap_colors;
			array_shift($array_alerts_qual_calheatmap_ranges);
			$view_data["array_alerts_qual_calheatmap_ranges"] = $array_alerts_qual_calheatmap_ranges;

			$view_data["array_alerts_qual_legend_map_ranges"] = $array_alerts_qual_legend_map_ranges; // COLORES Y VALORES PARA LEYENDA DEL MAPA


			$array_alerts_qual_heatmap_map_ranges_percent = array();
			foreach ($array_alerts_qual_heatmap_map_ranges as $index => $alert) {
				if ($alert["range"] < $array_alerts_qual_heatmap_map_ranges[count($array_alerts_qual_heatmap_map_ranges) - 1]["range"]) {
					$percent = (($alert["range"] * 100) / end($array_alerts_qual_heatmap_map_ranges)["range"]) / 100;
					$percent = ($percent > 1) ? 1.0 : $percent;
					$array_alerts_qual_heatmap_map_ranges_percent[(string) $percent] = $alert["color"];
				}
			}


			if (!count($array_alerts_qual_heatmap_map_ranges_percent)) {

				$array_no_alerts_gradients[] = array("color" => 'rgb(30,101,78)', "value" => '10');
				$array_no_alerts_gradients[] = array("color" => 'rgb(36,137,59)', "value" => '50');
				$array_no_alerts_gradients[] = array("color" => 'rgb(51,170,41)', "value" => '70');
				$array_no_alerts_gradients[] = array("color" => 'rgb(80,192,27)', "value" => '90');
				$array_no_alerts_gradients[] = array("color" => 'rgb(114,205,16)', "value" => '100');
				$array_no_alerts_gradients[] = array("color" => 'rgb(151,207,8)', "value" => '200');
				$array_no_alerts_gradients[] = array("color" => 'rgb(184,189,3)', "value" => '400');
				$array_no_alerts_gradients[] = array("color" => 'rgb(212,156,1)', "value" => '500');
				$array_no_alerts_gradients[] = array("color" => 'rgb(230,110,0)', "value" => '800');
				$array_no_alerts_gradients[] = array("color" => 'rgb(244,58,0)', "value" => '1000');
				$array_no_alerts_gradients[] = array("color" => 'rgb(255,0,0)', "value" => '2000');
				$array_no_alerts_gradients[] = array("color" => 'rgb(201,0,100)', "value" => '3000');
				$array_no_alerts_gradients[] = array("color" => 'rgb(145,0,127)', "value" => '4000');
				$array_no_alerts_gradients[] = array("color" => 'rgb(64,0,138)', "value" => '5000');

				foreach ($array_no_alerts_gradients as $index => $alert) {
					if ($index == 0) {
						$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => 0);
					} else if ($index == count($array_no_alerts_gradients) - 1) { // ÚLTIMO LOOP
						if ($alert["value"] < $current_qual_max_value) {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
						} else {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
						}
					} else {
						if ($prev_value < $current_qual_max_value) {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
						}
					}
				}

				$view_data["array_no_alerts_gradients"] = $array_no_alerts_gradients;

				$array_alerts_qual_heatmap_map_ranges_percent = array();
				foreach ($array_alerts_qual_heatmap_map_ranges as $index => $alert) {
					if ($alert["range"] < $array_alerts_qual_heatmap_map_ranges[count($array_alerts_qual_heatmap_map_ranges) - 1]["range"]) {
						$percent = (($alert["range"] * 100) / end($array_alerts_qual_heatmap_map_ranges)["range"]) / 100;
						$percent = ($percent > 1) ? 1.0 : $percent;
						$array_alerts_qual_heatmap_map_ranges_percent[(string) $percent] = $alert["color"];
					}
				}
			}

			$view_data["array_alerts_qual_heatmap_map_ranges"] = $array_alerts_qual_heatmap_map_ranges;
			$view_data["array_alerts_qual_heatmap_map_ranges_percent"] = $array_alerts_qual_heatmap_map_ranges_percent;



			foreach ($array_period as $date => $times) {

				$array_receptor_qual_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

				$array_data_times_values = array();
				$array_data_times_ranges = array();
				$array_data_times_values_min = array();
				$array_data_times_values_max = array();

				// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
				if ($air_quality_variable->id && $air_record->id) {

					$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
						array(
							"id_variable" => $air_quality_variable->id,
							"id_record" => $air_record->id,
							"date" => $date
						)
					)->row();

					if ($value_p->id) {

						foreach ($value_p as $field => $value) {
							if (in_array($field, $times)) {

								$range = "-";
								$prev_min_value = 0;
								foreach ($array_alerts as $alert) {
									if ($value <= $alert["min_value"]) {
										if ($prev_min_value) {
											$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										} else {
											$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										}
										break;
									}
									$prev_min_value = $alert["min_value"];
								}

								if ($value > end($array_alerts)["min_value"]) {
									$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
								}

								if (strpos($field, "min") !== false) {
									$array_data_times_values_min[] = $value;
								} elseif (strpos($field, "max") !== false) {
									$array_data_times_values_max[] = $value;
								} elseif (strpos($field, "porc_conf") !== false) {
									$array_qual_porc_conf[] = (float) $value;
								} else {
									$array_data_times_values[$field] = $value;
									$array_data_times_ranges[$field] = $range;
								}
							}
						}
					} else {

						foreach ($times as $index => $time) {

							if (strpos($time, "min") !== false) {
								$array_data_times_values_min[] = 0;
							} elseif (strpos($time, "max") !== false) {
								$array_data_times_values_max[] = 0;
							} elseif (strpos($time, "porc_conf") !== false) {
								$array_qual_porc_conf[] = 0;
							} else {
								$array_data_times_values[$time] = 0;
							}

							if ($array_alerts[0]["min_value"] > 0) {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
							} else {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
							}
						}
					}
				} else {

					foreach ($times as $index => $time) {

						if (strpos($time, "min") !== false) {
							$array_data_times_values_min[] = 0;
						} elseif (strpos($time, "max") !== false) {
							$array_data_times_values_max[] = 0;
						} elseif (strpos($time, "porc_conf") !== false) {
							$array_qual_porc_conf[] = 0;
						} else {
							$array_data_times_values[$time] = 0;
						}

						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
					}
				}

				$array_receptor_qual_variable_values_p[$date] = $array_data_times_values;
				$array_receptor_qual_variable_ranges_p[$date] = $array_data_times_ranges;

				foreach ($array_data_times_values_min as $index => $value) {
					$array_qual_intervalo_confianza[] = array((float) $value, (float) $array_data_times_values_max[$index]);
				}
			}

			$array_qual_intervalo_confianza = array_values($array_qual_intervalo_confianza);

			$view_data["array_receptor_qual_variable_values_p"] = $array_receptor_qual_variable_values_p;
			$view_data["array_receptor_qual_variable_ranges_p"] = $array_receptor_qual_variable_ranges_p;

			// $offsetKey = 71; // El desplazamiento que se necesita tomar
			// $n = array_keys($array_qual_intervalo_confianza); // Toma todas las keys del array real y las coloca en otra matriz
			// $count = array_search($offsetKey, $n); //<--- Devuelve la posición del desplazamiento del array usando array_search
			// $new_array_qual_intervalo_confianza = array_slice($array_qual_intervalo_confianza, 0, $count + 1, true);//<--- Cortar con el índice 0 como inicio y la posición +1 como lenght

			// $view_data["array_qual_intervalo_confianza"] = $new_array_qual_intervalo_confianza;
			$view_data["array_qual_intervalo_confianza"] = $array_qual_intervalo_confianza;
			$view_data["array_qual_porc_conf"] = $array_qual_porc_conf;
			$view_data["array_receptor_qual_variable_formatted_dates"] = $array_receptor_qual_variable_formatted_dates;


			// DATOS VARIABLE METEOROLÓGICA
			// BUSCA LOS VALORES DE LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES METEOROLÓGICAS PARA LA ESTACIÓN.
			// SI LA VARIABLE ES VELOCIDAD O DIRECCIÓN DEL VIENTO, ARMA UN ARREGLO CON LOS DATOS DE CADA VARIABLE, PARA CALHEATMAP
			if ($meteorological_variable->id == 1 || $meteorological_variable->id == 2) {

				// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHEATMAP
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $receptor->id,
						"id_air_sector" => $air_sector->id,
						"id_air_variable" => 1 // VELOCIDAD DEL VIENTO
					),
				);
				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				$array_alerts_meteo_chart = array();
				$array_alerts_meteo_calheatmap_colors = array();
				$array_alerts_meteo_calheatmap_ranges = array();
				$array_alerts = array();

				if (count($alert_config_forecast)) {
					$alert_config = $alert_config_forecast->alert_config;
					if (count($alert_config)) {
						foreach ($alert_config as $config) {

							if ($config->nc_active) {
								$array_alerts_meteo_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
								$array_alerts_meteo_calheatmap_colors[] = $config->nc_color;
								$array_alerts_meteo_calheatmap_ranges[] = $config->min_value;
								$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
							}
						}
					}
				}


				// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
				// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
				$array_alerts_meteo_chart_final = array();
				$i = 0;
				$prev_color = "";
				foreach ($array_alerts_meteo_chart as $alert) {
					if ($i == 0) { //primer loop
						$prev_color = $alert["color"];
						$i++;
						continue;
					} else {
						$array_alerts_meteo_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
					}
					$prev_color = $alert["color"];
					$i++;
				}

				$array_alerts_meteo_chart_final[] = array("color" => end($array_alerts_meteo_chart)["color"]);
				//$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart;
				$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart_final;
				$view_data["array_alerts_meteo_calheatmap_colors"] = $array_alerts_meteo_calheatmap_colors;
				//unset($array_alerts_meteo_calheatmap_ranges[count($array_alerts_meteo_calheatmap_ranges)-1]);
				array_shift($array_alerts_meteo_calheatmap_ranges);
				$view_data["array_alerts_meteo_calheatmap_ranges"] = $array_alerts_meteo_calheatmap_ranges;



				// CONFIGURACIÓN DE UNIDADES DE REPORTE
				$id_report_unit_setting_meteo_vel = $this->Reports_units_settings_model->get_one_where(
					array(
						"id_cliente" => $id_cliente,
						"id_proyecto" => $id_proyecto,
						"id_tipo_unidad" => 10, // VELOCIDAD
						"deleted" => 0
					)
				)->id_unidad;
				$unit_meteo_vel = $this->Unity_model->get_one($id_report_unit_setting_meteo_vel);
				$view_data["unit_meteo_vel"] = $unit_meteo_vel;
				$view_data["unit_type_meteo_vel"] = $this->Unity_type_model->get_one($unit_meteo_vel->id_tipo_unidad)->nombre;


				$id_report_unit_setting_meteo_dir = $this->Reports_units_settings_model->get_one_where(
					array(
						"id_cliente" => $id_cliente,
						"id_proyecto" => $id_proyecto,
						"id_tipo_unidad" => 11, // DIRECCIÓN
						"deleted" => 0
					)
				)->id_unidad;
				$unit_meteo_dir = $this->Unity_model->get_one($id_report_unit_setting_meteo_dir);
				$view_data["unit_meteo_dir"] = $unit_meteo_dir;
				$view_data["unit_type_meteo_dir"] = $this->Unity_type_model->get_one($unit_meteo_dir->id_tipo_unidad)->nombre;



				// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
				$array_receptor_meteo_data_values_p_vel = array();
				$array_receptor_meteo_data_ranges_p_vel = array();
				$array_receptor_meteo_variable_formatted_dates = array();

				foreach ($array_period as $date => $times) {

					$array_receptor_meteo_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

					if ($air_record->id) {

						$value_p_vel = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
							array(
								"id_variable" => 1, // VELOCIDAD DEL VIENTO
								"id_record" => $air_record->id,
								"date" => $date
							)
						)->row();

						if ($value_p_vel->id) {

							$array_data_times_values = array();
							$array_data_times_ranges = array();

							foreach ($value_p_vel as $field => $value) {
								if (in_array($field, $times)) {

									if (strpos($field, "min") !== false || strpos($field, "max") !== false || strpos($field, "porc_conf") !== false) {
										continue;
									}

									$range = "-";
									$prev_min_value = 0;
									foreach ($array_alerts as $alert) {
										if ($value <= $alert["min_value"]) {
											if ($prev_min_value) {
												$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
											} else {
												$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
											}
											break;
										}
										$prev_min_value = $alert["min_value"];
									}

									if ($value > end($array_alerts)["min_value"]) {
										$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
									}

									$array_data_times_values[$field] = $value;
									$array_data_times_ranges[$field] = $range;
								}
							}
							$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
							$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
						} else {
							$array_data_times_values = array();
							$array_data_times_ranges = array();

							foreach ($times as $index => $time) {

								if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
									continue;
								}

								$array_data_times_values[$time] = 0;
								if ($array_alerts[0]["min_value"] > 0) {
									$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
								} else {
									$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
								}
							}
							$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
							$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
						}
					} else {

						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($times as $index => $time) {

							if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
								continue;
							}

							$array_data_times_values[$time] = 0;
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
						}
						$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
					}
				}

				// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
				$array_receptor_meteo_data_values_p_dir = array();
				foreach ($array_period as $date => $times) {

					if ($air_record->id) {

						$value_p_dir = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
							array(
								"id_variable" => 2, // DIRECCIÓN DEL VIENTO
								"id_record" => $air_record->id,
								"date" => $date
							)
						)->row();

						if ($value_p_dir->id) {
							$array_data_times_values = array();
							foreach ($value_p_dir as $field => $value) {
								if (in_array($field, $times)) {
									if (strpos($field, "min") !== false || strpos($field, "max") !== false || strpos($field, "porc_conf") !== false) {
										continue;
									}
									$array_data_times_values[$field] = $value;
								}
							}
							$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
						} else {
							$array_data_times_values = array();
							foreach ($times as $index => $time) {
								if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
									continue;
								}
								$array_data_times_values[$time] = 0;
							}
							$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
						}
					} else {

						$array_data_times_values = array();
						foreach ($times as $index => $time) {
							if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
								continue;
							}
							$array_data_times_values[$time] = 0;
						}
						$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
					}
				}

				$view_data["array_receptor_meteo_data_values_p_dir"] = $array_receptor_meteo_data_values_p_dir;
				$view_data["array_receptor_meteo_data_values_p_vel"] = $array_receptor_meteo_data_values_p_vel;
				$view_data["array_receptor_meteo_data_ranges_p_vel"] = $array_receptor_meteo_data_ranges_p_vel;
				$view_data["array_receptor_meteo_variable_formatted_dates"] = $array_receptor_meteo_variable_formatted_dates;
			} else { // OTRAS VARIABLES METEOROLÓGICAS

				// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHEATMAP
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $receptor->id,
						"id_air_sector" => $air_sector->id,
						"id_air_variable" => $meteorological_variable->id
					),
				);
				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				$array_alerts_meteo_chart = array();
				$array_alerts_meteo_calheatmap_colors = array();
				$array_alerts_meteo_calheatmap_ranges = array();
				$array_alerts = array();

				$array_alerts_meteo_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA

				if (count($alert_config_forecast)) {
					$alert_config = $alert_config_forecast->alert_config;
					if (count($alert_config)) {
						foreach ($alert_config as $config) {

							if ($config->nc_active) {
								$array_alerts_meteo_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
								$array_alerts_meteo_calheatmap_colors[] = $config->nc_color;
								$array_alerts_meteo_calheatmap_ranges[] = $config->min_value;
								$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);

								$array_alerts_meteo_legend_map_ranges[$config->nc_color] = $config->min_value; // COLORES Y VALORES PARA LEYENDA DEL MAPA

							}
						}
					}
				}

				// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
				// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
				$array_alerts_meteo_chart_final = array();
				$i = 0;
				$prev_color = "";
				foreach ($array_alerts_meteo_chart as $alert) {
					if ($i == 0) { //primer loop
						$prev_color = $alert["color"];
						$i++;
						continue;
					} else {
						$array_alerts_meteo_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
					}
					$prev_color = $alert["color"];
					$i++;
				}

				$array_alerts_meteo_chart_final[] = array("color" => end($array_alerts_meteo_chart)["color"]);
				//$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart;
				$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart_final;
				$view_data["array_alerts_meteo_calheatmap_colors"] = $array_alerts_meteo_calheatmap_colors;
				array_shift($array_alerts_meteo_calheatmap_ranges);
				$view_data["array_alerts_meteo_calheatmap_ranges"] = $array_alerts_meteo_calheatmap_ranges;

				$view_data["array_alerts_meteo_legend_map_ranges"] = $array_alerts_meteo_legend_map_ranges; // COLORES Y VALORES PARA LEYENDA DEL MAPA

				// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
				$array_receptor_meteo_data_values_p = array();
				$array_receptor_meteo_data_ranges_p = array();
				$array_receptor_meteo_variable_formatted_dates = array();

				foreach ($array_period as $date => $times) {

					$array_receptor_meteo_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

					if ($air_record->id) {

						$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
							array(
								"id_variable" => $meteorological_variable->id, // TEMPERATURA
								"id_record" => $air_record->id,
								"date" => $date
							)
						)->row();

						if ($value_p->id) {

							$array_data_times_values = array();
							$array_data_times_ranges = array();

							foreach ($value_p as $field => $value) {
								if (in_array($field, $times)) {

									if (strpos($field, "min") !== false || strpos($field, "max") !== false || strpos($field, "porc_conf") !== false) {
										continue;
									}

									$range = "-";
									$prev_min_value = 0;
									foreach ($array_alerts as $alert) {
										if ($value <= $alert["min_value"]) {
											if ($prev_min_value) {
												$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
											} else {
												$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
											}
											break;
										}
										$prev_min_value = $alert["min_value"];
									}

									if ($value > end($array_alerts)["min_value"]) {
										$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
									}

									$array_data_times_values[$field] = $value;
									$array_data_times_ranges[$field] = $range;
								}
							}
							$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
							$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
						} else {
							$array_data_times_values = array();
							$array_data_times_ranges = array();

							foreach ($times as $index => $time) {

								if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
									continue;
								}

								$array_data_times_values[$time] = 0;
								if ($array_alerts[0]["min_value"] > 0) {
									$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
								} else {
									$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
								}
							}
							$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
							$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
						}
					} else {

						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($times as $index => $time) {

							if (strpos($time, "min") !== false || strpos($time, "max") !== false || strpos($time, "porc_conf") !== false) {
								continue;
							}

							$array_data_times_values[$time] = 0;
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
						}
						$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
					}
				}

				$view_data["array_receptor_meteo_data_values_p"] = $array_receptor_meteo_data_values_p;
				$view_data["array_receptor_meteo_data_ranges_p"] = $array_receptor_meteo_data_ranges_p;
				$view_data["array_receptor_meteo_variable_formatted_dates"] = $array_receptor_meteo_variable_formatted_dates;
			}
		}


		// /* SECCIÓN MODELO NEURONAL */

		// if (in_array(2, $id_models_of_sector)) {

		// 	// FILTRO VARIABLE DE CALIDAD DEL AIRE
		// 	$air_quality_variables_dropdown = array("" => "-"); // VARIABLES DE CALIDAD DEL AIRE DEL SECTOR
		// 	$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 2))->result();
		// 	foreach ($sector_variables as $variable) {
		// 		$air_quality_variables_dropdown[$variable->id_variable] = $variable->variable_name;
		// 	}
		// 	$view_data["air_quality_variables_stat_model_dropdown"] = $air_quality_variables_dropdown;

		// 	// FILTRO DE ESTACIONES DEL SECTOR
		// 	$receptors = $this->Air_stations_model->get_all_where(
		// 		array(
		// 			"id_air_sector" => $air_sector->id,
		// 			"is_active" => 1,
		// 			"is_forecast" => 1,
		// 			"is_receptor" => 0,
		// 			"deleted" => 0
		// 		)
		// 	)->result();

		// 	$receptors_dropdown = array("" => "-");
		// 	foreach ($receptors as $receptor) {
		// 		if ($receptor->id == 5) {
		// 			continue;
		// 		} // No mostrar Estación Meteorológica
		// 		$receptors_dropdown[$receptor->id] = $receptor->name;
		// 	}
		// 	$view_data["receptors_stat_model_dropdown"] = $receptors_dropdown;


		// 	// SI EL SECTOR TIENE LA VARIABLE PM10 (ID 9), SE CARGAN LOS DATOS INICIALES DE PM10,
		// 	// SI NO, SE CARGAN LOS DATOS INICIALES CON LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES "CALIDAD DEL AIRE"
		// 	$id_air_quality_variable = (array_key_exists(9, $air_quality_variables_dropdown)) ? 9 : array_keys($air_quality_variables_dropdown)[1];
		// 	$air_quality_variable = ($id_air_quality_variable) ? $this->Air_variables_model->get_details(array("id" => $id_air_quality_variable))->row() : null;


		// 	// CONFIGURACIÓN DE UNIDADES DE REPORTE
		// 	$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
		// 		array(
		// 			"id_cliente" => $id_cliente,
		// 			"id_proyecto" => $id_proyecto,
		// 			"id_tipo_unidad" => $air_quality_variable->id_unit_type,
		// 			"deleted" => 0
		// 		)
		// 	)->id_unidad;
		// 	$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
		// 	$view_data["unit_qual_stat_model"] = $unit_qual;
		// 	$view_data["unit_type_qual_stat_model"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;


		// 	// SI EL SECTOR TIENE LA ESTACIÓN HOTEL MINA (ID 2), SE CARGAN LOS GRÁFICOS INICIALES CON SUS DATOS.
		// 	// SI NO, SE CARGAN LOS GRÁFICOS INICIALES CON LOS DATOS DE LA PRIMERA ESTACIÓN DEL FILTRO DE RECEPTORES DEL SECTOR.
		// 	$id_receptor_stat_model = (array_key_exists(2, $receptors_dropdown)) ? 2 : array_keys($receptors_dropdown)[1];
		// 	$receptor_stat_model = $this->Air_stations_model->get_one($id_receptor_stat_model);
		// 	$view_data["receptor_stat_model"] = $receptor_stat_model;

		// 	$view_data["air_quality_variable_stat_model"] = $air_quality_variable;

		// 	// SI HAY AL MENOS UNA ESTACIÓN, BUSCA EL REGISTRO ASOCIADO AL CLIENTE / PROYECTO / SECTOR / ESTACIÓN / MODELO NEURONAL / TIPO DE REGISTRO: PRONÓSTICO
		// 	$air_record = null;
		// 	if ($receptor_stat_model->id) {
		// 		$air_record = $this->Air_records_model->get_details(
		// 			array(
		// 				"id_client" => $id_cliente,
		// 				"id_project" => $id_proyecto,
		// 				"id_air_sector" => $air_sector->id,
		// 				"id_air_station" => $receptor_stat_model->id,
		// 				"id_air_model" => 2, // NEURONAL
		// 				"id_air_record_type" => 2 // PRONÓSTICO
		// 			)
		// 		)->row();
		// 	}

		// 	$array_receptor_qual_stat_model_values_p = array();
		// 	$array_receptor_qual_stat_model_ranges_p = array();
		// 	$array_qual_stat_intervalo_confianza = array();
		// 	$array_qual_stat_porc_conf = array();
		// 	$array_receptor_qual_stat_formatted_dates = array();

		// 	// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHEATMAP
		// 	$config_options = array(
		// 		"id_client" => $id_cliente,
		// 		"id_project" => $id_proyecto,
		// 		"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
		// 		"id_client_submodule" => 0, // SIN SUBMÓDULO
		// 		"alert_config" => array(
		// 			"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
		// 			"id_air_station" => $receptor_stat_model->id,
		// 			"id_air_sector" => $air_sector->id,
		// 			"id_air_variable" => $air_quality_variable->id
		// 		),
		// 	);
		// 	$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();

		// 	$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		// 	$array_alerts_qual_chart_stat_model = array();
		// 	$array_alerts_qual_calheatmap_colors_stat_model = array();
		// 	$array_alerts_qual_calheatmap_ranges_stat_model = array();
		// 	$array_alerts = array();

		// 	if (count($alert_config_forecast)) {
		// 		$alert_config = $alert_config_forecast->alert_config;
		// 		if (count($alert_config)) {
		// 			foreach ($alert_config as $config) {

		// 				if ($config->nc_active) {
		// 					$array_alerts_qual_chart_stat_model[] = array("color" => $config->nc_color, "value" => $config->min_value);
		// 					$array_alerts_qual_calheatmap_colors_stat_model[] = $config->nc_color;
		// 					$array_alerts_qual_calheatmap_ranges_stat_model[] = $config->min_value;
		// 					$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
		// 				}
		// 			}
		// 		}
		// 	}


		// 	// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
		// 	// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
		// 	$array_alerts_qual_chart_stat_model_final = array();
		// 	$i = 0;
		// 	$prev_color = "";
		// 	foreach ($array_alerts_qual_chart_stat_model as $alert) {
		// 		if ($i == 0) {
		// 			$prev_color = $alert["color"];
		// 			$i++;
		// 			continue;
		// 		} else {
		// 			$array_alerts_qual_chart_stat_model_final[] = array("color" => $prev_color, "value" => $alert["value"]);
		// 		}
		// 		$prev_color = $alert["color"];
		// 		$i++;
		// 	}

		// 	$array_alerts_qual_chart_stat_model_final[] = array("color" => end($array_alerts_qual_chart_stat_model)["color"]);
		// 	//$view_data["array_alerts_qual_chart_stat_model"] = $array_alerts_qual_chart_stat_model;
		// 	$view_data["array_alerts_qual_chart_stat_model"] = $array_alerts_qual_chart_stat_model_final;
		// 	$view_data["array_alerts_qual_calheatmap_colors_stat_model"] = $array_alerts_qual_calheatmap_colors_stat_model;
		// 	array_shift($array_alerts_qual_calheatmap_ranges_stat_model);
		// 	$view_data["array_alerts_qual_calheatmap_ranges_stat_model"] = $array_alerts_qual_calheatmap_ranges_stat_model;

		// 	foreach ($array_period as $date => $times) {

		// 		$array_receptor_qual_stat_formatted_dates[$date] = get_date_format($date, $id_proyecto);

		// 		$array_data_times_values = array();
		// 		$array_data_times_ranges = array();
		// 		$array_data_times_values_min = array();
		// 		$array_data_times_values_max = array();

		// 		// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
		// 		if ($air_quality_variable->id && $air_record->id) {

		// 			$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
		// 				array(
		// 					"id_variable" => $air_quality_variable->id,
		// 					"id_record" => $air_record->id,
		// 					"date" => $date
		// 				)
		// 			)->row();

		// 			if ($value_p->id) {

		// 				foreach ($value_p as $field => $value) {
		// 					if (in_array($field, $times)) {

		// 						$range = "-";
		// 						$prev_min_value = 0;
		// 						foreach ($array_alerts as $alert) {
		// 							if ($value <= $alert["min_value"]) {
		// 								if ($prev_min_value) {
		// 									$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
		// 								} else {
		// 									$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
		// 								}
		// 								break;
		// 							}
		// 							$prev_min_value = $alert["min_value"];
		// 						}

		// 						if ($value > end($array_alerts)["min_value"]) {
		// 							$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
		// 						}

		// 						if (strpos($field, "min") !== false) {
		// 							$array_data_times_values_min[] = $value;
		// 						} elseif (strpos($field, "max") !== false) {
		// 							$array_data_times_values_max[] = $value;
		// 						} elseif (strpos($field, "porc_conf") !== false) {
		// 							$array_qual_stat_porc_conf[] = (float) $value;
		// 						} else {
		// 							$array_data_times_values[$field] = $value;
		// 							$array_data_times_ranges[$field] = $range;
		// 						}
		// 					}
		// 				}
		// 			} else {

		// 				foreach ($times as $index => $time) {

		// 					if (strpos($time, "min") !== false) {
		// 						$array_data_times_values_min[] = 0;
		// 					} elseif (strpos($time, "max") !== false) {
		// 						$array_data_times_values_max[] = 0;
		// 					} elseif (strpos($time, "porc_conf") !== false) {
		// 						$array_qual_stat_porc_conf[] = 0;
		// 					} else {
		// 						$array_data_times_values[$time] = 0;
		// 					}

		// 					if ($array_alerts[0]["min_value"] > 0) {
		// 						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
		// 					} else {
		// 						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
		// 					}
		// 				}
		// 			}
		// 		} else {

		// 			foreach ($times as $index => $time) {

		// 				if (strpos($time, "min") !== false) {
		// 					$array_data_times_values_min[] = 0;
		// 				} elseif (strpos($time, "max") !== false) {
		// 					$array_data_times_values_max[] = 0;
		// 				} elseif (strpos($time, "porc_conf") !== false) {
		// 					$array_qual_stat_porc_conf[] = 0;
		// 				} else {
		// 					$array_data_times_values[$time] = 0;
		// 				}

		// 				$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
		// 			}
		// 		}

		// 		$array_receptor_qual_stat_model_values_p[$date] = $array_data_times_values;
		// 		$array_receptor_qual_stat_model_ranges_p[$date] = $array_data_times_ranges;

		// 		foreach ($array_data_times_values_min as $index => $value) {
		// 			$array_qual_stat_intervalo_confianza[] = array((float) $value, (float) $array_data_times_values_max[$index]);
		// 		}
		// 	}

		// 	$array_qual_stat_intervalo_confianza = array_values($array_qual_stat_intervalo_confianza);

		// 	$view_data["array_receptor_qual_stat_model_values_p"] = $array_receptor_qual_stat_model_values_p;
		// 	$view_data["array_receptor_qual_stat_model_ranges_p"] = $array_receptor_qual_stat_model_ranges_p;

		// 	// $offsetKey = 71; // El desplazamiento que se necesita tomar
		// 	// $n = array_keys($array_qual_stat_intervalo_confianza); // Toma todas las keys del array real y las coloca en otra matriz
		// 	// $count = array_search($offsetKey, $n); //<--- Devuelve la posición del desplazamiento del array usando array_search
		// 	// $new_array_qual_stat_intervalo_confianza = array_slice($array_qual_stat_intervalo_confianza, 0, $count + 1, true);//<--- Cortar con el índice 0 como inicio y la posición +1 como lenght


		// 	// $view_data["array_qual_stat_intervalo_confianza"] = $new_array_qual_stat_intervalo_confianza;
		// 	$view_data["array_qual_stat_intervalo_confianza"] = $array_qual_stat_intervalo_confianza;
		// 	$view_data["array_qual_stat_porc_conf"] = $array_qual_stat_porc_conf;
		// 	$view_data["array_receptor_qual_stat_formatted_dates"] = $array_receptor_qual_stat_formatted_dates;
		// }


		// /* SECCIÓN MODELO MACHINE LEARNING */

		// if (in_array(1, $id_models_of_sector)) {


		// 	// FILTRO VARIABLE DE CALIDAD DEL AIRE
		// 	$air_quality_variables_dropdown = array("" => "-"); // VARIABLES DE TIPO CALIDAD DEL AIRE DEL SECTOR
		// 	$sector_variables = $this->Air_variables_model->get_variables_of_sector($air_sector->id, array("id_air_variable_type" => 2))->result();
		// 	foreach ($sector_variables as $variable) {
		// 		$air_quality_variables_dropdown[$variable->id_variable] = $variable->variable_name;
		// 	}
		// 	$view_data["air_quality_variables_neur_model_dropdown"] = $air_quality_variables_dropdown;

		// 	// FILTRO DE RECEPTORES DEL SECTOR
		// 	$receptors = $this->Air_stations_model->get_all_where(
		// 		array(
		// 			"id_air_sector" => $air_sector->id,
		// 			"is_active" => 1,
		// 			"is_forecast" => 1,
		// 			"is_receptor" => 0,
		// 			"deleted" => 0
		// 		)
		// 	)->result();

		// 	$receptors_dropdown = array("" => "-");
		// 	foreach ($receptors as $receptor) {
		// 		if ($receptor->id == 5) {
		// 			continue;
		// 		} // No mostrar Estación Meteorológica
		// 		$receptors_dropdown[$receptor->id] = $receptor->name;
		// 	}
		// 	$view_data["receptors_neur_model_dropdown"] = $receptors_dropdown;

		// 	// SI EL SECTOR TIENE LA VARIABLE PM10 (ID 9), SE CARGAN LOS DATOS INICIALES DE PM10,
		// 	// SI NO, SE CARGAN LOS DATOS INICIALES CON LA PRIMERA VARIABLE DEL FILTRO DE VARIABLES "CALIDAD DEL AIRE"
		// 	$id_air_quality_variable = (array_key_exists(9, $air_quality_variables_dropdown)) ? 9 : array_keys($air_quality_variables_dropdown)[1];
		// 	$air_quality_variable = ($id_air_quality_variable) ? $this->Air_variables_model->get_details(array("id" => $id_air_quality_variable))->row() : null;


		// 	// CONFIGURACIÓN DE UNIDADES DE REPORTE
		// 	$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
		// 		array(
		// 			"id_cliente" => $id_cliente,
		// 			"id_proyecto" => $id_proyecto,
		// 			"id_tipo_unidad" => $air_quality_variable->id_unit_type,
		// 			"deleted" => 0
		// 		)
		// 	)->id_unidad;
		// 	$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
		// 	$view_data["unit_qual_neur_model"] = $unit_qual;
		// 	$view_data["unit_type_qual_neur_model"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;


		// 	// SI EL SECTOR TIENE LA ESTACIÓN HOTEL MINA (ID 2), SE CARGAN LOS GRÁFICOS INICIALES CON SUS DATOS.
		// 	// SI NO, SE CARGAN LOS GRÁFICOS INICIALES CON LOS DATOS DE LA PRIMERA ESTACIÓN DEL FILTRO DE RECEPTORES DEL SECTOR.
		// 	$id_receptor_neur_model = (array_key_exists(2, $receptors_dropdown)) ? 2 : array_keys($receptors_dropdown)[1];
		// 	$receptor_neur_model = $this->Air_stations_model->get_one($id_receptor_neur_model);
		// 	$view_data["receptor_neur_model"] = $receptor_neur_model;

		// 	$view_data["air_quality_variable_neur_model"] = $air_quality_variable;

		// 	// SI HAY AL MENOS UNA ESTACIÓN, BUSCA EL REGISTRO ASOCIADO AL CLIENTE / PROYECTO / SECTOR / ESTACIÓN / MODELO MACHINE LEARNING / TIPO DE REGISTRO: PRONÓSTICO
		// 	$air_record = null;
		// 	if ($receptor_neur_model->id) {
		// 		$air_record = $this->Air_records_model->get_details(
		// 			array(
		// 				"id_client" => $id_cliente,
		// 				"id_project" => $id_proyecto,
		// 				"id_air_sector" => $air_sector->id,
		// 				"id_air_station" => $receptor_neur_model->id,
		// 				"id_air_model" => 1, // MACHINE LEARNING
		// 				"id_air_record_type" => 2 // PRONÓSTICO
		// 			)
		// 		)->row();
		// 	}


		// 	$array_receptor_qual_neur_model_values_p = array();
		// 	$array_receptor_qual_neur_model_ranges_p = array();
		// 	$array_qual_neur_intervalo_confianza = array();
		// 	$array_qual_neur_porc_conf = array();
		// 	$array_receptor_qual_neur_formatted_dates = array();

		// 	// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHEATMAP
		// 	$config_options = array(
		// 		"id_client" => $id_cliente,
		// 		"id_project" => $id_proyecto,
		// 		"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
		// 		"id_client_submodule" => 0, // SIN SUBMÓDULO
		// 		"alert_config" => array(
		// 			"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
		// 			"id_air_station" => $receptor_neur_model->id,
		// 			"id_air_sector" => $air_sector->id,
		// 			"id_air_variable" => $air_quality_variable->id
		// 		),
		// 	);
		// 	$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();

		// 	$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		// 	$array_alerts_qual_chart_neur_model = array();
		// 	$array_alerts_qual_calheatmap_colors_neur_model = array();
		// 	$array_alerts_qual_calheatmap_ranges_neur_model = array();
		// 	$array_alerts = array();

		// 	if (count($alert_config_forecast)) {
		// 		$alert_config = $alert_config_forecast->alert_config;
		// 		if (count($alert_config)) {
		// 			foreach ($alert_config as $config) {

		// 				if ($config->nc_active) {
		// 					$array_alerts_qual_chart_neur_model[] = array("color" => $config->nc_color, "value" => $config->min_value);
		// 					$array_alerts_qual_calheatmap_colors_neur_model[] = $config->nc_color;
		// 					$array_alerts_qual_calheatmap_ranges_neur_model[] = $config->min_value;
		// 					$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
		// 				}
		// 			}
		// 		}
		// 	}

		// 	// PARA EL GRÁFICO, UNA ZONA SERÁ DE CIERTO COLOR HASTA EL SIGUIENTE VALOR MÍNIMO.
		// 	// PARA ESTO, SE DEBEN MOVER LOS COLORES DE LAS ALERTAS:
		// 	$array_alerts_qual_chart_neur_model_final = array();
		// 	$i = 0;
		// 	$prev_color = "";
		// 	foreach ($array_alerts_qual_chart_neur_model as $alert) {
		// 		if ($i == 0) { //primer loop
		// 			$prev_color = $alert["color"];
		// 			$i++;
		// 			continue;
		// 		} else {
		// 			$array_alerts_qual_chart_neur_model_final[] = array("color" => $prev_color, "value" => $alert["value"]);
		// 		}
		// 		$prev_color = $alert["color"];
		// 		$i++;
		// 	}

		// 	$array_alerts_qual_chart_neur_model_final[] = array("color" => end($array_alerts_qual_chart_neur_model)["color"]);
		// 	//$view_data["array_alerts_qual_chart_neur_model"] = $array_alerts_qual_chart_neur_model;
		// 	$view_data["array_alerts_qual_chart_neur_model"] = $array_alerts_qual_chart_neur_model_final;
		// 	$view_data["array_alerts_qual_calheatmap_colors_neur_model"] = $array_alerts_qual_calheatmap_colors_neur_model;
		// 	array_shift($array_alerts_qual_calheatmap_ranges_neur_model);
		// 	$view_data["array_alerts_qual_calheatmap_ranges_neur_model"] = $array_alerts_qual_calheatmap_ranges_neur_model;

		// 	foreach ($array_period as $date => $times) {

		// 		$array_receptor_qual_neur_formatted_dates[$date] = get_date_format($date, $id_proyecto);

		// 		$array_data_times_values = array();
		// 		$array_data_times_ranges = array();
		// 		$array_data_times_values_min = array();
		// 		$array_data_times_values_max = array();

		// 		// ÚLTIMA CARGA DE DATOS 1D DE UNA FECHA ESPECÍFICA
		// 		if ($air_quality_variable->id && $air_record->id) {

		// 			$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
		// 				array(
		// 					"id_variable" => $air_quality_variable->id,
		// 					"id_record" => $air_record->id,
		// 					"date" => $date
		// 				)
		// 			)->row();

		// 			// if($value_p){
		// 			// 	var_dump($value_p);
		// 			// 	exit();
		// 			// }

		// 			if ($value_p->id) {

		// 				foreach ($value_p as $field => $value) {
		// 					if (in_array($field, $times)) {

		// 						$range = "-";
		// 						$prev_min_value = 0;
		// 						foreach ($array_alerts as $alert) {
		// 							if ($value <= $alert["min_value"]) {
		// 								if ($prev_min_value) {
		// 									$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
		// 								} else {
		// 									$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
		// 								}
		// 								break;
		// 							}
		// 							$prev_min_value = $alert["min_value"];
		// 						}

		// 						if ($value > end($array_alerts)["min_value"]) {
		// 							$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
		// 						}

		// 						if (strpos($field, "min") !== false) {
		// 							// $array_data_times_values_min[$field] = $value;
		// 							$array_data_times_values_min[] = $value;
		// 						} elseif (strpos($field, "max") !== false) {
		// 							// $array_data_times_values_max[$field] = $value;
		// 							$array_data_times_values_max[] = $value;
		// 						} elseif (strpos($field, "porc_conf") !== false) {
		// 							$array_qual_neur_porc_conf[] = (float) $value;
		// 						} else {
		// 							$array_data_times_values[$field] = $value;
		// 							$array_data_times_ranges[$field] = $range;
		// 						}
		// 					}
		// 				}
		// 			} else {

		// 				foreach ($times as $index => $time) {

		// 					if (strpos($time, "min") !== false) {
		// 						$array_data_times_values_min[] = 0;
		// 					} elseif (strpos($time, "max") !== false) {
		// 						$array_data_times_values_max[] = 0;
		// 					} elseif (strpos($time, "porc_conf") !== false) {
		// 						$array_qual_neur_porc_conf[] = 0;
		// 					} else {
		// 						$array_data_times_values[$time] = 0;
		// 					}

		// 					if ($array_alerts[0]["min_value"] > 0) {
		// 						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
		// 					} else {
		// 						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
		// 					}
		// 				}
		// 			}
		// 		} else {

		// 			foreach ($times as $index => $time) {

		// 				if (strpos($time, "min") !== false) {
		// 					$array_data_times_values_min[] = 0;
		// 				} elseif (strpos($time, "max") !== false) {
		// 					$array_data_times_values_max[] = 0;
		// 				} elseif (strpos($time, "porc_conf") !== false) {
		// 					$array_qual_neur_porc_conf[] = 0;
		// 				} else {
		// 					$array_data_times_values[$time] = 0;
		// 				}

		// 				$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
		// 			}
		// 		}

		// 		$array_receptor_qual_neur_model_values_p[$date] = $array_data_times_values;
		// 		$array_receptor_qual_neur_model_ranges_p[$date] = $array_data_times_ranges;

		// 		foreach ($array_data_times_values_min as $index => $value) {
		// 			$array_qual_neur_intervalo_confianza[] = array((float) $value, (float) $array_data_times_values_max[$index]);
		// 		}
		// 	}

		// 	$array_qual_neur_intervalo_confianza = array_values($array_qual_neur_intervalo_confianza);

		// 	$view_data["array_receptor_qual_neur_model_values_p"] = $array_receptor_qual_neur_model_values_p;
		// 	$view_data["array_receptor_qual_neur_model_ranges_p"] = $array_receptor_qual_neur_model_ranges_p;

		// 	// $offsetKey = 71; // El desplazamiento que se necesita tomar
		// 	// $n = array_keys($array_qual_neur_intervalo_confianza); // Toma todas las keys del array real y las coloca en otra matriz
		// 	// $count = array_search($offsetKey, $n); //<--- Devuelve la posición del desplazamiento del array usando array_search
		// 	// $new_array_qual_neur_intervalo_confianza = array_slice($array_qual_neur_intervalo_confianza, 0, $count + 1, true);//<--- Cortar con el índice 0 como inicio y la posición +1 como lenght

		// 	// $view_data["array_qual_neur_intervalo_confianza"] = $new_array_qual_neur_intervalo_confianza;
		// 	$view_data["array_qual_neur_intervalo_confianza"] = $array_qual_neur_intervalo_confianza;
		// 	$view_data["array_qual_neur_porc_conf"] = $array_qual_neur_porc_conf;
		// 	$view_data["array_receptor_qual_neur_formatted_dates"] = $array_receptor_qual_neur_formatted_dates;
		// }


		$this->template->rander("air_forecast_sectors_historico/index", $view_data);
	}

	/**
	 * get_data_variables
	 * 
	 * Datos de variable de Calidad del aire y/o Meteorológica para un Sector.
	 * Carga los datos de pronóstico de la variable en una fecha y hora determinadas.
	 * Se utiliza en la vista principal del módulo de Pronósticos via Ajax que se ejecuta
	 * mediante el evento on_change de los selectores de variables de la sección de Modelo Numérico,
	 * para actualizar los datos del mapa.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('air_quality_variable') id de variable de tipo Calidad del aire
	 * @uses int $this->input->post('meteorological_variable') id de variable de tipo Meteorológica
	 * @uses string $this->input->post('first_date') fecha de inicio para la consulta
	 * @uses string $this->input->post('last_date') fecha de término para la consulta
	 * @uses string $this->input->post('time_hora') para obtener el valor máximo de un registro para la fecha y hora actual
	 * @uses int $this->input->post('id_sector') id del Sector
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return JSON con datos asociados a las variables y pronósticos de un Sector
	 */
	function get_data_variables($fecha = null, $id_sector = null, $id_air_quality_variable = null, $id_meteorological_variable = null, $time_hora = null, $id_receptor = null)
	{

		ini_set("memory_limit", "-1");

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_air_quality_variable = $this->input->post('air_quality_variable');
		$id_meteorological_variable = $this->input->post('meteorological_variable');
		$id_receptor = $this->input->post('id_receptor');
		$fecha = $this->input->post('fecha'); // proviene del datepicker

		// FECHAS Y HORAS PARA CONSULTA DE DATOS DE VARIABLES
		// usar fecha proporcionada, si existe
		if($fecha){
			$first_datetime = new DateTime($fecha. " 00:00:00");
		}
		else{
			$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
			$first_datetime = new DateTime($first_datetime);
			$first_datetime->setTime(0, 0, 0);
		}

		$first_datetime = $first_datetime->format("Y-m-d H:i");

		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+23 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");


		// FECHAS Y HORAS PARA LAYER TIMEDIMENSION DE MAPA MODELO NUMÉRICO
		$first_date_map = date("Y-m-d", strtotime($first_datetime));
		$last_date_map = date("Y-m-d", strtotime($last_datetime));
		$first_time_map = date("H", strtotime($first_datetime));
		$last_time_map = date("H", strtotime($last_datetime));

		$view_data["first_date_map"] = $first_date_map;
		$view_data["last_date_map"] = $last_date_map;
		$view_data["first_time_map"] = $first_time_map;
		$view_data["last_time_map"] = $last_time_map;

		$time_hora = $this->input->post('time_hora'); // Ej.: time_11

		$id_sector = $this->input->post('id_sector');
		$air_sector = $this->Air_sectors_model->get_one($id_sector);
		$view_data["sector_info"] = $air_sector;

		$air_quality_variable = $this->Air_variables_model->get_one($id_air_quality_variable);
		$meteorological_variable = $this->Air_variables_model->get_one($id_meteorological_variable);

		if ($air_quality_variable->id) {

			// CONFIGURACIÓN DE UNIDADES DE REPORTE
			$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $air_quality_variable->id_unit_type,
					"deleted" => 0
				)
			)->id_unidad;
			$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
			$view_data["unit_qual"] = $unit_qual;
			$view_data["unit_type_qual"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;
			// DATOS DEL MAPA (REGISTROS DEL SECTOR / VARIABLE, COORDENADAS Y VALORES)
			$array_qual_data_values_p = array();
			$qual_values_p = $this->Air_records_values_p_model->get_values_details(
				array(
					"id_variable" => $air_quality_variable->id,
					"id_sector" => $id_sector,
					// "last_upload" => true,
					"first_date" => $first_date_map,
					"last_date" => $last_date_map
				)
			)->result();



			// VALOR MÁXIMO DEL REGISTRO DEL SECTOR / VARIABLE DE LA PRIMERA FECHA Y HORA, PARA LA LEYENDA
			$current_qual_max_value = $this->Air_records_values_p_model->get_current_max_variable_value(
				array(
					"id_variable" => $air_quality_variable->id,
					"id_sector" => $id_sector,
					"date" => $first_date_map,
					"time" => $time_hora
				)
			)->row()->max_value;


			if ($id_receptor) {

				// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS EN GRÁFICO Y CALHEATMAP
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $id_receptor,
						"id_air_sector" => $air_sector->id,
						"id_air_variable" => $air_quality_variable->id
					),
				);

				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				$array_alerts_qual_chart = array();
				$array_alerts_qual_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA

				if (count($alert_config_forecast)) {
					$alert_config = $alert_config_forecast->alert_config;
					if (count($alert_config)) {
						foreach ($alert_config as $config) {
							if ($config->nc_active) {
								$array_alerts_qual_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
								$array_alerts_qual_legend_map_ranges[$config->nc_color] = $config->min_value;
							}
						}
					}
				}

				// CÁLCULO DE GRADIENTES DE CADA VALOR MÍNIMO / COLOR, PARA CAPA HEATMAP DEL MAPA, SEGÚN CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO
				$array_alerts_qual_heatmap_map_ranges = array();
				$prev_color = "";
				$prev_value = "";
				foreach ($array_alerts_qual_chart as $index => $alert) {
					if ($index == 0) { // primer loop
						$prev_color = $alert["color"];
						$prev_value = $alert["value"];
						$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => 0);
						//continue;
					} else if ($index == count($array_alerts_qual_chart) - 1) { // ÚLTIMO LOOP
						if ($alert["value"] < $current_qual_max_value) {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
						} else {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $prev_color, "range" => $current_qual_max_value);
						}
					} else {
						if ($prev_value < $current_qual_max_value) {
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $prev_value);
							$prev_color = $alert["color"];
							$prev_value = $alert["value"];
						}
					}
				}

				$view_data["array_alerts_qual"] = $array_alerts_qual_chart;
				$view_data["array_alerts_qual_legend_map_ranges"] = $array_alerts_qual_legend_map_ranges;

				$array_alerts_qual_heatmap_map_ranges_percent = array();
				foreach ($array_alerts_qual_heatmap_map_ranges as $index => $alert) {
					if ($alert["range"] < $array_alerts_qual_heatmap_map_ranges[count($array_alerts_qual_heatmap_map_ranges) - 1]["range"]) {
						$percent = (($alert["range"] * 100) / end($array_alerts_qual_heatmap_map_ranges)["range"]) / 100;
						$percent = ($percent > 1) ? 1.0 : $percent;
						$array_alerts_qual_heatmap_map_ranges_percent[(string) $percent] = $alert["color"];
					}
				}

				if (!count($array_alerts_qual_heatmap_map_ranges_percent)) {

					$array_no_alerts_gradients[] = array("color" => 'rgb(30,101,78)', "value" => '10');
					$array_no_alerts_gradients[] = array("color" => 'rgb(36,137,59)', "value" => '50');
					$array_no_alerts_gradients[] = array("color" => 'rgb(51,170,41)', "value" => '70');
					$array_no_alerts_gradients[] = array("color" => 'rgb(80,192,27)', "value" => '90');
					$array_no_alerts_gradients[] = array("color" => 'rgb(114,205,16)', "value" => '100');
					$array_no_alerts_gradients[] = array("color" => 'rgb(151,207,8)', "value" => '200');
					$array_no_alerts_gradients[] = array("color" => 'rgb(184,189,3)', "value" => '400');
					$array_no_alerts_gradients[] = array("color" => 'rgb(212,156,1)', "value" => '500');
					$array_no_alerts_gradients[] = array("color" => 'rgb(230,110,0)', "value" => '800');
					$array_no_alerts_gradients[] = array("color" => 'rgb(244,58,0)', "value" => '1000');
					$array_no_alerts_gradients[] = array("color" => 'rgb(255,0,0)', "value" => '2000');
					$array_no_alerts_gradients[] = array("color" => 'rgb(201,0,100)', "value" => '3000');
					$array_no_alerts_gradients[] = array("color" => 'rgb(145,0,127)', "value" => '4000');
					$array_no_alerts_gradients[] = array("color" => 'rgb(64,0,138)', "value" => '5000');


					foreach ($array_no_alerts_gradients as $index => $alert) {
						if ($index == 0) { // primer loop
							$prev_color = $alert["color"];
							$prev_value = $alert["value"];
							$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => 0);
							//continue;
						} else if ($index == count($array_no_alerts_gradients) - 1) { // ÚLTIMO LOOP
							if ($alert["value"] < $current_qual_max_value) {
								$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $alert["value"]);
								$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $current_qual_max_value);
							} else {
								$array_alerts_qual_heatmap_map_ranges[] = array("color" => $prev_color, "range" => $current_qual_max_value);
							}
						} else {
							if ($prev_value < $current_qual_max_value) {
								$array_alerts_qual_heatmap_map_ranges[] = array("color" => $alert["color"], "range" => $prev_value);
								$prev_color = $alert["color"];
								$prev_value = $alert["value"];
							}
						}
					}

					$view_data["array_no_alerts_gradients"] = $array_no_alerts_gradients;

					$array_alerts_qual_heatmap_map_ranges_percent = array();
					foreach ($array_alerts_qual_heatmap_map_ranges as $index => $alert) {
						if ($alert["range"] < $array_alerts_qual_heatmap_map_ranges[count($array_alerts_qual_heatmap_map_ranges) - 1]["range"]) {
							$percent = (($alert["range"] * 100) / end($array_alerts_qual_heatmap_map_ranges)["range"]) / 100;
							$percent = ($percent > 1) ? 1.0 : $percent;
							$array_alerts_qual_heatmap_map_ranges_percent[(string) $percent] = $alert["color"];
						}
					}
				}

				$view_data["array_alerts_qual_heatmap_map_ranges"] = $array_alerts_qual_heatmap_map_ranges;
				$view_data["array_alerts_qual_heatmap_map_ranges_percent"] = $array_alerts_qual_heatmap_map_ranges_percent;
			}

			foreach ($qual_values_p as $value_p) {

				$array_qual_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(

					"time_00" => $value_p->time_00 < 15 ? 15 : $value_p->time_00,
					"time_01" => $value_p->time_01 < 15 ? 15 : $value_p->time_01,
					"time_02" => $value_p->time_02 < 15 ? 15 : $value_p->time_02,
					"time_03" => $value_p->time_03 < 15 ? 15 : $value_p->time_03,
					"time_04" => $value_p->time_04 < 15 ? 15 : $value_p->time_04,
					"time_05" => $value_p->time_05 < 15 ? 15 : $value_p->time_05,
					"time_06" => $value_p->time_06 < 15 ? 15 : $value_p->time_06,
					"time_07" => $value_p->time_07 < 15 ? 15 : $value_p->time_07,
					"time_08" => $value_p->time_08 < 15 ? 15 : $value_p->time_08,
					"time_09" => $value_p->time_09 < 15 ? 15 : $value_p->time_09,
					"time_10" => $value_p->time_10 < 15 ? 15 : $value_p->time_10,
					"time_11" => $value_p->time_11 < 15 ? 15 : $value_p->time_11,
					"time_12" => $value_p->time_12 < 15 ? 15 : $value_p->time_12,
					"time_13" => $value_p->time_13 < 15 ? 15 : $value_p->time_13,
					"time_14" => $value_p->time_14 < 15 ? 15 : $value_p->time_14,
					"time_15" => $value_p->time_15 < 15 ? 15 : $value_p->time_15,
					"time_16" => $value_p->time_16 < 15 ? 15 : $value_p->time_16,
					"time_17" => $value_p->time_17 < 15 ? 15 : $value_p->time_17,
					"time_18" => $value_p->time_18 < 15 ? 15 : $value_p->time_18,
					"time_19" => $value_p->time_19 < 15 ? 15 : $value_p->time_19,
					"time_20" => $value_p->time_20 < 15 ? 15 : $value_p->time_20,
					"time_21" => $value_p->time_21 < 15 ? 15 : $value_p->time_21,
					"time_22" => $value_p->time_22 < 15 ? 15 : $value_p->time_22,
					"time_23" => $value_p->time_23 < 15 ? 15 : $value_p->time_23,
				);
			}

			$view_data["air_quality_variable"] = $air_quality_variable;
			$view_data["array_qual_data_values_p"] = $array_qual_data_values_p;
		}


		if ($meteorological_variable->id) {

			// CONFIGURACIÓN DE UNIDADES DE REPORTE
			$id_report_unit_setting_meteo = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => $meteorological_variable->id_unit_type,
					"deleted" => 0
				)
			)->id_unidad;
			$unit_meteo = $this->Unity_model->get_one($id_report_unit_setting_meteo);
			$view_data["unit_meteo"] = $unit_meteo;
			$view_data["unit_type_meteo"] = $this->Unity_type_model->get_one($unit_meteo->id_tipo_unidad)->nombre;


			$view_data["meteorological_variable"] = $meteorological_variable;

			// DATOS DEL MAPA (REGISTROS DEL SECTOR / VARIABLE, COORDENADAS Y VALORES)
			$array_meteo_data_values_p = array();

			if ($meteorological_variable->id == 1) {
				$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => $meteorological_variable->id,
						"id_sector" => $id_sector,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result();

				$meteo_values_p_dir = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => 2, // DIRECCIÓN DEL VIENTO
						"id_sector" => $id_sector,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result();

				$array_meteo_data_values_p_dir = array();
				foreach ($meteo_values_p_dir as $value_p_dir) {
					$array_meteo_data_values_p_dir[$value_p_dir->date][$value_p_dir->latitude . ":" . $value_p_dir->longitude] = array(
						"time_00" => $value_p_dir->time_00,
						"time_01" => $value_p_dir->time_01,
						"time_02" => $value_p_dir->time_02,
						"time_03" => $value_p_dir->time_03,
						"time_04" => $value_p_dir->time_04,
						"time_05" => $value_p_dir->time_05,
						"time_06" => $value_p_dir->time_06,
						"time_07" => $value_p_dir->time_07,
						"time_08" => $value_p_dir->time_08,
						"time_09" => $value_p_dir->time_09,
						"time_10" => $value_p_dir->time_10,
						"time_11" => $value_p_dir->time_11,
						"time_12" => $value_p_dir->time_12,
						"time_13" => $value_p_dir->time_13,
						"time_14" => $value_p_dir->time_14,
						"time_15" => $value_p_dir->time_15,
						"time_16" => $value_p_dir->time_16,
						"time_17" => $value_p_dir->time_17,
						"time_18" => $value_p_dir->time_18,
						"time_19" => $value_p_dir->time_19,
						"time_20" => $value_p_dir->time_20,
						"time_21" => $value_p_dir->time_21,
						"time_22" => $value_p_dir->time_22,
						"time_23" => $value_p_dir->time_23,
					);
				}

				foreach ($meteo_values_p as $value_p) {

					$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
						"time_00" => array("velocity" => $value_p->time_00, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_00"]),
						"time_01" => array("velocity" => $value_p->time_01, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_01"]),
						"time_02" => array("velocity" => $value_p->time_02, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_02"]),
						"time_03" => array("velocity" => $value_p->time_03, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_03"]),
						"time_04" => array("velocity" => $value_p->time_04, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_04"]),
						"time_05" => array("velocity" => $value_p->time_05, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_05"]),
						"time_06" => array("velocity" => $value_p->time_06, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_06"]),
						"time_07" => array("velocity" => $value_p->time_07, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_07"]),
						"time_08" => array("velocity" => $value_p->time_08, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_08"]),
						"time_09" => array("velocity" => $value_p->time_09, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_09"]),
						"time_10" => array("velocity" => $value_p->time_10, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_10"]),
						"time_11" => array("velocity" => $value_p->time_11, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_11"]),
						"time_12" => array("velocity" => $value_p->time_12, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_12"]),
						"time_13" => array("velocity" => $value_p->time_13, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_13"]),
						"time_14" => array("velocity" => $value_p->time_14, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_14"]),
						"time_15" => array("velocity" => $value_p->time_15, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_15"]),
						"time_16" => array("velocity" => $value_p->time_16, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_16"]),
						"time_17" => array("velocity" => $value_p->time_17, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_17"]),
						"time_18" => array("velocity" => $value_p->time_18, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_18"]),
						"time_19" => array("velocity" => $value_p->time_19, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_19"]),
						"time_20" => array("velocity" => $value_p->time_20, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_20"]),
						"time_21" => array("velocity" => $value_p->time_21, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_21"]),
						"time_22" => array("velocity" => $value_p->time_22, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_22"]),
						"time_23" => array("velocity" => $value_p->time_23, "direction" => $array_meteo_data_values_p_dir[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_23"]),

					);
				}

				// SI HAY DATOS PARA AMBAS VARIABLES, DEVOLVER SUS DATOS PARA EL MAPA
				$view_data["array_meteo_data_values_p"] = (count($meteo_values_p) && count($meteo_values_p_dir)) ? $array_meteo_data_values_p : array();
					// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS
					$config_options = array(
						"id_client" => $id_cliente,
						"id_project" => $id_proyecto,
						"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
						"id_client_submodule" => 0, // SIN SUBMÓDULO
						"alert_config" => array(
							"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
							"id_air_station" => $id_receptor,
							"id_air_sector" => $air_sector->id,
							"id_air_variable" => $meteorological_variable->id
						),
					);
	
					$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
					$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
					$array_alerts_meteo_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA
	
					if (count($alert_config_forecast)) {
						$alert_config = $alert_config_forecast->alert_config;
						if (count($alert_config)) {
							foreach ($alert_config as $config) {
								$array_alerts_meteo_legend_map_ranges[$config->nc_color] = $config->min_value; // COLORES Y VALORES PARA LEYENDA DEL MAPA
							}
						}
					}
	
					$view_data["array_alerts_meteo_legend_map_ranges"] = $array_alerts_meteo_legend_map_ranges; // COLORES Y VALORES PARA LEYENDA DEL MAPA
			} elseif ($meteorological_variable->id == 2) {
				$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => $meteorological_variable->id,
						"id_sector" => $id_sector,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result();

				$meteo_values_p_vel = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => 1, // VELOCIDAD DEL VIENTO
						"id_sector" => $id_sector,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result();


				$array_meteo_data_values_p_vel = array();
				foreach ($meteo_values_p_vel as $value_p_vel) {
					$array_meteo_data_values_p_vel[$value_p_vel->date][$value_p_vel->latitude . ":" . $value_p_vel->longitude] = array(
						"time_00" => $value_p_vel->time_00,
						"time_01" => $value_p_vel->time_01,
						"time_02" => $value_p_vel->time_02,
						"time_03" => $value_p_vel->time_03,
						"time_04" => $value_p_vel->time_04,
						"time_05" => $value_p_vel->time_05,
						"time_06" => $value_p_vel->time_06,
						"time_07" => $value_p_vel->time_07,
						"time_08" => $value_p_vel->time_08,
						"time_09" => $value_p_vel->time_09,
						"time_10" => $value_p_vel->time_10,
						"time_11" => $value_p_vel->time_11,
						"time_12" => $value_p_vel->time_12,
						"time_13" => $value_p_vel->time_13,
						"time_14" => $value_p_vel->time_14,
						"time_15" => $value_p_vel->time_15,
						"time_16" => $value_p_vel->time_16,
						"time_17" => $value_p_vel->time_17,
						"time_18" => $value_p_vel->time_18,
						"time_19" => $value_p_vel->time_19,
						"time_20" => $value_p_vel->time_20,
						"time_21" => $value_p_vel->time_21,
						"time_22" => $value_p_vel->time_22,
						"time_23" => $value_p_vel->time_23,
					);
				}

				foreach ($meteo_values_p as $value_p) {
					$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
						"time_00" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_00"], "direction" => $value_p->time_00),
						"time_01" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_01"], "direction" => $value_p->time_01),
						"time_02" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_02"], "direction" => $value_p->time_02),
						"time_03" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_03"], "direction" => $value_p->time_03),
						"time_04" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_04"], "direction" => $value_p->time_04),
						"time_05" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_05"], "direction" => $value_p->time_05),
						"time_06" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_06"], "direction" => $value_p->time_06),
						"time_07" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_07"], "direction" => $value_p->time_07),
						"time_08" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_08"], "direction" => $value_p->time_08),
						"time_09" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_09"], "direction" => $value_p->time_09),
						"time_10" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_10"], "direction" => $value_p->time_10),
						"time_11" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_11"], "direction" => $value_p->time_11),
						"time_12" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_12"], "direction" => $value_p->time_12),
						"time_13" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_13"], "direction" => $value_p->time_13),
						"time_14" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_14"], "direction" => $value_p->time_14),
						"time_15" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_15"], "direction" => $value_p->time_15),
						"time_16" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_16"], "direction" => $value_p->time_16),
						"time_17" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_17"], "direction" => $value_p->time_17),
						"time_18" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_18"], "direction" => $value_p->time_18),
						"time_19" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_19"], "direction" => $value_p->time_19),
						"time_20" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_20"], "direction" => $value_p->time_20),
						"time_21" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_21"], "direction" => $value_p->time_21),
						"time_22" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_22"], "direction" => $value_p->time_22),
						"time_23" => array("velocity" => $array_meteo_data_values_p_vel[$value_p->date][$value_p->latitude . ":" . $value_p->longitude]["time_23"], "direction" => $value_p->time_23),
					);
				}

				// SI HAY DATOS PARA AMBAS VARIABLES, DEVOLVER SUS DATOS PARA EL MAPA
				$view_data["array_meteo_data_values_p"] = (count($meteo_values_p) && count($meteo_values_p_vel)) ? $array_meteo_data_values_p : array();
				// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $id_receptor,
						"id_air_sector" => $air_sector->id,
						"id_air_variable" => $meteorological_variable->id
					),
				);

				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				$array_alerts_meteo_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA

				if (count($alert_config_forecast)) {
					$alert_config = $alert_config_forecast->alert_config;
					if (count($alert_config)) {
						foreach ($alert_config as $config) {
							$array_alerts_meteo_legend_map_ranges[$config->nc_color] = $config->min_value; // COLORES Y VALORES PARA LEYENDA DEL MAPA
						}
					}
				}

				$view_data["array_alerts_meteo_legend_map_ranges"] = $array_alerts_meteo_legend_map_ranges; // COLORES Y VALORES PARA LEYENDA DEL MAPA

			} else { // OTRAS VARIABLES METEOROLÓGICAS

				// CONFIGURACIÓN DE ALERTAS DE PRONÓSTICO PARA CONFIGURACIÓN DE COLORES DE RANGOS
				$config_options = array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_client_module" => 14, // MÓDULO DE PRONÓSTICO
					"id_client_submodule" => 0, // SIN SUBMÓDULO
					"alert_config" => array(
						"air_config" => "forecast_alerts", // ACORDEÓN ALERTAS DE PRONÓSTICO
						"id_air_station" => $id_receptor,
						"id_air_sector" => $air_sector->id,
						"id_air_variable" => $meteorological_variable->id
					),
				);

				$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
				$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
				$array_alerts_meteo_legend_map_ranges = array(); // COLORES Y VALORES PARA LEYENDA DEL MAPA

				if (count($alert_config_forecast)) {
					$alert_config = $alert_config_forecast->alert_config;
					if (count($alert_config)) {
						foreach ($alert_config as $config) {
							$array_alerts_meteo_legend_map_ranges[$config->nc_color] = $config->min_value; // COLORES Y VALORES PARA LEYENDA DEL MAPA
						}
					}
				}

				$view_data["array_alerts_meteo_legend_map_ranges"] = $array_alerts_meteo_legend_map_ranges; // COLORES Y VALORES PARA LEYENDA DEL MAPA

				// DATOS DEL MAPA (REGISTROS DEL SECTOR / VARIABLE, COORDENADAS Y VALORES)
				$array_meteo_data_values_p = array();
				$meteo_values_p = $this->Air_records_values_p_model->get_values_details(
					array(
						"id_variable" => $meteorological_variable->id,
						"id_sector" => $id_sector,
						// "last_upload" => true,
						"first_date" => $first_date_map,
						"last_date" => $last_date_map
					)
				)->result();

				foreach ($meteo_values_p as $value_p) {
					$array_meteo_data_values_p[$value_p->date][$value_p->latitude . ":" . $value_p->longitude] = array(
						"time_00" => $value_p->time_00,
						"time_01" => $value_p->time_01,
						"time_02" => $value_p->time_02,
						"time_03" => $value_p->time_03,
						"time_04" => $value_p->time_04,
						"time_05" => $value_p->time_05,
						"time_06" => $value_p->time_06,
						"time_07" => $value_p->time_07,
						"time_08" => $value_p->time_08,
						"time_09" => $value_p->time_09,
						"time_10" => $value_p->time_10,
						"time_11" => $value_p->time_11,
						"time_12" => $value_p->time_12,
						"time_13" => $value_p->time_13,
						"time_14" => $value_p->time_14,
						"time_15" => $value_p->time_15,
						"time_16" => $value_p->time_16,
						"time_17" => $value_p->time_17,
						"time_18" => $value_p->time_18,
						"time_19" => $value_p->time_19,
						"time_20" => $value_p->time_20,
						"time_21" => $value_p->time_21,
						"time_22" => $value_p->time_22,
						"time_23" => $value_p->time_23,
					);
				}

				$view_data["array_meteo_data_values_p"] = $array_meteo_data_values_p;
			}
		}
		echo json_encode($view_data);	
	}

	/**
	 * get_map_data
	 * 
	 * maneja las solicitudes ajax para obtener los datos del mapa de pronóstico
	 * basados en la fecha seleccionada en la vista
	 *
	 * @author Luis Loyola Becerra
	 * @access public
	 * @return JSON Con datos asociados a las variables y pronósticos de un Sector
	 */
	function get_map_data(){
		if($this->input->is_ajax_request()){
			$fecha = $this->input->post('fecha');
			$id_sector = $this->input->post('id_sector');
			$air_quality_variable = $this->input->post('air_quality_variable');
			$meteorological_variable = $this->input->post('meteorological_variable');
			$time_hora = $this->input->post('time_hora');
			$id_receptor = $this->input->post('id_receptor');

			// llama  a la funcion get_data_variables y pasa la fecha seleccionada
			$this->load->model('Air_records_values_p_model');
			
			$view_data = $this->get_data_variables($fecha, $id_sector, $air_quality_variable, $meteorological_variable, $time_hora, $id_receptor);
			// echo json_encode($view_data);
		}
	}
	/**
	 * list_data_variable
	 * 
	 * Lista los datos de variable de calidad del aire y/o meteorológica para un Sector.
	 * Carga los datos de pronóstico de la variable en un periodo de 72 horas.
	 * Se utiliza via Ajax en los appTable de la vista principal del módulo de Pronósticos
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @param int $id_sector id del Sector
	 * @param int $id_station id de la Estación
	 * @param int $id_variable id de la Variable
	 * @param int $id_model id del Modelo
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return JSON Con datos asociados a las variables y pronósticos de un Sector
	 */
	function list_data_variable($id_sector = 0, $id_station = 0, $id_variable = 0, $id_model = 0)
	{

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		// Busca el registro asociado al cliente / proyecto / sector / receptor (estación) / modelo numérico / tipo de registro pronóstico
		$air_record = $this->Air_records_model->get_details(
			array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_air_sector" => $id_sector,
				"id_air_station" => $id_station,
				"id_air_model" => $id_model, // Numérico, NEURONAL o MACHINE LEARNING
				"id_air_record_type" => 2 // Pronóstico
			)
		)->row();

		// Variables de fechas y horas de los datos
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0, 0, 0);
		$first_datetime = $first_datetime->format("Y-m-d H:i");

		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+72 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");

		$period = new DatePeriod(
			new DateTime($first_datetime),
			new DateInterval('PT1H'),
			new DateTime($last_datetime)
		);

		$array_period = array();
		$array_times = array();
		$previous_date = $first_date;

		foreach ($period as $datetime) {
			$date = $datetime->format("Y-m-d");
			$time = $datetime->format("H");

			if ($previous_date == $date) {
				$array_times[] = "time_" . $time;
			} else {
				$array_times = array();
				$array_times[] = "time_" . $time;
			}

			$array_period[$date] = $array_times;
			$previous_date = $date;
		}

		// Llamar a la configuración de Alertas de Pronóstico
		$array_alerts_forecast = array();
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // Módulo de Pronóstico
			"id_client_submodule" => 0, // Sin submódulo
			"alert_config" => array(
				"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
				"id_air_station" => $id_station,
				"id_air_sector" => $id_sector,
				"id_air_variable" => $id_variable
			),
		);

		$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
		$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		if (count($alert_config_forecast)) {
			$alert_config = $alert_config_forecast->alert_config;
			if (count($alert_config)) {
				foreach ($alert_config as $config) {
					$array_alerts_forecast[] = array("nc_active" => $config->nc_active, "nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
				}
			}
		}

		// Llamar a la configuración de Plan de Acción
		$array_alerts_ap = array();
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // Módulo de Pronóstico
			"id_client_submodule" => 0, // Sin submódulo
			"alert_config" => array(
				"air_config" => "action_plan", // Acordeón Plan de Acción
				"id_air_station" => $id_station,
				"id_air_sector" => $id_sector,
				"id_air_variable" => $id_variable
			),
		);

		$alert_config_air_action_plan = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
		$alert_config_action_plan = json_decode($alert_config_air_action_plan->alert_config);

		$alert_config_air_action_plan_user = $this->AYN_Alert_projects_users_model->get_one_where(
			array(
				"id_alert_project" => $alert_config_air_action_plan->id,
				"id_user" => $this->session->user_id,
				"deleted" => 0
			)
		);

		if (count($alert_config_action_plan)) {
			$alert_config = $alert_config_action_plan->alert_config;
			if (count($alert_config)) {
				foreach ($alert_config as $config) {
					$array_alerts_ap[] = array("ap_active" => $config->ap_active, "ap_action_plan" => $config->ap_action_plan, "ap_email" => $config->ap_email, "ap_web" => $config->ap_web);
				}
			}
		}

		// Array con datos de configuración
		$array_alerts_final = array();
		foreach ($array_alerts_forecast as $index_alert => $alert) {
			$alert_ap = $array_alerts_ap[$index_alert];
			$array_alerts_final[] = array(
				"nc_active" => $alert["nc_active"],
				"nc_name" => $alert["nc_name"],
				"nc_color" => $alert["nc_color"],
				"min_value" => $alert["min_value"],
				"ap_active" => $alert_ap["ap_active"],
				"ap_action_plan" => $alert_ap["ap_action_plan"],
				"ap_email" => $alert_ap["ap_email"],
				"ap_web" => $alert_ap["ap_web"]
			);
		}


		$result = array();
		foreach ($array_period as $date => $times) {

			// Trae la última carga de datos (1D) de una fecha específica 
			$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
				array(
					"id_variable" => $id_variable,
					"id_record" => $air_record->id,
					"date" => $date
				)
			)->row();

			if ($value_p->id) {
				foreach ($value_p as $field => $value) {
					if (in_array($field, $times)) {

						if (strpos($field, "min") !== false || strpos($field, "max") !== false || strpos($field, "porc_conf") !== false) {
							continue;
						}

						$row_data = array();
						$row_data[] = $value_p->id;
						$row_data[] = get_date_format($date, $id_proyecto);
						$row_data[] = substr($field, 5) . ":00";

						$range = "-";
						$html_alert = "-";
						// $html_action_plan = "-";
						$html_action_plan_content = "-";

						$prev_min_value = 0;
						$prev_html_alert = "";
						// $prev_html_action_plan = "";
						$prev_html_action_plan_content = "";
						foreach ($array_alerts_final as $alert) {

							if ($alert["nc_active"] /*&& $alert_config_air_action_plan_user->id*/) {
								if ($value < $alert["min_value"]) {

									if ($prev_min_value) {
										$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									} else {
										$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									}

									$html_alert = ($prev_html_alert) ? $prev_html_alert : '<label class="label large" style="background-color:' . $alert["nc_color"] . '">' . $alert["nc_name"] . '</label>';

									if (/*$prev_html_action_plan && */ $prev_html_action_plan_content) {
										// $html_action_plan = $prev_html_action_plan;
										$html_action_plan_content = $prev_html_action_plan_content;
									} else {
										// $html_action_plan = "-";
										$html_action_plan_content = "";
									}

									break;
								}
								$prev_min_value = $alert["min_value"];
								$prev_html_alert = '<label class="label large" style="background-color:' . $alert["nc_color"] . '">' . $alert["nc_name"] . '</label>';

								if ($alert["ap_active"] /*&& $alert["ap_web"]*/) {
									// $prev_html_action_plan = '<i class="fa fa-exclamation-triangle" data-toggle="popover" data-html="true"></i>';
									// $prev_html_action_plan_content = '<div class="col-md-12"> '.$alert["ap_action_plan"].'</div>';
									$prev_html_action_plan_content = $alert["ap_action_plan"];
								} else {
									// $prev_html_action_plan = "-";
									$prev_html_action_plan_content = "";
								}
							}
						}

						// comparar si $value > al ultimo valor mímimo de alertas. Si se da esa condición setear $range = "más de [ultimo valor minimo de alertas]"
						if ($value > end($array_alerts_final)["min_value"] && end($array_alerts_final)["nc_active"] /*&& $alert_config_air_action_plan_user->id*/) {
							$range = lang("more_than") . " " . to_number_project_format(end($array_alerts_final)["min_value"], $id_proyecto);
							$html_alert = '<label class="label large" style="background-color:' . end($array_alerts_final)["nc_color"] . '">' . end($array_alerts_final)["nc_name"] . '</label>';

							if (end($array_alerts_final)["ap_active"] /*&& end($array_alerts_final)["ap_web"]*/) {
								// $html_action_plan = '<i class="fa fa-exclamation-triangle" data-toggle="popover" data-html="true"></i>';
								// $html_action_plan_content = '<div class="col-md-12"> '.end($array_alerts_final)["ap_action_plan"].'</div>';
								$html_action_plan_content = end($array_alerts_final)["ap_action_plan"];
							}
						}

						$row_data[] = $html_alert;
						$row_data[] = $range/*." (".$value.")"*/ ;

						// $row_data[] = $html_action_plan;
						$row_data[] = $html_action_plan_content;

						$result[] = $row_data;
					}
				}
			} else {
				foreach ($times as $index => $time) {

					$row_data = array();
					$row_data[] = $value_p->id;
					$row_data[] = get_date_format($date, $id_proyecto);
					$row_data[] = substr($time, 5) . ":00";
					$row_data[] = "-";
					$row_data[] = "-";
					$row_data[] = "-";
					$row_data[] = "-";

					$result[] = $row_data;
				}
			}
		}

		echo json_encode(array("data" => $result));
	}

	/**
	 * get_data_receptor
	 * 
	 * Datos de variable de Calidad del aire y/o Meteorológica para un Receptor de un Sector.
	 * Carga los datos de pronóstico de la variable en una fecha y hora determinadas.
	 * Se utiliza en la vista principal del módulo de Pronósticos via Ajax que se ejecuta
	 * mediante el evento on_change de los selectores de variables y receptor, de la sección de Modelo Numérico,
	 * para actualizar los datos de los gráficos y calheatmap.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id_receptor') id del Receptor
	 * @uses int $this->input->post('air_quality_variable') id de variable de tipo Calidad del aire
	 * @uses int $this->input->post('meteorological_variable') id de variable de tipo Meteorológica
	 * @uses int $this->input->post('id_sector') id del Sector
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return JSON con datos asociados a las variables del receptor y pronósticos de un Sector
	 */
	function get_data_receptor()
	{

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_receptor = $this->input->post('id_receptor');
		$receptor = $this->Air_stations_model->get_one($id_receptor);

		$id_air_quality_variable = $this->input->post('air_quality_variable');
		$air_quality_variable = ($id_air_quality_variable) ? $this->Air_variables_model->get_details(array("id" => $id_air_quality_variable))->row() : null;
		$view_data["air_quality_variable"] = $air_quality_variable;

		$id_meteorological_variable = $this->input->post('meteorological_variable');
		$meteorological_variable = ($id_meteorological_variable) ? $this->Air_variables_model->get_details(array("id" => $id_meteorological_variable))->row() : null;
		$view_data["meteorological_variable"] = $meteorological_variable;

		// Configuración de Unidades de Reporte
		$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
			array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_tipo_unidad" => $air_quality_variable->id_unit_type,
				"deleted" => 0
			)
		)->id_unidad;
		$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
		$view_data["unit_qual"] = $unit_qual;
		$view_data["unit_type_qual"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;

		$id_report_unit_setting_meteo = $this->Reports_units_settings_model->get_one_where(
			array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_tipo_unidad" => $meteorological_variable->id_unit_type,
				"deleted" => 0
			)
		)->id_unidad;
		$unit_meteo = $this->Unity_model->get_one($id_report_unit_setting_meteo);
		$view_data["unit_meteo"] = $unit_meteo;
		$view_data["unit_type_meteo"] = $this->Unity_type_model->get_one($unit_meteo->id_tipo_unidad)->nombre;

		$id_sector = $this->input->post('id_sector');
		$air_sector = $this->Air_sectors_model->get_one($id_sector);
		$view_data["sector_info"] = $air_sector;

		// Si hay al menos un receptor, busca el registro asociado al cliente / proyecto / sector / receptor (estación) / modelo numérico / tipo de registro pronóstico
		if ($receptor->id) {
			$air_record = $this->Air_records_model->get_details(
				array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_air_sector" => $air_sector->id,
					"id_air_station" => $receptor->id,
					"id_air_model" => 3, // Numérico
					"id_air_record_type" => 2 // Pronóstico
				)
			)->row();
		}

		// Variables de fechas y horas de los datos
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0, 0, 0);
		$first_datetime = $first_datetime->format("Y-m-d H:i");
		$first_date = date("Y-m-d", strtotime($first_datetime));
		$view_data["first_datetime"] = $first_datetime;

		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+72 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");

		$period = new DatePeriod(
			new DateTime($first_datetime),
			new DateInterval('PT1H'),
			new DateTime($last_datetime)
		);

		$array_period = array();
		$array_times = array();
		$previous_date = $first_date;

		foreach ($period as $datetime) {

			$date = $datetime->format("Y-m-d");
			$time = $datetime->format("H");

			if ($previous_date == $date) {
				$array_times[] = "time_" . $time;
				$array_times[] = "time_min_" . $time;
				$array_times[] = "time_max_" . $time;
				$array_times[] = "time_porc_conf_" . $time;
			} else {
				$array_times = array();
				$array_times[] = "time_" . $time;
				$array_times[] = "time_min_" . $time;
				$array_times[] = "time_max_" . $time;
				$array_times[] = "time_porc_conf_" . $time;
			}

			$array_period[$date] = $array_times;
			$previous_date = $date;
		}

		$view_data["first_datetime"] = $first_datetime;

		// Buscar los valores de la primera variable del filtro de variables de Calidad del aire para el receptor
		$array_receptor_qual_variable_values_p = array();
		$array_receptor_qual_variable_ranges_p = array();
		$array_qual_intervalo_confianza = array();
		$array_qual_porc_conf = array();
		$array_receptor_qual_variable_formatted_dates = array();


		// Llamar a la configuración de Alertas de Pronóstico para configuración de colores de rangos en gráfico y calheatmap
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // Módulo de Pronóstico
			"id_client_submodule" => 0, // Sin submódulo
			"alert_config" => array(
				"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
				"id_air_station" => $receptor->id,
				"id_air_sector" => $air_sector->id,
				"id_air_variable" => $air_quality_variable->id
			),
		);
		$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
		$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		$array_alerts_qual_chart = array();
		$array_alerts_qual_calheatmap_colors = array();
		$array_alerts_qual_calheatmap_ranges = array();
		$array_alerts = array();

		if (count($alert_config_forecast)) {
			$alert_config = $alert_config_forecast->alert_config;
			if (count($alert_config)) {
				foreach ($alert_config as $config) {

					if ($config->nc_active) {
						$array_alerts_qual_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
						$array_alerts_qual_calheatmap_colors[] = $config->nc_color;
						$array_alerts_qual_calheatmap_ranges[] = $config->min_value;
						$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
					}
				}
			}
		}

		// Para el gráfico, una zona será de cierto color hasta el valor anterior al que se le indique, que es hasta donde se extiende la zona,
		// es por esto que se deben mover los colores de las alertas:
		$array_alerts_qual_chart_final = array();
		$i = 0;
		$prev_color = "";
		foreach ($array_alerts_qual_chart as $alert) {
			if ($i == 0) { //primer loop
				$prev_color = $alert["color"];
				$i++;
				continue;
			} else {
				$array_alerts_qual_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
			}
			$prev_color = $alert["color"];
			$i++;
		}

		$array_alerts_qual_chart_final[] = array("color" => end($array_alerts_qual_chart)["color"]);
		//$view_data["array_alerts_qual_chart"] = $array_alerts_qual_chart;
		$view_data["array_alerts_qual_chart"] = $array_alerts_qual_chart_final;
		$view_data["array_alerts_qual_calheatmap_colors"] = $array_alerts_qual_calheatmap_colors;
		array_shift($array_alerts_qual_calheatmap_ranges);
		$view_data["array_alerts_qual_calheatmap_ranges"] = $array_alerts_qual_calheatmap_ranges;


		foreach ($array_period as $date => $times) {

			$array_receptor_qual_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

			$array_data_times_values = array();
			$array_data_times_ranges = array();
			$array_data_times_values_min = array();
			$array_data_times_values_max = array();

			// Trae la última carga de datos (1D) de una fecha específica
			if ($air_quality_variable->id && $air_record->id) {

				$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
					array(
						"id_variable" => $air_quality_variable->id,
						"id_record" => $air_record->id,
						"date" => $date
					)
				)->row();

				if ($value_p->id) {

					foreach ($value_p as $field => $value) {
						if (in_array($field, $times)) {

							$range = "-";
							$prev_min_value = 0;
							foreach ($array_alerts as $alert) {
								if ($value <= $alert["min_value"]) {
									if ($prev_min_value) {
										$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									} else {
										$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									}
									break;
								}
								$prev_min_value = $alert["min_value"];
							}

							// comparar si $value > al ultimo valor mímimo de alertas. Si se da esa condición setear $range = "más de [ultimo valor minimo de alertas]"
							if ($value > end($array_alerts)["min_value"]) {
								$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
							}

							if (strpos($field, "min") !== false) {
								$array_data_times_values_min[] = $value;
							} elseif (strpos($field, "max") !== false) {
								$array_data_times_values_max[] = $value;
							} elseif (strpos($field, "porc_conf") !== false) {
								$array_qual_porc_conf[] = (float) $value;
							} else {
								$array_data_times_values[$field] = $value;
								$array_data_times_ranges[$field] = $range;
							}
						}
					}
				} else {

					foreach ($times as $index => $time) {

						if (strpos($time, "min") !== false) {
							$array_data_times_values_min[] = 0;
						} elseif (strpos($time, "max") !== false) {
							$array_data_times_values_max[] = 0;
						} elseif (strpos($time, "porc_conf") !== false) {
							$array_qual_porc_conf[] = 0;
						} else {
							$array_data_times_values[$time] = 0;
						}

						if ($array_alerts[0]["min_value"] > 0) {
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
						} else {
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
						}
					}
				}
			} else {

				foreach ($times as $index => $time) {

					if (strpos($time, "min") !== false) {
						$array_data_times_values_min[] = 0;
					} elseif (strpos($time, "max") !== false) {
						$array_data_times_values_max[] = 0;
					} elseif (strpos($time, "porc_conf") !== false) {
						$array_qual_porc_conf[] = 0;
					} else {
						$array_data_times_values[$time] = 0;
					}

					$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
				}
			}

			$array_receptor_qual_variable_values_p[$date] = $array_data_times_values;
			$array_receptor_qual_variable_ranges_p[$date] = $array_data_times_ranges;

			foreach ($array_data_times_values_min as $index => $value) {
				$array_qual_intervalo_confianza[] = array((float) $value, (float) $array_data_times_values_max[$index]);
			}
		}

		$array_qual_intervalo_confianza = array_values($array_qual_intervalo_confianza);

		$view_data["array_receptor_qual_variable_values_p"] = $array_receptor_qual_variable_values_p;
		$view_data["array_receptor_qual_variable_ranges_p"] = $array_receptor_qual_variable_ranges_p;

		// $offsetKey = 71; // El desplazamiento que se necesita tomar
		// $n = array_keys($array_qual_intervalo_confianza); // Toma todas las keys del array real y las coloca en otra matriz
		// $count = array_search($offsetKey, $n); //<--- Devuelve la posición del desplazamiento del array usando array_search
		// $new_array_qual_intervalo_confianza = array_slice($array_qual_intervalo_confianza, 0, $count + 1, true);//<--- Cortar con el índice 0 como inicio y la posición +1 como lenght

		// $view_data["array_qual_intervalo_confianza"] = $new_array_qual_intervalo_confianza;
		$view_data["array_qual_intervalo_confianza"] = $array_qual_intervalo_confianza;
		$view_data["array_qual_porc_conf"] = $array_qual_porc_conf;
		$view_data["array_receptor_qual_variable_formatted_dates"] = $array_receptor_qual_variable_formatted_dates;



		// RESETEO $array_period
		$array_period = array();
		$array_times = array();
		$previous_date = $first_date;

		foreach ($period as $datetime) {

			$date = $datetime->format("Y-m-d");
			$time = $datetime->format("H");

			if ($previous_date == $date) {
				$array_times[] = "time_" . $time;
			} else {
				$array_times = array();
				$array_times[] = "time_" . $time;
			}

			$array_period[$date] = $array_times;
			$previous_date = $date;
		}



		// Si la variable es Velocidad del viento o Dirección del viento, armar un arreglo con los datos de cada variable para el Meteograma.
		if ($meteorological_variable->id == 1 || $meteorological_variable->id == 2) {

			$variable_vel_viento = $this->Air_variables_model->get_one(1);
			$view_data["variable_vel_viento"] = $variable_vel_viento;

			// Llamar a la configuración de Alertas de Pronóstico para configuración de colores de rangos en gráfico y calheatmap
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => 14, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
					"id_air_station" => $receptor->id,
					"id_air_sector" => $air_sector->id,
					"id_air_variable" => 1 // Velocidad del Viento
				),
			);
			$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
			$array_alerts_meteo_chart = array();
			$array_alerts_meteo_calheatmap_colors = array();
			$array_alerts_meteo_calheatmap_ranges = array();
			$array_alerts = array();

			if (count($alert_config_forecast)) {
				$alert_config = $alert_config_forecast->alert_config;
				if (count($alert_config)) {
					foreach ($alert_config as $config) {

						if ($config->nc_active) {
							$array_alerts_meteo_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
							$array_alerts_meteo_calheatmap_colors[] = $config->nc_color;
							$array_alerts_meteo_calheatmap_ranges[] = $config->min_value;
							$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
						}
					}
				}
			}

			// Para el gráfico, una zona será de cierto color hasta el valor anterior al que se le indique, que es hasta donde se extiende la zona,
			// es por esto que se deben mover los colores de las alertas:
			$array_alerts_meteo_chart_final = array();
			$i = 0;
			$prev_color = "";
			foreach ($array_alerts_meteo_chart as $alert) {
				if ($i == 0) { //primer loop
					$prev_color = $alert["color"];
					$i++;
					continue;
				} else {
					$array_alerts_meteo_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
				}
				$prev_color = $alert["color"];
				$i++;
			}

			$array_alerts_meteo_chart_final[] = array("color" => end($array_alerts_meteo_chart)["color"]);
			//$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart;
			$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart_final;
			$view_data["array_alerts_meteo_calheatmap_colors"] = $array_alerts_meteo_calheatmap_colors;
			//unset($array_alerts_meteo_calheatmap_ranges[count($array_alerts_meteo_calheatmap_ranges)-1]);
			array_shift($array_alerts_meteo_calheatmap_ranges);
			$view_data["array_alerts_meteo_calheatmap_ranges"] = $array_alerts_meteo_calheatmap_ranges;


			// Configuración de Unidades de Reporte
			$id_report_unit_setting_meteo_vel = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => 10, // Velocidad
					"deleted" => 0
				)
			)->id_unidad;
			$unit_meteo_vel = $this->Unity_model->get_one($id_report_unit_setting_meteo_vel);
			$view_data["unit_meteo_vel"] = $unit_meteo_vel;
			$view_data["unit_type_meteo_vel"] = $this->Unity_type_model->get_one($unit_meteo_vel->id_tipo_unidad)->nombre;

			$id_report_unit_setting_meteo_dir = $this->Reports_units_settings_model->get_one_where(
				array(
					"id_cliente" => $id_cliente,
					"id_proyecto" => $id_proyecto,
					"id_tipo_unidad" => 11, // Dirección
					"deleted" => 0
				)
			)->id_unidad;
			$unit_meteo_dir = $this->Unity_model->get_one($id_report_unit_setting_meteo_dir);
			$view_data["unit_meteo_dir"] = $unit_meteo_dir;
			$view_data["unit_type_meteo_dir"] = $this->Unity_type_model->get_one($unit_meteo_dir->id_tipo_unidad)->nombre;

			// Trae la última carga de datos (1D) de una variable en una fecha específica 
			$array_receptor_meteo_data_values_p_vel = array();
			$array_receptor_meteo_data_ranges_p_vel = array();
			$array_receptor_meteo_variable_formatted_dates = array();

			foreach ($array_period as $date => $times) {

				$array_receptor_meteo_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

				if ($air_record->id) {

					$value_p_vel = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
						array(
							"id_variable" => 1, // Velocidad del viento
							"id_record" => $air_record->id,
							"date" => $date
						)
					)->row();

					if ($value_p_vel->id) {

						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($value_p_vel as $field => $value) {
							if (in_array($field, $times)) {

								$range = "-";
								$prev_min_value = 0;
								foreach ($array_alerts as $alert) {
									if ($value <= $alert["min_value"]) {
										if ($prev_min_value) {
											$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										} else {
											$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										}
										break;
									}
									$prev_min_value = $alert["min_value"];
								}

								// comparar si $value > al ultimo valor mímimo de alertas. Si se da esa condición setear $range = "más de [ultimo valor minimo de alertas]"
								if ($value > end($array_alerts)["min_value"]) {
									$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
								}

								$array_data_times_values[$field] = $value;
								$array_data_times_ranges[$field] = $range;
							}
						}
						$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
					} else {
						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($times as $index => $time) {
							$array_data_times_values[$time] = 0;
							if ($array_alerts[0]["min_value"] > 0) {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
							} else {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
							}
						}
						$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
					}
				} else {

					$array_data_times_values = array();
					$array_data_times_ranges = array();

					foreach ($times as $index => $time) {
						$array_data_times_values[$time] = 0;
						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
					}
					$array_receptor_meteo_data_values_p_vel[$date] = $array_data_times_values;
					$array_receptor_meteo_data_ranges_p_vel[$date] = $array_data_times_ranges;
				}
			}

			// Trae la última carga de datos (1D) de una variable en una fecha específica 
			$array_receptor_meteo_data_values_p_dir = array();
			foreach ($array_period as $date => $times) {

				if ($air_record->id) {

					$value_p_dir = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
						array(
							"id_variable" => 2, // Dirección del viento
							"id_record" => $air_record->id,
							"date" => $date
						)
					)->row();

					if ($value_p_dir->id) {
						$array_data_times_values = array();
						foreach ($value_p_dir as $field => $value) {
							if (in_array($field, $times)) {
								$array_data_times_values[$field] = $value;
							}
						}
						$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
					} else {
						$array_data_times_values = array();
						foreach ($times as $index => $time) {
							$array_data_times_values[$time] = 0;
						}
						$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
					}
				} else {

					$array_data_times_values = array();
					foreach ($times as $index => $time) {
						$array_data_times_values[$time] = 0;
					}
					$array_receptor_meteo_data_values_p_dir[$date] = $array_data_times_values;
				}
			}

			$view_data["array_receptor_meteo_data_values_p_dir"] = $array_receptor_meteo_data_values_p_dir;
			$view_data["array_receptor_meteo_data_values_p_vel"] = $array_receptor_meteo_data_values_p_vel;
			$view_data["array_receptor_meteo_data_ranges_p_vel"] = $array_receptor_meteo_data_ranges_p_vel;
			$view_data["array_receptor_meteo_variable_formatted_dates"] = $array_receptor_meteo_variable_formatted_dates;
		} else {


			// Llamar a la configuración de Alertas de Pronóstico para configuración de colores de rangos en gráfico y calheatmap
			$config_options = array(
				"id_client" => $id_cliente,
				"id_project" => $id_proyecto,
				"id_client_module" => 14, // Módulo de Pronóstico
				"id_client_submodule" => 0, // Sin submódulo
				"alert_config" => array(
					"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
					"id_air_station" => $receptor->id,
					"id_air_sector" => $air_sector->id,
					"id_air_variable" => $meteorological_variable->id
				),
			);
			$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
			$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
			$array_alerts_meteo_chart = array();
			$array_alerts_meteo_calheatmap_colors = array();
			$array_alerts_meteo_calheatmap_ranges = array();
			$array_alerts = array();

			if (count($alert_config_forecast)) {
				$alert_config = $alert_config_forecast->alert_config;
				if (count($alert_config)) {
					foreach ($alert_config as $config) {

						if ($config->nc_active) {
							$array_alerts_meteo_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
							$array_alerts_meteo_calheatmap_colors[] = $config->nc_color;
							$array_alerts_meteo_calheatmap_ranges[] = $config->min_value;
							$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
						}
					}
				}
			}

			// Para el gráfico, una zona será de cierto color hasta el valor anterior al que se le indique, que es hasta donde se extiende la zona,
			// es por esto que se deben mover los colores de las alertas:
			$array_alerts_meteo_chart_final = array();
			$i = 0;
			$prev_color = "";
			foreach ($array_alerts_meteo_chart as $alert) {
				if ($i == 0) { //primer loop
					$prev_color = $alert["color"];
					$i++;
					continue;
				} else {
					$array_alerts_meteo_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
				}
				$prev_color = $alert["color"];
				$i++;
			}

			$array_alerts_meteo_chart_final[] = array("color" => end($array_alerts_meteo_chart)["color"]);
			//$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart;
			$view_data["array_alerts_meteo_chart"] = $array_alerts_meteo_chart_final;
			$view_data["array_alerts_meteo_calheatmap_colors"] = $array_alerts_meteo_calheatmap_colors;
			array_shift($array_alerts_meteo_calheatmap_ranges);
			$view_data["array_alerts_meteo_calheatmap_ranges"] = $array_alerts_meteo_calheatmap_ranges;


			// Trae la última carga de datos (1D) de una variable en una fecha específica 
			$array_receptor_meteo_data_values_p = array();
			$array_receptor_meteo_data_ranges_p = array();
			$array_receptor_meteo_variable_formatted_dates = array();

			foreach ($array_period as $date => $times) {

				$array_receptor_meteo_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

				if ($air_record->id && $meteorological_variable->id) {

					$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
						array(
							"id_variable" => $meteorological_variable->id,
							"id_record" => $air_record->id,
							"date" => $date
						)
					)->row();

					if ($value_p->id) {

						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($value_p as $field => $value) {
							if (in_array($field, $times)) {

								$range = "-";
								$prev_min_value = 0;
								foreach ($array_alerts as $alert) {
									if ($value <= $alert["min_value"]) {
										if ($prev_min_value) {
											$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										} else {
											$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
										}
										break;
									}
									$prev_min_value = $alert["min_value"];
								}

								// comparar si $value > al ultimo valor mímimo de alertas. Si se da esa condición setear $range = "más de [ultimo valor minimo de alertas]"
								if ($value > end($array_alerts)["min_value"]) {
									$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
								}

								$array_data_times_values[$field] = $value;
								$array_data_times_ranges[$field] = $range;
							}
						}
						$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
					} else {
						$array_data_times_values = array();
						$array_data_times_ranges = array();

						foreach ($times as $index => $time) {
							$array_data_times_values[$time] = 0;
							if ($array_alerts[0]["min_value"] > 0) {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
							} else {
								$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
							}
						}
						$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
						$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
					}
				} else {

					$array_data_times_values = array();
					$array_data_times_ranges = array();

					foreach ($times as $index => $time) {
						$array_data_times_values[$time] = 0;
						$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
					}
					$array_receptor_meteo_data_values_p[$date] = $array_data_times_values;
					$array_receptor_meteo_data_ranges_p[$date] = $array_data_times_ranges;
				}
			}

			$view_data["array_receptor_meteo_data_values_p"] = $array_receptor_meteo_data_values_p;
			$view_data["array_receptor_meteo_data_ranges_p"] = $array_receptor_meteo_data_ranges_p;
			$view_data["array_receptor_meteo_variable_formatted_dates"] = $array_receptor_meteo_variable_formatted_dates;
		}

		echo json_encode($view_data);
	}

	/**
	 * get_data_by_model
	 * 
	 * Datos de variable de calidad del aire para el Receptor de un Sector.
	 * Carga los datos de pronóstico de la variable en una fecha y hora determinadas.
	 * Se utiliza en la vista principal del módulo de Pronósticos via Ajax que se ejecuta
	 * mediante el evento on_change de los selectores de variables y receptor, de las secciones de
	 * Modelo NEURONAL y Modelo MACHINE LEARNING, para actualizar los datos de los gráficos y calheatmap.
	 *
	 * @author Gustavo Pinochet Altamirano
	 * @access public
	 * @uses int $this->input->post('id_sector') id del Sector
	 * @uses int $this->input->post('id_air_quality_variable') id de variable de tipo Calidad del aire
	 * @uses int $this->input->post('id_receptor') id del Receptor
	 * @uses int $this->input->post("id_model") id del Modelo
	 * @uses int $this->login_user->client_id id de Cliente perteneciente al Usuario en sesión
	 * @uses int $this->session->project_context id de Proyecto en el que se encuentra navegando el Usuario
	 * @return JSON con datos asociados a la variable del receptor y pronóstico de un Sector
	 */
	function get_data_by_model()
	{

		$id_cliente = $this->login_user->client_id;
		$id_proyecto = $this->session->project_context;

		$id_sector = $this->input->post('id_sector');
		$air_sector = $this->Air_sectors_model->get_one($id_sector);
		$view_data["sector_info"] = $air_sector;

		$id_air_quality_variable = $this->input->post('id_air_quality_variable');
		$air_quality_variable = ($id_air_quality_variable) ? $this->Air_variables_model->get_details(array("id" => $id_air_quality_variable))->row() : null;
		$view_data["air_quality_variable"] = $air_quality_variable;

		$id_receptor = $this->input->post('id_receptor');
		$receptor = $this->Air_stations_model->get_one($id_receptor);

		$id_model = $this->input->post("id_model");


		// Configuración de Unidades de Reporte
		$id_report_unit_setting_qual = $this->Reports_units_settings_model->get_one_where(
			array(
				"id_cliente" => $id_cliente,
				"id_proyecto" => $id_proyecto,
				"id_tipo_unidad" => $air_quality_variable->id_unit_type,
				"deleted" => 0
			)
		)->id_unidad;
		$unit_qual = $this->Unity_model->get_one($id_report_unit_setting_qual);
		$view_data["unit_qual"] = $unit_qual;
		$view_data["unit_type_qual"] = $this->Unity_type_model->get_one($unit_qual->id_tipo_unidad)->nombre;


		// Si hay al menos un receptor, busca el registro asociado al cliente / proyecto / sector / receptor (estación) / modelo numérico / tipo de registro pronóstico
		if ($receptor->id) {
			$air_record = $this->Air_records_model->get_details(
				array(
					"id_client" => $id_cliente,
					"id_project" => $id_proyecto,
					"id_air_sector" => $air_sector->id,
					"id_air_station" => $receptor->id,
					"id_air_model" => $id_model,
					"id_air_record_type" => 2 // Pronóstico
				)
			)->row();
		}


		// Variables de fechas y horas de los datos
		$first_datetime = convert_date_utc_to_local_mimasoft(get_current_utc_time(), "Y-m-d H:i", $this->session->project_context);
		$first_datetime = new DateTime($first_datetime);
		$first_datetime->setTime(0, 0, 0);
		$first_datetime = $first_datetime->format("Y-m-d H:i");

		$last_datetime = new DateTime($first_datetime);
		$last_datetime = $last_datetime->modify('+72 hours');
		$last_datetime = $last_datetime->format("Y-m-d H:i");

		$view_data["first_datetime"] = $first_datetime;

		$period = new DatePeriod(
			new DateTime($first_datetime),
			new DateInterval('PT1H'),
			new DateTime($last_datetime)
		);

		$array_period = array();
		$array_times = array();
		$previous_date = $first_date;

		foreach ($period as $datetime) {
			$date = $datetime->format("Y-m-d");
			$time = $datetime->format("H");

			if ($previous_date == $date) {
				$array_times[] = "time_" . $time;
				$array_times[] = "time_min_" . $time;
				$array_times[] = "time_max_" . $time;
				$array_times[] = "time_porc_conf_" . $time;
			} else {
				$array_times = array();
				$array_times[] = "time_" . $time;
				$array_times[] = "time_min_" . $time;
				$array_times[] = "time_max_" . $time;
				$array_times[] = "time_porc_conf_" . $time;
			}

			$array_period[$date] = $array_times;
			$previous_date = $date;
		}

		// Buscar los valores de la primera variable del filtro de variables de Calidad del aire para el receptor
		$array_receptor_qual_variable_values_p = array();
		$array_receptor_qual_variable_ranges_p = array();
		$array_qual_intervalo_confianza = array();
		$array_qual_porc_conf = array();
		$array_receptor_qual_variable_formatted_dates = array();

		// Llamar a la configuración de Alertas de Pronóstico para configuración de colores de rangos en gráfico y calheatmap
		$config_options = array(
			"id_client" => $id_cliente,
			"id_project" => $id_proyecto,
			"id_client_module" => 14, // Módulo de Pronóstico
			"id_client_submodule" => 0, // Sin submódulo
			"alert_config" => array(
				"air_config" => "forecast_alerts", // Acordeón Alertas de Pronóstico
				"id_air_station" => $receptor->id,
				"id_air_sector" => $air_sector->id,
				"id_air_variable" => $air_quality_variable->id
			),
		);
		$alert_config_air_forecast_alerts = $this->AYN_Alert_projects_model->get_alert_projects_config($config_options)->row();
		$alert_config_forecast = json_decode($alert_config_air_forecast_alerts->alert_config);
		$array_alerts_qual_chart = array();
		$array_alerts_qual_calheatmap_colors = array();
		$array_alerts_qual_calheatmap_ranges = array();
		$array_alerts = array();

		if (count($alert_config_forecast)) {
			$alert_config = $alert_config_forecast->alert_config;
			if (count($alert_config)) {
				foreach ($alert_config as $config) {

					if ($config->nc_active) {
						$array_alerts_qual_chart[] = array("color" => $config->nc_color, "value" => $config->min_value);
						$array_alerts_qual_calheatmap_colors[] = $config->nc_color;
						$array_alerts_qual_calheatmap_ranges[] = $config->min_value;
						$array_alerts[] = array("nc_name" => $config->nc_name, "nc_color" => $config->nc_color, "min_value" => $config->min_value);
					}
				}
			}
		}

		// Para el gráfico, una zona será de cierto color hasta el valor anterior al que se le indique, que es hasta donde se extiende la zona,
		// es por esto que se deben mover los colores de las alertas:
		$array_alerts_qual_chart_final = array();
		$i = 0;
		$prev_color = "";
		foreach ($array_alerts_qual_chart as $alert) {
			if ($i == 0) { //primer loop
				$prev_color = $alert["color"];
				$i++;
				continue;
			} else {
				$array_alerts_qual_chart_final[] = array("color" => $prev_color, "value" => $alert["value"]);
			}
			$prev_color = $alert["color"];
			$i++;
		}

		$array_alerts_qual_chart_final[] = array("color" => end($array_alerts_qual_chart)["color"]);
		//$view_data["array_alerts_qual_chart"] = $array_alerts_qual_chart;
		$view_data["array_alerts_qual_chart"] = $array_alerts_qual_chart_final;
		$view_data["array_alerts_qual_calheatmap_colors"] = $array_alerts_qual_calheatmap_colors;
		array_shift($array_alerts_qual_calheatmap_ranges);
		$view_data["array_alerts_qual_calheatmap_ranges"] = $array_alerts_qual_calheatmap_ranges;


		foreach ($array_period as $date => $times) {

			$array_receptor_qual_variable_formatted_dates[$date] = get_date_format($date, $id_proyecto);

			$array_data_times_values = array();
			$array_data_times_ranges = array();
			$array_data_times_values_min = array();
			$array_data_times_values_max = array();

			// Trae la última carga de datos (1D) de una fecha específica
			if ($air_quality_variable->id && $air_record->id) {

				$value_p = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
					array(
						"id_variable" => $air_quality_variable->id,
						"id_record" => $air_record->id,
						"date" => $date
					)
				)->row();

				if ($value_p->id) {

					foreach ($value_p as $field => $value) {
						if (in_array($field, $times)) {

							$range = "-";
							$prev_min_value = 0;
							foreach ($array_alerts as $alert) {
								if ($value <= $alert["min_value"]) {
									if ($prev_min_value) {
										$range = lang("between") . " " . to_number_project_format($prev_min_value, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									} else {
										$range = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($alert["min_value"], $id_proyecto);
									}
									break;
								}
								$prev_min_value = $alert["min_value"];
							}

							// comparar si $value > al ultimo valor mímimo de alertas. Si se da esa condición setear $range = "más de [ultimo valor minimo de alertas]"
							if ($value > end($array_alerts)["min_value"]) {
								$range = lang("more_than") . " " . to_number_project_format(end($array_alerts)["min_value"], $id_proyecto);
							}

							if (strpos($field, "min") !== false) {
								$array_data_times_values_min[] = $value;
							} elseif (strpos($field, "max") !== false) {
								$array_data_times_values_max[] = $value;
							} elseif (strpos($field, "porc_conf") !== false) {
								$array_qual_porc_conf[] = (float) $value;
							} else {
								$array_data_times_values[$field] = $value;
								$array_data_times_ranges[$field] = $range;
							}
						}
					}
				} else {

					foreach ($times as $index => $time) {

						if (strpos($time, "min") !== false) {
							$array_data_times_values_min[] = 0;
						} elseif (strpos($time, "max") !== false) {
							$array_data_times_values_max[] = 0;
						} elseif (strpos($time, "porc_conf") !== false) {
							$array_qual_porc_conf[] = 0;
						} else {
							$array_data_times_values[$time] = 0;
						}

						if ($array_alerts[0]["min_value"] > 0) {
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[0]["min_value"], $id_proyecto);
						} else {
							$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format($array_alerts[1]["min_value"], $id_proyecto);
						}
					}
				}
			} else {

				foreach ($times as $index => $time) {

					if (strpos($time, "min") !== false) {
						$array_data_times_values_min[] = 0;
					} elseif (strpos($time, "max") !== false) {
						$array_data_times_values_max[] = 0;
					} elseif (strpos($time, "porc_conf") !== false) {
						$array_qual_porc_conf[] = 0;
					} else {
						$array_data_times_values[$time] = 0;
					}

					$array_data_times_ranges[$time] = lang("between") . " " . to_number_project_format(0, $id_proyecto) . " - " . to_number_project_format(0, $id_proyecto);
				}
			}

			$array_receptor_qual_variable_values_p[$date] = $array_data_times_values;
			$array_receptor_qual_variable_ranges_p[$date] = $array_data_times_ranges;

			foreach ($array_data_times_values_min as $index => $value) {
				$array_qual_intervalo_confianza[] = array((float) $value, (float) $array_data_times_values_max[$index]);
			}
		}

		$array_qual_intervalo_confianza = array_values($array_qual_intervalo_confianza);

		$view_data["array_receptor_qual_variable_values_p"] = $array_receptor_qual_variable_values_p;
		$view_data["array_receptor_qual_variable_ranges_p"] = $array_receptor_qual_variable_ranges_p;

		// $offsetKey = 71; // El desplazamiento que se necesita tomar
		// $n = array_keys($array_qual_intervalo_confianza); // Toma todas las keys del array real y las coloca en otra matriz
		// $count = array_search($offsetKey, $n); //<--- Devuelve la posición del desplazamiento del array usando array_search
		// $new_array_qual_intervalo_confianza = array_slice($array_qual_intervalo_confianza, 0, $count + 1, true);//<--- Cortar con el índice 0 como inicio y la posición +1 como lenght

		// $view_data["array_qual_intervalo_confianza"] = $new_array_qual_intervalo_confianza;
		$view_data["array_qual_intervalo_confianza"] = $array_qual_intervalo_confianza;
		$view_data["array_qual_porc_conf"] = $array_qual_porc_conf;
		$view_data["array_receptor_qual_variable_formatted_dates"] = $array_receptor_qual_variable_formatted_dates;

		echo json_encode($view_data);
	}

	function get_excel($id_sector = 0)
	{

		// DATOS
		$array_data_by_station = array();

		// Sector
		$air_sector = $this->Air_sectors_model->get_one($id_sector);

		// Estaciones del sector
		$air_stations = $this->Air_stations_model->get_all_where(
			array(
				"id_air_sector" => $air_sector->id,
				"is_active" => 1,
				"is_forecast" => 1,
				//"is_receptor" => 1,
				"deleted" => 0
			)
		)->result();

		// Ids de los modelos asociados al sector
		$id_models_of_sector = json_decode($air_sector->air_models);
		$array_max_dates_by_station = array();

		foreach ($air_stations as $station) {

			$station_variables = $this->Air_variables_model->get_variables_of_station($station->id)->result();

			foreach ($station_variables as $station_variable) {

				// Solo se deben mostrar valores de la variable PM10. Si la Estación no tiene la variable PM10, continuar a la siguiente.
				if ($station_variable->id_variable != 9) {
					continue;
				} else {
					if (in_array(3, $id_models_of_sector)) { // Si el Sector tiene Modelo Numérico
						$air_record = $this->Air_records_model->get_details(
							array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_air_sector" => $air_sector->id,
								"id_air_station" => $station->id,
								"id_air_model" => 3, // Numérico
								"id_air_record_type" => 2 // Pronóstico
							)
						)->row();
						if ($air_record->id) {
							$value_p_num = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
								array(
									"id_variable" => $station_variable->id_variable,
									"id_record" => $air_record->id,
									//"date" => $date
								)
							)->row();
						}
					}
					if (in_array(2, $id_models_of_sector)) { // Modelo NEURONAL (Arima)
						$air_record = $this->Air_records_model->get_details(
							array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_air_sector" => $air_sector->id,
								"id_air_station" => $station->id,
								"id_air_model" => 2, // NEURONAL
								"id_air_record_type" => 2 // Pronóstico
							)
						)->row();
						if ($air_record->id) {
							$value_p_est = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
								array(
									"id_variable" => $station_variable->id_variable,
									"id_record" => $air_record->id,
									//"date" => $date
								)
							)->row();
						}
					}
					if (in_array(1, $id_models_of_sector)) { // Modelo MACHINE LEARNING
						$air_record = $this->Air_records_model->get_details(
							array(
								"id_client" => $id_cliente,
								"id_project" => $id_proyecto,
								"id_air_sector" => $air_sector->id,
								"id_air_station" => $station->id,
								"id_air_model" => 1, // MACHINE LEARNING
								"id_air_record_type" => 2 // Pronóstico
							)
						)->row();
						if ($air_record->id) {
							$value_p_neu = $this->Air_records_values_p_model->get_last_upload_data_1D_by_date(
								array(
									"id_variable" => $station_variable->id_variable,
									"id_record" => $air_record->id,
									//"date" => $date
								)
							)->row();
						}
					}

					$array_dates = array(strtotime($value_p_num->date), strtotime($value_p_est->date), strtotime($value_p_neu->date));
					$max_date = max($array_dates) ? max($array_dates) : false;

					$array_max_dates_by_station[$station->id] = $max_date;

					// Si el Sector tiene Modelo Numérico
					if (in_array(3, $id_models_of_sector)) {
						$array_data_by_station[$station->id][3] = ($value_p_num->date == date("Y-m-d", $max_date)) ? $value_p_num : null;
					}
					// Modelo NEURONAL (Arima)
					if (in_array(2, $id_models_of_sector)) {
						$array_data_by_station[$station->id][2] = ($value_p_est->date == date("Y-m-d", $max_date)) ? $value_p_est : null;
					}
					// Modelo MACHINE LEARNING
					if (in_array(1, $id_models_of_sector)) {
						$array_data_by_station[$station->id][1] = ($value_p_neu->date == date("Y-m-d", $max_date)) ? $value_p_neu : null;
					}
				}
			}
		}

		$max_date_of_stations = max($array_max_dates_by_station) ? date("Y-m-d", max($array_max_dates_by_station)) : false;

		$array_data_by_station_final = array();
		foreach ($array_data_by_station as $id_station => $values_p_by_model) {
			foreach ($values_p_by_model as $id_model => $value_p) {
				if ($value_p->date == $max_date_of_stations) {
					$array_data_by_station_final[$id_station][$id_model] = $value_p;
				} else {
					$array_data_by_station_final[$id_station][$id_model] = null;
				}
			}
		}

		// ARMADO EXCEL
		$this->load->library('excel');

		$doc = new PHPExcel();
		$doc->getProperties()->setCreator("MIMAire")
			->setLastModifiedBy("MIMAire")
			->setTitle("")
			->setSubject("")
			->setDescription("")
			->setKeywords("mimaire")
			->setCategory("excel");

		$client_info = $this->Clients_model->get_one($air_sector->id_client);
		$project_info = $this->Projects_model->get_one($air_sector->id_project);

		$doc->setActiveSheetIndex(0);

		// ESTILOS
		$style_header = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$col = 0; // EMPEZANDO DE LA COLUMNA 'A'
		$name_col = PHPExcel_Cell::stringFromColumnIndex($col);

		$row = 3;

		if ($max_date_of_stations) {

			$doc->getActiveSheet()->getColumnDimension($name_col)->setWidth(20);
			$doc->getActiveSheet()->setCellValue($name_col . "1", "");
			$doc->getActiveSheet()->setCellValue($name_col . "2", lang("date"));
			$doc->getActiveSheet()->getStyle($name_col . "1")->applyFromArray($style_header);
			$doc->getActiveSheet()->getStyle($name_col . "2")->applyFromArray($style_header);

			for ($i = 0; $i <= 23; $i++) {
				$hour = ($i <= 9) ? "0" . $i . ":00" : $i . ":00";
				$doc->getActiveSheet()->setCellValue('A' . $row, $fecha = get_date_format($max_date_of_stations, $project_info->id) . " " . $hour);
				$row++;
			}

			$col++; // COLUMNA 'B'

			foreach ($array_data_by_station_final as $id_station => $values_p_by_model) {

				$station = $this->Air_stations_model->get_one($id_station);

				$first_name_col_station = PHPExcel_Cell::stringFromColumnIndex($col);
				$last_name_col_station = PHPExcel_Cell::stringFromColumnIndex($col + 2);
				$doc->getActiveSheet()->mergeCells($first_name_col_station . '1' . ':' . $last_name_col_station . '1');
				$doc->getActiveSheet()->setCellValue($first_name_col_station . '1', $station->name);
				$doc->getActiveSheet()->getStyle($first_name_col_station . '1')->applyFromArray($style_header);

				foreach ($values_p_by_model as $id_model => $value_p) {

					$model = $this->Air_models_model->get_one($id_model);
					$name_col_model = PHPExcel_Cell::stringFromColumnIndex($col);

					$doc->getActiveSheet()->getColumnDimension($name_col_model)->setWidth(15);
					$doc->getActiveSheet()->setCellValue($name_col_model . '2', lang($model->name));
					$doc->getActiveSheet()->getStyle($name_col_model . '2')->applyFromArray($style_header);

					$array_value_p = (array) $value_p;
					$row = 3;

					foreach ($array_value_p as $field => $value) {
						if (strpos($field, 'time_') !== false) {
							$doc->getActiveSheet()->getStyle($name_col_model . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
							$doc->getActiveSheet()->setCellValue($name_col_model . $row, $value);
							$row++;
						}
					}

					$col++;
				}

				$col++;
			}
		}

		$nombre_hoja = strlen(lang("forecast")) > 31 ? substr(lang("forecast"), 0, 28) . '...' : lang("forecast");
		$nombre_hoja = $nombre_hoja ? $nombre_hoja : " ";
		$doc->getActiveSheet()->setTitle($nombre_hoja);

		$filename = $client_info->sigla . "_" . $project_info->sigla . "_" . lang("forecast") . "_" . $air_sector->name . "_" . date('Y-m-d');
		$filename = $filename . '.xlsx'; //save our workbook as this file name

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
		header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}

