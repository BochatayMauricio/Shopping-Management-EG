<?php
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();

// Datos estáticos de promociones
$promotions = [
    [
        'id' => 1,
        'title' => 'Combo Doble Cuarto',
        'image' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?auto=format&fit=crop&q=80&w=500',
        'discount' => '-30% OFF',
        'badge_color' => '#dc2626',
        'valid_until' => '30/11',
        'price' => 8500,
        'original_price' => 12150,
        'store_name' => "McDonald's",
        'store_logo' => 'fa-brands fa-mcdonalds',
        'store_color' => '#DB0007',
        'local_number' => '1',
        'store_category' => 'Gastronomia',
        'client_category' => 'Inicial',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'title' => 'Remeras Selección',
        'image' => 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&q=80&w=500',
        'discount' => '2x1',
        'badge_color' => '#000000',
        'valid_until' => 'Solo efectivo',
        'price' => 15000,
        'original_price' => null,
        'store_name' => 'H&M',
        'store_logo' => 'fa-solid fa-shirt',
        'store_color' => '#E50010',
        'local_number' => '18',
        'store_category' => 'Ropa',
        'client_category' => 'Medium',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'title' => 'Latte Grande',
        'image' => 'https://images.unsplash.com/photo-1559496417-e7f25cb247f3?auto=format&fit=crop&q=80&w=500',
        'discount' => '-15% OFF',
        'badge_color' => '#15803d',
        'valid_until' => 'Llevando 2 medialunas',
        'price' => 4200,
        'original_price' => null,
        'store_name' => 'Starbucks',
        'store_logo' => 'fa-solid fa-mug-hot',
        'store_color' => '#00704A',
        'local_number' => '2',
        'store_category' => 'Gastronomia',
        'client_category' => 'Premium',
        'status' => 'active'
    ],
    [
        'id' => 4,
        'title' => 'Airpods Pro',
        'image' => 'https://images.unsplash.com/photo-1595935736128-db1f0a261263?auto=format&fit=crop&q=80&w=500',
        'discount' => 'Finde',
        'badge_color' => '#2563eb',
        'valid_until' => '12 Cuotas s/interés',
        'price' => 199999,
        'original_price' => null,
        'store_name' => 'Apple Store',
        'store_logo' => 'fa-brands fa-apple',
        'store_color' => '#555555',
        'local_number' => '5',
        'store_category' => 'Tecnologia',
        'client_category' => 'Premium',
        'status' => 'active'
    ],
    [
        'id' => 5,
        'title' => 'Zapatillas Running',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=500',
        'discount' => '-40% OFF',
        'badge_color' => '#dc2626',
        'valid_until' => 'Hasta agotar stock',
        'price' => 45000,
        'original_price' => 75000,
        'store_name' => 'Nike',
        'store_logo' => 'fa-solid fa-shoe-prints',
        'store_color' => '#FF6600',
        'local_number' => '8',
        'store_category' => 'Deportes',
        'client_category' => 'Medium',
        'status' => 'active'
    ],
    [
        'id' => 6,
        'title' => 'Whopper Doble',
        'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&q=80&w=500',
        'discount' => '-25% OFF',
        'badge_color' => '#dc2626',
        'valid_until' => 'Martes y Miércoles',
        'price' => 6800,
        'original_price' => 9066,
        'store_name' => 'Burger King',
        'store_logo' => 'fa-solid fa-burger',
        'store_color' => '#D62300',
        'local_number' => '10',
        'store_category' => 'Gastronomia',
        'client_category' => 'Inicial',
        'status' => 'active'
    ],
    [
        'id' => 7,
        'title' => 'Jeans Premium',
        'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&q=80&w=500',
        'discount' => '3x2',
        'badge_color' => '#000000',
        'valid_until' => 'Válido hasta fin de mes',
        'price' => 28000,
        'original_price' => null,
        'store_name' => 'Zara',
        'store_logo' => 'fa-solid fa-shirt',
        'store_color' => '#000000',
        'local_number' => '12',
        'store_category' => 'Ropa',
        'client_category' => 'Premium',
        'status' => 'inactive'
    ],
    [
        'id' => 8,
        'title' => 'Notebook Gamer',
        'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&q=80&w=500',
        'discount' => '-20% OFF',
        'badge_color' => '#2563eb',
        'valid_until' => '18 cuotas sin interés',
        'price' => 850000,
        'original_price' => 1062500,
        'store_name' => 'Samsung',
        'store_logo' => 'fa-solid fa-laptop',
        'store_color' => '#1428A0',
        'local_number' => '15',
        'store_category' => 'Tecnologia',
        'client_category' => 'Medium',
        'status' => 'active'
    ]
];

