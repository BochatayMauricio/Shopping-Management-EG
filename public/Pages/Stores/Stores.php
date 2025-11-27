<?php
// Datos estáticos de las tiendas con colores de marca
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();

$stores = [
    [
        'id' => 1,
        'name' => "McDonald's",
        'floor' => 'Planta Baja',
        'local_number' => '12',
        'category' => 'Gastronomia',
        'logo_icon' => 'https://imgs.search.brave.com/sgIqmpaUkd9ap3Yjb8rqXxdeqKc_0m4j2iZD_nwCALo/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/Zm90b3MtcHJlbWl1/bS9tLWFtYXJpbGxh/LWVzdGEtZGlidWph/ZGEtcm9qby1uYXJh/bmphXzEwNTgzMzgt/Mjc1MDAuanBnP3Nl/bXQ9YWlzX2h5YnJp/ZCZ3PTc0MCZxPTgw',
        'brand_color' => '#DA291C',
        'owner' => 'Juan Pérez'
    ],
    [
        'id' => 2,
        'name' => 'Starbucks',
        'floor' => 'Planta Baja',
        'local_number' => '8',
        'category' => 'Gastronomia',
        'logo_icon' => 'https://imgs.search.brave.com/ywfHpQA0rfOt7M8eSY-gsGpv_Sn3Z2Lg_2dvo0W4UwQ/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9ibG9n/LmxvZ29tYXN0ZXIu/YWkvaHMtZnMvaHVi/ZnMvc3RhcmJ1Y2tz/JTIwbG9nbyUyMGN1/cnJlbnQuanBnP3dp/ZHRoPTE3MDAmaGVp/Z2h0PTExNDgmbmFt/ZT1zdGFyYnVja3Ml/MjBsb2dvJTIwY3Vy/cmVudC5qcGc',
        'brand_color' => '#00704A',
        'owner' => 'María García'
    ],
    [
        'id' => 3,
        'name' => 'Zara',
        'floor' => 'Primer Piso',
        'local_number' => '25',
        'category' => 'Ropa',
        'logo_icon' => 'https://imgs.search.brave.com/wtFEY8UZs9olLaBb-CCmf-1OOrATNoBvPp7kvmL72ss/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zLndp/ZGdldC1jbHViLmNv/bS9zYW1wbGVzL3o1/Z0RrNE02ZmtOVVEx/SFBXWmpVS1ZwbUp5/UDIvanJkMW1hVHZN/MVo3WUdhRkhPUHIv/QTBCNTlBRTYtM0Qz/Qy00Mzg1LUFCM0Yt/QjYyNTRFQThDNTZG/LmpwZz9xPTcw',
        'brand_color' => '#000000',
        'owner' => 'Carlos López'
    ],
    [
        'id' => 4,
        'name' => 'Nike',
        'floor' => 'Primer Piso',
        'local_number' => '15',
        'category' => 'Deportes',
        'logo_icon' => 'https://imgs.search.brave.com/4WnQhBRTGk8JLUqNETVFbzWVqGII_Yh0OQdFY1p0ihs/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMudmVjdGVlenku/Y29tL3N5c3RlbS9y/ZXNvdXJjZXMvdGh1/bWJuYWlscy8wMTAv/OTk0LzQxMS9zbWFs/bC9uaWtlLWxvZ28t/d2hpdGUtY2xvdGhl/cy1kZXNpZ24taWNv/bi1hYnN0cmFjdC1m/b290YmFsbC1pbGx1/c3RyYXRpb24td2l0/aC1ibGFjay1iYWNr/Z3JvdW5kLWZyZWUt/dmVjdG9yLmpwZw',
        'brand_color' => '#FF6600',
        'owner' => 'Ana Martínez'
    ],
    [
        'id' => 5,
        'name' => 'Apple Store',
        'floor' => 'Planta Baja',
        'local_number' => '5',
        'category' => 'Tecnologia',
        'logo_icon' => 'https://imgs.search.brave.com/UvL_BzNlgjjyoyf6_ctU7dySQx77eC3_Pv_6EaGP_lY/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4t/aWNvbnMtcG5nLmZy/ZWVwaWsuY29tLzI1/Ni8xNDA3Ny8xNDA3/NzA3MS5wbmc_c2Vt/dD1haXNfd2hpdGVf/bGFiZWw',
        'brand_color' => '#555555',
        'owner' => 'Pedro Sánchez'
    ],
    [
        'id' => 6,
        'name' => 'H&M',
        'floor' => 'Primer Piso',
        'local_number' => '18',
        'category' => 'Ropa',
        'logo_icon' => 'https://imgs.search.brave.com/wkxnVPP8Radkgp8_cDvO6QnFKaCr_VIokkNttOjeWwo/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly90aHVt/YnMuZHJlYW1zdGlt/ZS5jb20vYi9oLW0t/bG9nby04MDc4NjE2/MC5qcGc',
        'brand_color' => '#E50010',
        'owner' => 'Laura Fernández'
    ],
    [
        'id' => 7,
        'name' => 'Samsung',
        'floor' => 'Segundo Piso',
        'local_number' => '30',
        'category' => 'Tecnologia',
        'logo_icon' => 'https://imgs.search.brave.com/TN9BEKQfVPOl3i8djLiYwZzbMnkda5OsJ01r7P6YR1o/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4t/aWNvbnMtcG5nLmZy/ZWVwaWsuY29tLzI1/Ni84ODIvODgyNzQ3/LnBuZz9zZW10PWFp/c19oeWJyaWQ',
        'brand_color' => '#1428A0',
        'owner' => 'Roberto Díaz'
    ],
    [
        'id' => 8,
        'name' => 'Adidas',
        'floor' => 'Segundo Piso',
        'local_number' => '22',
        'category' => 'Deportes',
        'logo_icon' => 'https://imgs.search.brave.com/blm76o3mvLsvWFzG7cg6HMN9IgZ0kFA-Ahs7MfNxZ2k/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMudmVjdGVlenku/Y29tL3N5c3RlbS9y/ZXNvdXJjZXMvdGh1/bWJuYWlscy8wMTAv/OTk0LzI5MS9zbWFs/bC9hZGlkYXMtc3lt/Ym9sLWxvZ28td2hp/dGUtY2xvdGhlcy1k/ZXNpZ24taWNvbi1h/YnN0cmFjdC1mb290/YmFsbC1pbGx1c3Ry/YXRpb24td2l0aC1i/bGFjay1iYWNrZ3Jv/dW5kLWZyZWUtdmVj/dG9yLmpwZw',
        'brand_color' => '#000000',
        'owner' => 'Sofía Ruiz'
    ],
    [
        'id' => 9,
        'name' => 'Burger King',
        'floor' => 'Planta Baja',
        'local_number' => '10',
        'category' => 'Gastronomia',
        'logo_icon' => 'https://imgs.search.brave.com/OXl9xD16suJzYXninm99ZvPKR8Lq5PM9Z7ovneUc1Vw/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pbWcu/aWNvbnM4LmNvbS9j/b29sLzEyMDAvYnVy/Z2VyLWtpbmctbmV3/LWxvZ28uanBn',
        'brand_color' => '#D62300',
        'owner' => 'Miguel Torres'
    ]
];

