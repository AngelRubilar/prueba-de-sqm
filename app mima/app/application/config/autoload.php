<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------
  | AUTO-LOADER
  | -------------------------------------------------------------------
  | This file specifies which systems should be loaded by default.
  |
  | In order to keep the framework as light-weight as possible only the
  | absolute minimal resources are loaded by default. For example,
  | the database is not connected to automatically since no assumption
  | is made regarding whether you intend to use it.  This file lets
  | you globally define which systems you would like loaded with every
  | request.
  |
  | -------------------------------------------------------------------
  | Instructions
  | -------------------------------------------------------------------
  |
  | These are the things you can load automatically:
  |
  | 1. Packages
  | 2. Libraries
  | 3. Drivers
  | 4. Helper files
  | 5. Custom config files
  | 6. Language files
  | 7. Models
  |
 */

/*
  | -------------------------------------------------------------------
  |  Auto-load Packages
  | -------------------------------------------------------------------
  | Prototype:
  |
  |  $autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
  |
 */
$autoload['packages'] = array();

/*
  | -------------------------------------------------------------------
  |  Auto-load Libraries
  | -------------------------------------------------------------------
  | These are the classes located in system/libraries/ or your
  | application/libraries/ directory, with the addition of the
  | 'database' library, which is somewhat of a special case.
  |
  | Prototype:
  |
  |	$autoload['libraries'] = array('database', 'email', 'session');
  |
  | You can also supply an alternative library name to be assigned
  | in the controller:
  |
  |	$autoload['libraries'] = array('user_agent' => 'ua');
 */
$autoload['libraries'] = array('database', 'session', 'form_validation', 'encrypt', 'template', 'finediff', 'parser');

/*
  | -------------------------------------------------------------------
  |  Auto-load Drivers
  | -------------------------------------------------------------------
  | These classes are located in system/libraries/ or in your
  | application/libraries/ directory, but are also placed inside their
  | own subdirectory and they extend the CI_Driver_Library class. They
  | offer multiple interchangeable driver options.
  |
  | Prototype:
  |
  |	$autoload['drivers'] = array('cache');
 */
$autoload['drivers'] = array();

/*
  | -------------------------------------------------------------------
  |  Auto-load Helper Files
  | -------------------------------------------------------------------
  | Prototype:
  |
  |	$autoload['helper'] = array('url', 'file');
 */
$autoload['helper'] = array(
	'url', 
	'file', 
	'form', 
	'language', 
	'general', 
	'date_time', 
	'app_files', 
	'widget', 
	'activity_logs', 
	'currency', 
	'database',
	'alerts_and_notifications',
	'footprints',
	'forecast_email_alert_helper'
);

/*
  | -------------------------------------------------------------------
  |  Auto-load Config files
  | -------------------------------------------------------------------
  | Prototype:
  |
  |	$autoload['config'] = array('config1', 'config2');
  |
  | NOTE: This item is intended for use ONLY if you have created custom
  | config files.  Otherwise, leave it blank.
  |
 */
$autoload['config'] = array('app');

/*
  | -------------------------------------------------------------------
  |  Auto-load Language files
  | -------------------------------------------------------------------
  | Prototype:
  |
  |	$autoload['language'] = array('lang1', 'lang2');
  |
  | NOTE: Do not include the "_lang" part of your file.  For example
  | "codeigniter_lang.php" would be referenced as array('codeigniter');
  |
 */
$autoload['language'] = array('default', 'custom');

/*
  | -------------------------------------------------------------------
  |  Auto-load Models
  | -------------------------------------------------------------------
  | Prototype:
  |
  |	$autoload['model'] = array('first_model', 'second_model');
  |
  | You can also supply an alternative model name to be assigned
  | in the controller:
  |
  |	$autoload['model'] = array('first_model' => 'first');
 */
