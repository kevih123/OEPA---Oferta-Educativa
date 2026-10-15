```php
<?php
require '../conn.php';

if (!isset($_GET['id_carrera']) || empty($_GET['id_carrera'])) {
    header('Location: CarrerasAreas.php');
    exit();
}

$id_carrera = (int)$_GET['id_carrera'];

$modalidadesFiltro = [
    'presencial' => 1,
    'en_linea' => 2,
    'hibrida' => 3
];

$modalidadActual = $_GET['modalidad'] ?? 'todas';

if (
    $modalidadActual !== 'todas' &&
    !isset($modalidadesFiltro[$modalidadActual])
) {
    $modalidadActual = 'todas';
}

$consulta_carrera = "SELECT nombre_carrera
                     FROM carreras
                     WHERE id_carrera = $id_carrera";

$resultado_carrera = mysqli_query($conectar, $consulta_carrera);
$carrera_info = mysqli_fetch_assoc($resultado_carrera);

if (!$carrera_info) {
    header('Location: CarrerasAreas.php');
    exit();
}

$nombre_carrera = $carrera_info['nombre_carrera'];

$query = "SELECT
              u.universidad_id,
              u.nombre_institucion,
              u.direccion,
              u.sitio_web,
              uc.duracion,
              uc.turno
           FROM universidades_carreras AS uc
           JOIN universidades AS u
               ON uc.id_universidad = u.universidad_id
           WHERE uc.id_carrera = $id_carrera";

if ($modalidadActual !== 'todas') {
    $idModalidad = $modalidadesFiltro[$modalidadActual];

    $query .= " AND EXISTS (
                    SELECT 1
                    FROM carrera_modalidades AS cm
                    WHERE cm.id_carrera = uc.id_carrera
                    AND cm.id_modalidad = $idModalidad
                )";
}

$query .= " ORDER BY u.nombre_institucion ASC";

$resultado_unis = mysqli_query($conectar, $query);

$universidades = [];

while ($fila = mysqli_fetch_assoc($resultado_unis)) {
    $universidades[] = $fila;
}

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

    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>

            <div class="search-bar-container">
                <form action="ResultadoBusqueda.php" method="GET" class="search-bar">
                    <input type="text" id="buscador" name="q" placeholder="Busca Carrera" autocomplete="off">

                    <button type="submit" class="btn-search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <div id="sugerencias-box" class="sugerencias-box" style="position: absolute; top: 100%; left: 0; width: 100%; background: #ffffff; z-index: 99999; display: none; border-radius: 0 0 10px 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);"></div>
                </form>
            </div>

            <a class="btn-primary" href="../login.php">¿Eres admin?</a>
        </div>

        <nav class="header-nav">
            <a href="Paginaprincipal.php">Universidades</a>
            <a href="CarrerasAreas.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <?php include 'Modalmodalidades.php'; ?>

    <main class="admin-main">

        <div class="breadcrumb">
            <a href="CarrerasAreas.php">Oferta Educativa</a>
            &gt;
            <span><?php echo htmlspecialchars($nombre_carrera); ?></span>
        </div>

        <h2 class="section-title">
            Universidades con <?php echo htmlspecialchars($nombre_carrera); ?>
        </h2>

        <section class="unis-list">

            <?php if (empty($universidades)): ?>
                <p style="text-align:center; color:#666; padding: 40px;">
                    No hay universidades registradas que oferten esta carrera en la modalidad seleccionada.
                </p>
            <?php else: ?>

                <?php foreach ($universidades as $uni): ?>
                    <article class="uni-row">
                        <div class="uni-info">
                            <h3>
                                <?php echo htmlspecialchars($uni['nombre_institucion']); ?>
                            </h3>
                        </div>

                        <a href="DetalleCarrera.php?id_universidad=<?php echo $uni['universidad_id']; ?>&id_carrera=<?php echo $id_carrera; ?>&modalidad=<?php echo urlencode($modalidadActual); ?>" class="btn-detalles">
                            Ver detalles &gt;
                        </a>
                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>

    <script src="busqueda_predictiva.js"></script>

</body>
</html>
```