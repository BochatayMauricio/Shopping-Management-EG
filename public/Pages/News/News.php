<?php
include_once __DIR__ . '/../../../app/controllers/news.controller.php';
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/news.services.php';
session_start();
$user = getCurrentUser();
$news = getNews();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="News.css">
    <style>
        /* Mejoras estéticas adicionales */
        .admin-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            font-family: 'Poppins', sans-serif;
        }
        .modal-content { border-radius: 15px; border: none; font-family: 'Poppins', sans-serif; }
        .modal-header { background: #007bff; color: white; border-radius: 15px 15px 0 0; }
        .text-truncate-custom {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content">
        <div class="container-custom">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="news-section-title mb-4 pt-4">Novedades</h2>
        
        <?php if($user && $user['type'] === 'admin'): ?>
            <button type="button" class="btn btn-primary admin-fab" data-bs-toggle="modal" data-bs-target="#createNewsModal">
                <i class="fa-solid fa-plus me-2"></i> Crear Novedad
            </button>
        <?php endif; ?>
    </div>

    <div class="news-grid">
        <?php foreach ($news as $item): ?>
            <div class="news-item-card">
                <div class="news-image-container">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" class="news-image" alt="Novedad">
                    <div class="news-image-overlay"></div>
                </div>
                <div class="news-content">
                    <h3 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <div class="news-meta">
                        <i class="fa-regular fa-user"></i>
                        <span class="news-author-text">Por: <?php echo htmlspecialchars($item['author']); ?></span>
                        <span class="news-date-text ms-3">
                            <i class="fa-regular fa-calendar"></i> <?php echo date("d/m/Y", strtotime($item['date'])); ?>
                        </span>
                    </div>
                    <p class="news-description">
                        <?php echo htmlspecialchars($item['description']); ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

        <?php if($user && $user['type'] === 'admin'): ?>
        <div class="modal fade" id="createNewsModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel"><i class="fa-solid fa-pen-to-square me-2"></i>Nueva Publicación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-semibold">Título</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Fecha</label>
                                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">URL de la Imagen</label>
                                    <input type="url" name="image" class="form-control" placeholder="https://..." required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Descripción</label>
                                    <textarea name="description" class="form-control" rows="5" required></textarea>
                                </div>
                                <input type="hidden" name="author" value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="btnCreateNews" class="btn btn-success px-4">Publicar Novedad</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>