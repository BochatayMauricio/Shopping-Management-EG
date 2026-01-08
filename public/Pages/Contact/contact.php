<?php
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/contact.service.php';
include_once __DIR__ . '/../../../app/controllers/contact.controller.php';

session_start();
$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>

    <main class="main-content container py-5">
        <div class="mx-auto" style="max-width: 850px;">
            
            <header class="text-center mb-5">
                <h1 class="fw-bold">Contactanos</h1>
                <p class="text-muted">Dejanos tu consulta y simularemos el envío de una confirmación a tu casilla.</p>
            </header>

            <section class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="name" class="form-control rounded-pill px-3" required>
                        </div>
<div class="col-md-6">
    <label class="form-label small fw-bold">Correo Electrónico</label>
    <?php if($user): ?>
        <input type="email" name="email" 
               class="form-control rounded-pill px-3 bg-light" 
               readonly 
               value="<?php echo htmlspecialchars($user['email']); ?>">
        <small class="text-muted ms-2">Sesión iniciada como usuario registrado.</small>
    <?php else: ?>
        <input type="email" name="email" 
               class="form-control rounded-pill px-3" 
               required 
               placeholder="tu@email.com">
    <?php endif; ?>
</div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Asunto</label>
                            <select name="subject" class="form-select rounded-pill px-3">
                                <option value="Consulta General">Consulta General</option>
                                <option value="Locales">Información de Locales</option>
                                <option value="Sugerencias">Sugerencias</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Mensaje</label>
                            <textarea name="message" class="form-control rounded-4 px-3" rows="5" required></textarea>
                        </div>
                        
<div class="col-12 text-center">
            <button type="submit" name="btnSendMessage" class="btn btn-primary rounded-pill px-5">
                Enviar Mensaje <i class="fas fa-paper-plane ms-2"></i>
            </button>
        </div>
                    </div>
                </form>
            </section>

            <section class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-dark text-white p-4">
                    <div class="row text-center g-4">
                        <div class="col-md-4">
                            <i class="fas fa-phone text-primary mb-2"></i>
                            <p class="mb-0 small fw-bold">Teléfono</p>
                            <span class="text-secondary">0810-555-7672</span>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-envelope text-primary mb-2"></i>
                            <p class="mb-0 small fw-bold">Email</p>
                            <span class="text-secondary">info@rosario.com</span>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-clock text-primary mb-2"></i>
                            <p class="mb-0 small fw-bold">Horarios</p>
                            <span class="text-secondary">Todos los días 10-21hs</span>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <?php include_once __DIR__ . '/../../Components/footer/Footer.php'; ?>

    <?php if(isset($_GET['success'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                title: '¡Mensaje Enviado!',
                text: 'Se ha simulado el envío y deberías recibir una confirmación.',
                icon: 'success',
                confirmButtonColor: '#0d6efd'
            });
        </script>
    <?php endif; ?>
</body>
</html>