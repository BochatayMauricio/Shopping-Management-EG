<?php
    include_once __DIR__ . '/../../app/Services/login.services.php';
    include_once __DIR__ . '/../../app/Services/alert.service.php';

    // Procesar el formulario de login
    if (!empty($_POST)) {
        
        $userName = trim($_POST['userName']);
        $password = trim($_POST['password']);
        
        // Validar campos
        if (!isset($userName) || !isset($password)) {
            $loginError = 'Por favor, completa todos los campos.';
            AlertService::error($loginError);
        } elseif (strlen($password) < 6 || strlen($password) > 20) {
            $loginError = 'La contraseña debe tener entre 6 y 20 caracteres.';
            AlertService::error($loginError);
        } else {
            // Verificar credenciales
            $user = authenticateUser($userName, $password);
            if ($user) {
                $loginSuccess = 'Inicio de sesión exitoso. Redirigiendo...';
                // Redirigir a la página principal o dashboard
                AlertService::success($loginSuccess);
                $_SESSION['user'] = $user;
                
                header("Location: ./../../../public/Pages/Home/home.php");
                exit();
            } else {
                $loginError = 'Credenciales incorrectas. Verifica tu email y contraseña.';
                AlertService::error($loginError);
            }
        }
    }
    

?>