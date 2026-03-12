<?php
$base_path = realpath(__DIR__ . '/../../');

include_once $base_path . '/app/Config/config.php'; 
include_once $base_path . '/app/Services/contact.service.php';
include_once $base_path . '/app/Services/alert.service.php';
include_once $base_path . '/app/Services/validation.service.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['btnSendMessage'])) {
    
    // Captura y limpieza de datos
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $subject = trim(htmlspecialchars($_POST['subject'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // Validaciones
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        AlertService::error('Por favor, completa todos los campos.');
        header("Location: Contact.php"); 
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        AlertService::error('El correo electrónico no es válido.');
        header("Location: Contact.php"); 
        exit();
    }

    // Ejecutar el envío real del correo
    $isSent = sendContactEmail($name, $email, $subject, $message);

    if ($isSent) {
        AlertService::success('¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.');
    } else {
        AlertService::error('Hubo un problema al enviar el correo. Por favor, intenta de nuevo.');
    }

    // Redirección final para limpiar el formulario
    header("Location: Contact.php"); 
    exit();
}
?>