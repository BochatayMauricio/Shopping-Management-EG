<?php
session_start();
require_once __DIR__. '/../config/config.php';
include_once __DIR__ . '/alert.service.php';

function registerUser($userName, $password) {
    global $CONNECTION;
    if (!$CONNECTION) {
        AlertService::error("Error: No hay conexión a la base de datos");
        return false;
    }
    $userName = strtolower($userName);

    $checkQuery = "SELECT codUser FROM users WHERE userName = '$userName'";
    $checkResult = mysqli_query($CONNECTION, $checkQuery);
    if ($checkResult->num_rows > 0) {
        AlertService::warning('El nombre de usuario ya existe.');
        return false; // Usuario ya existe
    }
    // Hashear la contraseña antes de guardarla
    $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

    $query="INSERT INTO users (userName, userPassword, userType, userCategory) VALUES ('$userName', '$passwordHashed', 'client', 'inicial')";
    $result = mysqli_query($CONNECTION, $query);
    if (!$result) {
        AlertService::error("Error: " . mysqli_error($CONNECTION));
        return false;
    }    

    $codUser = mysqli_insert_id($CONNECTION);
   
    $query = "SELECT * FROM users WHERE codUser = $codUser";
    $result = mysqli_query($CONNECTION, $query);
    if (!$result) {
        AlertService::error("Error: " . mysqli_error($CONNECTION));
        return false;
    }
    if($result->num_rows == 0){
        return false;
    }
    $userLogued = $result->fetch_assoc();

    $_SESSION['user'] = $userLogued;

    mysqli_close($CONNECTION);
    
    return $userLogued;
}
