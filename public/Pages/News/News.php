<?php
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();

// Datos estáticos de novedades
$news = [
    [
        'id' => 1,
        'title' => '¡Mostaza inauguró su nuevo local!',
        'description' => 'La empresa de comida rápida acaba de inaugurar su nuevo local en el patio de comidas de Shopping Rosario. Podes pasar a disfrutar de sus exquisitas hamburguesas a un precio promocional por su apertura.',
        'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop',
        'author' => 'Admin 1',
        'date' => '2025-11-20'
    ],
    [
        'id' => 2,
        'title' => 'Nueva colección de invierno en Zara',
        'description' => 'Zara presenta su nueva colección de invierno 2025 con descuentos especiales para los primeros compradores. Visitanos en el primer piso.',
        'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1000&auto=format&fit=crop',
        'author' => 'Admin 2',
        'date' => '2025-11-18'
    ],
    [
        'id' => 3,
        'title' => 'Apple Store: Nuevos iPhone disponibles',
        'description' => 'Ya podes encontrar la nueva línea de iPhone en nuestra tienda oficial de Apple. Vení a probarlos y aprovechá nuestras promociones de lanzamiento.',
        'image' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?q=80&w=1000&auto=format&fit=crop',
        'author' => 'Admin 1',
        'date' => '2025-11-15'
    ],
    [
        'id' => 4,
        'title' => 'Starbucks lanza bebidas de temporada',
        'description' => 'Disfrutá de nuestras nuevas bebidas de invierno con sabores exclusivos. Disponibles por tiempo limitado en nuestro local de planta baja.',
        'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=1000&auto=format&fit=crop',
        'author' => 'Admin 3',
        'date' => '2025-11-12'
    ],
    [
        'id' => 5,
        'title' => 'Nike: Colección deportiva primavera',
        'description' => 'Descubrí la nueva colección deportiva de Nike con tecnología de última generación. Zapatillas, indumentaria y accesorios para todos los deportes.',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1000&auto=format&fit=crop',
        'author' => 'Admin 2',
        'date' => '2025-11-10'
    ]
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="News.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content">
        <div class="container-custom">
            
            <!-- Título de la Sección -->
            <h2 class="news-section-title">Novedades</h2>

            <!-- Tarjeta de Novedad -->
            <div class="news-card">
                
                <?php foreach ($news as $index => $item): ?>
                    <!-- Input radio oculto para controlar cada slide -->
                    <input type="radio" 
                           name="news-slider" 
                           id="news-<?php echo $index; ?>" 
                           class="news-radio"
                           <?php echo $index === 0 ? 'checked' : ''; ?>>
                <?php endforeach; ?>

                <!-- Contenedor de slides -->
                <div class="news-slider-container">
                    <?php foreach ($news as $index => $item): ?>
                        <div class="news-slide" data-slide="<?php echo $index; ?>">
                            <!-- Imagen de Portada -->
                            <div class="news-image-container">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="news-image">
                                <div class="news-image-overlay"></div>
                            </div>

                            <!-- Contenido de la Noticia -->
                            <div class="news-content">
                                
                                <!-- Título -->
                                <h3 class="news-title">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>

                                <!-- Meta Data (Autor) -->
                                <div class="news-meta">
                                    <div class="news-author-icon">
                                        <i class="fa-regular fa-user"></i>
                                    </div>
                                    <span class="news-author-text">Por: <?php echo htmlspecialchars($item['author']); ?></span>
                                </div>

                                <!-- Texto Descripción -->
                                <p class="news-description">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                    <a href="#" class="news-read-more">Ver más</a>
                                </p>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Footer del Slider (Puntos y Flechas) -->
                <div class="news-navigation">
                    <!-- Flecha Anterior -->
                    <div class="news-nav-arrows-container">
                        <?php for ($i = 0; $i < count($news); $i++): ?>
                            <?php if ($i > 0): ?>
                                <label for="news-<?php echo $i - 1; ?>" 
                                       class="news-nav-arrow news-nav-prev"
                                       data-for-slide="<?php echo $i; ?>">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </label>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <!-- Indicadores (Dots) -->
                    <div class="news-dots">
                        <?php for ($i = 0; $i < count($news); $i++): ?>
                            <label for="news-<?php echo $i; ?>" 
                                   class="news-dot"
                                   data-index="<?php echo $i; ?>"></label>
                        <?php endfor; ?>
                    </div>

                    <!-- Flecha Siguiente -->
                    <div class="news-nav-arrows-container">
                        <?php for ($i = 0; $i < count($news); $i++): ?>
                            <?php if ($i < count($news) - 1): ?>
                                <label for="news-<?php echo $i + 1; ?>" 
                                       class="news-nav-arrow news-nav-next"
                                       data-for-slide="<?php echo $i; ?>">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </label>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>