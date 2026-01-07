<?php
include_once __DIR__ . '/../Config/config.php';
// app/Services/store.services.php

/**
 * Trae todos los locales para la vista general
 */
function getAllStores() {
    global $CONNECTION; // Usamos la variable de conexión definida en db.php o config.php

    $query = "SELECT * FROM stores ORDER BY name ASC";
    $result = mysqli_query($CONNECTION, $query);

    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Trae solo los locales de un dueño específico
 */
function getStoresByOwner($id_owner) {
    global $CONNECTION;

    $query = "SELECT * FROM stores WHERE id_owner = ? ORDER BY name ASC";
    $stmt = mysqli_prepare($CONNECTION, $query);

    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, "i", $id_owner);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Obtiene un local específico por su ID
 */
function getStoreById($id) {
    global $CONNECTION;
    $query = "SELECT * FROM stores WHERE id = ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

/**
 * Crea un nuevo local asignado a un dueño
 */
function createStore($name, $local_number, $floor, $category, $color, $logo_icon, $id_owner) {
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

/**
 * Obtiene todos los usuarios que son de tipo 'owner' para el select del formulario
 */
function getAllOwners() {
    global $CONNECTION;
    $query = "SELECT cod, name FROM users WHERE type = 'owner'";
    $result = mysqli_query($CONNECTION, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function updateStore($id, $name, $ubication, $local_number) {
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