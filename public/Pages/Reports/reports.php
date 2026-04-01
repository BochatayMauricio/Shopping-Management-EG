<?php
// Inicialización (sesión, logout, usuario)
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';
include_once __DIR__ . '/../../../app/Services/user.services.php';

// Seguridad: Solo admin
if (!$user || $user['type'] !== 'admin') { 
    header("Location: userPortal.php"); 
    exit(); 
}

// ========== PROCESAR NUEVO ADMIN ==========
if (isset($_POST['btnCreateAdmin'])) {
    $adminName = trim($_POST['adminName']);
    $adminEmail = trim($_POST['adminEmail']);
    $adminPass = $_POST['adminPassword'];

    // Usamos la función registerUser con tipo 'admin'
    $result = registerUser($adminName, $adminEmail, $adminPass, 'admin');

    if ($result === true) {
        header("Location: reports.php?admin_created=1");
        exit();
    } else {
        $error_msg = ($result === "email_exists") ? "El email ya existe." : "Error al crear administrador.";
        if ($result === "password_too_short") $error_msg = "La contraseña es muy corta.";
        
        header("Location: reports.php?admin_error=" . urlencode($error_msg));
        exit();
    }
}

// --- 1. PROCESAMIENTO DE DATOS REALES ---

// Estadísticas de Promociones
$promoStatsRaw = getPromotionsStats();
$totalPromos = 0;
$rejectedPromos = 0;
foreach($promoStatsRaw as $stat) {
    $totalPromos += $stat['cantidad'];
    if($stat['status'] === 'rejected') $rejectedPromos = $stat['cantidad'];
}
$usedPromos = getTotalUsedPromotions();

// Estadísticas de Locales (Gráfica de Barras)
$storeStatsRaw = getStoresStatsByCategory();
$storeLabels = array_map('ucfirst', array_column($storeStatsRaw, 'category'));
$storeValues = array_column($storeStatsRaw, 'total');
$totalStores = array_sum($storeValues);

// Estadísticas de Clientes (Gráfica de Dona)
$clientStatsRaw = getClientsStatsByLevel();
$clientLabels = array_map('ucfirst', array_column($clientStatsRaw, 'level'));
$clientValues = array_column($clientStatsRaw, 'total');
$totalClients = array_sum($clientValues);

