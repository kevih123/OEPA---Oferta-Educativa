
<?php
session_start();
require 'conn.php';


// Destruir la sesión de PHP
$_SESSION = [];
session_destroy();

// Evitar que el navegador almacene la página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

header("Location: login.php");
exit;
?>
