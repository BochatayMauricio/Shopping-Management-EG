<?php
$base_path = realpath(__DIR__ . '/../../');

include_once $base_path . '/app/Config/config.php'; 
include_once $base_path . '/app/Services/contact.service.php';


if (isset($_POST['btnSendMessage'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = $_POST['subject'];
    $message = htmlspecialchars($_POST['message']);

    $isProcessed = simulateContactProcess($name, $email, $subject);

    if ($isProcessed) {
        // Redirigimos a la misma página con el parámetro success
        // Al usar action="", esto nos asegura que no haya errores de ruta
        header("Location: Contact.php?success=sent");
        exit();
    }
}