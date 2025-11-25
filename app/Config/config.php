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

//migraciones
$sql = "CREATE DATABASE IF NOT EXISTS `" . $CONNECTION->real_escape_string($dbname) . "`";
if (!$CONNECTION->query($sql)) {
    die("Error creating database: " . $CONNECTION->error);
}
if (!$CONNECTION->select_db($dbname)) {
    die("Error selecting database: " . $CONNECTION->error);
}

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        cod INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        password VARCHAR(100) UNIQUE NOT NULL,
        type VARCHAR(15) NOT NULL,
        category VARCHAR(10) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        ubication VARCHAR(50) NOT NULL,
        category VARCHAR(30) NOT NULL,
        id_owner INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_owner) REFERENCES users(cod) ON DELETE SET NULL ON UPDATE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        description VARCHAR(200) NOT NULL,
        date_from DATE NOT NULL,
        date_until DATE NOT NULL,
        client_category VARCHAR(15) NOT NULL,
        week_days VARCHAR(255) NOT NULL,
        status VARCHAR(15) NOT NULL,
        discount DECIMAL(5,2) NOT NULL,
        id_store INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_store) REFERENCES stores(id) ON DELETE CASCADE ON UPDATE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS user_promotions (
        id_client INT NOT NULL,
        id_promotion INT NOT NULL,
        date_from DATE NOT NULL,
        status VARCHAR(15) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_client, id_promotion),
        FOREIGN KEY (id_client) REFERENCES users(cod) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (id_promotion) REFERENCES promotions(id) ON DELETE CASCADE ON UPDATE CASCADE
    )"
];

foreach ($tables as $table) {
    if (!$CONNECTION->query($table)) {
        die("Error creating table: " . $CONNECTION->error);
    }
}
?>
