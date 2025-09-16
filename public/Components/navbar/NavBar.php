<?php
include_once '../../../app/Services/login.services.php';

if(isset($_GET) && isset($_GET['action'])) {
    if($_GET['action'] === 'logout') {
            logout();
    }
}
?>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="../../../public/Components/navbar/navbar.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

        <div class="container-fluid">
            <a class="navbar-brand" href="">Shopping Rosario</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../../Pages/Client Portal/clientPortal.php">Inicio</a>
                </li>
                <?php if(!$user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Stores/Stores.php">Locales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                <?php endif; ?>
                <?php if ($user && $user['userType'] === 'client'): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="../../Pages/Stores/Stores.php">Tiendas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Promociones</a>
                    </li>
                <?php endif; ?>
                <?php if ($user && $user['userType'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Gestion Locales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Validar Cuentas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Solicitudes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Novedades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reportes</a>
                    </li>
                <?php endif; ?>
                <?php if ($user): ?>
                    <li class="nav-item-dropdown dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            
                            <div class="user-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($user['userName']); ?></span>
                                <small class="user-role d-block text-muted"><?php echo htmlspecialchars($user['userType']).'-'.htmlspecialchars($user['userCategory']); ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-alt me-2"></i>Solicitudes</a></li>
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

