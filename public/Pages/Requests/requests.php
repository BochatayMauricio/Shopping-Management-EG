<?php
// Inicialización (sesión, logout, usuario)
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';

// Seguridad: Solo admin
if (!$user || $user['type'] !== 'admin') { 
    header("Location: userPortal.php"); 
    exit(); 
}

$allPendingPromos = getPendingPromotions();

// ========== PAGINACIÓN ==========
$cantPorPag = 6;
$paginaReq = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaReq < 1) $paginaReq = 1;

$totalRegistrosReq = count($allPendingPromos);
$totalPaginasReq = $totalRegistrosReq > 0 ? ceil($totalRegistrosReq / $cantPorPag) : 1;

$inicioReq = ($paginaReq - 1) * $cantPorPag;
$pendingPromos = array_slice($allPendingPromos, $inicioReq, $cantPorPag);
// ================================

// Procesar aprobación o rechazo
if (isset($_POST['action_promo'])) {
    $promoId = $_POST['promo_id'];
    $newStatus = ($_POST['action_promo'] === 'approve') ? 'active' : 'rejected';
    
    if (updatePromotionStatus($promoId, $newStatus)) {
        header("Location: requests.php?status=" . $newStatus); 
        exit();
    }
}

// ========== PROCESAR NUEVO ADMIN ==========
if (isset($_POST['btnCreateAdmin'])) {
    $adminName = trim($_POST['adminName']);
    $adminEmail = trim($_POST['adminEmail']);
    $adminPass = $_POST['adminPassword'];

    // Usamos la función registerUser con tipo 'admin'
    // Como type !== 'client', no afectará tu sesión actual
    $result = registerUser($adminName, $adminEmail, $adminPass, 'admin');

    if ($result === true) {
        header("Location: requests.php?admin_created=1");
        exit();
    } else {
        $error_msg = ($result === "email_exists") ? "El email ya existe." : "Error al crear administrador.";
        header("Location: requests.php?admin_error=" . urlencode($error_msg));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="requests.css">
</head>
<body class="bg-light">
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Solicitudes de Promociones</h2>
                <p class="text-muted">Revisa las sugerencias enviadas por los locatarios.</p>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2"><?= $totalRegistrosReq ?> Pendientes</span>
        </div>
        
        <?php if ($totalRegistrosReq > 0): ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="ps-4">Preview</th>
                                <th>Comercio / Título</th>
                                <th>Descuento y Precio</th>
                                <th>Nivel / Días</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingPromos as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="rounded-3 overflow-hidden" style="width: 80px; height: 60px;">
                                            <img src="<?= htmlspecialchars($p['image']) ?>" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDgwIDYwIj48cmVjdCBmaWxsPSIjZWVlIiB3aWR0aD0iODAiIGhlaWdodD0iNjAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iI2FhYSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTAiPkltZzwvdGV4dD48L3N2Zz4=';">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary small"><?= htmlspecialchars($p['store_name']) ?></div>
                                        <div class="fw-bold"><?= htmlspecialchars($p['title']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger mb-1">-<?= $p['discount'] ?>%</span>
                                        <div class="small"><b>$<?= number_format($p['price'], 0, ',', '.') ?></b> <span class="text-muted text-decoration-line-through">$<?= number_format($p['original_price'], 0, ',', '.') ?></span></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary mb-1"><?= htmlspecialchars($p['client_category']) ?></span>
                                        <div class="text-muted x-small"><?= htmlspecialchars($p['week_days']) ?></div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <form method="POST" class="d-flex justify-content-center gap-2">
                                            <input type="hidden" name="promo_id" value="<?= $p['id'] ?>">
                                            <button type="submit" name="action_promo" value="approve" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-check me-1"></i> Aprobar
                                            </button>
                                            <button type="submit" name="action_promo" value="reject" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="fas fa-times me-1"></i> Rechazar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if ($totalPaginasReq > 1): ?>
            <nav class="pagination-container mt-4 d-flex justify-content-center">
                <ul class="pagination">
                    <li class="page-item <?= $paginaReq <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $paginaReq - 1 ?>">Anterior</a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPaginasReq; $i++): ?>
                        <li class="page-item <?= $i === $paginaReq ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $paginaReq >= $totalPaginasReq ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $paginaReq + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
            <p class="text-center text-muted small">Mostrando página <?= $paginaReq ?> de <?= $totalPaginasReq ?> (<?= $totalRegistrosReq ?> solicitudes)</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-clipboard-check fa-4x text-light mb-3"></i>
                <h4 class="text-muted">¡Todo al día!</h4>
                <p class="text-muted mb-0">No hay nuevas promociones esperando aprobación.</p>
                <div class="mt-4">
                    <a href="../User%20Portal/userPortal.php" class="btn btn-primary rounded-pill px-4">Volver al Portal</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once '../../Components/footer/Footer.php'; ?>
</body>
</html>