<?php

$server = "localhost";
$db     = "oepa";
$user   = "root";
$pass   = "";

$conectar = mysqli_connect($server, $user, $pass, $db);

if (mysqli_connect_errno()) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo conectar a la BD"
    ]);
    exit();
}
?>