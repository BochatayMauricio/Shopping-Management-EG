<?php
require_once __DIR__. '/../Config/config.php';
// app/Services/contact.services.php

/**
 * Simula el proceso de contacto enviando una confirmación al usuario
 */
function simulateContactProcess($name, $user_email, $subject_type) {
    // Configuramos el "remitente" del Shopping
    $from = "no-reply@shoppingrosario.com.ar";
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

    // El asunto y cuerpo que recibirá el cliente
    $subject = "Confirmación de contacto - Shopping Rosario";
    $body = "Hola " . $name . ",\n\n";
    $body .= "Hemos recibido correctamente tu consulta sobre: '" . $subject_type . "'.\n";
    $body .= "Este es un mensaje automático para confirmarte que tu mensaje está en proceso.\n\n";
    $body .= "Gracias por comunicarte con nosotros.\n";
    $body .= "Equipo de Administración - Shopping Rosario.";

    // Intentamos enviar el mail al usuario (en Localhost esto devolverá true/false según configuración)
    // Pero para nuestra simulación, retornaremos true para que el flujo de la web continúe
    return @mail($user_email, $subject, $body, $headers);

}