<?php
header("Content-Type: application/json; charset=utf-8");

// datos
$id_ca  = $_POST["id_carrera"] ?? 0;
$ida    = $_POST["n_ida"]      ?? "";
$nombre = $_POST["n_nom"]      ?? "";
$des    = $_POST["n_des"]      ?? "";
$ce     = $_POST["n_cer"]      ?? "";

// validar
if (!is_numeric($id_ca) || !is_numeric($ida) || empty($nombre)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Datos incompletos o inválidos"
    ]);
    exit();
}

include "../conn2.php";

$id_ca = intval($id_ca);
$ida   = intval($ida);

$instru = "UPDATE carreras SET 
            id_area = $ida,
            nombre_carrera = '$nombre',
            descripcion = '$des',
            certificaciones = '$ce'
           WHERE id_carrera = $id_ca";

$re = mysqli_query($conectar, $instru);

if ($re) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Carrera actualizada correctamente",
        "carrera" => [
            "id"             => $id_ca,
            "id_area"        => $ida,
            "nombre_carrera" => $nombre,
            "descripcion"    => $des,
            "certificaciones"=> $ce
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo actualizar: " . mysqli_error($conectar)
    ]);
}

mysqli_close($conectar);
?>