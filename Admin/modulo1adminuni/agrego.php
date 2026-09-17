<?php
    
    $nombre = $_GET["nombre"];
    $des=$_GET["descri"];
    $per=$_GET["pe"];
    $pa=$_GET["pa"];
    $fe=$_GET["fe"];
    $di=$_GET["dire"];
    $tele=$_GET["te"];
    $si=$_GET["sitio"];
    $lo=$_GET["logo"];
    $server = "localhost";
    $db = "oepa";
    $user = "root";
    $pass = "";
    $conectar = mysqli_connect($server, $user, $pass,$db);
    if(mysqli_connect_errno()){
        echo "no se pudo conectar a la bd";
        exit();
    }
    $instru=" INSERT INTO Universidades ( nombre_institucion, descripción, periodo, pago, frecuencia_convocatoria, dirección, teléfono, sitio_web, logo) VALUES('$nombre','$des','$per','$pa','$fe','$di','$tele','$si','$lo');";
    $re=mysqli_query($conectar,$instru);
    if ($re == false) {
        echo "<script>alert('No se pudo agregar la universidad'); window.location='inicioadmin.php';</script>";
    } else {
        echo "<script>alert('Universidad registrada exitosamente'); window.location='inicioadmin.php';</script>";
    } 
mysqli_close($conectar); ?>   
        