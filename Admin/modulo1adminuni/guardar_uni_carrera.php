<?php
include '../validar_sesion.php';
include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_universidad      = (int)$_POST['id_universidad'];
    $id_carrera          = (int)$_POST['id_carrera'];
    $descripcion_general = trim($_POST['descripcion_general']);
    $duracion            = trim($_POST['duracion']);
    $plan_de_estudios    = trim($_POST['plan_de_estudios']);
    $turno               = $_POST['turno'];
    $perfil_ingreso      = trim($_POST['perfil_ingreso']);
    $perfil_egreso       = trim($_POST['perfil_egreso']);
    $demanda             = trim($_POST['demanda']);
    $cupo                = trim($_POST['cupo']);

    // 1. Validar que la URL sea un enlace válido
    if (!filter_var($plan_de_estudios, FILTER_VALIDATE_URL)) {
        echo "<script>alert('Error: La URL del plan de estudios no es un enlace válido.'); window.history.back();</script>";
        exit();
    }

    // (Opcional) Validar que un campo de texto no contenga números utilizando expresiones regulares (Ejemplo para un campo hipotético de solo letras)
    // if (preg_match('/[0-9]/', $algún_campo_de_texto)) { ... }

    $descripcion_general = mysqli_real_escape_string($conectar, $descripcion_general);
    $duracion            = mysqli_real_escape_string($conectar, $duracion);
    $plan_de_estudios    = mysqli_real_escape_string($conectar, $plan_de_estudios);
    $perfil_ingreso      = mysqli_real_escape_string($conectar, $perfil_ingreso);
    $perfil_egreso       = mysqli_real_escape_string($conectar, $perfil_egreso);
    $demanda             = mysqli_real_escape_string($conectar, $demanda);
    $cupo                = mysqli_real_escape_string($conectar, $cupo);

    $sql = "INSERT INTO universidades_carreras 
            (id_universidad, id_carrera, descripcion_general, duracion, plan_de_estudios, turno, perfil_ingreso, perfil_egreso, demanda, cupo) 
            VALUES 
            ($id_universidad, $id_carrera, '$descripcion_general', '$duracion', '$plan_de_estudios', '$turno', '$perfil_ingreso', '$perfil_egreso', '$demanda', '$cupo')";

    if (mysqli_query($conectar, $sql)) {
        header("Location: admin_uni_carreras.php?id_universidad=$id_universidad");
    } else {
        echo "Error al registrar la carrera: " . mysqli_error($conectar);
    }

    $modalidades = $_POST['modalidades'] ?? [];
    $idsPermitidos = [1, 2, 3];

    if (!is_array($modalidades) || count(array_intersect(array_map('intval', $modalidades), $idsPermitidos)) === 0) {
        echo "<script>alert('Error: Debes seleccionar al menos una modalidad.'); window.history.back();</script>";
        exit();
    }

    if (mysqli_query($conectar, $sql)) {
        $id_nueva_relacion = mysqli_insert_id($conectar);

        $stmtMod = mysqli_prepare(
            $conectar,
            "INSERT INTO universidades_carreras_modalidades (id_universidades_carreras, id_modalidad) VALUES (?, ?)"
        );

        foreach ($modalidades as $mod) {
            $idMod = (int)$mod;
            if (in_array($idMod, $idsPermitidos, true)) {
                mysqli_stmt_bind_param($stmtMod, "ii", $id_nueva_relacion, $idMod);
                mysqli_stmt_execute($stmtMod);
            }
        }
        mysqli_stmt_close($stmtMod);

        header("Location: admin_uni_carreras.php?id_universidad=$id_universidad");
    } else {
        echo "Error al registrar la carrera: " . mysqli_error($conectar);
    }
}
mysqli_close($conectar);
?>