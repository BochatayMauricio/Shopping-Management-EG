<?php
// Footer reutilizable con diseño similar a la navbar
?>
<head>
<link rel="stylesheet" href="public/Components/footer/footer.css">

</head>
<body>
    
<div class="container-fluid justify-content-center">
    <span class="navbar-text text-white">
      &copy; <?php echo date('Y'); ?> Shopping Management EG.
    </span>
    <ul class="navbar-nav ms-3">
      <li class="nav-item">
        <a class="nav-link text-white" href="#">Inicio</a>
      </li>
      <?php if(!$user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Promociones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                <?php endif; ?>
                <?php if ($user && $user['userType'] === 'client'): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Tiendas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ofertas</a>
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
    </ul>
  </div>
</body>
  
