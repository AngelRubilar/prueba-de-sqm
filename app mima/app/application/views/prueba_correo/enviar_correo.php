<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prueba de envio correo electronico</title>
</head>

<body>
  <h1>respuesta de envio de correo</h1>
  <p>
    <?php echo $response; ?>
  </p>

  <p>
    <?php echo $email_smtp_host; ?>
  </p>
  <p>
    <?php echo $email_smtp_user; ?>
  </p>
  <p><?php echo $email_smtp_pass; ?></p>
<p><?php echo $email_smtp_security_type; ?></p>
<p><?php echo $email_smtp_port; ?></p>
<p><?php echo $email_sent_from_address; ?></p>
<p><?php echo $email_sent_from_name; ?></p>
<p><?php echo $email_protocol; ?></p>
<p><?php echo $email_smtp_crypto; ?></p>
  
</body>

</html>