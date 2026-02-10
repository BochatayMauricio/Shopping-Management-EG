<?php
require_once __DIR__. '/../config/config.php';

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

function createPromotion($data) {
    global $CONNECTION;

    // El discount_label lo generamos automáticamente para mantener tu estética
    $discount_label = "-" . $data['discount'] . "% OFF";

    $query = "INSERT INTO promotions 
              (title, description, image, date_from, date_until, client_category, week_days, status, discount, price, original_price, id_store) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    
    // La descripción por ahora será igual al título
    $description = $data['title']; 

    mysqli_stmt_bind_param($stmt, "sssssssdddi", 
        $data['title'],
        $description,
        $data['image'],
        $data['date_from'],
        $data['date_until'],
        $data['client_category'],
        $data['week_days'],
        $data['discount'],
        $data['price'],
        $data['original_price'],
        $data['id_store']
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
                     cp.status as request_status, 
                     cp.date_from as obtained_at, 
                     s.name as store_name, 
                     s.logo as store_logo, 
                     s.color as store_color
              FROM user_promotions cp
              JOIN promotions p ON cp.id_promotion = p.id
              JOIN stores s ON p.id_store = s.id
              WHERE cp.id_client = ? AND cp.status = 'active'
              ORDER BY is_expired ASC, p.date_until ASC"; // Primero las vigentes, luego las vencidas
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $clientId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}