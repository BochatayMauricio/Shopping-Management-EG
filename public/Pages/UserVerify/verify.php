<?php
include_once __DIR__ . '/../../../app/Config/config.php';
include_once __DIR__ . '/../../../app/Services/user.services.php';
include_once __DIR__ . '/../../../app/Services/alert.service.php';

// Capturamos la BASE_URL para armar links a prueba de fallos
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

$token = $_GET['token'] ?? '';
$verificationResult = null;

if (!empty($token)) {
    $verificationResult = verifyUserByToken($token);
} else {
    $verificationResult = "Token no proporcionado.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Rosario - Verificación de Cuenta</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/public/Shared/globalStyles.css">
    <link rel="stylesheet" href="verify.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <main id="main-content" class="login-container" style="text-align: center;">
        <?php if ($verificationResult === true): ?>
            <div class="login-header" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="font-size: 4.5rem; color: #10b981; margin-bottom: 15px;"></i>
                <h1>¡Cuenta Verificada!</h1>
                <p class="shopping-subtitle">Tu correo ha sido confirmado con éxito.</p>
            </div>
            
            <p style="color: #555; margin-bottom: 25px; font-size: 0.95rem;">
                Ya podés acceder a todas nuestras promociones y beneficios exclusivos.
            </p>
            
            <a href="<?php echo $baseUrl; ?>/public/Pages/Login/login.php" class="login-button" style="text-decoration: none; display: block; text-align: center;">
                <span class="btn-text">Iniciar Sesión</span>
            </a>
            
        <?php else: ?>
            <div class="login-header" style="margin-bottom: 20px;">
                <i class="fas fa-times-circle" style="font-size: 4.5rem; color: #ef4444; margin-bottom: 15px;"></i>
                <h1>Error de Verificación</h1>
                <p class="shopping-subtitle" style="color: #ef4444; font-weight: 500;">
                    <?php echo htmlspecialchars($verificationResult); ?>
                </p>
            </div>
            
            <p style="color: #555; margin-bottom: 25px; font-size: 0.95rem;">
                Si creés que esto es un error, por favor intentá registrarte nuevamente o contactá a soporte.
            </p>
            
            <a href="<?php echo $baseUrl; ?>/public/Pages/Home/home.php" class="login-button" style="background: #4a5568; text-decoration: none; display: block; text-align: center;">
                <span class="btn-text">Volver al Inicio</span>
            </a>
        <?php endif; ?>
    </main>
</body>
</html>