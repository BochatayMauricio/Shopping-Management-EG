<?php
// Definimos la ruta base al principio de todo
$base_path = realpath(__DIR__ . '/../../');

include_once $base_path . '/app/Config/config.php'; 
include_once $base_path . '/app/Services/stores.services.php';
include_once $base_path . '/app/Services/user.services.php';
include_once $base_path . '/app/Services/validation.service.php';

if (isset($_POST['btnCreateStore'])) {
    
    // 1. Captura y limpieza de datos del local
    $name = trim($_POST['name'] ?? '');
    $local_number = trim($_POST['local_number'] ?? '');
    $floor = $_POST['ubication'] ?? ''; 
    $category = $_POST['category'] ?? '';
    $color = $_POST['color'] ?? '#0d6efd';
    
    // 2. Lógica de Asignación / Creación de Dueño
    $ownerMode = $_POST['owner_mode'] ?? 'existing';
    $id_owner = null;

    if ($ownerMode === 'new') {
        $newName = trim($_POST['new_owner_name'] ?? '');
        $newEmail = trim($_POST['new_owner_email'] ?? '');
        $newPass = $_POST['new_owner_password'] ?? '';

        // Validación de formato de email
        if (!ValidationService::isValidEmail($newEmail)) {
            AlertService::error(ValidationService::getEmailErrorMessage());
            header("Location: Stores.php?openModal=addStore"); exit();
        }

        // Validación de longitud de contraseña
        if (!ValidationService::isValidPassword($newPass)) {
            AlertService::error('La contraseña del dueño debe tener entre 6 y 20 caracteres.');
            header("Location: Stores.php?openModal=addStore"); exit();
        }

        // Creamos al usuario con rol 'owner'
        $ownerResult = registerUser($newName, $newEmail, $newPass, 'owner');

        if ($ownerResult === "username_exists") {
            AlertService::error('No se pudo crear el local: El nombre de usuario del dueño ya está registrado.');
            header("Location: Stores.php?openModal=addStore"); exit();
        } elseif ($ownerResult === "email_exists") {
            AlertService::error('No se pudo crear el local: El email del dueño ya está registrado.');
            header("Location: Stores.php?openModal=addStore"); exit();
        } elseif ($ownerResult === "password_too_short") {
            AlertService::error('No se pudo crear el local: La contraseña del dueño es muy corta.');
            header("Location: Stores.php?openModal=addStore"); exit();
        } elseif (!$ownerResult) {
            AlertService::error('Error al crear el dueño responsable.');
            header("Location: Stores.php?openModal=addStore"); exit();
        }

        // Para no tocar tu user.services.php, buscamos el ID del dueño que acabamos de crear
        global $CONNECTION;
        $stmtOwner = $CONNECTION->prepare("SELECT cod FROM users WHERE email = ?");
        $stmtOwner->bind_param("s", $newEmail);
        $stmtOwner->execute();
        $resOwner = $stmtOwner->get_result();
        
        if ($row = $resOwner->fetch_assoc()) {
            $id_owner = $row['cod'];
        } else {
            AlertService::error('Error al recuperar el ID del nuevo dueño.');
            header("Location: Stores.php"); exit();
        }
        
    } else {
        // Tomamos el dueño existente del select
        $id_owner = $_POST['id_owner'] ?? null;
    }

    // Validación de seguridad por si alguien manipula el HTML
    if (!$id_owner) {
        AlertService::error('Debes asignar un dueño al local.');
        header("Location: Stores.php?openModal=addStore"); exit();
    }

    // 3. Lógica del Logo (URL vs Archivo)
    $logo_name = "default_logo.png";
    $logo_url = trim($_POST['logo_icon'] ?? '');

    // Si pegó una URL válida, la usamos por defecto
    if (!empty($logo_url) && filter_var($logo_url, FILTER_VALIDATE_URL)) {
        $logo_name = $logo_url;
    }

    // Si subió un archivo, este tiene prioridad y "pisa" a la URL
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === 0) {
        $ext = pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION);
        $new_filename = str_replace(' ', '_', $name) . "_" . time() . "." . $ext;
        
        $target_path = $base_path . "/assets/stores/" . $new_filename;

        if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $target_path)) {
            $logo_name = $new_filename;
        }
    }

    // 4. Crear el local en la Base de Datos
    $result = createStore($name, $local_number, $floor, $category, $color, $logo_name, $id_owner);

    if ($result) {
        AlertService::success('Local creado con éxito.');
        header("Location: Stores.php"); 
    } else {
        AlertService::error('Error al guardar el local en la base de datos.');
        header("Location: Stores.php"); 
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

// Procesar la baja de un local
if (isset($_POST['btnDeleteStore'])) {
    $storeId = $_POST['store_id'];
    
    if (deleteStore($storeId)) {
        AlertService::success("Local eliminado con éxito.");
    } else {
        // Aquí entrará si countStorePromotions > 0
        AlertService::error("No se puede eliminar: El local tiene promociones activas.");
    }
    
    header("Location: Stores.php");
    exit();
}