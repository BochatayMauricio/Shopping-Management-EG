<?php
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal del Cliente</title>
  <link rel="stylesheet" href="/Shopping-Management-EG/public/pages/Client Portal/clientPortal.css">
  <!-- Agregar otros estilos o librerías aquí -->
</head>

<body style="background-color: #002a47;">

  <!-- Navbar -->
  <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>


<section class="promo-carousel">
    <h2 class="section-title">Promociones más llamativas</h2>
     <!-- Botón Prev -->
    <button class="carousel-btn prev">&#10094;</button>
    <div class="carousel-wrapper" id="carousel">
    <!-- Contenedor Carrusel -->
    <div class="carousel-container" >
    
      <!-- Tarjeta 1 -->
      <div class="carousel-card">
        <img src="../../../assets/local1.jpg" alt="Promo 1">
        <h3>Promo 1</h3>
        <p>Descuentos increíbles en productos seleccionados.</p>
      </div>

      <!-- Tarjeta 2 -->
      <div class="carousel-card">
        <img src="../../../assets/local2.jpg" alt="Promo 2">
        <h3>Promo 2</h3>
        <p>Ofertas flash disponibles solo por hoy.</p>
      </div>

      <!-- Tarjeta 3 -->
      <div class="carousel-card">
        <img src="../../../assets/local3.jpg" alt="Promo 3">
        <h3>Promo 3</h3>
        <p>Conocé las nuevas tiendas que se suman.</p>
      </div>

      <!-- Tarjeta 4 -->
      <div class="carousel-card">
        <img src="../../../assets/local1.jpg" alt="Promo 4">
        <h3>Promo 4</h3>
        <p>Regalos y premios con tus compras.</p>
      </div>

      <!-- Tarjeta 5 -->
      <div class="carousel-card">
        <img src="../../../assets/local1.jpg" alt="Promo 5">
        <h3>Promo 5</h3>
        <p>Hasta 50% off en marcas seleccionadas.</p>
      </div>

    </div>
    </div>
    <!-- Botón Next -->
    <button class="carousel-btn next">&#10095;</button>
    
  </section>
  <section class="search-promo">
  <h2 class="section-title">Buscar Promoción</h2>

  <form id="searchForm">
    <div class="form-group">
      <label for="localName">Nombre del local:</label>
      <input type="text" id="localName" placeholder="Ej: Tienda XYZ">
    </div>

    <div class="form-group">
      <label for="discount">Porcentaje de descuento mínimo:</label>
      <input type="number" id="discount" placeholder="Ej: 20" min="0" max="100">
    </div>

    <button type="submit" class="btn-primary">Buscar</button>
  </form>

  <div class="search-results" id="searchResults">
    <!-- Aquí se mostrarán las promociones filtradas -->
  </div>
</section>

  <!-- Footer -->
  <footer class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?php include_once __DIR__ . '/../../Components/footer/Footer.php'; ?>
  </footer>
  <script src="clientPortal.js"></script>

</body>
</html>
