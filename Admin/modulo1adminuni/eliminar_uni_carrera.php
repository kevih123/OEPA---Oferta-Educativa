<?php
include '../validar_sesion.php';
include '../conn.php';

if (isset($_GET['id']) && isset($_GET['id_uni'])) {
    $id_relacion = (int)$_GET['id'];
    $id_uni      = (int)$_GET['id_uni'];

    $sql = "DELETE FROM universidades_carreras WHERE id_universidades_carreras = $id_relacion";
    
    if (mysqli_query($conectar, $sql)) {
        header("Location: admin_uni_carreras.php?id_universidad=$id_uni");
    } else {
        echo "Error al eliminar la relación: " . mysqli_error($conectar);
    }
}
mysqli_close($conectar);
?>