<?php  
require_once __DIR__. '/../Config/config.php';
require_once __DIR__. '/../models/User.php';
include_once __DIR__ . '/alert.service.php';

/**
 * Función para validar email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Función para autenticar usuario
 * @param string $userName
 * @param string $password
 * @return User|false
 */
function authenticateUser($userName, $password) {
    global $CONNECTION;
    
    if (!$CONNECTION) {
        error_log("Error: No hay conexión a la base de datos");
        return false;
    }
    
    $userName = strtolower($userName);
    $query = "SELECT * FROM users WHERE name = '$userName'";

    $result = mysqli_query($CONNECTION, $query);
    
    if (!$result) {
        error_log("Error en la consulta: " . mysqli_error($CONNECTION));
        return false;
    }

    if($result->num_rows == 0){
        AlertService::error("Usuario incorrecto.");
        return false;
    }

    $userData = $result->fetch_assoc();
    if (!password_verify($password, $userData['password'])) {
        AlertService::error("Contraseña incorrecta.");
        return false;
    }

    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Guardamos el array para evitar problemas de serialización
    $_SESSION['user'] = $userData;
    
    AlertService::success("Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($userName) . "!");
    return $userData; // Retornamos array en lugar de objeto User
}

/**
 * Obtiene el usuario actual de la sesión
 * @return array|null Retorna array para evitar problemas de serialización
 */
function getCurrentUser(){
    $userData = $_SESSION['user'] ?? null;
    
    // Validar que es un array válido
    return is_array($userData) ? $userData : null;
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? 'guest';
}

// Función en local
function logout(){
    // Solo destruir si hay sesión activa
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    
    $redirectUrl = BASE_URL . '/public/Pages/Home/home.php';
    header("Location: " . $redirectUrl);
    exit();
}

?>