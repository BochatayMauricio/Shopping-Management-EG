<?php
session_start();
require_once __DIR__. '/../Config/config.php';
require_once __DIR__. '/../models/User.php';
include_once __DIR__ . '/alert.service.php';

/**
 * Función de registro actualizada con Email y Sentencias Preparadas
 * @return bool|string
 */
function registerUser($userName, $email, $password, $type = 'client') {
    global $CONNECTION;
    
    if (!$CONNECTION) {
        AlertService::error("Error: No hay conexión a la base de datos");
        return false;
    }

    $userName = strtolower(trim($userName));
    $email = strtolower(trim($email));

    // 1. Validar si el EMAIL ya existe
    $checkEmail = $CONNECTION->prepare("SELECT cod FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $resEmail = $checkEmail->get_result();
    
    if ($resEmail->num_rows > 0) {
        return "email_exists";
    }

    // 2. Hashear la contraseña
    $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insertar el nuevo usuario
    $stmt = $CONNECTION->prepare("INSERT INTO users (name, email, password, type, category) VALUES (?, ?, ?, ?, 'inicial')");
    $stmt->bind_param("ssss", $userName, $email, $passwordHashed, $type);
    
    if (!$stmt->execute()) {
        AlertService::error("Error al registrar: " . $CONNECTION->error);
        return false;
    }

    // 4. Obtener datos del usuario recién creado
    $codUser = $CONNECTION->insert_id;
    $getUser = $CONNECTION->prepare("SELECT * FROM users WHERE cod = ?");
    $getUser->bind_param("i", $codUser);
    $getUser->execute();
    $result = $getUser->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }

    $userData = $result->fetch_assoc();
    $user = User::fromArray($userData);

    // 5. Iniciar la sesión automáticamente
    $_SESSION['user'] = $userData; // Guardamos array para compatibilidad
    
    mysqli_close($CONNECTION);
    
    return true;
}

function getClientsStatsByLevel() {
    global $CONNECTION;
    $query = "SELECT category as level, COUNT(*) as total FROM users WHERE type = 'client' GROUP BY category";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function checkAndUpgradeLevel($userId) {
    global $CONNECTION;

    // 1. Contar promos usadas
    $qCount = "SELECT COUNT(*) as total FROM user_promotions WHERE id_user = ? AND status = 'used'";
    $stmt = mysqli_prepare($CONNECTION, $qCount);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $usedCount = mysqli_stmt_get_result($stmt)->fetch_assoc()['total'];

    // 2. Determinar nuevo nivel
    $newLevel = 'inicial';
    if ($usedCount >= 5) {
        $newLevel = 'premium';
    } elseif ($usedCount >= 3) {
        $newLevel = 'medium';
    }

    // 3. Actualizar la tabla de usuarios
    $qUpdate = "UPDATE users SET category = ? WHERE id = ? AND category != 'premium'"; // No bajamos de nivel si ya es premium
    $stmtUp = mysqli_prepare($CONNECTION, $qUpdate);
    mysqli_stmt_bind_param($stmtUp, "si", $newLevel, $userId);
    mysqli_stmt_execute($stmtUp);

    return $newLevel;
}

function getClientLevelProgress($userId) {
    global $CONNECTION;
    
    // Contamos las promociones realmente usadas (estado 'used')
    $query = "SELECT COUNT(*) as total FROM user_promotions WHERE id_client = ? AND status = 'used'";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['total'];

    $progress = [
        'used' => $count,
        'next_level' => '',
        'goal' => 0,
        'percentage' => 0,
        'missing' => 0,
        'is_premium' => false
    ];

    // Regla: 3 para Medium, 5 para Premium
    if ($count < 3) {
        $progress['next_level'] = 'Medium';
        $progress['goal'] = 3;
    } elseif ($count < 5) {
        $progress['next_level'] = 'Premium';
        $progress['goal'] = 5;
    } else {
        $progress['is_premium'] = true;
    }

    if (!$progress['is_premium']) {
        $progress['percentage'] = ($count / $progress['goal']) * 100;
        $progress['missing'] = $progress['goal'] - $count;
    }

    return $progress;
}