// Filtrar solo promociones activas
$activePromotions = array_filter($promotions, function($promo) {
    return $promo['status'] === 'active';
});

// Obtener filtros
$filterCategory = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$filterDiscount = isset($_GET['discount']) ? trim($_GET['discount']) : 'all';
$filterStore = isset($_GET['store']) ? trim(urldecode($_GET['store'])) : 'all';
$filterClientCategory = isset($_GET['client_category']) ? trim($_GET['client_category']) : 'all';

// Filtrar promociones activas
$filteredPromotions = array_filter($activePromotions, function($promo) use ($filterCategory, $filterDiscount, $filterStore, $filterClientCategory) {
    $categoryMatch = ($filterCategory === 'all') || (strtolower(trim($promo['store_category'])) === strtolower($filterCategory));
    $storeMatch = ($filterStore === 'all') || (strtolower(trim($promo['store_name'])) === strtolower($filterStore));
    $clientCategoryMatch = ($filterClientCategory === 'all') || (strtolower(trim($promo['client_category'])) === strtolower($filterClientCategory));
    
    // Filtro de descuento
    $discountMatch = true;
    if ($filterDiscount !== 'all') {
        if (strpos($promo['discount'], '%') !== false) {
            $percentage = intval(preg_replace('/[^0-9]/', '', $promo['discount']));
            if ($filterDiscount === 'high') {
                $discountMatch = $percentage >= 30;
            } elseif ($filterDiscount === 'medium') {
                $discountMatch = $percentage >= 15 && $percentage < 30;
            }
        } elseif ($filterDiscount === 'special') {
            $discountMatch = strpos($promo['discount'], 'x') !== false || strpos($promo['discount'], 'Finde') !== false;
        }
    }
    
    return $categoryMatch && $storeMatch && $discountMatch && $clientCategoryMatch;
});

