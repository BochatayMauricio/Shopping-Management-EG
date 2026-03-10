<footer class="bg-dark text-white">
    <link rel="stylesheet" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/public/Components/footer/footer.css">
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
                        <a class="nav-link text-white" href="../../Pages/Home/home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../../Pages/News/News.php">Novedades</a>
                    </li>
                    <?php if(!$user): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Stores/Stores.php">Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Contact/contact.php">Contacto</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['type'] === 'client'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Stores/Stores.php">Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Promotions/Promotions.php">Promociones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Contact/contact.php">Contacto</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['type'] === 'owner'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Stores/Stores.php">Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Promotions/Promotions.php">Promociones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Contact/contact.php">Contacto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Client%20Requests/clientRequests.php">Solicitudes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Redeem%20Promo/redeemPromo.php">Activar Promo</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user['type'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Stores/Stores.php">Gestión Locales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Requests/requests.php">Solicitudes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../../Pages/Reports/reports.php">Reportes</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4 map-container">
                <div id="map">
                    <div id="map-fallback" style="height:180px; background:#ddd; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#666;">
                        <span><i class="fas fa-map-marker-alt me-2"></i>Mapa no disponible</span>
                    </div>
                </div>
            </div>
            <span class="navbar-text text-white">
                &copy; <?php echo date('Y'); ?> Shopping Management EG. Todos los derechos reservados.
            </span>
        </div>
    </div>
</footer>


<script>
(function() {
    // Solo inicializar si el mapa no existe aún
    if (window.mapInitialized) return;
    
    function initMap() {
        var mapContainer = document.getElementById('map');
        var fallback = document.getElementById('map-fallback');
        
        if (!mapContainer || typeof L === 'undefined') {
            // Leaflet no cargó, mostrar fallback
            return;
        }
        
        // Evitar reinicialización
        if (mapContainer._leaflet_id) return;
        window.mapInitialized = true;
        
        try {
            // Ocultar fallback
            if (fallback) fallback.style.display = 'none';
            
            var map = L.map('map').setView([-32.927741122548404, -60.666928740713516], 15);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([-32.927741122548404, -60.666928740713516]).addTo(map)
                .bindPopup('Shopping Rosario')
                .openPopup();

            setTimeout(function() { map.invalidateSize(); }, 200);
        } catch(e) {
            console.error('Error mapa:', e);
            if (fallback) fallback.style.display = 'flex';
        }
    }
    
    if (document.readyState === 'complete') {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }
})();
</script>