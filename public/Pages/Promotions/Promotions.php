<?php
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/controllers/news.controller.php';
include_once __DIR__ . '/../../../app/controllers/promotion.controller.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';

session_start();
$user = getCurrentUser();

// Datos reales de la base de datos
$promotions = getPromotionsWithStoreData();
$allStores = getAllStores();

// Parámetros de filtros de la URL
$filterCategory = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$filterDiscount = isset($_GET['discount']) ? trim($_GET['discount']) : 'all';
$filterStore = isset($_GET['store']) ? trim(urldecode($_GET['store'])) : 'all';
$filterClientCategory = isset($_GET['client_category']) ? trim($_GET['client_category']) : 'all';

// Lógica de filtrado unificada y segura
$filteredPromotions = array_filter($promotions, function($promo) use ($filterCategory, $filterDiscount, $filterStore, $filterClientCategory) {
    
    // Solo mostrar si está activa
    if (strtolower($promo['status']) !== 'active') return false;

    // Filtro de Rubro/Categoría de tienda
    $categoryMatch = ($filterCategory === 'all') || 
                     (strtolower(trim($promo['store_category'])) === strtolower($filterCategory));

    // Filtro de Nombre de tienda
    $storeMatch = ($filterStore === 'all') || 
                  (strtolower(trim($promo['store_name'])) === strtolower($filterStore));

    // Filtro de Categoría de cliente
    $clientCategoryMatch = ($filterClientCategory === 'all') || 
                           (strtolower(trim($promo['client_category'])) === strtolower($filterClientCategory));
    
    // Filtro de Descuentos (Lógica numérica sobre el label)
    $discountMatch = true;
    if ($filterDiscount !== 'all') {
        // Extraemos el número de "-30% OFF" -> 30
        $percentage = intval(preg_replace('/[^0-9]/', '', $promo['discount_label']));
        
        if ($filterDiscount === 'high') {
            $discountMatch = $percentage >= 30;
        } elseif ($filterDiscount === 'medium') {
            $discountMatch = $percentage >= 15 && $percentage < 30;
        } elseif ($filterDiscount === 'special') {
            // Detecta si es 2x1 o texto similar en el label
            $discountMatch = (strpos(strtolower($promo['discount_label']), 'x') !== false);
        }
    }
    
    return $categoryMatch && $storeMatch && $clientCategoryMatch && $discountMatch;
});

$activeCount = count($filteredPromotions);

$activeFilters = [];
if ($filterCategory !== 'all') $activeFilters['category'] = "Rubro: " . ucfirst($filterCategory);
if ($filterDiscount !== 'all') {
    $discountTexts = ['high' => '30% o más', 'medium' => '15% - 29%', 'special' => '2x1 / Especiales'];
    $activeFilters['discount'] = "Descuento: " . ($discountTexts[$filterDiscount] ?? $filterDiscount);
}
if ($filterStore !== 'all') $activeFilters['store'] = "Local: " . $filterStore;
if ($filterClientCategory !== 'all') $activeFilters['client_category'] = "Categoría: " . ucfirst($filterClientCategory);

