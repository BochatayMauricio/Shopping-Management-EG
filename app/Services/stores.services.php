<?php
include_once __DIR__ . '/../Config/config.php';
include_once __DIR__ . '/../models/Store.php';
include_once __DIR__ . '/../models/User.php';

/**
 * Cuenta el total de locales (con filtros opcionales)
 */
function getTotalStores($filterCategory = 'all', $filterFloor = 'all', $searchName = '')
{
    global $CONNECTION;

    $query = "SELECT COUNT(*) as total FROM stores WHERE 1=1";
    $params = [];
    $types = "";

    if ($filterCategory !== 'all') {
        $query .= " AND LOWER(category) = LOWER(?)";
        $params[] = $filterCategory;
        $types .= "s";
    }
    if ($filterFloor !== 'all') {
        $query .= " AND ubication = ?";
        $params[] = $filterFloor;
        $types .= "s";
    }
    if (!empty($searchName)) {
        $query .= " AND name LIKE ?";
        $params[] = "%$searchName%";
        $types .= "s";
    }

    $stmt = mysqli_prepare($CONNECTION, $query);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
}

/**
 * Trae locales paginados (con filtros opcionales)
 */
function getStoresPaginated($inicio, $cantPorPag, $filterCategory = 'all', $filterFloor = 'all', $searchName = '')
{
    global $CONNECTION;

    $query = "SELECT * FROM stores WHERE 1=1";
    $params = [];
    $types = "";

    if ($filterCategory !== 'all') {
        $query .= " AND LOWER(category) = LOWER(?)";
        $params[] = $filterCategory;
        $types .= "s";
    }
    if ($filterFloor !== 'all') {
        $query .= " AND ubication = ?";
        $params[] = $filterFloor;
        $types .= "s";
    }
    if (!empty($searchName)) {
        $query .= " AND name LIKE ?";
        $params[] = "%$searchName%";
        $types .= "s";
    }

    $query .= " ORDER BY name ASC LIMIT ?, ?";
    $params[] = $inicio;
    $params[] = $cantPorPag;
    $types .= "ii";

    $stmt = mysqli_prepare($CONNECTION, $query);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stores[] = Store::fromArray($row);
    }
    return $stores;
}

/**
 * Trae todos los locales para la vista general
 * @return Store[]
 */
function getAllStores()
{
    global $CONNECTION;

    $query = "SELECT * FROM stores ORDER BY name ASC";
    $result = mysqli_query($CONNECTION, $query);

    if (!$result) {
        return [];
    }

    $stores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stores[] = Store::fromArray($row);
    }
    return $stores;
}

/**
 * Trae solo los locales de un dueño específico
 * @return Store[]
 */
function getStoresByOwner($id_owner)
{
    global $CONNECTION;

    $query = "SELECT * FROM stores WHERE id_owner = ? ORDER BY name ASC";
    $stmt = mysqli_prepare($CONNECTION, $query);

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $id_owner);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stores[] = Store::fromArray($row);
    }
    return $stores;
}

/**
 * Obtiene un local específico por su ID
 * @return Store|null
 */
function getStoreById($id)
{
    global $CONNECTION;
    $query = "SELECT * FROM stores WHERE id = ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    return $row ? Store::fromArray($row) : null;
}

/**
 * Crea un nuevo local asignado a un dueño
 */
function createStore($name, $local_number, $floor, $category, $color, $logo_icon, $id_owner)
{
    global $CONNECTION;

    // Cambié 'floor' por 'ubication' que es el nombre estándar que veníamos usando
    // Verifica en MySQL Workbench si tu columna es 'ubication' o 'floor'
    $query = "INSERT INTO stores (name, local_number, ubication, category, color, logo, id_owner) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($CONNECTION, $query);

    if (!$stmt) {
        die("Error en la preparación: " . mysqli_error($CONNECTION));
    }

    mysqli_stmt_bind_param($stmt, "ssssssi", $name, $local_number, $floor, $category, $color, $logo_icon, $id_owner);

    return mysqli_stmt_execute($stmt);
}

// Función para contar promociones de un local
function countStorePromotions($storeId)
{
    global $CONNECTION;
    $stmt = $CONNECTION->prepare("SELECT COUNT(*) as total FROM promotions WHERE id_store = ?");
    $stmt->bind_param("i", $storeId); // "i" significa que el ID es un entero
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'];
}

// Función de eliminar local con validación de promociones asociadas
function deleteStore($storeId)
{
    global $CONNECTION;
    // 1. Verificamos si tiene promociones
    if (countStorePromotions($storeId) > 0) {
        return false; // No permitimos borrar si tiene promos
    }

    // 2. Si no tiene, procedemos al borrado
    $stmt = $CONNECTION->prepare("DELETE FROM stores WHERE id = ?");
    return $stmt->execute([$storeId]);
}

/**
 * Obtiene todos los usuarios que son de tipo 'owner' para el select del formulario
 * @return User[]
 */
function getAllOwners()
{
    global $CONNECTION;
    $query = "SELECT * FROM users WHERE type = 'owner'";
    $result = mysqli_query($CONNECTION, $query);

    $owners = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $owners[] = User::fromArray($row);
    }
    return $owners;
}

function updateStore($id, $name, $ubication, $local_number)
{
    global $CONNECTION;

    $sql = "UPDATE stores SET name = ?, ubication = ?, local_number = ? WHERE id = ?";

    // 1. Preparamos la sentencia
    $stmt = mysqli_prepare($CONNECTION, $sql);

    if ($stmt) {
        // 2. Vinculamos los parámetros
        // "sssi" significa: string, string, string, integer (el ID suele ser entero)
        mysqli_stmt_bind_param($stmt, "sssi", $name, $ubication, $local_number, $id);

        // 3. Ejecutamos
        $result = mysqli_stmt_execute($stmt);

        // 4. Cerramos la sentencia
        mysqli_stmt_close($stmt);

        return $result;
    }

    return false;
}

/**
 * Cuenta locales agrupados por su categoría (Gastronomía, Ropa, etc.)
 */
function getStoresStatsByCategory()
{
    global $CONNECTION;
    $query = "SELECT category, COUNT(*) as total FROM stores GROUP BY category";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
