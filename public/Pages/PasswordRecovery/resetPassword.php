<?php
    include_once __DIR__ . '/../../../app/controllers/password.controller.php';
    
    // Capturamos el token de la URL. Si no hay token, lo mandamos al login.
    $token = $_GET['token'] ?? '';
    if (empty($token) && empty($_POST)) {
        header("Location: ../Login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Rosario - Nueva Contraseña</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="../Login/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Nueva Contraseña</h1>
            <p class="shopping-subtitle">Creá una contraseña segura para tu cuenta</p>
        </div>
        
        <form id="resetForm" method="POST" action="" novalidate>
            <!-- Campo oculto vital para pasarle el token al controlador -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="form-group">
                <label for="newPassword">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="newPassword" 
                        name="newPassword" 
                        class="form-control" 
                        placeholder="Mínimo 8 caracteres, mayúscula y número"
                        required
                    >
                    <span class="password-toggle" onclick="togglePassword('newPassword', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmar Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirmPassword" 
                        class="form-control" 
                        placeholder="Repetí tu nueva contraseña"
                        required
                    >
                    <span class="password-toggle" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="login-button" name="btnResetPassword">
                <span class="btn-text">Actualizar Contraseña</span>
            </button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <?php include_once '../../../public/Components/alert/alert.php' ?>

    <script>
        // Función para mostrar/ocultar contraseña
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

        // Validación frontend con Toastr (Idéntica a la del registro)
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const pass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (!pass || !confirm) {
                e.preventDefault();
                toastr.warning('Por favor, completá ambos campos.');
                return;
            }
            if (pass.length < 8) {
                e.preventDefault();
                toastr.warning('La contraseña debe tener al menos 8 caracteres.');
                return;
            }
            if (!/[A-Z]/.test(pass)) {
                e.preventDefault();
                toastr.warning('La contraseña debe incluir al menos una letra MAYÚSCULA.');
                return;
            }
            if (!/[a-z]/.test(pass)) {
                e.preventDefault();
                toastr.warning('La contraseña debe incluir al menos una letra MINÚSCULA.');
                return;
            }
            if (!/[0-9]/.test(pass)) {
                e.preventDefault();
                toastr.warning('La contraseña debe incluir al menos un NÚMERO.');
                return;
            }
            if (pass !== confirm) {
                e.preventDefault();
                toastr.error('Las contraseñas no coinciden.');
                return;
            }
        });
    </script>
</body>
</html>