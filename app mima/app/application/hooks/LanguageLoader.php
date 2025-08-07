<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
		
		$settings = $ci->Settings_model->get_all()->result();
		foreach ($settings as $setting) {
			$ci->config->set_item($setting->setting_name, $setting->setting_value);
		}
		
		if($ci->session->userdata('user_id')){
			
			$user_id = $ci->session->userdata('user_id');
			$user_data = $ci->Users_model->get_one_where(array("id" =>$user_id, "deleted" => 0));
			$language = $user_data->language;
			
			$ci->session->set_userdata('site_lang', $language);
			$site_lang = $ci->session->userdata('site_lang');
			if($site_lang){
				$ci->lang->load('default',$ci->session->userdata('site_lang'));
				$ci->lang->load('custom',$ci->session->userdata('site_lang'));
			}else{
				$ci->lang->load('default','spanish');
				$ci->lang->load('custom','spanish');
			}
			
		}else{
			$ci->lang->load('default','spanish');
			$ci->lang->load('custom','spanish');
		}
    }
}
