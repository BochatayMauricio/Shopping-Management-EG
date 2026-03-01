<?php
require_once __DIR__. '/../config/config.php';
require_once __DIR__. '/../models/Promotion.php';

function getAllPromotions() {
    global $CONNECTION;
    $query = "SELECT status, (SELECT COUNT(*) FROM user_promotions up WHERE up.id_promotion = p.id) as use_count 
              FROM promotions p";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Obtiene promociones con datos de tienda
 * @return Promotion[]
 */
function getPromotionsWithStoreData() {
    global $CONNECTION;
    
    $query = "SELECT 
                p.id, 
                p.title, 
                p.image, 
                p.description,
                p.date_until, 
                DATE_FORMAT(p.date_until, '%d/%m') as valid_until,
                CONCAT('-', CAST(p.discount AS UNSIGNED), '% OFF') as discount_label,
                p.price, 
                p.original_price, 
                p.date_from,
                p.client_category,
                p.week_days,
                p.status,
                p.discount,
                p.id_store,
                s.name as store_name, 
                s.logo as store_logo, 
                s.color as store_color,
                s.local_number,
                s.category as store_category
              FROM promotions p
              JOIN stores s ON p.id_store = s.id
              WHERE p.status = 'active'
              ORDER BY p.created_at DESC";

    $result = mysqli_query($CONNECTION, $query);
    
    if (!$result) {
        return [];
    }

    $promotions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = Promotion::fromArray($row);
    }
    return $promotions;
}

// En app/Services/promotions.services.php
function createPromotion($data) {
    global $CONNECTION;

    $query = "INSERT INTO promotions 
              (title, description, image, date_from, date_until, client_category, week_days, status, discount, price, original_price, id_store) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    
    // Limpiamos el descuento por si llega con el signo %
    $discount = intval(preg_replace('/[^0-9]/', '', $data['discount']));
    $price = floatval($data['price']);
    $originalPrice = floatval($data['original_price']);
    $storeId = intval($data['id_store']);

    mysqli_stmt_bind_param($stmt, "sssssssdddi", 
        $data['title'],
        $data['title'], // description
        $data['image'],
        $data['date_from'],
        $data['date_until'],
        $data['client_category'],
        $data['week_days'],
        $discount,      // d (double/numeric)
        $price,         // d
        $originalPrice, // d
        $storeId        // i (integer)
    );
    
    return mysqli_stmt_execute($stmt);
}

function hasClientRequestedPromo($clientId, $promoId) {
    global $CONNECTION;
    $query = "SELECT COUNT(*) as total FROM user_promotions WHERE id_client = ? AND id_promotion = ? AND status != 'canceled'";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "ii", $clientId, $promoId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] > 0;
}

/**
 * Registra la solicitud de la promoción en la tabla relacional
 * Estado inicial: 'pending' - El dueño del local debe aprobarla
 */
function requestPromotion($clientId, $promoId) {
    global $CONNECTION;
    
    // 1. Evitar solicitudes duplicadas
    if (hasClientRequestedPromo($clientId, $promoId)) {
        return "already_requested";
    }

    // 2. Insertar la nueva relación con estado pendiente
    $query = "INSERT INTO user_promotions (id_client, id_promotion, date_from, status) VALUES (?, ?, NOW(), 'pending')";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "ii", $clientId, $promoId);
    
    if (mysqli_stmt_execute($stmt)) {
        return true;
    }
    return false;
}

function getPromotionRequestStatus($clientId, $promoId) {
    global $CONNECTION;
    $query = "SELECT status FROM user_promotions WHERE id_client = ? AND id_promotion = ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "ii", $clientId, $promoId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['status']; // Retornará 'active' (obtenida) o 'used' (usada)
    }
    return false; // No solicitada aún
}

/**
 * Obtiene las promociones de un cliente
 * @return Promotion[]
 */
function getClientPromotions($clientId) {
    global $CONNECTION;
    
    $query = "SELECT p.*, 
                     CONCAT('-', CAST(p.discount AS UNSIGNED), '% OFF') as discount_label,
                     DATE_FORMAT(p.date_until, '%d/%m/%Y') as valid_until,
                     IF(p.date_until < CURDATE(), 1, 0) as is_expired,
                     cp.status, 
                     cp.date_from as obtained_at, 
                     s.name as store_name, 
                     s.logo as store_logo, 
                     s.color as store_color
              FROM user_promotions cp
              JOIN promotions p ON cp.id_promotion = p.id
              JOIN stores s ON p.id_store = s.id
              WHERE cp.id_client = ?
              ORDER BY is_expired ASC, p.date_until ASC";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $clientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $promotions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = Promotion::fromArray($row);
    }
    return $promotions;
}

/**
 * Obtiene todas las promociones con estado 'pending'
 * @return Promotion[]
 */
function getPendingPromotions() {
    global $CONNECTION;

    $query = "SELECT 
                p.*, 
                s.name as store_name, 
                s.color as store_color 
              FROM promotions p 
              JOIN stores s ON p.id_store = s.id 
              WHERE p.status = 'pending' 
              ORDER BY p.id DESC";

    $result = mysqli_query($CONNECTION, $query);

    if (!$result) {
        error_log("Error en getPendingPromotions: " . mysqli_error($CONNECTION));
        return [];
    }

    $promotions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = Promotion::fromArray($row);
    }
    return $promotions;
}

