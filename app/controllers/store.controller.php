<?php
// Definimos la ruta base al principio de todo
$base_path = realpath(__DIR__ . '/../../');

include_once $base_path . '/app/Config/config.php'; 
include_once $base_path . '/app/Services/stores.services.php';

if (isset($_POST['btnCreateStore'])) {
    $name = $_POST['name'];
    $local_number = $_POST['local_number'];
    $floor = $_POST['ubication']; // Ojo: chequearemos esto en el servicio
    $category = $_POST['category'];
    $color = $_POST['color'];
    $id_owner = $_POST['id_owner'];

    $logo_name = "default_logo.png";

    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === 0) {
        $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
        $new_filename = str_replace(' ', '_', $name) . "_" . time() . "." . $ext;
        
        // CORRECCIÓN DE RUTA: Usamos $base_path obligatoriamente
        $target_path = $base_path . "/assets/stores/" . $new_filename;

        if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $target_path)) {
            $logo_name = $new_filename;
        }
    }

    // Llamada al servicio
    $result = createStore($name, $local_number, $floor, $category, $color, $logo_name, $id_owner);

    if ($result) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=store_created");
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=failed");
    }
    exit();
}

if (isset($_POST['btnUpdateStore'])) {
    $id = $_POST['store_id'];
    $name = $_POST['name'];
    $floor = $_POST['ubication'];
    $local_number = $_POST['local_number'];

    if (updateStore($id, $name, $floor, $local_number)) {
        // Redirigir con éxito
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?success=updated");
    } else {
        // Redirigir con error
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=update_failed");
    }
    exit();
}