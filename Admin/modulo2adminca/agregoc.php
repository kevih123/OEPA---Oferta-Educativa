<?php
    $ida=$_GET["ida"];
    $nombre = $_GET["nom"];
    $des=$_GET["des"];
    $ce=$_GET["cer"];
    
    include '../conn.php';

    $instru="INSERT INTO carreras( id_area, nombre_carrera, descripcion, certificaciones) VALUES ($ida,'$nombre','$des','$ce');";
    $re=mysqli_query($conectar,$instru);
    if ($re == false) {
        echo "<script>alert('No se pudo agregar la carrera'); window.location='admincarreras.php';</script>";
    } else {
        echo "<script>alert('Carrera registrada con éxito'); window.location='admincarreras.php';</script>";
    }
        
mysqli_close($conectar); ?>   