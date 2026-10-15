php
<?php
require '../conn.php';

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

$instru = "SELECT
              c.id_carrera,
              a.id AS id_area,
              a.nombre_area,
              c.nombre_carrera,
              c.descripcion,
              c.certificaciones
           FROM carreras AS c
           JOIN areas AS a ON c.id_area = a.id";

if ($modalidadActual !== 'todas') {
    $idModalidad = $modalidadesFiltro[$modalidadActual];

    $instru .= " WHERE EXISTS (
                    SELECT 1
                    FROM carrera_modalidades AS cm
                    WHERE cm.id_carrera = c.id_carrera
                    AND cm.id_modalidad = $idModalidad
                )";
}

$instru .= " ORDER BY c.id_carrera ASC";

$re = mysqli_query($conectar, $instru);

$carreras = [];

while ($fila = mysqli_fetch_assoc($re)) {
    $carreras[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carreras</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

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
            <a href="admincarreras.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <main class="admin-main">

        <?php include 'Modalmodalidades.php'; ?>

        <div class="admin-toolbar">
            <h2>Carreras</h2>
        </div>

        <section class="cards-grid" id="cardsGrid">

            <?php if (empty($carreras)): ?>
                <p style="grid-column: 1/-1; text-align:center; color:#666; padding: 40px;">
                    No hay carreras registradas para esta modalidad.
                </p>
            <?php else: ?>

                <?php foreach ($carreras as $c): ?>
                    <article class="uni-card" data-id="<?php echo $c['id_carrera']; ?>">

                        <div class="card-actions"></div>

                        <span class="card-badge">
                            <?php echo htmlspecialchars($c['nombre_area']); ?>
                        </span>

                        <p class="card-nombre">
                            <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                        </p>

                        <p class="card-desc">
                            <?php echo htmlspecialchars($c['descripcion'] ?? ''); ?>
                        </p>

                        <?php if (!empty($c['certificaciones'])): ?>
                            <p class="card-cert">
                                <i class="fa-solid fa-certificate"></i>
                                <?php echo htmlspecialchars($c['certificaciones']); ?>
                            </p>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>

    <script src="buscador.js"></script>

</body>
</html>
```