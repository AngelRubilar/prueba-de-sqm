<?php

phpinfo(INFO_MODULES);
echo '<pre>';
print_r(get_loaded_extensions());
echo '</pre>';
// Verificar si la extensión openssl está cargada
if (extension_loaded('openssl')) {
    echo 'OpenSSL está cargado';
}
echo 'Versión de PHP: ' . phpversion();
echo 'Versión de OpenSSL: ' . phpversion('openssl');
echo '<pre>';
print_r(openssl_get_cipher_methods());
echo '</pre>';
$data = "dato secreto";
$method = 'AES-128-CBC';
$key = openssl_random_pseudo_bytes(16);
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

// Encriptar
$encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
echo 'Dato encriptado: ' . $encrypted . "\n";

// Desencriptar
$decrypted = openssl_decrypt($encrypted, $method, $key, 0, $iv);
echo 'Dato desencriptado: ' . $decrypted . "\n";

// Muestra todas las variables del servidor
echo '<pre>' . print_r($_SERVER, true) . '</pre>';

// Variables específicas de interés podrían incluir
echo 'Versión de Apache: ' . apache_get_version() . "<br>";
echo 'Módulos de Apache cargados: <pre>' . print_r(apache_get_modules(), true) . '</pre>';
?>

