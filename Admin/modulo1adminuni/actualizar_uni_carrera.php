<?php
include '../validar_sesion.php';
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_relacion         = (int)$_POST['id_universidades_carreras'];
    $id_universidad      = (int)$_POST['id_universidad'];
    $descripcion_general = trim($_POST['descripcion_general']);
    $duracion            = trim($_POST['duracion']);
    $plan_de_estudios    = trim($_POST['plan_de_estudios']);
    $turno               = $_POST['turno'];
    $perfil_ingreso      = trim($_POST['perfil_ingreso']);
    $perfil_egreso       = trim($_POST['perfil_egreso']);
    $demanda             = trim($_POST['demanda']);
    $cupo                = trim($_POST['cupo']);

    // 1. Validar formato de URL en edición
    if (!filter_var($plan_de_estudios, FILTER_VALIDATE_URL)) {
        echo "<script>alert('Error: La URL del plan de estudios no es un enlace válido.'); window.history.back();</script>";
        exit();
    }

    $descripcion_general = mysqli_real_escape_string($conectar, $descripcion_general);
    $duracion            = mysqli_real_escape_string($conectar, $duracion);
    $plan_de_estudios    = mysqli_real_escape_string($conectar, $plan_de_estudios);
    $perfil_ingreso      = mysqli_real_escape_string($conectar, $perfil_ingreso);
    $perfil_egreso       = mysqli_real_escape_string($conectar, $perfil_egreso);
    $demanda             = mysqli_real_escape_string($conectar, $demanda);
    $cupo                = mysqli_real_escape_string($conectar, $cupo);

    $sql = "UPDATE universidades_carreras SET 
            descripcion_general = '$descripcion_general',
            duracion = '$duracion',
            plan_de_estudios = '$plan_de_estudios',
            turno = '$turno',
            perfil_ingreso = '$perfil_ingreso',
            perfil_egreso = '$perfil_egreso',
            demanda = '$demanda',
            cupo = '$cupo'
            WHERE id_universidades_carreras = $id_relacion";

    if (mysqli_query($conectar, $sql)) {
        header("Location: admin_uni_carreras.php?id_universidad=$id_universidad");
    } else {
        echo "Error al actualizar la carrera: " . mysqli_error($conectar);
    }
}
mysqli_close($conectar);
?>