$ownersDirectory = getOwnersWithStoresAndPromotions();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="reports.css">
</head>
<body class="bg-light">
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="container py-5">
        
        <header class="reports-header mb-4 d-flex justify-content-between align-items-center">
            <h1 class="reports-title mb-0">Análisis Operativo</h1>
            <button class="btn btn-dark rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAdmin">
                <i class="fas fa-user-plus me-2"></i> Alta Administrador
            </button>
        </header>

        <?php if(isset($_GET['admin_created'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> Nuevo administrador creado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['admin_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['admin_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="kpi-card h-100">
                    <div class="kpi-icon bg-soft-blue"><i class="fas fa-tags"></i></div>
                    <span class="kpi-value"><?= $totalPromos ?></span>
                    <span class="kpi-label">Promociones Totales</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card h-100 border-danger-subtle">
                    <div class="kpi-icon" style="background-color: #fee2e2; color: #dc2626;"><i class="fas fa-ban"></i></div>
                    <span class="kpi-value"><?= $rejectedPromos ?></span>
                    <span class="kpi-label">Solicitudes Rechazadas</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card h-100 border-success-subtle">
                    <div class="kpi-icon bg-soft-green"><i class="fas fa-check-circle"></i></div>
                    <span class="kpi-value"><?= $usedPromos ?></span>
                    <span class="kpi-label">Cupones Canjeados</span>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-store-alt fa-2x text-primary mb-2"></i>
                        <h5 class="chart-title">Locales por Rubro (Total: <?= $totalStores ?>)</h5>
                    </div>
                    <div style="position: relative; height: 350px;">
                        <canvas id="storesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-users-cog fa-2x text-warning mb-2"></i>
                        <h5 class="chart-title">Clientes por Nivel (Total: <?= $totalClients ?>)</h5>
                    </div>
                    <div style="position: relative; height: 350px;">
                        <canvas id="clientsChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 mb-5">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-address-book fa-2x text-dark me-3"></i>
                <h3 class="fw-bold mb-0 text-dark">Directorio de Dueños y Locales</h3>
            </div>

            <div class="accordion shadow-sm" id="ownersAccordion">
                <?php if(empty($ownersDirectory)): ?>
                    <div class="alert alert-info border-0 rounded-4">No hay dueños registrados en el sistema.</div>
                <?php else: ?>
                    <?php foreach ($ownersDirectory as $ownerId => $ownerData): ?>
                        <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading-owner-<?= $ownerId ?>">
                                <button class="accordion-button collapsed fw-bold bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-owner-<?= $ownerId ?>" aria-expanded="false" aria-controls="collapse-owner-<?= $ownerId ?>">
                                    <i class="fas fa-user-tie text-primary me-2"></i> 
                                    <?= htmlspecialchars($ownerData['name']) ?> 
                                    <span class="text-muted fw-normal ms-2 small">(<?= htmlspecialchars($ownerData['email']) ?>)</span>
                                </button>
                            </h2>
                            <div id="collapse-owner-<?= $ownerId ?>" class="accordion-collapse collapse" aria-labelledby="heading-owner-<?= $ownerId ?>" data-bs-parent="#ownersAccordion">
                                <div class="accordion-body bg-light">
                                    
                                    <?php if(empty($ownerData['stores'])): ?>
                                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Este dueño aún no tiene locales asignados.</p>
                                    <?php else: ?>
                                        <div class="row g-4">
                                            <?php foreach ($ownerData['stores'] as $storeId => $storeData): ?>
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm h-100 rounded-4">
                                                        <div class="card-body">
                                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                                                <i class="fas fa-store text-warning me-2"></i> <?= htmlspecialchars($storeData['name']) ?> 
                                                                <span class="badge bg-secondary ms-1 fw-normal">Local <?= htmlspecialchars($storeData['local_number']) ?></span>
                                                            </h6>
                                                            
                                                            <?php if(empty($storeData['promotions'])): ?>
                                                                <p class="text-muted small mb-0">No hay promociones registradas en este local.</p>
                                                            <?php else: ?>
                                                                <ul class="list-group list-group-flush">
                                                                    <?php foreach ($storeData['promotions'] as $promo): 
                                                                        // Definir colores y etiquetas para las píldoras según el estado
                                                                        $badgeClass = 'bg-secondary';
                                                                        $statusLabel = 'Desconocido';
                                                                        
                                                                        if ($promo['status'] === 'active') {
                                                                            $badgeClass = 'bg-success';
                                                                            $statusLabel = 'Activa';
                                                                        } elseif ($promo['status'] === 'pending') {
                                                                            $badgeClass = 'bg-warning text-dark';
                                                                            $statusLabel = 'Pendiente';
                                                                        } elseif ($promo['status'] === 'cancelled') {
                                                                            $badgeClass = 'bg-danger';
                                                                            $statusLabel = 'Cancelada';
                                                                        }
                                                                    ?>
                                                                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-light">
                                                                            <span class="small fw-medium text-secondary text-truncate pe-2" style="max-width: 70%;" title="<?= htmlspecialchars($promo['title']) ?>">
                                                                                <?= htmlspecialchars($promo['title']) ?> (<?= intval($promo['discount']) ?>%)
                                                                            </span>
                                                                            <span class="badge <?= $badgeClass ?> rounded-pill" style="font-size: 0.7rem;">
                                                                                <?= $statusLabel ?>
                                                                            </span>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <div class="modal fade" id="modalAdmin" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white rounded-top-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-user-shield me-2"></i>Alta de Administrador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="adminName" class="form-control bg-light border-start-0" placeholder="Ej: Juan Pérez" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="adminEmail" class="form-control bg-light border-start-0" placeholder="admin@shopping.com" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Contraseña Temporal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="adminPassword" class="form-control bg-light border-start-0" placeholder="Min. 6 caracteres" minlength="6" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="btnCreateAdmin" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                                    Registrar Administrador
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Poppins', sans-serif";
            Chart.defaults.color = '#64748b';

            // 1. Gráfica de Locales (Barras)
            const ctxStores = document.getElementById('storesChart').getContext('2d');
            new Chart(ctxStores, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($storeLabels) ?>,
                    datasets: [{
                        label: 'Cantidad',
                        data: <?= json_encode($storeValues) ?>,
                        backgroundColor: '#0d6efd',
                        borderRadius: 10,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Gráfica de Clientes (Dona)
            const ctxClients = document.getElementById('clientsChart').getContext('2d');
            new Chart(ctxClients, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($clientLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($clientValues) ?>,
                        backgroundColor: ['#94a3b8', '#0d6efd', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 25, usePointStyle: true } }
                    }
                }
            });
        });
    </script>
</body>
</html>