/**
 * Cambia el estado de una promoción
 */
function updatePromotionStatus($promoId, $newStatus) {
    global $CONNECTION;
    $query = "UPDATE promotions SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $promoId);
    return mysqli_stmt_execute($stmt);
}

/**
 * Obtiene el conteo de promociones por estado (Total, Rechazadas, etc.)
 */
function getPromotionsStats() {
    global $CONNECTION;
    // Contamos cuantas hay de cada estado en una sola consulta
    $query = "SELECT status, COUNT(*) as cantidad FROM promotions GROUP BY status";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Cuenta cuántas promociones han sido marcadas como 'used' por los clientes
 */
function getTotalUsedPromotions() {
    global $CONNECTION;
    $query = "SELECT COUNT(*) as total FROM user_promotions WHERE status = 'used'";
    $result = mysqli_query($CONNECTION, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function redeemPromotionCode($fullCode) {
    global $CONNECTION;

    // 1. Limpieza del código (Formato SR-[ID_PROMO][ID_CLIENT])
    $cleanCode = str_replace('SR-', '', $fullCode);
    
    // 2. Buscamos el registro activo comparando la concatenación de IDs
    $query = "SELECT * FROM user_promotions 
              WHERE status = 'active' 
              AND CONCAT(id_promotion, id_client) = ?";
              
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "s", $cleanCode);
    mysqli_stmt_execute($stmt);
    $request = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$request) {
        return ["success" => false, "message" => "Código inválido, expirado o ya utilizado."];
    }

    $clientId = $request['id_client'];
    $promoId  = $request['id_promotion'];

    // 3. Marcar como usada usando la clave compuesta (id_client + id_promotion)
    $updateQ = "UPDATE user_promotions 
                SET status = 'used' 
                WHERE id_client = ? AND id_promotion = ?";
    $stmtUp = mysqli_prepare($CONNECTION, $updateQ);
    mysqli_stmt_bind_param($stmtUp, "ii", $clientId, $promoId);
    mysqli_stmt_execute($stmtUp);

    // 4. Contar promos totales USADAS por el cliente para calcular nivel
    $countQ = "SELECT COUNT(*) as total FROM user_promotions 
               WHERE id_client = ? AND status = 'used'";
    $stmtCount = mysqli_prepare($CONNECTION, $countQ);
    mysqli_stmt_bind_param($stmtCount, "i", $clientId);
    mysqli_stmt_execute($stmtCount);
    $totalUsed = mysqli_stmt_get_result($stmtCount)->fetch_assoc()['total'];

    // 5. Lógica de Categorías (3 -> Medium, 5 -> Premium)
    $newCategory = 'Inicial';
    if ($totalUsed >= 5) {
        $newCategory = 'Premium';
    } elseif ($totalUsed >= 3) {
        $newCategory = 'Medium';
    }

    // 6. Actualizar la categoría en la tabla de usuarios
    $userUpdateQ = "UPDATE users SET category = ? WHERE cod = ?";
    $stmtUser = mysqli_prepare($CONNECTION, $userUpdateQ);
    mysqli_stmt_bind_param($stmtUser, "si", $newCategory, $clientId);
    mysqli_stmt_execute($stmtUser);

    return [
        "success" => true, 
        "message" => "¡Canje exitoso! El cliente (ID: $clientId) ahora tiene $totalUsed promociones usadas y es nivel $newCategory."
    ];
}

/**
 * Obtiene las solicitudes de clientes pendientes para las tiendas de un owner
 */
function getPendingClientRequests($ownerId) {
    global $CONNECTION;
    
    $query = "SELECT 
                up.id_client,
                up.id_promotion,
                up.date_from as request_date,
                up.status,
                u.name as client_name,
                u.email as client_email,
                u.category as client_category,
                p.title as promo_title,
                p.discount,
                p.price,
                p.original_price,
                p.image as promo_image,
                s.name as store_name,
                s.color as store_color,
                s.id as store_id
              FROM user_promotions up
              JOIN users u ON up.id_client = u.cod
              JOIN promotions p ON up.id_promotion = p.id
              JOIN stores s ON p.id_store = s.id
              WHERE up.status = 'pending' AND s.id_owner = ?
              ORDER BY up.date_from DESC";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $ownerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        error_log("Error en getPendingClientRequests: " . mysqli_error($CONNECTION));
        return [];
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Aprueba o rechaza la solicitud de un cliente para una promoción
 */
function updateClientRequestStatus($clientId, $promoId, $newStatus) {
    global $CONNECTION;
    
    $query = "UPDATE user_promotions SET status = ? WHERE id_client = ? AND id_promotion = ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "sii", $newStatus, $clientId, $promoId);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Cuenta las solicitudes pendientes para las tiendas de un owner
 */
function countPendingClientRequests($ownerId) {
    global $CONNECTION;
    
    $query = "SELECT COUNT(*) as total
              FROM user_promotions up
              JOIN promotions p ON up.id_promotion = p.id
              JOIN stores s ON p.id_store = s.id
              WHERE up.status = 'pending' AND s.id_owner = ?";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $ownerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total'] ?? 0;
}