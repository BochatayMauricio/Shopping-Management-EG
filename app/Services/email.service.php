<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    public static function sendVerificationEmail($email, $token)
    {
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = env('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('SMTP_USER');
            $mail->Password   = env('SMTP_PASS');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = env('SMTP_PORT', 587);

            // Remitente y destinatario
            $mail->setFrom(env('SMTP_USER'), 'Shopping Rosario');
            $mail->addAddress($email);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta en Shopping Rosario';

            // Construir el enlace de verificación
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            $host = $_SERVER['HTTP_HOST'];
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $verifyLink = "{$protocol}://{$host}{$baseUrl}/public/Pages/User Register/verify.php?token={$token}";

            $mail->Body = "
                <h2>¡Bienvenido a Shopping Rosario!</h2>
                <p>Gracias por registrarte. Para poder iniciar sesión, necesitamos que verifiques tu dirección de correo electrónico haciendo clic en el siguiente enlace:</p>
                <p><a href='{$verifyLink}' style='padding: 10px 15px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Verificar mi cuenta</a></p>
                <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                <p>{$verifyLink}</p>
                <br>
                <p>Si no te registraste, por favor ignora este correo.</p>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }
}
