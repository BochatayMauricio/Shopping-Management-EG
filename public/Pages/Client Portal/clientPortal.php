<?php
include_once '../../../app/Services/login.services.php';
session_start();
$user = getCurrentUser();

include_once __DIR__ . '/../../Components/navbar/NavBar.php';
?>

