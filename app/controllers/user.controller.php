<?php
    // Cargar configuración primero
    include_once __DIR__ . '/../Config/config.php';
    include_once __DIR__ . '/../Services/user.services.php';
    include_once __DIR__ . '/../Services/alert.service.php';

    // Procesar el formulario de login
if (!empty($_POST)) {
    
    // Captura y limpieza de datos
    $userName = trim($_POST['userName']);
    $email = trim($_POST['email']); // <--- Agregamos el email
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validar campos vacíos
    if (empty($userName) || empty($email) || empty($password) || empty($confirmPassword)) {
        AlertService::error('Por favor, completa todos los campos.');
        
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validar formato de email
        AlertService::error('El formato del correo electrónico no es válido.');

    } elseif (strlen($password) < 6 || strlen($password) > 20) {
        AlertService::error('La contraseña debe tener entre 6 y 20 caracteres.');

    } elseif ($password !== $confirmPassword) {
        AlertService::error('Las contraseñas no coinciden.');

    } else {
        // Intentar registrar usuario
        // Pasamos por defecto 'client' como tipo de usuario
        $result = registerUser($userName, $email, $password, 'client');

        if ($result === true) {
            $loginSuccess = 'Registro exitoso. ¡Bienvenido!';
            AlertService::success($loginSuccess);
            
            // Redirigir al Login
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header("Location: " . $baseUrl . "/public/Pages/Login/login.php");
            exit();

        } elseif ($result === "email_exists") {
            // Manejo específico del error de duplicado que vimos en SQL
            AlertService::error('Este correo electrónico ya está registrado.');
            
        } else {
            AlertService::error('Error al registrar el usuario. Intenta nuevamente.');
        }
    }
}
    

?>