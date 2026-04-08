<?php
    // Mostrar errores temporalmente (salvavidas contra la pantalla blanca)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Cargar User antes de session_start para evitar problemas
    require_once __DIR__ . '/../models/User.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include_once __DIR__ . '/../Config/config.php';
    include_once __DIR__ . '/../Services/login.services.php';
    include_once __DIR__ . '/../Services/alert.service.php';
    include_once __DIR__ . '/../Services/validation.service.php';


    // ====================================================
    // 1. PROCESAR LOGOUT (Viene del enlace de la NavBar)
    // ====================================================
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        
        logoutUser(); // Función limpia en el servicio
        
        // Verificamos si lograste crear la función 'warning' en AlertService.
        // Si no existe, usamos 'success' como respaldo para que no explote.
        if (method_exists('AlertService', 'warning')) {
            AlertService::warning('Has cerrado sesión correctamente.');
        } else {
            AlertService::success('Has cerrado sesión correctamente.');
        }
        
        // Redirigir al inicio
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        header("Location: " . $baseUrl . "/public/Pages/Home/home.php");
        exit();
    }


    // ====================================================
    // 2. PROCESAR LOGIN (Viene del formulario)
    // ====================================================
    if (!empty($_POST)) {
        $userName = trim($_POST['userName']);
        $password = trim($_POST['password']);
        
        if (!isset($userName) || !isset($password)) {
            AlertService::error(ValidationService::getEmptyFieldsMessage());
        } elseif (!ValidationService::isValidPassword($password)) {
            AlertService::error(ValidationService::getPasswordErrorMessage());
        } else {
            $userResult = authenticateUser($userName, $password);
            
            if (is_array($userResult)) {
                AlertService::success('Inicio de sesión exitoso. ¡Bienvenido, ' . htmlspecialchars($userResult['name']) . '!');
                $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                header("Location: " . $baseUrl . "/public/Pages/Home/home.php");
                exit();
            } elseif ($userResult === "unverified") {
                AlertService::error('Tu cuenta aún no ha sido verificada. Por favor, revisá tu bandeja de entrada.');
            } else {
                AlertService::error('Usuario o contraseña incorrectos.');
            }
        }
    }
?>