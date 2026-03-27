<?php
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';

if (!$user || $user['type'] !== 'owner') {
    header("Location: ../Login/login.php");
    exit();
}

$message = "";
$messageType = "";

if (isset($_POST['btnRedeem'])) {
    $code = trim($_POST['promo_code']);
    $result = redeemPromotionCode($code);

    $message = $result['message'];
    $messageType = $result['success'] ? "success" : "danger";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Canjear Código - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">

</head>

<body class="bg-light">
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-ticket-alt fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Validar Promoción</h3>
                        <p class="text-muted">Ingresa el código SR mostrado por el cliente</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="text-center">
                        <div class="mb-4">
                            <input type="text" name="promo_code" class="form-control form-control-lg text-center fw-bold"
                                placeholder="SR-0000" required maxlength="20" style="letter-spacing: 2px;">
                        </div>
                        <button type="submit" name="btnRedeem" class="btn btn-dark btn-lg w-100 rounded-pill">
                            Confirmar Canje
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>