<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_templates extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin();
    }

    private function _templates() {
        return array(
            //"login_info" => array("USER_FIRST_NAME", "USER_LAST_NAME", "DASHBOARD_URL", "USER_LOGIN_EMAIL", "USER_LOGIN_PASSWORD", "SIGNATURE"),
            //"reset_password" => array("ACCOUNT_HOLDER_NAME", "RESET_PASSWORD_URL", "SITE_URL", "SIGNATURE"),
            //"team_member_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "SIGNATURE"),
            //"client_contact_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "SIGNATURE"),
            //"send_invoice" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "SIGNATURE", "INVOICE_URL"),
            //"invoice_payment_confirmation" => array("INVOICE_ID", "PAYMENT_AMOUNT", "INVOICE_URL", "SIGNATURE"),
            //"ticket_created" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "SIGNATURE"),
            //"ticket_commented" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "SIGNATURE"),
            //"ticket_closed" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "SIGNATURE"),
            //"ticket_reopened" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "SIGNATURE"),
            //"general_notification" => array("EVENT_TITLE", "EVENT_DETAILS", "APP_TITLE", "COMPANY_NAME", "NOTIFICATION_URL", "SIGNATURE"),
            //"message_received" => array("SUBJECT", "USER_NAME", "MESSAGE_CONTENT", "MESSAGE_URL", "APP_TITLE", "SIGNATURE"),
            "ayn_notification_general" => array("SITE_URL", "USER_TO_NOTIFY_NAME", "USER_ACTION_NAME", "EVENT", "SUBMODULE_NAME", "MODULE_NAME", "NOTIFIED_DATE", "CONTACT_URL", "SIGNATURE"),
			"ayn_notification_projects_clients" => array("SITE_URL", "USER_TO_NOTIFY_NAME", "USER_ACTION_NAME", "EVENT", "ELEMENT", "MODULE_NAME", "PROJECT_NAME", "NOTIFIED_DATE", "CONTACT_URL", "SIGNATURE"),
			"ayn_notification_projects_admin" => array("SITE_URL", "USER_TO_NOTIFY_NAME", "USER_ACTION_NAME", "EVENT", "PROJECT_NAME", "NOTIFIED_DATE", "CONTACT_URL", "SIGNATURE"),
			"ayn_alerts_admin" => array("SITE_URL", "USER_TO_NOTIFY_NAME", "MESSAGE_TYPE", "EVENT", "PROJECT_NAME", "ALERT_DATE", "ALERTED_USERS", "MODULE_NAME", "CONTACT_URL", "SIGNATURE"),
			"signature" => array(),
			"ayn_alerts_projects_air" => array("SITE_URL", "USER_TO_NOTIFY_NAME", "PROJECT_NAME", "ALERT_DATE", "MODEL_NAME", "ALERTED_USERS", "MODULE_NAME", "CONTACT_URL", "SIGNATURE"),
			"signature_air" => array()
        );
    }

    function index() {
        $this->template->rander("email_templates/index");
    }

    function save() {
		
		$custom_message = '<!doctype html>';
		$custom_message .= '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">';
		$custom_message .= '<head>';
		
			$custom_message .= '<title></title>';
			$custom_message .= '<!--[if !mso]><!-- -->';
			$custom_message .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
			$custom_message .= '<!--<![endif]-->';
			$custom_message .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
			$custom_message .= '<meta name="viewport" content="width=device-width,initial-scale=1">';
			$custom_message .= '<style type="text/css">';
			
				$custom_message .= '#outlook a {';
					$custom_message .= 'padding: 0;';
				$custom_message .= '}';
				
				$custom_message .= 'body {';
					$custom_message .= 'margin: 0;';
					$custom_message .= 'padding: 0;';
					$custom_message .= '-webkit-text-size-adjust: 100%;';
					$custom_message .= '-ms-text-size-adjust: 100%;';
				$custom_message .= '}';
				
				$custom_message .= 'table,';
				$custom_message .= 'td {';
					$custom_message .= 'border-collapse: collapse;';
					$custom_message .= 'mso-table-lspace: 0pt;';
					$custom_message .= 'mso-table-rspace: 0pt;';
				$custom_message .= '}';
				
				$custom_message .= 'img {';
					$custom_message .= 'border: 0;';
					$custom_message .= 'height: auto;';
					$custom_message .= 'line-height: 100%;';
					$custom_message .= 'outline: none;';
					$custom_message .= 'text-decoration: none;';
					$custom_message .= '-ms-interpolation-mode: bicubic;';
				$custom_message .= '}';
				
				$custom_message .= 'p {';
					$custom_message .= 'display: block;';
					$custom_message .= 'margin: 13px 0;';
				$custom_message .= '}';
				
			$custom_message .= '</style>';
			
			$custom_message .= '<!--[if mso]>';
				$custom_message .= '<xml>';
				$custom_message .= '<o:OfficeDocumentSettings>';
					$custom_message .= '<o:AllowPNG/>';
					$custom_message .= '<o:PixelsPerInch>96</o:PixelsPerInch>';
				$custom_message .= '</o:OfficeDocumentSettings>';
				$custom_message .= '</xml>';
			$custom_message .= '<![endif]-->';
			
			$custom_message .= '<!--[if lte mso 11]>';
				$custom_message .= '<style type="text/css">';
					$custom_message .= '.mj-outlook-group-fix { width:100% !important; }';
				$custom_message .= '</style>';
			$custom_message .= '<![endif]-->';
				
			$custom_message .= '<!--[if !mso]><!-->';
				$custom_message .= '<link href="https://fonts.googleapis.com/css?family=Lato:300,400,500,700" rel="stylesheet" type="text/css">';
				$custom_message .= '<style type="text/css">';
					$custom_message .= '@import url(https://fonts.googleapis.com/css?family=Lato:300,400,500,700);';
				$custom_message .= '</style>';
			$custom_message .= '<!--<![endif]-->';
			
			$custom_message .= '<style type="text/css">';
				$custom_message .= '@media only screen and (min-width:480px) {';
					$custom_message .= '.mj-column-per-33 {';
						$custom_message .= 'width: 33% !important;';
						$custom_message .= 'max-width: 33%;';
					$custom_message .= '}';
					$custom_message .= '.mj-column-per-66 {';
						$custom_message .= 'width: 66% !important;';
						$custom_message .= 'max-width: 66%;';
					$custom_message .= '}';
					$custom_message .= '.mj-column-per-100 {';
						$custom_message .= 'width: 100% !important;';
						$custom_message .= 'max-width: 100%;';
					$custom_message .= '}';
					$custom_message .= '.mj-column-per-33-333333333333336 {';
						$custom_message .= 'width: 33.333333333333336% !important;';
						$custom_message .= 'max-width: 33.333333333333336%;';
					$custom_message .= '}';
				$custom_message .= '}';
			$custom_message .= '</style>';
			
			$custom_message .= '<style type="text/css">';
				$custom_message .= '@media only screen and (max-width:480px) {';
					$custom_message .= 'table.mj-full-width-mobile {';
						$custom_message .= 'width: 100% !important;';
					$custom_message .= '}';
					$custom_message .= 'td.mj-full-width-mobile {';
						$custom_message .= 'width: auto !important;';
					$custom_message .= '}';
				$custom_message .= '}';
			$custom_message .= '</style>';
			
			$custom_message .= '<!--[if gte mso 9]>';
				$custom_message .= '<style type="text/css">';
				$custom_message .= 'img.header { width: 70px; }';
				$custom_message .= '</style>';
			$custom_message .= '<![endif]-->';

		$custom_message .= '<head>';	
		$custom_message .= '<body style="background-color:#eeeeef;">';	
		
			$custom_message .= str_replace(get_uri(), "{SITE_URL}", $this->input->post('custom_message'));
		
		$custom_message .= "</body>";
		$custom_message .= "</html>";
		
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');

        $data = array(
            "email_subject" => $this->input->post('email_subject'),
            "custom_message" => decode_ajax_post_data($custom_message)
        );
        $save_id = $this->Email_templates_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function restore_to_default() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $template_id = $this->input->post('id');

        $data = array(
            "custom_message" => ""
        );
        $save_id = $this->Email_templates_model->save($data, $template_id);
        if ($save_id) {
			
            $default_message = $this->Email_templates_model->get_one($save_id)->default_message;
			$default_message = str_replace("{SITE_URL}", get_uri(), $default_message);
			
            echo json_encode(array("success" => true, "data" => $default_message, 'message' => lang('template_restored')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function list_data() {
        $list = array();
        foreach ($this->_templates() as $template_name => $variables) {

            $list[] = array("<span class='template-row' data-name='$template_name'>" . lang($template_name) . "</span>");
        }
        echo json_encode(array("data" => $list));
    }

    /* load template edit form */

    function form($template_name = "") {
        $view_data['model_info'] = $this->Email_templates_model->get_one_where(array("template_name" => $template_name));
		
		$default_message = str_replace("{SITE_URL}", get_uri(),$view_data["model_info"]->default_message);
		$custom_message = str_replace("{SITE_URL}", get_uri(),$view_data["model_info"]->custom_message);
		$view_data["default_message"] = $default_message;
		$view_data["custom_message"] = $custom_message;
		
        $variables = get_array_value($this->_templates(), $template_name);
        $view_data['variables'] = $variables ? $variables : array();
        $this->load->view('email_templates/form', $view_data);
    }

}

/* End of file email_templates.php */
/* Location: ./application/controllers/email_templates.php */