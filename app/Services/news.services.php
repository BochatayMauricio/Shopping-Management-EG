<?php
// app/Services/news.services.php

// Supongamos que tienes un archivo de conexión global
require_once __DIR__. '/../config/config.php';

function getNews() {
    global $CONNECTION;

    $news = []; // Array donde guardaremos los resultados

    // 1. Verificamos la conexión
    if (!$CONNECTION) {
        error_log("Error: No hay conexión a la base de datos en getNews");
        return [];
    }

    // 2. Definimos la consulta (ajusta los nombres de las columnas según tu DB)
    // Es recomendable ordenar por fecha para que las últimas noticias salgan primero
    $query = "SELECT id, title, description, image, author, date FROM news ORDER BY date DESC";

    // 3. Ejecutamos la consulta
    $result = mysqli_query($CONNECTION, $query);

    // 4. Verificamos si hubo errores en la consulta
    if (!$result) {
        error_log("Error en la consulta de noticias: " . mysqli_error($CONNECTION));
        return [];
    }

    // 5. Recorremos los resultados y los guardamos en el array
    while ($row = mysqli_fetch_assoc($result)) {
        $news[] = $row;
    }

    // 6. Retornamos el array con los datos reales
    return $news;
}

function createNews($title, $description, $image, $author, $date) {
    global $CONNECTION;

    $query = "INSERT INTO news (title, description, image, author, date) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($CONNECTION, $query);
    
    // "sssss" indica que los 5 parámetros son strings
    mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $image, $author, $date);
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
// function getNews() {
//     // Simulamos la conexión y consulta a la base de datos
//     // En un entorno real usarías: $db->query("SELECT * FROM news");
    
//     $news = [
//         [
//             'id' => 1,
//             'title' => '¡Mostaza inauguró su nuevo local!',
//             'description' => 'La empresa de comida rápida acaba de inaugurar su nuevo local en el patio de comidas de Shopping Rosario. Podes pasar a disfrutar de sus exquisitas hamburguesas a un precio promocional por su apertura.',
//             'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop',
//             'author' => 'Admin 1',
//             'date' => '2025-11-20'
//         ],
//         [
//             'id' => 2,
//             'title' => 'Nueva colección de invierno en Zara',
//             'description' => 'Zara presenta su nueva colección de invierno 2025 con descuentos especiales para los primeros compradores. Visitanos en el primer piso.',
//             'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1000&auto=format&fit=crop',
//             'author' => 'Admin 2',
//             'date' => '2025-11-18'
//         ],
//         [
//             'id' => 3,
//             'title' => 'Apple Store: Nuevos iPhone disponibles',
//             'description' => 'Ya podes encontrar la nueva línea de iPhone en nuestra tienda oficial de Apple. Vení a probarlos y aprovechá nuestras promociones de lanzamiento.',
//             'image' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?q=80&w=1000&auto=format&fit=crop',
//             'author' => 'Admin 1',
//             'date' => '2025-11-15'
//         ],
//         [
//             'id' => 4,
//             'title' => 'Starbucks lanza bebidas de temporada',
//             'description' => 'Disfrutá de nuestras nuevas bebidas de invierno con sabores exclusivos. Disponibles por tiempo limitado en nuestro local de planta baja.',
//             'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=1000&auto=format&fit=crop',
//             'author' => 'Admin 3',
//             'date' => '2025-11-12'
//         ],
//         [
//             'id' => 5,
//             'title' => 'Nike: Colección deportiva primavera',
//             'description' => 'Descubrí la nueva colección deportiva de Nike con tecnología de última generación. Zapatillas, indumentaria y accesorios para todos los deportes.',
//             'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1000&auto=format&fit=crop',
//             'author' => 'Admin 2',
//             'date' => '2025-11-10'
//         ]
//     ];

//     return $news;
// }