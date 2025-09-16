<?php  
require_once __DIR__. '/../config/config.php';
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
 * @param string $email
 * @param string $password
 * @return array|false
 */
function authenticateUser($userName, $password) {
    global $CONNECTION;
    
    // Debug: verificar conexión
    if (!$CONNECTION) {
        error_log("Error: No hay conexión a la base de datos");
        return false;
    }
    
    $userName = strtolower($userName);
    $query = "SELECT * FROM users WHERE userName = '$userName'";

    $result = mysqli_query($CONNECTION, $query);
    
    // Debug: verificar errores de consulta
    if (!$result) {
        error_log("Error en la consulta: " . mysqli_error($CONNECTION));
        return false;
    }

    if($result->num_rows == 0){
        return false;
    } 

    // Verificar la contraseña
    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['userPassword'])) {
        return false; // Contraseña incorrecta
    }

    session_start();
    $_SESSION['user'] = $user;
    mysqli_close($CONNECTION);
    return $user;
}

function getCurrentUser(){
    return $_SESSION['user'] ?? null;
}

function getUserRole() {
    return $_SESSION['user']['role'] ?? 'guest';
}

function logout(){
    session_unset();
    session_destroy();
    header("Location: ./../../../public/Pages/Client Portal/clientPortal.php");
    exit();
}

?>