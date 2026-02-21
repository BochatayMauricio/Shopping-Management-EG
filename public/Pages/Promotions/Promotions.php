<?php
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/controllers/news.controller.php';
include_once __DIR__ . '/../../../app/controllers/promotion.controller.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';
include_once __DIR__ . '/../../../app/Services/user.services.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = getCurrentUser();

// --- LÓGICA DE CORRECCIÓN INTELIGENTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // SI ES OWNER CREANDO PROMO:
    if (isset($_POST['btnCreatePromo'])) {
        $_POST['id'] = $_POST['id_store']; // Adaptamos para el controlador
        if(isset($_POST['discount'])) {
            $_POST['discount'] = intval(preg_replace('/[^0-9]/', '', $_POST['discount']));
        }

        $storeId = isset($_POST['id_store']) ? intval($_POST['id_store']) : 0;
        if ($storeId > 0) {
            $promoData = [
                'title'           => $_POST['title'],
                'image'           => $_POST['image'],
                'date_from'       => $_POST['date_from'],
                'date_until'      => $_POST['date_until'],
                'client_category' => $_POST['client_category'],
                'week_days'       => $_POST['week_days'],
                'discount'        => $_POST['discount'],
                'price'           => $_POST['price'],
                'original_price'  => !empty($_POST['original_price']) ? $_POST['original_price'] : 0,
                'id_store'        => $storeId
            ];
            if (createPromotion($promoData)) {
                header("Location: Promotions.php?status=submitted");
                exit(); 
            }
        }
    }
    
    // SI ES CLIENTE SOLICITANDO PROMO:
    // Aseguramos que el controlador reciba 'id' que es lo que suele buscar
    if (isset($_POST['btnRequestPromo'])) {
        $_POST['id'] = $_POST['id_promotion'];
    }
}

// Datos de BD
$promotions = getPromotionsWithStoreData();
$allStores = getAllStores();
$myStores = ($user && $user['type'] === 'owner') ? getStoresByOwner($user['cod'] ?? $user['id']) : [];
$today = date('Y-m-d');

// --- LÓGICA DE NIVEL DINÁMICO ---
$userWeight = 1;
$myUsedPromoIds = []; 

if ($user && $user['type'] === 'client') {
    $userId = $user['cod'] ?? $user['id'];
    $progress = getClientLevelProgress($userId);
    
    // Peso dinámico para bloquear/desbloquear cards
    if ($progress['used'] >= 5) $userWeight = 3;
    elseif ($progress['used'] >= 3) $userWeight = 2;
    else $userWeight = 1;

    // Obtener las que ya usó para el check visual "YA UTILIZADA"
    $myPromos = getClientPromotions($userId);
    foreach ($myPromos as $mp) {
        // El modelo Promotion usa 'status' para el estado de la solicitud
        if ($mp['status'] === 'used') {
            $myUsedPromoIds[] = $mp['id'];
        }
    }
}

$levelWeights = ['inicial' => 1, 'medium' => 2, 'premium' => 3];

// Filtros URL
$filterCategory = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$filterDiscount = isset($_GET['discount']) ? trim($_GET['discount']) : 'all';
$filterStore = isset($_GET['store']) ? trim(urldecode($_GET['store'])) : 'all';
$filterClientCategory = isset($_GET['client_category']) ? trim($_GET['client_category']) : 'all';

