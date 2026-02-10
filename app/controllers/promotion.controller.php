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

if (isset($_POST['btnRequestPromo'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $clientId = $_SESSION['user']['cod'] ?? $_SESSION['user']['id'];
    $promoId = $_POST['id_promotion'];
    $userCategory = $_SESSION['user']['category'] ?? 'inicial';

    // 1. Validación de seguridad básica: solo clientes
    if ($_SESSION['user']['type'] !== 'client') {
        header("Location: " . $_SERVER['PHP_SELF'] . "?request=denied");
        exit();
    }

    // 2. Validación de Seguridad por Niveles (Protección contra bypass de HTML)
    $levelWeights = ['inicial' => 1, 'medium' => 2, 'premium' => 3];
    
    // Necesitamos traer la info de la promo para saber qué nivel requiere
    // Asumimos que tienes una función getPromotionById en tus servicios
    $allPromos = getPromotionsWithStoreData();
    $currentPromo = null;
    foreach($allPromos as $p) {
        if($p['id'] == $promoId) {
            $currentPromo = $p;
            break;
        }
    }

    if ($currentPromo) {
        $userWeight = $levelWeights[strtolower($userCategory)] ?? 1;
        $promoWeight = $levelWeights[strtolower($currentPromo['client_category'])] ?? 1;

        if ($userWeight < $promoWeight) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?request=level_low");
            exit();
        }
    }

    // 3. Procesar la solicitud
    $result = requestPromotion($clientId, $promoId);

    if ($result === true) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?request=success");
    } elseif ($result === "already_requested") {
        header("Location: " . $_SERVER['PHP_SELF'] . "?request=duplicate");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?request=error");
    }
    exit();
}