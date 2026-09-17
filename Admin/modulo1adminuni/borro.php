<?php
header("Content-Type: application/json; charset=utf-8");

$id_uni = $_GET["id_uni"] ?? 0;

if (!is_numeric($id_uni)) {
    echo json_encode(["success" => false, "mensaje" => "ID inválido"]);
    exit();
}

$server = "localhost";
$db     = "oepa";
$user   = "root";
$pass   = "";

$conectar = mysqli_connect($server, $user, $pass, $db);

if (mysqli_connect_errno()) {
    echo json_encode(["success" => false, "mensaje" => "No se pudo conectar a la BD"]);
    exit();
}

$id_uni = intval($id_uni);

$instru = "DELETE FROM universidades WHERE universidad_id = $id_uni";
$re = mysqli_query($conectar, $instru);

if ($re && mysqli_affected_rows($conectar) > 0) {
    echo json_encode(["success" => true, "mensaje" => "Universidad eliminada"]);
} elseif ($re) {
    echo json_encode(["success" => false, "mensaje" => "No existe esa universidad"]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Error al eliminar"]);
}

mysqli_close($conectar);
?>