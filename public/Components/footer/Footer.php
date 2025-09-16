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
                        <a class="nav-link" href="#">Locales</a>
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
    </ul>
  </div>
</body>
  
