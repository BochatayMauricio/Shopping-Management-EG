<?php
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/user.services.php';
include_once __DIR__ . '/../../../app/Services/alert.service.php';

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
    <title>Verificación de Cuenta | Shopping Rosario</title>
    <!-- Google Fonts consistent with Home -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Frameworks and Styles -->
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="userRegister.css">
</head>

<body style="background-color: #f8f9fa; font-family: 'Poppins', sans-serif;">
    <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>
    <?php include_once __DIR__ . '/../../Components/alert/alert.php'; ?>

    <main class="container my-5 py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 text-center p-4" style="border-radius: 15px;">
                    <div class="card-body">
                        <?php if ($verificationResult === true): ?>
                            <div class="mb-4">
                                <span style="font-size: 5rem; color: #28a745;">&#10004;</span>
                            </div>
                            <h1 class="fw-bold mb-3" style="color: #333;">¡Cuenta Verificada!</h1>
                            <p class="text-secondary mb-4">
                                Tu dirección de correo electrónico ha sido confirmada con éxito. 
                                Ya puedes disfrutar de todas nuestras promociones.
                            </p>
                            <a href="../Login/login.php" class="btn btn-primary btn-lg w-100 fw-bold" style="border-radius: 10px;">
                                Iniciar Sesión
                            </a>
                        <?php else: ?>
                            <div class="mb-4">
                                <span style="font-size: 5rem; color: #dc3545;">&#10008;</span>
                            </div>
                            <h1 class="fw-bold mb-3" style="color: #333;">Error de Verificación</h1>
                            <p class="text-secondary mb-4">
                                <?php echo htmlspecialchars($verificationResult); ?>
                            </p>
                            <p class="small text-muted mb-4">
                                Si crees que esto es un error, por favor intenta registrarte nuevamente o contacta a soporte.
                            </p>
                            <a href="../Home/home.php" class="btn btn-secondary btn-lg w-100 fw-bold" style="border-radius: 10px;">
                                Volver al Inicio
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../../Components/footer/Footer.php'; ?>
</body>

</html>