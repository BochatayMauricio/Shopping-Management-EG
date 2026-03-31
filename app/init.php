<?php
// Cargar el modelo User ANTES de session_start para evitar __PHP_Incomplete_Class
require_once __DIR__ . '/models/User.php';
include_once __DIR__ . '/Config/config.php';
include_once __DIR__ . '/Services/login.services.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar sesión corrupta si existe
if (isset($_SESSION['user']) && $_SESSION['user'] instanceof __PHP_Incomplete_Class) {
    unset($_SESSION['user']);
}

// Procesar logout ANTES de cualquier output HTML
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout();
    exit();
}

// Obtener usuario actual
$user = getCurrentUser();
