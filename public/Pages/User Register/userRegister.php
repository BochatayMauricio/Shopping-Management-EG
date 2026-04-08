<?php
    include_once __DIR__ . '/../../../app/controllers/user.controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Rosario - Formulario Registro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="userRegister.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-store"></i> Shopping Rosario</h1>
            <p class="shopping-subtitle">Sistema de Gestión de Promociones</p>
        </div>
        
        <form id="registerForm" method="POST" action="">
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
                    >
                </div>
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
                        placeholder="ejemplo@correo.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >
                </div>
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
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                        title="Debe contener al menos 8 caracteres, una mayúscula, una minúscula y un número"
                        minlength="8"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmar contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirmPassword" 
                        class="form-control" 
                        placeholder="Confirma tu contraseña"
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                        title="Debe contener al menos 8 caracteres, una mayúscula, una minúscula y un número"
                        minlength="8"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', 'toggleIcon2')" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-button" id="loginBtn" name="btnRegister">
                <span class="spinner"></span>
                <span class="btn-text">Registrarse</span>
            </button>

            <div class="forgot-password">
                <a href="../Login/login.php">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>
    
    <?php include_once '../../../public/Components/alert/alert.php' ?>
    
    <script>
        // Función para alternar visibilidad de contraseña por ID
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Validación usando Toastr en lugar de sintaxis PHP
        document.getElementById('registerForm').onsubmit = function(e) {
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            
            if (pass !== confirm) {
                e.preventDefault(); // Frenamos el envío
                
                // Usamos Toastr (que asumo está cargado por tu alert.php)
                if (typeof toastr !== 'undefined') {
                    toastr.error('Las contraseñas no coinciden.');
                } else {
                    alert('Las contraseñas no coinciden.');
                }
            }
        };
    </script>
</body>
</html>