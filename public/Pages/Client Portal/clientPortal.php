<?php
// Incluir servicios y iniciar sesión
include_once '../../../app/Services/login.services.php';
include_once '../../../app/Services/news.services.php';
session_start();
$user = getCurrentUser();

// =========================================================================
// LÓGICA DE DATOS (SIMULACIÓN DE BASE DE DATOS)
// =========================================================================

// Simulación de la base de datos de Promociones con el campo 'rubro'
$allPromotions = [
    ['local' => 'Tienda A', 'discount' => 30, 'description' => 'Descuento especial en productos de verano', 'image' => '../../../assets/local1.jpg', 'rubro' => 'ropa'],
    ['local' => 'Tienda B', 'discount' => 20, 'description' => 'Ofertas flash solo por hoy', 'image' => '../../../assets/local2.jpg', 'rubro' => 'tecnologia'],
    ['local' => 'Tienda C', 'discount' => 50, 'description' => 'Hasta 50% off en marcas seleccionadas', 'image' => '../../../assets/local3.jpg', 'rubro' => 'gastronomia'],
    ['local' => 'Tienda D', 'discount' => 15, 'description' => 'Promoción limitada de temporada', 'image' => '../../../assets/local1.jpg', 'rubro' => 'ropa'],
    ['local' => 'Tienda E', 'discount' => 45, 'description' => '2x1 en toda la sección infantil', 'image' => '../../../assets/local2.jpg', 'rubro' => 'servicios'],
    ['local' => 'Tienda F', 'discount' => 10, 'description' => 'Regalo exclusivo con tu compra', 'image' => '../../../assets/local3.jpg', 'rubro' => 'hogar'],
];

$news = getNews();

// No hay lógica de filtrado ya que se eliminó la búsqueda.
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de Promociones</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../Shared/globalStyles.css">
  <link rel="stylesheet" href="clientPortal.css">
  </head>

<body style="background-color: #ffffff;">

  <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>

<header class="main-banner">
    <div class="banner-content">
        <h1>PROMOSHOP</h1>
        <p>Tu guía diaria de ofertas y novedades exclusivas.</p>
    </div>
</header>
<section class="news-section my-5">
    <div class="container">
        <h2 class="section-title mb-4 fw-bold">Últimas Novedades y Eventos</h2>
        
        <div class="row g-4"> 
            <?php foreach ($news as $novedad): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark">
                                <?php echo htmlspecialchars($novedad['title']); ?>
                            </h5>
                            
                            <span class="news-author-text text-muted mb-2 italic">
                                Por: <?php echo htmlspecialchars($novedad['author']); ?>
                            </span>
                            
                            <p class="card-text text-secondary">
                                <?php echo htmlspecialchars($novedad['description']); ?>
                            </p>
                            
                            <div class="mt-auto">
                                <a href="../../Pages/News/News.php" class="btn btn-link p-0 text-decoration-none fw-bold">Leer más →</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="promo-carousel">
    <h2 class="section-title">Promociones Destacadas</h2>
    
    <div class="carousel-wrapper" id="carousel">
        <div class="carousel-container">
        
        <?php foreach ($allPromotions as $promo): 
             // Generar clase de rubro para el color dinámico
            $rubroClass = 'card-rubro-' . strtolower($promo['rubro']); 
        ?>
            <div class="carousel-card <?= htmlspecialchars($rubroClass) ?>">
              <img src="<?= htmlspecialchars($promo['image']) ?>" alt="<?= htmlspecialchars($promo['local']) ?>">
              <h3><?= htmlspecialchars($promo['local']) ?></h3>
              <p><?= htmlspecialchars($promo['description']) ?></p>
              <p><strong><?= htmlspecialchars($promo['discount']) ?>% OFF</strong></p>
            </div>
        <?php endforeach; ?>

        </div>
    </div>
</section>

  <footer class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?php include_once __DIR__ . '/../../Components/footer/Footer.php'; ?>
  </footer>

</body>
</html>