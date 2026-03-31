<?php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../models/User.php';
include_once __DIR__ . '/alert.service.php';

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function authenticateUser($userName, $password)
{
    global $CONNECTION;

    if (!$CONNECTION) {
        error_log("Error: No hay conexión a la base de datos");
        return false;
    }

    $userName = strtolower(trim($userName));

    $stmt = $CONNECTION->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        AlertService::error("Usuario o contraseña incorrectos.");
        return false;
    }

    $userData = $result->fetch_assoc();

    // 1. Verificar contraseña
    if (!password_verify($password, $userData['password'])) {
        AlertService::error("Usuario o contraseña incorrectos.");
        return false;
    }

    // 2. Verificar si la cuenta está validada por email
    if ((int)$userData['is_verified'] !== 1) {
        AlertService::error("Tu cuenta aún no ha sido verificada. Por favor, revisa tu correo electrónico.");
        return false;
    }

    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Guardamos el array en sesión para evitar problemas de serialización
    $_SESSION['user'] = $userData;

    AlertService::success("Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($userData['name']) . "!");
    return $userData;
}

function getCurrentUser()
{
    $userData = $_SESSION['user'] ?? null;

    // Validar que es un array válido y convertir a modelo
    return is_array($userData) ? User::fromArray($userData) : null;
}

function getUserRole()
{
    return $_SESSION['user']['role'] ?? 'guest';
}

// Función en local
function logout()
{
    // Solo destruir si hay sesión activa
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    $redirectUrl = BASE_URL . '/public/Pages/Home/home.php';
    header("Location: " . $redirectUrl);
    exit();
}
