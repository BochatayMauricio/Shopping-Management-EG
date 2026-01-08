<?php
session_start();
require_once __DIR__. '/../config/config.php';
include_once __DIR__ . '/alert.service.php';

/**
 * Función de registro actualizada con Email y Sentencias Preparadas
 */
function registerUser($userName, $email, $password, $type = 'client') {
    global $CONNECTION;
    
    if (!$CONNECTION) {
        AlertService::error("Error: No hay conexión a la base de datos");
        return false;
    }

    $userName = strtolower(trim($userName));
    $email = strtolower(trim($email));

    // 1. Validar si el EMAIL ya existe (es nuestra nueva clave única)
    $checkEmail = $CONNECTION->prepare("SELECT cod FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $resEmail = $checkEmail->get_result();
    
    if ($resEmail->num_rows > 0) {
        // Retornamos el string que el controlador ya sabe manejar
        return "email_exists";
    }

    // 2. Hashear la contraseña de forma segura
    $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insertar el nuevo usuario
    $stmt = $CONNECTION->prepare("INSERT INTO users (name, email, password, type, category) VALUES (?, ?, ?, ?, 'inicial')");
    $stmt->bind_param("ssss", $userName, $email, $passwordHashed, $type);
    
    if (!$stmt->execute()) {
        AlertService::error("Error al registrar: " . $CONNECTION->error);
        return false;
    }

    // 4. Obtener los datos del usuario recién creado para la sesión
    $codUser = $CONNECTION->insert_id;
    $getUser = $CONNECTION->prepare("SELECT * FROM users WHERE cod = ?");
    $getUser->bind_param("i", $codUser);
    $getUser->execute();
    $result = $getUser->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }

    $userLogued = $result->fetch_assoc();

    // 5. Iniciar la sesión automáticamente
    $_SESSION['user'] = $userLogued;

    // Nota: No cerramos la conexión aquí si el controlador aún debe usarse, 
    // pero si lo haces, asegúrate que sea al final del proceso.
    
    mysqli_close($CONNECTION);
    
    return true;
}
   
