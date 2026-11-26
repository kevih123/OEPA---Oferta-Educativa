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
    $modalidades         = $_POST['modalidades'] ?? [];
    $idsPermitidos       = [1, 2, 3];

    // 1. Validar formato de URL
    if (!filter_var($plan_de_estudios, FILTER_VALIDATE_URL)) {
        echo "<script>alert('Error: La URL del plan de estudios no es un enlace válido.'); window.history.back();</script>";
        exit();
    }

    // 2. Validar que haya al menos una modalidad válida antes de actualizar
    $modalidadesLimpias = array_values(array_intersect(
        array_map('intval', is_array($modalidades) ? $modalidades : []),
        $idsPermitidos
    ));

    if (count($modalidadesLimpias) === 0) {
        echo "<script>alert('Error: Debes seleccionar al menos una modalidad.'); window.history.back();</script>";
        exit();
    }

    $descripcion_general = mysqli_real_escape_string($conectar, $descripcion_general);
    $duracion            = mysqli_real_escape_string($conectar, $duracion);
    $plan_de_estudios    = mysqli_real_escape_string($conectar, $plan_de_estudios);
    $turno_seguro        = mysqli_real_escape_string($conectar, $turno);
    $perfil_ingreso      = mysqli_real_escape_string($conectar, $perfil_ingreso);
    $perfil_egreso       = mysqli_real_escape_string($conectar, $perfil_egreso);
    $demanda             = mysqli_real_escape_string($conectar, $demanda);
    $cupo                = mysqli_real_escape_string($conectar, $cupo);

    $sql = "UPDATE universidades_carreras SET 
            descripcion_general = '$descripcion_general',
            duracion = '$duracion',
            plan_de_estudios = '$plan_de_estudios',
            turno = '$turno_seguro',
            perfil_ingreso = '$perfil_ingreso',
            perfil_egreso = '$perfil_egreso',
            demanda = '$demanda',
            cupo = '$cupo'
            WHERE id_universidades_carreras = $id_relacion";

    if (mysqli_query($conectar, $sql)) {
        // 3. Limpiar modalidades anteriores de esta relación
        // Verifica si tu tabla se llama 'universidades_carreras_modalidades' o 'universidades_carreras_modalidades'
        $stmtDel = mysqli_prepare(
            $conectar,
            "DELETE FROM universidades_carreras_modalidades WHERE id_universidades_carreras = ?"
        );
        mysqli_stmt_bind_param($stmtDel, "i", $id_relacion);
        mysqli_stmt_execute($stmtDel);
        mysqli_stmt_close($stmtDel);

        // 4. Insertar las modalidades seleccionadas
        $stmtIns = mysqli_prepare(
            $conectar,
            "INSERT INTO universidades_carreras_modalidades (id_universidades_carreras, id_modalidad) VALUES (?, ?)"
        );
        foreach ($modalidadesLimpias as $idMod) {
            mysqli_stmt_bind_param($stmtIns, "ii", $id_relacion, $idMod);
            mysqli_stmt_execute($stmtIns);
        }
        mysqli_stmt_close($stmtIns);

        header("Location: admin_uni_carreras.php?id_universidad=$id_universidad");
        exit();
    } else {
        echo "Error al actualizar la carrera: " . mysqli_error($conectar);
    }
}

mysqli_close($conectar);
?>