<?php
    // Cargar User antes de session_start para evitar __PHP_Incomplete_Class
    require_once __DIR__ . '/../models/User.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include_once __DIR__ . '/../Config/config.php';
    include_once __DIR__ . '/../Services/login.services.php';
    include_once __DIR__ . '/../Services/alert.service.php';

    // Procesar el formulario de login
    if (!empty($_POST)) {
        
        $userName = trim($_POST['userName']);
        $password = trim($_POST['password']);
        
        // Validar campos
        if (!isset($userName) || !isset($password)) {
            $loginError = 'Por favor, completa todos los campos.';
            AlertService::error($loginError);
        } elseif (strlen($password) < 6 || strlen($password) > 20) {
            $loginError = 'La contraseña debe tener entre 6 y 20 caracteres.';
            AlertService::error($loginError);
        } else {
            // Verificar credenciales
            $user = authenticateUser($userName, $password);
            if ($user) {
                $loginSuccess = 'Inicio de sesión exitoso. Redirigiendo...';
                AlertService::success($loginSuccess);
                
                $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                header("Location: " . $baseUrl . "/public/Pages/Home/home.php");
                exit();
            } else {
                $loginError = 'Credenciales incorrectas. Verifica tu email y contraseña.';
                AlertService::error($loginError);
            }
        }
    }
?>