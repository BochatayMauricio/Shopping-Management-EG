<?php
// Inicialización (sesión, logout, usuario)
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/controllers/store.controller.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';
include_once __DIR__ . '/../../../app/Services/user.services.php';

// Si no está logueado, al login
if (!$user) {
    header("Location: ../Login/login.php");
    exit();
}

// Cargamos datos específicos según el tipo de usuario
$userId = $user['cod'] ?? $user['id'];
$allMyPromos = ($user['type'] === 'client') ? getClientPromotions($userId) : [];
$myStores = ($user['type'] === 'owner') ? getStoresByOwner($userId) : [];

// ========== PAGINACIÓN PARA PROMOCIONES DEL CLIENTE ==========
$cantPorPagPortal = 4;
$paginaPortal = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaPortal < 1) $paginaPortal = 1;

$totalRegistrosPortal = count($allMyPromos);
$totalPaginasPortal = $totalRegistrosPortal > 0 ? ceil($totalRegistrosPortal / $cantPorPagPortal) : 1;

$inicioPortal = ($paginaPortal - 1) * $cantPorPagPortal;
$myPromos = array_slice($allMyPromos, $inicioPortal, $cantPorPagPortal);
// =============================================================

// Lógica de progreso para la barra (3 para Medium, 5 para Premium)
$progress = ($user['type'] === 'client') ? getClientLevelProgress($userId) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Portal - Shopping Rosario</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="userPortal.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content py-5">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-lg-4">
    <div class="card profile-card shadow-sm rounded-4 p-4 text-center">
        <div class="user-avatar-circle rounded-circle mb-3 mx-auto">
            <i class="fas <?php echo ($user['type'] === 'admin') ? 'fa-user-shield' : 'fa-user'; ?>"></i>
        </div>
        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['name']); ?></h4>
        <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
        
        <?php if ($user['type'] === 'client'): 
            // Determinamos el nivel visual según la contabilidad de promos
            // Si tiene 5 o más es Premium, si tiene 3 o más es Medium, sino es Inicial
            $displayLevel = 'Inicial';
            if ($progress['used'] >= 5) $displayLevel = 'Premium';
            elseif ($progress['used'] >= 3) $displayLevel = 'Medium';
        ?>
            <div class="mb-3">
                <span class="badge badge-<?php echo strtolower($displayLevel); ?> rounded-pill px-3 py-2">
                    Nivel <?php echo $displayLevel; ?>
                </span>
            </div>

            <?php if ($progress && !$progress['is_premium']): ?>
                <div class="level-progress-container mt-4 mb-3 text-start">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <small class="fw-bold text-muted" style="font-size: 0.7rem;">PROGRESO A <?= strtoupper($progress['next_level']) ?></small>
                        <small class="text-primary fw-bold"><?= $progress['used'] ?>/<?= $progress['goal'] ?></small>
                    </div>
                    <div class="progress rounded-pill" style="height: 8px; background-color: #eee;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             style="width: <?= $progress['percentage'] ?>%"></div>
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">
                        <i class="fas fa-info-circle me-1"></i> Falta<?= $progress['missing'] > 1 ? 'n' : '' ?> <b><?= $progress['missing'] ?></b> para subir de nivel.
                    </p>
                </div>
            <?php elseif ($progress && $progress['is_premium']): ?>
                <div class="alert alert-warning border-0 rounded-3 py-2 mt-3 mb-0 d-flex align-items-center justify-content-center">
                    <i class="fas fa-crown me-2"></i> <small class="fw-bold">¡USUARIO PREMIUM!</small>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <hr>
        <div class="text-start mt-3">
            <p class="small mb-2"><strong>Tipo de cuenta:</strong> <?php echo ucfirst($user['type']); ?></p>
            <?php if($user['type'] === 'client'): ?>
                <p class="small mb-0"><strong>Promos obtenidas:</strong> <?php echo $totalRegistrosPortal; ?></p>
            <?php else: ?>
                <p class="small mb-0"><strong>ID Interno:</strong> #<?php echo $user['cod'] ?? $user['id']; ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

                <div class="col-lg-8">
                    
                    <?php if ($user['type'] === 'admin'): ?>
                        <h3 class="fw-bold mb-4">Panel de Administración</h3>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                                    <div class="icon-box bg-warning text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-clipboard-list fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold">Solicitudes</h5>
                                    <p class="text-muted small">Aprobar o rechazar nuevas promociones de los locales.</p>
                                    <a href="../../Pages/Requests/requests.php" class="btn btn-dark rounded-pill w-100 mt-auto">Gestionar</a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center">
                                    <div class="icon-box bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-pie fa-lg"></i>
                                    </div>
                                    <h5 class="fw-bold">Reportes</h5>
                                    <p class="text-muted small">Visualizar métricas de desempeño y estadísticas globales.</p>
                                    <a href="../../Pages/Reports/reports.php" class="btn btn-outline-primary rounded-pill w-100 mt-auto">Ver Gráficas</a>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border-0 shadow-sm rounded-4 p-4 d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary fa-2x me-3"></i>
                                    <p class="mb-0 small text-muted">Como administrador, puedes supervisar toda la actividad comercial del Shopping. Recuerda revisar las solicitudes diariamente para mantener el catálogo actualizado.</p>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($user['type'] === 'owner'): ?>
                        <h3 class="fw-bold mb-4">Panel de Gestión de Negocio</h3>
                        
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-store me-2 text-primary"></i>
                                <?php echo count($myStores) > 1 ? 'Mis Locales' : 'Mi Local Asignado'; ?>
                            </h5>

                            <?php if (!empty($myStores)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($myStores as $store): 
                                        $logo_db = $store['logo'] ?? '';
                                        $final_url = filter_var($logo_db, FILTER_VALIDATE_URL) ? $logo_db : "../../../assets/stores/" . ($logo_db ?: 'default_logo.png');
                                        $brand_color = $store['color'] ?? '#0d6efd';
                                    ?>
                                        <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
                                            <div class="store-logo-circle me-3" style="background-color: <?= $brand_color ?>10; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <img src="<?= $final_url ?>" style="width: 100%; height: 100%; object-fit: contain; padding: 10px;" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiB2aWV3Qm94PSIwIDAgMTUwIDE1MCI+PHJlY3QgZmlsbD0iI2VlZSIgd2lkdGg9IjE1MCIgaGVpZ2h0PSIxNTAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iI2FhYSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiPkxvZ288L3RleHQ+PC9zdmc+';">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($store['name'] ?? 'Sin nombre'); ?></h6>
                                                <p class="text-muted small mb-0">Local <?= htmlspecialchars($store['local_number'] ?? '-'); ?> • <?= htmlspecialchars($store['category'] ?? 'General'); ?></p>
                                            </div>
                                            <div class="ms-auto">
                                                <a href="../Promotions/Promotions.php?store=<?= urlencode($store['name']) ?>" class="btn btn-sm btn-outline-dark rounded-pill">Ver Promos</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0">No tienes locales vinculados.</div>
                            <?php endif; ?>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <div class="me-4"><i class="fas fa-bullhorn fa-3x"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Impulsa tus ventas</h5>
                                    <p class="mb-0 opacity-75 small">Crea promociones y espera la aprobación del administrador.</p>
                                </div>
                                <div class="ms-auto">
                                    <a href="../Promotions/Promotions.php" class="btn btn-light fw-bold px-4">Nueva Promo</a>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
    <h3 class="fw-bold mb-4">Mis Cupones Obtenidos</h3>
    
    <?php if ($totalRegistrosPortal > 0): ?>
        <div class="row g-3">
            <?php foreach ($myPromos as $promo): 
                // Forzamos la lectura del status y eliminamos posibles espacios
                $currentStatus = isset($promo['status']) ? trim(strtolower($promo['status'])) : 'active';
                
                $isUsed = ($currentStatus === 'used');
                $isPending = ($currentStatus === 'pending');
                $isRejected = ($currentStatus === 'rejected');
                $isExpired = (isset($promo['is_expired']) && $promo['is_expired'] == 1 && !$isUsed);
            ?>
                <div class="col-md-6">
                    <div class="card coupon-card h-100 shadow-sm <?php echo ($isExpired || $isUsed || $isPending || $isRejected) ? 'coupon-expired' : ''; ?>" 
                         style="border-top: 5px solid <?php 
                            if ($isUsed) echo '#198754';
                            elseif ($isPending) echo '#ffc107';
                            elseif ($isRejected) echo '#dc3545';
                            elseif ($isExpired) echo '#adb5bd';
                            else echo ($promo['store_color'] ?? '#0d6efd'); 
                         ?>;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="fw-bold mb-0 <?php echo ($isExpired || $isUsed || $isPending || $isRejected) ? 'text-muted' : ''; ?>"><?= htmlspecialchars($promo['title']) ?></h5>
                                <span class="badge <?php 
                                    if ($isUsed) echo 'bg-success';
                                    elseif ($isPending) echo 'bg-warning text-dark';
                                    elseif ($isRejected) echo 'bg-danger';
                                    elseif ($isExpired) echo 'bg-secondary';
                                    else echo 'bg-danger';
                                ?>">
                                    <?php 
                                        if ($isUsed) echo 'USADA';
                                        elseif ($isPending) echo 'PENDIENTE';
                                        elseif ($isRejected) echo 'RECHAZADA';
                                        else echo $promo['discount_label'];
                                    ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-3"><i class="fas fa-store me-1"></i> <?= htmlspecialchars($promo['store_name']) ?></p>
                            
                            <?php if ($isPending): ?>
                                <div class="alert alert-warning py-2 px-3 mb-3">
                                    <i class="fas fa-clock me-1"></i>
                                    <small>Esperando aprobación del local</small>
                                </div>
                            <?php elseif ($isRejected): ?>
                                <div class="alert alert-danger py-2 px-3 mb-3">
                                    <i class="fas fa-times-circle me-1"></i>
                                    <small>Solicitud rechazada por el local</small>
                                </div>
                            <?php else: ?>
                                <div class="coupon-code-box text-center mb-3 <?php echo ($isExpired || $isUsed) ? 'bg-light opacity-50' : ''; ?>">
                                    <span class="small d-block text-muted mb-1">CÓDIGO DE CANJE</span>
                                    <strong class="<?php echo ($isExpired || $isUsed) ? 'text-muted' : 'text-dark'; ?>">SR-<?= $promo['id'] . ($user['cod'] ?? $user['id']) ?></strong>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="<?php echo $isExpired ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                    <?php 
                                        if($isUsed) echo "Canjeado con éxito";
                                        elseif($isPending) echo "Solicitado: " . date('d/m/Y', strtotime($promo['obtained_at']));
                                        elseif($isRejected) echo "";
                                        else echo $isExpired ? 'VENCIDA' : 'Vence: ' . $promo['valid_until']; 
                                    ?>
                                </small>
                                
                                <?php if ($isUsed): ?>
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-none" disabled>
                                        <i class="fas fa-check-circle me-1"></i> Usada
                                    </button>
                                <?php elseif ($isPending): ?>
                                    <button class="btn btn-sm btn-warning rounded-pill px-3" disabled>
                                        <i class="fas fa-hourglass-half me-1"></i> Pendiente
                                    </button>
                                <?php elseif ($isRejected): ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" disabled>
                                        <i class="fas fa-times me-1"></i> Rechazada
                                    </button>
                                <?php elseif ($isExpired): ?>
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 disabled" disabled>Expiró</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-dark rounded-pill px-3">Listas para usar</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalPaginasPortal > 1): ?>
        <nav class="pagination-container mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item <?= $paginaPortal <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $paginaPortal - 1 ?>">Anterior</a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPaginasPortal; $i++): ?>
                    <li class="page-item <?= $i === $paginaPortal ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $paginaPortal >= $totalPaginasPortal ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $paginaPortal + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
        <p class="text-center text-muted small">Mostrando página <?= $paginaPortal ?> de <?= $totalPaginasPortal ?> (<?= $totalRegistrosPortal ?> cupones)</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
            <i class="fas fa-ticket-alt fa-3x text-light mb-3"></i>
            <h5 class="text-muted">Aún no tienes promociones.</h5>
            <a href="../Promotions/Promotions.php" class="btn btn-primary btn-sm mt-2 rounded-pill">Ver Catálogo</a>
        </div>
    <?php endif; ?>
<?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>
</body>
</html>