$autoload['model'] = array(
    'Crud_model',
	'Crud_bd_fc_model',
    'Settings_model',
    'Api_model',
    'Users_model',
    'Team_model',
    //'Attendance_model',
    'Leave_types_model',
    'Leave_applications_model',
    'Events_model',
    //'Announcements_model',
    'Messages_model',
    'Clients_model',
    'Projects_model',
	'Industries_model',
    'Milestones_model',
    'Tasks_model',
    'Project_comments_model',
    'Activity_logs_model',
    'Project_files_model',
    'Notes_model',
    'Project_members_model',
    'Ticket_types_model',
    'Tickets_model',
    'Ticket_comments_model',
    'Items_model',
    'Invoices_model',
    'Invoice_items_model',
    'Invoice_payments_model',
    'Payment_methods_model',
    'Email_templates_model',
    'Roles_model',
    'Posts_model',
    'Timesheets_model',
    'Expenses_model',
    'Expense_categories_model',
    'Taxes_model',
    'Social_links_model',
    'Notification_settings_model',
    'Notifications_model',
    'Custom_fields_model',
    'Estimate_forms_model',
    'Estimate_requests_model',
    'Custom_field_values_model',
    'Estimates_model',
    'Estimate_items_model',
    'General_files_model',
    'Todo_model',
	'Field_types_model',
	'Fields_model',
	'Forms_model',
	'Form_types_model',
	'Fixed_fields_model',
	'Fixed_field_rel_form_rel_project_model',
	'Fixed_form_values_model',
	'Functional_units_model',
	'Environmental_records_model',
	'Form_rel_project_model',
	'Field_rel_form_model',
	'Characterization_factors_model',
	'Materials_model',
	'Values_model',
	'Form_rel_material_model',
	'General_settings_model',
	'Phases_model',
	'Project_rel_phases_model',
	'Reports_configuration_model',
	'Module_availability_model',
	'Rule_model',
	'Assignment_model',
	'Unit_processes_model',
	'Phase_rel_pu_model', 
	'Profiles_model',
	'Project_rel_pu_model',
	'Clients_modules_model',
	'Project_rel_activities_model',
	'Clients_modules_rel_profiles_model',
	'Methodology_model',
	'Footprints_model',
	'Subprojects_model',
	'Project_rel_footprints_model',
	'Subindustries_model',
	'Technologies_model',
	'Unity_type_model', 
	'Module_footprint_units_model',
	'Project_rel_material_model',
	'Methodology_rel_footprints_model',
	'Categories_model',
	'Subcategories_model',
	'Feeders_model',
	'Unity_model',
	'Form_values_model',
	'Other_records_model',
	'Faq_model',
	'Wiki_model',
	'Mimasoft_model',
	'Contact_model',
	'Materials_rel_category_model',
	'Calculation_model',
	'Databases_model',
	'Subcategory_rel_categories_model',
	'Categories_alias_model',
	'Form_rel_materiales_rel_categorias_model',
	'Client_environmental_footprints_settings_model',
	'Client_waste_settings_model',
	'FH_rel_methodology_model',
	'Client_consumptions_settings_model',
	'Reports_units_settings_model',
	'Reports_units_settings_clients_model',
	'Reports_model',
	'Conversion_model',
	'Compromises_rca_model',
	'Compromises_rca_rel_fields_model',
	'Compromises_reportables_rel_fields_model',
	'Plans_reportables_compromises_model',
	'Values_compromises_reportables_model',
	'Compromises_reportables_model',
	'Compromises_compliance_evaluation_reportables_model',
	'Evaluated_rca_compromises_model',
	'Values_compromises_rca_model',
	'Compromises_compliance_status_model',
	'Indicators_model',
	'Compromises_compliance_evaluation_rca_model',
	'Compromises_compliance_evidences_model',
	'Thresholds_model',
	'Client_indicators_model',
	'Permitting_model',
	'Client_compromises_settings_model',
	'Tipo_tratamiento_model',
	'Permitting_rel_fields_model',
	'Evaluated_permitting_model',
	'Values_permitting_model',
	'Permitting_procedure_status_model',
	'Permitting_procedure_evaluation_model',
	'Permitting_procedure_evidences_model',
	'Client_permitting_settings_model',
	'Industries_rel_technologies_model',
	'Stakeholders_matrix_config_model',
	'Stakeholders_rel_fields_model',
	'Values_stakeholders_model',
	'Agreements_matrix_config_model',
	'Agreements_rel_fields_model',
	'Values_agreements_model',
	'Feedback_matrix_config_model',
	'Feedback_rel_fields_model',
	'Values_feedback_model',
	'Communities_evaluation_status_model',
	'Agreements_evidences_model',
	'Agreements_monitoring_model',
	'Types_of_organization_model',
	'Feedback_monitoring_model',
	'Feedback_monitoring_evidences_model',
	'Footprint_format_model',
	'Assignment_combinations_model',
	'Client_module_availability_model',
	'Clients_submodules_model',
	'Client_context_modules_model',
	'Client_context_submodules_model',
	'Fontawesome_model',
	'Countries_model',
	'Critical_levels_model',
	'Home_modules_info_model',
	'Client_context_profiles_model',
	'Client_context_modules_rel_profiles_model',
	'AYN_Admin_modules_model',
	'AYN_Admin_submodules_model',
	'AYN_Alert_historical_model',
	'AYN_Alert_historical_users_model',
	'AYN_Alert_projects_groups_model',
	'AYN_Alert_projects_model',
	'AYN_Alert_projects_users_model',
	'AYN_Clients_groups_model',
	'AYN_Notif_general_groups_model',
	'AYN_Notif_general_model',
	'AYN_Notif_general_users_model',
	'AYN_Notif_historical_model',
	'AYN_Notif_historical_users_model',
	'AYN_Notif_projects_admin_groups_model',
	'AYN_Notif_projects_admin_model',
	'AYN_Notif_projects_admin_users_model',
	'AYN_Notif_projects_clients_groups_model',
	'AYN_Notif_projects_clients_model',
	'AYN_Notif_projects_clients_users_model',
	'General_settings_clients_model',
	'KPI_Charts_structure_model',
	'KPI_Report_structure_model',
	'KPI_Report_templates_model',
	'KPI_Values_condition_model',
	'KPI_Values_model',
	'EC_Client_transformation_factors_config_model',
	'EC_Types_of_origin_model',
	'EC_Types_of_origin_matter_model',
	'EC_Types_no_apply_model',
	'Air_models_model',
	'Air_stations_model',
	'Air_stations_rel_variables_model',
	'Air_records_model',
	'Air_records_types_model',
	'Air_records_values_p_model',
	'Air_records_values_uploads_model',
	'Air_sectors_model',
	'Air_variables_model',
	'Air_variables_types_model',
	'AYN_Alert_historical_air_model',
	'Air_files_model',
	'Air_synoptic_data_model',
	'Air_stations_values_1m_model',
	'Air_stations_values_5m_model',
	'Air_stations_values_15m_model',
	'Air_stations_values_1h_model',
	'Air_records_values_p_min_model',
	'Air_records_values_p_max_model',
	'Air_records_values_p_porc_conf_model',
	'Logs_model'
);
