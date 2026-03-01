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
        <header class="reports-header mb-5">
            <h1 class="reports-title">Análisis Operativo</h1>
        </header>

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