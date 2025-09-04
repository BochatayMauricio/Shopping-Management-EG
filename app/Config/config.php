<?php
// Configuración de la aplicación
define('APP_NAME', 'Shopping Rosario');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Argentina/Buenos_Aires');

// Configurar zona horaria
date_default_timezone_set(TIMEZONE);

/**
 * Conexion a la base de datos
 */
$hostname = "localhost";
$username = "root";
$password = "root";
$dbname = "shopping_management";
$dbport = 3306;

$CONNECTION = new mysqli($hostname, $username, $password, $dbname, $dbport);
if ($CONNECTION->connect_error) {
    die("Connection failed: " . $CONNECTION->connect_error);
}


?>
