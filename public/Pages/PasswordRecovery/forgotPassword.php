<?php
    // Incluimos el controlador que armamos en el paso anterior
    include_once __DIR__ . '/../../../app/controllers/password.controller.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Rosario - Recuperar Contraseña</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ajustá las rutas de CSS según donde esté tu globalStyles y login.css -->
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="../Login/login.css"> 
    
    <!-- Dependencias de Toastr (Asegurate de que las rutas sean correctas o usá CDNs) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-unlock-alt"></i> Recuperar Clave</h1>
            <p class="shopping-subtitle">Ingresá tu correo para recibir las instrucciones</p>
        </div>
        
        <!-- Usamos novalidate para que Toastr ataje los errores de campos vacíos -->
        <form id="forgotForm" method="POST" action="" novalidate>
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
                        required
                    >
                </div>
            </div>

            <button type="submit" class="login-button" name="btnRecoverPassword">
                <span class="btn-text">Enviar enlace de recuperación</span>
            </button>

            <div class="forgot-password" style="margin-top: 20px; text-align: center;">
                <a href="../Login/login.php"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a>
            </div>
        </form>
    </div>

    <!-- Scripts de Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Incluimos tu renderizador de alertas -->
    <?php include_once '../../../public/Components/alert/alert.php' ?>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            if (!email) {
                e.preventDefault();
                toastr.warning('Por favor, ingresá tu correo electrónico.');
            }
        });
    </script>
</body>
</html>