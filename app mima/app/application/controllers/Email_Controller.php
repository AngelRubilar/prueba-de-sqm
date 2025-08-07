<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Email_Controller extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('PHPMailer_Lib');
    $mail = $this->phpmailer_lib->load();

  }

  public function index()
  {
    $this->enviar_correo_de_prueba();
  }
  function get_setting($key = "")
  {
    $ci = get_instance();
    return $ci->config->item($key);
  }
  public function enviar_correo($to, $subject, $message, $options = array())
  {
    // Crear objeto PHPMailer
    $mail = $this->phpmailer_lib->load();

    //check mail sending method from settings
    if (get_setting("email_protocol") === "smtp") {
      $mail->isSMTP();
      $mail->Host = get_setting("email_smtp_host");
      $mail->SMTPAuth = true;
      $mail->Username = get_setting("email_smtp_user");
      $mail->Password = get_setting("email_smtp_pass");
      $mail->SMTPSecure = get_setting("email_smtp_security_type");
      $mail->Port = get_setting("email_smtp_port");
      if (!$mail->smtp_crypto) {
        $mail->smtp_crypto = "tls";
      }

    }

    // limpiar destinatarios anteriores si los hay
    $mail->ClearAllRecipients();
    // agregar remitente
    $mail->setFrom(get_setting("email_sent_from_address"), get_setting("email_sent_from_address"));
    $mail->addReplyTo(get_setting("email_sent_from_address"), get_setting("email_sent_from_address"));
    // agregar destinatarios
    $mail->addAddress($to);

    // plantilla html
    $mail->isHTML(true);

    // establecer asunto y body
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->Charset = 'UTF-8';

    // enviar correo
    if (!$mail->send()) {
      return 'Error del mailer: ' . $mail->ErrorInfo;
    } else {
      return 'Correo enviado!';
    }
  }

  public function enviar_correo_de_prueba()
  {
    // destinatario 
    $to = 'angelrubilar07@gmail.com';
    // asunto y mensaje de prueba
    $subject = 'prueba de correo para la jefesita';
    $message = 'Este es un correo de prueba desde el body de la vista. Saludos!';
    // enviar correo electronico de prueba
    $response = $this->enviar_correo($to, $subject, $message);

    // pasar la respuesta a la vista
    $data['response'] = $response;
    $data['email_smtp_host'] = get_setting("email_smtp_host");
    $data['email_smtp_user'] = get_setting("email_smtp_user");
    $data['email_smtp_pass'] = get_setting("email_smtp_pass");
    $data['email_smtp_security_type'] = get_setting("email_smtp_security_type");
    $data['email_smtp_port'] = get_setting("email_smtp_port");
    $data['email_sent_from_address'] = get_setting("email_sent_from_address");
    $data['email_sent_from_name'] = get_setting("email_sent_from_name");
    $data['email_protocol'] = get_setting("email_protocol");
    $data['email_smtp_crypto'] = get_setting("email_smtp_crypto");


    // enviamos el resultado a la vista
    $this->load->view("prueba_correo/enviar_correo", $data);
  }

}