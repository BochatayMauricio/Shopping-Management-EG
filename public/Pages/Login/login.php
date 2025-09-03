<?php
    include_once __DIR__ . '/../../../app/controllers/login.controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Rosario - Iniciar Sesión</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-store"></i> Shopping Rosario</h1>
            <p class="shopping-subtitle">Sistema de Gestión de Promociones</p>
        </div>
        <form id="loginForm" method="POST" action="">
          
            <div class="form-group">
                <label for="userName">Nombre de Usuario</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input 
                        type="text" 
                        id="userName" 
                        name="userName" 
                        class="form-control" 
                        placeholder="Tu nombre de usuario"
                        value="<?php echo htmlspecialchars($_POST['userName'] ?? ''); ?>"
                        required
                        autocomplete="username"
                        aria-describedby="userName-error"
                    >
                </div>
                <div id="userName-error" class="sr-only" role="alert"></div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Tu contraseña"
                        required
                        autocomplete="current-password"
                        aria-describedby="password-error"
                    >
                    <span class="password-toggle" onclick="togglePassword()" aria-label="Mostrar/ocultar contraseña">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
                <div id="password-error" class="sr-only" role="alert"></div>
            </div>

            <button type="submit" class="login-button" id="loginBtn" name="loginBtn">
                <span class="spinner"></span>
                <span class="btn-text">Iniciar Sesión</span>
            </button>

            <div class="role-indicator" id="roleIndicator"></div>

            <div class="forgot-password">
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
        </form>

        <div class="divider">
            <span>¿Eres nuevo?</span>
        </div>

        <div class="register-link">
            <p>¿No tienes una cuenta?</p>
            <a href="#">Regístrate aquí</a>
        </div>
    </div>
    <?php include_once '../../Components/alert/alert.php' ?>
    
    <script>
        // Función para alternar visibilidad de contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

    </script>
</body>
</html>
