<?php
// Datos estáticos de las tiendas
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();
$stores = [
    [
        'id' => 1,
        'name' => 'Zara',
        'ubication' => 'Planta Baja - Local 12',
        'category' => 'Ropa',
        'id_owner' => 1,
        'created_at' => '2024-01-15 10:30:00'
    ],
    [
        'id' => 2,
        'name' => 'Nike Store',
        'ubication' => 'Primer Piso - Local 25',
        'category' => 'Deportes',
        'id_owner' => 2,
        'created_at' => '2024-01-20 14:20:00'
    ],
    [
        'id' => 3,
        'name' => 'Apple Store',
        'ubication' => 'Planta Baja - Local 5',
        'category' => 'Tecnologia',
        'id_owner' => 3,
        'created_at' => '2024-02-01 09:15:00'
    ],
    [
        'id' => 4,
        'name' => 'Burger King',
        'ubication' => 'Segundo Piso - Patio de Comidas',
        'category' => 'Comida',
        'id_owner' => 4,
        'created_at' => '2024-02-10 11:45:00'
    ],
    [
        'id' => 5,
        'name' => 'H&M',
        'ubication' => 'Primer Piso - Local 18',
        'category' => 'Ropa',
        'id_owner' => 5,
        'created_at' => '2024-02-15 16:30:00'
    ],
    [
        'id' => 6,
        'name' => 'Samsung Electronics',
        'ubication' => 'Planta Baja - Local 8',
        'category' => 'Tecnologia',
        'id_owner' => 6,
        'created_at' => '2024-03-01 10:00:00'
    ],
    [
        'id' => 7,
        'name' => 'Adidas Originals',
        'ubication' => 'Primer Piso - Local 30',
        'category' => 'Deportes',
        'id_owner' => 7,
        'created_at' => '2024-03-10 13:20:00'
    ],
    [
        'id' => 8,
        'name' => 'Starbucks',
        'ubication' => 'Planta Baja - Local 2',
        'category' => 'Comida',
        'id_owner' => 8,
        'created_at' => '2024-03-20 08:30:00'
    ],
    [
        'id' => 9,
        'name' => 'Librería Ateneo',
        'ubication' => 'Segundo Piso - Local 45',
        'category' => 'Otros',
        'id_owner' => 9,
        'created_at' => '2024-04-01 12:00:00'
    ],
    [
        'id' => 10,
        'name' => 'Farmacity',
        'ubication' => 'Planta Baja - Local 15',
        'category' => 'Otros',
        'id_owner' => 10,
        'created_at' => '2024-04-15 15:45:00'
    ]
];

// Obtener los filtros si existen
$filterCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$searchName = isset($_GET['search']) ? trim($_GET['search']) : '';

// Filtrar tiendas según la categoría y nombre
$filteredStores = array_filter($stores, function($store) use ($filterCategory, $searchName) {
    // Filtro por categoría
    $categoryMatch = ($filterCategory === 'all') || (strtolower($store['category']) === strtolower($filterCategory));
    
    // Filtro por nombre
    $nameMatch = empty($searchName) || (stripos($store['name'], $searchName) !== false);
    
    return $categoryMatch && $nameMatch;
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
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="stores.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Locales del Shopping</h1>
                <p class="page-subtitle">Descubre todas las tiendas disponibles y sus promociones</p>
            </div>

            <div class="filters-section">
                <form method="GET" action="" class="search-form">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input" 
                            placeholder="Buscar local por nombre..." 
                            value="<?php echo htmlspecialchars($searchName); ?>"
                        >
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($filterCategory); ?>">

                        <?php if (!empty($searchName)): ?>
                            <a href="?category=<?php echo htmlspecialchars($filterCategory); ?>" class="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                
                <div class="filter-buttons">
                    <a href="?category=all<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'all' ? 'active' : ''; ?>">Todos</a>
                    <a href="?category=ropa<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'ropa' ? 'active' : ''; ?>">Ropa</a>
                    <a href="?category=tecnologia<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'tecnologia' ? 'active' : ''; ?>">Tecnología</a>
                    <a href="?category=comida<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'comida' ? 'active' : ''; ?>">Comida</a>
                    <a href="?category=deportes<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'deportes' ? 'active' : ''; ?>">Deportes</a>
                    <a href="?category=otros<?php echo !empty($searchName) ? '&search=' . urlencode($searchName) : ''; ?>" class="filter-btn <?php echo $filterCategory === 'otros' ? 'active' : ''; ?>">Otros</a>
                </div>
            </div>

            <div class="stores-grid">
                <?php
                if (count($filteredStores) > 0) {
                    foreach ($filteredStores as $store) {
                        // Determinar el icono según la categoría
                        $icon = 'fa-store';
                        switch(strtolower($store['category'])) {
                            case 'ropa':
                                $icon = 'fa-shirt';
                                break;
                            case 'tecnologia':
                                $icon = 'fa-laptop';
                                break;
                            case 'comida':
                                $icon = 'fa-utensils';
                                break;
                            case 'deportes':
                                $icon = 'fa-dumbbell';
                                break;
                            case 'otros':
                                $icon = 'fa-shopping-bag';
                                break;
                        }
                ?>
                        <div class="store-card">
                            <div class="store-card-header">
                                <div class="store-icon">
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <span class="store-category"><?php echo htmlspecialchars($store['category']); ?></span>
                            </div>
                            
                            <div class="store-card-body">
                                <h3 class="store-name"><?php echo htmlspecialchars($store['name']); ?></h3>
                                
                                <div class="store-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($store['ubication']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?php echo date('d/m/Y', strtotime($store['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="store-card-footer">
                                <a href="promotions.php?store_id=<?php echo $store['id']; ?>" class="btn-promotions">
                                    <i class="fas fa-tags"></i>
                                    Ver Promociones
                                </a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    $message = !empty($searchName) 
                        ? 'No se encontraron locales con el nombre "' . htmlspecialchars($searchName) . '"' 
                        : 'No hay locales disponibles en esta categoría';
                    echo '<div class="no-stores">
                            <i class="fas fa-store-slash"></i>
                            <p>' . $message . '</p>
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