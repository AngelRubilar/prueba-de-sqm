<?php

function init_settings() {
    $ci = & get_instance();
	
    $settings = $ci->Settings_model->get_all()->result();
    foreach ($settings as $setting) {
		
        $ci->config->set_item($setting->setting_name, $setting->setting_value);
    }

    $language = get_setting("language");
	$language = $settings->language;
	
    $ci->lang->load('default', $language);
    $ci->lang->load('custom', $language); //load custom after loading the default. because custom will overwrite the default file.
}


/* function init_settings() {
	
	$ci = & get_instance();
	
	$settings = $ci->Settings_model->get_all()->result();
	
    foreach ($settings as $setting) {
        $ci->config->set_item($setting->setting_name, $setting->setting_value);
    }
	
	$id_cliente = $ci->login_user->client_id;
	$id_proyecto = $ci->session->project_context;
	//if($ci->login_user->user_type == "client" && $ci->session->project_context){
	if($ci->login_user->user_type == "client"){
		if($id_proyecto){
			$setting = $ci->General_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto));
			$language = $setting->language;
			$ci->config->set_item("language",$language);
			
		}else{
			$language = "spanish";
			$ci->config->set_item("language", $language);
		}
	} else {
		$language = "spanish";
		$ci->config->set_item("language", $language);
	}

    $ci->lang->load('default', $language);
    $ci->lang->load('custom', $language); //load custom after loading the default. because custom will overwrite the default file.
}   
 */