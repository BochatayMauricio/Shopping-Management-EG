<?php
include_once '../../../app/Services/login.services.php';

$user = getCurrentUser();

echo '<center>';
if ($user) {
    echo '<p>Bienvenido, ' . htmlspecialchars($user['name']) . '!</p>';
} else {
    echo '<p>Usuario no autenticado.</p>';
}
echo '</center>';
?>
