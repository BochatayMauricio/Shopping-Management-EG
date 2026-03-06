<?php
// Cargar autoload de Composer
 require_once __DIR__ . '/../../vendor/autoload.php';

// // Cargar variables de entorno desde .env usando phpdotenv (solo si existe)
 $envFile = __DIR__ . '/../../.env';
 if (file_exists($envFile)) {
     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
     $dotenv->load();
}

// // Función helper para obtener variables de entorno
function env($key, $default = null) {
     return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Configuración de la aplicación
define('APP_NAME', 'Shopping Rosario');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Argentina/Buenos_Aires');

// // Detectar BASE_URL automáticamente
$isProduction = env('APP_ENV', 'production') === 'production' || !str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');
define('BASE_URL', $isProduction ? '' : '/Shopping-Management-EG');

// Configurar zona horaria
date_default_timezone_set(TIMEZONE);

/**
 * Conexion a la base de datos
 */
$hostname = env('DB_HOST', 'localhost');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', 'vicen');
$dbname = env('DB_NAME', 'shopping_management');
$dbport = env('DB_PORT', 3306);



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
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(100) NOT NULL,
        type VARCHAR(15) NOT NULL,
        category VARCHAR(10) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        logo VARCHAR(255) DEFAULT 'default_logo.png',
        color VARCHAR(7) DEFAULT '#0d6efd',
        ubication VARCHAR(50) NOT NULL,
        local_number VARCHAR(10) NOT NULL,
        category VARCHAR(30) NOT NULL,
        id_owner INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_owner) REFERENCES users(cod) ON DELETE SET NULL ON UPDATE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS promotions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description VARCHAR(200) NOT NULL,
        image VARCHAR(255) DEFAULT 'default_logo.png',
        date_from DATE NOT NULL,
        date_until DATE NOT NULL,
        client_category VARCHAR(15) NOT NULL,
        week_days VARCHAR(255) NOT NULL,
        status VARCHAR(15) NOT NULL,
        discount DECIMAL(5,2) NOT NULL,
        price DECIMAL(10,2) DEFAULT NULL,
        original_price DECIMAL(10,2) DEFAULT NULL,
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
    )",
    "CREATE TABLE IF NOT EXISTS news (
        id INT NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        author VARCHAR(100) NOT NULL,
        date DATE NOT NULL,
        PRIMARY KEY (id)
    )",
    "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $table) {
    if (!$CONNECTION->query($table)) {
        die("Error creating table: " . $CONNECTION->error);
    }
}

// ============================================
// SEED DATA - Datos iniciales
// ============================================

// Verificar si ya hay datos insertados
$checkUsers = $CONNECTION->query("SELECT COUNT(*) as total FROM users");
$userCount = $checkUsers->fetch_assoc()['total'];

if ($userCount == 0) {
    // Passwords hasheados (password: "admin123" para admin, "cliente123" para clientes)
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $clientPassword = password_hash('cliente123', PASSWORD_DEFAULT);
    $ownerPassword = password_hash('tienda123', PASSWORD_DEFAULT);

    // Insertar usuarios
    $users = [
        "INSERT INTO users (name, email, password, type, category) VALUES 
            ('admin', 'admin@shopping.com', '$adminPassword', 'admin', 'premium'),
            ('cliente1', 'cliente1@email.com', '$clientPassword', 'client', 'gold'),
            ('cliente2', 'cliente2@email.com', '$clientPassword', 'client', 'silver'),
            ('tienda1', 'tienda1@shopping.com', '$ownerPassword', 'owner', 'premium'),
            ('tienda2', 'tienda2@shopping.com', '$ownerPassword', 'owner', 'standard')"
    ];

    foreach ($users as $sql) {
        if (!$CONNECTION->query($sql)) {
            error_log("Error inserting users: " . $CONNECTION->error);
        }
    }

    // Insertar tiendas
    $stores = "INSERT INTO stores (name, logo, color, ubication, local_number, category, id_owner) VALUES 
        ('Café Central', 'cafe_central.png', '#8B4513', 'Planta Baja', '101', 'gastronomia', 4),
        ('Tech Store', 'tech_store.png', '#0066CC', 'Primer Piso', '201', 'tecnologia', 4),
        ('Fashion Style', 'fashion_style.png', '#FF69B4', 'Segundo Piso', '301', 'ropa', 5),
        ('Librería Cultura', 'libreria.png', '#228B22', 'Planta Baja', '102', 'libreria', 5),
        ('Heladería Dulce', 'heladeria.png', '#FFA500', 'Patio de Comidas', '401', 'gastronomia', 4)";

    if (!$CONNECTION->query($stores)) {
        error_log("Error inserting stores: " . $CONNECTION->error);
    }

    // Insertar promociones
    $promotions = "INSERT INTO promotions (title, description, image, date_from, date_until, client_category, week_days, status, discount, price, original_price, id_store) VALUES 
        ('2x1 en Café', 'Llevá dos cafés por el precio de uno', 'promo_cafe.png', '2026-02-01', '2026-03-31', 'gold', 'Lunes,Martes,Miércoles', 'active', 50.00, 500.00, 1000.00, 1),
        ('20% en Electrónica', 'Descuento en toda la línea de smartphones', 'promo_tech.png', '2026-02-15', '2026-02-28', 'premium', 'Todos', 'active', 20.00, 80000.00, 100000.00, 2),
        ('Outlet de Ropa', 'Hasta 40% en ropa de temporada', 'promo_fashion.png', '2026-02-01', '2026-04-30', 'silver', 'Viernes,Sábado,Domingo', 'active', 40.00, 6000.00, 10000.00, 3),
        ('3x2 en Libros', 'Llevá 3 libros y pagá solo 2', 'promo_libros.png', '2026-02-10', '2026-03-10', 'gold', 'Todos', 'active', 33.33, 2000.00, 3000.00, 4),
        ('Helado Gratis', 'Por compras mayores a $2000, helado de regalo', 'promo_helado.png', '2026-02-01', '2026-02-28', 'premium', 'Lunes,Martes', 'active', 100.00, 0.00, 800.00, 5)";

    if (!$CONNECTION->query($promotions)) {
        error_log("Error inserting promotions: " . $CONNECTION->error);
    }

    // Insertar noticias
    $news = "INSERT INTO news (title, description, image, author, date) VALUES 
        ('¡Nuevo local de tecnología!', 'Tech Store abre sus puertas con increíbles ofertas de inauguración. Visitanos en el primer piso, local 201.', 'news_tech.png', 'Administración', '2026-02-15'),
        ('Horario extendido fin de semana', 'Este fin de semana el shopping estará abierto hasta las 23hs. ¡Aprovechá para recorrer todas las tiendas!', 'news_horario.png', 'Administración', '2026-02-18'),
        ('Festival Gastronómico', 'Del 1 al 15 de marzo, festival gastronómico en el patio de comidas con precios especiales.', 'news_gastro.png', 'Marketing', '2026-02-20')";

    if (!$CONNECTION->query($news)) {
        error_log("Error inserting news: " . $CONNECTION->error);
    }
}
?>
