<footer class="bg-dark text-white">
    <link rel="stylesheet" href="/Shopping-Management-EG/public/Components/footer/footer.css">
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>


    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span >
                    Contacto
                </span>
                <div class="d-flex flex-column contact-info">
                    <a href="mailto:info@shoppingrosario.com">info@shoppingrosario.com</a>
                    <span>Asunto: Shopping Management Rosario</span>
                </div>
            </div>
            <div class="col-md-3">
                <ul class="navbar-nav footer-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Inicio</a>
                    </li>
                    <?php if(!$user): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Stores/Stores.php">Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Contacto</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['userType'] === 'client'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Tiendas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Ofertas</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['userType'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Gestión Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Validar Cuentas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Solicitudes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Novedades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Reportes</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4 map-container">
                <div id="map"></div>
            </div>
            <span class="navbar-text text-white">
                &copy; <?php echo date('Y'); ?> Shopping Management EG. Todos los derechos reservados.
            </span>
        </div>
    </div>
</footer>


<script>
    var map = L.map('map').setView([-32.927741122548404, -60.666928740713516], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([-32.927741122548404, -60.666928740713516]).addTo(map)
        .bindPopup('Shopping Rosario')
        .openPopup();
</script>