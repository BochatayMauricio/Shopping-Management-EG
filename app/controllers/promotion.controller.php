<?php
// Aseguramos que la sesión esté iniciada para las validaciones
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carga de dependencias
require_once __DIR__ . '/../Services/stores.services.php';
require_once __DIR__ . '/../Services/promotions.services.php';
require_once __DIR__ . '/../Services/alert.service.php';
require_once __DIR__ . '/../Services/user.services.php';
require_once __DIR__ . '/../Services/clientLevel.service.php';

// Bloque central de peticiones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtenemos el usuario de la sesión para proteger las rutas
    $currentUser = $_SESSION['user'] ?? null;

    if (isset($_POST['btnCreatePromo']) && $currentUser && $currentUser['type'] === 'owner') {

        $cleanDiscount = intval(preg_replace('/[^0-9]/', '', $_POST['discount'] ?? '0'));
        $selectedStoreId = isset($_POST['id_store']) ? intval($_POST['id_store']) : 0;
        $originalPrice = isset($_POST['original_price']) ? floatval($_POST['original_price']) : 0;
        $safeDiscount = max(0, min(100, $cleanDiscount));
        $calculatedPrice = max(0, $originalPrice * (1 - ($safeDiscount / 100)));

        if ($selectedStoreId > 0) {
            $data = [
                'title'           => trim($_POST['title']),
                'description'     => trim($_POST['title']),
                'image'           => trim($_POST['image']),
                'date_from'       => $_POST['date_from'],
                'date_until'      => $_POST['date_until'],
                'client_category' => $_POST['client_category'],
                'week_days'       => !empty($_POST['week_days']) ? $_POST['week_days'] : 'Todos los días',
                'discount'        => $safeDiscount,
                'price'           => $calculatedPrice,
                'original_price'  => $originalPrice,
                'id_store'        => $selectedStoreId
            ];

            if (createPromotion($data)) {
                AlertService::success('Promoción creada y enviada a revisión correctamente.');
            } else {
                AlertService::error('Hubo un error al intentar crear la promoción.');
            }
            header("Location: Promotions.php");
            exit();
        }
    }

    if (isset($_POST['btnDeletePromo']) && $currentUser && $currentUser['type'] === 'owner') {
        $promoId = $_POST['promo_id'];

        if (deletePromotion($promoId)) {
            AlertService::success('La promoción ha sido dada de baja correctamente.');
        } else {
            AlertService::error('Error al intentar cancelar la promoción.');
        }

        header("Location: Promotions.php");
        exit();
    }

    if (isset($_POST['btnRequestPromo'])) {

        // Protección contra manipulación
        if (!$currentUser || $currentUser['type'] !== 'client') {
            header("Location: Promotions.php?request=denied");
            exit();
        }

        $clientId = $currentUser['cod'] ?? $currentUser['id'];
        $promoId = $_POST['id_promotion'];

        $allPromos = getPromotionsWithStoreData();
        $currentPromo = null;
        foreach ($allPromos as $p) {
            if ($p['id'] == $promoId) {
                $currentPromo = $p;
                break;
            }
        }

        if ($currentPromo) {
            $progress = getClientLevelProgress($clientId);
            $dynamicUserLevel = ClientLevel::calculateLevel($progress['used']);
            $promoLevel = strtolower(trim($currentPromo['client_category']));

            if (!ClientLevel::canAccess($dynamicUserLevel, $promoLevel)) {
                header("Location: Promotions.php?request=level_low");
                exit();
            }
        }

        // Procesar la solicitud
        $result = requestPromotion($clientId, $promoId);

        if ($result === true) {
            header("Location: Promotions.php?request=success");
        } elseif ($result === "already_requested") {
            header("Location: Promotions.php?request=duplicate");
        } else {
            header("Location: Promotions.php?request=error");
        }
        exit();
    }
}
