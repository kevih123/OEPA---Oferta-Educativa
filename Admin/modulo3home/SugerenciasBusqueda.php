<?php
require '../conn.php';

header('Content-Type: application/json; charset=utf-8');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

// Normalizar la cadena y obviar acentos/mayúsculas con COLLATE
$query_normal = mysqli_real_escape_string($conectar, $query);

$sql = "SELECT id_carrera, nombre_carrera 
        FROM carreras 
        WHERE nombre_carrera COLLATE utf8mb4_general_ci LIKE '%$query_normal%' 
        ORDER BY nombre_carrera ASC 
        LIMIT 6";

$res = mysqli_query($conectar, $sql);

$sugerencias = [];
if ($res) {
    while ($fila = mysqli_fetch_assoc($res)) {
        $sugerencias[] = [
            'id' => $fila['id_carrera'],
            'nombre' => $fila['nombre_carrera']
        ];
    }
}

mysqli_close($conectar);
echo json_encode($sugerencias, JSON_UNESCAPED_UNICODE);