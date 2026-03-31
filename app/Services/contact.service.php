<?php
// Cargar PHPMailer desde la carpeta vendor
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendContactEmail($userName, $userEmail, $subject, $message) {
    // 1. Obtenés la clave API desde Render
    $apiKey = $_ENV['RESEND_API_KEY'] ?? getenv('RESEND_API_KEY'); 
    
    // 2. Configurás el paquete de datos
    $data = [
        // 'from': En la versión gratis de Resend, tiene que ser este por defecto
        'from' => 'Web Shopping Rosario <onboarding@resend.dev>', 
        
        // 'to': SIEMPRE a tu correo personal
        'to' => ['aloivicente@gmail.com'], 
        
        // 'reply_to': ¡El truco mágico! Si le das a responder en tu Gmail, va al cliente
        'reply_to' => $userEmail, 
        
        'subject' => "Consulta Web: " . $subject,
        
        // 'html': El diseño del correo que vas a recibir vos
        'html' => "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2 style='color: #0d6efd;'>Nuevo mensaje desde la web</h2>
                <p><strong>Nombre del cliente:</strong> {$userName}</p>
                <p><strong>Email del cliente:</strong> {$userEmail}</p>
                <hr>
                <p><strong>Mensaje:</strong></p>
                <p style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>
                    " . nl2br(htmlspecialchars($message)) . "
                </p>
            </div>
        "
    ];

    // 3. Hacés la petición a la API (Esto salta el bloqueo de puertos de Render)
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

    if ($httpCode == 200) {
        return true;
    } else {
        error_log("Error de Resend: " . $response); // Queda registrado en los logs de Render
        return false;
    }
}