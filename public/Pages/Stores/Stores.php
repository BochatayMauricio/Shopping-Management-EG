<?php
session_start();
include_once './../../../app/Services/login.services.php';
$user = getCurrentUser();

// Datos de ejemplo de locales (en una aplicación real, esto vendría de la base de datos)
$stores = [
    [
        'id' => 1,
        'name' => 'McDonald\'s',
        'category' => 'comida',
        'description' => 'Comida rápida internacional con hamburguesas, papas fritas y más.',
        'location' => 'Planta Baja - Local 15',
        'phone' => '(341) 555-0101',
        'hours' => '10:00 - 22:00',
        'image' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://mcdonalds.com.ar',
        'featured' => true
    ],
    [
        'id' => 2,
        'name' => 'Zara',
        'category' => 'ropa',
        'description' => 'Moda internacional para hombres, mujeres y niños.',
        'location' => 'Primer Piso - Local 25',
        'phone' => '(341) 555-0102',
        'hours' => '10:00 - 21:00',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://zara.com',
        'featured' => false
    ],
    [
        'id' => 3,
        'name' => 'Freddo',
        'category' => 'helados',
        'description' => 'Helados artesanales con sabores únicos y deliciosos.',
        'location' => 'Planta Baja - Local 8',
        'phone' => '(341) 555-0103',
        'hours' => '12:00 - 23:00',
        'image' => 'https://images.unsplash.com/photo-1488900128323-21503983a07e?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://freddo.com.ar',
        'featured' => true
    ],
    [
        'id' => 4,
        'name' => 'Starbucks',
        'category' => 'bebidas',
        'description' => 'Café premium, frappés y bebidas especiales.',
        'location' => 'Planta Baja - Local 12',
        'phone' => '(341) 555-0104',
        'hours' => '07:00 - 22:00',
        'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://starbucks.com.ar',
        'featured' => false
    ],
    [
        'id' => 6,
        'name' => 'Burger King',
        'category' => 'comida',
        'description' => 'Hamburguesas a la parrilla con el sabor característico.',
        'location' => 'Food Court - Local F2',
        'phone' => '(341) 555-0106',
        'hours' => '10:00 - 22:00',
        'image' => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://burgerking.com.ar',
        'featured' => false
    ],
    [
        'id' => 7,
        'name' => 'Grido',
        'category' => 'helados',
        'description' => 'Helados artesanales argentinos con sabores tradicionales.',
        'location' => 'Food Court - Local F5',
        'phone' => '(341) 555-0107',
        'hours' => '11:00 - 23:00',
        'image' => 'https://images.unsplash.com/photo-1497671954146-59a89ff626ff?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://grido.com.ar',
        'featured' => true
    ],
    [
        'id' => 8,
        'name' => 'Nike',
        'category' => 'deportes',
        'description' => 'Ropa deportiva, calzado y accesorios de la marca líder.',
        'location' => 'Primer Piso - Local 18',
        'phone' => '(341) 555-0108',
        'hours' => '10:00 - 21:00',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://nike.com',
        'featured' => false
    ],
    [
        'id' => 9,
        'name' => 'La Salteña',
        'category' => 'comida',
        'description' => 'Empanadas artesanales con recetas tradicionales argentinas.',
        'location' => 'Food Court - Local F1',
        'phone' => '(341) 555-0109',
        'hours' => '11:00 - 21:00',
        'image' => 'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => '#',
        'featured' => true
    ],
    [
        'id' => 10,
        'name' => 'Havanna',
        'category' => 'bebidas',
        'description' => 'Café, alfajores y dulces de la marca argentina más reconocida.',
        'location' => 'Planta Baja - Local 20',
        'phone' => '(341) 555-0110',
        'hours' => '08:00 - 21:00',
        'image' => 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=400&h=250&fit=crop&crop=center&q=80',
        'website' => 'https://havanna.com.ar',
        'featured' => false
    ]
];

// Obtener categorías únicas para los filtros
$categories = array_unique(array_column($stores, 'category'));
sort($categories);

// Obtener filtro actual
$currentCategory = isset($_GET['category']) ? $_GET['category'] : 'todas';

// Filtrar tiendas según la categoría seleccionada
$filteredStores = $stores;
if ($currentCategory !== 'todas') {
    $filteredStores = array_filter($stores, function($store) use ($currentCategory) {
        return $store['category'] === $currentCategory;
    });
}

// Obtener búsqueda por nombre
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($searchTerm)) {
    $filteredStores = array_filter($filteredStores, function($store) use ($searchTerm) {
        return stripos($store['name'], $searchTerm) !== false || 
               stripos($store['description'], $searchTerm) !== false;
    });
}

// Función para obtener el icono de la categoría
function getCategoryIcon($category) {
    $icons = [
        'comida' => 'fas fa-utensils',
        'bebidas' => 'fas fa-coffee',
        'ropa' => 'fas fa-tshirt',
        'helados' => 'fas fa-ice-cream',
        'deportes' => 'fas fa-running',
        'tecnologia' => 'fas fa-laptop',
        'belleza' => 'fas fa-spa',
        'hogar' => 'fas fa-home',
        'libros' => 'fas fa-book',
        'juguetes' => 'fas fa-gamepad'
    ];
    
    return isset($icons[$category]) ? $icons[$category] : 'fas fa-store';
}

