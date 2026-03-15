<?php
// Cargar autoload de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

function env($key, $default = null)
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Configuración de la aplicación
define('APP_NAME', 'Shopping Rosario');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Argentina/Buenos_Aires');

// // Detectar BASE_URL automáticamente
$isProduction = env('APP_ENV', 'production') === 'production' || !str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');
define('BASE_URL', $isProduction ? '' : '/Shopping-Management-EG');

$envFilePath = __DIR__ . '/../../env.local.php';
if (file_exists($envFilePath)) {
    include_once $envFilePath;
}

// Configurar zona horaria
date_default_timezone_set(TIMEZONE);

/**
 * Conexion a la base de datos
 */
$hostname = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];
$dbport = $_ENV['DB_PORT'] ?? 3306;

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
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $clientPassword = password_hash('cliente123', PASSWORD_DEFAULT);
    $ownerPassword = password_hash('tienda123', PASSWORD_DEFAULT);

    $levelInicial = 'inicial';
    $levelMedium = 'medium';
    $levelPremium = 'premium';

    // Insertar usuarios
    $users = [
        "INSERT INTO users (name, email, password, type, category) VALUES 
            ('admin', 'admin@shopping.com', '$adminPassword', 'admin', '$levelPremium'),
            ('cliente1', 'cliente1@email.com', '$clientPassword', 'client', '$levelMedium'),
            ('cliente2', 'cliente2@email.com', '$clientPassword', 'client', '$levelInicial'),
            ('cliente3', 'cliente3@email.com', '$clientPassword', 'client', '$levelPremium'),
            ('cliente4', 'cliente4@email.com', '$clientPassword', 'client', '$levelInicial'),
            ('tienda1', 'tienda1@shopping.com', '$ownerPassword', 'owner', '$levelPremium'),
            ('tienda2', 'tienda2@shopping.com', '$ownerPassword', 'owner', '$levelInicial'),
            ('tienda3', 'tienda3@shopping.com', '$ownerPassword', 'owner', '$levelMedium')"
    ];

    foreach ($users as $sql) {
        if (!$CONNECTION->query($sql)) {
            error_log("Error inserting users: " . $CONNECTION->error);
        }
    }

    // Insertar tiendas
    $stores = "INSERT INTO stores (name, logo, color, ubication, local_number, category, id_owner) VALUES 
        ('Café Central', 'cafe_central.png', '#8B4513', 'Planta Baja', '101', 'gastronomia', 6),
        ('Tech Store', 'tech_store.png', '#0066CC', 'Primer Piso', '201', 'tecnologia', 6),
        ('Fashion Style', 'fashion_style.png', '#FF69B4', 'Segundo Piso', '301', 'ropa', 7),
        ('Librería Cultura', 'libreria.png', '#228B22', 'Planta Baja', '102', 'libreria', 7),
        ('Heladería Dulce', 'heladeria.png', '#FFA500', 'Patio de Comidas', '401', 'gastronomia', 6),
        ('Sport Planet', 'sport_planet.png', '#1D4ED8', 'Segundo Piso', '305', 'deportes', 8),
        ('Beauty Hub', 'beauty_hub.png', '#DB2777', 'Primer Piso', '210', 'belleza', 8)";

    if (!$CONNECTION->query($stores)) {
        error_log("Error inserting stores: " . $CONNECTION->error);
    }

    // Insertar promociones (client_category usa los mismos niveles)
    $promotions = "INSERT INTO promotions (title, description, image, date_from, date_until, client_category, week_days, status, discount, price, original_price, id_store) VALUES 
        ('2x1 en Café', 'Llevá dos cafés por el precio de uno', 'promo_cafe.png', '2026-03-01', '2026-12-31', '$levelMedium', 'Lunes,Martes,Miércoles', 'active', 50.00, 500.00, 1000.00, 1),
        ('20% en Electrónica', 'Descuento en toda la línea de smartphones', 'promo_tech.png', '2026-03-05', '2026-11-30', '$levelPremium', 'Todos', 'active', 20.00, 80000.00, 100000.00, 2),
        ('Outlet de Ropa', 'Hasta 40% en ropa de temporada', 'promo_fashion.png', '2026-03-01', '2026-10-31', '$levelInicial', 'Viernes,Sábado,Domingo', 'active', 40.00, 6000.00, 10000.00, 3),
        ('3x2 en Libros', 'Llevá 3 libros y pagá solo 2', 'promo_libros.png', '2026-03-10', '2026-09-30', '$levelMedium', 'Todos', 'active', 33.33, 2000.00, 3000.00, 4),
        ('Helado Gratis', 'Por compras mayores a $2000, helado de regalo', 'promo_helado.png', '2026-03-01', '2026-08-31', '$levelPremium', 'Lunes,Martes', 'active', 100.00, 0.00, 800.00, 5),
        ('25% Running', 'Descuento en zapatillas y accesorios running', 'promo_running.png', '2026-03-12', '2026-12-15', '$levelInicial', 'Jueves,Viernes,Sábado', 'active', 25.00, 22500.00, 30000.00, 6),
        ('Combo Skincare', 'Rutina completa con precio promocional', 'promo_beauty.png', '2026-03-15', '2026-11-20', '$levelMedium', 'Todos', 'pending', 30.00, 17500.00, 25000.00, 7)";

    if (!$CONNECTION->query($promotions)) {
        error_log("Error inserting promotions: " . $CONNECTION->error);
    }

    // Insertar noticias
    $news = "INSERT INTO news (title, description, image, author, date) VALUES 
        ('¡Nuevo local de tecnología!', 'Tech Store abre sus puertas con increíbles ofertas de inauguración. Visitanos en el primer piso, local 201.', 'news_tech.png', 'Administración', '2026-03-15'),
        ('Horario extendido fin de semana', 'Este fin de semana el shopping estará abierto hasta las 23hs. Aprovechá para recorrer todas las tiendas.', 'news_horario.png', 'Administración', '2026-03-18'),
        ('Festival Gastronómico', 'Del 1 al 15 de abril, festival gastronómico en el patio de comidas con precios especiales.', 'news_gastro.png', 'Marketing', '2026-03-20'),
        ('Semana del Deporte', 'Promociones en indumentaria y accesorios deportivos durante toda la semana.', 'news_sport.png', 'Marketing', '2026-03-22'),
        ('Beauty Days en el Shopping', 'Nuevos combos de cuidado personal con descuentos por tiempo limitado.', 'news_beauty.png', 'Comercial', '2026-03-25')";

    if (!$CONNECTION->query($news)) {
        error_log("Error inserting news: " . $CONNECTION->error);
    }

    // Insertar relaciones cliente-promoción para probar niveles y estados
    $userPromotions = "INSERT INTO user_promotions (id_client, id_promotion, date_from, status) VALUES
        (2, 1, '2026-03-03', 'used'),
        (2, 3, '2026-03-05', 'used'),
        (2, 4, '2026-03-08', 'used'),
        (3, 3, '2026-03-09', 'active'),
        (3, 6, '2026-03-10', 'pending'),
        (4, 1, '2026-03-01', 'used'),
        (4, 2, '2026-03-02', 'used'),
        (4, 4, '2026-03-04', 'used'),
        (4, 5, '2026-03-06', 'used'),
        (4, 6, '2026-03-11', 'used'),
        (5, 1, '2026-03-12', 'rejected')";

    if (!$CONNECTION->query($userPromotions)) {
        error_log("Error inserting user_promotions: " . $CONNECTION->error);
    }

    // Insertar mensajes de contacto de ejemplo
    $contacts = "INSERT INTO contact_messages (name, email, subject, message) VALUES
        ('Lucia Perez', 'lucia.perez@email.com', 'Consulta promo', 'Hola, quiero saber si la promo de tecnologia aplica a notebooks.'),
        ('Juan Gomez', 'juan.gomez@email.com', 'Problema de canje', 'No pude canjear una promo que figura activa en mi portal.'),
        ('Marta Lopez', 'marta.lopez@email.com', 'Sugerencia', 'Seria bueno agregar filtros por monto y por rubro en el home.')";

    if (!$CONNECTION->query($contacts)) {
        error_log("Error inserting contact_messages: " . $CONNECTION->error);
    }
}
