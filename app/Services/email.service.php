<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    /**
     * Función privada centralizada para conectarse con la API de Resend
     */
    private static function sendViaResend($toEmail, $subject, $htmlBody)
    {
        // Tu clave API configurada en Render o en tu .env
        $apiKey = env('RESEND_API_KEY'); 

        if (!$apiKey) {
            error_log("Error: RESEND_API_KEY no está configurada.");
            return false;
        }

        // Datos para la API
        $data = [
            // IMPORTANTE: Mientras pruebas en la versión gratuita de Resend sin verificar tu dominio, 
            // el remitente DEBE ser onboarding@resend.dev
            'from' => 'Shopping Rosario <onboarding@resend.dev>',
            'to' => [$toEmail],
            'subject' => $subject,
            'html' => $htmlBody
        ];

        // Inicializamos cURL
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Resend devuelve 200 cuando es exitoso
        if ($httpCode === 200) {
            return true;
        } else {
            error_log("Error en API de Resend: HTTP {$httpCode} - Respuesta: {$response}");
            return false;
        }
    }

    public static function sendVerificationEmail($email, $token)
    {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $host = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $verifyLink = "{$protocol}://{$host}{$baseUrl}/public/Pages/User Verify/verify.php?token={$token}";

        $subject = 'Verifica tu cuenta en Shopping Rosario';
        $body = "
            <h2>¡Bienvenido a Shopping Rosario!</h2>
            <p>Gracias por registrarte. Para poder iniciar sesión, necesitamos que verifiques tu dirección de correo electrónico haciendo clic en el siguiente enlace:</p>
            <p><a href='{$verifyLink}' style='padding: 10px 15px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>Verificar mi cuenta</a></p>
            <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
            <p style='color: #6c757d; word-break: break-all;'>{$verifyLink}</p>
            <br>
            <p>Si no te registraste, por favor ignora este correo.</p>
        ";

        return self::sendViaResend($email, $subject, $body);
    }

    public static function sendPasswordResetEmail($userEmailToReset, $token)
    {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        $host = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $resetLink = "{$protocol}://{$host}{$baseUrl}/public/Pages/PasswordRecovery/resetPassword.php?token={$token}";

        $subject = "Recuperacion de clave solicitada para: " . $userEmailToReset;
        $body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ccc; border-radius: 10px;'>
                <h2 style='color: #0d6efd;'>Token de Recuperación</h2>
                <p>Alguien solicitó restablecer la contraseña desde la web.</p>
                <p><strong>Cuenta solicitada:</strong> <span style='color: #dc3545;'>{$userEmailToReset}</span></p>
                <hr>
                <p>Para procesar el cambio de contraseña, hacé clic en el siguiente enlace seguro:</p>
                <br>
                <a href='{$resetLink}' style='background-color: #0d6efd; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Restablecer Contraseña</a>
                <br><br>
                <p><small style='color: #6c757d;'>* Este enlace expirará en 1 hora.</small></p>
            </div>
        ";

        return self::sendViaResend($userEmailToReset, $subject, $body);
    }
}
?>