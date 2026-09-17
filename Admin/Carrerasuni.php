<?php

$server   = "localhost";
$user     = "root";
$password = "";
$db       = "oepa";

$conectar = mysqli_connect($server, $user, $password, $db) or die("Error al conectar");

if (mysqli_connect_errno()) {
    die("No conectó");
}

// consultas de carreras
$instru = "SELECT 
              c.id_carrera,
              a.id AS id_area,
              a.nombre_area,
              c.nombre_carrera,
              c.descripcion,
              c.certificaciones
           FROM carreras AS c
           JOIN areas AS a ON c.id_area = a.id
           ORDER BY c.id_carrera ASC";

$re = mysqli_query($conectar, $instru);

$carreras = [];
while ($fila = mysqli_fetch_assoc($re)) {
    $carreras[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Carreras</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>