function buildFilterUrl($paramName, $paramValue) {
    $params = $_GET; // Obtenemos todos los parámetros actuales
    if ($paramValue === 'all') {
        unset($params[$paramName]); // Si es 'all', quitamos el filtro de la URL
    } else {
        $params[$paramName] = $paramValue; // Si no, agregamos o actualizamos el valor
    }
    return '?' . http_build_query($params); // Retornamos la nueva cadena de consulta
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            
<div class="promotions-header">
                <div class="promotions-title-section">
                    <h1 class="promotions-title mb-4 pt-4">Promociones</h1>
                    <span class="promotions-badge"><?php echo $activeCount; ?> Ofertas activas</span>
                </div>
                
<div class="promotions-filters">
    <span class="filter-label">Filtrar por:</span>
    
    <div class="dropdown-custom">
        <input type="checkbox" id="dropdown-rubro-promo" class="dropdown-checkbox">
        <label for="dropdown-rubro-promo" class="dropdown-toggle-custom">
            Rubro <i class="fas fa-chevron-down"></i>
        </label>
        <div class="dropdown-menu-custom">
            <a href="<?php echo buildFilterUrl('category', 'all'); ?>" class="dropdown-item-custom">Todos</a>
            <a href="<?php echo buildFilterUrl('category', 'gastronomia'); ?>" class="dropdown-item-custom">Gastronomía</a>
            <a href="<?php echo buildFilterUrl('category', 'ropa'); ?>" class="dropdown-item-custom">Ropa</a>
            <a href="<?php echo buildFilterUrl('category', 'tecnologia'); ?>" class="dropdown-item-custom">Tecnología</a>
            <a href="<?php echo buildFilterUrl('category', 'deportes'); ?>" class="dropdown-item-custom">Deportes</a>
        </div>
    </div>

    <div class="dropdown-custom">
        <input type="checkbox" id="dropdown-descuento" class="dropdown-checkbox">
        <label for="dropdown-descuento" class="dropdown-toggle-custom">
            Descuentos <i class="fas fa-chevron-down"></i>
        </label>
        <div class="dropdown-menu-custom">
            <a href="<?php echo buildFilterUrl('discount', 'all'); ?>" class="dropdown-item-custom">Todos</a>
            <a href="<?php echo buildFilterUrl('discount', 'high'); ?>" class="dropdown-item-custom">30% o más</a>
            <a href="<?php echo buildFilterUrl('discount', 'medium'); ?>" class="dropdown-item-custom">15% - 29%</a>
            <a href="<?php echo buildFilterUrl('discount', 'special'); ?>" class="dropdown-item-custom">2x1 / Especiales</a>
        </div>
    </div>

    <div class="dropdown-custom">
        <input type="checkbox" id="dropdown-local" class="dropdown-checkbox">
        <label for="dropdown-local" class="dropdown-toggle-custom">
            Local <i class="fas fa-chevron-down"></i>
        </label>
<div class="dropdown-menu-custom">
    <a href="<?php echo buildFilterUrl('store', 'all'); ?>" class="dropdown-item-custom">Todos</a>
    <?php foreach ($allStores as $store): ?>
        <a href="<?php echo buildFilterUrl('store', $store['name']); ?>" class="dropdown-item-custom">
            <?php echo htmlspecialchars($store['name']); ?>
        </a>
    <?php endforeach; ?>
</div>
    </div>

    <div class="dropdown-custom">
        <input type="checkbox" id="dropdown-client-category" class="dropdown-checkbox">
        <label for="dropdown-client-category" class="dropdown-toggle-custom">
            Categoría <i class="fas fa-chevron-down"></i>
        </label>
        <div class="dropdown-menu-custom">
            <a href="<?php echo buildFilterUrl('client_category', 'all'); ?>" class="dropdown-item-custom">Todas</a>
            <a href="<?php echo buildFilterUrl('client_category', 'inicial'); ?>" class="dropdown-item-custom">Inicial</a>
            <a href="<?php echo buildFilterUrl('client_category', 'medium'); ?>" class="dropdown-item-custom">Medium</a>
            <a href="<?php echo buildFilterUrl('client_category', 'premium'); ?>" class="dropdown-item-custom">Premium</a>
        </div>
    </div>
</div>

                <?php if (!empty($activeFilters)): ?>
                    <div class="active-filters-container mt-3 d-flex flex-wrap gap-2 align-items-center">
                        <span class="small text-muted fw-medium me-2">Filtros aplicados:</span>
                        <?php foreach ($activeFilters as $key => $label): ?>
                            <div class="filter-chip">
                                <?php echo htmlspecialchars($label); ?>
                                <a href="?<?php 
                                    $params = $_GET; 
                                    unset($params[$key]); 
                                    echo http_build_query($params); 
                                ?>" class="ms-2 text-decoration-none">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        
                        <a href="Promotions.php" class="btn-clear-all ms-2">Limpiar todos</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="promotions-grid">
                <?php if (count($filteredPromotions) > 0): ?>
                    <?php foreach ($filteredPromotions as $promo): ?>
                        <div class="promo-card">
                            <div class="promo-image-container">
                                <?php 
                                    // Lógica de color de badge dinámica
                                    $badge_color = '#000000';
                                    $discount_val = intval(preg_replace('/[^0-9]/', '', $promo['discount_label']));
                                    if($discount_val >= 30) $badge_color = '#dc2626';
                                    elseif($discount_val > 0) $badge_color = '#2563eb';
                                ?>
                                <span class="promo-badge" style="background-color: <?php echo $badge_color; ?>;">
                                    <?php echo htmlspecialchars($promo['discount_label']); ?>
                                </span>

                                <span class="promo-client-category <?php echo strtolower($promo['client_category']); ?>">
                                    <?php echo htmlspecialchars($promo['client_category']); ?>
                                </span>
                                
                                <img src="<?php echo htmlspecialchars($promo['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($promo['title']); ?>" 
                                     class="promo-image">
                            </div>
                            
                            <div class="promo-content">
                                <h3 class="promo-title"><?php echo htmlspecialchars($promo['title']); ?></h3>
                                <p class="promo-validity">Válido hasta: <?php echo htmlspecialchars($promo['valid_until']); ?></p>
                                
                                <div class="promo-price-section">
                                    <span class="promo-price">$<?php echo number_format($promo['price'], 0, ',', '.'); ?></span>
                                    <?php if ($promo['original_price'] > 0): ?>
                                        <span class="promo-price-original">$<?php echo number_format($promo['original_price'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="promo-store-section">
                                    <div class="promo-store-logo" style="background-color: <?php echo $promo['store_color']; ?>;">
                                        <i class="<?php echo $promo['store_logo']; ?>"></i>
                                    </div>
                                    <div class="promo-store-info">
                                        <span class="promo-store-name"><?php echo htmlspecialchars($promo['store_name']); ?></span>
                                        <span class="promo-store-local">Local <?php echo htmlspecialchars($promo['local_number']); ?></span>
                                    </div>
                                </div>

                                <?php if ($user && $user['type'] === 'client'): ?>
                                    <button class="promo-request-btn">
                                        <i class="fas fa-ticket-alt"></i> Solicitar Promoción
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <p>No se encontraron promociones con los filtros seleccionados</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
        <?php if($user && $user['type'] === 'owner'): ?>
    <button type="button" class="btn btn-warning admin-fab" data-bs-toggle="modal" data-bs-target="#createPromoModal">
        <i class="fa-solid fa-plus me-2"></i> Nueva Promo
    </button>

    <div class="modal fade" id="createPromoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-tag me-2"></i>Sugerir Nueva Promoción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">Nota: Tu promoción quedará en estado <b>pendiente</b> hasta que un administrador la apruebe.</p>
                    <form action="" method="POST" >
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Título de la Promo</label>
                                <input type="text" name="title" class="form-control" placeholder="Ej: 2x1 en Hamburguesas" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Inicio</label>
                                <input type="date" name="date_from" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha Fin</label>
                                <input type="date" name="date_until" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">% Descuento</label>
                                <input type="number" name="discount" class="form-control" placeholder="Ej: 30" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Precio Promo</label>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Precio Original</label>
                                <input type="number" name="original_price" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">URL Imagen</label>
                                <input type="url" name="image" class="form-control" placeholder="https://..." required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Días de validez</label>
                                <input type="text" name="week_days" class="form-control" placeholder="Ej: Lunes a Jueves">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Categoría Cliente</label>
                                <select name="client_category" class="form-select">
                                    <option value="Inicial">Inicial</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Premium">Premium</option>
                                </select>
                            </div>
                            </div>
                        <div class="text-end mt-3">
                            <button type="submit" name="btnCreatePromo" class="btn btn-dark px-4">Enviar para Revisión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>

</body>
</html>