<?php
// El logout se procesa en cada página ANTES del HTML, no aquí
// Este archivo solo renderiza el navbar
?>

<link rel="stylesheet" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/public/Components/navbar/navbar.css">

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="../../../public/Components/navbar/navbar.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

        <div class="container-fluid">
            <a class="navbar-brand" href="../../Pages/Home/home.php">Shopping Rosario</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../../Pages/Home/home.php">Inicio</a>
                </li>
                <li class="nav-item">
                        <a class="nav-link" href="../../Pages/News/News.php">Novedades</a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Stores/Stores.php">Locales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Promotions/Promotions.php">Promociones</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Contact/contact.php">Contacto</a>
                    </li>
                <?php if($user && $user['type'] === 'owner'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Client%20Requests/clientRequests.php">
                            Solicitudes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Redeem Promo/redeemPromo.php">Activar Promo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/ownerReports/ownerReports.php">Reportes</a>
                    </li>
                <?php endif; ?>
                <?php if ($user && $user['type'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Requests/requests.php">Solicitudes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Reports/reports.php">Reportes</a>
                    </li>
                <?php endif; ?>
                <?php if ($user): ?>
                    <li class="nav-item-dropdown dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            
                            <div class="user-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                                <small class="user-role d-block text-muted"><?php echo htmlspecialchars($user['type']).' - '.htmlspecialchars($user['category']); ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../Pages/User Portal/userPortal.php"><i class="fas fa-user me-2"></i>Mi perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php
                if (!$user):
                ?>
                    <li class="nav-item btn-session">
                        <a class="nav-link" href="../../../public/Pages/Login/login.php">Iniciar sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
            </div>
        </div>
    </nav>
    
<?php 

// RENDERIZADO GLOBAL DE ALERTAS
// Al estar aquí, cualquier redirección que traiga una alerta en $_SESSION la mostrará justo debajo del navbar.
if (class_exists('AlertService')) {
    AlertService::render(); 
}
?>

<script>
    // Función para cerrar la alerta manualmente al hacer clic en la "X"
    function closeAlert() {
        const alertBox = document.getElementById('flash-alert');
        if (alertBox) {
            // Le damos una pequeña transición de opacidad (opcional, depende de tu CSS)
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.3s ease';
            
            // Lo eliminamos del DOM después de la transición
            setTimeout(() => {
                alertBox.remove();
            }, 300);
        }
    }

    // Función para cerrar la alerta después de 5 segundos
    document.addEventListener('DOMContentLoaded', () => {
        const alertBox = document.getElementById('flash-alert');
        if (alertBox && alertBox.getAttribute('data-auto-hide') === 'true') {
            setTimeout(() => {
                closeAlert();
            }, 5000); // 5000 milisegundos = 5 segundos
        }
    });
</script>