<?php
/**
 * Archivo de configuración y utilidades para el sistema Shopping Rosario
 * Contiene funciones comunes y configuraciones del sistema
 */

// Configuración de la aplicación
define('APP_NAME', 'Shopping Rosario');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Argentina/Buenos_Aires');

// Configurar zona horaria
date_default_timezone_set(TIMEZONE);

/**
 * Función para validar email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Función para mostrar mensajes de error/éxito
 * @param string $message
 * @param string $type ('error', 'success', 'warning', 'info')
 * @return string HTML del mensaje
 */
function showMessage($message, $type = 'info') {
    $icons = [
        'error' => 'fa-exclamation-circle',
        'success' => 'fa-check-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle'
    ];
    
    $icon = $icons[$type] ?? $icons['info'];
    
    return "
    <div class='alert alert-{$type}' role='alert'>
        <i class='fas {$icon}'></i>
        " . sanitizeInput($message) . "
    </div>";
}

/**
 * Usuarios de prueba (temporal - reemplazar con base de datos)
 */
function getTestUsers() {
    return [
        'admin@shopping-rosario.com' => [
            'password' => 'admin123',
            'role' => 'admin',
            'name' => 'Juan Carlos Rodriguez',
            'roleDisplay' => 'Administrador del Sistema'
        ],
        'admin2@shopping-rosario.com' => [
            'password' => 'admin456',
            'role' => 'admin',
            'name' => 'María Elena Gutierrez',
            'roleDisplay' => 'Administrador del Sistema'
        ],
        
        // Dueños de locales
        'dueno1@local-moda.com' => [
            'password' => 'dueno123',
            'role' => 'owner',
            'name' => 'Carlos Alberto Fernandez',
            'roleDisplay' => 'Dueño de Local - Moda Express',
            'store' => 'Moda Express',
            'storeCode' => 'MOD001'
        ],
        'dueno2@tech-store.com' => [
            'password' => 'dueno456',
            'role' => 'owner',
            'name' => 'Ana Sofia Martinez',
            'roleDisplay' => 'Dueña de Local - Tech Store',
            'store' => 'Tech Store',
            'storeCode' => 'TEC002'
        ],
        'dueno3@zapatos-rosario.com' => [
            'password' => 'dueno789',
            'role' => 'owner',
            'name' => 'Roberto Luis Sanchez',
            'roleDisplay' => 'Dueño de Local - Zapatos Rosario',
            'store' => 'Zapatos Rosario',
            'storeCode' => 'ZAP003'
        ],
        
        // Clientes
        'cliente1@gmail.com' => [
            'password' => 'cliente123',
            'role' => 'client',
            'name' => 'Lucia Fernanda Torres',
            'roleDisplay' => 'Cliente Premium',
            'category' => 'Premium',
            'promotionsUsed' => 25
        ],
        'cliente2@hotmail.com' => [
            'password' => 'cliente456',
            'role' => 'client',
            'name' => 'Diego Alejandro Morales',
            'roleDisplay' => 'Cliente Medium',
            'category' => 'Medium',
            'promotionsUsed' => 12
        ],
        'cliente3@yahoo.com' => [
            'password' => 'cliente789',
            'role' => 'client',
            'name' => 'Valentina Isabel Ruiz',
            'roleDisplay' => 'Cliente Inicial',
            'category' => 'Inicial',
            'promotionsUsed' => 3
        ]
    ];
}

/**
 * Función para autenticar usuario
 * @param string $email
 * @param string $password
 * @return array|false
 */
function authenticateUser($email, $password) {
    $users = getTestUsers();
    $email = strtolower(trim($email));
    
    if (isset($users[$email]) && $users[$email]['password'] === $password) {
        $user = $users[$email];
        $user['email'] = $email;
        return $user;
    }
    
    return false;
}
?>