// Función para obtener el color de la categoría
function getCategoryColor($category) {
    $colors = [
        'comida' => 'primary',
        'bebidas' => 'success',
        'ropa' => 'info',
        'helados' => 'warning',
        'deportes' => 'danger',
        'tecnologia' => 'dark',
        'belleza' => 'secondary',
        'hogar' => 'primary',
        'libros' => 'info',
        'juguetes' => 'warning'
    ];
    
    return isset($colors[$category]) ? $colors[$category] : 'secondary';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locales - Shopping Rosario</title>
    
    <!-- Solo cargar el CSS personalizado, el navbar se encarga del resto -->
    <link rel="stylesheet" href="stores.css">
</head>
<body>
    <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>
    
    <div class="main-content">
        <div class="container">
        <!-- Filtros y Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm filters-section">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Filtros por categoría -->
                            <div class="col-lg-8">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-filter me-2"></i>
                                    Filtrar por categoría
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Botón "Todas" -->
                                    <a href="?<?php echo !empty($searchTerm) ? 'search=' . urlencode($searchTerm) : ''; ?>" 
                                       class="btn <?php echo $currentCategory === 'todas' ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                        <i class="fas fa-th-large me-1"></i>
                                        Todas
                                        <span class="badge bg-light text-primary ms-1"><?php echo count($stores); ?></span>
                                    </a>
                                    
                                    <!-- Botones de categorías -->
                                    <?php foreach ($categories as $category): ?>
                                        <?php 
                                        $categoryCount = count(array_filter($stores, function($store) use ($category) {
                                            return $store['category'] === $category;
                                        }));
                                        $searchParam = !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '';
                                        ?>
                                        <a href="?category=<?php echo $category; ?><?php echo $searchParam; ?>" 
                                           class="btn <?php echo $currentCategory === $category ? 'btn-' . getCategoryColor($category) : 'btn-outline-' . getCategoryColor($category); ?> btn-sm">
                                            <i class="<?php echo getCategoryIcon($category); ?> me-1"></i>
                                            <?php echo ucfirst($category); ?>
                                            <span class="badge bg-light text-<?php echo getCategoryColor($category); ?> ms-1">
                                                <?php echo $categoryCount; ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Buscador -->
                            <div class="col-lg-4">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-search me-2"></i>
                                    Buscar local
                                </h6>
                                <form method="GET" class="d-flex">
                                    <?php if ($currentCategory !== 'todas'): ?>
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($currentCategory); ?>">
                                    <?php endif; ?>
                                    <input type="text" 
                                           name="search" 
                                           class="form-control form-control-sm me-2" 
                                           placeholder="Nombre del local..."
                                           value="<?php echo htmlspecialchars($searchTerm); ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Limpiar filtros -->
                        <?php if ($currentCategory !== 'todas' || !empty($searchTerm)): ?>
                            <div class="mt-3 pt-3 border-top">
                                <a href="?" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>
                                    Limpiar filtros
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Locales -->
        <?php if (empty($filteredStores)): ?>
            <!-- Mensaje cuando no hay resultados -->
            <div class="row">
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No se encontraron locales</h4>
                            <p class="text-muted mb-3">
                                <?php if (!empty($searchTerm)): ?>
                                    No hay locales que coincidan con "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"
                                <?php else: ?>
                                    No hay locales en la categoría "<strong><?php echo ucfirst($currentCategory); ?></strong>"
                                <?php endif; ?>
                            </p>
                            <a href="?" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Ver todos los locales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Cards de locales -->
            <div class="row g-4">
                <?php foreach ($filteredStores as $store): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm store-card">
                            <!-- Imagen del local -->
                            <div class="position-relative">
                                <img src="<?php echo $store['image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($store['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                
                                <!-- Badge de categoría -->
                                <span class="position-absolute top-0 start-0 m-2 badge bg-<?php echo getCategoryColor($store['category']); ?>">
                                    <i class="<?php echo getCategoryIcon($store['category']); ?> me-1"></i>
                                    <?php echo ucfirst($store['category']); ?>
                                </span>
                                
                                <!-- Badge de destacado -->
                                <?php if ($store['featured']): ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                                        <i class="fas fa-star me-1"></i>
                                        Destacado
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Contenido de la card -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($store['name']); ?>
                                </h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars($store['description']); ?>
                                </p>
                                
                                <!-- Información del local -->
                                <div class="store-info mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($store['location']); ?></small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-clock text-success me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($store['hours']); ?></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-info me-2"></i>
                                        <small class="text-muted"><?php echo htmlspecialchars($store['phone']); ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <div class="d-grid gap-1 d-md-flex justify-content-md-between">
                                    <?php if ($store['website'] !== '#'): ?>
                                        <a href="<?php echo $store['website']; ?>" 
                                           target="_blank" 
                                           class="btn btn-primary btn-sm flex-fill">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            Ver promociones
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        </div> <!-- Cierre de container -->
    </div> <!-- Cierre de main-content -->
    
</body>
</html>