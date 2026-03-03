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
    
    // Si no hay datos, retornar null
    if (!$userData) {
        return null;
    }
    
    // Si es un objeto incompleto (sesión corrupta), limpiar y retornar null
    if ($userData instanceof __PHP_Incomplete_Class) {
        unset($_SESSION['user']);
        return null;
    }
    
    // Si ya es un array, retornarlo directamente
    if (is_array($userData)) {
        return $userData;
    }
    
    // Si es un objeto User, convertirlo a array
    if ($userData instanceof User) {
        return [
            'cod' => $userData['cod'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'type' => $userData['type'],
            'category' => $userData['category']
        ];
    }
    
    return null;
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? 'guest';
}

// function logout(){
//     // Asegurar que la sesión está iniciada
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }
    
//     session_unset();
//     session_destroy();
    
//     $baseUrl = defined('BASE_URL') ? BASE_URL : '';
//     $redirectUrl = $baseUrl . "public/Pages/Home/home.php";
    
//     // Si ya se enviaron headers, usar JavaScript
//     if (headers_sent()) {
//         echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
//         exit();
//     }
    
//     header("Location: " . $redirectUrl);
//     exit();
// }

// Función en local
function logout(){
    session_unset();
    session_destroy();
    AlertService::success("Sesión cerrada correctamente.");
    header("Location: ./../../../public/Pages/Home/home.php");
    exit();
}

?>