$activeCount = count($filteredPromotions);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="Promotions.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content">
        <div class="container-custom">
            
            <!-- HEADER Y FILTROS -->
            <div class="promotions-header">
                <div class="promotions-title-section">
                    <h1 class="promotions-title">Promociones</h1>
                    <span class="promotions-badge"><?php echo $activeCount; ?> Ofertas activas</span>
                </div>
                
                <div class="promotions-filters">
                    <span class="filter-label">Filtrar por:</span>
                    
                    <!-- Dropdown Rubro -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-rubro-promo" class="dropdown-checkbox">
                        <label for="dropdown-rubro-promo" class="dropdown-toggle-custom">
                            Rubro <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?category=all<?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Todos</a>
                            <a href="?category=gastronomia<?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Gastronomía</a>
                            <a href="?category=ropa<?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Ropa</a>
                            <a href="?category=tecnologia<?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Tecnología</a>
                            <a href="?category=deportes<?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Deportes</a>
                        </div>
                    </div>

                    <!-- Dropdown Descuentos -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-descuento" class="dropdown-checkbox">
                        <label for="dropdown-descuento" class="dropdown-toggle-custom">
                            Descuentos <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?discount=all<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Todos</a>
                            <a href="?discount=high<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">30% o más</a>
                            <a href="?discount=medium<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">15% - 29%</a>
                            <a href="?discount=special<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">2x1 / 3x2</a>
                        </div>
                    </div>

                    <!-- Dropdown Local -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-local" class="dropdown-checkbox">
                        <label for="dropdown-local" class="dropdown-toggle-custom">
                            Local <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?store=all<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom">Todos</a>
                            <?php
                            $uniqueStores = array_unique(array_column($activePromotions, 'store_name'));
                            foreach ($uniqueStores as $storeName): ?>
                                <a href="?store=<?php echo urlencode($storeName); ?><?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterClientCategory !== 'all' ? '&client_category=' . $filterClientCategory : ''; ?>" class="dropdown-item-custom"><?php echo htmlspecialchars($storeName); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Dropdown Categoría de Cliente -->
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-client-category" class="dropdown-checkbox">
                        <label for="dropdown-client-category" class="dropdown-toggle-custom">
                            Categoría <i class="fas fa-chevron-down"></i>
                        </label>
                        <div class="dropdown-menu-custom">
                            <a href="?client_category=all<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?>" class="dropdown-item-custom">Todas</a>
                            <a href="?client_category=inicial<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?>" class="dropdown-item-custom">Inicial</a>
                            <a href="?client_category=medium<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?>" class="dropdown-item-custom">Medium</a>
                            <a href="?client_category=premium<?php echo $filterCategory !== 'all' ? '&category=' . $filterCategory : ''; ?><?php echo $filterDiscount !== 'all' ? '&discount=' . $filterDiscount : ''; ?><?php echo $filterStore !== 'all' ? '&store=' . urlencode($filterStore) : ''; ?>" class="dropdown-item-custom">Premium</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID DE PROMOCIONES -->
            <div class="promotions-grid">
                <?php
                if (count($filteredPromotions) > 0) {
                    foreach ($filteredPromotions as $promo) {
                ?>
                        <div class="promo-card">
                            <!-- Imagen + Badge -->
                            <div class="promo-image-container">
                                <span class="promo-badge" style="background-color: <?php echo $promo['badge_color']; ?>;">
                                    <?php echo htmlspecialchars($promo['discount']); ?>
                                </span>
                                <!-- Chip de Categoría de Cliente -->
                                <span class="promo-client-category <?php echo strtolower($promo['client_category']); ?>">
                                    <?php echo htmlspecialchars($promo['client_category']); ?>
                                </span>
                                <img src="<?php echo htmlspecialchars($promo['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($promo['title']); ?>" 
                                     class="promo-image">
                            </div>
                            
                            <!-- Contenido -->
                            <div class="promo-content">
                                <h3 class="promo-title"><?php echo htmlspecialchars($promo['title']); ?></h3>
                                <p class="promo-validity"><?php echo htmlspecialchars($promo['valid_until']); ?></p>
                                
                                <div class="promo-price-section">
                                    <span class="promo-price">$<?php echo number_format($promo['price'], 0, ',', '.'); ?></span>
                                    <?php if ($promo['original_price']): ?>
                                        <span class="promo-price-original">$<?php echo number_format($promo['original_price'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Separador y Local -->
                                <div class="promo-store-section">
                                    <div class="promo-store-logo" style="background-color: <?php echo $promo['store_color']; ?>;">
                                        <?php if (isset($promo['store_logo'])): ?>
                                            <i class="<?php echo $promo['store_logo']; ?>"></i>
                                        <?php elseif (isset($promo['store_logo_text'])): ?>
                                            <span class="promo-store-logo-text"><?php echo $promo['store_logo_text']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="promo-store-info">
                                        <span class="promo-store-name"><?php echo htmlspecialchars($promo['store_name']); ?></span>
                                        <span class="promo-store-local">Local <?php echo htmlspecialchars($promo['local_number']); ?></span>
                                    </div>
                                </div>

                                <!-- Botón Solicitar (solo si es cliente logueado) -->
                                <?php if ($user && $user['rol'] === 'client'): ?>
                                    <button class="promo-request-btn">
                                        <i class="fas fa-ticket-alt"></i> Solicitar Promoción
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="no-results">
                            <i class="fas fa-search"></i>
                            <p>No se encontraron promociones con los filtros seleccionados</p>
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