// Obtener los filtros
$filterCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$filterFloor = isset($_GET['floor']) ? $_GET['floor'] : 'all';
$searchName = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filtrar tiendas
$filteredStores = array_filter($stores, function($store) use ($filterCategory, $filterFloor, $searchName) {
    $categoryMatch = ($filterCategory === 'all') || (strtolower($store['category']) === strtolower($filterCategory));
    $floorMatch = ($filterFloor === 'all') || ($store['floor'] === $filterFloor);
    $nameMatch = empty($searchName) || (stripos($store['name'], $searchName) !== false);
    
    return $categoryMatch && $floorMatch && $nameMatch;
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locales - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="stores.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content">
        <div class="container-custom">
            <!-- Header con filtros -->
            <div class="stores-header">
                <h1 class="stores-title">Locales</h1>
                <div class="filter-dropdown-group">
                    <span class="filter-label">Filtrar por:</span>
                    
                    <!-- Dropdown Rubro -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-rubro" class="dropdown-checkbox">
                        <label for="dropdown-rubro" class="dropdown-toggle-custom">
                            Rubro <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?category=all<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'all' ? 'active' : ''; ?>">Todos</a>
                            <a href="?category=gastronomia<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'gastronomia' ? 'active' : ''; ?>">Gastronomía</a>
                            <a href="?category=ropa<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'ropa' ? 'active' : ''; ?>">Ropa</a>
                            <a href="?category=tecnologia<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'tecnologia' ? 'active' : ''; ?>">Tecnología</a>
                            <a href="?category=deportes<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'deportes' ? 'active' : ''; ?>">Deportes</a>
                            <a href="?category=retail<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterFloor !== 'all' ? '&floor=' . urlencode($filterFloor) : ''; ?>" class="dropdown-item-custom <?php echo $filterCategory === 'retail' ? 'active' : ''; ?>">Retail</a>
                        </div>
                    </div>

                    <!-- Input directo de Nombre -->
                    <form method="GET" class="filter-input-form">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($filterCategory); ?>">
                        <input type="hidden" name="floor" value="<?php echo htmlspecialchars($filterFloor); ?>">
                        <input 
                            type="text" 
                            name="search" 
                            class="filter-input-direct" 
                            placeholder="Buscar por nombre..." 
                            value="<?php echo htmlspecialchars($searchName); ?>"
                        >
                        <button type="submit" class="filter-input-btn"><i class="fas fa-search"></i></button>
                    </form>

                    <!-- Dropdown Piso -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-piso" class="dropdown-checkbox">
                        <label for="dropdown-piso" class="dropdown-toggle-custom">
                            Piso <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?floor=all<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterCategory !== 'all' ? '&category=' . urlencode($filterCategory) : ''; ?>" class="dropdown-item-custom <?php echo $filterFloor === 'all' ? 'active' : ''; ?>">Todos</a>
                            <a href="?floor=Planta+Baja<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterCategory !== 'all' ? '&category=' . urlencode($filterCategory) : ''; ?>" class="dropdown-item-custom <?php echo $filterFloor === 'Planta Baja' ? 'active' : ''; ?>">Planta Baja</a>
                            <a href="?floor=Primer+Piso<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterCategory !== 'all' ? '&category=' . urlencode($filterCategory) : ''; ?>" class="dropdown-item-custom <?php echo $filterFloor === 'Primer Piso' ? 'active' : ''; ?>">Primer Piso</a>
                            <a href="?floor=Segundo+Piso<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?><?php echo $filterCategory !== 'all' ? '&category=' . urlencode($filterCategory) : ''; ?>" class="dropdown-item-custom <?php echo $filterFloor === 'Segundo Piso' ? 'active' : ''; ?>">Segundo Piso</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de locales -->
            <div class="stores-grid-horizontal">
                <?php
                if (count($filteredStores) > 0) {
                    foreach ($filteredStores as $store) {
                ?>
                        <div class="store-card-horizontal">
                            <div>
                                <!-- Lado izquierdo: Logo con color de marca -->
                                <div class="store-logo-section" style="background-color: <?php echo $store['brand_color']; ?>;">
                                    <!-- <i class="fas store-logo-icon"></i> -->
                                    <img class="store-logo-icon" src="<?php echo htmlspecialchars($store['logo_icon']); ?>" alt="<?php echo htmlspecialchars($store['name']); ?> Logo" >
                                </div>
                                
                                <!-- Lado derecho: Información -->
                                <div class="store-info-section">
                                    <h3 class="store-brand-name"><?php echo htmlspecialchars($store['name']); ?></h3>
                                    <p class="store-location"><?php echo htmlspecialchars($store['floor']); ?> - Local <?php echo htmlspecialchars($store['local_number']); ?></p>
                                </div>
                            </div>

                            <a href="../Promotions/Promotions.php?store=<?php echo urlencode($store['name']); ?>" class="store-promotions-btn">Ver Promociones</a>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="no-results">
                            <i class="fas fa-search"></i>
                            <p>No se encontraron locales con los filtros seleccionados</p>
                          </div>';
                }
                ?>
            </div>
        </div>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>