$filteredPromotions = array_filter($promotions, function($promo) use ($filterCategory, $filterDiscount, $filterStore, $filterClientCategory, $today) {    
    if (strtolower($promo['status']) !== 'active') return false;
    if ($promo['date_until'] < $today) return false;
    $categoryMatch = ($filterCategory === 'all') || (strtolower(trim($promo['store_category'])) === strtolower($filterCategory));
    $storeMatch = ($filterStore === 'all') || (strtolower(trim($promo['store_name'])) === strtolower($filterStore));
    $clientCategoryMatch = ($filterClientCategory === 'all') || (strtolower(trim($promo['client_category'])) === strtolower($filterClientCategory));
    $discountMatch = true;
    if ($filterDiscount !== 'all') {
        $percentage = intval(preg_replace('/[^0-9]/', '', $promo['discount_label']));
        if ($filterDiscount === 'high') $discountMatch = $percentage >= 30;
        elseif ($filterDiscount === 'medium') $discountMatch = $percentage >= 15 && $percentage < 30;
        elseif ($filterDiscount === 'special') $discountMatch = (strpos(strtolower($promo['discount_label']), 'x') !== false);
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
    $params = $_GET; 
    if ($paramValue === 'all') unset($params[$paramName]); 
    else $params[$paramName] = $paramValue; 
    return '?' . http_build_query($params); 
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="Promotions.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <?php if (isset($_GET['request'])): ?>
        <div class="container-custom mt-3">
            <?php if ($_GET['request'] === 'success'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-clock me-2"></i>
                    <strong>¡Solicitud enviada!</strong> Tu solicitud fue enviada al dueño del local. Recibirás la promoción cuando sea aprobada.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['request'] === 'duplicate'): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Ya has solicitado esta promoción anteriormente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['request'] === 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Ocurrió un error al procesar tu solicitud. Intenta nuevamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['request'] === 'denied'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-ban me-2"></i>
                    Solo los clientes pueden solicitar promociones.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['request'] === 'level_low'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-lock me-2"></i>
                    No tienes el nivel requerido para solicitar esta promoción.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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
                        <label for="dropdown-rubro-promo" class="dropdown-toggle-custom">Rubro <i class="fas fa-chevron-down"></i></label>
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
                        <label for="dropdown-descuento" class="dropdown-toggle-custom">Descuentos <i class="fas fa-chevron-down"></i></label>
                        <div class="dropdown-menu-custom">
                            <a href="<?php echo buildFilterUrl('discount', 'all'); ?>" class="dropdown-item-custom">Todos</a>
                            <a href="<?php echo buildFilterUrl('discount', 'high'); ?>" class="dropdown-item-custom">30% o más</a>
                            <a href="<?php echo buildFilterUrl('discount', 'medium'); ?>" class="dropdown-item-custom">15% - 29%</a>
                            <a href="<?php echo buildFilterUrl('discount', 'special'); ?>" class="dropdown-item-custom">2x1 / Especiales</a>
                        </div>
                    </div>
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-local" class="dropdown-checkbox">
                        <label for="dropdown-local" class="dropdown-toggle-custom">Local <i class="fas fa-chevron-down"></i></label>
                        <div class="dropdown-menu-custom">
                            <a href="<?php echo buildFilterUrl('store', 'all'); ?>" class="dropdown-item-custom">Todos</a>
                            <?php foreach ($allStores as $store): ?>
                                <a href="<?php echo buildFilterUrl('store', $store['name']); ?>" class="dropdown-item-custom"><?php echo htmlspecialchars($store['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="dropdown-custom">
                        <input type="checkbox" id="dropdown-client-category" class="dropdown-checkbox">
                        <label for="dropdown-client-category" class="dropdown-toggle-custom">Categoría <i class="fas fa-chevron-down"></i></label>
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
                        <?php foreach ($activeFilters as $key => $label): ?>
                            <div class="filter-chip">
                                <?php echo htmlspecialchars($label); ?>
                                <a href="?<?php $params = $_GET; unset($params[$key]); echo http_build_query($params); ?>" class="ms-2 text-decoration-none"><i class="fas fa-times-circle"></i></a>
                            </div>
                        <?php endforeach; ?>
                        <a href="Promotions.php" class="btn-clear-all ms-2">Limpiar todos</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="promotions-grid">
                <?php if (count($filteredPromotions) > 0): ?>
                    <?php foreach ($filteredPromotions as $promo): ?>
                        <?php 
                            $promoCategory = strtolower($promo['client_category']);
                            $promoWeight = $levelWeights[$promoCategory] ?? 1;
                            
                            // LOGICA DINÁMICA
                            $isLocked = ($userWeight < $promoWeight);
                            $isAlreadyUsed = in_array($promo['id'], $myUsedPromoIds);
                            $isExpired = ($promo['date_until'] < $today);
                        ?>
                        <div class="promo-card <?php echo ($isLocked || $isAlreadyUsed) ? 'promo-locked' : ''; ?>">
                            <div class="promo-image-container">
                                <?php 
                                    $badge_color = '#000000';
                                    $discount_val = intval(preg_replace('/[^0-9]/', '', $promo['discount_label']));
                                    if($discount_val >= 30) $badge_color = '#dc2626';
                                    elseif($discount_val > 0) $badge_color = '#2563eb';
                                ?>

                                <?php if ($isAlreadyUsed): ?>
                                    <div class="promo-lock-overlay" style="background: rgba(25, 135, 84, 0.75);">
                                        <i class="fas fa-check-circle mb-2"></i>
                                        <span class="small fw-bold">YA UTILIZADA</span>
                                    </div>
                                <?php elseif ($isLocked): ?>
                                    <div class="promo-lock-overlay">
                                        <i class="fas fa-lock mb-2"></i>
                                        <span class="small fw-bold">Nivel <?php echo htmlspecialchars($promo['client_category']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <span class="promo-badge" style="background-color: <?php echo $badge_color; ?>;">
                                    <?php echo htmlspecialchars($promo['discount_label']); ?>
                                </span>

                                <span class="promo-client-category <?php echo strtolower($promo['client_category']); ?>">
                                    <?php echo htmlspecialchars($promo['client_category']); ?>
                                </span>
                                
                                <img src="<?php echo htmlspecialchars($promo['image']); ?>" class="promo-image">
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
                                    <?php $requestStatus = getPromotionRequestStatus($user['cod'] ?? $user['id'], $promo['id']); ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="id_promotion" value="<?php echo $promo['id']; ?>">
                                        
                                        <?php if ($isAlreadyUsed): ?>
                                            <button type="button" class="promo-request-btn" style="background-color: #198754; border-color: #198754; color: white;" disabled>
                                                Utilizada con éxito
                                            </button>
                                        <?php elseif ($requestStatus === 'pending'): ?>
                                            <button type="button" class="promo-request-btn" style="background-color: #ffc107; border-color: #ffc107; color: #212529;" disabled>
                                                <i class="fas fa-clock me-1"></i>Pendiente de aprobación
                                            </button>
                                        <?php elseif ($requestStatus === 'active'): ?>
                                            <button type="button" class="promo-request-btn btn-obtained" disabled>Promoción Obtenida</button>
                                        <?php elseif ($requestStatus === 'rejected'): ?>
                                            <button type="button" class="promo-request-btn" style="background-color: #dc3545; border-color: #dc3545; color: white;" disabled>
                                                <i class="fas fa-times-circle me-1"></i>Solicitud Rechazada
                                            </button>
                                        <?php elseif ($isLocked): ?>
                                            <button type="button" class="promo-request-btn btn-locked" disabled>Bloqueado (Nivel <?= ucfirst($promo['client_category']) ?>)</button>
                                        <?php else: ?>
                                            <button type="submit" name="btnRequestPromo" class="promo-request-btn">Solicitar Promoción</button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if($user && $user['type'] === 'owner'): ?>
            <div class="container-custom d-flex justify-content-center my-5">
                <button type="button" class="btn btn-warning admin-fab" data-bs-toggle="modal" data-bs-target="#createPromoModal">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Promo
                </button>
            </div>
            <div class="modal fade" id="createPromoModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0 rounded-4">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold">Sugerir Nueva Promoción</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="" method="POST">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">¿Para qué local es la promo?</label>
                                        <select name="id_store" class="form-select border-primary" required>
                                            <option value="" disabled selected>Selecciona un local...</option>
                                            <?php foreach ($myStores as $s): ?>
                                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (Local <?= $s['local_number'] ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Título de la Promo</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Fecha Inicio</label>
                                        <input type="date" name="date_from" class="form-control" value="<?= $today ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Fecha Fin</label>
                                        <input type="date" name="date_until" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">% Descuento</label>
                                        <input type="number" name="discount" class="form-control" required>
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
                                        <input type="url" name="image" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Categoría Cliente</label>
                                        <select name="client_category" class="form-select">
                                            <option value="Inicial">Inicial</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Premium">Premium</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Días de validez</label>
                                        <input type="text" name="week_days" class="form-control" placeholder="Lunes a Jueves">
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" name="btnCreatePromo" class="btn btn-dark px-4 rounded-pill">Enviar para Revisión</button>
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