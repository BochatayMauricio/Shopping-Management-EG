<?php
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';

// Seguridad: Solo Owner
if (!$user || $user['type'] !== 'owner') { 
    header("Location: ../Home/home.php"); 
    exit(); 
}

$ownerId = $user['cod'] ?? $user['id'];

// 1. Data Rendimiento Promociones (Barras)
$performanceStats = getOwnerPromotionsPerformance($ownerId);
$perfLabels = array_column($performanceStats, 'title');
$perfValues = array_column($performanceStats, 'used_count');

// 2. Data Estado de Ofertas (Dona)
$statusStats = getOwnerPromotionsStatusStats($ownerId);
$statusLabels = [];
$statusValues = [];
$statusColors = [];
foreach($statusStats as $stat) {
    if($stat['status'] === 'active') { $statusLabels[] = 'Activas'; $statusColors[] = '#10b981'; } // Verde
    elseif($stat['status'] === 'pending') { $statusLabels[] = 'Pendientes'; $statusColors[] = '#f59e0b'; } // Amarillo
    else { $statusLabels[] = 'Canceladas/Expiradas'; $statusColors[] = '#ef4444'; } // Rojo
    $statusValues[] = $stat['total'];
}

// 3. Data Tipos de Clientes (Torta)
$clientStats = getOwnerClientsLevelStats($ownerId);
$clientLabels = array_map('ucfirst', array_column($clientStats, 'level'));
$clientValues = array_column($clientStats, 'total');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="ownerReports.css">
</head>
<body class="bg-light">
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="container py-5 mt-4">
        <header class="mb-5 border-bottom pb-3">
            <h1 class="fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-3"></i>Dashboard de Mis Locales</h1>
            <p class="text-muted">Analizá el rendimiento de tus promociones y conoce a tus clientes.</p>
        </header>

        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 text-center">Rendimiento de Promociones Activas (Cupones Canjeados)</h5>
                        <?php if(empty($perfLabels)): ?>
                            <div class="alert alert-info text-center mt-4">No tienes promociones activas para medir.</div>
                        <?php else: ?>
                            <div style="position: relative; height: 350px;">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 text-center">Estado de mis Ofertas</h5>
                        <?php if(empty($statusLabels)): ?>
                            <div class="alert alert-info text-center mt-4">No has creado ninguna promoción aún.</div>
                        <?php else: ?>
                            <div style="position: relative; height: 300px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 text-center">Tipos de Clientes que me eligen</h5>
                        <?php if(empty($clientLabels)): ?>
                            <div class="alert alert-info text-center mt-4">Aún no han canjeado cupones en tus locales.</div>
                        <?php else: ?>
                            <div style="position: relative; height: 300px;">
                                <canvas id="clientsChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Poppins', sans-serif";
            
            // 1. Gráfico de Barras (Rendimiento)
            <?php if(!empty($perfLabels)): ?>
            new Chart(document.getElementById('performanceChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($perfLabels) ?>,
                    datasets: [{
                        label: 'Cupones Canjeados',
                        data: <?= json_encode($perfValues) ?>,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
            <?php endif; ?>

            // 2. Gráfico de Dona (Estados)
            <?php if(!empty($statusLabels)): ?>
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($statusLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($statusValues) ?>,
                        backgroundColor: <?= json_encode($statusColors) ?>,
                        borderWidth: 0, hoverOffset: 10
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
            });
            <?php endif; ?>

            // 3. Gráfico de Torta (Clientes)
            <?php if(!empty($clientLabels)): ?>
            new Chart(document.getElementById('clientsChart'), {
                type: 'pie',
                data: {
                    labels: <?= json_encode($clientLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($clientValues) ?>,
                        backgroundColor: ['#94a3b8', '#0d6efd', '#fbbf24'],
                        borderWidth: 0, hoverOffset: 10
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>