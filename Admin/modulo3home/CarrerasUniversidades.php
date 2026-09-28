<?php
require '../conn.php';

// Validar que se haya recibido el ID de la carrera mediante GET
if (!isset($_GET['id_carrera']) || empty($_GET['id_carrera'])) {
    // Si no enviaron un ID, redirigir a la oferta educativa
    header('Location: CarrerasAreas.php');
    exit();
}

// Convertir a entero por seguridad (sanitización básica)
$id_carrera = (int)$_GET['id_carrera'];

// Obtener el nombre de la carrera de la BD
$consulta_carrera = "SELECT nombre_carrera FROM carreras WHERE id_carrera = $id_carrera";
$resultado_carrera = mysqli_query($conectar, $consulta_carrera);
$carrera_info = mysqli_fetch_assoc($resultado_carrera);

// Si la carrera con ese ID no existe en la BD
if (!$carrera_info) {
    header('Location: CarrerasAreas.php');
    exit();
}

$nombre_carrera = $carrera_info['nombre_carrera'];

// Consulta JOIN para obtener las universidades que imparten esa carrera
$query = "SELECT 
              u.universidad_id,
              u.nombre_institucion,
              u.direccion,
              u.sitio_web,
              uc.duracion,
              uc.turno
           FROM universidades_carreras AS uc
           JOIN universidades AS u ON uc.id_universidad = u.universidad_id
           WHERE uc.id_carrera = $id_carrera
           ORDER BY u.nombre_institucion ASC";

$resultado_unis = mysqli_query($conectar, $query);

// Agregar las universidades a un arreglo
$universidades = [];
while ($fila = mysqli_fetch_assoc($resultado_unis)) {
    $universidades[] = $fila;
}

// Cerrar la conexión
mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universidades - <?php echo htmlspecialchars($nombre_carrera); ?></title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="../Estilos/CarrerasUniversidades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ============ HEADER ============ -->
    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Carrera">
            </div>
            <a class="btn-primary" href="../login.php">¿Eres admin?</a>
        </div>
        <nav class="header-nav">
            <a href="Paginaprincipal.php">Universidades</a>
            <a href="CarrerasAreas.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <!-- ============ CONTENIDO ============ -->
    <main class="admin-main">

        <!-- Ruta / Breadcrumb -->
        <div class="breadcrumb">
            <a href="CarrerasAreas.php">Oferta Educativa</a> &gt; <span><?php echo htmlspecialchars($nombre_carrera); ?></span>
        </div>

        <h2 class="section-title">Universidades con <?php echo htmlspecialchars($nombre_carrera); ?></h2>

        <section class="unis-list">

            <?php if (empty($universidades)): ?>
                <p style="text-align:center; color:#666; padding: 40px;">
                    No hay universidades registradas que oferten esta carrera por el momento.
                </p>
            <?php else: ?>
                <?php foreach ($universidades as $uni): ?>
                    <article class="uni-row">
                        <div class="uni-info">
                            <h3><?php echo htmlspecialchars($uni['nombre_institucion']); ?></h3>
                        </div>
                        <!-- Botón para ver los detalles completos de la universidad o su plan de estudios -->
                        <a href="DetalleCarrera.php?id_universidad=<?php echo $uni['universidad_id']; ?>&id_carrera=<?php echo $id_carrera; ?>" class="btn-detalles">
                            Ver detalles &gt;
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>

    </main>

    <script src="buscador.js"></script>
</body>
</html>