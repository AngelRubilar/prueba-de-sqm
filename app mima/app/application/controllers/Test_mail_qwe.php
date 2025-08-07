<?php

    class Test_mail_qwe extends MY_Controller{

        function __construct() {
            parent::__construct();
    
        }
    
        function index(){
            // PRUEBA ENVÍO DE CORREO
            $email_template = $this->Email_templates_model->get_final_template("ayn_notification_general");

            $parser_data["USER_TO_NOTIFY_NAME"] = "";
            $parser_data["USER_ACTION_NAME"] = "";
            $parser_data["EVENT"] = "";
            $parser_data["SUBMODULE_NAME"] = "";
            $parser_data["MODULE_NAME"] = "";
            $parser_data["NOTIFIED_DATE"] = "";
            $parser_data["CONTACT_URL"] = "";

            $parser_data_signature["SITE_URL"] = get_uri();

            $signature_template = $this->Email_templates_model->get_one_where(array("template_name" => "signature", "deleted" => 0));
            $signature = ($signature_template->custom_message) ? $signature_template->custom_message : $signature_template->default_message;
            $signature_message = $this->parser->parse_string($signature, $parser_data_signature, TRUE);
            $parser_data["SIGNATURE"] = $signature_message;

            $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
            $send_app_mail = send_app_mail("gustavo@mimasoft.cl", $email_template->subject, $message);

            if($send_app_mail){
                echo "Correo enviado";
            } else {
                echo "error";
            }

            exit();
        }

    }