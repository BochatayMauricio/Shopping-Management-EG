<?php
// Incluir archivo de configuración
require_once '../../Config/config.php';

$loginError = '';
$loginSuccess = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    
    // Validar campos
    if (empty($email) || empty($password)) {
        $loginError = 'Por favor, completa todos los campos.';
    } elseif (!validateEmail($email)) {
        $loginError = 'El formato del correo electrónico no es válido.';
    } elseif (strlen($password) < 6) {
        $loginError = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verificar credenciales
        $user = authenticateUser($email, $password);
        
        if ($user) {
            // Login exitoso
            $_SESSION['user'] = $user;
            $_SESSION['login_time'] = time();
            
            $loginSuccess = '¡Autenticación exitosa! Redirigiendo...';
            
            // Redirigir según el rol después de un pequeño delay
            echo "<script>
                setTimeout(function() {
                    window.location.href = '" . getDashboardUrl($user['role']) . "';
                }, 1500);
            </script>";
        } else {
            $loginError = 'Credenciales incorrectas. Verifica tu email y contraseña.';
        }
    }
}

// Función para obtener URL del dashboard según el rol
function getDashboardUrl($role) {
    switch ($role) {
        case 'admin':
            return '../Administrator Portal/admin-dashboard.php';
        case 'owner':
            return '../Owner Portal/owner-dashboard.php';
        case 'client':
            return '../Client Portal/client-dashboard.php';
        default:
            return '../Client Portal/client-dashboard.php';
    }
}
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
            <div id="alertContainer">
                <?php if (!empty($loginError)): ?>
                    <div class="alert alert-error" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($loginSuccess)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($loginSuccess); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="tu@email.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                        autocomplete="email"
                        aria-describedby="email-error"
                    >
                </div>
                <div id="email-error" class="sr-only" role="alert"></div>
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

            <button type="submit" class="login-button" id="loginBtn">
                <span class="spinner"></span>
                <span class="btn-text">Iniciar Sesión</span>
            </button>

            <div class="role-indicator" id="roleIndicator"></div>

            <div class="forgot-password">
                <a href="#" onclick="showForgotPassword()">¿Olvidaste tu contraseña?</a>
            </div>
        </form>

        <div class="divider">
            <span>¿Eres nuevo?</span>
        </div>

        <div class="register-link">
            <p>¿No tienes una cuenta?</p>
            <a href="#" onclick="showRegisterOptions()">Regístrate aquí</a>
        </div>
    </div>

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
        // Función para mostrar alertas
        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}" role="alert">
                    <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                    ${message}
                </div>
            `;
        }
    </script>
</body>
</html>
