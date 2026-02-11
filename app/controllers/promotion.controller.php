<?php

require_once __DIR__. '/../Services/stores.services.php';

// --- LÓGICA DE CREACIÓN PARA OWNERS CORREGIDA ---
if (isset($_POST['btnCreatePromo'])) {
    // 1. Evitamos el Warning "id" enviando lo que el controlador espera
    $_POST['id'] = $_POST['id_store']; 

    // 2. Limpiamos el descuento para evitar el "Out of range" (solo números)
    $cleanDiscount = intval(preg_replace('/[^0-9]/', '', $_POST['discount']));

    // 3. Capturamos el ID del local seleccionado en el select del modal
    $selectedStoreId = isset($_POST['id_store']) ? intval($_POST['id_store']) : 0;

    if ($selectedStoreId > 0) {
        $data = [
            'title'           => $_POST['title'],
            'description'     => $_POST['title'], // Usamos el título como descripción
            'image'           => $_POST['image'],
            'date_from'       => $_POST['date_from'],
            'date_until'      => $_POST['date_until'],
            'client_category' => $_POST['client_category'],
            'week_days'       => $_POST['week_days'] ?: 'Todos los días',
            'discount'        => $cleanDiscount,
            'price'           => $_POST['price'],
            'original_price'  => !empty($_POST['original_price']) ? $_POST['original_price'] : 0,
            'id_store'        => $selectedStoreId // ID real del local elegido en el modal
        ];

        if (createPromotion($data)) {
            header("Location: Promotions.php?status=submitted");
            exit(); // Cortamos para que el controlador no duplique el proceso
        }
    }
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