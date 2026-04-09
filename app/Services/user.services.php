<?php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../models/User.php';
include_once __DIR__ . '/alert.service.php';
include_once __DIR__ . '/clientLevel.service.php';
include_once __DIR__ . '/email.service.php';


function insertUserDatabase($userName, $email, $password, $type = 'client')
{
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
    $token = bin2hex(random_bytes(32));
    $isVerified = 0;
    $stmt = $CONNECTION->prepare("INSERT INTO users (name, email, password, type, category,verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $userName, $email, $passwordHashed, $type, $initialLevel, $token, $isVerified);

    if (!$stmt->execute()) return false;

    // 3. Retornar datos del nuevo usuario
    $codUser = $CONNECTION->insert_id;
    $getUser = $CONNECTION->prepare("SELECT * FROM users WHERE cod = ?");
    $getUser->bind_param("i", $codUser);
    $getUser->execute();
    return $getUser->get_result()->fetch_assoc();
}

function registerUser($userName, $email, $password, $type = 'client')
{
    $userData = insertUserDatabase($userName, $email, $password, $type);

    if ($userData === "username_exists") return "username_exists";
    if ($userData === "email_exists") return "email_exists";
    if (!$userData) return false;

    EmailService::sendVerificationEmail($userData['email'], $userData['verification_token']);

    return true;
}

function getClientsStatsByLevel()
{
    global $CONNECTION;
    $query = "SELECT category as level, COUNT(*) as total FROM users WHERE type = 'client' GROUP BY category";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function checkAndUpgradeLevel($userId)
{
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

function getClientLevelProgress($userId)
{
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

function updateUserPassword($userId, $currentPassword, $newPassword)
{
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

function verifyUserByToken($token)
{
    global $CONNECTION;

    if (!$CONNECTION) return false;

    // 1. Buscar usuario por token
    $stmt = $CONNECTION->prepare("SELECT cod FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return "El token es inválido o la cuenta ya ha sido verificada.";
    }

    $userData = $result->fetch_assoc();
    $userId = $userData['cod'];

    // 2. Marcar como verificado y limpiar el token
    $updateStmt = $CONNECTION->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE cod = ?");
    $updateStmt->bind_param("i", $userId);

    if ($updateStmt->execute()) {
        return true;
    }

    return "Error al verificar la cuenta. Intente más tarde.";
}

/**
 * Obtiene un usuario por su correo electrónico
 */
function getUserByEmail($email)
{
    global $CONNECTION;
    $stmt = $CONNECTION->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Guarda el token de recuperación y su expiración en el usuario
 */
function savePasswordResetToken($userId, $token, $expires)
{
    global $CONNECTION;
    $stmt = $CONNECTION->prepare("UPDATE users SET reset_token = ?, token_expires = ? WHERE cod = ?");
    $stmt->bind_param("ssi", $token, $expires, $userId);
    return $stmt->execute();
}

/**
 * Verifica si un token de recuperación es válido y no expiró
 * Retorna el ID del usuario si es exitoso, o false si falla.
 */
function verifyPasswordResetToken($token)
{
    global $CONNECTION;
    
    // 1. Le preguntamos a PHP qué hora es exactamente AHORA
    $now = date("Y-m-d H:i:s");
    
    // 2. Comparamos el vencimiento contra el reloj de PHP (pasando $now en lugar de usar NOW())
    $stmt = $CONNECTION->prepare("SELECT cod FROM users WHERE reset_token = ? AND token_expires >= ?");
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['cod'];
    }
    
    return false;
}

/**
 * Actualiza la contraseña del usuario y limpia los tokens de recuperación
 */
function resetUserPassword($userId, $newPassword)
{
    global $CONNECTION;
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    // Cambiamos la clave y anulamos el token para que no se pueda volver a usar
    $stmt = $CONNECTION->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expires = NULL WHERE cod = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    return $stmt->execute();
}

/**
 * Obtiene el directorio completo: Dueños -> Sus Locales -> Sus Promociones (Para el Admin Report)
 */
function getOwnersWithStoresAndPromotions(){
    global $CONNECTION;
    $query = "
        SELECT 
            u.cod as owner_id, u.name as owner_name, u.email as owner_email,
            s.id as store_id, s.name as store_name, s.local_number, s.category as store_category,
            p.id as promo_id, p.title as promo_title, p.status as promo_status, p.discount
        FROM users u
        LEFT JOIN stores s ON u.cod = s.id_owner
        LEFT JOIN promotions p ON s.id = p.id_store
        WHERE u.type = 'owner'
        ORDER BY u.name ASC, s.name ASC, p.id DESC
    ";
    
    $result = mysqli_query($CONNECTION, $query);
    $data = [];
    
    if (!$result) return $data;

    while ($row = mysqli_fetch_assoc($result)) {
        $ownerId = $row['owner_id'];
        $storeId = $row['store_id'];
        $promoId = $row['promo_id'];
        
        if (!isset($data[$ownerId])) {
            $data[$ownerId] = ['name' => $row['owner_name'], 'email' => $row['owner_email'], 'stores' => []];
        }
        
        if ($storeId && !isset($data[$ownerId]['stores'][$storeId])) {
            $data[$ownerId]['stores'][$storeId] = [
                'name' => $row['store_name'], 'local_number' => $row['local_number'], 
                'category' => $row['store_category'], 'promotions' => []
            ];
        }
        
        if ($promoId) {
            $data[$ownerId]['stores'][$storeId]['promotions'][] = [
                'title' => $row['promo_title'], 'status' => $row['promo_status'], 'discount' => $row['discount']
            ];
        }
    }
    return $data;
}