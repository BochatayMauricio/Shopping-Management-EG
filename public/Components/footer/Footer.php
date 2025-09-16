<footer class="bg-dark text-white py-3 mt-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="navbar-text text-white">
                    &copy; <?php echo date('Y'); ?> Shopping Management EG. Todos los derechos reservados.
                </span>
            </div>
            <div class="col-md-6">
                <ul class="navbar-nav d-flex flex-row justify-content-end">
                    <li class="nav-item me-3">
                        <a class="nav-link text-white" href="#">Inicio</a>
                    </li>
                    <?php if(!$user): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Locales</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Contacto</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['userType'] === 'client'): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Tiendas</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Ofertas</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['userType'] === 'admin'): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Gestión Locales</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Validar Cuentas</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Solicitudes</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Novedades</a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link text-white" href="#">Reportes</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</footer>
  
