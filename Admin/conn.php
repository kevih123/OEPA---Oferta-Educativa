<?php
$server = "localhost";
$db     = "oepa";
$user   = "root";
$pass   = "";

// Conexión (NO MODIFICAR)
$conectar = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    die("No se pudo conectar a la base de datos");
}
?>