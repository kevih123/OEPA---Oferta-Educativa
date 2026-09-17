<?php
header("Content-Type: application/json; charset=utf-8");

$id_uni = $_POST["id_uni"]      ?? 0;
$nombre = $_POST["nuev_no_uni"] ?? "";
$des    = $_POST["n_descri"]    ?? "";
$per    = $_POST["n_pe"]        ?? "";
$pa     = $_POST["n_pa"]        ?? "";
$fe     = $_POST["n_fe"]        ?? "";
$di     = $_POST["n_dire"]      ?? "";
$tele   = $_POST["n_te"]        ?? "";
$si     = $_POST["n_sitio"]     ?? "";
$lo     = $_POST["n_logo"]      ?? "";

// Validar datos 
if (!is_numeric($id_uni) || empty($nombre)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Datos incompletos o inválidos"
    ]);
    exit();
}

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

$id_uni = intval($id_uni);

$instru = "UPDATE universidades SET 
            nombre_institucion = '$nombre',
            descripción = '$des',
            periodo = '$per',
            pago = '$pa',
            frecuencia_convocatoria = '$fe',
            dirección = '$di',
            teléfono = '$tele',
            sitio_web = '$si',
            logo = '$lo'
           WHERE universidad_id = $id_uni";

$re = mysqli_query($conectar, $instru);

if ($re) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Universidad actualizada correctamente",
        "universidad" => [
            "id"     => $id_uni,
            "nombre" => $nombre,
            "logo"   => $lo
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