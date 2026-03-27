<?php
// Cargar PHPMailer desde la carpeta vendor
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendContactEmail($name, $clientEmail, $subject, $messageBody)
{
    // true habilita las excepciones para atrapar errores
    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURACIÓN PARA PRODUCCIÓN (DOCKER/RENDER) ---
        $mail->isSMTP();
        $mail->Host       = env('SMTP_HOST'); 
        $mail->SMTPAuth   = true;
        $mail->Username   = env('SMTP_USER');
        $mail->Password   = env('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPDebug  = 2;
        $mail->Port       = 465;

        // --- PARCHE CRÍTICO PARA DOCKER/RENDER ---
        // Esto evita el error "Network is unreachable" al saltarse la verificación de certificados interna del contenedor
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // El correo "sale" de tu cuenta (usamos getenv)
        $mail->setFrom(env('SMTP_USER'), 'Shopping Rosario Web');

        // El correo "llega" a tu cuenta (usamos getenv)
        $mail->addAddress(env('SMTP_USER'), 'Admin Shopping');

        // Si le das a "Responder", le contestas al cliente
        $mail->addReplyTo($clientEmail, $name);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Nuevo mensaje web: ' . $subject;

        // Cuerpo HTML del mensaje
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px;'>
                <h3 style='color: #0d6efd;'>Nuevo mensaje de contacto</h3>
                <p><strong>De:</strong> {$name}</p>
                <p><strong>Email:</strong> {$clientEmail}</p>
                <p><strong>Asunto:</strong> {$subject}</p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p><strong>Mensaje:</strong></p>
                <p style='background: #f8f9fa; padding: 15px; border-radius: 5px; color: #333;'>" . nl2br($messageBody) . "</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // En producción es mejor no usar die() para no romper la estética del sitio,
        // pero para debuguear el error de Render está bien.
        die("Ocurrió un error con el servidor de correos: " . $mail->ErrorInfo);
        return false;
    }
}