<?php
/**
 * Archivo de inicialización de sesión y manejo de logout
 * Incluir al inicio de TODAS las páginas, ANTES de cualquier HTML
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración y servicios base
include_once __DIR__ . '/Config/config.php';
include_once __DIR__ . '/Services/login.services.php';

// Procesar logout ANTES de cualquier output HTML
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
    exit();
}

// Obtener usuario actual
$user = getCurrentUser();
?>
