<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Config/config.php';
include_once __DIR__ . '/../Services/user.services.php';
include_once __DIR__ . '/../Services/email.service.php';
include_once __DIR__ . '/../Services/alert.service.php';
include_once __DIR__ . '/../Services/validation.service.php';

if (isset($_POST['btnRecoverPassword'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        AlertService::warning('Por favor, ingresá tu correo electrónico.');
    } elseif (!ValidationService::isValidEmail($email)) {
        AlertService::warning('El formato del correo no es válido.');
    } else {
        // 1. Verificamos si el usuario existe usando el servicio[cite: 5]
        $user = getUserByEmail($email);

        if ($user) {
            // 2. Generamos el Token y la fecha de expiración
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // 3. Guardamos en la BD usando el servicio[cite: 5]
            if (savePasswordResetToken($user['cod'], $token, $expires)) {
                // 4. Enviamos el mail
                EmailService::sendPasswordResetEmail($email, $token);
            }
        }

        // 5. Mensaje genérico de éxito por seguridad
        AlertService::success('Si el correo está registrado, hemos enviado un enlace de recuperación a tu bandeja.');
    }
}

if (isset($_POST['btnResetPassword'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($newPassword) || empty($confirmPassword)) {
        AlertService::warning('Por favor, completá ambos campos.');
    } elseif (!ValidationService::isValidPassword($newPassword)) {
        AlertService::warning("La contraseña debe tener entre 8 y 20 caracteres, incluir una mayúscula, una minúscula y un número.");
    } elseif ($newPassword !== $confirmPassword) {
        AlertService::warning('Las contraseñas no coinciden.');
    } else {
        // Verificar token y obtener ID del usuario
        $userId = verifyPasswordResetToken($token);

        if ($userId) {
            // Actualizar contraseña[cite: 5]
            if (resetUserPassword($userId, $newPassword)) {
                AlertService::success('Contraseña actualizada correctamente. Ya podés iniciar sesión.');
                // Redirigir al login
                $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                header("Location: " . $baseUrl . "/public/Pages/Login/login.php");
                exit();
            } else {
                AlertService::error('Error al actualizar la contraseña. Intentá nuevamente.');
            }
        } else {
            AlertService::error('El enlace de recuperación es inválido o ha expirado.');
        }
    }
}