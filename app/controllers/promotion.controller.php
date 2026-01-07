<?php

if (isset($_POST['btnCreatePromo'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    // 1. Obtener el local del Owner (Asumimos que el ID del usuario está en la sesión)
    $ownerId = $_SESSION['user']['id'] ?? $_SESSION['user']['cod']; 
    $store = getStoreByOwner($ownerId);

    if (!$store) {
        // Si no hay local, no podemos crear promo
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=no_store");
        exit();
    }

    // 2. Mapear datos para el servicio
    $data = [
        'title'           => $_POST['title'],
        'date_from'       => $_POST['date_from'],
        'date_until'      => $_POST['date_until'],
        'discount'        => $_POST['discount'],
        'price'           => $_POST['price'],
        'original_price'  => $_POST['original_price'] ?: 0,
        'image'           => $_POST['image'],
        'week_days'       => $_POST['week_days'] ?: 'Todos los días',
        'client_category' => $_POST['client_category'],
        'id_store'        => $store['id']
    ];

    if (createPromotion($data)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=pending");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=error");
    }
    exit();
}