<?php
    // Cargar configuración primero
    include_once __DIR__ . '/../Config/config.php';
    include_once __DIR__ . '/../Services/user.services.php';
    include_once __DIR__ . '/../Services/alert.service.php';
    include_once __DIR__ . '/../Services/validation.service.php';

    // Procesar el formulario de login
if (isset($_POST['btnRegister'])) {
    
    // Captura y limpieza de datos
    $userName = trim($_POST['userName']);
    $email = trim($_POST['email']); // <--- Agregamos el email
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validar campos vacíos
    if (!ValidationService::areFieldsNotEmpty([$userName, $email, $password, $confirmPassword])) {
        AlertService::error(ValidationService::getEmptyFieldsMessage());
        
    } elseif (!ValidationService::isValidEmail($email)) {
        // Validar formato de email
        AlertService::error(ValidationService::getEmailErrorMessage());

    } elseif (!ValidationService::isValidPassword($password)) {
        AlertService::error(ValidationService::getPasswordErrorMessage());

    } elseif (!ValidationService::passwordsMatch($password, $confirmPassword)) {
        AlertService::error(ValidationService::getPasswordMismatchMessage());

    } else {
        // Intentar registrar usuario
        // Pasamos por defecto 'client' como tipo de usuario
        $result = registerUser($userName, $email, $password, 'client');

        if ($result === true) {
            $loginSuccess = 'Registro exitoso. ¡Bienvenido!';
            AlertService::success($loginSuccess);
            
            // Redirigir al Login
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header("Location: " . $baseUrl . "/public/Pages/Home/home.php");
            exit();

        } elseif ($result === "username_exists") {
            AlertService::error(ValidationService::getUsernameExistsMessage());

        } elseif ($result === "email_exists") {
            // Manejo específico del error de duplicado que vimos en SQL
            AlertService::error(ValidationService::getEmailExistsMessage());
            
        } else {
            AlertService::error('Error al registrar el usuario. Intenta nuevamente.');
        }
    }
}
// ========== PROCESAR CAMBIO DE CONTRASEÑA ==========
// Usamos isset en el botón específico por si a futuro agregas más formularios en esta vista
if (isset($_POST['btnChangePassword']) && $user) {
    
    $currentPassword = trim($_POST['currentPassword']);
    $newPassword = trim($_POST['newPassword']);
    $confirmNewPassword = trim($_POST['confirmNewPassword']);
    $userId = $user['cod'] ?? $user['id'];

    if (!ValidationService::areFieldsNotEmpty([$currentPassword, $newPassword, $confirmNewPassword])) {
        AlertService::error(ValidationService::getEmptyFieldsMessage());
        
    } elseif (!ValidationService::isValidPassword($newPassword)) {
        AlertService::error('La nueva contraseña debe tener entre 6 y 20 caracteres.');

    } elseif (!ValidationService::passwordsMatch($newPassword, $confirmNewPassword)) {
        AlertService::error('Las contraseñas nuevas no coinciden.');

    } else {
        // Llamamos a la función del servicio
        $updateResult = updateUserPassword($userId, $currentPassword, $newPassword);

        if ($updateResult === true) {
            AlertService::success('Contraseña actualizada con éxito.');
            
            // Redirigimos para limpiar la petición POST y evitar reenvíos al recargar
            header("Location: userPortal.php"); 
            exit();

        } elseif ($updateResult === "incorrect_password") {
            AlertService::error('La contraseña actual es incorrecta.');
            
        } else {
            AlertService::error('Error al actualizar la contraseña. Intenta nuevamente.');
        }
    }
}
// =====================================================
    

?>