<?php
    // Cargar configuración primero
    include_once __DIR__ . '/../Config/config.php';
    include_once __DIR__ . '/../Services/user.services.php';
    include_once __DIR__ . '/../Services/alert.service.php';

    // Procesar el formulario de login
if (isset($_POST['btnRegister'])) {
    
    // Captura y limpieza de datos
    $userName = trim($_POST['userName']);
    $email = trim($_POST['email']); // <--- Agregamos el email
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validar campos vacíos
    if (empty($userName) || empty($email) || empty($password) || empty($confirmPassword)) {
        AlertService::error('Por favor, completa todos los campos.');
        
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validar formato de email
        AlertService::error('El formato del correo electrónico no es válido.');

    } elseif (strlen($password) < 6 || strlen($password) > 20) {
        AlertService::error('La contraseña debe tener entre 6 y 20 caracteres.');

    } elseif ($password !== $confirmPassword) {
        AlertService::error('Las contraseñas no coinciden.');

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

        } elseif ($result === "email_exists") {
            // Manejo específico del error de duplicado que vimos en SQL
            AlertService::error('Este correo electrónico ya está registrado.');
            
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

    if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
        AlertService::error('Por favor, completa todos los campos.');
        
    } elseif (strlen($newPassword) < 6 || strlen($newPassword) > 20) {
        AlertService::error('La nueva contraseña debe tener entre 6 y 20 caracteres.');

    } elseif ($newPassword !== $confirmNewPassword) {
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