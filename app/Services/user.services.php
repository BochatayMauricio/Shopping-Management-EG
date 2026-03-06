<?php
// Sesión manejada por init.php o login.controller.php
require_once __DIR__. '/../Config/config.php';
require_once __DIR__. '/../models/User.php';
include_once __DIR__ . '/alert.service.php';
include_once __DIR__ . '/clientLevel.service.php';

/**
 * Función Base: Solo inserta en la DB y devuelve el array del usuario
 */
function insertUserDatabase($userName, $email, $password, $type = 'client') {
    global $CONNECTION;
    
    $userName = strtolower(trim($userName));
    $email = strtolower(trim($email));

    // 1. Validar duplicado de username
    $checkUsername = $CONNECTION->prepare("SELECT cod FROM users WHERE name = ?");
    $checkUsername->bind_param("s", $userName);
    $checkUsername->execute();
    if ($checkUsername->get_result()->num_rows > 0) return "username_exists";

    // 2. Validar duplicado de email
    $checkEmail = $CONNECTION->prepare("SELECT cod FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    if ($checkEmail->get_result()->num_rows > 0) return "email_exists";

    // 2. Insertar
    $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
    $initialLevel = ClientLevel::INICIAL;
    $stmt = $CONNECTION->prepare("INSERT INTO users (name, email, password, type, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $userName, $email, $passwordHashed, $type, $initialLevel);
    
    if (!$stmt->execute()) return false;

    // 3. Retornar datos del nuevo usuario
    $codUser = $CONNECTION->insert_id;
    $getUser = $CONNECTION->prepare("SELECT * FROM users WHERE cod = ?");
    $getUser->bind_param("i", $codUser);
    $getUser->execute();
    return $getUser->get_result()->fetch_assoc();
}

/**
 * Función Pública: La que usarás en tus formularios
 */
function registerUser($userName, $email, $password, $type = 'client') {
    $userData = insertUserDatabase($userName, $email, $password, $type);

    if ($userData === "username_exists") return "username_exists";
    if ($userData === "email_exists") return "email_exists";
    if (!$userData) return false;

    // LÓGICA DE SESIÓN: Solo si es cliente se loguea automáticamente
    if ($type === 'client') {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = $userData;
    }

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

    // 2. Determinar nuevo nivel usando ClientLevel
    $newLevel = ClientLevel::calculateLevel($usedCount);

    // 3. Actualizar la tabla de usuarios (no bajamos de nivel si ya es premium)
    $premiumLevel = ClientLevel::PREMIUM;
    $qUpdate = "UPDATE users SET category = ? WHERE id = ? AND category != ?";
    $stmtUp = mysqli_prepare($CONNECTION, $qUpdate);
    mysqli_stmt_bind_param($stmtUp, "sis", $newLevel, $userId, $premiumLevel);
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

    // Usar ClientLevel para determinar progreso
    $levelInfo = ClientLevel::getNextLevelInfo($count);
    
    if ($levelInfo['is_max']) {
        $progress['is_premium'] = true;
    } else {
        $progress['next_level'] = ClientLevel::getLabel($levelInfo['next_level']);
        $progress['missing'] = $levelInfo['remaining'];
        
        // Calcular goal y porcentaje
        if ($count < ClientLevel::THRESHOLD_MEDIUM) {
            $progress['goal'] = ClientLevel::THRESHOLD_MEDIUM;
        } else {
            $progress['goal'] = ClientLevel::THRESHOLD_PREMIUM;
        }
        $progress['percentage'] = ($count / $progress['goal']) * 100;
    }

    return $progress;
}

/**
 * Actualiza la contraseña de un usuario verificando primero su contraseña actual.
 * * @param int $userId El ID (cod) del usuario.
 * @param string $currentPassword La contraseña actual ingresada en el formulario.
 * @param string $newPassword La nueva contraseña elegida.
 * @return bool|string true en éxito, "incorrect_password" si falla la validación, false en error.
 */
function updateUserPassword($userId, $currentPassword, $newPassword) {
    global $CONNECTION;
    
    if (!$CONNECTION) return false;

    // 1. Obtener el hash de la contraseña actual desde la base de datos
    // Usamos 'cod' ya que es el identificador primario que vimos en tu tabla users
    $stmt = $CONNECTION->prepare("SELECT password FROM users WHERE cod = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false; // El usuario no existe
    }

    $userData = $result->fetch_assoc();
    $savedHash = $userData['password'];

    // 2. Verificar que la contraseña actual ingresada sea la correcta
    if (!password_verify($currentPassword, $savedHash)) {
        return "incorrect_password";
    }

    // 3. Generar el hash para la nueva contraseña
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

    // 4. Actualizar la contraseña en la base de datos
    $updateStmt = $CONNECTION->prepare("UPDATE users SET password = ? WHERE cod = ?");
    $updateStmt->bind_param("si", $newHash, $userId);
    
    if ($updateStmt->execute()) {
        // Opcional: Si guardas el hash en la variable de sesión, actualízalo para mantener consistencia
        if (isset($_SESSION['user']['password'])) {
            $_SESSION['user']['password'] = $newHash;
        }
        return true;
    }

    return false;
}