<?php
require_once __DIR__. '/../Services/news.services.php';
require_once __DIR__. '/../Services/alert.service.php';
require_once __DIR__. '/../config/config.php';

if (isset($_POST['btnCreateNews'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $author = $_POST['author'];
    $date = $_POST['date'];

    $success = createNews($title, $description, $image, $author, $date);

    if ($success) {
        // Aquí podrías usar tu alertService
        AlertService::success("Novedad creada exitosamente.");
        header("Location: News.php?status=success");
        exit();
    } else {
        AlertService::error("Error al crear la novedad. Intenta nuevamente.");
        header("Location: ../../public/Pages/Admin/createNews.php?error=1");
    }
    exit();
}