<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Config/config.php';
include_once __DIR__ . '/../Services/user.services.php';
include_once __DIR__ . '/../Services/alert.service.php';
include_once __DIR__ . '/../Services/validation.service.php';
include_once __DIR__ . '/../Services/email.service.php';

// Procesar el cierre de sesión (Logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logoutUser(); // Llamamos al servicio purgado
    
    // El controlador dispara la alerta amarilla
    AlertService::warning('Has cerrado sesión correctamente.');
    
    // Redireccionamos
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    header("Location: " . $baseUrl . "/public/Pages/Home/home.php");
    exit();
}

// Procesar el formulario de login
if (isset($_POST['btnRegister'])) {

    // Captura y limpieza de datos
    $userName = trim($_POST['userName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if (!ValidationService::areFieldsNotEmpty([$userName, $email, $password, $confirmPassword])) {
        AlertService::error(ValidationService::getEmptyFieldsMessage());
    } elseif (!ValidationService::isValidEmail($email)) {
        AlertService::error(ValidationService::getEmailErrorMessage());
    } elseif (!ValidationService::isValidPassword($password)) {
        AlertService::error(ValidationService::getPasswordErrorMessage());
    } elseif (!ValidationService::passwordsMatch($password, $confirmPassword)) {
        AlertService::error(ValidationService::getPasswordMismatchMessage());
    } else {
        $result = registerUser($userName, $email, $password, 'client');

        if ($result === true) {
            $loginSuccess = 'Correo enviado con éxito. Por favor, revisá tu bandeja para verificar tu cuenta.';
            AlertService::success($loginSuccess);

            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header("Location: " . $baseUrl . "/public/Pages/Login/login.php");
            exit();
        } elseif ($result === "username_exists") {
            AlertService::error(ValidationService::getUsernameExistsMessage());
        } elseif ($result === "email_exists") {
            AlertService::error(ValidationService::getEmailExistsMessage());
        } else {
            AlertService::error('Error al registrar el usuario. Intenta nuevamente.');
        }
    }
}

if (isset($_POST['btnChangePassword']) && $user) {

    $currentPassword = trim($_POST['currentPassword']);
    $newPassword = trim($_POST['newPassword']);
    $confirmNewPassword = trim($_POST['confirmNewPassword']);
    $userId = $user['cod'] ?? $user['id'];

    if (!ValidationService::areFieldsNotEmpty([$currentPassword, $newPassword, $confirmNewPassword])) {
        AlertService::error(ValidationService::getEmptyFieldsMessage());
    } elseif (!ValidationService::isValidPassword($newPassword)) {
        AlertService::error('La nueva contraseña debe tener entre 8 y 20 caracteres, al menos una mayúscula, una minúscula y un número.');
    } elseif (!ValidationService::passwordsMatch($newPassword, $confirmNewPassword)) {
        AlertService::error('Las contraseñas nuevas no coinciden.');
    } else {
        $updateResult = updateUserPassword($userId, $currentPassword, $newPassword);

        if ($updateResult === true) {
            AlertService::success('Contraseña actualizada con éxito.');
            header("Location: userPortal.php");
            exit();
        } elseif ($updateResult === "incorrect_password") {
            AlertService::error('La contraseña actual es incorrecta.');
        } else {
            AlertService::error('Error al actualizar la contraseña. Intenta nuevamente.');
        }
    }
}