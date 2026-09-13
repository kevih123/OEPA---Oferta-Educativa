<?php
header("Content-Type: application/json; charset=utf-8");

// recibir id
$id_ca = $_GET["id_ca"] ?? 0;

// validar
if (!is_numeric($id_ca)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "ID inválido"
    ]);
    exit();
}

$server = "localhost";
$db     = "oepa";
$user   = "root";
$pass   = "Dilialaide123";

$conectar = mysqli_connect($server, $user, $pass, $db);

if (mysqli_connect_errno()) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo conectar a la BD"
    ]);
    exit();
}

// borrar
$id_ca = intval($id_ca);

$instru = "DELETE FROM carreras WHERE id_carrera = $id_ca";
$re = mysqli_query($conectar, $instru);

if ($re && mysqli_affected_rows($conectar) > 0) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Carrera eliminada correctamente"
    ]);
} elseif ($re) {
    echo json_encode([
        "success" => false,
        "mensaje" => "No existe esa carrera"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al eliminar: " . mysqli_error($conectar)
    ]);
}

mysqli_close($conectar);
?>