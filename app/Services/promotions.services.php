<?php
require_once __DIR__. '/../config/config.php';

function getPromotionsWithStoreData() {
    global $CONNECTION;
    
    $query = "SELECT 
                p.id, 
                p.title, 
                p.image, 
                p.description,
                CONCAT('-', CAST(p.discount AS UNSIGNED), '% OFF') as discount_label,
                DATE_FORMAT(p.date_until, '%d/%m') as valid_until,
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
};

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
