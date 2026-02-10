<?php
include_once __DIR__ . '/../../../app/Services/login.services.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';

session_start();
$user = getCurrentUser();

// Si no está logueado, al login
if (!$user) {
    header("Location: ../Login/login.php");
    exit();
}

$myPromos = getClientPromotions($user['cod'] ?? $user['id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Portal - Shopping Rosario</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="clientPortal.css">
</head>
<body>
    <?php include_once '../../Components/navbar/NavBar.php'; ?>

    <main class="main-content py-5">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-lg-4">
                    <div class="card profile-card shadow-sm rounded-4 p-4 text-center">
                        <div class="user-avatar-circle rounded-circle mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <div class="mb-3">
                            <span class="badge badge-<?php echo strtolower($user['category']); ?> rounded-pill px-3 py-2">
                                Nivel <?php echo $user['category']; ?>
                            </span>
                        </div>
                        <hr>
                        <div class="text-start mt-3">
                            <p class="small mb-2"><strong>Tipo de cuenta:</strong> <?php echo ucfirst($user['type']); ?></p>
                            <p class="small mb-0"><strong>Promos obtenidas:</strong> <?php echo count($myPromos); ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h3 class="fw-bold mb-4">Mis Cupones Obtenidos</h3>
                    
                    <?php if (count($myPromos) > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($myPromos as $promo): 
    $expired = $promo['is_expired'] == 1;
?>
    <div class="col-md-6">
        <div class="card coupon-card h-100 shadow-sm <?php echo $expired ? 'coupon-expired' : ''; ?>" 
             style="border-top: 5px solid <?php echo $expired ? '#adb5bd' : $promo['store_color']; ?>;">
            
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold mb-0 <?php echo $expired ? 'text-muted' : ''; ?>">
                        <?php echo htmlspecialchars($promo['title']); ?>
                    </h5>
                    <span class="badge <?php echo $expired ? 'bg-secondary' : 'bg-danger'; ?>">
                        <?php echo $promo['discount_label']; ?>
                    </span>
                </div>
                
                <p class="text-muted small mb-3">
                    <i class="fas fa-store me-1"></i> <?php echo htmlspecialchars($promo['store_name']); ?>
                </p>
                
                <div class="coupon-code-box text-center mb-3 <?php echo $expired ? 'bg-light opacity-50' : ''; ?>">
                    <span class="small d-block text-muted mb-1">CÓDIGO DE CANJE</span>
                    <strong class="<?php echo $expired ? 'text-muted' : 'text-dark'; ?>">
                        SR-<?php echo $promo['id'] . $user['cod']; ?>
                    </strong>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <small class="<?php echo $expired ? 'text-danger fw-bold' : 'text-muted'; ?>">
                        <?php echo $expired ? 'VENCIDA' : 'Vence: ' . $promo['valid_until']; ?>
                    </small>
                    
                    <button class="btn btn-sm rounded-pill px-3 <?php echo $expired ? 'btn-secondary disabled' : 'btn-dark'; ?>" 
                            <?php echo $expired ? 'disabled' : ''; ?>>
                        <?php echo $expired ? 'Expiró' : 'Usar'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
                            <i class="fas fa-ticket-alt fa-3x text-light mb-3"></i>
                            <h5 class="text-muted">Aún no tienes promociones.</h5>
                            <a href="../Promotions/Promotions.php" class="btn btn-primary btn-sm mt-2 rounded-pill">Ver Promociones</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <?php include_once '../../Components/footer/Footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>