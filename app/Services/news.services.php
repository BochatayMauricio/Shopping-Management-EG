<?php
// app/Services/news.services.php

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../models/News.php';

/**
 * Cuenta el total de noticias
 */
function getTotalNews()
{
    global $CONNECTION;

    $query = "SELECT COUNT(*) as total FROM news";
    $result = mysqli_query($CONNECTION, $query);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] ?? 0;
}

/**
 * Obtiene noticias paginadas
 * @return News[]
 */
function getNewsPaginated($inicio, $cantPorPag)
{
    global $CONNECTION;

    $query = "SELECT id, title, description, image, author, date FROM news ORDER BY date DESC LIMIT ?, ?";
    $stmt = mysqli_prepare($CONNECTION, $query);
    mysqli_stmt_bind_param($stmt, "ii", $inicio, $cantPorPag);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $news = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $news[] = News::fromArray($row);
    }
    return $news;
}

/**
 * Obtiene todas las noticias
 * @return News[]
 */
function getNews()
{
    global $CONNECTION;

    if (!$CONNECTION) {
        error_log("Error: No hay conexión a la base de datos en getNews");
        return [];
    }

    $query = "SELECT id, title, description, image, author, date FROM news ORDER BY date DESC";
    $result = mysqli_query($CONNECTION, $query);

    if (!$result) {
        error_log("Error en la consulta de noticias: " . mysqli_error($CONNECTION));
        return [];
    }

    $news = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $news[] = News::fromArray($row);
    }

    return $news;
}

/**
 * Crea una nueva noticia
 * @param News|array $newsData Objeto News o array con datos
 * @return bool
 */
function createNews($title, $description, $image, $author, $date)
{
    global $CONNECTION;

    $query = "INSERT INTO news (title, description, image, author, date) VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($CONNECTION, $query);

    mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $image, $author, $date);

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
