<?php
// Cargar PHPMailer desde la carpeta vendor
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendContactEmail($name, $clientEmail, $subject, $messageBody) {
    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURACIÓN PARA PRODUCCIÓN (DOCKER/RENDER) ---
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST'); 
        $mail->SMTPAuth   = true;
        
        // Usamos getenv() para leer las variables de entorno de Render
        $mail->Username   = getenv('SMTP_USER'); 
        $mail->Password   = getenv('SMTP_PASS'); 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- PARCHE CRÍTICO DE RED ---
        // Esto soluciona el error "Network is unreachable" y problemas de certificados en Docker
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Configuración de los correos
        $mail->setFrom(getenv('SMTP_USER'), 'Shopping Rosario Web');
        $mail->addAddress(getenv('SMTP_USER'), 'Admin Shopping'); 
        $mail->addReplyTo($clientEmail, $name);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Nuevo mensaje web: ' . $subject;
        
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
        // En producción, podrías loguear esto en un archivo en vez de usar die()
        error_log("Error de PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}
?>