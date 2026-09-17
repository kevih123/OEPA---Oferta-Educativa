<?php
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    // Opcional: registrar intento o mostrar mensaje
    header('Location: http://localhost/OEPA/Admin/login.php'); // ruta relativa al login
    exit();
}

?>