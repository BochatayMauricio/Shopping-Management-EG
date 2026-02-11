<?php
require_once __DIR__. '/../config/config.php';

function getAllPromotions() {
    global $CONNECTION;
    // Quitamos el filtro de status para traer el histórico completo
    $query = "SELECT status, (SELECT COUNT(*) FROM user_promotions up WHERE up.id_promotion = p.id) as use_count 
              FROM promotions p";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getPromotionsWithStoreData() {
    global $CONNECTION;
    
    $query = "SELECT 
                p.id, 
                p.title, 
                p.image, 
                p.description,
                -- Traemos la fecha real para las comparaciones de PHP (Línea clave)
                p.date_until, 
                -- Traemos la fecha formateada para la vista
                DATE_FORMAT(p.date_until, '%d/%m') as valid_until,
                CONCAT('-', CAST(p.discount AS UNSIGNED), '% OFF') as discount_label,
                p.price, 
                p.original_price, 
                s.name as store_name, 
                s.logo as store_logo, 
                s.color as store_color,
                s.local_number,
                s.category as store_category,
                p.client_category,
                p.status
              FROM promotions p
              JOIN stores s ON p.id_store = s.id
              WHERE p.status = 'active'
              ORDER BY p.created_at DESC";

    $result = mysqli_query($CONNECTION, $query);
    
    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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
 */
function requestPromotion($clientId, $promoId) {
    global $CONNECTION;
    
    // 1. Evitar solicitudes duplicadas
    if (hasClientRequestedPromo($clientId, $promoId)) {
        return "already_requested";
    }

    // 2. Insertar la nueva relación
    $query = "INSERT INTO user_promotions (id_client, id_promotion, date_from, status) VALUES (?, ?, NOW(), 'active')";
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

function getClientPromotions($clientId) {
    global $CONNECTION;
    
    $query = "SELECT p.*, 
                     CONCAT('-', CAST(p.discount AS UNSIGNED), '% OFF') as discount_label,
                     DATE_FORMAT(p.date_until, '%d/%m/%Y') as valid_until,
                     -- Comparamos la fecha actual con la de vencimiento
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
              ORDER BY is_expired ASC, p.date_until ASC"; // Primero las vigentes, luego las vencidas
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $clientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Obtiene todas las promociones con estado 'pending'
 */
function getPendingPromotions() {
    global $CONNECTION;

    // Asegúrate de que las columnas 'status', 'id_store' (en p) e 'id' (en s) existan con esos nombres exactos
    $query = "SELECT 
                p.*, 
                s.name as store_name, 
                s.color as store_color 
              FROM promotions p 
              JOIN stores s ON p.id_store = s.id 
              WHERE p.status = 'pending' 
              ORDER BY p.id DESC"; // Cambié created_at por id por si esa columna no existe

    $result = mysqli_query($CONNECTION, $query);

    if (!$result) {
        // Esto te dirá el error exacto de SQL en la consola del navegador o pantalla
        error_log("Error en getPendingPromotions: " . mysqli_error($CONNECTION));
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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