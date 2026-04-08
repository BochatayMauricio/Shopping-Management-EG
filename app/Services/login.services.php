<?php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../models/User.php';
// Ya no necesitamos llamar a AlertService acá, lo hará el controlador

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
        return "invalid"; // Usuario no existe
    }

    $userData = $result->fetch_assoc();

    // 1. Verificar contraseña
    if (!password_verify($password, $userData['password'])) {
        return "invalid"; // Contraseña mal
    }

    // 2. Verificar si la cuenta está validada por email
    // A los administradores (si los hay) normalmente no se les pide verificar, 
    // pero si querés que aplique a todos, dejamos la validación tal cual.
    if ((int)$userData['is_verified'] !== 1) {
        return "unverified"; // Falta verificar email
    }

    // 3. Todo correcto: Iniciar sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Guardamos el array en sesión
    $_SESSION['user'] = $userData;

    return $userData; // Retornamos los datos
}

function getCurrentUser()
{
    $userData = $_SESSION['user'] ?? null;
    return is_array($userData) ? User::fromArray($userData) : null;
}

function getUserRole()
{
    return $_SESSION['user']['role'] ?? 'guest';
}

function logoutUser()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }
    $redirectUrl = defined('BASE_URL') ? BASE_URL . '/public/Pages/Home/home.php' : '/public/Pages/Home/home.php';
    header("Location: " . $redirectUrl);
    exit();
}