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
        AlertService::success("Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($userName) . "!");
        return false;
    } 

    $userData = $result->fetch_assoc();
    if (!password_verify($password, $userData['password'])) {
        return false;
    }

    $user = User::fromArray($userData);
    
    session_start();
    $_SESSION['user'] = $userData; // Guardamos el array para compatibilidad
    mysqli_close($CONNECTION);
    AlertService::success("Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($userName) . "!");
    return $user;
}

/**
 * Obtiene el usuario actual de la sesión
 * @return User|null
 */
function getCurrentUser(){
    $userData = $_SESSION['user'] ?? null;
    return $userData ? User::fromArray($userData) : null;
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? 'guest';
}

function logout(){
    session_unset();
    session_destroy();
    AlertService::success("Sesión cerrada correctamente.");
    header("Location: ./../../../public/Pages/Home/home.php");
    exit();
}

?>