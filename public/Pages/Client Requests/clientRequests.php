<?php
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';

// Control de sesión seguro
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = getCurrentUser();

// Seguridad: Solo owners
if (!$user || $user['type'] !== 'owner') { 
    header("Location: ../User%20Portal/userPortal.php"); 
    exit(); 
}

$ownerId = $user['cod'];
$pendingRequests = getPendingClientRequests($ownerId);

// Procesar aprobación o rechazo
if (isset($_POST['action_request'])) {
    $clientId = $_POST['client_id'];
    $promoId = $_POST['promo_id'];
    $newStatus = ($_POST['action_request'] === 'approve') ? 'active' : 'rejected';
    
    if (updateClientRequestStatus($clientId, $promoId, $newStatus)) {
        $statusMsg = ($newStatus === 'active') ? 'approved' : 'rejected';
        header("Location: clientRequests.php?status=" . $statusMsg); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Clientes - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="clientRequests.css">
</head>
<body class="bg-light">
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0"><i class="fas fa-user-clock me-2 text-warning"></i>Solicitudes de Clientes</h2>
                <p class="text-muted">Revisa y aprueba las solicitudes de promociones de tus clientes.</p>
            </div>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">
                <i class="fas fa-hourglass-half me-1"></i><?= count($pendingRequests) ?> Pendientes
            </span>
        </div>
        
        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?= $_GET['status'] === 'approved' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $_GET['status'] === 'approved' ? 'check-circle' : 'times-circle' ?> me-2"></i>
                <?= $_GET['status'] === 'approved' ? 'Solicitud aprobada correctamente. El cliente ya puede usar su promoción.' : 'Solicitud rechazada.' ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (count($pendingRequests) > 0): ?>
            <div class="row g-4">
                <?php foreach ($pendingRequests as $req): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 request-card">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge rounded-pill" style="background-color: <?= htmlspecialchars($req['store_color']) ?>">
                                        <?= htmlspecialchars($req['store_name']) ?>
                                    </span>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i>Pendiente
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-bold mb-2"><?= htmlspecialchars($req['promo_title']) ?></h5>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-danger">-<?= $req['discount'] ?>%</span>
                                    <span class="fw-bold text-success">$<?= number_format($req['price'], 0, ',', '.') ?></span>
                                    <span class="text-muted text-decoration-line-through small">$<?= number_format($req['original_price'], 0, ',', '.') ?></span>
                                </div>
                                
                                <hr class="my-3">
                                
                                <div class="client-info">
                                    <p class="mb-1"><i class="fas fa-user text-primary me-2"></i><strong><?= htmlspecialchars($req['client_name']) ?></strong></p>
                                    <p class="mb-1 small text-muted"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($req['client_email']) ?></p>
                                    <p class="mb-0">
                                        <i class="fas fa-star text-warning me-2"></i>Nivel: 
                                        <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($req['client_category'])) ?></span>
                                    </p>
                                    <p class="mb-0 mt-2 small text-muted">
                                        <i class="fas fa-calendar me-2"></i>Solicitado: <?= date('d/m/Y H:i', strtotime($req['request_date'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pb-3">
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="client_id" value="<?= $req['id_client'] ?>">
                                    <input type="hidden" name="promo_id" value="<?= $req['id_promotion'] ?>">
                                    <button type="submit" name="action_request" value="approve" class="btn btn-success flex-fill rounded-pill">
                                        <i class="fas fa-check me-1"></i>Aprobar
                                    </button>
                                    <button type="submit" name="action_request" value="reject" class="btn btn-outline-danger flex-fill rounded-pill">
                                        <i class="fas fa-times me-1"></i>Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                <h4 class="text-muted">No hay solicitudes pendientes</h4>
                <p class="text-muted mb-0">Cuando tus clientes soliciten promociones, aparecerán aquí para que las apruebes.</p>
                <div class="mt-4">
                    <a href="../User%20Portal/userPortal.php" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Portal
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once '../../Components/footer/Footer.php'; ?>
</body>
</html>
