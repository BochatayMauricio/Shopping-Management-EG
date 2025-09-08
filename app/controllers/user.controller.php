<?php
    include_once __DIR__ . '/../../app/Services/user.services.php';
    include_once __DIR__ . '/../../app/Services/alert.service.php';

    // Procesar el formulario de login
    if (!empty($_POST)){
        
        $userName = trim($_POST['userName']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);

        // Validar campos
        if (!isset($userName) || !isset($password) || !isset($confirmPassword)) {
            $loginError = 'Por favor, completa todos los campos.';
            AlertService::error($loginError);
        } elseif (strlen($password) < 6 || strlen($password) > 8 ) {
            $loginError = 'La contraseña debe tener entre 6 y 8 caracteres.';
            AlertService::error($loginError);
        } elseif ($password !== $confirmPassword) {
            $loginError = 'Las contraseñas no coinciden.';
            AlertService::error($loginError);
        } else {
            // Registrar usuario
            $user = registerUser($userName, $password);
            if ($user) {
                $loginSuccess = 'Registro exitoso. Redirigiendo...';
                // Redirigir a la página principal o dashboard
                AlertService::success($loginSuccess);
                header("Location: ./../../../public/Pages/Client Portal/clientPortal.php");
                exit();
            } else {
                $loginError = 'Error al registrar el usuario. Intenta nuevamente.';
                AlertService::error($loginError);
            }
        }
    }
    

?>