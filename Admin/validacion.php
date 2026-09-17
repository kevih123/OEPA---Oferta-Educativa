
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="">
    <title>Document</title>
</head>
<body >
<?php  

$server = "localhost";
$db     = "oepa";
$user   = "root";
$pass   = "";
//evitar inserción sql
$conectar = mysqli_connect($server, $user, $pass, $db);
$usua=$conectar->real_escape_string( $_POST['usuario']);
$usuario=strtolower($usua);
$contra=$conectar->real_escape_string( $_POST['contra']);


if (mysqli_connect_errno()) {
    die("No se pudo conectar a la base de datos");
}

// consulta para universidades
$sql = "SELECT count(*) as cuenta From administradores where user_name='$usuario' and password='$contra'";
$res = mysqli_query($conectar, $sql);
if($reg=$res->fetch_array()){
        if($reg['cuenta']>0){
           
            header('location:../Admin/modulo1adminuni/inicioadmin.php');
                   exit(); 
                        
                }

         else{?>
    <center><h1><?php echo 'Los datos son incorrectos';?></h1><button  onclick="top.location='login.php'">Regresar al login
</button></center><?php
    }       
            }
    
    
     mysqli_close($conectar);
    
    ?>
    
</body>
</html>
    
    